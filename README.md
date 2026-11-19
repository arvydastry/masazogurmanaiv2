# Masažo Gurmanai — WordPress tema v2 (Elementor)

Atnaujinta masazogurmanai.lt svetainė: masažuotojas Povilas Maliukevičius, Kaunas.
Nauja tema `masazo-gurmanai` — juoda ir auksas pagal kliento iškabą, puslapiai redaguojami su **Elementor**,
rezervacija vyksta tik per **Treatwell**.

Peržiūra: `ekrano-nuotraukos/` · Temos archyvas diegimui: `dist/masazo-gurmanai-2.0.0.zip`

## Kas padaryta pagal kliento prašymus

| Prašymas | Kaip įgyvendinta |
|---|---|
| Dizainas pagal nuotraukų spalvas ir šriftus | Spalvos ir šriftai paimti iš kliento iškabos (ji matoma jo „Treatwell“ puslapyje): juoda + auksas, rašytinis pavadinimas (Allura), antraštės klasikinėmis raidėmis (Marcellus), tekstas — gerai įskaitomas Hanken Grotesk. Kai klientas atsiųs savo nuotraukas, spalvos ir nuotraukos bus patikslintos. |
| Nuotrauka pradžios puslapyje prie „Masažo Gurmanai“ | Hero blokas: pavadinimas, šūkis, nuotrauka. Nuotrauka keičiama Elementor (blokas „Hero“ → Nuotrauka). Kol kas įdėtas esamas Povilo portretas. Tinka ir portretas be fono, ir įprasta nuotrauka (laukas „Nuotraukos tipas“). |
| Naujos kabineto nuotraukos | Kabineto nuotraukos naudojamos trijose vietose (pradžia, „Apie mane“ galerija, kontaktai) — visos keičiamos Elementor. Kai klientas atsiųs naujas, užtenka jas pakeisti. |
| Pašalinti „Body Map“ | Naujoje temoje šios skilties nėra. |
| Išjungti straipsnius | *Masažo Gurmanai → Nustatymai → Straipsniai*: numatyta **„Paslėpta“** — meniu punkto ir sąrašo nėra, bet du jau parašyti straipsniai lieka pasiekiami per „Google“ (neprarandami lankytojai). Galima ir „Visiškai išjungta“, ir bet kada vėl „Rodoma“. Straipsniai neištrinami. |
| Rezervacija per „Treatwell“ | Visi mygtukai „Rezervuoti laiką“ veda į Treatwell. Prie kiekvieno masažo — **tiesioginė nuoroda į tos paslaugos laikų pasirinkimą** (vienu paspaudimu mažiau). Svetainės kalendoriaus ir rezervacijos formų nebėra — lieka vienas kalendorius. |

Papildomai: telefone ekrano apačioje visada matoma juosta „Skambinti / Rezervuoti laiką“; paslaugos ir kainos
valdomos vienoje vietoje; sutvarkyta antraštė „Google“ rezultatuose (anksčiau buvo tiesiog „masazogurmanai.lt“),
aprašymai, dalijimosi paveikslėlis ir struktūriniai duomenys (adresas, darbo laikas, kainos).

## Kur kas keičiama

| Ką | Kur |
|---|---|
| Paslaugos pavadinimas, aprašymas, kaina, trukmė, akcija, „Treatwell“ nuoroda | **Paslaugos** (kairiajame meniu). Pakeitus kainą čia — ji pasikeičia ir pradžios, ir paslaugų puslapyje. |
| Telefonas, adresas, darbo laikas, rezervacijos nuoroda, įvertinimas | **Masažo Gurmanai → Nustatymai** |
| Puslapių tekstai ir nuotraukos | **Puslapiai → „Redaguoti su Elementor“**. Spustelėjus bloką, kairėje atsiranda jo laukai. |
| Nauji blokai | Elementor valdiklių sąrašo viršuje — kategorija **„Masažo Gurmanai“** (15 blokų). |
| Meniu | Išvaizda → Meniu |
| Logotipas, svetainės ikona | Išvaizda → Konfigūruoti → Tinklalapio identifikacija |

