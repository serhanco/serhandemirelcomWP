<?php
/**
 * All service pages (/services/).
 *
 * @package serhandemirel
 */

get_header();

$services = array();
while ( have_posts() ) {
	the_post();
	if ( function_exists( 'sdc_service_data' ) ) {
		$services[] = sdc_service_data( get_post() );
	}
}
$accents = array( 'text-blue-400', 'text-purple-400', 'text-emerald-400', 'text-pink-400', 'text-amber-400' );
?>
<main class="relative overflow-hidden">
    <div class="absolute bottom-[-10%] right-[-10%] w-[60vw] h-[60vw] rounded-full bg-blue-900/10 blur-[150px] pointer-events-none z-0"></div>

    <section class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 pt-40 pb-24">
        <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-purple-500 mb-4"><?php echo esc_html( sd_opt( 'expertise_eyebrow' ) ); ?></h2>
        <h1 class="text-4xl md:text-6xl font-bold text-white mb-12 max-w-3xl"><?php esc_html_e( 'Services', 'serhandemirel' ); ?></h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ( $services as $i => $service ) : ?>
            <a href="<?php echo esc_url( $service['permalink'] ); ?>" class="group glass rounded-3xl p-8 flex flex-col hover:bg-white/10 transition-colors duration-500">
                <span class="text-sm font-bold <?php echo esc_attr( $accents[ $i % count( $accents ) ] ); ?> mb-4"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
                <h2 class="text-2xl font-bold text-white mb-3"><?php echo esc_html( $service['title'] ); ?></h2>
                <p class="text-gray-400 text-sm leading-relaxed mb-6 flex-grow"><?php echo esc_html( $service['excerpt'] ? $service['excerpt'] : wp_trim_words( $service['definition'], 24 ) ); ?></p>
                <span class="text-sm font-semibold text-white"><?php esc_html_e( 'Learn more', 'serhandemirel' ); ?> <span class="inline-block transition-transform group-hover:translate-x-1">→</span></span>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="mt-12 text-gray-400"><?php the_posts_pagination(); ?></div>
    </section>
</main>
<?php
get_template_part( 'template-parts/contact' );
get_footer();
