<?php
/**
 * Custom Post Type for client logos shown in the homepage / pricing marquee.
 *
 * - Admin → "Asiakaslogot" lets you add, edit, reorder and delete logos.
 * - Each logo: title, featured image (the logo PNG/SVG/WebP) and a destination URL.
 * - One-click "Tuo oletuslogot" button seeds the nine bundled defaults into the
 *   media library + CPT entries so the admin can edit them like any other.
 * - Until the admin has added at least one CPT entry, the front end falls back
 *   to the static bundled list (graceful default for fresh installs).
 *
 * Query results are cached in a transient and busted on save/delete.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

const RAIDER_LOGO_CPT       = 'raider_client';
const RAIDER_LOGO_META_URL  = '_raider_client_url';
const RAIDER_LOGO_CACHE_KEY = 'raider_client_logos_v1';

/* ---------- Register CPT + custom image size ---------- */

add_action(
	'init',
	function () {
		register_post_type(
			RAIDER_LOGO_CPT,
			array(
				'label'              => __( 'Asiakaslogot', 'raider' ),
				'labels'             => array(
					'name'          => __( 'Asiakaslogot', 'raider' ),
					'singular_name' => __( 'Asiakaslogo', 'raider' ),
					'add_new'       => __( 'Lisää uusi', 'raider' ),
					'add_new_item'  => __( 'Lisää uusi asiakaslogo', 'raider' ),
					'edit_item'     => __( 'Muokkaa asiakaslogoa', 'raider' ),
					'new_item'      => __( 'Uusi asiakaslogo', 'raider' ),
					'view_item'     => __( 'Katso asiakaslogo', 'raider' ),
					'all_items'     => __( 'Kaikki asiakaslogot', 'raider' ),
					'search_items'  => __( 'Etsi asiakaslogoja', 'raider' ),
				),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => false,
				'menu_icon'          => 'dashicons-format-image',
				'menu_position'      => 21,
				'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
				'has_archive'        => false,
				'rewrite'            => false,
			)
		);

		add_image_size( 'raider-logo', 240, 0, false );
	}
);

/* ---------- URL meta box ---------- */

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'raider-logo-url',
			__( 'Linkki (vapaaehtoinen)', 'raider' ),
			function ( $post ) {
				wp_nonce_field( 'raider_logo_url_save', 'raider_logo_url_nonce' );
				$value = get_post_meta( $post->ID, RAIDER_LOGO_META_URL, true );
				echo '<p><label for="raider_logo_url"><strong>URL:</strong></label></p>';
				printf( '<input type="url" id="raider_logo_url" name="raider_logo_url" value="%s" style="width:100%%" placeholder="https://...">', esc_attr( $value ) );
				echo '<p class="description">' . esc_html__( 'Jos täytetty, logosta tulee klikattava linkki. Avautuu uuteen välilehteen.', 'raider' ) . '</p>';
			},
			RAIDER_LOGO_CPT,
			'normal',
			'high'
		);
	}
);

