<?php
/**
 * Vienkartiniai duomenų atnaujinimai, kai atnaujinama tema.
 *
 * Kiekvienas atnaujinimas paleidžiamas vieną kartą (žymima nustatyme „mg_data_version“)
 * ir keičia tik tai, kas vis dar sutampa su ankstesniu pradiniu turiniu — ranka pakeistų
 * reikšmių neliečia.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function () {
		$done = (string) get_option( 'mg_data_version', '' );
		if ( '' !== $done && version_compare( $done, MG_VERSION, '>=' ) ) {
			return;
		}

		// Nauja svetainė (pradinis turinys dar nesukurtas) — atnaujinti nėra ko.
		if ( '' === $done && ! get_option( 'mg_settings' ) ) {
			update_option( 'mg_data_version', MG_VERSION, false );
			return;
		}

		if ( '' === $done || version_compare( $done, '2.0.1', '<' ) ) {
			mg_migrate_201();
		}

		update_option( 'mg_data_version', MG_VERSION, false );
	},
	20
);

/**
 * 2.0.1: visi „Rezervuoti“ mygtukai veda į vieną „Treatwell“ nuorodą (trea.tw).
 */
function mg_migrate_201(): void {
	$new_url = 'https://trea.tw/HVeWMChwFZ2375fuC';

	// 1. Bendroji nuoroda — jei vis dar ankstesnė numatytoji.
	$settings = get_option( 'mg_settings' );
	if ( is_array( $settings ) && in_array( $settings['booking_url'] ?? '', array( '', 'https://www.treatwell.lt/salonas/masazo-gurmanai/' ), true ) ) {
		$settings['booking_url'] = $new_url;
		update_option( 'mg_settings', $settings );
		if ( function_exists( 'mg_settings' ) ) {
			mg_settings( true );
		}
	}

	// 2. Paslaugų tiesioginės nuorodos, sukurtos pradinio turinio, — išvalomos (naudojama bendroji).
	$services = get_posts(
		array(
			'post_type'   => MG_SERVICE_TYPE,
			'post_status' => 'any',
			'numberposts' => -1,
			'fields'      => 'ids',
		)
	);
	foreach ( $services as $id ) {
		$url = (string) get_post_meta( (int) $id, '_mg_booking_url', true );
		if ( 0 === strpos( $url, 'https://www.treatwell.lt/availability?venueId=480496' ) ) {
			update_post_meta( (int) $id, '_mg_booking_url', '' );
		}
	}

	// 3. Paslaugų puslapio sakinys apie „tos paslaugos laisvus laikus“.
	$page = get_page_by_path( 'paslaugos' );
	if ( $page ) {
		$raw = (string) get_post_meta( $page->ID, '_elementor_data', true );
		$old = 'Mygtukas „Rezervuoti“ iškart atveria tos paslaugos laisvus laikus.';
		$new = 'Mygtukas „Rezervuoti“ atveria rezervaciją „Treatwell“ sistemoje.';
		$data = '' !== $raw ? json_decode( $raw, true ) : null;
		if ( is_array( $data ) ) {
			$changed = false;
			$walk    = static function ( array &$items ) use ( &$walk, $old, $new, &$changed ) {
				foreach ( $items as &$item ) {
					if ( isset( $item['settings'] ) && is_array( $item['settings'] ) ) {
						foreach ( $item['settings'] as &$value ) {
							if ( is_string( $value ) && false !== strpos( $value, $old ) ) {
								$value   = str_replace( $old, $new, $value );
								$changed = true;
							}
						}
						unset( $value );
					}
					if ( ! empty( $item['elements'] ) && is_array( $item['elements'] ) ) {
						$walk( $item['elements'] );
					}
				}
				unset( $item );
			};
			$walk( $data );

			if ( $changed ) {
				update_post_meta( $page->ID, '_elementor_data', wp_slash( (string) wp_json_encode( $data ) ) );
				delete_post_meta( $page->ID, '_elementor_element_cache' );
				if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
					\Elementor\Plugin::$instance->files_manager->clear_cache();
				}
			}
		}
	}

	// 4. Talpykla (LiteSpeed), kad lankytojai iškart matytų naujas nuorodas.
	do_action( 'litespeed_purge_all' );
}
