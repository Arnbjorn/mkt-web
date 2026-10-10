<?php
/* "Tävlingar" is hidden from the primary menu unless switched on. */
tmc_load( [ 'tmc_maybe_hide_events_menu_item' ] );
section( 'Menyfiltret för Tävlingar' );

function menu_item( $title, $url ) { $o = new stdClass; $o->title = $title; $o->url = $url; return $o; }
function titles( $items ) { return array_map( function ( $i ) { return $i->title; }, $items ); }
function sample_menu() {
	return [ menu_item( 'Start', '/' ), menu_item( 'Tävlingar', '/tavlingar/' ), menu_item( 'TÄVLINGAR', '/x/' ), menu_item( 'Evenemang', '/evenemang/' ),
	         menu_item( 'Förare & Lag', '/forare-och-lag/' ), menu_item( 'Prova På', '/prova-pa/' ), menu_item( 'Sliding', '/sliding/' ) ];
}
$primary = (object) [ 'theme_location' => 'primary' ];

test( 'döljer Tävlingar som standard (titel, VERSALER och /evenemang/-länk)', function () use ( $primary ) {
	same( [ 'Start', 'Förare & Lag', 'Prova På', 'Sliding' ], titles( tmc_maybe_hide_events_menu_item( sample_menu(), $primary ) ) );
} );
test( 'visar allt när kryssrutan "Visa Tävlingar i huvudmenyn" är på', function () use ( $primary ) {
	$GLOBALS['__mods']['tmc_nav_show_events'] = true;
	same( 7, count( tmc_maybe_hide_events_menu_item( sample_menu(), $primary ) ) );
} );
test( 'rör inte andra menyer (t.ex. footern)', function () {
	same( 7, count( tmc_maybe_hide_events_menu_item( sample_menu(), (object) [ 'theme_location' => 'footer-om' ] ) ) );
} );
test( 'tål menyer utan theme_location', function () {
	same( 7, count( tmc_maybe_hide_events_menu_item( sample_menu(), (object) [] ) ) );
} );
