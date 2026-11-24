<?php
/**
 * Search results, grouped by type: projects, services, insights, pages.
 *
 * @package serhandemirel
 */

get_header();

global $wp_query;
$sd_groups = sd_search_groups();
$sd_total  = (int) $wp_query->found_posts;
?>
<main id="main" class="relative overflow-hidden">
    <div class="sd-orb w-[45rem] h-[45rem] -top-80 -right-40 bg-purple-900/25"></div>

    <header class="relative z-10 max-w-6xl mx-auto px-6 pt-36 md:pt-44 pb-10">
        <p class="text-sm font-bold tracking-[0.2em] uppercase text-pink-500 mb-4"><?php esc_html_e( 'Search', 'serhandemirel' ); ?></p>
        <h1 class="text-4xl md:text-6xl font-black tracking-tighter leading-[0.95] text-white [text-wrap:balance]">
            <?php
            printf(
                /* translators: %s: search terms */
                esc_html__( 'Results for “%s”', 'serhandemirel' ),
                '<span class="gradient-text">' . esc_html( get_search_query() ) . '</span>'
            );
            ?>
        </h1>
        <p class="mt-4 text-gray-400"><?php echo esc_html( sprintf( /* translators: %d: number of results */ _n( '%d result', '%d results', $sd_total, 'serhandemirel' ), $sd_total ) ); ?></p>
        <div class="mt-8 max-w-xl"><?php sd_search_form( 'sd-search', __( 'Search again', 'serhandemirel' ) ); ?></div>
    </header>

    <?php if ( $sd_groups ) : ?>
        <?php foreach ( $sd_groups as $sd_type => $sd_group ) : ?>
        <section class="relative z-10 max-w-6xl mx-auto px-6 mt-12" aria-labelledby="sd-results-<?php echo esc_attr( $sd_type ); ?>">
            <h2 id="sd-results-<?php echo esc_attr( $sd_type ); ?>" class="flex items-baseline gap-3 text-2xl font-bold tracking-tight text-white mb-6"><?php echo esc_html( $sd_group['label'] ); ?><span class="text-sm font-medium text-gray-500"><?php echo esc_html( (string) count( $sd_group['posts'] ) ); ?></span></h2>
            <?php if ( 'sd_project' === $sd_type ) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php
                foreach ( $sd_group['posts'] as $sd_i => $sd_post ) {
                    get_template_part( 'template-parts/project-card', null, array( 'project' => sdc_project_data( $sd_post ), 'index' => $sd_i ) );
                }
                ?>
            </div>
            <?php elseif ( 'post' === $sd_type ) : ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php
                foreach ( $sd_group['posts'] as $sd_i => $sd_post ) {
                    get_template_part( 'template-parts/insight-tile', null, array( 'post' => $sd_post, 'index' => $sd_i ) );
                }
                ?>
            </div>
            <?php else : ?>
            <div class="grid md:grid-cols-2 gap-4">
                <?php foreach ( $sd_group['posts'] as $sd_post ) : ?>
                <?php $sd_text = 'sd_service_page' === $sd_type ? (string) sdc_get( $sd_post->ID, 'definition' ) : get_the_excerpt( $sd_post ); ?>
                <a href="<?php echo esc_url( get_permalink( $sd_post ) ); ?>" class="group glass rounded-3xl p-6 hover:bg-white/[0.06] transition-colors">
                    <span class="block text-lg font-bold text-white"><?php echo esc_html( get_the_title( $sd_post ) ); ?></span>
                    <?php if ( $sd_text ) : ?>
                    <span class="mt-2 block text-sm text-gray-400 leading-relaxed"><?php echo esc_html( wp_trim_words( $sd_text, 28 ) ); ?></span>
                    <?php endif; ?>
                    <span class="mt-4 inline-flex items-center gap-1 text-sm text-gray-400 group-hover:text-white"><?php esc_html_e( 'Open', 'serhandemirel' ); ?> <span class="transition-transform group-hover:translate-x-1" aria-hidden="true">→</span></span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
        <?php endforeach; ?>
        <div class="relative z-10 max-w-6xl mx-auto px-6 mt-12 text-gray-400"><?php the_posts_pagination(); ?></div>
    <?php else : ?>
        <section class="relative z-10 max-w-6xl mx-auto px-6">
            <p class="text-gray-400"><?php esc_html_e( 'Nothing matched. Try fewer or different words.', 'serhandemirel' ); ?></p>
        </section>
    <?php endif; ?>
</main>
<?php
get_template_part( 'template-parts/contact' );
get_footer();
