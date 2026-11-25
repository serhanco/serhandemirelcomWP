<?php
/**
 * Industry page: a short answer for the industry, the problems solved
 * there and the service for each, the projects, brands, a client quote,
 * the industry's questions and its dates.
 *
 * @package serhandemirel
 */

get_header();

$sd_term     = get_queried_object();
$sd_industry = sdc_industry_data( $sd_term );
$sd_projects = array();
while ( have_posts() ) {
	the_post();
	$sd_projects[] = sdc_project_data( get_post() );
}
$sd_quotes = sdc_get_testimonials( array( 'industry' => $sd_term->term_id, 'limit' => 1 ) );
$sd_title  = esc_html( $sd_industry['title'] );
$sd_name   = esc_html( $sd_term->name );
// Colour the industry name inside the title, as on the other pages.
$sd_pos = $sd_name ? stripos( $sd_title, $sd_name ) : false;
if ( false !== $sd_pos ) {
	$sd_title = substr( $sd_title, 0, $sd_pos ) . '<span class="gradient-text">' . substr( $sd_title, $sd_pos, strlen( $sd_name ) ) . '</span>' . substr( $sd_title, $sd_pos + strlen( $sd_name ) );
}
$sd_chips = array();
if ( $sd_projects ) {
	/* translators: %d: number of projects */
	$sd_chips[] = sprintf( _n( '%d project', '%d projects', count( $sd_projects ), 'serhandemirel' ), count( $sd_projects ) );
}
if ( $sd_industry['brands'] ) {
	/* translators: %d: number of brands */
	$sd_chips[] = sprintf( _n( '%d brand', '%d brands', count( $sd_industry['brands'] ), 'serhandemirel' ), count( $sd_industry['brands'] ) );
}
?>
<main id="main" class="relative overflow-hidden">
    <div class="sd-orb w-[50rem] h-[50rem] -top-96 -right-40 bg-teal-900/30"></div>
    <div class="sd-orb w-[40rem] h-[40rem] top-60 -left-80 bg-indigo-900/25"></div>

    <header class="relative z-10 max-w-6xl mx-auto px-6 pt-36 md:pt-44">
        <?php sd_breadcrumb( array( array( sd_opt( 'work_eyebrow' ), get_post_type_archive_link( 'sd_project' ) ), array( $sd_term->name, '' ) ) ); ?>
        <p class="text-sm font-bold tracking-[0.2em] uppercase text-teal-400 mb-4"><?php esc_html_e( 'Industry', 'serhandemirel' ); ?></p>
        <h1 class="text-5xl md:text-8xl font-black tracking-tighter leading-[0.92] max-w-4xl text-white [text-wrap:balance]"><?php echo $sd_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></h1>
        <?php if ( $sd_industry['intro'] ) : ?>
        <p class="mt-6 text-xl md:text-2xl text-gray-300 max-w-3xl leading-snug"><?php echo esc_html( wp_strip_all_tags( $sd_industry['intro'] ) ); ?></p>
        <?php endif; ?>
        <?php if ( $sd_chips || $sd_industry['published'] || $sd_industry['updated'] ) : ?>
        <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3">
            <?php if ( $sd_chips ) : ?>
            <div class="flex flex-wrap gap-2">
                <?php foreach ( $sd_chips as $sd_chip ) : ?>
                <span class="px-3 py-1.5 rounded-full border border-white/10 text-sm text-gray-300"><?php echo esc_html( $sd_chip ); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div><?php get_template_part( 'template-parts/dates', null, array( 'dates' => array( 'published' => $sd_industry['published'], 'updated' => $sd_industry['updated'], 'note' => $sd_industry['note'] ) ) ); ?></div>
        </div>
        <?php endif; ?>
    </header>

    <?php if ( $sd_industry['problems'] ) : ?>
    <section class="relative z-10 max-w-6xl mx-auto px-6 mt-16 grid md:grid-cols-3 gap-4" aria-label="<?php esc_attr_e( 'Problems I solve', 'serhandemirel' ); ?>">
        <?php foreach ( $sd_industry['problems'] as $sd_i => $sd_p ) : ?>
        <div class="rounded-3xl glass p-6 md:p-7 flex flex-col">
            <span class="text-sm text-gray-500 font-mono"><?php echo esc_html( sprintf( '%02d', $sd_i + 1 ) ); ?></span>
            <h2 class="mt-3 text-xl font-bold tracking-tight text-white"><?php echo esc_html( $sd_p['problem'] ); ?></h2>
            <?php if ( $sd_p['answer'] ) : ?>
            <p class="mt-3 text-gray-400 leading-relaxed flex-1"><?php echo esc_html( $sd_p['answer'] ); ?></p>
            <?php endif; ?>
            <?php if ( $sd_p['service'] ) : ?>
            <a href="<?php echo esc_url( $sd_p['service']['url'] ); ?>" class="mt-6 text-sm text-gray-300 hover:text-white transition-colors"><?php echo esc_html( $sd_p['service']['title'] ); ?> →</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if ( $sd_projects ) : ?>
    <section class="relative z-10 max-w-6xl mx-auto px-6 mt-20">
        <div class="flex items-end justify-between gap-4 mb-6">
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white"><?php echo esc_html( sprintf( /* translators: %s: industry name */ __( '%s work', 'serhandemirel' ), $sd_term->name ) ); ?></h2>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'sd_project' ) ); ?>" class="text-sm text-gray-400 hover:text-white transition-colors shrink-0"><?php esc_html_e( 'All work', 'serhandemirel' ); ?> →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            foreach ( $sd_projects as $sd_i => $sd_project ) {
                get_template_part( 'template-parts/project-card', null, array( 'project' => $sd_project, 'index' => $sd_i ) );
            }
            ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $sd_industry['brands'] ) : ?>
    <section class="relative z-10 max-w-6xl mx-auto px-6 mt-10 flex flex-wrap items-center gap-2">
        <span class="text-sm text-gray-500 mr-2"><?php esc_html_e( 'Also worked with', 'serhandemirel' ); ?></span>
        <?php foreach ( $sd_industry['brands'] as $sd_brand ) : ?>
        <span class="h-12 px-4 rounded-xl bg-white/[0.04] border border-white/10 flex items-center"><img src="<?php echo esc_url( $sd_brand['logo'] ); ?>" alt="<?php echo esc_attr( $sd_brand['name'] ); ?>" loading="lazy" class="h-6 w-auto max-w-[120px] object-contain opacity-80"></span>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if ( $sd_quotes ) : ?>
    <section class="relative z-10 max-w-4xl mx-auto px-6 mt-20">
        <?php get_template_part( 'template-parts/testimonial', null, array( 'item' => $sd_quotes[0], 'index' => 2, 'large' => true, 'show_project' => true ) ); ?>
    </section>
    <?php endif; ?>

    <?php if ( $sd_industry['faq'] ) : ?>
    <section id="faq" class="relative z-10 max-w-3xl mx-auto px-6 mt-20 scroll-mt-28">
        <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white mb-8"><?php echo esc_html( sprintf( /* translators: %s: industry name */ __( '%s questions', 'serhandemirel' ), $sd_term->name ) ); ?></h2>
        <?php get_template_part( 'template-parts/faq-list', null, array( 'items' => $sd_industry['faq'], 'open_first' => true ) ); ?>
    </section>
    <?php endif; ?>
</main>
<?php
get_template_part( 'template-parts/contact' );
get_footer();
