<?php
/**
 * Numbered source list. Args: sources (from sdc_sources()), heading (string, empty for none).
 *
 * @package serhandemirel
 */

$sd_sources = $args['sources'] ?? array();
if ( ! $sd_sources ) {
	return;
}
?>
<section<?php echo ! empty( $args['id'] ) ? ' id="' . esc_attr( $args['id'] ) . '" class="scroll-mt-28"' : ''; ?>>
    <?php if ( ! empty( $args['heading'] ) ) : ?>
    <h2 class="text-[11px] font-semibold tracking-[0.2em] uppercase text-gray-500 mb-2"><?php echo esc_html( $args['heading'] ); ?></h2>
    <?php endif; ?>
    <ol class="text-[15px]">
        <?php foreach ( $sd_sources as $sd_i => $sd_src ) : ?>
        <li id="src-<?php echo esc_attr( $sd_i + 1 ); ?>" class="flex gap-4 py-3 border-t border-white/5 scroll-mt-28">
            <span class="w-6 h-6 shrink-0 rounded-full bg-white/5 border border-white/10 text-[11px] font-semibold flex items-center justify-center text-gray-400"><?php echo esc_html( $sd_i + 1 ); ?></span>
            <span class="min-w-0">
                <?php if ( $sd_src['url'] ) : ?>
                <a href="<?php echo esc_url( $sd_src['url'] ); ?>" target="_blank" rel="noopener" class="text-gray-200 hover:text-white underline decoration-purple-500/60 underline-offset-4 break-words"><?php echo esc_html( $sd_src['title'] ); ?></a>
                <?php else : ?>
                <span class="text-gray-200"><?php echo esc_html( $sd_src['title'] ); ?></span>
                <?php endif; ?>
                <?php if ( $sd_src['publisher'] ) : ?>
                <span class="block text-sm text-gray-500"><?php echo esc_html( $sd_src['publisher'] ); ?></span>
                <?php endif; ?>
            </span>
        </li>
        <?php endforeach; ?>
    </ol>
</section>
