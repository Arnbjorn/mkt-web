<?php
/**
 * Tiny test harness — no PHPUnit, no Composer, nothing to install except PHP.
 *
 * The theme's functions live in functions.php, which can't be included outside
 * WordPress (it calls WordPress functions while loading). So tests pull the
 * REAL source of one function at a time out of the theme file (using PHP's own
 * tokenizer, so braces inside strings can't confuse it) and define it here,
 * next to a few stand-ins for the WordPress functions it relies on.
 */

define( 'TMC_ROOT', dirname( __DIR__, 2 ) );
define( 'TMC_THEME', TMC_ROOT . '/theme/' . tmc_theme_slug() );

function tmc_theme_slug() {
	$cfg = file_get_contents( dirname( __DIR__, 2 ) . '/scripts/config.sh' );
	if ( ! preg_match( '/^THEME_SLUG="([^"]+)"/m', $cfg, $m ) ) { fwrite( STDERR, "THEME_SLUG not found in scripts/config.sh\n" ); exit( 2 ); }
	return $m[1];
}

/* ── Stand-ins for WordPress ─────────────────────────────────── */
$GLOBALS['__options'] = [];   // get_option / update_option
$GLOBALS['__mods']    = [];   // get_theme_mod
$GLOBALS['__themes']  = [];   // wp_get_themes

class Fake_Theme {
	private $domain;
	public function __construct( $text_domain ) { $this->domain = $text_domain; }
	public function get( $key ) { return $key === 'TextDomain' ? $this->domain : ''; }
}
function get_option( $key, $default = false ) { return $GLOBALS['__options'][ $key ] ?? $default; }
function update_option( $key, $value )        { $GLOBALS['__options'][ $key ] = $value; return true; }
function get_theme_mod( $key, $default = false ) { return array_key_exists( $key, $GLOBALS['__mods'] ) ? $GLOBALS['__mods'][ $key ] : $default; }
function wp_get_themes()                      { return $GLOBALS['__themes']; }
function esc_html( $s )                       { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function wp_strip_all_tags( $s )              { return strip_tags( (string) $s ); }
function sanitize_email( $s )                 { return trim( (string) $s ); }

/* ── Pull real functions out of a theme file ─────────────────── */
function tmc_load( array $names, $file = 'functions.php' ) {
	static $loaded = [];
	$src    = file_get_contents( TMC_THEME . '/' . $file );
	$tokens = token_get_all( $src );
	$n      = count( $tokens );
	foreach ( $names as $name ) {
		if ( isset( $loaded[ $name ] ) ) continue;
		$start = null;
		for ( $i = 0; $i < $n; $i++ ) {
			if ( is_array( $tokens[ $i ] ) && $tokens[ $i ][0] === T_FUNCTION ) {
				$j = $i + 1;
				while ( is_array( $tokens[ $j ] ) && $tokens[ $j ][0] === T_WHITESPACE ) $j++;
				if ( is_array( $tokens[ $j ] ) && $tokens[ $j ][0] === T_STRING && $tokens[ $j ][1] === $name ) { $start = $i; break; }
			}
		}
		if ( $start === null ) throw new RuntimeException( "Function $name() not found in $file" );
		$code = ''; $depth = 0; $began = false;
		for ( $k = $start; $k < $n; $k++ ) {
			$t = $tokens[ $k ]; $code .= is_array( $t ) ? $t[1] : $t;
			if ( $t === '{' || ( is_array( $t ) && in_array( $t[0], [ T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ], true ) ) ) { $depth++; $began = true; }
			elseif ( $t === '}' ) { $depth--; if ( $began && $depth === 0 ) break; }
		}
		eval( $code ); // phpcs:ignore — test code only
		$loaded[ $name ] = true;
	}
}

/* ── Minimal test API ────────────────────────────────────────── */
$GLOBALS['__tests'] = [ 'pass' => 0, 'fail' => 0 ];
function reset_world() { $GLOBALS['__options'] = []; $GLOBALS['__mods'] = []; $GLOBALS['__themes'] = []; $_POST = []; }
function test( $name, callable $fn ) {
	reset_world();
	try { $fn(); $GLOBALS['__tests']['pass']++; echo "  \033[32m✓\033[0m $name\n"; }
	catch ( Throwable $e ) { $GLOBALS['__tests']['fail']++; echo "  \033[31m✗\033[0m $name\n      " . $e->getMessage() . "\n"; }
}
function same( $expected, $actual, $label = '' ) {
	if ( $expected !== $actual ) {
		throw new RuntimeException( ( $label ? "$label: " : '' ) . 'förväntade ' . var_export( $expected, true ) . ', fick ' . var_export( $actual, true ) );
	}
}
function truthy( $value, $label = '' ) { if ( ! $value ) throw new RuntimeException( ( $label ?: 'förväntade sant' ) ); }
function section( $title ) { echo "\n$title\n"; }
function tmc_test_report() {
	$t = $GLOBALS['__tests'];
	echo "\n{$t['pass']} godkända, {$t['fail']} misslyckade\n";
	return $t['fail'] > 0 ? 1 : 0;
}
