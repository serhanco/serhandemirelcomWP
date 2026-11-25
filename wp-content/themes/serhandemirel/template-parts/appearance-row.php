<?php
/**
 * One talk, training or press item as a list row. Args: item (from sdc_appearance_data()).
 *
 * @package serhandemirel
 */

$sd_item = $args['item'] ?? null;
if ( ! $sd_item ) {
	return;
}
$sd_links = sdc_appearance_links( $sd_item );
$sd_line  = sdc_appearance_line( $sd_item );
?>
<li id="item-<?php echo esc_attr( $sd_item['id'] ); ?>" data-kind="<?php echo esc_attr( $sd_item['kind'] ); ?>" class="grid grid-cols-[72px_1fr] md:grid-cols-[110px_110px_1fr_auto] gap-x-4 gap-y-2 items-center py-5 border-t border-white/10 scroll-mt-28">
    <time datetime="<?php echo esc_attr( $sd_item['date'] ); ?>" class="text-sm text-gray-500 font-mono"><?php echo esc_html( $sd_item['month'] ); ?></time>
    <span class="justify-self-start text-[11px] font-semibold px-2.5 py-1 rounded-full border <?php echo esc_attr( sdc_appearance_kind_class( $sd_item['kind'] ) ); ?>"><?php echo esc_html( $sd_item['kind_label'] ); ?></span>
    <div class="col-span-2 md:col-span-1 min-w-0">
        <p class="font-semibold text-white"><?php echo esc_html( $sd_item['title'] ); ?></p>
        <?php if ( $sd_line ) : ?>
        <p class="text-sm text-gray-500"><?php echo esc_html( $sd_line ); ?></p>
        <?php endif; ?>
    </div>
    <?php if ( $sd_links ) : ?>
    <div class="col-span-2 md:col-span-1 flex flex-wrap gap-2">
        <?php foreach ( $sd_links as $sd_label => $sd_url ) : ?>
        <a href="<?php echo esc_url( $sd_url ); ?>" target="_blank" rel="noopener" class="text-xs px-3 py-1 rounded-full border border-white/10 text-gray-300 hover:bg-white/10 transition-colors"><?php echo esc_html( $sd_label ); ?> ↗</a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</li>
