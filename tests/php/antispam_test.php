<?php
/* Spam protection for the four forms. */
tmc_load( [ 'tmc_antispam_valid_age', 'tmc_antispam_valid_age_or_year', 'tmc_antispam_text_is_spammy', 'tmc_antispam_is_blocked_email', 'tmc_antispam_is_bot' ] );
section( 'Spamskydd' );

test( 'ålder: tomt är OK, 1–100 är OK, allt annat avvisas', function () {
	truthy( tmc_antispam_valid_age( '' ) ); truthy( tmc_antispam_valid_age( '1' ) ); truthy( tmc_antispam_valid_age( '100' ) ); truthy( tmc_antispam_valid_age( ' 12 ' ) );
	foreach ( [ '0', '101', '9999', 'abc', '12a', '-5', '1.5' ] as $bad ) same( false, tmc_antispam_valid_age( $bad ), "'$bad'" );
} );
test( 'ålder för barn (max 18)', function () {
	truthy( tmc_antispam_valid_age( '18', 1, 18 ) ); same( false, tmc_antispam_valid_age( '19', 1, 18 ) );
} );
test( 'ålder eller födelseår: 1–100 eller 1920–i år', function () {
	$year = (int) date( 'Y' );
	truthy( tmc_antispam_valid_age_or_year( '' ) ); truthy( tmc_antispam_valid_age_or_year( '42' ) ); truthy( tmc_antispam_valid_age_or_year( '1920' ) ); truthy( tmc_antispam_valid_age_or_year( (string) $year ) );
	foreach ( [ '101', '1919', (string) ( $year + 1 ), '19a0', '0' ] as $bad ) same( false, tmc_antispam_valid_age_or_year( $bad ), "'$bad'" );
} );
test( 'länkspam: max 1 länk i meddelande, 0 i namn, och inga väggar av text', function () {
	same( false, tmc_antispam_text_is_spammy( 'Hej, jag vill prova på!' ) );
	same( false, tmc_antispam_text_is_spammy( 'Se https://exempel.se' ) );
	same( true,  tmc_antispam_text_is_spammy( 'http://a.se och http://b.se' ) );
	same( true,  tmc_antispam_text_is_spammy( 'www.spam.se', 0 ), 'länk i namnfält' );
	same( true,  tmc_antispam_text_is_spammy( str_repeat( 'a', 3001 ) ) );
	same( false, tmc_antispam_text_is_spammy( '' ) );
} );
test( 'spärrlista: exakt adress, hel domän, olika skiftläge, komma/radbrytning', function () {
	$GLOBALS['__mods']['tmc_antispam_blocklist'] = "Spam@Example.com\n@skräp.se, annan@bot.net";
	same( true,  tmc_antispam_is_blocked_email( 'spam@example.com' ), 'exakt, skiftlägesokänsligt' );
	same( true,  tmc_antispam_is_blocked_email( 'vem.som.helst@skräp.se' ), 'hel domän' );
	same( true,  tmc_antispam_is_blocked_email( 'annan@bot.net' ), 'efter komma' );
	same( false, tmc_antispam_is_blocked_email( 'snäll@example.com' ), 'annan adress på samma domän som en EXAKT post' );
	same( false, tmc_antispam_is_blocked_email( '' ) );
} );
test( 'spärrlista tom → ingen blockeras', function () { same( false, tmc_antispam_is_blocked_email( 'a@b.se' ) ); } );
test( 'bot: honeypot ifyllt avvisas', function () { $_POST = [ 'tmc_hp_website' => 'x', 'tmc_form_ts' => time() - 60 ]; same( true, tmc_antispam_is_bot() ); } );
test( 'bot: för snabbt inskick (under 3 s) och saknad tidsstämpel avvisas', function () {
	$_POST = [ 'tmc_form_ts' => time() ]; same( true, tmc_antispam_is_bot(), 'nyss laddad' );
	$_POST = []; same( true, tmc_antispam_is_bot(), 'ingen tidsstämpel' );
} );
test( 'bot: normal människa släpps igenom', function () { $_POST = [ 'tmc_form_ts' => time() - 20, 'pp_email' => 'kalle@exempel.se' ]; same( false, tmc_antispam_is_bot() ); } );
test( 'bot: spärrad e-post avvisas — oavsett vilket formulär (pp_email / contact_email)', function () {
	$GLOBALS['__mods']['tmc_antispam_blocklist'] = '@skräp.se';
	$_POST = [ 'tmc_form_ts' => time() - 20, 'pp_email' => 'x@skräp.se' ];      same( true, tmc_antispam_is_bot(), 'Prova På / Sliding' );
	$_POST = [ 'tmc_form_ts' => time() - 20, 'contact_email' => 'x@skräp.se' ]; same( true, tmc_antispam_is_bot(), 'Kontakt / Bli medlem' );
} );
