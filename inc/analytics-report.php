<?php
/**
 * Visitor analytics report in the admin (menu: Kävijäseuranta).
 *
 * @package Raider
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		add_menu_page( 'Kävijäseuranta', 'Kävijäseuranta', 'edit_pages', 'raider-stats', 'raider_stats_report_page', 'dashicons-chart-bar', 3 );
	}
);

/**
 * Duration as "1 min 24 s".
 *
 * @param float $seconds Seconds.
 */
function raider_stats_format_duration( $seconds ) {
	$s = (int) round( $seconds );
	if ( $s <= 0 ) {
		return '–';
	}
	return $s < 60 ? $s . ' s' : floor( $s / 60 ) . ' min ' . ( $s % 60 ) . ' s';
}

/**
 * Change against the previous period of the same length.
 *
 * @param int $now      Value in this period.
 * @param int $previous Value in the previous period.
 */
function raider_stats_format_change( $now, $previous ) {
	if ( $previous <= 0 ) {
		return $now > 0 ? 'edellisellä jaksolla 0' : '';
	}
	$p = (int) round( ( $now - $previous ) / $previous * 100 );
	if ( 0 === $p ) {
		return 'sama kuin edellisellä jaksolla (' . number_format_i18n( $previous ) . ')';
	}
	return ( $p > 0 ? '▲ +' : '▼ −' ) . abs( $p ) . ' % edelliseen jaksoon (' . number_format_i18n( $previous ) . ')';
}

/**
 * Page name from its path.
 *
 * @param string $path Path.
 */
function raider_stats_page_name( $path ) {
	return '/' === $path ? 'Etusivu' : $path;
}

/**
 * Bar chart: visitors over the period. One series, so no legend is needed.
 *
 * @param array $bars Rows: array( 'label' => , 'visitors' => , 'views' => ).
 */
