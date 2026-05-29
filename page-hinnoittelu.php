<?php
/**
 * Template for the "Hinnoittelu" page.
 *
 * Pricing cards come from the "Hinnoittelu" custom post type
 * (Admin → Hinnoittelu). When no entries exist yet, the bundled
 * defaults are used as a graceful fallback.
 *
 * @package Raider
 */

if ( have_posts() ) {
	the_post();
	rewind_posts();
}
get_header();

$groups = raider_get_pricing_cards();
?>
<section class="section">
	<div class="container">
		<h1 class="text-center"><?php the_title(); ?></h1>
		<div class="text-center" style="max-width: 640px; margin-inline: auto;">
			<?php raider_the_field( 'intro_text', __( 'Selkeät paketit ja läpinäkyvä hinnoittelu. Kaikki hinnat alv 0 %.', 'raider' ), true ); ?>
		</div>

		<?php foreach ( $groups as $group_name => $cards ) : ?>
			<div class="pricing-group">
				<h2><?php echo esc_html( $group_name ); ?></h2>
				<div class="price-grid">
					<?php foreach ( $cards as $card ) : ?>
						<article class="price-card">
							<h3><?php echo esc_html( $card['title'] ); ?></h3>
							<?php if ( ! empty( $card['price'] ) ) : ?>
								<div class="price">
									<?php echo esc_html( $card['price'] ); ?>
									<?php if ( ! empty( $card['unit'] ) ) : ?>
										<small><?php echo esc_html( $card['unit'] ); ?></small>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $card['features'] ) ) : ?>
								<ul>
									<?php foreach ( $card['features'] as $feature ) : ?>
										<li><?php echo esc_html( $feature ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<a class="btn btn--ghost" href="<?php echo esc_url( $card['cta_url'] ); ?>"><?php echo esc_html( $card['cta_text'] ); ?></a>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="cta-wrap">
			<a class="btn" href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Ota yhteyttä ja pyydä tarjous', 'raider' ); ?></a>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/logo-marquee' ); ?>

<?php
get_footer();
