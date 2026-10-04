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
	if ( ! function_exists( 'pll_the_languages' ) ) {
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
