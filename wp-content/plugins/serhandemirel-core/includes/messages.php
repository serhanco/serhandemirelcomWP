<?php
/**
 * "Start a Project" form: the Messages post type and its AJAX endpoint.
 *
 * Moved here from the theme so submissions survive a theme change.
 * Submissions are stored as private Messages and emailed to the site admin.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin-only post type holding form submissions.
 */
function sdc_register_message_post_type() {
	register_post_type(
		'sd_message',
		array(
			'labels'          => array(
				'name'          => __( 'Messages', 'serhandemirel-core' ),
				'singular_name' => __( 'Message', 'serhandemirel-core' ),
				'edit_item'     => __( 'Message', 'serhandemirel-core' ),
				'all_items'     => __( 'All Messages', 'serhandemirel-core' ),
				'not_found'     => __( 'No messages yet.', 'serhandemirel-core' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'menu_icon'       => 'dashicons-email',
			'menu_position'   => 26,
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'sdc_register_message_post_type' );

/**
 * Language the visitor submitted the form in, as a two-letter code.
 *
 * @return string
 */
function sdc_request_language() {
	$posted = isset( $_POST['lang'] ) ? sanitize_key( wp_unslash( $_POST['lang'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked by the caller.
	if ( preg_match( '/^[a-z]{2}$/', $posted ) ) {
		return $posted;
	}
	if ( function_exists( 'pll_current_language' ) && pll_current_language() ) {
		return pll_current_language();
	}
	return substr( determine_locale(), 0, 2 );
}

/**
 * Campaign the visitor arrived from: posted fields first, then the
 * "sd_utm" cookie (JSON with "source" and "campaign") set by the theme.
 *
 * @return array{source: string, campaign: string}
 */
function sdc_request_utm() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- checked by the caller.
	$utm = array(
		'source'   => isset( $_POST['utm_source'] ) ? sanitize_text_field( wp_unslash( $_POST['utm_source'] ) ) : '',
		'campaign' => isset( $_POST['utm_campaign'] ) ? sanitize_text_field( wp_unslash( $_POST['utm_campaign'] ) ) : '',
	);
	// phpcs:enable
	if ( '' === $utm['source'] && ! empty( $_COOKIE['sd_utm'] ) ) {
		$cookie = json_decode( wp_unslash( $_COOKIE['sd_utm'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- fields sanitized below.
		if ( is_array( $cookie ) ) {
			$utm['source']   = sanitize_text_field( (string) ( $cookie['source'] ?? '' ) );
			$utm['campaign'] = sanitize_text_field( (string) ( $cookie['campaign'] ?? '' ) );
		}
	}
	return $utm;
}

/**
 * AJAX endpoint. Responds with the same JSON shape the static site used:
 * { "status": "success"|"error", "message": "..." }.
 */
function sdc_handle_contact() {
	if ( ! check_ajax_referer( 'sd_contact', 'nonce', false ) ) {
		wp_send_json( array( 'status' => 'error', 'message' => __( 'Your session expired. Please reload the page and try again.', 'serhandemirel-core' ) ), 403 );
	}

	$raw_email = trim( wp_unslash( $_POST['Email'] ?? '' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
	$name      = sanitize_text_field( wp_unslash( $_POST['Name'] ?? '' ) );
	$email     = sanitize_email( $raw_email );
	$message   = sanitize_textarea_field( wp_unslash( $_POST['Message'] ?? '' ) );

	if ( '' === $name || '' === $message || '' === $raw_email ) {
		wp_send_json( array( 'status' => 'error', 'message' => __( 'Please fill in all fields.', 'serhandemirel-core' ) ), 400 );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json( array( 'status' => 'error', 'message' => __( 'Invalid email format.', 'serhandemirel-core' ) ), 400 );
	}

	$lang   = sdc_request_language();
	$utm    = sdc_request_utm();
	$source = isset( $_POST['source'] ) ? esc_url_raw( wp_unslash( $_POST['source'] ) ) : (string) wp_get_referer();

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'sd_message',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s (%s)', $name, $email ),
			'post_content' => $message,
			'meta_input'   => array(
				sdc_meta_key( 'status' )       => 'new',
				sdc_meta_key( 'name' )         => $name,
				sdc_meta_key( 'email' )        => $email,
				sdc_meta_key( 'lang' )         => $lang,
				sdc_meta_key( 'source_url' )   => $source,
				sdc_meta_key( 'utm_source' )   => $utm['source'],
				sdc_meta_key( 'utm_campaign' ) => $utm['campaign'],
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		wp_send_json( array( 'status' => 'error', 'message' => __( 'Failed to save your message. Please try again later.', 'serhandemirel-core' ) ), 500 );
	}

	$lines = array(
		'Name: ' . $name,
		'Email: ' . $email,
		'Language: ' . strtoupper( $lang ),
	);
	if ( $source ) {
		$lines[] = 'Page: ' . $source;
	}
	if ( $utm['source'] ) {
		$lines[] = 'Campaign: ' . trim( $utm['source'] . ' / ' . $utm['campaign'], ' /' );
	}

	/**
	 * Address that receives form messages; the theme's Form settings can change it.
	 *
	 * @param string $email Defaults to the site admin address.
	 */
	$recipient = apply_filters( 'sdc_contact_recipient', get_option( 'admin_email' ) );

	wp_mail(
		$recipient,
		sprintf( 'New project inquiry from %s', $name ),
		implode( "\n", $lines ) . "\n\n" . $message . "\n\n" . admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	wp_send_json( array( 'status' => 'success', 'message' => __( 'Your message has been sent successfully. I will get back to you soon!', 'serhandemirel-core' ) ), 200 );
}
add_action( 'wp_ajax_sd_contact', 'sdc_handle_contact' );
add_action( 'wp_ajax_nopriv_sd_contact', 'sdc_handle_contact' );

/**
 * Show the number of new messages next to the menu item.
 */
function sdc_message_menu_count() {
	global $menu;
	$new = get_posts(
		array(
			'post_type'      => 'sd_message',
			'post_status'    => 'private',
			'posts_per_page' => 100,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- admin only, small table.
				'relation' => 'OR',
				array(
					'key'   => sdc_meta_key( 'status' ),
					'value' => 'new',
				),
				array(
					'key'     => sdc_meta_key( 'status' ),
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);
	if ( ! $new || ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( 'edit.php?post_type=sd_message' === $item[2] ) {
			$menu[ $i ][0] .= sprintf( ' <span class="awaiting-mod"><span class="pending-count">%d</span></span>', count( $new ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- standard way to add a menu badge.
		}
	}
}
add_action( 'admin_menu', 'sdc_message_menu_count', 99 );
