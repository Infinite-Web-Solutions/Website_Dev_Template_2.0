# Motion Design — Lenis, GSAP, Vanta.js (optional!)

## Grundprinzip: Kein Standard, sondern Ausnahme

**Default für jedes Projekt: KEINE dieser Libraries.**

Motion-Libraries bedeuten zusätzliches JS-Gewicht, GPU-Last und Bewegungseffekte —
alles Dinge, die der Performance-Pflicht (07-performance.md) und der
Barrierefreiheits-Pflicht (03-accessibility.md) dieses Templates widersprechen.
Einsatz **nur**, wenn `_briefs/customer-brief.md` es explizit vorsieht
(z. B. Stil "modern/bold", "Tech-Startup", "Premium-Feel gewünscht").

Für einfache Einblend-Animationen reicht der Intersection Observer aus
`07-performance.md` — dafür braucht es keine Library.

## Entscheidungsbaum

```
Steht im Brief explizit "Premium-Feel", "Tech-Startup",
"aufwendige Animationen" o. ä.?
│
├─ NEIN → Keine Motion-Library. CSS-Transitions + Intersection Observer.
│
└─ JA → Was genau wird gebraucht?
    │
    ├─ Nur weiches Scrollgefühl
    │   → Lenis allein (~18 kB)
    │
    ├─ Scroll-gebundene Animationen (Elemente bewegen sich mit dem Scrollen,
    │   Pinning, Parallax, Timelines)
    │   → GSAP + ScrollTrigger (~115 kB)
    │   → optional zusätzlich Lenis für Smooth Scroll
    │
    └─ Animierter WebGL-Hintergrund im Hero (Waves, Net, Birds, Fog)
        → Vanta.js + Three.js r134 (~630 kB gzipped deutlich weniger,
          aber trotzdem das schwerste Geschütz!)
        → Nur wenn der Brief es rechtfertigt UND kein Hero-Foto
          die bessere Wahl ist. Immer zuerst hinterfragen.
```

**Kombinationsregel:** Jede Library einzeln begründen. „Wir haben GSAP, also
nehmen wir auch Vanta" ist keine Begründung.

## Vendoring — niemals CDN (Pflicht)

Alle Libraries liegen lokal in `public/assets/js/vendor/`:

```
public/assets/js/vendor/
├── lenis.min.js            (Lenis 1.3.25)
├── gsap.min.js             (GSAP 3.15.0)
├── ScrollTrigger.min.js    (GSAP 3.15.0)
├── three.min.js            (Three.js r134 / 0.134.0 — NICHT aktualisieren!)
├── vanta.waves.min.js      (Vanta 0.5.24)
├── vanta.net.min.js        (Vanta 0.5.24)
├── vanta.birds.min.js      (Vanta 0.5.24)
└── vanta.fog.min.js        (Vanta 0.5.24)
```

- **Kein CDN** (unpkg, jsdelivr, cdnjs, …): verstößt gegen
  „Keine externen CDN-Abhängigkeiten" (07-performance.md) und überträgt bei
  jedem Seitenaufruf die Besucher-IP an Dritte (DSGVO, 04-dsgvo.md).
- In die Abgabe kommen **nur die tatsächlich genutzten** Dateien —
  ungenutzte Vendor-Skripte aus `public/` des Projekts entfernen.
- Eingesetzte Versionen ins Projekt-`docs/customer-documentation.md`
  übernehmen (Vorlage: `docs/customer-documentation.md` im Repo).
- GSAP-Lizenz: GreenSock „No Charge License" — kostenlos, aber nicht GPL.
  Für individuelle Kundenprojekte unkritisch; nur relevant, falls ein
  Theme/Plugin über WP.org distribuiert werden soll.

## ⚠️ Vanta ↔ Three.js Versionskopplung (Pflicht-Warnhinweis)

