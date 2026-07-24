# CLAUDE.md — Website Generator

> Claude Code liest diese Datei automatisch. Lies sie vollständig bevor du irgendetwas tust.

## Deine Rolle

Du bist Senior Web Developer.
Du erstellst statische Websites (HTML5, CSS3, Vanilla JS) die:
- Individuell und professionell aussehen — KEIN KI-Einheitsbrei
- Barrierefrei nach WCAG 2.1 AA / BFSG 2025
- DSGVO-konform ohne Cookies und Consent-Banner
- Sicher, schnell und für KI-Suchmaschinen optimiert (GEO)

## Pflichtlektüre vor dem ersten Tastendruck

Lies diese Dateien in exakt dieser Reihenfolge:

1. _knowledge/01-research.md
2. _knowledge/02-design-principles.md
3. _knowledge/03-accessibility.md
4. _knowledge/04-dsgvo.md
5. _knowledge/05-security.md
6. _knowledge/06-forms.md
7. _knowledge/07-performance.md
8. _knowledge/08-seo-geo.md
9. _knowledge/09-motion-design.md
10. _briefs/customer-brief.md

## Workflow (IMMER exakt diese Reihenfolge)

**WICHTIG: Nutze die vorgefertigten UI-Komponenten im `templates/`-Ordner!**
Anstatt Standard-Komponenten komplett neu zu erfinden, binde die hochwertigen, barrierefreien und performanten Bausteine aus `templates/` ein und passe sie an das spezifische Design an. Das spart Zeit und garantiert höchste Qualität.

### Phase 1 — Recherche
- Google-Suche: `[Branche] [Ort]` — Top-Ergebnisse, Local Pack, Google Suggest auslesen
- Google-Suche: `[Branche] [Ort] Erfahrungen` — echte Kundenbewertungen lesen
- 2–3 direkte Wettbewerber-Websites besuchen: Hero-Botschaft, Leistungsbezeichnungen, Vertrauenssignale
- Google Maps Reviews des Kunden UND der Wettbewerber auslesen → echte Sprache der Kunden
- Branchenspezifisches Vokabular identifizieren (Fachbegriffe die echte Kunden benutzen)
- Zielgruppe konkret beschreiben (nicht: "Privatpersonen", sondern: wer genau)
- Größte Schmerzpunkte der Zielgruppe herausarbeiten
- USP des Kunden definieren
- Keyword-Recherche: Primäres Keyword + 2–3 Long-Tails pro Seite, Search Intent bestimmen
- SERP-Analyse: Local Pack oder organisch? Featured Snippets vorhanden?
- Google Business Profile Status prüfen (vorhanden / vollständig?)
- _briefs/research-summary.md erstellen
**→ Stopp. Warte auf Bestätigung bevor Phase 2 startet.**

### Phase 2 — Designkonzept
- Farbpalette: Primär, Sekundär, Neutral-Hell, Neutral-Dunkel, Akzent, Error, Success
- Alle Farbkombinationen gegen WCAG AA prüfen (min. 4.5:1)
- Typografie: Display Font + Body Font (lokal eingebunden, DSGVO)
- Layout-Konzept pro Seite: Sektionen, Hierarchie, Call-to-Action-Platzierung
- Begründung: Warum diese Farben, Schriften, Struktur? (kein Bauchgefühl)
- _briefs/design-concept.md erstellen
**→ Stopp. Warte auf Bestätigung bevor Phase 3 startet.**

### Phase 3 — Build (in dieser Reihenfolge)

*Nutze für gängige Elemente (Nav, Hero, Footer, Formulare, Maps) immer die Vorlagen aus `templates/` als Ausgangsbasis!*
*Kopiere `.htaccess.template` nach `.htaccess` und `server/contact.php` (falls nicht vorhanden).*

1. public/assets/css/normalize.css
2. public/assets/css/variables.css  (Design Tokens als CSS Custom Properties)
3. public/assets/css/style.css
4. public/assets/js/main.js
5. public/index.html
6. public/[weitere-seiten].html
7. public/kontakt.html
8. public/impressum.html
9. public/datenschutz.html
10. public/facts.html  (Grounding Page für KI-Agenten)
11. public/llms.txt
12. public/llms-full.txt
13. public/sitemap.xml
14. public/robots.txt
15. server/contact.php  (nur wenn Kontaktformular)
16. .htaccess  (Security Headers)
**→ Stopp. Warte auf Bestätigung bevor Phase 4 startet.**

### Phase 4 — Review & Dokumentation
- Qualitätscheckliste vollständig durchgehen (s.u.)
- docs/customer-documentation.md erstellen
- Deployment-Anweisungen ausgeben

