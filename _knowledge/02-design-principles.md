# Design-Prinzipien — Gegen den KI-Einheitsbrei

## Die Kernfrage vor dem ersten CSS

> "Sieht das aus wie jede andere Website in dieser Branche?"
> Wenn ja — neu anfangen.

## Verbotene Klischees

### Layout-Klischees (niemals)
- Hero: Bild links, Text rechts, blauer Gradient-Button
- Drei gleichhohe Spalten: Icon, H3, zwei Zeilen Text
- Testimonials: Anführungszeichen-Icon, Sternchen, rundes Foto
- CTA: dunkler Hintergrund, zentriert, ein Button

### Copy-Klischees (niemals)
- "Herzlich Willkommen auf unserer Website"
- "Ihr zuverlässiger Partner für..."
- "Wir helfen Ihnen dabei..."
- "Qualität, die überzeugt"
→ Konkret und spezifisch statt abstrakt und nichtssagend

### Design-Klischees (niemals)
- Gradient lila → pink / blau → lila
- Stock-Foto: Hände-die-sich-schütteln, Glühbirne auf Notizbuch
- Flat Icons die zu nichts passen
- "Modernes" Design das überall gleich aussieht

## Was stattdessen funktioniert

### Typografie als Designelement
- Große mutige Überschriften: font-size: clamp(2.5rem, 6vw, 5rem)
- Unerwartete Schriftkombinationen (Slab Serif + Mono, Condensed + Humanist)
- Fluid Typography überall: clamp(min, preferred, max)

### Farbe mit Charakter
- Eine starke Primärfarbe + viel Neutral
- Akzentfarbe sparsam (max. 10% der Fläche)
- Alle Kombinationen auf WCAG AA testen

### Layout-Überraschungen
- Asymmetrie statt perfekte Symmetrie
- Full-Bleed Elemente die aus dem Grid ausbrechen
- Starker Kontrast: großzügige Leeräume neben dichten Bereichen

### Authentische Details
- Echte Zahlen: "47 Projekte" statt "viele Projekte"
- Spezifische Sprache: "Hochzeitsfotos Berlin Mitte" statt "professionelle Fotografie"
- Charakter durch Details: ungewöhnliche Hover-States, sichtbares Grid

## Schriften lokal einbinden (DSGVO-Pflicht)

Quellen:
- Google Fonts herunterladen → public/assets/fonts/ hosten
- Bunny Fonts API (EU-Server, datenschutzfreundlich)
- Font Squirrel (lizenzfrei)

```css
/* url() ist relativ zur CSS-Datei (assets/css/) → ../fonts/ */
@font-face {
  font-family: 'DisplayFont';
  src: url('../fonts/display.woff2') format('woff2');
  font-weight: 400 700;
  font-style: normal;
  font-display: swap;
}
```

## Responsive Design — Mobile First

```css
/* Fluid Typography */
font-size: clamp(1rem, 2.5vw, 1.25rem);

/* Fluid Spacing */
padding: clamp(1rem, 5vw, 3rem);

/* Breakpoints */
/* 320px  — kleinstes Smartphone */
/* 768px  — Tablet */
/* 1024px — kleiner Desktop */
/* 1440px — Standard Desktop */
```
