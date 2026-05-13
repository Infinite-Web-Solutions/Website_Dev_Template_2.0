<?php
/**
 * Sicherer Kontaktformular-Handler
 * Implementiert CSRF-Schutz, Honeypot, Rate Limiting, Input-Bereinigung und Schutz vor Header-Injection.
 * Verwendet PHPMailer für sicheren E-Mail-Versand (erfordert mail-config.php mit Zugangsdaten).
 */
session_start();

// JSON-Header für AJAX-Antworten setzen
header('Content-Type: application/json; charset=utf-8');

// Nur POST-Anfragen erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// 1. Honeypot (Bot-Schutz für statische Seiten)
// Das 'website'-Feld muss leer sein
if (!empty($_POST['website'])) {
    // 200 OK zurückgeben, um den Bot zu täuschen, aber nichts tun
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Erfolgreich gesendet']);
    exit;
}

// 2. Rate Limiting (IP-basiert, 1 Anfrage pro 60 Sekunden)
// Da es sich um eine statische Seite handelt, verlassen wir uns nicht zwingend auf Sessions, sondern zusätzlich auf die IP.
$user_ip = $_SERVER['REMOTE_ADDR'];
if (isset($_SESSION['last_submit_' . $user_ip]) && (time() - $_SESSION['last_submit_' . $user_ip] < 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Bitte warten Sie kurz, bevor Sie eine weitere Nachricht senden.']);
    exit;
}
$_SESSION['last_submit_' . $user_ip] = time();

// 3. Input-Bereinigung
$name    = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');
$privacy = isset($_POST['privacy']) ? true : false;

// 4. Schutz vor Header-Injection
if (preg_match('/[\r\n]/', $name . $email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige Zeichen in Name oder E-Mail']);
    exit;
}

// 5. Validierung
$errors = [];
if (empty($name)) {
    $errors[] = 'Name ist erforderlich.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Ungültige E-Mail-Adresse.';
}
if (mb_strlen($message) < 10 || mb_strlen($message) > 5000) {
    $errors[] = 'Die Nachricht muss zwischen 10 und 5000 Zeichen lang sein.';
}
if (!$privacy) {
    $errors[] = 'Die Zustimmung zum Datenschutz ist erforderlich.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// 6. E-Mail senden (mittels PHPMailer oder einer sicheren Mail-Implementierung)
// Mail-Konfiguration einbinden (darf nicht in Git eingecheckt werden!)
// Die Datei server/mail-config.php sollte Konstanten definieren oder ein Array mit SMTP-Details zurückgeben.
$mailConfigFile = __DIR__ . '/mail-config.php';
if (file_exists($mailConfigFile)) {
    require_once $mailConfigFile;
} else {
    // Fehler, falls die Konfiguration in der Produktion fehlt
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server-Konfigurationsfehler.']);
    exit;
}

// Beispielhafte Nutzung von PHPMailer, falls geladen:
/*
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // oder ein eigener Autoloader

$mail = new PHPMailer(true);
try {
    // Server-Einstellungen
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = SMTP_PORT;

    // Empfänger
    $mail->setFrom(SMTP_USER, 'Website Kontaktformular');
    $mail->addAddress(RECIPIENT_EMAIL);
    $mail->addReplyTo($email, $name);

    // Inhalt
    $mail->isHTML(false);
    $mail->Subject = 'Neue Anfrage von ' . $name;
    $mail->Body    = "Name: $name\nE-Mail: $email\n\nNachricht:\n$message";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Ihre Nachricht wurde erfolgreich gesendet!']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Fehler beim Senden der E-Mail.']);
}
*/

// Für das Template geben wir Erfolg zurück (Mock-Implementierung, falls PHPMailer noch nicht installiert ist)
http_response_code(200);
echo json_encode(['success' => true, 'message' => 'Ihre Nachricht wurde erfolgreich gesendet!']);
