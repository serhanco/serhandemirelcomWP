<?php
/**
 * Published and updated dates with the "What changed" note.
 *
 * Args: dates (from sdc_dates()), center (bool), size (text class).
 *
 * @package serhandemirel
 */

$sd_dates = $args['dates'] ?? array();
if ( empty( $sd_dates['published'] ) && empty( $sd_dates['updated'] ) ) {
	return;
}
$sd_center = ! empty( $args['center'] ) ? 'justify-center' : '';
$sd_size   = $args['size'] ?? 'text-sm';
$sd_size   = in_array( $sd_size, array( 'text-sm', 'text-xs' ), true ) ? $sd_size : 'text-sm';
?>
<div class="flex flex-wrap items-center <?php echo esc_attr( $sd_center . ' ' . $sd_size ); ?> gap-x-4 gap-y-1 text-gray-500">
    <?php if ( ! empty( $sd_dates['published'] ) ) : ?>
    <span><?php esc_html_e( 'Published', 'serhandemirel' ); ?> <time datetime="<?php echo esc_attr( $sd_dates['published'] ); ?>"><?php echo esc_html( sdc_date_label( $sd_dates['published'] ) ); ?></time></span>
    <?php endif; ?>
    <?php if ( ! empty( $sd_dates['updated'] ) ) : ?>
    <span class="text-gray-300"><?php esc_html_e( 'Updated', 'serhandemirel' ); ?> <time datetime="<?php echo esc_attr( $sd_dates['updated'] ); ?>"><?php echo esc_html( sdc_date_label( $sd_dates['updated'] ) ); ?></time></span>
    <?php endif; ?>
</div>
<?php if ( ! empty( $sd_dates['note'] ) ) : ?>
<p class="mt-3 flex <?php echo esc_attr( $sd_center ); ?>"><span class="inline-flex items-center gap-2 text-xs text-gray-400 rounded-full border border-white/10 bg-white/[0.03] px-3 py-1.5 text-left"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0" aria-hidden="true"></span><span><span class="text-gray-300"><?php esc_html_e( 'What changed:', 'serhandemirel' ); ?></span> <?php echo esc_html( $sd_dates['note'] ); ?></span></span></p>
<?php endif; ?>