add_action(
	'save_post_' . RAIDER_LOGO_CPT,
	function ( $post_id ) {
		if ( ! isset( $_POST['raider_logo_url_nonce'] ) || ! wp_verify_nonce( $_POST['raider_logo_url_nonce'], 'raider_logo_url_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$url = isset( $_POST['raider_logo_url'] ) ? esc_url_raw( wp_unslash( $_POST['raider_logo_url'] ) ) : '';
		if ( '' === $url ) {
			delete_post_meta( $post_id, RAIDER_LOGO_META_URL );
		} else {
			update_post_meta( $post_id, RAIDER_LOGO_META_URL, $url );
		}
		raider_logos_clear_cache();
	}
);

add_action( 'deleted_post', 'raider_logos_clear_cache_on_delete', 10, 2 );
function raider_logos_clear_cache_on_delete( $post_id, $post ) {
	if ( $post && RAIDER_LOGO_CPT === $post->post_type ) {
		raider_logos_clear_cache();
	}
}

function raider_logos_clear_cache() {
	delete_transient( RAIDER_LOGO_CACHE_KEY );
}

/* ---------- Admin list: logo thumb column ---------- */

add_filter(
	'manage_' . RAIDER_LOGO_CPT . '_posts_columns',
	function ( $columns ) {
		$new = array();
		foreach ( $columns as $k => $v ) {
			if ( 'title' === $k ) {
				$new['raider_logo_thumb'] = __( 'Logo', 'raider' );
			}
			$new[ $k ] = $v;
			if ( 'title' === $k ) {
				$new['raider_logo_url_col'] = __( 'URL', 'raider' );
				$new['raider_logo_order']   = __( 'Järjestys', 'raider' );
			}
		}
		return $new;
	}
);

add_action(
	'manage_' . RAIDER_LOGO_CPT . '_posts_custom_column',
	function ( $column, $post_id ) {
		if ( 'raider_logo_thumb' === $column ) {
			$img = get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'background:#f0f0f1;padding:4px;border-radius:4px;' ) );
			echo $img ? $img : '<em style="color:#999">(ei kuvaa)</em>'; // phpcs:ignore
		}
		if ( 'raider_logo_url_col' === $column ) {
			$url = get_post_meta( $post_id, RAIDER_LOGO_META_URL, true );
			echo $url ? '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( wp_parse_url( $url, PHP_URL_HOST ) ) . '</a>' : '—';
		}
		if ( 'raider_logo_order' === $column ) {
			echo (int) get_post_field( 'menu_order', $post_id );
		}
	},
	10,
	2
);

add_filter(
	'manage_edit-' . RAIDER_LOGO_CPT . '_sortable_columns',
	function ( $cols ) {
		$cols['raider_logo_order'] = 'menu_order';
		return $cols;
	}
);

// Default admin list order = menu_order ASC.
add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( ! is_admin() || ! $q->is_main_query() ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'edit-' . RAIDER_LOGO_CPT === $screen->id && ! isset( $_GET['orderby'] ) ) {
			$q->set( 'orderby', 'menu_order' );
			$q->set( 'order', 'ASC' );
		}
	}
);

/* ---------- "Tuo oletuslogot" admin button ---------- */

add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || 'edit-' . RAIDER_LOGO_CPT !== $screen->id ) {
			return;
		}
		$count = wp_count_posts( RAIDER_LOGO_CPT )->publish ?? 0;
		if ( $count > 0 ) {
			return;
		}
		$url = wp_nonce_url( admin_url( 'edit.php?post_type=' . RAIDER_LOGO_CPT . '&raider_import_defaults=1' ), 'raider_import_defaults' );
		echo '<div class="notice notice-info"><p><strong>' . esc_html__( 'Et ole vielä lisännyt yhtään asiakaslogoa.', 'raider' ) . '</strong> ' . esc_html__( 'Etusivulla näkyy tällä hetkellä yhdeksän oletuslogoa. Voit tuoda ne tähän muokattaviksi yhdellä klikkauksella.', 'raider' );
		echo ' <a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Tuo oletuslogot', 'raider' ) . '</a></p></div>';
	}
);

add_action(
	'admin_init',
	function () {
		if ( empty( $_GET['raider_import_defaults'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'raider_import_defaults' );
		raider_import_default_logos();
		wp_safe_redirect( admin_url( 'edit.php?post_type=' . RAIDER_LOGO_CPT . '&raider_imported=1' ) );
		exit;
	}
);

add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || 'edit-' . RAIDER_LOGO_CPT !== $screen->id ) {
			return;
		}
		if ( empty( $_GET['raider_imported'] ) ) {
			return;
		}
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Oletuslogot tuotu mediakirjastoon ja CPT-tietueisiin. Voit nyt muokata, järjestää tai poistaa ne.', 'raider' ) . '</p></div>';
	}
);