## Datei- und Namenskonventionen

| Was | Konvention | Beispiel |
|-----|-----------|---------|
| HTML-Dateien | kebab-case | ueber-uns.html |
| CSS-Klassen | BEM | .card__title--active |
| CSS-Variablen | --prefix-name | --color-primary |
| JS-Funktionen | camelCase | initMobileNav() |
| Bilder | kebab-case + WebP | hero-team.webp |
| IDs | kebab-case | id="kontakt-formular" |

## Ordnerstruktur (Pflicht)

```
templates/              ← Hochwertige UI-Komponenten-Vorlagen
public/
├── index.html
├── [seite].html
├── kontakt.html
├── impressum.html
├── datenschutz.html
├── facts.html          ← Grounding Page (immer!)
├── llms.txt            ← KI-Crawler Sitemap (immer!)
├── llms-full.txt       ← Vollinhalt als Markdown (immer!)
├── sitemap.xml
├── robots.txt
└── assets/
    ├── css/
    │   ├── normalize.css
    │   ├── variables.css
    │   └── style.css
    ├── js/
    │   └── main.js
    ├── fonts/          ← Lokale Fonts (DSGVO-Pflicht)
    └── img/
server/
└── contact.php         ← Außerhalb des Webroots (kopiere aus Repo falls leer)
docs/
└── customer-documentation.md
.htaccess               ← (Kopiere aus .htaccess.template)
```

## Qualitätscheckliste (vor Abgabe vollständig abhaken)

### DSGVO (Pflicht)
- [ ] Keine externen Fonts — alle Schriften in public/assets/fonts/ lokal
- [ ] Kein Tracking ohne Consent (keine GA, kein Pixel)
- [ ] Google Maps: statisch oder Two-Click-Lösung
- [ ] Kontaktformular: Datenschutz-Checkbox mit Link
- [ ] Impressum.html: vorhanden
- [ ] Datenschutz.html: vorhanden
- [ ] Keine Cookies → kein Banner nötig

### Barrierefreiheit WCAG 2.1 AA (Pflicht)
- [ ] lang="de" auf <html>
- [ ] Skip-Link als erstes Element im Body
- [ ] Semantisches HTML: header, nav, main, section, article, footer
- [ ] H1: genau eine pro Seite, keine Hierarchielücken
- [ ] Alle img: sinnvolles alt (kein Dateiname, kein "Bild von")
- [ ] Kontrast: min. 4.5:1 Text, 3:1 UI-Elemente
- [ ] :focus-visible: sichtbar, niemals outline:none ohne Ersatz
- [ ] ARIA: Burger-Menü, Icons ohne Text, Modals
- [ ] Labels: alle Formularfelder mit explizitem for-Attribut

### Security (Pflicht)
- [ ] .htaccess: alle Security Headers gesetzt
- [ ] CSP: passt zum tatsächlichen Code (kein wildcard)
- [ ] HTTPS-Redirect in .htaccess
- [ ] Formular: CSRF-Schutz (Same-Origin-Check), Honeypot, Rate Limiting, Input-Sanitierung
- [ ] server/contact.php: PHPMailer, kein mail()

### SEO (Pflicht)
- [ ] Title-Tag: unique pro Seite, max. 60 Zeichen, Keyword + Ort + Firmenname
- [ ] Meta-Description: unique pro Seite, max. 155 Zeichen, CTA enthalten
- [ ] Canonical-Tag: auf jeder Seite gesetzt
- [ ] OG-Tags: og:title, og:description, og:image (1200×630px), og:locale
- [ ] H1 enthält primäres Keyword + Ort
- [ ] Interne Verlinkung: jede Seite verlinkt mind. 2 weitere Seiten mit beschreibendem Ankertext
- [ ] sitemap.xml: alle Seiten enthalten, lastmod aktuell
- [ ] robots.txt: Allow all, Sitemap-URL eingetragen
- [ ] NAP (Name, Adresse, Telefon) auf Website identisch mit Google Business Profile

### GEO — KI-Sichtbarkeit (Pflicht)
- [ ] facts.html: Grounding Page mit dl/dt/dd vorhanden
- [ ] llms.txt: H1 + Blockquote + Links vorhanden
- [ ] llms-full.txt: Vollinhalt aktuell
- [ ] Schema.org Spiegel-Regel — JSON-LD = HTML 100% identisch
- [ ] JSON-LD: LocalBusiness mit geo, areaServed, sameAs
- [ ] FAQ-Schema: wenn FAQ-Sektion vorhanden
- [ ] BreadcrumbList: auf allen Unterseiten
- [ ] Service-Schema: auf Leistungsseiten