Tas pats aprašyta administravime: *Masažo Gurmanai → Pagalba*.

### Elementor blokai (kategorija „Masažo Gurmanai“)

Hero · Puslapio antraštė · Faktų juosta · Paslaugos ir kainos (kortelės arba kainoraštis) · Žingsniai ·
Tekstas ir nuotrauka · Citata ir skaičiai · Privalumai · Atsiliepimai · Nuorodų kortelės · Dovanų kuponai ·
Nuotraukų galerija · Kontaktai ir kaip rasti · Kvietimas rezervuoti · Teksto blokas

Kiekvienas blokas — viena puslapio sekcija su savo laukais lietuvių kalba. Galima naudoti ir įprastus
Elementor valdiklius: globalūs Elementor stiliai (spalvos, šriftai, mygtukai) jau suderinti su dizainu.

## Vietinė aplinka

**Paleisti:** dukart spustelėkite `PALEISTI.command` (arba `./skriptai/paleisti.sh`).
Svetainė — http://localhost:8170, administravimas — http://localhost:8170/wp-admin
(naudotojas ir slaptažodis — faile `.env`). Išjungti — `SUSTABDYTI.command`.

Pirmą kartą skriptas pats sudiegia WordPress, Elementor, temą ir visą turinį. Aplinka sąmoningai tokia pati kaip
tikrame serveryje: **WordPress 7.1, PHP 8.5**, klaidų rodymas įjungtas (tema nemeta nė vieno įspėjimo).

```bash
./skriptai/paleisti.sh --force   # iš naujo įrašyti pradinį puslapių turinį ir paslaugas
./skriptai/paleisti.sh --reset   # ištrinti vietinę duomenų bazę ir diegti nuo nulio
./skriptai/paketas.sh            # paruošti dist/masazo-gurmanai-<versija>.zip
```

Projektas guli `~/Documents`, todėl iCloud kartais „iškelia“ failus iš disko ir Docker jų nemato —
skriptai tokius failus prieš darbą parsiunčia patys.

## Kaip sudaryta tema

```
wp-content/themes/masazo-gurmanai/
  functions.php            įkelia modulius iš inc/
  header.php, footer.php   antraštė, mobilus meniu, poraštė, apatinė juosta telefone
  page.php, single.php, index.php, 404.php
  inc/settings.php         nustatymų puslapis (viena vieta kontaktams, darbo laikui, Treatwell nuorodai)
  inc/services.php         įrašų tipas „Paslaugos“ ir jų atvaizdavimas
  inc/blog.php             straipsnių režimai ir nukreipimai
  inc/seo.php              antraštė, aprašymas, Open Graph, JSON-LD (išsijungia, jei įdiegtas SEO įskiepis)
  inc/tracking.php         „Google Ads“ žyma (tik tikroje svetainėje)
  inc/elementor.php        Elementor integracija; inc/widgets/ — 15 valdiklių
  inc/setup-content.php    pradinio turinio sukūrimas; inc/seed/ — pradiniai tekstai ir paslaugos
  assets/css/main.css      visi stiliai (spalvos ir šriftai — kintamieji failo pradžioje)
  assets/js/main.js        be bibliotekų: meniu, žemėlapis, pasirodymo animacija, paspaudimų įvykiai
  assets/fonts, assets/icons   šriftai ir ikonos laikomi temoje (jokių užklausų į Google Fonts)
```

Spalvos keičiamos vienoje vietoje — `assets/css/main.css` pradžioje (`--mg-gold`, `--mg-bg` ir kt.).

## Tikra svetainė — masazogurmanai.lt

**Tema įdiegta ir įjungta 2026-11-19.** Atlikta:

1. Atsarginė kopija prieš pakeitimus — UpdraftPlus, 2026-11-19 14:56 (duomenų bazė ir failai, saugoma serveryje).
2. Įdiegtas ir įjungtas **Elementor** 4.4.1 (nemokamas).
3. Įkelta ir įjungta tema `masazo-gurmanai` 2.0.0, paleistas *Masažo Gurmanai → Pradinis turinys*:
   užpildyti šeši esami puslapiai (adresai nepasikeitė), sukurta 18 paslaugų, meniu ir nustatymai.
