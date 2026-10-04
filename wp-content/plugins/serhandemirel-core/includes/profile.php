<?php
/**
 * Person profile: who Serhan is, in one place.
 *
 * Feeds the structured data on every page (schema.php), llms.txt and the
 * theme's profile page. Stored in one option, "sdc_profile":
 *   array( 'global' => array( key => value ), 'lang' => array( 'en' => array( key => value ), ... ) )
 * Fields marked "i18n" are saved per language; an empty one falls back to
 * the default language, then to the built-in text.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Profile fields. Types: text, textarea, url, image; "lines" are textareas
 * read one item per line.
 *
 * @return array<string, array>
 */
function sdc_profile_fields() {
	return array(
		'full_name'        => array(
			'label'   => __( 'Full name', 'serhandemirel-core' ),
			'type'    => 'text',
			'default' => 'Serhan Demirel',
		),
		'alternate_names'  => array(
			'label' => __( 'Other spellings of the name', 'serhandemirel-core' ),
			'type'  => 'lines',
			'help'  => __( 'One per line, e.g. without Turkish characters. Helps search engines and AI tools match mentions to you.', 'serhandemirel-core' ),
		),
		'job_title'        => array(
			'label'   => __( 'Job title', 'serhandemirel-core' ),
			'type'    => 'text',
			'i18n'    => true,
			'default' => __( 'Digital Solutions Provider', 'serhandemirel-core' ),
		),
		'short_bio'        => array(
			'label'   => __( 'Short bio (about 50 words)', 'serhandemirel-core' ),
			'type'    => 'textarea',
			'i18n'    => true,
			'default' => __( 'Serhan Demirel is an Istanbul-based digital solutions provider. He helps companies grow through data-driven digital marketing, builds web products and landing pages, automates work with AI, improves search and AI visibility, and trains teams for digital transformation.', 'serhandemirel-core' ),
			'help'    => __( 'Written in the third person. Used as the description in structured data and llms.txt.', 'serhandemirel-core' ),
		),
		'long_bio'         => array(
			'label' => __( 'Long bio', 'serhandemirel-core' ),
			'type'  => 'textarea',
			'i18n'  => true,
			'rows'  => 8,
			'help'  => __( 'Shown on the profile page. Paragraphs are separated by an empty line.', 'serhandemirel-core' ),
		),
		'headshot'         => array(
			'label' => __( 'Photo', 'serhandemirel-core' ),
			'type'  => 'image',
			'help'  => __( 'A square portrait, at least 400×400.', 'serhandemirel-core' ),
		),
		'city'             => array(
			'label'   => __( 'City', 'serhandemirel-core' ),
			'type'    => 'text',
			'default' => 'Istanbul',
		),
		'country'          => array(
			'label'   => __( 'Country code', 'serhandemirel-core' ),
			'type'    => 'text',
			'default' => 'TR',
			'help'    => __( 'Two letters, e.g. TR or NL.', 'serhandemirel-core' ),
		),
		'languages_spoken' => array(
			'label' => __( 'Languages spoken', 'serhandemirel-core' ),
			'type'  => 'lines',
			'help'  => __( 'One per line, in English, e.g. Turkish.', 'serhandemirel-core' ),
		),
		'knows_about'      => array(
			'label'   => __( 'Areas of expertise', 'serhandemirel-core' ),
			'type'    => 'lines',
			'i18n'    => true,
			'default' => __( "Digital marketing\nGrowth strategy\nLead generation\nSales optimization\nWeb application development\nLanding page optimization\nAI automation\nSearch engine optimization\nGenerative engine optimization\nCompetitor analysis\nDigital transformation\nCorporate training", 'serhandemirel-core' ),
			'help'    => __( 'One topic per line.', 'serhandemirel-core' ),
		),
		'same_as'          => array(
			'label'   => __( 'Profile links', 'serhandemirel-core' ),
			'type'    => 'lines',
			'default' => "https://linkedin.com/in/serhandemirel\nhttps://github.com/serhanco\nhttps://instagram.com/serhanco",
			'help'    => __( 'One URL per line: LinkedIn, X, GitHub, Medium, Wikidata and other profiles about you.', 'serhandemirel-core' ),
		),
		'works_for'        => array(
			'label' => __( 'Company', 'serhandemirel-core' ),
			'type'  => 'text',
			'help'  => __( 'Your own company or employer, if any.', 'serhandemirel-core' ),
		),
		'works_for_url'    => array(
			'label' => __( 'Company website', 'serhandemirel-core' ),
			'type'  => 'url',
		),
		'alumni_of'        => array(
			'label' => __( 'Schools', 'serhandemirel-core' ),
			'type'  => 'lines',
			'help'  => __( 'One per line.', 'serhandemirel-core' ),
		),
		'credentials'      => array(
			'label' => __( 'Certificates', 'serhandemirel-core' ),
			'type'  => 'lines',
			'help'  => __( 'One per line, e.g. "Google Ads Search Certification".', 'serhandemirel-core' ),
		),
	);
}

