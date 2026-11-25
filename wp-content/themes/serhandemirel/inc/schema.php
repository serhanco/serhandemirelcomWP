<?php
/**
 * Structured data (JSON-LD) on every front-end page.
 *
 * One graph per page: the Person from the Profile screen, the WebSite, the
 * page itself, and what the page is about (an article, a project, a service
 * and its FAQ). Skipped when Yoast, Rank Math, AIOSEO or SEOPress print
 * their own graph; filter "sdc_output_schema" to force it either way.
 *
 * @package serhandemirel
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
	foreach ( sdc_parse_credentials( $profile['credentials'] ) as $credential ) {
		$person['hasCredential'][] = array_filter(
			array(
				'@type' => 'EducationalOccupationalCredential',
				'name'  => $credential['name'],
				'url'   => $credential['url'],
			)
		);
	}
	foreach ( sdc_parse_credentials( $profile['awards'] ) as $award ) {
		$person['award'][] = trim( $award['name'] . ( $award['detail'] ? ' (' . $award['detail'] . ')' : '' ) );
	}
	foreach ( sdc_parse_credentials( $profile['memberships'] ) as $membership ) {
		$person['memberOf'][] = array(
			'@type' => 'Organization',
			'name'  => $membership['name'],
		);
	}
	$about = function_exists( 'sd_about_url' ) ? sd_about_url() : '';
	if ( $about ) {
		$person['mainEntityOfPage'] = $about;
	}
	$email = function_exists( 'sd_opt' ) ? (string) sd_opt( 'email' ) : '';
	if ( $email ) {
		$person['contactPoint'] = array(
			'@type'       => 'ContactPoint',
			'contactType' => 'sales',
			'email'       => $email,
			'url'         => home_url( '/#contact' ),
		);
	}
	// Talks, trainings and press: other pages about the person.
	foreach ( sdc_get_appearances( 10 ) as $item ) {
		$link = $item['url'] ? $item['url'] : $item['video_url'];
		if ( $link ) {
			$person['subjectOf'][] = array_filter(
				array(
					'@type' => in_array( $item['kind'], array( 'press', 'interview' ), true ) ? 'Article' : ( 'podcast' === $item['kind'] ? 'PodcastEpisode' : 'Event' ),
					'name'  => $item['title'],
					'url'   => $link,
				)
			);
		}
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
	$url      = function_exists( 'sd_page_meta' ) ? sd_page_meta()['url'] : ( is_singular() ? get_permalink() : home_url( add_query_arg( array() ) ) );
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

	$crumbs = sdc_schema_breadcrumbs( $url );
	if ( $crumbs ) {
		$page['breadcrumb'] = array( '@id' => $crumbs['@id'] );
		$graph[]            = $crumbs;
	}

	if ( is_singular() ) {
		$post                  = get_queried_object();
		$dates                 = sdc_dates( $post->ID );
		$page['datePublished'] = get_post_time( 'c', true, $post );
		$page['dateModified']  = $dates['updated'] ? gmdate( 'c', strtotime( $dates['updated'] ) ) : get_post_time( 'c', true, $post );
		$page['author']        = $person;
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
		if ( is_page_template( 'template-faq.php' ) ) {
			$items = array();
			foreach ( sdc_faq_hub() as $group ) {
				$items = array_merge( $items, $group['items'] );
			}
			if ( $items ) {
				$graph[]            = sdc_schema_faq( $items, $url );
				$page['mainEntity'] = array( '@id' => $url . '#faq' );
			}
		}
	} else {
		$graph = array_merge( $graph, sdc_schema_archive( $url, $person, $page ) );
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
			$extras = sdc_post_extras( $post->ID );
			if ( $extras['reviewed'] ) {
				$article['dateModified'] = gmdate( 'c', strtotime( $extras['reviewed'] ) );
			}
			if ( $extras['question'] ) {
				$article['alternativeHeadline'] = $extras['question'];
			}
			foreach ( $extras['sources'] as $source ) {
				$article['citation'][] = array_filter(
					array(
						'@type'     => 'CreativeWork',
						'name'      => $source['title'],
						'url'       => $source['url'],
						'publisher' => $source['publisher'],
					)
				);
			}
			foreach ( $extras['terms'] as $term ) {
				$article['mentions'][] = array(
					'@type' => 'DefinedTerm',
					'@id'   => $term['url'] . '#term',
					'name'  => $term['title'],
					'url'   => $term['url'],
				);
			}
			if ( $extras['experience'] && $extras['experience']['project'] ) {
				$article['isBasedOn'] = array(
					'@type' => 'CreativeWork',
					'name'  => $extras['experience']['project']['title'],
					'url'   => $extras['experience']['project']['permalink'],
				);
			}
			if ( $extras['service'] ) {
				$article['about'] = array(
					'@type' => 'Service',
					'name'  => $extras['service']['title'],
					'url'   => $extras['service']['url'],
				);
			}
			$nodes = array( $article );
			if ( $extras['faq'] ) {
				$nodes[] = sdc_schema_faq( $extras['faq'], $url );
			}
			return $nodes;

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
					'dateCreated'      => $project['start'] ? $project['start'] : ( $project['year'] ? (string) $project['year'] : '' ),
					'temporalCoverage' => $project['start'] ? $project['start'] . '/' . ( $project['end'] ? $project['end'] : '..' ) : '',
					'datePublished'    => get_post_time( 'c', true, $post ),
					'inLanguage'       => sdc_schema_language(),
				)
			);
			if ( $project['tools'] ) {
				$work['keywords'] = implode( ', ', $project['tools'] );
			}
			if ( $project['client'] && ! $project['hidden'] ) {
				$work['sourceOrganization'] = array_filter(
					array(
						'@type' => 'Organization',
						'name'  => $project['client'],
						'url'   => $project['url'],
					)
				);
			}
			$quotes = sdc_get_testimonials( array( 'project' => $post->ID, 'limit' => 1 ) );
			if ( $quotes ) {
				$work['comment'] = array(
					'@type'  => 'Comment',
					'text'   => $quotes[0]['quote'],
					'author' => array_filter(
						array(
							'@type'    => 'Person',
							'name'     => $quotes[0]['person'],
							'jobTitle' => $quotes[0]['role'],
							'sameAs'   => $quotes[0]['linkedin'],
						)
					),
				);
			}
			return array( $work );

		case 'sd_term':
			$term = sdc_term_data( $post );
			$node = array_filter(
				array(
					'@type'            => 'DefinedTerm',
					'@id'              => $url . '#term',
					'name'             => $term['title'],
					'alternateName'    => $term['aliases'],
					'description'      => $term['short'],
					'url'              => $url,
					'inDefinedTermSet' => array( '@id' => get_post_type_archive_link( 'sd_term' ) . '#set' ),
				)
			);
			$nodes = array( $node );
			if ( $term['faq'] ) {
				$nodes[] = sdc_schema_faq( $term['faq'], $url );
			}
			return $nodes;

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
 * BreadcrumbList for the current page: home, the section, the page.
 *
 * @param string $url Page URL.
 * @return array|null
 */
