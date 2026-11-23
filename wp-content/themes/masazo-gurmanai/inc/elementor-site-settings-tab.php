<?php
/**
 * Elementor svetainės nustatymų skiltis „Masažo Gurmanai“ (žr. inc/elementor-site-settings.php).
 *
 * @package MasazoGurmanai
 */

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Core\Kits\Documents\Tabs\Tab_Base;

defined( 'ABSPATH' ) || exit;

class MG_Kit_Tab extends Tab_Base {

	const ID = 'mg-site';

	public function get_id() {
		return self::ID;
	}

	public function get_title() {
		return 'Masažo Gurmanai';
	}

	public function get_group() {
		return 'settings';
	}

	public function get_icon() {
		return 'eicon-site-identity';
	}

	public function get_help_url() {
		return admin_url( 'admin.php?page=mg-help' );
	}

	protected function register_tab_controls() {
		$settings = mg_settings( true );
		$schema   = mg_settings_schema();
		$first    = true;

		foreach ( mg_kit_sections() as $section_id ) {
			$section = $schema[ $section_id ];

			$this->start_controls_section(
				'mg_section_' . $section_id,
				array(
					'label' => $section['title'],
					'tab'   => $this->get_id(),
				)
			);

			if ( $first ) {
				$this->add_control(
					'mg_notice_' . $section_id,
					array(
						'type'       => Controls_Manager::ALERT,
						'alert_type' => 'info',
						'content'    => 'Čia keičiama antraštė, poraštė, telefono juosta, kontaktai ir kiti visoje svetainėje pasikartojantys dalykai. '
							. 'Pakeitimai matomi <a href="javascript: $e.run( \'document/save/default\' )">išsaugojus</a> ir '
							. '<a href="javascript: $e.run( \'preview/reload\' )">perkrovus peržiūrą</a>.<br><br>'
							. 'Kitur: logotipas — <em>Svetainės nustatymai → Site Identity</em>; meniu punktai — '
							. '<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '" target="_blank">Išvaizda → Meniu</a>; paslaugos ir kainos — '
							. '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . MG_SERVICE_TYPE ) ) . '" target="_blank">Paslaugos</a>.',
					)
				);
				$first = false;
			} elseif ( ! empty( $section['intro'] ) ) {
				$this->add_control(
					'mg_notice_' . $section_id,
					array(
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => esc_html( $section['intro'] ),
						'content_classes' => 'elementor-descriptor',
					)
				);
			}

			foreach ( $section['fields'] as $key => $field ) {
				$this->add_field( $key, $field, $settings );
			}

			$this->end_controls_section();
		}
	}

	/** Vienas nustatymo laukas kaip Elementor valdiklis. */
	private function add_field( string $key, array $field, array $settings ): void {
		$value = $settings[ $key ] ?? '';
		$desc  = $field['desc'] ?? '';

		switch ( $field['type'] ) {
			case 'hours':
				foreach ( mg_days() as $num => $day ) {
					$hours = is_array( $value ) && isset( $value[ $num ] ) ? $value[ $num ] : array();
					$this->add_control(
						'mg_hours_' . $num . '_heading',
						array(
							'type'      => Controls_Manager::HEADING,
							'label'     => $day[1],
							'separator' => 'before',
						)
					);
					$this->add_control(
						'mg_hours_' . $num . '_closed',
						array(
							'type'         => Controls_Manager::SWITCHER,
							'label'        => 'Nedirbame',
							'return_value' => 'yes',
							'default'      => ! empty( $hours['closed'] ) ? 'yes' : '',
						)
					);
					foreach ( array( 'open' => 'Nuo', 'close' => 'Iki' ) as $part => $label ) {
						$this->add_control(
							'mg_hours_' . $num . '_' . $part,
							array(
								'type'        => Controls_Manager::TEXT,
								'label'       => $label,
								'placeholder' => 'open' === $part ? '08:00' : '20:00',
								'default'     => (string) ( $hours[ $part ] ?? '' ),
								'condition'   => array( 'mg_hours_' . $num . '_closed!' => 'yes' ),
							)
						);
					}
				}
				return;

			case 'checkbox':
				$this->add_control(
					'mg_' . $key,
					array(
						'type'         => Controls_Manager::SWITCHER,
						'label'        => $field['label'],
						'return_value' => 'yes',
						'default'      => ! empty( $value ) ? 'yes' : '',
						'description'  => $desc,
					)
				);
				return;

			case 'select':
				$this->add_control(
					'mg_' . $key,
					array(
						'type'        => Controls_Manager::SELECT,
						'label'       => $field['label'],
						'options'     => $field['options'],
						'default'     => (string) $value,
						'label_block' => true,
						'description' => $desc,
					)
				);
				return;

			case 'textarea':
				$this->add_control(
					'mg_' . $key,
					array(
						'type'        => Controls_Manager::TEXTAREA,
						'label'       => $field['label'],
						'default'     => (string) $value,
						'rows'        => 4,
						'description' => $desc,
					)
				);
				return;

			default: // text, url, email.
				$this->add_control(
					'mg_' . $key,
					array(
						'type'        => Controls_Manager::TEXT,
						'label'       => $field['label'],
						'default'     => (string) $value,
						'label_block' => true,
						'input_type'  => 'email' === $field['type'] ? 'email' : ( 'url' === $field['type'] ? 'url' : 'text' ),
						'description' => $desc,
					)
				);
		}
	}

	/**
	 * Prieš įrašant Elementor rinkinį: „mg_…“ laukus įrašome į „mg_settings“ ir išimame iš rinkinio,
	 * kad reikšmės būtų laikomos vienoje vietoje (o ne dviejose, kurios galėtų išsiskirti).
	 */
	public function before_save( array $data ) {
		if ( empty( $data['settings'] ) || ! is_array( $data['settings'] ) ) {
			return $data;
		}

		$changed = array();
		foreach ( $data['settings'] as $id => $value ) {
			if ( 0 === strpos( (string) $id, 'mg_' ) ) {
				$changed[ $id ] = $value;
			}
		}
		if ( ! $changed ) {
			return $data;
		}

		// Juodraštis / automatinis išsaugojimas — svetainės dar nekeičiame.
		$status = $data['settings']['post_status'] ?? Document::STATUS_PUBLISH;
		if ( Document::STATUS_PUBLISH !== $status ) {
			return $data;
		}

		update_option( 'mg_settings', mg_kit_apply( $changed ) );
		mg_settings( true );

		foreach ( array_keys( $changed ) as $id ) {
			unset( $data['settings'][ $id ] );
		}

		do_action( 'litespeed_purge_all' );

		return $data;
	}
}
