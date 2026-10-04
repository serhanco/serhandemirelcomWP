<?php
/**
 * Site setup: one button (or `wp sdc setup`) that configures Polylang for
 * this site, and a checklist of what still needs doing before going live.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site languages, default first.
 *
 * @return string[] WordPress locales.
 */
function sdc_setup_locales() {
	return (array) apply_filters( 'sdc_setup_locales', array( 'en_US', 'tr_TR', 'fr_FR', 'nl_NL', 'de_DE', 'it_IT' ) );
}

/**
 * Whether Polylang is loaded with the API this setup uses.
 *
 * @return bool
 */
function sdc_setup_has_polylang() {
	return function_exists( 'PLL' ) && PLL() && isset( PLL()->model ) && function_exists( 'pll_languages_list' );
}

/**
 * Add the missing languages and set Polylang's options: English at the
 * root, the others in folders (/tr/, /fr/, ...), no browser detection
 * (the theme handles that), content without a language moved to English.
 *
 * @return string[] What was done, one line each.
 */
function sdc_setup_languages() {
	if ( ! sdc_setup_has_polylang() ) {
		return array( __( 'Polylang is not active. Install and activate it first.', 'serhandemirel-core' ) );
	}
	$done     = array();
	$model    = PLL()->model;
	$existing = (array) pll_languages_list( array( 'fields' => 'locale' ) );

	if ( current_user_can( 'install_languages' ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';
	}

	foreach ( sdc_setup_locales() as $order => $locale ) {
		if ( in_array( $locale, $existing, true ) ) {
			continue;
		}
		$args   = array(
			'locale'     => $locale,
			'term_group' => $order,
		);
		$result = isset( $model->languages ) && method_exists( $model->languages, 'add' ) ? $model->languages->add( $args ) : $model->add_language( $args );
		if ( is_wp_error( $result ) ) {
			/* translators: 1: locale, 2: error message */
			$done[] = sprintf( __( 'Could not add %1$s: %2$s', 'serhandemirel-core' ), $locale, $result->get_error_message() );
			continue;
		}
		/* translators: %s: locale, e.g. fr_FR */
		$done[] = sprintf( __( 'Added %s.', 'serhandemirel-core' ), $locale );
		if ( 'en_US' !== $locale && function_exists( 'wp_download_language_pack' ) ) {
			wp_download_language_pack( $locale );
		}
	}

	$default = (string) strtok( sdc_setup_locales()[0], '_' );
	$wanted  = array(
		'default_lang'  => $default,
		'force_lang'    => 1,
		'hide_default'  => true,
		'rewrite'       => true,
		'browser'       => false,
		'redirect_lang' => false,
	);
	$options = PLL()->options;
	foreach ( $wanted as $key => $value ) {
		$options[ $key ] = $value;
	}
	if ( is_object( $options ) && method_exists( $options, 'save' ) ) {
		$options->save();
	} else {
		update_option( 'polylang', $options );
	}
	$done[] = __( 'Polylang set up: English at the site root, other languages in folders, browser detection left to the theme.', 'serhandemirel-core' );

	if ( method_exists( $model, 'set_language_in_mass' ) ) {
		$model->set_language_in_mass( $default );
		$done[] = __( 'Content without a language was set to English.', 'serhandemirel-core' );
	}

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$done[] = __( 'Permalinks set to "Post name".', 'serhandemirel-core' );
	}
	update_option( 'sdc_flush_rewrite', 1 );

	return $done;
}

/**
 * Flush permalinks on the request after setup, once Polylang has added
 * its language rules.
 */
function sdc_setup_flush_rewrite() {
	if ( get_option( 'sdc_flush_rewrite' ) ) {
		delete_option( 'sdc_flush_rewrite' );
		flush_rewrite_rules();
	}
}
add_action( 'wp_loaded', 'sdc_setup_flush_rewrite' );

/**
 * Checklist rows: label, done, what to do.
 *
 * @return array<int, array{label: string, ok: bool, fix: string}>
 */
