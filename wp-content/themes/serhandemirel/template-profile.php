<?php
/**
 * Template Name: Profile (About)
 *
 * The About page: who Serhan is (from the Profile screen), a few numbers,
 * the bio (the page's own content, or the long bio when the page is
 * empty), talks and press, what clients say, brands by industry,
 * expertise, certificates, awards, and the page's dates.
 *
 * @package serhandemirel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$profile = sdc_get_profile();
	$author  = sd_author();
	$name    = $profile['full_name'] ?? get_bloginfo( 'name' );
	$photo   = $profile['headshot_url'] ?? '';
	$place   = $profile['city'] ?? '';
	$stats   = sdc_profile_stats();
	$talks   = sdc_get_appearances( 4 );
	$quotes  = sdc_get_testimonials( array( 'limit' => 2 ) );
	$brands  = sdc_brands_by_industry();
	$certs   = sdc_parse_credentials( $profile['credentials'] ?? array() );
	$schools = $profile['alumni_of'] ?? array();
	$honours = array_merge( sdc_parse_credentials( $profile['awards'] ?? array() ), sdc_parse_credentials( $profile['memberships'] ?? array() ) );
	$langs   = $profile['languages_spoken'] ?? array();
	$cols    = array( 1 => 'md:grid-cols-1', 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4' );
	?>
<main id="main" class="relative overflow-hidden">
    <div class="sd-orb w-[45rem] h-[45rem] -top-60 -left-60 bg-purple-900/25"></div>

    <article <?php post_class( 'relative z-10 max-w-6xl mx-auto px-6 pt-36 md:pt-44' ); ?>>
        <header class="flex flex-col md:flex-row md:items-center gap-8 md:gap-12">
            <?php if ( $photo ) : ?>
            <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="224" height="224" class="w-40 h-40 md:w-56 md:h-56 rounded-full object-cover border border-white/10 shrink-0">
            <?php endif; ?>
            <div>
                <p class="text-sm font-bold tracking-[0.2em] uppercase text-purple-500 mb-4"><?php the_title(); ?></p>
                <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-tight text-white"><?php echo esc_html( $name ); ?></h1>
                <p class="mt-3 text-lg md:text-xl text-gray-300">
                    <?php echo esc_html( $profile['job_title'] ?? '' ); ?>
                    <?php if ( $place ) : ?><span class="text-gray-500"> · <?php echo esc_html( $place ); ?></span><?php endif; ?>
                </p>
                <?php if ( $langs ) : ?>
                <p class="mt-1 text-sm text-gray-500"><?php echo esc_html( sprintf( /* translators: %s: list of languages */ __( 'Works in %s', 'serhandemirel' ), implode( ', ', $langs ) ) ); ?></p>
                <?php endif; ?>
                <?php if ( ! empty( $profile['same_as'] ) ) : ?>
                <div class="mt-5 flex flex-wrap gap-2 text-sm">
                    <?php foreach ( $profile['same_as'] as $link ) : ?>
                        <?php $host = preg_replace( '/^www\./', '', (string) wp_parse_url( $link, PHP_URL_HOST ) ); ?>
                    <a href="<?php echo esc_url( $link ); ?>" rel="me noopener" target="_blank" class="px-3 py-1.5 rounded-full border border-white/10 text-gray-300 hover:text-white hover:bg-white/10 transition-colors flex items-center gap-1.5">
                        <?php if ( false !== strpos( $host, 'linkedin.com' ) ) : ?>
                            <?php sd_icon_linkedin(); ?>LinkedIn
                        <?php else : ?>
                            <?php echo esc_html( ucfirst( preg_replace( '/\.(com|org|net|io|co)$/', '', $host ) ) ); ?>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </header>

        <?php if ( $stats ) : ?>
        <div class="mt-12 grid grid-cols-2 <?php echo esc_attr( $cols[ count( $stats ) ] ?? 'md:grid-cols-4' ); ?> gap-4">
            <?php foreach ( $stats as $stat ) : ?>
            <div class="rounded-3xl glass p-5 md:p-6">
                <p class="text-4xl md:text-5xl font-black tracking-tighter gradient-text pb-1"><?php echo esc_html( $stat['value'] ); ?></p>
                <p class="mt-2 text-sm text-gray-400"><?php echo esc_html( $stat['label'] ); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( ! empty( $profile['short_bio'] ) ) : ?>
        <p class="mt-12 text-lg md:text-2xl text-gray-300 font-light leading-relaxed max-w-3xl"><?php echo esc_html( $profile['short_bio'] ); ?></p>
        <?php endif; ?>

        <?php
        $bio = '' !== trim( get_the_content() ) ? apply_filters( 'the_content', get_the_content() ) : ( ! empty( $profile['long_bio'] ) ? wpautop( $profile['long_bio'] ) : '' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core filter.
        ?>
        <?php if ( $bio ) : ?>
        <div class="sd-prose max-w-3xl mt-10"><?php echo wp_kses_post( $bio ); ?></div>
        <?php endif; ?>

        <?php if ( $talks ) : ?>
        <section class="mt-20">
            <div class="flex items-end justify-between gap-4 mb-4">
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white"><?php esc_html_e( 'Talks, trainings & press', 'serhandemirel' ); ?></h2>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'sd_appearance' ) ); ?>" class="text-sm text-gray-400 hover:text-white shrink-0 transition-colors"><?php esc_html_e( 'All', 'serhandemirel' ); ?> →</a>
            </div>
            <ul>
                <?php
                foreach ( $talks as $item ) {
                    get_template_part( 'template-parts/appearance-row', null, array( 'item' => $item ) );
                }
                ?>
            </ul>
        </section>
        <?php endif; ?>

        <?php if ( $quotes ) : ?>
        <section class="mt-20">
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white mb-8"><?php esc_html_e( 'What clients say', 'serhandemirel' ); ?></h2>
            <div class="grid <?php echo count( $quotes ) > 1 ? 'md:grid-cols-2' : ''; ?> gap-5">
                <?php
                foreach ( $quotes as $i => $quote ) {
                    get_template_part( 'template-parts/testimonial', null, array( 'item' => $quote, 'index' => $i + 1, 'show_project' => true ) );
                }
                ?>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( $brands ) : ?>
        <section class="mt-20">
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white mb-8"><?php esc_html_e( 'Brands I have worked with', 'serhandemirel' ); ?></h2>
            <div class="space-y-5">
                <?php foreach ( $brands as $group ) : ?>
                <div class="grid md:grid-cols-[180px_1fr] gap-3 items-center">
                    <?php if ( $group['term'] ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $group['term'] ) ); ?>" class="text-sm font-semibold text-gray-400 hover:text-white transition-colors"><?php echo esc_html( $group['term']->name ); ?> →</a>
                    <?php else : ?>
                    <span class="text-sm font-semibold text-gray-500"><?php echo esc_html( count( $brands ) > 1 ? __( 'Other', 'serhandemirel' ) : '' ); ?></span>
                    <?php endif; ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ( $group['brands'] as $brand ) : ?>
                        <span class="h-12 px-4 rounded-xl bg-white/[0.04] border border-white/10 flex items-center"><img src="<?php echo esc_url( $brand['logo'] ); ?>" alt="<?php echo esc_attr( $brand['name'] ); ?>" loading="lazy" class="h-6 w-auto max-w-[120px] object-contain opacity-80"></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php
        $cards = array_filter(
            array(
                'expertise' => ! empty( $profile['knows_about'] ),
                'certs'     => $certs || $schools,
                'honours'   => (bool) $honours,
            )
        );
        ?>
        <?php if ( $cards ) : ?>
        <div class="mt-20 grid <?php echo esc_attr( $cols[ count( $cards ) ] ); ?> gap-5">
            <?php if ( ! empty( $cards['expertise'] ) ) : ?>
            <section class="glass rounded-3xl p-7">
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-blue-500 mb-4"><?php esc_html_e( 'Expertise', 'serhandemirel' ); ?></h2>
                <ul class="flex flex-wrap gap-2">
                    <?php foreach ( $profile['knows_about'] as $topic ) : ?>
                    <li class="px-3 py-1.5 rounded-full bg-white/5 text-sm text-gray-300"><?php echo esc_html( $topic ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>
            <?php if ( ! empty( $cards['certs'] ) ) : ?>
            <section class="glass rounded-3xl p-7">
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-emerald-500 mb-4"><?php echo esc_html( $certs ? __( 'Certificates', 'serhandemirel' ) : __( 'Education', 'serhandemirel' ) ); ?></h2>
                <ul class="space-y-2 text-gray-300">
                    <?php foreach ( $certs as $cert ) : ?>
                    <li>
                        <?php if ( $cert['url'] ) : ?>
                        <a href="<?php echo esc_url( $cert['url'] ); ?>" target="_blank" rel="noopener" class="hover:text-white underline decoration-white/20 underline-offset-4"><?php echo esc_html( $cert['name'] ); ?></a>
                        <?php else : ?>
                            <?php echo esc_html( $cert['name'] ); ?>
                        <?php endif; ?>
                        <?php if ( $cert['detail'] ) : ?><span class="text-gray-500"> · <?php echo esc_html( $cert['detail'] ); ?></span><?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                    <?php foreach ( $schools as $school ) : ?>
                    <li><?php echo esc_html( $school ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>
            <?php if ( ! empty( $cards['honours'] ) ) : ?>
            <section class="glass rounded-3xl p-7">
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-pink-500 mb-4"><?php esc_html_e( 'Awards & memberships', 'serhandemirel' ); ?></h2>
                <ul class="space-y-2 text-gray-300">
                    <?php foreach ( $honours as $item ) : ?>
                    <li><?php echo esc_html( $item['name'] ); ?><?php if ( $item['detail'] ) : ?><span class="text-gray-500"> · <?php echo esc_html( $item['detail'] ); ?></span><?php endif; ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="mt-14 flex flex-col sm:flex-row sm:items-center justify-between gap-6 pt-6 border-t border-white/10">
            <div class="flex flex-wrap gap-3">
                <button type="button" onclick="openProjectModal()" class="px-6 py-3 rounded-full bg-white text-black font-semibold hover:bg-gray-200 transition-colors"><?php esc_html_e( 'Start a Project', 'serhandemirel' ); ?></button>
                <?php if ( $author['booking'] ) : ?>
                <a href="<?php echo esc_url( $author['booking'] ); ?>" target="_blank" rel="noopener" class="px-6 py-3 rounded-full border border-white/15 text-white font-semibold hover:bg-white/5 transition-colors"><?php esc_html_e( 'Book a 15-min call', 'serhandemirel' ); ?> ↗</a>
                <?php endif; ?>
            </div>
            <div><?php get_template_part( 'template-parts/dates', null, array( 'dates' => sdc_dates( get_the_ID() ), 'size' => 'text-xs' ) ); ?></div>
        </div>
    </article>
</main>
	<?php
endwhile;

get_template_part( 'template-parts/contact' );
get_footer();
