<?php
/**
 * Minimal native contact form handler.
 *
 * No external plugin needed. Sends to admin email. Nonce + CSRF safe.
 *
 * Spam protection (no external services):
 *   1. Honeypot field hidden with a CSS class.
 *   2. JS token: hidden fields are filled by an inline script when the visitor
 *      touches the form, so plain HTTP bots never send them.
 *   3. Time trap: signed render timestamp, submissions faster than
 *      RAIDER_FORM_MIN_SECONDS are dropped.
 *   4. Rate limit per IP address.
 *   5. WordPress "Disallowed Comment Keys" list (Settings → Discussion).
 *
 * Rejected submissions are never mailed. They are kept for
 * RAIDER_FORM_LOG_DAYS days under Tools → Hylätyt yhteydenotot so that false
 * positives can be spotted.
 *
 * Note: the nonce and the signed timestamp are rendered into the page, so the
 * pages that contain the form must not be served from a full-page cache.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

const RAIDER_FORM_ACTION       = 'raider_contact_submit';
const RAIDER_FORM_MIN_SECONDS  = 3;
const RAIDER_FORM_MAX_PER_HOUR = 3;
const RAIDER_FORM_LOG_OPTION   = 'raider_contact_rejected';
const RAIDER_FORM_LOG_DAYS     = 14;
const RAIDER_FORM_LOG_MAX_ROWS = 100;

add_action( 'admin_post_nopriv_' . RAIDER_FORM_ACTION, 'raider_handle_contact' );
add_action( 'admin_post_' . RAIDER_FORM_ACTION, 'raider_handle_contact' );
add_action( 'admin_menu', 'raider_form_log_menu' );

/**
 * Short keyed signature for the form tokens.
 */
function raider_form_sign( $data ) {
	return substr( wp_hash( 'raider_form|' . $data, 'nonce' ), 0, 20 );
}

/**
 * Why a submission should be dropped as spam, or '' when it looks human.
 */
function raider_form_spam_reason( $name, $email, $message ) {
	// Honeypot: real visitors never see this field.
	if ( ! empty( $_POST['subject'] ) ) {
		return 'honeypot';
	}

	$ts     = isset( $_POST['raider_t'] ) ? sanitize_text_field( wp_unslash( $_POST['raider_t'] ) ) : '';
	$sig    = isset( $_POST['raider_s'] ) ? sanitize_text_field( wp_unslash( $_POST['raider_s'] ) ) : '';
	$js_key = isset( $_POST['raider_k'] ) ? sanitize_text_field( wp_unslash( $_POST['raider_k'] ) ) : '';

	// The inline script copies these into the form. No script, no values.
	if ( '' === $ts || '' === $sig || '' === $js_key ) {
		return 'no_js';
	}
	if ( ! ctype_digit( $ts ) || ! hash_equals( raider_form_sign( 't|' . $ts ), $sig ) || ! hash_equals( raider_form_sign( 'k|' . $ts ), $js_key ) ) {
		return 'bad_token';
	}
	if ( time() - (int) $ts < RAIDER_FORM_MIN_SECONDS ) {
		return 'too_fast';
	}

	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

	if ( '' !== $ip ) {
		$key = 'raider_cf_' . substr( wp_hash( 'rate|' . $ip, 'nonce' ), 0, 24 );
		$hit = get_transient( $key );
		if ( ! is_array( $hit ) || ! isset( $hit['start'], $hit['count'] ) || $hit['start'] + HOUR_IN_SECONDS < time() ) {
			$hit = array(
				'start' => time(),
				'count' => 0,
			);
		}
		++$hit['count'];
		set_transient( $key, $hit, HOUR_IN_SECONDS );
		if ( $hit['count'] > RAIDER_FORM_MAX_PER_HOUR ) {
			return 'rate_limit';
		}
	}

	// Settings → Discussion → Disallowed Comment Keys.
	if ( function_exists( 'wp_check_comment_disallowed_list' ) && wp_check_comment_disallowed_list( $name, $email, '', $message, $ip, $ua ) ) {
		return 'blocklist';
	}

	return '';
}

