# Ändringslogg

Alla märkbara ändringar i temat. Format efter [Keep a Changelog](https://keepachangelog.com/sv/1.1.0/),
versionsnummer enligt [semantisk versionering](https://semver.org/lang/sv/) (`ÖVERGRIPANDE.NYTT.RÄTTNING`).

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
