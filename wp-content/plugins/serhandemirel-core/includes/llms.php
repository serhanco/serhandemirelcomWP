<?php
/**
 * /llms.txt: a plain-text summary of the site for AI assistants
 * (https://llmstxt.org), built from the profile, service pages, projects
 * and posts in the default language.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the request is for /llms.txt at the site root.
 *
 * @return bool
 */
function sdc_is_llms_request() {
	$path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '', PHP_URL_PATH );
	$home = (string) wp_parse_url( get_option( 'home' ), PHP_URL_PATH );
	return trailingslashit( $home ) . 'llms.txt' === $path;
}

/**
 * One Markdown list line: "- [Title](url): text".
 *
 * @param string $title Link text.
 * @param string $url   URL.
 * @param string $text  Description.
 * @return string
 */
function sdc_llms_line( $title, $url, $text = '' ) {
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $text ) ) );
	return '- [' . $title . '](' . $url . ')' . ( '' !== $text ? ': ' . $text : '' );
}

/**
 * The file's contents.
 *
 * @return string
 */
function sdc_llms_text() {
	$lang    = sdc_profile_default_lang();
	$profile = sdc_get_profile( $lang );
	$query   = array( 'lang' => function_exists( 'pll_default_language' ) ? $lang : '' );
	$lines   = array(
		'# ' . $profile['full_name'],
		'',
		'> ' . trim( preg_replace( '/\s+/', ' ', $profile['short_bio'] ) ),
		'',
		$profile['job_title'] . ( $profile['city'] ? ' · ' . $profile['city'] . ( $profile['country'] ? ', ' . $profile['country'] : '' ) : '' ) . '.',
	);
	if ( $profile['knows_about'] ) {
		$lines[] = 'Areas of expertise: ' . implode( ', ', $profile['knows_about'] ) . '.';
	}

	if ( function_exists( 'pll_the_languages' ) ) {
		$languages = (array) pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0, 'force_home' => 1 ) );
		if ( count( $languages ) > 1 ) {
			$lines[] = '';
			$lines[] = '## Languages';
			$lines[] = '';
			foreach ( $languages as $language ) {
				$lines[] = sdc_llms_line( $language['name'], pll_home_url( $language['slug'] ) );
			}
		}
	}

	$sections = array(
		'Services'      => get_posts( array( 'post_type' => 'sd_service_page', 'posts_per_page' => 20, 'orderby' => 'menu_order', 'order' => 'ASC' ) + $query ),
		'Selected work' => get_posts( array( 'post_type' => 'sd_project', 'posts_per_page' => 20, 'orderby' => 'menu_order', 'order' => 'ASC' ) + $query ),
		'Insights'      => get_posts( array( 'post_type' => 'post', 'posts_per_page' => 20 ) + $query ),
	);
	foreach ( $sections as $heading => $posts ) {
		if ( ! $posts ) {
			continue;
		}
		$lines[] = '';
		$lines[] = '## ' . $heading;
		$lines[] = '';
		foreach ( $posts as $post ) {
			if ( 'sd_service_page' === $post->post_type ) {
				$text = sdc_get( $post->ID, 'definition' );
			} elseif ( 'sd_project' === $post->post_type ) {
				$text = sdc_get( $post->ID, 'summary' );
			} else {
				$text = get_the_excerpt( $post );
			}
			$lines[] = sdc_llms_line( html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ), get_permalink( $post ), $text );
		}
	}

	$lines[] = '';
	$lines[] = '## Contact';
	$lines[] = '';
	$lines[] = sdc_llms_line( __( 'Contact form', 'serhandemirel-core' ), home_url( '/#contact' ) );
	foreach ( $profile['same_as'] as $profile_url ) {
		$lines[] = '- ' . $profile_url;
	}

	/**
	 * Filters the llms.txt lines before they are joined.
	 *
	 * @param string[] $lines Lines.
	 */
	return implode( "\n", apply_filters( 'sdc_llms_lines', $lines ) ) . "\n";
}

/**
 * Answer /llms.txt before WordPress looks for a page.
 */
function sdc_serve_llms() {
	if ( ! sdc_is_llms_request() || ! apply_filters( 'sdc_serve_llms', true ) ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	echo sdc_llms_text(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain text.
	exit;
}
add_action( 'wp_loaded', 'sdc_serve_llms' );
