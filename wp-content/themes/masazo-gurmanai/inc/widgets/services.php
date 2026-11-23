<?php
/**
 * Valdiklis „Paslaugos“ — paslaugų kortelės arba kainoraštis.
 *
 * Paslaugos imamos iš meniu „Paslaugos“ (pavadinimas, kaina, trukmė, rezervacijos nuoroda),
 * todėl kainą užtenka pakeisti vienoje vietoje.
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

class Services extends Base {

	public function get_name() {
		return 'mg-services';
	}

	public function get_title() {
		return 'Paslaugos ir kainos';
	}

	public function get_icon() {
		return 'eicon-price-list';
	}

	public function get_keywords() {
		return array( 'paslaugos', 'kainos', 'kainoraštis', 'masažai', 'programos' );
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'title' => 'Populiariausi masažai',
				'lead'  => 'Pasirinkite masažą ir iškart rezervuokite Jums patogų laiką.',
			),
			false,
			true
		);

		$this->section( 'section_content', 'Paslaugos' );
		$this->note( 'info', 'Paslaugų pavadinimai, aprašymai, trukmės ir kainos laikomi vienoje vietoje — <a href="' . esc_url( admin_url( 'edit.php?post_type=' . MG_SERVICE_TYPE ) ) . '" target="_blank">Paslaugos</a> (atsidaro naujame skirtuke), todėl pakeitimas iškart matomas visuose puslapiuose. Čia pasirenkate, kurias paslaugas ir kaip rodyti. Mygtukų užrašai: Svetainės nustatymai → Masažo Gurmanai → Kiti svetainės tekstai.' );
		$this->select(
			'layout',
			'Išdėstymas',
			array(
				'cards' => 'Kortelės (tinka pradžios puslapiui)',
				'list'  => 'Kainoraštis su išskleidžiamais aprašymais',
			),
			'cards'
		);
		$this->select(
			'type',
			'Kurios paslaugos',
			array(
				'all'     => 'Visos',
				'single'  => 'Tik masažai (vienas vizitas)',
				'program' => 'Tik programos (keli vizitai)',
			),
			'single'
		);
		$this->switcher( 'featured', 'Tik pažymėtos „Rodyti pradžios puslapyje“', true );
		$this->add_control(
			'limit',
			array(
				'label'       => 'Kiek daugiausia rodyti',
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 50,
				'default'     => 6,
				'description' => '0 — rodyti visas.',
			)
		);
		$this->switcher( 'first_open', 'Pirmos paslaugos aprašymą rodyti išskleistą', false, 'Galioja tik kainoraščiui.' );
		$this->textarea( 'note', 'Pastaba virš sąrašo (nebūtina)', '', 'Pvz. apie nuolaidas rezervuojant ne piko valandomis.', 3 );
		$this->button_controls(
			'btn',
			'Mygtukas po sąrašu',
			array(
				'type'  => 'custom',
				'label' => 'Visos paslaugos ir kainos',
				'url'   => '/paslaugos',
			)
		);
		$this->end_controls_section();

		$this->layout_controls();
	}

	protected function render() {
		$s        = $this->get_settings_for_display();
		$layout   = 'list' === $s['layout'] ? 'list' : 'cards';
		$services = mg_get_services(
			array(
				'type'     => (string) $s['type'],
				'featured' => 'yes' === $s['featured'],
				'limit'    => (int) $s['limit'],
			)
		);

		if ( ! $services ) {
			echo $this->editor_notice( 'Pagal pasirinktus nustatymus paslaugų nerasta. Paslaugos pridedamos: Skydelis → Paslaugos.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$heading_id = 'mg-services-' . $this->get_id();
		$labelled   = '' !== trim( (string) $s['h_title'] ) ? ' aria-labelledby="' . esc_attr( $heading_id ) . '"' : '';

		echo $this->open_section( $s, 'services', 'mg-services--' . $layout, 'section', $labelled ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container">';
		echo $this->heading_html( $s, $heading_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-services__main">';

		$note = trim( (string) $s['note'] );
		if ( '' !== $note ) {
			echo '<p class="mg-note">' . mg_icon( 'info' ) . '<span>' . nl2br( esc_html( $note ) ) . '</span></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( 'cards' === $layout ) {
			echo '<ul class="mg-svc-grid">';
			foreach ( $services as $svc ) {
				echo mg_render_service_card( $svc, $this->item_level( $s ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</ul>';
		} else {
			echo '<ul class="mg-svc-list">';
			foreach ( $services as $index => $svc ) {
				echo mg_render_service_row( $svc, 0 === $index && 'yes' === $s['first_open'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</ul>';
		}

		$button = $this->button_html( $s, 'btn', array( 'style' => 'outline', 'place' => 'paslaugos' ) );
		if ( '' !== $button ) {
			echo '<div class="mg-actions mg-section__more' . ( 'cards' === $layout ? ' mg-actions--center' : '' ) . '">' . $button . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div></div></section>';
	}
}
