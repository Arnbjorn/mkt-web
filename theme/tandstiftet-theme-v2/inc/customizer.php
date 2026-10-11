<?php
/**
 * Theme Customizer
 *
 * @package tandstiftet
 */

function tmc_customizer( $wp_customize ) {

    $wp_customize->add_panel('tmc_panel', [
        'title'    => __('Tändstiftet MC', 'tandstiftet'),
        'priority' => 130,
    ]);

    // =========================================================
    // SECTION: Hero
    // =========================================================
    $wp_customize->add_section('tmc_hero', [
        'title'       => __('Hero — bild & texter', 'tandstiftet'),
        'description' => __('Ladda upp 1 bild för fast bakgrund, eller 2–5 bilder för automatiskt bildspel (växlar var 5:e sekund med fade).', 'tandstiftet'),
        'panel'       => 'tmc_panel',
    ]);

    // Slideshow images 1–5
    for ( $i = 1; $i <= 5; $i++ ) {
        $id    = 'tmc_hero_image_' . $i;
        $label = $i === 1
            ? __('Bakgrundsbild 1 (krävs)', 'tandstiftet')
            : sprintf( __('Bakgrundsbild %d (valfri — aktiverar bildspel)', 'tandstiftet'), $i );
        $wp_customize->add_setting( $id, [ 'default' => '', 'sanitize_callback' => 'absint' ]);
        $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, [
            'section' => 'tmc_hero', 'label' => $label, 'mime_type' => 'image', 'priority' => $i,
        ]));
    }

    // Torn photo edge
    $wp_customize->add_setting('tmc_hero_torn_edge', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ]);
    $wp_customize->add_control('tmc_hero_torn_edge', [
        'section'     => 'tmc_hero',
        'type'        => 'checkbox',
        'label'       => __('Rivd bildkant (bilden syns på högra delen, texten ligger på mörk botten)', 'tandstiftet'),
        'description' => __('Bocka ur för att visa bilden över hela hero-ytan istället.', 'tandstiftet'),
        'priority'    => 6,
    ]);

    // Title
    $wp_customize->add_setting('tmc_hero_title', [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ]);
    $wp_customize->add_control('tmc_hero_title', [
        'section' => 'tmc_hero', 'label' => __('Rubrik (lämna tomt = webbplatsnamnet)', 'tandstiftet'), 'type' => 'text', 'priority' => 10,
    ]);

    // Subtitle — textarea so line breaks work, live preview via postMessage
    $wp_customize->add_setting('tmc_hero_subtitle', [
        'default'           => __('Vi kör dirt track med hjärta. Välkommen till klubben.', 'tandstiftet'),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('tmc_hero_subtitle', [
        'section'     => 'tmc_hero',
        'label'       => __('Underrubrik / Beskrivningstext', 'tandstiftet'),
        'description' => __('Radbrytningar visas på sidan.', 'tandstiftet'),
        'type'        => 'textarea',
        'priority'    => 11,
    ]);

    // Buttons
    foreach ([
        'tmc_hero_btn1_text' => [ __('Knapp 1 — text', 'tandstiftet'), 'text' ],
        'tmc_hero_btn1_url'  => [ __('Knapp 1 — länk', 'tandstiftet'), 'url'  ],
        'tmc_hero_btn2_text' => [ __('Knapp 2 — text', 'tandstiftet'), 'text' ],
        'tmc_hero_btn2_url'  => [ __('Knapp 2 — länk', 'tandstiftet'), 'url'  ],
    ] as $id => [$label, $type] ) {
        $san = $type === 'url' ? 'esc_url_raw' : 'sanitize_text_field';
        $wp_customize->add_setting( $id, [ 'default' => '', 'sanitize_callback' => $san ]);
        $wp_customize->add_control( $id, [ 'section' => 'tmc_hero', 'label' => $label, 'type' => $type, 'priority' => 20 ]);
    }

    // =========================================================
    // SECTION: Stats bar
    // =========================================================
    $wp_customize->add_section('tmc_stats', [
        'title' => __('Statistikrad (startsida)', 'tandstiftet'),
        'panel' => 'tmc_panel',
    ]);
    for ($i = 1; $i <= 4; $i++) {
        foreach ([ 'num' => __('Siffra / text','tandstiftet'), 'label' => __('Etikett','tandstiftet') ] as $k => $l) {
            $id = "tmc_stat{$i}_{$k}";
            $wp_customize->add_setting($id, ['sanitize_callback' => 'sanitize_text_field']);
            $wp_customize->add_control($id, [ 'section' => 'tmc_stats', 'label' => "Stat $i — $l", 'type' => 'text' ]);
        }
    }

    // =========================================================
    // SECTION: Lagtexter
    // =========================================================
    $wp_customize->add_section('tmc_teams', [
        'title' => __('Lagtexter', 'tandstiftet'),
        'panel' => 'tmc_panel',
    ]);
    foreach ([
        'tmc_div1_bio'   => __('Division 1 — beskrivning', 'tandstiftet'),
        'tmc_adults_bio' => __('Vuxna — beskrivning',      'tandstiftet'),
        'tmc_youth_bio'  => __('Ungdomar — beskrivning',   'tandstiftet'),
    ] as $id => $label) {
        $wp_customize->add_setting($id, ['sanitize_callback' => 'sanitize_textarea_field']);
        $wp_customize->add_control($id, [ 'section' => 'tmc_teams', 'label' => $label, 'type' => 'textarea' ]);
    }

    // =========================================================
    // SECTION: Sidfot
    // =========================================================
    $wp_customize->add_section('tmc_footer', [
        'title' => __('Sidfot & Sidhuvud', 'tandstiftet'),
        'panel' => 'tmc_panel',
    ]);

    // Page header label (shown above page titles on all inner pages)
    $wp_customize->add_setting('tmc_page_header_label', [
        'default'           => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('tmc_page_header_label', [
        'section'     => 'tmc_footer',
        'label'       => __('Klubbnamn i sidrubriker', 'tandstiftet'),
        'description' => __('Texten i guld ovanför rubriken på alla undersidor (t.ex. "Motorklubben Tändstiftet"). Lämna tomt för att använda webbplatsnamnet.', 'tandstiftet'),
        'type'        => 'text',
    ]);
    $wp_customize->add_setting('tmc_footer_desc', [
        'default'           => __('Tändstiftet MC är en speedway- och flattrack-klubb med passion för motorsport. Vi välkomnar förare i alla åldrar och klasser.', 'tandstiftet'),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('tmc_footer_desc', [
        'section'     => 'tmc_footer',
        'label'       => __('Beskrivningstext i sidfot', 'tandstiftet'),
        'description' => __('Texten under logotypen i sidfoten.', 'tandstiftet'),
        'type'        => 'textarea',
    ]);

    // =========================================================
    // SECTION: Sociala medier & Kontakt
    // =========================================================
    $wp_customize->add_section('tmc_social', [
        'title' => __('Sociala medier & Kontakt', 'tandstiftet'),
        'panel' => 'tmc_panel',
    ]);
    foreach ([
        'tmc_facebook'        => __('Facebook URL',           'tandstiftet'),
        'tmc_instagram'       => __('Instagram URL',          'tandstiftet'),
        'tmc_youtube'         => __('YouTube URL',            'tandstiftet'),
        'tmc_contact_phone'   => __('Telefon',                'tandstiftet'),
        'tmc_contact_email'   => __('Kontakt-epost',          'tandstiftet'),
        'tmc_contact_address' => __('Adress (bana/klubbhus)', 'tandstiftet'),
    ] as $id => $label) {
        $wp_customize->add_setting($id, ['sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control($id, [ 'section' => 'tmc_social', 'label' => $label, 'type' => 'text' ]);
    }
}
add_action( 'customize_register', 'tmc_customizer' );


/**
 * Live preview via postMessage (no full page reload)
 */
function tmc_customizer_preview_js() { ?>
    <script>
    (function($){
        wp.customize('tmc_hero_subtitle', function(value){
            value.bind(function(v){
                $('.hero__subtitle').html(v.replace(/\n/g,'<br>'));
            });
        });
        wp.customize('tmc_footer_desc', function(value){
            value.bind(function(v){ $('.footer-brand__desc').text(v); });
        });
    }(jQuery));
    </script>
<?php }
add_action( 'customize_preview_init', 'tmc_customizer_preview_js' );

/* =========================================================
   Discipliner & Prova På (startsida)
   ========================================================= */
add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section('tmc_disciplines', [
        'title'       => __('Discipliner (startsida)', 'tandstiftet'),
        'description' => __('De tre korten Speedway, Flat Track, Veteran.', 'tandstiftet'),
        'panel'       => 'tmc_panel',
    ]);

    for ( $i = 1; $i <= 3; $i++ ) {
        $defaults = [
            1 => ['Speedway',   'Oval bana, 500cc utan broms.',   '#'],
            2 => ['Flat Track', 'Dirt track med bromsar.',        '#'],
            3 => ['Veteran',    'För förare 40+ år.',             '#'],
        ];
        foreach ([
            "tmc_disc{$i}_title" => [ __('Titel','tandstiftet'),       'text',  $defaults[$i][0] ],
            "tmc_disc{$i}_desc"  => [ __('Beskrivning','tandstiftet'), 'text',  $defaults[$i][1] ],
            "tmc_disc{$i}_url"   => [ __('Länk','tandstiftet'),        'url',   $defaults[$i][2] ],
        ] as $id => [$label, $type, $default] ) {
            $wp_customize->add_setting( $id, ['default'=>$default,'sanitize_callback'=>$type==='url'?'esc_url_raw':'sanitize_text_field']);
            $wp_customize->add_control( $id, ['section'=>'tmc_disciplines','label'=>"Disciplin $i — $label",'type'=>$type]);
        }
        $wp_customize->add_setting( "tmc_disc{$i}_image", ['default'=>'','sanitize_callback'=>'absint']);
        $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, "tmc_disc{$i}_image", [
            'section'=>'tmc_disciplines','label'=>"Disciplin $i — Bild",'mime_type'=>'image',
        ]));
    }

    $wp_customize->add_section('tmc_provapa', [
        'title' => __('Prova På / Sliding (startsida)', 'tandstiftet'),
        'description' => __('Gemensam teasertext för Prova På-sektionen på startsidan. Sektionen länkar till både /prova-pa/ (barn & ungdom, gratis) och /sliding/ (vuxna, 800 kr).', 'tandstiftet'),
        'panel' => 'tmc_panel',
    ]);
    $wp_customize->add_setting('tmc_provapa_desc', [
        'default'           => __('Ingen erfarenhet krävs — vi lånar ut utrustning och har instruktörer på plats.', 'tandstiftet'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('tmc_provapa_desc', [
        'section' => 'tmc_provapa',
        'label'   => __('Beskrivningstext', 'tandstiftet'),
        'type'    => 'textarea',
    ]);
});

/* =========================================================
   Startsida — Sektions-toggles
   ========================================================= */
add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section('tmc_sections_visibility', [
        'title'       => __('Startsida — Visa/dölj sektioner', 'tandstiftet'),
        'description' => __('Stäng av sektioner som inte ska visas på startsidan.', 'tandstiftet'),
        'panel'       => 'tmc_panel',
        'priority'    => 5,
    ]);

    $sections = [
        'tmc_show_next_event'   => __('Nästa tävling-banner', 'tandstiftet'),
        'tmc_show_disciplines'  => __('Discipliner (Speedway, Flat Track, Veteran)', 'tandstiftet'),
        'tmc_show_provapa'      => __('Prova På-sektion', 'tandstiftet'),
        'tmc_show_events'       => __('Kalender / Kommande tävlingar', 'tandstiftet'),
        'tmc_show_news'         => __('Nyheter', 'tandstiftet'),
        'tmc_show_riders'       => __('Förare & Lag', 'tandstiftet'),
        'tmc_show_values'       => __('Vi tar hand om varandra', 'tandstiftet'),
        'tmc_show_sponsors'     => __('Sponsorer', 'tandstiftet'),
    ];

    foreach ( $sections as $id => $label ) {
        $wp_customize->add_setting( $id, [
            'default'           => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ]);
        $wp_customize->add_control( $id, [
            'section' => 'tmc_sections_visibility',
            'label'   => $label,
            'type'    => 'checkbox',
        ]);
    }
});

/* =========================================================
   Sliding — helt av/på (paus-flagga)
   ========================================================= */
add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section('tmc_sliding_toggle', [
        'title'       => __('Sliding — Paus/Aktiv', 'tandstiftet'),
        'description' => __('En enda växel för hela Sliding-erbjudandet. Avbockad: Sliding-sidan visar ett "pausad"-meddelande istället för sitt vanliga innehåll, länken till Sliding tas bort från Prova På-sidan och startsidan, och "Sliding" försvinner ur menyerna automatiskt. Bocka i igen när ni vill återställa allt exakt som det var.', 'tandstiftet'),
        'panel'       => 'tmc_panel',
        'priority'    => 4,
    ]);

    $wp_customize->add_setting('tmc_sliding_enabled', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    $wp_customize->add_control('tmc_sliding_enabled', [
        'section' => 'tmc_sliding_toggle',
        'label'   => __('Sliding är aktivt (bocka ur för att pausa tillfälligt)', 'tandstiftet'),
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('tmc_flattrack_enabled', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    $wp_customize->add_control('tmc_flattrack_enabled', [
        'section'     => 'tmc_sliding_toggle',
        'label'       => __('Flat track är en del av Sliding just nu (bocka ur för att stänga av helt)', 'tandstiftet'),
        'description' => __('Avbockad: inget Flat track-kort, ingen Sparks-ruta, inget Flat track-alternativ i bokningsformuläret, och inga Flat track-luckor i kalendern. Speedway påverkas inte.', 'tandstiftet'),
        'type'        => 'checkbox',
    ]);
});

/* =========================================================
   Värderingar (startsida)
   ========================================================= */
add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section('tmc_values', [
        'title' => __('Vi tar hand om varandra', 'tandstiftet'),
        'panel' => 'tmc_panel',
    ]);

    $fields = [
        'tmc_values_label'   => [ __('Etikett (liten text ovan rubrik)', 'tandstiftet'), 'text',     'Vår kultur' ],
        'tmc_values_title'   => [ __('Rubrik', 'tandstiftet'),                           'text',     'Vi tar hand om varandra' ],
        'tmc_values_intro'   => [ __('Ingress', 'tandstiftet'),                          'textarea', 'På Tändstiftet tävlar vi hårt på banan — men utanför den tar vi hand om varandra. Det är en av de viktigaste reglerna vi har.' ],
        'tmc_values_card1_title' => [ __('Kort 1 — Rubrik', 'tandstiftet'),              'text',     'Barnen först' ],
        'tmc_values_card1_text'  => [ __('Kort 1 — Text', 'tandstiftet'),                'textarea', 'Varje barn som kliver in på vår bana ska känna sig välkommen, sedd och trygg — oavsett om de vinner, förlorar eller råkar ut för en olycka. Vid en incident är första prioritet alltid omhändertagande, inte skuld.' ],
        'tmc_values_card2_title' => [ __('Kort 2 — Rubrik', 'tandstiftet'),              'text',     'Nolltolerans' ],
        'tmc_values_card2_text'  => [ __('Kort 2 — Text', 'tandstiftet'),                'textarea', 'Vi har nolltolerans mot mobbning och kränkande behandling — på banan, i paddocken och online. Om något känns fel ska det alltid gå att prata med en vuxen i klubben.' ],
        'tmc_values_card3_title' => [ __('Kort 3 — Rubrik', 'tandstiftet'),              'text',     'Föräldrar & vuxna' ],
        'tmc_values_card3_text'  => [ __('Kort 3 — Text', 'tandstiftet'),                'textarea', 'Föräldrar och anhöriga är välkomna och uppskattade. Vi ber er stötta era egna barn, visa respekt mot alla andra och lita på att funktionärerna gör sitt bästa. Hur vi vuxna uppför oss sätter tonen för hela klubben.' ],
        'tmc_values_quote'       => [ __('Citatsblock — Text', 'tandstiftet'),           'textarea', 'Ett gott bemötande är inte valfritt hos oss — det är en förutsättning för att få vara en del av vår klubb. Vi stödjer SVEMO:s arbete för en trygg och ren motorsport för unga, och uppmanar alla familjer att bekanta sig med deras riktlinjer.' ],
        'tmc_values_punchline'   => [ __('Punch-line (längst ned)', 'tandstiftet'),      'text',     'Snabbast på banan. Schysst mot varandra.' ],
    ];

    foreach ( $fields as $id => [$label, $type, $default] ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => $type === 'textarea' ? 'sanitize_textarea_field' : 'sanitize_text_field',
        ]);
        $wp_customize->add_control( $id, [
            'section' => 'tmc_values',
            'label'   => $label,
            'type'    => $type,
        ]);
    }
});

/* =========================================================
   Sliding — Bokningskalender (kapacitet & veckoschema, per disciplin)
   ========================================================= */
add_action( 'customize_register', function( $wp_customize ) {

    $weekdays = [
        'mon' => __('Måndagar', 'tandstiftet'),
        'tue' => __('Tisdagar', 'tandstiftet'),
        'wed' => __('Onsdagar', 'tandstiftet'),
        'thu' => __('Torsdagar', 'tandstiftet'),
        'fri' => __('Fredagar', 'tandstiftet'),
        'sat' => __('Lördagar', 'tandstiftet'),
        'sun' => __('Söndagar', 'tandstiftet'),
    ];

    /* ── Speedway ── */
    $wp_customize->add_section('tmc_sliding_booking_speedway', [
        'title'       => __('Sliding — Speedway-schema', 'tandstiftet'),
        'description' => __('Max antal personer tränaren kan ha ansvar för samtidigt, per kalenderlucka (dag), samt vilka veckodagar Speedway erbjuds på. Enskilda datum kan override:as under Sliding — Datum.', 'tandstiftet'),
        'panel'       => 'tmc_panel',
        'priority'    => 5,
    ]);
    $wp_customize->add_setting('tmc_sliding_daily_capacity_speedway', [
        'default'           => 8,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('tmc_sliding_daily_capacity_speedway', [
        'section' => 'tmc_sliding_booking_speedway',
        'label'   => __('Max antal personer per kalenderlucka (dag) — Speedway', 'tandstiftet'),
        'type'    => 'number',
        'input_attrs' => [ 'min' => 0 ],
    ]);
    foreach ( $weekdays as $key => $label ) {
        $id = 'tmc_sliding_hours_' . $key . '_speedway';
        $wp_customize->add_setting( $id, [
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ] );
        $wp_customize->add_control( $id, [
            'section'     => 'tmc_sliding_booking_speedway',
            'label'       => $label,
            'description' => __('Lämna tomt om Speedway inte erbjuds den här veckodagen. Fyll i en tid (t.ex. 18:00) för att öppna den för bokning.', 'tandstiftet'),
            'type'        => 'text',
            'input_attrs' => [ 'placeholder' => __('t.ex. 18:00 — lämna tomt = stängd', 'tandstiftet') ],
        ] );
    }

    /* ── Flat track ── */
    $wp_customize->add_section('tmc_sliding_booking_flattrack', [
        'title'       => __('Sliding — Flat track-schema', 'tandstiftet'),
        'description' => __('Samma som Speedway-schemat, men för Flat track. Hela den här sektionen är verkningslös om Flat track är avstängt under Sliding — Paus/Aktiv.', 'tandstiftet'),
        'panel'       => 'tmc_panel',
        'priority'    => 6,
        'active_callback' => 'tmc_flattrack_enabled',
    ]);
    $wp_customize->add_setting('tmc_sliding_daily_capacity_flattrack', [
        'default'           => 8,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('tmc_sliding_daily_capacity_flattrack', [
        'section' => 'tmc_sliding_booking_flattrack',
        'label'   => __('Max antal personer per kalenderlucka (dag) — Flat track', 'tandstiftet'),
        'type'    => 'number',
        'input_attrs' => [ 'min' => 0 ],
        'active_callback' => 'tmc_flattrack_enabled',
    ]);
    foreach ( $weekdays as $key => $label ) {
        $id = 'tmc_sliding_hours_' . $key . '_flattrack';
        $wp_customize->add_setting( $id, [
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ] );
        $wp_customize->add_control( $id, [
            'section'     => 'tmc_sliding_booking_flattrack',
            'label'       => $label,
            'description' => __('Lämna tomt om Flat track inte erbjuds den här veckodagen. Fyll i en tid (t.ex. 18:00) för att öppna den för bokning.', 'tandstiftet'),
            'type'        => 'text',
            'input_attrs' => [ 'placeholder' => __('t.ex. 18:00 — lämna tomt = stängd', 'tandstiftet') ],
            'active_callback' => 'tmc_flattrack_enabled',
        ] );
    }
});

/* =========================================================
   Formulär — Blockeringslista
   ========================================================= */
add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section('tmc_antispam_blocklist', [
        'title'       => __('Formulär — Blockeringslista', 'tandstiftet'),
        'description' => __('Blockera specifika avsändare från alla formulär på sajten (Kontakta oss, Bli medlem, Prova På, Sliding). En rad per post: en hel e-postadress (t.ex. spam@example.com) eller en hel domän (t.ex. @example.com för att blockera alla adresser på den domänen). Blockerade inskick avvisas tyst — samma sätt som honeypot/tidsspärren.', 'tandstiftet'),
        'panel'       => 'tmc_panel',
        'priority'    => 7,
    ]);

    $wp_customize->add_setting('tmc_antispam_blocklist', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('tmc_antispam_blocklist', [
        'section'     => 'tmc_antispam_blocklist',
        'label'       => __('Blockerade e-postadresser / domäner', 'tandstiftet'),
        'type'        => 'textarea',
        'input_attrs' => [ 'rows' => 8, 'placeholder' => "spam@example.com\n@spammy-domain.com" ],
    ]);
});

/* =========================================================
   Startsida — Viktigt meddelande (notisbanderoll)
   ========================================================= */
add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section('tmc_notice', [
        'title'       => __('Startsida — Viktigt meddelande', 'tandstiftet'),
        'description' => __('En banderoll högst upp på startsidan för viktig information till medlemmar och besökare (t.ex. medlemsmöten, inställda träningar, akuta uppdateringar). Styrs helt härifrån — ingen kodning krävs.', 'tandstiftet'),
        'panel'       => 'tmc_panel',
        'priority'    => 3,
    ]);

    $wp_customize->add_setting('tmc_notice_enabled', [
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    $wp_customize->add_control('tmc_notice_enabled', [
        'section' => 'tmc_notice',
        'label'   => __('Visa meddelandet', 'tandstiftet'),
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('tmc_notice_style', [
        'default'           => 'info',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('tmc_notice_style', [
        'section' => 'tmc_notice',
        'label'   => __('Allvarlighetsgrad', 'tandstiftet'),
        'type'    => 'select',
        'choices' => [
            'info'   => __('Vanlig (guld)', 'tandstiftet'),
            'urgent' => __('Viktigt / akut (röd)', 'tandstiftet'),
        ],
    ]);

    $wp_customize->add_setting('tmc_notice_title', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('tmc_notice_title', [
        'section' => 'tmc_notice',
        'label'   => __('Rubrik', 'tandstiftet'),
        'type'    => 'text',
        'input_attrs' => [ 'placeholder' => __('t.ex. Medlemsmöte 3 september', 'tandstiftet') ],
    ]);

    $wp_customize->add_setting('tmc_notice_text', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('tmc_notice_text', [
        'section' => 'tmc_notice',
        'label'   => __('Text', 'tandstiftet'),
        'type'    => 'textarea',
        'input_attrs' => [ 'rows' => 4 ],
    ]);

    $wp_customize->add_setting('tmc_notice_link_text', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('tmc_notice_link_text', [
        'section'     => 'tmc_notice',
        'label'       => __('Knapptext (valfritt)', 'tandstiftet'),
        'description' => __('Lämna tomt om ni inte vill ha någon knapp/länk alls.', 'tandstiftet'),
        'type'        => 'text',
        'input_attrs' => [ 'placeholder' => __('t.ex. Läs mer', 'tandstiftet') ],
    ]);

    $wp_customize->add_setting('tmc_notice_link_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('tmc_notice_link_url', [
        'section'     => 'tmc_notice',
        'label'       => __('Länk-URL (valfritt)', 'tandstiftet'),
        'type'        => 'url',
        'input_attrs' => [ 'placeholder' => 'https://... eller /klubbinfo/#medlemsmoten' ],
    ]);
});

/* =========================================================
   Prova På — Säsong öppen/stängd
   ========================================================= */
add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section('tmc_provapa_season', [
        'title'       => __('Prova På — Säsong', 'tandstiftet'),
        'description' => __('Stänger bokningsdelen av Prova På-sidan (barn & ungdom) och visar ett säsongsmeddelande istället. Resten av sidan (utrustning, hur det går till, licensinfo) ligger kvar som vanligt — bara "hör av dig"-formuläret och uppmaningarna att bara dyka upp stängs av.', 'tandstiftet'),
        'panel'       => 'tmc_panel',
        'priority'    => 4,
    ]);

    $wp_customize->add_setting('tmc_provapa_season_open', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    $wp_customize->add_control('tmc_provapa_season_open', [
        'section' => 'tmc_provapa_season',
        'label'   => __('Prova På tar emot fler för säsongen (bocka ur när säsongen är slut)', 'tandstiftet'),
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('tmc_provapa_season_message', [
        'default'           => __('Vi har just nu inga fler Prova På-tillfällen den här säsongen. Vi öppnar upp igen till nästa säsong — håll utkik här eller på våra sociala kanaler!', 'tandstiftet'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('tmc_provapa_season_message', [
        'section'     => 'tmc_provapa_season',
        'label'       => __('Säsongsmeddelande', 'tandstiftet'),
        'type'        => 'textarea',
        'input_attrs' => [ 'rows' => 4 ],
    ]);
});


/* =========================================================
   Prova På — Sidan (alla texter på Prova På-sidan)
   Fälten kommer från tmc_provapa_fields() i functions.php.
   ========================================================= */
add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section('tmc_provapa_page', [
        'title'       => __('Prova På — Sidan', 'tandstiftet'),
        'description' => __('Alla texter på Prova På-sidan (barn & ungdom), uppifrån och ner. Töm ett fält för att dölja den delen (t.ex. en faktaruta eller en fråga). Fotot i ramen är sidans Utvald bild — byt den under Sidor → Prova På. Kom ihåg vårdnadshavares medgivande för bilder på barn.', 'tandstiftet'),
        'panel'       => 'tmc_panel',
        'priority'    => 5,
    ]);

    tmc_customizer_add_text_fields( $wp_customize, 'tmc_provapa_page', tmc_provapa_fields(), 'tmc_pp_' );

    $wp_customize->add_section('tmc_sliding_page', [
        'title'       => __('Sliding — Sidan', 'tandstiftet'),
        'description' => __('Alla texter på Sliding-sidan (vuxna), uppifrån och ner. Töm ett fält för att dölja den delen. Fält märkta "(när Flat track är avstängt)" används automatiskt när Flat track är avstängt under Sliding — Paus/Aktiv. Fotot i toppen är sidans Utvald bild. Schema och platser ställer ni in under Sliding — Speedway-/Flat track-schema.', 'tandstiftet'),
        'panel'       => 'tmc_panel',
        'priority'    => 6,
    ]);
    tmc_customizer_add_text_fields( $wp_customize, 'tmc_sliding_page', tmc_sliding_fields(), 'tmc_sl_' );
});

/* Registers one Customizer field per entry in a page's field list
   ( key => [ group, label, default, 'text'|'textarea'|'url' ] ). */
function tmc_customizer_add_text_fields( $wp_customize, $section, array $fields, $prefix ) {
    $sanitize = [ 'text' => 'sanitize_text_field', 'textarea' => 'sanitize_textarea_field', 'url' => 'esc_url_raw' ];
    foreach ( $fields as $key => $field ) {
        list( $group, $label, $default, $type ) = $field;
        $wp_customize->add_setting( $prefix . $key, [
            'default'           => $default,
            'sanitize_callback' => $sanitize[ $type ],
        ]);
        $wp_customize->add_control( $prefix . $key, [
            'section' => $section,
            'label'   => $group . ' — ' . $label,
            'type'    => $type,
        ]);
    }
}


/* =========================================================
   v2 — Header & meny
   ========================================================= */
add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section('tmc_header', [
        'title'       => __('Header & meny', 'tandstiftet'),
        'description' => __('Namn och knapp i sidhuvudet. Själva menylänkarna ändrar ni under Utseende → Menyer (Primär navigation).', 'tandstiftet'),
        'panel'       => 'tmc_panel',
        'priority'    => 2,
    ]);

    $wp_customize->add_setting('tmc_brand_short', [ 'default' => 'Tändstiftet', 'sanitize_callback' => 'sanitize_text_field' ]);
    $wp_customize->add_control('tmc_brand_short', [
        'section' => 'tmc_header', 'type' => 'text',
        'label'   => __('Klubbnamn bredvid loggan (kort)', 'tandstiftet'),
    ]);

    $wp_customize->add_setting('tmc_brand_tagline', [ 'default' => 'Malmö · Sedan 1924', 'sanitize_callback' => 'sanitize_text_field' ]);
    $wp_customize->add_control('tmc_brand_tagline', [
        'section' => 'tmc_header', 'type' => 'text',
        'label'   => __('Rad under klubbnamnet', 'tandstiftet'),
        'description' => __('Lämna tomt för att bara visa namnet.', 'tandstiftet'),
    ]);

    $wp_customize->add_setting('tmc_nav_cta_text', [ 'default' => 'Prova på →', 'sanitize_callback' => 'sanitize_text_field' ]);
    $wp_customize->add_control('tmc_nav_cta_text', [
        'section' => 'tmc_header', 'type' => 'text',
        'label'   => __('Knapptext i headern', 'tandstiftet'),
        'description' => __('Lämna tomt för att dölja knappen helt.', 'tandstiftet'),
    ]);

    $wp_customize->add_setting('tmc_nav_cta_url', [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ]);
    $wp_customize->add_control('tmc_nav_cta_url', [
        'section' => 'tmc_header', 'type' => 'text',
        'label'   => __('Knappens länk', 'tandstiftet'),
        'description' => __('Lämna tomt för att länka till Prova På-sidan.', 'tandstiftet'),
        'input_attrs' => [ 'placeholder' => '/prova-pa/' ],
    ]);

    $wp_customize->add_setting('tmc_nav_show_events', [ 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean' ]);
    $wp_customize->add_control('tmc_nav_show_events', [
        'section' => 'tmc_header', 'type' => 'checkbox',
        'label'   => __('Visa "Tävlingar" i huvudmenyn', 'tandstiftet'),
        'description' => __('Av som standard — Tävlingar nås då via "Nästa match"-remsan på startsidan. Bocka i för att ta med den i menyn.', 'tandstiftet'),
    ]);
});
