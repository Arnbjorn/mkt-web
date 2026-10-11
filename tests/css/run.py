#!/usr/bin/env python3
"""CSS guard rails — protects the readability work (contrast + text size).

  python3 tests/css/run.py

1. Contrast: key text/background pairs from the palette must keep their WCAG
   contrast ratio. Changing a colour variable so that text gets harder to read
   fails here, before it reaches the live site.
2. Text size: fixed font sizes in style.css may not get smaller than a floor,
   and the number of "small" sizes may only go DOWN (a ratchet). Lower
   SMALL_TEXT_BASELINE when you fix some; never raise it to make a test pass.
"""
import re, sys, pathlib

ROOT = pathlib.Path(__file__).resolve().parents[2]
slug = re.search(r'^THEME_SLUG="([^"]+)"', (ROOT / "scripts/config.sh").read_text(), re.M).group(1)
CSS = (ROOT / "theme" / slug / "style.css").read_text(encoding="utf-8")

GREEN, RED, RESET = "\033[32m", "\033[31m", "\033[0m"
failed = 0
def ok(msg):  print(f"  {GREEN}✓{RESET} {msg}")
def bad(msg):
    global failed; failed += 1; print(f"  {RED}✗{RESET} {msg}")

# ── colour variables (later declarations win, like in the browser) ──────────────
VARS = {}
for m in re.finditer(r"(--[a-z0-9-]+):\s*(#[0-9a-fA-F]{6})\b", CSS):
    VARS[m.group(1)] = m.group(2).lower()

def lum(hexcolor):
    h = hexcolor.lstrip("#"); r, g, b = (int(h[i:i+2], 16) / 255 for i in (0, 2, 4))
    f = lambda c: c / 12.92 if c <= 0.03928 else ((c + 0.055) / 1.055) ** 2.4
    return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b)
def ratio(a, b):
    la, lb = lum(a), lum(b); hi, lo = max(la, lb), min(la, lb)
    return (hi + 0.05) / (lo + 0.05)
def blend(fg, bg, alpha):
    f, b = fg.lstrip("#"), bg.lstrip("#")
    return "#" + "".join("%02x" % round(int(f[i:i+2], 16) * alpha + int(b[i:i+2], 16) * (1 - alpha)) for i in (0, 2, 4))
def v(name):
    if name not in VARS: raise SystemExit(f"{RED}✗{RESET} variabeln {name} hittas inte i style.css")
    return VARS[name]

print("Kontrast (WCAG) — nyckelpar i paletten")
PAIRS = [  # (text, background, minimum, what it is)
    ("--color-cream",      "--color-black",     7.0, "brödtext på sidans bakgrund"),
    ("--color-cream",      "--color-coal",      7.0, "brödtext på växlande sektion"),
    ("--color-cream",      "--color-asphalt",   7.0, "brödtext på kort"),
    ("--color-grey-light", "--color-black",     7.0, "ljusare dämpad text på svart"),
    ("--color-grey-light", "--color-asphalt",   7.0, "ljusare dämpad text på kort"),
    ("--color-grey-mid",   "--color-black",     6.0, "dämpad text på svart"),
    ("--color-grey-mid",   "--color-coal",      5.5, "dämpad text på växlande sektion"),
    ("--color-grey-mid",   "--color-asphalt",   5.5, "dämpad text på kort"),
    ("--color-gold",       "--color-black",     4.5, "guldtext/länkar på svart"),
    ("--color-gold-light", "--color-black",     4.5, "ljus guldtext på svart"),
    ("--color-orange",     "--color-black",     4.5, "orange kicker på svart"),
    ("--color-black",      "--color-gold",      4.5, "knapptext (svart på guld)"),
    ("--color-black",      "--color-gold-light",4.5, "knapptext, hover"),
    ("--color-ink",        "--color-beige",     7.0, "text på beige läsyta"),
    ("--color-ink",        "--color-beige-deep",7.0, "text på beigt kort"),
    ("--color-ink-soft",   "--color-beige",     7.0, "sekundär text på beige"),
    ("--color-ink-soft",   "--color-beige-deep",6.5, "sekundär text på beigt kort"),
    ("--color-rust",       "--color-beige",     4.5, "rostbruna etiketter på beige"),
    ("--color-rust",       "--color-beige-deep",4.5, "rostbruna länkar på beigt kort"),
]
for fg, bg, need, what in PAIRS:
    r = ratio(v(fg), v(bg))
    (ok if r >= need else bad)(f"{what}: {r:.1f}:1 (minst {need:g}) — {fg} på {bg}")

