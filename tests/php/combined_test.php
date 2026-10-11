<?php
/* "Barn och vuxna på samma sida" — a test mode that must be easy to switch off again. */
tmc_load( [ 'tmc_sliding_enabled', 'tmc_provapa_combined', 'tmc_sliding_own_page', 'tmc_maybe_hide_sliding_menu_items' ] );
section( 'Gemensam Prova På-sida (testläge)' );

function cmb_menu() {
	return [ (object) [ 'title' => 'Start', 'url' => '/' ], (object) [ 'title' => 'Prova På', 'url' => '/prova-pa/' ], (object) [ 'title' => 'Sliding', 'url' => '/sliding/' ] ];
}
function cmb_titles( $items ) { return array_values( array_map( function ( $i ) { return $i->title; }, $items ) ); }

test( 'av som standard — två separata sidor, Sliding finns i menyn', function () {
	same( false, tmc_provapa_combined() );
	same( true, tmc_sliding_own_page() );
	same( [ 'Start', 'Prova På', 'Sliding' ], cmb_titles( tmc_maybe_hide_sliding_menu_items( cmb_menu(), null ) ) );
} );

test( 'på — Sliding försvinner ur menyerna (vuxendelen finns på Prova På-sidan)', function () {
	$GLOBALS['__mods']['tmc_provapa_combined'] = true;
	same( false, tmc_sliding_own_page() );
	same( [ 'Start', 'Prova På' ], cmb_titles( tmc_maybe_hide_sliding_menu_items( cmb_menu(), null ) ) );
} );

test( 'av igen — allt som förut', function () {
	$GLOBALS['__mods']['tmc_provapa_combined'] = false;
	same( [ 'Start', 'Prova På', 'Sliding' ], cmb_titles( tmc_maybe_hide_sliding_menu_items( cmb_menu(), null ) ) );
} );

test( 'Sliding pausat döljer fortfarande menylänken, oavsett läge', function () {
	$GLOBALS['__mods']['tmc_sliding_enabled'] = false;
	same( [ 'Start', 'Prova På' ], cmb_titles( tmc_maybe_hide_sliding_menu_items( cmb_menu(), null ) ) );
} );
