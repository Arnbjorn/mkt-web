<?php
/* v2 copies the previous theme's Customizer settings on first activation. */
tmc_load( [ 'tmc_import_previous_theme_mods' ] );
section( 'Import av inställningar från förra temat' );

function v1_mods() { return [ 'custom_logo' => 42, 'tmc_sliding_enabled' => false, 'tmc_contact_phone' => '040-1', 'nav_menu_locations' => [ 'primary' => 7 ] ]; }
function both_themes() { return [ 'tandstiftet-theme' => new Fake_Theme( 'tandstiftet' ), 'tandstiftet-theme-v2' => new Fake_Theme( 'tandstiftet' ), 'twentytwentyfour' => new Fake_Theme( 'twentytwentyfour' ) ]; }

test( 'ett nytt v2 får alla inställningar från v1', function () {
	$GLOBALS['__themes']  = both_themes();
	$GLOBALS['__options'] = [ 'stylesheet' => 'tandstiftet-theme-v2', 'theme_mods_tandstiftet-theme' => v1_mods() ];
	tmc_import_previous_theme_mods();
	$got = get_option( 'theme_mods_tandstiftet-theme-v2' );
	same( 42, $got['custom_logo'] ); same( false, $got['tmc_sliding_enabled'] ); same( 7, $got['nav_menu_locations']['primary'] );
} );

test( 'skriver ALDRIG över inställningar som v2 redan har', function () {
	$GLOBALS['__themes']  = both_themes();
	$GLOBALS['__options'] = [ 'stylesheet' => 'tandstiftet-theme-v2', 'theme_mods_tandstiftet-theme' => v1_mods(), 'theme_mods_tandstiftet-theme-v2' => [ 'tmc_brand_short' => 'Mitt namn' ] ];
	tmc_import_previous_theme_mods();
	same( [ 'tmc_brand_short' => 'Mitt namn' ], get_option( 'theme_mods_tandstiftet-theme-v2' ) );
} );

test( 'importerar även när WordPress bara hunnit mappa menyplatser — och behåller de mappade menyerna', function () {
	$GLOBALS['__themes']  = both_themes();
	$GLOBALS['__options'] = [ 'stylesheet' => 'tandstiftet-theme-v2', 'theme_mods_tandstiftet-theme' => v1_mods(), 'theme_mods_tandstiftet-theme-v2' => [ 'nav_menu_locations' => [ 'primary' => 99 ] ] ];
	tmc_import_previous_theme_mods();
	$got = get_option( 'theme_mods_tandstiftet-theme-v2' );
	same( 42, $got['custom_logo'] ); same( 99, $got['nav_menu_locations']['primary'] );
} );

test( 'gör ingenting om det inte finns något tidigare tema', function () {
	$GLOBALS['__themes']  = [ 'tandstiftet-theme-v2' => new Fake_Theme( 'tandstiftet' ) ];
	$GLOBALS['__options'] = [ 'stylesheet' => 'tandstiftet-theme-v2' ];
	tmc_import_previous_theme_mods();
	same( false, get_option( 'theme_mods_tandstiftet-theme-v2' ) );
} );

test( 'använder aldrig ett orelaterat tema som källa', function () {
	$GLOBALS['__themes']  = [ 'tandstiftet-theme-v2' => new Fake_Theme( 'tandstiftet' ), 'twentytwentyfour' => new Fake_Theme( 'twentytwentyfour' ) ];
	$GLOBALS['__options'] = [ 'stylesheet' => 'tandstiftet-theme-v2', 'theme_mods_twentytwentyfour' => [ 'x' => 1 ] ];
	tmc_import_previous_theme_mods();
	same( false, get_option( 'theme_mods_tandstiftet-theme-v2' ) );
} );
