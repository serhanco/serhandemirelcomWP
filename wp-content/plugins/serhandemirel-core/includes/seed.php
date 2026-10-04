<?php
/**
 * One-time import of the content that was hard-coded in the one-page site:
 * the 5 expertise cards, the 37 brand logos and starter project terms.
 *
 * Runs on plugin activation. Brand logos are copied from the theme's
 * assets/img/brands/ folder into the Media Library, so the theme must be
 * installed; if it was not, an admin notice offers to run the import later.
 * Each part runs once and is remembered in the "sdc_seeded" option.
 *
 * @package serhandemirel-core
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
 * Brand logos in marquee order: file name => brand name.
 *
 * @return array<string, string>
 */
function sdc_seed_brand_data() {
	return array(
		'acibadem.svg'             => 'Acıbadem',
		'florence.png'             => 'Florence Nightingale',
		'arcelik.svg'              => 'Arçelik',
		'beko.png'                 => 'Beko',
		'tse.svg'                  => 'TSE',
		'kollektif.svg'            => 'Kollektif',
		'ulker_arena.png'          => 'Ülker Sports Arena',
		'burn.png'                 => 'Burn',
		'adresin.png'              => 'Adresin.com',
		'steminorder.png'          => 'Steminorder',
		'hekim_bilisim.svg'        => 'Hekim Bilişim',
		'dbs.png'                  => 'DBS Architecture',
		'acibadem_imc.png'         => 'Acıbadem International Medical Center',
		'iesyazilim.png'           => 'İES Yazılım',
		'healthcare_diplomacy.png' => 'Healthcare Diplomacy',
		'semih_halezeroglu.svg'    => 'Prof. Dr. Semih Halezeroğlu',
		'superpay.png'             => 'SuperPay',
		'hilal_celik.png'          => 'Hilal Çelik Halat',
		'oguz_kayiran.png'         => 'Prof. Dr. Oğuz Kayıran',
		'renewa.png'               => 'Renewa Clinic',
		'koray_ozduman.png'        => 'Prof. Dr. Koray Özduman',
		'acibadem_mobil.png'       => 'Acıbadem Mobil',
		'whitesoft.png'            => 'White&Soft',
		'ulker_yupo.png'           => 'Ülker Yupo',
		'paraf.png'                => 'Paraf',
		'clinicton.png'            => 'Clinicton',
		'albayrak_tente.png'       => 'Albayrak Tente',
		'coordin_art.png'          => 'Coordin.Art',
		'meseri_energy.png'        => 'Meseri Energy',
		'ee_architecture.png'      => 'EE Architecture',
		'uniplast.png'             => 'Uniplast',
		'koza.png'                 => 'Koza Otomotiv',
		'corin.png'                => 'Corin',
		'gungor.png'               => 'Güngör Otomobil',
		'dgs.png'                  => 'DGS',
		'keysmart.png'             => 'KeySmart',
		'hedef_yelken.png'         => 'Hedef Yelken',
	);
}

/**
 * Folder holding the theme's bundled logos, or '' when not found.
 *
 * @return string
 */
function sdc_seed_logo_dir() {
	foreach ( array( get_stylesheet_directory(), get_theme_root() . '/serhandemirel' ) as $dir ) {
		if ( is_dir( $dir . '/assets/img/brands/white' ) ) {
			return $dir . '/assets/img/brands';
		}
	}
	return '';
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
	if ( empty( $done['brands'] ) && sdc_seed_logo_dir() ) {
		sdc_seed_brands();
		sdc_mark_seeded( 'brands' );
	}
}

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
 * Create a brand per logo, copying both logo variants into the Media Library.
 */
function sdc_seed_brands() {
	$dir = sdc_seed_logo_dir();
	if ( ! $dir || get_posts( array( 'post_type' => 'sd_brand', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	add_filter( 'upload_mimes', 'sdc_seed_allow_svg' );

	$order = 0;
	foreach ( sdc_seed_brand_data() as $file => $name ) {
		++$order;
		$white = sdc_seed_import_image( $dir . '/white/' . $file, $name );
		$color = sdc_seed_import_image( $dir . '/color/' . $file, $name );

		wp_insert_post(
			array(
				'post_type'   => 'sd_brand',
				'post_status' => 'publish',
				'post_title'  => $name,
				'menu_order'  => $order,
				'meta_input'  => array(
					sdc_meta_key( 'logo_white' ) => $white,
					sdc_meta_key( 'logo_color' ) => $color,
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
	$folder   = basename( dirname( $path ) );
	$upload   = wp_upload_bits( 'brand-' . $folder . '-' . basename( $path ), null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local file.
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$type          = wp_check_filetype( $upload['file'] );
	$attachment_id = wp_insert_attachment(
		array(
			'post_title'     => $alt . ( 'color' === $folder ? ' (color)' : '' ),
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
 * Offer the brand import when the theme was not installed at activation.
 */
function sdc_seed_notice() {
	$done = sdc_seeded();
	if ( ! empty( $done['brands'] ) || ! current_user_can( 'manage_options' ) || ! sdc_seed_logo_dir() ) {
		return;
	}
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=sdc_seed' ), 'sdc_seed' );
	printf(
		'<div class="notice notice-info"><p>%s <a class="button button-primary" href="%s">%s</a></p></div>',
		esc_html__( 'Serhan Demirel Core: the brand logos from the theme have not been imported yet.', 'serhandemirel-core' ),
		esc_url( $url ),
		esc_html__( 'Import logos', 'serhandemirel-core' )
	);
}
add_action( 'admin_notices', 'sdc_seed_notice' );

/**
 * Handle the import button.
 */
function sdc_seed_action() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'serhandemirel-core' ) );
	}
	check_admin_referer( 'sdc_seed' );
	sdc_seed_content();
	wp_safe_redirect( admin_url( 'edit.php?post_type=sd_brand' ) );
	exit;
}
add_action( 'admin_post_sdc_seed', 'sdc_seed_action' );
