<?php
/**
 * Selected Work: project cards with industry filters.
 *
 * @package serhandemirel
 */

$projects = sd_work_projects();
$filters  = sd_work_filters( $projects );
?>
<!-- 5. Portfolio Section -->
<section id="portfolio" class="relative py-24 md:py-32 bg-[#050505] border-t border-white/5 z-10 overflow-hidden">
    <div class="absolute bottom-[-10%] left-[-10%] w-[60vw] h-[60vw] rounded-full bg-purple-900/10 blur-[150px] pointer-events-none z-0"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-blue-500 mb-4"><?php echo esc_html( sd_opt( 'work_eyebrow' ) ); ?></h2>
                <h3 class="text-3xl md:text-5xl font-bold text-white"><?php echo esc_html( sd_opt( 'work_title' ) ); ?></h3>
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
    </div>
</section>
