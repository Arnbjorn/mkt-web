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

/* The theme's real helpers */
tmc_load( [
	'tmc_sliding_enabled', 'tmc_provapa_season_open', 'tmc_provapa_season_message',
	'tmc_provapa_fields', 'tmc_provapa_text', 'tmc_provapa_slots', 'tmc_provapa_items', 'tmc_provapa_lines',
	'tmc_antispam_fields',
] );
// Theme mods arrive as strings from the command line; '0' must mean "off" like WordPress' stored false.
foreach ( $GLOBALS['__mods'] as $k => $v ) { if ( $v === '0' ) $GLOBALS['__mods'][ $k ] = false; }

include TMC_THEME . '/' . $template;
