<?php
/**
 * Pradinio turinio sukūrimas.
 *
 * Paleidžiama: „Masažo Gurmanai → Pradinis turinys“ arba komanda `wp mg setup`.
 *
 * Ką daro:
 *   1. įrašo nustatymus (kontaktai, „Treatwell“ nuoroda, darbo laikas);
 *   2. pritaiko Elementor globalius stilius (spalvos, šriftai);
 *   3. įkelia temos nuotraukas į Media biblioteką;
 *   4. sukuria paslaugas (mygtukas „Rezervuoti“ veda į bendrąją rezervacijos nuorodą);
 *   5. puslapius užpildo Elementor turiniu (esami puslapiai randami pagal adresą, nauji nekuriami dukart);
 *   6. priskiria pradžios puslapį ir sukuria meniu.
 *
 * Saugu paleisti kelis kartus: tai, kas jau sukurta ar redaguota, NEPERRAŠOMA,
 * nebent pažymėta „perrašyti“ (`--force`).
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

/** Temos nuotraukos: failas => [pavadinimas, alternatyvus tekstas]. */
function mg_setup_images(): array {
	return array(
		'povilas-maliukevicius-portretas.webp'           => array( 'Povilas Maliukevičius (portretas)', 'Masažuotojas Povilas Maliukevičius' ),
		'povilas-maliukevicius.jpg'                      => array( 'Povilas Maliukevičius', 'Povilas Maliukevičius' ),
		'masazo-kabinetas-1.jpg'                         => array( 'Masažo kabinetas 1', 'Masažo kabinetas: masažo stalas ant oranžinio kilimo' ),
		'masazo-kabinetas-2.jpg'                         => array( 'Masažo kabinetas 2', 'Masažo kabinetas žaliomis sienomis: masažo stalas, krėslas ir drabužių kabykla' ),
		'masazo-kabinetas-3.jpg'                         => array( 'Masažo kabinetas 3', 'Masažo stalas ir komoda su priemonėmis masažo kabinete' ),
		'iejimas-lass-pietvakariu-centras.jpg'           => array( 'Įėjimas į LASS pietvakarių centrą', 'Įėjimas į pastatą „LASS pietvakarių centras“ Savanorių pr. 206, Kaune' ),
		'kuponas-klasikinis-nugaros-masazas.jpg'         => array( 'Kuponas: klasikinis nugaros masažas', '' ),
		'kuponas-klasikinis-viso-kuno-masazas.jpg'       => array( 'Kuponas: klasikinis viso kūno masažas', '' ),
		'kuponas-atpalaiduojantis-viso-kuno-masazas.jpg' => array( 'Kuponas: atpalaiduojantis viso kūno masažas', '' ),
		'kuponas-sportinis-masazas.jpg'                  => array( 'Kuponas: sportinis masažas', '' ),
		'kuponas-limfodrenazinis-masazas.jpg'            => array( 'Kuponas: limfodrenažinis masažas', '' ),
		'kuponas-lomi-lomi-masazas.jpg'                  => array( 'Kuponas: Lomi Lomi masažas', '' ),
		'kuponas-atpalaiduojantis-nugaros-masazas.jpg'   => array( 'Kuponas: atpalaiduojantis nugaros masažas', '' ),
		'kuponas-dovanu-cekis.jpg'                       => array( 'Dovanų čekis', '' ),
		'masazo-gurmanai-dalijimuisi.jpg'                => array( 'Masažo Gurmanai (paveikslėlis dalijimuisi)', 'Masažo Gurmanai – išskirtinė masažo patirtis' ),
	);
}