# dark text at 85% opacity (used on beige and on the gold badge) must stay readable
ink = v("--color-ink")
for bgname, need in (("--color-beige", 7.0), ("--color-beige-deep", 7.0), ("--color-gold", 4.5)):
    r = ratio(blend(ink, v(bgname), 0.85), v(bgname))
    (ok if r >= need else bad)(f"mörk text i 85 % opacitet på {bgname}: {r:.1f}:1 (minst {need:g})")
low_alpha = re.findall(r"color:\s*rgba\(36,\s*31,\s*24,\s*(0?\.\d+)\)", CSS)
weak = [a for a in low_alpha if float(a) < 0.8]
(ok if not weak else bad)(f"ingen mörk text på ljus yta under 80 % opacitet ({'inga träffar' if not weak else weak})")

# ── text sizes ───────────────────────────────────────────────────────────────────
print("\nTextstorlek")
FLOOR_REM = 0.68             # nothing may be set smaller than this
SMALL_REM = 0.85             # "small" threshold for the ratchet
SMALL_TEXT_BASELINE = 2      # fixed sizes below SMALL_REM today: .hero__badge-text, mobile .site-header__cta — may only go down (calendar tags fixed in 2.1.0)
def to_rem(value):
    value = value.strip()
    m = re.fullmatch(r"([\d.]+)rem", value)
    if m: return float(m.group(1))
    m = re.fullmatch(r"([\d.]+)px", value)
    return float(m.group(1)) / 16 if m else None
sizes = [(m.group(1), to_rem(m.group(1))) for m in re.finditer(r"font-size:\s*([^;}\n]+)", CSS)]
sizes = [(raw, r) for raw, r in sizes if r is not None]
tiny  = sorted({raw for raw, r in sizes if r < FLOOR_REM})
small = [raw for raw, r in sizes if r < SMALL_REM]
(ok if not tiny else bad)(f"ingen fast textstorlek under {FLOOR_REM}rem" + ("" if not tiny else f" — hittade: {', '.join(tiny)}"))
(ok if len(small) <= SMALL_TEXT_BASELINE else bad)(
    f"antal fasta storlekar under {SMALL_REM}rem: {len(small)} (tak {SMALL_TEXT_BASELINE})"
    + ("" if len(small) <= SMALL_TEXT_BASELINE else " — du har lagt till liten text; gör den större"))
if len(small) < SMALL_TEXT_BASELINE:
    print(f"    tips: det blev färre små storlekar än taket — sänk SMALL_TEXT_BASELINE till {len(small)} i tests/css/run.py")

# the root size is what makes everything else scale — guard it
m = re.search(r"\bhtml\s*\{[^}]*?font-size:\s*([\d.]+)%", CSS)
(ok if m and float(m.group(1)) >= 112.5 else bad)(f"html-basstorlek ≥ 112,5 % (18 px) på dator: {m.group(1) + ' %' if m else 'hittas inte'}")
m2 = re.search(r"@media \(max-width: 480px\) \{ html \{ font-size: ([\d.]+)%", CSS)
(ok if m2 and float(m2.group(1)) >= 106.25 else bad)(f"html-basstorlek ≥ 106,25 % (17 px) på mobil: {m2.group(1) + ' %' if m2 else 'hittas inte'}")

print()
print("Alla CSS-tester OK." if not failed else f"{failed} CSS-test misslyckades.")
sys.exit(1 if failed else 0)
