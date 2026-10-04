# Serhan Demirel WordPress theme

WordPress version of the one-page site at [serhandemirel.com](https://serhandemirel.com) (source: [serhanco/serhandemirel.comLP](https://github.com/serhanco/serhandemirel.comLP)).

## Install

1. Zip this folder: `cd wp-content/themes && zip -r serhandemirel.zip serhandemirel -x 'serhandemirel/node_modules/*'`
2. In wp-admin go to **Appearance → Themes → Add New → Upload Theme** and upload the zip, then activate it.
3. Install and activate the **Serhan Demirel Core** plugin from `wp-content/plugins/serhandemirel-core` (see its README). It holds the content types and the contact form.
4. Under **Settings → General** set Site Title to `Serhan Demirel` and Tagline to `Digital Solutions Provider` (the browser tab title is built from these).

The front page renders whenever "Your homepage displays" is left on "Your latest posts" or set to any static page.

## Layout

| File | What it holds |
| --- | --- |
| `header.php` | `<head>`, `wp_head()`, opening `<body>`, navigation |
| `front-page.php` | The one-page layout, assembled from the template parts |
| `footer.php` | Floating contact bar, project modal, `wp_footer()` |
| `index.php` | Fallback for posts and other pages |
| `template-parts/` | `nav`, `hero`, `word-slot`, `expertise`, `brands`, `contact`, `sticky-contact`, `project-modal` |
| `functions.php` | Asset loading (compiled Tailwind, Inter, GSAP, ScrollTrigger, Lenis), meta/OG tags |
| `inc/content.php` | Data for the front-page sections, read from the Core plugin with built-in fallbacks |
| `single-sd_project.php`, `archive-sd_project.php` | Project page and `/work/` list (also industry and service archives) |
| `home.php`, `single.php` | Insights list (posts page and archives) and article page |
| `inc/options.php` | Settings fields, defaults and `sd_opt()`; per-language values with fallback to the default language |
| `inc/options-page.php` | The **Serhan Demirel** settings screen in wp-admin |
| `inc/tracking.php` | GTM, GA4, Meta Pixel, LinkedIn, Clarity, extra code, Consent Mode defaults, per-page switch |
| `inc/brands.php` | Brand logo list and order for the marquee |
| `assets/js/` | The page scripts that used to be inline in `index.html`; `tracking.js` pushes `sd_form_submit`, `sd_whatsapp_click` and `sd_email_click` to `dataLayer` and stores the UTM campaign in an `sd_utm` cookie |

## Contact form

The form posts to `admin-ajax.php`; the **Serhan Demirel Core** plugin saves each submission under **Messages** in wp-admin and emails the site's admin address (needs working mail on the host, e.g. an SMTP plugin). Without the plugin the form does not work and wp-admin shows a warning.

## Settings

wp-admin → **Serhan Demirel** has tabs for General (SEO and sharing image), Hero, Word slot, Sections (show/hide and headings), Contact, Form and Tracking. With Polylang, texts marked with a globe are saved per language; an empty field shows the default-language text. Tracking codes are skipped for logged-in editors (unless enabled), in previews and on pages with **Turn off tracking codes on this page** ticked. When Yoast, Rank Math, AIOSEO or SEOPress is active, the theme leaves the description and social tags to it.

## Styles

Tailwind is compiled into `assets/css/tailwind.css`, which is committed so the theme works without a build. After adding or changing Tailwind classes in templates or scripts, rebuild it:

```
cd wp-content/themes/serhandemirel
npm install
npm run build
```

## Work and Insights pages

Projects live at `/work/` and `/work/<project>/`. For an Insights list, create a page called Insights and pick it as **Posts page** under **Settings → Reading** (with a static homepage); for article URLs like `/insights/<post>/`, set **Settings → Permalinks** to Custom `/insights/%postname%/`.

## Menu and languages

- **Menu:** assign a menu to "Main menu" under Appearance → Menus. A menu item's
  "Title Attribute" can hold an emoji, shown next to it in the mobile menu.
  Without a menu, the navbar links to the visible front-page sections.
- **Languages:** with Polylang active, a language switcher appears in the navbar
  and the mobile menu. With Polylang, assign one menu per language.
- **Translations:** every theme string is in `languages/serhandemirel.pot`
  (the plugin's in `serhandemirel-core/languages/serhandemirel-core.pot`).
  Translate with Poedit or Loco Translate into `languages/<locale>.po/.mo`
  (e.g. `fr_FR.mo`). Regenerate the .pot after changing strings:
  `wp i18n make-pot . languages/serhandemirel.pot --exclude=node_modules,assets/css`.
