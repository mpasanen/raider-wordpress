<?php
/**
 * Template for the "Meistä" page.
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
				<?php raider_the_field( 'about_p1', __( 'Raider Production Oy syntyi intohimosta perhokalastuksen kuvaamiseen ja sivutoimisen yritystoiminnan kehittämiseen Suomen luonnossa. Perustaja Tommilla on noin kymmenen vuoden kokemus B2B-myynnistä, ja yrityksen ytimessä on aito kumppanuus asiakkaiden kanssa.', 'raider' ), true ); ?>
				<?php raider_the_field( 'about_p2', __( 'Tavoitteenamme on tuottaa tehokkaita, laadukkaita brändi- ja lyhytvideoita yritysten tarpeisiin sekä tarjota matalan kynnyksen someapua. Hoidamme visuaalisen viestinnän ja teemme video- ja kuvatuotannosta mukavan vaihtelun arkeen.', 'raider' ), true ); ?>
				<?php raider_the_field( 'about_p3', __( 'Videoiden lisäksi toteutamme myös nettisivuprojekteja, logojen vektoroinnit ja logosuunnittelua. Nämä visuaalisesti vaativat työt ovat meille mieluisia haasteita — ymmärrämme, että niiden takana on aina jotain tunteellisesti merkittävää.', 'raider' ), true ); ?>
				<p style="margin-top: 2rem;"><a class="btn" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Ota yhteyttä', 'raider' ); ?></a></p>
			</div>
			<?php
			// Photo of Tommi / the company. The page's featured image (Artikkelikuva)
			// overrides the bundled photo, so it can be swapped from the admin.
			$raider_about_sizes = '(max-width: 720px) 100vw, 50vw';
			if ( has_post_thumbnail() ) {
				$raider_about_id  = get_post_thumbnail_id();
				$raider_about_alt = trim( (string) get_post_meta( $raider_about_id, '_wp_attachment_image_alt', true ) );
				echo wp_get_attachment_image(
					$raider_about_id,
					'large',
					false,
					array(
						'sizes'   => $raider_about_sizes,
						'loading' => 'lazy',
						'alt'     =>'' !== $raider_about_alt ? $raider_about_alt : 'Tommi Parhiala, Raider Production',
					)
				);
			} else {
				raider_picture(
					array(
						'name'   => 'contact',
						'widths' => array( 360, 540, 720, 960 ),
						'sizes'  => $raider_about_sizes,
						'alt'    => 'Tommi Parhiala valmistelee kuvauskalustoa',
						'dir'    => 'images/optimized',
						'width'  => 960,
						'height' => 1269,
					)
				);
			}
			?>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="container text-center">
		<h2><?php raider_the_field( 'cta_heading', __( 'Tehdäänkö yhdessä jotain hienoa?', 'raider' ) ); ?></h2>
		<p><?php raider_the_field( 'cta_text', __( 'Soita, sähköpostita tai jätä yhteydenotto.', 'raider' ) ); ?></p>
		<p style="margin-top: 1rem;">
			<a class="btn btn--ghost" href="tel:+358407245604">040 7245604</a>
			<span style="display:inline-block; width: 1rem;"></span>
			<a class="btn" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Lähetä viesti', 'raider' ); ?></a>
		</p>
	</div>
</section>

<?php
get_footer();