/**
 * Sideload the bundled assets/logos/*.png|jpg|webp to the WP media library
 * and create CPT entries for each.
 */
function raider_import_default_logos() {
	$defaults = array(
		array( 'name' => 'elva',                'title' => 'ELVA',                  'file' => 'elva.png',                'url' => 'https://www.elva.fi' ),
		array( 'name' => 'lvienergia',          'title' => 'LVI Energia',           'file' => 'lvienergia.png',          'url' => 'https://lvienergia.fi' ),
		array( 'name' => 'puuykkoset',          'title' => 'Puuykköset',            'file' => 'puuykkoset.png',          'url' => 'https://puuykkoset.fi' ),
		array( 'name' => 'finlaude',            'title' => 'Finlaude',              'file' => 'finlaude.png',            'url' => 'https://finlaude.fi' ),
		array( 'name' => 'valko',               'title' => 'Maalausliike Valko',    'file' => 'valko.png',               'url' => 'https://www.maalausvalko.fi' ),
		array( 'name' => 'jkv',                 'title' => 'JKV Vuokravekotin',     'file' => 'jkv.jpg',                 'url' => 'https://www.vuokravekotin.fi' ),
		array( 'name' => 'suomenkaivohuolto',   'title' => 'Suomen Kaivohuolto',    'file' => 'suomenkaivohuolto.png',   'url' => 'https://suomenkaivohuolto.fi' ),
		array( 'name' => 'teku',                'title' => 'TekuFix',               'file' => 'teku.webp',               'url' => 'https://www.tekufix.fi' ),
		array( 'name' => 'teemuntalotekniikka', 'title' => 'Teemun Talotekniikka',  'file' => 'teemuntalotekniikka.png', 'url' => 'https://teemuntalotekniikka.fi' ),
	);

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$order = 10;
	foreach ( $defaults as $d ) {
		$path = RAIDER_DIR . '/assets/logos/' . $d['file'];
		if ( ! file_exists( $path ) ) {
			continue;
		}

		// Copy to a temp file so wp_handle_sideload can move it.
		$tmp = wp_tempnam( $d['file'] );
		copy( $path, $tmp );
		$file_array = array(
			'name'     => $d['file'],
			'tmp_name' => $tmp,
		);
		$attach_id = media_handle_sideload( $file_array, 0, $d['title'] );
		if ( is_wp_error( $attach_id ) ) {
			@unlink( $tmp ); // phpcs:ignore
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => RAIDER_LOGO_CPT,
				'post_title'  => $d['title'],
				'post_status' => 'publish',
				'menu_order'  => $order,
			)
		);
		if ( ! is_wp_error( $post_id ) ) {
			set_post_thumbnail( $post_id, $attach_id );
			if ( ! empty( $d['url'] ) ) {
				update_post_meta( $post_id, RAIDER_LOGO_META_URL, $d['url'] );
			}
			$order += 10;
		}
	}
	raider_logos_clear_cache();
}

/* ---------- Front-end: fetch logos (cached) ---------- */

/**
 * Returns an array of logo render data: [{title, url, attachment_id}, ...]
 * Falls back to bundled defaults if no CPT entries exist.
 */
function raider_get_client_logos() {
	$cached = get_transient( RAIDER_LOGO_CACHE_KEY );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$out   = array();
	$posts = get_posts(
		array(
			'post_type'      => RAIDER_LOGO_CPT,
			'posts_per_page' => 50,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		)
	);

	foreach ( $posts as $p ) {
		$thumb_id = get_post_thumbnail_id( $p->ID );
		if ( ! $thumb_id ) {
			continue;
		}
		$out[] = array(
			'title'         => $p->post_title,
			'url'           => get_post_meta( $p->ID, RAIDER_LOGO_META_URL, true ),
			'attachment_id' => (int) $thumb_id,
			'is_default'    => false,
		);
	}

	if ( empty( $out ) ) {
		$out = raider_get_default_logos();
	}

	set_transient( RAIDER_LOGO_CACHE_KEY, $out, HOUR_IN_SECONDS );
	return $out;
}

