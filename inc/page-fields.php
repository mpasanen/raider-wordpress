<?php
/**
 * Editable text fields per page, exposed as native WP meta boxes.
 *
 * No plugin required, no block-editor bloat. Each registered page slug shows a
 * "Sisältö" meta box in the admin with simple input/textarea fields. Templates
 * read these via raider_field( $key, $default ).
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field configurations per page slug.
 *
 * Each field: array( 'key' => string, 'label' => string, 'type' => 'text'|'textarea', 'help' => string ).
 * Defaults are pulled from the template fallback strings at render time.
 */
function raider_page_fields_config() {
	return array(
		// Homepage (post_name 'etusivu' OR page set as front).
		'etusivu'      => array(
			array( 'key' => 'hero_tagline',     'label' => 'Hero — tagline (esim. ”kuvaukset ja videotuotanto”)', 'type' => 'text' ),
			array( 'key' => 'intro_heading',    'label' => 'Intro — otsikko',                                     'type' => 'text' ),
			array( 'key' => 'intro_text',       'label' => 'Intro — leipäteksti',                                 'type' => 'textarea' ),
			array( 'key' => 'services_heading', 'label' => 'Palvelut — pääotsikko',                               'type' => 'text' ),
			array( 'key' => 'service_1_title',  'label' => 'Palvelu 1 — otsikko',                                 'type' => 'text' ),
			array( 'key' => 'service_1_text',   'label' => 'Palvelu 1 — kuvaus',                                  'type' => 'textarea' ),
			array( 'key' => 'service_2_title',  'label' => 'Palvelu 2 — otsikko',                                 'type' => 'text' ),
			array( 'key' => 'service_2_text',   'label' => 'Palvelu 2 — kuvaus',                                  'type' => 'textarea' ),
			array( 'key' => 'service_3_title',  'label' => 'Palvelu 3 — otsikko',                                 'type' => 'text' ),
			array( 'key' => 'service_3_text',   'label' => 'Palvelu 3 — kuvaus',                                  'type' => 'textarea' ),
			array( 'key' => 'cta_label',        'label' => 'CTA-painikkeen teksti',                               'type' => 'text' ),
			array( 'key' => 'process_heading',  'label' => 'Prosessi — otsikko',                                  'type' => 'text' ),
			array( 'key' => 'process_subtitle', 'label' => 'Prosessi — alaotsikko',                               'type' => 'text' ),
			array( 'key' => 'step_1_title',     'label' => 'Vaihe 1 — otsikko',                                   'type' => 'text' ),
			array( 'key' => 'step_1_text',      'label' => 'Vaihe 1 — kuvaus',                                    'type' => 'textarea' ),
			array( 'key' => 'step_2_title',     'label' => 'Vaihe 2 — otsikko',                                   'type' => 'text' ),
			array( 'key' => 'step_2_text',      'label' => 'Vaihe 2 — kuvaus',                                    'type' => 'textarea' ),
			array( 'key' => 'step_3_title',     'label' => 'Vaihe 3 — otsikko',                                   'type' => 'text' ),
			array( 'key' => 'step_3_text',      'label' => 'Vaihe 3 — kuvaus',                                    'type' => 'textarea' ),
			array( 'key' => 'quote_text',       'label' => 'Sitaatti — teksti',                                   'type' => 'textarea' ),
			array( 'key' => 'quote_author',     'label' => 'Sitaatti — kirjoittaja',                              'type' => 'text' ),
			array( 'key' => 'contact_heading',  'label' => 'Yhteydenotto-osio — otsikko',                         'type' => 'textarea' ),
			array( 'key' => 'contact_sub',      'label' => 'Yhteydenotto-osio — alaotsikko',                      'type' => 'text' ),
		),

		'hinnoittelu'  => array(
			array( 'key' => 'intro_text', 'label' => 'Sivun johdantoteksti', 'type' => 'textarea' ),
		),

		'meista'       => array(
			array( 'key' => 'about_p1', 'label' => 'Kappale 1', 'type' => 'textarea' ),
			array( 'key' => 'about_p2', 'label' => 'Kappale 2', 'type' => 'textarea' ),
			array( 'key' => 'about_p3', 'label' => 'Kappale 3', 'type' => 'textarea' ),
			array( 'key' => 'cta_heading', 'label' => 'CTA-osio — otsikko', 'type' => 'text' ),
			array( 'key' => 'cta_text',    'label' => 'CTA-osio — leipäteksti', 'type' => 'text' ),
		),

		'ota-yhteytta' => array(
			array( 'key' => 'lead_text',     'label' => 'Yhteyshenkilön esittely',  'type' => 'textarea' ),
			array( 'key' => 'form_heading',  'label' => 'Lomake — otsikko',         'type' => 'text' ),
			array( 'key' => 'form_subtext',  'label' => 'Lomake — alaotsikko',      'type' => 'textarea' ),
		),

		'kiitos'       => array(
			array( 'key' => 'message', 'label' => 'Kiitos-viesti', 'type' => 'textarea' ),
		),
	);
}

/**
 * Determine the "config slug" for a given post (handles front page).
 */
