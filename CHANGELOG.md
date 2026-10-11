# Ändringslogg

Alla märkbara ändringar i temat. Format efter [Keep a Changelog](https://keepachangelog.com/sv/1.1.0/),
versionsnummer enligt [semantisk versionering](https://semver.org/lang/sv/) (`ÖVERGRIPANDE.NYTT.RÄTTNING`).

## [2.1.0] — 2026-10-11

Prova På-sidan (barn & ungdom) i v2-stilen, enligt `design/concepts/koncept-provapa.html` med hero "D".

### Nytt
- **Prova På — ny hero:** affischrubrik ("Prova på" i guld, "speedway" med penseldrag), gnistor i bakgrunden och foto i polaroidram med klistermärket "0 kr under 12 år". Fotot är sidans **Utvald bild**; utan bild visas bara klistermärket (ingen tom ram).
- **Prova På — nya sektioner:** faktaremsa, beige "Kom till en vanlig träning" med tider och licensinfo (länk till Svemo), "Fyra enkla steg", "Bra att veta" med frågor som fälls ut, och formuläret på beige.
- **Anpassa → Prova På — Sidan:** alla texter på sidan går att ändra (standard = nuvarande text). Tomt fält döljer den delen. Träningstiderna styr både tidskorten och valen i formuläret; en tredje tid kan läggas till.
- **Vanliga sidor (Standardmall, `page.php`) i v2:** innehållet på beige läsyta i en smal spalt (max 800 px), större text, rubriker i Bebas, rostbruna länkar, guldknappar för WordPress knappblock och sidans Utvald bild överst. Bloggsidopanelen (Sök, Senaste inläggen) är borttagen från vanliga sidor. Används t.ex. för Speedway, Flat track och Veteraner.
- **Publicera med en knapp:** GitHub → Actions → *Publicera tema* kör alla tester, laddar upp temat till one.com via SFTP, byter version på en sekund, kontrollerar sajten och backar automatiskt vid fel (`scripts/deploy.sh`, `.github/workflows/deploy.yml`). Live bara från `master`. Inställning: `docs/PUBLICERA.md`.
- Webbläsartesterna återger nu den **riktiga** PHP-mallen för Prova På (`tests/browser/render_page.php`) och kontrollerar bl.a. bredder, klistermärket, säsongsstängning och minsta textstorlek.

### Ändrat
- Prova På: texten om att höra av sig säger nu att det annars är risk att motorcyklar inte är förberedda (inte "väder eller banans skick").
- Prova På: formuläret visas igen tillsammans med felmeddelandet (förut försvann det), med ifyllda fält kvar. Fälten har nu etiketter kopplade till rutorna (bättre för skärmläsare). "Namn" heter "Barnets namn".
- Prova På: utrustning, säkerhet, fler tillfällen och "nästa steg" (medlemskap, trackracing-skolan, tävla) ligger nu som frågor under "Bra att veta" i stället för egna långa sektioner.
- Rutan **"Prova På — Utrustningsikoner"** i sidredigeraren är borttagen (används inte av den nya sidan). Sparade värden ligger kvar i databasen men visas inte.
### Rättat
- CI kördes bara vid push till `main`, men huvudgrenen heter `master` — testerna kördes alltså aldrig automatiskt vid push. Nu körs de för båda.

## [2.0.0] — 2026-10-10

Första versionen av det nya utseendet ("v2"): varm svart / guld / rost, med grädde och beige där man läser.

### Nytt
- **Ny palett och textur:** varmt svart, guld, rost och orange; filmkorn över sidan. Variabelnamnen från v1 behölls (t.ex. `--color-navy`) så att befintliga komponenter byter utseende utan att skrivas om.
- **Ny header:** logga + klubbnamn + meny + guldknapp. Topprad och rutband borttagna. Namn, tagline och knapp ändras under *Anpassa → Header & meny*.
- **Mobilmeny som dropdown** med kryss som stänger den (den gamla fullskärmsmenyn täckte knappen). Stänger även med Esc och vid tryck utanför.
- **Automatisk kompakt header** (`main.js`): om menyn inte ryms på en rad döljs taglinen och länkavståndet minskas.
- **Tyst footer** med samma menyer och kontaktuppgifter som tidigare.
- **Startsidan:** hero med rivd bildkant (kan stängas av under *Anpassa → Hero*), sista ordet i rubriken i guld, logga som vattenstämpel (12 %), kantknapp bredvid guldknappen, lugn statistikrad, "Nästa tävling"-remsa, beige nyhetssektion och beige sponsorremsa.
- **"Visa Tävlingar i huvudmenyn":** kryssruta under *Anpassa → Header & meny* (av som standard). Tävlingar nås då via "Nästa match"-remsan.
- **Inställningar följer med:** vid första aktiveringen kopieras Customizer-inställningarna från det tidigare Tändstiftet-temat (det gamla temat rörs inte). Skriver aldrig över befintliga v2-inställningar.
- **Läsbarhet:** bastextstorlek 18 px på dator/surfplatta och 17 px på mobil (förut 16 px), ljusare dämpad grå, minsta storlekar höjda, tätare bokstavsavstånd på etiketter. Skyddas av tester (`tests/css`).

### Ändrat
- **"Köp biljett" borttaget överallt** (startsida, tävlingslista, enskild tävling, adminfältet "Länk köp biljetter"). Biljetter säljs på plats.
- Statistikraden är inte längre ett solitt guldblock.
- Internt, för testbarhet: rubriklogiken ligger nu i `tmc_hero_title_html()` och `tmc_sliding_is_within_24h()` kan ta en fast "nu"-tid (valfritt andra argument; beteendet i sajten är oförändrat).
- Repo, byggskript, tester (PHP, CSS, webbläsare) och GitHub Actions tillagda.

### Rättat
- **Bredden:** en äldre "mobilfix" satte `max-width: 100%` på `.container`, `.site-header__inner` m.fl. och upphävde 1200/800 px-taken, så innehållet gick kant till kant på breda skärmar. Taken återställda.
- **Sponsorsektionen** saknade sin öppnande `<section>`-tagg; rubriken (mörk text) hamnade på mörk bakgrund och syntes inte.
- Hero-kickerns guldstreck hamnade ensamt på egen rad på mobil (äldre `flex-wrap: wrap`).
- Menyfiltret döljer "TÄVLINGAR" även i versaler, utan beroende av PHP-tillägget `mbstring`.
- Städat bort en tom mapp med klammerparenteser i namnet (`{inc,template-parts`).
