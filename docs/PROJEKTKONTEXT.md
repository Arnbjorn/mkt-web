# Projektkontext — Tändstiftet MC

Det här är bakgrunden från den långa arbetsdiskussionen (flera sessioner i Claude), så att arbetet kan fortsätta i Claude Code utan att något går förlorat.
**Korta reglerna** står i `CLAUDE.md`. Här står *varför*, vad som är beslutat, vad som är gjort och vad som återstår.

> Markeringar: **✅ klart**, **🟡 klart men ej bekräftat live**, **⬜ återstår**, **❓ öppen fråga till användaren**, **⚠ platshållare/ej verifierat**.

---

## 1. Projektet

- **Kund:** Motorklubben Tändstiftet (MKT), Malmö. Speedway och flat track (Division 1 speedway, ungdoms- och nybörjarverksamhet, "Sliding" för vuxna). Svemo-ansluten klubb. Hemmabana: Malmö Motorstadion.
- **Sajt:** WordPress, hostad hos **one.com**. Testsajt: `https://test.tändstiftet.se` (ny ren WordPress, okt 2026 — `beta.tändstiftet.se` finns men fungerar inte just nu och filerna där går inte att nå). ⚠ Testsajten är **tom**: inga sidor, menyer, bilder eller Anpassa-inställningar, och inget v1-tema att importera inställningar från — allt får läggas in på nytt (se "Verifiera live"). Mejl via SMTP (WP Mail SMTP, one.com); SPF-post som ska finnas: `v=spf1 include:_custspf.one.com -all`.
- **Användaren** är ingen utvecklare. Styrelsen ska själv kunna uppdatera allt rimligt via *Utseende → Anpassa* och wp-admin.
- **Publik:** föräldrar, nybörjare och klubbmedlemmar — många **äldre med sämre syn**. Sajten ska vara **inbjudande, snabb att navigera** och inte dra uppmärksamhet till administrativ info (klubbinfo/styrelse) men ändå ha den.
- **Bakgrund (känsligt, håll det internt):** det finns oenighet i klubben kring **Sparks** (organisationen som lånar ut motorcyklar för flat track), trots att alla är medlemmar. Därför kan **Sliding pausas** och **Flat track stängas av helt**, och Sparks-rutan ska kunna ändras/tas bort. **Sparks tränarteam finns inte just nu.** Skriv aldrig om konflikten på publika sidor.

## 2. Arbetsgången hittills

1. **v1-sessionerna** (före detta arbete): temat byggdes (sidmallar, CPT:er, Customizer), Sparks fick egen fristående sida, ikonset, bokningskalender m.m. Se avsnitt 4.
2. **Fas 1 — koncept** (HTML-mockuper, ej WordPress): startsida, Prova På, Sliding, Förare & Lag, Tävlingar, enskild match. **Godkända** → `design/concepts/`.
3. **Fas 2 — finslipning:** footer, färgkonsekvens, kortlayouter, logga-vattenstämpel, mobil (hamburgare), Prova På-hero.
4. **Fas 3 — implementation i temat, en sida i taget:** grund + **startsida klar (v2.0.0)**. Därefter Prova På → Sliding → Förare & Lag → Tävlingar → övrigt.
5. **Repo + testsystem + GitHub Actions** uppsatt (det här repot).

Användarens uttryckta önskan: *"En sida i taget, med din feedback mellan varje."*

## 3. Designbeslut och varför

**Resan:** mörk marinblå/guld (v1, vintage-affisch) → referensaffischer (mörka, dramatiska, guld, grov textur) → ett försök med **ljus beige som bas** (**förkastat**: blev "företag/politiskt", läsbarhetsproblem, förlorade "track racing") → **Sparks-inspirerad varm svart + guld + rost/orange**, med **beige exakt där man läser**. Den godkända kombinationen är *"motorsport"* (blå+guld från loggan, orange som komplementfärg). Blått finns i praktiken via **loggan**; UI:t är svart/guld/orange. (En "blå vågtopp" provades i `sparks-blavag-koncept` men valdes inte.)

