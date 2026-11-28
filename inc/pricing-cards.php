<?php
/**
 * Custom Post Type for pricing cards shown on /hinnoittelu/.
 *
 * - Admin → "Hinnoittelu" lets you add, edit, reorder and delete pricing cards.
 * - Each card belongs to a group (Videotuotanto, Asiakaskokemusvideo, Someapu…).
 *   The group is a simple text field so the admin can rename or add new groups
 *   without learning the taxonomy UI. The display order is per-card menu_order,
 *   and groups appear in the order their first card encounters them in the
 *   sorted query.
 * - One-click "Tuo oletushinnasto" seeds the bundled defaults.
 * - Until any CPT entry exists, the pricing page falls back to the bundled
 *   defaults — fresh installs render correctly out of the box.
 *
 * Query results are cached in a transient, busted on save/delete.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

const RAIDER_PRICE_CPT       = 'raider_price';
const RAIDER_PRICE_CACHE_KEY = 'raider_pricing_cards_v2';

/* ---------- Register CPT ---------- */

add_action(
	'init',
	function () {
		register_post_type(
			RAIDER_PRICE_CPT,
			array(
				'label'              => __( 'Hinnoittelu', 'raider' ),
				'labels'             => array(
					'name'          => __( 'Hinnoittelu', 'raider' ),
					'singular_name' => __( 'Hintapaketti', 'raider' ),
					'add_new'       => __( 'Lisää uusi', 'raider' ),
					'add_new_item'  => __( 'Lisää uusi hintapaketti', 'raider' ),
					'edit_item'     => __( 'Muokkaa hintapakettia', 'raider' ),
					'all_items'     => __( 'Kaikki hintapaketit', 'raider' ),
				),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => false,
				'menu_icon'          => 'dashicons-tag',
				'menu_position'      => 22,
				'supports'           => array( 'title', 'page-attributes' ),
				'has_archive'        => false,
				'rewrite'            => false,
			)
		);
	}
);

/* ---------- Meta box ---------- */

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'raider-price-fields',
			__( 'Hintatiedot', 'raider' ),
			'raider_render_price_meta_box',
			RAIDER_PRICE_CPT,
			'normal',
			'high'
		);
	}
);

