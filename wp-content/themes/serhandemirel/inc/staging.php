<?php
/**
 * Test copies stay out of search engines: on a host that starts with
 * "staging." (e.g. staging.serhandemirel.com) or when WordPress runs as a
 * staging, development or local environment, the site behaves as if
 * "Discourage search engines" were ticked, whatever the setting says.
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether this is a test copy of the site.
 *
 * @return bool
 */
function sd_is_staging() {
	if ( in_array( wp_get_environment_type(), array( 'staging', 'development', 'local' ), true ) ) {
		return true;
	}
	$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	return 0 === strpos( strtolower( $host ), 'staging.' );
}

if ( sd_is_staging() ) {
	// Core then prints a noindex robots tag on every page, turns off the sitemap and leaves the AI lines out of robots.txt.
	add_filter( 'pre_option_blog_public', '__return_zero' );

	add_action(
		'send_headers',
		function () {
			header( 'X-Robots-Tag: noindex, nofollow', true );
		}
	);

	add_action(
		'admin_notices',
		function () {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			echo '<div class="notice notice-info"><p>' . esc_html__( 'This is a test copy of the site: search engines are asked not to index it, whatever Settings → Reading says.', 'serhandemirel' ) . '</p></div>';
		}
	);
}
