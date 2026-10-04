<?php
/**
 * Plugin Name: Serhan Demirel Core
 * Plugin URI:  https://serhandemirel.com
 * Description: Content types, custom fields and the contact form for serhandemirel.com. Keeps projects, expertise cards, brand logos and messages safe when the theme changes.
 * Version:     1.0.0
 * Author:      Serhan Demirel
 * Author URI:  https://serhandemirel.com
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License:     All rights reserved
 * Text Domain: serhandemirel-core
 * Domain Path: /languages
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SDC_VERSION', '1.0.0' );
define( 'SDC_FILE', __FILE__ );
define( 'SDC_DIR', plugin_dir_path( __FILE__ ) );
define( 'SDC_URL', plugin_dir_url( __FILE__ ) );

require SDC_DIR . 'includes/post-types.php';
require SDC_DIR . 'includes/fields.php';
require SDC_DIR . 'includes/meta-boxes.php';
require SDC_DIR . 'includes/admin-columns.php';
require SDC_DIR . 'includes/messages.php';
require SDC_DIR . 'includes/multilingual.php';
require SDC_DIR . 'includes/seed.php';
require SDC_DIR . 'includes/api.php';

/**
 * Load translations for admin labels.
 */
function sdc_load_textdomain() {
	load_plugin_textdomain( 'serhandemirel-core', false, dirname( plugin_basename( SDC_FILE ) ) . '/languages' );
}
add_action( 'init', 'sdc_load_textdomain', 0 );

/**
 * On activation: register types so rewrite rules include them, import the
 * starter content once, then flush permalinks.
 */
function sdc_activate() {
	sdc_register_post_types();
	sdc_register_taxonomies();
	sdc_seed_content();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'sdc_activate' );

/**
 * Drop our rewrite rules on deactivation.
 */
function sdc_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'sdc_deactivate' );
