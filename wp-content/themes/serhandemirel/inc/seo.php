<?php
/**
 * Search and AI crawler settings: per-page SEO title, description and
 * sharing image, noindex, sitemaps and robots.txt. Titles and tags are left
 * to an SEO plugin when one is active; robots.txt rules are added either way.
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Title, description, image and address of the current page, for the
 * description and social tags.
 *
 * @return array{title: string, description: string, image: string, url: string, type: string}
 */
function sd_page_meta() {
	$title = sd_opt( 'seo_title' );
	$desc  = sd_opt( 'seo_description' );
	$image = sd_opt( 'og_image' ) ? (string) wp_get_attachment_image_url( (int) sd_opt( 'og_image' ), 'full' ) : '';
	$url   = home_url( '/' );
	$type  = 'website';

	if ( is_singular() && ! is_front_page() ) {
		$post  = get_queried_object();
		$title = sdc_get( $post->ID, 'seo_title' ) ? (string) sdc_get( $post->ID, 'seo_title' ) : wp_get_document_title();
		$found = (string) sdc_get( $post->ID, 'seo_description' );
		if ( ! $found ) {
			switch ( $post->post_type ) {
				case 'sd_service_page':
					$found = (string) sdc_get( $post->ID, 'definition' );
					break;
				case 'sd_project':
					$found = (string) sdc_get( $post->ID, 'summary' );
					break;
				case 'sd_term':
					$found = (string) sdc_get( $post->ID, 'short_definition' );
					break;
			}
		}
		if ( ! $found && has_excerpt( $post ) ) {
			$found = get_the_excerpt( $post );
		}
		$desc  = $found ? $found : $desc;
		$share = (int) sdc_get( $post->ID, 'share_image' );
		if ( $share ) {
			$image = (string) wp_get_attachment_image_url( $share, 'full' );
		} elseif ( has_post_thumbnail( $post ) ) {
			$image = (string) get_the_post_thumbnail_url( $post, 'full' );
		}
		$url  = (string) get_permalink( $post );
		$type = 'post' === $post->post_type ? 'article' : 'website';
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$term  = get_queried_object();
		$url   = (string) get_term_link( $term );
		$title = wp_get_document_title();
		if ( 'sd_industry' === $term->taxonomy ) {
			$title = sdc_term_get( $term->term_id, 'seo_title' ) ? sdc_term_get( $term->term_id, 'seo_title' ) : $title;
			$found = sdc_term_get( $term->term_id, 'seo_description' ) ? sdc_term_get( $term->term_id, 'seo_description' ) : sdc_term_get( $term->term_id, 'intro' );
			$desc  = $found ? $found : $desc;
		} elseif ( $term->description ) {
			$desc = $term->description;
		}
	} elseif ( is_post_type_archive() ) {
		$post_type = get_query_var( 'post_type' );
		$post_type = is_array( $post_type ) ? reset( $post_type ) : $post_type;
		$url       = (string) get_post_type_archive_link( $post_type );
		$title     = wp_get_document_title();
		$intros    = array(
			'sd_term'       => sd_opt( 'glossary_intro' ),
			'sd_appearance' => sd_opt( 'speaking_intro' ),
		);
		$desc      = ! empty( $intros[ $post_type ] ) ? $intros[ $post_type ] : $desc;
	} elseif ( is_home() && ! is_front_page() ) {
		$url   = sd_insights_url();
		$title = wp_get_document_title();
		$desc  = sd_opt( 'insights_intro' ) ? sd_opt( 'insights_intro' ) : $desc;
	}

	return array(
		'title'       => wp_strip_all_tags( $title ),
		'description' => trim( wp_strip_all_tags( $desc ) ),
		'image'       => $image ? $image : get_template_directory_uri() . '/assets/img/SD-logo-300px.webp',
		'url'         => $url,
		'type'        => $type,
	);
}

/**
 * Browser-tab title from the "SEO title" field, when filled in.
 *
 * @param string $title Title WordPress would build.
 * @return string
 */