function raider_stats_chart( $bars ) {
	$count = count( $bars );
	if ( 0 === $count ) {
		return;
	}
	$w      = 760;
	$h      = 230;
	$left   = 40;
	$right  = 10;
	$top    = 12;
	$bottom = 28;
	$base_y = $h - $bottom;
	$area_w = $w - $left - $right;
	$area_h = $base_y - $top;

	$largest = 1;
	foreach ( $bars as $b ) {
		$largest = max( $largest, $b['visitors'] );
	}
	// Tidy axis maximum: 1, 2 or 5 times a power of ten, four intervals.
	$step = 1;
	foreach ( array( 1, 2, 5, 10, 20, 50, 100, 200, 500, 1000, 2000, 5000, 10000, 20000, 50000 ) as $a ) {
		$step = $a;
		if ( $a * 4 >= $largest ) {
			break;
		}
	}
	$max = $step * 4;

	$slot        = $area_w / $count;
	$bar_w       = max( 2, min( 24, $slot - 2 ) );
	$label_every = (int) max( 1, ceil( $count / 8 ) );

	echo '<div class="rs-chart"><svg viewBox="0 0 ' . (int) $w . ' ' . (int) $h . '" role="img" aria-label="Kävijät jakson aikana">';
	for ( $i = 0; $i <= 4; $i++ ) {
		$y = $base_y - $area_h * $i / 4;
		echo '<line class="rs-grid" x1="' . (int) $left . '" x2="' . (int) ( $w - $right ) . '" y1="' . esc_attr( round( $y, 1 ) ) . '" y2="' . esc_attr( round( $y, 1 ) ) . '"/>';
		echo '<text class="rs-axis" x="' . (int) ( $left - 6 ) . '" y="' . esc_attr( round( $y + 4, 1 ) ) . '" text-anchor="end">' . esc_html( number_format_i18n( $step * $i ) ) . '</text>';
	}
	foreach ( $bars as $i => $b ) {
		$x   = $left + $slot * $i + ( $slot - $bar_w ) / 2;
		$bh  = $area_h * $b['visitors'] / $max;
		$y   = $base_y - $bh;
		$r   = min( 4, $bar_w / 2, $bh );
		$tip = $b['label'] . ': ' . number_format_i18n( $b['visitors'] ) . ' kävijää, ' . number_format_i18n( $b['views'] ) . ' katselua';
		echo '<g class="rs-bar" tabindex="0" data-tip="' . esc_attr( $tip ) . '">';
		echo '<title>' . esc_html( $tip ) . '</title>';
		// The hit area is the whole column, so even a thin bar is easy to point at.
		echo '<rect class="rs-hit" x="' . esc_attr( round( $left + $slot * $i, 1 ) ) . '" y="' . (int) $top . '" width="' . esc_attr( round( $slot, 1 ) ) . '" height="' . (int) $area_h . '"/>';
		if ( $bh > 0 ) {
			// Rounded top, straight bottom on the baseline.
			$d = 'M' . round( $x, 1 ) . ',' . round( $base_y, 1 )
				. ' V' . round( $y + $r, 1 )
				. ' Q' . round( $x, 1 ) . ',' . round( $y, 1 ) . ' ' . round( $x + $r, 1 ) . ',' . round( $y, 1 )
				. ' H' . round( $x + $bar_w - $r, 1 )
				. ' Q' . round( $x + $bar_w, 1 ) . ',' . round( $y, 1 ) . ' ' . round( $x + $bar_w, 1 ) . ',' . round( $y + $r, 1 )
				. ' V' . round( $base_y, 1 ) . ' Z';
			echo '<path class="rs-fill" d="' . esc_attr( $d ) . '"/>';
		}
		echo '</g>';
		if ( 0 === $i % $label_every ) {
			echo '<text class="rs-axis" x="' . esc_attr( round( $left + $slot * $i + $slot / 2, 1 ) ) . '" y="' . (int) ( $h - 8 ) . '" text-anchor="middle">' . esc_html( $b['label'] ) . '</text>';
		}
	}
	echo '<line class="rs-base" x1="' . (int) $left . '" x2="' . (int) ( $w - $right ) . '" y1="' . (int) $base_y . '" y2="' . (int) $base_y . '"/>';
	echo '</svg><div class="rs-tip" hidden></div></div>';
}

/**
 * Table cell with a thin share bar next to the number.
 *
 * @param int $value   Value.
 * @param int $largest Largest value in the column.
 */
function raider_stats_meter( $value, $largest ) {
	$share = $largest > 0 ? max( 2, (int) round( $value / $largest * 100 ) ) : 0;
	return '<span class="rs-value">' . esc_html( number_format_i18n( $value ) ) . '</span>'
		. '<span class="rs-meter" aria-hidden="true"><span style="width:' . (int) $share . '%"></span></span>';
}

