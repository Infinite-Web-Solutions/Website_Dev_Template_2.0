# DSGVO — Datenschutz-Compliance

## Grundprinzip: Cookieless by Default

Wenn die Website kein Tracking, keine eingebetteten Dienste und keine externen Ressourcen hat:
→ Kein Cookie-Banner nötig. Das ist unser Ziel.

## Schriften — lokal hosten (Pflicht)

VERBOTEN:
```html
<link href="https://fonts.googleapis.com/css2?family=Roboto" rel="stylesheet">
```

### Font-Setup Prozess (Phase 3 — vor dem ersten CSS)

**Schritt 1: Font-Dateien beschaffen**

Option A — Google Fonts herunterladen (empfohlen für Produktion):
1. fonts.google.com → Schrift wählen → "Download family"
2. ZIP entpacken → `.ttf` Dateien vorhanden
3. TTF → WOFF2 konvertieren: fontsquirrel.com/tools/webfont-generator
4. `.woff2` Dateien nach `public/assets/fonts/` kopieren

Option B — Bunny Fonts CDN (DSGVO-konform, EU-Server):
```html
<!-- Erlaubt für Entwicklung und Produktion — EU-Server, keine US-Datenübertragung -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet">
```

**Schritt 2: @font-face nur mit tatsächlich vorhandenen Dateien**

```css
/* NUR wenn die Datei physisch in public/assets/fonts/ liegt! */
/* url() ist relativ zur CSS-Datei (assets/css/) → ../fonts/ */
@font-face {
  font-family: 'Inter';
  src: url('../fonts/inter-regular.woff2') format('woff2');
  font-weight: 400;
  font-display: swap;
}
@font-face {
  font-family: 'Inter';
  src: url('../fonts/inter-bold.woff2') format('woff2');
  font-weight: 700;
  font-display: swap;
}
```

**Kritisch:** Wird eine Fontdatei im CSS referenziert die nicht existiert → 404-Fehler → sichtbarer Layout-Fehler. Vor dem Commit prüfen: existiert jede referenzierte `.woff2` Datei wirklich?

## Google Maps — Two-Click-Lösung

Fertige, barrierefreie Vorlage: **`templates/two-click-map.html`** (kopieren,
nicht neu bauen). Prinzip: Vor dem Klick wird **keine** Google-Ressource
geladen — erst der Button injiziert das iframe.

Pflicht-Details:
- Embed-URL muss vom Host `https://www.google.com/maps/embed?pb=…` kommen —
  genau dieser Host steht in der CSP (`frame-src`, siehe `.htaccess.template`).
  **Nicht** `maps.google.com/maps?q=…` verwenden — anderer Host, von der CSP
  blockiert.
- Ohne Two-Click-Map: `frame-src` in der CSP auf `'none'` setzen.
- `onclick`-Handler vermeiden (CSP verbietet Inline-Skripte) — Event-Listener
  in `main.js`, wie im Template gezeigt.

```html
<!-- Kern der Vorlage (vollständig in templates/two-click-map.html) -->
<button class="button button--primary js-load-map"
        data-map-url="https://www.google.com/maps/embed?pb=DEINE_EMBED_URL">
  Karte laden
</button>
```

## Impressum & Datenschutz — Pflichtseiten

Beide immer anlegen. Ohne Kundeninhalt: Platzhalter mit deutlichem Hinweis.

## Kontaktformular — Pflicht-Checkbox

```html
<div class="form__group form__group--checkbox">
  <!-- name="privacy" — muss zu $_POST['privacy'] in server/contact.php passen! -->
  <input type="checkbox" id="privacy" name="privacy" required aria-required="true">
  <label for="privacy">
    Ich stimme der Verarbeitung meiner Daten gemäß
    <a href="datenschutz.html" target="_blank" rel="noopener">Datenschutzerklärung</a> zu.
    <span aria-label="Pflichtfeld">*</span>
  </label>
</div>
```

## Externe Dienste — Übersicht

| Dienst | Status | Alternative |
|--------|--------|------------|
| Google Fonts | ❌ extern | Lokal / Bunny Fonts |
| Google Analytics | ❌ ohne Consent | Matomo self-hosted |
| Google Maps | ⚠️ Two-Click | OpenStreetMap |
| YouTube Embed | ⚠️ Two-Click | youtube-nocookie.com |
| reCAPTCHA | ⚠️ Consent | Honeypot (unser Standard) |
| Facebook Pixel | ❌ ohne Consent | Keines |
