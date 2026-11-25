<?php
/**
 * Author box under an article: who wrote it, what they know, where to check.
 *
 * @package serhandemirel
 */

$sd_author = sd_author();
$sd_about  = sd_about_url();
$sd_talks  = sd_archive_url_if_any( 'sd_appearance' );
?>
<section class="mt-14 rounded-3xl border border-white/10 p-6 md:p-7" aria-label="<?php esc_attr_e( 'About the author', 'serhandemirel' ); ?>">
    <div class="flex gap-5 items-start">
        <?php if ( $sd_author['photo'] ) : ?>
        <img src="<?php echo esc_url( $sd_author['photo'] ); ?>" alt="<?php echo esc_attr( $sd_author['name'] ); ?>" width="64" height="64" class="w-16 h-16 rounded-2xl object-cover shrink-0" loading="lazy">
        <?php endif; ?>
        <div class="min-w-0">
            <p class="font-bold text-white"><?php echo esc_html( $sd_author['name'] ); ?></p>
            <p class="text-sm text-gray-400"><?php echo esc_html( sd_author_line( $sd_author ) ); ?></p>
        </div>
    </div>
    <?php if ( $sd_author['bio'] ) : ?>
    <p class="mt-4 text-[15px] text-gray-300 leading-relaxed"><?php echo esc_html( $sd_author['bio'] ); ?></p>
    <?php endif; ?>
    <?php if ( $sd_author['expertise'] ) : ?>
    <ul class="mt-4 flex flex-wrap gap-2" aria-label="<?php esc_attr_e( 'Expertise', 'serhandemirel' ); ?>">
        <?php foreach ( $sd_author['expertise'] as $sd_topic ) : ?>
        <li class="text-xs px-3 py-1 rounded-full bg-white/5 border border-white/10 text-gray-300"><?php echo esc_html( $sd_topic ); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <div class="mt-5 flex flex-wrap gap-2 text-sm">
        <button type="button" onclick="openProjectModal()" class="px-4 py-1.5 rounded-full bg-white text-black font-semibold hover:bg-gray-200 transition-colors"><?php esc_html_e( 'Work with me', 'serhandemirel' ); ?></button>
        <?php if ( $sd_about ) : ?>
        <a href="<?php echo esc_url( $sd_about ); ?>" rel="author" class="px-4 py-1.5 rounded-full border border-white/10 text-gray-200 hover:bg-white/10 transition-colors"><?php esc_html_e( 'About', 'serhandemirel' ); ?></a>
        <?php endif; ?>
        <?php if ( $sd_talks ) : ?>
        <a href="<?php echo esc_url( $sd_talks ); ?>" class="px-4 py-1.5 rounded-full border border-white/10 text-gray-200 hover:bg-white/10 transition-colors"><?php esc_html_e( 'Talks & press', 'serhandemirel' ); ?></a>
        <?php endif; ?>
        <?php if ( $sd_author['linkedin'] ) : ?>
        <a href="<?php echo esc_url( $sd_author['linkedin'] ); ?>" target="_blank" rel="noopener me" class="px-3 py-1.5 rounded-full border border-white/10 text-gray-200 hover:bg-white/10 transition-colors flex items-center gap-1.5"><?php sd_icon_linkedin(); ?>LinkedIn</a>
        <?php endif; ?>
    </div>
</section>
