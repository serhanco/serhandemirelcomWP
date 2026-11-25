<?php
/**
 * An insight card on the Insights pages. Expects $args['post'] and optional $args['index'].
 *
 * @package serhandemirel
 */

$tile_post  = $args['post'];
$tile_index = (int) ( $args['index'] ?? 0 );
$tile_cats  = get_the_category( $tile_post->ID );
$tile_cat   = ( $tile_cats && 'uncategorized' !== $tile_cats[0]->slug ) ? $tile_cats[0]->name : '';
$tile_find  = function_exists( 'mb_strtolower' ) ? mb_strtolower( get_the_title( $tile_post ) . ' ' . $tile_cat ) : strtolower( get_the_title( $tile_post ) . ' ' . $tile_cat );
?>
<article data-insight-card data-search="<?php echo esc_attr( $tile_find ); ?>" class="sd-rise" style="animation-delay:<?php echo esc_attr( min( $tile_index, 6 ) * 60 ); ?>ms">
    <a href="<?php echo esc_url( get_permalink( $tile_post ) ); ?>" class="group flex flex-col h-full rounded-3xl glass p-2 hover:bg-white/[0.06] transition-colors duration-300">
        <div class="relative aspect-[16/10] rounded-[1.25rem] overflow-hidden" style="<?php echo has_post_thumbnail( $tile_post ) ? '' : esc_attr( sd_placeholder_style( $tile_index + 1 ) ); ?>">
            <?php if ( has_post_thumbnail( $tile_post ) ) : ?>
                <?php echo get_the_post_thumbnail( $tile_post, 'large', array( 'class' => 'absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 1024px) 380px, (min-width: 640px) 50vw, 100vw' ) ); ?>
            <?php endif; ?>
            <?php if ( $tile_cat ) : ?>
            <span class="absolute top-3 left-3 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-black/40 backdrop-blur border border-white/10 text-white"><?php echo esc_html( $tile_cat ); ?></span>
            <?php endif; ?>
        </div>
        <div class="p-4 md:p-5 flex flex-col flex-1">
            <?php sd_insight_badges( $tile_post->ID ); ?>
            <p class="text-xs text-gray-500 mb-2"><time datetime="<?php echo esc_attr( get_the_date( 'c', $tile_post ) ); ?>"><?php echo esc_html( get_the_date( '', $tile_post ) ); ?></time> · <?php echo esc_html( sprintf( /* translators: %d: minutes */ _n( '%d min read', '%d min read', sd_read_time( $tile_post->ID ), 'serhandemirel' ), sd_read_time( $tile_post->ID ) ) ); ?></p>
            <h2 class="text-lg font-bold leading-snug tracking-tight text-gray-100 group-hover:text-white [text-wrap:balance]"><?php echo esc_html( get_the_title( $tile_post ) ); ?></h2>
            <span class="mt-auto pt-4 inline-flex items-center gap-1 text-sm text-gray-400 group-hover:text-white transition-colors"><?php esc_html_e( 'Read', 'serhandemirel' ); ?> <span class="transition-transform group-hover:translate-x-1" aria-hidden="true">→</span></span>
        </div>
    </a>
</article>
