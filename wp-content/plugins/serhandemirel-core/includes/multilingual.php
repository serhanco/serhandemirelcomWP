<?php
/**
 * Polylang integration. wpml-config.xml in the plugin root describes the
 * same rules for WPML (Polylang reads it too).
 *
 * Projects, expertise cards, posts and the project taxonomies get one copy
 * per language. Brands and messages are shared by every language. Fields
 * marked "shared" in fields.php stay in sync between translations; the
 * others are copied once into a new translation and then edited per language.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Translated post types.
 *
 * @param array $post_types Post types Polylang manages.
 * @param bool  $is_settings True when building the settings screen.
 * @return array
 */
function sdc_pll_post_types( $post_types, $is_settings ) {
	if ( $is_settings ) {
		// Hide our types from the settings list so they cannot be toggled by mistake.
		unset( $post_types['sd_project'], $post_types['sd_expertise'], $post_types['sd_brand'], $post_types['sd_message'] );
		return $post_types;
	}
	$post_types['sd_project']   = 'sd_project';
	$post_types['sd_expertise'] = 'sd_expertise';
	unset( $post_types['sd_brand'], $post_types['sd_message'] );
	return $post_types;
}
add_filter( 'pll_get_post_types', 'sdc_pll_post_types', 10, 2 );

/**
 * Translated taxonomies.
 *
 * @param array $taxonomies Taxonomies Polylang manages.
 * @param bool  $is_settings True when building the settings screen.
 * @return array
 */
function sdc_pll_taxonomies( $taxonomies, $is_settings ) {
	if ( $is_settings ) {
		unset( $taxonomies['sd_industry'], $taxonomies['sd_service'] );
		return $taxonomies;
	}
	$taxonomies['sd_industry'] = 'sd_industry';
	$taxonomies['sd_service']  = 'sd_service';
	return $taxonomies;
}
add_filter( 'pll_get_taxonomies', 'sdc_pll_taxonomies', 10, 2 );

/**
 * Which meta keys Polylang copies to a new translation and keeps in sync.
 *
 * A new translation starts with every field filled in from the source, so
 * the per-language texts are there to translate. After that only the shared
 * fields stay in sync; editing a French summary never touches the English one.
 *
 * @param string[] $keys Meta keys Polylang plans to copy.
 * @param bool     $sync True when syncing existing translations, false on first copy.
 * @param int      $from Source post ID.
 * @return string[]
 */
function sdc_pll_copy_post_metas( $keys, $sync, $from ) {
	$post_type = get_post_type( $from );
	if ( ! $post_type ) {
		return $keys;
	}
	$keys = array_merge( $keys, sdc_meta_keys_for( $post_type, true ) );
	$keys = $sync ? array_diff( $keys, sdc_meta_keys_for( $post_type, false ) ) : array_merge( $keys, sdc_meta_keys_for( $post_type, false ) );
	return array_values( array_unique( $keys ) );
}
add_filter( 'pll_copy_post_metas', 'sdc_pll_copy_post_metas', 10, 3 );

/**
 * Assign a post to the default language, if Polylang is active.
 *
 * @param int $post_id Post ID.
 */
function sdc_set_default_language( $post_id ) {
	if ( function_exists( 'pll_set_post_language' ) && function_exists( 'pll_default_language' ) && pll_default_language() ) {
		pll_set_post_language( $post_id, pll_default_language() );
	}
}
