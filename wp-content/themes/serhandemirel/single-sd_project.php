<?php
/**
 * Project page as a case study: big title and one-line result, cover,
 * results, challenge / approach / outcome, the page content, a gallery,
 * the client's quote and a full-width link to the next project.
 *
 * @package serhandemirel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$sd_project    = sdc_project_data( get_post() );
	$sd_industries = get_the_terms( get_the_ID(), 'sd_industry' );
	$sd_industry   = is_array( $sd_industries ) ? $sd_industries[0] : null;
	$sd_metrics    = $sd_project['metrics'];
	if ( ! $sd_metrics && $sd_project['metric'] ) {
		$sd_metrics = array( array( 'value' => $sd_project['metric'], 'label' => '', 'note' => '' ) );
	}
	$sd_eyebrow = implode( ' · ', array_filter( array( $sd_project['location'], $sd_project['year'] ? (string) $sd_project['year'] : '' ) ) );
	$sd_acts    = array_filter(
		array(
			array( __( 'Challenge', 'serhandemirel' ), $sd_project['challenge'] ),
			array( __( 'Approach', 'serhandemirel' ), $sd_project['approach'] ),
			array( __( 'Outcome', 'serhandemirel' ), $sd_project['outcome'] ),
		),
		function ( $act ) {
			return '' !== trim( $act[1] );
		}
	);
	$sd_next    = sdc_next_project( get_the_ID() );
	$sd_bento   = array( 'md:col-span-4 aspect-[16/10]', 'md:col-span-2 aspect-square md:aspect-auto', 'md:col-span-2 aspect-square', 'md:col-span-4 aspect-[16/10] md:aspect-auto' );
	?>
<main id="main" class="relative overflow-hidden">
    <div class="sd-orb w-[50rem] h-[50rem] -top-96 -left-40 bg-indigo-900/30"></div>

    <article <?php post_class( 'relative z-10' ); ?>>
        <header class="max-w-6xl mx-auto px-6 pt-36 md:pt-44">
            <?php
            $sd_crumbs = array( array( sd_opt( 'work_eyebrow' ), get_post_type_archive_link( 'sd_project' ) ) );
            if ( $sd_industry ) {
                $sd_crumbs[] = array( $sd_industry->name, get_term_link( $sd_industry ) );
            }
            $sd_crumbs[] = array( get_the_title(), '' );
            sd_breadcrumb( $sd_crumbs );
            ?>
            <div class="grid md:grid-cols-[1fr_auto] gap-8 items-end">
                <div>
                    <?php if ( $sd_eyebrow ) : ?>
                    <p class="text-sm font-bold tracking-[0.2em] uppercase text-blue-400 mb-4"><?php echo esc_html( $sd_eyebrow ); ?></p>
                    <?php endif; ?>
                    <h1 class="text-5xl md:text-8xl font-black tracking-tighter leading-[0.92] text-white [text-wrap:balance]"><?php the_title(); ?></h1>
                    <?php if ( $sd_project['summary'] ) : ?>
                    <p class="mt-6 text-xl md:text-2xl text-gray-300 max-w-2xl leading-snug"><?php echo esc_html( $sd_project['summary'] ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex flex-wrap gap-2 md:justify-end md:max-w-xs">
                    <?php foreach ( $sd_project['tools'] as $sd_tool ) : ?>
                    <span class="px-3 py-1.5 rounded-full border border-white/10 text-sm text-gray-300"><?php echo esc_html( $sd_tool ); ?></span>
                    <?php endforeach; ?>
                    <?php foreach ( is_array( $sd_industries ) ? $sd_industries : array() as $sd_term ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $sd_term ) ); ?>" class="px-3 py-1.5 rounded-full border border-white/10 text-sm text-gray-300 hover:bg-white/10 hover:text-white transition-colors"><?php echo esc_html( $sd_term->name ); ?></a>
                    <?php endforeach; ?>
                    <?php if ( $sd_project['url'] ) : ?>
                    <a href="<?php echo esc_url( $sd_project['url'] ); ?>" target="_blank" rel="noopener" class="px-4 py-1.5 rounded-full bg-white text-black text-sm font-semibold hover:bg-gray-200 transition-colors"><?php esc_html_e( 'Visit site', 'serhandemirel' ); ?> ↗</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <?php if ( has_post_thumbnail() ) : ?>
        <div class="max-w-7xl mx-auto px-4 md:px-6 mt-12">
            <div class="relative aspect-[16/9] md:aspect-[21/9] rounded-[2rem] overflow-hidden border border-white/10">
                <?php echo sd_cover_image( get_the_ID(), 'absolute inset-0 w-full h-full object-cover', '(min-width: 1280px) 1232px, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image markup. ?>
                <?php if ( $sd_project['role'] || $sd_project['client'] ) : ?>
                <div class="absolute bottom-4 left-4 right-4 md:bottom-8 md:left-8 flex flex-wrap gap-2">
                    <?php if ( $sd_project['client'] ) : ?>
                    <span class="px-3 py-1.5 rounded-full bg-black/40 backdrop-blur border border-white/10 text-xs text-white"><?php echo esc_html( $sd_project['client'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( $sd_project['role'] ) : ?>
                    <span class="px-3 py-1.5 rounded-full bg-black/40 backdrop-blur border border-white/10 text-xs text-white"><?php echo esc_html( sprintf( /* translators: %s: what Serhan did on the project */ __( 'Role: %s', 'serhandemirel' ), $sd_project['role'] ) ); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $sd_metrics ) : ?>
        <section class="max-w-6xl mx-auto px-6 mt-12 grid sm:grid-cols-2 <?php echo count( $sd_metrics ) > 2 ? 'lg:grid-cols-3' : ''; ?> gap-4" aria-label="<?php esc_attr_e( 'Results', 'serhandemirel' ); ?>">
            <?php foreach ( $sd_metrics as $sd_i => $sd_m ) : ?>
            <div class="sd-rise rounded-3xl glass p-6 md:p-7" style="animation-delay:<?php echo esc_attr( $sd_i * 80 ); ?>ms">
                <p class="text-5xl md:text-6xl font-black tracking-tighter gradient-text pb-1"><?php echo esc_html( $sd_m['value'] ); ?></p>
                <?php if ( $sd_m['label'] ) : ?>
                <p class="mt-3 font-semibold text-white"><?php echo esc_html( $sd_m['label'] ); ?></p>
                <?php endif; ?>
                <?php if ( $sd_m['note'] ) : ?>
                <p class="text-sm text-gray-500"><?php echo esc_html( $sd_m['note'] ); ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <?php if ( $sd_acts ) : ?>
        <section class="max-w-6xl mx-auto px-6 mt-20">
            <?php foreach ( array_values( $sd_acts ) as $sd_i => $sd_act ) : ?>
            <div class="grid md:grid-cols-[200px_1fr] gap-3 md:gap-10 py-10 border-t border-white/10">
                <div class="md:sticky md:top-32 self-start">
                    <span class="text-sm text-gray-500 font-mono"><?php echo esc_html( sprintf( '%02d', $sd_i + 1 ) ); ?></span>
                    <h2 class="text-2xl font-bold tracking-tight text-white"><?php echo esc_html( $sd_act[0] ); ?></h2>
                </div>
                <div class="text-lg md:text-xl text-gray-300 leading-relaxed max-w-2xl space-y-4"><?php echo wp_kses_post( wpautop( $sd_act[1] ) ); ?></div>
            </div>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <?php if ( '' !== trim( get_the_content() ) ) : ?>
        <div class="max-w-6xl mx-auto px-6 mt-10">
            <div class="sd-prose <?php echo $sd_acts ? 'md:ml-[240px]' : ''; ?>">
                <?php the_content(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $sd_project['gallery'] ) : ?>
        <section class="max-w-6xl mx-auto px-6 mt-16 grid grid-cols-2 md:grid-cols-6 gap-4" aria-label="<?php esc_attr_e( 'Gallery', 'serhandemirel' ); ?>">
            <?php foreach ( $sd_project['gallery'] as $sd_i => $sd_image ) : ?>
            <figure class="col-span-2 <?php echo esc_attr( $sd_bento[ $sd_i % count( $sd_bento ) ] ); ?> relative rounded-3xl overflow-hidden border border-white/10 bg-white/[0.03]">
                <?php echo wp_get_attachment_image( $sd_image, 'large', false, array( 'class' => 'absolute inset-0 w-full h-full object-cover', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 768px) 66vw, 100vw' ) ); ?>
            </figure>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <?php if ( $sd_project['quote'] ) : ?>
        <figure class="max-w-4xl mx-auto px-6 mt-24 text-center">
            <blockquote class="text-2xl md:text-4xl font-semibold tracking-tight leading-snug text-white [text-wrap:balance]">“<?php echo esc_html( $sd_project['quote'] ); ?>”</blockquote>
            <?php if ( $sd_project['quote_by'] ) : ?>
            <figcaption class="mt-6 text-gray-400"><?php echo esc_html( $sd_project['quote_by'] ); ?></figcaption>
            <?php endif; ?>
        </figure>
        <?php endif; ?>
    </article>

    <?php if ( $sd_next ) : ?>
    <a href="<?php echo esc_url( $sd_next['permalink'] ); ?>" class="group relative z-10 block max-w-7xl mx-4 xl:mx-auto mt-24 rounded-[2rem] overflow-hidden border border-white/10" style="<?php echo $sd_next['image'] ? '' : esc_attr( sd_placeholder_style( 1 ) ); ?>">
        <?php if ( $sd_next['image'] ) : ?>
        <img src="<?php echo esc_url( $sd_next['image'] ); ?>" alt="" loading="lazy" decoding="async" class="absolute inset-0 w-full h-full object-cover opacity-60 transition-transform duration-700 group-hover:scale-105">
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-black/10"></div>
        <div class="relative px-6 md:px-12 py-16 md:py-24">
            <p class="text-sm font-bold tracking-[0.2em] uppercase text-gray-300 mb-3"><?php esc_html_e( 'Next project', 'serhandemirel' ); ?></p>
            <p class="text-4xl md:text-7xl font-black tracking-tighter text-white [text-wrap:balance]"><?php echo esc_html( $sd_next['title'] ); ?> <span class="inline-block transition-transform group-hover:translate-x-3" aria-hidden="true">→</span></p>
            <?php $sd_next_line = implode( ' · ', array_filter( array( $sd_next['location'], $sd_next['metric'] ) ) ); ?>
            <?php if ( $sd_next_line ) : ?>
            <p class="mt-3 text-gray-300"><?php echo esc_html( $sd_next_line ); ?></p>
            <?php endif; ?>
        </div>
    </a>
    <?php endif; ?>
</main>
	<?php
endwhile;

get_template_part( 'template-parts/contact' );
get_footer();
