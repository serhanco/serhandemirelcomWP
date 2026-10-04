<?php
/**
 * Content for the front-page sections.
 *
 * Reads the Serhan Demirel Core plugin's content types when the plugin is
 * active, and falls back to the content the static site shipped with, so
 * the page never renders empty.
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Card colour classes per accent. Written out in full so the Tailwind build
 * finds them.
 *
 * @return array<string, string>
 */
function sd_accent_classes() {
	return array(
		'blue'    => 'bg-blue-500/20 text-blue-400',
		'purple'  => 'bg-purple-500/20 text-purple-400',
		'emerald' => 'bg-emerald-500/20 text-emerald-400',
		'pink'    => 'bg-pink-500/20 text-pink-400',
		'amber'   => 'bg-amber-500/20 text-amber-400',
	);
}

/**
 * SVG path data per expertise icon.
 *
 * @return array<string, string>
 */
function sd_icon_paths() {
	return array(
		'growth' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
		'code'   => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
		'flask'  => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
		'chart'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
		'book'   => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
	);
}

/**
 * Expertise cards: from the plugin, or the static site's five cards.
 *
 * @return array<int, array{title: string, description: string, tags: string[], accent: string, icon: string}>
 */
function sd_expertise_cards() {
	if ( function_exists( 'sdc_get_expertise' ) ) {
		$cards = sdc_get_expertise();
		if ( $cards ) {
			return $cards;
		}
	}
	return array(
		array( 'title' => 'Marketing & Growth', 'description' => 'Driving measurable growth and expanding reach through data-driven digital marketing strategies.', 'tags' => array( 'Sales Optimization', 'Lead Gen', 'Digital Marketing' ), 'accent' => 'blue', 'icon' => 'growth' ),
		array( 'title' => 'Digital Products', 'description' => 'Architecting and building modern web applications, platforms, and high-converting landing pages.', 'tags' => array( 'Web Apps', 'Digital Product', 'Landing Pages' ), 'accent' => 'purple', 'icon' => 'code' ),
		array( 'title' => 'AI & Automation', 'description' => 'Streamlining workflows and accelerating business processes using intelligent AI solutions and smart automation.', 'tags' => array( 'AI Solutions', 'Automation' ), 'accent' => 'emerald', 'icon' => 'flask' ),
		array( 'title' => 'Strategy & Visibility', 'description' => 'Elevating brand presence through deep competitor analysis and aggressive search engine optimization.', 'tags' => array( 'SEO', 'Competitor Analysis' ), 'accent' => 'pink', 'icon' => 'chart' ),
		array( 'title' => 'Transformation & Ed', 'description' => 'Guiding companies through digital transformation and providing corporate training for sustainable growth.', 'tags' => array( 'Digital Transformation', 'Training & Ed' ), 'accent' => 'amber', 'icon' => 'book' ),
	);
}

/**
 * Logos for the marquee script: from the plugin's Brands, or the bundled files.
 *
 * @return array<int, array{white: string, color: string, alt: string, url: string}>
 */
function sd_marquee_logos() {
	if ( function_exists( 'sdc_get_brands' ) ) {
		$brands = sdc_get_brands();
		if ( $brands ) {
			return array_map(
				function ( $brand ) {
					return array(
						'white' => $brand['white'],
						'color' => $brand['color'],
						'alt'   => $brand['name'],
						'url'   => $brand['url'],
					);
				},
				$brands
			);
		}
	}
	$base = get_template_directory_uri() . '/assets/img/brands/';
	return array_map(
		function ( $logo ) use ( $base ) {
			return array(
				'white' => $base . 'white/' . $logo['src'],
				'color' => $base . 'color/' . $logo['src'],
				'alt'   => $logo['alt'],
				'url'   => '',
			);
		},
		sd_brand_logos()
	);
}

/**
 * Projects for the Work section: those ticked "Show on home page", or all.
 *
 * @return array<int, array>
 */
function sd_work_projects() {
	static $projects = null;
	if ( null === $projects ) {
		$projects = array();
		if ( function_exists( 'sdc_get_projects' ) ) {
			$projects = sdc_get_projects( true );
			if ( ! $projects ) {
				$projects = sdc_get_projects();
			}
		}
	}
	return $projects;
}

/**
 * Industry filter buttons for a set of projects, slug => name.
 *
 * @param array $projects Projects from sd_work_projects().
 * @return array<string, string>
 */
function sd_work_filters( $projects ) {
	$filters = array();
	foreach ( $projects as $project ) {
		foreach ( (array) get_the_terms( $project['id'], 'sd_industry' ) as $term ) {
			if ( $term instanceof WP_Term ) {
				$filters[ $term->slug ] = $term->name;
			}
		}
	}
	asort( $filters );
	return $filters;
}

/**
 * Posts for the Insights section: those ticked "Show on home page", or the two newest.
 *
 * @return WP_Post[]
 */
function sd_insight_posts() {
	static $posts = null;
	if ( null === $posts ) {
		$posts = get_posts(
			array(
				'posts_per_page' => 4,
				'meta_key'       => '_sd_featured', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- small table.
				'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- small table.
			)
		);
		if ( ! $posts ) {
			$posts = get_posts( array( 'posts_per_page' => 2 ) );
		}
	}
	return $posts;
}

/**
 * Whether a front-page section is switched on and has content.
 *
 * @param string $section expertise, brands, work or insights.
 * @return bool
 */
function sd_show_section( $section ) {
	if ( ! sd_opt( 'show_' . $section ) ) {
		return false;
	}
	switch ( $section ) {
		case 'work':
			return (bool) sd_work_projects();
		case 'insights':
			return (bool) sd_insight_posts();
		default:
			return true;
	}
}

/**
 * Reading time in minutes.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function sd_read_time( $post_id ) {
	if ( function_exists( 'sdc_read_time' ) ) {
		return sdc_read_time( $post_id );
	}
	$words = str_word_count( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * "Technology • 5 min read" line for an insight card.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function sd_insight_meta( $post_id ) {
	$parts      = array();
	$categories = get_the_category( $post_id );
	if ( $categories && 'uncategorized' !== $categories[0]->slug ) {
		$parts[] = $categories[0]->name;
	}
	/* translators: %d: minutes */
	$parts[] = sprintf( _n( '%d min read', '%d min read', sd_read_time( $post_id ), 'serhandemirel' ), sd_read_time( $post_id ) );
	return implode( ' • ', $parts );
}

/**
 * Project lists: all projects on one page, in the "Order" set in wp-admin.
 *
 * @param WP_Query $query Main query.
 */
function sd_project_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'sd_project' ) || $query->is_tax( array( 'sd_industry', 'sd_service' ) ) ) {
		$query->set( 'posts_per_page', -1 );
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
}
add_action( 'pre_get_posts', 'sd_project_archive_query' );
