<?php
/**
 * Custom field definitions and registration.
 *
 * Every field is declared once in sdc_field_groups(); the same list drives
 * meta registration, the edit screen boxes, sanitizing and which fields are
 * shared between translations ("shared" => true) or entered per language.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta keys are stored as "_sd_<field>", hidden from the core Custom Fields box.
 *
 * @param string $field Field name.
 * @return string
 */
function sdc_meta_key( $field ) {
	return '_sd_' . $field;
}

/**
 * All field groups.
 *
 * Group keys: title, post_types, context (meta box position), fields.
 * Field keys: label, type, shared, help, options (select), post_type (post),
 * default, cap (capability needed to edit), rows (textarea height).
 *
 * @return array<string, array>
 */
function sdc_field_groups() {
	static $groups = null;
	if ( null !== $groups ) {
		return $groups;
	}

	$groups = array(
		'project'   => array(
			'title'      => __( 'Project details', 'serhandemirel-core' ),
			'post_types' => array( 'sd_project' ),
			'context'    => 'normal',
			'fields'     => array(
				'client'   => array(
					'label'  => __( 'Client name', 'serhandemirel-core' ),
					'type'   => 'text',
					'shared' => true,
				),
				'location' => array(
					'label' => __( 'Location label', 'serhandemirel-core' ),
					'type'  => 'text',
					'help'  => __( 'Shown above the title on the card, e.g. "Fintech / London".', 'serhandemirel-core' ),
				),
				'summary'  => array(
					'label' => __( 'Short summary (card text)', 'serhandemirel-core' ),
					'type'  => 'textarea',
				),
				'metric'   => array(
					'label' => __( 'Result metric', 'serhandemirel-core' ),
					'type'  => 'text',
					'help'  => __( 'e.g. "+24% conversion".', 'serhandemirel-core' ),
				),
				'year'     => array(
					'label'  => __( 'Year', 'serhandemirel-core' ),
					'type'   => 'number',
					'shared' => true,
				),
				'url'      => array(
					'label'  => __( 'Live site URL', 'serhandemirel-core' ),
					'type'   => 'url',
					'shared' => true,
				),
				'brand'    => array(
					'label'     => __( 'Brand', 'serhandemirel-core' ),
					'type'      => 'post',
					'post_type' => 'sd_brand',
					'shared'    => true,
					'help'      => __( 'The brand logo is shown as a small badge on the card.', 'serhandemirel-core' ),
				),
				'gallery'  => array(
					'label'  => __( 'Gallery', 'serhandemirel-core' ),
					'type'   => 'gallery',
					'shared' => true,
					'help'   => __( 'Images for the project detail page.', 'serhandemirel-core' ),
				),
				'featured' => array(
					'label'  => __( 'Show on home page', 'serhandemirel-core' ),
					'type'   => 'checkbox',
					'shared' => true,
				),
			),
		),
		'service'   => array(
			'title'      => __( 'Service details', 'serhandemirel-core' ),
			'post_types' => array( 'sd_service_page' ),
			'context'    => 'normal',
			'fields'     => array(
				'definition'       => array(
					'label' => __( 'Definition (40–60 words)', 'serhandemirel-core' ),
					'type'  => 'textarea',
					'help'  => __( 'Answers "What is this service?" in plain words. Shown first on the page and used in search results and AI answers.', 'serhandemirel-core' ),
				),
				'who_for'          => array(
					'label' => __( 'Who it is for', 'serhandemirel-core' ),
					'type'  => 'textarea',
				),
				'deliverables'     => array(
					'label' => __( 'Deliverables', 'serhandemirel-core' ),
					'type'  => 'textarea',
					'rows'  => 6,
					'help'  => __( 'One per line.', 'serhandemirel-core' ),
				),
				'process'          => array(
					'label' => __( 'Process', 'serhandemirel-core' ),
					'type'  => 'textarea',
					'rows'  => 6,
					'help'  => __( 'One step per line, as "Step name: what happens".', 'serhandemirel-core' ),
				),
				'duration'         => array(
					'label' => __( 'Typical duration', 'serhandemirel-core' ),
					'type'  => 'text',
					'help'  => __( 'e.g. "4–8 weeks".', 'serhandemirel-core' ),
				),
				'engagement_model' => array(
					'label'   => __( 'Engagement model', 'serhandemirel-core' ),
					'type'    => 'select',
					'shared'  => true,
					'default' => 'project',
					'options' => array(
						'project'  => __( 'Fixed-scope project', 'serhandemirel-core' ),
						'retainer' => __( 'Monthly retainer', 'serhandemirel-core' ),
						'workshop' => __( 'Workshop or training', 'serhandemirel-core' ),
						'hourly'   => __( 'Hourly consulting', 'serhandemirel-core' ),
					),
				),
				'price_from'       => array(
					'label'  => __( 'Price from', 'serhandemirel-core' ),
					'type'   => 'number',
					'shared' => true,
					'help'   => __( 'Leave empty to show no price.', 'serhandemirel-core' ),
				),
				'currency'         => array(
					'label'   => __( 'Currency', 'serhandemirel-core' ),
					'type'    => 'select',
					'shared'  => true,
					'default' => 'EUR',
					'options' => array(
						'EUR' => 'EUR',
						'USD' => 'USD',
						'GBP' => 'GBP',
						'TRY' => 'TRY',
					),
				),
				'area_served'      => array(
					'label'   => __( 'Area served', 'serhandemirel-core' ),
					'type'    => 'text',
					'shared'  => true,
					'default' => 'Worldwide',
					'help'    => __( 'Countries, or "Worldwide" for remote work.', 'serhandemirel-core' ),
				),
				'faq'              => array(
					'label' => __( 'Frequently asked questions', 'serhandemirel-core' ),
					'type'  => 'textarea',
					'rows'  => 10,
					'help'  => __( 'A question on one line, its answer on the next lines; an empty line between questions.', 'serhandemirel-core' ),
				),
				'reviewed_date'    => array(
					'label'  => __( 'Last reviewed', 'serhandemirel-core' ),
					'type'   => 'date',
					'shared' => true,
					'help'   => __( 'Shown as "Last updated" on the page. Empty uses the last edit date.', 'serhandemirel-core' ),
				),
			),
		),
		'expertise' => array(
			'title'      => __( 'Card content', 'serhandemirel-core' ),
			'post_types' => array( 'sd_expertise' ),
			'context'    => 'normal',
			'fields'     => array(
				'description' => array(
					'label' => __( 'Description', 'serhandemirel-core' ),
					'type'  => 'textarea',
				),
				'tags'        => array(
					'label' => __( 'Tags', 'serhandemirel-core' ),
					'type'  => 'text',
					'help'  => __( 'Comma separated, e.g. "Sales Optimization, Lead Gen".', 'serhandemirel-core' ),
				),
				'accent'      => array(
					'label'   => __( 'Accent color', 'serhandemirel-core' ),
					'type'    => 'select',
					'shared'  => true,
					'default' => 'blue',
					'options' => array(
						'blue'    => __( 'Blue', 'serhandemirel-core' ),
						'purple'  => __( 'Purple', 'serhandemirel-core' ),
						'emerald' => __( 'Green', 'serhandemirel-core' ),
						'pink'    => __( 'Pink', 'serhandemirel-core' ),
						'amber'   => __( 'Amber', 'serhandemirel-core' ),
					),
				),
				'service_page' => array(
					'label'     => __( 'Service page', 'serhandemirel-core' ),
					'type'      => 'post',
					'post_type' => 'sd_service_page',
					'shared'    => true,
					'help'      => __( 'The card links to this page. In other languages it links to the page\'s translation.', 'serhandemirel-core' ),
				),
				'icon'        => array(
					'label'   => __( 'Icon', 'serhandemirel-core' ),
					'type'    => 'select',
					'shared'  => true,
					'default' => 'growth',
					'options' => array(
						'growth' => __( 'Growth chart', 'serhandemirel-core' ),
						'code'   => __( 'Code', 'serhandemirel-core' ),
						'flask'  => __( 'Flask', 'serhandemirel-core' ),
						'chart'  => __( 'Bar chart', 'serhandemirel-core' ),
						'book'   => __( 'Book', 'serhandemirel-core' ),
					),
				),
			),
		),
		'brand'     => array(
			'title'      => __( 'Logo', 'serhandemirel-core' ),
			'post_types' => array( 'sd_brand' ),
			'context'    => 'normal',
			'fields'     => array(
				'logo_white' => array(
					'label'  => __( 'White logo', 'serhandemirel-core' ),
					'type'   => 'image',
					'shared' => true,
					'help'   => __( 'Shown in the logo band on the dark background.', 'serhandemirel-core' ),
				),
				'logo_color' => array(
					'label'  => __( 'Color logo', 'serhandemirel-core' ),
					'type'   => 'image',
					'shared' => true,
					'help'   => __( 'Optional. Used where the logo sits on a light background.', 'serhandemirel-core' ),
				),
				'url'        => array(
					'label'  => __( 'Website', 'serhandemirel-core' ),
					'type'   => 'url',
					'shared' => true,
					'help'   => __( 'Leave empty to keep the logo unlinked.', 'serhandemirel-core' ),
				),
				'visible'    => array(
					'label'   => __( 'Show in the logo band', 'serhandemirel-core' ),
					'type'    => 'checkbox',
					'shared'  => true,
					'default' => true,
				),
			),
		),
		'insight'   => array(
			'title'      => __( 'Insight details', 'serhandemirel-core' ),
			'post_types' => array( 'post' ),
			'context'    => 'side',
			'fields'     => array(
				'read_time' => array(
					'label' => __( 'Reading time (minutes)', 'serhandemirel-core' ),
					'type'  => 'number',
					'help'  => __( 'Leave empty to calculate from the word count.', 'serhandemirel-core' ),
				),
				'featured'  => array(
					'label'  => __( 'Show on home page', 'serhandemirel-core' ),
					'type'   => 'checkbox',
					'shared' => true,
					'help'   => __( 'If no post is ticked, the two newest posts are shown.', 'serhandemirel-core' ),
				),
			),
		),
		'tracking'  => array(
			'title'      => __( 'Tracking on this page', 'serhandemirel-core' ),
			'post_types' => array( 'page', 'post', 'sd_project', 'sd_service_page' ),
			'context'    => 'side',
			'fields'     => array(
				'disable_tracking' => array(
					'label'  => __( 'Turn off tracking codes on this page', 'serhandemirel-core' ),
					'type'   => 'checkbox',
					'shared' => true,
				),
				'extra_head_code'  => array(
					'label'  => __( 'Extra code for this page', 'serhandemirel-core' ),
					'type'   => 'code',
					'shared' => true,
					'cap'    => 'unfiltered_html',
					'help'   => __( 'Printed in <head>, e.g. a conversion pixel for a campaign page.', 'serhandemirel-core' ),
				),
			),
		),
		'message'   => array(
			'title'      => __( 'Message details', 'serhandemirel-core' ),
			'post_types' => array( 'sd_message' ),
			'context'    => 'side',
			'fields'     => array(
				'status'       => array(
					'label'   => __( 'Status', 'serhandemirel-core' ),
					'type'    => 'select',
					'default' => 'new',
					'options' => array(
						'new'      => __( 'New', 'serhandemirel-core' ),
						'replied'  => __( 'Replied', 'serhandemirel-core' ),
						'archived' => __( 'Archived', 'serhandemirel-core' ),
					),
				),
				'name'         => array(
					'label' => __( 'Name', 'serhandemirel-core' ),
					'type'  => 'text',
				),
				'email'        => array(
					'label' => __( 'Email', 'serhandemirel-core' ),
					'type'  => 'email',
				),
				'lang'         => array(
					'label' => __( 'Language', 'serhandemirel-core' ),
					'type'  => 'text',
				),
				'source_url'   => array(
					'label' => __( 'Source page', 'serhandemirel-core' ),
					'type'  => 'url',
				),
				'utm_source'   => array(
					'label' => __( 'UTM source', 'serhandemirel-core' ),
					'type'  => 'text',
				),
				'utm_campaign' => array(
					'label' => __( 'UTM campaign', 'serhandemirel-core' ),
					'type'  => 'text',
				),
			),
		),
	);

	return $groups;
}