function raider_config_slug_for_post( $post ) {
	if ( ! $post ) {
		return '';
	}
	$front_id = (int) get_option( 'page_on_front' );
	if ( $front_id && $front_id === (int) $post->ID ) {
		return 'etusivu';
	}
	return $post->post_name;
}

/**
 * Read a saved field, falling back to a hardcoded default (for fresh installs).
 */
function raider_field( $key, $default = '' ) {
	global $post;
	if ( ! $post ) {
		return $default;
	}
	$value = get_post_meta( $post->ID, '_raider_' . $key, true );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * Render a field, preserving line breaks for textareas.
 *
 * @param string $key
 * @param string $default
 * @param bool   $paragraphs  If true, wrap each paragraph in <p>...</p>. Otherwise echo as plain escaped text.
 */
function raider_the_field( $key, $default = '', $paragraphs = false ) {
	$value = raider_field( $key, $default );
	if ( $paragraphs ) {
		echo wp_kses_post( wpautop( $value ) );
	} else {
		echo nl2br( esc_html( $value ) );
	}
}

/* ---------- Admin: meta box ---------- */

add_action( 'add_meta_boxes', function () {
	$config = raider_page_fields_config();
	add_meta_box(
		'raider-page-fields',
		__( 'Sisältö (Raider)', 'raider' ),
		'raider_render_meta_box',
		'page',
		'normal',
		'high'
	);
} );

function raider_render_meta_box( $post ) {
	$slug   = raider_config_slug_for_post( $post );
	$config = raider_page_fields_config();
	if ( ! isset( $config[ $slug ] ) ) {
		echo '<p>' . esc_html__( 'Tällä sivulla ei ole erityisesti muokattavia kenttiä. Sivun otsikko on yllä.', 'raider' ) . '</p>';
		return;
	}
	wp_nonce_field( 'raider_save_fields', 'raider_fields_nonce' );
	echo '<style>.raider-field{margin:1rem 0;}.raider-field label{display:block;font-weight:600;margin-bottom:.35rem;}.raider-field input[type=text],.raider-field textarea{width:100%;}.raider-field textarea{min-height:90px;}.raider-field .description{color:#666;font-size:12px;}</style>';
	foreach ( $config[ $slug ] as $field ) {
		$id    = 'raider_' . $field['key'];
		$value = get_post_meta( $post->ID, '_raider_' . $field['key'], true );
		echo '<div class="raider-field">';
		printf( '<label for="%s">%s</label>', esc_attr( $id ), esc_html( $field['label'] ) );
		if ( 'textarea' === $field['type'] ) {
			printf( '<textarea id="%s" name="%s" rows="3">%s</textarea>', esc_attr( $id ), esc_attr( $id ), esc_textarea( $value ) );
		} else {
			printf( '<input type="text" id="%s" name="%s" value="%s">', esc_attr( $id ), esc_attr( $id ), esc_attr( $value ) );
		}
		if ( ! empty( $field['help'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['help'] ) );
		}
		echo '</div>';
	}
	echo '<p><em>' . esc_html__( 'Tyhjäksi jätetyt kentät käyttävät teeman oletustekstiä.', 'raider' ) . '</em></p>';
}

add_action( 'save_post_page', function ( $post_id ) {
	if ( ! isset( $_POST['raider_fields_nonce'] ) || ! wp_verify_nonce( $_POST['raider_fields_nonce'], 'raider_save_fields' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$post = get_post( $post_id );
	$slug = raider_config_slug_for_post( $post );
	$cfg  = raider_page_fields_config();
	if ( ! isset( $cfg[ $slug ] ) ) {
		return;
	}
	foreach ( $cfg[ $slug ] as $field ) {
		$name = 'raider_' . $field['key'];
		if ( ! isset( $_POST[ $name ] ) ) {
			continue;
		}
		$value = wp_unslash( $_POST[ $name ] );
		if ( 'textarea' === $field['type'] ) {
			$value = sanitize_textarea_field( $value );
		} else {
			$value = sanitize_text_field( $value );
		}
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_raider_' . $field['key'] );
		} else {
			update_post_meta( $post_id, '_raider_' . $field['key'], $value );
		}
	}
} );

/**
 * Hide the block editor on pages that we render fully from PHP templates.
 * Page title (h1) is still editable from the title bar at the top.
 */
add_filter( 'use_block_editor_for_post', function ( $use_block_editor, $post ) {
	if ( $post && 'page' === $post->post_type ) {
		$slug = raider_config_slug_for_post( $post );
		$cfg  = raider_page_fields_config();
		if ( isset( $cfg[ $slug ] ) ) {
			return false;
		}
	}
	return $use_block_editor;
}, 10, 2 );

/**
 * Hide the content editor area entirely (classic editor fallback) on
 * pages that we fully render from PHP. Cleaner admin UI.
 */
add_action( 'current_screen', function ( $screen ) {
	if ( ! $screen || 'page' !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
	if ( ! $post_id ) {
		return;
	}
	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}
	$slug = raider_config_slug_for_post( $post );
	$cfg  = raider_page_fields_config();
	if ( isset( $cfg[ $slug ] ) ) {
		remove_post_type_support( 'page', 'editor' );
	}
} );
