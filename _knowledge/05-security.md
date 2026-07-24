# Security — Headers & Formular-Sicherheit

## .htaccess — kanonische Vorlage: `.htaccess.template` (Repo-Root)

Die vollständige, getestete Konfiguration liegt in **`.htaccess.template`**.
In Phase 3 nach `public/.htaccess` kopieren — nicht neu erfinden, nicht
hier aus einem Snippet abtippen.

Was die Vorlage abdeckt:

| Block | Zweck |
|-------|-------|
| HTTPS- & www-Redirect | Erzwingt eine kanonische URL |
| Security Headers | nosniff, X-Frame-Options, Referrer-Policy, Permissions-Policy, HSTS |
| Content Security Policy | `script-src 'self'` — kein `unsafe-inline`, kein Wildcard |
| `Options -Indexes` | Kein Verzeichnis-Browsing |
| FilesMatch / Files | Sperrt .env, .log, .sql, … und mail-config.php |
| mod_deflate / mod_expires | Gzip + Browser-Caching (1 Jahr Assets, 1 h HTML) |

**Pro Projekt anpassen:**
- CSP `frame-src https://www.google.com/maps/` nur behalten, wenn die
  Two-Click-Map im Einsatz ist — sonst auf `frame-src 'none'` setzen.
- CSP muss zum tatsächlichen Code passen: keine Quelle erlauben, die die
  Website nicht nutzt (Checkliste: „kein wildcard").
- `X-XSS-Protection` steht bewusst auf `"0"` — der alte Browser-Auditor ist
  fehleranfällig und von OWASP zur Deaktivierung empfohlen. Nicht auf
  `"1; mode=block"` „zurückkorrigieren".

## PHP-Formular-Sicherheit — kanonische Implementierung: `server/contact.php`

Der vollständige, funktionsfähige Handler liegt in **`server/contact.php`**.
Nicht duplizieren — bei Bedarf dort anpassen. Diese Schutzschichten sind
Pflicht und bereits implementiert:

1. **Nur POST** — alle anderen Methoden → 405
2. **CSRF-Schutz per Same-Origin-Check** — Origin- (Fallback: Referer-)
   Header muss zum eigenen Host passen. Session-Token sind bei statischem
   HTML nicht einbettbar, deshalb dieser Ansatz (form-action 'self' in der
   CSP ergänzt ihn browserseitig).
3. **Honeypot** — verstecktes Feld `website`; ausgefüllt → Erfolg vortäuschen,
   nichts senden
4. **Rate Limiting** — 1 Anfrage / 60 s pro Session; Zeitstempel erst nach
   erfolgreichem Versand setzen
5. **Input-Sanitierung** — `htmlspecialchars(…, ENT_QUOTES, 'UTF-8')`,
   `FILTER_SANITIZE_EMAIL`, Längen-Validierung mit `mb_strlen`
6. **Header-Injection-Schutz** — `\r`/`\n` in Name oder E-Mail → 400
7. **PHPMailer über SMTP** — niemals `mail()`; Fehler nur per `error_log`,
   nie Interna an den Client

SMTP-Zugangsdaten liegen ausschließlich in `server/mail-config.php`
(gitignored, per `.htaccess` gesperrt, manuell deployt — siehe 06-forms.md).

## Grundregeln

- Keine API-Keys, Passwörter oder SMTP-Daten in HTML/JS (siehe CLAUDE.md)
- Fehlermeldungen an Nutzer sind generisch — Details nur ins `error_log`
- Jede neue externe Ressource erfordert eine bewusste CSP-Erweiterung —
  im Zweifel lokal vendoren (09-motion-design.md)