function raider_render_price_meta_box( $post ) {
	wp_nonce_field( 'raider_price_save', 'raider_price_nonce' );
	$group    = get_post_meta( $post->ID, '_raider_price_group', true );
	$price    = get_post_meta( $post->ID, '_raider_price_amount', true );
	$unit     = get_post_meta( $post->ID, '_raider_price_unit', true );
	$features = get_post_meta( $post->ID, '_raider_price_features', true );
	$cta_url  = get_post_meta( $post->ID, '_raider_price_cta_url', true );
	$cta_text = get_post_meta( $post->ID, '_raider_price_cta_text', true );
	?>
	<style>
		.raider-pf{margin:1rem 0;padding-bottom:.9rem;border-bottom:1px solid #f0f0f1}
		.raider-pf:last-child{border-bottom:0}
		.raider-pf label{display:block;font-weight:600;margin-bottom:.35rem}
		.raider-pf input,.raider-pf textarea{width:100%;font-size:14px}
		.raider-pf textarea{min-height:90px}
		.raider-pf .description{color:#646970;font-size:12px;margin:.3rem 0 0}
		.raider-pf-two{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
	</style>

	<div class="raider-pf">
		<label for="raider_price_group"><?php esc_html_e( 'Ryhmä', 'raider' ); ?></label>
		<input type="text" id="raider_price_group" name="raider_price_group" value="<?php echo esc_attr( $group ); ?>" placeholder="<?php esc_attr_e( 'esim. Videotuotanto', 'raider' ); ?>">
		<p class="description"><?php esc_html_e( 'Saman ryhmän paketit näytetään yhdessä otsikon alla. Ryhmien järjestys: ryhmä jossa on alimman järjestyksen kortti tulee ensin.', 'raider' ); ?></p>
	</div>

	<div class="raider-pf raider-pf-two">
		<div>
			<label for="raider_price_amount"><?php esc_html_e( 'Hinta', 'raider' ); ?></label>
			<input type="text" id="raider_price_amount" name="raider_price_amount" value="<?php echo esc_attr( $price ); ?>" placeholder="esim. 1 800 €">
		</div>
		<div>
			<label for="raider_price_unit"><?php esc_html_e( 'Hinnan yksikkö / huomautus', 'raider' ); ?></label>
			<input type="text" id="raider_price_unit" name="raider_price_unit" value="<?php echo esc_attr( $unit ); ?>" placeholder="esim. (alv 0) tai /kk">
		</div>
	</div>

	<div class="raider-pf">
		<label for="raider_price_features"><?php esc_html_e( 'Ominaisuudet (yksi per rivi)', 'raider' ); ?></label>
		<textarea id="raider_price_features" name="raider_price_features" rows="6" placeholder="10 julkaisuvalmista lyhytvideota&#10;Kuvaus + editointi&#10;Matkakulut 300 km säteellä"><?php echo esc_textarea( $features ); ?></textarea>
		<p class="description"><?php esc_html_e( 'Joka rivi näytetään omana bullet-listan kohtana.', 'raider' ); ?></p>
	</div>

	<div class="raider-pf raider-pf-two">
		<div>
			<label for="raider_price_cta_text"><?php esc_html_e( 'CTA-napin teksti', 'raider' ); ?></label>
			<input type="text" id="raider_price_cta_text" name="raider_price_cta_text" value="<?php echo esc_attr( $cta_text ); ?>" placeholder="Pyydä tarjous">
		</div>
		<div>
			<label for="raider_price_cta_url"><?php esc_html_e( 'CTA-napin osoite', 'raider' ); ?></label>
			<input type="text" id="raider_price_cta_url" name="raider_price_cta_url" value="<?php echo esc_attr( $cta_url ); ?>" placeholder="<?php echo esc_attr( home_url( '/ota-yhteytta/' ) ); ?>">
			<p class="description"><?php esc_html_e( 'Tyhjä = oletuksena yhteyssivu.', 'raider' ); ?></p>
		</div>
	</div>
	<?php
}

add_action(
	'save_post_' . RAIDER_PRICE_CPT,
	function ( $post_id ) {
		if ( ! isset( $_POST['raider_price_nonce'] ) || ! wp_verify_nonce( $_POST['raider_price_nonce'], 'raider_price_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$map = array(
			'_raider_price_group'    => 'sanitize_text_field',
			'_raider_price_amount'   => 'sanitize_text_field',
			'_raider_price_unit'     => 'sanitize_text_field',
			'_raider_price_features' => 'sanitize_textarea_field',
			'_raider_price_cta_text' => 'sanitize_text_field',
			'_raider_price_cta_url'  => 'esc_url_raw',
		);
		foreach ( $map as $meta_key => $sanitizer ) {
			$post_key = str_replace( '_raider_price_', 'raider_price_', $meta_key );
			if ( ! isset( $_POST[ $post_key ] ) ) {
				continue;
			}
			$value = call_user_func( $sanitizer, wp_unslash( $_POST[ $post_key ] ) );
			if ( '' === $value ) {
				delete_post_meta( $post_id, $meta_key );
			} else {
				update_post_meta( $post_id, $meta_key, $value );
			}
		}
		raider_pricing_clear_cache();
	}
);

add_action(
	'deleted_post',
	function ( $post_id, $post ) {
		if ( $post && RAIDER_PRICE_CPT === $post->post_type ) {
			raider_pricing_clear_cache();
		}
	},
	10,
	2
);

function raider_pricing_clear_cache() {
	delete_transient( RAIDER_PRICE_CACHE_KEY );
}

/* ---------- Admin list columns ---------- */

add_filter(
	'manage_' . RAIDER_PRICE_CPT . '_posts_columns',
	function ( $cols ) {
		$new = array();
		foreach ( $cols as $k => $v ) {
			$new[ $k ] = $v;
			if ( 'title' === $k ) {
				$new['raider_price_group']  = __( 'Ryhmä', 'raider' );
				$new['raider_price_amount'] = __( 'Hinta', 'raider' );
				$new['raider_price_order']  = __( 'Järjestys', 'raider' );
			}
		}
		return $new;
	}
);

add_action(
	'manage_' . RAIDER_PRICE_CPT . '_posts_custom_column',
	function ( $col, $post_id ) {
		if ( 'raider_price_group' === $col ) {
			echo esc_html( get_post_meta( $post_id, '_raider_price_group', true ) ?: '—' );
		}
		if ( 'raider_price_amount' === $col ) {
			$p = get_post_meta( $post_id, '_raider_price_amount', true );
			$u = get_post_meta( $post_id, '_raider_price_unit', true );
			echo esc_html( trim( $p . ' ' . $u ) ) ?: '—';
		}
		if ( 'raider_price_order' === $col ) {
			echo (int) get_post_field( 'menu_order', $post_id );
		}
	},
	10,
	2
);

add_filter(
	'manage_edit-' . RAIDER_PRICE_CPT . '_sortable_columns',
	function ( $cols ) {
		$cols['raider_price_order'] = 'menu_order';
		return $cols;
	}
);

add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( ! is_admin() || ! $q->is_main_query() ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'edit-' . RAIDER_PRICE_CPT === $screen->id && ! isset( $_GET['orderby'] ) ) {
			$q->set( 'orderby', 'menu_order' );
			$q->set( 'order', 'ASC' );
		}
	}
);

/* ---------- "Tuo oletushinnasto" button + handler ---------- */

add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || 'edit-' . RAIDER_PRICE_CPT !== $screen->id ) {
			return;
		}
		$count = wp_count_posts( RAIDER_PRICE_CPT )->publish ?? 0;
		if ( $count > 0 ) {
			if ( ! empty( $_GET['raider_pricing_imported'] ) ) {
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Oletushinnasto tuotu. Voit nyt muokata kortteja.', 'raider' ) . '</p></div>';
			}
			return;
		}
		$url = wp_nonce_url( admin_url( 'edit.php?post_type=' . RAIDER_PRICE_CPT . '&raider_import_pricing=1' ), 'raider_import_pricing' );
		echo '<div class="notice notice-info"><p><strong>' . esc_html__( 'Et ole vielä lisännyt yhtään hintapakettia.', 'raider' ) . '</strong> ' . esc_html__( '/hinnoittelu/-sivulla näkyy tällä hetkellä kuuden paketin oletushinnasto. Voit tuoda sen tähän muokattavaksi yhdellä klikkauksella.', 'raider' );
		echo ' <a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Tuo oletushinnasto', 'raider' ) . '</a></p></div>';
	}
);

