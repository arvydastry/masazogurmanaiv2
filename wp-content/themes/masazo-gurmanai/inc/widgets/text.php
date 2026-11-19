<?php
/**
 * Valdiklis „Teksto blokas“ — antraštė ir laisvas tekstas siaurame, patogiame skaityti stulpelyje.
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

defined( 'ABSPATH' ) || exit;

class Text extends Base {

	public function get_name() {
		return 'mg-text';
	}

	public function get_title() {
		return 'Teksto blokas';
	}

	public function get_icon() {
		return 'eicon-text';
	}

	public function get_keywords() {
		return array( 'tekstas', 'pastraipa', 'aprašymas' );
	}

	protected function register_controls() {
		$this->heading_controls();

		$this->section( 'section_content', 'Tekstas' );
		$this->wysiwyg( 'text', 'Tekstas' );
		$this->select(
			'width',
			'Plotis',
			array(
				'narrow' => 'Siauras (patogu skaityti)',
				'wide'   => 'Visas turinio plotis',
			),
			'narrow'
		);
		$this->button_controls( 'btn', 'Mygtukas', array( 'type' => 'none' ) );
		$this->end_controls_section();

		$this->layout_controls();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$text = trim( (string) $s['text'] );

		echo $this->open_section( $s, 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container' . ( 'wide' === $s['width'] ? '' : ' mg-container--narrow' ) . '">';
		echo $this->heading_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( '' !== $text ) {
			echo '<div class="mg-prose">' . wp_kses_post( $this->parse_text_editor( $text ) ) . '</div>';
		}

		$button = $this->button_html( $s, 'btn', array( 'place' => 'tekstas' ) );
		if ( '' !== $button ) {
			echo '<div class="mg-actions">' . $button . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div></section>';
	}
}
