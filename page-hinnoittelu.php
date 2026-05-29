<?php
/**
 * Template for the "Hinnoittelu" page (slug-based: page-hinnoittelu.php).
 *
 * @package Raider
 */

get_header();
?>
<section class="section">
	<div class="container">
		<h1 class="text-center"><?php esc_html_e( 'Hinnoittelu', 'raider' ); ?></h1>
		<p class="text-center" style="max-width: 640px; margin-inline: auto;">
			<?php esc_html_e( 'Selkeät paketit ja läpinäkyvä hinnoittelu. Kaikki hinnat alv 0 %.', 'raider' ); ?>
		</p>

		<div class="pricing-group">
			<h2>Videotuotanto</h2>
			<div class="price-grid">
				<article class="price-card">
					<h3>10 kpl lyhytvideoita</h3>
					<div class="price">1 800 € <small>(alv 0)</small></div>
					<ul>
						<li>10 julkaisuvalmista lyhytvideota</li>
						<li>Kuvaus + editointi</li>
						<li>Matkakulut 300 km säteellä (ei majoitusta)</li>
						<li>Sisällön suunnittelu yhdessä lyhyesti, jotta tyyli ja viesti osuvat kohdalleen</li>
					</ul>
					<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Pyydä tarjous', 'raider' ); ?></a>
				</article>
				<article class="price-card">
					<h3>Jatkuva tuotanto 3 kk</h3>
					<div class="price">4 100 € <small>(alv 0)</small></div>
					<ul>
						<li>25 lyhytvideota (3 kk tuotanto)</li>
						<li>Kuvaus + editointi</li>
						<li>Matkakulut 300 km säteellä</li>
					</ul>
					<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Pyydä tarjous', 'raider' ); ?></a>
				</article>
			</div>
		</div>

		<div class="pricing-group">
			<h2>Asiakaskokemusvideo</h2>
			<div class="price-grid">
				<article class="price-card">
					<h3>Lyhytvideo</h3>
					<div class="price">300 € <small>/kpl</small></div>
					<ul>
						<li>Kuvaus + editointi</li>
						<li>Napakka asiakaskokemus lyhytvideon muodossa</li>
						<li>Mainio Reels / TikTok / Shorts -käyttöön</li>
					</ul>
					<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Pyydä tarjous', 'raider' ); ?></a>
				</article>
				<article class="price-card">
					<h3>Perinteinen 2–3 min</h3>
					<div class="price">800 € <small>/kpl</small></div>
					<ul>
						<li>Kuvaus + editointi</li>
						<li>Syvempää tarinointia ja enemmän sisältöä</li>
						<li>Sopii nettisivuille, myyntiin ja referenssikäyttöön</li>
					</ul>
					<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Pyydä tarjous', 'raider' ); ?></a>
				</article>
			</div>
		</div>

		<div class="pricing-group">
			<h2>Someapu</h2>
			<div class="price-grid">
				<article class="price-card">
					<h3>Peruspaketti</h3>
					<div class="price">200 € <small>/kk (alv 0)</small></div>
					<ul>
						<li>2 julkaisua viikossa</li>
						<li>2 h / vko yrityksen viestinnän kehitystä</li>
						<li>Pois lukien kommenttien ja DM-viestien vastaaminen</li>
					</ul>
					<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Pyydä tarjous', 'raider' ); ?></a>
				</article>
			</div>
		</div>

		<div class="cta-wrap">
			<a class="btn" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Ota yhteyttä ja pyydä tarjous', 'raider' ); ?></a>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/logo-marquee' ); ?>

<?php
get_footer();
