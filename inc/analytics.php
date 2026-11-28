<?php
/**
 * Cookieless visitor analytics: collection and storage.
 *
 * Principles:
 * - Nothing is stored in the browser (no cookies, no localStorage) and no device
 *   fingerprint is read.
 * - The IP address is never stored. A visitor is recognised for one day by a hash
 *   of IP address + user agent + a random salt that changes daily. The salt is
 *   discarded when the day changes, so the hash cannot be tied to a person or to
 *   another day.
 * - Logged-in users and bots are not counted.
 * - Data stays in this site's own database and is kept for 25 months.
 *
 * Report: inc/analytics-report.php. Browser script: assets/js/analytics.js.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

const RAIDER_STATS_DB_VERSION = '1';

/**
 * Is tracking on. Turn off with: add_filter( 'raider_stats_enabled', '__return_false' );
 */
function raider_stats_enabled() {
	return (bool) apply_filters( 'raider_stats_enabled', true );
}

/**
 * Table name with prefix.
 *
 * @param string $name views or events.
 */
function raider_stats_table( $name ) {
	global $wpdb;
	return $wpdb->prefix . 'raider_' . $name;
}

/**
 * Contact event types and their labels in the report.
 */
function raider_stats_event_types() {
	return array(
		'phone'    => 'Puhelinnumeron napautus',
		'whatsapp' => 'WhatsApp',
		'email'    => 'Sähköpostilinkki',
		'form'     => 'Yhteydenottolomake',
	);
}

/* ---------- Install + retention ---------- */

/**
 * Create the tables when the theme is first loaded or the table version changes.
 */
function raider_stats_install() {
	if ( RAIDER_STATS_DB_VERSION === get_option( 'raider_stats_db' ) ) {
		return;
	}
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();

	dbDelta(
		'CREATE TABLE ' . raider_stats_table( 'views' ) . " (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		created datetime NOT NULL,
		day date NOT NULL,
		visitor char(16) NOT NULL,
		path varchar(191) NOT NULL DEFAULT '',
		source varchar(100) NOT NULL DEFAULT '',
		campaign varchar(100) NOT NULL DEFAULT '',
		internal tinyint(1) NOT NULL DEFAULT 0,
		device varchar(12) NOT NULL DEFAULT '',
		duration smallint(5) unsigned NOT NULL DEFAULT 0,
		PRIMARY KEY  (id),
		KEY day (day)
		) $charset;"
	);
	dbDelta(
		'CREATE TABLE ' . raider_stats_table( 'events' ) . " (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		created datetime NOT NULL,
		day date NOT NULL,
		visitor char(16) NOT NULL,
		type varchar(20) NOT NULL DEFAULT '',
		target varchar(191) NOT NULL DEFAULT '',
		path varchar(191) NOT NULL DEFAULT '',
		PRIMARY KEY  (id),
		KEY day (day)
		) $charset;"
	);
	update_option( 'raider_stats_db', RAIDER_STATS_DB_VERSION );
	if ( ! get_option( 'raider_stats_started' ) ) {
		update_option( 'raider_stats_started', wp_date( 'Y-m-d' ), false );
	}
}
add_action( 'init', 'raider_stats_install' );

/**
 * Daily cleanup: rows older than 25 months are deleted.
 */
add_action(
	'init',
	function () {
		if ( ! wp_next_scheduled( 'raider_stats_cleanup' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'raider_stats_cleanup' );
		}
	}
);

add_action(
	'raider_stats_cleanup',
	function () {
		global $wpdb;
		$limit = wp_date( 'Y-m-d', strtotime( '-25 months' ) );
		foreach ( array( 'views', 'events' ) as $name ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . raider_stats_table( $name ) . ' WHERE day < %s', $limit ) );
		}
	}
);

/* ---------- Helpers ---------- */

/**
 * Visitor IP for the daily hash. The address itself is never stored.
 */
function raider_stats_ip() {
	$ip     = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : '';
	$public = filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );
	if ( ! $public && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		// Behind the host's own reverse proxy: the original address is the first value.
		$parts = explode( ',', (string) $_SERVER['HTTP_X_FORWARDED_FOR'] );
		$first = trim( $parts[0] );
		if ( filter_var( $first, FILTER_VALIDATE_IP ) ) {
			$ip = $first;
		}
	}
	return $ip;
}

