<?php
/**
 * Insights: the posts page and post archives (category, tag, date, author).
 *
 * The first page of the posts page opens with the newest post as a wide
 * card; category pills link to the category archives; the search box
 * filters the cards on the page while typing and searches all posts on
 * Enter; "Load more" appends the next page without a reload.
 *
 * @package serhandemirel
 */

get_header();

global $wp_query;
$sd_is_blog   = is_home();
$sd_paged     = max( 1, (int) get_query_var( 'paged' ) );
$sd_lead      = ( $sd_is_blog && 1 === $sd_paged && have_posts() ) ? $wp_query->posts[0] : null;
$sd_term      = single_term_title( '', false );
$sd_heading   = $sd_is_blog ? sd_opt( 'insights_title' ) : wp_strip_all_tags( $sd_term ? $sd_term : get_the_archive_title() );
$sd_intro     = $sd_is_blog ? sd_opt( 'insights_intro' ) : wp_strip_all_tags( get_the_archive_description() );
$sd_blog_url  = sd_insights_url();
$sd_current   = is_category() ? get_queried_object_id() : 0;
$sd_has_next  = $sd_paged < (int) $wp_query->max_num_pages;
$sd_next_page = $sd_has_next ? get_pagenum_link( $sd_paged + 1 ) : '';
?>
<main id="main" class="relative overflow-hidden">
    <div class="sd-orb w-[50rem] h-[50rem] -top-96 -right-60 bg-fuchsia-900/25"></div>
    <div class="sd-orb w-[40rem] h-[40rem] top-40 -left-80 bg-blue-900/20"></div>

    <header class="relative z-10 max-w-6xl mx-auto px-6 pt-36 md:pt-44 pb-10">
        <?php if ( ! $sd_is_blog && $sd_blog_url ) : ?>
            <?php sd_breadcrumb( array( array( sd_opt( 'insights_eyebrow' ), $sd_blog_url ), array( $sd_heading, '' ) ) ); ?>
        <?php else : ?>
        <p class="text-sm font-bold tracking-[0.2em] uppercase text-pink-500 mb-4"><?php echo esc_html( sd_opt( 'insights_eyebrow' ) ); ?></p>
        <?php endif; ?>
        <h1 class="text-5xl md:text-7xl font-black tracking-tighter leading-[0.95] max-w-3xl [text-wrap:balance]"><?php echo esc_html( $sd_heading ); ?></h1>
        <?php if ( $sd_intro ) : ?>
        <p class="mt-6 text-lg text-gray-400 max-w-xl"><?php echo esc_html( $sd_intro ); ?></p>
        <?php endif; ?>
    </header>

    <?php if ( have_posts() ) : ?>

        <?php if ( $sd_lead ) : ?>
        <section class="relative z-10 max-w-6xl mx-auto px-6" aria-label="<?php esc_attr_e( 'Latest', 'serhandemirel' ); ?>">
            <?php
            $sd_lead_cats = get_the_category( $sd_lead->ID );
            $sd_author    = sd_author();
            ?>
            <a href="<?php echo esc_url( get_permalink( $sd_lead ) ); ?>" class="group sd-rise grid md:grid-cols-[1.25fr_1fr] gap-2 rounded-[2rem] glass p-2 hover:bg-white/[0.06] transition-colors duration-300">
                <div class="relative aspect-[16/10] md:aspect-auto md:min-h-[360px] rounded-[1.6rem] overflow-hidden" style="<?php echo has_post_thumbnail( $sd_lead ) ? '' : esc_attr( sd_placeholder_style( 0 ) ); ?>">
                    <?php if ( has_post_thumbnail( $sd_lead ) ) : ?>
                        <?php echo sd_cover_image( $sd_lead->ID, 'absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105', '(min-width: 1024px) 640px, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image markup. ?>
                    <?php endif; ?>
                    <span class="absolute top-4 left-4 text-[11px] font-semibold px-3 py-1 rounded-full bg-white text-black"><?php esc_html_e( 'Latest', 'serhandemirel' ); ?></span>
                </div>
                <div class="p-5 md:p-8 flex flex-col justify-end">
                    <?php if ( $sd_lead_cats && 'uncategorized' !== $sd_lead_cats[0]->slug ) : ?>
                    <p class="text-xs font-semibold tracking-[0.15em] uppercase text-blue-400 mb-3"><?php echo esc_html( $sd_lead_cats[0]->name ); ?></p>
                    <?php endif; ?>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight leading-tight text-white [text-wrap:balance]"><?php echo esc_html( get_the_title( $sd_lead ) ); ?></h2>
                    <?php if ( has_excerpt( $sd_lead ) ) : ?>
                    <p class="mt-4 text-gray-400 leading-relaxed"><?php echo esc_html( get_the_excerpt( $sd_lead ) ); ?></p>
                    <?php endif; ?>
                    <div class="mt-6 flex items-center gap-3 text-sm text-gray-400">
                        <?php if ( $sd_author['photo'] ) : ?>
                        <img src="<?php echo esc_url( $sd_author['photo'] ); ?>" alt="" width="32" height="32" class="w-8 h-8 rounded-full object-cover" loading="lazy">
                        <?php endif; ?>
                        <span><?php echo esc_html( $sd_author['name'] ); ?> · <?php echo esc_html( get_the_date( '', $sd_lead ) ); ?> · <?php echo esc_html( sprintf( /* translators: %d: minutes */ _n( '%d min read', '%d min read', sd_read_time( $sd_lead->ID ), 'serhandemirel' ), sd_read_time( $sd_lead->ID ) ) ); ?></span>
                    </div>
                </div>
            </a>
        </section>
        <?php endif; ?>

        <section class="relative z-10 max-w-6xl mx-auto px-6 mt-14">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <?php $sd_categories = sd_insight_categories(); ?>
                <?php if ( $sd_blog_url && count( $sd_categories ) > 1 ) : ?>
                <nav aria-label="<?php esc_attr_e( 'Categories', 'serhandemirel' ); ?>" class="flex gap-2 overflow-x-auto no-scrollbar -mx-6 px-6 md:mx-0 md:px-0 pb-1">
                    <a href="<?php echo esc_url( $sd_blog_url ); ?>" class="shrink-0 px-4 py-2 rounded-full text-sm border transition-colors <?php echo $sd_current ? 'border-white/10 text-gray-300 hover:bg-white/10' : 'bg-white text-black border-white font-semibold'; ?>"<?php echo $sd_current ? '' : ' aria-current="page"'; ?>><?php echo esc_html( sd_opt( 'work_all_label' ) ); ?></a>
                    <?php foreach ( $sd_categories as $sd_cat ) : ?>
                    <a href="<?php echo esc_url( get_category_link( $sd_cat ) ); ?>" class="shrink-0 px-4 py-2 rounded-full text-sm border transition-colors <?php echo $sd_current === $sd_cat->term_id ? 'bg-white text-black border-white font-semibold' : 'border-white/10 text-gray-300 hover:bg-white/10'; ?>"<?php echo $sd_current === $sd_cat->term_id ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $sd_cat->name ); ?></a>
                    <?php endforeach; ?>
                </nav>
                <?php else : ?>
                <span></span>
                <?php endif; ?>
                <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative shrink-0 md:w-72">
                    <label for="sd-insights-search" class="sr-only"><?php esc_html_e( 'Search insights', 'serhandemirel' ); ?></label>
                    <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="2"></circle><path d="m20 20-3.5-3.5" stroke-width="2" stroke-linecap="round"></path></svg>
                    <input id="sd-insights-search" data-insights-filter type="search" name="s" placeholder="<?php esc_attr_e( 'Search insights', 'serhandemirel' ); ?>" class="w-full rounded-full bg-white/5 border border-white/10 pl-10 pr-4 py-2.5 text-sm text-white placeholder:text-gray-500 focus:outline-none focus:border-purple-500/60 transition-colors">
                    <input type="hidden" name="post_type" value="post">
                </form>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5" data-insights-grid>
                <?php
                $sd_index = 0;
                while ( have_posts() ) {
                    the_post();
                    if ( $sd_lead && get_the_ID() === $sd_lead->ID ) {
                        continue;
                    }
                    get_template_part( 'template-parts/insight-tile', null, array( 'post' => get_post(), 'index' => $sd_index++ ) );
                }
                ?>
                <?php if ( sd_opt( 'newsletter_url' ) && 1 === $sd_paged ) : ?>
                <aside class="rounded-3xl border border-dashed border-white/10 p-6 flex flex-col justify-between min-h-[260px]">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.15em] uppercase text-pink-500 mb-3"><?php esc_html_e( 'Newsletter', 'serhandemirel' ); ?></p>
                        <h2 class="text-2xl font-bold tracking-tight text-white [text-wrap:balance]"><?php echo esc_html( sd_opt( 'newsletter_title' ) ); ?></h2>
                    </div>
                    <a href="<?php echo esc_url( sd_opt( 'newsletter_url' ) ); ?>" target="_blank" rel="noopener" class="mt-6 self-start px-5 py-2.5 rounded-full bg-white text-black text-sm font-semibold hover:bg-gray-200 transition-colors"><?php esc_html_e( 'Subscribe', 'serhandemirel' ); ?> ↗</a>
                </aside>
                <?php endif; ?>
                <span data-insights-tail hidden></span>
            </div>
            <p data-insights-empty hidden class="mt-8 text-gray-400"><?php esc_html_e( 'No post on this page matches. Press Enter to search all posts.', 'serhandemirel' ); ?></p>

            <?php if ( $sd_has_next ) : ?>
            <div class="mt-12 flex justify-center">
                <a href="<?php echo esc_url( $sd_next_page ); ?>" data-load-more class="px-6 py-3 rounded-full border border-white/10 text-sm text-gray-300 hover:bg-white/10 hover:text-white transition-colors"><?php esc_html_e( 'Load more', 'serhandemirel' ); ?></a>
            </div>
            <?php endif; ?>
        </section>

    <?php else : ?>
        <section class="relative z-10 max-w-6xl mx-auto px-6">
            <p class="text-gray-400"><?php esc_html_e( 'No posts yet.', 'serhandemirel' ); ?></p>
        </section>
    <?php endif; ?>
</main>
<?php
get_template_part( 'template-parts/contact' );
get_footer();
