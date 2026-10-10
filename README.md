# Motorklubben Tändstiftet — webbplats

WordPress-temat för Tändstiftet MC, plus designunderlag, tester och skript som bygger ett installerbart tema-paket.

> **Första gången?** Börja med [docs/KOMMA-IGANG.md](docs/KOMMA-IGANG.md) — hur du får upp repot på GitHub, steg för steg.

## Vad som finns här

```
theme/tandstiftet-theme-v2/   själva temat — det som blir zip-filen
design/concepts/              godkända konceptsidor (HTML) — designreferens, inte en del av sajten
sites/sparks-flytt/           den fristående "Sparks har flyttat"-sidan
archive/                      sparade backuper (t.ex. den gamla "Ytterligare CSS")
scripts/                      bygg-, kontroll- och versionsskript
tests/                        automatiska tester (PHP, CSS, webbläsare)
docs/                         designguide och instruktioner
.github/workflows/            automatik: testar varje ändring, bygger zip vid ny version
```

## Bygg tema-paketet

Behöver `bash`, `php` och `zip` (Node är valfritt och används för JavaScript-kontroll).

```bash
bash scripts/build.sh                 # kontrollerar temat och bygger dist/tandstiftet-theme-v2-<version>.zip
bash scripts/build.sh --with-tests    # kör dessutom alla tester först
```

Zip-filen innehåller en enda toppmapp med exakt temats namn, vilket är det WordPress vill ha
(*Utseende → Teman → Lägg till nytt → Ladda upp tema*). Utvecklingsfiler (`.DS_Store`, `*.map` m.m.) kommer inte med.
Windows utan bash? Se nästa avsnitt — då bygger GitHub paketet åt dig.

## Släpp en ny version

1. **Höj versionen:** `bash scripts/bump-version.sh 2.1.0` (ändrar `Version:` i `style.css` och lägger till ett avsnitt i `CHANGELOG.md`).
   Utan skript: ändra raden `Version:` i `theme/tandstiftet-theme-v2/style.css` och lägg till `## [2.1.0] — datum` i `CHANGELOG.md` för hand.
2. **Fyll i ändringsloggen** — den blir texten på GitHub-releasen.
3. **Commit och push** till `main`. Fliken *Actions* kör alla tester; är allt grönt går det att gå vidare.
4. **Skapa en release:** på GitHub → *Releases → Draft a new release → välj "Create new tag" `v2.1.0` → Publish*.
   (Eller på kommandoraden: `git tag v2.1.0 && git push origin v2.1.0`.)
5. Efter någon minut ligger **`tandstiftet-theme-v2-2.1.0.zip` bifogad på releasen.** Ladda ner den och installera i WordPress.

Taggen måste vara `v` + versionen i `style.css`, annars stoppar bygget med ett tydligt felmeddelande (så att man aldrig släpper en zip vars innehåll säger en annan version än namnet).

Versionsnummer: `ÖVERGRIPANDE.NYTT.RÄTTNING` — t.ex. `2.1.0` när något nytt tillkommer, `2.0.1` för en ren buggrättning, `3.0.0` för ett helt nytt utseende.

## Köra testerna

```bash
bash scripts/check.sh          # snabbt (några sekunder): syntax, CSS-parenteser, version, ändringslogg
bash scripts/test.sh           # allt som går att köra på din dator
bash scripts/test.sh --no-browser   # hoppa över de långsammare webbläsartesterna
```

| Test | Vad det skyddar |
|---|---|
| `tests/php/` | Logiken i `functions.php`: import av inställningar, menyfiltret för Tävlingar, rubriken, spamskyddet, spärrlistan, Sliding-schemat och 24-timmarsregeln. Testar den **riktiga** koden, inte en kopia. |
| `tests/css/` | Läsbarheten: kontrasten mellan nyckelfärger och att texten inte blir mindre. Ändrar någon en färg så att text blir svårare att läsa fallerar testet innan det når sajten. |
| `tests/browser/` | Riktig Chromium: ingen sidledsscroll på 18 skärmbredder, headern på en rad, hamburgarmenyn öppnas/stängs, minsta renderade textstorlek. |
| `tests/meta/` | Testar testerna: förstör koden med flit och kontrollerar att larmen går. |

