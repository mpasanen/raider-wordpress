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
		<div class="site-contact" aria-label="<?php esc_attr_e( 'Suora yhteys Tommiin', 'raider' ); ?>">
			<a class="site-contact__btn" href="https://wa.me/358407245604" target="_blank" rel="noopener" aria-label="WhatsApp Tommille">
				<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.93 9.93 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91A9.82 9.82 0 0 0 19.05 4.91zM12.04 20.15a8.23 8.23 0 0 1-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.22 8.22 0 0 1-1.26-4.38c0-4.54 3.7-8.23 8.25-8.23 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.69 8.22-8.24 8.22zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.16.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.84-.2-.49-.41-.42-.56-.43h-.48c-.16 0-.42.06-.64.31-.22.25-.84.82-.84 2s.86 2.32.98 2.48c.12.17 1.7 2.6 4.12 3.65 1.55.67 2.16.73 2.93.61.47-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.11-.22-.17-.46-.29z"/></svg>
				<span class="site-contact__txt">WhatsApp</span>
			</a>
			<a class="site-contact__btn" href="tel:+358407245604" aria-label="Soita Tommille: 040 724 5604">
				<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57a1.02 1.02 0 0 0-1.02.24l-2.2 2.2a15.06 15.06 0 0 1-6.58-6.58l2.2-2.2a1 1 0 0 0 .25-1.02A11.36 11.36 0 0 1 8.5 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5c0-.55-.45-1-1-1z"/></svg>
				<span class="site-contact__txt">Soita</span>
			</a>
			<a class="site-contact__btn" href="mailto:tommi.parhiala@gmail.com" aria-label="Lähetä sähköpostia Tommille">
				<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
				<span class="site-contact__txt">Sähköposti</span>
			</a>
		</div>
		<button class="nav-toggle" type="button" aria-controls="primary-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Avaa valikko', 'raider' ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round"/></svg>
		</button>
		<nav id="primary-nav" class="site-nav" aria-label="<?php esc_attr_e( 'Päävalikko', 'raider' ); ?>">
			<?php raider_render_nav( raider_current_slug() ); ?>
		</nav>
	</div>
</header>
<main id="main">