/**
 * Anonymous visitor id that stays the same for one day.
 *
 * @param string $ua User agent.
 */
function raider_stats_visitor( $ua ) {
	$today = wp_date( 'Y-m-d' );
	$salt  = get_option( 'raider_stats_salt' );
	if ( ! is_array( $salt ) || empty( $salt['value'] ) || $today !== $salt['day'] ) {
		$salt = array(
			'day'   => $today,
			'value' => wp_generate_password( 40, false ),
		);
		update_option( 'raider_stats_salt', $salt, false );
	}
	return substr( hash( 'sha256', $salt['value'] . '|' . raider_stats_ip() . '|' . $ua ), 0, 16 );
}

/**
 * Is the requester a bot or a measurement tool.
 *
 * @param string $ua User agent.
 */
function raider_stats_is_bot( $ua ) {
	return '' === $ua || (bool) preg_match( '/bot|crawl|spider|slurp|lighthouse|pagespeed|headless|gtmetrix|pingdom|uptime|monitor|ptst|curl|wget|python|scrapy/i', $ua );
}

/**
 * Does the request carry a WordPress login cookie. Works in REST requests too,
 * where the user is not authenticated without a nonce.
 */
function raider_stats_has_login_cookie() {
	foreach ( array_keys( $_COOKIE ) as $name ) {
		if ( 0 === strpos( (string) $name, 'wordpress_logged_in_' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Host name without the leading www.
 *
 * @param string $url URL.
 */
function raider_stats_host( $url ) {
	$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	return (string) preg_replace( '/^www\./', '', $host );
}

/**
 * Traffic source from the referrer and the utm_source parameter.
 *
 * @param string $referrer document.referrer.
 * @param string $utm      utm_source.
 * @return array { source name, did the visitor come from inside the site }
 */
function raider_stats_source( $referrer, $utm ) {
	if ( '' !== $utm ) {
		return array( $utm, false );
	}
	$host = raider_stats_host( $referrer );
	if ( '' === $host ) {
		return array( '', false );
	}
	if ( raider_stats_host( home_url() ) === $host ) {
		return array( '', true );
	}
	$known = array(
		'gemini.google' => 'Gemini',
		'google'        => 'Google',
		'bing.'         => 'Bing',
		'duckduckgo'    => 'DuckDuckGo',
		'yahoo'         => 'Yahoo',
		'ecosia'        => 'Ecosia',
		'search.brave'  => 'Brave Search',
		'yandex'        => 'Yandex',
		'facebook'      => 'Facebook',
		'fb.com'        => 'Facebook',
		'instagram'     => 'Instagram',
		'linkedin'      => 'LinkedIn',
		'lnkd.in'       => 'LinkedIn',
		'youtube'       => 'YouTube',
		'youtu.be'      => 'YouTube',
		'tiktok'        => 'TikTok',
		'whatsapp'      => 'WhatsApp',
		'wa.me'         => 'WhatsApp',
		'chatgpt'       => 'ChatGPT',
		'openai'        => 'ChatGPT',
		'perplexity'    => 'Perplexity',
		'copilot'       => 'Copilot',
		'claude.ai'     => 'Claude',
	);
	foreach ( $known as $part => $name ) {
		if ( false !== strpos( $host, $part ) ) {
			return array( $name, false );
		}
	}
	if ( 't.co' === $host || 'x.com' === $host || false !== strpos( $host, 'twitter' ) ) {
		return array( 'X (Twitter)', false );
	}
	return array( $host, false );
}

/**
 * Check value that lets a browser update the duration of its own view only.
 *
 * @param int $id View id.
 */
function raider_stats_view_key( $id ) {
	return substr( hash_hmac( 'sha256', 'raider-view-' . (int) $id, wp_salt( 'nonce' ) ), 0, 12 );
}

/**
 * Clean text sent by the browser: printable characters only, limited length.
 *
 * @param mixed $value  Value.
 * @param int   $length Maximum length.
 */
function raider_stats_text( $value, $length ) {
	if ( ! is_scalar( $value ) ) {
		return '';
	}
	$text = sanitize_text_field( (string) $value );
	return function_exists( 'mb_substr' ) ? mb_substr( $text, 0, $length ) : substr( $text, 0, $length );
}

/**
 * Normalise a path sent by the browser.
 *
 * @param mixed $value Path.
 */
function raider_stats_path( $value ) {
	$path = raider_stats_text( $value, 191 );
	return ( '' === $path || '/' !== $path[0] ) ? '/' : $path;
}

/**
 * Store one contact event. Shared by the REST endpoint (link taps) and the
 * contact form handler (server side).
 *
 * @param string $type    Key from raider_stats_event_types().
 * @param string $target  What was tapped, e.g. the phone number.
 * @param string $path    Page the event happened on.
 * @param string $visitor Daily visitor hash.
 */
function raider_stats_record_event( $type, $target, $path, $visitor ) {
	if ( ! array_key_exists( $type, raider_stats_event_types() ) ) {
		return;
	}
	global $wpdb;
	$events = raider_stats_table( 'events' );
	$day    = wp_date( 'Y-m-d' );
	// Sanity cap per visitor per day: protects the table from abuse.
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $events WHERE day = %s AND visitor = %s", $day, $visitor ) );
	if ( $count >= 60 ) {
		return;
	}
	$wpdb->insert(
		$events,
		array(
			'created' => current_time( 'mysql' ),
			'day'     => $day,
			'visitor' => $visitor,
			'type'    => $type,
			'target'  => raider_stats_text( $target, 191 ),
			'path'    => raider_stats_path( $path ),
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s' )
	);
}

/* ---------- REST endpoint ---------- */

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'raider/v1',
			'/s',
			array(
				'methods'             => 'POST',
				'callback'            => 'raider_stats_receive',
				'permission_callback' => '__return_true',
			)
		);
	}
);

/**
 * Store one message from the browser script: a view (v), a duration update (d)
 * or a contact event (e).
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function raider_stats_receive( $request ) {
	$empty = new WP_REST_Response( null, 204 );
	$empty->header( 'Cache-Control', 'no-store' );

	if ( ! raider_stats_enabled() ) {
		return $empty;
	}
	$ua = substr( (string) $request->get_header( 'user_agent' ), 0, 400 );
	if ( raider_stats_is_bot( $ua ) || raider_stats_has_login_cookie() ) {
		return $empty;
	}
	// Only messages sent from this site's own pages.
	$origin = (string) $request->get_header( 'origin' );
	if ( '' !== $origin && raider_stats_host( $origin ) !== raider_stats_host( home_url() ) ) {
		return $empty;
	}
	$data = json_decode( (string) $request->get_body(), true );
	if ( ! is_array( $data ) || empty( $data['t'] ) || ! is_string( $data['t'] ) ) {
		return $empty;
	}

	global $wpdb;
	$views   = raider_stats_table( 'views' );
	$visitor = raider_stats_visitor( $ua );
	$day     = wp_date( 'Y-m-d' );
	$path    = raider_stats_path( isset( $data['p'] ) ? $data['p'] : '' );

	if ( 'v' === $data['t'] ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $views WHERE day = %s AND visitor = %s", $day, $visitor ) );
		if ( $count >= 300 ) {
			return $empty;
		}
		list( $source, $internal ) = raider_stats_source(
			isset( $data['r'] ) && is_string( $data['r'] ) ? substr( $data['r'], 0, 600 ) : '',
			isset( $data['us'] ) ? raider_stats_text( $data['us'], 100 ) : ''
		);
		$width  = isset( $data['w'] ) ? (int) $data['w'] : 0;
		$device = $width <= 0 ? '' : ( $width < 768 ? 'mobile' : ( $width < 1100 ? 'tablet' : 'desktop' ) );
		$wpdb->insert(
			$views,
			array(
				'created'  => current_time( 'mysql' ),
				'day'      => $day,
				'visitor'  => $visitor,
				'path'     => $path,
				'source'   => raider_stats_text( $source, 100 ),
				'campaign' => isset( $data['uc'] ) ? raider_stats_text( $data['uc'], 100 ) : '',
				'internal' => $internal ? 1 : 0,
				'device'   => $device,
				'duration' => 0,
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d' )
		);
		$id = (int) $wpdb->insert_id;
		if ( ! $id ) {
			return $empty;
		}
		$response = new WP_REST_Response(
			array(
				'i' => $id,
				'k' => raider_stats_view_key( $id ),
			),
			200
		);
		$response->header( 'Cache-Control', 'no-store' );
		return $response;
	}

	if ( 'd' === $data['t'] ) {
		$id       = isset( $data['i'] ) ? (int) $data['i'] : 0;
		$key      = isset( $data['k'] ) && is_string( $data['k'] ) ? $data['k'] : '';
		$duration = isset( $data['s'] ) ? min( 3600, max( 0, (int) $data['s'] ) ) : 0;
		if ( $id > 0 && $duration > 0 && hash_equals( raider_stats_view_key( $id ), $key ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( $wpdb->prepare( "UPDATE $views SET duration = %d WHERE id = %d AND duration < %d", $duration, $id, $duration ) );
		}
		return $empty;
	}

	if ( 'e' === $data['t'] ) {
		// The form is recorded on the server, never from the browser.
		$type = isset( $data['n'] ) && is_string( $data['n'] ) ? $data['n'] : '';
		if ( 'form' !== $type ) {
			raider_stats_record_event( $type, isset( $data['v'] ) ? $data['v'] : '', $path, $visitor );
		}
	}
	return $empty;
}

/* ---------- Contact form (server side) ---------- */

/**
 * Count a sent contact form. Fired by raider_handle_contact() once the
 * submission has passed validation.
 */
add_action(
	'raider_contact_submitted',
	function () {
		if ( ! raider_stats_enabled() || is_user_logged_in() ) {
			return;
		}
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ), 0, 400 ) : '';
		if ( raider_stats_is_bot( $ua ) ) {
			return;
		}
		$referer = wp_get_referer();
		$path    = $referer ? (string) wp_parse_url( $referer, PHP_URL_PATH ) : '';
		raider_stats_record_event( 'form', '', $path, raider_stats_visitor( $ua ) );
	}
);