function sd_seo_document_title( $title ) {
	if ( sd_has_seo_plugin() ) {
		return $title;
	}
	if ( is_singular() && ! is_front_page() ) {
		$custom = (string) sdc_get( get_queried_object_id(), 'seo_title' );
		return $custom ? $custom : $title;
	}
	if ( is_tax( 'sd_industry' ) ) {
		$custom = sdc_term_get( get_queried_object_id(), 'seo_title' );
		return $custom ? $custom : $title;
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'sd_seo_document_title' );

/**
 * "Hide from search engines": noindex on the page itself.
 *
 * @param array $robots Robots directives.
 * @return array
 */
function sd_noindex_robots( $robots ) {
	if ( is_singular() && sdc_is_noindex( get_queried_object_id() ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'sd_noindex_robots' );

/**
 * Leave hidden pages out of the sitemap.
 *
 * @param array $args Query arguments for one post type's sitemap.
 * @return array
 */
function sd_sitemap_skip_noindex( $args ) {
	$args['meta_query']   = isset( $args['meta_query'] ) ? (array) $args['meta_query'] : array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- sitemap only.
	$args['meta_query'][] = array(
		'relation' => 'OR',
		array(
			'key'     => sdc_meta_key( 'noindex' ),
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => sdc_meta_key( 'noindex' ),
			'value'   => '1',
			'compare' => '!=',
		),
	);
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'sd_sitemap_skip_noindex' );

/**
 * Talks and press items have no page of their own (they redirect to the
 * list), so they stay out of the sitemap.
 *
 * @param array $post_types Post types in the sitemap.
 * @return array
 */
function sd_sitemap_post_types( $post_types ) {
	unset( $post_types['sd_appearance'], $post_types['sd_testimonial'] );
	return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'sd_sitemap_post_types' );

/**
 * Crawlers that collect text to train AI models, as opposed to the ones
 * that fetch pages to answer a question or build a search index.
 *
 * @return string[]
 */
function sd_ai_training_bots() {
	return array( 'GPTBot', 'ClaudeBot', 'Google-Extended', 'Applebot-Extended', 'CCBot', 'Meta-ExternalAgent', 'Bytespider', 'cohere-training-data-crawler' );
}

/**
 * Search and AI answer crawlers that are always welcome.
 *
 * @return string[]
 */
function sd_ai_search_bots() {
	return array( 'OAI-SearchBot', 'ChatGPT-User', 'Claude-SearchBot', 'Claude-User', 'PerplexityBot', 'Perplexity-User' );
}

/**
 * robots.txt: name the AI search and answer bots explicitly, keep the
 * training bots out only when the theme setting asks for it, and point to
 * llms.txt. WordPress adds the sitemap line itself.
 *
 * @param string $output Robots.txt WordPress built.
 * @param bool   $public Whether the site is visible to search engines.
 * @return string
 */
function sd_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}
	$lines = array( '', '# AI search and answer assistants: welcome.' );
	foreach ( sd_ai_search_bots() as $bot ) {
		$lines[] = 'User-agent: ' . $bot;
	}
	$lines[] = 'Allow: /';
	$lines[] = 'Disallow: /wp-admin/';
	$lines[] = '';
	if ( sd_opt( 'block_ai_training' ) ) {
		$lines[] = '# AI model training: not allowed (Theme settings → General).';
		foreach ( sd_ai_training_bots() as $bot ) {
			$lines[] = 'User-agent: ' . $bot;
		}
		$lines[] = 'Disallow: /';
	} else {
		$lines[] = '# AI model training: allowed, so assistants know who runs this site.';
		foreach ( sd_ai_training_bots() as $bot ) {
			$lines[] = 'User-agent: ' . $bot;
		}
		$lines[] = 'Allow: /';
		$lines[] = 'Disallow: /wp-admin/';
	}
	$lines[] = '';
	$lines[] = '# A short guide to this site for AI assistants: ' . home_url( '/llms.txt' );
	return rtrim( $output ) . "\n" . implode( "\n", $lines ) . "\n";
}
add_filter( 'robots_txt', 'sd_robots_txt', 10, 2 );
