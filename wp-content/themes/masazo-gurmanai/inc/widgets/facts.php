<?php
/**
 * Valdiklis „Faktų juosta“ — svarbiausia informacija viena eilute (įvertinimas, adresas, darbo laikas…).
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

class Facts extends Base {

	public function get_name() {
		return 'mg-facts';
	}

	public function get_title() {
		return 'Faktų juosta';
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	public function get_keywords() {
		return array( 'faktai', 'įvertinimas', 'adresas', 'darbo laikas', 'juosta' );
	}

	/** Dalis duomenų imama iš nustatymų — pakeitus juos, juosta atsinaujina pati. */
	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_controls() {
		$this->section( 'section_content', 'Faktai' );
		$this->note( 'info', 'Įvertinimas, adresas, darbo laikas ir telefonas imami iš <strong>Svetainės nustatymai → Masažo Gurmanai</strong> (arba Masažo Gurmanai → Nustatymai). Pasirinkus „Savas tekstas“, reikšmę įrašote čia.' );

		$repeater = new Repeater();
		$repeater->add_control(
			'source',
			array(
				'label'   => 'Kas rodoma',
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'rating'  => 'Įvertinimas (iš nustatymų)',
					'address' => 'Adresas (iš nustatymų)',
					'hours'   => 'Darbo laikas (iš nustatymų)',
					'phone'   => 'Telefonas (iš nustatymų)',
					'custom'  => 'Savas tekstas',
				),
				'default' => 'custom',
			)
		);
		$repeater->add_control( 'icon', array_merge( self::icon_control( 'tag' ), array( 'condition' => array( 'source' => 'custom' ) ) ) );
		$repeater->add_control( 'value', array_merge( self::repeater_text( 'Reikšmė (ryškiai)', '' ), array( 'condition' => array( 'source' => 'custom' ) ) ) );
		$repeater->add_control( 'label', array_merge( self::repeater_text( 'Paaiškinimas (mažesniu šriftu)', '' ), array( 'condition' => array( 'source' => 'custom' ) ) ) );
		$repeater->add_control(
			'url',
			array(
				'label'     => 'Nuoroda (nebūtina)',
				'type'      => Controls_Manager::URL,
				'options'   => false,
				'default'   => array( 'url' => '' ),
				'condition' => array( 'source' => 'custom' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '<# var t={rating:"Įvertinimas",address:"Adresas",hours:"Darbo laikas",phone:"Telefonas"}; #>{{{ t[source] || value }}}',
				'default'     => array(
					array( 'source' => 'rating' ),
					array( 'source' => 'address' ),
					array( 'source' => 'hours' ),
					array(
						'source' => 'custom',
						'icon'   => 'tag',
						'value'  => 'Masažai nuo 29 €',
						'label'  => '30–90 min trukmės',
						'url'    => array( 'url' => '/paslaugos' ),
					),
				),
			)
		);
		$this->end_controls_section();
	}

	/** Vieno fakto duomenys: icon, value, label, url, sr (papildomas tekstas ekrano skaitytuvui). */
	private function resolve( array $item ): array {
		$source = (string) ( $item['source'] ?? 'custom' );

		switch ( $source ) {
			case 'rating':
				$value = trim( (string) mg_get( 'rating_value' ) );
				return array(
					'icon'  => 'star',
					'value' => '' !== $value ? $value . ' iš 5' : '',
					'label' => mg_get( 'rating_count' ) . ' atsiliepimai „' . mg_get( 'rating_source' ) . '“',
					'url'   => (string) mg_get( 'rating_url' ),
				);

			case 'address':
				return array(
					'icon'  => 'map-pin',
					'value' => (string) mg_get( 'address_street' ),
					'label' => trim( mg_get( 'address_city' ) . ', ' . mg_get( 'address_note' ), ', ' ),
					'url'   => (string) mg_get( 'map_url' ),
				);

			case 'hours':
				return array(
					'icon'  => 'clock',
					'value' => mg_hours_short(),
					'label' => 'Darbo laikas',
					'url'   => '',
				);

			case 'phone':
				return array(
					'icon'  => 'phone',
					'value' => mg_phone_display(),
					'label' => 'Skambinkite',
					'url'   => mg_phone_href(),
				);

			default:
				return array(
					'icon'  => (string) ( $item['icon'] ?? '' ),
					'value' => trim( (string) ( $item['value'] ?? '' ) ),
					'label' => trim( (string) ( $item['label'] ?? '' ) ),
					'url'   => mg_local_url( is_array( $item['url'] ?? null ) ? (string) ( $item['url']['url'] ?? '' ) : '' ),
				);
		}
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = is_array( $s['items'] ) ? $s['items'] : array();
		$out   = '';

		foreach ( $items as $item ) {
			$fact = $this->resolve( $item );
			if ( '' === $fact['value'] ) {
				continue;
			}

			$inner = '';
			if ( '' !== $fact['icon'] ) {
				$inner .= '<span class="mg-facts__icon">' . mg_icon( $fact['icon'] ) . '</span>';
			}
			$inner .= '<span class="mg-facts__text"><span class="mg-facts__value">' . esc_html( $fact['value'] ) . '</span>';
			if ( '' !== $fact['label'] ) {
				$inner .= '<span class="mg-facts__label">' . esc_html( $fact['label'] ) . '</span>';
			}
			$inner .= '</span>';

			if ( '' !== $fact['url'] ) {
				$sr    = mg_opens_new_tab( $fact['url'] ) ? mg_sr( ' (atsidaro naujame skirtuke)' ) : '';
				$inner = '<a class="mg-facts__link"' . mg_link_attrs( $fact['url'] ) . '>' . $inner . $sr . '</a>';
			} else {
				$inner = '<span class="mg-facts__link">' . $inner . '</span>';
			}

			$out .= '<li class="mg-facts__item">' . $inner . '</li>';
		}

		if ( '' === $out ) {
			echo $this->editor_notice( 'Pridėkite bent vieną faktą.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		echo '<section class="mg-facts" aria-label="Svarbiausia informacija"><div class="mg-container"><ul class="mg-facts__list">' . $out . '</ul></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