| Beslut | Motivering |
|---|---|
| Varm svart (`#14151a`/`#1c1a16`), guld, rost, orange | Fångar dammig dirt-track-känsla; guld + orange är blå+guld:s komplement (som racing-liveries) |
| **Beige/grädde läsytor** (`.section--beige`) | Längre text är lättare att läsa mörkt-på-ljust; används för nyheter, schema, formulär |
| **Filmkorn** över sidan, rivd bildkant i hero | Gör att det inte känns som ett "rent digitalt företag" |
| **Riktiga loggan** (vingat tändstift) i header, footer, vattenstämpel 12 % **bara på startsidans hero** | Användaren valde 12 % och bara startsidan |
| **Header:** logga + namn + meny + guldknapp; ingen topprad, inget rutband | "Inbjudande och snabb" |
| **Tyst footer** (små, dämpade länkar) | Klubbinfo/styrelse ska finnas men inte konkurrera |
| **18 px bas / 17 px mobil**, ljusare dämpad grå, inget tunt typsnitt | Äldre/sämre syn. Kontrast mellan nyckelfärger vaktas av test |
| **Hamburgermeny ≤1099 px**, dropdown, stängs av samma knapp/Esc/utanför | Gamla fullskärmsmenyn täckte knappen → gick inte att stänga |
| **Tävlingar ej i huvudmenyn** | Nås via "Nästa match"-remsan (kryssruta för att visa i meny) |
| **Inga biljettköp** | Biljetter säljs alltid på plats |
| **Textstorleksknapp (A/A+/A++)** | ⬜ *uppskjuten* — användaren vill se sidorna live först |

**Konceptsidor (facit):** `design/concepts/koncept-med-riktig-logga.html` (startsida), `koncept-provapa.html`, `koncept-sliding.html`, `koncept-forare-lag.html`, `koncept-tavlingar.html`, `koncept-match-enskild.html`, `provapa-hero-alternativ.html`.
Äldre förkastade utforskningar (ljus, "grittig", "moderniserad", blå våg) finns **inte** i repot.

## 4. Funktioner som redan finns i temat

Alla med Customizer-nycklar (`tmc_…`) — se `CLAUDE.md` för kartan.

- **Sliding — Paus/Aktiv** (`tmc_sliding_enabled`): av → `/sliding/` visar pausmeddelande (med Prova På-länk + direktkontakt); Sliding-länken försvinner ur **alla** menyer, korslänkar och startsidans punkter.
- **Flat track på/av** (`tmc_flattrack_enabled`, i samma sektion): av → inga Flat track-kort, ingen Sparks-ruta, inget Flat track-val i formuläret, inga Flat track-luckor i kalendern. Speedway påverkas inte.
- **Bokningskalender (Sliding):** visar 90 dagar; **veckoschema per disciplin** (`tmc_sliding_hours_{mon…sun}_{speedway|flattrack}`; tomt = stängd dag, annars tid t.ex. `18:00`); **kapacitet per disciplin** (standard 8 = tränarens ansvarstak); **datum-överstyrningar** (CPT `tmc_sliding_availability`, fält för disciplin Båda/Speedway/Flat track, egen kapacitet eller stängt) vinner alltid över veckoschemat. Flikar Speedway/Flat track. **24 h-regel:** datum <24 h bort visas "Ring oss" och nekas även på servern. **"Fullt"** visas bara när kapaciteten är slut (dagar utan schema är bara grå). Serverkontroll av allt vid inskick (dubbelbokning, ogiltigt datum, för sent). Gruppstorlek räknas mot kapacitet.
- **Bokningar** sparas som CPT `tmc_sliding_booking` (räknas mot kapacitet; avbokad = frigör platser) + mejl till `admin_email`. Adminlista med kolumner och **CSV-export** (semikolon, UTF-8 BOM för svensk Excel).
- **Spamskydd (alla fyra formulär: Kontakt, Bli medlem, Prova På, Sliding):** honeypot, **tidsfälla (<3 s)**, validering (ålder 1–100, barn max 18, födelseår 1920–i år, länkar i namn/meddelande, max 3000 tecken) och **spärrlista** (e-post eller `@domän`, *Anpassa → Formulär — Blockeringslista*; tyst avvisning). Bots avvisas tyst; felmeddelande bara vid valideringsfel.
- **Formulär:** fält fylls i igen vid fel (inkl. valt datum/disciplin i kalendern); formuläret **döljs efter lyckat inskick**.
- **Notisbanderoll på startsidan** (*Anpassa → Startsida — Viktigt meddelande*): på/av, "vanlig" (guld) eller "viktig" (röd), rubrik, text, valfri knapp. Stängbar; återkommer automatiskt om texten ändras (hash).
- **Prova På — Säsong** (`tmc_provapa_season_open` + meddelandetext): stänger *bara* formuläret och "kom bara"-uppmaningarna; resten av sidan ligger kvar.
- **Header & meny** (`tmc_brand_short`, `tmc_brand_tagline`, `tmc_nav_cta_text/url`, `tmc_nav_show_events`), **Rivd bildkant** (`tmc_hero_torn_edge`).
- **Import av inställningar** från v1 vid första aktivering (v2 är ett *separat* tema; v1 rörs inte → alltid möjligt att byta tillbaka).
- **Klubbinfo** (`page-longform.php`): sektioner via egna fält `tmc_section_count` och `tmc_section_N_title|slug|content|image`.

