"""
Builds static test pages that reproduce the HTML header.php / footer.php /
front-page.php output, wired to the theme's REAL style.css and main.js.
Page templates that are easy to render outside WordPress (Prova På) are run
through PHP for real — see render_page.php.

WordPress itself isn't needed: what matters for these tests is layout and
behaviour, and that comes from the CSS/JS. If you change the PHP markup of the
header, footer or front page in a way that adds/renames elements, update the
templates below to match.
"""
import re, pathlib, shutil, subprocess, html as _html

ROOT = pathlib.Path(__file__).resolve().parents[2]
SLUG = re.search(r'^THEME_SLUG="([^"]+)"', (ROOT / "scripts/config.sh").read_text(), re.M).group(1)
THEME = ROOT / "theme" / SLUG
IMG = ROOT / "design" / "concepts" / "images"
OUT = pathlib.Path(__file__).resolve().parent / ".out"

BASE_ITEMS = [("Start", "/"), ("Förare & Lag", "/forare-och-lag/"), ("Prova På", "/prova-pa/"), ("Sliding", "/sliding/")]
EXTRA_ITEMS = [("Tävlingar", "/tavlingar/"), ("Klubbinfo", "/klubbinfo/"), ("Regler & Säkerhet", "/regler/")]

def e(s): return _html.escape(s, quote=True)
def url(p): return "file://" + str(p)

def menu(items, current="Start", css="menu"):
    li = "".join(f'<li class="menu-item{" current-menu-item" if t == current else ""}"><a href="{u}">{e(t)}</a></li>' for t, u in items)
    return f'<ul class="{css}">{li}</ul>'

def header(n_items=4, current="Start"):
    items = BASE_ITEMS + EXTRA_ITEMS[: max(0, n_items - 4)]
    return f'''<header class="site-header" role="banner"><div class="site-header__inner">
<a href="/" class="site-branding" aria-label="Motorklubben Tändstiftet"><img class="site-branding__logo" src="{url(IMG / "mkt-logo.png")}" alt="">
<span class="site-branding__text"><span class="site-branding__name">Tändstiftet</span><span class="site-branding__tagline">Malmö · Sedan 1924</span></span></a>
<nav class="primary-nav" id="primary-nav" aria-label="Primär navigation">{menu(items, current)}</nav>
<a href="/prova-pa/" class="site-header__cta">Prova på →</a>
<button class="nav-toggle" id="nav-toggle" aria-label="Öppna meny" aria-expanded="false" aria-controls="primary-nav"><span></span><span></span><span></span></button>
</div></header>'''

def footer():
    return f'''<footer class="site-footer" role="contentinfo"><div class="site-footer__main">
<div class="footer-brand"><a class="custom-logo-link" href="/"><img class="custom-logo" src="{url(IMG / "mkt-logo.png")}" alt="Motorklubben Tändstiftet"></a>
<p class="footer-brand__desc">Speedway &amp; flat track i Malmö sedan 1924. Svemo-ansluten klubb för förare i alla åldrar.</p>
<div class="footer-social"><a href="#" class="footer-social__link" aria-label="Facebook"><svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a></div></div>
<div class="footer-nav-col"><h3 class="footer-nav-col__title">Om klubben</h3><div>{menu([("Klubbinfo","#"),("Styrelsen","#"),("Bli medlem","#")], css="footer-nav-col__list")}</div></div>
<div class="footer-nav-col"><h3 class="footer-nav-col__title">Trackracing</h3><div>{menu([("Speedway","#"),("Flat track","#")], css="footer-nav-col__list")}</div></div>
<div class="footer-nav-col"><h3 class="footer-nav-col__title">Kontakt</h3><ul class="footer-nav-col__list footer-contact-list"><li><span>Malmö Motorstadion</span></li><li><a href="#">040-XX XX XX</a></li><li><a href="#">info@tandstiftet.se</a></li></ul></div>
</div><div class="site-footer__bottom"><div class="site-footer__bottom-inner"><span class="site-footer__copy">© 2026 Motorklubben Tändstiftet. Alla rättigheter förbehållna. · Svemo-ansluten sedan 1924</span><div class="footer-bottom-links"><a href="#">Svemo</a><a href="#">Stöd via Bingolotto</a></div></div></div></footer>'''

def wrap(body, title="test"):
    return f'''<!DOCTYPE html><html lang="sv"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{title}</title>
<link rel="stylesheet" href="{url(THEME / "style.css")}"></head><body class="page">{body}<script src="{url(THEME / "assets/js/main.js")}"></script></body></html>'''

def plain_page(n_items):
    main = '''<main style="min-height:420px"><section class="section"><div class="container"><span class="label">Testsida</span><h1 style="margin-top:.6rem">Förare &amp; Lag</h1>
<p style="max-width:60ch;margin-top:1rem">Innehållsyta för att se headern i sitt sammanhang.</p>
<p style="margin-top:2rem"><a class="btn btn--primary" href="#">Primär knapp</a> <a class="btn btn--outline" href="#">Outline</a></p></div></section></main>'''
    return wrap(header(n_items) + main + footer(), f"{n_items} menyalternativ")