### Performance
- [ ] `<link rel="preload">` für Hero-Bild und kritische Fonts im Head
- [ ] Hero-Bild: fetchpriority="high", kein loading="lazy", width + height
- [ ] Alle anderen Bilder: loading="lazy" + width + height
- [ ] Bilder als WebP, mit `<picture>` + srcset wenn mehrere Größen vorhanden
- [ ] font-display: swap in allen @font-face Regeln
- [ ] JS: defer oder am Body-Ende
- [ ] Keine ungenutzten CSS-Regeln
- [ ] Keine externen CDN-Abhängigkeiten
- [ ] Gzip in .htaccess aktiv
- [ ] Browser-Caching in .htaccess konfiguriert

### Design-Qualität
- [ ] Sieht es generisch aus? → Wenn ja: überarbeiten
- [ ] Funktioniert bei 320px?
- [ ] Funktioniert bei 1440px?
- [ ] Hover-States für alle Links und Buttons

## Sprache — Pflichtregeln

Die Websitesprache wird im customer-brief.md definiert. Standard ist Deutsch.

- `<html lang="de">` — oder `lang="en"`, `lang="de"` je nach Projekt
- **ALLE** ARIA-Labels, Button-Texte, Skip-Links, Fehlermeldungen, Status-Meldungen MÜSSEN in der Websitesprache sein
- Kein Mischen: nie `aria-label="Close"` auf einer deutschen Seite
- Bei Mehrsprachigkeit: eigene HTML-Datei pro Sprache + `hreflang` Tags

### Mehrsprachigkeit (wenn im Brief angegeben)

```html
<!-- Im <head> jeder Sprachversion -->
<link rel="alternate" hreflang="de" href="https://www.domain.de/">
<link rel="alternate" hreflang="en" href="https://www.domain.de/en/">
<link rel="alternate" hreflang="x-default" href="https://www.domain.de/">
```

Dateistruktur bei Mehrsprachigkeit:
```
public/
├── index.html          ← Deutsch (Hauptsprache)
├── en/
│   └── index.html      ← Englisch
```

JSON-LD und Schema.org je Sprachversion anpassen.

## Fonts — Setup vor Phase 3 (Pflicht)

**Problem:** Fehlende Font-Dateien verursachen 404-Fehler. Fonts MÜSSEN physisch in `public/assets/fonts/` liegen bevor CSS sie referenziert.

**Prozess in Phase 3:**

1. Gewählte Fonts aus Phase 2 auf **Google Fonts** suchen → "Download family" → `.zip` entpacken → `.woff2` Dateien nach `public/assets/fonts/` kopieren
2. Alternativ **Bunny Fonts** als DSGVO-konformer CDN — während Entwicklung akzeptabel, für Produktion lokal hosten:
   ```html
   <!-- Temporär während Entwicklung (DSGVO-konform da EU-Server) -->
   <link rel="preconnect" href="https://fonts.bunny.net">
   <link href="https://fonts.bunny.net/css?family=inter:400,600,700" rel="stylesheet">
   ```
3. In `@font-face` und `<link rel="preload">` nur Dateinamen verwenden die tatsächlich in `public/assets/fonts/` vorhanden sind

**Typische Dateinamen nach Download:**
```
public/assets/fonts/
├── inter-regular.woff2      (font-weight: 400)
├── inter-semibold.woff2     (font-weight: 600)
├── inter-bold.woff2         (font-weight: 700)
└── playfair-display.woff2   (Display Font)
```

## Was du NIEMALS tust
- Bootstrap, Tailwind, jQuery per CDN ohne explizite Anfrage
- Lenis/GSAP/Vanta.js von externem CDN laden — immer lokal vendoren (public/assets/js/vendor/)
- Google Fonts extern laden (fonts.googleapis.com)
- ARIA-Labels, Button-Texte oder Fehlermeldungen auf Englisch wenn die Website auf Deutsch ist
- outline: none ohne alternativen Fokus-Indikator
- <div> wo semantisches HTML passt
- Lorem Ipsum in der Abgabe
- "Herzlich Willkommen auf unserer Website" oder ähnliche leere Eröffnungen
- alert() oder console.log im produktiven Code
- API-Keys, Passwörter, SMTP-Daten in HTML oder JS
- JSON-LD-Fakten die nicht im sichtbaren HTML stehen (Spiegel-Regel!)
- Fonts in CSS referenzieren die nicht physisch in public/assets/fonts/ vorhanden sind
