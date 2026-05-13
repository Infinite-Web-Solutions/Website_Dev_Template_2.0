# Performance — Core Web Vitals

## Zielwerte

| Metrik | Gut | Bedeutung |
|--------|-----|-----------|
| LCP | < 2.5s | Largest Contentful Paint — wann ist das Hauptelement sichtbar? |
| CLS | < 0.1 | Cumulative Layout Shift — springt etwas beim Laden? |
| INP | < 100ms | Interaction to Next Paint — wie schnell reagiert die Seite? |

## HTML `<head>` — Reihenfolge und Preloading (Pflicht)

```html
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#[--color-primary]">
  <title>...</title>

  <!-- 1. Kritische Fonts vorab laden (DSGVO: nur lokale Fonts!) -->
  <link rel="preload" href="/assets/fonts/display.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="/assets/fonts/body.woff2" as="font" type="font/woff2" crossorigin>

  <!-- 2. Hero-Bild vorab laden (nur above-the-fold!) -->
  <link rel="preload" as="image" href="/assets/img/hero.webp">

  <!-- 3. CSS (render-blocking — so klein wie möglich halten) -->
  <link rel="stylesheet" href="/assets/css/normalize.css">
  <link rel="stylesheet" href="/assets/css/variables.css">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
```

## CSS Design Tokens (variables.css)

```css
:root {
  /* Farben */
  --color-primary:    #[aus Konzept];
  --color-secondary:  #[aus Konzept];
  --color-accent:     #[aus Konzept];
  --color-text:       #1a1a1a;
  --color-text-muted: #666;
  --color-bg:         #fff;
  --color-bg-alt:     #f8f8f6;
  --color-border:     #e0e0e0;
  --color-focus:      #005fcc;
  --color-success:    #10b981;
  --color-error:      #ef4444;

  /* Typografie */
  --font-display: '[Display Font]', Georgia, serif;
  --font-body:    '[Body Font]', system-ui, sans-serif;
  --font-mono:    'Fira Code', monospace;

  /* Spacing (8px-Grid) */
  --space-1: 0.5rem;   /* 8px */
  --space-2: 1rem;     /* 16px */
  --space-3: 1.5rem;   /* 24px */
  --space-4: 2rem;     /* 32px */
  --space-6: 3rem;     /* 48px */
  --space-8: 4rem;     /* 64px */
  --space-12: 6rem;    /* 96px */
  --space-16: 8rem;    /* 128px */

  /* Layout */
  --max-width: 1200px;
  --content-width: 720px;
  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 16px;

  /* Effekte */
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
  --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
  --transition: 200ms ease;
}
```

## Bilder — vollständige Regeln

### Hero-Bild (above the fold)
```html
<!-- fetchpriority="high" + kein lazy + width/height Pflicht -->
<img
  src="/assets/img/hero.webp"
  alt="Beschreibender Alt-Text"
  width="1200"
  height="600"
  fetchpriority="high"
>
```

### Alle anderen Bilder — responsive mit WebP + Fallback
```html
<picture>
  <!-- WebP für moderne Browser -->
  <source
    type="image/webp"
    srcset="/assets/img/team-400.webp 400w,
            /assets/img/team-800.webp 800w,
            /assets/img/team-1200.webp 1200w"
    sizes="(max-width: 768px) 100vw, 50vw"
  >
  <!-- JPG/PNG als Fallback für ältere Browser -->
  <img
    src="/assets/img/team-800.jpg"
    alt="Das 5-köpfige Team von Muster GmbH im Büro"
    width="800"
    height="600"
    loading="lazy"
  >
</picture>
```

**Faustregel Bildgrößen erzeugen:**
- 400px — Mobile
- 800px — Tablet / Standard
- 1200px — Desktop

Wenn nur eine Bildgröße vorhanden: mindestens WebP + width/height + loading="lazy".

### Dekorative Bilder (kein Informationswert)
```html
<img src="trennlinie.webp" alt="" role="presentation" width="1200" height="4" loading="lazy">
```

## Fonts — @font-face korrekt

```css
@font-face {
  font-family: 'DisplayFont';
  src: url('/assets/fonts/display.woff2') format('woff2');
  font-weight: 400 700;
  font-style: normal;
  font-display: swap; /* Verhindert FOIT, zeigt Fallback-Font bis geladen */
}
```

`font-display: swap` ist Pflicht — verhindert unsichtbaren Text während des Ladens (FOIT = Flash of Invisible Text).

## JavaScript — minimal und defer

```html
<!-- Immer am Body-Ende ODER mit defer im Head -->
<script src="/assets/js/main.js" defer></script>
```

```javascript
// Intersection Observer für Einblend-Animationen
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('is-visible');
      observer.unobserve(e.target);
    }
  });
}, { threshold: 0.1 });
document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));
```

## Layout Shift vermeiden (CLS)

```css
/* Immer Seitenverhältnis für Mediencontainer reservieren */
.img-wrapper {
  aspect-ratio: 16 / 9;
  overflow: hidden;
}

/* Skeleton-Placeholder während Bilder laden */
img {
  background-color: var(--color-bg-alt);
}
```

Regeln:
- Alle `<img>` brauchen `width` und `height` Attribute (auch wenn CSS sie überschreibt)
- Keine Inhalte die nach dem ersten Paint einspringen (Ads, Banners, late-loaded fonts)
- Keine `position: absolute` Elemente die den Fluss verschieben

## Performance-Checkliste

- [ ] `<link rel="preload">` für Hero-Bild und kritische Fonts
- [ ] Hero-Bild: `fetchpriority="high"`, kein `loading="lazy"`
- [ ] Alle anderen Bilder: `loading="lazy"` + `width` + `height`
- [ ] Bilder als WebP, idealerweise mit `<picture>` + `srcset`
- [ ] `font-display: swap` in allen `@font-face` Regeln
- [ ] JS: `defer` oder am Body-Ende
- [ ] Keine ungenutzten CSS-Regeln
- [ ] Keine externen CDN-Abhängigkeiten
- [ ] Gzip/Deflate in .htaccess aktiviert
- [ ] Browser-Caching in .htaccess konfiguriert (1 Jahr für Assets)
