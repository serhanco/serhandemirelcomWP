<?php
/**
 * Glossary (/glossary/): a letter index, a live search box and every term
 * with its short definition, grouped by first letter.
 *
 * @package serhandemirel
 */

get_header();

$sd_groups = sdc_get_glossary();
$sd_count  = array_sum( array_map( 'count', $sd_groups ) );
?>
<main id="main" class="relative overflow-hidden">
    <div class="sd-orb w-[50rem] h-[50rem] -top-96 left-1/3 bg-fuchsia-900/25"></div>

    <header class="relative z-10 max-w-6xl mx-auto px-6 pt-36 md:pt-44 pb-8">
        <p class="text-sm font-bold tracking-[0.2em] uppercase text-pink-500 mb-4"><?php esc_html_e( 'Glossary', 'serhandemirel' ); ?></p>
        <h1 class="text-5xl md:text-7xl font-black tracking-tighter leading-[0.95] max-w-3xl text-white [text-wrap:balance]"><?php echo wp_kses( __( 'Digital growth,<br><span class="gradient-text">in plain words.</span>', 'serhandemirel' ), array( 'br' => array(), 'span' => array( 'class' => array() ) ) ); ?></h1>
        <?php if ( sd_opt( 'glossary_intro' ) ) : ?>
        <p class="mt-6 text-lg text-gray-400 max-w-xl"><?php echo esc_html( sd_opt( 'glossary_intro' ) ); ?></p>
        <?php endif; ?>
        <?php if ( $sd_count ) : ?>
        <div class="mt-10 flex flex-col gap-4">
            <nav class="flex flex-wrap gap-1" aria-label="<?php esc_attr_e( 'Letters', 'serhandemirel' ); ?>">
                <?php foreach ( range( 'A', 'Z' ) as $sd_letter ) : ?>
                    <?php if ( isset( $sd_groups[ $sd_letter ] ) ) : ?>
                <a href="#l-<?php echo esc_attr( $sd_letter ); ?>" class="w-9 h-9 rounded-full flex items-center justify-center text-sm bg-white/10 text-white font-semibold hover:bg-white hover:text-black transition-colors"><?php echo esc_html( $sd_letter ); ?></a>
                    <?php else : ?>
                <span class="w-9 h-9 rounded-full flex items-center justify-center text-sm text-gray-700" aria-hidden="true"><?php echo esc_html( $sd_letter ); ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
            <label class="relative">
                <span class="sr-only"><?php esc_html_e( 'Search terms', 'serhandemirel' ); ?></span>
                <input type="search" data-live-search="#sd-glossary" placeholder="<?php esc_attr_e( 'Search terms', 'serhandemirel' ); ?>" class="w-full md:w-80 rounded-full bg-white/5 border border-white/10 pl-10 pr-4 py-2.5 text-sm text-white placeholder:text-gray-500 focus:outline-none focus:border-purple-500/60">
                <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="2"></circle><path d="m20 20-3.5-3.5" stroke-width="2" stroke-linecap="round"></path></svg>
            </label>
        </div>
        <?php endif; ?>
    </header>

    <div id="sd-glossary" class="relative z-10 max-w-6xl mx-auto px-6">
        <?php foreach ( $sd_groups as $sd_letter => $sd_terms ) : ?>
        <section id="l-<?php echo esc_attr( $sd_letter ); ?>" data-live-group class="grid md:grid-cols-[80px_1fr] gap-4 py-8 border-t border-white/10 scroll-mt-28">
            <h2 class="text-4xl font-black tracking-tighter gradient-text"><?php echo esc_html( $sd_letter ); ?></h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <?php foreach ( $sd_terms as $sd_t ) : ?>
                <a href="<?php echo esc_url( $sd_t['url'] ); ?>" data-live-item class="group rounded-3xl glass p-6 hover:bg-white/[0.06] transition-colors">
                    <span class="text-lg font-bold text-white flex justify-between gap-4"><?php echo esc_html( $sd_t['title'] ); ?><span class="text-gray-500 transition-transform group-hover:translate-x-1" aria-hidden="true">→</span></span>
                    <?php if ( $sd_t['short'] ) : ?>
                    <span class="block mt-2 text-sm text-gray-400 leading-relaxed"><?php echo esc_html( $sd_t['short'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( $sd_t['aliases'] ) : ?>
                    <span class="sr-only"><?php echo esc_html( implode( ', ', $sd_t['aliases'] ) ); ?></span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>
        <p data-live-empty hidden class="py-10 text-gray-400"><?php esc_html_e( 'No term matches. Try another word.', 'serhandemirel' ); ?></p>
        <?php if ( ! $sd_count ) : ?>
        <p class="py-10 text-gray-400"><?php esc_html_e( 'No terms yet.', 'serhandemirel' ); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php
get_template_part( 'template-parts/contact' );
get_footer();
