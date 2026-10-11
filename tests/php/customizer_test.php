<?php
/* The Customizer (inc/customizer.php) run against a recording stand-in: every
   control must land in a section that exists, nothing may be registered twice,
   and the special pages must get their own, ordered panels. */
tmc_load( [
	'tmc_page_text_sets', 'tmc_page_sections', 'tmc_page_section_hideable',
	'tmc_provapa_fields', 'tmc_sliding_fields', 'tmc_home_events_fields',
] );

if ( ! function_exists( 'add_action' ) ) {
	$GLOBALS['__actions'] = [];
	function add_action( $hook, $cb ) { $GLOBALS['__actions'][ $hook ][] = $cb; }
	function esc_html__( $s, $d = null ) { return $s; }
	function get_template_directory_uri() { return ''; }
	function get_bloginfo( $k = '' ) { return 'Tändstiftet'; }
	class WP_Customize_Media_Control  { public function __construct( $m, $id, $args ) { $this->id = $id; $this->args = $args; } }
	class WP_Customize_Image_Control  extends WP_Customize_Media_Control {}
	class WP_Customize_Color_Control  extends WP_Customize_Media_Control {}
	class Recording_Customizer {
		public $panels = [], $sections = [], $settings = [], $controls = [], $dupes = [];
		public function add_panel( $id, $a = [] )   { if ( isset( $this->panels[ $id ] ) ) $this->dupes[] = "panel $id"; $this->panels[ $id ] = $a; }
		public function add_section( $id, $a = [] ) { if ( isset( $this->sections[ $id ] ) ) $this->dupes[] = "section $id"; $this->sections[ $id ] = $a; }
		public function add_setting( $id, $a = [] ) { if ( isset( $this->settings[ $id ] ) ) $this->dupes[] = "setting $id"; $this->settings[ $id ] = $a; }
		public function add_control( $id, $a = [] ) {
			if ( is_object( $id ) ) { $a = $id->args; $id = $id->id; }
			if ( isset( $this->controls[ $id ] ) ) $this->dupes[] = "control $id";
			$this->controls[ $id ] = $a;
		}
		public function get_setting( $id ) { return null; }
		public function get_section( $id ) { return null; }
		public function get_control( $id ) { return null; }
		public function remove_section( $id ) {}
		public function remove_control( $id ) {}
	}
}

function tmc_run_customizer() {
	$GLOBALS['__actions'] = [];
	require TMC_THEME . '/inc/customizer.php';
	$c = new Recording_Customizer();
	foreach ( $GLOBALS['__actions']['customize_register'] ?? [] as $cb ) call_user_func( is_string( $cb ) ? $cb : $cb, $c );
	return $c;
}
$GLOBALS['__customizer'] = tmc_run_customizer();

section( 'Anpassa (Customizer)' );

test( 'inget registreras två gånger', function () {
	same( [], $GLOBALS['__customizer']->dupes );
} );

test( 'varje kontroll ligger i en sektion som finns, och varje sektion i en panel som finns', function () {
	$c = $GLOBALS['__customizer'];
	foreach ( $c->controls as $id => $a ) truthy( isset( $c->sections[ $a['section'] ?? '' ] ), "kontrollen $id pekar på en sektion som inte finns: " . ( $a['section'] ?? '?' ) );
	foreach ( $c->sections as $id => $a ) if ( ! empty( $a['panel'] ) ) truthy( isset( $c->panels[ $a['panel'] ] ), "sektionen $id pekar på en panel som inte finns" );
} );

test( 'Prova På och Sliding har varsin panel, med "Anmälan och säsong" överst och delarna i sidans ordning', function () {
	$c = $GLOBALS['__customizer'];
	foreach ( [ 'provapa' => 'tmc_panel_provapa', 'sliding' => 'tmc_panel_sliding' ] as $set => $panel ) {
		truthy( isset( $c->panels[ $panel ] ), "panelen $panel saknas" );
		$mine = array_filter( $c->sections, function ( $a ) use ( $panel ) { return ( $a['panel'] ?? '' ) === $panel; } );
		uasort( $mine, function ( $x, $y ) { return $x['priority'] <=> $y['priority']; } );
		same( array_values( array_keys( tmc_page_sections( $set ) ) ), array_values( array_map( function ( $a ) { return $a['title']; }, $mine ) ), "ordningen i $panel" );
	}
} );

