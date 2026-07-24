# Website Generator — Template

Dieses Repository ist ein GitHub Template für die automatisierte Erstellung von
statischen Kunden-Websites mit Claude Code.

## Verwendung

1. "Use this template" → neues Repository für Kundenprojekt
2. Claude Code mit Repository verbinden
3. `/neues-projekt` eingeben und Projektdaten ausfüllen
4. Claude startet automatisch mit Recherche → Konzept → Build → Review

## Slash Commands

| Command | Beschreibung |
|---------|-------------|
| `/neues-projekt` | Startet ein neues Kundenprojekt — öffnet das Projektdaten-Template (Kunde, Branche, Seiten, Design, Features). Claude liest automatisch CLAUDE.md + alle Knowledge-Dateien und beginnt mit Phase 1 Recherche. |

> Slash Commands liegen in `.claude/commands/` und werden von Claude Code automatisch erkannt.

## Enthaltene Knowledge-Dateien

| Datei | Inhalt |
|-------|--------|
| _knowledge/01-research.md | Business-Recherche Methodik |
| _knowledge/02-design-principles.md | Anti-Generic Design Regeln |
| _knowledge/03-accessibility.md | WCAG 2.1 AA / BFSG 2025 |
| _knowledge/04-dsgvo.md | DSGVO-Compliance |
| _knowledge/05-security.md | Security Headers & PHP-Sicherheit |
| _knowledge/06-forms.md | Formulare & PHPMailer SMTP |
| _knowledge/07-performance.md | Core Web Vitals & CSS Tokens |
| _knowledge/08-seo-geo.md | SEO & GEO (llms.txt, facts.html) |
| _knowledge/09-motion-design.md | Optionale Motion-Libraries (Lenis, GSAP, Vanta) |

## Stack
- HTML5 + CSS3 + Vanilla JS
- PHP + PHPMailer für Formulare
- all-inkl Hosting (Apache)
- Kein Framework, kein CMS

## Automatisches Deployment

Bei jedem Push auf `main` wird `public/` automatisch per **FTPS** auf den Server deployt.
Nur geänderte Dateien werden übertragen (differenzieller Sync via State-Datei).

### GitHub Secrets konfigurieren

**Einmalig als Organization Secrets setzen** → gelten automatisch für alle Repos:

> GitHub → Organization Settings → Secrets and variables → Actions

| Secret | Inhalt | Beispiel |
|--------|--------|---------|
| `FTP_SERVER` | FTP-Hostname | `w01234ab.kasserver.com` |
| `FTP_USER` | FTP-Benutzername | `w01234ab` |
| `FTP_PASS` | FTP-Passwort | |
| `FTP_BASE_DIR` | Webroot-Pfad von infinitewebsolutions.de | `/www/htdocs/username/infinitewebsolutions.de` |

**Pro Kundenprojekt: keine weiteren Secrets nötig.**
Der Repo-Name wird automatisch als Unterverzeichnis verwendet.

→ Repo `muster-gmbh` deployt nach: `infinitewebsolutions.de/muster-gmbh/`

> Kein GitHub Org? Dann die 4 Secrets einmalig pro Repo setzen — Werte sind immer identisch.

### Hinweis: mail-config.php

`server/mail-config.php` ist in `.gitignore` und wird **nie** deployed.
SMTP-Zugangsdaten einmalig manuell per FTP auf den Server hochladen.