function sdc_schema_breadcrumbs( $url ) {
	if ( is_front_page() ) {
		return null;
	}
	$items = array( array( get_bloginfo( 'name' ), home_url( '/' ) ) );
	$label = function_exists( 'sd_opt' ) ? 'sd_opt' : '__return_empty_string';
	if ( is_singular() ) {
		$post = get_queried_object();
		switch ( $post->post_type ) {
			case 'post':
				if ( function_exists( 'sd_insights_url' ) && sd_insights_url() ) {
					$items[] = array( call_user_func( $label, 'insights_eyebrow' ), sd_insights_url() );
				}
				break;
			case 'sd_project':
				$items[] = array( call_user_func( $label, 'work_eyebrow' ), get_post_type_archive_link( 'sd_project' ) );
				$terms   = get_the_terms( $post, 'sd_industry' );
				if ( is_array( $terms ) ) {
					$items[] = array( $terms[0]->name, get_term_link( $terms[0] ) );
				}
				break;
			case 'sd_service_page':
				$items[] = array( __( 'Services', 'serhandemirel' ), get_post_type_archive_link( 'sd_service_page' ) );
				break;
			case 'sd_term':
				$items[] = array( __( 'Glossary', 'serhandemirel' ), get_post_type_archive_link( 'sd_term' ) );
				break;
		}
		$items[] = array( get_the_title( $post ), $url );
	} elseif ( is_tax( 'sd_industry' ) || is_tax( 'sd_service' ) ) {
		$items[] = array( call_user_func( $label, 'work_eyebrow' ), get_post_type_archive_link( 'sd_project' ) );
		$items[] = array( single_term_title( '', false ), $url );
	} else {
		$items[] = array( wp_strip_all_tags( wp_get_document_title() ), $url );
	}
	$list = array();
	foreach ( array_values( array_filter( $items, function ( $item ) {
		return '' !== (string) $item[0] && ! is_wp_error( $item[1] ) && $item[1];
	} ) ) as $i => $item ) {
		$list[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => wp_strip_all_tags( (string) $item[0] ),
			'item'     => $item[1],
		);
	}
	return count( $list ) > 1 ? array(
		'@type'           => 'BreadcrumbList',
		'@id'             => $url . '#breadcrumb',
		'itemListElement' => $list,
	) : null;
}

