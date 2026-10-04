<?php
/**
 * Read helpers for the theme. Each returns plain arrays so templates do not
 * need to know meta keys. Translated types return the current language.
 * Text values are raw; escape them on output.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL of an attachment; falls back to the original file for SVGs, which
 * have no generated sizes.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $size          Image size.
 * @return string
 */
function sdc_attachment_src( $attachment_id, $size = 'full' ) {
	if ( ! $attachment_id ) {
		return '';
	}
	$src = wp_get_attachment_image_url( $attachment_id, $size );
	return $src ? $src : (string) wp_get_attachment_url( $attachment_id );
}

/**
 * Visible brands in display order.
 *
 * @return array<int, array{name: string, white: string, color: string, url: string}>
 */
function sdc_get_brands() {
	$brands = array();
	$posts  = get_posts(
		array(
			'post_type'      => 'sd_brand',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	foreach ( $posts as $post ) {
		if ( ! sdc_get( $post->ID, 'visible' ) ) {
			continue;
		}
		$white = sdc_attachment_src( (int) sdc_get( $post->ID, 'logo_white' ) );
		if ( ! $white ) {
			continue;
		}
		$brands[] = array(
			'name'  => $post->post_title,
			'white' => $white,
			'color' => sdc_attachment_src( (int) sdc_get( $post->ID, 'logo_color' ) ),
			'url'   => (string) sdc_get( $post->ID, 'url' ),
		);
	}
	return $brands;
}

/**
 * Expertise cards in display order.
 *
 * @return array<int, array{title: string, description: string, tags: string[], accent: string, icon: string, url: string}>
 */
function sdc_get_expertise() {
	$cards = array();
	$posts = get_posts(
		array(
			'post_type'      => 'sd_expertise',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	foreach ( $posts as $post ) {
		$cards[] = array(
			'title'       => $post->post_title,
			'description' => (string) sdc_get( $post->ID, 'description' ),
			'tags'        => array_values( array_filter( array_map( 'trim', explode( ',', (string) sdc_get( $post->ID, 'tags' ) ) ) ) ),
			'accent'      => (string) sdc_get( $post->ID, 'accent' ),
			'icon'        => (string) sdc_get( $post->ID, 'icon' ),
			'url'         => sdc_translated_permalink( (int) sdc_get( $post->ID, 'service_page' ) ),
		);
	}
	return $cards;
}

/**
 * Projects for cards.
 *
 * @param bool $featured_only Only projects ticked "Show on home page".
 * @return array<int, array>
 */
function sdc_get_projects( $featured_only = false ) {
	$args = array(
		'post_type'      => 'sd_project',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	);
	if ( $featured_only ) {
		$args['meta_key']   = sdc_meta_key( 'featured' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- small table.
		$args['meta_value'] = '1'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- small table.
	}

	return array_map( 'sdc_project_data', get_posts( $args ) );
}

/**
 * Card and detail data for one project.
 *
 * @param WP_Post|int $post Project.
 * @return array
 */
function sdc_project_data( $post ) {
	$post       = get_post( $post );
	$industries = get_the_terms( $post, 'sd_industry' );
	$brand      = (int) sdc_get( $post->ID, 'brand' );
	return array(
		'id'         => $post->ID,
		'title'      => $post->post_title,
		'permalink'  => get_permalink( $post ),
		'image'      => (string) get_the_post_thumbnail_url( $post, 'large' ),
		'client'     => (string) sdc_get( $post->ID, 'client' ),
		'location'   => (string) sdc_get( $post->ID, 'location' ),
		'summary'    => (string) sdc_get( $post->ID, 'summary' ),
		'metric'     => (string) sdc_get( $post->ID, 'metric' ),
		'year'       => (int) sdc_get( $post->ID, 'year' ),
		'url'        => (string) sdc_get( $post->ID, 'url' ),
		'industries' => is_array( $industries ) ? wp_list_pluck( $industries, 'slug' ) : array(),
		'brand_logo' => $brand ? sdc_attachment_src( (int) sdc_get( $brand, 'logo_white' ) ) : '',
		'gallery'    => array_values( array_filter( array_map( 'absint', explode( ',', (string) sdc_get( $post->ID, 'gallery' ) ) ) ) ),
	);
}

/**
 * Reading time of a post in minutes: the "Reading time" field, or the
 * word count at 200 words per minute.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function sdc_read_time( $post_id ) {
	$minutes = (int) sdc_get( $post_id, 'read_time' );
	if ( $minutes ) {
		return $minutes;
	}
	$words = str_word_count( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Permalink of a post in the current language: its translation when one is
 * published, else the post itself; empty when the post is not published.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function sdc_translated_permalink( $post_id ) {
	if ( ! $post_id ) {
		return '';
	}
	if ( function_exists( 'pll_get_post' ) ) {
		$translation = pll_get_post( $post_id );
		if ( $translation && 'publish' === get_post_status( $translation ) ) {
			$post_id = $translation;
		}
	}
	return 'publish' === get_post_status( $post_id ) ? (string) get_permalink( $post_id ) : '';
}

/**
 * Non-empty lines of a text field.
 *
 * @param string $text Field value.
 * @return string[]
 */
function sdc_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
}

/**
 * Questions and answers from a FAQ field: a question line, its answer on
 * the following lines, an empty line between pairs.
 *
 * @param string $text Field value.
 * @return array<int, array{question: string, answer: string}>
 */
function sdc_parse_faq( $text ) {
	$faq = array();
	foreach ( preg_split( '/(\r\n|\r|\n)\s*(\r\n|\r|\n)/', trim( (string) $text ) ) as $block ) {
		$lines = sdc_lines( $block );
		if ( count( $lines ) < 2 ) {
			continue;
		}
		$faq[] = array(
			'question' => array_shift( $lines ),
			'answer'   => implode( ' ', $lines ),
		);
	}
	return $faq;
}

/**
 * Steps from a process field: one "Step name: what happens" per line.
 *
 * @param string $text Field value.
 * @return array<int, array{title: string, text: string}>
 */
function sdc_parse_steps( $text ) {
	$steps = array();
	foreach ( sdc_lines( $text ) as $line ) {
		$parts   = array_map( 'trim', explode( ':', $line, 2 ) );
		$steps[] = array(
			'title' => $parts[0],
			'text'  => $parts[1] ?? '',
		);
	}
	return $steps;
}

/**
 * Published service pages in display order (current language).
 *
 * @return array<int, array>
 */
function sdc_get_services() {
	$posts = get_posts(
		array(
			'post_type'      => 'sd_service_page',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	return array_map( 'sdc_service_data', $posts );
}

/**
 * Everything a service page shows.
 *
 * @param WP_Post|int $post Service page.
 * @return array
 */
function sdc_service_data( $post ) {
	$post        = get_post( $post );
	$terms       = get_the_terms( $post, 'sd_service' );
	$term_ids    = is_array( $terms ) ? wp_list_pluck( $terms, 'term_id' ) : array();
	$engagements = sdc_field_groups()['service']['fields']['engagement_model']['options'];
	$engagement  = (string) sdc_get( $post->ID, 'engagement_model' );
	return array(
		'id'               => $post->ID,
		'title'            => $post->post_title,
		'permalink'        => get_permalink( $post ),
		'excerpt'          => has_excerpt( $post ) ? get_the_excerpt( $post ) : '',
		'image'            => (string) get_the_post_thumbnail_url( $post, 'large' ),
		'definition'       => (string) sdc_get( $post->ID, 'definition' ),
		'who_for'          => (string) sdc_get( $post->ID, 'who_for' ),
		'deliverables'     => sdc_lines( sdc_get( $post->ID, 'deliverables' ) ),
		'process'          => sdc_parse_steps( sdc_get( $post->ID, 'process' ) ),
		'duration'         => (string) sdc_get( $post->ID, 'duration' ),
		'engagement'       => $engagement,
		'engagement_label' => $engagements[ $engagement ] ?? '',
		'price_from'       => (int) sdc_get( $post->ID, 'price_from' ),
		'currency'         => (string) sdc_get( $post->ID, 'currency' ),
		'area_served'      => (string) sdc_get( $post->ID, 'area_served' ),
		'faq'              => sdc_parse_faq( sdc_get( $post->ID, 'faq' ) ),
		'reviewed'         => (string) sdc_get( $post->ID, 'reviewed_date' ),
		'modified'         => get_post_modified_time( 'Y-m-d', false, $post ),
		'projects'         => $term_ids ? array_map(
			'sdc_project_data',
			get_posts(
				array(
					'post_type'      => 'sd_project',
					'posts_per_page' => 6,
					'tax_query'      => array( array( 'taxonomy' => 'sd_service', 'terms' => $term_ids ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- few projects.
				)
			)
		) : array(),
	);
}