function raider_stats_report_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	global $wpdb;
	$views  = raider_stats_table( 'views' );
	$events = raider_stats_table( 'events' );

	$periods = array(
		7   => '7 päivää',
		30  => '30 päivää',
		90  => '3 kuukautta',
		365 => '12 kuukautta',
	);
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$period = isset( $_GET['jakso'] ) ? (int) $_GET['jakso'] : 30;
	if ( ! isset( $periods[ $period ] ) ) {
		$period = 30;
	}
	$today      = wp_date( 'Y-m-d' );
	$now_ts     = strtotime( $today . ' 12:00:00' );
	$start      = gmdate( 'Y-m-d', $now_ts - ( $period - 1 ) * DAY_IN_SECONDS );
	$prev_end   = gmdate( 'Y-m-d', $now_ts - $period * DAY_IN_SECONDS );
	$prev_start = gmdate( 'Y-m-d', $now_ts - ( 2 * $period - 1 ) * DAY_IN_SECONDS );

	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$total         = $wpdb->get_row( $wpdb->prepare( "SELECT COUNT(*) AS n, COUNT(DISTINCT visitor) AS k, COALESCE(SUM(duration),0) AS s, COALESCE(SUM(CASE WHEN duration > 0 THEN 1 ELSE 0 END),0) AS m FROM $views WHERE day BETWEEN %s AND %s", $start, $today ) );
	$prev_visitors = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT visitor) FROM $views WHERE day BETWEEN %s AND %s", $prev_start, $prev_end ) );
	$days          = $wpdb->get_results( $wpdb->prepare( "SELECT day, COUNT(*) AS n, COUNT(DISTINCT visitor) AS k FROM $views WHERE day BETWEEN %s AND %s GROUP BY day", $start, $today ), OBJECT_K );
	$sources       = $wpdb->get_results( $wpdb->prepare( "SELECT source, COUNT(*) AS k FROM $views WHERE id IN (SELECT MIN(id) FROM $views WHERE day BETWEEN %s AND %s GROUP BY visitor) GROUP BY source ORDER BY k DESC LIMIT 15", $start, $today ) );
	$campaigns     = $wpdb->get_results( $wpdb->prepare( "SELECT campaign, COUNT(DISTINCT visitor) AS k FROM $views WHERE day BETWEEN %s AND %s AND campaign <> '' GROUP BY campaign ORDER BY k DESC LIMIT 10", $start, $today ) );
	$pages         = $wpdb->get_results( $wpdb->prepare( "SELECT path, COUNT(*) AS n, COUNT(DISTINCT visitor) AS k, COALESCE(SUM(duration),0) AS s, COALESCE(SUM(CASE WHEN duration > 0 THEN 1 ELSE 0 END),0) AS m FROM $views WHERE day BETWEEN %s AND %s GROUP BY path ORDER BY n DESC LIMIT 15", $start, $today ) );
	$devices       = $wpdb->get_results( $wpdb->prepare( "SELECT device, COUNT(DISTINCT visitor) AS k FROM $views WHERE day BETWEEN %s AND %s GROUP BY device ORDER BY k DESC", $start, $today ) );
	$types         = $wpdb->get_results( $wpdb->prepare( "SELECT type, COUNT(*) AS n, COUNT(DISTINCT visitor) AS k FROM $events WHERE day BETWEEN %s AND %s GROUP BY type ORDER BY n DESC", $start, $today ), OBJECT_K );
	$contacted     = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT visitor) FROM $events WHERE day BETWEEN %s AND %s", $start, $today ) );
	$prev_contact  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT visitor) FROM $events WHERE day BETWEEN %s AND %s", $prev_start, $prev_end ) );
	$latest        = $wpdb->get_results( $wpdb->prepare( "SELECT created, type, target, path FROM $events WHERE day BETWEEN %s AND %s ORDER BY id DESC LIMIT 25", $start, $today ) );
	// Source of the visitors who made contact: the source of the visitor's first view of the day.
	$source_contact = $wpdb->get_results( $wpdb->prepare( "SELECT source, COUNT(*) AS k FROM $views WHERE id IN (SELECT MIN(id) FROM $views WHERE day BETWEEN %s AND %s GROUP BY visitor) AND visitor IN (SELECT DISTINCT visitor FROM $events WHERE day BETWEEN %s AND %s) GROUP BY source", $start, $today, $start, $today ), OBJECT_K );
	// phpcs:enable

	$visitors      = $total ? (int) $total->k : 0;
	$view_count    = $total ? (int) $total->n : 0;
	$time_visitor  = $visitors > 0 ? (int) $total->s / $visitors : 0;
	$time_page     = ( $total && (int) $total->m > 0 ) ? (int) $total->s / (int) $total->m : 0;
	$contact_share = $visitors > 0 ? round( $contacted / $visitors * 100, 1 ) : 0;

	// Chart bars: day, week or month depending on the period length.
	$bars = array();
	for ( $i = $period - 1; $i >= 0; $i-- ) {
		$ts  = $now_ts - $i * DAY_IN_SECONDS;
		$day = gmdate( 'Y-m-d', $ts );
		if ( $period <= 31 ) {
			$key   = $day;
			$label = gmdate( 'j.n.', $ts );
		} elseif ( $period <= 120 ) {
			$key   = gmdate( 'o-W', $ts );
			$label = 'vk ' . (int) gmdate( 'W', $ts );
		} else {
			$key   = gmdate( 'Y-m', $ts );
			$label = gmdate( 'n/Y', $ts );
		}
		if ( ! isset( $bars[ $key ] ) ) {
			$bars[ $key ] = array(
				'label'    => $label,
				'visitors' => 0,
				'views'    => 0,
			);
		}
		if ( isset( $days[ $day ] ) ) {
			$bars[ $key ]['visitors'] += (int) $days[ $day ]->k;
			$bars[ $key ]['views']    += (int) $days[ $day ]->n;
		}
	}
	$bars = array_values( $bars );

	$type_names = raider_stats_event_types();
	$started    = get_option( 'raider_stats_started' );
	$page_url   = admin_url( 'admin.php?page=raider-stats' );
	?>
	<div class="wrap rs">
		<h1>Kävijäseuranta</h1>
		<p class="rs-periods">
			<?php foreach ( $periods as $length => $name ) : ?>
				<a class="button<?php echo $length === $period ? ' button-primary' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'jakso', $length, $page_url ) ); ?>"<?php echo $length === $period ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $name ); ?></a>
			<?php endforeach; ?>
			<span class="rs-range"><?php echo esc_html( wp_date( 'j.n.Y', strtotime( $start ) ) . ' – ' . wp_date( 'j.n.Y', strtotime( $today ) ) ); ?></span>
		</p>

		<?php if ( 0 === $view_count ) : ?>
			<div class="notice notice-info inline"><p>Tältä jaksolta ei ole vielä tietoja.<?php echo $started ? ' Seuranta alkoi ' . esc_html( wp_date( 'j.n.Y', strtotime( $started ) ) ) . '.' : ''; ?> Kirjautuneiden käyttäjien omia käyntejä ei lasketa.</p></div>
		<?php endif; ?>

		<div class="rs-tiles">
			<div class="rs-tile">
				<div class="rs-name">Kävijät</div>
				<div class="rs-figure"><?php echo esc_html( number_format_i18n( $visitors ) ); ?></div>
				<div class="rs-note"><?php echo esc_html( raider_stats_format_change( $visitors, $prev_visitors ) ); ?></div>
			</div>
			<div class="rs-tile">
				<div class="rs-name">Sivunkatselut</div>
				<div class="rs-figure"><?php echo esc_html( number_format_i18n( $view_count ) ); ?></div>
				<div class="rs-note"><?php echo $visitors > 0 ? esc_html( number_format_i18n( $view_count / $visitors, 1 ) . ' sivua / kävijä' ) : ''; ?></div>
			</div>
			<div class="rs-tile">
				<div class="rs-name">Aika sivustolla / kävijä</div>
				<div class="rs-figure"><?php echo esc_html( raider_stats_format_duration( $time_visitor ) ); ?></div>
				<div class="rs-note"><?php echo $time_page > 0 ? esc_html( 'keskimäärin ' . raider_stats_format_duration( $time_page ) . ' / sivu' ) : ''; ?></div>
			</div>
			<div class="rs-tile">
				<div class="rs-name">Yhteyttä ottaneet kävijät</div>
				<div class="rs-figure"><?php echo esc_html( number_format_i18n( $contacted ) ); ?></div>
				<div class="rs-note"><?php echo esc_html( trim( ( $visitors > 0 ? number_format_i18n( $contact_share, 1 ) . ' % kävijöistä. ' : '' ) . raider_stats_format_change( $contacted, $prev_contact ) ) ); ?></div>
			</div>
		</div>

		<div class="rs-card">
			<h2>Kävijät <?php echo $period <= 31 ? 'päivittäin' : ( $period <= 120 ? 'viikoittain' : 'kuukausittain' ); ?></h2>
			<?php raider_stats_chart( $bars ); ?>
			<details>
				<summary>Näytä luvut taulukkona</summary>
				<table class="widefat striped rs-table">
					<thead><tr><th>Jakso</th><th class="rs-num">Kävijät</th><th class="rs-num">Katselut</th></tr></thead>
					<tbody>
					<?php foreach ( $bars as $b ) : ?>
						<tr><td><?php echo esc_html( $b['label'] ); ?></td><td class="rs-num"><?php echo esc_html( number_format_i18n( $b['visitors'] ) ); ?></td><td class="rs-num"><?php echo esc_html( number_format_i18n( $b['views'] ) ); ?></td></tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</details>
		</div>

		<div class="rs-grid2">
			<div class="rs-card">
				<h2>Mistä kävijät tulivat</h2>
				<?php if ( $sources ) : ?>
				<table class="widefat striped rs-table">
					<thead><tr><th>Lähde</th><th class="rs-num">Kävijät</th><th class="rs-num">Joista otti yhteyttä</th></tr></thead>
					<tbody>
					<?php
					$largest = (int) $sources[0]->k;
					foreach ( $sources as $row ) :
						$contact_count = isset( $source_contact[ $row->source ] ) ? (int) $source_contact[ $row->source ]->k : 0;
						?>
						<tr>
							<td><?php echo '' === $row->source ? 'Suora käynti tai tuntematon' : esc_html( $row->source ); ?></td>
							<td class="rs-num"><?php echo raider_stats_meter( (int) $row->k, $largest ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
							<td class="rs-num"><?php echo esc_html( number_format_i18n( $contact_count ) ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<p class="description">Lähde on kävijän päivän ensimmäisen sivun viittaaja. Suora käynti tarkoittaa esimerkiksi kirjoitettua osoitetta, kirjanmerkkiä tai sovellusta, joka ei kerro lähdettä.</p>
				<?php else : ?>
				<p class="description">Ei tietoja.</p>
				<?php endif; ?>

				<?php if ( $campaigns ) : ?>
				<h3>Kampanjat (utm_campaign)</h3>
				<table class="widefat striped rs-table">
					<thead><tr><th>Kampanja</th><th class="rs-num">Kävijät</th></tr></thead>
					<tbody>
					<?php foreach ( $campaigns as $row ) : ?>
						<tr><td><?php echo esc_html( $row->campaign ); ?></td><td class="rs-num"><?php echo esc_html( number_format_i18n( (int) $row->k ) ); ?></td></tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php endif; ?>
			</div>

			<div class="rs-card">
				<h2>Yhteydenotot</h2>
				<table class="widefat striped rs-table">
					<thead><tr><th>Tapa</th><th class="rs-num">Kertoja</th><th class="rs-num">Kävijöitä</th></tr></thead>
					<tbody>
					<?php foreach ( $type_names as $type => $name ) : ?>
						<tr>
							<td><?php echo esc_html( $name ); ?></td>
							<td class="rs-num"><?php echo esc_html( number_format_i18n( isset( $types[ $type ] ) ? (int) $types[ $type ]->n : 0 ) ); ?></td>
							<td class="rs-num"><?php echo esc_html( number_format_i18n( isset( $types[ $type ] ) ? (int) $types[ $type ]->k : 0 ) ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<p class="description">Puhelin, sähköposti ja WhatsApp lasketaan linkin napautuksesta: se kertoo aikomuksesta ottaa yhteyttä, ei varmista soittoa tai lähetettyä viestiä. Lomake lasketaan, kun kävijä on lähettänyt yhteydenottolomakkeen.</p>
			</div>
		</div>

		<div class="rs-grid2">
			<div class="rs-card">
				<h2>Katsotuimmat sivut</h2>
				<?php if ( $pages ) : ?>
				<table class="widefat striped rs-table">
					<thead><tr><th>Sivu</th><th class="rs-num">Katselut</th><th class="rs-num">Kävijät</th><th class="rs-num">Aika / katselu</th></tr></thead>
					<tbody>
					<?php
					$largest = (int) $pages[0]->n;
					foreach ( $pages as $row ) :
						?>
						<tr>
							<td><a href="<?php echo esc_url( home_url( $row->path ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( raider_stats_page_name( $row->path ) ); ?></a></td>
							<td class="rs-num"><?php echo raider_stats_meter( (int) $row->n, $largest ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
							<td class="rs-num"><?php echo esc_html( number_format_i18n( (int) $row->k ) ); ?></td>
							<td class="rs-num"><?php echo esc_html( raider_stats_format_duration( (int) $row->m > 0 ? (int) $row->s / (int) $row->m : 0 ) ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php else : ?>
				<p class="description">Ei tietoja.</p>
				<?php endif; ?>
			</div>

			<div class="rs-card">
				<h2>Laitteet</h2>
				<?php if ( $devices ) : ?>
				<table class="widefat striped rs-table">
					<thead><tr><th>Laite</th><th class="rs-num">Kävijät</th></tr></thead>
					<tbody>
					<?php
					$largest      = (int) $devices[0]->k;
					$device_names = array(
						'mobile'  => 'Puhelin',
						'tablet'  => 'Tabletti',
						'desktop' => 'Tietokone',
					);
					foreach ( $devices as $row ) :
						?>
						<tr>
							<td><?php echo esc_html( isset( $device_names[ $row->device ] ) ? $device_names[ $row->device ] : 'Tuntematon' ); ?></td>
							<td class="rs-num"><?php echo raider_stats_meter( (int) $row->k, $largest ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php else : ?>
				<p class="description">Ei tietoja.</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="rs-card">
			<h2>Viimeisimmät yhteydenotot</h2>
			<?php if ( $latest ) : ?>
			<table class="widefat striped rs-table">
				<thead><tr><th>Aika</th><th>Tapa</th><th>Kohde</th><th>Sivu</th></tr></thead>
				<tbody>
				<?php foreach ( $latest as $row ) : ?>
					<tr>
						<td><?php echo esc_html( mysql2date( 'j.n.Y H:i', $row->created ) ); ?></td>
						<td><?php echo esc_html( isset( $type_names[ $row->type ] ) ? $type_names[ $row->type ] : $row->type ); ?></td>
						<td><?php echo esc_html( $row->target ); ?></td>
						<td><?php echo esc_html( raider_stats_page_name( $row->path ) ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php else : ?>
			<p class="description">Ei yhteydenottoja tällä jaksolla.</p>
			<?php endif; ?>
		</div>

		<p class="description rs-footnote">Seuranta ei käytä evästeitä eikä tallenna IP-osoitteita. Kävijä tunnistetaan nimettömästi yhden päivän ajan, joten usean päivän jaksolla sama ihminen lasketaan kävijäksi jokaisena käyntipäivänään. Kirjautuneita käyttäjiä ja hakurobotteja ei lasketa. Tiedot säilytetään 25 kuukautta.</p>
	</div>

	<style>
		.rs{--rs-surface:#fff;--rs-ink:#1d2327;--rs-ink2:#50575e;--rs-line:#e3e4e7;--rs-series:#c10000;--rs-series-dark:#8a0000;--rs-track:#f5dcdc;max-width:1200px}
		.rs .rs-periods{display:flex;flex-wrap:wrap;gap:6px;align-items:center;margin:14px 0 18px}
		.rs .rs-range{margin-left:10px;color:var(--rs-ink2)}
		.rs .rs-tiles{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:14px}
		.rs .rs-tile,.rs .rs-card{background:var(--rs-surface);border:1px solid #dcdcde;border-radius:6px;padding:16px 18px}
		.rs .rs-name{font-size:13px;color:var(--rs-ink2)}
		.rs .rs-figure{font-size:30px;line-height:1.25;font-weight:600;color:var(--rs-ink);margin:4px 0 2px}
		.rs .rs-note{font-size:12px;color:var(--rs-ink2);min-height:18px}
		.rs .rs-card{margin-bottom:14px}
		.rs .rs-card h2{font-size:15px;margin:0 0 12px;padding:0}
		.rs .rs-card h3{font-size:13px;margin:18px 0 8px}
		.rs .rs-grid2{display:grid;grid-template-columns:minmax(0,3fr) minmax(0,2fr);gap:14px}
		.rs .rs-table{border:0;box-shadow:none}
		.rs .rs-table th,.rs .rs-table td{padding:7px 10px;vertical-align:middle}
		.rs .rs-num{text-align:right;font-variant-numeric:tabular-nums;white-space:nowrap}
		.rs .rs-value{display:inline-block;min-width:34px}
		.rs .rs-meter{display:inline-block;width:72px;height:8px;margin-left:10px;border-radius:4px;background:var(--rs-track);vertical-align:middle;overflow:hidden}
		.rs .rs-meter span{display:block;height:100%;border-radius:4px;background:var(--rs-series)}
		.rs .rs-chart{position:relative}
		.rs .rs-chart svg{display:block;width:100%;height:auto;max-height:300px}
		.rs .rs-grid{stroke:var(--rs-line);stroke-width:1}
		.rs .rs-base{stroke:#c3c4c7;stroke-width:1}
		.rs .rs-axis{font-size:11px;fill:var(--rs-ink2);font-variant-numeric:tabular-nums}
		.rs .rs-fill{fill:var(--rs-series)}
		.rs .rs-hit{fill:transparent}
		.rs .rs-bar{outline:none;cursor:default}
		.rs .rs-bar:hover .rs-fill,.rs .rs-bar:focus .rs-fill{fill:var(--rs-series-dark)}
		.rs .rs-bar:hover .rs-hit,.rs .rs-bar:focus .rs-hit{fill:rgba(193,0,0,.07)}
		.rs .rs-tip{position:absolute;top:0;left:0;pointer-events:none;background:var(--rs-ink);color:#fff;font-size:12px;line-height:1.4;padding:6px 9px;border-radius:4px;white-space:nowrap;transform:translate(-50%,-100%)}
		.rs details{margin-top:10px}
		.rs summary{cursor:pointer;color:var(--rs-ink2)}
		.rs .rs-footnote{max-width:900px}
		@media (max-width:960px){.rs .rs-tiles{grid-template-columns:repeat(2,minmax(0,1fr))}.rs .rs-grid2{grid-template-columns:minmax(0,1fr)}}
	</style>
	<script>
	(function () {
		var chart = document.querySelector('.rs-chart');
		if (!chart) return;
		var tip = chart.querySelector('.rs-tip');
		function show(g) {
			var c = chart.getBoundingClientRect(), o = g.querySelector('.rs-hit').getBoundingClientRect();
			var p = g.querySelector('.rs-fill'), y = p ? p.getBoundingClientRect().top : o.bottom;
			tip.textContent = g.getAttribute('data-tip');
			tip.hidden = false;
			var x = o.left + o.width / 2 - c.left, half = tip.offsetWidth / 2;
			tip.style.left = Math.max(half, Math.min(c.width - half, x)) + 'px';
			tip.style.top = Math.max(tip.offsetHeight, y - c.top - 8) + 'px';
		}
		function hide() { tip.hidden = true; }
		chart.querySelectorAll('.rs-bar').forEach(function (g) {
			// drop the browser's own title tooltip once the custom one is in use
			var t = g.querySelector('title'); if (t) g.removeChild(t);
			g.setAttribute('aria-label', g.getAttribute('data-tip'));
			g.addEventListener('mouseenter', function () { show(g); });
			g.addEventListener('focus', function () { show(g); });
			g.addEventListener('mouseleave', hide);
			g.addEventListener('blur', hide);
		});
	})();
	</script>
	<?php
}
