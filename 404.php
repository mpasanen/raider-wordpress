<?php
/**
 * 404 template.
 *
 * @package Raider
 */

get_header();
?>
<section class="section">
	<div class="container text-center" style="max-width: 640px;">
		<h1>404 — <?php esc_html_e( 'sivua ei löydy', 'raider' ); ?></h1>
		<p><?php esc_html_e( 'Etsimääsi sivua ei valitettavasti löytynyt.', 'raider' ); ?></p>
		<p style="margin-top: 2rem;"><a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Etusivulle', 'raider' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
