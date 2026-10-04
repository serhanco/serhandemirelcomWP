<?php
/**
 * Insights: featured or latest posts.
 *
 * @package serhandemirel
 */

$posts_page = (int) get_option( 'page_for_posts' );
?>
<!-- 6. Insights Section -->
<section id="insights" class="relative py-24 md:py-32 bg-[#050505] z-10 overflow-hidden">
    <div class="absolute top-[20%] right-[10%] w-[50vw] h-[50vw] rounded-full bg-fuchsia-900/10 blur-[150px] pointer-events-none z-0"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-pink-500 mb-4"><?php echo esc_html( sd_opt( 'insights_eyebrow' ) ); ?></h2>
                <h3 class="text-3xl md:text-5xl font-bold text-white"><?php echo esc_html( sd_opt( 'insights_title' ) ); ?></h3>
            </div>
            <?php if ( $posts_page ) : ?>
            <a href="<?php echo esc_url( get_permalink( $posts_page ) ); ?>" class="px-5 py-2 rounded-full border border-white/20 text-white hover:bg-white/10 text-sm font-medium transition-colors"><?php esc_html_e( 'All insights', 'serhandemirel' ); ?> →</a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php
            foreach ( sd_insight_posts() as $i => $insight ) {
                get_template_part( 'template-parts/insight-card', null, array( 'post' => $insight, 'index' => $i ) );
            }
            ?>
        </div>
    </div>
</section>
