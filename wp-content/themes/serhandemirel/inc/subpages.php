<?php
/**
 * Helpers for the inner pages: article outline, related posts, author box,
 * search grouping and the small script that runs on every page.
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Give every level-2 heading in an article an id and list them for the
 * "On this page" outline.
 *
 * @param string $html Rendered article content.
 * @return array{html: string, toc: array<int, array{id: string, title: string}>}
 */
function sd_article_outline( $html ) {
	$toc  = array();
	$used = array();
	$html = preg_replace_callback(
		'/<h2(\s[^>]*)?>(.*?)<\/h2>/is',
		function ( $match ) use ( &$toc, &$used ) {
			$attrs = $match[1] ?? '';
			$title = trim( wp_strip_all_tags( $match[2] ) );
			if ( '' === $title ) {
				return $match[0];
			}
			if ( preg_match( '/\sid=["\']([^"\']+)["\']/', $attrs, $id_match ) ) {
				$id = $id_match[1];
			} else {
				$id = sanitize_title( $title );
				$id = '' === $id ? 'section' : $id;
				$base = $id;
				$n    = 2;
				while ( isset( $used[ $id ] ) ) {
					$id = $base . '-' . $n++;
				}
				$attrs .= ' id="' . esc_attr( $id ) . '"';
			}
			$used[ $id ] = true;
			$toc[]       = array(
				'id'    => $id,
				'title' => $title,
			);
			return '<h2' . $attrs . '>' . $match[2] . '</h2>';
		},
		$html
	);
	return array(
		'html' => $html,
		'toc'  => $toc,
	);
}

/**
 * Posts to read next: same category first, then the newest others.
 *
 * @param int $post_id Current post.
 * @param int $count   How many.
 * @return WP_Post[]
 */
function sd_related_posts( $post_id, $count = 2 ) {
	$categories = wp_get_post_categories( $post_id );
	$posts      = $categories ? get_posts(
		array(
			'posts_per_page'      => $count,
			'category__in'        => $categories,
			'post__not_in'        => array( $post_id ),
			'ignore_sticky_posts' => true,
		)
	) : array();
	if ( count( $posts ) < $count ) {
		$posts = array_merge(
			$posts,
			get_posts(
				array(
					'posts_per_page'      => $count - count( $posts ),
					'post__not_in'        => array_merge( array( $post_id ), wp_list_pluck( $posts, 'ID' ) ),
					'ignore_sticky_posts' => true,
				)
			)
		);
	}
	return $posts;
}

/**
 * Link to the About page (the page using the Profile template), in the
 * current language. Empty when there is none.
 *
 * @return string
 */
function sd_about_url() {
	static $url = null;
	if ( null !== $url ) {
		return $url;
	}
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one lookup per request.
			'meta_value'     => 'template-profile.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- one lookup per request.
		)
	);
	$url   = $pages ? sdc_translated_permalink( $pages[0] ) : '';
	return $url;
}

/**
 * Author details for article bylines and the author box, from the Profile screen.
 *
 * @return array{name: string, title: string, bio: string, photo: string, place: string}
 */
function sd_author() {
	$profile = function_exists( 'sdc_get_profile' ) ? sdc_get_profile() : array();
	return array(
		'name'  => (string) ( $profile['full_name'] ?? get_bloginfo( 'name' ) ),
		'title' => (string) ( $profile['job_title'] ?? '' ),
		'bio'   => (string) ( $profile['short_bio'] ?? '' ),
		'photo' => (string) ( $profile['headshot_url'] ?? '' ),
		'place' => (string) ( $profile['city'] ?? '' ),
	);
}

/**
 * The posts page URL, or empty when the site has none.
 *
 * @return string
 */
function sd_insights_url() {
	$page = (int) get_option( 'page_for_posts' );
	return $page ? sdc_translated_permalink( $page ) : '';
}

/**
 * Categories that have posts, for the Insights filter row.
 *
 * @return WP_Term[]
 */
function sd_insight_categories() {
	$terms = get_categories(
		array(
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
	return array_values(
		array_filter(
			$terms,
			function ( $term ) {
				return 'uncategorized' !== $term->slug;
			}
		)
	);
}

/**
 * Search results grouped by type, in the order they are shown.
 *
 * @return array<string, array{label: string, posts: WP_Post[]}>
 */
function sd_search_groups() {
	global $wp_query;
	$groups = array(
		'sd_project'      => array( 'label' => __( 'Projects', 'serhandemirel' ), 'posts' => array() ),
		'sd_service_page' => array( 'label' => __( 'Services', 'serhandemirel' ), 'posts' => array() ),
		'post'            => array( 'label' => __( 'Insights', 'serhandemirel' ), 'posts' => array() ),
		'page'            => array( 'label' => __( 'Pages', 'serhandemirel' ), 'posts' => array() ),
	);
	foreach ( $wp_query->posts as $post ) {
		if ( isset( $groups[ $post->post_type ] ) ) {
			$groups[ $post->post_type ]['posts'][] = $post;
		}
	}
	return array_filter(
		$groups,
		function ( $group ) {
			return (bool) $group['posts'];
		}
	);
}

/**
 * Search covers projects, services, posts and pages, 30 results a page.
 *
 * @param WP_Query $query Main query.
 */
function sd_search_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}
	if ( ! $query->get( 'post_type' ) ) {
		$query->set( 'post_type', array( 'sd_project', 'sd_service_page', 'post', 'page' ) );
	}
	$query->set( 'posts_per_page', 30 );
}
add_action( 'pre_get_posts', 'sd_search_query' );

