<?php
/* Hero title: the last word becomes gold. */
tmc_load( [ 'tmc_hero_title_html' ] );
section( 'Hero-rubriken' );

test( 'sista ordet får en <span> (guld)', function () {
	same( 'Motorklubben <span>Tändstiftet</span>', tmc_hero_title_html( 'Motorklubben Tändstiftet' ) );
	same( 'Känn <span>sladden</span>', tmc_hero_title_html( 'Känn sladden' ) );
} );
test( 'ett ensamt ord lämnas orört', function () { same( 'Tändstiftet', tmc_hero_title_html( 'Tändstiftet' ) ); } );
test( 'extra mellanslag hanteras', function () { same( 'Tändstiftet <span>MC</span>', tmc_hero_title_html( '  Tändstiftet   MC ' ) ); } );
test( 'HTML i rubriken escapas (inget kan smugglas in)', function () {
	same( 'Rider &amp; Co <span>&lt;b&gt;x&lt;/b&gt;</span>', tmc_hero_title_html( 'Rider & Co <b>x</b>' ) );
} );
