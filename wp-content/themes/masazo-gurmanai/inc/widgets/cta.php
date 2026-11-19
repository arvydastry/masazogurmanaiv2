<?php
/**
 * Valdiklis „Kvietimas rezervuoti“ — ryški juosta su antrašte ir mygtukais.
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

defined( 'ABSPATH' ) || exit;

class Cta extends Base {

	public function get_name() {
		return 'mg-cta';
	}

	public function get_title() {
		return 'Kvietimas rezervuoti';
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_keywords() {
		return array( 'kvietimas', 'cta', 'rezervuoti', 'mygtukas' );
	}

	protected function register_controls() {
		$this->section( 'section_content', 'Turinys' );
		$this->text( 'title', 'Antraštė', 'Skirkite laiko sau', 'Žodžius tarp žvaigždučių, pvz. *sau*, galima išskirti auksine spalva.' );
		$this->textarea( 'text', 'Tekstas', 'Rezervacija internetu veikia visą parą. Laisvus laikus matysite iškart.', '', 3 );
		$this->button_controls( 'btn1', 'Pagrindinis mygtukas', array( 'type' => 'booking' ) );
		$this->button_controls( 'btn2', 'Antras mygtukas', array( 'type' => 'phone' ) );
		$this->end_controls_section();

		$this->layout_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$title = trim( (string) $s['title'] );
		$text  = trim( (string) $s['text'] );

		echo $this->open_section( $s, 'cta' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container"><div class="mg-cta__box" data-mg-reveal>';
		echo '<span class="mg-ornament" aria-hidden="true"></span>';
		if ( '' !== $title ) {
			echo '<h2 class="mg-title">' . mg_format_title( $title ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( '' !== $text ) {
			echo '<p class="mg-lead">' . nl2br( esc_html( $text ) ) . '</p>';
		}

		$buttons = $this->button_html( $s, 'btn1', array( 'size' => 'lg', 'place' => 'kvietimas' ) )
			. $this->button_html( $s, 'btn2', array( 'style' => 'outline', 'size' => 'lg', 'place' => 'kvietimas' ) );
		if ( '' !== $buttons ) {
			echo '<div class="mg-actions mg-actions--center">' . $buttons . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div></div></section>';
	}
}
