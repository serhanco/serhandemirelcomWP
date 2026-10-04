<?php
/**
 * Structured data (JSON-LD) on every front-end page.
 *
 * One graph per page: the Person from the Profile screen, the WebSite, the
 * page itself, and what the page is about (an article, a project, a service
 * and its FAQ). Skipped when Yoast, Rank Math, AIOSEO or SEOPress print
 * their own graph; filter "sdc_output_schema" to force it either way.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether to print the graph.
 *
 * @return bool
 */
function sdc_schema_enabled() {
	$seo_plugin = defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
	return (bool) apply_filters( 'sdc_output_schema', ! $seo_plugin );
}

/**
 * BCP 47 code of the current language, e.g. "tr-TR".
 *
 * @return string
 */
function sdc_schema_language() {
	return str_replace( '_', '-', determine_locale() );
}

/**
 * The Person node.
 *
 * @return array
 */
function sdc_schema_person() {
	$profile = sdc_get_profile();
	$home    = home_url( '/' );
	$person  = array(
		'@type'       => 'Person',
		'@id'         => trailingslashit( get_option( 'home' ) ) . '#person',
		'name'        => $profile['full_name'],
		'url'         => $home,
		'jobTitle'    => $profile['job_title'],
		'description' => $profile['short_bio'],
		'address'     => array_filter(
			array(
				'@type'           => 'PostalAddress',
				'addressLocality' => $profile['city'],
				'addressCountry'  => $profile['country'],
			)
		),
	);
	if ( $profile['alternate_names'] ) {
		$person['alternateName'] = $profile['alternate_names'];
	}
	if ( $profile['headshot_url'] ) {
		$person['image'] = $profile['headshot_url'];
	}
	if ( $profile['same_as'] ) {
		$person['sameAs'] = $profile['same_as'];
	}
	if ( $profile['knows_about'] ) {
		$person['knowsAbout'] = $profile['knows_about'];
	}
	if ( $profile['languages_spoken'] ) {
		$person['knowsLanguage'] = $profile['languages_spoken'];
	}
	if ( $profile['works_for'] ) {
		$person['worksFor'] = array_filter(
			array(
				'@type' => 'Organization',
				'name'  => $profile['works_for'],
				'url'   => $profile['works_for_url'],
			)
		);
	}
	foreach ( $profile['alumni_of'] as $school ) {
		$person['alumniOf'][] = array(
			'@type' => 'EducationalOrganization',
			'name'  => $school,
		);
	}
	foreach ( $profile['credentials'] as $credential ) {
		$person['hasCredential'][] = array(
			'@type' => 'EducationalOccupationalCredential',
			'name'  => $credential,
		);
	}
	return $person;
}

/**
 * The whole graph for the current page.
 *
 * @return array
 */
function sdc_schema_graph() {
	$home     = trailingslashit( get_option( 'home' ) );
	$person   = array( '@id' => $home . '#person' );
	$website  = array( '@id' => $home . '#website' );
	$language = sdc_schema_language();
	$url      = is_singular() ? get_permalink() : ( is_post_type_archive() ? get_post_type_archive_link( get_query_var( 'post_type' ) ) : home_url( add_query_arg( array() ) ) );
	if ( is_front_page() ) {
		$url = home_url( '/' );
	}

	$graph = array(
		sdc_schema_person(),
		array(
			'@type'      => 'WebSite',
			'@id'        => $website['@id'],
			'url'        => home_url( '/' ),
			'name'       => get_bloginfo( 'name' ),
			'inLanguage' => $language,
			'publisher'  => $person,
		),
	);

	/**
	 * Filters whether this page is about Serhan (ProfilePage with the
	 * Person as its main entity): the home page and the About template.
	 *
	 * @param bool $is_profile Whether it is a profile page.
	 */
	$is_profile = (bool) apply_filters( 'sdc_is_profile_page', is_front_page() || is_page_template( 'template-profile.php' ) );

	$page = array(
		'@type'      => $is_profile ? 'ProfilePage' : ( is_singular() ? 'WebPage' : 'CollectionPage' ),
		'@id'        => $url . '#webpage',
		'url'        => $url,
		'name'       => wp_get_document_title(),
		'isPartOf'   => $website,
		'inLanguage' => $language,
	);
	if ( $is_profile ) {
		$page['mainEntity'] = $person;
	}

	if ( is_singular() ) {
		$post                  = get_queried_object();
		$page['datePublished'] = get_post_time( 'c', true, $post );
		$page['dateModified']  = get_post_modified_time( 'c', true, $post );
		if ( has_post_thumbnail( $post ) ) {
			$page['primaryImageOfPage'] = array(
				'@type' => 'ImageObject',
				'url'   => get_the_post_thumbnail_url( $post, 'full' ),
			);
		}
		$main = sdc_schema_main_entity( $post, $url, $person );
		if ( $main ) {
			$page['mainEntity'] = array( '@id' => $main[0]['@id'] );
			$graph              = array_merge( $graph, $main );
		}
	}
	$graph[] = $page;

	/**
	 * Filters the JSON-LD graph before it is printed.
	 *
	 * @param array $graph Nodes.
	 */
	return apply_filters( 'sdc_schema_graph', $graph );
}