Vill du veta att testerna faktiskt **slår larm**? `python3 tests/meta/mutation_check.py` förstör temat med flit på 20 olika sätt (en 24-timmarsregel som blir 12 timmar,
en färg som blir för mörk, en meny som inte går att stänga …) och kräver att rätt test fallerar varje gång. Kör det när du lagt till egna tester.

Webbläsartesterna kräver Python och Playwright en gång:

```bash
pip install -r tests/browser/requirements.txt
python3 -m playwright install chromium
sudo apt install fonts-bebas-neue      # Linux — ger testerna rätt rubriktypsnitt (annars varnar de)
```

**Det här är inte WordPress.** Testerna kör temats logik och CSS/JS i isolering. De ersätter inte att titta på sajten på
beta-adressen efter en ny version — de fångar bara de fel som annars skulle hinna dit.

## Installera i WordPress

*Utseende → Teman → Lägg till nytt → Ladda upp tema → välj zip-filen → Aktivera.*

- **Era inställningar följer med.** Vid första aktiveringen kopierar temat Customizer-inställningarna (logga, kontaktuppgifter, hero-bilder,
  menyplatser, Sliding-pausen, notisbanderollen …) från det tidigare Tändstiftet-temat. Det gamla temat rörs inte, så det går alltid att byta tillbaka.
  Befintliga inställningar i det nya temat skrivs aldrig över.
- Därefter: **Ctrl+Shift+R**, och rensa one.coms *Performance Cache* om något ser gammalt ut.
- Menylänkarna styr ni under *Utseende → Menyer → Primär navigation*.

### Uppdatera ett tema som redan är aktivt

Ladda upp den nya zip-filen på samma sätt och välj att **ersätta** det befintliga. Inställningarna ligger kvar.

## Mappnamnet är permanent

WordPress sparar alla Customizer-inställningar under temats **mappnamn** (`theme_mods_<mappnamn>`). Därför:

> `THEME_SLUG` i `scripts/config.sh` (nu `tandstiftet-theme-v2`) ska **aldrig ändras** när temat väl är i drift.

Ändras namnet ser sajten tom ut tills inställningarna flyttats över — importfunktionen i temat finns just för den engångsflytten från v1.
Vill man någon gång byta till ett snyggare namn utan "v2" går det, men då måste inställningarna flyttas på samma sätt (be om hjälp med det först).

## Vad som INTE ligger i repot

Repot innehåller **temat**. Innehållet bor i WordPress databas och följer inte med:

- sidor, inlägg, förarprofiler, tävlingar, sponsorer och deras **bilder** (mediabiblioteket),
- menyernas innehåll,
- **bokningarna** (Sliding-bokningar) och alla formulärdata,
- spärrlistan, notisbanderollens text, Customizer-värdena (de kopieras mellan teman, men versionshanteras inte).

Ta därför regelbundna säkerhetskopior av databasen och `wp-content/uploads` (t.ex. via one.coms backup eller ett backup-tillägg).
Allt som ligger i repot går att återskapa från GitHub — innehållet går inte det.

## Privat repo — och bilder på barn

Håll repot **privat**. Själva temat innehåller inga bilder, men konceptsidorna i `design/concepts/images/` har några foton, och bilder på identifierbara
barn och ungdomar kräver vårdnadshavares medgivande innan de publiceras — ett publikt repo räknas som publicering.
Just nu är det **ett** foto på en ung person: `provapa-kid-photo.jpg` (identisk med `rider-card-3.jpg`), en platshållare i Prova På- och Förare & Lag-koncepten.
Ska repot någon gång bli publikt: ta bort de två filerna först och byt ut bilden i konceptsidorna. Lägg inte heller in lösenord, `wp-config.php` eller databasdumpar
(`.gitignore` stoppar de vanligaste, men det är ditt ansvar att kolla innan en push).

## Arbetssätt

- Gör ändringar på en **gren** (`git checkout -b prova-pa-hero`) och öppna en *pull request* — då kör GitHub alla tester innan det når `main`.
- Korta, beskrivande commit-meddelanden på svenska eller engelska: *"Prova På: ny hero med foto i ram"*.
- Skriv i `CHANGELOG.md` medan du jobbar, inte i efterhand.
- Skapa en ny sida eller komponent i den nya stilen? Följ checklistan i [docs/DESIGN.md](docs/DESIGN.md).
