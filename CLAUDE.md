# CLAUDE.md — Motorklubben Tändstiftet (WordPress-tema)

Läs det här först. Den längre bakgrunden — varför saker är som de är, beslut, öppna frågor och att-göra-lista — finns i
**`docs/PROJEKTKONTEXT.md`**. Läs den när du behöver veta *varför*, eller innan du ändrar något som verkar konstigt.

## Vem du jobbar med

- Användaren är med i **Motorklubben Tändstiftet Malmö** (speedway & flat track, Division 1, ungdoms- och nybörjarverksamhet). **Inte utvecklare.**
  **Svara alltid på svenska**, enkelt och konkret. Förklara tekniska ord första gången du använder dem.
- Sajten är WordPress på one.com. **Testsajt just nu: `https://test.tändstiftet.se`** (punycode `test.xn--tndstiftet-q5a.se`) — ny, ren WordPress-installation (okt 2026) eftersom `beta.tändstiftet.se` slutade fungera och filerna där inte gick att nå. Valet heter ändå **beta** i publiceringsknappen.
- Läsarna är ofta **äldre och har sämre syn** — läsbarhet går före snygghet (se `docs/DESIGN.md`).
- Styrelsen ska kunna ändra texter och slå av/på saker **utan kod** → allt sådant ska ligga i Customizer (*Utseende → Anpassa*), med bra standardvärden.

### Så vill användaren arbeta

- **En sida i taget, med feedback emellan.** Visa resultatet, fråga, gå sedan vidare. Dumpa inte allt på en gång.
- **Visa konkret innan du bygger stort** (mockup/skärmdump/jämförelse). Användaren reagerar bäst på något att titta på.
- Feedbacken är kort ("ser bra ut", "lite konstigt") ofta med skärmdump — **läs bilden noga och mät** hellre än gissa. Om ett svar är tvetydigt: ställ en kort fråga med 2–3 alternativ.
- Användaren upptäcker verkliga fel (och har ofta rätt). Ta kritik som information, rätta, och förklara kort vad orsaken var.
- **Var ärlig om vad som inte är testat.** Det finns ingen riktig WordPress i testmiljön (se "Testa" nedan).

## Repo-karta

```
theme/tandstiftet-theme-v2/   temat (det som blir zip). THEME_SLUG i scripts/config.sh
design/concepts/              godkända konceptsidor (HTML) — designfacit per sida
sites/sparks-flytt/           fristående "Sparks har flyttat"-sida
archive/                      backuper (gamla "Ytterligare CSS")
scripts/                      check.sh · test.sh · build.sh · bump-version.sh · changelog-section.sh · deploy.sh
tests/php|css|browser|meta/   tester (se nedan)
docs/                         DESIGN.md (designregler) · PROJEKTKONTEXT.md (bakgrund/beslut/att-göra) · KOMMA-IGANG.md (GitHub) · PUBLICERA.md (knappen som lägger upp temat på one.com)
```

## Kommandon

```bash
bash scripts/check.sh                 # snabb statisk kontroll: PHP/JS-syntax, CSS-parenteser, version, ändringslogg
bash scripts/test.sh                  # PHP-, CSS- och webbläsartester (--no-browser hoppar över det sista)
bash scripts/build.sh --with-tests    # kör allt och bygger dist/tandstiftet-theme-v2-<version>.zip
bash scripts/bump-version.sh 2.1.0    # ny version i style.css + ny rubrik i CHANGELOG.md
python3 tests/meta/mutation_check.py  # förstör koden med flit och kontrollerar att testerna slår larm
bash scripts/deploy.sh check          # (med SFTP-uppgifter i miljön) testa anslutningen till webbhotellet
```

Krav lokalt: `bash`, `php` 8.x, `zip`; valfritt `node`; för webbläsartester `pip install -r tests/browser/requirements.txt`,
`python3 -m playwright install chromium` och typsnittet Bebas Neue (`fonts-bebas-neue` på Linux) — annars varnar testerna.

**Avsluta aldrig ett arbetspass med "klart" utan att `check.sh` och `test.sh` är gröna.** Skriv vad du körde.

## Hårda regler

