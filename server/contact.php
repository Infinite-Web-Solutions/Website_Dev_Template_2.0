<?php
/**
 * Sicherer Kontaktformular-Handler (server/contact.php)
 *
 * Schutzmechanismen:
 * - Same-Origin-Check als CSRF-Schutz: Session-Tokens lassen sich in statisches
 *   HTML nicht einbetten, daher wird die Anfrage-Herkunft (Origin/Referer) geprüft
 * - Honeypot-Feld "website" (muss leer bleiben)
 * - Rate Limiting: 1 Anfrage pro 60 Sekunden pro Session
 * - Input-Sanitierung, Längen-Validierung, Schutz vor Header-Injection
 * - Versand ausschließlich per PHPMailer über SMTP — kein mail()
 *
 * Voraussetzungen auf dem Server:
 * - server/mail-config.php (nicht im Repo! Format siehe _knowledge/06-forms.md)
 * - PHPMailer: entweder Composer (vendor/ im Site-Root) oder manuell
 *   entpackt nach server/phpmailer/src/
 */
declare(strict_types=1);
session_start();

header('Content-Type: application/json; charset=utf-8');

function respond(int $status, bool $success, string $message): void {
    http_response_code($status);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// Nur POST-Anfragen erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, false, 'Methode nicht erlaubt.');
}

// 1. CSRF-Schutz: Same-Origin-Check (Origin bevorzugt, Referer als Fallback)
$requestHost = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? '')[0]);
$sourceUrl   = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
$sourceHost  = strtolower((string) parse_url($sourceUrl, PHP_URL_HOST));
if ($requestHost === '' || $sourceHost === '' || $sourceHost !== $requestHost) {
    respond(403, false, 'Sicherheitsfehler: Ungültige Anfrage-Herkunft.');
}

// 2. Honeypot — Bots füllen das unsichtbare Feld aus; Erfolg vortäuschen
if (!empty($_POST['website'])) {
    respond(200, true, 'Ihre Nachricht wurde erfolgreich gesendet!');
}

// 3. Rate Limiting (Session-basiert — Bots ohne Cookies scheitern bereits am
//    Origin-Check; für härtere Limits serverseitig fail2ban/mod_security nutzen)
if (isset($_SESSION['last_submit']) && (time() - $_SESSION['last_submit'] < 60)) {
    respond(429, false, 'Bitte warten Sie eine Minute, bevor Sie eine weitere Nachricht senden.');
}

// 4. Input-Bereinigung
$name    = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');
$privacy = !empty($_POST['privacy']);

// 5. Schutz vor Header-Injection
if (preg_match('/[\r\n]/', $name . $email)) {
    respond(400, false, 'Ungültige Zeichen in Name oder E-Mail.');
}

// 6. Validierung
$errors = [];
if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    $errors[] = 'Bitte geben Sie Ihren Namen an (2–100 Zeichen).';
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
    respond(400, false, implode(' ', $errors));
}

// 7. SMTP-Konfiguration laden (liegt NICHT im Repo — manuell hochladen)
$mailConfigFile = __DIR__ . '/mail-config.php';
if (!file_exists($mailConfigFile)) {
    error_log('contact.php: mail-config.php fehlt');
    respond(500, false, 'Server-Konfigurationsfehler. Bitte kontaktieren Sie uns telefonisch.');
}
$cfg = require $mailConfigFile;

// 8. PHPMailer laden: Composer-Autoload oder manuelle Installation
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} elseif (file_exists(__DIR__ . '/phpmailer/src/PHPMailer.php')) {
    require_once __DIR__ . '/phpmailer/src/Exception.php';
    require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/phpmailer/src/SMTP.php';
} else {
    error_log('contact.php: PHPMailer nicht gefunden (weder Composer noch server/phpmailer/src/)');
    respond(500, false, 'Server-Konfigurationsfehler. Bitte kontaktieren Sie uns telefonisch.');
}

// 9. E-Mail versenden
$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = $cfg['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $cfg['user'];
    $mail->Password   = $cfg['pass'];
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $cfg['port'] ?? 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($cfg['from'], 'Website Kontaktformular');
    $mail->addAddress($cfg['to']);
    $mail->addReplyTo($email, $name);

    $mail->isHTML(false);
    $mail->Subject = 'Neue Kontaktanfrage von ' . $name;
    $mail->Body    = "Name: $name\nE-Mail: $email\n\nNachricht:\n$message";

    $mail->send();
    $_SESSION['last_submit'] = time(); // Rate-Limit erst nach erfolgreichem Versand setzen
    respond(200, true, 'Ihre Nachricht wurde erfolgreich gesendet!');
} catch (\Throwable $e) {
    error_log('contact.php: Mailversand fehlgeschlagen — ' . $e->getMessage());
    respond(500, false, 'Fehler beim Senden. Bitte versuchen Sie es später erneut oder rufen Sie uns an.');
}
