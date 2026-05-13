# Security — Headers & Formular-Sicherheit

## .htaccess (vollständig, Apache / all-inkl)

```apache
# HTTP → HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# www → non-www
RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^ https://%1%{REQUEST_URI} [R=301,L]

<IfModule mod_headers.c>
  Header always set X-Content-Type-Options "nosniff"
  Header always set X-Frame-Options "SAMEORIGIN"
  Header always set X-XSS-Protection "1; mode=block"
  Header always set Referrer-Policy "strict-origin-when-cross-origin"
  Header always set Permissions-Policy "geolocation=(), microphone=(), camera=(), payment=()"
  Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
  # CSP — ANPASSEN je nach genutzten Ressourcen
  Header always set Content-Security-Policy "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-src 'none'; object-src 'none'; base-uri 'self'; form-action 'self'"
</IfModule>

Options -Indexes

<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|sql|bak|env|php)$">
  <FilesMatch "\.php$">
    # Nur in öffentlichen Verzeichnissen blockieren
  </FilesMatch>
  Order Allow,Deny
</FilesMatch>

<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/css application/javascript font/woff2
</IfModule>

<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType font/woff2 "access plus 1 year"
  ExpiresByType text/html "access plus 1 hour"
</IfModule>
```

## PHP Formular-Sicherheit

```php
<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

// CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  http_response_code(403); die('Sicherheitsfehler');
}

// Honeypot
if (!empty($_POST['website'])) { http_response_code(200); die(); }

// Rate Limiting
if (isset($_SESSION['last_submit']) && time() - $_SESSION['last_submit'] < 60) {
  http_response_code(429); die(json_encode(['success' => false, 'message' => 'Bitte warten']));
}
$_SESSION['last_submit'] = time();

// Input sanitisieren
$name    = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

// Header Injection verhindern
if (preg_match('/[\r\n]/', $name . $email)) { http_response_code(400); exit; }

// Validierung
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { http_response_code(400); exit; }
if (strlen($message) < 10 || strlen($message) > 5000) { http_response_code(400); exit; }
```
