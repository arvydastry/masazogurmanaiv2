<?php
/**
 * Pradinis puslapių turinys (Elementor valdikliai ir jų laukai).
 *
 * Naudojamas tik kuriant pradinį turinį (inc/setup-content.php). Tekstai perkelti iš
 * ankstesnės svetainės; vėliau viskas redaguojama su Elementor.
 *
 * Nuotraukos nurodomos failo vardu: array( 'img' => 'failas.jpg' ) — kuriant turinį
 * jos įkeliamos į Media biblioteką ir pakeičiamos tikru įrašu.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

$mg_img = static function ( string $file ): array {
	return array( 'img' => $file );
};

$mg_url = static function ( string $url ): array {
	return array( 'url' => $url );
};

$mg_late_note = 'Vertindami Jūsų ir kitų klientų laiką, informuojame, kad vėlavimo atveju procedūra gali būti trumpinama, o esant poreikiui – atšaukiama arba perkeliama.';

$mg_route = '<p>Masažo studija įsikūrusi Savanorių pr. 206, Kaune, 112 kabinete. Įėjimas yra ties Savanorių pr. ir Taikos pr. sankryža, šalia požeminės pėsčiųjų perėjos. Į pastatą patenkama pro stiklines duris su užrašu „LASS pietvakarių centras“.</p>'
	. '<p><strong>Viešuoju transportu:</strong> stotelė „Kauno aklųjų ir silpnaregių centras“ (autobusai 6, 6G, 26, 29, 36, 41, 54).</p>';

return array(

	/* ------------------------------------------------------------------ */
	'pradzia'      => array(
		'title'     => 'Pradžia',
		'front'     => true,
		'seo_title' => 'Masažas Kaune – Masažo Gurmanai | Povilas Maliukevičius',
		'seo_desc'  => 'Profesionalūs, individualiai pritaikyti masažai Kaune: klasikinis, gydomasis, atpalaiduojantis, sportinis, limfodrenažinis. Masažuotojas Povilas Maliukevičius. Rezervacija internetu.',
		'widgets'   => array(
			array(
				'mg-hero',
				array(
					'name'        => 'Masažo Gurmanai',
					'tagline'     => 'Išskirtinė masažo patirtis',
					'text'        => 'Profesionalūs, individualiai pritaikyti masažai Kaune. Juos atlieka masažuotojas Povilas Maliukevičius.',
					'btn1_type'   => 'booking',
					'btn1_label'  => '',
					'btn2_type'   => 'custom',
					'btn2_label'  => 'Paslaugos ir kainos',
					'btn2_url'    => $mg_url( '/paslaugos' ),
					'image'       => $mg_img( 'povilas-maliukevicius-portretas.webp' ),
					'image_alt'   => 'Masažuotojas Povilas Maliukevičius',
					'image_style' => 'cutout',
				),
			),
			array(
				'mg-facts',
				array(
					'items' => array(
						array( 'source' => 'rating' ),
						array( 'source' => 'address' ),
						array( 'source' => 'hours' ),
						array(
							'source' => 'custom',
							'icon'   => 'tag',
							'value'  => 'Masažai nuo 29 €',
							'label'  => '30–90 min trukmės',
							'url'    => $mg_url( '/paslaugos' ),
						),
					),
				),
			),
			array(
				'mg-services',
				array(
					'h_title'   => 'Populiariausi masažai',
					'h_lead'    => 'Pasirinkite masažą ir iškart rezervuokite Jums patogų laiką.',
					'h_tag'     => 'h2',
					'layout'    => 'cards',
					'type'      => 'single',
					'featured'  => 'yes',
					'limit'     => 6,
					'btn_type'  => 'custom',
					'btn_label' => 'Visos paslaugos ir kainos',
					'btn_url'   => $mg_url( '/paslaugos' ),
				),
			),
			array(
				'mg-steps',
				array(
					'h_title' => 'Kaip užsiregistruoti',
					'h_tag'   => 'h2',
					'items'   => array(
						array(
							'title' => 'Pasirinkite masažą',
							'text'  => 'Peržiūrėkite paslaugas ir kainas. Jei abejojate, kuris masažas tinka Jums, paskambinkite: patarsiu.',
						),
						array(
							'title' => 'Pasirinkite laiką',
							'text'  => 'Laisvus laikus matysite „Treatwell“ kalendoriuje. Rezervacija užtrunka apie minutę.',
						),
						array(
							'title' => 'Gaukite priminimą',
							'text'  => 'Patvirtinimą ir priminimą apie vizitą gausite SMS žinute ir el. paštu.',
						),
					),
					'note'    => $mg_late_note,
					'btn_type' => 'none',
					'l_tone'  => 'alt',
				),
			),
			array(
				'mg-split',
				array(
					'h_title'   => 'Kada Jūsų kūnui reikia specialisto?',
					'h_lead'    => 'Kai kūnas signalizuoja apie disbalansą – skausmu, sustingimu ar sumažėjusiu judrumu.',
					'h_tag'     => 'h2',
					'image'     => $mg_img( 'masazo-kabinetas-2.jpg' ),
					'image_alt' => 'Masažo kabinetas žaliomis sienomis: masažo stalas, krėslas ir drabužių kabykla',
					'side'      => 'left',
					'ratio'     => 'portrait',
					'text'      => '',
					'items'     => array(
						array(
							'icon'  => 'person-arms-spread',
							'title' => 'Nugaros skausmas',
							'text'  => 'Nuolatinė ar pasikartojanti įtampa juosmens srityje.',
						),
						array(
							'icon'  => 'user-focus',
							'title' => 'Kaklo ir pečių sustingimas',
							'text'  => 'Sėdimo darbo ar streso pasekmė.',
						),
						array(
							'icon'  => 'brain',
							'title' => 'Galvos skausmai',
							'text'  => 'Įtampa kaklo ir trapecijos zonoje.',
						),
						array(
							'icon'  => 'bandaids',
							'title' => 'Atsigavimas po traumų',
							'text'  => 'Po fizinio krūvio ar trauminių įvykių.',
						),
					),
					'btn_type'  => 'none',
				),
			),
			array(
				'mg-statement',
				array(
					'h_title'   => 'Kodėl verta pasitikėti?',
					'h_tag'     => 'h2',
					'quote'     => 'Nors neturiu regėjimo, tai tapo mano didžiausia stiprybe – per rankas jaučiu tai, ko kiti nemato. Kiekvienas raumuo, kiekviena įtampa byloja savo istoriją.',
					'author'    => 'Povilas Maliukevičius',
					'role'      => 'Masažuotojas',
					'avatar'    => $mg_img( 'povilas-maliukevicius.jpg' ),
					'stats'     => array(
						array(
							'value' => '7200+',
							'label' => 'atliktų masažų',
						),
						array(
							'value' => '400+',
							'label' => 'sugrįžtančių klientų',
						),
						array(
							'value' => '6',
							'label' => 'metai patirties',
						),
					),
					'btn_type'  => 'custom',
					'btn_label' => 'Apie mane',
					'btn_url'   => $mg_url( '/apie' ),
					'l_tone'    => 'alt',
				),
			),
			array(
				'mg-features',
				array(
					'h_title' => '',
					'h_items' => 'h3',
					'cols'    => '3',
					'items'   => array(
						array(
							'icon'  => 'hand-palm',
							'title' => 'Išskirtinis jutiminis tikslumas',
							'text'  => 'Unikalus požiūris į kiekvieną klientą, paremtas giliu kūno pajautimu ir individualiu pritaikymu.',
						),
						array(
							'icon'  => 'heartbeat',
							'title' => 'Gili kūno diagnostika',
							'text'  => 'Prieš masažą įvertinama kūno būklė, raumenų įtampa ir jautrios zonos, kad masažas būtų maksimaliai tikslus.',
						),
						array(
							'icon'  => 'user-focus',
							'title' => 'Individualus masažo metodas',
							'text'  => 'Kiekvienas masažas pritaikomas pagal žmogaus kūno reakciją, o ne pagal vieną standartinę techniką.',
						),
						array(
							'icon'  => 'flower-lotus',
							'title' => 'Holistinis požiūris',
							'text'  => 'Dirbama ne tik su simptomu, bet ir su priežastimi, siekiant ilgalaikio kūno balanso.',
						),
						array(
							'icon'  => 'medal',
							'title' => 'Patirtis ir jautrumas',
							'text'  => 'Daug metų praktikos leidžia tiksliai atpažinti įtampas ir efektyviai jas pašalinti.',
						),
						array(
							'icon'  => 'leaf',
							'title' => 'Atsipalaidavimo efektas',
							'text'  => 'Masažas sumažina stresą ir atkuria vidinę harmoniją.',
						),
					),
					'l_tone'  => 'alt',
					'l_space' => 'flush',
				),
			),
			array(
				'mg-reviews',
				array(
					'h_title'     => 'Klientų patirtys',
					'h_tag'       => 'h2',
					'show_rating' => 'yes',
					'rating_link' => 'Skaityti visus atsiliepimus',
					'cols'        => '3',
					'items'       => array(
						array(
							'text'    => 'Po ilgo sėdimo darbo pečių juosta buvo lyg akmeninė. Povilo taikoma metodika padėjo ne tik atsikratyti skausmo, bet ir suprasti, kaip taisyklingai judėti kasdienybėje. Profesionalumas aukščiausiame lygyje.',
							'name'    => 'Linas M.',
							'context' => '',
						),
						array(
							'text'    => 'Ilgai ieškojau specialisto, kuris ne tik atliktų masažą, bet ir suprastų kūno visumą. Havajietiškas Lomi Lomi masažas buvo kai kas magiško – visiška ramybė ir nauja energija. Ačiū!',
							'name'    => 'Eglė R.',
							'context' => '',
						),
						array(
							'text'    => 'Kaip aktyviai sportuojantis, dažnai susiduriu su raumenų įtampa. Sportinio masažo metu Povilas tiksliai surado problemines vietas. Po seanso jaučiuosi žymiai lengviau, o atsistatymas tapo greitesnis.',
							'name'    => 'Marius V.',
							'context' => '',
						),
					),
				),
			),
			array(
				'mg-tiles',
				array(
					'h_title' => '',
					'h_items' => 'h2',
					'items'   => array(
						array(
							'image'      => array(
								'id'  => 0,
								'url' => '',
							),
							'icon'       => 'repeat',
							'title'      => 'Masažų programos',
							'text'       => '10 vizitų programos reguliariai kūno priežiūrai. Sutaupote apie 25 %, lyginant su pavieniais vizitais.',
							'link_label' => 'Peržiūrėti programas',
							'url'        => $mg_url( '/paslaugos#programos' ),
						),
						array(
							'image'      => $mg_img( 'kuponas-dovanu-cekis.jpg' ),
							'icon'       => 'gift',
							'title'      => 'Dovanų kuponai',
							'text'       => 'Kartais gražiausia dovana – galimybė atsipalaiduoti. Padovanokite masažo malonumą, kuris palieka šiltus prisiminimus.',
							'link_label' => 'Rinktis kuponą',
							'url'        => $mg_url( '/kuponai' ),
						),
					),
					'l_space' => 'flush',
				),
			),
			array(
				'mg-contact',
				array(
					'h_title'    => 'Kaip mus rasti',
					'h_tag'      => 'h2',
					'route'      => $mg_route,
					'photo'      => $mg_img( 'iejimas-lass-pietvakariu-centras.jpg' ),
					'photo_alt'  => 'Įėjimas į pastatą „LASS pietvakarių centras“ Savanorių pr. 206, Kaune',
					'show_map'   => 'yes',
					'btn1_type'  => 'none',
					'btn2_type'  => 'none',
					'l_tone'     => 'alt',
				),
			),
			array(
				'mg-cta',
				array(
					'title'     => 'Skirkite laiko *sau*',
					'text'      => 'Rezervacija internetu veikia visą parą. Laisvus laikus matysite iškart.',
					'btn1_type' => 'booking',
					'btn2_type' => 'phone',
				),
			),
		),
	),

	/* ------------------------------------------------------------------ */
	'paslaugos'    => array(
		'title'     => 'Paslaugos',
		'seo_title' => 'Masažo paslaugos ir kainos Kaune – Masažo Gurmanai',
		'seo_desc'  => 'Klasikinis, gydomasis, atpalaiduojantis, sportinis, limfodrenažinis ir kiti masažai Kaune. Kainos nuo 29 €, rezervacija internetu per „Treatwell“.',
		'widgets'   => array(
			array(
				'mg-page-hero',
				array(
					'title'    => 'Masažo paslaugos ir kainos',
					'lead'     => 'Pasirinkite masažą pagal savo poreikius.',
					'crumbs'   => 'yes',
					'btn_type' => 'none',
				),
			),
			array(
				'mg-services',
				array(
					'h_title'    => 'Masažai',
					'h_lead'     => 'Paspauskite paslaugą ir pamatysite jos aprašymą. Mygtukas „Rezervuoti“ atveria rezervaciją „Treatwell“ sistemoje.',
					'h_tag'      => 'h2',
					'layout'     => 'list',
					'type'       => 'single',
					'featured'   => '',
					'limit'      => 0,
					'first_open' => '',
					'note'       => 'Rezervuojant per „Treatwell“ ne piko valandomis taikomos nuolaidos. Tikslią kainą pamatysite pasirinkę laiką.',
					'btn_type'   => 'none',
					'l_anchor'   => 'masazai',
				),
			),
			array(
				'mg-services',
				array(
					'h_title'    => 'Masažų programos',
					'h_lead'     => '10 vizitų programos reguliariai kūno priežiūrai. Sutaupote apie 25 %, lyginant su pavieniais vizitais.',
					'h_tag'      => 'h2',
					'layout'     => 'list',
					'type'       => 'program',
					'featured'   => '',
					'limit'      => 0,
					'first_open' => '',
					'note'       => 'Programos užsakomos telefonu: suderinsime Jums patogų vizitų grafiką.',
					'btn_type'   => 'none',
					'l_tone'     => 'alt',
					'l_anchor'   => 'programos',
				),
			),
			array(
				'mg-cta',
				array(
					'title'      => 'Nežinote, kokį masažą pasirinkti?',
					'text'       => 'Susisiekite su manimi ir kartu išsirinksime tinkamiausią variantą pagal Jūsų poreikius.',
					'btn1_type'  => 'phone',
					'btn2_type'  => 'custom',
					'btn2_label' => 'Visi kontaktai',
					'btn2_url'   => $mg_url( '/kontaktai' ),
				),
			),
		),
	),

	/* ------------------------------------------------------------------ */
	'kuponai'      => array(
		'title'     => 'Dovanų kuponai',
		'seo_title' => 'Masažo dovanų kuponai Kaune – Masažo Gurmanai',
		'seo_desc'  => 'Padovanokite masažą: dovanų kuponai klasikiniam, atpalaiduojančiam, sportiniam ir kitiems masažams Kaune arba dovanų čekis nuo 20 iki 300 €.',
		'widgets'   => array(
			array(
				'mg-page-hero',
				array(
					'title'    => 'Dovanų kuponai',
					'lead'     => 'Kartais gražiausia dovana – galimybė atsipalaiduoti. Padovanokite masažo malonumą, kuris palieka šiltus prisiminimus.',
					'crumbs'   => 'yes',
					'btn_type' => 'none',
				),
			),
			array(
				'mg-vouchers',
				array(
					'h_title'   => '',
					'buy_label' => 'Pirkti kuponą',
					'note'      => 'Kuponai perkami ir apmokami svetainėje dovanusala.lt.',
					'items'     => array(
						array(
							'image' => $mg_img( 'kuponas-klasikinis-nugaros-masazas.jpg' ),
							'title' => 'Klasikinis nugaros masažas Kaune',
							'meta'  => '1 asm. · 1 val.',
							'price' => '39,00 €',
							'url'   => $mg_url( 'https://www.dovanusala.lt/lt/24467-klasikinis-nugaros-masazas-kaune.html' ),
						),
						array(
							'image' => $mg_img( 'kuponas-klasikinis-viso-kuno-masazas.jpg' ),
							'title' => 'Klasikinis viso kūno masažas Kaune',
							'meta'  => '1 asm. · 90 min.',
							'price' => '59,00 €',
							'url'   => $mg_url( 'https://www.dovanusala.lt/lt/24468-klasikinis-viso-kuno-masazas-kaune.html' ),
						),
						array(
							'image' => $mg_img( 'kuponas-atpalaiduojantis-viso-kuno-masazas.jpg' ),
							'title' => 'Atpalaiduojantis viso kūno masažas Kaune',
							'meta'  => '1 asm. · 90 min.',
							'price' => '69,00 €',
							'url'   => $mg_url( 'https://www.dovanusala.lt/lt/24469-viso-kuno-atpalaiduojantis-masazas-kaune.html' ),
						),
						array(
							'image' => $mg_img( 'kuponas-sportinis-masazas.jpg' ),
							'title' => 'Sportinis masažas Kaune',
							'meta'  => '1 asm. · 1 val.',
							'price' => '59,00 €',
							'url'   => $mg_url( 'https://www.dovanusala.lt/lt/24471-sportinis-masazas-kaune.html' ),
						),
						array(
							'image' => $mg_img( 'kuponas-limfodrenazinis-masazas.jpg' ),
							'title' => 'Limfodrenažinis masažas rankomis Kaune',
							'meta'  => '1 asm. · 1 val.',
							'price' => '49,00 €',
							'url'   => $mg_url( 'https://www.dovanusala.lt/lt/25841-limfodrenazinis-masazas-kaune.html' ),
						),
						array(
							'image' => $mg_img( 'kuponas-lomi-lomi-masazas.jpg' ),
							'title' => 'Havajietiškas Lomi Lomi masažas Kaune',
							'meta'  => '1 asm. · 1 val.',
							'price' => '49,00 €',
							'url'   => $mg_url( 'https://www.dovanusala.lt/lt/25842-havajietiskas-lomi-lomi-masazas-kaune.html' ),
						),
						array(
							'image' => $mg_img( 'kuponas-atpalaiduojantis-nugaros-masazas.jpg' ),
							'title' => 'Atpalaiduojantis nugaros masažas Kaune',
							'meta'  => '1 asm. · 1 val.',
							'price' => '39,00 €',
							'url'   => $mg_url( 'https://www.dovanusala.lt/lt/27198-atpalaiduojantis-nugaros-masazas-kaune.html' ),
						),
						array(
							'image' => $mg_img( 'kuponas-dovanu-cekis.jpg' ),
							'title' => 'Dovanų čekis (bet kuriai paslaugai)',
							'meta'  => 'Individuali vertė',
							'price' => 'Nuo 20 iki 300 €',
							'url'   => $mg_url( 'https://www.dovanusala.lt/lt/27201-masazu-salono-masazo-gurmanai-kaune-dovanu-cekis.html' ),
						),
					),
				),
			),
			array(
				'mg-cta',
				array(
					'title'     => 'Skirkite laiko *sau*',
					'text'      => 'Rezervacija internetu veikia visą parą. Laisvus laikus matysite iškart.',
					'btn1_type' => 'booking',
					'btn2_type' => 'phone',
					'l_tone'    => 'alt',
				),
			),
		),
	),

	/* ------------------------------------------------------------------ */
	'apie'         => array(
		'title'     => 'Apie mane',
		'seo_title' => 'Apie mane – masažuotojas Povilas Maliukevičius, Kaunas',
		'seo_desc'  => 'Esu Povilas, neregys masažuotojas Kaune. Per rankas jaučiu tai, ko kiti nemato: kiekvienas masažas pritaikomas individualiai.',
		'widgets'   => array(
			array(
				'mg-page-hero',
				array(
					'title'    => 'Apie mane',
					'lead'     => 'Neregys masažuotojas su unikaliu jautrumu ir meile savo darbui.',
					'crumbs'   => 'yes',
					'btn_type' => 'none',
				),
			),
			array(
				'mg-split',
				array(
					'h_title'   => 'Mano istorija',
					'h_tag'     => 'h2',
					'image'     => $mg_img( 'povilas-maliukevicius-portretas.webp' ),
					'image_alt' => 'Masažuotojas Povilas Maliukevičius',
					'side'      => 'left',
					'ratio'     => 'portrait',
					'text'      => '<p>Esu Povilas, profesionalus masažuotojas. Nors neturiu regėjimo, tai tapo mano didžiausia stiprybe – per rankas jaučiu tai, ko kiti nemato. Kiekvienas raumuo, kiekviena įtampa byloja savo istoriją.</p>'
						. '<p>Mano kelionė į masažą prasidėjo prieš daugelį metų, kai supratau, kad prisilietimas gali būti galingas gydymo įrankis. Per šiuos metus tobulinau savo technikas ir išmokau klausytis kūno ženklų.</p>'
						. '<p>Kiekvieną klientą sutinku kaip unikalų žmogų su savo istorija, skausmais ir tikslais. Man svarbu ne tik atlikti masažą, bet ir sukurti pasitikėjimo ryšį, kuris padeda pasiekti geriausius rezultatus.</p>',
					'items'     => array(),
					'btn_type'  => 'none',
				),
			),
			array(
				'mg-features',
				array(
					'h_title' => 'Mano vertybės',
					'h_tag'   => 'h2',
					'cols'    => '2',
					'items'   => array(
						array(
							'icon'  => 'eye-slash',
							'title' => 'Unikalus jautrumas',
							'text'  => 'Mano rankos jaučia tai, ko akys nemato – subtilius raumenų pokyčius ir įtampas.',
						),
						array(
							'icon'  => 'user-focus',
							'title' => 'Individualus dėmesys',
							'text'  => 'Kiekvienas masažas pritaikomas pagal jūsų unikalius poreikius ir problemas.',
						),
						array(
							'icon'  => 'seal-check',
							'title' => 'Profesionalumas',
							'text'  => 'Nuolat tobulinu savo žinias ir technikas, kad pasiūlyčiau geriausią paslaugą.',
						),
						array(
							'icon'  => 'handshake',
							'title' => 'Pasitikėjimo ryšys',
							'text'  => 'Kuriu ilgalaikius santykius su klientais, paremtus atvirumu ir pagarba.',
						),
					),
					'l_tone'  => 'alt',
				),
			),
			array(
				'mg-gallery',
				array(
					'h_title'  => 'Masažo studija',
					'h_lead'   => 'Jauki ir profesionaliai įrengta studija Kaune, kur galėsite atsipalaiduoti ir pamiršti kasdienius rūpesčius.',
					'h_tag'    => 'h2',
					'images'   => array(
						$mg_img( 'masazo-kabinetas-2.jpg' ),
						$mg_img( 'masazo-kabinetas-3.jpg' ),
						$mg_img( 'masazo-kabinetas-1.jpg' ),
					),
					'lightbox' => 'yes',
				),
			),
			array(
				'mg-steps',
				array(
					'h_title'  => 'Mano požiūris į masažą',
					'h_tag'    => 'h2',
					'items'    => array(
						array(
							'title' => 'Išklausymas',
							'text'  => 'Pradedu nuo pokalbio – išsiaiškinu jūsų problemas, norus ir tikslus.',
						),
						array(
							'title' => 'Pritaikymas',
							'text'  => 'Parenku tinkamiausias technikas ir intensyvumą pagal jūsų poreikius.',
						),
						array(
							'title' => 'Rezultatas',
							'text'  => 'Siekiu ilgalaikio palengvėjimo ir siūlau rekomendacijas profilaktikai.',
						),
					),
					'note'     => '',
					'btn_type' => 'none',
					'l_tone'   => 'alt',
				),
			),
			array(
				'mg-cta',
				array(
					'title'     => 'Norite patys įsitikinti?',
					'text'      => 'Rezervuokite vizitą ir pajuskite, kaip individualus dėmesys gali pakeisti jūsų savijautą.',
					'btn1_type' => 'booking',
					'btn2_type' => 'phone',
				),
			),
		),
	),

	/* ------------------------------------------------------------------ */
	'atsiliepimai' => array(
		'title'     => 'Atsiliepimai',
		'seo_title' => 'Klientų atsiliepimai – Masažo Gurmanai, Kaunas',
		'seo_desc'  => 'Skaitykite klientų atsiliepimus apie masažus pas Povilą Maliukevičių Kaune ir „Treatwell“ patvirtintus įvertinimus.',
		'widgets'   => array(
			array(
				'mg-page-hero',
				array(
					'title'    => 'Klientų atsiliepimai',
					'lead'     => 'Tikros patirtys ir rezultatai po vizitų pas Povilą.',
					'crumbs'   => 'yes',
					'btn_type' => 'none',
				),
			),
			array(
				'mg-reviews',
				array(
					'h_title'     => '',
					'show_rating' => 'yes',
					'rating_link' => 'Skaityti visus atsiliepimus',
					'cols'        => '3',
					'items'       => array(
						array(
							'text'    => 'Puikus specialistas, puikios žinios ir profesionaliai atliekamas darbas. Kreipiausi su nugaros skausmais ir jau po pirmo vizito pajutau pagerėjimą. Rekomenduoju!',
							'name'    => 'Andrius S.',
							'context' => 'Viso kūno masažas',
						),
						array(
							'text'    => 'Nuostabi patirtis! Nors esu lankiusis pas daug masažuotojų, Povilo prisilietimas ir technika yra išskirtiniai. Labai rami aplinka ir malonus bendravimas.',
							'name'    => 'Jelena V.',
							'context' => 'Lomi Lomi masažas',
						),
						array(
							'text'    => 'Labai profesionalu. Matosi, kad žmogus myli savo darbą ir puikiai išmano anatomiją. Po masažo jaučiuosi tarsi naujai gimęs.',
							'name'    => 'Mantas G.',
							'context' => 'Gydomasis masažas',
						),
						array(
							'text'    => 'Puiki studija ir aukštas meistriškumas. Probleminei nugarai padėjo kur kas geriau nei kiti specialistai, pas kuriuos lankiausi anksčiau.',
							'name'    => 'Dovilė P.',
							'context' => 'Nugaros masažas',
						),
						array(
							'text'    => 'Povilas yra labai jautrus ir dėmesingas specialistas. Jo rankos tiesiog „skaito“ kūną. Po vizito ne tik dingo skausmas, bet ir pagerėjo miegas.',
							'name'    => 'Laura K.',
							'context' => 'Atpalaiduojantis masažas',
						),
						array(
							'text'    => 'Rekomenduoju visiems! Profesionalumas 10/10. Labai gera kaina už tokią aukštą kokybę. Sugrįšiu dar ne kartą.',
							'name'    => 'Karolis B.',
							'context' => 'Pečių juostos masažas',
						),
					),
				),
			),
			array(
				'mg-cta',
				array(
					'title'      => 'Palikite atsiliepimą',
					'text'       => 'Jūsų nuomonė man labai svarbi. Po vizito „Treatwell“ el. paštu atsiunčia kvietimą įvertinti masažą. Atsiliepimą taip pat galite palikti „Google“.',
					'btn1_type'  => 'custom',
					'btn1_label' => 'Rašyti atsiliepimą „Google“',
					'btn1_url'   => $mg_url( 'https://www.google.com/search?q=Masa%C5%BEuotojas+Povilas+Maliukevi%C4%8Dius' ),
					'btn2_type'  => 'none',
					'l_tone'     => 'alt',
				),
			),
		),
	),

	/* ------------------------------------------------------------------ */
	'kontaktai'    => array(
		'title'     => 'Kontaktai',
		'seo_title' => 'Kontaktai – Masažo Gurmanai, Savanorių pr. 206-112, Kaunas',
		'seo_desc'  => 'Masažo studija Kaune, Savanorių pr. 206-112 (LASS pietvakarių centras). Tel. +370 668 88481. Dirbame I–V 8:00–20:00. Rezervacija internetu.',
		'widgets'   => array(
			array(
				'mg-page-hero',
				array(
					'title'    => 'Kontaktai',
					'lead'     => 'Susisiekite su manimi ir rezervuokite masažą, pritaikytą jūsų poreikiams.',
					'crumbs'   => 'yes',
					'btn_type' => 'none',
				),
			),
			array(
				'mg-contact',
				array(
					'h_title'   => 'Kontaktinė informacija',
					'h_tag'     => 'h2',
					'route'     => '<h3>Kaip atvykti</h3>' . $mg_route,
					'photo'     => $mg_img( 'iejimas-lass-pietvakariu-centras.jpg' ),
					'photo_alt' => 'Įėjimas į pastatą „LASS pietvakarių centras“ Savanorių pr. 206, Kaune',
					'show_map'  => 'yes',
					'btn1_type' => 'booking',
					'btn2_type' => 'phone',
				),
			),
		),
	),
);