**Three.js ist fest auf r134 (npm: 0.134.0) gepinnt. Niemals auf „latest"
aktualisieren!** Neuere Three.js-Versionen entfernen/ändern APIs, auf die
Vanta 0.5.x aufbaut (u. a. Geometry-Klassen) — die Effekte brechen dann
kommentarlos oder werfen Laufzeitfehler. Ein Three.js-Update ist nur
zusammen mit einer Vanta-Version erlaubt, die es nachweislich unterstützt,
und nach Sichttest aller genutzten Effekte.

Hinweis: `vanta.birds` bringt zusätzliche Abhängigkeiten mit — nach dem
Einbinden immer in der Browser-Konsole prüfen, dass keine Fehler auftreten.

## Pflicht-Pattern: `prefers-reduced-motion` (vor JEDER Initialisierung)

Menschen mit vestibulären Störungen aktivieren „Bewegung reduzieren" im
Betriebssystem. Dieses Signal MUSS respektiert werden — WCAG 2.1 AA
(2.3.3 Animation from Interactions). Keine Motion-Library darf ohne
diesen Check initialisiert werden:

```javascript
// Pflicht-Guard am Anfang jeder Motion-Initialisierung
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

function initMotion() {
  if (prefersReducedMotion.matches) {
    // Kein Lenis, kein GSAP-Tween, kein Vanta — statischer Fallback bleibt sichtbar
    return;
  }
  initSmoothScroll();
  initScrollAnimations();
  initVantaHero();
}

initMotion();

// Auch auf Änderung zur Laufzeit reagieren (User schaltet Setting um)
prefersReducedMotion.addEventListener('change', (e) => {
  if (e.matches) {
    destroyMotion(); // lenis.destroy(), ScrollTrigger.killAll(), vantaEffect.destroy()
  }
});
```

Ergänzend im CSS (fängt auch reine CSS-Animationen ab):

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

**Testen:** DevTools → Rendering → „Emulate CSS media feature
prefers-reduced-motion: reduce" → Seite neu laden → es darf sich nichts
bewegen, und der statische Fallback muss vollwertig aussehen.

## Standard-Einbindung

### Lenis (Smooth Scroll, ~18 kB)

```html
<!-- Am Body-Ende, immer mit defer -->
<script src="/assets/js/vendor/lenis.min.js" defer></script>
<script src="/assets/js/main.js" defer></script>
```

```javascript
// main.js
function initSmoothScroll() {
  const lenis = new Lenis({
    autoRaf: true,          // Lenis übernimmt den requestAnimationFrame-Loop
    duration: 1.1,
    anchors: true           // #-Sprungziele (Skip-Link!) weich anfahren
  });
  return lenis;
}
```

**Barrierefreiheit:** Lenis darf Tastatur-Scrolling (Pfeiltasten, Bild↑/↓,
Pos1/Ende) und den Skip-Link nicht brechen — nach Einbau mit Tastatur testen.

### GSAP + ScrollTrigger (~115 kB)

```html
<!-- Reihenfolge: gsap vor ScrollTrigger, beide defer, vor main.js -->
<script src="/assets/js/vendor/gsap.min.js" defer></script>
<script src="/assets/js/vendor/ScrollTrigger.min.js" defer></script>
<script src="/assets/js/main.js" defer></script>
```

```javascript
// main.js
function initScrollAnimations() {
  gsap.registerPlugin(ScrollTrigger);

  gsap.utils.toArray('[data-animate]').forEach((el) => {
    gsap.from(el, {
      opacity: 0,
      y: 40,
      duration: 0.8,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: el,
        start: 'top 85%',
        once: true
      }
    });
  });
}
```

**Wichtig bei Kombination mit Lenis:** ScrollTrigger über den Lenis-Scroll
aktualisieren, sonst laufen Trigger asynchron:

```javascript
const lenis = new Lenis({ autoRaf: true });
lenis.on('scroll', ScrollTrigger.update);
```

