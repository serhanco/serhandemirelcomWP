<?php
/**
 * Navigation and language switcher.
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Navbar links: the menu assigned to "Main menu", or the visible sections.
 *
 * A menu item's "Title Attribute" field, when it holds an emoji, is shown as
 * its icon in the mobile menu.
 *
 * @return array<int, array{url: string, label: string, icon: string}>
 */
function sd_nav_items() {
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['primary'] ) ) {
		$items = wp_get_nav_menu_items( $locations['primary'] );
		if ( $items ) {
			$links = array();
			foreach ( $items as $item ) {
				if ( (int) $item->menu_item_parent ) {
					continue;
				}
				$links[] = array(
					'url'   => $item->url,
					'label' => $item->title,
					'icon'  => (string) $item->attr_title,
				);
			}
			return $links;
		}
	}

	$sections = array(
		'expertise' => array( '#expertise', __( 'Expertise', 'serhandemirel' ), '⚡️' ),
		'brands'    => array( '#brands', __( 'Brands', 'serhandemirel' ), '🏆' ),
		'work'      => array( '#portfolio', __( 'Work', 'serhandemirel' ), '💼' ),
		'insights'  => array( '#insights', __( 'Insights', 'serhandemirel' ), '📝' ),
	);
	$links    = array();
	foreach ( $sections as $section => $link ) {
		if ( sd_show_section( $section ) ) {
			$links[] = array(
				'url'   => sd_anchor( $link[0] ),
				'label' => $link[1],
				'icon'  => $link[2],
			);
		}
	}
	return $links;
}

/**
 * Languages for the switcher, from Polylang. Empty without it.
 *
 * Languages with no translation of the current page link to their home page.
 *
 * @return array<int, array{url: string, locale: string, slug: string, name: string, current: bool}>
 */
function sd_language_links() {
	static $links = null;
	if ( null !== $links ) {
		return $links;
	}
	if ( ! function_exists( 'pll_the_languages' ) || ! did_action( 'wp' ) ) {
		return array();
	}
	$languages = pll_the_languages(
		array(
			'raw'           => 1,
			'hide_if_empty' => 0,
		)
	);
	$links     = array();
	foreach ( (array) $languages as $language ) {
		$links[] = array(
			'url'     => $language['url'],
			'locale'  => str_replace( '_', '-', $language['locale'] ),
			'slug'    => $language['slug'],
			'name'    => $language['name'],
			'current' => ! empty( $language['current_lang'] ),
		);
	}
	return $links;
}

/**
 * Name of the cookie that remembers the visitor's language.
 *
 * Set when the visitor picks a language in the switcher, or once by the
 * browser-language check below. Polylang's own "Detect browser language"
 * option should stay off so the two do not compete.
 */
const SD_LANG_COOKIE = 'sd_lang';

/**
 * Whether this request gets the browser-language check: the default
 * language's front page, with more than one language set up.
 *
 * @return bool
 */
function sd_lang_detection_applies() {
	if ( ! function_exists( 'pll_home_url' ) || ! is_front_page() || is_paged() || is_customize_preview() || is_preview() ) {
		return false;
	}
	return sd_current_lang() === sd_default_lang() && count( sd_language_links() ) > 1;
}

/**
 * Browser-language check on the default language's front page.
 *
 * Runs in the browser, before anything renders, so it works the same with
 * page caching. Visitors with no saved choice whose browser prefers another
 * site language go to that language's front page once; the choice is saved
 * in the sd_lang cookie (also set by the switcher), so they are never moved
 * again against their will. Crawlers and visits from the site's own pages
 * are left alone, so search engines index every language as linked by
 * hreflang. On a redirect the rest of the page is not parsed, so no
 * tracking tag counts a page view for it.
 */
function sd_lang_detection_script() {
	if ( ! sd_lang_detection_applies() ) {
		return;
	}
	$homes = array();
	foreach ( sd_language_links() as $lang ) {
		$homes[ $lang['slug'] ] = pll_home_url( $lang['slug'] );
	}
	$config = array(
		'cookie'  => SD_LANG_COOKIE,
		'default' => sd_default_lang(),
		'homes'   => $homes,
	);
	?>
	<script>(function(c,d,n,l){try{var m=d.cookie.match(new RegExp('(?:^|; )'+c.cookie+'=([a-z_-]+)')),p=m&&m[1];if(!p){if(/bot|crawl|spider|slurp|archiver|facebookexternalhit|embedly|preview|lighthouse|headless|pagespeed/i.test(n.userAgent))return;if(d.referrer.indexOf(l.origin+'/')===0)return;var w=n.languages&&n.languages.length?n.languages:[n.language||''];for(var i=0;i<w.length&&!p;i++){var s=String(w[i]).toLowerCase().split('-')[0];if(c.homes[s])p=s;}p=p||c.default;d.cookie=c.cookie+'='+p+';path=/;max-age=31536000;samesite=lax'+('https:'===l.protocol?';secure':'');}if(p!==c.default&&c.homes[p]){l.replace(c.homes[p]+l.search+l.hash);d.write('<plaintext style="display:none">');}}catch(e){}})(<?php echo wp_json_encode( $config ); ?>,document,navigator,location);</script>
	<?php
}
add_action( 'wp_head', 'sd_lang_detection_script', 0 );

/**
 * Add hreflang="x-default" (the default language's URL) to Polylang's
 * alternate links. Polylang leaves it out when the default language has no
 * URL prefix, which is how this site is set up.
 *
 * @param array<string, string> $hreflangs URLs keyed by hreflang code.
 * @return array<string, string>
 */
function sd_hreflang_x_default( $hreflangs ) {
	if ( isset( $hreflangs['x-default'] ) || ! function_exists( 'pll_default_language' ) ) {
		return $hreflangs;
	}
	$default = pll_default_language( 'slug' );
	$locale  = str_replace( '_', '-', (string) pll_default_language( 'locale' ) );
	foreach ( array( $default, $locale ) as $code ) {
		if ( $code && isset( $hreflangs[ $code ] ) ) {
			$hreflangs['x-default'] = $hreflangs[ $code ];
			break;
		}
	}
	return $hreflangs;
}
add_filter( 'pll_rel_hreflang_attributes', 'sd_hreflang_x_default' );

/**
 * Switcher script: opens the desktop menu and saves the visitor's choice.
 */
function sd_language_switcher_script() {
	if ( count( sd_language_links() ) < 2 ) {
		return;
	}
	wp_enqueue_script( 'sd-language', get_template_directory_uri() . '/assets/js/language.js', array(), SD_THEME_VERSION, true );
	wp_localize_script( 'sd-language', 'sdLanguage', array( 'cookie' => SD_LANG_COOKIE ) );
}
add_action( 'wp_enqueue_scripts', 'sd_language_switcher_script' );
