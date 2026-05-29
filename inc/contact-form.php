<?php
/**
 * Minimal native contact form handler.
 *
 * No external plugin needed. Sends to admin email. Honeypot + nonce + CSRF safe.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

const RAIDER_FORM_ACTION = 'raider_contact_submit';

add_action( 'admin_post_nopriv_' . RAIDER_FORM_ACTION, 'raider_handle_contact' );
add_action( 'admin_post_' . RAIDER_FORM_ACTION, 'raider_handle_contact' );

function raider_handle_contact() {
	check_admin_referer( RAIDER_FORM_ACTION );

	// Honeypot — silently drop bots.
	if ( ! empty( $_POST['raider_hp'] ) ) {
		wp_safe_redirect( home_url( '/kiitos/' ) );
		exit;
	}

	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

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
	?>
<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<input type="hidden" name="action" value="<?php echo esc_attr( RAIDER_FORM_ACTION ); ?>">
	<?php wp_nonce_field( RAIDER_FORM_ACTION ); ?>
	<p style="position:absolute;left:-9999px;" aria-hidden="true">
		<label>Älä täytä: <input type="text" name="raider_hp" tabindex="-1" autocomplete="off"></label>
	</p>
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
	<?php
}
