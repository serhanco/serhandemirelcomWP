<?php
/**
 * Service page: the answer first (definition), then who it is for, what
 * you get, how it works, related projects and FAQ.
 *
 * @package serhandemirel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$service = sdc_service_data( get_post() );
	$updated = ! empty( $service['reviewed'] ) ? $service['reviewed'] : get_the_modified_date( 'Y-m-d' );
	$price   = ! empty( $service['price_from'] ) ? sprintf(
		/* translators: %s: price with currency, e.g. "2,000 EUR" */
		__( 'From %s', 'serhandemirel' ),
		number_format_i18n( $service['price_from'] ) . ' ' . $service['currency']
	) : '';
	?>
<main id="main" class="relative overflow-hidden">
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
                    <?php if ( $price ) : ?>
                    <div class="flex justify-between gap-4 border-b border-white/5 py-3"><dt class="text-gray-500"><?php esc_html_e( 'Price', 'serhandemirel' ); ?></dt><dd class="font-semibold text-white text-right"><?php echo esc_html( $price ); ?></dd></div>
                    <?php endif; ?>
                </dl>
                <button type="button" onclick="openProjectModal()" class="mt-5 w-full flex justify-between items-center px-5 py-3 rounded-full bg-white text-black font-semibold hover:bg-gray-200 transition-colors"><?php esc_html_e( 'Start a Project', 'serhandemirel' ); ?> <span aria-hidden="true">→</span></button>
            </div>
        </header>

        <div class="grid md:grid-cols-2 gap-6 mb-16">
            <?php if ( ! empty( $service['who_for'] ) ) : ?>
            <section class="glass rounded-3xl p-8">
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-blue-500 mb-4"><?php esc_html_e( 'Who it is for', 'serhandemirel' ); ?></h2>
                <p class="text-gray-300 leading-relaxed"><?php echo esc_html( $service['who_for'] ); ?></p>
            </section>
            <?php endif; ?>
            <?php if ( ! empty( $service['deliverables'] ) ) : ?>
            <section class="glass rounded-3xl p-8">
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-emerald-500 mb-4"><?php esc_html_e( 'What you get', 'serhandemirel' ); ?></h2>
                <ul class="space-y-3">
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

        <?php if ( ! empty( $service['projects'] ) ) : ?>
        <section class="mb-16">
            <div class="flex items-end justify-between gap-4 mb-8"><h2 class="text-3xl md:text-4xl font-bold text-white"><?php esc_html_e( 'Related work', 'serhandemirel' ); ?></h2><a href="<?php echo esc_url( get_post_type_archive_link( 'sd_project' ) ); ?>" class="text-sm text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'All work', 'serhandemirel' ); ?> →</a></div>
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
            <div class="space-y-3">
                <?php foreach ( $service['faq'] as $item ) : ?>
                <details class="group glass rounded-2xl px-6 py-5">
                    <summary class="flex items-center justify-between gap-4 cursor-pointer list-none text-white font-semibold">
                        <?php echo esc_html( $item['question'] ); ?>
                        <svg class="w-5 h-5 shrink-0 text-gray-400 transition-transform group-open:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"></path></svg>
                    </summary>
                    <p class="mt-3 text-gray-400 leading-relaxed"><?php echo esc_html( $item['answer'] ); ?></p>
                </details>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <p class="text-sm text-gray-500">
            <?php
            printf(
                /* translators: %s: date */
                esc_html__( 'Last updated: %s', 'serhandemirel' ),
                '<time datetime="' . esc_attr( $updated ) . '">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $updated ) ) ) . '</time>'
            );
            ?>
        </p>
    </article>
</main>
	<?php
endwhile;

get_template_part( 'template-parts/contact' );
get_footer();
