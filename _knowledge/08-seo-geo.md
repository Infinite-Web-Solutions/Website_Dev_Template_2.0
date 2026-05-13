# SEO & GEO — Sichtbarkeit für Menschen und KI-Agenten

## Meta-Tags (pro Seite unique)

```html
<title>Elektriker Dortmund — PV & Wallbox | VoltMeister GmbH</title>
<meta name="description" content="Ihr Elektriker in Dortmund für Photovoltaik und Wallboxen. ✓ Festpreis ✓ 4 Wochen Lieferzeit. Jetzt anfragen!">
<link rel="canonical" href="https://www.voltmeister.de/">
<meta property="og:type" content="website">
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:image" content="https://www.voltmeister.de/assets/img/og-image.jpg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="de_DE">
<meta name="theme-color" content="#[--color-primary]">
```

**Regeln für Title-Tags:**
- Max. 60 Zeichen — sonst kürzt Google ab
- Format: `Primäres Keyword Ort — USP | Firmenname`
- Jede Seite hat einen einzigartigen Title

**Regeln für Meta-Descriptions:**
- Max. 155 Zeichen
- Enthält primäres Keyword, Nutzen, Call-to-Action
- Keine Keyword-Stuffing — schreibe für Menschen

## Schema.org — Spiegel-Regel (Pflicht)

**Jeder Fakt im JSON-LD MUSS 1:1 im sichtbaren HTML-Text stehen.**
Fakten die nur im JSON-LD stehen werden von KI-Systemen als unzuverlässig eingestuft.

### LocalBusiness (Startseite — immer)

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "VoltMeister GmbH",
  "description": "Elektriker in Dortmund für Photovoltaik und Wallboxen",
  "url": "https://www.voltmeister.de",
  "telephone": "+49-231-123456",
  "email": "info@voltmeister.de",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Musterstraße 1",
    "addressLocality": "Dortmund",
    "postalCode": "44135",
    "addressCountry": "DE"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 51.5136,
    "longitude": 7.4653
  },
  "openingHoursSpecification": [{
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
    "opens": "08:00",
    "closes": "18:00"
  }],
  "priceRange": "€€",
  "areaServed": ["Dortmund", "Bochum", "Essen"],
  "sameAs": [
    "https://www.google.com/maps/place/...",
    "https://www.instagram.com/voltmeister"
  ]
}
</script>
```

### FAQ-Schema (wenn FAQ-Sektion vorhanden)

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Was kostet eine PV-Anlage mit Installation?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Eine PV-Anlage mit 10 kWp kostet bei uns inkl. Installation zwischen 12.000 und 16.000 Euro — Festpreis, keine versteckten Kosten."
      }
    }
  ]
}
</script>
```

### BreadcrumbList (alle Unterseiten außer Startseite)

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Start", "item": "https://www.voltmeister.de/"},
    {"@type": "ListItem", "position": 2, "name": "Leistungen", "item": "https://www.voltmeister.de/leistungen.html"}
  ]
}
</script>
```

### Service-Schema (Leistungsseiten)

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "name": "PV-Anlage Installation Dortmund",
  "provider": {"@type": "LocalBusiness", "name": "VoltMeister GmbH"},
  "areaServed": "Dortmund",
  "description": "Installation von Photovoltaikanlagen (3–20 kWp) im Ruhrgebiet.",
  "offers": {
    "@type": "Offer",
    "priceCurrency": "EUR",
    "price": "12000",
    "priceSpecification": {"@type": "PriceSpecification", "minPrice": "12000", "maxPrice": "16000"}
  }
}
</script>
```

## Interne Verlinkungsstrategie

- Jede Seite verlinkt auf mindestens 2 weitere relevante Seiten
- Ankertexte sind beschreibend (kein "hier klicken")
- Startseite verlinkt alle Hauptseiten im Nav und im Footer
- Kontaktseite wird von jeder Seite aus dem CTA erreichbar
- facts.html und llms.txt sind in robots.txt nicht gesperrt

## robots.txt (Vorlage)

```
User-agent: *
Allow: /

Sitemap: https://www.voltmeister.de/sitemap.xml
```