/** Įkelia temos nuotrauką į Media biblioteką (vieną kartą) ir grąžina jos ID. */
function mg_setup_import_image( string $file ): int {
	static $cache = array();

	if ( isset( $cache[ $file ] ) ) {
		return $cache[ $file ];
	}

	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_mg_import_file', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $file, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $existing ) {
		$cache[ $file ] = (int) $existing[0];
		return $cache[ $file ];
	}

	$path = MG_DIR . '/assets/import/' . $file;
	if ( ! is_readable( $path ) ) {
		return 0;
	}

	$upload = wp_upload_bits( $file, null, (string) file_get_contents( $path ) );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$images = mg_setup_images();
	$meta   = $images[ $file ] ?? array( $file, '' );
	$type   = wp_check_filetype( $upload['file'] );

	$id = wp_insert_attachment(
		array(
			'post_title'     => $meta[0],
			'post_mime_type' => (string) $type['type'],
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( ! $id || is_wp_error( $id ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_mg_import_file', $file );
	if ( '' !== $meta[1] ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $meta[1] );
	}

	$cache[ $file ] = (int) $id;

	return $cache[ $file ];
}

/**
 * Paruošia valdiklio nustatymus Elementor formatui:
 * – array( 'img' => 'failas' ) pakeičia Media įrašu;
 * – kartotiniams elementams prideda _id.
 *
 * @param mixed  $value Reikšmė.
 * @param string $seed  Tekstas pastoviems identifikatoriams generuoti.
 * @return mixed
 */
function mg_setup_resolve( $value, string $seed ) {
	if ( ! is_array( $value ) ) {
		return $value;
	}

	if ( isset( $value['img'] ) && 1 === count( $value ) ) {
		$id = mg_setup_import_image( (string) $value['img'] );

		return array(
			'id'  => $id,
			'url' => $id ? (string) wp_get_attachment_url( $id ) : '',
		);
	}

	$is_list = array_is_list( $value );
	$out     = array();
	foreach ( $value as $key => $item ) {
		$resolved = mg_setup_resolve( $item, $seed . ':' . $key );
		// Kartotinio lauko elementas (ne Media įrašas) — reikia unikalaus _id.
		if ( $is_list && is_array( $resolved ) && ! array_key_exists( 'id', $resolved ) && ! isset( $resolved['_id'] ) ) {
			$resolved = array_merge( array( '_id' => substr( md5( $seed . ':' . $key ), 0, 7 ) ), $resolved );
		}
		$out[ $key ] = $resolved;
	}

	return $out;
}

/** Sudaro Elementor puslapio struktūrą: kiekvienas valdiklis — atskirame viso pločio konteineryje. */
function mg_setup_build_elements( string $slug, array $widgets ): array {
	$elements = array();

	foreach ( $widgets as $index => $widget ) {
		list( $type, $settings ) = $widget;
		$seed = $slug . ':' . $index . ':' . $type;

		$elements[] = array(
			'id'       => substr( md5( 'c:' . $seed ), 0, 7 ),
			'elType'   => 'container',
			'isInner'  => false,
			'settings' => array(
				'content_width'  => 'full',
				'flex_direction' => 'column',
				'css_classes'    => 'mg-wrap',
				'flex_gap'       => array(
					'unit'     => 'px',
					'size'     => 0,
					'column'   => '0',
					'row'      => '0',
					'isLinked' => true,
				),
				'padding'        => array(
					'unit'     => 'px',
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => true,
				),
			),
			'elements' => array(
				array(
					'id'         => substr( md5( 'w:' . $seed ), 0, 7 ),
					'elType'     => 'widget',
					'widgetType' => $type,
					'settings'   => mg_setup_resolve( $settings, $seed ),
					'elements'   => array(),
				),
			),
		);
	}

	return $elements;
}

/** Išsaugo Elementor turinį puslapiui. */
function mg_setup_save_elementor( int $post_id, array $elements ): bool {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	// Esamuose puslapiuose gali būti įrašytas ankstesnės temos šablonas (pvz. page-paslaugos.php).
	// Elementor išsaugodamas jį pakeičia, todėl pirmiausia įsimename — jei kada reikėtų grįžti
	// prie senos temos, šablonai atkuriami automatiškai (žr. mg_restore_page_templates()).
	$template = (string) get_post_meta( $post_id, '_wp_page_template', true );
	if ( '' !== $template && 'default' !== $template && ! metadata_exists( 'post', $post_id, '_mg_prev_page_template' ) ) {
		update_post_meta( $post_id, '_mg_prev_page_template', $template );
	}

	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );

	$saved    = false;
	$document = \Elementor\Plugin::$instance->documents->get( $post_id, false );
	if ( $document ) {
		try {
			$saved = (bool) $document->save(
				array(
					'elements' => $elements,
					'settings' => array(),
				)
			);
		} catch ( \Throwable $e ) {
			$saved = false;
		}
	}

	if ( ! $saved ) {
		// Atsarginis kelias: įrašome tiesiogiai (tokiu pat formatu, kokį naudoja Elementor).
		update_post_meta( $post_id, '_elementor_data', wp_slash( (string) wp_json_encode( $elements ) ) );
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
		}
		delete_post_meta( $post_id, '_elementor_css' );
	}

	return true;
}

