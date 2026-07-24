# Formulare & SMTP-Zustellung

## Architektur-Entscheidung

Standard: PHP + all-inkl SMTP via PHPMailer.
Kein externer Dienst, keine US-Server, DSGVO-einfach.

## Der Handler liegt fertig im Repo: `server/contact.php`

Die vollständige, funktionsfähige Implementierung ist **`server/contact.php`** —
in Phase 3 kopieren, nicht neu schreiben. Sie enthält alle Pflicht-Schutzschichten
(POST-only, Same-Origin-CSRF, Honeypot, Rate Limit, Sanitierung,
Header-Injection-Schutz, PHPMailer). Details zur Sicherheit: `05-security.md`.

**Feldnamen — verbindlich (HTML ↔ PHP müssen exakt übereinstimmen):**

| Feld | `name`-Attribut | Pflicht |
|------|-----------------|---------|
| Name | `name` | ja |
| E-Mail | `email` | ja |
| Nachricht | `message` | ja |
| Datenschutz-Checkbox | `privacy` | ja |
| Honeypot (versteckt) | `website` | muss leer bleiben |

> Historische Warnung: Nicht `name="datenschutz"` verwenden — `server/contact.php`
> liest `$_POST['privacy']`. Ein abweichender Feldname lässt die Zustimmung
> immer fehlschlagen.

## SMTP-Konfiguration: `server/mail-config.php`

Liegt **nicht** im Repo (`.gitignore` + per `.htaccess` gesperrt), wird einmalig
manuell per FTP hochgeladen. Gibt ein Array zurück:

```php
<?php
// server/mail-config.php — NIEMALS committen!
return [
  'host' => 'w01xxxxx.kasserver.com',
  'user' => 'm0xxxxxx',
  'pass' => 'GEHEIM',
  'port' => 587,                       // STARTTLS
  'from' => 'website@kundendomain.de', // Absender = authentifiziertes Postfach
  'to'   => 'info@kundendomain.de',    // Empfänger der Anfragen
];
```

Wichtig: `from` muss das authentifizierte SMTP-Postfach sein (SPF/DKIM),
sonst landet die Mail im Spam. Die Antwortadresse des Absenders wird über
`addReplyTo()` gesetzt — so kann direkt auf die Anfrage geantwortet werden.

## PHPMailer beschaffen

- **Composer (empfohlen):** `composer require phpmailer/phpmailer` →
  `vendor/` im Site-Root (außerhalb `public/`).
- **Ohne Composer:** PHPMailer-Release entpacken nach `server/phpmailer/src/`.

`server/contact.php` erkennt beide Varianten automatisch.

## HTML-Formular

Die fertige, barrierefreie Vorlage ist **`templates/contact-form.html`**.
Eckpunkte (siehe auch `03-accessibility.md`):

```html
<form action="server/contact.php" method="POST" class="form js-contact-form" novalidate>

  <!-- Honeypot: für Menschen unsichtbar, per CSS ausgelagert (nicht display:none,
       das würde Screenreader-Bots ebenfalls „helfen"). CSS:
       .form__group--hidden { position:absolute; left:-9999px; } -->
  <div class="form__group form__group--hidden" aria-hidden="true">
    <label for="website">Website (nicht ausfüllen)</label>
    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
  </div>

  <div class="form__group">
    <label for="name">Name <span aria-label="Pflichtfeld">*</span></label>
    <input type="text" id="name" name="name" required aria-required="true"
           autocomplete="name" aria-describedby="name-error">
    <span id="name-error" role="alert" aria-live="polite" class="form__error"></span>
  </div>

  <div class="form__group">
    <label for="email">E-Mail <span aria-label="Pflichtfeld">*</span></label>
    <input type="email" id="email" name="email" required aria-required="true"
           autocomplete="email" aria-describedby="email-error">
    <span id="email-error" role="alert" aria-live="polite" class="form__error"></span>
  </div>

  <div class="form__group">
    <label for="message">Nachricht <span aria-label="Pflichtfeld">*</span></label>
    <textarea id="message" name="message" rows="5" required aria-required="true"
              minlength="10" maxlength="5000" aria-describedby="message-error"></textarea>
    <span id="message-error" role="alert" aria-live="polite" class="form__error"></span>
  </div>

  <div class="form__group form__group--checkbox">
    <input type="checkbox" id="privacy" name="privacy" required aria-required="true">
    <label for="privacy">
      Ich stimme der Verarbeitung gemäß
      <a href="datenschutz.html" target="_blank" rel="noopener">Datenschutzerklärung</a> zu.
      <span aria-label="Pflichtfeld">*</span>
    </label>
  </div>

  <button type="submit" class="button button--primary">Nachricht senden</button>
  <div class="form__status js-form-status" role="status" aria-live="polite" hidden></div>
</form>
```

## Progressive Enhancement — JS-Versand

Das AJAX-Beispiel steht als Kommentar in `templates/contact-form.html` und
gehört nach `public/assets/js/main.js`. Regeln:

- `e.preventDefault()` erst nach erfolgreicher Feature-Prüfung — ohne JS muss
  das Formular klassisch an `server/contact.php` absenden können.
- Serverseitige Fehlermeldungen (`data.message`) im Status-Element anzeigen,
  nicht nur ein generisches „Fehler".
- Statusmeldungen in der Websitesprache (`Wird gesendet…`, nicht `Sending…`).