## 5. Innehålls- och affärsregler (per sida)

**Prova På (barn & ungdom)**
- Gratis; ordinarie barnträning **onsdag 17:00 och lördag 10:00**. Åldersgräns "6+" bekräftad av användaren.
- *"Ingen anmälan krävs men det är risk att vi inte har förberett motorcyklar eller är redo att ta emot dem"* → man bör höra av sig (det är den formuleringen som ska gälla, inte "väder/banans skick").
- **Från 12 år: Svemo prova på-licens, 225 kr**, betalas separat till Svemo. Under 12: ingen avgift.
- Formulär "Hör av dig innan du kommer": namn, ålder, e-post, telefon, skostorlek, längd, träningstillfälle, provat förut, övrigt.
- Säsongsstängning via Customizer när träningarna är slut.
- **Hero: alternativ D är godkänt** — affischrubrik ("Prova på" guld, "speedway" vit med penseldrag), foto i **ram/polaroid** med klistermärke "0 kr under 12 år", gnistor i bakgrunden. Fotot i konceptet är en **lågupplöst platshållare** (562×750, tonåring). Behövs ett riktigt foto + vårdnadshavares medgivande. Bilden ska gå att byta (Utvald bild).

**Sliding (vuxna)**
- **800 kr till klubben + 225 kr Svemo-licens.** Boka i förväg (ingen drop-in). Utrustning/instruktör ingår. Även kalas/möhippa/svensexa/företag. Disciplin: Speedway eller Flat track. ⚠ "1,5 h längd" och andra faktarutor i konceptet ska verifieras mot nuvarande text.
- Kalender enligt avsnitt 4. Veckodagar/tider styr styrelsen själva ("1–3 gånger i veckan").
- **Sparks-ruta** ("Flat track i samarbete med The Sparks Flattrack School") har kontaktnamn/telefon **hårdkodade i `templates/page-sliding.php`** → ⬜ gör till Customizer-fält så de kan ändras/tömmas (inget tränarteam just nu). Försvinner redan helt när Flat track stängs av.
- ⬜ Kalenderns små dag-etiketter ("Fullt"/"Ring oss", ≈12 px) är för små för målgruppen — korta texten och/eller gör rutorna större när sidan tas.

