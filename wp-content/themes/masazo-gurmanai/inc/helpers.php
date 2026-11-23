<?php
/**
 * Pagalbinės funkcijos: ikonos, nuorodos, mygtukai, telefonas, darbo laikas.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ikona (Phosphor Icons, „light“ storis; failai — assets/icons/*.svg).
 * Ikonos visada dekoratyvios (aria-hidden) — prasmę perteikia šalia esantis tekstas.
 */
function mg_icon( string $name, string $class = '' ): string {
	static $cache = array();

	$name = preg_replace( '/[^a-z0-9\-]/', '', strtolower( $name ) );
	if ( '' === $name ) {
		return '';
	}

	if ( ! array_key_exists( $name, $cache ) ) {
		$file           = MG_DIR . '/assets/icons/' . $name . '.svg';
		$cache[ $name ] = is_readable( $file ) ? (string) file_get_contents( $file ) : '';
	}

	if ( '' === $cache[ $name ] ) {
		return '';
	}

	$classes = trim( 'mg-icon ' . $class );

	return str_replace(
		'<svg ',
		'<svg class="' . esc_attr( $classes ) . '" aria-hidden="true" focusable="false" width="1em" height="1em" ',
		$cache[ $name ]
	);
}

/**
 * Ikonų sąrašas Elementor pasirinkimo laukams (pavadinimas => lietuviškas užrašas).
 */
function mg_icon_options(): array {
	return array(
		''                   => '— be ikonos —',
		'flower-lotus'       => 'Lotosas',
		'hand-heart'         => 'Ranka su širdimi',
		'hand-palm'          => 'Delnas',
		'hands-praying'      => 'Sudėtos rankos',
		'handshake'          => 'Rankos paspaudimas',
		'heartbeat'          => 'Širdies ritmas',
		'brain'              => 'Galva / mintys',
		'lightning'          => 'Energija',
		'first-aid-kit'      => 'Pirmoji pagalba',
		'bandaids'           => 'Pleistras',
		'bed'                => 'Poilsis',
		'smiley'             => 'Šypsena',
		'person'             => 'Žmogus',
		'person-arms-spread' => 'Žmogus (visas kūnas)',
		'person-simple-walk' => 'Ėjimas / kojos',
		'footprints'         => 'Pėdos',
		'barbell'            => 'Sportas',
		'leaf'               => 'Lapas',
		'plant'              => 'Augalas',
		'drop'               => 'Lašas',
		'waves'              => 'Bangos',
		'wind'               => 'Vėjas',
		'sparkle'            => 'Žybsnis',
		'spiral'             => 'Spiralė',
		'fire-simple'        => 'Šiluma',
		'moon-stars'         => 'Mėnulis',
		'sun-horizon'        => 'Saulė',
		'eye-slash'          => 'Užmerkta akis',
		'user-focus'         => 'Individualus dėmesys',
		'users-three'        => 'Žmonės',
		'target'             => 'Taikinys',
		'repeat'             => 'Kartojimas / programa',
		'arrows-clockwise'   => 'Ratas',
		'path'               => 'Kelias',
		'steps'              => 'Laiptai',
		'seal-check'         => 'Patvirtinta',
		'shield-check'       => 'Skydas',
		'medal'              => 'Medalis',
		'certificate'        => 'Sertifikatas',
		'check-circle'       => 'Varnelė apskritime',
		'check'              => 'Varnelė',
		'star'               => 'Žvaigždė',
		'gift'               => 'Dovana',
		'tag'                => 'Kaina / akcija',
		'currency-eur'       => 'Euras',
		'credit-card'        => 'Mokėjimo kortelė',
		'clock'              => 'Laikrodis',
		'timer'              => 'Trukmė',
		'calendar-check'     => 'Kalendorius (rezervacija)',
		'calendar-blank'     => 'Kalendorius',
		'bell-ringing'       => 'Priminimas',
		'device-mobile'      => 'Telefonas (išmanusis)',
		'phone'              => 'Telefono ragelis',
		'envelope-simple'    => 'Laiškas',
		'chat-circle-text'   => 'Pokalbis',
		'quotes'             => 'Kabutės',
		'map-pin'            => 'Vieta',
		'navigation-arrow'   => 'Maršrutas',
		'map-trifold'        => 'Žemėlapis',
		'compass'            => 'Kompasas',
		'signpost'           => 'Rodyklė (kelio ženklas)',
		'bus'                => 'Autobusas',
		'door-open'          => 'Durys',
		'buildings'          => 'Pastatai',
		'storefront'         => 'Salonas',
		'wheelchair'         => 'Prieinamumas',
		'info'               => 'Informacija',
		'question'           => 'Klausimas',
		'lock-simple'        => 'Spyna',
		'link-simple'        => 'Nuoroda',
		'arrow-right'        => 'Rodyklė dešinėn',
		'arrow-up-right'     => 'Rodyklė (išorinė nuoroda)',
		'facebook-logo'      => 'Facebook',
		'instagram-logo'     => 'Instagram',
		'google-logo'        => 'Google',
		'whatsapp-logo'      => 'WhatsApp',
		'messenger-logo'     => 'Messenger',
		'tiktok-logo'        => 'TikTok',
		'youtube-logo'       => 'YouTube',
	);
}

