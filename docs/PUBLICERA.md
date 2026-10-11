# Publicera temat till sajten med en knapp

> **Just nu går knappen bara till testsajten (test.tändstiftet.se).** Livesajten kör det gamla temat och ska inte
> röras förrän arbetet på testsajten är klart och godkänt. Valet *live* är därför borttaget ur knappen.
> När det är dags: lägg tillbaka `live` i `.github/workflows/deploy.yml` (`options: [beta, live]`), skapa miljön
> `live` med egna Secrets och **Required reviewers**, och publicera från `master`.

GitHub kan bygga temat och lägga upp det på one.com åt er — ingen zip-fil att ladda upp för hand.

**Så går det till:** GitHub → fliken **Actions** → **Publicera tema** → **Run workflow** → *beta* (testsajten) → **Run workflow**.

1. Alla tester körs först. Är något rött publiceras **ingenting**.
2. Temat laddas upp till en tillfällig mapp (`tandstiftet-theme-v2.new`) — sajten använder den gamla versionen under tiden.
3. Mapparna byter plats på en sekund. Förra versionen sparas som `tandstiftet-theme-v2.old`.
4. Sajten kontrolleras. Svarar den inte, eller visar ett PHP-fel, **backar det automatiskt** till förra versionen.
5. Rensa one.com-cachen i wp-admin om sidan ser gammal ut (Performance Cache), och tryck Ctrl+Shift+R.

Mappnamnet är detsamma som förut, så alla inställningar i *Anpassa* finns kvar.
**Live** kan bara publiceras från grenen `master` — det som ligger på en sidogren kan bara gå till beta.

---

## Engångsinställning (ca 15 minuter)

Inloggningen till one.com sparas krypterat i GitHub ("Secrets"). Den syns aldrig i repot eller i loggarna, och
**ni skriver in den själva** — Claude ska aldrig få lösenordet.

### 1. Hämta uppgifterna hos one.com

Logga in på one.com → kontrollpanelen → leta efter **SSH & SFTP** (eller "SFTP-åtkomst"). Där står:

- **Server/värd** (t.ex. `ssh.xn--tndstiftet-q5a.se` — skriv av exakt det som står där)
- **Användarnamn**
- **Port** (oftast 22)
- **Lösenord** — samma som visas/sätts där

Ta reda på **sökvägen till temamappen**. Enklast: anslut med ett SFTP-program (t.ex. FileZilla eller WinSCP) med
uppgifterna ovan och leta upp mappen `wp-content/themes` för **betasajten**. Skriv av hela sökvägen som programmet visar,
t.ex. `/customers/…/test.xn--tndstiftet-q5a.se/httpd.www/wp-content/themes`. Den ska sluta på `/wp-content/themes`.
Testsajten och riktiga sajten har olika mappar.

> **"beta" i knappen = testsajten**, som just nu är **test.tändstiftet.se** (beta.tändstiftet.se fungerar inte för tillfället).
> Vilken sajt det blir bestäms helt av Secrets i miljön `beta` — byter ni testsajt senare ändrar ni bara `THEMES_DIR` och `SITE_URL` där.

### 2. Skapa miljön i GitHub

GitHub → repot → **Settings** → **Environments** → **New environment** → `beta`.

(Miljön `live` skapas först när livesajten ska bytas — se rutan överst.)

### 3. Lägg in Secrets i varje miljö

Klicka på miljön → **Add environment secret**. Namnen måste stavas exakt så här:

| Namn | Värde | Krävs? |
|---|---|---|
| `SFTP_HOST` | servern från steg 1 | ja |
| `SFTP_USER` | användarnamnet | ja |
| `SFTP_PASSWORD` | lösenordet | ja (eller `SFTP_KEY`) |
| `THEMES_DIR` | sökvägen som slutar på `/wp-content/themes` | ja |
| `SITE_URL` | `https://test.xn--tndstiftet-q5a.se/` (för live: riktiga adressen) | rekommenderas — utan den görs ingen kontroll/automatisk backning |
| `SFTP_PORT` | bara om det inte är 22 | nej |
| `SFTP_KEY` | en SSH-nyckel i stället för lösenord (om one.com stöder det) | nej |
| `SFTP_KNOWN_HOSTS` | serverns fingeravtryck (se nedan) | rekommenderas |

**Fingeravtrycket** (`SFTP_KNOWN_HOSTS`) gör att GitHub vägrar skicka lösenordet till en server som utger sig för att vara one.com.
Kör i en terminal (byt ut servern) och klistra in hela utskriften som värde:

```bash
ssh-keyscan -p 22 ssh.xn--tndstiftet-q5a.se
```

Utan det hämtas fingeravtrycket automatiskt vid varje körning (fungerar, men skyddar inte mot en falsk server).

### Vem kan se lösenordet?

Ingen. Ett Secret krypteras när det sparas och kan efteråt bara bytas ut eller tas bort — aldrig visas, inte ens för
ägaren. Det finns inte i koden eller historiken, visas som `***` i loggarna och ges aldrig till kopior (forks) av repot.

Det enda att tänka på: den som har **skrivrätt** till repot kan i teorin skriva ett eget arbetsflöde som *använder*
lösenordet. Därför:

1. Ge bara **skrivrätt** till personer ni litar på — andra får läsrätt.
2. **Lås `live` till `master`:** *Settings → Environments → live → Deployment branches → Selected branches* → `master`.
   (Gör samma sak för `beta` om ni inte behöver prova sidogrenar på testsajten.)
3. **Required reviewers** på `live`, så att inget går ut på riktiga sajten utan godkännande.
4. **Byt lösenordet** hos one.com (och i GitHub) om någon med skrivrätt lämnar.

### 4. Prova anslutningen — ändrar ingenting

Actions → **Publicera tema** → **Run workflow** → *Vart?* `beta`, *Vad?* `testa-anslutning`.
Grönt = inloggningen och sökvägen stämmer, och loggen visar vilka teman som ligger i mappen.

### 5. Första riktiga publiceringen

Samma sak med *Vad?* `publicera`. Titta sedan på betasajten.

---

## Om något går fel

| Meddelande | Betyder |
|---|---|
| `… saknas (lägg in den som GitHub Secret)` | ett Secret saknas eller är felstavat — och ligger det i rätt miljö (`beta`/`live`)? |
| `kom inte in i …` | fel användare, lösenord, server eller sökväg |
| `THEMES_DIR måste sluta på /wp-content/themes` | sökvägen pekar fel — skyddar mot att något annat skrivs över |
| `sajten svarar inte som den ska … backade` | nya versionen gav fel på sajten; förra versionen är tillbaka. Den trasiga ligger i `….new` |
| `Till live publiceras bara från grenen master` | slå ihop ändringarna med master först |

**Backa för hand:** anslut med SFTP, byt namn på `tandstiftet-theme-v2` till något annat och `tandstiftet-theme-v2.old` till `tandstiftet-theme-v2`.

**Från datorn** går samma skript att köra (`bash scripts/deploy.sh check`) med uppgifterna som miljövariabler och `lftp`
installerat — men knappen i GitHub är enklast och kör alltid testerna först.
