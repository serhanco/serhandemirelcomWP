<?php
/**
 * Post types and taxonomies.
 *
 * Projects, service pages and expertise cards are translated per language;
 * brands and messages are shared by every language (see multilingual.php).
 *
 * @package serhandemirel
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
				'name'               => __( 'Projects', 'serhandemirel' ),
				'singular_name'      => __( 'Project', 'serhandemirel' ),
				'add_new_item'       => __( 'Add New Project', 'serhandemirel' ),
				'edit_item'          => __( 'Edit Project', 'serhandemirel' ),
				'new_item'           => __( 'New Project', 'serhandemirel' ),
				'view_item'          => __( 'View Project', 'serhandemirel' ),
				'search_items'       => __( 'Search Projects', 'serhandemirel' ),
				'not_found'          => __( 'No projects found.', 'serhandemirel' ),
				'all_items'          => __( 'All Projects', 'serhandemirel' ),
				'featured_image'     => __( 'Card image (1200×900)', 'serhandemirel' ),
				'set_featured_image' => __( 'Set card image', 'serhandemirel' ),
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
		'sd_service_page',
		array(
			'labels'        => array(
				'name'          => __( 'Services', 'serhandemirel' ),
				'singular_name' => __( 'Service page', 'serhandemirel' ),
				'add_new_item'  => __( 'Add New Service Page', 'serhandemirel' ),
				'edit_item'     => __( 'Edit Service Page', 'serhandemirel' ),
				'new_item'      => __( 'New Service Page', 'serhandemirel' ),
				'view_item'     => __( 'View Service Page', 'serhandemirel' ),
				'search_items'  => __( 'Search Service Pages', 'serhandemirel' ),
				'not_found'     => __( 'No service pages found.', 'serhandemirel' ),
				'all_items'     => __( 'All Service Pages', 'serhandemirel' ),
			),
			'description'   => __( 'One page per service: what it is, who it is for, deliverables, process and FAQ.', 'serhandemirel' ),
			'public'        => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-clipboard',
			'menu_position' => 5,
			'has_archive'   => 'services',
			'rewrite'       => array( 'slug' => 'services', 'with_front' => false ),
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions' ),
		)
	);

	register_post_type(
		'sd_expertise',
		array(
			'labels'              => array(
				'name'          => __( 'Expertise', 'serhandemirel' ),
				'singular_name' => __( 'Expertise card', 'serhandemirel' ),
				'add_new_item'  => __( 'Add New Expertise Card', 'serhandemirel' ),
				'edit_item'     => __( 'Edit Expertise Card', 'serhandemirel' ),
				'all_items'     => __( 'All Expertise Cards', 'serhandemirel' ),
				'not_found'     => __( 'No expertise cards found.', 'serhandemirel' ),
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
				'name'          => __( 'Brands', 'serhandemirel' ),
				'singular_name' => __( 'Brand', 'serhandemirel' ),
				'add_new_item'  => __( 'Add New Brand', 'serhandemirel' ),
				'edit_item'     => __( 'Edit Brand', 'serhandemirel' ),
				'all_items'     => __( 'All Brands', 'serhandemirel' ),
				'not_found'     => __( 'No brands found.', 'serhandemirel' ),
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

	register_post_type(
		'sd_testimonial',
		array(
			'labels'              => array(
				'name'          => __( 'Testimonials', 'serhandemirel' ),
				'singular_name' => __( 'Testimonial', 'serhandemirel' ),
				'add_new_item'  => __( 'Add New Testimonial', 'serhandemirel' ),
				'edit_item'     => __( 'Edit Testimonial', 'serhandemirel' ),
				'all_items'     => __( 'All Testimonials', 'serhandemirel' ),
				'not_found'     => __( 'No testimonials found.', 'serhandemirel' ),
			),
			'description'         => __( 'Client quotes, written once and shown on projects, service pages, industry pages and the profile page.', 'serhandemirel' ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-format-quote',
			'menu_position'       => 7,
			'supports'            => array( 'title', 'custom-fields' ),
		)
	);

	register_post_type(
		'sd_appearance',
		array(
			'labels'        => array(
				'name'          => __( 'Talks & press', 'serhandemirel' ),
				'singular_name' => __( 'Talk, training or press item', 'serhandemirel' ),
				'add_new_item'  => __( 'Add New Talk, Training or Press Item', 'serhandemirel' ),
				'edit_item'     => __( 'Edit Talk, Training or Press Item', 'serhandemirel' ),
				'all_items'     => __( 'All Talks & Press', 'serhandemirel' ),
				'not_found'     => __( 'Nothing here yet.', 'serhandemirel' ),
				'archives'      => __( 'Talks, trainings & press', 'serhandemirel' ),
			),
			'description'   => __( 'Talks, workshops, corporate trainings, podcasts, interviews and press mentions. Listed on one page; each item links out to the event, recording or article.', 'serhandemirel' ),
			'public'        => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-microphone',
			'menu_position' => 6,
			'has_archive'   => 'speaking',
			'rewrite'       => array( 'slug' => 'speaking', 'with_front' => false ),
			'supports'      => array( 'title', 'excerpt', 'thumbnail', 'custom-fields' ),
		)
	);

	register_post_type(
		'sd_term',
		array(
			'labels'        => array(
				'name'          => __( 'Glossary', 'serhandemirel' ),
				'singular_name' => __( 'Glossary term', 'serhandemirel' ),
				'add_new_item'  => __( 'Add New Term', 'serhandemirel' ),
				'edit_item'     => __( 'Edit Term', 'serhandemirel' ),
				'all_items'     => __( 'All Terms', 'serhandemirel' ),
				'not_found'     => __( 'No terms found.', 'serhandemirel' ),
				'archives'      => __( 'Glossary', 'serhandemirel' ),
			),
			'description'   => __( 'Short definitions of the terms you use with clients. Each term has its own page.', 'serhandemirel' ),
			'public'        => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-book-alt',
			'menu_position' => 6,
			'has_archive'   => 'glossary',
			'rewrite'       => array( 'slug' => 'glossary', 'with_front' => false ),
			'supports'      => array( 'title', 'editor', 'custom-fields', 'revisions' ),
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
		array( 'sd_project', 'sd_brand' ),
		array(
			'labels'            => array(
				'name'          => __( 'Industries', 'serhandemirel' ),
				'singular_name' => __( 'Industry', 'serhandemirel' ),
				'add_new_item'  => __( 'Add New Industry', 'serhandemirel' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'industry', 'with_front' => false ),
		)
	);

	// Services tag projects (the portfolio filter) and link each service page
	// to its projects: a service page and the projects sharing its term.
	register_taxonomy(
		'sd_service',
		array( 'sd_project', 'sd_service_page' ),
		array(
			'labels'            => array(
				'name'          => __( 'Services', 'serhandemirel' ),
				'singular_name' => __( 'Service', 'serhandemirel' ),
				'add_new_item'  => __( 'Add New Service', 'serhandemirel' ),
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

/**
 * Kinds of talks, trainings and press items.
 *
 * @return array<string, string>
 */
function sdc_appearance_kinds() {
	return array(
		'talk'      => __( 'Talk', 'serhandemirel' ),
		'workshop'  => __( 'Workshop', 'serhandemirel' ),
		'training'  => __( 'Training', 'serhandemirel' ),
		'podcast'   => __( 'Podcast', 'serhandemirel' ),
		'interview' => __( 'Interview', 'serhandemirel' ),
		'press'     => __( 'Press', 'serhandemirel' ),
	);
}

/**
 * A talk or press item has no page of its own: send its address to its
 * place in the list.
 */
function sdc_redirect_single_appearance() {
	if ( is_singular( 'sd_appearance' ) ) {
		wp_safe_redirect( get_post_type_archive_link( 'sd_appearance' ) . '#item-' . get_queried_object_id(), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'sdc_redirect_single_appearance' );
