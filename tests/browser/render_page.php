<?php
/**
 * Renders a REAL page template from the theme (between the header and footer)
 * with stand-ins for the WordPress functions it uses, so the browser tests
 * look at exactly the markup that ships — not a hand-copied version.
 *
 *   php tests/browser/render_page.php <template> <photo-url|""> [key=value …]
 *
 * key=value pairs become theme mods, e.g. tmc_provapa_season_open=0.
 * get_header()/get_footer() print nothing: harness.py wraps the output.
 */
require dirname( __DIR__ ) . '/php/bootstrap.php';

$template = $argv[1] ?? 'templates/page-prova-pa.php';
$photo    = $argv[2] ?? '';
foreach ( array_slice( $argv, 3 ) as $pair ) {
	list( $k, $v ) = array_pad( explode( '=', $pair, 2 ), 2, '' );
	$GLOBALS['__mods'][ $k ] = $v;
}
$GLOBALS['__mods'] += [ 'tmc_contact_email' => 'info@tandstiftet.se', 'tmc_contact_phone' => '040-12 34 56' ];
$_SERVER['REQUEST_METHOD'] = 'GET';

/* ── WordPress stand-ins (output only — no logic worth testing here) ── */
function get_header() {}
function get_footer() {}
function get_the_ID() { return 42; }
function get_page_by_path( $p ) { return (object) [ 'ID' => 7 ]; }
function get_permalink( $id ) { return '/sliding/'; }
function home_url( $p = '' ) { return $p; }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_textarea( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_html_e( $s, $d = null ) { echo esc_html( $s ); }
function esc_attr_e( $s, $d = null ) { echo esc_attr( $s ); }
function selected( $a, $b ) { if ( (string) $a === (string) $b ) echo ' selected="selected"'; }
function wp_nonce_field( $a, $n ) { echo '<input type="hidden" name="' . esc_attr( $n ) . '" value="test">'; }
function has_post_thumbnail( $id = null ) { return $GLOBALS['__photo'] !== ''; }
function get_the_post_thumbnail( $id, $size, $attr = [] ) {
	return '<img src="' . esc_url( $GLOBALS['__photo'] ) . '" class="' . esc_attr( $attr['class'] ?? '' ) . '" alt="Ung förare på banan" width="562" height="750">';
}
$GLOBALS['__photo'] = $photo;

/* For page.php: one page in "the loop", with content that uses the usual editor blocks */
$GLOBALS['__loop'] = 1;
function have_posts() { return $GLOBALS['__loop'] > 0; }
function the_post() { $GLOBALS['__loop']--; }
function get_the_title( $id = null ) { return 'Speedway'; }
function get_bloginfo( $k = '' ) { return 'Motorklubben Tändstiftet – Malmö'; }
function esc_html__( $s, $d = null ) { return esc_html( $s ); }
function esc_attr__( $s, $d = null ) { return esc_attr( $s ); }
function wp_link_pages() {}
function the_post_thumbnail( $size = '' ) { echo get_the_post_thumbnail( 0, $size, [] ); }
foreach ( [ 'is_home', 'is_category', 'is_tag', 'is_post_type_archive', 'is_tax', 'is_date', 'is_author', 'is_search', 'is_404' ] as $fn ) {
	eval( "function $fn() { return false; }" );
}
function is_singular( $type = '' ) { return $type === '' || $type === 'page'; }
function the_content() {
	echo '<p>Speedway körs på en oval grusbana med 500cc-motorcyklar <strong>utan bromsar</strong>. Det är den klassiska trackracing-grenen, och den <a href="#">vi tävlar i Division 1</a>.</p>'
		. '<h2>Så börjar man</h2><p>De flesta börjar på Prova På och fortsätter sedan på ordinarie träning.</p>'
		. '<ul><li>Träning onsdagar och lördagar</li><li>Utrustning finns att låna</li><li>Licens via Svemo</li></ul>'
		. '<h3>Bra att veta</h3><ol><li>Hör av er innan</li><li>Kom i god tid</li></ol>'
		. '<blockquote><p>"Det bästa jag gjort på länge."</p></blockquote>'
		. '<div class="wp-block-buttons"><div class="wp-block-button"><a class="wp-block-button__link" href="#">Prova på →</a></div></div>';
}

/* For the Sliding page */
function get_the_post_thumbnail_url( $id = null, $size = '' ) { return $GLOBALS['__photo']; }
function wp_json_encode( $v ) { return json_encode( $v ); }
// A believable calendar: open Wednesdays and Saturdays; tomorrow is "within 24 h",
// the second open day is full, the third has only 2 places left.
function tmc_sliding_get_calendar_data( $days = 90 ) {
	$out = [];
	foreach ( [ 'speedway', 'flattrack' ] as $disc ) {
		$n = 0;
		for ( $i = 0; $i < $days; $i++ ) {
			$d = ( new DateTime( 'today' ) )->modify( "+$i days" );
			$open = in_array( (int) $d->format( 'N' ), $disc === 'speedway' ? [ 3, 6 ] : [ 4 ], true ) || $i === 1;
			if ( $open && $i > 1 ) $n++;
			$out[ $disc ][ $d->format( 'Y-m-d' ) ] = [
				'open' => $open, 'within24h' => $i <= 1, 'time' => '18:00',
				'remaining' => ! $open ? 0 : ( $n === 2 ? 0 : ( $n === 3 ? 2 : 8 ) ),
			];
		}
	}
	return $out;
}
$GLOBALS['__mods'] += [ 'tmc_sliding_hours_wed_speedway' => '18:00', 'tmc_sliding_hours_sat_speedway' => '10:00', 'tmc_sliding_hours_thu_flattrack' => '18:00' ];

/* The theme's real helpers */
tmc_load( [
	'tmc_sliding_enabled', 'tmc_flattrack_enabled', 'tmc_provapa_season_open', 'tmc_provapa_season_message',
	'tmc_page_text_sets', 'tmc_page_text', 'tmc_page_items',
	'tmc_provapa_fields', 'tmc_provapa_text', 'tmc_provapa_slots', 'tmc_provapa_items', 'tmc_provapa_lines',
	'tmc_sliding_fields', 'tmc_sliding_text', 'tmc_sliding_items', 'tmc_sliding_facts', 'tmc_sliding_schedule_summary',
	'tmc_hero_title_html', 'tmc_antispam_fields', 'tmc_page_header', 'tmc_breadcrumbs',
	'tmc_page_sections', 'tmc_page_section_hideable', 'tmc_page_section_visible',
	'tmc_swedish_date', 'tmc_season_contains', 'tmc_season_next_start', 'tmc_season_md',
	'tmc_signup_settings', 'tmc_signup_open_on', 'tmc_signup_state',
] );
// Theme mods arrive as strings from the command line; '0' must mean "off" like WordPress' stored false.
foreach ( $GLOBALS['__mods'] as $k => $v ) { if ( $v === '0' ) $GLOBALS['__mods'][ $k ] = false; elseif ( $v === '1' ) $GLOBALS['__mods'][ $k ] = true; }

include TMC_THEME . '/' . $template;
