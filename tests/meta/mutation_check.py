#!/usr/bin/env python3
"""
"Do the tests actually bite?" — breaks the theme on purpose, one way at a time,
and requires that the right check fails each time.

  python3 tests/meta/mutation_check.py          # takes a few minutes (it runs the browser tests too)
  python3 tests/meta/mutation_check.py --fast   # skip the browser mutations

Run it after you add or change tests, or before relying on them for something
important. A mutation that is NOT caught means a test is too weak: it would
stay green while the real thing is broken. Fix the test, never the mutation.
To protect a new rule, add a line to CASES below.
"""
import os, sys, shutil, pathlib, subprocess, tempfile, re

ROOT = pathlib.Path(__file__).resolve().parents[2]
SLUG = re.search(r'^THEME_SLUG="([^"]+)"', (ROOT / "scripts/config.sh").read_text(), re.M).group(1)
FAST = "--fast" in sys.argv

def case(label, kind, expect, edits=(), extra=None):
    return dict(label=label, kind=kind, expect=expect, edits=edits, extra=extra)

F = "functions.php"; S = "style.css"
CASES = [
    # static checks ---------------------------------------------------------------
    case("check: mapp med {…} i namnet",          "check", "konstiga",       extra=lambda t: (t / "{x,y").mkdir()),
    case("check: PHP-syntaxfel",                  "check", "PHP-fel",        [("header.php", '<header class="site-header"', '<?php if ( ?><header class="site-header"')]),
    case("check: JS-syntaxfel",                   "check", "JS-fel",         [("assets/js/main.js", "function fitHeader() {", "function fitHeader( {")]),
    case("check: CSS saknar en }",                "check", "CSS-parenteser", [(S, "/* ── Phones: 17px root", ".broken { color: red;\n/* ── Phones: 17px root")]),
    case("check: ny version utan ändringslogg",   "check", "CHANGELOG",      [(S, "Version:     2.0.0", "Version:     2.0.1")]),
    # php logic -----------------------------------------------------------------------
    case("php: 24 h-regeln blir 12 h",            "php", "24-timmarsregeln", [(F, "return $diff_h < 24;", "return $diff_h < 12;")]),
    case("php: 24 h-regeln blir 48 h",            "php", "24-timmarsregeln", [(F, "return $diff_h < 24;", "return $diff_h < 48;")]),
    case("php: 24 h-regeln blir ≤ 24 (gränsfall)","php", "24-timmarsregeln", [(F, "return $diff_h < 24;", "return $diff_h <= 24;")]),
    case("php: födelseår 1920 → 1900",            "php", "födelseår",        [(F, "return $n >= 1920 && $n <= (int) date( 'Y' );", "return $n >= 1900 && $n <= (int) date( 'Y' );")]),
    case("php: menyfiltret glömmer /evenemang/",  "php", "Tävlingar",        [(F, "&& strpos( $url, '/evenemang' ) === false", "")]),
    case("php: importen skriver över v2",         "php", "ALDRIG",           [(F, "if ( ! empty( $real ) ) return; // v2 already has its own settings — leave them alone.", "")]),
    case("php: rubriken escapas inte",            "php", "escapas",          [(F, "return esc_html( implode( ' ', $words ) ) . ' <span>' . esc_html( $last ) . '</span>';", "return implode( ' ', $words ) . ' <span>' . $last . '</span>';")]),
    case("php: spärrlistan ignorerar domäner",    "php", "spärrlista",       [(F, "if ( $domain && $line === $domain ) return true;", "")]),
    # css guard rails ------------------------------------------------------------------
    case("css: dämpad grå görs för mörk",         "css", "dämpad text",      [(S, "--color-grey-mid:    #9d9789;", "--color-grey-mid:    #6b6658;")]),
    case("css: någon lägger till 0,5 rem-text",   "css", "under 0.68rem",    [(S, "/* ── Phones: 17px root", ".tiny { font-size: 0.5rem; }\n/* ── Phones: 17px root")]),
    case("css: någon lägger till 0,8 rem-text",   "css", "antal fasta",      [(S, "/* ── Phones: 17px root", ".small { font-size: 0.8rem; }\n/* ── Phones: 17px root")]),
    case("css: basstorleken tillbaka till 100 %", "css", "basstorlek",        [(S, "font-size: 112.5%;", "font-size: 100%;")]),
    # browser ---------------------------------------------------------------------------
    case("browser: rubriken tappar bredden",      "browser", "vänstra sidan",    [(S, "  max-width: 640px;                 /* two lines", "  max-width: none;                 /* two lines")]),
    case("browser: hamburgaren för liten",        "browser", "40×40",        [(S, "  width: 40px;\n  height: 40px;\n  padding: 0;\n  align-items: center;", "  width: 28px;\n  height: 28px;\n  padding: 0;\n  align-items: center;")]),
    case("browser: menyn kan inte stängas",       "browser", "stängs av samma knapp", [("assets/js/main.js", "const open = nav.classList.toggle('is-open');", "const open = nav.classList.add('is-open') || true;")]),
]
CMD = {"check": ["bash", "scripts/check.sh"], "php": ["php", "tests/php/run.php"], "css": [sys.executable, "tests/css/run.py"], "browser": [sys.executable, "tests/browser/run.py"]}

def run_case(c, work):
    d = pathlib.Path(work) / "repo"
    if d.exists(): shutil.rmtree(d)
    shutil.copytree(ROOT, d, ignore=shutil.ignore_patterns(".git", "dist", ".out", "__pycache__", "node_modules"))
    theme = d / "theme" / SLUG
    for rel, old, new in c["edits"]:
        f = theme / rel; s = f.read_text(encoding="utf-8")
        if old not in s: return None, f"mutationen passar inte längre — hittar inte {old[:60]!r} i {rel} (uppdatera CASES)"
        f.write_text(s.replace(old, new, 1), encoding="utf-8")
    if c["extra"]: c["extra"](theme)
    r = subprocess.run(CMD[c["kind"]], cwd=d, capture_output=True, text=True, encoding="utf-8", errors="replace", env={**os.environ, "CI": "true"})
    out = r.stdout + r.stderr
    return (r.returncode != 0 and c["expect"].lower() in out.lower()), out

caught_all = True
print(f"{'Vad som förstörs':46} Resultat")
with tempfile.TemporaryDirectory() as work:
    for c in CASES:
        if FAST and c["kind"] == "browser": continue
        res, out = run_case(c, work)
        if res is None:
            caught_all = False; print(f"{c['label']:46} \033[33m? {out}\033[0m"); continue
        caught_all &= bool(res)
        print(f"{c['label']:46} " + ("\033[32m✓ fångades\033[0m" if res else "\033[31m✗ SLIPPTE IGENOM\033[0m"))
        if not res: print("      " + out[-500:].replace("\n", "\n      "))
print("\nAlla förstörelser fångades — skyddsnäten biter." if caught_all else "\nNågot slank igenom (eller passar inte längre) — se ovan.")
sys.exit(0 if caught_all else 1)
