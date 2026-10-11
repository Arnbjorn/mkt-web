#!/usr/bin/env python3
"""
Real-browser tests (Chromium via Playwright).

  pip install -r tests/browser/requirements.txt
  python3 -m playwright install chromium
  python3 tests/browser/run.py

Set REQUIRE_FONTS=1 to FAIL (instead of warn) when the Bebas Neue font isn't
installed — the CI does this. On Ubuntu: sudo apt install fonts-bebas-neue
"""
import os, sys, harness
from playwright.sync_api import sync_playwright

GREEN, RED, YELLOW, RESET = "\033[32m", "\033[31m", "\033[33m", "\033[0m"
failures = []
def ok(m):   print(f"  {GREEN}✓{RESET} {m}")
def bad(m):  failures.append(m); print(f"  {RED}✗{RESET} {m}")
def warn(m): print(f"  {YELLOW}!{RESET} {m}")

WIDTHS = [320, 360, 375, 390, 412, 430, 480, 600, 768, 900, 1024, 1099, 1100, 1199, 1200, 1280, 1440, 1920]
ONE_ROW = 90   # px — a single header row is ~72–85 px; a wrapped header is 130+

paths = harness.build_all()
with sync_playwright() as p:
    browser = p.chromium.launch()
    def open_page(name, width, height=800):
        pg = browser.new_page(viewport={"width": width, "height": height})
        pg.goto("file://" + str(paths[name])); pg.wait_for_timeout(150)
        return pg

    # 0. fonts ------------------------------------------------------------------
    pg = open_page("home", 1280)
    have_bebas = pg.evaluate("document.fonts.check('40px \"Bebas Neue\"')")
    pg.close()
    print("Typsnitt")
    if have_bebas: ok("Bebas Neue finns (tester med riktiga rubriktypsnittet)")
    elif os.environ.get("REQUIRE_FONTS") == "1": bad("Bebas Neue saknas — installera fonts-bebas-neue")
    else: warn("Bebas Neue saknas — rubriktesterna körs med ett bredare reservtypsnitt (försiktigare, men mindre exakt)")

    # 1. no sideways scrolling, anywhere -------------------------------------------
    print("\nIngen sidledsscroll (%d sidor × %d bredder)" % (len(paths), len(WIDTHS)))
    bad_cells = []
    for name in paths:
        for w in WIDTHS:
            pg = open_page(name, w)
            sw = pg.evaluate("document.documentElement.scrollWidth")
            if sw > w + 1: bad_cells.append(f"{name} @ {w}px (scrollWidth {sw})")
            pg.close()
    (bad if bad_cells else ok)("ingen sida spränger bredden" if not bad_cells else "sidledsscroll: " + "; ".join(bad_cells))

    # 2. header stays on one row --------------------------------------------------
    print("\nHeadern ligger på en rad")
    rows_bad = []
    for name in paths:
        for w in WIDTHS:
            if name == "menu7" and w >= 1100: continue            # 7 items may wrap on desktop (by design)
            if name == "menu6" and 1100 <= w < 1200: continue     # 6 items need the compact mode at ≥1200
            pg = open_page(name, w)
            h = pg.evaluate("document.querySelector('.site-header').getBoundingClientRect().height")
            if h > ONE_ROW: rows_bad.append(f"{name} @ {w}px ({round(h)}px)")
            pg.close()
    (bad if rows_bad else ok)("headern är en rad överallt det förväntas" if not rows_bad else "header radbryts: " + "; ".join(rows_bad))
    print("    (4 och 5 menyalternativ ryms alltid, 6 från 1200 px; 7 radbryts på dator med flit utan att något spricker)")

    # 3. hamburger vs desktop menu ---------------------------------------------------
    print("\nHamburgare under 1100 px, hel meny över")
    for w, want_toggle in ((390, True), (768, True), (1099, True), (1100, False), (1280, False)):
        pg = open_page("menu4", w)
        tog = pg.evaluate("getComputedStyle(document.querySelector('.nav-toggle')).display") != "none"
        nav = pg.evaluate("getComputedStyle(document.querySelector('.primary-nav')).display") != "none"
        (ok if tog == want_toggle and nav == (not want_toggle) else bad)(f"{w}px: hamburgare {'syns' if tog else 'dold'}, meny {'syns' if nav else 'dold'}")
        pg.close()

    # 4. mobile menu behaviour ----------------------------------------------------------
    print("\nMobilmenyn")
    pg = open_page("menu4", 390)
    is_open = lambda: pg.evaluate("document.getElementById('primary-nav').classList.contains('is-open')")
    tgt = pg.evaluate("(()=>{const r=document.getElementById('nav-toggle').getBoundingClientRect();return [r.width,r.height]})()")
    (ok if min(tgt) >= 40 else bad)(f"hamburgaren är minst 40×40 px (är {round(tgt[0])}×{round(tgt[1])})")
    pg.click("#nav-toggle"); pg.wait_for_timeout(100)
    (ok if is_open() and pg.get_attribute("#nav-toggle", "aria-expanded") == "true" else bad)("öppnas av hamburgaren (och aria-expanded blir true)")
    box = pg.evaluate("(()=>{const r=document.getElementById('primary-nav').getBoundingClientRect();return [r.left,r.right]})()")
    (ok if box[0] >= 0 and box[1] <= 390 else bad)(f"panelen ryms på skärmen ({round(box[0])}–{round(box[1])} av 390px)")
    pg.click("#nav-toggle"); pg.wait_for_timeout(100)
    (ok if not is_open() else bad)("stängs av samma knapp (gamla fullskärmsmenyn gick inte att stänga)")
    pg.click("#nav-toggle"); pg.keyboard.press("Escape"); pg.wait_for_timeout(100)
    (ok if not is_open() else bad)("stängs med Esc")
    pg.click("#nav-toggle"); pg.mouse.click(200, 700); pg.wait_for_timeout(100)
    (ok if not is_open() else bad)("stängs vid tryck utanför panelen")
    pg.close()

    # 5. the hero title ---------------------------------------------------------------
    print("\nStartsidans rubrik")
    pg = open_page("home", 1280)
    if have_bebas:
        lines = pg.evaluate("""(() => { const t = document.querySelector('.hero__title'); const lh = parseFloat(getComputedStyle(t).lineHeight);
                                       return t.getBoundingClientRect().height / lh; })()""")
        (ok if 1.8 <= lines <= 2.2 else bad)(f"rubriken ligger på två rader på dator (mätte {lines:.1f} rader)")
    else:
        warn("hoppar över radantalet (kräver Bebas Neue)")
    # The real point of the width cap: the headline stays on the dark left side, clear of the photo.
    # (Two lines alone proved nothing — "Motorklubben Tändstiftet" wraps even without the cap.)
    right = pg.evaluate("document.querySelector('.hero__title').getBoundingClientRect().right")
    (ok if right <= 1280 * 0.6 else bad)(f"rubriken håller sig på den mörka vänstra sidan (högerkant {round(right)} px, högst {round(1280 * 0.6)})")
    clip = pg.evaluate("getComputedStyle(document.querySelector('.hero__bg')).clipPath")
    (ok if clip.startswith("polygon") else bad)("rivd bildkant är aktiv på dator")
    pg.close()
    pg = open_page("home", 390)
    clip = pg.evaluate("getComputedStyle(document.querySelector('.hero__bg')).clipPath")
    (ok if clip == "none" else bad)("ingen rivd kant på mobil (bilden dämpas bakom texten istället)")
    pg.close()
    pg = open_page("home-full", 1280)
    clip = pg.evaluate("getComputedStyle(document.querySelector('.hero__bg')).clipPath")
    (ok if clip == "none" else bad)("kryssrutan 'Rivd bildkant' av → ingen klippning")
    pg.close()

    # 6. the smallest text actually rendered --------------------------------------------
    print("\nMinsta renderade textstorlek")
    MIN_PX = 13.0
    checks = [("home", 1280), ("home", 390), ("menu4", 1280), ("menu4", 390)]
    if harness.HAVE_PHP: checks += [("provapa", 1280), ("provapa", 390), ("provapa-closed", 390), ("page", 1280), ("page", 390), ("sliding", 1280), ("sliding", 390), ("sliding-paused", 390)]
    for name, w in checks:
        pg = open_page(name, w)
        res = pg.evaluate("""() => {
          let min = 999, who = '';
          const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
          while (walker.nextNode()) {
            const n = walker.currentNode; if (!n.textContent.trim()) continue;
            const el = n.parentElement; if (!el || ['SCRIPT','STYLE'].includes(el.tagName)) continue;
            const cs = getComputedStyle(el); if (cs.display === 'none' || cs.visibility === 'hidden') continue;
            if (!el.getClientRects().length) continue;
            const fs = parseFloat(cs.fontSize); if (fs < min) { min = fs; who = el.className || el.tagName; }
          } return [min, who]; }""")
        (ok if res[0] >= MIN_PX else bad)(f"{name} @ {w}px: minsta text {res[0]:.1f}px ({res[1]}) — minst {MIN_PX:g}px")
        pg.close()

    # 7. Prova På (the real PHP template) ------------------------------------------------
    print("\nProva På-sidan (riktiga PHP-mallen)")
    if not harness.HAVE_PHP:
        if os.environ.get("CI") == "true": bad("php saknas — Prova På-sidan kan inte återges")
        else: warn("php saknas — hoppar över Prova På-testerna")
    else:
        rect = "(s=>{const r=document.querySelector(s);if(!r)return null;const b=r.getBoundingClientRect();return [b.left,b.right,b.top,b.bottom,b.width,b.height]})"
        pg = open_page("provapa", 1280)
        btns = pg.evaluate("[...document.querySelectorAll('.provapa-hero__actions .btn')].map(b=>Math.round(b.getBoundingClientRect().top))")
        (ok if len(btns) == 2 and btns[0] == btns[1] else bad)(f"heroknapparna ligger bredvid varandra på dator (överkanter {btns})")
        stroke = pg.evaluate(rect + "('.provapa-hero__stroke')")
        word = pg.evaluate(rect + "('.provapa-hero__line:not(.provapa-hero__line--gold)')")
        (ok if stroke and stroke[4] > 100 and stroke[2] > word[2] and stroke[3] > word[3] - 5 else bad)("penseldraget ligger under andra rubrikraden")
        pg.click(".provapa-faq__item summary"); pg.wait_for_timeout(100)
        (ok if pg.evaluate("document.querySelector('.provapa-faq__item').open") else bad)("en fråga under 'Bra att veta' fälls ut vid klick")
        n_opts = pg.evaluate("document.querySelectorAll('#pp_slot option').length")
        (ok if n_opts == 3 else bad)(f"formuläret erbjuder träningstiderna + 'Vet ej' ({n_opts} val)")
        pg.close()
        off = []
        for w in WIDTHS:
            pg = open_page("provapa", w)
            s = pg.evaluate(rect + "('.provapa-sticker')")
            if s[0] < 0 or s[1] > w: off.append(f"{w}px ({round(s[0])}–{round(s[1])})")
            pg.close()
        (ok if not off else bad)("klistermärket syns helt på alla bredder" if not off else "klistermärket klipps: " + "; ".join(off))
        pg = open_page("provapa-nophoto", 390)
        (ok if pg.evaluate("!document.querySelector('.provapa-polaroid') && !!document.querySelector('.provapa-hero__visual--no-photo .provapa-sticker')") else bad)(
            "utan Utvald bild: ingen tom fotoram, bara klistermärket")
        pg.close()
        pg = open_page("provapa-closed", 1280)
        state = pg.evaluate("""({form: !!document.querySelector('form.provapa-form'), closed: !!document.querySelector('.provapa-closed'),
                                btn2: !!document.querySelector('.provapa-hero__actions a[href="#boka"]'), adult: !!document.querySelector('.provapa-adult')})""")
        (ok if state == {"form": False, "closed": True, "btn2": False, "adult": False} else bad)(
            f"säsong stängd: meddelande visas, formulär och 'hör av dig'-knapp döljs; Sliding pausad: ingen vuxenhänvisning ({state})")
        pg.close()

    # 8. Sliding (the real PHP template, with a made-up calendar) ------------------------
    print("\nSliding-sidan (riktiga PHP-mallen)")
    if harness.HAVE_PHP:
        pg = open_page("sliding", 390)
        cal = pg.evaluate("""(()=>({ days: document.querySelectorAll('#pp-cal-grid .pp-calendar__day:not(.is-empty)').length,
            bookable: document.querySelectorAll('#pp-cal-grid button.pp-calendar__day').length,
            tags: [...document.querySelectorAll('.pp-calendar__day-tag')].map(t=>t.textContent),
            tagPx: Math.min(...[...document.querySelectorAll('.pp-calendar__day-tag')].map(t=>parseFloat(getComputedStyle(t).fontSize)), 99) }))()""")
        (ok if cal["days"] >= 28 and cal["bookable"] >= 1 else bad)(f"kalendern ritas upp ({cal['days']} dagar, {cal['bookable']} bokningsbara knappar)")
        (ok if "Fullt" in cal["tags"] and "Ring" in cal["tags"] else bad)(f"fulla dagar och dagar inom 24 h märks ({cal['tags']})")
        (ok if cal["tagPx"] >= 14 else bad)(f"dag-etiketterna är läsbara på mobil ({cal['tagPx']:.1f} px, minst 14)")
        dialogs = []
        pg.on("dialog", lambda d: (dialogs.append(d.message), d.dismiss()))
        pg.fill("#pp_name", "Test"); pg.fill("#pp_email", "test@example.com")
        pg.click("#pp_submit_btn"); pg.wait_for_timeout(200)
        shown = pg.evaluate("!document.getElementById('pp-date-missing').hidden")
        (ok if shown and not dialogs else bad)("skicka utan datum → tydligt meddelande på sidan (ingen popup)")
        pg.click("#pp-cal-grid button.pp-calendar__day"); pg.wait_for_timeout(100)
        st = pg.evaluate("({d: document.getElementById('pp_date_input').value, shown: document.getElementById('pp_date_display').value, err: document.getElementById('pp-date-missing').hidden})")
        (ok if st["d"] and st["shown"] and st["err"] else bad)(f"klick på ledig dag väljer datumet ({st['shown'] or 'inget'})")
        if pg.query_selector(".pp-discipline-tab[data-discipline=flattrack]"):
            pg.click(".pp-discipline-tab[data-discipline=flattrack]"); pg.wait_for_timeout(100)
            st2 = pg.evaluate("({d: document.getElementById('pp_date_input').value, disc: document.getElementById('pp_discipline_input').value})")
            (ok if st2 == {"d": "", "disc": "flattrack"} else bad)(f"byte till Flat track nollställer valt datum ({st2})")
        pg.close()
        pg = open_page("sliding-noft", 1280)
        st = pg.evaluate("""({tabs: !!document.querySelector('.pp-discipline-tabs'), partner: !!document.querySelector('.sliding-partner'),
            ftcard: [...document.querySelectorAll('.sliding-disc__name')].map(e=>e.textContent),
            facts: [...document.querySelectorAll('.provapa-fact__num')].map(e=>e.textContent),
            text: document.body.innerText.toLowerCase()})""")
        (ok if not st["tabs"] and not st["partner"] and st["ftcard"] == ["Speedway"] else bad)(f"Flat track avstängt: inga flikar, ingen samarbetsruta, bara Speedway-kortet ({st['ftcard']})")
        (ok if "Speedway" in st["facts"] and "2" not in st["facts"] else bad)(f"Flat track avstängt: faktaremsan säger Speedway ({st['facts']})")
        (ok if "flat track eller speedway" not in st["text"] else bad)("Flat track avstängt: texterna nämner inte flat track som val")
        pg.close()
        pg = open_page("sliding-paused", 1280)
        st = pg.evaluate("({form: !!document.querySelector('#pp-sliding-form'), cal: !!document.querySelector('#pp-calendar'), paused: !!document.querySelector('.sliding-paused')})")
        (ok if st == {"form": False, "cal": False, "paused": True} else bad)(f"Sliding pausat: pausmeddelande, ingen kalender och inget formulär ({st})")
        pg.close()

    # 9. Signup closed by the season, and parts switched off ---------------------------------
    print("\nAnmälan och säsong, dolda delar")
    if harness.HAVE_PHP:
        months = ["januari","februari","mars","april","maj","juni","juli","augusti","september","oktober","november","december"]
        r = harness.off_season_reopens(); when = f"{r.day} {months[r.month - 1]} {r.year}"
        pg = open_page("provapa-offseason", 1280)
        st = pg.evaluate("({form: !!document.querySelector('form.provapa-form'), text: document.body.innerText})")
        (ok if not st["form"] and when in st["text"] else bad)(f"Prova På utanför säsongen: inget formulär, och sidan säger när det öppnar ({when})")
        pg.close()
        pg = open_page("sliding-offseason", 1280)
        st = pg.evaluate("""({form: !!document.querySelector('#pp-sliding-form'), cal: !!document.querySelector('#pp-calendar'),
            boka: !!document.querySelector('.hero__actions a[href="#boka"]'), text: document.body.innerText})""")
        (ok if not st["form"] and not st["cal"] and not st["boka"] and when in st["text"] else bad)(
            f"Sliding utanför säsongen: ingen kalender, inget formulär, ingen Boka-knapp, och sidan säger när bokningen öppnar ({when})")
        pg.close()
        pg = open_page("sliding-hidden", 1280)
        st = pg.evaluate("({price: !!document.querySelector('.sliding-price'), groups: !!document.querySelector('.sliding-groups'), intro: !!document.querySelector('.sliding-intro'), faq: !!document.querySelector('.provapa-faq'), disc: !!document.querySelector('.sliding-disc'), form: !!document.querySelector('#pp-sliding-form')})")
        (ok if st == {"price": False, "groups": False, "intro": False, "faq": False, "disc": True, "form": True} else bad)(f"dolda delar försvinner, resten finns kvar ({st})")
        pg.close()

    # 8b. A normal page (page.php) --------------------------------------------------------
    print("\nVanlig sida (riktiga page.php)")
    if harness.HAVE_PHP:
        pg = open_page("page", 1280)
        st = pg.evaluate("""(()=>{const c=document.querySelector('.page-read__content'), m=document.querySelector('main.page-read');
            return {sidebar: !!document.querySelector('.sidebar'), width: Math.round(c.getBoundingClientRect().width),
                    bg: getComputedStyle(m).backgroundColor, text: getComputedStyle(c.querySelector('p')).color}})()""")
        (ok if not st["sidebar"] else bad)("ingen bloggsidopanel")
        (ok if st["width"] <= 820 else bad)(f"texten ligger i en smal, lättläst spalt ({st['width']} px, högst 820)")
        (ok if st["bg"] == "rgb(242, 236, 225)" and st["text"] != "rgb(216, 207, 188)" else bad)(f"beige läsyta med mörk text (bakgrund {st['bg']}, text {st['text']})")
        pg.close()
        pg = open_page("page-photo", 390)
        (ok if pg.evaluate("!!document.querySelector('.page-read__image img')") else bad)("utvald bild visas överst när sidan har en")
        pg.close()

    browser.close()

print()
if failures:
    print(f"{RED}{len(failures)} webbläsartest misslyckades.{RESET}"); sys.exit(1)
print("Alla webbläsartester OK."); sys.exit(0)