4. *Code Snippets* (WPCode) išjungti senojo kalendoriaus intarpai: „Hero greita rezervacija (JS)“,
   „Hero greita rezervacija (CSS)“, „Body map sekcijos paslėpimas (CSS)“,
   „Rezervacijų konversijos (Meta Pixel + GA4) + šaltinis (JS)“.
   Palikti įjungti: „Rezervacijų žurnalas ir statistika (PHP)“ — kad administravime liktų senų rezervacijų sąrašas
   (*Rezervacijos*), ir „pop up akcija“ (nuo temos nepriklauso).
5. Išvalyta LiteSpeed talpykla.

Patikrinta po įdiegimo: visi puslapiai atsidaro be PHP pranešimų (kompiuteryje ir telefono pločiu), senieji adresai
nukreipiami (`/kursai`, `/straipsniai`), visos 14 tiesioginių „Treatwell“ nuorodų atveria teisingą paslaugą ir trukmę,
Elementor redaktorius atsidaro su visais 15 temos blokų, reklamos žymos („Google Ads“, GA4, „Meta Pixel“)
liko vietoje.

**Dar reikia padaryti ranka:** senajame kalendoriuje priimtas būsimas rezervacijas (*Rezervacijos* administravime)
įvesti į „Treatwell“ — kitaip tie laikai ten liks laisvi.

### Temos atnaujinimas

1. Pakeisti versiją `style.css` (`Version:`) ir `functions.php` (`MG_VERSION`), paleisti `./skriptai/paketas.sh`.
2. Išvaizda → Temos → Pridėti temą → Įkelti → pasirinkti naują archyvą → patvirtinti, kad esama tema pakeičiama įkeltąja.
3. LiteSpeed Cache → **Purge All**.

Puslapių turinys ir paslaugos atnaujinant temą nesikeičia. „Pradinis turinys“ be varnelės „Perrašyti“
jau užpildytų puslapių neliečia.

### Jei reikia grįžti atgal

Išvaizda → Temos → vėl įjungti „Gydomasis Masažas“ ir WPCode įjungti keturis išjungtus intarpus.
Senosios temos nustatymai neliesti, o puslapių šablonai atkuriami automatiškai. Pilnas atstatymas — iš UpdraftPlus kopijos.

### Naujas diegimas kitoje svetainėje

Įskiepiai → Pridėti → **Elementor** → Įjungti · Išvaizda → Temos → Įkelti `dist/masazo-gurmanai-2.0.0.zip` → Įjungti ·
*Masažo Gurmanai → Pradinis turinys → „Sukurti pradinį turinį“* · peržiūrėti *Masažo Gurmanai → Nustatymai*.

## Techninės pastabos

- Tiesioginės paslaugų nuorodos (`treatwell.lt/availability?venueId=480496&proposedServices=…`) paimtos iš paties
  Treatwell rezervacijos proceso; tai nėra oficialiai dokumentuotas adresas. Jei kuri nors nustotų veikti —
  ištrynus lauką „Rezervacijos nuoroda“ paslaugos lange, mygtukas ves į bendrą salono puslapį.
- Įvertinimas (4,9 / 46) įrašytas nustatymuose ir pats neatsinaujina.
- Prieinamumas: tikrinta su axe (WCAG 2.1 AA) — pažeidimų nerasta; visi puslapiai valdomi klaviatūra,
  veikia „pereiti prie turinio“ nuoroda, gerbiamas „sumažinto judesio“ nustatymas.
- Šriftai (SIL OFL) ir ikonos (Phosphor, MIT) — licencijos failuose `assets/fonts/LICENCIJOS.txt` ir
  `assets/icons/LICENSE-phosphor.txt`.
- Jei įdiegtas „Elementor Pro“ ir jame sukurta antraštė ar poraštė — tema naudoja jas vietoje savųjų.
