<?php
/**
 * One testimonial card. Args: item (from sdc_testimonial_data()), index, large (bool).
 *
 * @package serhandemirel
 */

$sd_t = $args['item'] ?? null;
if ( ! $sd_t ) {
	return;
}
$sd_gradients = array( '#6366f1,#ec4899', '#0ea5e9,#22c55e', '#f97316,#a855f7', '#14b8a6,#6366f1' );
$sd_gradient  = $sd_gradients[ (int) ( $args['index'] ?? 0 ) % count( $sd_gradients ) ];
$sd_initials  = '';
foreach ( array_slice( preg_split( '/\s+/', trim( preg_replace( '/^(Dr|Prof|Dr\.|Prof\.)\s+/i', '', $sd_t['person'] ) ) ), 0, 2 ) as $sd_word ) {
	$sd_initials .= mb_substr( $sd_word, 0, 1 );
}
$sd_role = implode( ', ', array_filter( array( $sd_t['role'], $sd_t['company'] ) ) );
?>
<figure class="rounded-3xl glass p-6 md:p-8 <?php echo ! empty( $args['large'] ) ? 'md:p-10' : ''; ?> flex flex-col justify-between gap-8">
    <blockquote class="<?php echo ! empty( $args['large'] ) ? 'text-xl md:text-2xl' : 'text-lg md:text-xl'; ?> leading-relaxed text-gray-100">“<?php echo esc_html( $sd_t['quote'] ); ?>”</blockquote>
    <figcaption class="flex items-center gap-4">
        <?php if ( $sd_t['photo'] ) : ?>
        <img src="<?php echo esc_url( $sd_t['photo'] ); ?>" alt="" width="48" height="48" loading="lazy" class="w-12 h-12 shrink-0 rounded-full object-cover">
        <?php else : ?>
        <span class="w-12 h-12 shrink-0 rounded-full flex items-center justify-center text-sm font-bold text-white" style="background:linear-gradient(135deg,<?php echo esc_attr( $sd_gradient ); ?>)" aria-hidden="true"><?php echo esc_html( $sd_initials ); ?></span>
        <?php endif; ?>
        <div class="min-w-0 flex-1">
            <p class="font-semibold text-white"><?php echo esc_html( $sd_t['person'] ); ?></p>
            <?php if ( $sd_role ) : ?>
            <p class="text-sm text-gray-400"><?php echo esc_html( $sd_role ); ?></p>
            <?php endif; ?>
            <?php if ( ! empty( $args['show_project'] ) && $sd_t['project_url'] ) : ?>
            <a href="<?php echo esc_url( $sd_t['project_url'] ); ?>" class="text-xs text-gray-500 hover:text-white"><?php esc_html_e( 'See the project', 'serhandemirel' ); ?> →</a>
            <?php endif; ?>
        </div>
        <?php if ( $sd_t['linkedin'] ) : ?>
        <a href="<?php echo esc_url( $sd_t['linkedin'] ); ?>" target="_blank" rel="noopener" class="w-9 h-9 shrink-0 rounded-full border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-colors" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name */ __( '%s on LinkedIn', 'serhandemirel' ), $sd_t['person'] ) ); ?>"><?php sd_icon_linkedin(); ?></a>
        <?php endif; ?>
    </figcaption>
</figure>
