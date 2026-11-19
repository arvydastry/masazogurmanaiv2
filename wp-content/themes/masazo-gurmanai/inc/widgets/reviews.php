<?php
/**
 * Valdiklis „Atsiliepimai“ — bendras įvertinimas (iš nustatymų) ir klientų atsiliepimų kortelės.
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

class Reviews extends Base {

	public function get_name() {
		return 'mg-reviews';
	}

	public function get_title() {
		return 'Atsiliepimai';
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}

	public function get_keywords() {
		return array( 'atsiliepimai', 'įvertinimas', 'klientai', 'treatwell' );
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_controls() {
		$this->heading_controls( array( 'title' => 'Klientų patirtys' ) );

		$this->section( 'section_content', 'Atsiliepimai' );
		$this->switcher( 'show_rating', 'Rodyti bendrą įvertinimą', true, 'Įvertinimas ir atsiliepimų skaičius keičiami: Masažo Gurmanai → Nustatymai → Įvertinimas.' );
		$this->text( 'rating_link', 'Nuorodos į visus atsiliepimus tekstas', 'Skaityti visus atsiliepimus' );

		$repeater = new Repeater();
		$repeater->add_control( 'text', self::repeater_text( 'Atsiliepimas', '', true ) );
		$repeater->add_control( 'name', self::repeater_text( 'Vardas', '' ) );
		$repeater->add_control( 'context', self::repeater_text( 'Paslauga (nebūtina)', '' ) );
		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(),
			)
		);
		$this->select(
			'cols',
			'Stulpelių skaičius (plačiame ekrane)',
			array(
				'2' => '2',
				'3' => '3',
			),
			'3'
		);
		$this->end_controls_section();

		$this->layout_controls();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = is_array( $s['items'] ) ? $s['items'] : array();
		$cols  = '2' === (string) $s['cols'] ? '2' : '3';

		$rating       = trim( (string) mg_get( 'rating_value' ) );
		$rating_url   = (string) mg_get( 'rating_url' );
		$rating_block = '';

		if ( 'yes' === $s['show_rating'] && '' !== $rating ) {
			$meta  = mg_get( 'rating_count' ) . ' atsiliepimai „' . mg_get( 'rating_source' ) . '“';
			$inner = '<span class="mg-rating__value">' . esc_html( $rating ) . '</span>'
				. '<span class="mg-rating__side">' . mg_stars() . mg_sr( ' iš 5. ' ) . '<span class="mg-rating__meta">' . esc_html( $meta ) . '</span></span>';

			$rating_block = '<div class="mg-rating">' . $inner . '</div>';
			$link_label   = trim( (string) $s['rating_link'] );
			if ( '' !== $rating_url && '' !== $link_label ) {
				$rating_block .= mg_button(
					array(
						'label' => $link_label,
						'url'   => $rating_url,
						'style' => 'link',
						'icon'  => 'arrow-up-right',
					)
				);
			}
		}

		$list = '';
		foreach ( $items as $item ) {
			$text = trim( (string) ( $item['text'] ?? '' ) );
			$name = trim( (string) ( $item['name'] ?? '' ) );
			if ( '' === $text ) {
				continue;
			}
			$context = trim( (string) ( $item['context'] ?? '' ) );
			$initial = '' !== $name ? mb_strtoupper( mb_substr( $name, 0, 1 ) ) : '';

			$list .= '<li class="mg-review" data-mg-reveal><figure>';
			$list .= mg_stars() . mg_sr( 'Įvertinimas: 5 iš 5.' );
			$list .= '<blockquote><p>' . esc_html( $text ) . '</p></blockquote>';
			if ( '' !== $name ) {
				$list .= '<figcaption class="mg-review__by">';
				$list .= '<span class="mg-review__avatar" aria-hidden="true">' . esc_html( $initial ) . '</span>';
				$list .= '<span><span class="mg-review__name">' . esc_html( $name ) . '</span>';
				if ( '' !== $context ) {
					$list .= '<span class="mg-review__ctx">' . esc_html( $context ) . '</span>';
				}
				$list .= '</span></figcaption>';
			}
			$list .= '</figure></li>';
		}

		if ( '' === $list && '' === $rating_block ) {
			echo $this->editor_notice( 'Pridėkite bent vieną atsiliepimą.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		echo $this->open_section( $s, 'reviews' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container">';
		echo '<div class="mg-reviews__top">';
		echo $this->heading_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( '' !== $rating_block ) {
			echo '<div class="mg-reviews__rating">' . $rating_block . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
		if ( '' !== $list ) {
			echo '<ul class="mg-reviews__list mg-reviews__list--' . esc_attr( $cols ) . '">' . $list . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div></section>';
	}
}
