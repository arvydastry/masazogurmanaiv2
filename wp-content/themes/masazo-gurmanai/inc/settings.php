<?php
/**
 * Temos nustatymai: „Masažo Gurmanai → Nustatymai“.
 *
 * Viskas, kas kartojasi keliose svetainės vietose (telefonas, adresas, darbo laikas,
 * rezervacijos nuoroda, įvertinimas), keičiama čia — vieną kartą.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

/** Numatytosios reikšmės. */
function mg_defaults(): array {
	$weekday = array(
		'open'   => '08:00',
		'close'  => '20:00',
		'closed' => 0,
	);
	$weekend = array(
		'open'   => '',
		'close'  => '',
		'closed' => 1,
	);

	return array(
		// Rezervacija.
		'booking_url'       => 'https://trea.tw/HVeWMChwFZ2375fuC',
		'booking_label'     => 'Rezervuoti laiką',
		'booking_new_tab'   => 1,
		'mobile_bar'        => 1,

		// Kontaktai.
		'phone'             => '+37066888481',
		'email'             => 'hello@masazogurmanai.lt',
		'address_street'    => 'Savanorių pr. 206-112',
		'address_city'      => 'Kaunas',
		'address_zip'       => 'LT-50193',
		'address_note'      => 'LASS pietvakarių centras, 112 kabinetas',
		'map_url'           => 'https://www.google.com/maps/search/?api=1&query=Savanori%C5%B3+pr.+206%2C+Kaunas',
		'map_embed'         => 'https://www.google.com/maps?q=Savanoriu%20pr.%20206-112%20Kaunas&output=embed',

		// Darbo laikas (1 = pirmadienis … 7 = sekmadienis).
		'hours'             => array(
			1 => $weekday,
			2 => $weekday,
			3 => $weekday,
			4 => $weekday,
			5 => $weekday,
			6 => $weekend,
			7 => $weekend,
		),

		// Įvertinimas.
		'rating_value'      => '4,9',
		'rating_count'      => '46',
		'rating_source'     => 'Treatwell',
		'rating_url'        => 'https://www.treatwell.lt/salonas/masazo-gurmanai/',

		// Prekės ženklas.
		'brand_name'        => 'Masažo Gurmanai',
		'brand_tagline'     => 'Išskirtinė masažo patirtis',
		'owner_name'        => 'Povilas Maliukevičius',
		'footer_about'      => 'Masažuotojas Povilas Maliukevičius, Kaunas. Masažai, kuriuose susilieja laikysenos korekcija, terapinis poveikis ir gilus poilsio pojūtis.',
		'facebook_url'      => '',
		'instagram_url'     => '',

		// Straipsniai: on (rodoma) | hidden (paslėpta, straipsniai pasiekiami tiesiogine nuoroda) | off (išjungta).
		'blog_mode'         => 'hidden',

		// Nukreipimai: po vieną eilutėje, „/senas-adresas => /naujas-adresas“.
		'redirects'         => '',

		// Reklamos sekimas (pildoma tik tikroje svetainėje).
		'gads_id'           => '',
		'gads_send_to'      => '',
		'ga4_event'         => 'rezervacija_treatwell',
		'meta_event'        => 'Schedule',
		'track_calls'       => 1,

		// SEO.
		'seo_description'   => 'Profesionalūs, individualiai pritaikyti masažai Kaune: klasikinis, gydomasis, atpalaiduojantis, sportinis, limfodrenažinis. Masažuotojas Povilas Maliukevičius. Rezervacija internetu.',
		'seo_share_image'   => '',
	);
}

/**
 * Nustatymo reikšmė.
 *
 * @param string $key      Raktas.
 * @param mixed  $fallback Reikšmė, jei rakto nėra.
 * @return mixed
 */
function mg_get( string $key, $fallback = null ) {
	$all = mg_settings();

	return array_key_exists( $key, $all ) ? $all[ $key ] : $fallback;
}

/** Visi nustatymai (išsaugoti + numatytieji). */
function mg_settings( bool $refresh = false ): array {
	static $cache = null;

	if ( null === $cache || $refresh ) {
		$saved = get_option( 'mg_settings', array() );
		$cache = array_merge( mg_defaults(), is_array( $saved ) ? $saved : array() );
	}

	return $cache;
}

