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
@font-face {
  font-family: 'Inter';
  src: url('/assets/fonts/inter-regular.woff2') format('woff2');
  font-weight: 400;
  font-display: swap;
}
@font-face {
  font-family: 'Inter';
  src: url('/assets/fonts/inter-bold.woff2') format('woff2');
  font-weight: 700;
  font-display: swap;
}
```

**Kritisch:** Wird eine Fontdatei im CSS referenziert die nicht existiert → 404-Fehler → sichtbarer Layout-Fehler. Vor dem Commit prüfen: existiert jede referenzierte `.woff2` Datei wirklich?

## Google Maps — Two-Click-Lösung

```html
<div class="map-consent" id="map-wrapper">
  <p>Diese Karte wird von Google Maps bereitgestellt.
     Mit dem Laden stimmen Sie der <a href="/datenschutz">Datenschutzerklärung</a> von Google zu.</p>
  <button onclick="loadMap()" class="btn">Karte laden</button>
</div>
<script>
function loadMap() {
  document.getElementById('map-wrapper').innerHTML =
    '<iframe src="https://maps.google.com/maps?q=..." loading="lazy" width="100%" height="400"></iframe>';
}
</script>
```

## Impressum & Datenschutz — Pflichtseiten

Beide immer anlegen. Ohne Kundeninhalt: Platzhalter mit deutlichem Hinweis.

## Kontaktformular — Pflicht-Checkbox

```html
<div class="form__group form__group--checkbox">
  <input type="checkbox" id="datenschutz" name="datenschutz" value="1" required>
  <label for="datenschutz">
    Ich stimme der Verarbeitung meiner Daten gemäß
    <a href="/datenschutz" target="_blank" rel="noopener">Datenschutzerklärung</a> zu. *
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