/**
 * Stored rejections, newest last, without rows older than the retention time.
 */
function raider_form_log_rows() {
	$rows = get_option( RAIDER_FORM_LOG_OPTION, array() );
	if ( ! is_array( $rows ) ) {
		return array();
	}
	$cutoff = time() - RAIDER_FORM_LOG_DAYS * DAY_IN_SECONDS;
	$fresh  = array();
	foreach ( $rows as $row ) {
		if ( is_array( $row ) && isset( $row['time'] ) && $row['time'] >= $cutoff ) {
			$fresh[] = $row;
		}
	}
	return $fresh;
}

/**
 * Remember a rejected submission. No IP address is stored.
 */
function raider_form_log_reject( $reason, $name, $email, $message ) {
	$rows   = raider_form_log_rows();
	$rows[] = array(
		'time'    => time(),
		'reason'  => $reason,
		'name'    => mb_substr( $name, 0, 100 ),
		'email'   => mb_substr( $email, 0, 150 ),
		'message' => mb_substr( $message, 0, 1000 ),
	);
	$rows   = array_slice( $rows, -RAIDER_FORM_LOG_MAX_ROWS );
	update_option( RAIDER_FORM_LOG_OPTION, $rows, false );
}

function raider_handle_contact() {
	check_admin_referer( RAIDER_FORM_ACTION );

	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

	// Spam — drop silently so bots learn nothing, but keep a copy in the log.
	$reason = raider_form_spam_reason( $name, $email, $message );
	if ( '' !== $reason ) {
		raider_form_log_reject( $reason, $name, $email, $message );
		wp_safe_redirect( home_url( '/kiitos/' ) );
		exit;
	}

	if ( '' === $name || '' === $email || '' === $message || ! is_email( $email ) ) {
		wp_safe_redirect( wp_get_referer() ?: home_url( '/ota-yhteytta/' ) );
		exit;
	}

	$to      = get_option( 'admin_email' );
	$subject = sprintf( '[Raider] Yhteydenotto: %s', $name );
	$body    = sprintf(
		"Nimi: %s\nSähköposti: %s\nPuhelin: %s\n\nViesti:\n%s\n",
		$name,
		$email,
		$phone,
		$message
	);
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( home_url( '/kiitos/' ) );
	exit;
}

/**
 * Render the contact form. Used in front-page.php and page-ota-yhteytta.php.
 */