/**
 * Field definitions for one post type, keyed by field name.
 *
 * @param string $post_type Post type.
 * @return array<string, array>
 */
function sdc_fields_for( $post_type ) {
	$fields = array();
	foreach ( sdc_field_groups() as $group ) {
		if ( in_array( $post_type, $group['post_types'], true ) ) {
			$fields += $group['fields'];
		}
	}
	return $fields;
}

/**
 * Meta keys that are copied between translations, for a post type.
 *
 * @param string $post_type Post type.
 * @param bool   $shared    True for shared keys, false for per-language keys.
 * @return string[]
 */
function sdc_meta_keys_for( $post_type, $shared ) {
	$keys = array();
	foreach ( sdc_fields_for( $post_type ) as $name => $field ) {
		if ( ! empty( $field['shared'] ) === $shared ) {
			$keys[] = sdc_meta_key( $name );
		}
	}
	return $keys;
}

/**
 * Storage type of a field for register_post_meta().
 *
 * @param array $field Field definition.
 * @return string
 */
function sdc_field_meta_type( $field ) {
	switch ( $field['type'] ) {
		case 'checkbox':
			return 'boolean';
		case 'number':
		case 'image':
		case 'post':
			return 'integer';
		default:
			return 'string';
	}
}

/**
 * Clean a submitted value according to its field type.
 *
 * @param mixed $value Raw value.
 * @param array $field Field definition.
 * @return mixed
 */
