<?php
/**
 * Trust and answer helpers: publish and review dates, sources,
 * testimonials, talks and press, glossary terms, industry pages and the
 * FAQ hub. Each returns plain arrays; escape text on output.
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a stored post ID points at a post or one of its translations.
 * Shared "post" fields keep the ID chosen in the first language.
 *
 * @param int $stored_id Stored ID.
 * @param int $post_id   Post to compare with.
 * @return bool
 */
function sdc_same_post( $stored_id, $post_id ) {
	$stored_id = (int) $stored_id;
	if ( ! $stored_id || ! $post_id ) {
		return false;
	}
	if ( $stored_id === (int) $post_id ) {
		return true;
	}
	if ( function_exists( 'pll_get_post_translations' ) ) {
		return in_array( $stored_id, array_map( 'intval', (array) pll_get_post_translations( $post_id ) ), true );
	}
	return false;
}

/**
 * A post ID in the current language when a translation exists.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function sdc_current_lang_id( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id && function_exists( 'pll_get_post' ) ) {
		$translation = pll_get_post( $post_id );
		if ( $translation ) {
			return (int) $translation;
		}
	}
	return $post_id;
}

/**
 * Comma-separated IDs of a "posts" field.
 *
 * @param int    $post_id Post ID.
 * @param string $field   Field name.
 * @return int[]
 */
function sdc_get_ids( $post_id, $field ) {
	return array_values( array_filter( array_map( 'absint', explode( ',', (string) sdc_get( $post_id, $field ) ) ) ) );
}

/**
 * A date as the site writes dates.
 *
 * @param string $ymd Date as Y-m-d.
 * @return string
 */
function sdc_date_label( $ymd ) {
	return $ymd ? date_i18n( get_option( 'date_format' ), strtotime( $ymd ) ) : '';
}

/**
 * Published and reviewed dates of a post. "Updated" only counts when the
 * "Last reviewed" field is later than the publish date: editing a typo
 * does not make a page look new.
 *
 * @param int $post_id Post ID.
 * @return array{published: string, updated: string, note: string}
 */
function sdc_dates( $post_id ) {
	$published = (string) get_the_date( 'Y-m-d', $post_id );
	$reviewed  = (string) sdc_get( $post_id, 'reviewed_date' );
	$updated   = ( $reviewed && strtotime( $reviewed ) > strtotime( $published ) ) ? $reviewed : '';
	return array(
		'published' => $published,
		'updated'   => $updated,
		'note'      => $updated ? trim( (string) sdc_get( $post_id, 'update_note' ) ) : '',
	);
}

/**
 * Whether a post was reviewed in the last 90 days, for "Updated" badges.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function sdc_recently_updated( $post_id ) {
	$dates = sdc_dates( $post_id );
	return $dates['updated'] && strtotime( $dates['updated'] ) >= strtotime( '-90 days' );
}

/**
 * Sources from a field: one "Title | URL | publisher and year" per line.
 * A line with only a URL uses the site name as its title.
 *
 * @param string $text Field value.
 * @return array<int, array{title: string, url: string, publisher: string}>
 */
function sdc_parse_sources( $text ) {
	$sources = array();
	foreach ( sdc_lines( $text ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line ) );
		if ( 1 === count( $parts ) && preg_match( '#^https?://#i', $parts[0] ) ) {
			$parts = array( (string) wp_parse_url( $parts[0], PHP_URL_HOST ), $parts[0] );
		}
		$url = isset( $parts[1] ) ? esc_url_raw( $parts[1] ) : '';
		if ( '' === $parts[0] ) {
			continue;
		}
		$sources[] = array(
			'title'     => $parts[0],
			'url'       => $url,
			'publisher' => $parts[2] ?? '',
		);
	}
	return $sources;
}

/**
 * Sources of a post.
 *
 * @param int $post_id Post ID.
 * @return array
 */
function sdc_sources( $post_id ) {
	return sdc_parse_sources( sdc_get( $post_id, 'sources' ) );
}

