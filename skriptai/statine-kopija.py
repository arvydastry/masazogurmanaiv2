#!/usr/bin/env python3
"""Statinė vietinės svetainės kopija peržiūrai per GitHub Pages.

Naudojimas (vietinė svetainė turi veikti — ./skriptai/paleisti.sh):

    python3 skriptai/statine-kopija.py <išvesties katalogas> [viešas adresas]

Parsiunčia puslapius ir visus jų failus (CSS, JS, šriftus, paveikslėlius) iš http://localhost:8170,
pakeičia adresus į viešą adresą ir prideda „noindex“, kad peržiūros kopija nepatektų į „Google“.
Tai tik peržiūra: WordPress, Elementor redaktoriaus ir administravimo joje nėra.
"""

import html
import os
import re
import shutil
import subprocess
import sys
import urllib.error
import urllib.parse
import urllib.request

LOCAL = 'http://localhost:8170'
PAGES = ['/', '/paslaugos', '/kuponai', '/apie', '/atsiliepimai', '/kontaktai']
ASSET_EXT = ('css', 'js', 'png', 'jpg', 'jpeg', 'webp', 'avif', 'svg', 'gif', 'ico', 'woff', 'woff2', 'ttf', 'json', 'mp4', 'webm')
ASSET_RE = re.compile(r'\.(?:' + '|'.join(ASSET_EXT) + r')$', re.I)


def fetch(url):
    req = urllib.request.Request(url, headers={'User-Agent': 'statine-kopija'})
    with urllib.request.urlopen(req, timeout=60) as r:
        return r.read()


def local_path(url):
    """Vietinio failo kelias (be užklausos dalies) arba None, jei tai ne šios svetainės failas."""
    u = urllib.parse.urlsplit(url)
    if u.scheme and (u.scheme + '://' + u.netloc) != LOCAL:
        return None
    path = urllib.parse.unquote(u.path)
    if not path.startswith('/') or '..' in path or not ASSET_RE.search(path):
        return None
    return path


