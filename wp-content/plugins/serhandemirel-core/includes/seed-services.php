<?php
/**
 * Starter service pages, imported as drafts in the default language.
 *
 * Draft copy for Serhan to review: durations and engagement models are
 * suggestions, and no prices are set. Each page is tagged with the matching
 * "Services" term and linked from the matching expertise card.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service page drafts, in the order of the expertise cards.
 *
 * Keys: title (also the Services term and expertise card it belongs to),
 * slug, excerpt, content, and the service fields from fields.php.
 *
 * @return array<int, array<string, string>>
 */
function sdc_seed_service_data() {
	return array(
		array(
			'title'            => 'Marketing & Growth',
			'slug'             => 'marketing-growth',
			'excerpt'          => 'Data-driven digital marketing and lead generation that turns traffic into measurable revenue.',
			'definition'       => 'Marketing & Growth is hands-on consulting that finds where your revenue comes from and grows it. I audit your funnel, set up clean tracking, and run lead generation and sales optimization programmes across search, social and email, so every channel is judged by the pipeline it creates, not by clicks.',
			'who_for'          => 'B2B companies, scale-ups and established brands that already have a product people buy, but whose marketing spend is hard to tie to sales, or whose lead flow has stalled.',
			'deliverables'     => "Funnel and channel audit with a prioritised action list\nTracking plan: GA4, Google Tag Manager, conversion and CRM events\nLead generation programme with landing pages, offers and nurture emails\nSales optimisation: lead scoring and hand-off rules between marketing and sales\nMonthly growth report on pipeline, cost per lead and revenue",
			'process'          => "Audit: I review your data, channels, funnel and competitors.\nPlan: We agree on targets, budget and the first three experiments.\nBuild: Tracking, landing pages and campaigns go live.\nOptimise: Weekly tests on offers, audiences and creatives.\nReport: A monthly review of what moved revenue and what comes next.",
			'duration'         => '3 months to start, then monthly',
			'engagement_model' => 'retainer',
			'content'          => "<p>Most marketing teams do not lack ideas; they lack a clear view of which activities bring in customers. I start there: with measurement you can trust, and a short list of experiments chosen for their expected impact on revenue.</p>\n<p>From there we build a repeatable growth engine: campaigns that bring qualified leads, pages that convert them, and a hand-off to sales that does not lose them.</p>",
			'faq'              => "How quickly will I see results?\nTracking and quick wins usually land in the first month. Lead volume and cost per lead typically improve within the first quarter, once enough tests have run.\n\nDo you run the ads yourself or work with my team?\nBoth work. I can run campaigns end to end, or plan and coach while your team or agency executes.\n\nWhich channels do you work with?\nGoogle Ads, LinkedIn, Meta, SEO, email and marketing automation. The mix depends on where your buyers are, not on a fixed package.\n\nWhat do you need from us to start?\nAccess to your analytics, ad accounts and CRM, plus one person on your side who can make decisions quickly.\n\nHow do you measure success?\nBy pipeline and revenue from marketing, cost per qualified lead and conversion rate between funnel stages, agreed with you at the start.",
		),
		array(
			'title'            => 'Digital Products',
			'slug'             => 'digital-products',
			'excerpt'          => 'Web applications, platforms and high-converting landing pages, from idea to launch.',
			'definition'       => 'Digital Products covers designing and building the web products a business runs on: web applications, customer platforms and high-converting landing pages. I take a product from idea to launch, keeping scope tight, choosing a stack your team can maintain, and measuring what users actually do after release.',
			'who_for'          => 'Founders validating a new product, companies replacing slow or outdated web tools, and marketing teams that need landing pages they can change without waiting on developers.',
			'deliverables'     => "Product scope, user flows and clickable prototype\nDesign system and responsive UI\nWeb application or landing pages, built and deployed\nAnalytics and conversion tracking from day one\nHandover documentation and team training",
			'process'          => "Discover: Goals, users and constraints in one or two workshops.\nPrototype: A clickable prototype tested with real users.\nBuild: Short iterations with a working version every week or two.\nLaunch: Deployment, tracking and a launch checklist.\nImprove: Data-led iterations after release.",
			'duration'         => '4–12 weeks',
			'engagement_model' => 'project',
			'content'          => "<p>A good digital product solves one problem very well. I help you find that problem, prove the solution with a prototype, and ship a first version quickly, so you learn from real users instead of long specifications.</p>\n<p>For landing pages the goal is simple: more of the right visitors take the next step. Every page is built to be tested and improved.</p>",
			'faq'              => "What does a typical project cost?\nIt depends on scope. After a short discovery call I send a fixed-scope proposal, so you know the price before work starts.\n\nWhich technologies do you use?\nModern, widely supported tools such as WordPress, headless CMSs and JavaScript frameworks, chosen so your team can maintain the result.\n\nCan you work with our existing developers?\nYes. I can lead the product and design work while your developers build, or deliver the whole project.\n\nHow long does a landing page take?\nA focused landing page usually takes one to two weeks, including copy, design, build and tracking.\n\nWho owns the code and designs?\nYou do. Everything is handed over with documentation at the end of the project.",
		),
		array(
			'title'            => 'AI & Automation',
			'slug'             => 'ai-automation',
			'excerpt'          => 'Practical AI and automation that removes repetitive work and speeds up business processes.',
			'definition'       => 'AI & Automation means finding the repetitive work in your business and handing it to software. I map your processes, pick the tasks where AI assistants or workflow automation save the most time, and build them safely into the tools you already use, with clear rules on data and human review.',
			'who_for'          => 'Teams that spend hours on copy-paste work, reporting, lead handling or customer questions, and leaders who want a realistic AI plan rather than experiments that never reach daily use.',
			'deliverables'     => "Process map with time and cost per task\nAI and automation opportunity list, ranked by impact and effort\nWorking automations in your tools (e.g. CRM, email, spreadsheets, chat)\nAI assistant set-up with prompts, guardrails and data rules\nTeam training and a playbook for adding new automations",
			'process'          => "Map: We list the processes and measure where time goes.\nPrioritise: We choose two or three automations with the clearest return.\nBuild: Automations are built, tested and documented.\nRoll out: Your team is trained and the results are measured.\nScale: The next set of automations, based on what worked.",
			'duration'         => '2–8 weeks per automation package',
			'engagement_model' => 'project',
			'content'          => "<p>AI is most useful when it is boring: answering routine questions, drafting reports, sorting leads, moving data between systems. I focus on those wins first, because they pay for the rest.</p>\n<p>Every automation comes with rules for what data it may use and where a person still checks the result.</p>",
			'faq'              => "Is our data safe with AI tools?\nWe decide up front which data may be used, choose tools with suitable privacy terms, and keep sensitive steps under human review.\n\nDo we need developers to keep automations running?\nUsually not. I build with tools your team can manage and document every workflow.\n\nWhich tasks are best to automate first?\nFrequent, rule-based tasks with a clear input and output, such as lead routing, reporting and first replies to common questions.\n\nWill AI replace our staff?\nThe aim is to remove repetitive work so people spend their time on customers and decisions, not to replace the team.\n\nHow do you measure the return?\nWe measure time per task before and after, error rates and response times, and compare them with the cost of the tools.",
		),
		array(
			'title'            => 'Strategy & Visibility',
			'slug'             => 'strategy-visibility',
			'excerpt'          => 'SEO, AI-search visibility and competitor analysis that put your brand where buyers look.',
			'definition'       => 'Strategy & Visibility makes your brand easy to find and easy to choose, in search engines and in AI assistants such as ChatGPT, Perplexity and Google AI Overviews. It combines competitor analysis, technical and content SEO, and generative engine optimisation (GEO) built on clear facts and structured data.',
			'who_for'          => 'Companies whose competitors outrank them, brands that are missing from AI answers in their category, and teams planning a new site or market entry.',
			'deliverables'     => "Competitor and market visibility analysis\nTechnical SEO audit and fix list\nKeyword and question map, with a content plan\nStructured data and entity set-up (Organization, Person, Service, FAQ)\nAI visibility check across ChatGPT, Perplexity and Google, repeated monthly",
			'process'          => "Benchmark: Where you and your competitors appear today, in search and in AI answers.\nFix: Technical issues and structured data first.\nPlan: The questions buyers ask, mapped to pages.\nPublish: Content written to answer those questions clearly.\nMonitor: Rankings, AI mentions and traffic every month.",
			'duration'         => '6 months recommended',
			'engagement_model' => 'retainer',
			'content'          => "<p>Search has two front doors now: the classic results page and the AI answer above it. Both reward the same things: a site that is technically sound, content that answers real questions directly, and facts about your brand that machines can read without guessing.</p>\n<p>I start by measuring where you stand against competitors in both, then fix the gaps in the order that brings visitors fastest.</p>",
			'faq'              => "What is the difference between SEO and GEO?\nSEO helps your pages rank in search results. GEO helps AI assistants understand, trust and cite your brand in their answers. They share the same foundation, but GEO puts more weight on clear facts, structured data and direct answers.\n\nHow long does SEO take to work?\nTechnical fixes can show results within weeks; content usually needs three to six months to build steady traffic.\n\nCan you guarantee first place on Google?\nNo one honestly can. I commit to clear targets, transparent reporting and the work that moves them.\n\nHow do you check visibility in AI assistants?\nWe ask a fixed set of buyer questions in ChatGPT, Perplexity and Google every month and track whether, and how, your brand is mentioned and linked.\n\nDo you write the content too?\nYes, or I brief and edit your writers. Either way, each page is built around one clear question.",
		),
		array(
			'title'            => 'Transformation & Ed',
			'slug'             => 'transformation-training',
			'excerpt'          => 'Digital transformation guidance and corporate training that make new ways of working stick.',
			'definition'       => 'Transformation & Education helps organisations change how they work with digital tools and make that change last. I assess your current digital maturity, build a practical roadmap, and train teams through workshops on digital marketing, AI and data, tailored to their daily work so new skills are used the next morning.',
			'who_for'          => 'Leadership teams starting a digital transformation, HR and learning teams planning corporate training, and departments adopting AI or new marketing tools.',
			'deliverables'     => "Digital maturity assessment with interviews and a short survey\nTransformation roadmap with priorities, owners and milestones\nTailored workshops and training programmes (on site or online)\nTraining materials, exercises and templates teams keep\nFollow-up session to review adoption and next steps",
			'process'          => "Assess: Interviews and a survey show where the organisation stands.\nDesign: A roadmap and a training plan built around real tasks.\nTrain: Hands-on workshops in small groups.\nApply: Teams use what they learned on their own projects, with support.\nReview: We measure adoption and adjust the plan.",
			'duration'         => 'Workshops from one day; programmes 1–6 months',
			'engagement_model' => 'workshop',
			'content'          => "<p>Transformation fails when it stays a slide deck. I keep it practical: a roadmap with owners and dates, and training built on your own data, tools and cases, so teams leave each session with something they can use straight away.</p>\n<p>Workshops are available in English and Turkish, on site or online.</p>",
			'faq'              => "Can the training be tailored to our company?\nYes. Every programme is built around your tools, processes and examples, after a short assessment.\n\nWhich topics do you teach?\nDigital marketing, growth and analytics, SEO and AI visibility, AI tools for daily work, and digital transformation for leaders.\n\nHow large can a group be?\nHands-on workshops work best with 8 to 20 people. Larger groups are split or run as talks with follow-up sessions.\n\nIn which languages do you train?\nEnglish and Turkish.\n\nHow do you measure whether training worked?\nWith a short skills check before and after, and a follow-up review of how teams use the new tools in their work.",
		),
	);
}

