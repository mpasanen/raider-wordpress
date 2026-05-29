<?php
/**
 * Theme setup: supports, menus, image sizes, language.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'raider', RAIDER_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );

		register_nav_menus(
			array(
				'primary' => __( 'Päävalikko', 'raider' ),
				'footer'  => __( 'Alatunnisteen valikko', 'raider' ),
			)
		);

		// Tiny critical-path image sizes; full responsive variants are pre-built in /assets/.
		add_image_size( 'raider-hero', 1920, 1280, true );
	}
);

// Send modern viewport + theme color early.
add_action(
	'wp_head',
	function () {
		echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
		echo "<meta name=\"theme-color\" content=\"#c10000\">\n";
		echo "<meta name=\"color-scheme\" content=\"light\">\n";
	},
	1
);
