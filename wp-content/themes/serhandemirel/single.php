<?php
/**
 * Insight (post) detail page.
 *
 * @package serhandemirel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$posts_page = (int) get_option( 'page_for_posts' );
	?>
<main class="relative overflow-hidden">
    <div class="absolute top-[-10%] right-[-20%] w-[400px] md:w-[50vw] h-[400px] md:h-[50vw] rounded-full bg-fuchsia-900/20 blur-[120px] pointer-events-none z-0"></div>

    <article <?php post_class( 'relative z-10 max-w-3xl mx-auto px-6 pt-40 pb-24' ); ?>>
        <?php if ( $posts_page ) : ?>
        <a href="<?php echo esc_url( get_permalink( $posts_page ) ); ?>" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors mb-10">← <?php echo esc_html( sd_opt( 'insights_eyebrow' ) ); ?></a>
        <?php endif; ?>

        <p class="text-gray-400 text-sm mb-4 font-medium"><?php echo esc_html( sd_insight_meta( get_the_ID() ) ); ?> • <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
        <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-tight text-white mb-10"><?php the_title(); ?></h1>

        <?php if ( has_post_thumbnail() ) : ?>
        <div class="rounded-3xl overflow-hidden glass mb-12">
            <?php the_post_thumbnail( 'full', array( 'class' => 'w-full h-auto' ) ); ?>
        </div>
        <?php endif; ?>

        <div class="sd-prose">
            <?php the_content(); ?>
        </div>
    </article>
</main>
	<?php
endwhile;

get_template_part( 'template-parts/contact' );
get_footer();
