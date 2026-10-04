<?php
/**
 * All projects (/work/) and industry or service archives.
 *
 * @package serhandemirel
 */

get_header();

$projects = array();
while ( have_posts() ) {
	the_post();
	if ( function_exists( 'sdc_project_data' ) ) {
		$projects[] = sdc_project_data( get_post() );
	}
}
$filters = is_post_type_archive( 'sd_project' ) ? sd_work_filters( $projects ) : array();
?>
<main class="relative overflow-hidden">
    <div class="absolute bottom-[-10%] left-[-10%] w-[60vw] h-[60vw] rounded-full bg-purple-900/10 blur-[150px] pointer-events-none z-0"></div>

    <section class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 pt-40 pb-24">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-blue-500 mb-4"><?php echo esc_html( sd_opt( 'work_eyebrow' ) ); ?></h2>
                <h1 class="text-4xl md:text-6xl font-bold text-white"><?php echo esc_html( is_tax() ? single_term_title( '', false ) : sd_opt( 'work_title' ) ); ?></h1>
            </div>

            <?php if ( count( $filters ) > 1 ) : ?>
            <div class="flex flex-wrap gap-2" id="portfolio-filters">
                <button type="button" class="filter-btn active px-4 py-2 rounded-full border border-white/20 bg-white text-black text-sm font-medium transition-colors" data-filter="all"><?php echo esc_html( sd_opt( 'work_all_label' ) ); ?></button>
                <?php foreach ( $filters as $slug => $name ) : ?>
                <button type="button" class="filter-btn px-4 py-2 rounded-full border border-white/20 text-white hover:bg-white/10 text-sm font-medium transition-colors" data-filter="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="portfolio-grid">
            <?php
            foreach ( $projects as $i => $project ) {
                get_template_part( 'template-parts/project-card', null, array( 'project' => $project, 'index' => $i ) );
            }
            ?>
        </div>

        <div class="mt-12 text-gray-400"><?php the_posts_pagination(); ?></div>
    </section>
</main>
<?php
get_template_part( 'template-parts/contact' );
get_footer();