add_action(
	'admin_init',
	function () {
		if ( empty( $_GET['raider_import_pricing'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'raider_import_pricing' );
		raider_import_default_pricing();
		wp_safe_redirect( admin_url( 'edit.php?post_type=' . RAIDER_PRICE_CPT . '&raider_pricing_imported=1' ) );
		exit;
	}
);

function raider_import_default_pricing() {
	$defaults = raider_get_default_pricing();
	$order    = 10;
	foreach ( $defaults as $card ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => RAIDER_PRICE_CPT,
				'post_title'  => $card['title'],
				'post_status' => 'publish',
				'menu_order'  => $order,
			)
		);
		if ( is_wp_error( $post_id ) ) {
			continue;
		}
		update_post_meta( $post_id, '_raider_price_group',    $card['group'] );
		update_post_meta( $post_id, '_raider_price_amount',   $card['price'] );
		update_post_meta( $post_id, '_raider_price_unit',     $card['unit'] );
		update_post_meta( $post_id, '_raider_price_features', implode( "\n", $card['features'] ) );
		update_post_meta( $post_id, '_raider_price_cta_text', $card['cta_text'] );
		if ( ! empty( $card['cta_url'] ) ) {
			update_post_meta( $post_id, '_raider_price_cta_url', $card['cta_url'] );
		}
		$order += 10;
	}
	raider_pricing_clear_cache();
}

/* ---------- Defaults ---------- */