/** Tekstas, matomas tik ekrano skaitytuvams. */
function mg_sr( string $text ): string {
	return '<span class="mg-sr">' . esc_html( $text ) . '</span>';
}

/** Leidžiamos paprastos žymos trumpuose tekstuose (paryškinimas, nuoroda, nauja eilutė). */
function mg_kses_inline( string $text ): string {
	return wp_kses(
		$text,
		array(
			'strong' => array(),
			'b'      => array(),
			'em'     => array(),
			'i'      => array(),
			'br'     => array(),
			'span'   => array( 'class' => true ),
			'a'      => array(
				'href'   => true,
				'target' => true,
				'rel'    => true,
				'class'  => true,
			),
		)
	);
}

/** Antraštė su išskirtais žodžiais: „Masažai, *pritaikyti Jums*“ → žodžiai tarp žvaigždučių išskiriami. */
function mg_format_title( string $title ): string {
	return (string) preg_replace( '/\*([^*]+)\*/u', '<em>$1</em>', esc_html( $title ) );
}

/**
 * Vidinė nuoroda: „/paslaugos“ → pilnas adresas.
 * Puslapių turinyje laikomi adresai be domeno, todėl svetainę galima perkelti niekur nieko nekeičiant.
 */
function mg_local_url( string $url ): string {
	$url = trim( $url );
	if ( '' === $url ) {
		return '';
	}
	if ( '/' === $url[0] && ( 1 === strlen( $url ) || '/' !== $url[1] ) ) {
		return home_url( $url );
	}

	return $url;
}

/** Teksto lauką (po vieną įrašą eilutėje) paverčia netuščių eilučių masyvu. */
function mg_lines( string $text ): array {
	$lines = preg_split( '/\r\n|\r|\n/', $text );
	$lines = is_array( $lines ) ? $lines : array();
	$lines = array_map( 'trim', $lines );

	return array_values(
		array_filter(
			$lines,
			static function ( $line ) {
				return '' !== $line;
			}
		)
	);
}

/* -------------------------------------------------------------------------
 * Telefonas
 * ---------------------------------------------------------------------- */

/** Telefono numeris nuorodai (tel:) — tik skaitmenys ir pliusas. */
function mg_phone_href( string $phone = '' ): string {
	$phone = '' !== $phone ? $phone : (string) mg_get( 'phone' );
	$clean = preg_replace( '/[^0-9+]/', '', $phone );

	return '' !== (string) $clean ? 'tel:' . $clean : '';
}

/** Telefono numeris rodymui: +37066888481 → +370 668 88481. */
function mg_phone_display( string $phone = '' ): string {
	$phone = '' !== $phone ? $phone : (string) mg_get( 'phone' );
	$clean = (string) preg_replace( '/[^0-9+]/', '', $phone );

	if ( preg_match( '/^\+370(\d{3})(\d{5})$/', $clean, $m ) ) {
		return '+370 ' . $m[1] . ' ' . $m[2];
	}

	return $phone;
}

