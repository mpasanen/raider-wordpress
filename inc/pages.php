<?php
/**
 * Auto-bootstrap the four canonical pages and configure the static front page
 * on theme activation, so the site works without manual admin setup.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'raider_bootstrap_pages' );

function raider_bootstrap_pages() {
	$pages = array(
		'etusivu'      => array( 'title' => 'Etusivu',      'template' => '' ),
		'hinnoittelu'  => array( 'title' => 'Hinnoittelu',  'template' => '' ),
		'meista'       => array( 'title' => 'Meistä',       'template' => '' ),
		'ota-yhteytta' => array( 'title' => 'Ota yhteyttä', 'template' => '' ),
		'kiitos'       => array( 'title' => 'Kiitos',       'template' => '' ),
	);

	$created = array();
	foreach ( $pages as $slug => $cfg ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$created[ $slug ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_title'   => $cfg['title'],
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			)
		);
		if ( ! is_wp_error( $id ) ) {
			$created[ $slug ] = $id;
		}
	}

	if ( isset( $created['etusivu'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $created['etusivu'] );
	}

	// Pretty permalinks (postname) for SEO-friendly URLs.
	if ( '' === get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules();
	}

	// Site identity defaults if blank.
	if ( ! get_bloginfo( 'description' ) || 'Just another WordPress site' === get_bloginfo( 'description' ) ) {
		update_option( 'blogdescription', 'Lyhytvideot, mainosvideot ja someapu yrityksille.' );
	}
}