function raider_get_default_pricing() {
	$ota = home_url( '/ota-yhteytta/' );
	return array(
		array(
			'group'    => 'Videotuotanto',
			'title'    => '10 kpl lyhytvideoita',
			'price'    => '1 000 €',
			'unit'     => '(alv 0)',
			'features' => array(
				'10 julkaisuvalmista lyhytvideota',
				'Kuvaus + editointi',
			),
			'cta_text' => 'Pyydä tarjous',
			'cta_url'  => $ota,
		),
		array(
			'group'    => 'Videotuotanto',
			'title'    => '16 kpl lyhytvideoita',
			'price'    => '1 800 €',
			'unit'     => '(alv 0)',
			'features' => array(
				'16 julkaisuvalmista lyhytvideota',
				'Kuvaus + editointi',
				'Matkakulut 300 km säteellä (ei majoitusta)',
				'Sisällön suunnittelu yhdessä lyhyesti, jotta tyyli ja viesti osuvat kohdalleen',
			),
			'cta_text' => 'Pyydä tarjous',
			'cta_url'  => $ota,
		),
		array(
			'group'    => 'Videotuotanto',
			'title'    => 'Jatkuva tuotanto 3 kk',
			'price'    => '4 100 €',
			'unit'     => '(alv 0)',
			'features' => array(
				'25 lyhytvideota (3 kk tuotanto)',
				'Kuvaus + editointi',
				'Matkakulut 300 km säteellä',
			),
			'cta_text' => 'Pyydä tarjous',
			'cta_url'  => $ota,
		),
		array(
			'group'    => 'Asiakaskokemusvideo',
			'title'    => 'Kevyt asiakasreferenssi',
			'price'    => '300 €',
			'unit'     => '/kpl',
			'features' => array(
				'Videon kesto noin 1 min',
				'Kevyellä tyylillä kuvattu ja toteutettu, mutta laadukas',
				'Dronekuvaus',
				'Kuvituskuvat',
				'Haastattelukuvaus',
			),
			'cta_text' => 'Pyydä tarjous',
			'cta_url'  => $ota,
		),
		array(
			'group'    => 'Asiakaskokemusvideo',
			'title'    => 'Perinteinen 2–3 min',
			'price'    => '800 €',
			'unit'     => '/kpl',
			'features' => array(
				'Kuvaus + editointi',
				'Syvempää tarinointia ja enemmän sisältöä',
				'Sopii nettisivuille, myyntiin ja referenssikäyttöön',
			),
			'cta_text' => 'Pyydä tarjous',
			'cta_url'  => $ota,
		),
		array(
			'group'    => 'Someapu',
			'title'    => 'Peruspaketti',
			'price'    => '200 €',
			'unit'     => '/kk (alv 0)',
			'features' => array(
				'2 julkaisua viikossa',
				'2 h / vko yrityksen viestinnän kehitystä',
				'Pois lukien kommenttien ja DM-viestien vastaaminen',
			),
			'cta_text' => 'Pyydä tarjous',
			'cta_url'  => $ota,
		),
	);
}

/* ---------- One-time update of imported cards (11/2026 price list) ---------- */

/**
 * If the bundled defaults were imported into the CPT, changing the defaults in
 * code no longer reaches the page. This brings imported cards up to date once:
 *   - "10 kpl lyhytvideoita" 1 800 € becomes the 16-video package,
 *   - a new 10-video package at 1 000 € is added before it,
 *   - "Lyhytvideo" 300 € becomes "Kevyt asiakasreferenssi".
 * A card is only touched while its title and price still match the old default,
 * so anything the admin has already edited by hand is left alone.
 */