test( 'varje textfält har en kontroll i rätt dels sektion', function () {
	$c = $GLOBALS['__customizer'];
	foreach ( [ 'provapa' => [ 'tmc_provapa_fields', 'tmc_pp_', 'tmc_panel_provapa' ], 'sliding' => [ 'tmc_sliding_fields', 'tmc_sl_', 'tmc_panel_sliding' ] ] as $set => list( $fn, $prefix, $panel ) ) {
		$slugs = tmc_page_sections( $set );
		foreach ( call_user_func( $fn ) as $key => $f ) {
			truthy( isset( $slugs[ $f[0] ] ), "fältet $key har gruppen '{$f[0]}' som inte är en del av sidan" );
			same( $panel . '_' . $slugs[ $f[0] ], $c->controls[ $prefix . $key ]['section'] ?? null, "sektion för $prefix$key" );
		}
	}
} );

test( 'delar som går att dölja har en "Visa"-ruta (på som standard); Hero och formuläret har ingen', function () {
	$c = $GLOBALS['__customizer'];
	foreach ( [ 'provapa' => 'tmc_pp_', 'sliding' => 'tmc_sl_' ] as $set => $prefix ) {
		foreach ( tmc_page_sections( $set ) as $slug ) {
			$has = isset( $c->settings[ $prefix . 'show_' . $slug ] );
			same( tmc_page_section_hideable( $slug ), $has, "visa-ruta för $prefix$slug" );
			if ( $has ) same( true, $c->settings[ $prefix . 'show_' . $slug ]['default'] );
		}
	}
} );

test( 'anmälan: läge (säsong som standard) och säsongsdatum 1 april – 31 oktober finns för båda sidorna', function () {
	$c = $GLOBALS['__customizer'];
	foreach ( [ 'tmc_pp_', 'tmc_sl_' ] as $p ) {
		same( 'season', $c->settings[ $p . 'signup_mode' ]['default'] );
		same( [ 'season', 'open', 'closed' ], array_keys( $c->controls[ $p . 'signup_mode' ]['choices'] ) );
		same( [ 4, 1, 10, 31 ], [ $c->settings[ $p . 'season_start_month' ]['default'], $c->settings[ $p . 'season_start_day' ]['default'], $c->settings[ $p . 'season_end_month' ]['default'], $c->settings[ $p . 'season_end_day' ]['default'] ] );
	}
} );

test( 'Sliding-pausen och Flat track-växeln ligger under Sida: Sliding → Anmälan och säsong', function () {
	$c = $GLOBALS['__customizer'];
	same( 'tmc_panel_sliding_signup', $c->controls['tmc_sliding_enabled']['section'] );
	same( 'tmc_panel_sliding_signup', $c->controls['tmc_flattrack_enabled']['section'] );
	same( 'tmc_panel_provapa_signup', $c->controls['tmc_provapa_season_message']['section'] );
} );

test( 'Startsida — Tävlingar: säsongen (1 april – första söndagen i oktober) och alla texter finns', function () {
	$c = $GLOBALS['__customizer'];
	same( 'tmc_panel', $c->sections['tmc_home_events']['panel'] ?? null );
	same( [ 'date', 4, 1, 'first_sunday', 10 ], [ $c->settings['tmc_comp_start_rule']['default'], $c->settings['tmc_comp_start_month']['default'], $c->settings['tmc_comp_start_day']['default'], $c->settings['tmc_comp_end_rule']['default'], $c->settings['tmc_comp_end_month']['default'] ] );
	foreach ( tmc_home_events_fields() as $key => $f ) same( 'tmc_home_events', $c->controls[ 'tmc_he_' . $key ]['section'] ?? null, "kontroll tmc_he_$key" );
} );