/* ---------- Front end script ---------- */

/**
 * Print the collection script at the end of the page. It is tiny, inlined (no
 * extra request) and only runs once the page has loaded.
 */
add_action(
	'wp_footer',
	function () {
		if ( ! raider_stats_enabled() || is_user_logged_in() || is_customize_preview() || is_preview() ) {
			return;
		}
		$file = RAIDER_DIR . '/assets/js/analytics.js';
		if ( ! file_exists( $file ) ) {
			return;
		}
		$config = array( 'u' => esc_url_raw( rest_url( 'raider/v1/s' ) ) );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.AlternativeFunctions
		echo '<script>window.raiderStats=' . wp_json_encode( $config ) . ';' . trim( (string) file_get_contents( $file ) ) . "</script>\n";
	},
	99
);

/* ---------- Privacy policy suggestion ---------- */

/**
 * Suggested privacy policy text (Settings → Privacy → policy guide).
 */
add_action(
	'admin_init',
	function () {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}
		$text = '<p>Sivustolla on oma kävijäseuranta, jolla tilastoidaan sivujen katselut, liikenteen lähteet, sivuilla vietetty aika '
			. 'sekä yhteydenotot (puhelin-, sähköposti- ja WhatsApp-linkkien napautukset ja yhteydenottolomakkeen lähetykset).</p>'
			. '<p>Seuranta ei käytä evästeitä eikä tallenna mitään kävijän laitteelle. IP-osoitetta ei tallenneta. '
			. 'Saman päivän käynnit yhdistetään nimettömällä tunnisteella, joka lasketaan IP-osoitteesta, selaimen tunnisteesta ja '
			. 'päivittäin vaihtuvasta satunnaisluvusta. Tunnistetta ei voi yhdistää henkilöön eikä eri päivien käynteihin.</p>'
			. '<p>Tiedot tallennetaan vain tämän sivuston omaan tietokantaan, niitä ei luovuteta kolmansille osapuolille ja ne poistetaan 25 kuukauden kuluttua.</p>';
		wp_add_privacy_policy_content( 'Raider Production: kävijäseuranta', wp_kses_post( $text ) );
	}
);

if ( is_admin() ) {
	require_once RAIDER_DIR . '/inc/analytics-report.php';
}
