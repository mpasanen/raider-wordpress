<?php
/**
 * Template for the "Meistä" page.
 *
 * @package Raider
 */

get_header();
?>
<section class="section">
	<div class="container">
		<div class="grid-2">
			<div>
				<h1><?php esc_html_e( 'Ketä olemme', 'raider' ); ?></h1>
				<p><?php esc_html_e( 'Raider Production Oy syntyi intohimosta perhokalastuksen kuvaamiseen ja sivutoimisen yritystoiminnan kehittämiseen Suomen luonnossa. Perustaja Tommilla on noin kymmenen vuoden kokemus B2B-myynnistä, ja yrityksen ytimessä on aito kumppanuus asiakkaiden kanssa.', 'raider' ); ?></p>
				<p><?php esc_html_e( 'Tavoitteenamme on tuottaa tehokkaita, laadukkaita brändi- ja lyhytvideoita yritysten tarpeisiin sekä tarjota matalan kynnyksen someapua. Hoidamme visuaalisen viestinnän ja teemme video- ja kuvatuotannosta mukavan vaihtelun arkeen.', 'raider' ); ?></p>
				<p><?php esc_html_e( 'Videoiden lisäksi toteutamme myös nettisivuprojekteja, logojen vektoroinnit ja logosuunnittelua. Nämä visuaalisesti vaativat työt ovat meille mieluisia haasteita — ymmärrämme, että niiden takana on aina jotain tunteellisesti merkittävää.', 'raider' ); ?></p>
				<p style="margin-top: 2rem;"><a class="btn" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Ota yhteyttä', 'raider' ); ?></a></p>
			</div>
			<?php
			raider_picture(
				array(
					'name'   => 'about',
					'widths' => array( 360, 540, 720, 960 ),
					'sizes'  => '(max-width: 720px) 100vw, 50vw',
					'alt'    => 'Kuvauskalustoa pöydällä',
					'dir'    => 'images/optimized',
					'width'  => 960,
					'height' => 640,
				)
			);
			?>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="container text-center">
		<h2><?php esc_html_e( 'Tehdäänkö yhdessä jotain hienoa?', 'raider' ); ?></h2>
		<p><?php esc_html_e( 'Soita, sähköpostita tai jätä yhteydenotto.', 'raider' ); ?></p>
		<p style="margin-top: 1rem;">
			<a class="btn btn--ghost" href="tel:+358407245604">040 7245604</a>
			<span style="display:inline-block; width: 1rem;"></span>
			<a class="btn" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Lähetä viesti', 'raider' ); ?></a>
		</p>
	</div>
</section>

<?php
get_footer();