/* -------------------------------------------------------------------------
 * Nuorodos ir rezervacija
 * ---------------------------------------------------------------------- */

/** Ar nuoroda veda į kitą svetainę. */
function mg_is_external( string $url ): bool {
	if ( '' === $url || '#' === $url[0] || '/' === $url[0] ) {
		return false;
	}
	if ( 0 === strpos( $url, 'tel:' ) || 0 === strpos( $url, 'mailto:' ) ) {
		return false;
	}

	$host = (string) wp_parse_url( $url, PHP_URL_HOST );
	$home = (string) wp_parse_url( home_url(), PHP_URL_HOST );

	return '' !== $host && strtolower( $host ) !== strtolower( $home );
}

/**
 * Ar nuoroda veda į rezervaciją: bendroji rezervacijos nuoroda iš nustatymų, trumpoji „Treatwell“ nuoroda (trea.tw)
 * arba „Treatwell“ rezervacijos puslapis. Salono puslapis su atsiliepimais (/salonas/…) rezervacija nelaikomas,
 * kad paspaudimas „Skaityti atsiliepimus“ nebūtų skaičiuojamas kaip rezervacija.
 */
function mg_is_booking_url( string $url ): bool {
	$url = trim( $url );
	if ( '' === $url ) {
		return false;
	}

	$booking = trim( (string) mg_get( 'booking_url' ) );
	if ( '' !== $booking && untrailingslashit( $url ) === untrailingslashit( $booking ) ) {
		return true;
	}

	$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	$path = strtolower( (string) wp_parse_url( $url, PHP_URL_PATH ) );

	if ( 'trea.tw' === $host || 0 === strpos( $host, 'book.treatwell.' ) ) {
		return true;
	}

	return false !== strpos( $host, 'treatwell.' ) && 0 !== strpos( $path, '/salonas/' );
}

/** Bendroji rezervacijos nuoroda (arba konkrečios paslaugos, jei nurodyta). */
function mg_booking_url( string $override = '' ): string {
	$override = trim( $override );
	if ( '' !== $override ) {
		return $override;
	}

	return (string) mg_get( 'booking_url' );
}

/**
 * Nuorodos atributai: href, o išorinėms nuorodoms — target/rel ir žyma sekimui.
 *
 * @param string $url     Adresas.
 * @param string $service Paslaugos pavadinimas (rezervacijos įvykiui).
 * @param string $place   Kur puslapyje yra nuoroda (antraštė, hero, paslaugos…).
 */
function mg_link_attrs( string $url, string $service = '', string $place = '' ): string {
	$attrs = ' href="' . esc_url( $url ) . '"';

	if ( mg_is_booking_url( $url ) ) {
		$attrs .= ' data-mg-book="1"';
		if ( '' !== $service ) {
			$attrs .= ' data-mg-service="' . esc_attr( $service ) . '"';
		}
		if ( '' !== $place ) {
			$attrs .= ' data-mg-place="' . esc_attr( $place ) . '"';
		}
		if ( mg_get( 'booking_new_tab' ) ) {
			$attrs .= ' target="_blank" rel="noopener"';
		}
	} elseif ( mg_is_external( $url ) ) {
		$attrs .= ' target="_blank" rel="noopener"';
	}

	return $attrs;
}

/** Ar nuoroda atsidarys naujame skirtuke (kad apie tai būtų pranešta ekrano skaitytuvui). */
function mg_opens_new_tab( string $url ): bool {
	if ( mg_is_booking_url( $url ) ) {
		return (bool) mg_get( 'booking_new_tab' );
	}

	return mg_is_external( $url );
}

/**
 * Mygtukas (nuoroda).
 *
 * @param array $args label, url, style (primary|outline|ghost|link), size (''|sm|lg), icon, icon_pos (end|start),
 *                    service, place, class, aria_label.
 */
