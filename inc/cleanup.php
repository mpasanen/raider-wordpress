<?php
/**
 * Strip WordPress noise from the front end for maximum performance.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

// --- Remove emoji bloat ---
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'tiny_mce_plugins', fn( $p ) => array_diff( (array) $p, array( 'wpemoji' ) ) );
add_filter(
	'wp_resource_hints',
	function ( $hints, $relation ) {
		if ( 'dns-prefetch' === $relation ) {
			$hints = array_diff( $hints, array( 'https://s.w.org' ) );
		}
		return $hints;
	},
	10,
	2
);

// --- Remove unused header noise ---
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'parent_post_rel_link', 10 );
remove_action( 'wp_head', 'start_post_rel_link', 10 );

// --- Drop block-library and global-styles for non-block content (this theme uses none) ---
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'wc-block-style' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'core-block-supports' );
		wp_deregister_script( 'wp-embed' );
	},
	100
);

// --- Disable XML-RPC ---
add_filter( 'xmlrpc_enabled', '__return_false' );

// --- Tighten REST API: keep enabled but drop link header ---
remove_action( 'template_redirect', 'rest_output_link_header', 11 );

// --- Disable pingbacks ---
add_filter(
	'wp_headers',
	function ( $headers ) {
		unset( $headers['X-Pingback'] );
		return $headers;
	}
);

// --- Lazy-load by default plus async decoding on all theme-rendered images ---
add_filter(
	'wp_get_attachment_image_attributes',
	function ( $attr ) {
		if ( empty( $attr['loading'] ) ) {
			$attr['loading'] = 'lazy';
		}
		if ( empty( $attr['decoding'] ) ) {
			$attr['decoding'] = 'async';
		}
		return $attr;
	}
);

// --- Limit srcset noise from cores' default scaling ---
add_filter( 'max_srcset_image_width', fn() => 1920 );

// --- Trim admin bar from front end ---
add_action(
	'wp_before_admin_bar_render',
	function () {
		if ( ! is_admin() && ! is_user_logged_in() ) {
			show_admin_bar( false );
		}
	}
);