**Förare & Lag**
- Två sektioner: **Ungdomslaget** (primär, beige, *före*) som hyllar barn/ungdomar som gått från Prova På till tävling, med länk "Vill ditt barn också börja? → Prova På", och **Division 1 / Säsongens förare** (mörk). **Inget filter** just nu. **Vuxna förare (utanför Div 1) finns inga just nu** — lägg till sektion/filter när de finns (eller uppdelning speedway/flat track).
- Data: CPT `tmc_rider`, taxonomi `tmc_division` (`division-1`, `vuxen`, `ungdom`), kortformat 3:4 (`rider-card`).
- ⚠ "12 förare", nummer (32X, 32, U19), namn och bilder i konceptet är platshållare.

**Tävlingar (lista) och enskild match**
- Lista: "senaste resultat"-block, hemma/borta-markering, schema, knapp **"Matchinfo →"**, text *"Biljett säljs på plats"*. Enskild match: lag, datum/tid/plats, "Om matchen", faktaruta (pris, barn gratis ⚠ platshållare), laguppställning med länk till Förare & Lag.
- ⬜ **Datamodellen räcker inte** för konceptet: `tmc_event` har datum, tid, plats, adress, pris, Facebook-länk — men **inte** motståndare, hemma/borta, resultat eller laguppställning. Nya fält/taxonomi behövs när sidan byggs. Motståndarnamn och resultat (Vikingarna, Kaparna, 48–42 …) i konceptet är påhittade.

**Disciplinerna (Speedway, Flat track, Veteraner)** — beslut 2026-10-11: korten tas **bort från startsidan** (kryssruta *Startsida — Visa/dölj sektioner → Discipliner*) och läggs som länkar i footerns kolumn **Trackracing** (meny i *Utseende → Menyer*). Varje disciplin får en **enkel egen sida** (slug `speedway`, `flat-track`, `veteraner`). Skäl: Veteraner är bara 2 personer och flat track ligger på is (stödet finns kvar). ⬜ **Delprojekt senare:** gå igenom och utveckla innehållet på de tre sidorna. ⚠ Standardtexterna i temat ("500cc utan broms", "40+ år") är inte verifierade. Obs: footermenyn döljer inte Flat track-länken automatiskt när `tmc_flattrack_enabled` är av.

**Startsida** ✅🟡 — hero, statistikrad, nästa tävling (utan biljettknapp), sektioner, Prova På-ruta, kommande tävlingar, **nyheter på beige**, förare, värderingar, **sponsorer på beige** (en saknad öppnande `<section>` rättades).

**Footer** — tre/fyra kolumner (brand, menyer, kontakt) + Svemo/Bingolotto-länkar; menyerna sätts i *Utseende → Menyer*.

**Medlemsmöten** (text från styrelsens adjungerade): *"Första onsdagen varje månad kl. 18.00 håller vi medlemsmöte på klubben. På medlemsmötena pratar vi om vad som händer i föreningen, planerar kommande aktiviteter och tävlingar och hjälps åt med det som behöver göras. Alla medlemmar är varmt välkomna att delta, komma med idéer och bidra där de kan."* → som sektion "Medlemsmöten" (slug `medlemsmoten`) på Klubbinfo, och/eller i notisbanderollen. ❓ Oklart om det är inlagt.

## 6. Att göra

### Verifiera live (användaren)
- 🟡 **Testsajten test.tändstiftet.se är en ny tom WordPress.** Klart: temat publicerat och aktivt, sidan Prova På skapad. Kvar: logga (*Anpassa → Webbplatsidentitet*), övriga sidor, menyer. För att se v2 där: publicera temat (knappen, `docs/PUBLICERA.md`) och aktivera det; skapa sidorna (Start som statisk startsida, Prova På med mallen *Prova På (Barn & Ungdom)* och slug `prova-pa`, Sliding m.fl.); sätt menyerna; ladda upp logga och bilder. Importen från v1 gör ingenting här (inget v1-tema på den sajten).
- ⬜ Aktivera `tandstiftet-theme-v2` på betasajten; kolla att **inställningarna följde med** (logga, kontakt, hero, menyer). *Importen är enhetstestad men aldrig körd i riktig WordPress.*
- ⬜ Titta på startsidan på dator **och** mobil. Rensa one.com-cachen först.
- ⬜ **Statistikraden:** standardvärdet för "Grundat" är **1970**, men loggan/footern säger **1924**. ❓ Vilket stämmer? (Anpassa → Statistikrad)
- ⬜ Menyn: *Utseende → Menyer → Primär* ska vara Start · Förare & Lag · Prova På · Sliding (+ Tävlingar om kryssrutan slås på).
- ⬜ **GitHub Actions** har aldrig körts på GitHub (YAML validerad, kommandon körda lokalt). Titta på fliken *Actions* efter första push.