function mg_button( array $args ): string {
	$a = wp_parse_args(
		$args,
		array(
			'label'      => '',
			'url'        => '',
			'style'      => 'primary',
			'size'       => '',
			'icon'       => '',
			'icon_pos'   => 'end',
			'service'    => '',
			'place'      => '',
			'class'      => '',
			'aria_label' => '',
		)
	);

	$label = trim( (string) $a['label'] );
	$url   = trim( (string) $a['url'] );
	if ( '' === $label || '' === $url ) {
		return '';
	}

	$classes = array( 'mg-btn', 'mg-btn--' . sanitize_html_class( (string) $a['style'] ) );
	if ( '' !== $a['size'] ) {
		$classes[] = 'mg-btn--' . sanitize_html_class( (string) $a['size'] );
	}
	if ( '' !== $a['class'] ) {
		$classes[] = (string) $a['class'];
	}

	$icon = '' !== $a['icon'] ? mg_icon( (string) $a['icon'], 'mg-btn__icon' ) : '';
	$sr   = mg_opens_new_tab( $url ) ? mg_sr( ' (atsidaro naujame skirtuke)' ) : '';
	$aria = '' !== $a['aria_label'] ? ' aria-label="' . esc_attr( (string) $a['aria_label'] . ( mg_opens_new_tab( $url ) ? ' (atsidaro naujame skirtuke)' : '' ) ) . '"' : '';

	$inner = '<span class="mg-btn__label">' . esc_html( $label ) . '</span>';
	$inner = 'start' === $a['icon_pos'] ? $icon . $inner : $inner . $icon;

	return '<a class="' . esc_attr( implode( ' ', $classes ) ) . '"' . mg_link_attrs( $url, (string) $a['service'], (string) $a['place'] ) . $aria . '>' . $inner . $sr . '</a>';
}

/** Pagrindinis rezervacijos mygtukas su bendra etikete (viena etiketė visoje svetainėje). */
function mg_booking_button( array $args = array() ): string {
	$a = wp_parse_args(
		$args,
		array(
			'label' => (string) mg_get( 'booking_label' ),
			'url'   => mg_booking_url(),
			'style' => 'primary',
			'icon'  => 'arrow-up-right',
		)
	);

	return mg_button( $a );
}

/* -------------------------------------------------------------------------
 * Adresas ir darbo laikas
 * ---------------------------------------------------------------------- */

/** Visas adresas viena eilute. */
function mg_address_line(): string {
	$parts = array_filter(
		array(
			trim( (string) mg_get( 'address_street' ) ),
			trim( (string) mg_get( 'address_city' ) ),
		)
	);

	return implode( ', ', $parts );
}

/** Savaitės dienos: numeris (1–7) => [trumpinys, pavadinimas, schema.org pavadinimas]. */
function mg_days(): array {
	return array(
		1 => array( 'I', 'Pirmadienis', 'Monday' ),
		2 => array( 'II', 'Antradienis', 'Tuesday' ),
		3 => array( 'III', 'Trečiadienis', 'Wednesday' ),
		4 => array( 'IV', 'Ketvirtadienis', 'Thursday' ),
		5 => array( 'V', 'Penktadienis', 'Friday' ),
		6 => array( 'VI', 'Šeštadienis', 'Saturday' ),
		7 => array( 'VII', 'Sekmadienis', 'Sunday' ),
	);
}

/** Laikas rodymui: 08:00 → 8:00. */
function mg_time_display( string $time ): string {
	return (string) preg_replace( '/^0(\d:)/', '$1', trim( $time ) );
}

/**
 * Darbo laikas, sugrupuotas iš eilės einančiomis dienomis su tomis pačiomis valandomis.
 *
 * @return array<int, array{label:string, sr:string, value:string, closed:bool}>
 */