def home_page(torn=True, slides=False):
    cls = "hero" + (" hero--slideshow" if slides else "") + (" hero--torn" if torn else "")
    img1, img2 = url(IMG / "action-1.jpg"), url(IMG / "rider-card-1.jpg")
    if slides:
        bg = (f'<div class="hero__slides" aria-hidden="true"><div class="hero__slide is-active" style="background-image:url(\'{img1}\')"></div>'
              f'<div class="hero__slide" style="background-image:url(\'{img2}\')"></div></div><div class="hero__dots"><button class="hero__dot is-active"></button><button class="hero__dot"></button></div>')
    else:
        bg = f'<div class="hero__bg" style="background-image:url(\'{img1}\')" aria-hidden="true"></div>'
    news = "".join(f'''<article class="news-card"><div class="news-card__image"><img src="{url(IMG / i)}" alt=""></div><div class="news-card__body"><div class="news-card__meta"><span class="news-card__date">{d}</span></div>
<h3 class="news-card__title"><a href="#">{t}</a></h3><p class="news-card__excerpt">Kort sammanfattning av nyheten så att man hinner se vad den handlar om innan man klickar vidare.</p><a href="#" class="news-card__link">Läs mer</a></div></article>'''
        for i, d, t in (("action-1.jpg", "12 sep 2026", "Säsongsavslutning på banan"), ("rider-card-1.jpg", "28 aug 2026", "Nya ungdomsförare tog steget"), ("rider-card-2.jpg", "3 aug 2026", "Tack för en fantastisk match")))
    spons = "".join(f'<a class="sponsor-item" href="#"><span style="font-family:var(--font-condensed);font-weight:700;font-size:0.9rem;color:#666">{n}</span></a>' for n in ("Malmö Bil", "Skånska El", "Verkstan AB", "Café Gnistan"))
    body = f'''{header(4)}
<section class="{cls}">{bg}<div class="hero__overlay"></div><img class="hero__watermark" src="{url(IMG / "mkt-logo.png")}" alt="" aria-hidden="true">
<div class="hero__content"><p class="hero__kicker">Speedway &amp; Flattrack · Malmö</p><h1 class="hero__title">Motorklubben <span>Tändstiftet</span></h1><p class="hero__subtitle">Vi kör dirt track med hjärta. Välkommen till klubben.</p>
<div class="hero__actions"><a href="#" class="btn btn--primary">Prova På — gratis →</a><a href="#" class="btn btn--outline">Se kommande tävlingar</a></div></div><div class="hero__diagonal"></div></section>
<div class="stats-bar"><div class="stats-bar__inner"><div class="stat-item"><div class="stat-item__number">1924</div><div class="stat-item__label">Grundat</div></div><div class="stat-item"><div class="stat-item__number">Div 1</div><div class="stat-item__label">Seriespel</div></div><div class="stat-item"><div class="stat-item__number">20+</div><div class="stat-item__label">Aktiva förare</div></div><div class="stat-item"><div class="stat-item__number">Malmö</div><div class="stat-item__label">Hemmatrack</div></div></div></div>
<section class="next-event-banner"><div class="container"><div class="next-event-banner__inner"><div class="next-event-banner__left"><div class="label" style="margin-bottom:0.4rem">Nästa tävling</div><div class="next-event-banner__date"><span class="next-event-banner__day">13</span><span class="next-event-banner__month">September 2026</span></div><h2 class="next-event-banner__title">Tändstiftet — Vikingarna</h2><p class="next-event-banner__meta">Malmö Motorstadion · 19:00</p></div><div class="next-event-banner__right"><a href="#" class="btn btn--primary">Matchinfo →</a><span class="next-event-banner__price">180 kr · Biljetter säljs på plats</span></div></div></div></section>
<section class="section section--beige" id="nyheter"><div class="container"><div class="section-header"><div class="label" style="margin-bottom:0.4rem">Senaste nytt</div><h2 class="section-title">Nyheter</h2></div><div class="grid grid--3">{news}</div><div style="text-align:center;margin-top:var(--space-xl)"><a href="#" class="btn btn--outline">Alla nyheter</a></div></div></section>
<section class="sponsors-section" id="sponsorer"><div class="container"><div class="section-header"><div class="label" style="margin-bottom:0.4rem">Tack till</div><h2 class="section-title">Våra sponsorer</h2></div><div class="sponsors-grid">{spons}</div></div></section>
{footer()}'''
    return wrap(body, "startsida")

# ── Pages rendered from the REAL PHP template (tests/browser/render_page.php) ──
# These need php. Without it they are skipped (run.py says so) — the CI has php.
HAVE_PHP = shutil.which("php") is not None

def php_page(template, photo, *mods, title="sida", current="Start"):
    out = subprocess.run(["php", str(pathlib.Path(__file__).with_name("render_page.php")), template, photo, *mods],
                         capture_output=True, text=True, encoding="utf-8", check=True).stdout
    if "Fatal error" in out or "Warning:" in out or "Deprecated:" in out:
        raise RuntimeError(f"PHP-fel när {template} återgavs:\n{out[:2000]}")
    return wrap(header(4, current) + f'<main id="main">{out}</main>' + footer(), title)

def provapa_page(photo=True, *mods):
    return php_page("templates/page-prova-pa.php", url(IMG / "provapa-kid-photo.jpg") if photo else "", *mods,
                    title="Prova På", current="Prova På")

PAGES = {
    "menu4":      lambda: plain_page(4),
    "menu5":      lambda: plain_page(5),
    "menu6":      lambda: plain_page(6),
    "menu7":      lambda: plain_page(7),
    "home":       lambda: home_page(torn=True),
    "home-full":  lambda: home_page(torn=False),
    "home-slides":lambda: home_page(torn=True, slides=True),
}
if HAVE_PHP:
    PAGES.update({
        "provapa":         lambda: provapa_page(True),
        "provapa-nophoto": lambda: provapa_page(False),
        "provapa-closed":  lambda: provapa_page(True, "tmc_provapa_season_open=0", "tmc_sliding_enabled=0"),
    })

def build_all():
    OUT.mkdir(exist_ok=True)
    paths = {}
    for name, make in PAGES.items():
        p = OUT / f"{name}.html"; p.write_text(make(), encoding="utf-8"); paths[name] = p
    return paths

if __name__ == "__main__":
    for n, p in build_all().items(): print(n, "→", p)