add_action(
	'update_option_mg_settings',
	static function () {
		mg_settings( true );
	}
);
add_action(
	'add_option_mg_settings',
	static function () {
		mg_settings( true );
	}
);

/** Nustatymų laukų aprašas (naudojamas formai piešti ir reikšmėms valyti). */
function mg_settings_schema(): array {
	return array(
		'rezervacija' => array(
			'title'  => 'Rezervacija',
			'intro'  => 'Visi svetainės mygtukai „Rezervuoti“ veda į šią nuorodą (taip pat ir paslaugų sąraše). Jei kada prireiktų, konkrečiai paslaugai galima nurodyti atskirą nuorodą: Paslaugos → pasirinkite paslaugą → „Rezervacijos nuoroda“.',
			'fields' => array(
				'booking_url'     => array(
					'label' => 'Rezervacijos („Treatwell“) nuoroda',
					'type'  => 'url',
				),
				'booking_label'   => array(
					'label' => 'Rezervacijos mygtuko tekstas',
					'type'  => 'text',
				),
				'booking_new_tab' => array(
					'label' => 'Rezervacijos puslapį atidaryti naujame skirtuke',
					'type'  => 'checkbox',
				),
				'mobile_bar'      => array(
					'label' => 'Telefone ekrano apačioje rodyti juostą „Skambinti / Rezervuoti“',
					'type'  => 'checkbox',
				),
			),
		),
		'kontaktai'   => array(
			'title'  => 'Kontaktai',
			'intro'  => 'Rodoma antraštėje, poraštėje, kontaktų puslapyje ir paieškos sistemoms skirtuose duomenyse.',
			'fields' => array(
				'phone'          => array(
					'label' => 'Telefonas',
					'type'  => 'text',
					'desc'  => 'Tarptautiniu formatu, pvz. +37066888481.',
				),
				'email'          => array(
					'label' => 'El. paštas',
					'type'  => 'email',
				),
				'address_street' => array(
					'label' => 'Gatvė, namas, kabinetas',
					'type'  => 'text',
				),
				'address_city'   => array(
					'label' => 'Miestas',
					'type'  => 'text',
				),
				'address_zip'    => array(
					'label' => 'Pašto kodas',
					'type'  => 'text',
				),
				'address_note'   => array(
					'label' => 'Patikslinimas prie adreso',
					'type'  => 'text',
					'desc'  => 'Pvz. pastato pavadinimas ar kabineto numeris.',
				),
				'map_url'        => array(
					'label' => 'Žemėlapio nuoroda (mygtukui „Atidaryti žemėlapyje“)',
					'type'  => 'url',
				),
				'map_embed'      => array(
					'label' => 'Įterpiamo žemėlapio adresas',
					'type'  => 'url',
					'desc'  => 'Žemėlapis įkeliamas tik lankytojui paspaudus „Rodyti žemėlapį“. Palikus tuščią — mygtukas nerodomas.',
				),
			),
		),
		'laikas'      => array(
			'title'  => 'Darbo laikas',
			'intro'  => 'Laiką rašykite formatu 08:00. Pažymėjus „Nedirbame“, valandos nepaisomos.',
			'fields' => array(
				'hours' => array(
					'label' => 'Darbo laikas',
					'type'  => 'hours',
				),
			),
		),
		'ivertinimas' => array(
			'title'  => 'Įvertinimas',
			'intro'  => 'Skaičiai neatsinaujina patys — kartkartėmis juos pasitikslinkite savo „Treatwell“ puslapyje.',
			'fields' => array(
				'rating_value'  => array(
					'label' => 'Įvertinimas',
					'type'  => 'text',
					'desc'  => 'Pvz. 4,9',
				),
				'rating_count'  => array(
					'label' => 'Atsiliepimų skaičius',
					'type'  => 'text',
				),
				'rating_source' => array(
					'label' => 'Šaltinis',
					'type'  => 'text',
					'desc'  => 'Pvz. Treatwell arba Google.',
				),
				'rating_url'    => array(
					'label' => 'Nuoroda į atsiliepimus',
					'type'  => 'url',
				),
			),
		),
		'zenklas'     => array(
			'title'  => 'Pavadinimas ir poraštė',
			'intro'  => 'Logotipo paveikslėlis įkeliamas: Išvaizda → Konfigūruoti → Tinklalapio identifikacija. Kol jo nėra, rodomas pavadinimas tekstu.',
			'fields' => array(
				'brand_name'    => array(
					'label' => 'Pavadinimas',
					'type'  => 'text',
				),
				'brand_tagline' => array(
					'label' => 'Šūkis po pavadinimu',
					'type'  => 'text',
				),
				'owner_name'    => array(
					'label' => 'Specialisto vardas ir pavardė',
					'type'  => 'text',
				),
				'footer_about'  => array(
					'label' => 'Trumpas aprašymas poraštėje',
					'type'  => 'textarea',
				),
				'facebook_url'  => array(
					'label' => '„Facebook“ nuoroda',
					'type'  => 'url',
				),
				'instagram_url' => array(
					'label' => '„Instagram“ nuoroda',
					'type'  => 'url',
				),
			),
		),
		'straipsniai' => array(
			'title'  => 'Straipsniai',
			'intro'  => 'Straipsnių skiltį galima bet kada vėl įjungti — parašyti straipsniai neištrinami.',
			'fields' => array(
				'blog_mode' => array(
					'label'   => 'Straipsnių skiltis',
					'type'    => 'select',
					'options' => array(
						'on'     => 'Rodoma (meniu punktas ir straipsnių sąrašas)',
						'hidden' => 'Paslėpta (sąrašo nėra; jau parašyti straipsniai atsidaro tik per tiesioginę nuorodą ar „Google“)',
						'off'    => 'Visiškai išjungta (visi straipsnių adresai nukreipiami į pradžios puslapį)',
					),
				),
			),
		),
		'nukreipimai' => array(
			'title'  => 'Nukreipimai',
			'intro'  => 'Seni adresai, kurių nebėra. Po vieną eilutėje: /senas-adresas => /naujas-adresas',
			'fields' => array(
				'redirects' => array(
					'label' => 'Nukreipimai',
					'type'  => 'textarea',
				),
			),
		),
		'sekimas'     => array(
			'title'  => 'Reklamos sekimas',
			'intro'  => 'Kai lankytojas paspaudžia rezervacijos nuorodą, svetainė praneša apie tai „Google Analytics“, „Google Ads“ ir „Meta Pixel“ (jei jie svetainėje įdiegti). Pati rezervacija baigiama „Treatwell“ puslapyje, todėl fiksuojamas paspaudimas, o ne užbaigta rezervacija.',
			'fields' => array(
				'gads_id'      => array(
					'label' => '„Google Ads“ žymos ID',
					'type'  => 'text',
					'desc'  => 'Pvz. AW-123456789. Palikus tuščią, žyma neįterpiama.',
				),
				'gads_send_to' => array(
					'label' => '„Google Ads“ konversijos žyma (send_to)',
					'type'  => 'text',
					'desc'  => 'Pvz. AW-123456789/AbCdEfGhIj. Siunčiama paspaudus rezervacijos nuorodą.',
				),
				'ga4_event'    => array(
					'label' => '„Google Analytics“ įvykio pavadinimas',
					'type'  => 'text',
				),
				'meta_event'   => array(
					'label' => '„Meta Pixel“ įvykio pavadinimas',
					'type'  => 'text',
					'desc'  => 'Standartinis įvykis, pvz. Schedule arba Lead.',
				),
				'track_calls'  => array(
					'label' => 'Fiksuoti ir telefono numerio paspaudimus',
					'type'  => 'checkbox',
				),
			),
		),
		'seo'         => array(
			'title'  => 'Paieškos sistemoms',
			'intro'  => 'Kiekvienam puslapiui antraštę ir aprašymą galima nurodyti atskirai — puslapio redagavimo lange, skiltyje „Paieškos sistemoms (SEO)“.',
			'fields' => array(
				'seo_description' => array(
					'label' => 'Numatytasis svetainės aprašymas',
					'type'  => 'textarea',
					'desc'  => 'Iki 160 ženklų. Naudojamas, kai puslapis neturi savo aprašymo.',
				),
				'seo_share_image' => array(
					'label' => 'Paveikslėlio adresas dalijantis nuoroda',
					'type'  => 'url',
					'desc'  => 'Rodomas, kai svetainės nuoroda dalijamasi „Facebook“ ar „Messenger“. Rekomenduojamas dydis 1200 × 630.',
				),
			),
		),
	);
}

