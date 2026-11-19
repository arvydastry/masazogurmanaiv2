<?php
/**
 * Valdiklis „Privalumai“ — ikona, pavadinimas ir trumpas tekstas keliuose stulpeliuose.
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

class Features extends Base {

	public function get_name() {
		return 'mg-features';
	}

	public function get_title() {
		return 'Privalumai (ikonos su tekstu)';
	}

	public function get_icon() {
		return 'eicon-icon-box';
	}

	public function get_keywords() {
		return array( 'privalumai', 'vertybės', 'ikonos', 'sąrašas' );
	}

	protected function register_controls() {
		$this->heading_controls( array( 'title' => 'Mano vertybės' ), false, true );

		$this->section( 'section_content', 'Punktai' );
		$repeater = new Repeater();
		$repeater->add_control( 'icon', self::icon_control( 'flower-lotus' ) );
		$repeater->add_control( 'title', self::repeater_text( 'Pavadinimas', '' ) );
		$repeater->add_control( 'text', self::repeater_text( 'Tekstas', '', true ) );
		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(),
			)
		);
		$this->select(
			'cols',
			'Stulpelių skaičius (plačiame ekrane)',
			array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
			),
			'2'
		);
		$this->end_controls_section();

		$this->layout_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = is_array( $s['items'] ) ? $s['items'] : array();
		$cols  = in_array( (string) $s['cols'], array( '2', '3', '4' ), true ) ? (string) $s['cols'] : '2';
		$h     = 'h' . $this->item_level( $s );

		$list = '';
		foreach ( $items as $item ) {
			$title = trim( (string) ( $item['title'] ?? '' ) );
			$text  = trim( (string) ( $item['text'] ?? '' ) );
			if ( '' === $title && '' === $text ) {
				continue;
			}
			$list .= '<li class="mg-features__item" data-mg-reveal>';
			if ( '' !== ( $item['icon'] ?? '' ) ) {
				$list .= '<span class="mg-features__icon">' . mg_icon( (string) $item['icon'] ) . '</span>';
			}
			$list .= '<div>';
			if ( '' !== $title ) {
				$list .= '<' . $h . ' class="mg-features__title">' . esc_html( $title ) . '</' . $h . '>';
			}
			if ( '' !== $text ) {
				$list .= '<p class="mg-features__text">' . nl2br( esc_html( $text ) ) . '</p>';
			}
			$list .= '</div></li>';
		}

		if ( '' === $list ) {
			echo $this->editor_notice( 'Pridėkite bent vieną punktą.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		echo $this->open_section( $s, 'features' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container">';
		echo $this->heading_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ul class="mg-features__list mg-features__list--' . esc_attr( $cols ) . '">' . $list . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div></section>';
	}
}
