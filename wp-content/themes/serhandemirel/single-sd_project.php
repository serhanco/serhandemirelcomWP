<?php
/**
 * Project detail page.
 *
 * @package serhandemirel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$project    = function_exists( 'sdc_project_data' ) ? sdc_project_data( get_post() ) : array();
	$industries = get_the_terms( get_the_ID(), 'sd_industry' );
	?>
<main class="relative overflow-hidden">
    <div class="absolute top-[-10%] left-[-20%] w-[400px] md:w-[50vw] h-[400px] md:h-[50vw] rounded-full bg-purple-900/20 blur-[120px] pointer-events-none z-0"></div>

    <article <?php post_class( 'relative z-10 max-w-5xl mx-auto px-6 lg:px-8 pt-40 pb-24' ); ?>>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'sd_project' ) ); ?>" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors mb-10">← <?php echo esc_html( sd_opt( 'work_eyebrow' ) ); ?></a>

        <?php if ( ! empty( $project['location'] ) ) : ?>
        <p class="text-sm font-bold tracking-[0.2em] uppercase text-blue-500 mb-4"><?php echo esc_html( $project['location'] ); ?></p>
        <?php endif; ?>
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black tracking-tight leading-tight text-white mb-6"><?php the_title(); ?></h1>

        <?php if ( ! empty( $project['metric'] ) ) : ?>
        <p class="text-3xl md:text-5xl font-bold gradient-text mb-6"><?php echo esc_html( $project['metric'] ); ?></p>
        <?php endif; ?>
        <?php if ( ! empty( $project['summary'] ) ) : ?>
        <p class="text-lg md:text-2xl text-gray-400 font-light max-w-3xl mb-10"><?php echo esc_html( $project['summary'] ); ?></p>
        <?php endif; ?>

        <div class="flex flex-wrap items-center gap-3 mb-12 text-sm">
            <?php if ( ! empty( $project['client'] ) ) : ?>
            <span class="px-4 py-2 rounded-full glass text-gray-300"><?php echo esc_html( $project['client'] ); ?></span>
            <?php endif; ?>
            <?php if ( ! empty( $project['year'] ) ) : ?>
            <span class="px-4 py-2 rounded-full glass text-gray-300"><?php echo esc_html( $project['year'] ); ?></span>
            <?php endif; ?>
            <?php foreach ( is_array( $industries ) ? $industries : array() as $industry ) : ?>
            <a href="<?php echo esc_url( get_term_link( $industry ) ); ?>" class="px-4 py-2 rounded-full glass text-gray-300 hover:text-white hover:bg-white/10 transition-colors"><?php echo esc_html( $industry->name ); ?></a>
            <?php endforeach; ?>
            <?php if ( ! empty( $project['url'] ) ) : ?>
            <a href="<?php echo esc_url( $project['url'] ); ?>" target="_blank" rel="noopener" class="px-5 py-2 rounded-full bg-white text-black font-semibold hover:bg-gray-200 transition-colors"><?php esc_html_e( 'Visit site', 'serhandemirel' ); ?> ↗</a>
            <?php endif; ?>
        </div>

        <?php if ( has_post_thumbnail() ) : ?>
        <div class="rounded-3xl overflow-hidden glass mb-16">
            <?php the_post_thumbnail( 'full', array( 'class' => 'w-full h-auto' ) ); ?>
        </div>
        <?php endif; ?>

        <div class="sd-prose max-w-3xl">
            <?php the_content(); ?>
        </div>

        <?php if ( ! empty( $project['gallery'] ) ) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-16">
            <?php foreach ( $project['gallery'] as $image_id ) : ?>
            <div class="rounded-3xl overflow-hidden glass">
                <?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'w-full h-full object-cover', 'loading' => 'lazy' ) ); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </article>
</main>
	<?php
endwhile;

get_template_part( 'template-parts/contact' );
get_footer();