/** Išvalo ir patikrina iš formos gautas reikšmes. */
function mg_sanitize_settings( $input ): array {
	$input    = is_array( $input ) ? $input : array();
	$current  = mg_settings( true );
	$defaults = mg_defaults();
	$out      = $current;

	foreach ( mg_settings_schema() as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$raw = $input[ $key ] ?? null;

			switch ( $field['type'] ) {
				case 'checkbox':
					$out[ $key ] = empty( $raw ) ? 0 : 1;
					break;

				case 'url':
					$out[ $key ] = esc_url_raw( trim( (string) $raw ) );
					break;

				case 'email':
					$out[ $key ] = sanitize_email( (string) $raw );
					break;

				case 'textarea':
					$out[ $key ] = sanitize_textarea_field( (string) $raw );
					break;

				case 'select':
					$value       = sanitize_key( (string) $raw );
					$out[ $key ] = isset( $field['options'][ $value ] ) ? $value : $defaults[ $key ];
					break;

				case 'hours':
					$hours = array();
					foreach ( array_keys( mg_days() ) as $num ) {
						$day   = isset( $raw[ $num ] ) && is_array( $raw[ $num ] ) ? $raw[ $num ] : array();
						$open  = mg_sanitize_time( (string) ( $day['open'] ?? '' ) );
						$close = mg_sanitize_time( (string) ( $day['close'] ?? '' ) );

						$hours[ $num ] = array(
							'open'   => $open,
							'close'  => $close,
							'closed' => ( ! empty( $day['closed'] ) || '' === $open || '' === $close ) ? 1 : 0,
						);
					}
					$out[ $key ] = $hours;
					break;

				default:
					$out[ $key ] = sanitize_text_field( (string) $raw );
			}
		}
	}

	// Privalomos reikšmės negali likti tuščios.
	foreach ( array( 'booking_url', 'booking_label', 'brand_name', 'ga4_event', 'meta_event' ) as $required ) {
		if ( '' === trim( (string) $out[ $required ] ) ) {
			$out[ $required ] = $defaults[ $required ];
		}
	}

	return $out;
}