/**
 * Grįžtant prie kitos (senosios) temos — puslapiams grąžinami jų ankstesni šablonai,
 * kad sena tema veiktų lygiai taip, kaip iki pakeitimo.
 */
function mg_restore_page_templates(): void {
	$pages = get_posts(
		array(
			'post_type'   => 'page',
			'post_status' => 'any',
			'numberposts' => -1,
			'fields'      => 'ids',
			'meta_key'    => '_mg_prev_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);

	foreach ( $pages as $page_id ) {
		$previous = (string) get_post_meta( $page_id, '_mg_prev_page_template', true );
		if ( '' !== $previous ) {
			update_post_meta( $page_id, '_wp_page_template', $previous );
		}
	}
}
add_action( 'switch_theme', 'mg_restore_page_templates' );

/** Ar puslapis jau turi Elementor turinį. */
function mg_setup_has_elementor( int $post_id ): bool {
	$data = get_post_meta( $post_id, '_elementor_data', true );
	if ( is_string( $data ) ) {
		$data = json_decode( $data, true );
	}

	return is_array( $data ) && count( $data ) > 0;
}

/* -------------------------------------------------------------------------
 * Žingsniai
 * ---------------------------------------------------------------------- */

function mg_setup_settings( array &$log ): void {
	$saved = get_option( 'mg_settings', null );

	if ( is_array( $saved ) && $saved ) {
		$log[] = 'Nustatymai jau yra — nekeista.';
		return;
	}

	$settings              = mg_defaults();
	$settings['redirects'] = '/kursai => /paslaugos#programos';

	// Reklamos žymos — tik tikroje svetainėje (vietinėje aplinkoje statistika neteršiama).
	if ( 'production' === wp_get_environment_type() ) {
		$settings['gads_id']      = 'AW-17832357898';
		$settings['gads_send_to'] = 'AW-17832357898/vEvbCPG0vZscEIrgkLdC';
	}

	update_option( 'mg_settings', $settings );
	mg_settings( true );
	$log[] = 'Nustatymai įrašyti (kontaktai, „Treatwell“ nuoroda, darbo laikas).';
}

/** Paveikslėlis, rodomas dalijantis svetainės nuoroda („Facebook“, „Messenger“ ir pan.). */
function mg_setup_share_image( array &$log ): void {
	$settings = mg_settings( true );
	if ( '' !== trim( (string) $settings['seo_share_image'] ) ) {
		return;
	}

	$id = mg_setup_import_image( 'masazo-gurmanai-dalijimuisi.jpg' );
	if ( ! $id ) {
		return;
	}

	$settings['seo_share_image'] = (string) wp_get_attachment_url( $id );
	update_option( 'mg_settings', $settings );
	mg_settings( true );
	$log[] = 'Nustatytas paveikslėlis, rodomas dalijantis svetainės nuoroda.';
}

function mg_setup_services( bool $force, array &$log ): void {
	$existing = get_posts(
		array(
			'post_type'   => MG_SERVICE_TYPE,
			'post_status' => 'any',
			'numberposts' => -1,
			'fields'      => 'ids',
		)
	);

	if ( $existing && ! $force ) {
		$log[] = 'Paslaugos jau yra (' . count( $existing ) . ') — nekeista.';
		return;
	}

	$by_key = array();
	foreach ( $existing as $id ) {
		$key = (string) get_post_meta( $id, '_mg_seed_key', true );
		if ( '' !== $key ) {
			$by_key[ $key ] = (int) $id;
		}
	}

	$seed    = require MG_DIR . '/inc/seed/services.php';
	$created = 0;
	$updated = 0;

	foreach ( $seed as $row ) {
		$post = array(
			'post_type'   => MG_SERVICE_TYPE,
			'post_status' => 'publish',
			'post_title'  => $row['title'],
			'menu_order'  => (int) $row['order'],
		);

		if ( isset( $by_key[ $row['key'] ] ) ) {
			$post['ID'] = $by_key[ $row['key'] ];
			$id         = wp_update_post( $post, true );
			++$updated;
		} else {
			$id = wp_insert_post( $post, true );
			++$created;
		}
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}

		$is_program = 'program' === $row['type'];
		$meta       = array(
			'_mg_seed_key'    => $row['key'],
			'_mg_type'        => $is_program ? 'program' : 'single',
			'_mg_duration'    => $row['duration'],
			'_mg_price'       => $row['price'],
			'_mg_old_price'   => '',
			'_mg_promo_note'  => '',
			'_mg_desc'        => $row['desc'],
			'_mg_bullets'     => implode( "\n", $row['bullets'] ),
			'_mg_cta'         => $is_program ? 'call' : 'book',
			'_mg_booking_url' => '', // visos paslaugos naudoja bendrąją rezervacijos nuorodą (Nustatymai).
			'_mg_icon'        => $row['icon'],
			'_mg_featured'    => $row['featured'] ? '1' : '',
		);
		foreach ( $meta as $key => $value ) {
			update_post_meta( (int) $id, $key, $value );
		}
	}

	$log[] = sprintf( 'Paslaugos: sukurta %d, atnaujinta %d.', $created, $updated );
}

function mg_setup_pages( bool $force, array &$log ): void {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		$log[] = 'Puslapiai neužpildyti: neįjungtas „Elementor“. Įjunkite jį ir paleiskite dar kartą.';
		return;
	}

	$pages = require MG_DIR . '/inc/seed/pages.php';

	foreach ( $pages as $slug => $def ) {
		$page = get_page_by_path( $slug );

		if ( ! $page ) {
			$id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_title'  => $def['title'],
					'post_name'   => $slug,
				),
				true
			);
			if ( ! $id || is_wp_error( $id ) ) {
				$log[] = 'Nepavyko sukurti puslapio „' . $def['title'] . '“.';
				continue;
			}
			$page_id = (int) $id;
			$state   = 'sukurtas';
		} else {
			$page_id = (int) $page->ID;
			$state   = 'rastas';
		}

		if ( mg_setup_has_elementor( $page_id ) && ! $force ) {
			$log[] = 'Puslapis „' . $def['title'] . '“ (/' . $slug . ') jau turi Elementor turinį — nekeista.';
		} else {
			mg_setup_save_elementor( $page_id, mg_setup_build_elements( $slug, $def['widgets'] ) );
			$log[] = 'Puslapis „' . $def['title'] . '“ (/' . $slug . ') ' . $state . ' ir užpildytas.';
		}

		foreach ( array( '_mg_seo_title' => 'seo_title', '_mg_seo_desc' => 'seo_desc' ) as $meta_key => $def_key ) {
			if ( ! empty( $def[ $def_key ] ) && ( $force || '' === (string) get_post_meta( $page_id, $meta_key, true ) ) ) {
				update_post_meta( $page_id, $meta_key, $def[ $def_key ] );
			}
		}

		if ( ! empty( $def['front'] ) && ( 'page' !== get_option( 'show_on_front' ) || ! (int) get_option( 'page_on_front' ) ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $page_id );
			$log[] = 'Pradžios puslapiu nustatytas „' . $def['title'] . '“.';
		}
	}

	// Straipsnių sąrašo puslapis (jei yra) — kad įjungus skiltį jis rodytų straipsnius.
	$blog = get_page_by_path( 'straipsniai' );
	if ( $blog && ! (int) get_option( 'page_for_posts' ) ) {
		update_option( 'page_for_posts', (int) $blog->ID );
		$log[] = 'Straipsnių sąrašo puslapiu nustatytas „' . get_the_title( $blog ) . '“ (skiltis šiuo metu paslėpta).';
	}
}