/**
 * Create the service pages as drafts and link the expertise cards, unless
 * service pages already exist.
 */
function sdc_seed_services() {
	if ( get_posts( array( 'post_type' => 'sd_service_page', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'lang' => '' ) ) ) {
		return;
	}
	$fields = array_keys( sdc_field_groups()['service']['fields'] );
	$cards  = get_posts(
		array(
			'post_type'      => 'sd_expertise',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'lang'           => '',
		)
	);

	foreach ( sdc_seed_service_data() as $i => $service ) {
		$meta = array();
		foreach ( $fields as $field ) {
			if ( isset( $service[ $field ] ) ) {
				$meta[ sdc_meta_key( $field ) ] = $service[ $field ];
			}
		}
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'sd_service_page',
				'post_status'  => 'draft',
				'post_title'   => $service['title'],
				'post_name'    => $service['slug'],
				'post_excerpt' => $service['excerpt'],
				'post_content' => $service['content'],
				'menu_order'   => $i + 1,
				'meta_input'   => $meta,
			)
		);
		if ( ! $post_id || is_wp_error( $post_id ) ) {
			continue;
		}
		sdc_set_default_language( $post_id );

		$term = term_exists( $service['title'], 'sd_service' );
		if ( $term ) {
			wp_set_object_terms( $post_id, (int) $term['term_id'], 'sd_service' );
		}
		foreach ( $cards as $card ) {
			if ( $card->post_title === $service['title'] && ! sdc_get( $card->ID, 'service_page' ) ) {
				update_post_meta( $card->ID, sdc_meta_key( 'service_page' ), $post_id );
			}
		}
	}
}

/**
 * A draft About page on the theme's Profile template, filled from the
 * Profile screen; its text is a starting point to rewrite.
 */
function sdc_seed_about_page() {
	if ( get_page_by_path( 'about', OBJECT, 'page' ) ) {
		return;
	}
	$content = implode(
		"\n\n",
		array(
			'<!-- wp:paragraph --><p>I work with companies that want digital work to show up in the numbers: more qualified leads, better conversion, less manual work and a brand that people (and AI assistants) find when they search.</p><!-- /wp:paragraph -->',
			'<!-- wp:paragraph --><p>My work sits where marketing, product and technology meet. I plan and run data-driven campaigns, build web apps and landing pages, set up AI tools and automation, improve search and AI visibility, and train teams so the results last after the project ends.</p><!-- /wp:paragraph -->',
			'<!-- wp:paragraph --><p>I am based in Istanbul and work with clients across Europe and beyond, in English and Turkish.</p><!-- /wp:paragraph -->',
		)
	);
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'draft',
			'post_title'   => 'About',
			'post_name'    => 'about',
			'post_content' => $content,
			'meta_input'   => array( '_wp_page_template' => 'template-profile.php' ),
		)
	);
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		sdc_set_default_language( $post_id );
	}
}
