<?php
/**
 * Talks, trainings and press (/speaking/): the newest item with a video
 * up top, a filter by kind, then everything by year. Each item links out
 * to its video, slides or article.
 *
 * @package serhandemirel
 */

get_header();

$sd_items    = sdc_get_appearances();
$sd_featured = null;
foreach ( $sd_items as $sd_item ) {
	if ( $sd_item['video_url'] ) {
		$sd_featured = $sd_item;
		break;
	}
}
$sd_years = array();
$sd_kinds = array();
foreach ( $sd_items as $sd_item ) {
	$sd_years[ $sd_item['year'] ][]   = $sd_item;
	$sd_kinds[ $sd_item['kind'] ] = $sd_item['kind_label'];
}
$sd_all_kinds = sdc_appearance_kinds();
$sd_kinds     = array_intersect_key( $sd_all_kinds, $sd_kinds );
$sd_about     = sd_about_url();
?>
<main id="main" class="relative overflow-hidden">
    <div class="sd-orb w-[50rem] h-[50rem] -top-96 -left-40 bg-blue-900/30"></div>

    <header class="relative z-10 max-w-6xl mx-auto px-6 pt-36 md:pt-44 pb-10">
        <?php
        $sd_crumbs = array();
        if ( $sd_about ) {
            $sd_crumbs[] = array( __( 'About', 'serhandemirel' ), $sd_about );
        }
        $sd_crumbs[] = array( __( 'Talks, trainings & press', 'serhandemirel' ), '' );
        sd_breadcrumb( $sd_crumbs );
        ?>
        <h1 class="text-5xl md:text-7xl font-black tracking-tighter leading-[0.95] max-w-3xl text-white [text-wrap:balance]"><?php echo wp_kses( __( 'Talks, trainings<br><span class="gradient-text">&amp; press.</span>', 'serhandemirel' ), array( 'br' => array(), 'span' => array( 'class' => array() ) ) ); ?></h1>
        <p class="mt-6 text-lg text-gray-400 max-w-xl">
            <?php echo esc_html( sd_opt( 'speaking_intro' ) ); ?>
            <button type="button" onclick="openProjectModal()" class="text-white underline decoration-purple-500 underline-offset-4"><?php esc_html_e( 'Get in touch', 'serhandemirel' ); ?></button>.
        </p>
    </header>

    <section class="relative z-10 max-w-6xl mx-auto px-6">
        <?php if ( $sd_featured ) : ?>
        <a href="<?php echo esc_url( $sd_featured['video_url'] ); ?>" target="_blank" rel="noopener" class="group grid md:grid-cols-[1.4fr_1fr] gap-2 rounded-[2rem] glass p-2 hover:bg-white/[0.06] transition-colors">
            <div class="relative aspect-video rounded-[1.6rem] overflow-hidden" style="<?php echo $sd_featured['image'] ? '' : esc_attr( sd_placeholder_style( 4 ) ); ?>">
                <?php if ( $sd_featured['image'] ) : ?>
                <img src="<?php echo esc_url( $sd_featured['image'] ); ?>" alt="" class="absolute inset-0 w-full h-full object-cover" loading="eager">
                <?php endif; ?>
                <span class="absolute inset-0 flex items-center justify-center" aria-hidden="true"><span class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-white text-black flex items-center justify-center transition-transform group-hover:scale-110"><svg class="w-7 h-7 ml-1" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span></span>
                <?php if ( $sd_featured['duration'] ) : ?>
                <span class="absolute bottom-4 left-4 text-xs px-3 py-1 rounded-full bg-black/40 backdrop-blur border border-white/10 text-white"><?php echo esc_html( sprintf( /* translators: %d: minutes */ __( '%d min', 'serhandemirel' ), $sd_featured['duration'] ) ); ?></span>
                <?php endif; ?>
            </div>
            <div class="p-5 md:p-8 flex flex-col justify-end">
                <span class="self-start text-[11px] font-semibold px-2.5 py-1 rounded-full border <?php echo esc_attr( sdc_appearance_kind_class( $sd_featured['kind'] ) ); ?>"><?php echo esc_html( $sd_featured['kind_label'] ); ?></span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight leading-tight text-white"><?php echo esc_html( $sd_featured['title'] ); ?></h2>
                <p class="mt-3 text-gray-400"><?php echo esc_html( implode( ' · ', array_filter( array( sdc_appearance_line( $sd_featured ), $sd_featured['month'], $sd_featured['language'] ) ) ) ); ?></p>
                <?php if ( $sd_featured['excerpt'] ) : ?>
                <p class="mt-3 text-sm text-gray-400 leading-relaxed"><?php echo esc_html( $sd_featured['excerpt'] ); ?></p>
                <?php endif; ?>
                <span class="mt-6 self-start text-sm px-4 py-2 rounded-full bg-white text-black font-semibold"><?php esc_html_e( 'Watch', 'serhandemirel' ); ?></span>
            </div>
        </a>
        <?php endif; ?>

        <?php if ( count( $sd_kinds ) > 1 ) : ?>
        <div class="mt-12 flex gap-2 overflow-x-auto -mx-6 px-6 md:mx-0 md:px-0 pb-1 [scrollbar-width:none]" role="group" aria-label="<?php esc_attr_e( 'Filter by type', 'serhandemirel' ); ?>">
            <button type="button" data-kind-filter="all" aria-pressed="true" class="shrink-0 px-4 py-2 rounded-full text-sm border bg-white text-black border-white font-semibold transition-colors"><?php esc_html_e( 'All', 'serhandemirel' ); ?></button>
            <?php foreach ( $sd_kinds as $sd_kind => $sd_label ) : ?>
            <button type="button" data-kind-filter="<?php echo esc_attr( $sd_kind ); ?>" aria-pressed="false" class="shrink-0 px-4 py-2 rounded-full text-sm border border-white/10 text-gray-300 hover:bg-white/10 transition-colors"><?php echo esc_html( $sd_label ); ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php foreach ( $sd_years as $sd_year => $sd_list ) : ?>
        <div class="mt-10" data-year-group>
            <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-gray-500 mb-2"><?php echo esc_html( $sd_year ); ?></h2>
            <ul>
                <?php
                foreach ( $sd_list as $sd_item ) {
                    get_template_part( 'template-parts/appearance-row', null, array( 'item' => $sd_item ) );
                }
                ?>
            </ul>
        </div>
        <?php endforeach; ?>

        <?php if ( ! $sd_items ) : ?>
        <p class="text-gray-400"><?php esc_html_e( 'Nothing here yet.', 'serhandemirel' ); ?></p>
        <?php endif; ?>
    </section>
</main>
<?php
get_template_part( 'template-parts/contact' );
get_footer();
