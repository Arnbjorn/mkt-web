# Designguide — Tändstiftet v2

Utseendet är en varm svart bas med guld och rost/orange, grädde och beige där man läser, och en lugn, kornig textur över allt.
Konceptsidorna i `design/concepts/` är den godkända referensen. Det här dokumentet samlar reglerna bakom dem.

## Färger

Variabelnamnen kommer från v1 (t.ex. `--color-navy`) men **värdena** är nya — därför bytte alla befintliga komponenter utseende utan att skrivas om.
Definieras i `:root` överst i `style.css`.

| Variabel | Värde | Används till |
|---|---|---|
| `--color-black` | `#14151a` | sidans bakgrund, footer |
| `--color-dark` | `#14151a` | sektionsbakgrund |
| `--color-coal` | `#1c1a16` | växlande sektion (varm) |
| `--color-asphalt` | `#221e18` | kortytor |
| `--color-mid` / `--color-dust` | `#3a2f22` | kanter, avdelare, varm övergång |
| `--color-gold` / `--color-gold-light` | `#d4a017` / `#e8b830` | accent, knappar, siffror |
| `--color-orange` | `#e0842c` | kickers/etiketter på mörkt, hemma-markering |
| `--color-rust` | `#6b4226` | etiketter och länkar på beige |
| `--color-cream` | `#f2ece1` | brödtext på mörkt |
| `--color-grey-light` / `--color-grey-mid` | `#bdb7a9` / `#9d9789` | dämpad text (8–9:1 resp. 6:1 på mörkt) |
| `--color-beige` / `--color-beige-deep` | `#f2ece1` / `#e6dcc8` | läsytor och kort på dem |
| `--color-ink` / `--color-ink-soft` | `#241f18` / `#4a4033` | text på beige |

Kontrasten för nyckelparen vaktas av `tests/css/run.py`.

## Typografi

- **Rubriker:** Bebas Neue (versaler). **Etiketter/knappar:** Barlow Condensed 600–700. **Brödtext:** Barlow.
- **Bastextstorlek 18 px** på dator/surfplatta (`html { font-size: 112.5% }`) och **17 px** på mobil (≤480 px). Allt är i `rem` och skalar med.
  Procent (inte px) gör att besökare som höjt webbläsarens egen textstorlek får det respekterat.
- Målet är ingen text under ca 14 px. **Två kända undantag** (testet tillåter exakt två och får bara minska; kalenderetiketterna rättades i 2.1.0): kalenderns dag-etiketter
  texten i hero-märket `.hero__badge-text` (≈12 px) och headerknappen på mobil `.site-header__cta` (≈13 px).
  Tunn text (vikt 300) undviks — den är svår att läsa för många.
- Brett bokstavsavstånd (>0,2em) undviks på små etiketter.
- Läsare: många är äldre och har sämre syn. När du är osäker — gör texten större och kontrasten starkare, inte tvärtom.

## Layout och brytpunkter

| Bredd | Vad som händer |
|---|---|
| ≤ 480 px | mobil: root 17 px, tätare sidmarginaler |
| ≤ 900 px | hero: ingen rivd bildkant, bilden dämpas bakom texten |
| ≤ 1099 px | **hamburgermeny** (dropdown under headern) |
| 1100–1199 px | kompakt header (taglinen döljs) |
| ≥ 1200 px | full header; innehållet är högst 1200 px brett (`--container`), smala texter 800 px |

Menyn kompakteras automatiskt om den inte ryms på en rad (`main.js`, `fitHeader`). Header, innehåll och footer delar samma 1200 px-kolumn.

## Principer

1. **Beige där man läser.** Längre text (nyheter, regler, schema, formulär) ligger på beige med mörk text. Hero, remsor och navigation är mörka.
2. **Tyst footer.** Klubbinfo och styrelse finns där, men tar ingen uppmärksamhet från den snabba, inbjudande startupplevelsen.
3. **Tävlingar nås via "Nästa match"-remsan**, inte huvudmenyn — om inte kryssrutan *Visa Tävlingar i huvudmenyn* är på.
4. **Inga biljettköp online.** Biljetter säljs på plats. Det finns inga "Köp biljett"-knappar och inget fält för det.
5. **Allt som kan ändras av styrelsen ligger i Anpassa** (Customizer) — inte hårdkodat i mallarna. Nya texter = nya Customizer-fält.
6. **Av/på-växlar för det som kan pausas:** Sliding, Flat track, Prova På-säsongen, notisbanderollen, sektioner på startsidan.

## Komponenter och var de bor i `style.css`

| Block (sök efter rubriken) | Innehåll |
|---|---|
| `v2 FOUNDATION` | header, navigation, mobilmeny, footer, fokus/markering, återställda breddtak |
| `v2 — STARTSIDA` | hero, statistikrad, nästa-tävling-remsa, `.section--beige`, sponsorremsa |
| `v2 — SLIDING` | `sliding-*`: pris, grupper, discipliner, samarbetsruta, kalender på beige; hero = startsidans `.hero` + `.hero--page` |
| `v2 — VANLIG SIDA` | `page-read`: Standardmallen — beige läsyta, smal spalt, typografi för editorinnehåll (rubriker, listor, citat, knappblock) |
| `v2 — PROVA PÅ` | `provapa-*`: hero D (polaroid, klistermärke, penseldrag), faktaremsa, tidskort, steg, fällbara frågor, formulär på beige. Eget prefix — de äldre `pp-*` delas med Sliding |
| Övriga block ovanför | v1:s komponenter (kort, formulär, Prova På, Sliding, kalender …) — omfärgade via variablerna |

Senare regler vinner över tidigare (samma specificitet). Därför ligger v2-blocken **sist** med flit.

## Checklista för en ny sida i den nya stilen

1. Utgå från närmaste konceptsida i `design/concepts/`.
2. Använd befintliga variabler och komponenter. Nya färgvärden? Lägg dem som variabel i `:root`, inte som hex i en regel.
3. Läsyta → `section--beige`. Mörk yta → `section`, `section--dark` eller `section--coal`.
4. Texten som kan ändras: Customizer-fält med vettigt standardvärde.
5. Kör `bash scripts/test.sh` — kontrast, textstorlek och bredder ska vara gröna.
6. Titta på sidan på mobil **och** dator, med riktiga typsnitt.
7. Skriv det i `CHANGELOG.md`.

## Bekanta fällor

- **Gamla `max-width: 100%`-regeln** (i "GLOBAL MOBILFIX") upphävde containerbredderna. v2 återställer dem; ta inte bort `Restore the width caps` i slutet av `style.css`.
- **Scroll-animationen** (`main.js`) sätter korten (`.news-card`, `.rider-card`, `.event-card`) till opacitet 0 tills man skrollar förbi dem. En skärmdump utan skrollning ser därför tom ut.
- Kalenderetiketterna ("Fullt", "Ring") är 0,85 rem sedan 2.1.0.