/**
 * Prefetch a page as soon as the visitor shows intent (hover or touch),
 * so inner pages open instantly. Uses WordPress 6.8+ speculative loading.
 *
 * @return array
 */
function sd_speculation_rules() {
	return array(
		'mode'      => 'prefetch',
		'eagerness' => 'moderate',
	);
}
add_filter( 'wp_speculation_rules_configuration', 'sd_speculation_rules' );

/**
 * The first image a page shows is its largest paint: load it first.
 *
 * @param int    $post_id Post.
 * @param string $class   Classes for the image.
 * @param string $sizes   The sizes attribute.
 * @return string
 */
function sd_cover_image( $post_id, $class, $sizes = '100vw' ) {
	return get_the_post_thumbnail(
		$post_id,
		'full',
		array(
			'class'         => $class,
			'loading'       => 'eager',
			'fetchpriority' => 'high',
			'decoding'      => 'async',
			'sizes'         => $sizes,
		)
	);
}

/**
 * Search form in the site's style.
 *
 * @param string $id          Input id.
 * @param string $placeholder Placeholder text.
 * @param string $post_type   Limit to one post type, or empty for everything.
 */
function sd_search_form( $id, $placeholder, $post_type = '' ) {
	?>
	<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative w-full">
		<label for="<?php echo esc_attr( $id ); ?>" class="sr-only"><?php esc_html_e( 'Search', 'serhandemirel' ); ?></label>
		<svg class="w-4 h-4 absolute left-5 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="2"></circle><path d="m20 20-3.5-3.5" stroke-width="2" stroke-linecap="round"></path></svg>
		<input id="<?php echo esc_attr( $id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" class="w-full rounded-full bg-white/5 border border-white/10 pl-12 pr-28 py-3.5 text-base text-white placeholder:text-gray-500 focus:outline-none focus:border-purple-500/60 focus:bg-white/[0.07] transition-colors">
		<?php if ( $post_type ) : ?>
		<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>">
		<?php endif; ?>
		<button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 px-5 rounded-full bg-white text-black text-sm font-semibold hover:bg-gray-200 transition-colors"><?php esc_html_e( 'Search', 'serhandemirel' ); ?></button>
	</form>
	<?php
}

/**
 * Breadcrumb trail above a page title.
 *
 * @param array<int, array{0: string, 1: string}> $items Label and URL pairs; the last one has no link.
 */
function sd_breadcrumb( $items ) {
	echo '<nav class="text-xs text-gray-500 mb-8" aria-label="' . esc_attr__( 'Breadcrumb', 'serhandemirel' ) . '"><ol class="flex flex-wrap items-center gap-1.5">';
	$last = count( $items ) - 1;
	foreach ( $items as $i => $item ) {
		echo '<li class="flex items-center gap-1.5">';
		if ( $i < $last && $item[1] ) {
			echo '<a href="' . esc_url( $item[1] ) . '" class="hover:text-white transition-colors">' . esc_html( $item[0] ) . '</a><span aria-hidden="true">/</span>';
		} else {
			echo '<span class="text-gray-400"' . ( $i === $last ? ' aria-current="page"' : '' ) . '>' . esc_html( $item[0] ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/**
 * Background colours for project and post images that are missing, so a
 * card without a picture still looks deliberate.
 *
 * @param int $index Position in a list.
 * @return string Inline style.
 */
function sd_placeholder_style( $index ) {
	$pairs = array( array( '#4f46e5', '#ec4899' ), array( '#0ea5e9', '#6366f1' ), array( '#f97316', '#be185d' ), array( '#10b981', '#3b82f6' ), array( '#a855f7', '#f43f5e' ) );
	$pair  = $pairs[ $index % count( $pairs ) ];
	return sprintf( 'background:radial-gradient(circle at 30%% 30%%,%1$s88,transparent 60%%),radial-gradient(circle at 80%% 70%%,%2$s77,transparent 55%%),#0b0b10', $pair[0], $pair[1] );
}