### Beslut från användaren
- ❓ **Hur går testsajten live till slut?** Antingen flyttas hela test.tändstiftet.se (med innehåll) till livesajtens adress, eller så byts bara temat på livesajten (som behåller sitt innehåll). Avgör om innehåll som läggs in på testsajten är "på riktigt" eller bara testdata. Livesajten rörs inte förrän testsajten är klar.
- ❓ **Permanent mappnamn** för temat (nu `tandstiftet-theme-v2`). Ändras det senare måste inställningarna flyttas igen.
- ❓ Ska Sparks-rutan bli redigerbar/borttagbar i Anpassa (förslag: ja)?
- ❓ Ska "Vuxna förare" få en egen sektion/platshållare, eller vänta tills det finns några?
- ❓ Textstorleksknapp — efter att sidorna setts live.

### Kodbacklog (i föreslagen ordning)
1. 🟡 **Prova På** i temat enligt `koncept-provapa.html` + hero D (v2.1.0). Texter i *Anpassa → Prova På — Sidan*. Godkänd av användaren 2026-10-11 (inkl. fällbara frågor och "6+ år"). ✅ Publicerad via knappen och kontrollerad på test.tändstiftet.se/prova-pa/ 2026-10-11 (dator + mobil, riktiga typsnitt, ingen sidledsscroll). ⬜ byt platshållarfotot mot riktigt foto (Utvald bild) med vårdnadshavares medgivande.
2. **Sliding** (kalender, flikar, disciplinkort, redigerbar Sparks-ruta, större dag-etiketter).
3. **Förare & Lag** (Ungdomslaget först).
4. **Tävlingar:** lista + enskild match + nya datafält (motståndare, hemma/borta, resultat, laguppställning).
5. Klubbinfo, Styrelse, Kontakt, 404, inlägg, galleri — kontroll att de ser bra ut i v2. **`page.php` (vanlig sida) har fortfarande v1-utseende** med bloggsidopanel (Sök, Senaste inläggen) — påverkar bl.a. disciplinsidorna; kontrollerat på testsajten 2026-10-11.
6. **Hero-fallback:** `front-page.php` pekar på `assets/images/hero-bg.jpg` som inte finns i temat om ingen hero-bild är vald → bakgrunden blir tom. Lägg en fallback eller dölj.
7. Barlow (brödtextfonten) har inte kunnat testas med riktig font i testmiljön — kontrollera radbrytningar live.

### WordPress-admin (från tidigare sessioner; status ej bekräftad)
- ⬜ Ställ in kapacitet och veckodagar: *Anpassa → Sliding — Speedway-schema / Flat track-schema*; skapa vid behov undantag under *Sliding — Datum*.
- ⬜ Kontrollera WP Mail SMTP (one.com) + SPF-posten ovan.
- ⬜ Ladda upp `sites/sparks-flytt/index.html` till webbroten för `sparks.tändstiftet.se` (ersätter gamla Sparks-sidan). Texten är medvetet **stöttande** ("Sparks har vuxit och drivs självständigt"), inget om avslutat samarbete. Länken pekar på `thesparks.vercel.app` (temporär) → byt till permanent (`thesparks.se` / `thesparksflattrackschool.se`; thesparks.se verkar vara live nu).
- ⬜ Ladda upp rensade styrelsefoton (Gert Friman, Dawid Owizyc, Fredrik Billgren — text borttagen med inpainting) som *Utvald bild* på respektive styrelsemedlem, och ungdomsförarens bild (`forare_bild.jpg`, beskuren 562×750) på ungdomsföraren.
- ⬜ Favicon-set (vingat tändstift + rutflagga) till Sparks-sidan.
- ⬜ Medlemsmöten (avsnitt 5).
- *Bildfilerna för styrelsefoton, favicons och bakgrundsbilder ligger **inte** i repot (personbilder/binärt). Be användaren om dem vid behov.*