/**
 * Nodes for what a single page is about; the first is the main entity.
 *
 * @param WP_Post $post   Post.
 * @param string  $url    Page URL.
 * @param array   $person Person reference.
 * @return array[]
 */
function sdc_schema_main_entity( $post, $url, $person ) {
	$image = has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'full' ) : '';

	switch ( $post->post_type ) {
		case 'post':
			$article = array(
				'@type'            => 'BlogPosting',
				'@id'              => $url . '#article',
				'headline'         => get_the_title( $post ),
				'description'      => wp_strip_all_tags( get_the_excerpt( $post ) ),
				'datePublished'    => get_post_time( 'c', true, $post ),
				'dateModified'     => get_post_modified_time( 'c', true, $post ),
				'author'           => $person,
				'publisher'        => $person,
				'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
				'inLanguage'       => sdc_schema_language(),
				'wordCount'        => str_word_count( wp_strip_all_tags( $post->post_content ) ),
			);
			if ( $image ) {
				$article['image'] = $image;
			}
			return array( $article );

		case 'sd_project':
			$project = sdc_project_data( $post );
			$work    = array_filter(
				array(
					'@type'       => 'CreativeWork',
					'@id'         => $url . '#project',
					'name'        => $project['title'],
					'description' => $project['summary'],
					'url'         => $url,
					'image'       => $image,
					'creator'     => $person,
					'dateCreated' => $project['year'] ? (string) $project['year'] : '',
					'inLanguage'  => sdc_schema_language(),
				)
			);
			if ( $project['client'] ) {
				$work['sourceOrganization'] = array_filter(
					array(
						'@type' => 'Organization',
						'name'  => $project['client'],
						'url'   => $project['url'],
					)
				);
			}
			return array( $work );

		case 'sd_service_page':
			$service = sdc_service_data( $post );
			$node    = array_filter(
				array(
					'@type'       => 'Service',
					'@id'         => $url . '#service',
					'name'        => $service['title'],
					'description' => $service['definition'] ? $service['definition'] : $service['excerpt'],
					'url'         => $url,
					'image'       => $image,
					'provider'    => $person,
					'serviceType' => $service['title'],
					'areaServed'  => $service['area_served'],
					'audience'    => $service['who_for'] ? array( '@type' => 'Audience', 'audienceType' => $service['who_for'] ) : null,
				)
			);
			if ( $service['price_from'] ) {
				$node['offers'] = array(
					'@type'              => 'Offer',
					'priceSpecification' => array(
						'@type'         => 'PriceSpecification',
						'minPrice'      => $service['price_from'],
						'priceCurrency' => $service['currency'],
					),
				);
			}
			if ( $service['deliverables'] ) {
				$node['hasOfferCatalog'] = array(
					'@type'           => 'OfferCatalog',
					'name'            => $service['title'],
					'itemListElement' => array_map(
						function ( $item ) {
							return array(
								'@type'       => 'Offer',
								'itemOffered' => array(
									'@type' => 'Service',
									'name'  => $item,
								),
							);
						},
						$service['deliverables']
					),
				);
			}
			$nodes = array( $node );
			if ( $service['faq'] ) {
				$nodes[] = sdc_schema_faq( $service['faq'], $url );
			}
			return $nodes;
	}
	return array();
}

/**
 * FAQPage node.
 *
 * @param array  $faq Questions and answers.
 * @param string $url Page URL.
 * @return array
 */
function sdc_schema_faq( $faq, $url ) {
	return array(
		'@type'      => 'FAQPage',
		'@id'        => $url . '#faq',
		'isPartOf'   => array( '@id' => $url . '#webpage' ),
		'mainEntity' => array_map(
			function ( $item ) {
				return array(
					'@type'          => 'Question',
					'name'           => $item['question'],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $item['answer'],
					),
				);
			},
			$faq
		),
	);
}

/**
 * Print the graph in <head>.
 */
function sdc_print_schema() {
	if ( is_admin() || is_feed() || is_404() || ! sdc_schema_enabled() ) {
		return;
	}
	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => array_values( sdc_schema_graph() ),
	);
	echo "<script type=\"application/ld+json\">\n" . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . "\n</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON with < and > escaped.
}
add_action( 'wp_head', 'sdc_print_schema', 5 );
