<?php
/**
 * List-table columns and default ordering in wp-admin.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extra columns per post type: column id => label.
 *
 * @return array<string, array<string, string>>
 */
function sdc_admin_columns() {
	return array(
		'sd_brand'     => array(
			'sdc_logo'    => __( 'Logo', 'serhandemirel-core' ),
			'sdc_visible' => __( 'Visible', 'serhandemirel-core' ),
			'sdc_order'   => __( 'Order', 'serhandemirel-core' ),
		),
		'sd_expertise' => array(
			'sdc_order' => __( 'Order', 'serhandemirel-core' ),
		),
		'sd_project'   => array(
			'sdc_client'   => __( 'Client', 'serhandemirel-core' ),
			'sdc_year'     => __( 'Year', 'serhandemirel-core' ),
			'sdc_featured' => __( 'Home page', 'serhandemirel-core' ),
			'sdc_order'    => __( 'Order', 'serhandemirel-core' ),
		),
		'sd_message'   => array(
			'sdc_email'  => __( 'Email', 'serhandemirel-core' ),
			'sdc_lang'   => __( 'Language', 'serhandemirel-core' ),
			'sdc_status' => __( 'Status', 'serhandemirel-core' ),
		),
	);
}

/**
 * Hook the columns for each post type.
 */
function sdc_register_admin_columns() {
	foreach ( sdc_admin_columns() as $post_type => $columns ) {
		add_filter(
			"manage_{$post_type}_posts_columns",
			function ( $existing ) use ( $columns ) {
				$date = $existing['date'] ?? null;
				unset( $existing['date'] );
				$existing = array_merge( $existing, $columns );
				if ( $date ) {
					$existing['date'] = $date;
				}
				return $existing;
			}
		);
		add_action( "manage_{$post_type}_posts_custom_column", 'sdc_render_admin_column', 10, 2 );
	}
}
add_action( 'admin_init', 'sdc_register_admin_columns' );

/**
 * Print a column cell.
 *
 * @param string $column  Column id.
 * @param int    $post_id Post ID.
 */
function sdc_render_admin_column( $column, $post_id ) {
	switch ( $column ) {
		case 'sdc_logo':
			$logo = sdc_attachment_src( (int) sdc_get( $post_id, 'logo_white' ), 'medium' );
			if ( $logo ) {
				echo '<img src="' . esc_url( $logo ) . '" alt="">';
			}
			break;
		case 'sdc_visible':
		case 'sdc_featured':
			$field = 'sdc_visible' === $column ? 'visible' : 'featured';
			echo sdc_get( $post_id, $field ) ? '✓' : '—';
			break;
		case 'sdc_order':
			echo (int) get_post_field( 'menu_order', $post_id );
			break;
		case 'sdc_client':
		case 'sdc_year':
		case 'sdc_email':
		case 'sdc_lang':
			$value = sdc_get( $post_id, substr( $column, 4 ) );
			echo esc_html( $value ? $value : '—' );
			break;
		case 'sdc_status':
			$options = sdc_fields_for( 'sd_message' )['status']['options'];
			$status  = sdc_get( $post_id, 'status' );
			echo esc_html( $options[ $status ] ?? $options['new'] );
			break;
	}
}

/**
 * Sort brand, expertise and project lists by their "Order" value by default.
 *
 * @param WP_Query $query Admin list query.
 */
function sdc_admin_default_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || $query->get( 'orderby' ) ) {
		return;
	}
	if ( in_array( $query->get( 'post_type' ), array( 'sd_brand', 'sd_expertise', 'sd_project' ), true ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
	}
}
add_action( 'pre_get_posts', 'sdc_admin_default_order' );