/**
 * Languages to edit the profile in, slug => name.
 *
 * @return array<string, string>
 */
function sdc_profile_languages() {
	if ( function_exists( 'pll_languages_list' ) ) {
		$slugs = pll_languages_list( array( 'fields' => 'slug' ) );
		$names = pll_languages_list( array( 'fields' => 'name' ) );
		if ( $slugs ) {
			return array_combine( $slugs, $names );
		}
	}
	$lang = sdc_profile_default_lang();
	return array( $lang => $lang );
}

/**
 * Default language slug.
 *
 * @return string
 */
function sdc_profile_default_lang() {
	if ( function_exists( 'pll_default_language' ) && pll_default_language() ) {
		return pll_default_language();
	}
	return substr( get_locale(), 0, 2 );
}

/**
 * Stored profile option.
 *
 * @return array{global: array, lang: array}
 */
function sdc_profile_stored() {
	$stored = get_option( 'sdc_profile', array() );
	return array(
		'global' => isset( $stored['global'] ) && is_array( $stored['global'] ) ? $stored['global'] : array(),
		'lang'   => isset( $stored['lang'] ) && is_array( $stored['lang'] ) ? $stored['lang'] : array(),
	);
}

/**
 * Profile values for a language, with fallbacks; "lines" fields as arrays.
 *
 * @param string|null $lang Language slug; null for the current language.
 * @return array<string, mixed>
 */
function sdc_get_profile( $lang = null ) {
	if ( null === $lang ) {
		$lang = function_exists( 'pll_current_language' ) && pll_current_language() ? pll_current_language() : sdc_profile_default_lang();
	}
	$stored  = sdc_profile_stored();
	$default = sdc_profile_default_lang();
	$profile = array();
	foreach ( sdc_profile_fields() as $key => $field ) {
		if ( ! empty( $field['i18n'] ) ) {
			$value = $stored['lang'][ $lang ][ $key ] ?? '';
			if ( '' === $value ) {
				$value = $stored['lang'][ $default ][ $key ] ?? '';
			}
		} else {
			$value = $stored['global'][ $key ] ?? null;
		}
		if ( null === $value || ( '' === $value && ! empty( $field['i18n'] ) ) ) {
			$value = $field['default'] ?? '';
		}
		$profile[ $key ] = 'lines' === $field['type'] ? sdc_lines( $value ) : $value;
	}
	$profile['headshot_url'] = sdc_attachment_src( (int) $profile['headshot'] );
	return $profile;
}

/**
 * What an empty per-language field shows in a language: the default
 * language's text, else the built-in text in that language.
 *
 * @param string $key  Field key.
 * @param string $lang Language slug.
 * @return string
 */
function sdc_profile_fallback( $key, $lang ) {
	$stored  = sdc_profile_stored();
	$default = sdc_profile_default_lang();
	if ( $lang !== $default && ! empty( $stored['lang'][ $default ][ $key ] ) ) {
		return (string) $stored['lang'][ $default ][ $key ];
	}
	$locale   = function_exists( 'pll_languages_list' ) ? (string) ( array_combine( pll_languages_list( array( 'fields' => 'slug' ) ), pll_languages_list( array( 'fields' => 'locale' ) ) )[ $lang ] ?? '' ) : '';
	$switched = $locale && switch_to_locale( $locale );
	$fields   = sdc_profile_fields();
	if ( $switched ) {
		restore_previous_locale();
	}
	return (string) ( $fields[ $key ]['default'] ?? '' );
}

/**
 * Admin menu entry.
 */
