<?php
/**
 * Valdiklis „Dovanų kuponai“ — kuponų kortelės su kaina ir nuoroda į pirkimo puslapį.
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

class Vouchers extends Base {

	public function get_name() {
		return 'mg-vouchers';
	}

	public function get_title() {
		return 'Dovanų kuponai';
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_keywords() {
		return array( 'kuponai', 'dovanos', 'dovanų čekis' );
	}

	protected function register_controls() {
		$this->heading_controls( array(), false, true );

		$this->section( 'section_content', 'Kuponai' );
		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label'   => 'Nuotrauka',
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'id'  => 0,
					'url' => '',
				),
				'ai'      => array( 'active' => false ),
			)
		);
		$repeater->add_control( 'title', self::repeater_text( 'Pavadinimas', '' ) );
		$repeater->add_control( 'meta', self::repeater_text( 'Papildoma informacija', '', false ) );
		$repeater->add_control( 'price', self::repeater_text( 'Kaina', '' ) );
		$repeater->add_control(
			'url',
			array(
				'label'   => 'Nuoroda į pirkimo puslapį',
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
		$this->text( 'buy_label', 'Nuorodos tekstas kortelėje', 'Pirkti kuponą' );
		$this->textarea( 'note', 'Pastaba po kuponais (nebūtina)', '', '', 2 );
		$this->end_controls_section();

		$this->layout_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = is_array( $s['items'] ) ? $s['items'] : array();
		$buy   = trim( (string) $s['buy_label'] );
		$level = $this->item_level( $s );

		$list = '';
		foreach ( $items as $item ) {
			$title = trim( (string) ( $item['title'] ?? '' ) );
			if ( '' === $title ) {
				continue;
			}
			$meta  = trim( (string) ( $item['meta'] ?? '' ) );
			$price = trim( (string) ( $item['price'] ?? '' ) );
			$url   = mg_local_url( is_array( $item['url'] ?? null ) ? (string) ( $item['url']['url'] ?? '' ) : '' );
			$image = mg_media_img(
				$item['image'] ?? null,
				'medium_large',
				array(
					'alt'   => '',
					'sizes' => '(min-width: 1024px) 280px, (min-width: 640px) 45vw, 92vw',
				)
			);

			$inner = '<span class="mg-voucher__media">' . $image . '</span>';
			$inner .= '<span class="mg-voucher__body">';
			$inner .= '<span class="mg-voucher__title" role="heading" aria-level="' . $level . '">' . esc_html( $title ) . '</span>';
			if ( '' !== $meta ) {
				$inner .= '<span class="mg-voucher__meta">' . esc_html( $meta ) . '</span>';
			}
			$inner .= '<span class="mg-voucher__foot">';
			if ( '' !== $price ) {
				$inner .= '<span class="mg-price">' . mg_sr( 'Kaina: ' ) . esc_html( $price ) . '</span>';
			}
			if ( '' !== $url && '' !== $buy ) {
				$inner .= '<span class="mg-voucher__buy">' . esc_html( $buy ) . mg_icon( 'arrow-up-right' ) . '</span>';
			}
			$inner .= '</span></span>';

			if ( '' !== $url ) {
				$sr    = mg_opens_new_tab( $url ) ? mg_sr( ' (atsidaro naujame skirtuke)' ) : '';
				$list .= '<li data-mg-reveal><a class="mg-voucher"' . mg_link_attrs( $url ) . ' data-mg-voucher="' . esc_attr( $title ) . '">' . $inner . $sr . '</a></li>';
			} else {
				$list .= '<li data-mg-reveal><div class="mg-voucher">' . $inner . '</div></li>';
			}
		}

		if ( '' === $list ) {
			echo $this->editor_notice( 'Pridėkite bent vieną kuponą.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		echo $this->open_section( $s, 'vouchers' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container">';
		echo $this->heading_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ul class="mg-vouchers__list">' . $list . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$note = trim( (string) $s['note'] );
		if ( '' !== $note ) {
			echo '<p class="mg-note">' . mg_icon( 'info' ) . '<span>' . nl2br( esc_html( $note ) ) . '</span></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div></section>';
	}
}
