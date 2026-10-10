<?php
/* Sliding: weekly schedule per discipline and the 24-hour rule. */
tmc_load( [ 'tmc_sliding_weekday_time', 'tmc_sliding_is_weekday_open', 'tmc_sliding_is_within_24h', 'tmc_sliding_schedule_summary' ] );
section( 'Sliding — veckoschema och 24-timmarsregeln' );

test( 'veckodagstid hämtas per disciplin (speedway och flat track är separata)', function () {
	$GLOBALS['__mods'] = [ 'tmc_sliding_hours_tue_speedway' => '18:00', 'tmc_sliding_hours_sat_flattrack' => ' 10:00 ' ];
	same( '18:00', tmc_sliding_weekday_time( '2026-10-13', 'speedway' ), 'tisdag speedway' );     // 2026-10-13 = tisdag
	same( '',      tmc_sliding_weekday_time( '2026-10-13', 'flattrack' ), 'tisdag flat track är stängt' );
	same( '10:00', tmc_sliding_weekday_time( '2026-10-17', 'flattrack' ), 'lördag flat track (trimmad)' ); // lördag
	same( '',      tmc_sliding_weekday_time( '2026-10-14', 'speedway' ), 'onsdag har inget schema' );
} );
test( 'ogiltigt datum ger tom tid och stängt', function () {
	same( '', tmc_sliding_weekday_time( 'inte-ett-datum' ) ); same( false, tmc_sliding_is_weekday_open( '2026-13-45' ) );
} );
test( 'veckodag öppen/stängd följer schemat', function () {
	$GLOBALS['__mods'] = [ 'tmc_sliding_hours_tue_speedway' => '18:00' ];
	same( true, tmc_sliding_is_weekday_open( '2026-10-13' ) ); same( false, tmc_sliding_is_weekday_open( '2026-10-14' ) );
} );
test( '24-timmarsregeln: igår, idag och imorgon är för sent — i övermorgon och senare går att boka', function () {
	$d = function ( $plus ) { return ( new DateTime( 'today' ) )->modify( "$plus days" )->format( 'Y-m-d' ); };
	same( true,  tmc_sliding_is_within_24h( $d( -1 ) ), 'igår' );
	same( true,  tmc_sliding_is_within_24h( $d( 0 ) ),  'idag' );
	same( true,  tmc_sliding_is_within_24h( $d( 1 ) ),  'imorgon (midnatt är <24 h bort)' );
	same( false, tmc_sliding_is_within_24h( $d( 2 ) ),  'i övermorgon' );
	same( false, tmc_sliding_is_within_24h( '2999-01-01' ) );
	same( true,  tmc_sliding_is_within_24h( 'skräp' ), 'ogiltigt datum behandlas försiktigt' );
} );
test( '24-timmarsregeln: exakt gräns, oberoende av klockslag', function () {
	// Plats att boka: onsdag 2026-10-14 (midnatt = start). Gränsen är exakt 24 timmar före midnatt.
	same( false, tmc_sliding_is_within_24h( '2026-10-14', '2026-10-13 00:00:00' ), 'exakt 24,0 h kvar → går att boka' );
	same( true,  tmc_sliding_is_within_24h( '2026-10-14', '2026-10-13 00:00:01' ), '23,9997 h kvar → för sent' );
	same( false, tmc_sliding_is_within_24h( '2026-10-14', '2026-10-12 23:59:59' ), '24,0003 h kvar → går att boka' );
	same( false, tmc_sliding_is_within_24h( '2026-10-14', '2026-10-10 08:00:00' ), 'flera dagar kvar' );
	same( true,  tmc_sliding_is_within_24h( '2026-10-14', '2026-10-13 12:00:00' ), '12 h kvar → för sent (fångar 12-timmarsfel på morgonen)' );
	same( true,  tmc_sliding_is_within_24h( '2026-10-14', '2026-10-13 20:00:00' ), '4 h kvar → för sent' );
	same( true,  tmc_sliding_is_within_24h( '2026-10-14', '2026-10-14 09:00:00' ), 'samma dag → för sent' );
} );
test( 'schemasammanfattningen på sidan', function () {
	$GLOBALS['__mods'] = [ 'tmc_sliding_hours_tue_speedway' => '18:00', 'tmc_sliding_hours_thu_speedway' => '18:30' ];
	$labels = [ 1 => 'Måndagar', 2 => 'Tisdagar', 3 => 'Onsdagar', 4 => 'Torsdagar', 5 => 'Fredagar', 6 => 'Lördagar', 7 => 'Söndagar' ];
	$map    = [ 1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat', 7 => 'sun' ];
	same( [ 'Tisdagar kl 18:00', 'Torsdagar kl 18:30' ], tmc_sliding_schedule_summary( $labels, $map, '_speedway' ) );
	same( [], tmc_sliding_schedule_summary( $labels, $map, '_flattrack' ) );
} );
