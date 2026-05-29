<?php
/**
 * Global header.
 *
 * @package Raider
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<link rel="profile" href="https://gmpg.org/xfn/11">
<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( RAIDER_URI . '/assets/favicon.svg' ); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( RAIDER_URI . '/assets/favicon-32.png' ); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( RAIDER_URI . '/assets/apple-touch-icon.png' ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Siirry sisältöön', 'raider' ); ?></a>

<header class="site-header">
	<div class="container site-header__inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo" aria-label="<?php esc_attr_e( 'Raider Production etusivu', 'raider' ); ?>">
			<?php raider_logo_picture(); ?>
		</a>
		<button class="nav-toggle" type="button" aria-controls="primary-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Avaa valikko', 'raider' ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round"/></svg>
		</button>
		<nav id="primary-nav" class="site-nav" aria-label="<?php esc_attr_e( 'Päävalikko', 'raider' ); ?>">
			<?php raider_render_nav( raider_current_slug() ); ?>
		</nav>
	</div>
</header>
<main id="main">
