<?php
/* Prova På page: every text comes from the Customizer, with the current text as default. */
tmc_load( [ 'tmc_provapa_fields', 'tmc_provapa_text', 'tmc_provapa_slots', 'tmc_provapa_items', 'tmc_provapa_lines' ] );
section( 'Prova På-sidans texter' );

test( 'alla fält har grupp, etikett, standardtext och giltig typ', function () {
	$fields = tmc_provapa_fields();
	truthy( count( $fields ) > 40, 'förväntade många fält, fick ' . count( $fields ) );
	foreach ( $fields as $key => $f ) {
		truthy( preg_match( '/^[a-z0-9_]+$/', $key ), "ogiltig nyckel: $key" );
		same( 4, count( $f ), "fält $key" );
		truthy( in_array( $f[3], [ 'text', 'textarea', 'url' ], true ), "fält $key har okänd typ {$f[3]}" );
		truthy( $f[0] !== '' && $f[1] !== '', "fält $key saknar grupp/etikett" );
	}
} );

test( 'standardtexten används tills styrelsen ändrar den', function () {
	same( 'Prova på', tmc_provapa_text( 'hero_title_1' ) );
	$GLOBALS['__mods']['tmc_pp_hero_title_1'] = 'Testa på';
	same( 'Testa på', tmc_provapa_text( 'hero_title_1' ) );
} );

test( 'ett tömt fält förblir tomt (så döljs delen) och okänd nyckel ger tom text', function () {
	$GLOBALS['__mods']['tmc_pp_hero_eyebrow'] = '   ';
	same( '', tmc_provapa_text( 'hero_eyebrow' ) );
	same( '', tmc_provapa_text( 'finns_inte' ) );
} );

test( 'affärsreglerna finns i standardtexterna (gratis, ons 17 / lör 10, 225 kr från 12 år, förberedda motorcyklar)', function () {
	same( 'Onsdagar', tmc_provapa_text( 'slot_1_day' ) );
	same( '17:00', tmc_provapa_text( 'slot_1_time' ) );
	same( 'Lördagar', tmc_provapa_text( 'slot_2_day' ) );
	same( '10:00', tmc_provapa_text( 'slot_2_time' ) );
	truthy( strpos( tmc_provapa_text( 'license_text' ), '225 kr' ) !== false, 'licenstexten nämner inte 225 kr' );
	truthy( strpos( tmc_provapa_text( 'license_text' ), '12 år' ) !== false, 'licenstexten nämner inte 12 år' );
	truthy( strpos( tmc_provapa_text( 'times_text' ), 'förberett motorcyklar' ) !== false, 'fel formulering om att höra av sig' );
	foreach ( tmc_provapa_fields() as $key => $f ) {
		truthy( stripos( $f[2], 'väder' ) === false, "gammal formulering (väder/banans skick) i $key" );
		truthy( stripos( $f[2], 'köp biljett' ) === false, "biljettköp nämns i $key" );
	}
} );

test( 'träningstider: två som standard, en tredje kan läggas till, tom dag hoppas över', function () {
	same( [ [ 'day' => 'Onsdagar', 'time' => '17:00' ], [ 'day' => 'Lördagar', 'time' => '10:00' ] ], tmc_provapa_slots() );
	$GLOBALS['__mods']['tmc_pp_slot_3_day']  = 'Söndagar';
	$GLOBALS['__mods']['tmc_pp_slot_3_time'] = '12:00';
	$GLOBALS['__mods']['tmc_pp_slot_1_day']  = '';
	same( [ [ 'day' => 'Lördagar', 'time' => '10:00' ], [ 'day' => 'Söndagar', 'time' => '12:00' ] ], tmc_provapa_slots() );
} );

test( 'numrerade delar: tom första del döljer hela rutan', function () {
	same( 6, count( tmc_provapa_items( 'fact_%d_', [ 'num', 'label' ], 6 ) ) );
	$GLOBALS['__mods']['tmc_pp_fact_4_num'] = '';
	$facts = tmc_provapa_items( 'fact_%d_', [ 'num', 'label' ], 6 );
	same( 5, count( $facts ) );
	same( [ 'num' => '225 kr', 'label' => 'Licens från 12 år (Svemo)' ], $facts[3] );
	same( 5, count( tmc_provapa_items( 'faq_%d_', [ 'q', 'a' ], 6 ) ), 'fråga 6 är tom som standard' );
} );

test( 'textrutor delas upp i rader utan tomma rader', function () {
	same( [ 'Ett', 'Två', 'Tre' ], tmc_provapa_lines( "Ett\r\n\n  Två  \nTre\n" ) );
	same( [], tmc_provapa_lines( '' ) );
} );