function sdc_setup_checks() {
	$languages = sdc_setup_has_polylang() ? (array) pll_languages_list( array( 'fields' => 'locale' ) ) : array();
	$missing   = array_diff( sdc_setup_locales(), $languages );
	$profile   = get_option( 'sdc_profile', array() );
	$theme_dir = get_template_directory() . '/languages/';
	$options   = sdc_setup_has_polylang() ? PLL()->options : array();
	$services  = wp_count_posts( 'sd_service_page' );

	return array(
		array(
			'label' => __( 'Polylang is active', 'serhandemirel-core' ),
			'ok'    => sdc_setup_has_polylang(),
			'fix'   => __( 'Plugins → Add New → search "Polylang" → Install → Activate.', 'serhandemirel-core' ),
		),
		array(
			'label' => __( 'All six languages exist', 'serhandemirel-core' ),
			'ok'    => sdc_setup_has_polylang() && ! $missing,
			'fix'   => __( 'Press "Set up languages" below.', 'serhandemirel-core' ),
		),
		array(
			'label' => __( 'Polylang\'s own browser detection is off', 'serhandemirel-core' ),
			'ok'    => sdc_setup_has_polylang() && empty( $options['browser'] ),
			'fix'   => __( 'Press "Set up languages" below.', 'serhandemirel-core' ),
		),
		array(
			'label' => __( 'Pretty permalinks', 'serhandemirel-core' ),
			'ok'    => (bool) get_option( 'permalink_structure' ),
			'fix'   => __( 'Settings → Permalinks → Post name.', 'serhandemirel-core' ),
		),
		array(
			'label' => __( 'Theme translations are installed', 'serhandemirel-core' ),
			'ok'    => file_exists( $theme_dir . 'tr_TR.mo' ),
			'fix'   => __( 'Use the Serhan Demirel theme from the repository, including its languages folder.', 'serhandemirel-core' ),
		),
		array(
			'label' => __( 'Profile is filled in', 'serhandemirel-core' ),
			'ok'    => ! empty( $profile['global']['headshot'] ) && ! empty( $profile['lang'] ),
			'fix'   => __( 'Profile → add your photo, bio and profile links.', 'serhandemirel-core' ),
		),
		array(
			'label' => __( 'Service pages are published', 'serhandemirel-core' ),
			'ok'    => $services && $services->publish > 0,
			'fix'   => __( 'Services → review the drafts and publish them.', 'serhandemirel-core' ),
		),
		array(
			'label' => __( 'Search engines may index the site', 'serhandemirel-core' ),
			'ok'    => (bool) get_option( 'blog_public' ),
			'fix'   => __( 'Settings → Reading → untick "Discourage search engines".', 'serhandemirel-core' ),
		),
		array(
			'label' => __( 'Mail can be sent (for the contact form)', 'serhandemirel-core' ),
			'ok'    => defined( 'WPMS_ON' ) || class_exists( 'PostmanOptions' ) || function_exists( 'fluentSmtpInit' ) || has_action( 'phpmailer_init' ),
			'fix'   => __( 'Install an SMTP plugin (e.g. WP Mail SMTP or FluentSMTP) and send a test email.', 'serhandemirel-core' ),
		),
	);
}

/**
 * Tools → Site setup.
 */
function sdc_setup_menu() {
	add_management_page( __( 'Site setup', 'serhandemirel-core' ), __( 'Site setup', 'serhandemirel-core' ), 'manage_options', 'sdc-setup', 'sdc_render_setup_page' );
}
add_action( 'admin_menu', 'sdc_setup_menu' );

/**
 * Print the setup screen.
 */
function sdc_render_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$report = get_transient( 'sdc_setup_report' );
	delete_transient( 'sdc_setup_report' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Site setup', 'serhandemirel-core' ); ?></h1>
		<?php if ( $report ) : ?>
			<div class="notice notice-success"><ul><?php foreach ( (array) $report as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?></ul></div>
		<?php endif; ?>
		<table class="widefat striped" style="max-width:860px">
			<tbody>
			<?php foreach ( sdc_setup_checks() as $check ) : ?>
				<tr>
					<td style="width:28px"><span class="dashicons <?php echo $check['ok'] ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>" style="color:<?php echo $check['ok'] ? '#008a20' : '#dba617'; ?>"></span></td>
					<td><strong><?php echo esc_html( $check['label'] ); ?></strong><?php if ( ! $check['ok'] ) : ?><br><span class="description"><?php echo esc_html( $check['fix'] ); ?></span><?php endif; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1.5em">
			<input type="hidden" name="action" value="sdc_setup_languages">
			<?php wp_nonce_field( 'sdc_setup_languages' ); ?>
			<?php submit_button( __( 'Set up languages', 'serhandemirel-core' ), 'primary', 'submit', false, sdc_setup_has_polylang() ? array() : array( 'disabled' => 'disabled' ) ); ?>
			<p class="description"><?php esc_html_e( 'Adds English (default, at the site root), Turkish, French, Dutch, German and Italian to Polylang, downloads their WordPress translations and sets the recommended options. Safe to run again.', 'serhandemirel-core' ); ?></p>
		</form>
	</div>
	<?php
}

/**
 * Handle the button.
 */
function sdc_setup_languages_action() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'serhandemirel-core' ) );
	}
	check_admin_referer( 'sdc_setup_languages' );
	set_transient( 'sdc_setup_report', sdc_setup_languages(), 60 );
	wp_safe_redirect( admin_url( 'tools.php?page=sdc-setup' ) );
	exit;
}
add_action( 'admin_post_sdc_setup_languages', 'sdc_setup_languages_action' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * `wp sdc setup`: set up the site languages.
	 */
	WP_CLI::add_command(
		'sdc setup',
		function () {
			foreach ( sdc_setup_languages() as $line ) {
				WP_CLI::log( $line );
			}
			WP_CLI::success( __( 'Done.', 'serhandemirel-core' ) );
		}
	);
}
