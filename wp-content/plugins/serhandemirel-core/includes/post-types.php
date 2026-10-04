<?php
/**
 * Post types and taxonomies.
 *
 * Projects and expertise cards are translated per language; brands and
 * messages are shared by every language (see multilingual.php).
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the content types.
 */
function sdc_register_post_types() {
	register_post_type(
		'sd_project',
		array(
			'labels'        => array(
				'name'               => __( 'Projects', 'serhandemirel-core' ),
				'singular_name'      => __( 'Project', 'serhandemirel-core' ),
				'add_new_item'       => __( 'Add New Project', 'serhandemirel-core' ),
				'edit_item'          => __( 'Edit Project', 'serhandemirel-core' ),
				'new_item'           => __( 'New Project', 'serhandemirel-core' ),
				'view_item'          => __( 'View Project', 'serhandemirel-core' ),
				'search_items'       => __( 'Search Projects', 'serhandemirel-core' ),
				'not_found'          => __( 'No projects found.', 'serhandemirel-core' ),
				'all_items'          => __( 'All Projects', 'serhandemirel-core' ),
				'featured_image'     => __( 'Card image (1200×900)', 'serhandemirel-core' ),
				'set_featured_image' => __( 'Set card image', 'serhandemirel-core' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-portfolio',
			'menu_position' => 5,
			'has_archive'   => 'work',
			'rewrite'       => array( 'slug' => 'work', 'with_front' => false ),
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions' ),
		)
	);

	register_post_type(
		'sd_expertise',
		array(
			'labels'              => array(
				'name'          => __( 'Expertise', 'serhandemirel-core' ),
				'singular_name' => __( 'Expertise card', 'serhandemirel-core' ),
				'add_new_item'  => __( 'Add New Expertise Card', 'serhandemirel-core' ),
				'edit_item'     => __( 'Edit Expertise Card', 'serhandemirel-core' ),
				'all_items'     => __( 'All Expertise Cards', 'serhandemirel-core' ),
				'not_found'     => __( 'No expertise cards found.', 'serhandemirel-core' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-lightbulb',
			'menu_position'       => 6,
			'supports'            => array( 'title', 'page-attributes' ),
		)
	);

	register_post_type(
		'sd_brand',
		array(
			'labels'              => array(
				'name'          => __( 'Brands', 'serhandemirel-core' ),
				'singular_name' => __( 'Brand', 'serhandemirel-core' ),
				'add_new_item'  => __( 'Add New Brand', 'serhandemirel-core' ),
				'edit_item'     => __( 'Edit Brand', 'serhandemirel-core' ),
				'all_items'     => __( 'All Brands', 'serhandemirel-core' ),
				'not_found'     => __( 'No brands found.', 'serhandemirel-core' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-awards',
			'menu_position'       => 7,
			'supports'            => array( 'title', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'sdc_register_post_types' );

/**
 * Register the project taxonomies.
 */
function sdc_register_taxonomies() {
	register_taxonomy(
		'sd_industry',
		'sd_project',
		array(
			'labels'            => array(
				'name'          => __( 'Industries', 'serhandemirel-core' ),
				'singular_name' => __( 'Industry', 'serhandemirel-core' ),
				'add_new_item'  => __( 'Add New Industry', 'serhandemirel-core' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'industry', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'sd_service',
		'sd_project',
		array(
			'labels'            => array(
				'name'          => __( 'Services', 'serhandemirel-core' ),
				'singular_name' => __( 'Service', 'serhandemirel-core' ),
				'add_new_item'  => __( 'Add New Service', 'serhandemirel-core' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'service', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'sdc_register_taxonomies' );
