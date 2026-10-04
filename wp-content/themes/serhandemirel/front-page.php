<?php
/**
 * One-page front page.
 *
 * @package serhandemirel
 */

get_header();

get_template_part( 'template-parts/hero' );
get_template_part( 'template-parts/word-slot' );
foreach ( array( 'expertise', 'brands', 'work', 'insights' ) as $sd_section ) {
	if ( sd_show_section( $sd_section ) ) {
		get_template_part( 'template-parts/' . $sd_section );
	}
}
get_template_part( 'template-parts/contact' );

get_footer();