/**
 * Nodes for list pages: the glossary as a DefinedTermSet, talks as events
 * and episodes, industries with their questions.
 *
 * @param string $url    Page URL.
 * @param array  $person Person reference.
 * @param array  $page   The page node, changed in place.
 * @return array[]
 */
function sdc_schema_archive( $url, $person, &$page ) {
	$nodes = array();
	if ( is_post_type_archive( 'sd_term' ) ) {
		$set = array(
			'@type'          => 'DefinedTermSet',
			'@id'            => $url . '#set',
			'name'           => __( 'Glossary', 'serhandemirel' ),
			'url'            => $url,
			'publisher'      => $person,
			'hasDefinedTerm' => array(),
		);
		foreach ( sdc_get_glossary() as $terms ) {
			foreach ( $terms as $term ) {
				$set['hasDefinedTerm'][] = array_filter(
					array(
						'@type'       => 'DefinedTerm',
						'@id'         => $term['url'] . '#term',
						'name'        => $term['title'],
						'description' => $term['short'],
						'url'         => $term['url'],
					)
				);
			}
		}
		$page['mainEntity'] = array( '@id' => $set['@id'] );
		$nodes[]            = $set;
	} elseif ( is_post_type_archive( 'sd_appearance' ) ) {
		$list = array();
		foreach ( sdc_get_appearances() as $i => $item ) {
			$link = $item['url'] ? $item['url'] : ( $item['video_url'] ? $item['video_url'] : $url . '#item-' . $item['id'] );
			if ( in_array( $item['kind'], array( 'press', 'interview' ), true ) ) {
				$node = array(
					'@type'         => 'Article',
					'headline'      => $item['title'],
					'datePublished' => $item['date'],
					'publisher'     => $item['event'] ? array( '@type' => 'Organization', 'name' => $item['event'] ) : null,
					'about'         => $person,
				);
			} elseif ( 'podcast' === $item['kind'] ) {
				$node = array(
					'@type'         => 'PodcastEpisode',
					'name'          => $item['title'],
					'datePublished' => $item['date'],
					'partOfSeries'  => $item['event'] ? array( '@type' => 'PodcastSeries', 'name' => $item['event'] ) : null,
					'actor'         => $person,
				);
			} else {
				$node = array(
					'@type'               => 'Event',
					'name'                => $item['title'],
					'startDate'           => $item['date'],
					'eventStatus'         => 'https://schema.org/EventScheduled',
					'eventAttendanceMode' => $item['location'] ? 'https://schema.org/OfflineEventAttendanceMode' : 'https://schema.org/OnlineEventAttendanceMode',
					'location'            => $item['location'] ? array( '@type' => 'Place', 'name' => $item['location'], 'address' => $item['location'] ) : array( '@type' => 'VirtualLocation', 'url' => $link ),
					'organizer'           => $item['host'] ? array( '@type' => 'Organization', 'name' => $item['host'] ) : null,
					'performer'           => $person,
					'inLanguage'          => $item['language'],
				);
			}
			$node['url']         = $link;
			$node['description'] = $item['excerpt'];
			if ( $item['video_url'] && $item['image'] ) {
				$node['subjectOf'] = array(
					'@type'        => 'VideoObject',
					'name'         => $item['title'],
					'contentUrl'   => $item['video_url'],
					'thumbnailUrl' => $item['image'],
					'uploadDate'   => $item['date'],
					'duration'     => $item['duration'] ? 'PT' . $item['duration'] . 'M' : null,
				);
				$node['subjectOf'] = array_filter( $node['subjectOf'] );
			}
			$list[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'item'     => array_filter( $node ),
			);
		}
		if ( $list ) {
			$nodes[]            = array(
				'@type'           => 'ItemList',
				'@id'             => $url . '#list',
				'itemListElement' => $list,
			);
			$page['mainEntity'] = array( '@id' => $url . '#list' );
			$page['about']      = $person;
		}
	} elseif ( is_tax( 'sd_industry' ) ) {
		$term          = get_queried_object();
		$data          = sdc_industry_data( $term );
		$page['about'] = array(
			'@type' => 'Thing',
			'name'  => $term->name,
		);
		if ( $data['intro'] ) {
			$page['description'] = wp_strip_all_tags( $data['intro'] );
		}
		if ( $data['updated'] ) {
			$page['dateModified'] = gmdate( 'c', strtotime( $data['updated'] ) );
		}
		if ( $data['published'] ) {
			$page['datePublished'] = gmdate( 'c', strtotime( $data['published'] ) );
		}
		if ( $data['faq'] ) {
			$nodes[] = sdc_schema_faq( $data['faq'], $url );
		}
	}
	return $nodes;
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
