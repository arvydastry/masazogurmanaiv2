<?php
/**
 * Valdiklis „Žingsniai“ — sunumeruoti žingsniai (pvz. kaip užsiregistruoti).
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

class Steps extends Base {

	public function get_name() {
		return 'mg-steps';
	}

	public function get_title() {
		return 'Žingsniai';
	}

	public function get_icon() {
		return 'eicon-number-field';
	}

	public function get_keywords() {
		return array( 'žingsniai', 'eiga', 'kaip', 'registracija' );
	}

	protected function register_controls() {
		$this->heading_controls(
			array(
				'title' => 'Kaip užsiregistruoti',
			),
			false,
			true
		);

		$this->section( 'section_content', 'Žingsniai' );

		$repeater = new Repeater();
		$repeater->add_control( 'title', self::repeater_text( 'Pavadinimas', '' ) );
		$repeater->add_control( 'text', self::repeater_text( 'Tekstas', '', true ) );

		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title' => 'Pasirinkite masažą',
						'text'  => 'Peržiūrėkite paslaugas ir kainas. Jei abejojate, kuris masažas tinka Jums, paskambinkite: patarsiu.',
					),
					array(
						'title' => 'Pasirinkite laiką',
						'text'  => 'Laisvus laikus matysite „Treatwell“ kalendoriuje. Rezervacija užtrunka apie minutę.',
					),
					array(
						'title' => 'Gaukite priminimą',
						'text'  => 'Patvirtinimą ir priminimą apie vizitą gausite SMS žinute ir el. paštu.',
					),
				),
			)
		);
		$this->textarea( 'note', 'Pastaba po žingsniais (nebūtina)', '', '', 3 );
		$this->button_controls( 'btn', 'Mygtukas', array( 'type' => 'none' ) );
		$this->end_controls_section();

		$this->layout_controls( array( 'tone' => 'alt' ) );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = is_array( $s['items'] ) ? $s['items'] : array();

		$list = '';
		$num  = 0;
		$h    = 'h' . $this->item_level( $s );
		foreach ( $items as $item ) {
			$title = trim( (string) ( $item['title'] ?? '' ) );
			$text  = trim( (string) ( $item['text'] ?? '' ) );
			if ( '' === $title && '' === $text ) {
				continue;
			}
			++$num;
			$list .= '<li class="mg-steps__item" data-mg-reveal>';
			$list .= '<span class="mg-steps__num" aria-hidden="true">' . $num . '</span>';
			if ( '' !== $title ) {
				$list .= '<' . $h . ' class="mg-steps__title">' . esc_html( $title ) . '</' . $h . '>';
			}
			if ( '' !== $text ) {
				$list .= '<p class="mg-steps__text">' . nl2br( esc_html( $text ) ) . '</p>';
			}
			$list .= '</li>';
		}

		if ( '' === $list ) {
			echo $this->editor_notice( 'Pridėkite bent vieną žingsnį.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		echo $this->open_section( $s, 'steps' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container">';
		echo $this->heading_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ol class="mg-steps__list mg-steps__list--' . (int) min( 4, $num ) . '">' . $list . '</ol>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$note = trim( (string) $s['note'] );
		if ( '' !== $note ) {
			echo '<p class="mg-note">' . mg_icon( 'info' ) . '<span>' . nl2br( esc_html( $note ) ) . '</span></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$button = $this->button_html( $s, 'btn', array( 'place' => 'zingsniai' ) );
		if ( '' !== $button ) {
			echo '<div class="mg-actions mg-section__more">' . $button . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div></section>';
	}
}
