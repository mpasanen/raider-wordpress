<?php
/**
 * Homepage. Structure is fixed in PHP; text is editable via the
 * "Sisältö (Raider)" meta box on the Etusivu page.
 *
 * @package Raider
 */

if ( have_posts() ) {
	the_post(); // populate $post for raider_field()
	rewind_posts();
}

get_header();
?>

<section class="hero">
	<?php
	raider_picture(
		array(
			'name'   => 'hero',
			'widths' => array( 480, 768, 1024, 1440, 1920 ),
			'sizes'  => '100vw',
			'alt'    => '',
			'class'  => 'hero__bg',
			'eager'  => true,
			'dir'    => 'images/optimized',
			'width'  => 1920,
			'height' => 917,
		)
	);
	?>
	<div>
		<h1 class="hero__brand"><strong>Raider</strong> Production</h1>
		<p class="hero__tagline"><?php raider_the_field( 'hero_tagline', __( 'kuvaukset ja videotuotanto', 'raider' ) ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container">
		<h2 class="text-center"><?php raider_the_field( 'intro_heading', __( 'Miksi some-sisällöntuotanto on nykypäivänä tärkeää?', 'raider' ) ); ?></h2>
		<div style="max-width: 760px; margin-inline: auto; text-align: center;">
			<?php raider_the_field( 'intro_text', __( 'Some-sisällöntuotanto on nykypäivänä tärkeää, koska asiakkaat etsivät ja kuluttavat tietoa sosiaalisessa mediassa päivittäin. Laadukas ja johdonmukainen sisältö lisää näkyvyyttä, vahvistaa brändin luotettavuutta ja ohjaa potentiaalisia asiakkaita toimimaan. Ilman aktiivista sisällöntuotantoa yritys jää helposti kilpailijoidensa varjoon.', 'raider' ), true ); ?>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="container">
		<h2 class="text-center"><?php raider_the_field( 'services_heading', __( 'Tarjoamamme somepalvelut', 'raider' ) ); ?></h2>

		<article id="mainosvideo" class="service">
			<h2><?php raider_the_field( 'service_1_title', __( 'Brändi ja mainosvideot', 'raider' ) ); ?></h2>
			<?php raider_the_field( 'service_1_text', __( 'Tuotamme tehokasta materiaalia videon ja kuvan muodossa tehostamaan yrityksien mainontaa. Tehokkuus ja ketteryys on seikka millä erotumme joukosta — pidämme huolen että asiakkaamme saavat materiaalit sujuvasti nähtäville kuvauksien jälkeen.', 'raider' ), true ); ?>
		</article>

		<article id="somevideo" class="service">
			<h2><?php raider_the_field( 'service_2_title', __( 'Somevideot', 'raider' ) ); ?></h2>
			<?php raider_the_field( 'service_2_text', __( 'Jatkuva videoiden tuotanto takaa parempaa näkyvyyttä ja asiakkaittesi tavoitettavuutta sosiaalisessa mediassa. Tuotannon ei tarvitse olla näyteltyä ja käsikirjoitettua, vaan tuotamme autenttista materiaalia mihin on helppo samaistua.', 'raider' ), true ); ?>
		</article>

		<article id="someapu" class="service">
			<h2><?php raider_the_field( 'service_3_title', __( 'Someapu', 'raider' ) ); ?></h2>
			<?php raider_the_field( 'service_3_text', __( 'Autamme mielellään yrityksiä somen käyttöön ja perusasioiden hallintaan. Voimme kasvattaa markkinoinnin osaamista yrityksessäsi ja pitää näin kiinni siitä että vahvuutesi arjessa kasvaa ja samalla voit keskittyä vapaa-aikaasi entistä huolettomammin.', 'raider' ), true ); ?>
		</article>

		<div class="cta-wrap">
			<a class="btn" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php raider_the_field( 'cta_label', __( 'Ota yhteyttä ja pyydä tarjous', 'raider' ) ); ?></a>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<h2 class="text-center"><?php raider_the_field( 'process_heading', __( 'Miten brändi- ja mainosvideot tuotetaan?', 'raider' ) ); ?></h2>
		<p class="text-center" style="margin-bottom: 2rem;"><em><?php raider_the_field( 'process_subtitle', __( 'Mallina suosittu paketti: 10 × 30–60 s video', 'raider' ) ); ?></em></p>
		<div class="process" style="max-width: 760px; margin-inline: auto;">
			<p>
				<strong><?php raider_the_field( 'step_1_title', __( 'Yhteydenotto', 'raider' ) ); ?></strong>
				<?php raider_the_field( 'step_1_text', __( 'Kaikki alkaa kontaktoinnista, jonka aikana selvitetään tuotannon paketti. Tässä yhteydessä huomioidaan tarvittava kalusto, rekvisiitat sekä kuvauspaikka. Hintaan vaikuttaa puhtaasti käytettävä aika.', 'raider' ) ); ?>
			</p>
			<p>
				<strong><?php raider_the_field( 'step_2_title', __( 'Kuvauspäivä', 'raider' ) ); ?></strong>
				<?php raider_the_field( 'step_2_text', __( 'Kuvaus tapahtuu peruspaketissa yhden päivän aikana. Jos kuvauksien yhteydessä tulee tarpeita mieleen, pystymme lisäämään pakettiin muitakin kuvauksia. Esimerkiksi henkilökuvauksia ja brändikuvia.', 'raider' ) ); ?>
			</p>
			<p>
				<strong><?php raider_the_field( 'step_3_title', __( 'Editointi', 'raider' ) ); ?></strong>
				<?php raider_the_field( 'step_3_text', __( 'Editointi suoritetaan seuraavien päivien aikana valmiiksi. Jos toiveet ovat kokonaisuudessaan selvät, koko prosessi hoituu viikon sisään.', 'raider' ) ); ?>
			</p>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/logo-marquee' ); ?>

<section class="quote">
	<div class="container">
		<div class="quote__inner">
			<?php
			raider_picture(
				array(
					'name'   => 'tomppa-quote',
					'widths' => array( 400, 600, 800 ),
					'sizes'  => '(max-width: 720px) 100vw, 40vw',
					'alt'    => 'Tommi Parhiala kameran kanssa',
					'class'  => 'quote__img',
					'dir'    => 'images/optimized',
					'width'  => 800,
					'height' => 588,
				)
			);
			?>
			<blockquote class="quote__text">
				<?php raider_the_field( 'quote_text', __( '”Olen huomannut että visuaaliselle silmälle on usein tarvetta, mutta silti tärkeintä on tarttua työhön ja saattaa se valmiiksi.”', 'raider' ) ); ?>
				<cite class="quote__cite">– <?php raider_the_field( 'quote_author', __( 'Tomppa', 'raider' ) ); ?></cite>
			</blockquote>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="container text-center">
		<h2><?php raider_the_field( 'contact_heading', __( 'Raider Production tarjoaa somepalvelua niin kuvauksiin ja videotuotantoon kuin somen ylläpitoonkin.', 'raider' ) ); ?></h2>
		<h3 style="margin-top:2rem;"><?php raider_the_field( 'contact_sub', __( 'Jätä yhteydenottopyyntö', 'raider' ) ); ?></h3>
		<?php raider_contact_form( false ); ?>
	</div>
</section>

<?php
get_footer();
