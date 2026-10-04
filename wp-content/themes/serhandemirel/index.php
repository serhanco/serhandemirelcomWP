<?php
/**
 * Fallback template for any view other than the front page.
 *
 * @package serhandemirel
 */

get_header();
?>

<main class="relative max-w-3xl mx-auto px-6 pt-40 pb-24">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'mb-16' ); ?>>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-6 text-white">
                    <?php if ( is_singular() ) : ?>
                        <?php the_title(); ?>
                    <?php else : ?>
                        <a href="<?php the_permalink(); ?>" class="hover:text-gray-300 transition-colors"><?php the_title(); ?></a>
                    <?php endif; ?>
                </h1>
                <div class="text-gray-300 leading-relaxed space-y-4">
                    <?php is_singular() ? the_content() : the_excerpt(); ?>
                </div>
            </article>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <h1 class="text-4xl font-black text-white mb-6"><?php esc_html_e( 'Nothing here', 'serhandemirel' ); ?></h1>
        <p class="text-gray-400"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="underline hover:text-white"><?php esc_html_e( 'Back to the homepage', 'serhandemirel' ); ?></a></p>
    <?php endif; ?>
</main>

<?php
get_template_part( 'template-parts/contact' );
get_footer();
