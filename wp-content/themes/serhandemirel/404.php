<?php
/**
 * Page not found: a search box and the main places to go.
 *
 * @package serhandemirel
 */

get_header();

$sd_places = array(
	array( __( 'Home', 'serhandemirel' ), __( 'Start over', 'serhandemirel' ), home_url( '/' ) ),
);
if ( wp_count_posts( 'sd_project' )->publish ) {
	$sd_places[] = array( sd_opt( 'work_eyebrow' ), __( 'Projects and results', 'serhandemirel' ), get_post_type_archive_link( 'sd_project' ) );
}
if ( wp_count_posts( 'sd_service_page' )->publish ) {
	$sd_places[] = array( __( 'Services', 'serhandemirel' ), __( 'What I can do for you', 'serhandemirel' ), get_post_type_archive_link( 'sd_service_page' ) );
}
if ( sd_insights_url() ) {
	$sd_places[] = array( sd_opt( 'insights_eyebrow' ), __( 'Latest writing', 'serhandemirel' ), sd_insights_url() );
}
$sd_places = array_slice( $sd_places, 0, 3 );
?>
<main id="main" class="relative overflow-hidden min-h-[80vh] flex items-center">
    <div class="sd-orb w-[45rem] h-[45rem] top-0 left-1/2 -translate-x-1/2 bg-fuchsia-900/30"></div>
    <div class="relative z-10 max-w-3xl mx-auto px-6 pt-36 pb-20 text-center w-full">
        <p class="text-sm font-bold tracking-[0.2em] uppercase text-pink-500 mb-4"><?php esc_html_e( 'Error 404', 'serhandemirel' ); ?></p>
        <h1 class="text-6xl md:text-8xl font-black tracking-tighter leading-[0.9] text-white [text-wrap:balance]"><?php esc_html_e( 'This page', 'serhandemirel' ); ?><br><span class="gradient-text"><?php esc_html_e( 'drifted away.', 'serhandemirel' ); ?></span></h1>
        <p class="mt-6 text-lg text-gray-400"><?php esc_html_e( 'The link may be old or mistyped. Try a search, or pick one of the places below.', 'serhandemirel' ); ?></p>
        <div class="mt-10 max-w-xl mx-auto">
            <?php sd_search_form( 'sd-404-search', __( 'Search projects, services and insights', 'serhandemirel' ) ); ?>
        </div>
        <div class="mt-10 grid <?php echo esc_attr( array( 1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3' )[ count( $sd_places ) ] ); ?> gap-3 text-left">
            <?php foreach ( $sd_places as $sd_place ) : ?>
            <a href="<?php echo esc_url( $sd_place[2] ); ?>" class="group rounded-2xl glass p-5 hover:bg-white/[0.06] transition-colors">
                <span class="block text-xs text-gray-500"><?php echo esc_html( $sd_place[1] ); ?></span>
                <span class="mt-1 font-semibold text-white flex justify-between"><?php echo esc_html( $sd_place[0] ); ?><span class="transition-transform group-hover:translate-x-1" aria-hidden="true">→</span></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<?php
get_template_part( 'template-parts/contact' );
get_footer();
