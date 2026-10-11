# Publicera temat till sajten med en knapp

GitHub kan bygga temat och lägga upp det på one.com åt er — ingen zip-fil att ladda upp för hand.

**Så går det till:** GitHub → fliken **Actions** → **Publicera tema** → **Run workflow** → välj *beta* eller *live* → **Run workflow**.

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
t.ex. `/customers/…/beta.xn--tndstiftet-q5a.se/httpd.www/wp-content/themes`. Den ska sluta på `/wp-content/themes`.
Betasajten och riktiga sajten har olika mappar.

### 2. Skapa två miljöer i GitHub

GitHub → repot → **Settings** → **Environments** → **New environment**:

- `beta`
- `live` — bocka gärna i **Required reviewers** och välj dig själv. Då måste någon godkänna varje publicering till riktiga sajten.

### 3. Lägg in Secrets i varje miljö

Klicka på miljön → **Add environment secret**. Namnen måste stavas exakt så här:

| Namn | Värde | Krävs? |
|---|---|---|
| `SFTP_HOST` | servern från steg 1 | ja |
| `SFTP_USER` | användarnamnet | ja |
| `SFTP_PASSWORD` | lösenordet | ja (eller `SFTP_KEY`) |
| `THEMES_DIR` | sökvägen som slutar på `/wp-content/themes` | ja |
| `SITE_URL` | `https://beta.xn--tndstiftet-q5a.se/` (för live: riktiga adressen) | rekommenderas — utan den görs ingen kontroll/automatisk backning |
| `SFTP_PORT` | bara om det inte är 22 | nej |
| `SFTP_KEY` | en SSH-nyckel i stället för lösenord (om one.com stöder det) | nej |
| `SFTP_KNOWN_HOSTS` | serverns fingeravtryck (se nedan) | rekommenderas |

**Fingeravtrycket** (`SFTP_KNOWN_HOSTS`) gör att GitHub vägrar skicka lösenordet till en server som utger sig för att vara one.com.
Kör i en terminal (byt ut servern) och klistra in hela utskriften som värde:

```bash
ssh-keyscan -p 22 ssh.xn--tndstiftet-q5a.se
```

Utan det hämtas fingeravtrycket automatiskt vid varje körning (fungerar, men skyddar inte mot en falsk server).

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
