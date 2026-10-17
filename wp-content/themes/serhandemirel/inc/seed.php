<?php
/**
 * One-time import of the content that was hard-coded in the one-page site:
 * the 5 expertise cards, the 37 brand logos and starter project terms.
 *
 * Runs when the theme is activated, and again after a theme update for
 * parts added since. Brand logos are copied from assets/img/brands/white/
 * into the Media Library. Each part runs once and is remembered in the
 * "sdc_seeded" option.
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Expertise cards as they appear on the static site, in display order.
 *
 * @return array<int, array>
 */
function sdc_seed_expertise_data() {
	return array(
		array( 'Marketing & Growth', 'Driving measurable growth and expanding reach through data-driven digital marketing strategies.', 'Sales Optimization, Lead Gen, Digital Marketing', 'blue', 'growth' ),
		array( 'Digital Products', 'Architecting and building modern web applications, platforms, and high-converting landing pages.', 'Web Apps, Digital Product, Landing Pages', 'purple', 'code' ),
		array( 'AI & Automation', 'Streamlining workflows and accelerating business processes using intelligent AI solutions and smart automation.', 'AI Solutions, Automation', 'emerald', 'flask' ),
		array( 'Strategy & Visibility', 'Elevating brand presence through deep competitor analysis and aggressive search engine optimization.', 'SEO, Competitor Analysis', 'pink', 'chart' ),
		array( 'Transformation & Ed', 'Guiding companies through digital transformation and providing corporate training for sustainable growth.', 'Digital Transformation, Training & Ed', 'amber', 'book' ),
	);
}

/**
 * Which import parts have run.
 *
 * @return array<string, bool>
 */
function sdc_seeded() {
	return (array) get_option( 'sdc_seeded', array() );
}

/**
 * Remember that an import part has run.
 *
 * @param string $part Part name.
 */
function sdc_mark_seeded( $part ) {
	$done          = sdc_seeded();
	$done[ $part ] = true;
	update_option( 'sdc_seeded', $done, false );
}

/**
 * Run every import part that has not run yet.
 */
function sdc_seed_content() {
	$done = sdc_seeded();

	if ( empty( $done['expertise'] ) ) {
		sdc_seed_expertise();
		sdc_mark_seeded( 'expertise' );
	}
	if ( empty( $done['terms'] ) ) {
		sdc_seed_terms();
		sdc_mark_seeded( 'terms' );
	}
	if ( empty( $done['brands'] ) ) {
		sdc_seed_brands();
		sdc_mark_seeded( 'brands' );
	}
	if ( empty( $done['services'] ) ) {
		sdc_seed_services();
		sdc_mark_seeded( 'services' );
	}

	if ( empty( $done['about'] ) ) {
		sdc_seed_about_page();
		sdc_mark_seeded( 'about' );
	}
}

/**
 * Run import parts added in a theme update (e.g. the service pages) on
 * sites that activated an older version.
 */
function sdc_seed_on_update() {
	if ( get_option( 'sdc_seed_version' ) === SD_THEME_VERSION || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	update_option( 'sdc_seed_version', SD_THEME_VERSION, false );
	if ( sdc_seeded() ) {
		sdc_seed_content();
		update_option( 'sdc_flush_rewrite', 1 );
	}
}
add_action( 'admin_init', 'sdc_seed_on_update' );

/**
 * Create the expertise cards, unless some already exist.
 */
function sdc_seed_expertise() {
	if ( get_posts( array( 'post_type' => 'sd_expertise', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'lang' => '' ) ) ) {
		return;
	}
	foreach ( sdc_seed_expertise_data() as $i => $card ) {
		list( $title, $description, $tags, $accent, $icon ) = $card;
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'sd_expertise',
				'post_status' => 'publish',
				'post_title'  => $title,
				'menu_order'  => $i + 1,
				'meta_input'  => array(
					sdc_meta_key( 'description' ) => $description,
					sdc_meta_key( 'tags' )        => $tags,
					sdc_meta_key( 'accent' )      => $accent,
					sdc_meta_key( 'icon' )        => $icon,
				),
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			sdc_set_default_language( $post_id );
		}
	}
}

/**
 * Starter industries (the hidden portfolio filters) and services (the
 * expertise areas) for tagging projects.
 */
function sdc_seed_terms() {
	$terms = array(
		'sd_industry' => array( 'Fintech', 'E-Commerce' ),
		'sd_service'  => array_column( sdc_seed_expertise_data(), 0 ),
	);
	foreach ( $terms as $taxonomy => $names ) {
		foreach ( $names as $name ) {
			if ( term_exists( $name, $taxonomy ) ) {
				continue;
			}
			$term = wp_insert_term( $name, $taxonomy );
			if ( ! is_wp_error( $term ) && function_exists( 'pll_set_term_language' ) && function_exists( 'pll_default_language' ) && pll_default_language() ) {
				pll_set_term_language( $term['term_id'], pll_default_language() );
			}
		}
	}
}

/**
 * Create a brand per bundled logo, copying the white logo into the Media Library.
 */
function sdc_seed_brands() {
	if ( get_posts( array( 'post_type' => 'sd_brand', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) ) ) {
		return;
	}
	$dir = get_template_directory() . '/assets/img/brands/white/';

	require_once ABSPATH . 'wp-admin/includes/image.php';
	add_filter( 'upload_mimes', 'sdc_seed_allow_svg' );

	$order = 0;
	foreach ( sd_brand_logos() as $logo ) {
		++$order;
		$name  = $logo['alt'];
		$white = sdc_seed_import_image( $dir . $logo['src'], $name );

		wp_insert_post(
			array(
				'post_type'   => 'sd_brand',
				'post_status' => 'publish',
				'post_title'  => $name,
				'menu_order'  => $order,
				'meta_input'  => array(
					sdc_meta_key( 'logo_white' ) => $white,
					sdc_meta_key( 'visible' )    => true,
				),
			)
		);
	}

	remove_filter( 'upload_mimes', 'sdc_seed_allow_svg' );
}

/**
 * Allow SVG while importing the bundled logos.
 *
 * @param array $mimes Allowed types.
 * @return array
 */
function sdc_seed_allow_svg( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}

/**
 * Copy a file into uploads and create its attachment.
 *
 * @param string $path File path.
 * @param string $alt  Alt text.
 * @return int Attachment ID, 0 on failure.
 */
function sdc_seed_import_image( $path, $alt ) {
	if ( ! is_readable( $path ) ) {
		return 0;
	}
	$upload = wp_upload_bits( 'brand-white-' . basename( $path ), null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local file.
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$type          = wp_check_filetype( $upload['file'] );
	$attachment_id = wp_insert_attachment(
		array(
			'post_title'     => $alt,
			'post_mime_type' => $type['type'],
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( ! $attachment_id || is_wp_error( $attachment_id ) ) {
		return 0;
	}
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
	return (int) $attachment_id;
}

/**
 * Let administrators upload SVG logos later (they can already add any HTML).
 *
 * @param array $mimes Allowed types.
 * @return array
 */
function sdc_allow_svg_for_admins( $mimes ) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'sdc_allow_svg_for_admins' );

/**
 * Import the starter content when the theme is activated.
 */
function sdc_seed_on_activation() {
	sdc_register_post_types();
	sdc_register_taxonomies();
	sdc_seed_content();
	update_option( 'sdc_seed_version', SD_THEME_VERSION, false );
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'sdc_seed_on_activation' );
