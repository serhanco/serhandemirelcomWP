<?php
/**
 * Tracking codes from the "Tracking" settings tab, Consent Mode defaults,
 * per-page switches (from the Serhan Demirel Core plugin's fields) and the
 * front-end dataLayer events script.
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether tracking codes print on this request.
 *
 * Off for logged-in editors (unless enabled in the panel), in previews, and
 * on pages with "Turn off tracking codes on this page" ticked.
 *
 * @return bool
 */
function sd_tracking_enabled() {
	static $enabled = null;
	if ( null !== $enabled ) {
		return $enabled;
	}
	$enabled = true;
	if ( is_preview() || ( current_user_can( 'edit_posts' ) && ! sd_opt( 'track_admins' ) ) ) {
		$enabled = false;
	} elseif ( is_singular() && get_post_meta( get_queried_object_id(), '_sd_disable_tracking', true ) ) {
		$enabled = false;
	}
	return (bool) apply_filters( 'sd_tracking_enabled', $enabled );
}

/**
 * An ID option, reduced to the characters such IDs use.
 *
 * @param string $key Option key.
 * @return string
 */
function sd_tracking_id( $key ) {
	return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) sd_opt( $key ) );
}

/**
 * Consent defaults, GTM, GA4, Meta Pixel, Clarity and extra head code.
 */
function sd_tracking_head() {
	if ( ! sd_tracking_enabled() ) {
		return;
	}
	$gtm     = sd_tracking_id( 'gtm_id' );
	$ga4     = sd_tracking_id( 'ga4_id' );
	$pixel   = sd_tracking_id( 'meta_pixel_id' );
	$clarity = sd_tracking_id( 'clarity_id' );

	echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}</script>\n";

	if ( sd_opt( 'consent_mode' ) ) {
		echo "<script>gtag('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'denied',wait_for_update:500});</script>\n";
	}

	if ( $gtm ) {
		printf(
			"<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer',%s);</script>\n",
			wp_json_encode( $gtm )
		);
	}

	if ( $ga4 ) {
		printf(
			"<!-- Google tag (gtag.js) -->\n<script async src=\"%s\"></script>\n<script>gtag('js',new Date());gtag('config',%s);</script>\n",
			esc_url( 'https://www.googletagmanager.com/gtag/js?id=' . $ga4 ),
			wp_json_encode( $ga4 )
		);
	}

	if ( $pixel ) {
		printf(
			"<!-- Meta Pixel -->\n<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init',%s);fbq('track','PageView');</script>\n",
			wp_json_encode( $pixel )
		);
	}

	if ( $clarity ) {
		printf(
			"<!-- Microsoft Clarity -->\n<script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src='https://www.clarity.ms/tag/'+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,'clarity','script',%s);</script>\n",
			wp_json_encode( $clarity )
		);
	}

	echo sd_opt( 'code_head' ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- administrator-entered code (unfiltered_html).

	if ( is_singular() ) {
		echo get_post_meta( get_queried_object_id(), '_sd_extra_head_code', true ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- administrator-entered code (unfiltered_html).
	}
}
add_action( 'wp_head', 'sd_tracking_head', 2 );

/**
 * GTM noscript fallback and extra body code, right after <body>.
 */
function sd_tracking_body_open() {
	if ( ! sd_tracking_enabled() ) {
		return;
	}
	$gtm = sd_tracking_id( 'gtm_id' );
	if ( $gtm ) {
		printf(
			'<noscript><iframe src="%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n",
			esc_url( 'https://www.googletagmanager.com/ns.html?id=' . $gtm )
		);
	}
	echo sd_opt( 'code_body' ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- administrator-entered code (unfiltered_html).
}
add_action( 'wp_body_open', 'sd_tracking_body_open', 1 );

/**
 * LinkedIn Insight Tag and extra footer code.
 */
function sd_tracking_footer() {
	if ( ! sd_tracking_enabled() ) {
		return;
	}
	$linkedin = preg_replace( '/\D+/', '', (string) sd_opt( 'linkedin_partner_id' ) );
	if ( $linkedin ) {
		printf(
			"<!-- LinkedIn Insight Tag -->\n<script>window._linkedin_partner_id=%s;window._linkedin_data_partner_ids=window._linkedin_data_partner_ids||[];window._linkedin_data_partner_ids.push(window._linkedin_partner_id);(function(l){if(!l){window.lintrk=function(a,b){window.lintrk.q.push([a,b])};window.lintrk.q=[]}var s=document.getElementsByTagName('script')[0];var b=document.createElement('script');b.type='text/javascript';b.async=true;b.src='https://snap.licdn.com/li.lms-analytics/insight.min.js';s.parentNode.insertBefore(b,s);})(window.lintrk);</script>\n",
			wp_json_encode( $linkedin )
		);
	}
	echo sd_opt( 'code_footer' ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- administrator-entered code (unfiltered_html).
}
add_action( 'wp_footer', 'sd_tracking_footer', 100 );

/**
 * dataLayer events (form submit, WhatsApp and email clicks) and the UTM
 * cookie the contact form reads. Loaded even when tracking codes are off;
 * pushing to dataLayer without a tag on the page does nothing.
 */
function sd_tracking_script() {
	wp_enqueue_script( 'sd-tracking', get_template_directory_uri() . '/assets/js/tracking.js', array(), SD_THEME_VERSION, true );
	wp_localize_script( 'sd-tracking', 'sdTracking', array( 'lang' => sd_current_lang() ) );
}
add_action( 'wp_enqueue_scripts', 'sd_tracking_script' );
