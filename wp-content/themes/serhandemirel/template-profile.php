<?php
/**
 * Template Name: Profile (About)
 *
 * The About page: who Serhan is, from the plugin's Profile screen, then
 * the page's own content (or the long bio when the page is empty).
 *
 * @package serhandemirel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$profile = function_exists( 'sdc_get_profile' ) ? sdc_get_profile() : array();
	$name    = $profile['full_name'] ?? get_bloginfo( 'name' );
	$photo   = $profile['headshot_url'] ?? '';
	$place   = trim( ( $profile['city'] ?? '' ) . ( ! empty( $profile['country'] ) ? ', ' . $profile['country'] : '' ), ', ' );
	$facts   = array(
		__( 'Languages', 'serhandemirel' )    => $profile['languages_spoken'] ?? array(),
		__( 'Education', 'serhandemirel' )    => $profile['alumni_of'] ?? array(),
		__( 'Certificates', 'serhandemirel' ) => $profile['credentials'] ?? array(),
	);
	?>
<main class="relative overflow-hidden">
    <div class="absolute top-[-10%] left-[-20%] w-[400px] md:w-[50vw] h-[400px] md:h-[50vw] rounded-full bg-purple-900/20 blur-[120px] pointer-events-none z-0"></div>

    <article <?php post_class( 'relative z-10 max-w-5xl mx-auto px-6 lg:px-8 pt-40 pb-24' ); ?>>
        <header class="flex flex-col md:flex-row md:items-center gap-8 md:gap-12 mb-16">
            <?php if ( $photo ) : ?>
            <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="224" height="224" class="w-40 h-40 md:w-56 md:h-56 rounded-full object-cover border border-white/10 shrink-0">
            <?php endif; ?>
            <div>
                <p class="text-sm font-bold tracking-[0.2em] uppercase text-purple-500 mb-4"><?php the_title(); ?></p>
                <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-tight text-white mb-4"><?php echo esc_html( $name ); ?></h1>
                <p class="text-lg md:text-xl text-gray-300">
                    <?php echo esc_html( $profile['job_title'] ?? '' ); ?>
                    <?php if ( $place ) : ?><span class="text-gray-500"> · <?php echo esc_html( $place ); ?></span><?php endif; ?>
                </p>
            </div>
        </header>

        <?php if ( ! empty( $profile['short_bio'] ) ) : ?>
        <p class="text-lg md:text-2xl text-gray-300 font-light leading-relaxed max-w-3xl mb-12"><?php echo esc_html( $profile['short_bio'] ); ?></p>
        <?php endif; ?>

        <div class="sd-prose max-w-3xl mb-16">
            <?php
            if ( '' !== trim( get_the_content() ) ) {
                the_content();
            } elseif ( ! empty( $profile['long_bio'] ) ) {
                echo wp_kses_post( wpautop( $profile['long_bio'] ) );
            }
            ?>
        </div>

        <?php if ( ! empty( $profile['knows_about'] ) ) : ?>
        <section class="mb-16">
            <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-blue-500 mb-6"><?php esc_html_e( 'Areas of expertise', 'serhandemirel' ); ?></h2>
            <ul class="flex flex-wrap gap-2">
                <?php foreach ( $profile['knows_about'] as $topic ) : ?>
                <li class="px-4 py-2 rounded-full glass text-sm text-gray-300"><?php echo esc_html( $topic ); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endif; ?>

        <?php if ( array_filter( $facts ) ) : ?>
        <div class="grid md:grid-cols-3 gap-6 mb-16">
            <?php foreach ( array_filter( $facts ) as $label => $items ) : ?>
            <section class="glass rounded-3xl p-8">
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-emerald-500 mb-4"><?php echo esc_html( $label ); ?></h2>
                <ul class="space-y-2 text-gray-300">
                    <?php foreach ( $items as $item ) : ?>
                    <li><?php echo esc_html( $item ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="flex flex-wrap items-center gap-3">
            <button type="button" onclick="openProjectModal()" class="px-6 py-3 rounded-full bg-white text-black font-semibold hover:bg-gray-200 transition-colors"><?php esc_html_e( 'Start a Project', 'serhandemirel' ); ?></button>
            <?php foreach ( $profile['same_as'] ?? array() as $link ) : ?>
            <a href="<?php echo esc_url( $link ); ?>" rel="me noopener" target="_blank" class="px-5 py-3 rounded-full glass text-gray-300 hover:text-white transition-colors"><?php echo esc_html( preg_replace( '/^www\./', '', (string) wp_parse_url( $link, PHP_URL_HOST ) ) ); ?></a>
            <?php endforeach; ?>
        </div>
    </article>
</main>
	<?php
endwhile;

get_template_part( 'template-parts/contact' );
get_footer();