function raider_contact_form( $with_phone = true ) {
	static $printed_style = false;
	$ts = (string) time();

	if ( ! $printed_style ) {
		$printed_style = true;
		echo '<style>.form__alt{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}</style>' . "\n";
	}
	?>
<form class="form js-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-t="<?php echo esc_attr( $ts ); ?>" data-s="<?php echo esc_attr( raider_form_sign( 't|' . $ts ) ); ?>" data-k="<?php echo esc_attr( raider_form_sign( 'k|' . $ts ) ); ?>">
	<input type="hidden" name="action" value="<?php echo esc_attr( RAIDER_FORM_ACTION ); ?>">
	<?php wp_nonce_field( RAIDER_FORM_ACTION ); ?>
	<input type="hidden" name="raider_t" value="" autocomplete="off">
	<input type="hidden" name="raider_s" value="" autocomplete="off">
	<input type="hidden" name="raider_k" value="" autocomplete="off">
	<p class="form__alt" aria-hidden="true">
		<label>Aihe <input type="text" name="subject" value="" tabindex="-1" autocomplete="off"></label>
	</p>
	<noscript><p><?php esc_html_e( 'Lomakkeen lähettäminen vaatii JavaScriptin. Voit myös soittaa tai lähettää sähköpostia.', 'raider' ); ?></p></noscript>
	<div class="form__row">
		<div>
			<label for="raider-name"><?php esc_html_e( 'Nimi', 'raider' ); ?></label>
			<input id="raider-name" name="name" type="text" autocomplete="name" required>
		</div>
		<div>
			<label for="raider-email"><?php esc_html_e( 'Sähköpostiosoite', 'raider' ); ?></label>
			<input id="raider-email" name="email" type="email" autocomplete="email" required>
		</div>
	</div>
	<?php if ( $with_phone ) : ?>
	<div>
		<label for="raider-phone"><?php esc_html_e( 'Puhelin (vapaaehtoinen)', 'raider' ); ?></label>
		<input id="raider-phone" name="phone" type="tel" autocomplete="tel">
	</div>
	<?php endif; ?>
	<div>
		<label for="raider-message"><?php esc_html_e( 'Viesti', 'raider' ); ?></label>
		<textarea id="raider-message" name="message" required></textarea>
	</div>
	<div class="text-center">
		<button class="btn" type="submit"><?php esc_html_e( 'Lähetä', 'raider' ); ?></button>
	</div>
</form>
<script>
(function(){var fs=document.querySelectorAll('form.js-contact-form:not([data-armed])');for(var i=0;i<fs.length;i++){(function(f){f.setAttribute('data-armed','1');function arm(){['t','s','k'].forEach(function(n){var e=f.querySelector('input[name="raider_'+n+'"]');if(e){e.value=f.getAttribute('data-'+n)||'';}});}['focusin','keydown','pointerdown','touchstart','input','submit'].forEach(function(ev){f.addEventListener(ev,arm,true);});})(fs[i]);}})();
</script>
	<?php
}

/**
 * Tools → Hylätyt yhteydenotot.
 */
function raider_form_log_menu() {
	add_management_page(
		'Hylätyt yhteydenotot',
		'Hylätyt yhteydenotot',
		'manage_options',
		'raider-form-log',
		'raider_form_log_page'
	);
}

function raider_form_log_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$labels = array(
		'honeypot'   => 'Piilokenttä täytetty',
		'no_js'      => 'JavaScript-tunniste puuttuu',
		'bad_token'  => 'Virheellinen tunniste',
		'too_fast'   => 'Lähetetty liian nopeasti',
		'rate_limit' => 'Liian monta lähetystä tunnissa',
		'blocklist'  => 'Kielletty sana tai lause',
	);
	$rows   = array_reverse( raider_form_log_rows() );
	?>
<div class="wrap">
	<h1>Hylätyt yhteydenotot</h1>
	<p>
		Yhteydenottolomakkeen roskapostisuodatus on pysäyttänyt nämä viestit, eikä niitä ole lähetetty sähköpostiin.
		Viestit säilyvät <?php echo esc_html( (string) RAIDER_FORM_LOG_DAYS ); ?> päivää. Jos listalla on oikea yhteydenotto, suodatusta on syytä löysätä.
	</p>
	<?php if ( empty( $rows ) ) : ?>
	<p><strong>Ei hylättyjä viestejä.</strong></p>
	<?php else : ?>
	<table class="widefat striped">
		<thead>
			<tr><th>Aika</th><th>Syy</th><th>Nimi</th><th>Sähköposti</th><th>Viesti</th></tr>
		</thead>
		<tbody>
			<?php foreach ( $rows as $row ) : ?>
			<tr>
				<td><?php echo esc_html( wp_date( 'j.n.Y H.i', (int) $row['time'] ) ); ?></td>
				<td><?php echo esc_html( $labels[ $row['reason'] ?? '' ] ?? ( $row['reason'] ?? '' ) ); ?></td>
				<td><?php echo esc_html( $row['name'] ?? '' ); ?></td>
				<td><?php echo esc_html( $row['email'] ?? '' ); ?></td>
				<td><?php echo nl2br( esc_html( $row['message'] ?? '' ) ); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
	<?php
}
