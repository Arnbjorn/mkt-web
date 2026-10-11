<?php
/* Signup / booking: open, closed, or following a season that repeats every year. */
tmc_load( [
	'tmc_page_text_sets', 'tmc_page_text', 'tmc_provapa_fields', 'tmc_sliding_fields', 'tmc_provapa_season_message',
	'tmc_page_sections', 'tmc_page_section_hideable', 'tmc_page_section_visible',
	'tmc_swedish_date', 'tmc_season_contains', 'tmc_season_next_start', 'tmc_season_md',
	'tmc_signup_settings', 'tmc_signup_open_on', 'tmc_signup_state',
] );
section( 'Anmälan och säsong' );

test( 'säsongen april–oktober: kanterna räknas, vintern är stängd', function () {
	same( true,  tmc_season_contains( '2027-04-01', '04-01', '10-31' ), '1 april' );
	same( true,  tmc_season_contains( '2027-10-31', '04-01', '10-31' ), '31 oktober' );
	same( true,  tmc_season_contains( '2027-07-15', '04-01', '10-31' ), 'mitt i sommaren' );
	same( false, tmc_season_contains( '2027-11-01', '04-01', '10-31' ), '1 november' );
	same( false, tmc_season_contains( '2027-03-31', '04-01', '10-31' ), '31 mars' );
	same( false, tmc_season_contains( '2027-01-10', '04-01', '10-31' ), 'januari' );
} );

test( 'en säsong över nyår fungerar också (t.ex. november–mars)', function () {
	same( true,  tmc_season_contains( '2027-12-24', '11-01', '03-31' ) );
	same( true,  tmc_season_contains( '2028-02-01', '11-01', '03-31' ) );
	same( false, tmc_season_contains( '2027-07-01', '11-01', '03-31' ) );
} );

test( 'nästa säsongsstart: i år om den inte varit, annars nästa år', function () {
	same( '2027-04-01', tmc_season_next_start( '2027-02-10', '04-01' ) );
	same( '2028-04-01', tmc_season_next_start( '2027-11-05', '04-01' ) );
	same( '2028-04-01', tmc_season_next_start( '2027-04-01', '04-01' ), 'samma dag → nästa år' );
} );

test( 'datum från Anpassa hålls giltiga (31 april → 30 april, månad 13 → december)', function () {
	same( '04-30', tmc_season_md( 4, 31 ) );
	same( '12-01', tmc_season_md( 13, 0 ) );
	same( '02-29', tmc_season_md( '2', '30' ), 'skottdag tillåts' );
} );

test( 'standard: följer säsongen 1 april – 31 oktober', function () {
	same( [ 'mode' => 'season', 'start' => '04-01', 'end' => '10-31' ], tmc_signup_settings( 'sliding' ) );
	same( true,  tmc_signup_state( 'sliding', '2026-10-11' )['open'] );
	$off = tmc_signup_state( 'sliding', '2026-11-15' );
	same( false, $off['open'] );
	same( '2027-04-01', $off['reopens'], 'öppnar igen av sig själv' );
	truthy( strpos( $off['message'], '1 april 2027' ) !== false, 'meddelandet säger när: ' . $off['message'] );
} );

test( 'ändrade säsongsdatum används', function () {
	$GLOBALS['__mods'] += [ 'tmc_pp_season_start_month' => '5', 'tmc_pp_season_start_day' => '15', 'tmc_pp_season_end_month' => '9', 'tmc_pp_season_end_day' => '30' ];
	same( false, tmc_signup_open_on( 'provapa', '2027-05-14' ) );
	same( true,  tmc_signup_open_on( 'provapa', '2027-05-15' ) );
	same( false, tmc_signup_open_on( 'provapa', '2027-10-01' ) );
} );

test( '"Alltid öppen" och "Stängd tills vidare" bryr sig inte om datum', function () {
	$GLOBALS['__mods']['tmc_sl_signup_mode'] = 'open';
	same( true, tmc_signup_state( 'sliding', '2027-01-10' )['open'] );
	$GLOBALS['__mods']['tmc_sl_signup_mode'] = 'closed';
	$s = tmc_signup_state( 'sliding', '2027-07-01' );
	same( false, $s['open'] );
	same( null, $s['reopens'] );
	truthy( $s['message'] !== '', 'stängd → ett meddelande visas' );
	$GLOBALS['__mods']['tmc_sl_signup_mode'] = 'nonsens';
	same( 'season', tmc_signup_settings( 'sliding' )['mode'], 'okänt värde → säsong' );
} );

test( 'Prova På: den gamla kryssrutan (ur) betyder fortfarande stängt, med det gamla meddelandet', function () {
	$GLOBALS['__mods']['tmc_provapa_season_open']    = false;
	$GLOBALS['__mods']['tmc_provapa_season_message'] = 'Vi ses i vår!';
	$s = tmc_signup_state( 'provapa', '2027-07-01' );
	same( false, $s['open'] );
	same( 'Vi ses i vår!', $s['message'] );
	$GLOBALS['__mods']['tmc_pp_signup_mode'] = 'open';
	same( true, tmc_signup_state( 'provapa', '2027-07-01' )['open'], 'ett nytt val vinner över den gamla rutan' );
} );

test( 'delar av sidan: synliga som standard, kan döljas — utom Hero och formuläret', function () {
	same( true, tmc_page_section_visible( 'sliding', 'groups' ) );
	$GLOBALS['__mods']['tmc_sl_show_groups'] = false;
	same( false, tmc_page_section_visible( 'sliding', 'groups' ) );
	$GLOBALS['__mods']['tmc_sl_show_form'] = false;
	same( true, tmc_page_section_visible( 'sliding', 'form' ), 'formuläret styrs av anmälan, inte av en visa-ruta' );
} );
