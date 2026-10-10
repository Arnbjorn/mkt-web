# Komma igång: få upp repot på GitHub

Du behöver inte kunna programmera. Du behöver ett GitHub-konto och antingen **GitHub Desktop** (ett vanligt program med knappar)
eller kommandoraden. Det går bra att välja det som känns enklast.

## 1. Skapa ett tomt, privat repo på GitHub

1. Logga in på github.com → **New repository**.
2. Namn, t.ex. `tandstiftet`. Välj **Private**.
3. Bocka **inte** i "Add a README", "Add .gitignore" eller "Choose a license" — repot ska vara tomt, filerna finns redan.
4. **Create repository.**

## 2. Packa upp mappen och lägg upp den

Packa upp `tandstiftet-repo.zip` någonstans, t.ex. `Dokument/tandstiftet`.

### Alternativ A — GitHub Desktop (utan kommandorad)

1. Installera [GitHub Desktop](https://desktop.github.com/) och logga in.
2. **File → Add local repository →** välj mappen `tandstiftet`. Säger den "This directory does not appear to be a Git repository" → klicka **create a repository**.
3. Skriv ett commit-meddelande, t.ex. *Första versionen: tema 2.0.0, design, tester*, och klicka **Commit to main**.
4. Klicka **Publish repository** (eller **Push origin**) — kryssa i **Keep this code private** och koppla till repot du skapade.

### Alternativ B — kommandoraden

```bash
cd tandstiftet
git init -b main
git add -A
git commit -m "Första versionen: tema 2.0.0, design, tester"
git remote add origin https://github.com/DITT-ANVÄNDARNAMN/tandstiftet.git
git push -u origin main
```

## 3. Kolla att automatiken kör

På GitHub → fliken **Actions**. Du ska se en körning som heter **CI** med tre steg:
*Kontroller och PHP/CSS-tester*, *Webbläsartester* och *Bygg tema-paketet*. Efter några minuter ska alla vara gröna (✓).

- **Rött (✗)?** Klicka på körningen → klicka på det röda steget → läs felmeddelandet (det är på svenska och säger vad som är fel).
- När *Bygg tema-paketet* är klart finns zip-filen under **Artifacts** längst ner på körningens sida.

## 4. Skapa din första "riktiga" release

Det är releaser du ska ladda ner och installera ifrån.

1. På GitHub: **Releases → Draft a new release.**
2. **Choose a tag** → skriv `v2.0.0` → **Create new tag.** (Taggen måste vara `v` + versionen i `style.css`.)
3. Rubrik valfri. **Publish release.**
4. Efter någon minut visas `tandstiftet-theme-v2-2.0.0.zip` under **Assets** på releasen. Det är filen du laddar upp i WordPress.

## Vanliga frågor

**Måste jag köra skripten på min dator?** Nej. GitHub kör allt åt dig. Skripten finns för den som vill bygga lokalt.

**Jag använder Windows — fungerar `bash scripts/...`?** Via *Git Bash* (följer med Git) eller WSL, ja. Annars: låt GitHub bygga (steg 4).

**Hur ändrar jag något?** Ändra filen (på datorn eller direkt på GitHub), skriv ett meddelande, commit. Börja gärna på en gren och öppna en *pull request* — då kör GitHub testerna innan ändringen når `main`.

**Hur backar jag?** Varje commit finns kvar. På GitHub: öppna filen → **History** → välj en äldre version. Eller installera en äldre release-zip i WordPress.

**Får jag lägga in lösenord / wp-config?** Nej. Aldrig. Inte heller databasdumpar eller kunddata.

**Det står "main" men min har "master".** Det spelar ingen roll, men byt gärna namn till `main` så att automatiken (som lyssnar på `main`) körs.
