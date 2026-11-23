<?php
/**
 * Valdiklis „Puslapio antraštė“ — vidinių puslapių viršus (H1 ir trumpas įvadas).
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

defined( 'ABSPATH' ) || exit;

class Page_Hero extends Base {

	public function get_name() {
		return 'mg-page-hero';
	}

	public function get_title() {
		return 'Puslapio antraštė';
	}

	public function get_icon() {
		return 'eicon-t-letter';
	}

	public function get_keywords() {
		return array( 'antraštė', 'h1', 'puslapis', 'title' );
	}

	protected function register_controls() {
		$this->section( 'section_content', 'Turinys' );
		$this->text( 'title', 'Antraštė (H1)', '', 'Palikus tuščią, rodomas puslapio pavadinimas.' );
		$this->textarea( 'lead', 'Trumpas įvadas', '', '', 3 );
		$this->switcher( 'crumbs', 'Rodyti kelią „Pradžia / Puslapis“', true, 'Žodis „Pradžia“ keičiamas: Svetainės nustatymai → Masažo Gurmanai → Kiti svetainės tekstai.' );
		$this->button_controls( 'btn', 'Mygtukas', array( 'type' => 'none' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$title = trim( (string) $s['title'] );
		$lead  = trim( (string) $s['lead'] );
		$page  = get_the_title();

		if ( '' === $title ) {
			$title = $page;
		}

		echo '<header class="mg-section mg-pagehero">';
		echo '<div class="mg-container">';

		if ( 'yes' === $s['crumbs'] ) {
			echo '<nav class="mg-crumbs" aria-label="Kelias iki šio puslapio"><ol>';
			echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( '' !== mg_text( 'crumb_home_label' ) ? mg_text( 'crumb_home_label' ) : 'Pradžia' ) . '</a></li>';
			echo '<li aria-current="page">' . esc_html( '' !== $page ? $page : $title ) . '</li>';
			echo '</ol></nav>';
		}

		echo '<h1 class="mg-pagehero__title">' . mg_format_title( $title ) . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( '' !== $lead ) {
			echo '<p class="mg-pagehero__lead">' . nl2br( esc_html( $lead ) ) . '</p>';
		}

		$button = $this->button_html( $s, 'btn', array( 'place' => 'puslapio-antraste' ) );
		if ( '' !== $button ) {
			echo '<div class="mg-actions">' . $button . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div></header>';
	}
}
