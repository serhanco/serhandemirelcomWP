<?php
/**
 * Glossary term: a quotable definition first, other names, dates, the
 * explanation, "From the field", questions and sources; related terms,
 * the service and articles beside it.
 *
 * @package serhandemirel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$sd_id      = get_the_ID();
	$sd_t       = sdc_term_data( get_post() );
	$sd_sources = sdc_sources( $sd_id );
	$sd_reads   = sdc_posts_for_term( $sd_id, 3 );
	$sd_glossary = get_post_type_archive_link( 'sd_term' );
	?>
<main id="main" class="relative overflow-hidden">
    <div class="sd-orb w-[45rem] h-[45rem] -top-80 -right-40 bg-purple-900/30"></div>

    <div class="relative z-10 max-w-6xl mx-auto px-6 pt-36 md:pt-44 grid lg:grid-cols-[minmax(0,1fr)_280px] gap-12">
        <article <?php post_class( 'min-w-0 max-w-[68ch]' ); ?>>
            <?php sd_breadcrumb( array( array( __( 'Glossary', 'serhandemirel' ), $sd_glossary ), array( get_the_title(), '' ) ) ); ?>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter leading-[1.02] text-white [text-wrap:balance]"><?php the_title(); ?></h1>
            <?php if ( $sd_t['aliases'] ) : ?>
            <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
                <span class="text-gray-500 mr-1"><?php esc_html_e( 'Also called', 'serhandemirel' ); ?></span>
                <?php foreach ( $sd_t['aliases'] as $sd_alias ) : ?>
                <span class="px-3 py-1.5 rounded-full border border-white/10 text-gray-300"><?php echo esc_html( $sd_alias ); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ( $sd_t['short'] ) : ?>
            <div class="mt-8 rounded-3xl p-px bg-gradient-to-r from-blue-500/60 via-purple-500/60 to-pink-500/60">
                <div class="rounded-[calc(1.5rem-1px)] bg-[#0b0a10] p-6 md:p-8">
                    <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-pink-500 mb-3"><?php esc_html_e( 'Definition', 'serhandemirel' ); ?></p>
                    <p class="text-xl md:text-2xl leading-snug font-medium text-white"><?php echo esc_html( $sd_t['short'] ); ?></p>
                </div>
            </div>
            <?php endif; ?>
            <div class="mt-5"><?php get_template_part( 'template-parts/dates', null, array( 'dates' => sdc_dates( $sd_id ) ) ); ?></div>

            <?php if ( '' !== trim( get_the_content() ) ) : ?>
            <div class="sd-prose mt-10"><?php echo sdc_link_citations( apply_filters( 'the_content', get_the_content() ), count( $sd_sources ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- post content. ?></div>
            <?php endif; ?>

            <?php if ( $sd_t['example'] ) : ?>
            <div class="mt-8 rounded-3xl border border-white/10 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-transparent p-6">
                <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-blue-400 mb-2"><?php esc_html_e( 'From the field', 'serhandemirel' ); ?></p>
                <p class="text-gray-200 leading-relaxed"><?php echo esc_html( $sd_t['example'] ); ?></p>
                <?php if ( $sd_t['project'] ) : ?>
                <a href="<?php echo esc_url( $sd_t['project']['url'] ); ?>" class="mt-3 inline-flex text-sm text-gray-400 hover:text-white transition-colors"><?php echo esc_html( sprintf( /* translators: %s: project name */ __( '%s case study', 'serhandemirel' ), $sd_t['project']['title'] ) ); ?> →</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ( $sd_t['faq'] ) : ?>
            <section id="faq" class="scroll-mt-28">
                <h2 class="mt-14 text-3xl font-bold tracking-tight text-white mb-6"><?php esc_html_e( 'Questions', 'serhandemirel' ); ?></h2>
                <?php get_template_part( 'template-parts/faq-list', null, array( 'items' => $sd_t['faq'] ) ); ?>
            </section>
            <?php endif; ?>

            <?php if ( $sd_sources ) : ?>
            <div class="mt-12"><?php get_template_part( 'template-parts/sources', null, array( 'sources' => $sd_sources, 'heading' => __( 'Sources', 'serhandemirel' ) ) ); ?></div>
            <?php endif; ?>
        </article>

        <aside class="lg:pt-24">
            <div class="lg:sticky lg:top-32 space-y-4">
                <?php if ( $sd_t['related'] ) : ?>
                <div class="rounded-3xl glass p-5">
                    <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-gray-500 mb-3"><?php esc_html_e( 'Related terms', 'serhandemirel' ); ?></p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ( $sd_t['related'] as $sd_rel ) : ?>
                        <a href="<?php echo esc_url( $sd_rel['url'] ); ?>" class="px-3 py-1.5 rounded-full border border-white/10 text-sm text-gray-300 hover:bg-white/10 transition-colors"><?php echo esc_html( $sd_rel['title'] ); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ( $sd_t['service'] ) : ?>
                <div class="rounded-3xl glass p-5">
                    <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-blue-400 mb-2"><?php esc_html_e( 'Related service', 'serhandemirel' ); ?></p>
                    <p class="font-semibold text-white"><?php echo esc_html( $sd_t['service']['title'] ); ?></p>
                    <?php if ( $sd_t['service']['excerpt'] ) : ?>
                    <p class="mt-1 text-sm text-gray-400"><?php echo esc_html( $sd_t['service']['excerpt'] ); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( $sd_t['service']['url'] ); ?>" class="mt-3 inline-flex text-sm text-gray-300 hover:text-white transition-colors"><?php esc_html_e( 'See the service', 'serhandemirel' ); ?> →</a>
                </div>
                <?php endif; ?>
                <?php if ( $sd_reads ) : ?>
                <div class="rounded-3xl glass p-5">
                    <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-pink-500 mb-2"><?php esc_html_e( 'Read more', 'serhandemirel' ); ?></p>
                    <?php foreach ( $sd_reads as $sd_read ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $sd_read ) ); ?>" class="block py-1 font-semibold leading-snug text-gray-200 hover:text-white"><?php echo esc_html( get_the_title( $sd_read ) ); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <a href="<?php echo esc_url( $sd_glossary ); ?>" class="block text-sm text-gray-400 hover:text-white px-1 transition-colors">← <?php esc_html_e( 'All terms', 'serhandemirel' ); ?></a>
            </div>
        </aside>
    </div>
</main>
	<?php
endwhile;

get_template_part( 'template-parts/contact' );
get_footer();
