<?php
/**
 * Serhan Demirel theme functions.
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SD_THEME_VERSION', '1.2.0' );

/*
 * Content types, fields, the contact form, profile, structured data,
 * llms.txt and site setup. They used to live in the "Serhan Demirel Core"
 * plugin, which loads first and declares the same functions: while it is
 * still active, its copy is used and an admin notice asks to remove it.
 * Content, settings and messages are stored the same way by both.
 */
if ( ! defined( 'SDC_VERSION' ) ) {
	foreach ( array( 'post-types', 'fields', 'meta-boxes', 'admin-columns', 'messages', 'multilingual', 'api', 'profile', 'schema', 'llms', 'setup', 'seed', 'seed-services' ) as $sd_file ) {
		require get_template_directory() . '/inc/' . $sd_file . '.php';
	}
}
require get_template_directory() . '/inc/brands.php';
require get_template_directory() . '/inc/options.php';
require get_template_directory() . '/inc/options-page.php';
require get_template_directory() . '/inc/tracking.php';
require get_template_directory() . '/inc/content.php';
require get_template_directory() . '/inc/i18n.php';

/**
 * Ask to remove the old companion plugin: the theme now includes everything it did.
 */
function sd_old_core_plugin_notice() {
	if ( ! defined( 'SDC_VERSION' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'The Serhan Demirel theme now includes everything the "Serhan Demirel Core" plugin did. Deactivate and delete the plugin under Plugins; your projects, services, messages and profile stay as they are.', 'serhandemirel' ) . '</p></div>';
}
add_action( 'admin_notices', 'sd_old_core_plugin_notice' );

/**
 * Theme setup.
 */
function sd_setup() {
	load_theme_textdomain( 'serhandemirel', get_template_directory() . '/languages' );
	register_nav_menus(
		array(
			'primary' => __( 'Main menu', 'serhandemirel' ),
		)
	);
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'script', 'style', 'search-form', 'gallery', 'caption' ) );
}
add_action( 'after_setup_theme', 'sd_setup' );

/**
 * Use "|" between site title and tagline, as the static site did.
 */
function sd_title_separator() {
	return '|';
}
add_filter( 'document_title_separator', 'sd_title_separator' );

/**
 * Styles and scripts.
 */
function sd_enqueue_assets() {
	$uri = get_template_directory_uri();

	// Built from the theme's classes with `npm run build` (see README).
	wp_enqueue_style( 'sd-tailwind', $uri . '/assets/css/tailwind.css', array(), filemtime( get_template_directory() . '/assets/css/tailwind.css' ) );
	// style.css also holds the self-hosted Inter font faces.
	wp_enqueue_style( 'sd-style', get_stylesheet_uri(), array( 'sd-tailwind' ), SD_THEME_VERSION );

	// Served from the theme, not a CDN: no third-party requests before the page can animate.
	wp_enqueue_script( 'gsap', $uri . '/assets/vendor/gsap.min.js', array(), '3.12.2', true );
	wp_enqueue_script( 'gsap-scrolltrigger', $uri . '/assets/vendor/ScrollTrigger.min.js', array( 'gsap' ), '3.12.2', true );
	wp_enqueue_script( 'lenis', $uri . '/assets/vendor/lenis.min.js', array(), '1.1.13', true );

	wp_enqueue_script( 'sd-main', $uri . '/assets/js/main.js', array( 'gsap', 'gsap-scrolltrigger', 'lenis' ), SD_THEME_VERSION, true );
	wp_localize_script(
		'sd-main',
		'sdMain',
		array(
			'words'         => sd_opt_lines( 'words' ),
			'timezone'      => sd_opt( 'timezone' ),
			'timezoneLabel' => sd_opt( 'timezone_label' ),
		)
	);

	wp_enqueue_script( 'sd-brands', $uri . '/assets/js/brands.js', array(), SD_THEME_VERSION, true );
	wp_localize_script(
		'sd-brands',
		'sdBrands',
		array(
			'logos' => sd_marquee_logos(),
		)
	);

	wp_enqueue_script( 'sd-project-modal', $uri . '/assets/js/project-modal.js', array(), SD_THEME_VERSION, true );
	wp_localize_script(
		'sd-project-modal',
		'sdContact',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'action'  => 'sd_contact',
			'lang'    => sd_current_lang(),
			'i18n'    => array(
				'sending' => __( 'Sending...', 'serhandemirel' ),
				'error'   => __( 'Something went wrong. Please try again.', 'serhandemirel' ),
				'network' => __( 'Network error. Please try again later.', 'serhandemirel' ),
				'friend'  => __( 'Friend', 'serhandemirel' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'sd_enqueue_assets' );

/**
 * Drop block-editor front-end styles so the page renders like the static site.
 */
function sd_dequeue_block_styles() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
}
add_action( 'wp_enqueue_scripts', 'sd_dequeue_block_styles', 100 );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * Preload the Latin Inter file so text does not swap fonts after the first paint.
 */
function sd_preload_font() {
	printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( get_template_directory_uri() . '/assets/fonts/inter-latin-wght-normal.woff2' ) );
}
add_action( 'wp_head', 'sd_preload_font', 1 );

/**
 * Whether an SEO plugin prints description and social tags itself.
 *
 * @return bool
 */
function sd_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Meta, social and favicon tags. Description and social tags come from the
 * General settings tab and are left to an SEO plugin when one is active.
 */
function sd_head_meta() {
	$icons = get_template_directory_uri() . '/assets/icons/';
	?>
	<meta name="author" content="Serhan Demirel">
	<meta name="theme-color" content="#050505">

	<!-- Favicons -->
	<?php if ( ! has_site_icon() ) : ?>
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $icons . 'apple-touch-icon.png' ); ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( $icons . 'favicon-32x32.png' ); ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( $icons . 'favicon-16x16.png' ); ?>">
	<link rel="shortcut icon" href="<?php echo esc_url( $icons . 'favicon.ico' ); ?>">
	<?php endif; ?>
	<link rel="manifest" href="<?php echo esc_url( get_template_directory_uri() . '/site.webmanifest' ); ?>">
	<?php
	if ( sd_has_seo_plugin() ) {
		return;
	}

	$title = sd_opt( 'seo_title' );
	$desc  = sd_opt( 'seo_description' );
	$image = sd_opt( 'og_image' ) ? wp_get_attachment_image_url( (int) sd_opt( 'og_image' ), 'full' ) : '';
	$image = $image ? $image : get_template_directory_uri() . '/assets/img/SD-logo-300px.webp';
	$url   = is_singular() ? get_permalink() : home_url( '/' );
	if ( is_singular() && ! is_front_page() ) {
		$title = wp_get_document_title();
		$desc  = has_excerpt() ? get_the_excerpt() : $desc;
	}
	?>
	<meta name="description" content="<?php echo esc_attr( $desc ); ?>">

	<!-- Open Graph / Facebook / LinkedIn / WhatsApp -->
	<meta property="og:type" content="<?php echo is_singular( 'post' ) ? 'article' : 'website'; ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>">

	<!-- Twitter / X -->
	<meta property="twitter:card" content="summary_large_image">
	<meta property="twitter:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="twitter:description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="twitter:image" content="<?php echo esc_url( $image ); ?>">
	<?php
}
add_action( 'wp_head', 'sd_head_meta', 1 );

/**
 * Prefix for in-page anchors: "#contact" on the front page, "/#contact" elsewhere.
 *
 * @param string $anchor Anchor including the leading "#".
 * @return string
 */
function sd_anchor( $anchor ) {
	return is_front_page() ? $anchor : home_url( '/' ) . $anchor;
}