/** Laikas formatu HH:MM (arba tuščia eilutė, jei įvesta ne laikas). */
function mg_sanitize_time( string $value ): string {
	$value = trim( str_replace( '.', ':', $value ) );
	if ( preg_match( '/^(\d{1,2}):(\d{2})$/', $value, $m ) && (int) $m[1] < 24 && (int) $m[2] < 60 ) {
		return sprintf( '%02d:%02d', (int) $m[1], (int) $m[2] );
	}

	return '';
}

add_action(
	'admin_init',
	static function () {
		register_setting(
			'mg_settings_group',
			'mg_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'mg_sanitize_settings',
				'default'           => array(),
			)
		);
	}
);

add_action(
	'admin_menu',
	static function () {
		add_menu_page(
			'Masažo Gurmanai',
			'Masažo Gurmanai',
			'manage_options',
			'mg-settings',
			'mg_render_settings_page',
			'dashicons-heart',
			3
		);
		add_submenu_page( 'mg-settings', 'Nustatymai', 'Nustatymai', 'manage_options', 'mg-settings', 'mg_render_settings_page' );
		add_submenu_page( 'mg-settings', 'Pagalba', 'Pagalba', 'edit_pages', 'mg-help', 'mg_render_help_page' );
	}
);

/** Nustatymų puslapis. */
function mg_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$values = mg_settings( true );
	?>
	<div class="wrap mg-admin">
		<h1>Masažo Gurmanai — nustatymai</h1>
		<p>Čia keičiama informacija, kuri kartojasi visoje svetainėje. Puslapių tekstai ir nuotraukos keičiami: Puslapiai → „Redaguoti su Elementor“. Paslaugos ir kainos — meniu „Paslaugos“.</p>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'mg_settings_group' ); ?>

			<?php foreach ( mg_settings_schema() as $section_id => $section ) : ?>
				<h2 id="mg-<?php echo esc_attr( $section_id ); ?>"><?php echo esc_html( $section['title'] ); ?></h2>
				<?php if ( ! empty( $section['intro'] ) ) : ?>
					<p><?php echo esc_html( $section['intro'] ); ?></p>
				<?php endif; ?>

				<table class="form-table" role="presentation">
					<tbody>
					<?php foreach ( $section['fields'] as $key => $field ) : ?>
						<?php
						$id    = 'mg-field-' . $key;
						$name  = 'mg_settings[' . $key . ']';
						$value = $values[ $key ] ?? '';
						$desc  = $field['desc'] ?? '';
						?>
						<?php if ( 'hours' === $field['type'] ) : ?>
							<tr>
								<td colspan="2" style="padding-left:0">
									<table class="widefat striped" style="max-width:640px">
										<thead>
											<tr>
												<th scope="col">Diena</th>
												<th scope="col">Nuo</th>
												<th scope="col">Iki</th>
												<th scope="col">Nedirbame</th>
											</tr>
										</thead>
										<tbody>
										<?php foreach ( mg_days() as $num => $names ) : ?>
											<?php $day = isset( $value[ $num ] ) && is_array( $value[ $num ] ) ? $value[ $num ] : array(); ?>
											<tr>
												<th scope="row"><?php echo esc_html( $names[1] ); ?></th>
												<td>
													<label class="screen-reader-text" for="<?php echo esc_attr( $id . '-' . $num . '-open' ); ?>"><?php echo esc_html( $names[1] . ': dirbama nuo' ); ?></label>
													<input type="text" inputmode="numeric" class="small-text" style="width:6em" placeholder="08:00"
														id="<?php echo esc_attr( $id . '-' . $num . '-open' ); ?>"
														name="<?php echo esc_attr( $name . '[' . $num . '][open]' ); ?>"
														value="<?php echo esc_attr( (string) ( $day['open'] ?? '' ) ); ?>">
												</td>
												<td>
													<label class="screen-reader-text" for="<?php echo esc_attr( $id . '-' . $num . '-close' ); ?>"><?php echo esc_html( $names[1] . ': dirbama iki' ); ?></label>
													<input type="text" inputmode="numeric" class="small-text" style="width:6em" placeholder="20:00"
														id="<?php echo esc_attr( $id . '-' . $num . '-close' ); ?>"
														name="<?php echo esc_attr( $name . '[' . $num . '][close]' ); ?>"
														value="<?php echo esc_attr( (string) ( $day['close'] ?? '' ) ); ?>">
												</td>
												<td>
													<label for="<?php echo esc_attr( $id . '-' . $num . '-closed' ); ?>">
														<input type="checkbox" value="1"
															id="<?php echo esc_attr( $id . '-' . $num . '-closed' ); ?>"
															name="<?php echo esc_attr( $name . '[' . $num . '][closed]' ); ?>"
															<?php checked( ! empty( $day['closed'] ) ); ?>>
														<span class="screen-reader-text"><?php echo esc_html( $names[1] . ': ' ); ?></span>nedirbame
													</label>
												</td>
											</tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</td>
							</tr>
						<?php elseif ( 'checkbox' === $field['type'] ) : ?>
							<tr>
								<td colspan="2" style="padding-left:0">
									<label for="<?php echo esc_attr( $id ); ?>">
										<input type="checkbox" value="1" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php checked( ! empty( $value ) ); ?>>
										<?php echo esc_html( $field['label'] ); ?>
									</label>
								</td>
							</tr>
						<?php else : ?>
							<tr>
								<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
								<td>
									<?php if ( 'textarea' === $field['type'] ) : ?>
										<textarea class="large-text" rows="4" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>"<?php echo $desc ? ' aria-describedby="' . esc_attr( $id ) . '-desc"' : ''; ?>><?php echo esc_textarea( (string) $value ); ?></textarea>
									<?php elseif ( 'select' === $field['type'] ) : ?>
										<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
											<?php foreach ( $field['options'] as $opt_value => $opt_label ) : ?>
												<option value="<?php echo esc_attr( $opt_value ); ?>" <?php selected( (string) $value, (string) $opt_value ); ?>><?php echo esc_html( $opt_label ); ?></option>
											<?php endforeach; ?>
										</select>
									<?php else : ?>
										<input type="<?php echo esc_attr( in_array( $field['type'], array( 'url', 'email' ), true ) ? $field['type'] : 'text' ); ?>" class="regular-text"
											id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>"<?php echo $desc ? ' aria-describedby="' . esc_attr( $id ) . '-desc"' : ''; ?>>
									<?php endif; ?>
									<?php if ( $desc ) : ?>
										<p class="description" id="<?php echo esc_attr( $id ); ?>-desc"><?php echo esc_html( $desc ); ?></p>
									<?php endif; ?>
								</td>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endforeach; ?>

			<?php submit_button( 'Išsaugoti nustatymus' ); ?>
		</form>
	</div>
	<?php
}