1. **Ändra aldrig `THEME_SLUG`** (`tandstiftet-theme-v2`). WordPress knyter alla Customizer-inställningar till temats mappnamn. Temat importerar inställningar från v1 vid första aktivering (`tmc_import_previous_theme_mods`).
2. **Ny logik = testbar funktion i `functions.php` + test i `tests/php/`.** Testerna plockar ut funktioner *med namn* ur `functions.php` (PHP:s tokenizer) och kör dem mot stand-ins för WordPress. Håll funktionerna fria från global state där det går; ta tid/slump som valfritt argument (se `tmc_sliding_is_within_24h( $date, $now = null )`).
3. **Ändrar du markup i header, footer eller startsida** → uppdatera `tests/browser/harness.py` (den återger PHP:ns HTML; testerna körs mot den). Prova På återges i stället med den **riktiga** mallen via `tests/browser/render_page.php` (kräver php) — gör likadant för nya sidmallar när det går.
4. **CSS:** lägg nya regler i v2-blocken **sist** i `style.css` (senare regel vinner). Rör de äldre v1-blocken bara när det behövs. **Inga hårdkodade färger** — använd variablerna i `:root`. Kontrast och minsta textstorlek vaktas av `tests/css/run.py`; höj aldrig `SMALL_TEXT_BASELINE` för att få grönt.
5. **Ingen text som styrelsen kan vilja ändra får vara hårdkodad i en mall.** Lägg ett Customizer-fält (med standardvärde som är den nuvarande texten).
6. **Förutsätt inte att innehåll finns** (bilder, menyer, inlägg). Innehållet bor i WordPress databas, inte i repot. Bygg snälla tomlägen.
7. **Inget PHP-tillägg utöver standard** — använd inte `mb_*`. (`strtolower` hanterar inte Å/Ä/Ö sedan PHP 8.2; normalisera manuellt som i `tmc_maybe_hide_events_menu_item`.)
8. **Livesajten får inte röras** förrän testsajten är klar och användaren uttryckligen säger till — ingen publicering, inga ändringar, inget "live"-val i knappen (borttaget med flit). All utveckling visas på test.tändstiftet.se.
9. **Publicering** sker med knappen *Actions → Publicera tema* (`docs/PUBLICERA.md`). Inloggningen ligger som GitHub Secrets i miljöerna `beta`/`live` — be aldrig om den och skriv aldrig in den själv. Live bara från `master`.
10. **Inga hemligheter, lösenord, `wp-config.php` eller databasdumpar i repot.** Repot ska vara **privat** (det finns foto på en ung person i `design/concepts/images/`).
11. **Ändringslogg:** varje märkbar ändring → `CHANGELOG.md`. Release = höj version, tagga `vX.Y.Z` (GitHub Actions bygger zip).
12. **Rör inte det gamla temat** (v1 ligger kvar på sajten som reträtt).

## Teknisk karta (temat)

- **Sidmallar** (`templates/`): `page-prova-pa.php` (barn & ungdom), `page-sliding.php` (vuxna, bokningskalender), `page-forare-och-lag.php`, `page-info.php`, `page-longform.php` (Klubbinfo, ankar-sektioner via `tmc_section_N_*` egna fält), `page-kontakt.php` (Kontakta oss + Bli medlem), `page-styrelse.php`. Plus `front-page.php`, `archive-tmc_event.php`, `single-tmc_event.php`, `single.php`, `page.php`.
- **Post types:** `tmc_event` (tävlingar, slug `evenemang`), `tmc_rider` (förare; taxonomi `tmc_division`: `division-1`, `vuxen`, `ungdom`), `tmc_sponsor`, `tmc_gallery`, `tmc_board` (styrelse), `tmc_sliding_booking` (bokningar), `tmc_sliding_availability` ("Sliding — Datum": undantag per datum/disciplin).
- **Menyplatser:** `primary`, `footer-om`, `footer-trackracing`, `footer-besok`, `footer-praktiskt` (+ `footer`, `mobile`).
- **Customizer** (`inc/customizer.php`, panel `tmc_panel`): Hero · Statistikrad · Lagtexter · Sidfot & Sidhuvud · Sociala medier & Kontakt · Discipliner · Prova På/Sliding (startsida) · Visa/dölj sektioner · **Sliding — Speedway-schema** · **Sliding — Flat track-schema** · **Formulär — Blockeringslista** · **Startsida — Viktigt meddelande** · **Startsida — Tävlingar** (tävlingssäsong + texter, `tmc_comp_*`, `tmc_he_*`; läge via `tmc_home_events_state()`) · egna paneler **Sida: Prova På** / **Sida: Sliding** (`tmc_customizer_page_panel`: "Anmälan och säsong" + en sektion per del av sidan med "Visa"-ruta och texter; delar i `tmc_page_sections()`, texter i `tmc_provapa_fields()`/`tmc_sliding_fields()`, nycklar `tmc_pp_*`/`tmc_sl_*`) · **Header & meny** · Vi tar hand om varandra. Alla nycklar börjar på `tmc_`.
- **Av/på-växlar:** `tmc_sliding_enabled`, `tmc_flattrack_enabled`, anmälan per sida `tmc_pp_signup_mode`/`tmc_sl_signup_mode` (`season` = 1 apr–31 okt som standard, `open`, `closed`; `tmc_signup_state()`), delar av sidan `tmc_pp_show_*`/`tmc_sl_show_*`, (gamla `tmc_provapa_season_open` läses som reserv), `tmc_notice_enabled`, `tmc_nav_show_events`, `tmc_hero_torn_edge`, `tmc_show_*` (startsidans sektioner).
- **Specialsidor:** nya sidor av samma typ ska följa samma mönster — fältlista + `tmc_page_sections()` + panel via `tmc_customizer_page_panel()`, så att styrelsen får samma struktur överallt.
- **Hjälpfunktioner** i `functions.php`: `tmc_page_text`/`tmc_page_items`, `tmc_signup_*`/`tmc_season_*`, `tmc_sliding_*` (kapacitet, veckoschema per disciplin `tmc_sliding_hours_{mon..sun}_{speedway|flattrack}`, 24 h-regel, kalenderdata), `tmc_antispam_*` (honeypot, tidsfälla, validering, spärrlista), `tmc_provapa_*` (sidans texter, tider, numrerade delar), `tmc_render_notice_banner`, `tmc_hero_title_html`, `tmc_import_previous_theme_mods`, `tmc_maybe_hide_*_menu_item`.
- **JS:** `assets/js/main.js` — mobilmeny (dropdown), `fitHeader` (kompakt header om menyn inte ryms), hero-bildspel, scroll-animation, filter.
- **`style.css`:** v1-block först, sedan `v2 FOUNDATION`, `Restore the width caps`, `v2 — STARTSIDA`, `Phones: 17px root`. Se `docs/DESIGN.md` för färger, typografi, brytpunkter, komponenter.

