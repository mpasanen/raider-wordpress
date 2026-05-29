<?php
/**
 * Template for the "Ota yhteyttä" page.
 *
 * @package Raider
 */

if ( have_posts() ) { the_post(); rewind_posts(); }
get_header();
?>
<section class="section">
	<div class="container">
		<div class="grid-2">
			<div>
				<h1><?php the_title(); ?></h1>
				<h2 style="color: var(--c-red); margin-top: 0;">Tommi Parhiala</h2>
				<?php raider_the_field( 'lead_text', __( 'Lyhytvideot, mainosvideot ja someapu yrityksellesi. Tehdään yhdessä tehokasta sisältöä, joka tuottaa tulosta.', 'raider' ), true ); ?>
				<address style="font-style: normal; font-size: 1.1rem; line-height: 1.8;">
					<strong>Puhelin:</strong> <a href="tel:+358407245604">040 7245604</a><br>
					<strong>Sähköposti:</strong> <a href="mailto:tommi.parhiala@gmail.com">tommi.parhiala@gmail.com</a><br>
					<strong>Osoite:</strong> Päivämiehenkuja 10, 41340 Laukaa<br>
					<strong>Y-tunnus:</strong> 3539320-3
				</address>
			</div>
			<?php
			raider_picture(
				array(
					'name'   => 'contact',
					'widths' => array( 360, 540, 720, 960 ),
					'sizes'  => '(max-width: 720px) 100vw, 50vw',
					'alt'    => 'Tommi Parhiala',
					'dir'    => 'images/optimized',
					'width'  => 960,
					'height' => 1269,
				)
			);
			?>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="container">
		<h2 class="text-center"><?php raider_the_field( 'form_heading', __( 'Boostia somemarkkinointiin', 'raider' ) ); ?></h2>
		<div class="text-center" style="max-width: 640px; margin-inline: auto;">
			<?php raider_the_field( 'form_subtext', __( 'Kerro lyhyesti, mitä tarvitset — palaan asiaan yleensä saman päivän aikana.', 'raider' ), true ); ?>
		</div>
		<?php raider_contact_form( true ); ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<h2 class="text-center"><?php esc_html_e( 'Sisällöntuotanto', 'raider' ); ?></h2>
		<div class="grid-3">
			<article class="service"><h3><?php esc_html_e( 'Brändi ja mainosvideot', 'raider' ); ?></h3><p><?php esc_html_e( 'Tehokasta video- ja kuvamateriaalia mainontaan ja brändäykseen.', 'raider' ); ?></p><a href="<?php echo esc_url( home_url( '/#mainosvideo' ) ); ?>">Lue lisää →</a></article>
			<article class="service"><h3><?php esc_html_e( 'Somevideot', 'raider' ); ?></h3><p><?php esc_html_e( 'Jatkuvaa, autenttista somevideomateriaalia näkyvyyden kasvattamiseen.', 'raider' ); ?></p><a href="<?php echo esc_url( home_url( '/#somevideo' ) ); ?>">Lue lisää →</a></article>
			<article class="service"><h3><?php esc_html_e( 'Someapu', 'raider' ); ?></h3><p><?php esc_html_e( 'Apua somen perusasioihin ja yrityksen markkinoinnin kehittämiseen.', 'raider' ); ?></p><a href="<?php echo esc_url( home_url( '/#someapu' ) ); ?>">Lue lisää →</a></article>
		</div>
	</div>
</section>

<?php
get_footer();
