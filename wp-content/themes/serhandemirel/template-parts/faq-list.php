<?php
/**
 * Questions as open/close rows. Args: items (question, answer, optional from/from_url),
 * open_first (bool), show_from (bool).
 *
 * @package serhandemirel
 */

$sd_items = $args['items'] ?? array();
if ( ! $sd_items ) {
	return;
}
?>
<div class="space-y-3">
    <?php foreach ( $sd_items as $sd_i => $sd_qa ) : ?>
    <details class="group glass rounded-2xl px-5 py-4" data-faq-item<?php echo ( 0 === $sd_i && ! empty( $args['open_first'] ) ) ? ' open' : ''; ?>>
        <summary class="flex justify-between items-center gap-4 font-semibold text-white cursor-pointer"><?php echo esc_html( $sd_qa['question'] ); ?><svg class="w-5 h-5 shrink-0 text-gray-400 transition-transform group-open:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"></path></svg></summary>
        <p class="mt-3 text-gray-400 leading-relaxed"><?php echo esc_html( $sd_qa['answer'] ); ?></p>
        <?php if ( ! empty( $args['show_from'] ) && ! empty( $sd_qa['from_url'] ) ) : ?>
        <a href="<?php echo esc_url( $sd_qa['from_url'] ); ?>" class="mt-3 inline-flex text-xs text-gray-500 hover:text-white"><?php echo esc_html( sprintf( /* translators: %s: page title */ __( 'From: %s', 'serhandemirel' ), $sd_qa['from'] ) ); ?> →</a>
        <?php endif; ?>
    </details>
    <?php endforeach; ?>
</div>