**CLS-Regel:** Niemals Elemente per GSAP initial verstecken, die ohne JS
sichtbar sein müssen — bei deaktiviertem JS oder Reduced Motion muss der
gesamte Inhalt sichtbar bleiben (kein `opacity: 0` im CSS als Startzustand).

### Vanta.js (WebGL-Hintergrund, nur Hero!)

**Regeln (Pflicht):**
- Vanta läuft **ausschließlich im Hero-Bereich** — nie seitenweit,
  nie auf mehreren Sektionen, nie auf Unterseiten ohne Grund.
- Scripts **nur auf Seiten laden, die den Effekt nutzen** (i. d. R. nur
  `index.html`), immer mit `defer`.
- Es wird genau **eine** `vanta.[effect].min.js` eingebunden — die des im
  Brief gewählten Effekts.
- Der Hero braucht einen **statischen Fallback** (CSS-Hintergrundfarbe/
  -verlauf oder Bild), der ohne JS, bei Reduced Motion und bei fehlendem
  WebGL vollwertig aussieht.
- Drei Pflicht-Checks vor Init: `prefers-reduced-motion`, WebGL verfügbar,
  Effekt bei Verlassen der Seite/Sektion zerstören (Akku!).

```html
<!-- Nur auf der Seite mit Vanta-Hero, am Body-Ende -->
<script src="/assets/js/vendor/three.min.js" defer></script>
<script src="/assets/js/vendor/vanta.waves.min.js" defer></script>
<script src="/assets/js/main.js" defer></script>
```

```javascript
// main.js
function initVantaHero() {
  const heroEl = document.getElementById('hero-vanta');
  if (!heroEl || typeof VANTA === 'undefined') return null;

  const vantaEffect = VANTA.WAVES({
    el: heroEl,
    mouseControls: true,
    touchControls: false,   // Touch-Scrolling nicht kapern
    gyroControls: false,
    minHeight: 200,
    minWidth: 200,
    color: 0x1a1a2e,        // an --color-primary anlehnen
    waveHeight: 15,
    waveSpeed: 0.6,
    zoom: 0.9
  });

  // GPU/Akku schonen: Effekt pausieren, wenn Hero nicht sichtbar
  const observer = new IntersectionObserver(([entry]) => {
    if (!vantaEffect.renderer) return;
    entry.isIntersecting ? vantaEffect.play?.() : vantaEffect.pause?.();
  }, { threshold: 0 });
  observer.observe(heroEl);

  return vantaEffect;
}
```

Vollständige Kopiervorlage inkl. Reduced-Motion-Fallback:
`templates/hero-vanta.html`.

## CSP anpassen (05-security.md)

Die vendorten Dateien laufen unter `script-src 'self'` — **keine**
CSP-Änderung nötig. Genau deshalb ist Vendoring Pflicht: Ein CDN würde
eine Aufweichung der CSP erfordern.

## Checkliste Motion (nur wenn Libraries im Einsatz)

- [ ] Einsatz explizit durch den Brief gedeckt (nicht „sieht cool aus")
- [ ] Alle Libraries lokal aus `public/assets/js/vendor/`, kein CDN
- [ ] Ungenutzte Vendor-Dateien aus der Abgabe entfernt
- [ ] `prefers-reduced-motion` vor jeder Initialisierung geprüft UND getestet
- [ ] Statischer Fallback ohne JS / ohne WebGL vollwertig
- [ ] Vanta nur im Hero, nur eine Effekt-Datei, Scripts mit `defer`
- [ ] Three.js bleibt r134 — nicht „mitaktualisiert"
- [ ] Tastatur-Scrolling und Skip-Link funktionieren (Lenis!)
- [ ] Versionsnummern in docs/customer-documentation.md dokumentiert
- [ ] Lighthouse-Check nach Einbau: LCP < 2.5s, CLS < 0.1 weiterhin erfüllt
