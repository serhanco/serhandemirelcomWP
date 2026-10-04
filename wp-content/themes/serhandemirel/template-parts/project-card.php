<?php
/**
 * One project card. Expects $args['project'] from sdc_get_projects().
 *
 * @package serhandemirel
 */

$project = $args['project'];
$accents = array( 'text-blue-400', 'text-purple-400', 'text-pink-400', 'text-emerald-400' );
$accent  = $accents[ ( $args['index'] ?? 0 ) % count( $accents ) ];
?>
<a href="<?php echo esc_url( $project['permalink'] ); ?>" class="portfolio-item group relative block rounded-3xl overflow-hidden glass aspect-[4/3] cursor-pointer" data-category="<?php echo esc_attr( implode( ' ', $project['industries'] ) ); ?>">
    <?php if ( $project['image'] ) : ?>
    <img src="<?php echo esc_url( $project['image'] ); ?>" alt="<?php echo esc_attr( $project['title'] ); ?>" loading="lazy" class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-80 transition-all duration-700 group-hover:scale-105">
    <?php endif; ?>
    <?php if ( $project['brand_logo'] ) : ?>
    <img src="<?php echo esc_url( $project['brand_logo'] ); ?>" alt="<?php echo esc_attr( $project['client'] ); ?>" loading="lazy" class="absolute top-6 left-6 h-8 w-auto opacity-80">
    <?php endif; ?>
    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent flex flex-col justify-end p-8">
        <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
            <?php if ( $project['location'] ) : ?>
            <p class="<?php echo esc_attr( $accent ); ?> text-sm font-bold mb-2"><?php echo esc_html( $project['location'] ); ?></p>
            <?php endif; ?>
            <h4 class="text-2xl md:text-3xl font-bold text-white mb-2"><?php echo esc_html( $project['title'] ); ?></h4>
            <?php if ( $project['summary'] || $project['metric'] ) : ?>
            <p class="text-gray-400 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                <?php if ( $project['metric'] ) : ?><span class="text-white font-semibold"><?php echo esc_html( $project['metric'] ); ?></span><?php echo $project['summary'] ? ' · ' : ''; ?><?php endif; ?><?php echo esc_html( $project['summary'] ); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</a>
