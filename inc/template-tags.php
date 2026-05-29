<?php
/**
 * Template helpers, especially the responsive <picture> renderer.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a responsive <picture> with AVIF + WebP + raster fallback.
 *
 * @param array $args {
 *   @type string $name          Stem of the image filename (without extension or width suffix).
 *   @type array  $widths        Available widths to include in srcset.
 *   @type string $sizes         HTML sizes attribute, e.g. '100vw' or '(max-width: 720px) 100vw, 50vw'.
 *   @type string $alt           Alt text (use '' for purely decorative images).
 *   @type string $class         CSS class(es) for the <img>.
 *   @type bool   $eager         True → loading="eager" + fetchpriority="high".
 *   @type string $fallback_ext  'jpg' (default) or 'png'.
 *   @type string $dir           Sub-directory under assets/ (e.g. 'images/optimized' or 'logos/optimized').
 *   @type int    $width         Intrinsic width attribute (largest variant).
 *   @type int    $height        Intrinsic height attribute (largest variant).
 * }
 */
function raider_picture( array $args ) {
	$defaults = array(
		'name'         => '',
		'widths'       => array(),
		'sizes'        => '100vw',
		'alt'          => '',
		'class'        => '',
		'eager'        => false,
		'fallback_ext' => 'jpg',
		'dir'          => 'images/optimized',
		'width'        => 0,
		'height'       => 0,
	);
	$a = array_merge( $defaults, $args );
	if ( empty( $a['name'] ) || empty( $a['widths'] ) ) {
		return;
	}

	$base = trailingslashit( RAIDER_URI ) . 'assets/' . trim( $a['dir'], '/' ) . '/' . $a['name'];

	$srcset = function ( $ext ) use ( $a, $base ) {
		$parts = array();
		foreach ( $a['widths'] as $w ) {
			$parts[] = sprintf( '%s-%d.%s %dw', esc_url( $base ), $w, $ext, $w );
		}
		return implode( ', ', $parts );
	};

	$largest    = max( $a['widths'] );
	$fb_ext     = ( 'png' === $a['fallback_ext'] ) ? 'png' : 'jpg';
	$fb_src     = esc_url( $base . '-' . $largest . '.' . $fb_ext );
	$alt        = esc_attr( $a['alt'] );
	$class_attr = $a['class'] ? ' class="' . esc_attr( $a['class'] ) . '"' : '';
	$w_attr     = $a['width'] ? ' width="' . (int) $a['width'] . '"' : '';
	$h_attr     = $a['height'] ? ' height="' . (int) $a['height'] . '"' : '';
	$loading    = $a['eager'] ? 'eager' : 'lazy';
	$fp         = $a['eager'] ? ' fetchpriority="high"' : '';

	?>
<picture>
	<source type="image/avif" srcset="<?php echo $srcset( 'avif' ); // phpcs:ignore ?>" sizes="<?php echo esc_attr( $a['sizes'] ); ?>">
	<source type="image/webp" srcset="<?php echo $srcset( 'webp' ); // phpcs:ignore ?>" sizes="<?php echo esc_attr( $a['sizes'] ); ?>">
	<img src="<?php echo $fb_src; // phpcs:ignore ?>" srcset="<?php echo $srcset( $fb_ext ); // phpcs:ignore ?>" sizes="<?php echo esc_attr( $a['sizes'] ); ?>" alt="<?php echo $alt; // phpcs:ignore ?>" loading="<?php echo esc_attr( $loading ); ?>" decoding="async"<?php echo $fp . $class_attr . $w_attr . $h_attr; // phpcs:ignore ?>>
</picture>
	<?php
}

/**
 * Helper to print the site logo as a <picture>.
 */
function raider_logo_picture( $args = array() ) {
	raider_picture(
		array_merge(
			array(
				'name'         => 'logo',
				'widths'       => array( 120, 240, 440 ),
				'sizes'        => '120px',
				'alt'          => 'Raider Production',
				'fallback_ext' => 'png',
				'dir'          => 'images/optimized',
				'width'        => 240,
				'height'       => 163,
				'eager'        => true,
			),
			$args
		)
	);
}

/**
 * Primary nav renderer (used when no WP menu is assigned, or as the fallback for one).
 */
function raider_render_nav( $current_slug = '' ) {
	$links = array(
		array( 'slug' => '',                'url' => home_url( '/' ),                 'label' => __( 'Etusivu', 'raider' ) ),
		array( 'slug' => 'hinnoittelu',     'url' => home_url( '/hinnoittelu/' ),     'label' => __( 'Hinnoittelu', 'raider' ) ),
		array( 'slug' => 'meista',          'url' => home_url( '/meista/' ),          'label' => __( 'Meistä', 'raider' ) ),
		array( 'slug' => 'ota-yhteytta',    'url' => home_url( '/ota-yhteytta/' ),    'label' => __( 'Ota yhteyttä', 'raider' ) ),
	);
	echo '<ul class="site-nav__list">';
	foreach ( $links as $l ) {
		$current = ( $l['slug'] === $current_slug ) ? ' aria-current="page"' : '';
		printf(
			'<li><a class="site-nav__link" href="%s"%s>%s</a></li>',
			esc_url( $l['url'] ),
			$current,
			esc_html( $l['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Slug of the current queried object (for highlighting the nav item).
 */
function raider_current_slug() {
	if ( is_front_page() ) {
		return '';
	}
	$q = get_queried_object();
	return ( $q && isset( $q->post_name ) ) ? $q->post_name : '';
}