function mg_hours_rows(): array {
	$hours = mg_get( 'hours' );
	$hours = is_array( $hours ) ? $hours : array();
	$days  = mg_days();
	$rows  = array();
	$group = null;

	foreach ( $days as $num => $names ) {
		$day    = isset( $hours[ $num ] ) && is_array( $hours[ $num ] ) ? $hours[ $num ] : array();
		$closed = ! empty( $day['closed'] ) || empty( $day['open'] ) || empty( $day['close'] );
		$value  = $closed ? 'Nedirbame' : mg_time_display( (string) $day['open'] ) . '–' . mg_time_display( (string) $day['close'] );

		if ( null !== $group && $group['value'] === $value ) {
			$group['to'] = $num;
		} else {
			if ( null !== $group ) {
				$rows[] = $group;
			}
			$group = array(
				'from'   => $num,
				'to'     => $num,
				'value'  => $value,
				'closed' => $closed,
			);
		}
	}
	if ( null !== $group ) {
		$rows[] = $group;
	}

	$out = array();
	foreach ( $rows as $row ) {
		$from = $days[ $row['from'] ];
		$to   = $days[ $row['to'] ];
		if ( $row['from'] === $row['to'] ) {
			$label = $from[0];
			$sr    = $from[1];
		} else {
			$label = $from[0] . '–' . $to[0];
			$sr    = $from[1] . ' – ' . mb_strtolower( $to[1] );
		}
		$out[] = array(
			'label'  => $label,
			'sr'     => $sr,
			'value'  => $row['value'],
			'closed' => $row['closed'],
		);
	}

	return $out;
}

/** Darbo laiko sąrašo HTML (naudojamas poraštėje ir kontaktų valdiklyje). */
function mg_hours_html( string $class = '' ): string {
	$rows = mg_hours_rows();
	if ( ! $rows ) {
		return '';
	}

	$html = '<dl class="' . esc_attr( trim( 'mg-hours ' . $class ) ) . '">';
	foreach ( $rows as $row ) {
		$html .= '<div class="mg-hours__row' . ( $row['closed'] ? ' is-closed' : '' ) . '">';
		$html .= '<dt><span aria-hidden="true">' . esc_html( $row['label'] ) . '</span>' . mg_sr( $row['sr'] ) . '</dt>';
		$html .= '<dd>' . esc_html( $row['value'] ) . '</dd>';
		$html .= '</div>';
	}
	$html .= '</dl>';

	return $html;
}

/** Trumpas darbo laikas viena eilute (pirma dirbama grupė), pvz. „I–V 8:00–20:00“. */
function mg_hours_short(): string {
	foreach ( mg_hours_rows() as $row ) {
		if ( ! $row['closed'] ) {
			return $row['label'] . ' ' . $row['value'];
		}
	}

	return '';
}

/* -------------------------------------------------------------------------
 * Paveikslėliai
 * ---------------------------------------------------------------------- */

/**
 * Paveikslėlis iš Elementor „media“ lauko reikšmės (['id' => .., 'url' => ..]).
 *
 * @param mixed  $media Elementor lauko reikšmė.
 * @param string $size  WordPress dydis.
 * @param array  $attrs Papildomi <img> atributai (class, alt, sizes…).
 */
function mg_media_img( $media, string $size = 'large', array $attrs = array() ): string {
	if ( ! is_array( $media ) ) {
		return '';
	}

	$id  = isset( $media['id'] ) ? absint( $media['id'] ) : 0;
	$url = isset( $media['url'] ) ? (string) $media['url'] : '';

	// „loading“ ir „fetchpriority“ čia nenurodomi sąmoningai: juos pagal paveikslėlio vietą puslapyje
	// prideda pats WordPress arba Elementor (pirmas didelis paveikslėlis — iškart, kiti — atidėtai).

	if ( $id && wp_attachment_is_image( $id ) ) {
		if ( ! isset( $attrs['alt'] ) || '' === $attrs['alt'] ) {
			unset( $attrs['alt'] ); // Leidžiame WordPress paimti alt iš Media bibliotekos.
		}

		return wp_get_attachment_image( $id, $size, false, $attrs );
	}

	if ( '' === $url ) {
		return '';
	}

	$html = '<img src="' . esc_url( $url ) . '"';
	foreach ( $attrs as $key => $value ) {
		$html .= ' ' . esc_attr( (string) $key ) . '="' . esc_attr( (string) $value ) . '"';
	}
	if ( ! isset( $attrs['alt'] ) ) {
		$html .= ' alt=""';
	}

	return $html . '>';
}

/** Žvaigždutės (dekoratyvios — reikšmę perteikia tekstas šalia). */
function mg_stars( int $count = 5 ): string {
	$count = max( 0, min( 5, $count ) );

	return '<span class="mg-stars" aria-hidden="true">' . str_repeat( mg_icon( 'star-fill' ), $count ) . '</span>';
}
