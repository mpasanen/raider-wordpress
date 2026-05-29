<?php
/**
 * Enqueue strategy:
 *   1. Inline ALL critical CSS in <head>.
 *   2. Preload self-hosted woff2 fonts.
 *   3. Print main stylesheet via <link rel="preload" as="style"> swap-to-stylesheet pattern (non-blocking).
 *   4. No JS by default. Tiny nav-toggle is inlined in footer.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cache-busting version: file mtime in production, time() in dev.
 */
function raider_asset_ver( $rel_path ) {
	$full = RAIDER_DIR . '/' . ltrim( $rel_path, '/' );
	return file_exists( $full ) ? (string) filemtime( $full ) : RAIDER_VERSION;
}

/**
 * Inline critical CSS very early in <head>.
 */
add_action(
	'wp_head',
	function () {
		$crit = RAIDER_DIR . '/assets/css/critical.css';
		if ( ! file_exists( $crit ) ) {
			return;
		}
		$css = file_get_contents( $crit ); // phpcs:ignore
		$css = str_replace( '__THEME_URI__', untrailingslashit( RAIDER_URI ), $css );
		echo '<style id="raider-critical">' . $css . "</style>\n"; // phpcs:ignore
	},
	2
);

/**
 * Preload fonts (must be in head, before stylesheet swap).
 */
add_action(
	'wp_head',
	function () {
		$fonts = array(
			'open-sans-latin-400-normal.woff2',
			'open-sans-latin-700-normal.woff2',
			'gruppo-latin-400-normal.woff2',
			'bebas-neue-latin-400-normal.woff2',
		);
		foreach ( $fonts as $f ) {
			$url = RAIDER_URI . '/assets/fonts/' . $f;
			printf(
				"<link rel=\"preload\" as=\"font\" type=\"font/woff2\" href=\"%s\" crossorigin>\n",
				esc_url( $url )
			);
		}
	},
	3
);

/**
 * Print main stylesheet via non-blocking preload-swap.
 */
add_action(
	'wp_head',
	function () {
		$href = esc_url( RAIDER_URI . '/assets/css/main.css?v=' . raider_asset_ver( 'assets/css/main.css' ) );
		echo "<link rel=\"preload\" href=\"{$href}\" as=\"style\" onload=\"this.onload=null;this.rel='stylesheet'\">\n"; // phpcs:ignore
		echo "<noscript><link rel=\"stylesheet\" href=\"{$href}\"></noscript>\n"; // phpcs:ignore
	},
	5
);

