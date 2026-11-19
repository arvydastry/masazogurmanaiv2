<?php
/**
 * Valdiklis „Citata ir skaičiai“ — specialisto mintis, parašas ir keli skaičiai (patirtis, klientai…).
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

class Statement extends Base {

	public function get_name() {
		return 'mg-statement';
	}

	public function get_title() {
		return 'Citata ir skaičiai';
	}

	public function get_icon() {
		return 'eicon-blockquote';
	}

	public function get_keywords() {
		return array( 'citata', 'skaičiai', 'statistika', 'patirtis', 'apie' );
	}

	protected function register_controls() {
		$this->heading_controls( array( 'title' => 'Kodėl verta pasitikėti?' ) );

		$this->section( 'section_quote', 'Citata' );
		$this->textarea( 'quote', 'Citatos tekstas', '', 'Be kabučių — jos pridedamos automatiškai.', 4 );
		$this->text( 'author', 'Autorius', 'Povilas Maliukevičius' );
		$this->text( 'role', 'Pareigos', 'Masažuotojas' );
		$this->media( 'avatar', 'Autoriaus nuotrauka (nebūtina)' );
		$this->end_controls_section();

		$this->section( 'section_stats', 'Skaičiai' );
		$repeater = new Repeater();
		$repeater->add_control( 'value', self::repeater_text( 'Skaičius', '' ) );
		$repeater->add_control( 'label', self::repeater_text( 'Paaiškinimas', '' ) );
		$this->add_control(
			'stats',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ value }}} {{{ label }}}',
				'default'     => array(),
			)
		);
		$this->button_controls( 'btn', 'Mygtukas', array( 'type' => 'none' ) );
		$this->end_controls_section();

		$this->layout_controls();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$quote  = trim( (string) $s['quote'] );
		$author = trim( (string) $s['author'] );
		$role   = trim( (string) $s['role'] );
		$stats  = is_array( $s['stats'] ) ? $s['stats'] : array();

		echo $this->open_section( $s, 'statement' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container mg-statement__in">';
		echo $this->heading_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( '' !== $quote ) {
			echo '<figure class="mg-statement__quote" data-mg-reveal>';
			echo '<blockquote><p>„' . nl2br( esc_html( $quote ) ) . '“</p></blockquote>';
			if ( '' !== $author ) {
				$avatar = mg_media_img(
					$s['avatar'],
					'thumbnail',
					array(
						'class' => 'mg-statement__avatar',
						'alt'   => '',
					)
				);
				echo '<figcaption class="mg-statement__by">' . $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<span><span class="mg-statement__author">' . esc_html( $author ) . '</span>';
				if ( '' !== $role ) {
					echo '<span class="mg-statement__role">' . esc_html( $role ) . '</span>';
				}
				echo '</span></figcaption>';
			}
			echo '</figure>';
		}

		$list = '';
		foreach ( $stats as $stat ) {
			$value = trim( (string) ( $stat['value'] ?? '' ) );
			$label = trim( (string) ( $stat['label'] ?? '' ) );
			if ( '' === $value ) {
				continue;
			}
			// Vizualiai skaičius viršuje; ekrano skaitytuvui — „7200+ atliktų masažų“.
			$list .= '<li class="mg-stats__item"><span class="mg-stats__value">' . esc_html( $value ) . '</span> <span class="mg-stats__label">' . esc_html( $label ) . '</span></li>';
		}
		if ( '' !== $list ) {
			echo '<ul class="mg-stats">' . $list . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$button = $this->button_html( $s, 'btn', array( 'style' => 'outline', 'place' => 'citata' ) );
		if ( '' !== $button ) {
			echo '<div class="mg-actions">' . $button . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div></section>';
	}
}
