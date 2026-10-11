<?php
/* The competitions part of the homepage: which of the four states it shows. */
tmc_load( [ 'tmc_season_md', 'tmc_competition_season', 'tmc_competition_season_year', 'tmc_home_events_state' ] );
section( 'Startsidan — tävlingssäsongen' );

test( 'säsongen slutar första söndagen i oktober (standard)', function () {
	same( [ '2026-04-01', '2026-10-04' ], tmc_competition_season( 2026 ), '2026: 1 okt är en torsdag' );
	same( '2027-10-03', tmc_competition_season( 2027 )[1], '2027: 1 okt är en fredag' );
	same( '2028-10-01', tmc_competition_season( 2028 )[1], '2028: 1 okt är en söndag — då är det den dagen' );
} );

test( 'fast slutdatum och ändrad start kan väljas i Anpassa', function () {
	$GLOBALS['__mods'] += [ 'tmc_comp_end_rule' => 'date', 'tmc_comp_end_month' => '9', 'tmc_comp_end_day' => '30', 'tmc_comp_start_month' => '5', 'tmc_comp_start_day' => '1' ];
	same( [ '2026-05-01', '2026-09-30' ], tmc_competition_season( 2026 ) );
} );

test( 'januari–mars hör till förra säsongen', function () {
	same( 2026, tmc_competition_season_year( '2027-02-15' ) );
	same( 2027, tmc_competition_season_year( '2027-04-01' ) );
	same( 2027, tmc_competition_season_year( '2027-12-01' ) );
} );

test( 'fyra lägen: kommande / fler kommer / tack för i år / dold', function () {
	same( 'upcoming',    tmc_home_events_state( '2026-12-01', true, 0 ), 'kommande tävlingar vinner alltid' );
	same( 'more_coming', tmc_home_events_state( '2026-07-10', false, 3 ), 'uppehåll mitt i säsongen' );
	same( 'more_coming', tmc_home_events_state( '2026-10-04', false, 3 ), 'sista dagen i säsongen' );
	same( 'season_over', tmc_home_events_state( '2026-10-05', false, 3 ), 'dagen efter första söndagen i oktober' );
	same( 'season_over', tmc_home_events_state( '2027-02-01', false, 6 ), 'vintern: förra säsongen visas fortfarande' );
	same( 'hidden',      tmc_home_events_state( '2026-07-10', false, 0 ), 'inga tävlingar alls i säsongen' );
} );

test( 'starten kan också vara första söndagen i en månad', function () {
	$GLOBALS['__mods'] += [ 'tmc_comp_start_rule' => 'first_sunday', 'tmc_comp_start_month' => '4' ];
	same( '2026-04-05', tmc_competition_season( 2026 )[0], '2026: 1 april är en onsdag' );
	same( '2029-04-01', tmc_competition_season( 2029 )[0], '2029: 1 april är en söndag' );
} );
