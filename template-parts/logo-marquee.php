<?php
/**
 * Auto-scrolling client logos marquee. Pure CSS animation, no JS.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

$logos = array(
	array( 'name' => 'elva',                'alt' => 'ELVA',                  'href' => 'https://www.elva.fi' ),
	array( 'name' => 'lvienergia',          'alt' => 'LVI Energia',           'href' => 'https://lvienergia.fi' ),
	array( 'name' => 'puuykkoset',          'alt' => 'Puuykköset',            'href' => 'https://puuykkoset.fi' ),
	array( 'name' => 'finlaude',            'alt' => 'Finlaude',              'href' => 'https://finlaude.fi' ),
	array( 'name' => 'valko',               'alt' => 'Maalausliike Valko',    'href' => 'https://www.maalausvalko.fi' ),
	array( 'name' => 'jkv',                 'alt' => 'JKV Vuokravekotin',     'href' => 'https://www.vuokravekotin.fi' ),
	array( 'name' => 'suomenkaivohuolto',   'alt' => 'Suomen Kaivohuolto',    'href' => 'https://suomenkaivohuolto.fi' ),
	array( 'name' => 'teku',                'alt' => 'TekuFix',               'href' => 'https://www.tekufix.fi' ),
	array( 'name' => 'teemuntalotekniikka', 'alt' => 'Teemun Talotekniikka',  'href' => 'https://teemuntalotekniikka.fi' ),
);

$render_logo = function ( $logo, $is_clone ) {
	$alt = $is_clone ? '' : esc_attr( $logo['alt'] );
	$base = trailingslashit( RAIDER_URI ) . 'assets/logos/optimized/' . $logo['name'];
	?>
	<a class="marquee__item" href="<?php echo esc_url( $logo['href'] ); ?>" target="_blank" rel="noopener"<?php echo $is_clone ? ' aria-hidden="true" tabindex="-1"' : ''; ?>>
		<picture>
			<source type="image/avif" srcset="<?php echo esc_url( $base . '-120.avif' ); ?> 120w, <?php echo esc_url( $base . '-240.avif' ); ?> 240w" sizes="120px">
			<source type="image/webp" srcset="<?php echo esc_url( $base . '-120.webp' ); ?> 120w, <?php echo esc_url( $base . '-240.webp' ); ?> 240w" sizes="120px">
			<img src="<?php echo esc_url( $base . '-240.webp' ); ?>" alt="<?php echo $alt; // phpcs:ignore ?>" loading="lazy" decoding="async" width="120" height="60">
		</picture>
	</a>
	<?php
};
?>
<section class="marquee" aria-label="<?php esc_attr_e( 'Asiakkaita', 'raider' ); ?>">
	<div class="marquee__track">
		<?php
		foreach ( $logos as $l ) {
			$render_logo( $l, false );
		}
		foreach ( $logos as $l ) {
			$render_logo( $l, true ); // Seamless loop duplicate.
		}
		?>
	</div>
</section>