### Senare
- Textstorleksknapp (A/A+/A++), uppdatering av temat direkt från GitHub-releaser i wp-admin, exportskript för Customizer-inställningar (säkerhetskopia), Barlow-test.

## 7. Tekniska lärdomar

- **WordPress knyter Customizer-inställningar till temats mappnamn** (`theme_mods_<slug>`). Därför importfunktionen och den permanenta mappnamnsregeln.
- **Testsystemet:** PHP-testerna plockar *riktiga* funktioner ur `functions.php` med tokenizern och kör dem mot stand-ins; webbläsartesterna använder `tests/browser/harness.py`, som återger PHP:ns HTML med temats riktiga CSS/JS. `mutation_check.py` bevisar att larmen går. Två tester har tidigare varit värdelösa (en radräkning som alltid gav 0; en 24 h-test som berodde på klockslag) — **testa alltid att ett nytt test *kan* misslyckas.**
- **Headern** är en rad (logga · meny · knapp). `fitHeader` i `main.js` kompakterar den automatiskt; 7+ menyalternativ radbryts på dator med flit (utan att något spricker).
- **Scroll-animation** → opacitet 0 tills man skrollar; **äldre `flex-wrap: wrap` på `.hero__kicker`** skjöt texten under strecket; **`max-width:100%` i mobilfixen** upphävde containerbredder. (Alla rättade — se `CHANGELOG.md`.)
- **Typsnitt:** utan Bebas Neue blir rubriker bredare och bryts fult. `fonts-bebas-neue` finns i Ubuntu; Barlow finns inte som paket.
- **Rättigheter/foto:** referensaffischerna användaren laddade upp (Division 1-affisch, Prova På-affisch) och fotona i konceptsidorna används där som platshållare. ❓ Bekräfta att klubben har rättigheterna innan någon av dem används på den publika sajten (affischerna ser ut att vara klubbens egna, men det är ett antagande).

## 8. Komma igång i Claude Code

```bash
cd tandstiftet
claude
```

Förslag på första meddelande:

> Läs `CLAUDE.md` och `docs/PROJEKTKONTEXT.md`. Kör `bash scripts/check.sh` och `bash scripts/test.sh` och berätta om allt är grönt (och vad som eventuellt saknas på min dator). Fortsätt sedan med **Prova På-sidan** (`theme/tandstiftet-theme-v2/templates/page-prova-pa.php`) enligt `design/concepts/koncept-provapa.html` med hero "D". En sida i taget — visa mig resultatet och fråga innan du går vidare. Behåll alla Customizer-inställningar, formuläret, spamskyddet och säsongsstängningen.

Bra vanor: be Claude köra `scripts/test.sh` innan den säger att något är klart; be om skärmdump/jämförelse vid designändringar; skriv i `CHANGELOG.md` löpande; släpp via `bump-version.sh` + tagg.

## 9. Ordlista

- **Prova På** — gratis nybörjarträning för barn & ungdom (speedway). **Sliding** — betald prova-på för vuxna (flat track/speedway). 
- **Svemo** — Svenska Motorcykel- och Snöskoterförbundet; licenser. **Div 1** — Division 1 (speedway-serie). **Speedway** — oval bana, ingen broms. **Flat track** — dirt track med bromsar.
- **Sparks (The Sparks Flattrack School)** — separat organisation som driver flat track-skola/lånar ut motorcyklar; har nu egen sajt.
- **Customizer / Anpassa** — WordPress inställningspanel under *Utseende → Anpassa*. **CPT** — custom post type (egen innehållstyp). **Slug** — mappnamn/URL-namn.