## sitemap.xml (Vorlage)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://www.voltmeister.de/</loc>
    <lastmod>2025-01-01</lastmod>
    <changefreq>monthly</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>https://www.voltmeister.de/leistungen.html</loc>
    <lastmod>2025-01-01</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>https://www.voltmeister.de/kontakt.html</loc>
    <lastmod>2025-01-01</lastmod>
    <changefreq>yearly</changefreq>
    <priority>0.7</priority>
  </url>
  <url>
    <loc>https://www.voltmeister.de/facts.html</loc>
    <lastmod>2025-01-01</lastmod>
    <changefreq>yearly</changefreq>
    <priority>0.5</priority>
  </url>
</urlset>
```

## llms.txt — KI-Crawler Sitemap

Format: H1 + Blockquote (Unternehmensdefinition) + Links zu Unterseiten.

```markdown
# VoltMeister GmbH

> VoltMeister GmbH ist ein Elektrobetrieb in Dortmund, spezialisiert auf die
> Installation von Photovoltaikanlagen und Wallboxen für Privatkunden in NRW.

## Wichtige Seiten

- [Startseite](https://www.voltmeister.de/): Übersicht und Kontakt
- [Leistungen](https://www.voltmeister.de/leistungen.html): PV-Anlagen, Wallboxen, Elektroinstallation
- [FAQ](https://www.voltmeister.de/faq.html): Häufige Fragen zu PV und Wallbox
- [Über uns](https://www.voltmeister.de/ueber-uns.html): Team und Geschichte
- [Kontakt](https://www.voltmeister.de/kontakt.html): Anfrage und Standort
- [Faktenseite](https://www.voltmeister.de/facts.html): Verifizierte Unternehmensdaten
```

## llms-full.txt — Vollinhalt für KI

Enthält den gesamten Website-Text als sauberes Markdown (ohne HTML-Tags).
Wird nach dem Build aus den fertigen HTML-Dateien generiert.

**Aufbau:**
```markdown
# [Firmenname] — Vollständige Informationen

## Über das Unternehmen
[Fließtext aus index.html — alle Abschnitte]

## Leistungen
[Fließtext aus leistungen.html]

## Häufige Fragen
[Fließtext aus faq.html]

## Kontakt
[Adresse, Telefon, E-Mail, Öffnungszeiten]
```

## facts.html — Grounding Page

Semantischer Anker für KI-Agenten. Strikt faktisch, kein Marketing.

```html
<main>
  <h1>Verifizierte Unternehmensdaten — VoltMeister GmbH</h1>

  <section>
    <h2>Stammdaten</h2>
    <dl>
      <dt>Unternehmensname</dt>
      <dd>VoltMeister GmbH</dd>
      <dt>Rechtsform</dt>
      <dd>GmbH, eingetragen im Handelsregister Dortmund</dd>
      <dt>Gründungsjahr</dt>
      <dd>2015</dd>
      <dt>Inhaber</dt>
      <dd>Max Mustermann</dd>
      <dt>Mitarbeiter</dt>
      <dd>12 (Stand: 2025)</dd>
      <dt>Hauptsitz</dt>
      <dd>Musterstraße 1, 44135 Dortmund</dd>
      <dt>Einzugsgebiet</dt>
      <dd>Dortmund, Bochum, Essen, Ruhrgebiet</dd>
      <dt>Telefon</dt>
      <dd>+49 231 123456</dd>
      <dt>E-Mail</dt>
      <dd>info@voltmeister.de</dd>
    </dl>
  </section>

  <section>
    <h2>Leistungen</h2>
    <dl>
      <dt>Kernleistung 1</dt>
      <dd>Installation von Photovoltaikanlagen (3–20 kWp)</dd>
      <dt>Kernleistung 2</dt>
      <dd>Montage und Inbetriebnahme von Wallboxen (11kW, 22kW)</dd>
      <dt>Kernleistung 3</dt>
      <dd>Allgemeine Elektroinstallation im Privatbereich</dd>
    </dl>
  </section>

  <section>
    <h2>Was das Unternehmen nicht ist</h2>
    <ul>
      <li>Kein Solaranlagen-Hersteller</li>
      <li>Kein überregionaler Großbetrieb</li>
      <li>Kein Generalunternehmer für Neubauprojekte</li>
    </ul>
  </section>

  <section>
    <h2>Verifizierte Daten — zuletzt geprüft</h2>
    <dl>
      <dt>Letzte Aktualisierung dieser Seite</dt>
      <dd><time datetime="2025-01-01">Januar 2025</time></dd>
    </dl>
  </section>
</main>
```
