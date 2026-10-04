<?php
/**
 * Theme options: field definitions, storage and the sd_opt() accessor.
 *
 * Every option is declared once in sd_option_tabs(). Fields marked
 * "i18n" are saved per language and fall back to the default language,
 * then to the built-in default (the text the static site used). Other
 * fields are shared by every language.
 *
 * Stored in one option, "sd_theme_options":
 *   array( 'global' => array( key => value ), 'lang' => array( 'en' => array( key => value ), ... ) )
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tabs and their fields.
 *
 * Field keys: label, type (text, textarea, lines, url, email, checkbox, code,
 * image), i18n, default, help, cap (capability needed to edit).
 *
 * @return array<string, array{title: string, fields: array<string, array>}>
 */
function sd_option_tabs() {
	static $tabs = null;
	if ( null !== $tabs ) {
		return $tabs;
	}

	$tabs = array(
		'general'  => array(
			'title'  => __( 'General', 'serhandemirel' ),
			'fields' => array(
				'seo_title'       => array(
					'label'   => __( 'Default SEO title', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Serhan Demirel | Digital Solutions Provider',
					'help'    => __( 'Used for social sharing. The browser tab title comes from Settings → General.', 'serhandemirel' ),
				),
				'seo_description' => array(
					'label'   => __( 'Default SEO description', 'serhandemirel' ),
					'type'    => 'textarea',
					'i18n'    => true,
					'default' => 'Serhan Demirel - Digital Solutions Provider specializing in business transformation, scalable digital foundations, and AI & automation.',
				),
				'og_image'        => array(
					'label' => __( 'Sharing image', 'serhandemirel' ),
					'type'  => 'image',
					'help'  => __( 'Shown when the site is shared on LinkedIn, WhatsApp or X. 1200×630 works best. Empty uses the SD logo.', 'serhandemirel' ),
				),
			),
		),
		'hero'     => array(
			'title'  => __( 'Hero', 'serhandemirel' ),
			'fields' => array(
				'hero_badge'    => array(
					'label'   => __( 'Badge', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Engineering the digital future',
				),
				'hero_title'    => array(
					'label'   => __( 'Title', 'serhandemirel' ),
					'type'    => 'text',
					'default' => 'Serhan Demirel',
				),
				'hero_subtitle' => array(
					'label'   => __( 'Subtitle', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Digital Solutions Provider',
				),
				'hero_explore'  => array(
					'label'   => __( 'Scroll hint', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Explore',
				),
			),
		),
		'words'    => array(
			'title'  => __( 'Word slot', 'serhandemirel' ),
			'fields' => array(
				'words_prefix' => array(
					'label'   => __( 'Prefix', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'We can',
				),
				'words'        => array(
					'label'   => __( 'Words', 'serhandemirel' ),
					'type'    => 'lines',
					'i18n'    => true,
					'default' => "design\nprototype\nsolve\nbuild\ndevelop\ndebug\nlearn\noptimize\nship\nprompt\ncollaborate\ncreate\ntransform\nautomate\ninnovate\nscale\nintegrate\ndeploy\nsucceed",
					'help'    => __( 'One word per line, shown in this order. A full stop is added after each word.', 'serhandemirel' ),
				),
			),
		),
		'sections' => array(
			'title'  => __( 'Sections', 'serhandemirel' ),
			'fields' => array(
				'show_expertise'    => array(
					'label'   => __( 'Show the Core Expertise section', 'serhandemirel' ),
					'type'    => 'checkbox',
					'default' => true,
				),
				'expertise_eyebrow' => array(
					'label'   => __( 'Core Expertise: small heading', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Core Expertise',
				),
				'expertise_title'   => array(
					'label'   => __( 'Core Expertise: heading', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Building scalable digital foundations for tomorrow.',
				),
				'show_brands'       => array(
					'label'   => __( 'Show the Brands section', 'serhandemirel' ),
					'type'    => 'checkbox',
					'default' => true,
				),
				'brands_eyebrow'    => array(
					'label'   => __( 'Brands: small heading', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Part of the journey',
				),
				'brands_title'      => array(
					'label'   => __( 'Brands: heading', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Brands That Shaped My Story',
				),
				'show_work'         => array(
					'label'   => __( 'Show the Work section', 'serhandemirel' ),
					'type'    => 'checkbox',
					'default' => true,
					'help'    => __( 'Appears once at least one project is published. Projects ticked "Show on home page" are listed; if none are ticked, all projects are.', 'serhandemirel' ),
				),
				'work_eyebrow'      => array(
					'label'   => __( 'Work: small heading', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Selected Work',
				),
				'work_title'        => array(
					'label'   => __( 'Work: heading', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Featured Projects',
				),
				'work_all_label'    => array(
					'label'   => __( 'Work: "All" filter button', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'All',
				),
				'show_insights'     => array(
					'label'   => __( 'Show the Insights section', 'serhandemirel' ),
					'type'    => 'checkbox',
					'default' => true,
					'help'    => __( 'Appears once at least one post is published. Posts ticked "Show on home page" are listed; if none are ticked, the two newest are.', 'serhandemirel' ),
				),
				'insights_eyebrow'  => array(
					'label'   => __( 'Insights: small heading', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Insights',
				),
				'insights_title'    => array(
					'label'   => __( 'Insights: heading', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Latest Thoughts',
				),
			),
		),
		'contact'  => array(
			'title'  => __( 'Contact', 'serhandemirel' ),
			'fields' => array(
				'whatsapp_number' => array(
					'label'   => __( 'WhatsApp number', 'serhandemirel' ),
					'type'    => 'text',
					'default' => '905322702736',
					'help'    => __( 'International format, digits only, e.g. 905322702736.', 'serhandemirel' ),
				),
				'whatsapp_text'   => array(
					'label'   => __( 'WhatsApp starter message', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Hello Serhan,',
				),
				'email'           => array(
					'label'   => __( 'Email address', 'serhandemirel' ),
					'type'    => 'email',
					'default' => 'info@serhandemirel.com',
				),
				'email_subject'   => array(
					'label'   => __( 'Email subject', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Consulting',
				),
				'linkedin'        => array(
					'label'   => 'LinkedIn',
					'type'    => 'url',
					'default' => 'https://linkedin.com/in/serhandemirel',
				),
				'github'          => array(
					'label'   => 'GitHub',
					'type'    => 'url',
					'default' => 'https://github.com/serhanco',
				),
				'instagram'       => array(
					'label'   => 'Instagram',
					'type'    => 'url',
					'default' => 'https://instagram.com/serhanco',
				),
				'status_badge'    => array(
					'label'   => __( 'Status badge', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Status: Ready to Innovate',
				),
				'cta_line1'       => array(
					'label'   => __( 'Closing heading, line 1', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => "Let's build",
				),
				'cta_line2'       => array(
					'label'   => __( 'Closing heading, line 2 (gradient)', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'the future.',
				),
				'timezone'        => array(
					'label'   => __( 'Clock time zone', 'serhandemirel' ),
					'type'    => 'text',
					'default' => 'Europe/Istanbul',
					'help'    => __( 'IANA name, e.g. Europe/Istanbul or Europe/Amsterdam.', 'serhandemirel' ),
				),
				'timezone_label'  => array(
					'label'   => __( 'Time zone label', 'serhandemirel' ),
					'type'    => 'text',
					'default' => 'TRT',
				),
				'city_label'      => array(
					'label'   => __( 'City label', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Istanbul, TR',
				),
				'sticky_bar'      => array(
					'label'   => __( 'Show the floating WhatsApp / Start a Project bar', 'serhandemirel' ),
					'type'    => 'checkbox',
					'default' => true,
				),
			),
		),
		'form'     => array(
			'title'  => __( 'Form', 'serhandemirel' ),
			'fields' => array(
				'form_recipient'    => array(
					'label' => __( 'Send form messages to', 'serhandemirel' ),
					'type'  => 'email',
					'help'  => __( 'Empty uses the administration email address from Settings → General.', 'serhandemirel' ),
				),
				'form_title'        => array(
					'label'   => __( 'Title', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => "Let's Create Something Great",
				),
				'form_intro'        => array(
					'label'   => __( 'Intro', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => "Tell me about your project and I'll get back to you shortly.",
				),
				'form_button'       => array(
					'label'   => __( 'Button', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'Send Message',
				),
				'form_success_text' => array(
					'label'   => __( 'Thank-you text', 'serhandemirel' ),
					'type'    => 'text',
					'i18n'    => true,
					'default' => 'I have received your message and will get back to you shortly.',
				),
			),
		),
		'tracking' => array(
			'title'  => __( 'Tracking', 'serhandemirel' ),
			'fields' => array(
				'gtm_id'              => array(
					'label'   => __( 'Google Tag Manager ID', 'serhandemirel' ),
					'type'    => 'text',
					'default' => 'GTM-5JJSC7D5',
					'help'    => __( 'e.g. GTM-XXXXXXX.', 'serhandemirel' ),
				),
				'ga4_id'              => array(
					'label' => __( 'Google Analytics 4 ID', 'serhandemirel' ),
					'type'  => 'text',
					'help'  => __( 'e.g. G-XXXXXXXXXX. Leave empty if GA4 is set up inside Google Tag Manager, or visits are counted twice.', 'serhandemirel' ),
				),
				'meta_pixel_id'       => array(
					'label' => __( 'Meta Pixel ID', 'serhandemirel' ),
					'type'  => 'text',
				),
				'linkedin_partner_id' => array(
					'label' => __( 'LinkedIn Insight Tag partner ID', 'serhandemirel' ),
					'type'  => 'text',
				),
				'clarity_id'          => array(
					'label' => __( 'Microsoft Clarity project ID', 'serhandemirel' ),
					'type'  => 'text',
				),
				'consent_mode'        => array(
					'label'   => __( 'Google Consent Mode v2: start with consent denied', 'serhandemirel' ),
					'type'    => 'checkbox',
					'default' => true,
					'help'    => __( 'Required for EU visitors. Needs a cookie banner (e.g. Complianz or CookieYes) to grant consent; without one Google only receives cookieless pings.', 'serhandemirel' ),
				),
				'track_admins'        => array(
					'label'   => __( 'Also track logged-in editors and administrators', 'serhandemirel' ),
					'type'    => 'checkbox',
					'default' => false,
				),
				'code_head'           => array(
					'label' => __( 'Extra code: end of <head>', 'serhandemirel' ),
					'type'  => 'code',
					'cap'   => 'unfiltered_html',
				),
				'code_body'           => array(
					'label' => __( 'Extra code: start of <body>', 'serhandemirel' ),
					'type'  => 'code',
					'cap'   => 'unfiltered_html',
				),
				'code_footer'         => array(
					'label' => __( 'Extra code: end of page', 'serhandemirel' ),
					'type'  => 'code',
					'cap'   => 'unfiltered_html',
				),
			),
		),
	);

	return $tabs;
}

/**
 * Definition of one option field, or null.
 *
 * @param string $key Option key.
 * @return array|null
 */
function sd_option_field( $key ) {
	foreach ( sd_option_tabs() as $tab ) {
		if ( isset( $tab['fields'][ $key ] ) ) {
			return $tab['fields'][ $key ];
		}
	}
	return null;
}

/**
 * Languages the panel can edit, slug => name. Without Polylang: the site language only.
 *
 * @return array<string, string>
 */
function sd_languages() {
	if ( function_exists( 'pll_languages_list' ) ) {
		$slugs = pll_languages_list( array( 'fields' => 'slug' ) );
		$names = pll_languages_list( array( 'fields' => 'name' ) );
		if ( $slugs ) {
			return array_combine( $slugs, $names );
		}
	}
	return array( sd_default_lang() => sd_default_lang() );
}

/**
 * Default language slug.
 *
 * @return string
 */
function sd_default_lang() {
	if ( function_exists( 'pll_default_language' ) && pll_default_language() ) {
		return pll_default_language();
	}
	return substr( get_locale(), 0, 2 );
}

/**
 * Language of the current request.
 *
 * @return string
 */
function sd_current_lang() {
	if ( function_exists( 'pll_current_language' ) && pll_current_language() ) {
		return pll_current_language();
	}
	return sd_default_lang();
}

/**
 * Raw stored options.
 *
 * @return array{global: array, lang: array}
 */
function sd_stored_options() {
	$stored = get_option( 'sd_theme_options', array() );
	return array(
		'global' => isset( $stored['global'] ) && is_array( $stored['global'] ) ? $stored['global'] : array(),
		'lang'   => isset( $stored['lang'] ) && is_array( $stored['lang'] ) ? $stored['lang'] : array(),
	);
}

/**
 * Stored value of a field, without fallbacks; null when never saved.
 *
 * @param string      $key  Option key.
 * @param string|null $lang Language for i18n fields.
 * @return mixed
 */
function sd_stored_value( $key, $lang = null ) {
	$field  = sd_option_field( $key );
	$stored = sd_stored_options();
	if ( ! empty( $field['i18n'] ) ) {
		$lang = $lang ? $lang : sd_current_lang();
		return $stored['lang'][ $lang ][ $key ] ?? null;
	}
	return $stored['global'][ $key ] ?? null;
}

/**
 * Value of a theme option for the current language.
 *
 * i18n text left empty in a language falls back to the default language,
 * then to the built-in default. A shared field saved empty stays empty
 * (e.g. to remove a tracking ID).
 *
 * @param string $key Option key.
 * @return mixed
 */
function sd_opt( $key ) {
	$field = sd_option_field( $key );
	if ( ! $field ) {
		return null;
	}
	$default = $field['default'] ?? ( 'checkbox' === $field['type'] ? false : '' );

	if ( empty( $field['i18n'] ) ) {
		$value = sd_stored_value( $key );
		return null === $value ? $default : $value;
	}

	foreach ( array( sd_current_lang(), sd_default_lang() ) as $lang ) {
		$value = sd_stored_value( $key, $lang );
		if ( null !== $value && '' !== $value ) {
			return $value;
		}
	}
	return $default;
}

/**
 * A "lines" option as a list of non-empty lines.
 *
 * @param string $key Option key.
 * @return string[]
 */
function sd_opt_lines( $key ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) sd_opt( $key ) ) ) ) );
}

/**
 * Clean a submitted option value.
 *
 * @param mixed $value Raw value.
 * @param array $field Field definition.
 * @return mixed
 */
function sd_sanitize_option( $value, $field ) {
	switch ( $field['type'] ) {
		case 'checkbox':
			return (bool) $value;
		case 'url':
			return esc_url_raw( trim( (string) $value ) );
		case 'email':
			return sanitize_email( (string) $value );
		case 'image':
			return absint( $value );
		case 'textarea':
		case 'lines':
			return sanitize_textarea_field( (string) $value );
		case 'code':
			return current_user_can( 'unfiltered_html' ) ? trim( (string) $value ) : wp_kses_post( (string) $value );
		default:
			return sanitize_text_field( (string) $value );
	}
}

/**
 * WhatsApp link with the starter message.
 *
 * @return string
 */
function sd_whatsapp_url() {
	return 'https://wa.me/' . preg_replace( '/\D+/', '', (string) sd_opt( 'whatsapp_number' ) ) . '?text=' . rawurlencode( (string) sd_opt( 'whatsapp_text' ) );
}

/**
 * mailto: link with the subject.
 *
 * @return string
 */
function sd_email_url() {
	return 'mailto:' . sd_opt( 'email' ) . '?subject=' . rawurlencode( (string) sd_opt( 'email_subject' ) );
}

/**
 * Send form messages to the address set in the panel.
 *
 * @param string $email Default recipient.
 * @return string
 */
function sd_contact_recipient( $email ) {
	$recipient = sd_opt( 'form_recipient' );
	return is_email( $recipient ) ? $recipient : $email;
}
add_filter( 'sdc_contact_recipient', 'sd_contact_recipient' );
