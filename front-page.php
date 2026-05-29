<?php
/**
 * Homepage. Always uses static content (mirrors raider.fi structure).
 *
 * @package Raider
 */

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
		<p class="hero__tagline"><?php esc_html_e( 'kuvaukset ja videotuotanto', 'raider' ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container">
		<h2 class="text-center"><?php esc_html_e( 'Miksi some-sisällöntuotanto on nykypäivänä tärkeää?', 'raider' ); ?></h2>
		<p style="max-width: 760px; margin-inline: auto; text-align: center;">
			<?php esc_html_e( 'Some-sisällöntuotanto on nykypäivänä tärkeää, koska asiakkaat etsivät ja kuluttavat tietoa sosiaalisessa mediassa päivittäin. Laadukas ja johdonmukainen sisältö lisää näkyvyyttä, vahvistaa brändin luotettavuutta ja ohjaa potentiaalisia asiakkaita toimimaan. Ilman aktiivista sisällöntuotantoa yritys jää helposti kilpailijoidensa varjoon.', 'raider' ); ?>
		</p>
	</div>
</section>

<section class="section section--alt">
	<div class="container">
		<h2 class="text-center"><?php esc_html_e( 'Tarjoamamme somepalvelut', 'raider' ); ?></h2>

		<article id="mainosvideo" class="service">
			<h2><?php esc_html_e( 'Brändi ja mainosvideot', 'raider' ); ?></h2>
			<p><?php esc_html_e( 'Tuotamme tehokasta materiaalia videon ja kuvan muodossa tehostamaan yrityksien mainontaa. Tehokkuus ja ketteryys on seikka millä erotumme joukosta — pidämme huolen että asiakkaamme saavat materiaalit sujuvasti nähtäville kuvauksien jälkeen.', 'raider' ); ?></p>
		</article>

		<article id="somevideo" class="service">
			<h2><?php esc_html_e( 'Somevideot', 'raider' ); ?></h2>
			<p><?php esc_html_e( 'Jatkuva videoiden tuotanto takaa parempaa näkyvyyttä ja asiakkaittesi tavoitettavuutta sosiaalisessa mediassa. Tuotannon ei tarvitse olla näyteltyä ja käsikirjoitettua, vaan tuotamme autenttista materiaalia mihin on helppo samaistua.', 'raider' ); ?></p>
		</article>

		<article id="someapu" class="service">
			<h2><?php esc_html_e( 'Someapu', 'raider' ); ?></h2>
			<p><?php esc_html_e( 'Autamme mielellään yrityksiä somen käyttöön ja perusasioiden hallintaan. Voimme kasvattaa markkinoinnin osaamista yrityksessäsi ja pitää näin kiinni siitä että vahvuutesi arjessa kasvaa ja samalla voit keskittyä vapaa-aikaasi entistä huolettomammin.', 'raider' ); ?></p>
		</article>

		<div class="cta-wrap">
			<a class="btn" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Ota yhteyttä ja pyydä tarjous', 'raider' ); ?></a>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<h2 class="text-center"><?php esc_html_e( 'Miten brändi- ja mainosvideot tuotetaan?', 'raider' ); ?></h2>
		<p class="text-center" style="margin-bottom: 2rem;"><em><?php esc_html_e( 'Mallina suosittu paketti: 10 × 30–60 s video', 'raider' ); ?></em></p>
		<div class="process" style="max-width: 760px; margin-inline: auto;">
			<p><strong><?php esc_html_e( 'Yhteydenotto', 'raider' ); ?></strong><?php esc_html_e( 'Kaikki alkaa kontaktoinnista, jonka aikana selvitetään tuotannon paketti. Tässä yhteydessä huomioidaan tarvittava kalusto, rekvisiitat sekä kuvauspaikka. Hintaan vaikuttaa puhtaasti käytettävä aika.', 'raider' ); ?></p>
			<p><strong><?php esc_html_e( 'Kuvauspäivä', 'raider' ); ?></strong><?php esc_html_e( 'Kuvaus tapahtuu peruspaketissa yhden päivän aikana. Jos kuvauksien yhteydessä tulee tarpeita mieleen, pystymme lisäämään pakettiin muitakin kuvauksia. Esimerkiksi henkilökuvauksia ja brändikuvia.', 'raider' ); ?></p>
			<p><strong><?php esc_html_e( 'Editointi', 'raider' ); ?></strong><?php esc_html_e( 'Editointi suoritetaan seuraavien päivien aikana valmiiksi. Jos toiveet ovat kokonaisuudessaan selvät, koko prosessi hoituu viikon sisään.', 'raider' ); ?></p>
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
				<?php esc_html_e( '”Olen huomannut että visuaaliselle silmälle on usein tarvetta, mutta silti tärkeintä on tarttua työhön ja saattaa se valmiiksi.”', 'raider' ); ?>
				<cite class="quote__cite">– Tomppa</cite>
			</blockquote>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="container text-center">
		<h2><?php esc_html_e( 'Raider Production tarjoaa somepalvelua niin kuvauksiin ja videotuotantoon kuin somen ylläpitoonkin.', 'raider' ); ?></h2>
		<h3 style="margin-top:2rem;"><?php esc_html_e( 'Jätä yhteydenottopyyntö', 'raider' ); ?></h3>
		<?php raider_contact_form( false ); ?>
	</div>
</section>

<?php
get_footer();
