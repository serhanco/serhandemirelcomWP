<?php
/**
 * Insight (post) page: reading bar, format and question, title, short
 * answer, byline with published and updated dates, cover, key takeaways,
 * the "From my work" box, outline beside the text, sources, FAQ, author
 * box, glossary links and two posts to read next.
 *
 * @package serhandemirel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$sd_id       = get_the_ID();
	$sd_extras   = sdc_post_extras( $sd_id );
	$sd_author   = sd_author();
	$sd_outline  = sd_article_outline( apply_filters( 'the_content', get_the_content() ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core filter.
	$sd_toc      = $sd_outline['toc'];
	$sd_outline['html'] = sdc_link_citations( $sd_outline['html'], count( $sd_extras['sources'] ) );
	$sd_cats     = get_the_category();
	$sd_cat      = ( $sd_cats && 'uncategorized' !== $sd_cats[0]->slug ) ? $sd_cats[0] : null;
	$sd_blog_url = sd_insights_url();
	$sd_about    = sd_about_url();
	$sd_share    = rawurlencode( get_permalink() );
	if ( $sd_extras['takeaways'] ) {
		array_unshift( $sd_toc, array( 'id' => 'key-takeaways', 'title' => __( 'Key takeaways', 'serhandemirel' ) ) );
	}
	if ( $sd_extras['sources'] ) {
		$sd_toc[] = array( 'id' => 'sources', 'title' => __( 'Sources', 'serhandemirel' ) );
	}
	if ( $sd_extras['faq'] ) {
		$sd_toc[] = array( 'id' => 'faq', 'title' => __( 'FAQ', 'serhandemirel' ) );
	}
	?>
<div class="fixed top-0 left-0 right-0 h-[3px] z-[130] pointer-events-none" aria-hidden="true"><div id="sd-reading-bar" class="h-full w-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div></div>

<main id="main" class="relative overflow-hidden">
    <div class="sd-orb w-[45rem] h-[45rem] -top-80 left-1/2 -translate-x-1/2 bg-purple-900/30"></div>

    <article <?php post_class( 'relative z-10' ); ?> data-sd-article>
        <header class="max-w-3xl mx-auto px-6 pt-36 md:pt-44 text-center">
            <div class="flex justify-center">
                <?php
                $sd_crumbs = array();
                if ( $sd_blog_url ) {
                    $sd_crumbs[] = array( sd_opt( 'insights_eyebrow' ), $sd_blog_url );
                }
                if ( $sd_cat ) {
                    $sd_crumbs[] = array( $sd_cat->name, get_category_link( $sd_cat ) );
                }
                $sd_crumbs[] = array( get_the_title(), '' );
                sd_breadcrumb( $sd_crumbs );
                ?>
            </div>
            <?php if ( $sd_extras['format'] || $sd_extras['question'] ) : ?>
            <div class="flex flex-wrap justify-center items-center gap-2 mb-6">
                <?php if ( $sd_extras['format'] ) : ?>
                <span class="text-[11px] font-semibold px-3 py-1 rounded-full border border-white/10 text-gray-300"><?php echo esc_html( $sd_extras['format'] ); ?></span>
                <?php endif; ?>
                <?php if ( $sd_extras['question'] ) : ?>
                <span class="text-xs text-gray-400"><?php esc_html_e( 'Answers:', 'serhandemirel' ); ?> <span class="text-gray-200"><?php echo esc_html( $sd_extras['question'] ); ?></span></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter leading-[1.02] text-white [text-wrap:balance]"><?php the_title(); ?></h1>
            <?php if ( has_excerpt() ) : ?>
            <p class="mt-6 text-lg md:text-xl text-gray-300 leading-relaxed [text-wrap:pretty]"><?php echo esc_html( get_the_excerpt() ); ?></p>
            <?php endif; ?>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-sm text-gray-400">
                <span class="flex items-center gap-2">
                    <?php if ( $sd_author['photo'] ) : ?>
                    <img src="<?php echo esc_url( $sd_author['photo'] ); ?>" alt="" width="28" height="28" class="w-7 h-7 rounded-full object-cover">
                    <?php endif; ?>
                    <?php if ( $sd_about ) : ?>
                    <a href="<?php echo esc_url( $sd_about ); ?>" rel="author" class="text-gray-200 hover:text-white transition-colors"><?php echo esc_html( $sd_author['name'] ); ?></a>
                    <?php else : ?>
                    <span class="text-gray-200"><?php echo esc_html( $sd_author['name'] ); ?></span>
                    <?php endif; ?>
                </span>
                <span><?php echo esc_html( sprintf( /* translators: %d: minutes */ _n( '%d min read', '%d min read', sd_read_time( $sd_id ), 'serhandemirel' ), sd_read_time( $sd_id ) ) ); ?></span>
            </div>
            <div class="mt-3"><?php get_template_part( 'template-parts/dates', null, array( 'dates' => sdc_dates( $sd_id ), 'center' => true ) ); ?></div>
        </header>

        <?php if ( has_post_thumbnail() ) : ?>
        <div class="max-w-5xl mx-auto px-4 md:px-6 mt-12">
            <div class="aspect-[16/9] md:aspect-[21/9] rounded-[2rem] overflow-hidden border border-white/10">
                <?php echo sd_cover_image( $sd_id, 'w-full h-full object-cover', '(min-width: 1024px) 1024px, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image markup. ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="max-w-6xl mx-auto px-6 mt-12 md:mt-14 grid lg:grid-cols-[200px_minmax(0,1fr)_200px] gap-10">
            <aside class="hidden lg:block">
                <?php if ( count( $sd_toc ) > 1 ) : ?>
                <nav class="sticky top-32 text-sm" aria-label="<?php esc_attr_e( 'On this page', 'serhandemirel' ); ?>">
                    <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-gray-500 mb-3"><?php esc_html_e( 'On this page', 'serhandemirel' ); ?></p>
                    <?php foreach ( $sd_toc as $sd_item ) : ?>
                    <a href="#<?php echo esc_attr( $sd_item['id'] ); ?>" data-toc="<?php echo esc_attr( $sd_item['id'] ); ?>" class="block py-1.5 pl-4 -ml-px border-l border-white/10 text-gray-500 hover:text-white transition-colors"><?php echo esc_html( $sd_item['title'] ); ?></a>
                    <?php endforeach; ?>
                </nav>
                <?php endif; ?>
            </aside>

            <div class="min-w-0 w-full max-w-[68ch] mx-auto">
                <?php if ( count( $sd_toc ) > 1 ) : ?>
                <details class="lg:hidden glass rounded-2xl px-5 py-4 mb-8" data-toc-mobile>
                    <summary class="flex justify-between items-center gap-4 font-semibold text-white cursor-pointer"><?php esc_html_e( 'On this page', 'serhandemirel' ); ?><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></summary>
                    <ol class="mt-3 space-y-2 text-sm">
                        <?php foreach ( $sd_toc as $sd_item ) : ?>
                        <li><a href="#<?php echo esc_attr( $sd_item['id'] ); ?>" class="text-gray-400 hover:text-white"><?php echo esc_html( $sd_item['title'] ); ?></a></li>
                        <?php endforeach; ?>
                    </ol>
                </details>
                <?php endif; ?>

                <?php if ( $sd_extras['takeaways'] ) : ?>
                <section id="key-takeaways" class="glass rounded-3xl p-6 md:p-7 mb-10 scroll-mt-28" aria-labelledby="key-takeaways-title">
                    <h2 id="key-takeaways-title" class="text-xs font-semibold tracking-[0.2em] uppercase text-pink-500 mb-4"><?php esc_html_e( 'Key takeaways', 'serhandemirel' ); ?></h2>
                    <ul class="space-y-2.5">
                        <?php foreach ( $sd_extras['takeaways'] as $sd_point ) : ?>
                        <li class="flex gap-3 text-gray-200 leading-relaxed"><svg class="w-5 h-5 mt-0.5 shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span><?php echo esc_html( $sd_point ); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php endif; ?>

                <?php if ( $sd_extras['experience'] ) : ?>
                    <?php $sd_exp = $sd_extras['experience']; ?>
                    <?php if ( $sd_exp['project'] ) : ?>
                <a href="<?php echo esc_url( $sd_exp['project']['permalink'] ); ?>" class="group flex gap-4 items-center rounded-3xl border border-white/10 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-transparent p-3 pr-5 mb-10 hover:border-white/20 transition-colors">
                    <span class="relative w-20 h-20 md:w-24 shrink-0 rounded-2xl overflow-hidden" style="<?php echo $sd_exp['project']['image'] ? '' : esc_attr( sd_placeholder_style( 1 ) ); ?>">
                        <?php if ( $sd_exp['project']['image'] ) : ?>
                        <img src="<?php echo esc_url( $sd_exp['project']['image'] ); ?>" alt="" loading="lazy" class="absolute inset-0 w-full h-full object-cover">
                        <?php endif; ?>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-[11px] font-semibold tracking-[0.2em] uppercase text-blue-400"><?php esc_html_e( 'From my work', 'serhandemirel' ); ?></span>
                        <span class="block mt-1 text-[15px] leading-snug text-gray-200"><?php echo esc_html( $sd_exp['note'] ? $sd_exp['note'] : $sd_exp['project']['title'] ); ?> <span class="text-white whitespace-nowrap"><?php esc_html_e( 'Read the case study', 'serhandemirel' ); ?> <span class="inline-block transition-transform group-hover:translate-x-1" aria-hidden="true">→</span></span></span>
                    </span>
                </a>
                    <?php else : ?>
                <div class="rounded-3xl border border-white/10 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-transparent p-5 mb-10">
                    <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-blue-400"><?php esc_html_e( 'From my work', 'serhandemirel' ); ?></p>
                    <p class="mt-1 text-[15px] leading-snug text-gray-200"><?php echo esc_html( $sd_exp['note'] ); ?></p>
                </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="sd-prose">
                    <?php echo $sd_outline['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content. ?>
                </div>

                <?php if ( $sd_extras['sources'] ) : ?>
                <h2 id="sources" class="text-2xl md:text-3xl font-bold tracking-tight text-white mt-16 mb-5 scroll-mt-28"><?php esc_html_e( 'Sources', 'serhandemirel' ); ?></h2>
                <?php get_template_part( 'template-parts/sources', null, array( 'sources' => $sd_extras['sources'] ) ); ?>
                <?php endif; ?>

                <?php if ( $sd_extras['faq'] ) : ?>
                <section id="faq" class="mt-16 scroll-mt-28">
                    <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-white mb-6"><?php esc_html_e( 'FAQ', 'serhandemirel' ); ?></h2>
                    <?php get_template_part( 'template-parts/faq-list', null, array( 'items' => $sd_extras['faq'] ) ); ?>
                </section>
                <?php endif; ?>

                <?php the_tags( '<p class="mt-12 flex flex-wrap gap-2 text-sm [&>a]:px-3 [&>a]:py-1.5 [&>a]:rounded-full [&>a]:border [&>a]:border-white/10 [&>a]:text-gray-300 hover:[&>a]:bg-white/10">', '', '</p>' ); ?>

                <?php get_template_part( 'template-parts/author-box' ); ?>

                <div class="lg:hidden mt-8 flex flex-wrap gap-2 text-sm">
                    <button type="button" data-copy-link class="px-4 py-2 rounded-full border border-white/10 text-gray-200 hover:bg-white/10"><?php esc_html_e( 'Copy link', 'serhandemirel' ); ?></button>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo esc_attr( $sd_share ); ?>" target="_blank" rel="noopener" class="px-4 py-2 rounded-full border border-white/10 text-gray-200 hover:bg-white/10">LinkedIn</a>
                    <a href="https://x.com/intent/post?url=<?php echo esc_attr( $sd_share ); ?>" target="_blank" rel="noopener" class="px-4 py-2 rounded-full border border-white/10 text-gray-200 hover:bg-white/10">X</a>
                </div>
            </div>

            <aside class="hidden lg:block">
                <div class="sticky top-32 space-y-3 text-sm">
                    <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-gray-500"><?php esc_html_e( 'Share', 'serhandemirel' ); ?></p>
                    <button type="button" data-copy-link class="w-full text-left px-4 py-2.5 rounded-full border border-white/10 text-gray-200 hover:bg-white/10 transition-colors"><?php esc_html_e( 'Copy link', 'serhandemirel' ); ?></button>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo esc_attr( $sd_share ); ?>" target="_blank" rel="noopener" class="block px-4 py-2.5 rounded-full border border-white/10 text-gray-200 hover:bg-white/10 transition-colors">LinkedIn</a>
                    <a href="https://x.com/intent/post?url=<?php echo esc_attr( $sd_share ); ?>" target="_blank" rel="noopener" class="block px-4 py-2.5 rounded-full border border-white/10 text-gray-200 hover:bg-white/10 transition-colors">X</a>
                    <?php if ( $sd_extras['service'] ) : ?>
                    <a href="<?php echo esc_url( $sd_extras['service']['url'] ); ?>" class="group block mt-6 rounded-2xl glass p-4 hover:bg-white/[0.06] transition-colors">
                        <span class="block text-[11px] font-semibold tracking-[0.2em] uppercase text-blue-400 mb-2"><?php esc_html_e( 'Related service', 'serhandemirel' ); ?></span>
                        <span class="block font-semibold text-white"><?php echo esc_html( $sd_extras['service']['title'] ); ?></span>
                        <span class="text-gray-400 group-hover:text-white text-xs"><?php esc_html_e( 'See the service', 'serhandemirel' ); ?> →</span>
                    </a>
                    <?php endif; ?>
                    <?php if ( $sd_extras['terms'] ) : ?>
                    <div class="rounded-2xl glass p-4">
                        <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-pink-500 mb-2"><?php esc_html_e( 'In the glossary', 'serhandemirel' ); ?></p>
                        <?php foreach ( $sd_extras['terms'] as $sd_term ) : ?>
                        <a href="<?php echo esc_url( $sd_term['url'] ); ?>" class="block font-semibold text-gray-200 hover:text-white py-0.5"><?php echo esc_html( $sd_term['title'] ); ?></a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </article>

    <?php if ( $sd_extras['service'] ) : ?>
    <div class="lg:hidden relative z-10 max-w-[68ch] mx-auto px-6 mt-8">
        <a href="<?php echo esc_url( $sd_extras['service']['url'] ); ?>" class="block rounded-2xl glass p-5">
            <span class="block text-[11px] font-semibold tracking-[0.2em] uppercase text-blue-400 mb-2"><?php esc_html_e( 'Related service', 'serhandemirel' ); ?></span>
            <span class="font-semibold text-white"><?php echo esc_html( $sd_extras['service']['title'] ); ?> →</span>
        </a>
    </div>
    <?php endif; ?>

    <?php $sd_related = sd_related_posts( $sd_id, 2 ); ?>
    <?php if ( $sd_related ) : ?>
    <section class="relative z-10 max-w-6xl mx-auto px-6 mt-24">
        <div class="flex items-end justify-between gap-4 mb-6">
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-white"><?php esc_html_e( 'Keep reading', 'serhandemirel' ); ?></h2>
            <?php if ( $sd_blog_url ) : ?>
            <a href="<?php echo esc_url( $sd_blog_url ); ?>" class="text-sm text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'All insights', 'serhandemirel' ); ?> →</a>
            <?php endif; ?>
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
            <?php foreach ( $sd_related as $sd_i => $sd_rel ) : ?>
            <a href="<?php echo esc_url( get_permalink( $sd_rel ) ); ?>" class="group flex gap-4 items-center rounded-3xl glass p-2 pr-5 hover:bg-white/[0.06] transition-colors">
                <div class="relative w-28 h-24 shrink-0 rounded-2xl overflow-hidden" style="<?php echo has_post_thumbnail( $sd_rel ) ? '' : esc_attr( sd_placeholder_style( $sd_i + 1 ) ); ?>">
                    <?php if ( has_post_thumbnail( $sd_rel ) ) : ?>
                        <?php echo get_the_post_thumbnail( $sd_rel, 'medium', array( 'class' => 'absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105', 'loading' => 'lazy' ) ); ?>
                    <?php endif; ?>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 mb-1"><?php echo esc_html( sd_insight_meta( $sd_rel->ID ) ); ?></p>
                    <p class="font-semibold leading-snug text-white"><?php echo esc_html( get_the_title( $sd_rel ) ); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</main>
	<?php
endwhile;

get_template_part( 'template-parts/contact' );
get_footer();
