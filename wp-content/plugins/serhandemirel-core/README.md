# Serhan Demirel Core

Companion plugin for the `serhandemirel` theme. It holds the site's content types, their fields and the contact form, so nothing is lost if the theme changes.

## Install

1. Zip this folder: `cd wp-content/plugins && zip -r serhandemirel-core.zip serhandemirel-core`
2. In wp-admin go to **Plugins → Add New → Upload Plugin**, upload the zip and activate it.

Activate it with the `serhandemirel` theme installed: on activation it imports the 5 expertise cards and copies the 37 brand logos from the theme's `assets/img/brands/` folder into the Media Library. If the theme was not there yet, an admin notice offers an **Import logos** button later. Each import runs once.

## What it adds

| Type | Menu | Translated | Used for |
| --- | --- | --- | --- |
| `sd_project` | Projects | Yes | Selected Work cards and `/work/<project>` pages; taxonomies **Industries** (portfolio filters) and **Services** |
| `sd_service_page` | Services | Yes | Service pages at `/services/<service>/` and the `/services/` list; shares the **Services** taxonomy with projects, which fills "Related work" |
| `sd_expertise` | Expertise | Yes | Core Expertise cards; each card can link to a service page |
| `sd_brand` | Brands | No | Logo band |
| `sd_message` | Messages | No | "Start a Project" form submissions (moved here from the theme) |

Posts (Insights) get a **Reading time** and **Show on home page** field. Pages, posts and projects get **Turn off tracking codes on this page** and **Extra code for this page** (administrators only).

Order in every list comes from the **Order** box (Post Attributes).

Service pages have a definition (the answer-first summary), who it is for, deliverables (one per line), process steps (`Step: text` per line), duration, engagement model, starting price, area served, FAQ (question line, answer below, blank line between) and a reviewed date. On activation (and on update to a new version) the plugin creates five English drafts, one per expertise area, links the matching expertise cards to them and adds a draft **About** page that uses the theme's Profile template.

**Profile** (its own menu) holds who Serhan is: name, other spellings, job title, short and long bio, photo, city and country, languages, areas of expertise, profile links, company, schools and certificates. Texts marked with a globe are kept per language.

**Structured data and llms.txt.** Every front-end page prints one JSON-LD graph: Person, WebSite and the page (ProfilePage on the home page and the About template), plus BlogPosting for posts, CreativeWork for projects and Service with FAQPage for service pages. It is skipped when Yoast, Rank Math, AIOSEO or SEOPress is active (filters `sdc_output_schema`, `sdc_schema_graph`, `sdc_is_profile_page`). `/llms.txt` lists the profile, languages, services, projects, posts and contact links in the default language (filters `sdc_llms_lines`, `sdc_serve_llms`).

**Tools → Site setup** shows a go-live checklist and a **Set up languages** button (or `wp sdc setup`) that adds the six Polylang languages, downloads WordPress's translations for them and sets the recommended Polylang options. See [`docs/go-live.md`](../../../docs/go-live.md).

## Fields

All fields are declared once in `includes/fields.php` (`sdc_field_groups()`). That list drives meta registration, the edit-screen boxes, sanitizing and translation. Meta keys are stored as `_sd_<field>`.

## Languages

With Polylang, projects, service pages, expertise cards, posts, industries and services get a copy per language; brands and messages are shared. A new translation starts with every field copied from the source; afterwards only the fields marked `shared` (year, images, links, flags) stay in sync, and texts such as the summary are edited per language. `wpml-config.xml` describes the same rules for WPML.

## For the theme

`includes/api.php` returns plain arrays for templates: `sdc_get_brands()`, `sdc_get_expertise()`, `sdc_get_projects( $featured_only )`, `sdc_project_data( $post )`, `sdc_get_services()`, `sdc_service_data( $post )`, `sdc_get_profile( $lang )`, `sdc_read_time( $post_id )`. Values are raw; escape on output.

## Contact form

The theme's form posts to `admin-ajax.php` (`action=sd_contact`) with `Name`, `Email`, `Message` and a nonce, and may also send `lang`, `source`, `utm_source`, `utm_campaign`. Without UTM fields the handler reads an `sd_utm` cookie (`{"source": "...", "campaign": "..."}`). Each message is saved with its name, email, language, source page, campaign and a status (New, Replied, Archived), and emailed to the site admin address (filter `sdc_contact_recipient`; the theme sets it from its Form settings). New messages show as a count next to the menu item.
