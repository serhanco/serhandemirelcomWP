<?php
/**
 * Custom field definitions and registration.
 *
 * Every field is declared once in sdc_field_groups(); the same list drives
 * meta registration, the edit screen boxes, sanitizing and which fields are
 * shared between translations ("shared" => true) or entered per language.
 *
 * @package serhandemirel
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
			'title'      => __( 'Project details', 'serhandemirel' ),
			'post_types' => array( 'sd_project' ),
			'context'    => 'normal',
			'fields'     => array(
				'client'   => array(
					'label'  => __( 'Client name', 'serhandemirel' ),
					'type'   => 'text',
					'shared' => true,
				),
				'location' => array(
					'label' => __( 'Location label', 'serhandemirel' ),
					'type'  => 'text',
					'help'  => __( 'Shown above the title on the card, e.g. "Fintech / London".', 'serhandemirel' ),
				),
				'summary'  => array(
					'label' => __( 'Short summary (card text)', 'serhandemirel' ),
					'type'  => 'textarea',
				),
				'metric'   => array(
					'label' => __( 'Result metric', 'serhandemirel' ),
					'type'  => 'text',
					'help'  => __( 'e.g. "+24% conversion".', 'serhandemirel' ),
				),
				'year'     => array(
					'label'  => __( 'Year', 'serhandemirel' ),
					'type'   => 'number',
					'shared' => true,
				),
				'url'      => array(
					'label'  => __( 'Live site URL', 'serhandemirel' ),
					'type'   => 'url',
					'shared' => true,
				),
				'brand'    => array(
					'label'     => __( 'Brand', 'serhandemirel' ),
					'type'      => 'post',
					'post_type' => 'sd_brand',
					'shared'    => true,
					'help'      => __( 'The brand logo is shown as a small badge on the card.', 'serhandemirel' ),
				),
				'gallery'  => array(
					'label'  => __( 'Gallery', 'serhandemirel' ),
					'type'   => 'gallery',
					'shared' => true,
					'help'   => __( 'Images for the project detail page.', 'serhandemirel' ),
				),
				'featured' => array(
					'label'  => __( 'Show on home page', 'serhandemirel' ),
					'type'   => 'checkbox',
					'shared' => true,
				),
			),
		),
		'service'   => array(
			'title'      => __( 'Service details', 'serhandemirel' ),
			'post_types' => array( 'sd_service_page' ),
			'context'    => 'normal',
			'fields'     => array(
				'definition'       => array(
					'label' => __( 'Definition (40–60 words)', 'serhandemirel' ),
					'type'  => 'textarea',
					'help'  => __( 'Answers "What is this service?" in plain words. Shown first on the page and used in search results and AI answers.', 'serhandemirel' ),
				),
				'who_for'          => array(
					'label' => __( 'Who it is for', 'serhandemirel' ),
					'type'  => 'textarea',
				),
				'deliverables'     => array(
					'label' => __( 'Deliverables', 'serhandemirel' ),
					'type'  => 'textarea',
					'rows'  => 6,
					'help'  => __( 'One per line.', 'serhandemirel' ),
				),
				'process'          => array(
					'label' => __( 'Process', 'serhandemirel' ),
					'type'  => 'textarea',
					'rows'  => 6,
					'help'  => __( 'One step per line, as "Step name: what happens".', 'serhandemirel' ),
				),
				'duration'         => array(
					'label' => __( 'Typical duration', 'serhandemirel' ),
					'type'  => 'text',
					'help'  => __( 'e.g. "4–8 weeks".', 'serhandemirel' ),
				),
				'engagement_model' => array(
					'label'   => __( 'Engagement model', 'serhandemirel' ),
					'type'    => 'select',
					'shared'  => true,
					'default' => 'project',
					'options' => array(
						'project'  => __( 'Fixed-scope project', 'serhandemirel' ),
						'retainer' => __( 'Monthly retainer', 'serhandemirel' ),
						'workshop' => __( 'Workshop or training', 'serhandemirel' ),
						'hourly'   => __( 'Hourly consulting', 'serhandemirel' ),
					),
				),
				'price_from'       => array(
					'label'  => __( 'Price from', 'serhandemirel' ),
					'type'   => 'number',
					'shared' => true,
					'help'   => __( 'Leave empty to show no price.', 'serhandemirel' ),
				),
				'currency'         => array(
					'label'   => __( 'Currency', 'serhandemirel' ),
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
					'label'   => __( 'Area served', 'serhandemirel' ),
					'type'    => 'text',
					'shared'  => true,
					'default' => 'Worldwide',
					'help'    => __( 'Countries, or "Worldwide" for remote work.', 'serhandemirel' ),
				),
				'faq'              => array(
					'label' => __( 'Frequently asked questions', 'serhandemirel' ),
					'type'  => 'textarea',
					'rows'  => 10,
					'help'  => __( 'A question on one line, its answer on the next lines; an empty line between questions.', 'serhandemirel' ),
				),
				'reviewed_date'    => array(
					'label'  => __( 'Last reviewed', 'serhandemirel' ),
					'type'   => 'date',
					'shared' => true,
					'help'   => __( 'Shown as "Last updated" on the page. Empty uses the last edit date.', 'serhandemirel' ),
				),
			),
		),
		'expertise' => array(
			'title'      => __( 'Card content', 'serhandemirel' ),
			'post_types' => array( 'sd_expertise' ),
			'context'    => 'normal',
			'fields'     => array(
				'description' => array(
					'label' => __( 'Description', 'serhandemirel' ),
					'type'  => 'textarea',
				),
				'tags'        => array(
					'label' => __( 'Tags', 'serhandemirel' ),
					'type'  => 'text',
					'help'  => __( 'Comma separated, e.g. "Sales Optimization, Lead Gen".', 'serhandemirel' ),
				),
				'accent'      => array(
					'label'   => __( 'Accent color', 'serhandemirel' ),
					'type'    => 'select',
					'shared'  => true,
					'default' => 'blue',
					'options' => array(
						'blue'    => __( 'Blue', 'serhandemirel' ),
						'purple'  => __( 'Purple', 'serhandemirel' ),
						'emerald' => __( 'Green', 'serhandemirel' ),
						'pink'    => __( 'Pink', 'serhandemirel' ),
						'amber'   => __( 'Amber', 'serhandemirel' ),
					),
				),
				'service_page' => array(
					'label'     => __( 'Service page', 'serhandemirel' ),
					'type'      => 'post',
					'post_type' => 'sd_service_page',
					'shared'    => true,
					'help'      => __( 'The card links to this page. In other languages it links to the page\'s translation.', 'serhandemirel' ),
				),
				'icon'        => array(
					'label'   => __( 'Icon', 'serhandemirel' ),
					'type'    => 'select',
					'shared'  => true,
					'default' => 'growth',
					'options' => array(
						'growth' => __( 'Growth chart', 'serhandemirel' ),
						'code'   => __( 'Code', 'serhandemirel' ),
						'flask'  => __( 'Flask', 'serhandemirel' ),
						'chart'  => __( 'Bar chart', 'serhandemirel' ),
						'book'   => __( 'Book', 'serhandemirel' ),
					),
				),
			),
		),
		'brand'     => array(
			'title'      => __( 'Logo', 'serhandemirel' ),
			'post_types' => array( 'sd_brand' ),
			'context'    => 'normal',
			'fields'     => array(
				'logo_white' => array(
					'label'  => __( 'White logo', 'serhandemirel' ),
					'type'   => 'image',
					'shared' => true,
					'help'   => __( 'Shown in the logo band on the dark background.', 'serhandemirel' ),
				),
				'logo_color' => array(
					'label'  => __( 'Color logo', 'serhandemirel' ),
					'type'   => 'image',
					'shared' => true,
					'help'   => __( 'Optional. Used where the logo sits on a light background.', 'serhandemirel' ),
				),
				'url'        => array(
					'label'  => __( 'Website', 'serhandemirel' ),
					'type'   => 'url',
					'shared' => true,
					'help'   => __( 'Leave empty to keep the logo unlinked.', 'serhandemirel' ),
				),
				'visible'    => array(
					'label'   => __( 'Show in the logo band', 'serhandemirel' ),
					'type'    => 'checkbox',
					'shared'  => true,
					'default' => true,
				),
			),
		),
		'insight'   => array(
			'title'      => __( 'Insight details', 'serhandemirel' ),
			'post_types' => array( 'post' ),
			'context'    => 'side',
			'fields'     => array(
				'read_time' => array(
					'label' => __( 'Reading time (minutes)', 'serhandemirel' ),
					'type'  => 'number',
					'help'  => __( 'Leave empty to calculate from the word count.', 'serhandemirel' ),
				),
				'featured'  => array(
					'label'  => __( 'Show on home page', 'serhandemirel' ),
					'type'   => 'checkbox',
					'shared' => true,
					'help'   => __( 'If no post is ticked, the two newest posts are shown.', 'serhandemirel' ),
				),
			),
		),
		'tracking'  => array(
			'title'      => __( 'Tracking on this page', 'serhandemirel' ),
			'post_types' => array( 'page', 'post', 'sd_project', 'sd_service_page' ),
			'context'    => 'side',
			'fields'     => array(
				'disable_tracking' => array(
					'label'  => __( 'Turn off tracking codes on this page', 'serhandemirel' ),
					'type'   => 'checkbox',
					'shared' => true,
				),
				'extra_head_code'  => array(
					'label'  => __( 'Extra code for this page', 'serhandemirel' ),
					'type'   => 'code',
					'shared' => true,
					'cap'    => 'unfiltered_html',
					'help'   => __( 'Printed in <head>, e.g. a conversion pixel for a campaign page.', 'serhandemirel' ),
				),
			),
		),
		'message'   => array(
			'title'      => __( 'Message details', 'serhandemirel' ),
			'post_types' => array( 'sd_message' ),
			'context'    => 'side',
			'fields'     => array(
				'status'       => array(
					'label'   => __( 'Status', 'serhandemirel' ),
					'type'    => 'select',
					'default' => 'new',
					'options' => array(
						'new'      => __( 'New', 'serhandemirel' ),
						'replied'  => __( 'Replied', 'serhandemirel' ),
						'archived' => __( 'Archived', 'serhandemirel' ),
					),
				),
				'name'         => array(
					'label' => __( 'Name', 'serhandemirel' ),
					'type'  => 'text',
				),
				'email'        => array(
					'label' => __( 'Email', 'serhandemirel' ),
					'type'  => 'email',
				),
				'lang'         => array(
					'label' => __( 'Language', 'serhandemirel' ),
					'type'  => 'text',
				),
				'source_url'   => array(
					'label' => __( 'Source page', 'serhandemirel' ),
					'type'  => 'url',
				),
				'utm_source'   => array(
					'label' => __( 'UTM source', 'serhandemirel' ),
					'type'  => 'text',
				),
				'utm_campaign' => array(
					'label' => __( 'UTM campaign', 'serhandemirel' ),
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
