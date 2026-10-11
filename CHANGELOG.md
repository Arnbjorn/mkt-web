# Ändringslogg

Alla märkbara ändringar i temat. Format efter [Keep a Changelog](https://keepachangelog.com/sv/1.1.0/),
versionsnummer enligt [semantisk versionering](https://semver.org/lang/sv/) (`ÖVERGRIPANDE.NYTT.RÄTTNING`).

## [2.1.0] — 2026-10-11

Prova På-sidan (barn & ungdom) i v2-stilen, enligt `design/concepts/koncept-provapa.html` med hero "D".

### Nytt
- **Prova På — ny hero:** affischrubrik ("Prova på" i guld, "speedway" med penseldrag), gnistor i bakgrunden och foto i polaroidram med klistermärket "0 kr under 12 år". Fotot är sidans **Utvald bild**; utan bild visas bara klistermärket (ingen tom ram).
- **Prova På — nya sektioner:** faktaremsa, beige "Kom till en vanlig träning" med tider och licensinfo (länk till Svemo), "Fyra enkla steg", "Bra att veta" med frågor som fälls ut, och formuläret på beige.
- **Prova På-texterna i Anpassa** (*Sida: Prova På*): alla texter på sidan går att ändra (standard = nuvarande text). Tomt fält döljer den delen. Träningstiderna styr både tidskorten och valen i formuläret; en tredje tid kan läggas till.
- **Sliding i v2** enligt `koncept-sliding.html`: stor hero med foto (Utvald bild) och "Känn **sladden**", faktaremsa (antal discipliner följer Flat track-växeln), tydlig prisruta (800 + 225 kr), grupper/kalas, disciplinkort, redigerbar samarbetsruta (töm rubriken för att dölja), steg, "Bra att veta" och kalender + formulär på beige. Alla texter i *Anpassa → Sida: Sliding*, med egna varianter som används automatiskt när Flat track är avstängt. Bokningslogiken (kapacitet, 24 h, serverkontroller, spamskydd) är oförändrad.
- **Egna paneler i Anpassa för specialsidorna:** *Sida: Prova På* och *Sida: Sliding*. Överst **Anmälan och säsong**, sedan en rubrik per del av sidan i samma ordning som på sidan, var och en med **"Visa den här delen"** och delens texter. (Ersätter de långa listorna *Prova På — Sidan*, *Sliding — Sidan*, *Prova På — Säsong* och *Sliding — Paus/Aktiv*; Sliding-pausen och Flat track-växeln ligger nu under *Sida: Sliding → Anmälan och säsong*. Inga inställningar bytte namn.)
- **Anmälan och säsong:** *Följer säsongen* (standard, **1 april – 31 oktober varje år**), *Alltid öppen* eller *Stängd tills vidare*. Utanför säsongen stängs formuläret/bokningen automatiskt och sidan säger när det öppnar igen ("Vi öppnar igen 1 april 2027"). För Sliding blir dagar utanför säsongen ej bokningsbara i kalendern och nekas även på servern. Den gamla Prova På-kryssrutan respekteras fortfarande om den var urbockad.
- **Startsidans tävlingar väljer själv vad de visar** (*Anpassa → Startsida — Tävlingar*): kommande tävlingar när sådana finns (plus en rad om årets redan körda); **"Fler tävlingar kommer"** vid uppehåll under säsongen; **"Tack för i år!"** med årets tävlingar och tre rutor (Nyheter, Prova På med öppningsdatum, Medlemsmöten) när säsongen är slut — även under vintern tills nästa säsong börjar; och **ingenting** om säsongen saknar tävlingar. "Nästa tävling"-remsan visas bara när något är på gång. Tävlingssäsongen: start och slut var för sig som fast datum eller "första söndagen i månaden" (standard 1 april – första söndagen i oktober). Alla texter och rutor redigerbara.
- **Kalendern:** lediga dagar är riktiga knappar (tangentbord/skärmläsare), etiketterna "Fullt"/"Ring" är större (0,85 rem i stället för 0,68), och "skicka utan datum" visar ett meddelande på sidan i stället för en popup.
- **Vanliga sidor (Standardmall, `page.php`) i v2:** innehållet på beige läsyta i en smal spalt (max 800 px), större text, rubriker i Bebas, rostbruna länkar, guldknappar för WordPress knappblock och sidans Utvald bild överst. Bloggsidopanelen (Sök, Senaste inläggen) är borttagen från vanliga sidor. Används t.ex. för Speedway, Flat track och Veteraner.
- **Publicera med en knapp:** GitHub → Actions → *Publicera tema* kör alla tester, laddar upp temat till one.com via SFTP, byter version på en sekund, kontrollerar sajten och backar automatiskt vid fel (`scripts/deploy.sh`, `.github/workflows/deploy.yml`). Live bara från `master`. Inställning: `docs/PUBLICERA.md`.
- Webbläsartesterna återger nu den **riktiga** PHP-mallen för Prova På (`tests/browser/render_page.php`) och kontrollerar bl.a. bredder, klistermärket, säsongsstängning och minsta textstorlek.

### Ändrat
- **Flat track i Sliding är avstängt som standard** (`tmc_flattrack_enabled` = av): banan är godkänd för flat track men klubben har inga flat track-tränare just nu, och Sliding inkluderar instruktör. Slå på under *Sida: Sliding → Anmälan och säsong* när det finns tränare.
- **Samarbetsrutan på Sliding är tom som standard** — Sparks Flattrack School och kontaktpersonen är borttagna (finns inte längre). Fälten finns kvar för en eventuell framtida partner.
- Prova På: texten om att höra av sig säger nu att det annars är risk att motorcyklar inte är förberedda (inte "väder eller banans skick").
- Prova På: formuläret visas igen tillsammans med felmeddelandet (förut försvann det), med ifyllda fält kvar. Fälten har nu etiketter kopplade till rutorna (bättre för skärmläsare). "Namn" heter "Barnets namn".
- Prova På: utrustning, säkerhet, fler tillfällen och "nästa steg" (medlemskap, trackracing-skolan, tävla) ligger nu som frågor under "Bra att veta" i stället för egna långa sektioner.
- Rutan **"Prova På — Utrustningsikoner"** i sidredigeraren är borttagen (används inte av den nya sidan). Sparade värden ligger kvar i databasen men visas inte.
### Rättat
- Tävlingskortens datumruta: månaden klipptes ("24 MA…") — dag och månad ligger nu under varandra. Korten går till en kolumn när två inte får plats.
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
