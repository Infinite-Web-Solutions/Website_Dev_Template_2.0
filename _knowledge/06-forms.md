# Formulare & SMTP-Zustellung

## Architektur-Entscheidung

Standard: PHP + all-inkl SMTP via PHPMailer.
Kein externer Dienst, keine US-Server, DSGVO-einfach.

## Vollständiges server/contact.php

```php
<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Nicht erlaubt']);
  exit;
}

// CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Sicherheitsfehler']);
  exit;
}

// Honeypot
if (!empty($_POST['website'])) {
  echo json_encode(['success' => true]);
  exit;
}

// Rate Limit
if (isset($_SESSION['last_submit']) && time() - $_SESSION['last_submit'] < 60) {
  http_response_code(429);
  echo json_encode(['success' => false, 'message' => 'Bitte eine Minute warten']);
  exit;
}
$_SESSION['last_submit'] = time();

// Input
$name    = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');
$privacy = !empty($_POST['datenschutz']);

// Validierung
$errors = [];
if (strlen($name) < 2)                              $errors[] = 'Name fehlt';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))     $errors[] = 'E-Mail ungültig';
if (strlen($message) < 10)                          $errors[] = 'Nachricht zu kurz';
if (!$privacy)                                      $errors[] = 'Datenschutz nicht bestätigt';
if (preg_match('/[\r\n]/', $name . $email))         $errors[] = 'Ungültige Eingabe';

if (!empty($errors)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
  exit;
}

// PHPMailer via Composer (vendor/ außerhalb public/)
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

// SMTP-Config außerhalb Webroot
$cfg = require '../server/mail-config.php';

$mail = new PHPMailer(true);
try {
  $mail->isSMTP();
  $mail->Host       = $cfg['host'];
  $mail->SMTPAuth   = true;
  $mail->Username   = $cfg['user'];
  $mail->Password   = $cfg['pass'];
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587;
  $mail->CharSet    = 'UTF-8';
  $mail->setFrom($cfg['from'], 'Kontaktformular');
  $mail->addAddress($cfg['to']);
  $mail->addReplyTo($email, $name);
  $mail->Subject = "Kontaktanfrage von $name";
  $mail->Body    = "Name: $name\nE-Mail: $email\n\nNachricht:\n$message";
  $mail->send();
  echo json_encode(['success' => true, 'message' => 'Nachricht gesendet']);
} catch (Exception $e) {
  error_log('Mail failed: ' . $mail->ErrorInfo);
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Sendefehler — bitte direkt anrufen']);
}
```

## HTML-Formular (vollständig, barrierefrei)

```html
<form id="contact-form" novalidate>
  <!-- CSRF Token (PHP: session_start() + token generieren) -->
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

  <div class="form__group">
    <label for="cf-name">Name <span aria-label="Pflichtfeld">*</span></label>
    <input type="text" id="cf-name" name="name" required aria-required="true"
           autocomplete="name" aria-describedby="cf-name-error">
    <span id="cf-name-error" role="alert" aria-live="polite" class="form__error"></span>
  </div>

  <div class="form__group">
    <label for="cf-email">E-Mail <span aria-label="Pflichtfeld">*</span></label>
    <input type="email" id="cf-email" name="email" required aria-required="true"
           autocomplete="email" aria-describedby="cf-email-error">
    <span id="cf-email-error" role="alert" aria-live="polite" class="form__error"></span>
  </div>

  <div class="form__group">
    <label for="cf-message">Nachricht <span aria-label="Pflichtfeld">*</span></label>
    <textarea id="cf-message" name="message" rows="5" required aria-required="true"
              aria-describedby="cf-message-error"></textarea>
    <span id="cf-message-error" role="alert" aria-live="polite" class="form__error"></span>
  </div>

  <div class="form__group form__group--checkbox">
    <input type="checkbox" id="cf-privacy" name="datenschutz" value="1" required>
    <label for="cf-privacy">
      Ich stimme der Verarbeitung gemäß <a href="/datenschutz">Datenschutzerklärung</a> zu. *
    </label>
  </div>

  <!-- Honeypot (für Menschen unsichtbar) -->
  <div aria-hidden="true" style="position:absolute;left:-9999px">
    <input type="text" name="website" tabindex="-1" autocomplete="off">
  </div>

  <button type="submit" class="btn btn--primary">
    <span class="btn__text">Nachricht senden</span>
    <span class="btn__loading" hidden aria-hidden="true">Wird gesendet...</span>
  </button>
  <div id="form-status" role="status" aria-live="polite"></div>
</form>
```