function sdc_profile_menu() {
	add_menu_page( __( 'Profile', 'serhandemirel-core' ), __( 'Profile', 'serhandemirel-core' ), 'manage_options', 'sdc-profile', 'sdc_render_profile_page', 'dashicons-id-alt', 8 );
}
add_action( 'admin_menu', 'sdc_profile_menu' );

/**
 * Print the profile screen.
 */
function sdc_render_profile_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$languages = sdc_profile_languages();
	$default   = sdc_profile_default_lang();
	$lang      = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : $default; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- screen state only.
	$lang      = isset( $languages[ $lang ] ) ? $lang : $default;
	$stored    = sdc_profile_stored();
	$base      = admin_url( 'admin.php?page=sdc-profile' );
	?>
	<div class="wrap sdc-profile">
		<h1><?php esc_html_e( 'Profile', 'serhandemirel-core' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Who you are, in one place. Search engines and AI assistants read this from the structured data on every page and from /llms.txt.', 'serhandemirel-core' ); ?></p>

		<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only. ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Profile saved.', 'serhandemirel-core' ); ?></p></div>
		<?php endif; ?>

		<?php if ( count( $languages ) > 1 ) : ?>
			<p>
				<?php esc_html_e( 'Editing texts in:', 'serhandemirel-core' ); ?>
				<?php foreach ( $languages as $slug => $name ) : ?>
					<a class="button <?php echo $slug === $lang ? 'button-primary' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'lang', $slug, $base ) ); ?>"><?php echo esc_html( $name ); ?></a>
				<?php endforeach; ?>
			</p>
			<p class="description"><?php esc_html_e( 'Fields marked with a globe are saved for this language only; empty ones use the default language. Other fields are shared by all languages.', 'serhandemirel-core' ); ?></p>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="sdc_save_profile">
			<input type="hidden" name="lang" value="<?php echo esc_attr( $lang ); ?>">
			<?php wp_nonce_field( 'sdc_save_profile' ); ?>
			<div class="sdc-fields">
				<?php
				foreach ( sdc_profile_fields() as $key => $field ) {
					$i18n  = ! empty( $field['i18n'] );
					$value = $i18n ? ( $stored['lang'][ $lang ][ $key ] ?? '' ) : ( $stored['global'][ $key ] ?? ( $field['default'] ?? '' ) );
					// Per-language texts left empty show their fallback in grey and stay
					// empty when saved, so the built-in text keeps following the site language.
					$render                = $field;
					$render['placeholder'] = $i18n ? sdc_profile_fallback( $key, $lang ) : '';
					$render['type']        = 'lines' === $field['type'] ? 'textarea' : $field['type'];
					$render['rows']        = $field['rows'] ?? ( 'lines' === $field['type'] ? 5 : 3 );
					$render['label']       = $field['label'] . ( $i18n && count( $languages ) > 1 ? ' 🌐' : '' );
					sdc_render_field( $key, $render, $value );
				}
				?>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Save the profile.
 */
function sdc_save_profile() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'serhandemirel-core' ) );
	}
	check_admin_referer( 'sdc_save_profile' );

	$lang = isset( $_POST['lang'] ) ? sanitize_key( wp_unslash( $_POST['lang'] ) ) : '';
	if ( ! isset( sdc_profile_languages()[ $lang ] ) ) {
		$lang = sdc_profile_default_lang();
	}
	$submitted = isset( $_POST['sdc'] ) && is_array( $_POST['sdc'] ) ? wp_unslash( $_POST['sdc'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
	$stored    = sdc_profile_stored();

	foreach ( sdc_profile_fields() as $key => $field ) {
		if ( ! array_key_exists( $key, $submitted ) ) {
			continue;
		}
		$sanitize = $field;
		if ( 'lines' === $field['type'] ) {
			$sanitize['type'] = 'textarea';
		}
		$value = sdc_sanitize_field( $submitted[ $key ], $sanitize );
		if ( 'same_as' === $key ) {
			$value = implode( "\n", array_filter( array_map( 'esc_url_raw', sdc_lines( $value ) ) ) );
		}
		if ( ! empty( $field['i18n'] ) ) {
			$stored['lang'][ $lang ][ $key ] = $value;
		} else {
			$stored['global'][ $key ] = $value;
		}
	}
	update_option( 'sdc_profile', $stored );

	wp_safe_redirect( add_query_arg( array( 'page' => 'sdc-profile', 'lang' => $lang, 'updated' => 1 ), admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_post_sdc_save_profile', 'sdc_save_profile' );