function mg_setup_menus( array &$log ): void {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$locations = is_array( $locations ) ? $locations : array();

	$menus = array(
		'primary' => array(
			'Pagrindinis meniu',
			array(
				'paslaugos'    => 'Masažo paslaugos',
				'kuponai'      => 'Dovanų kuponai',
				'apie'         => 'Apie mane',
				'atsiliepimai' => 'Atsiliepimai',
				'kontaktai'    => 'Kontaktai',
			),
		),
		'footer'  => array(
			'Poraštės meniu',
			array(
				'paslaugos'    => 'Paslaugos',
				'kuponai'      => 'Dovanų kuponai',
				'apie'         => 'Apie mane',
				'atsiliepimai' => 'Atsiliepimai',
				'kontaktai'    => 'Kontaktai',
			),
		),
	);

	foreach ( $menus as $location => $menu ) {
		if ( ! empty( $locations[ $location ] ) && wp_get_nav_menu_object( (int) $locations[ $location ] ) ) {
			$log[] = 'Meniu vietai „' . $location . '“ jau priskirtas — nekeista.';
			continue;
		}

		list( $name, $items ) = $menu;

		$object  = wp_get_nav_menu_object( $name );
		$menu_id = $object ? (int) $object->term_id : wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			$log[] = 'Nepavyko sukurti meniu „' . $name . '“.';
			continue;
		}

		if ( ! $object ) {
			foreach ( $items as $slug => $label ) {
				$page = get_page_by_path( $slug );
				if ( ! $page ) {
					continue;
				}
				wp_update_nav_menu_item(
					(int) $menu_id,
					0,
					array(
						'menu-item-title'     => $label,
						'menu-item-object'    => 'page',
						'menu-item-object-id' => (int) $page->ID,
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
					)
				);
			}
		}

		$locations[ $location ] = (int) $menu_id;
		$log[]                  = 'Meniu „' . $name . '“ sukurtas ir priskirtas.';
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Visas pradinio turinio sukūrimas.
 *
 * @param bool $force Perrašyti jau esantį puslapių turinį ir paslaugas.
 * @return string[] Žurnalas.
 */
function mg_setup_run( bool $force = false ): array {
	$log = array();

	// Elementor dokumentus gali išsaugoti tik prisijungęs redaktorius — komandinėje eilutėje parenkame administratorių.
	if ( ! get_current_user_id() ) {
		$admins = get_users(
			array(
				'role'   => 'administrator',
				'number' => 1,
				'fields' => 'ID',
			)
		);
		if ( $admins ) {
			wp_set_current_user( (int) $admins[0] );
		}
	}

	mg_setup_settings( $log );
	mg_setup_share_image( $log );

	if ( mg_elementor_apply_kit( $force ) ) {
		$log[] = 'Elementor globalūs stiliai pritaikyti (spalvos, šriftai, turinio plotis).';
	}

	mg_setup_services( $force, $log );
	mg_setup_pages( $force, $log );
	mg_setup_menus( $log );

	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	flush_rewrite_rules();
	update_option( 'mg_setup_done', gmdate( 'c' ) );

	$log[] = 'Baigta.';

	return $log;
}

/* -------------------------------------------------------------------------
 * Administravimo puslapis
 * ---------------------------------------------------------------------- */

add_action(
	'admin_menu',
	static function () {
		add_submenu_page( 'mg-settings', 'Pradinis turinys', 'Pradinis turinys', 'manage_options', 'mg-setup', 'mg_render_setup_page' );
	},
	20
);

add_action(
	'admin_post_mg_setup',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Neturite teisės atlikti šio veiksmo.' );
		}
		check_admin_referer( 'mg_setup' );

		$force = ! empty( $_POST['mg_force'] );
		set_transient( 'mg_setup_log_' . get_current_user_id(), mg_setup_run( $force ), 300 );

		wp_safe_redirect( admin_url( 'admin.php?page=mg-setup&done=1' ) );
		exit;
	}
);