/**
 * Whether a post asks search engines not to index it.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function sdc_is_noindex( $post_id ) {
	return (bool) sdc_get( $post_id, 'noindex' );
}

/* -------------------------------------------------------------------------
 * Testimonials
 * ---------------------------------------------------------------------- */

/**
 * One testimonial.
 *
 * @param WP_Post|int $post Testimonial.
 * @return array
 */
function sdc_testimonial_data( $post ) {
	$post    = get_post( $post );
	$brand   = (int) sdc_get( $post->ID, 'company' );
	$company = (string) sdc_get( $post->ID, 'company_name' );
	if ( ! $company && $brand ) {
		$company = get_the_title( $brand );
	}
	$project = (int) sdc_get( $post->ID, 'project' );
	return array(
		'id'          => $post->ID,
		'quote'       => (string) sdc_get( $post->ID, 'quote' ),
		'person'      => (string) sdc_get( $post->ID, 'person' ),
		'role'        => (string) sdc_get( $post->ID, 'role' ),
		'company'     => $company,
		'brand'       => $brand,
		'photo'       => sdc_attachment_src( (int) sdc_get( $post->ID, 'photo' ), 'thumbnail' ),
		'linkedin'    => (string) sdc_get( $post->ID, 'linkedin' ),
		'project'     => $project,
		'project_url' => $project ? sdc_translated_permalink( $project ) : '',
		'services'    => sdc_get_ids( $post->ID, 'services' ),
		'date'        => (string) sdc_get( $post->ID, 'date' ),
	);
}

/**
 * Testimonials the client agreed to publish, newest first.
 *
 * @param array $args {
 *     Optional filters.
 *     @type int $project  Only quotes about this project.
 *     @type int $service  Only quotes linked to this service page.
 *     @type int $industry Only quotes about projects or companies in this industry (term ID).
 *     @type int $limit    How many; 0 for all.
 * }
 * @return array[]
 */