def main():
    if len(sys.argv) < 2:
        sys.exit(__doc__)
    out = os.path.abspath(sys.argv[1])
    public = (sys.argv[2] if len(sys.argv) > 2 else 'https://arvydastry.github.io/masazogurmanaiv2').rstrip('/')

    if os.path.isdir(out):
        for name in os.listdir(out):
            if name == '.git':
                continue
            p = os.path.join(out, name)
            shutil.rmtree(p) if os.path.isdir(p) else os.remove(p)
    os.makedirs(out, exist_ok=True)

    queue, done, missing = [], set(), []

    def want(url, base):
        url = html.unescape(url.strip()).replace('\\/', '/')
        if not url or url.startswith(('data:', '#', 'mailto:', 'tel:', 'javascript:')):
            return
        absolute = urllib.parse.urljoin(base, url)
        path = local_path(absolute)
        if path and path not in done:
            done.add(path)
            queue.append(path)

    def scan_css(text, base):
        for m in re.finditer(r'url\(\s*["\']?([^"\')]+)["\']?\s*\)', text):
            want(m.group(1), base)
        for m in re.finditer(r'@import\s+["\']([^"\']+)["\']', text):
            want(m.group(1), base)

    slugs = [p.strip('/') for p in PAGES if p != '/']

    def rewrite(text):
        # Nuorodos į puslapius — su pasviruoju brūkšniu gale (GitHub Pages atiduoda <katalogas>/index.html).
        for slug in slugs:
            text = re.sub(re.escape(LOCAL + '/' + slug) + r'(?=["\'#?<\s])', LOCAL + '/' + slug + '/', text)
            esc = (LOCAL + '/' + slug).replace('/', '\\/')
            text = re.sub(re.escape(esc) + r'(?=["\'#?])', esc + '\\/', text)
        text = text.replace(LOCAL, public)
        text = text.replace(LOCAL.replace('/', '\\/'), public.replace('/', '\\/'))
        text = text.replace(urllib.parse.quote(LOCAL, safe=''), urllib.parse.quote(public, safe=''))
        return text

    def clean_page(text):
        # WordPress API, RSS ir pan. nuorodų statinėje kopijoje nėra.
        text = re.sub(r'<link[^>]+rel=["\'](?:https://api\.w\.org/|EditURI|wlwmanifest|shortlink|pingback|alternate|profile)["\'][^>]*>\s*', '', text)
        text = re.sub(r'<meta[^>]+name=["\']robots["\'][^>]*>\s*', '', text)
        text = re.sub(r'(<meta charset=[^>]*>)', r'\1\n<meta name="robots" content="noindex, nofollow">', text, count=1)
        return text

    def save(path, data):
        target = os.path.join(out, path.lstrip('/'))
        os.makedirs(os.path.dirname(target), exist_ok=True)
        with open(target, 'wb') as f:
            f.write(data)

    def scan_page(text, base):
        for m in re.finditer(r'(?:src|href|poster|data-src|content)=["\']([^"\']+)["\']', text):
            want(m.group(1), base)
        for m in re.finditer(r'(?:srcset|imagesrcset)=["\']([^"\']+)["\']', text):
            for part in m.group(1).split(','):
                bits = part.strip().split()
                if bits:
                    want(bits[0], base)
        for m in re.finditer(re.escape(LOCAL) + r'[^"\'\s<>)\\]+', text):
            want(m.group(0), base)
        for m in re.finditer(re.escape(LOCAL.replace('/', '\\/')) + r'[^"\'\s<>)]+', text):
            want(m.group(0), base)
        scan_css(text, base)

    # 1. Puslapiai.
    for page in PAGES + ['/404-statine-kopija']:
        url = LOCAL + page
        try:
            raw = fetch(url)
        except urllib.error.HTTPError as e:
            raw = e.read()  # 404 puslapis
        text = raw.decode('utf-8')
        scan_page(text, url)
        text = rewrite(clean_page(text))
        if page == '/':
            save('/index.html', text.encode('utf-8'))
        elif page.startswith('/404'):
            save('/404.html', text.encode('utf-8'))
        else:
            save(page + '/index.html', text.encode('utf-8'))
        print('puslapis', page)

    # Elementor dalį savo scenarijų įkelia tik prireikus (pvz. konteinerių fonams) — puslapio kode jų adresų nėra,
    # todėl sąrašą pasiimame iš vietinės svetainės konteinerio.
    chunks_dir = '/wp-content/plugins/elementor/assets/js/chunks'
    try:
        listing = subprocess.run(
            ['docker', 'compose', 'exec', '-T', 'wp', 'ls', '/var/www/html' + chunks_dir],
            capture_output=True, text=True, timeout=60,
            cwd=os.path.dirname(os.path.dirname(os.path.abspath(__file__))),
        ).stdout.split()
    except Exception:  # noqa: BLE001
        listing = []
    for name in listing or ['background-slideshow.min.js', 'background-video.min.js']:
        if name.endswith('.min.js'):
            want(LOCAL + chunks_dir + '/' + name, LOCAL)

    # 2. Failai (CSS viduje gali būti nuorodų į kitus failus).
    count = 0
    while queue:
        path = queue.pop(0)
        url = LOCAL + urllib.parse.quote(path)
        try:
            data = fetch(url)
        except Exception as e:  # noqa: BLE001 — trūkstamą failą tik pažymime
            missing.append(path + ' (' + str(e)[:40] + ')')
            continue
        if path.lower().endswith('.css'):
            text = data.decode('utf-8', errors='replace')
            scan_css(text, url)
            data = rewrite(text).encode('utf-8')
        elif path.lower().endswith(('.js', '.json', '.svg')):
            text = data.decode('utf-8', errors='replace')
            if LOCAL in text or LOCAL.replace('/', '\\/') in text:
                data = rewrite(text).encode('utf-8')
        save(path, data)
        count += 1

    open(os.path.join(out, '.nojekyll'), 'w').close()
    print('failų:', count, '| nerasta:', len(missing))
    for m in missing:
        print('  nerasta:', m)

    # 3. Patikra: neturi likti vietinių adresų.
    left = 0
    for root, _dirs, files in os.walk(out):
        if '.git' in root.split(os.sep):
            continue
        for name in files:
            if name.endswith(('.html', '.css', '.js', '.json')):
                with open(os.path.join(root, name), encoding='utf-8', errors='replace') as f:
                    left += f.read().count('localhost:8170')
    print('likusių „localhost:8170“:', left)


if __name__ == '__main__':
    main()
