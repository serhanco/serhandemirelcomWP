<?php
/**
 * Service page: the answer first (definition) with its dates, then who it
 * is for and who it is not for, what you get, how it works, what clients
 * say, related projects with their industries and FAQ.
 *
 * @package serhandemirel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$service = sdc_service_data( get_post() );
	$booking = $service['booking_url'] ? $service['booking_url'] : (string) ( sdc_get_profile()['booking_url'] ?? '' );
	$quotes  = sdc_get_testimonials( array( 'service' => get_the_ID(), 'limit' => 4 ) );
	$sectors = array();
	foreach ( $service['projects'] as $project ) {
		foreach ( (array) get_the_terms( $project['id'], 'sd_industry' ) as $term ) {
			if ( $term instanceof WP_Term ) {
				$sectors[ $term->term_id ] = $term;
			}
		}
	}
	$price   = ! empty( $service['price_from'] ) ? sprintf(
		/* translators: %s: price with currency, e.g. "2,000 EUR" */
		__( 'From %s', 'serhandemirel' ),
		number_format_i18n( $service['price_from'] ) . ' ' . $service['currency']
	) : '';
	?>
<main id="main" class="relative overflow-hidden" data-sd-service="<?php echo esc_attr( get_the_title() ); ?>">
    <div class="sd-orb w-[50rem] h-[50rem] -top-80 -right-40 bg-blue-900/30"></div>

    <article <?php post_class( 'relative z-10 max-w-6xl mx-auto px-6 pt-36 md:pt-44' ); ?>>
        <?php sd_breadcrumb( array( array( __( 'Services', 'serhandemirel' ), get_post_type_archive_link( 'sd_service_page' ) ), array( get_the_title(), '' ) ) ); ?>

        <header class="grid lg:grid-cols-[1.4fr_1fr] gap-10 items-end mb-20">
            <div>
                <p class="text-sm font-bold tracking-[0.2em] uppercase text-purple-400 mb-4"><?php esc_html_e( 'Service', 'serhandemirel' ); ?></p>
                <h1 class="text-5xl md:text-7xl font-black tracking-tighter leading-[0.95] text-white [text-wrap:balance]"><?php the_title(); ?></h1>
                <?php if ( ! empty( $service['definition'] ) ) : ?>
                <p class="mt-6 text-xl text-gray-300 max-w-2xl leading-relaxed"><?php echo esc_html( $service['definition'] ); ?></p>
                <?php endif; ?>
                <div class="mt-6"><?php get_template_part( 'template-parts/dates', null, array( 'dates' => sdc_dates( get_the_ID() ) ) ); ?></div>
            </div>
            <div class="rounded-3xl glass p-6 text-sm">
                <dl class="space-y-0">
                    <?php if ( ! empty( $service['duration'] ) ) : ?>
                    <div class="flex justify-between gap-4 border-b border-white/5 py-3"><dt class="text-gray-500"><?php esc_html_e( 'Typical duration', 'serhandemirel' ); ?></dt><dd class="font-semibold text-white text-right"><?php echo esc_html( $service['duration'] ); ?></dd></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $service['engagement_label'] ) ) : ?>
                    <div class="flex justify-between gap-4 border-b border-white/5 py-3"><dt class="text-gray-500"><?php esc_html_e( 'Engagement', 'serhandemirel' ); ?></dt><dd class="font-semibold text-white text-right"><?php echo esc_html( $service['engagement_label'] ); ?></dd></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $service['area_served'] ) ) : ?>
                    <div class="flex justify-between gap-4 border-b border-white/5 py-3"><dt class="text-gray-500"><?php esc_html_e( 'Where', 'serhandemirel' ); ?></dt><dd class="font-semibold text-white text-right"><?php echo esc_html( $service['area_served'] ); ?></dd></div>
                    <?php endif; ?>
                    <?php if ( $service['languages'] ) : ?>
                    <div class="flex justify-between gap-4 border-b border-white/5 py-3"><dt class="text-gray-500"><?php esc_html_e( 'Languages', 'serhandemirel' ); ?></dt><dd class="font-semibold text-white text-right"><?php echo esc_html( $service['languages'] ); ?></dd></div>
                    <?php endif; ?>
                    <?php if ( $price ) : ?>
                    <div class="flex justify-between gap-4 border-b border-white/5 py-3"><dt class="text-gray-500"><?php esc_html_e( 'Price', 'serhandemirel' ); ?></dt><dd class="font-semibold text-white text-right"><?php echo esc_html( $price ); ?></dd></div>
                    <?php endif; ?>
                </dl>
                <div class="mt-5 grid gap-2">
                    <button type="button" onclick="openProjectModal()" class="w-full flex justify-between items-center px-5 py-3 rounded-full bg-white text-black font-semibold hover:bg-gray-200 transition-colors"><?php esc_html_e( 'Start a Project', 'serhandemirel' ); ?> <span aria-hidden="true">→</span></button>
                    <?php if ( $booking ) : ?>
                    <a href="<?php echo esc_url( $booking ); ?>" target="_blank" rel="noopener" class="w-full flex justify-between items-center px-5 py-3 rounded-full border border-white/15 text-white font-semibold hover:bg-white/5 transition-colors"><?php esc_html_e( 'Book a 15-min call', 'serhandemirel' ); ?> <span class="text-gray-400" aria-hidden="true">↗</span></a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="grid md:grid-cols-2 gap-6 mb-16">
            <?php if ( ! empty( $service['who_for'] ) ) : ?>
            <section class="glass rounded-3xl p-8">
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-blue-500 mb-4"><?php echo esc_html( $service['not_for'] ? __( 'A good fit if', 'serhandemirel' ) : __( 'Who it is for', 'serhandemirel' ) ); ?></h2>
                <?php $fit = sdc_lines( $service['who_for'] ); ?>
                <?php if ( count( $fit ) > 1 ) : ?>
                <ul class="space-y-3">
                    <?php foreach ( $fit as $item ) : ?>
                    <li class="flex gap-3 text-gray-300"><svg class="w-5 h-5 mt-0.5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span><?php echo esc_html( $item ); ?></span></li>
                    <?php endforeach; ?>
                </ul>
                <?php else : ?>
                <p class="text-gray-300 leading-relaxed"><?php echo esc_html( $service['who_for'] ); ?></p>
                <?php endif; ?>
            </section>
            <?php endif; ?>
            <?php if ( $service['not_for'] ) : ?>
            <section class="rounded-3xl border border-white/10 p-8">
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-gray-500 mb-4"><?php esc_html_e( 'Not a fit if', 'serhandemirel' ); ?></h2>
                <ul class="space-y-3">
                    <?php foreach ( $service['not_for'] as $item ) : ?>
                    <li class="flex gap-3 text-gray-400"><svg class="w-5 h-5 mt-0.5 shrink-0 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg><span><?php echo esc_html( $item ); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>
            <?php if ( ! empty( $service['deliverables'] ) ) : ?>
            <section class="glass rounded-3xl p-8 <?php echo ( $service['not_for'] || empty( $service['who_for'] ) ) ? 'md:col-span-2' : ''; ?>">
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-emerald-500 mb-4"><?php esc_html_e( 'What you get', 'serhandemirel' ); ?></h2>
                <ul class="<?php echo $service['not_for'] ? 'grid md:grid-cols-2 gap-x-8 gap-y-3' : 'space-y-3'; ?>">
                    <?php foreach ( $service['deliverables'] as $item ) : ?>
                    <li class="flex gap-3 text-gray-300"><svg class="w-5 h-5 mt-0.5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span><?php echo esc_html( $item ); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>
        </div>

        <?php if ( ! empty( $service['process'] ) ) : ?>
        <section class="mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-8"><?php esc_html_e( 'How it works', 'serhandemirel' ); ?></h2>
            <?php $cols = array( 1 => 'lg:grid-cols-2', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4' ); ?>
            <ol class="grid sm:grid-cols-2 <?php echo esc_attr( $cols[ count( $service['process'] ) ] ?? 'lg:grid-cols-5' ); ?> gap-4">
                <?php foreach ( $service['process'] as $i => $step ) : ?>
                <li class="glass rounded-3xl p-6">
                    <span class="w-9 h-9 rounded-full bg-white text-black text-sm font-bold flex items-center justify-center mb-5"><?php echo esc_html( (string) ( $i + 1 ) ); ?></span>
                    <h3 class="text-lg font-bold text-white mb-2"><?php echo esc_html( $step['title'] ); ?></h3>
                    <?php if ( $step['text'] ) : ?>
                    <p class="text-gray-400 text-sm leading-relaxed"><?php echo esc_html( $step['text'] ); ?></p>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ol>
        </section>
        <?php endif; ?>

        <?php if ( '' !== trim( get_the_content() ) ) : ?>
        <div class="sd-prose max-w-3xl mb-16">
            <?php the_content(); ?>
        </div>
        <?php endif; ?>

        <?php if ( $quotes ) : ?>
        <section class="mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-8"><?php esc_html_e( 'What clients say', 'serhandemirel' ); ?></h2>
            <div class="grid <?php echo count( $quotes ) > 1 ? 'md:grid-cols-2' : 'max-w-4xl'; ?> gap-5">
                <?php
                foreach ( $quotes as $i => $quote ) {
                    get_template_part( 'template-parts/testimonial', null, array( 'item' => $quote, 'index' => $i, 'show_project' => true ) );
                }
                ?>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( ! empty( $service['projects'] ) ) : ?>
        <section class="mb-16">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <h2 class="text-3xl md:text-4xl font-bold text-white"><?php esc_html_e( 'Related work', 'serhandemirel' ); ?></h2>
                <div class="flex flex-wrap gap-2 items-center text-sm">
                    <?php if ( $sectors ) : ?>
                    <span class="text-gray-500 mr-1"><?php esc_html_e( 'Industries:', 'serhandemirel' ); ?></span>
                        <?php foreach ( $sectors as $term ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="px-3 py-1.5 rounded-full border border-white/10 text-gray-300 hover:bg-white/10 transition-colors"><?php echo esc_html( $term->name ); ?></a>
                        <?php endforeach; ?>
                    <?php else : ?>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'sd_project' ) ); ?>" class="text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'All work', 'serhandemirel' ); ?> →</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php
                foreach ( $service['projects'] as $i => $project ) {
                    get_template_part( 'template-parts/project-card', null, array( 'project' => $project, 'index' => $i ) );
                }
                ?>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( ! empty( $service['faq'] ) ) : ?>
        <section class="mb-16 max-w-3xl">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-8"><?php esc_html_e( 'Frequently asked questions', 'serhandemirel' ); ?></h2>
            <?php get_template_part( 'template-parts/faq-list', null, array( 'items' => $service['faq'], 'open_first' => true ) ); ?>
            <?php if ( sd_faq_url() ) : ?>
            <a href="<?php echo esc_url( sd_faq_url() ); ?>" class="mt-6 inline-flex text-sm text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'All questions', 'serhandemirel' ); ?> →</a>
            <?php endif; ?>
        </section>
        <?php endif; ?>

    </article>
</main>
	<?php
endwhile;

get_template_part( 'template-parts/contact' );
get_footer();