function sdc_get_testimonials( $args = array() ) {
	$args  = wp_parse_args(
		$args,
		array(
			'project'  => 0,
			'service'  => 0,
			'industry' => 0,
			'limit'    => 0,
		)
	);
	$posts = get_posts(
		array(
			'post_type'      => 'sd_testimonial',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'meta_key'       => sdc_meta_key( 'consent' ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- small table.
			'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- small table.
		)
	);
	$items = array();
	foreach ( $posts as $post ) {
		$item = sdc_testimonial_data( $post );
		if ( '' === trim( $item['quote'] ) ) {
			continue;
		}
		if ( $args['project'] && ! sdc_same_post( $item['project'], $args['project'] ) ) {
			continue;
		}
		if ( $args['service'] ) {
			$match = false;
			foreach ( $item['services'] as $service ) {
				$match = $match || sdc_same_post( $service, $args['service'] );
			}
			if ( ! $match ) {
				continue;
			}
		}
		if ( $args['industry'] ) {
			$in = ( $item['project'] && has_term( (int) $args['industry'], 'sd_industry', sdc_current_lang_id( $item['project'] ) ) )
				|| ( $item['brand'] && has_term( (int) $args['industry'], 'sd_industry', $item['brand'] ) );
			if ( ! $in ) {
				continue;
			}
		}
		$items[] = $item;
	}
	return $args['limit'] ? array_slice( $items, 0, (int) $args['limit'] ) : $items;
}

/* -------------------------------------------------------------------------
 * Talks, trainings and press
 * ---------------------------------------------------------------------- */

/**
 * One talk, training or press item.
 *
 * @param WP_Post|int $post Item.
 * @return array
 */
function sdc_appearance_data( $post ) {
	$post    = get_post( $post );
	$kinds   = sdc_appearance_kinds();
	$kind    = (string) sdc_get( $post->ID, 'kind' );
	$kind    = isset( $kinds[ $kind ] ) ? $kind : 'talk';
	$host    = (int) sdc_get( $post->ID, 'host' );
	$date    = (string) sdc_get( $post->ID, 'date' );
	$date    = $date ? $date : (string) get_the_date( 'Y-m-d', $post );
	$service = sdc_current_lang_id( (int) sdc_get( $post->ID, 'related_service' ) );
	return array(
		'id'           => $post->ID,
		'title'        => $post->post_title,
		'excerpt'      => has_excerpt( $post ) ? get_the_excerpt( $post ) : '',
		'image'        => (string) get_the_post_thumbnail_url( $post, 'large' ),
		'kind'         => $kind,
		'kind_label'   => $kinds[ $kind ],
		'event'        => (string) sdc_get( $post->ID, 'event' ),
		'host'         => $host && ! sdc_get( $post->ID, 'withheld' ) ? get_the_title( $host ) : '',
		'withheld'     => (bool) sdc_get( $post->ID, 'withheld' ),
		'date'         => $date,
		'year'         => substr( $date, 0, 4 ),
		'month'        => date_i18n( 'M Y', strtotime( $date ) ),
		'location'     => (string) sdc_get( $post->ID, 'location' ),
		'language'     => (string) sdc_get( $post->ID, 'language' ),
		'participants' => (int) sdc_get( $post->ID, 'participants' ),
		'url'          => (string) sdc_get( $post->ID, 'url' ),
		'video_url'    => (string) sdc_get( $post->ID, 'video_url' ),
		'slides_url'   => (string) sdc_get( $post->ID, 'slides_url' ),
		'duration'     => (int) sdc_get( $post->ID, 'duration' ),
		'service'      => $service ? array(
			'title' => get_the_title( $service ),
			'url'   => sdc_translated_permalink( $service ),
		) : null,
	);
}

/**
 * Talks, trainings and press items, newest first.
 *
 * @param int $limit How many; 0 for all.
 * @return array[]
 */
function sdc_get_appearances( $limit = 0 ) {
	$items = array_map(
		'sdc_appearance_data',
		get_posts(
			array(
				'post_type'      => 'sd_appearance',
				'posts_per_page' => -1,
			)
		)
	);
	usort(
		$items,
		function ( $a, $b ) {
			return strcmp( $b['date'], $a['date'] );
		}
	);
	return $limit ? array_slice( $items, 0, $limit ) : $items;
}

/**
 * The "where and who" line of an item: organiser, event, place, size.
 *
 * @param array $item From sdc_appearance_data().
 * @return string
 */
function sdc_appearance_line( $item ) {
	$parts = array( $item['event'] );
	if ( $item['host'] && false === stripos( $item['event'], $item['host'] ) ) {
		$parts[] = $item['host'];
	} elseif ( $item['withheld'] && ! $item['event'] ) {
		$parts[] = __( 'Organiser not named', 'serhandemirel' );
	}
	$parts[] = $item['location'];
	if ( $item['participants'] ) {
		/* translators: %d: number of people */
		$parts[] = sprintf( _n( '%d person', '%d people', $item['participants'], 'serhandemirel' ), $item['participants'] );
	}
	return implode( ' · ', array_filter( $parts ) );
}

/**
 * Links of an item, label => URL.
 *
 * @param array $item From sdc_appearance_data().
 * @return array<string, string>
 */
function sdc_appearance_links( $item ) {
	$read  = in_array( $item['kind'], array( 'press', 'interview' ), true ) ? __( 'Read', 'serhandemirel' ) : __( 'Event page', 'serhandemirel' );
	$watch = 'podcast' === $item['kind'] ? __( 'Listen', 'serhandemirel' ) : __( 'Video', 'serhandemirel' );
	return array_filter(
		array(
			$watch                          => $item['video_url'],
			__( 'Slides', 'serhandemirel' ) => $item['slides_url'],
			$read                           => $item['url'],
		)
	);
}

/**
 * Text colour classes for each kind, as literal classes for Tailwind.
 *
 * @param string $kind Kind slug.
 * @return string
 */
function sdc_appearance_kind_class( $kind ) {
	$classes = array(
		'talk'      => 'text-blue-300 border-blue-400/30',
		'workshop'  => 'text-purple-300 border-purple-400/30',
		'training'  => 'text-emerald-300 border-emerald-400/30',
		'podcast'   => 'text-pink-300 border-pink-400/30',
		'interview' => 'text-sky-300 border-sky-400/30',
		'press'     => 'text-amber-300 border-amber-400/30',
	);
	return $classes[ $kind ] ?? $classes['talk'];
}

/* -------------------------------------------------------------------------
 * Glossary
 * ---------------------------------------------------------------------- */

/**
 * Title and link of posts by ID, current language, published only.
 *
 * @param int[] $ids Post IDs.
 * @return array<int, array{id: int, title: string, url: string}>
 */
function sdc_post_links( $ids ) {
	$links = array();
	foreach ( $ids as $id ) {
		$id  = sdc_current_lang_id( $id );
		$url = sdc_translated_permalink( $id );
		if ( $url ) {
			$links[] = array(
				'id'    => $id,
				'title' => get_the_title( $id ),
				'url'   => $url,
			);
		}
	}
	return $links;
}

/**
 * One glossary term.
 *
 * @param WP_Post|int $post Term post.
 * @return array
 */
function sdc_term_data( $post ) {
	$post    = get_post( $post );
	$service = sdc_current_lang_id( (int) sdc_get( $post->ID, 'related_service' ) );
	$project = sdc_current_lang_id( (int) sdc_get( $post->ID, 'example_project' ) );
	return array(
		'id'         => $post->ID,
		'title'      => $post->post_title,
		'url'        => get_permalink( $post ),
		'short'      => (string) sdc_get( $post->ID, 'short_definition' ),
		'aliases'    => sdc_lines( sdc_get( $post->ID, 'aliases' ) ),
		'example'    => (string) sdc_get( $post->ID, 'example' ),
		'project'    => $project && 'publish' === get_post_status( $project ) ? array(
			'title' => get_the_title( $project ),
			'url'   => get_permalink( $project ),
		) : null,
		'related'    => sdc_post_links( sdc_get_ids( $post->ID, 'related_terms' ) ),
		'service'    => $service && sdc_translated_permalink( $service ) ? array(
			'title'   => get_the_title( $service ),
			'url'     => sdc_translated_permalink( $service ),
			'excerpt' => has_excerpt( $service ) ? get_the_excerpt( $service ) : '',
		) : null,
		'faq'        => sdc_parse_faq( sdc_get( $post->ID, 'faq' ) ),
	);
}

/**
 * Glossary terms grouped by first letter, A to Z.
 *
 * @return array<string, array[]>
 */
function sdc_get_glossary() {
	$groups = array();
	$posts  = get_posts(
		array(
			'post_type'      => 'sd_term',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	foreach ( $posts as $post ) {
		$letter = strtoupper( substr( remove_accents( $post->post_title ), 0, 1 ) );
		$letter = preg_match( '/[A-Z]/', $letter ) ? $letter : '#';
		$groups[ $letter ][] = sdc_term_data( $post );
	}
	ksort( $groups );
	return $groups;
}

/**
 * Posts that link to a glossary term in their "Glossary terms" field.
 *
 * @param int $term_id Term post ID.
 * @param int $limit   How many.
 * @return WP_Post[]
 */
function sdc_posts_for_term( $term_id, $limit = 3 ) {
	$found = array();
	foreach ( get_posts( array( 'posts_per_page' => 50 ) ) as $post ) {
		foreach ( sdc_get_ids( $post->ID, 'glossary_terms' ) as $id ) {
			if ( sdc_same_post( $id, $term_id ) ) {
				$found[] = $post;
				break;
			}
		}
		if ( count( $found ) >= $limit ) {
			break;
		}
	}
	return $found;
}

/* -------------------------------------------------------------------------
 * Industry pages (sd_industry term fields)
 * ---------------------------------------------------------------------- */

/**
 * Fields of an industry page, stored as term meta "_sd_<key>".
 *
 * @return array<string, array>
 */
function sdc_industry_fields() {
	return array(
		'headline'        => array(
			'label' => __( 'Page title', 'serhandemirel' ),
			'type'  => 'text',
			'help'  => __( 'e.g. "Digital growth for healthcare". Empty uses the industry name.', 'serhandemirel' ),
		),
		'intro'           => array(
			'label' => __( 'Short answer', 'serhandemirel' ),
			'type'  => 'textarea',
			'rows'  => 4,
			'help'  => __( 'Two or three sentences under the title: what you do for this industry and why it works. Search engines and AI tools quote this paragraph.', 'serhandemirel' ),
		),
		'problems'        => array(
			'label' => __( 'Problems you solve', 'serhandemirel' ),
			'type'  => 'textarea',
			'rows'  => 5,
			'help'  => __( 'One per line, as "Problem | how you solve it | service page slug", e.g. "International leads get lost | One inbox for WhatsApp and forms | ai-automation".', 'serhandemirel' ),
		),
		'faq'             => array(
			'label' => __( 'Frequently asked questions', 'serhandemirel' ),
			'type'  => 'textarea',
			'rows'  => 8,
			'help'  => __( 'A question on one line, its answer on the next lines, an empty line between pairs.', 'serhandemirel' ),
		),
		'published_date'  => array(
			'label' => __( 'Published', 'serhandemirel' ),
			'type'  => 'date',
		),
		'reviewed_date'   => array(
			'label' => __( 'Last reviewed', 'serhandemirel' ),
			'type'  => 'date',
			'help'  => __( 'Shown as "Updated" when later than the publish date.', 'serhandemirel' ),
		),
		'update_note'     => array(
			'label' => __( 'What changed', 'serhandemirel' ),
			'type'  => 'text',
		),
		'seo_title'       => array(
			'label' => __( 'SEO title', 'serhandemirel' ),
			'type'  => 'text',
		),
		'seo_description' => array(
			'label' => __( 'SEO description', 'serhandemirel' ),
			'type'  => 'textarea',
			'rows'  => 2,
		),
	);
}

/**
 * Value of an industry field.
 *
 * @param int    $term_id Term ID.
 * @param string $name    Field name.
 * @return string
 */
function sdc_term_get( $term_id, $name ) {
	return (string) get_term_meta( $term_id, sdc_meta_key( $name ), true );
}

/**
 * Industry fields on the "Add" screen.
 */
function sdc_industry_add_fields() {
	wp_nonce_field( 'sdc_industry_fields', 'sdc_industry_nonce' );
	foreach ( sdc_industry_fields() as $key => $field ) {
		echo '<div class="form-field sdc-term-field">';
		sdc_render_field( $key, $field, '' );
		echo '</div>';
	}
}
add_action( 'sd_industry_add_form_fields', 'sdc_industry_add_fields' );

/**
 * Industry fields on the "Edit" screen.
 *
 * @param WP_Term $term Term.
 */
function sdc_industry_edit_fields( $term ) {
	wp_nonce_field( 'sdc_industry_fields', 'sdc_industry_nonce' );
	echo '<tr class="form-field"><th scope="row">' . esc_html__( 'Industry page', 'serhandemirel' ) . '</th><td><p class="description">' . esc_html__( 'These fields fill the industry page that lists your projects in this industry.', 'serhandemirel' ) . '</p></td></tr>';
	foreach ( sdc_industry_fields() as $key => $field ) {
		echo '<tr class="form-field"><th scope="row"></th><td class="sdc-term-field">';
		sdc_render_field( $key, $field, sdc_term_get( $term->term_id, $key ) );
		echo '</td></tr>';
	}
}
add_action( 'sd_industry_edit_form_fields', 'sdc_industry_edit_fields' );

/**
 * Save industry fields.
 *
 * @param int $term_id Term ID.
 */
function sdc_save_industry_fields( $term_id ) {
	if ( ! isset( $_POST['sdc_industry_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['sdc_industry_nonce'] ) ), 'sdc_industry_fields' ) || ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	$submitted = isset( $_POST['sdc'] ) && is_array( $_POST['sdc'] ) ? wp_unslash( $_POST['sdc'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
	foreach ( sdc_industry_fields() as $key => $field ) {
		if ( array_key_exists( $key, $submitted ) ) {
			update_term_meta( $term_id, sdc_meta_key( $key ), sdc_sanitize_field( $submitted[ $key ], $field ) );
		}
	}
}
add_action( 'created_sd_industry', 'sdc_save_industry_fields' );
add_action( 'edited_sd_industry', 'sdc_save_industry_fields' );

/**
 * A service page by slug or ID, in the current language.
 *
 * @param string $ref Slug or ID.
 * @return array{title: string, url: string}|null
 */
function sdc_service_ref( $ref ) {
	$ref = trim( (string) $ref );
	if ( '' === $ref ) {
		return null;
	}
	if ( ctype_digit( $ref ) ) {
		$id = (int) $ref;
	} else {
		$found = get_posts(
			array(
				'post_type'      => 'sd_service_page',
				'name'           => sanitize_title( $ref ),
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'lang'           => '',
			)
		);
		$id    = $found ? (int) $found[0] : 0;
	}
	$id  = sdc_current_lang_id( $id );
	$url = sdc_translated_permalink( $id );
	return $url ? array(
		'title' => get_the_title( $id ),
		'url'   => $url,
	) : null;
}

/**
 * Everything an industry page shows.
 *
 * @param WP_Term $term Industry.
 * @return array
 */
function sdc_industry_data( $term ) {
	$problems = array();
	foreach ( sdc_lines( sdc_term_get( $term->term_id, 'problems' ) ) as $line ) {
		$parts      = array_map( 'trim', explode( '|', $line ) );
		$problems[] = array(
			'problem' => $parts[0],
			'answer'  => $parts[1] ?? '',
			'service' => sdc_service_ref( $parts[2] ?? '' ),
		);
	}
	$published = sdc_term_get( $term->term_id, 'published_date' );
	$reviewed  = sdc_term_get( $term->term_id, 'reviewed_date' );
	$updated   = ( $reviewed && ( ! $published || strtotime( $reviewed ) > strtotime( $published ) ) ) ? $reviewed : '';
	$brands    = array();
	foreach ( get_posts(
		array(
			'post_type'      => 'sd_brand',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'tax_query'      => array( array( 'taxonomy' => 'sd_industry', 'terms' => $term->term_id ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- few brands.
		)
	) as $brand ) {
		$logo = sdc_attachment_src( (int) sdc_get( $brand->ID, 'logo_white' ) );
		if ( sdc_get( $brand->ID, 'visible' ) && $logo ) {
			$brands[] = array(
				'name' => $brand->post_title,
				'logo' => $logo,
			);
		}
	}
	return array(
		'title'     => sdc_term_get( $term->term_id, 'headline' ) ? sdc_term_get( $term->term_id, 'headline' ) : $term->name,
		'intro'     => sdc_term_get( $term->term_id, 'intro' ) ? sdc_term_get( $term->term_id, 'intro' ) : $term->description,
		'problems'  => $problems,
		'faq'       => sdc_parse_faq( sdc_term_get( $term->term_id, 'faq' ) ),
		'published' => $published,
		'updated'   => $updated,
		'note'      => $updated ? sdc_term_get( $term->term_id, 'update_note' ) : '',
		'brands'    => $brands,
	);
}

/* -------------------------------------------------------------------------
 * FAQ hub
 * ---------------------------------------------------------------------- */

/**
 * Every question on the site, grouped: general questions from the theme
 * settings, then each service page, insights, glossary terms and
 * industries. Each answer links back to the page it lives on.
 *
 * @return array<int, array{id: string, title: string, items: array[]}>
 */
function sdc_faq_hub() {
	$groups  = array();
	$general = sdc_parse_faq( sd_opt( 'faq_general' ) );
	if ( $general ) {
		$groups[] = array(
			'id'    => 'general',
			'title' => sd_opt( 'faq_general_title' ),
			'items' => $general,
		);
	}
	$add = function ( $faq, $title, $url ) {
		return array_map(
			function ( $item ) use ( $title, $url ) {
				return $item + array(
					'from'     => $title,
					'from_url' => $url,
				);
			},
			$faq
		);
	};
	foreach ( get_posts( array( 'post_type' => 'sd_service_page', 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC' ) ) ) as $post ) {
		$faq = sdc_parse_faq( sdc_get( $post->ID, 'faq' ) );
		if ( $faq ) {
			$groups[] = array(
				'id'    => 'service-' . $post->post_name,
				'title' => $post->post_title,
				'items' => $add( $faq, $post->post_title, get_permalink( $post ) ),
			);
		}
	}
	$sets = array(
		'industries' => array( __( 'Industries', 'serhandemirel' ), array() ),
		'insights'   => array( __( 'From the insights', 'serhandemirel' ), array() ),
		'glossary'   => array( __( 'Glossary', 'serhandemirel' ), array() ),
	);
	foreach ( get_terms( array( 'taxonomy' => 'sd_industry', 'hide_empty' => false ) ) as $term ) {
		$sets['industries'][1] = array_merge( $sets['industries'][1], $add( sdc_parse_faq( sdc_term_get( $term->term_id, 'faq' ) ), $term->name, get_term_link( $term ) ) );
	}
	foreach ( get_posts( array( 'posts_per_page' => 100 ) ) as $post ) {
		$sets['insights'][1] = array_merge( $sets['insights'][1], $add( sdc_parse_faq( sdc_get( $post->ID, 'faq' ) ), $post->post_title, get_permalink( $post ) ) );
	}
	foreach ( get_posts( array( 'post_type' => 'sd_term', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ) as $post ) {
		$sets['glossary'][1] = array_merge( $sets['glossary'][1], $add( sdc_parse_faq( sdc_get( $post->ID, 'faq' ) ), $post->post_title, get_permalink( $post ) ) );
	}
	foreach ( $sets as $id => $set ) {
		if ( $set[1] ) {
			$groups[] = array(
				'id'    => $id,
				'title' => $set[0],
				'items' => $set[1],
			);
		}
	}
	return $groups;
}

/**
 * Link to the FAQ page (the page using the FAQ template), current language.
 *
 * @return string
 */
function sd_faq_url() {
	static $url = null;
	if ( null === $url ) {
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one lookup per request.
				'meta_value'     => 'template-faq.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- one lookup per request.
			)
		);
		$url   = $pages ? sdc_translated_permalink( $pages[0] ) : '';
	}
	return $url;
}

/**
 * Archive link of a post type when it has published posts, else empty.
 *
 * @param string $post_type Post type.
 * @return string
 */
function sd_archive_url_if_any( $post_type ) {
	$has = get_posts(
		array(
			'post_type'      => $post_type,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	return $has ? (string) get_post_type_archive_link( $post_type ) : '';
}

/**
 * Format chip and "Updated" badge of an insight card.
 *
 * @param int $post_id Post ID.
 */
function sd_insight_badges( $post_id ) {
	$formats = sdc_field_groups()['answer']['fields']['format']['options'];
	$format  = (string) sdc_get( $post_id, 'format' );
	$label   = $format && isset( $formats[ $format ] ) ? $formats[ $format ] : '';
	$updated = sdc_recently_updated( $post_id );
	if ( ! $label && ! $updated ) {
		return;
	}
	echo '<span class="flex flex-wrap items-center gap-2 mb-3">';
	if ( $label ) {
		echo '<span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border border-white/10 text-gray-300">' . esc_html( $label ) . '</span>';
	}
	if ( $updated ) {
		echo '<span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-emerald-400/10 text-emerald-300 border border-emerald-400/20"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400" aria-hidden="true"></span>' . esc_html__( 'Updated', 'serhandemirel' ) . '</span>';
	}
	echo '</span>';
}

/**
 * Lines as "Name | detail | year | link" parts, for certificates, awards
 * and memberships.
 *
 * @param string[] $lines Lines.
 * @return array<int, array{name: string, detail: string, url: string}>
 */
function sdc_parse_credentials( $lines ) {
	$items = array();
	foreach ( (array) $lines as $line ) {
		$parts = array_map( 'trim', explode( '|', $line ) );
		$url   = '';
		foreach ( $parts as $i => $part ) {
			if ( $i && preg_match( '#^https?://#i', $part ) ) {
				$url = $part;
				unset( $parts[ $i ] );
			}
		}
		$parts   = array_values( array_filter( $parts, 'strlen' ) );
		$items[] = array(
			'name'   => (string) array_shift( $parts ),
			'detail' => implode( ' · ', $parts ),
			'url'    => $url,
		);
	}
	return $items;
}

/**
 * Up to four numbers for the profile page: the "Numbers" profile field,
 * or years in digital, projects, industries and talks counted from the site.
 *
 * @return array<int, array{value: string, label: string}>
 */
function sdc_profile_stats() {
	$profile = sdc_get_profile();
	$stats   = array();
	foreach ( (array) $profile['stats'] as $line ) {
		$parts   = array_map( 'trim', explode( '|', $line, 2 ) );
		$stats[] = array(
			'value' => $parts[0],
			'label' => $parts[1] ?? '',
		);
	}
	if ( $stats ) {
		return array_slice( $stats, 0, 4 );
	}
	$author = sd_author();
	if ( $author['years'] ) {
		$stats[] = array(
			'value' => $author['years'] . '+',
			'label' => __( 'years in digital', 'serhandemirel' ),
		);
	}
	$counts = array(
		array( count( get_posts( array( 'post_type' => 'sd_project', 'posts_per_page' => -1, 'fields' => 'ids' ) ) ), __( 'projects shipped', 'serhandemirel' ) ),
		array( count( get_terms( array( 'taxonomy' => 'sd_industry', 'hide_empty' => true, 'fields' => 'ids' ) ) ), __( 'industries', 'serhandemirel' ) ),
		array( count( get_posts( array( 'post_type' => 'sd_appearance', 'posts_per_page' => -1, 'fields' => 'ids' ) ) ), __( 'talks, trainings and press', 'serhandemirel' ) ),
	);
	foreach ( $counts as $count ) {
		if ( $count[0] > 1 ) {
			$stats[] = array(
				'value' => (string) $count[0],
				'label' => $count[1],
			);
		}
	}
	return $stats;
}

/**
 * Visible brands grouped by industry, for the profile page. Brands with no
 * industry come last, under "Other".
 *
 * @return array<int, array{term: WP_Term|null, brands: array[]}>
 */
function sdc_brands_by_industry() {
	$groups = array();
	$posts  = get_posts(
		array(
			'post_type'      => 'sd_brand',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	foreach ( $posts as $post ) {
		$logo = sdc_attachment_src( (int) sdc_get( $post->ID, 'logo_white' ) );
		if ( ! sdc_get( $post->ID, 'visible' ) || ! $logo ) {
			continue;
		}
		$terms = get_the_terms( $post, 'sd_industry' );
		$term  = is_array( $terms ) ? $terms[0] : null;
		if ( $term && function_exists( 'pll_get_term' ) && pll_get_term( $term->term_id ) ) {
			$term = get_term( pll_get_term( $term->term_id ) );
		}
		$key = $term ? $term->term_id : 0;
		if ( ! isset( $groups[ $key ] ) ) {
			$groups[ $key ] = array(
				'term'   => $term,
				'brands' => array(),
			);
		}
		$groups[ $key ]['brands'][] = array(
			'name' => $post->post_title,
			'logo' => $logo,
		);
	}
	if ( isset( $groups[0] ) ) {
		$other = $groups[0];
		unset( $groups[0] );
		$groups[0] = $other;
	}
	return array_values( $groups );
}

/**
 * Turns "[2]" in post text into a superscript link to the second source.
 *
 * @param string $html  Filtered post content.
 * @param int    $count Number of sources listed under the text.
 * @return string
 */
function sdc_link_citations( $html, $count ) {
	if ( $count < 1 ) {
		return $html;
	}
	return preg_replace_callback(
		'/\[(\d{1,2})\]/',
		function ( $m ) use ( $count ) {
			$n = (int) $m[1];
			return ( $n >= 1 && $n <= $count ) ? '<sup class="ml-0.5"><a href="#src-' . $n . '" class="sd-cite">' . $n . '</a></sup>' : $m[0];
		},
		$html
	);
}
