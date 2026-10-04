<?php
/**
 * One insight (post) card. Expects $args['post'] and optional $args['index'].
 *
 * @package serhandemirel
 */

$card_post = $args['post'];
$hovers    = array( 'group-hover:text-pink-400', 'group-hover:text-blue-400' );
$hover     = $hovers[ ( $args['index'] ?? 0 ) % count( $hovers ) ];
?>
<a href="<?php echo esc_url( get_permalink( $card_post ) ); ?>" class="group block">
    <div class="overflow-hidden rounded-3xl mb-6 glass relative aspect-video">
        <?php if ( has_post_thumbnail( $card_post ) ) : ?>
            <?php echo get_the_post_thumbnail( $card_post, 'large', array( 'class' => 'w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700', 'loading' => 'lazy' ) ); ?>
        <?php endif; ?>
    </div>
    <p class="text-gray-400 text-sm mb-2 font-medium"><?php echo esc_html( sd_insight_meta( $card_post->ID ) ); ?></p>
    <h4 class="text-2xl font-bold text-white <?php echo esc_attr( $hover ); ?> transition-colors duration-300"><?php echo esc_html( get_the_title( $card_post ) ); ?></h4>
</a>