add_action(
	'init',
	function () {
		if ( (int) get_option( 'raider_pricing_data_version' ) >= 2 ) {
			return;
		}
		update_option( 'raider_pricing_data_version', 2 );

		$posts = get_posts(
			array(
				'post_type'      => RAIDER_PRICE_CPT,
				'posts_per_page' => 50,
				'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
				'no_found_rows'  => true,
			)
		);
		if ( empty( $posts ) ) {
			return; // Nothing imported: the page already renders the new defaults.
		}

		$defaults = array();
		foreach ( raider_get_default_pricing() as $card ) {
			$defaults[ $card['title'] ] = $card;
		}
		$has_small_package = false;
		$old_package       = null;
		$old_reference     = null;
		foreach ( $posts as $p ) {
			$amount = preg_replace( '/[^0-9]/', '', (string) get_post_meta( $p->ID, '_raider_price_amount', true ) );
			if ( '1000' === $amount ) {
				$has_small_package = true;
			}
			if ( '10 kpl lyhytvideoita' === $p->post_title && '1800' === $amount ) {
				$old_package = $p;
			}
			if ( 'Lyhytvideo' === $p->post_title && '300' === $amount ) {
				$old_reference = $p;
			}
		}

		if ( $old_package ) {
			wp_update_post(
				array(
					'ID'         => $old_package->ID,
					'post_title' => '16 kpl lyhytvideoita',
				)
			);
			$features = (string) get_post_meta( $old_package->ID, '_raider_price_features', true );
			update_post_meta( $old_package->ID, '_raider_price_features', str_replace( '10 julkaisuvalmista', '16 julkaisuvalmista', $features ) );

			if ( ! $has_small_package ) {
				$card    = $defaults['10 kpl lyhytvideoita'];
				$post_id = wp_insert_post(
					array(
						'post_type'   => RAIDER_PRICE_CPT,
						'post_title'  => $card['title'],
						'post_status' => $old_package->post_status,
						'menu_order'  => max( 0, (int) $old_package->menu_order - 5 ),
					)
				);
				if ( $post_id && ! is_wp_error( $post_id ) ) {
					$group = get_post_meta( $old_package->ID, '_raider_price_group', true );
					update_post_meta( $post_id, '_raider_price_group', $group ? $group : $card['group'] );
					update_post_meta( $post_id, '_raider_price_amount', $card['price'] );
					update_post_meta( $post_id, '_raider_price_unit', $card['unit'] );
					update_post_meta( $post_id, '_raider_price_features', implode( "
", $card['features'] ) );
					update_post_meta( $post_id, '_raider_price_cta_text', $card['cta_text'] );
				}
			}
		}

		if ( $old_reference ) {
			$card = $defaults['Kevyt asiakasreferenssi'];
			wp_update_post(
				array(
					'ID'         => $old_reference->ID,
					'post_title' => $card['title'],
				)
			);
			update_post_meta( $old_reference->ID, '_raider_price_features', implode( "
", $card['features'] ) );
		}

		raider_pricing_clear_cache();
	},
	20
);

/* ---------- Front-end: grouped, cached fetch ---------- */

/**
 * Returns groups as an ordered associative array:
 *   [ 'Videotuotanto' => [ card1, card2 ], 'Asiakaskokemusvideo' => [...] ]
 * Each card: title, price, unit, features (array of strings), cta_text, cta_url.
 *
 * Group order = the order in which each group's first card appears in the
 * menu_order-sorted CPT query. Cards within a group keep menu_order.
 */
function raider_get_pricing_cards() {
	$cached = get_transient( RAIDER_PRICE_CACHE_KEY );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$grouped = array();
	$posts   = get_posts(
		array(
			'post_type'      => RAIDER_PRICE_CPT,
			'posts_per_page' => 50,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		)
	);

	if ( ! empty( $posts ) ) {
		foreach ( $posts as $p ) {
			$group   = get_post_meta( $p->ID, '_raider_price_group', true ) ?: __( 'Muut', 'raider' );
			$features_raw = get_post_meta( $p->ID, '_raider_price_features', true );
			$features     = $features_raw
				? array_values( array_filter( array_map( 'trim', preg_split( "/\r?\n/", $features_raw ) ) ) )
				: array();
			$grouped[ $group ][] = array(
				'title'    => $p->post_title,
				'price'    => get_post_meta( $p->ID, '_raider_price_amount', true ),
				'unit'     => get_post_meta( $p->ID, '_raider_price_unit', true ),
				'features' => $features,
				'cta_text' => get_post_meta( $p->ID, '_raider_price_cta_text', true ) ?: __( 'Pyydä tarjous', 'raider' ),
				'cta_url'  => get_post_meta( $p->ID, '_raider_price_cta_url', true ) ?: home_url( '/ota-yhteytta/' ),
			);
		}
	} else {
		// Fallback: bundled defaults.
		foreach ( raider_get_default_pricing() as $card ) {
			$grouped[ $card['group'] ][] = array(
				'title'    => $card['title'],
				'price'    => $card['price'],
				'unit'     => $card['unit'],
				'features' => $card['features'],
				'cta_text' => $card['cta_text'],
				'cta_url'  => $card['cta_url'],
			);
		}
	}

	set_transient( RAIDER_PRICE_CACHE_KEY, $grouped, HOUR_IN_SECONDS );
	return $grouped;
}
