<?php
/**
 * Auto-scrolling client logos marquee. Pure CSS animation, no JS.
 *
 * Logos come from the "Asiakaslogot" custom post type (Admin → Asiakaslogot).
 * If none exist yet, the bundled defaults are used as a graceful fallback.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

$logos = raider_get_client_logos();
if ( empty( $logos ) ) {
	return;
}
?>
<section class="marquee" aria-label="<?php esc_attr_e( 'Asiakkaita', 'raider' ); ?>">
	<div class="marquee__track">
		<?php
		foreach ( $logos as $l ) {
			raider_render_logo( $l, false );
		}
		// Seamless loop duplicate.
		foreach ( $logos as $l ) {
			raider_render_logo( $l, true );
		}
		?>
	</div>
</section>
