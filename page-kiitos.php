<?php
/**
 * Template for the "Kiitos" page after a contact form submission.
 *
 * @package Raider
 */

get_header();
?>
<section class="section">
	<div class="container text-center" style="max-width: 640px;">
		<h1><?php esc_html_e( 'Kiitos viestistäsi!', 'raider' ); ?></h1>
		<p><?php esc_html_e( 'Otamme sinuun yhteyttä yleensä saman päivän aikana.', 'raider' ); ?></p>
		<p style="margin-top: 2rem;"><a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Takaisin etusivulle', 'raider' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