## Design i en minut

Varm svart bas, guld + rost/orange, grädde/beige där man läser, filmkorn, tyst footer. **18 px bas** på dator/surfplatta, 17 px mobil.
Hamburgermeny ≤1099 px. Beige läsyta för längre text. Konceptsidorna i `design/concepts/` är facit — utgå från dem. Detaljer: `docs/DESIGN.md`.

## Innehålls- och affärsregler som inte får glömmas

- **Inga biljettköp online.** Biljetter säljs på plats. Inga "Köp biljett"-knappar eller fält.
- **Tävlingar** nås via "Nästa match"-remsan på startsidan, inte huvudmenyn (kryssruta i Anpassa slår på det). Huvudmeny: Start · Förare & Lag · Prova På · Sliding.
- **Prova På (barn & ungdom):** gratis; kom på ordinarie barnträning ons 17:00 / lör 10:00; ingen anmälan *krävs* men man bör höra av sig (annars risk att motorcyklar inte är förberedda). **Från 12 år krävs Svemo prova på-licens, 225 kr**, betalas separat till Svemo.
- **Sliding (vuxna):** 800 kr till klubben + 225 kr Svemo-licens; bokas alltid i förväg; kalas/möhippa/företag går bra. Kapacitet per kalenderlucka = tränarens tak (standard 8). **Ej bokningsbart inom 24 h** ("Ring oss"). "Fullt" visas bara när kapaciteten är nådd.
- **Flat track kan stängas av helt** och **Sliding kan pausas**. **Läget okt 2026:** banan är godkänd för flat track och klubben kan stötta, men det finns **inga flat track-tränare** → flat track i Sliding är **av som standard**. **Sparks Flattrack School finns inte längre** (inte heller kopplingen till Sören Teil Hansen) — nämn dem inte på sajten. Bakgrunden (oenighet kring Sparks) är känslig — skriv aldrig om den publikt.
- **Läsbarhet:** inget under ca 14 px, kontrast ≥ 6:1 för dämpad text, inget tunt typsnitt.
- **Foton på barn** kräver vårdnadshavares medgivande innan de används publikt.

## Status och nästa steg

- **v2.0.0 = grund + startsida**, byggd och testad lokalt men **inte ännu bekräftad live** av användaren.
- **v2.1.0 = Prova På** i v2-stilen (hero D), testad lokalt — väntar på användarens feedback och test live.
- **Kvar att klä om i v2-stilen (en i taget, i den här ordningen):** 1) ~~Prova På~~ (gjord i 2.1.0, väntar på feedback), 2) **Sliding**, 3) **Förare & Lag**, 4) **Tävlingar** (lista + enskild match), 5) övrigt (Klubbinfo, Styrelse, Kontakt, 404, inlägg).
- Hela att-göra-listan, öppna frågor och platshållardata: **`docs/PROJEKTKONTEXT.md` → "Att göra"**.

## Fallgropar (har bitit förut)

- En äldre "GLOBAL MOBILFIX" i `style.css` satte `max-width:100%` och upphävde containerbredderna. v2 återställer dem — ta inte bort blocket `Restore the width caps`.
- **Scroll-animationen** sätter `.news-card`/`.rider-card`/`.event-card` till opacitet 0 tills man skrollar. Skärmdumpar i tester måste skrolla först.
- **one.com Performance Cache (Varnish)** gör att ändringar kan se gamla ut — rensa cachen i wp-admin + Ctrl+Shift+R.
- Testmiljön saknar WordPress: harnessen återger PHP:ns HTML. Verifiera alltid på betasajten också.
- Webbtypsnitt (Bebas Neue, Barlow) laddas inte alltid i sandlådor → mät med riktiga typsnitt, annars blir rubriker bredare än i verkligheten.
- Konceptsidornas data (motståndare, resultat, förarnamn, "12 förare", priser) är **platshållare** — se PROJEKTKONTEXT.
