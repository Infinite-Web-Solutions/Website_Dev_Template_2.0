# Barrierefreiheit — WCAG 2.1 AA / BFSG 2025

## Rechtliche Grundlage
- BFSG gilt ab 28.06.2025 für private Anbieter
- WCAG 2.1 Level AA ist unser Mindeststandard für ALLE Projekte

## Sprachprinzip (Pflicht)

**Alle barrierefreiheitsrelevanten Texte MÜSSEN in der Websitesprache sein.**

| Element | Deutsch | FALSCH (Englisch) |
|---------|---------|-------------------|
| Skip-Link | `Zum Hauptinhalt springen` | `Skip to main content` |
| Burger-Menü öffnen | `aria-label="Menü öffnen"` | `aria-label="Open menu"` |
| Burger-Menü schließen | `aria-label="Menü schließen"` | `aria-label="Close menu"` |
| Pflichtfeld | `aria-label="Pflichtfeld"` | `aria-label="Required"` |
| Fehlermeldung | `Bitte geben Sie Ihren Namen ein` | `Please enter your name` |
| Ladehinweis | `Wird gesendet…` | `Sending…` |
| Footer-Nav | `aria-label="Footer-Navigation"` | `aria-label="Footer navigation"` |
| Breadcrumb | `aria-label="Brotkrümelpfad"` | `aria-label="Breadcrumb"` |

Bei mehrsprachigen Projekten: `lang`-Attribut der jeweiligen Seite bestimmt die Sprache aller ARIA-Texte.

## HTML-Grundgerüst (Pflicht)

```html
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seitentitel — Firmenname</title>
</head>
<body>
  <a href="#main-content" class="skip-link">Zum Hauptinhalt springen</a>
  <header role="banner">
    <nav aria-label="Hauptnavigation">
      <ul>
        <li><a href="/" aria-current="page">Home</a></li>
      </ul>
      <button aria-expanded="false" aria-controls="mobile-menu" aria-label="Menü öffnen">
        <span aria-hidden="true">&#9776;</span>
      </button>
    </nav>
  </header>
  <main id="main-content">
    <h1>Einzige H1 pro Seite</h1>
  </main>
  <footer role="contentinfo">
    <nav aria-label="Footer-Navigation">...</nav>
  </footer>
</body>
</html>
```

## Kontrast-Mindestanforderungen

| Element | Mindest | Prüftool |
|---------|---------|---------|
| Text < 18pt | 4.5:1 | WebAIM Contrast Checker |
| Text ≥ 18pt fett | 3:1 | |
| UI-Elemente, Icons | 3:1 | |

## Fokus-Styles (niemals entfernen)

```css
:focus-visible {
  outline: 3px solid var(--color-focus);
  outline-offset: 3px;
  border-radius: 2px;
}
.skip-link {
  position: absolute;
  top: -100%;
  left: 1rem;
  background: var(--color-primary);
  color: #fff;
  padding: 0.5rem 1rem;
  z-index: 9999;
}
.skip-link:focus { top: 0; }
```

## Bilder

```html
<!-- Informativ: beschreibendes alt -->
<img src="team.webp" alt="Das 5-köpfige Team von Muster GmbH im Büro" width="800" height="600" loading="lazy">

<!-- Dekorativ: leeres alt -->
<img src="trennlinie.webp" alt="" role="presentation" width="1200" height="4">

<!-- Icon ohne sichtbaren Text -->
<button aria-label="Menü schließen">
  <svg aria-hidden="true" focusable="false">...</svg>
</button>
```

## Formulare (barrierefrei)

```html
<div class="form__group">
  <label for="name">Name <span aria-label="Pflichtfeld">*</span></label>
  <input type="text" id="name" name="name" required aria-required="true"
         aria-describedby="name-error" autocomplete="name">
  <span id="name-error" role="alert" aria-live="polite" class="form__error"></span>
</div>
```

## Tastaturzugänglichkeit

```javascript
const burger = document.querySelector('[aria-controls="mobile-menu"]');
const menu = document.getElementById('mobile-menu');
burger.addEventListener('click', () => {
  const isOpen = burger.getAttribute('aria-expanded') === 'true';
  burger.setAttribute('aria-expanded', String(!isOpen));
  menu.hidden = isOpen;
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && burger.getAttribute('aria-expanded') === 'true') {
    burger.setAttribute('aria-expanded', 'false');
    menu.hidden = true;
    burger.focus();
  }
});
```

## Motion & Animationen (WCAG 2.3.3)

Bewegungseffekte können bei vestibulären Störungen Schwindel und Übelkeit
auslösen. Die Systemeinstellung „Bewegung reduzieren" MUSS respektiert werden —
per `@media (prefers-reduced-motion: reduce)` im CSS und per
`window.matchMedia('(prefers-reduced-motion: reduce)')` vor jeder
JS-Animations-Initialisierung. Details und Pflicht-Pattern: `09-motion-design.md`.

- [ ] Bei Motion-Libraries (Lenis/GSAP/Vanta): `prefers-reduced-motion` implementiert und getestet