function mg_render_setup_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$log       = get_transient( 'mg_setup_log_' . get_current_user_id() );
	$elementor = did_action( 'elementor/loaded' );
	$services  = wp_count_posts( MG_SERVICE_TYPE );
	$pages     = require MG_DIR . '/inc/seed/pages.php';
	?>
	<div class="wrap mg-admin" style="max-width:820px">
		<h1>Masažo Gurmanai — pradinis turinys</h1>
		<p>Šis įrankis užpildo svetainę paruoštu turiniu: puslapius (pradžia, paslaugos, dovanų kuponai, apie mane, atsiliepimai, kontaktai), paslaugų sąrašą su kainomis ir rezervacijos mygtukais, meniu ir nuotraukas.</p>

		<?php if ( is_array( $log ) && $log ) : ?>
			<?php delete_transient( 'mg_setup_log_' . get_current_user_id() ); ?>
			<div class="notice notice-success inline"><p><strong>Atlikta.</strong></p></div>
			<ul class="mg-log">
				<?php foreach ( $log as $line ) : ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<h2>Dabartinė būklė</h2>
		<table class="widefat striped">
			<tbody>
				<tr>
					<th scope="row">Elementor</th>
					<td><?php echo $elementor ? '<span class="mg-status mg-status--ok">įjungtas</span>' : '<span class="mg-status mg-status--todo">neįjungtas</span> — be jo puslapiai nebus užpildyti.'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>
				<tr>
					<th scope="row">Paslaugos</th>
					<td><?php echo esc_html( (string) ( (int) ( $services->publish ?? 0 ) ) ); ?> paskelbtų</td>
				</tr>
				<?php foreach ( $pages as $slug => $def ) : ?>
					<?php $page = get_page_by_path( $slug ); ?>
					<tr>
						<th scope="row"><?php echo esc_html( $def['title'] ); ?> <code>/<?php echo esc_html( $slug ); ?></code></th>
						<td>
							<?php
							if ( ! $page ) {
								echo '<span class="mg-status mg-status--todo">puslapio nėra</span> — bus sukurtas.';
							} elseif ( mg_setup_has_elementor( (int) $page->ID ) ) {
								echo '<span class="mg-status mg-status--ok">užpildytas</span> — nebus keičiamas.';
							} else {
								echo '<span class="mg-status mg-status--todo">be Elementor turinio</span> — bus užpildytas.';
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:24px">
			<input type="hidden" name="action" value="mg_setup">
			<?php wp_nonce_field( 'mg_setup' ); ?>

			<p>
				<label for="mg-force">
					<input type="checkbox" id="mg-force" name="mg_force" value="1" aria-describedby="mg-force-desc">
					Perrašyti jau užpildytus puslapius ir paslaugas pradiniu turiniu
				</label>
			</p>
			<p class="description" id="mg-force-desc"><strong>Dėmesio:</strong> pažymėjus, šių puslapių Elementor pakeitimai ir pradinių paslaugų kainos bus pakeisti pradinėmis reikšmėmis. Ankstesnes puslapių versijas galima atkurti per „Versijos“. Nepažymėjus — sukuriama tik tai, ko trūksta.</p>

			<?php submit_button( 'Sukurti pradinį turinį', 'primary', 'submit', true, $elementor ? array() : array( 'disabled' => 'disabled' ) ); ?>
		</form>
	</div>
	<?php
}

/** Po temos įjungimo — priminimas sukurti turinį. */
add_action(
	'admin_notices',
	static function () {
		if ( get_option( 'mg_setup_done' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && false !== strpos( (string) $screen->id, 'mg-setup' ) ) {
			return;
		}
		echo '<div class="notice notice-info"><p><strong>Masažo Gurmanai:</strong> tema įjungta. Liko sukurti pradinį turinį — <a href="' . esc_url( admin_url( 'admin.php?page=mg-setup' ) ) . '">atidaryti „Pradinis turinys“</a>.</p></div>';
	}
);

/* -------------------------------------------------------------------------
 * WP-CLI
 * ---------------------------------------------------------------------- */

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Sukuria pradinį svetainės turinį.
	 *
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Perrašyti jau užpildytus puslapius ir paslaugas.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mg setup
	 *     wp mg setup --force
	 */
	WP_CLI::add_command(
		'mg setup',
		static function ( $args, $assoc_args ) {
			foreach ( mg_setup_run( ! empty( $assoc_args['force'] ) ) as $line ) {
				WP_CLI::log( $line );
			}
			WP_CLI::success( 'Pradinis turinys paruoštas.' );
		}
	);
}