/** Pagalbos puslapis: kur kas keičiama. */
function mg_render_help_page(): void {
	?>
	<div class="wrap mg-admin" style="max-width:820px">
		<h1>Masažo Gurmanai — pagalba</h1>

		<h2>Kur kas keičiama</h2>
		<table class="widefat striped">
			<thead>
				<tr><th scope="col">Ką norite pakeisti</th><th scope="col">Kur</th></tr>
			</thead>
			<tbody>
				<tr><td>Paslaugos pavadinimą, aprašymą, kainą, trukmę, akciją</td><td><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=mg_paslauga' ) ); ?>">Paslaugos</a> → pasirinkite paslaugą. Pakeitimas iškart matomas ir pradžios, ir paslaugų puslapyje.</td></tr>
				<tr><td>Telefoną, adresą, darbo laiką, rezervacijos nuorodą, įvertinimą</td><td><a href="<?php echo esc_url( admin_url( 'admin.php?page=mg-settings' ) ); ?>">Masažo Gurmanai → Nustatymai</a></td></tr>
				<tr><td>Puslapio tekstus ir nuotraukas</td><td><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">Puslapiai</a> → „Redaguoti su Elementor“. Spustelėkite norimą bloką — kairėje atsiras jo laukai.</td></tr>
				<tr><td>Meniu punktus</td><td><a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">Išvaizda → Meniu</a></td></tr>
				<tr><td>Logotipą ir svetainės ikoną</td><td><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">Išvaizda → Konfigūruoti</a> → Tinklalapio identifikacija</td></tr>
				<tr><td>Straipsnių skilties įjungimą</td><td><a href="<?php echo esc_url( admin_url( 'admin.php?page=mg-settings#mg-straipsniai' ) ); ?>">Masažo Gurmanai → Nustatymai → Straipsniai</a></td></tr>
			</tbody>
		</table>

		<h2>Nauji blokai puslapyje</h2>
		<p>Elementor redaktoriuje kairėje, valdiklių sąraše, yra kategorija <strong>„Masažo Gurmanai“</strong>. Joje — paruošti šios svetainės blokai (hero, paslaugos, žingsniai, atsiliepimai, kontaktai ir kiti). Įtempkite bloką į puslapį ir užpildykite laukus.</p>

		<h2>Rezervacija</h2>
		<p>Rezervacijos vyksta tik per „Treatwell“. Svetainė savo kalendoriaus neturi, todėl užsakymus, priminimus klientams ir apskaitą matote vienoje vietoje — „Treatwell“ paskyroje.</p>
		<p>Visi mygtukai „Rezervuoti“ veda į bendrąją rezervacijos nuorodą iš nustatymų. Jei kada prireiktų, prie paslaugos galima įrašyti atskirą nuorodą — tada tos paslaugos mygtukas ves į ją.</p>
	</div>
	<?php
}
