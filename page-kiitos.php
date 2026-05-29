<?php
/**
 * Template for the "Kiitos" page after a contact form submission.
 *
 * @package Raider
 */

if ( have_posts() ) { the_post(); rewind_posts(); }
get_header();
?>
<section class="section">
	<div class="container text-center" style="max-width: 640px;">
		<h1><?php esc_html_e( 'Kiitos viestistäsi!', 'raider' ); ?></h1>
		<?php raider_the_field( 'message', __( 'Otamme sinuun yhteyttä yleensä saman päivän aikana.', 'raider' ), true ); ?>
		<p style="margin-top: 2rem;"><a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Takaisin etusivulle', 'raider' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
