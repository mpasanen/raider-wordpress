<?php
/**
 * Editable text fields per page, exposed as native WP meta boxes.
 *
 * Each registered page slug shows a "Sisältö" meta box with simple
 * input/textarea fields. Templates read these via
 *   raider_field( $key )           // string, falls back to config default
 *   raider_the_field( $key, false, $paragraphs = false )
 *
 * On the edit screen, fields are pre-filled with their default text so the
 * admin can see what they are editing. Clearing a field reverts to the default.
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field configs per page slug.
 *
 * Each field: array(
 *   'key'      => string  — meta key suffix,
 *   'label'    => string  — admin label,
 *   'type'     => 'text'|'textarea',
 *   'default'  => string  — fallback shown on the front end AND pre-filled in the editor.
 * )
 */
function raider_page_fields_config() {
	return array(
		'etusivu'      => array(
			array( 'key' => 'hero_tagline',     'label' => 'Hero — tagline',           'type' => 'text',     'default' => 'kuvaukset ja videotuotanto' ),
			array( 'key' => 'intro_heading',    'label' => 'Intro — otsikko',          'type' => 'text',     'default' => 'Miksi some-sisällöntuotanto on nykypäivänä tärkeää?' ),
			array( 'key' => 'intro_text',       'label' => 'Intro — leipäteksti',      'type' => 'textarea', 'default' => 'Some-sisällöntuotanto on nykypäivänä tärkeää, koska asiakkaat etsivät ja kuluttavat tietoa sosiaalisessa mediassa päivittäin. Laadukas ja johdonmukainen sisältö lisää näkyvyyttä, vahvistaa brändin luotettavuutta ja ohjaa potentiaalisia asiakkaita toimimaan. Ilman aktiivista sisällöntuotantoa yritys jää helposti kilpailijoidensa varjoon.' ),
			array( 'key' => 'services_heading', 'label' => 'Palvelut — pääotsikko',    'type' => 'text',     'default' => 'Tarjoamamme somepalvelut' ),
			array( 'key' => 'service_1_title',  'label' => 'Palvelu 1 — otsikko',      'type' => 'text',     'default' => 'Brändi ja mainosvideot' ),
			array( 'key' => 'service_1_text',   'label' => 'Palvelu 1 — kuvaus',       'type' => 'textarea', 'default' => 'Tuotamme tehokasta materiaalia videon ja kuvan muodossa tehostamaan yrityksien mainontaa. Tehokkuus ja ketteryys on seikka millä erotumme joukosta — pidämme huolen että asiakkaamme saavat materiaalit sujuvasti nähtäville kuvauksien jälkeen.' ),
			array( 'key' => 'service_2_title',  'label' => 'Palvelu 2 — otsikko',      'type' => 'text',     'default' => 'Somevideot' ),
			array( 'key' => 'service_2_text',   'label' => 'Palvelu 2 — kuvaus',       'type' => 'textarea', 'default' => 'Jatkuva videoiden tuotanto takaa parempaa näkyvyyttä ja asiakkaittesi tavoitettavuutta sosiaalisessa mediassa. Tuotannon ei tarvitse olla näyteltyä ja käsikirjoitettua, vaan tuotamme autenttista materiaalia mihin on helppo samaistua.' ),
			array( 'key' => 'service_3_title',  'label' => 'Palvelu 3 — otsikko',      'type' => 'text',     'default' => 'Someapu' ),
			array( 'key' => 'service_3_text',   'label' => 'Palvelu 3 — kuvaus',       'type' => 'textarea', 'default' => 'Autamme mielellään yrityksiä somen käyttöön ja perusasioiden hallintaan. Voimme kasvattaa markkinoinnin osaamista yrityksessäsi ja pitää näin kiinni siitä että vahvuutesi arjessa kasvaa ja samalla voit keskittyä vapaa-aikaasi entistä huolettomammin.' ),
			array( 'key' => 'cta_label',        'label' => 'CTA-painikkeen teksti',    'type' => 'text',     'default' => 'Ota yhteyttä ja pyydä tarjous' ),
			array( 'key' => 'process_heading',  'label' => 'Prosessi — otsikko',       'type' => 'text',     'default' => 'Miten brändi- ja mainosvideot tuotetaan?' ),
			array( 'key' => 'process_subtitle', 'label' => 'Prosessi — alaotsikko',    'type' => 'text',     'default' => 'Mallina suosittu paketti: 10 × 30–60 s video' ),
			array( 'key' => 'step_1_title',     'label' => 'Vaihe 1 — otsikko',        'type' => 'text',     'default' => 'Yhteydenotto' ),
			array( 'key' => 'step_1_text',      'label' => 'Vaihe 1 — kuvaus',         'type' => 'textarea', 'default' => 'Kaikki alkaa kontaktoinnista, jonka aikana selvitetään tuotannon paketti. Tässä yhteydessä huomioidaan tarvittava kalusto, rekvisiitat sekä kuvauspaikka. Hintaan vaikuttaa puhtaasti käytettävä aika.' ),
			array( 'key' => 'step_2_title',     'label' => 'Vaihe 2 — otsikko',        'type' => 'text',     'default' => 'Kuvauspäivä' ),
			array( 'key' => 'step_2_text',      'label' => 'Vaihe 2 — kuvaus',         'type' => 'textarea', 'default' => 'Kuvaus tapahtuu peruspaketissa yhden päivän aikana. Jos kuvauksien yhteydessä tulee tarpeita mieleen, pystymme lisäämään pakettiin muitakin kuvauksia. Esimerkiksi henkilökuvauksia ja brändikuvia.' ),
			array( 'key' => 'step_3_title',     'label' => 'Vaihe 3 — otsikko',        'type' => 'text',     'default' => 'Editointi' ),
			array( 'key' => 'step_3_text',      'label' => 'Vaihe 3 — kuvaus',         'type' => 'textarea', 'default' => 'Editointi suoritetaan seuraavien päivien aikana valmiiksi. Jos toiveet ovat kokonaisuudessaan selvät, koko prosessi hoituu viikon sisään.' ),
			array( 'key' => 'quote_text',       'label' => 'Sitaatti — teksti',        'type' => 'textarea', 'default' => '”Olen huomannut että visuaaliselle silmälle on usein tarvetta, mutta silti tärkeintä on tarttua työhön ja saattaa se valmiiksi.”' ),
			array( 'key' => 'quote_author',     'label' => 'Sitaatti — kirjoittaja',   'type' => 'text',     'default' => 'Tomppa' ),
			array( 'key' => 'contact_heading',  'label' => 'Yhteydenotto — otsikko',   'type' => 'textarea', 'default' => 'Raider Production tarjoaa somepalvelua niin kuvauksiin ja videotuotantoon kuin somen ylläpitoonkin.' ),
			array( 'key' => 'contact_sub',      'label' => 'Yhteydenotto — alaotsikko','type' => 'text',     'default' => 'Jätä yhteydenottopyyntö' ),
		),

		'hinnoittelu'  => array(
			array( 'key' => 'intro_text', 'label' => 'Sivun johdantoteksti', 'type' => 'textarea', 'default' => 'Selkeät paketit ja läpinäkyvä hinnoittelu. Kaikki hinnat alv 0 %.' ),
		),

		'meista'       => array(
			array( 'key' => 'about_p1',    'label' => 'Kappale 1',          'type' => 'textarea', 'default' => 'Raider Production Oy syntyi intohimosta perhokalastuksen kuvaamiseen ja sivutoimisen yritystoiminnan kehittämiseen Suomen luonnossa. Perustaja Tommilla on noin kymmenen vuoden kokemus B2B-myynnistä, ja yrityksen ytimessä on aito kumppanuus asiakkaiden kanssa.' ),
			array( 'key' => 'about_p2',    'label' => 'Kappale 2',          'type' => 'textarea', 'default' => 'Tavoitteenamme on tuottaa tehokkaita, laadukkaita brändi- ja lyhytvideoita yritysten tarpeisiin sekä tarjota matalan kynnyksen someapua. Hoidamme visuaalisen viestinnän ja teemme video- ja kuvatuotannosta mukavan vaihtelun arkeen.' ),
			array( 'key' => 'about_p3',    'label' => 'Kappale 3',          'type' => 'textarea', 'default' => 'Videoiden lisäksi toteutamme myös nettisivuprojekteja, logojen vektoroinnit ja logosuunnittelua. Nämä visuaalisesti vaativat työt ovat meille mieluisia haasteita — ymmärrämme, että niiden takana on aina jotain tunteellisesti merkittävää.' ),
			array( 'key' => 'cta_heading', 'label' => 'CTA-osio — otsikko', 'type' => 'text',     'default' => 'Tehdäänkö yhdessä jotain hienoa?' ),
			array( 'key' => 'cta_text',    'label' => 'CTA-osio — teksti',  'type' => 'text',     'default' => 'Soita, sähköpostita tai jätä yhteydenotto.' ),
		),

		'ota-yhteytta' => array(
			array( 'key' => 'lead_text',    'label' => 'Yhteyshenkilön esittely', 'type' => 'textarea', 'default' => 'Lyhytvideot, mainosvideot ja someapu yrityksellesi. Tehdään yhdessä tehokasta sisältöä, joka tuottaa tulosta.' ),
			array( 'key' => 'form_heading', 'label' => 'Lomake — otsikko',        'type' => 'text',     'default' => 'Boostia somemarkkinointiin' ),
			array( 'key' => 'form_subtext', 'label' => 'Lomake — alaotsikko',     'type' => 'textarea', 'default' => 'Kerro lyhyesti, mitä tarvitset — palaan asiaan yleensä saman päivän aikana.' ),
		),

		'kiitos'       => array(
			array( 'key' => 'message', 'label' => 'Kiitos-viesti', 'type' => 'textarea', 'default' => 'Otamme sinuun yhteyttä yleensä saman päivän aikana.' ),
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
 * Look up a field config entry by key for the current post.
 */
function raider_field_config( $key ) {
	$post = get_post();
	if ( ! $post ) {
		return null;
	}
	$slug = raider_config_slug_for_post( $post );
	$cfg  = raider_page_fields_config();
	if ( ! isset( $cfg[ $slug ] ) ) {
		return null;
	}
	foreach ( $cfg[ $slug ] as $f ) {
		if ( $f['key'] === $key ) {
			return $f;
		}
	}
	return null;
}

/**
 * Get saved field value, falling back to the configured default.
 * An explicit $default arg overrides the config default.
 */
function raider_field( $key, $default = null ) {
	$post = get_post();
	$value = $post ? get_post_meta( $post->ID, '_raider_' . $key, true ) : '';
	if ( '' !== $value && null !== $value ) {
		return $value;
	}
	if ( null !== $default ) {
		return $default;
	}
	$cfg = raider_field_config( $key );
	return $cfg && isset( $cfg['default'] ) ? $cfg['default'] : '';
}

/**
 * Render a field, preserving line breaks for textareas.
 */
function raider_the_field( $key, $default = null, $paragraphs = false ) {
	$value = raider_field( $key, $default );
	if ( $paragraphs ) {
		echo wp_kses_post( wpautop( $value ) );
	} else {
		echo nl2br( esc_html( $value ) );
	}
}

/* ---------- Admin: meta box ---------- */

add_action( 'add_meta_boxes', function () {
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
	?>
	<style>
		.raider-field{margin:1.1rem 0;padding-bottom:.9rem;border-bottom:1px solid #f0f0f1}
		.raider-field:last-child{border-bottom:0}
		.raider-field label{display:block;font-weight:600;margin-bottom:.35rem}
		.raider-field input[type=text],.raider-field textarea{width:100%;font-size:14px}
		.raider-field textarea{min-height:90px}
		.raider-field .description{color:#646970;font-size:12px;margin:.3rem 0 0}
		.raider-reset{margin-left:.5rem;font-size:11px;color:#2271b1;text-decoration:underline;background:none;border:0;cursor:pointer;padding:0}
		.raider-reset:hover{color:#c10000}
		.raider-help{background:#f6f7f7;border-left:4px solid #c10000;padding:.6rem .8rem;margin-bottom:1rem;font-size:13px}
	</style>
	<p class="raider-help">
		<?php esc_html_e( 'Kentät on esitäytetty oletusteksteillä. Muokkaa ne haluamiksesi ja paina Päivitä. Tyhjäksi jätetty kenttä palauttaa oletustekstin.', 'raider' ); ?>
	</p>
	<?php
	foreach ( $config[ $slug ] as $field ) {
		$id      = 'raider_' . $field['key'];
		$saved   = get_post_meta( $post->ID, '_raider_' . $field['key'], true );
		$default = isset( $field['default'] ) ? $field['default'] : '';
		// Show saved value if any, otherwise pre-fill with default so editor can see what they're editing.
		$value   = ( '' !== $saved ) ? $saved : $default;
		?>
		<div class="raider-field">
			<label for="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $field['label'] ); ?>
				<?php if ( '' !== $saved ) : ?>
					<button type="button" class="raider-reset" data-target="<?php echo esc_attr( $id ); ?>" data-default="<?php echo esc_attr( $default ); ?>"><?php esc_html_e( 'Palauta oletusteksti', 'raider' ); ?></button>
				<?php endif; ?>
			</label>
			<?php if ( 'textarea' === $field['type'] ) : ?>
				<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
			<?php else : ?>
				<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>">
			<?php endif; ?>
		</div>
		<?php
	}
	?>
	<script>
	(function(){
		document.querySelectorAll('.raider-reset').forEach(function(btn){
			btn.addEventListener('click', function(){
				var el = document.getElementById(btn.dataset.target);
				if (el) { el.value = btn.dataset.default || ''; el.focus(); }
			});
		});
	})();
	</script>
	<?php
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
		$default = isset( $field['default'] ) ? $field['default'] : '';
		// If submitted value equals default OR is empty, store nothing — fall back to default at render time.
		if ( '' === $value || $value === $default ) {
			delete_post_meta( $post_id, '_raider_' . $field['key'] );
		} else {
			update_post_meta( $post_id, '_raider_' . $field['key'], $value );
		}
	}
} );

/**
 * Disable block editor on pages we render fully from PHP.
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
 * Hide classic editor content area on managed pages.
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