function sdc_sanitize_field( $value, $field ) {
	switch ( $field['type'] ) {
		case 'checkbox':
			return (bool) $value;
		case 'number':
		case 'image':
		case 'post':
			return '' === $value || null === $value ? 0 : absint( $value );
		case 'url':
			return esc_url_raw( trim( (string) $value ) );
		case 'email':
			return sanitize_email( (string) $value );
		case 'textarea':
			return sanitize_textarea_field( (string) $value );
		case 'select':
			$value = (string) $value;
			return isset( $field['options'][ $value ] ) ? $value : ( $field['default'] ?? '' );
		case 'date':
			$value = trim( (string) $value );
			return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ? $value : '';
		case 'gallery':
			$ids = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
			return implode( ',', $ids );
		case 'code':
			return current_user_can( 'unfiltered_html' ) ? (string) $value : wp_kses_post( (string) $value );
		default:
			return sanitize_text_field( (string) $value );
	}
}

/**
 * Register every field as post meta, so it is typed, sanitized and
 * available in the REST API and to translation plugins.
 */
function sdc_register_meta() {
	foreach ( sdc_field_groups() as $group ) {
		foreach ( $group['post_types'] as $post_type ) {
			foreach ( $group['fields'] as $name => $field ) {
				$cap  = $field['cap'] ?? 'edit_post';
				$args = array(
					'type'              => sdc_field_meta_type( $field ),
					'single'            => true,
					'show_in_rest'      => 'code' !== $field['type'],
					'sanitize_callback' => function ( $value ) use ( $field ) {
						return sdc_sanitize_field( $value, $field );
					},
					'auth_callback'     => function ( $allowed, $meta_key, $post_id ) use ( $cap ) {
						if ( 'edit_post' === $cap ) {
							return current_user_can( 'edit_post', $post_id );
						}
						return current_user_can( 'edit_post', $post_id ) && current_user_can( $cap );
					},
				);
				if ( array_key_exists( 'default', $field ) ) {
					$args['default'] = $field['default'];
				}
				register_post_meta( $post_type, sdc_meta_key( $name ), $args );
			}
		}
	}
}
add_action( 'init', 'sdc_register_meta', 20 );

/**
 * Read a field value, falling back to its registered default.
 *
 * @param int    $post_id Post ID.
 * @param string $name    Field name, without prefix.
 * @return mixed
 */
function sdc_get( $post_id, $name ) {
	return get_post_meta( $post_id, sdc_meta_key( $name ), true );
}
