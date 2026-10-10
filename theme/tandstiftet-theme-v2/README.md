# Tändstiftet MC — WordPress Theme

**Version:** 1.0.0  
**Requires WordPress:** 6.0+  
**Requires PHP:** 8.0+  
**Text Domain:** `tandstiftet`

Classic vintage motorsport aesthetic — think retro race poster, dark asphalt, gold accents.

---

## Installation

1. Copy the `tandstiftet-theme/` folder to `/wp-content/themes/tandstiftet/`
2. Activate via **Appearance → Themes**
3. WordPress will flush rewrite rules automatically on activation

---

## First-time Setup Checklist

### Pages to create
Create these pages in **Pages → Add New** and assign the correct template:

| Page title (Swedish) | Slug | Template |
|---|---|---|
| Startsida | `/` | **Startsida** |
| Förare & Lag | `forare-och-lag` | **Förare & Lag** |
| Tävlingar | *(auto — archive)* | *(none)* |
| Galleri | *(auto — archive)* | *(none)* |
| Kontakt & Medlemskap | `kontakt` | **Kontakt & Medlemskap** |
| Regler | `regler` | **Informationssida** |
| Om klubben | `om-oss` | **Informationssida** |
| Säkerhet | `sakerhet` | **Informationssida** |
| Historia | `historia` | **Informationssida** |

→ Set **Startsida** as static front page under **Settings → Reading**

### Menus
Go to **Appearance → Menus** and create a menu assigned to **Primär navigation**.
Recommended structure:

```
Start
Nyheter
Förare & Lag
  └ Division 1 (#division-1)
  └ Vuxna (#vuxna)
  └ Ungdomar (#ungdomar)
Tävlingar
Galleri
Info
  └ Om klubben
  └ Regler
  └ Säkerhet
  └ Historia
Kontakt
```

For the anchor sub-items (Division 1 etc.), use **Custom Links** with URLs like `[page-url]#division-1`.

### Rider Divisions (Taxonomies)
Go to **Förare → Division / Klass** and create:
- `division-1` — Division 1
- `vuxen` — Vuxna
- `ungdom` — Ungdomar
- `veteran` — Veteraner *(optional)*

Then assign each rider profile to the correct division.

### Team Leader
Create a Rider profile, fill in **Lagledare / Tränare** = checked, and set **Ledarroll** (e.g. *Lagledare*, *Tränare*, *Lagkapten*). Assign to the `division-1` taxonomy. This person will appear in the featured leader card at the top of the Division 1 section.

### Rider ordering
Set **Visningsordning** (meta field) to control the order riders appear:
- `1` = first, `2` = second, etc.
- Team leader is always shown separately regardless of order number

### Customizer settings
**Appearance → Customize → Tändstiftet MC:**
- Hero image, title, subtitle, buttons
- Stats bar (founded year, number of riders, etc.)
- Team/division description texts
- Social media URLs (Facebook, Instagram, YouTube)
- Contact info (phone, email, address)

### Sponsors
Add sponsors under **Sponsorer → Add New**:
- Title = sponsor name
- Featured image = logo (transparent PNG preferred)
- Meta: `tmc_sponsor_url` = website URL
- Meta: `tmc_sponsor_order` = display order

### Event types
Go to **Evenemang → Tävlingstyp** and create tags like:
- Speedway
- Flattrack
- Träning
- Uppvisning

---

## Custom Post Types Summary

| CPT | Slug | Archive URL | Purpose |
|---|---|---|---|
| `tmc_event` | `evenemang` | `/evenemang/` | Races & events |
| `tmc_rider` | `forare` | *(no archive)* | Rider profiles |
| `tmc_sponsor` | *(private)* | *(none)* | Sponsor logos |
| `tmc_gallery` | `galleri` | `/galleri/` | Photo albums |

---

## Gallery Albums
Each `tmc_gallery` post is an album. Inside the post content, use the built-in **WordPress Gallery block** to add photos. The gallery page template renders them with hover effects and a lightbox.

---

## i18n / Translations

The theme ships with a `.pot` stub at `languages/tandstiftet.pot`.

Generate the full `.pot` with WP-CLI:
```bash
wp i18n make-pot . languages/tandstiftet.pot --domain=tandstiftet
```

Create a Swedish translation:
```bash
cp languages/tandstiftet.pot languages/sv_SE.po
# edit sv_SE.po in Poedit or similar
msgfmt languages/sv_SE.po -o languages/sv_SE.mo
```

WordPress will auto-load `sv_SE.mo` when the site language is set to Swedish.

---

## Child Theme

For site-specific customizations, create a child theme:
```
/wp-content/themes/tandstiftet-child/
  style.css   (with Template: tandstiftet)
  functions.php
```

---

## Requirements

- **Fonts:** Google Fonts (Bebas Neue + Barlow Condensed + Barlow) — loaded via CSS `@import`. If GDPR compliance requires local hosting, download from [google-webfonts-helper.herokuapp.com](https://google-webfonts-helper.herokuapp.com) and update the `@import` in `style.css`.
- **Email:** The contact/membership forms use `wp_mail()`. Ensure your hosting SMTP is configured, or install a plugin like WP Mail SMTP.

---

## File Structure

```
tandstiftet-theme/
├── style.css                          ← Theme header + all styles
├── functions.php                      ← CPTs, meta boxes, helpers
├── header.php
├── footer.php
├── index.php                          ← Blog archive
├── single.php                         ← Single blog post
├── page.php                           ← Default page
├── front-page.php                     ← Homepage
├── 404.php
├── archive-tmc_event.php              ← Events calendar archive
├── single-tmc_event.php               ← Single event
├── archive-tmc_gallery.php            ← Gallery album list
├── single-tmc_gallery.php             ← Single album
├── templates/
│   ├── page-forare-och-lag.php        ← Riders & Teams (anchor page)
│   ├── page-info.php                  ← Flexible info page
│   └── page-kontakt.php               ← Contact & Membership forms
├── inc/
│   └── customizer.php                 ← Theme Customizer options
├── assets/
│   └── js/
│       └── main.js                    ← Nav, lightbox, scroll animations
├── languages/
│   └── tandstiftet.pot                ← Translation template
└── README.md
```
