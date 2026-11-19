<?php
/**
 * Valdiklis „Nuorodų kortelės“ — didelės kortelės su nuotrauka ir nuoroda (pvz. programos, dovanų kuponai).
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

class Tiles extends Base {

	public function get_name() {
		return 'mg-tiles';
	}

	public function get_title() {
		return 'Nuorodų kortelės';
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_keywords() {
		return array( 'kortelės', 'nuorodos', 'programos', 'kuponai' );
	}

	protected function register_controls() {
		$this->heading_controls( array(), false, true );

		$this->section( 'section_content', 'Kortelės' );
		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label'   => 'Nuotrauka (nebūtina)',
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'id'  => 0,
					'url' => '',
				),
				'ai'      => array( 'active' => false ),
			)
		);
		$repeater->add_control( 'icon', array_merge( self::icon_control( '' ), array( 'description' => 'Rodoma, kai nėra nuotraukos.' ) ) );
		$repeater->add_control( 'title', self::repeater_text( 'Pavadinimas', '' ) );
		$repeater->add_control( 'text', self::repeater_text( 'Tekstas', '', true ) );
		$repeater->add_control( 'link_label', self::repeater_text( 'Nuorodos tekstas', 'Plačiau' ) );
		$repeater->add_control(
			'url',
			array(
				'label'   => 'Nuoroda',
				'type'    => Controls_Manager::URL,
				'options' => false,
				'default' => array( 'url' => '' ),
			)
		);
		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(),
			)
		);
		$this->end_controls_section();

		$this->layout_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = is_array( $s['items'] ) ? $s['items'] : array();

		$list  = '';
		$count = 0;
		$level = $this->item_level( $s );
		foreach ( $items as $item ) {
			$title = trim( (string) ( $item['title'] ?? '' ) );
			if ( '' === $title ) {
				continue;
			}
			++$count;
			$text  = trim( (string) ( $item['text'] ?? '' ) );
			$label = trim( (string) ( $item['link_label'] ?? '' ) );
			$url   = mg_local_url( is_array( $item['url'] ?? null ) ? (string) ( $item['url']['url'] ?? '' ) : '' );
			$image = mg_media_img(
				$item['image'] ?? null,
				'large',
				array(
					'alt'   => '',
					'sizes' => '(min-width: 768px) 300px, 92vw',
				)
			);

			$inner = '';
			if ( '' !== $image ) {
				$inner .= '<span class="mg-tile__media">' . $image . '</span>';
			}
			$inner .= '<span class="mg-tile__body">';
			if ( '' === $image && '' !== ( $item['icon'] ?? '' ) ) {
				$inner .= '<span class="mg-tile__icon">' . mg_icon( (string) $item['icon'] ) . '</span>';
			}
			$inner .= '<span class="mg-tile__title" role="heading" aria-level="' . $level . '">' . esc_html( $title ) . '</span>';
			if ( '' !== $text ) {
				$inner .= '<span class="mg-tile__text">' . nl2br( esc_html( $text ) ) . '</span>';
			}
			if ( '' !== $url && '' !== $label ) {
				$inner .= '<span class="mg-tile__more">' . esc_html( $label ) . mg_icon( mg_is_external( $url ) ? 'arrow-up-right' : 'arrow-right' ) . '</span>';
			}
			$inner .= '</span>';

			$class = 'mg-tile' . ( '' !== $image ? ' mg-tile--media' : '' );
			if ( '' !== $url ) {
				$sr    = mg_opens_new_tab( $url ) ? mg_sr( ' (atsidaro naujame skirtuke)' ) : '';
				$list .= '<li data-mg-reveal><a class="' . esc_attr( $class ) . '"' . mg_link_attrs( $url ) . '>' . $inner . $sr . '</a></li>';
			} else {
				$list .= '<li data-mg-reveal><div class="' . esc_attr( $class ) . '">' . $inner . '</div></li>';
			}
		}

		if ( '' === $list ) {
			echo $this->editor_notice( 'Pridėkite bent vieną kortelę.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		echo $this->open_section( $s, 'tiles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container">';
		echo $this->heading_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ul class="mg-tiles__list mg-tiles__list--' . (int) min( 3, $count ) . '">' . $list . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div></section>';
	}
}
