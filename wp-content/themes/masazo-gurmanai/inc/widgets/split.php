<?php
/**
 * Valdiklis „Tekstas ir nuotrauka“ — nuotrauka vienoje pusėje, antraštė, tekstas ir sąrašas kitoje.
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

class Split extends Base {

	public function get_name() {
		return 'mg-split';
	}

	public function get_title() {
		return 'Tekstas ir nuotrauka';
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_keywords() {
		return array( 'tekstas', 'nuotrauka', 'apie', 'istorija', 'sąrašas' );
	}

	protected function register_controls() {
		$this->heading_controls( array( 'title' => 'Antraštė' ), false, true );

		$this->section( 'section_image', 'Nuotrauka' );
		$this->media( 'image', 'Nuotrauka' );
		$this->text( 'image_alt', 'Nuotraukos aprašymas (neregintiems)', '', 'Trumpai, kas matoma nuotraukoje. Jei nuotrauka tik puošia — palikite tuščią.' );
		$this->select(
			'side',
			'Nuotraukos vieta',
			array(
				'left'  => 'Kairėje',
				'right' => 'Dešinėje',
			),
			'left'
		);
		$this->select(
			'ratio',
			'Nuotraukos forma',
			array(
				'portrait'  => 'Vertikali (4:5)',
				'landscape' => 'Horizontali (4:3)',
				'square'    => 'Kvadratinė',
				'natural'   => 'Originali (neapkarpoma)',
			),
			'portrait'
		);
		$this->end_controls_section();

		$this->section( 'section_content', 'Tekstas ir sąrašas' );
		$this->wysiwyg( 'text', 'Tekstas' );

		$repeater = new Repeater();
		$repeater->add_control( 'icon', self::icon_control( 'check-circle' ) );
		$repeater->add_control( 'title', self::repeater_text( 'Pavadinimas', '' ) );
		$repeater->add_control( 'text', self::repeater_text( 'Tekstas', '', true ) );

		$this->add_control(
			'items',
			array(
				'label'       => 'Sąrašas (nebūtina)',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(),
			)
		);
		$this->button_controls( 'btn', 'Mygtukas', array( 'type' => 'none' ) );
		$this->end_controls_section();

		$this->layout_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$side  = 'right' === $s['side'] ? 'right' : 'left';
		$ratio = in_array( $s['ratio'], array( 'portrait', 'landscape', 'square', 'natural' ), true ) ? $s['ratio'] : 'portrait';
		$image = mg_media_img(
			$s['image'],
			'large',
			array(
				'alt'   => (string) $s['image_alt'],
				'sizes' => '(min-width: 1024px) 560px, 92vw',
			)
		);

		echo $this->open_section( $s, 'split', 'mg-split--media-' . $side . ( '' === $image ? ' mg-split--no-media' : '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container mg-split__in">';

		if ( '' !== $image ) {
			echo '<figure class="mg-split__media mg-ratio--' . esc_attr( $ratio ) . '" data-mg-reveal>' . $image . '</figure>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<div class="mg-split__body">';
		echo $this->heading_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$text = trim( (string) $s['text'] );
		if ( '' !== $text ) {
			echo '<div class="mg-prose">' . wp_kses_post( $this->parse_text_editor( $text ) ) . '</div>';
		}

		$items = is_array( $s['items'] ) ? $s['items'] : array();
		$list  = '';
		$h     = 'h' . $this->item_level( $s );
		foreach ( $items as $item ) {
			$title = trim( (string) ( $item['title'] ?? '' ) );
			$body  = trim( (string) ( $item['text'] ?? '' ) );
			if ( '' === $title && '' === $body ) {
				continue;
			}
			$list .= '<li class="mg-iconlist__item">';
			$list .= '<span class="mg-iconlist__icon">' . mg_icon( '' !== ( $item['icon'] ?? '' ) ? (string) $item['icon'] : 'check-circle' ) . '</span>';
			$list .= '<div>';
			if ( '' !== $title ) {
				$list .= '<' . $h . ' class="mg-iconlist__title">' . esc_html( $title ) . '</' . $h . '>';
			}
			if ( '' !== $body ) {
				$list .= '<p class="mg-iconlist__text">' . nl2br( esc_html( $body ) ) . '</p>';
			}
			$list .= '</div></li>';
		}
		if ( '' !== $list ) {
			echo '<ul class="mg-iconlist">' . $list . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$button = $this->button_html( $s, 'btn', array( 'place' => 'tekstas-nuotrauka' ) );
		if ( '' !== $button ) {
			echo '<div class="mg-actions">' . $button . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div></div></section>';
	}
}
