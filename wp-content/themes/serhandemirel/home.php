<?php
/**
 * Insights: the posts page and post archives (category, tag, date, author).
 *
 * @package serhandemirel
 */

get_header();

$sd_heading = is_home() ? sd_opt( 'insights_title' ) : wp_strip_all_tags( get_the_archive_title() );
?>
<main class="relative overflow-hidden">
    <div class="absolute top-[20%] right-[10%] w-[50vw] h-[50vw] rounded-full bg-fuchsia-900/10 blur-[150px] pointer-events-none z-0"></div>

    <section class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 pt-40 pb-24">
        <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-pink-500 mb-4"><?php echo esc_html( sd_opt( 'insights_eyebrow' ) ); ?></h2>
        <h1 class="text-4xl md:text-6xl font-bold text-white mb-12"><?php echo esc_html( $sd_heading ); ?></h1>

        <?php if ( have_posts() ) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php
            $sd_index = 0;
            while ( have_posts() ) {
                the_post();
                get_template_part( 'template-parts/insight-card', null, array( 'post' => get_post(), 'index' => $sd_index++ ) );
            }
            ?>
        </div>
        <div class="mt-12 text-gray-400"><?php the_posts_pagination(); ?></div>
        <?php else : ?>
        <p class="text-gray-400"><?php esc_html_e( 'No posts yet.', 'serhandemirel' ); ?></p>
        <?php endif; ?>
    </section>
</main>
<?php
get_template_part( 'template-parts/contact' );
get_footer();
