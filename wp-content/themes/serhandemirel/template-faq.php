<?php
/**
 * Template Name: FAQ
 *
 * Every question on the site in one place: the general questions from
 * Theme settings → Sections, then each service page, industry, insight and
 * glossary term. Each answer links to the page it lives on, so it is
 * written once. A search box filters while typing.
 *
 * @package serhandemirel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$sd_groups = sdc_faq_hub();
	$sd_total  = array_sum(
		array_map(
			function ( $group ) {
				return count( $group['items'] );
			},
			$sd_groups
		)
	);
	$sd_dates  = sdc_dates( get_the_ID() );
	?>
<main id="main" class="relative overflow-hidden">
    <div class="sd-orb w-[50rem] h-[50rem] -top-96 -right-60 bg-purple-900/30"></div>

    <header class="relative z-10 max-w-3xl mx-auto px-6 pt-36 md:pt-44 pb-10 text-center">
        <p class="text-sm font-bold tracking-[0.2em] uppercase text-pink-500 mb-4"><?php esc_html_e( 'FAQ', 'serhandemirel' ); ?></p>
        <h1 class="text-5xl md:text-7xl font-black tracking-tighter leading-[0.95] text-white [text-wrap:balance]"><?php the_title(); ?></h1>
        <p class="mt-6 text-lg text-gray-400">
            <?php
            if ( has_excerpt() ) {
                echo esc_html( get_the_excerpt() );
            } else {
                esc_html_e( 'Every answer here also lives on the page it belongs to.', 'serhandemirel' );
            }
            ?>
            <button type="button" onclick="openProjectModal()" class="text-white underline decoration-purple-500 underline-offset-4"><?php esc_html_e( 'Ask me', 'serhandemirel' ); ?></button>.
        </p>
        <?php if ( $sd_total > 4 ) : ?>
        <div class="mt-8 relative">
            <label for="sd-faq-search" class="sr-only"><?php esc_html_e( 'Search questions', 'serhandemirel' ); ?></label>
            <input id="sd-faq-search" type="search" data-live-search="#sd-faq" placeholder="<?php echo esc_attr( sprintf( /* translators: %d: number of questions */ _n( 'Search %d question', 'Search %d questions', $sd_total, 'serhandemirel' ), $sd_total ) ); ?>" class="w-full rounded-full bg-white/5 border border-white/10 pl-12 pr-5 py-4 text-base text-white placeholder:text-gray-500 focus:outline-none focus:border-purple-500/60">
            <svg class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="2"></circle><path d="m20 20-3.5-3.5" stroke-width="2" stroke-linecap="round"></path></svg>
        </div>
        <?php endif; ?>
    </header>

    <div id="sd-faq" class="relative z-10 max-w-3xl mx-auto px-6">
        <?php if ( count( $sd_groups ) > 1 ) : ?>
        <nav class="flex gap-2 overflow-x-auto -mx-6 px-6 pb-1 [scrollbar-width:none] md:flex-wrap md:justify-center" aria-label="<?php esc_attr_e( 'Topics', 'serhandemirel' ); ?>">
            <?php foreach ( $sd_groups as $sd_group ) : ?>
            <a href="#<?php echo esc_attr( $sd_group['id'] ); ?>" class="shrink-0 px-4 py-2 rounded-full text-sm border border-white/10 text-gray-300 hover:bg-white/10 transition-colors"><?php echo esc_html( $sd_group['title'] ); ?></a>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>

        <?php if ( '' !== trim( get_the_content() ) ) : ?>
        <div class="sd-prose mt-10"><?php the_content(); ?></div>
        <?php endif; ?>

        <?php foreach ( $sd_groups as $sd_n => $sd_group ) : ?>
        <section id="<?php echo esc_attr( $sd_group['id'] ); ?>" data-live-group class="mt-12 scroll-mt-28">
            <h2 class="text-2xl font-bold tracking-tight text-white mb-5"><?php echo esc_html( $sd_group['title'] ); ?></h2>
            <div class="space-y-3">
                <?php foreach ( $sd_group['items'] as $sd_i => $sd_qa ) : ?>
                <details class="group glass rounded-2xl px-5 py-4" data-live-item<?php echo ( 0 === $sd_n && 0 === $sd_i ) ? ' open' : ''; ?>>
                    <summary class="flex justify-between items-center gap-4 font-semibold text-white cursor-pointer"><?php echo esc_html( $sd_qa['question'] ); ?><svg class="w-5 h-5 shrink-0 text-gray-400 transition-transform group-open:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"></path></svg></summary>
                    <p class="mt-3 text-gray-400 leading-relaxed"><?php echo esc_html( $sd_qa['answer'] ); ?></p>
                    <?php if ( ! empty( $sd_qa['from_url'] ) && 'service-' !== substr( $sd_group['id'], 0, 8 ) ) : ?>
                    <a href="<?php echo esc_url( $sd_qa['from_url'] ); ?>" class="mt-3 inline-flex text-xs text-gray-500 hover:text-white"><?php echo esc_html( sprintf( /* translators: %s: page title */ __( 'From: %s', 'serhandemirel' ), $sd_qa['from'] ) ); ?> →</a>
                    <?php elseif ( ! empty( $sd_qa['from_url'] ) ) : ?>
                    <a href="<?php echo esc_url( $sd_qa['from_url'] ); ?>" class="mt-3 inline-flex text-xs text-gray-500 hover:text-white"><?php esc_html_e( 'See the service', 'serhandemirel' ); ?> →</a>
                    <?php endif; ?>
                </details>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>
        <p data-live-empty hidden class="mt-12 text-center text-gray-400"><?php esc_html_e( 'No question matches. Try another word, or ask me directly.', 'serhandemirel' ); ?></p>

        <?php if ( $sd_dates['updated'] ) : ?>
        <p class="mt-10 text-xs text-gray-500 text-center">
            <?php
            /* translators: %s: month and year */
            printf( esc_html__( 'Answers reviewed %s', 'serhandemirel' ), '<time datetime="' . esc_attr( $sd_dates['updated'] ) . '">' . esc_html( date_i18n( 'F Y', strtotime( $sd_dates['updated'] ) ) ) . '</time>' );
            ?>
        </p>
        <?php endif; ?>
    </div>
</main>
	<?php
endwhile;

get_template_part( 'template-parts/contact' );
get_footer();