/**
 * Hardcoded list used when admin hasn't added any CPT entries yet.
 * Uses the optimized AVIF/WebP variants from the theme bundle.
 */
function raider_get_default_logos() {
	return array(
		array( 'title' => 'ELVA',                  'url' => 'https://www.elva.fi',           'name' => 'elva',                'is_default' => true ),
		array( 'title' => 'LVI Energia',           'url' => 'https://lvienergia.fi',         'name' => 'lvienergia',          'is_default' => true ),
		array( 'title' => 'Puuykköset',            'url' => 'https://puuykkoset.fi',         'name' => 'puuykkoset',          'is_default' => true ),
		array( 'title' => 'Finlaude',              'url' => 'https://finlaude.fi',           'name' => 'finlaude',            'is_default' => true ),
		array( 'title' => 'Maalausliike Valko',    'url' => 'https://www.maalausvalko.fi',   'name' => 'valko',               'is_default' => true ),
		array( 'title' => 'JKV Vuokravekotin',     'url' => 'https://www.vuokravekotin.fi',  'name' => 'jkv',                 'is_default' => true ),
		array( 'title' => 'Suomen Kaivohuolto',    'url' => 'https://suomenkaivohuolto.fi',  'name' => 'suomenkaivohuolto',   'is_default' => true ),
		array( 'title' => 'TekuFix',               'url' => 'https://www.tekufix.fi',        'name' => 'teku',                'is_default' => true ),
		array( 'title' => 'Teemun Talotekniikka',  'url' => 'https://teemuntalotekniikka.fi','name' => 'teemuntalotekniikka', 'is_default' => true ),
	);
}

/**
 * Render a single logo card inside the marquee.
 *
 * @param array $logo     Logo entry from raider_get_client_logos().
 * @param bool  $is_clone True for the duplicate copy used to make the loop seamless.
 */
function raider_render_logo( $logo, $is_clone = false ) {
	$alt = $is_clone ? '' : esc_attr( $logo['title'] );
	$has_link = ! empty( $logo['url'] );
	$tag   = $has_link ? 'a' : 'div';
	$attrs = $has_link
		? ' href="' . esc_url( $logo['url'] ) . '" target="_blank" rel="noopener"'
		: '';
	if ( $is_clone ) {
		$attrs .= ' aria-hidden="true" tabindex="-1"';
	}

	echo '<' . $tag . ' class="marquee__item"' . $attrs . '>'; // phpcs:ignore

	if ( ! empty( $logo['is_default'] ) ) {
		// Pre-built AVIF/WebP variants shipped with the theme.
		$base = trailingslashit( RAIDER_URI ) . 'assets/logos/optimized/' . $logo['name'];
		?>
		<picture>
			<source type="image/avif" srcset="<?php echo esc_url( $base . '-120.avif' ); ?> 120w, <?php echo esc_url( $base . '-240.avif' ); ?> 240w" sizes="120px">
			<source type="image/webp" srcset="<?php echo esc_url( $base . '-120.webp' ); ?> 120w, <?php echo esc_url( $base . '-240.webp' ); ?> 240w" sizes="120px">
			<img src="<?php echo esc_url( $base . '-240.webp' ); ?>" alt="<?php echo $alt; // phpcs:ignore ?>" loading="lazy" decoding="async" width="120" height="60">
		</picture>
		<?php
	} else {
		// User-uploaded image, served through wp_get_attachment_image with our custom size.
		echo wp_get_attachment_image(
			$logo['attachment_id'],
			'raider-logo',
			false,
			array(
				'alt'      => $alt,
				'loading'  => 'lazy',
				'decoding' => 'async',
				'width'    => 120,
				'height'   => 60,
				'style'    => 'object-fit:contain',
			)
		); // phpcs:ignore
	}

	echo '</' . $tag . '>'; // phpcs:ignore
}
