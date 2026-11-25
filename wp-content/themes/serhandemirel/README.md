# Serhan Demirel WordPress theme

WordPress version of the one-page site at [serhandemirel.com](https://serhandemirel.com) (source: [serhanco/serhandemirel.comLP](https://github.com/serhanco/serhandemirel.comLP)).

## Install

1. Zip this folder without the build files: `cd wp-content/themes && zip -r serhandemirel.zip serhandemirel -x 'serhandemirel/node_modules/*' 'serhandemirel/package*.json' 'serhandemirel/tailwind.config.js' 'serhandemirel/assets/css/tailwind.src.css'`
2. In wp-admin go to **Appearance → Themes → Add New → Upload Theme** and upload the zip, then activate it. Activation imports the 5 expertise cards, the 37 brand logos (into the Media Library), starter industry and service terms, five draft service pages and a draft **About** page. Each import runs once.
3. Under **Settings → General** set Site Title to `Serhan Demirel` and Tagline to `Digital Solutions Architect` (the browser tab title is built from these).

No plugin is required. [Polylang](https://wordpress.org/plugins/polylang/) is optional, for the six-language site (see Menu and languages).

**Coming from the Serhan Demirel Core plugin?** Update the theme, then deactivate and delete the plugin. The theme stores content, fields, the profile, settings and messages exactly as the plugin did. While the plugin is still active the theme uses the plugin's copy of that code and shows a reminder in wp-admin.

The front page renders whenever "Your homepage displays" is left on "Your latest posts" or set to any static page.

## Layout

| File | What it holds |
| --- | --- |
| `header.php` | `<head>`, `wp_head()`, opening `<body>`, navigation |
| `front-page.php` | The one-page layout, assembled from the template parts |
| `footer.php` | Floating contact bar, project modal, `wp_footer()` |
| `index.php` | Fallback for posts and other pages |
| `template-parts/` | Front page: `nav`, `hero`, `word-slot`, `expertise`, `brands`, `contact`, `sticky-contact`, `project-modal`. Inner pages: `project-card`, `insight-tile`, `dates` (published, updated and "What changed"), `sources`, `faq-list`, `testimonial`, `author-box`, `appearance-row` |
| `functions.php` | Loads the `inc/` files, theme supports, assets (compiled Tailwind, Inter, GSAP, ScrollTrigger, Lenis, all from the theme folder), meta/OG tags |
| `inc/post-types.php`, `inc/fields.php`, `inc/meta-boxes.php`, `inc/admin-columns.php` | Content types and taxonomies, every custom field (declared once in `sdc_field_groups()`), edit-screen boxes and list columns |
| `inc/api.php` | Read helpers for templates: `sdc_get_brands()`, `sdc_get_expertise()`, `sdc_get_projects()`, `sdc_project_data()`, `sdc_get_services()`, `sdc_service_data()`, `sdc_read_time()` |
| `inc/messages.php` | The contact form endpoint and the **Messages** list |
| `inc/profile.php`, `inc/schema.php`, `inc/llms.php` | **Profile** screen, JSON-LD graph, `/llms.txt` |
| `inc/trust.php` | Dates, sources, noindex, testimonials, talks, glossary, industry page fields (term meta), the FAQ hub, credentials and profile numbers |
| `inc/seo.php` | Page title, description and sharing image per page, noindex, sitemap rules and the AI crawler lines in `robots.txt` |
| `inc/multilingual.php`, `wpml-config.xml` | Which types and fields Polylang (or WPML) translates or shares |
| `inc/setup.php` | **Tools → Site setup** checklist and the **Set up languages** button (`wp sdc setup`) |
| `inc/seed.php`, `inc/seed-services.php` | The one-time starter content |
| `inc/content.php` | Data for the front-page sections, with built-in fallbacks so the page never renders empty |
| `single-sd_project.php`, `archive-sd_project.php` | Project page as a case study (period and duration, results with their measurement source, challenge / approach / outcome, gallery and video, testimonial, dates, next project; client name can be withheld) and `/work/` list (also service archives) |
| `taxonomy-sd_industry.php` | Industry page at `/industry/<industry>/`: answer-first intro, problems and the service for each, projects, brands, a testimonial and industry questions |
| `archive-sd_term.php`, `single-sd_term.php` | Glossary at `/glossary/` (letter index, search) and term pages (definition box, other names, "From the field", questions, sources, related terms) |
| `archive-sd_appearance.php` | Talks, trainings & press at `/speaking/`: featured video, filter by kind, list by year with video, slides and article links |
| `template-faq.php` | Page template **FAQ**: every question on the site in one searchable page, grouped by topic, each linking back to its own page |
| `single-sd_service_page.php`, `archive-sd_service_page.php` | Service page (definition, dates, price, languages, a good fit / not a fit, deliverables, process, testimonials, related work with industries, FAQ, 15-minute call button) and `/services/` list |
| `template-profile.php` | Page template **Profile (About)**: photo, title and languages, numbers, bio, talks, testimonials, brands by industry, expertise, certificates, awards and memberships |
| `home.php`, `single.php` | Insights list (format and "Updated" badges, newest post as a wide card, category pills, search, "Load more", optional newsletter card) and article page (format, the question it answers, published and updated dates with "What changed", reading bar, key takeaways, "From my work", outline, numbered sources, FAQ, author box with title and years, glossary links, related service, two posts to read next). The list needs **Settings → Reading → Your homepage displays: A static page** with a page chosen as **Posts page** |
| `404.php`, `search.php` | Not-found page with search and shortcuts; search results grouped into projects, services, insights, glossary and pages |
| `inc/subpages.php` | Helpers for the inner pages: article outline, related posts, author box, breadcrumb, search form, cover image priority, prefetching (WordPress 6.8+ speculative loading) |
| `inc/options.php` | Settings fields, defaults and `sd_opt()`; per-language values with fallback to the default language |
| `inc/options-page.php` | The **Serhan Demirel** settings screen in wp-admin |
| `inc/tracking.php` | GTM, GA4, Meta Pixel, LinkedIn, Clarity, extra code, Consent Mode defaults, per-page switch |
| `inc/brands.php` | The bundled brand logos and their order, imported into **Brands** on activation |
| `inc/i18n.php` | Navigation links, language switcher data, first-visit browser-language redirect, `x-default` hreflang |
| `assets/vendor/`, `assets/fonts/` | GSAP 3.12.2, ScrollTrigger, Lenis 1.1.13 and the Inter variable font (Latin and Latin Extended, OFL), served from the theme instead of CDNs and Google Fonts |
| `assets/admin/` | Media picker and styles for the edit screens and the Profile screen |
| `assets/js/` | `site.js` runs on every page without libraries (menu, navbar, filters, clock, reading bar, outline, copy link, Insights filter and "Load more"); `main.js` and `brands.js` run only on the front page, together with GSAP and Lenis. The page scripts (`language.js` runs the switcher) that used to be inline in `index.html`; `tracking.js` pushes `sd_form_submit`, `sd_whatsapp_click` and `sd_email_click` to `dataLayer` and stores the UTM campaign in an `sd_utm` cookie |

## Content types

| Type | Menu | Translated with Polylang | Used for |
| --- | --- | --- | --- |
| `sd_project` | Projects | Yes | Selected Work cards and `/work/<project>/` pages; taxonomies **Industries** (portfolio filters) and **Services** |
| `sd_service_page` | Services | Yes | Service pages at `/services/<service>/` and the `/services/` list; shares the **Services** taxonomy with projects, which fills "Related work" |
| `sd_expertise` | Expertise | Yes | Core Expertise cards; each card can link to a service page |
| `sd_testimonial` | Testimonials | Yes | Client quotes with name, role, photo and LinkedIn, linked to a project, services and a brand; shown on service, project, industry and About pages only when **The client agreed to publish this** is ticked |
| `sd_appearance` | Talks & press | Yes | Talks, workshops, trainings, podcasts, interviews and press at `/speaking/` (items have no page of their own) |
| `sd_term` | Glossary | Yes | Term pages at `/glossary/<term>/` |
| `sd_brand` | Brands | No | Logo band; taxonomy **Industries** groups them on About and industry pages |
| `sd_message` | Messages | No | "Start a Project" form submissions |

Posts (Insights) get **Reading time**, **Show on home page**, format, the question the post answers, key takeaways, an experience note and project, glossary terms and FAQ. Posts, pages, projects, service pages and terms share a **Sources, updates and search** box: reviewed date and what changed, sources, SEO title and description, sharing image and **Hide from search engines**. **Industries** terms have their own page fields (headline, intro, problems, FAQ, dates, SEO). Pages, posts, projects and service pages get **Turn off tracking codes on this page** and **Extra code for this page** (administrators only). Order in every list comes from the **Order** box (Post Attributes). Fields are declared once in `inc/fields.php`; meta keys are `_sd_<field>`. See [`docs/content-model.md`](../../../docs/content-model.md).

**Profile** (its own menu) holds who Serhan is: name, other spellings, job title, short and long bio, photo, city and country, languages, areas of expertise, profile links, company, schools and certificates. It also holds career start, numbers, awards, memberships and a booking link. It feeds the About template, the author box, the JSON-LD and `/llms.txt`.

**Structured data and llms.txt.** Every front-end page prints one JSON-LD graph: Person (with credentials, awards, memberships and talks), WebSite, the page (ProfilePage on the home page and the About template) and a BreadcrumbList, plus BlogPosting (sources as `citation`, glossary terms as `mentions`) for posts, CreativeWork for projects, Service for service pages, DefinedTerm and DefinedTermSet for the glossary, an ItemList of events, podcast episodes and articles for talks, and FAQPage wherever there are questions (including the FAQ page). Dates come from the publish date and **Last reviewed**. It is skipped when Yoast, Rank Math, AIOSEO or SEOPress is active (filters `sdc_output_schema`, `sdc_schema_graph`, `sdc_is_profile_page`). `/llms.txt` lists the profile, languages, services, industries, projects, posts, glossary, talks, key pages and contact links in the default language, leaving out hidden pages (filters `sdc_llms_lines`, `sdc_serve_llms`).

## Contact form

The form posts to `admin-ajax.php` (`action=sd_contact`) with `Name`, `Email`, `Message`, `lang`, `source`, `consent` (required), and if present `Service` (the service page it was opened from, shown as a removable chip), `found_via` (the optional "How did you find me?" choice, with AI assistants as their own option), `referrer` (the first outside site of the visit) and `utm_source` / `utm_campaign` (else the `sd_utm` cookie). The email says when the visitor came from an AI assistant. Each submission is saved under **Messages** with its language, page, campaign and a status (New, Replied, Archived) and emailed to the address in **Serhan Demirel → Form** (empty: the admin address from Settings → General). Mail needs an SMTP plugin on most hosts.

Spam protection: a hidden `website` field that people leave empty (bots that fill it get a normal-looking answer and nothing is saved) and at most five messages per visitor per hour. The form sends no nonce, so it keeps working on cached pages.

## Settings

wp-admin → **Serhan Demirel** has tabs for General (SEO, sharing image and **Keep AI training crawlers out**), Hero, Word slot, Sections (show/hide and headings), Contact, Form and Tracking. With Polylang, texts marked with a globe are saved per language; an empty field shows the theme's translation of the built-in text (or the default-language text in a language the theme has no translation for). Sections also holds the general FAQ and the glossary and talks intros; Form holds **Ask "How did you find me?"** and the privacy link. Tracking codes are skipped for logged-in editors (unless enabled), in previews and on pages with **Turn off tracking codes on this page** ticked. When Yoast, Rank Math, AIOSEO or SEOPress is active, the theme leaves the title, description and social tags to it.

**Search and AI crawlers.** `robots.txt` names the AI search and answer bots (OAI-SearchBot, ChatGPT-User, Claude-SearchBot, Claude-User, PerplexityBot, Perplexity-User) as welcome, allows or blocks the AI training bots (GPTBot, ClaudeBot, Google-Extended and others) according to **Keep AI training crawlers out**, and points to `/llms.txt`. Pages with **Hide from search engines** get `noindex, follow` and leave the sitemap and `/llms.txt`. For the FAQ hub, create a page with the **FAQ** template.

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
- **One language:** without Polylang the site runs in the language set under
  **Settings → General** (e.g. Türkçe), using the theme's translation for it;
  there is no switcher and no redirect.
- **Languages:** with Polylang active, a language switcher appears in the navbar
  (globe button with the current code, opening a list of languages) and in the
  mobile menu (a grid of language codes and names). With Polylang, assign one
  menu per language. Set Polylang up with the default language (English) at the
  site root and the others in folders (`/tr/`, `/fr/`, ...).
- **Browser language:** on a visitor's first visit to the English home page, a
  small script in `<head>` sends them to the home page in their browser's
  language when the site has it (e.g. a German browser goes to `/de/`). The
  choice is saved in the `sd_lang` cookie for a year, and picking a language in
  the switcher overwrites it, so nobody is redirected against their will.
  Other pages, crawlers and visits that come from the site's own pages are
  never redirected. Keep Polylang's own **Detect browser language** option off.
  The theme also adds the `hreflang="x-default"` link (the English URL) that
  Polylang leaves out when English has no URL prefix.
- **Translations:** the theme ships Turkish, French, Dutch, German and Italian
  in `languages/<locale>.po/.mo`, one file per language for the site and the
  wp-admin screens. They cover the interface and the built-in site texts: in a language with a
  translation, an empty field in the **Serhan Demirel** settings shows the
  translated built-in text; texts you enter per language always win.
  Edit them with Poedit or Loco Translate. Regenerate the .pot after changing
  strings: `wp i18n make-pot . languages/serhandemirel.pot --exclude=node_modules,assets/css`.
