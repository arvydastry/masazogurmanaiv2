<?php
/**
 * Valdiklis „Nuotraukų galerija“ — kelios nuotraukos tinkleliu (pvz. masažo kabinetas).
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

class Gallery extends Base {

	public function get_name() {
		return 'mg-gallery';
	}

	public function get_title() {
		return 'Nuotraukų galerija';
	}

	public function get_icon() {
		return 'eicon-gallery-masonry';
	}

	public function get_keywords() {
		return array( 'galerija', 'nuotraukos', 'kabinetas', 'studija' );
	}

	protected function register_controls() {
		$this->heading_controls( array( 'title' => 'Masažo studija' ) );

		$this->section( 'section_content', 'Nuotraukos' );
		$this->note( 'info', 'Kiekvienai nuotraukai Media bibliotekoje įrašykite <strong>alternatyvų tekstą</strong> — trumpą aprašymą, kurį išgirs ekrano skaitytuvu besinaudojantys lankytojai.' );
		$this->add_control(
			'images',
			array(
				'label'      => 'Nuotraukos',
				'type'       => Controls_Manager::GALLERY,
				'default'    => array(),
				'show_label' => false,
			)
		);
		$this->switcher( 'lightbox', 'Paspaudus atidaryti didelę nuotrauką', true );
		$this->end_controls_section();

		$this->layout_controls();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$images = is_array( $s['images'] ) ? $s['images'] : array();
		$group  = 'mg-' . $this->get_id();

		$list  = '';
		$count = 0;
		foreach ( $images as $image ) {
			$id = isset( $image['id'] ) ? absint( $image['id'] ) : 0;
			if ( ! $id || ! wp_attachment_is_image( $id ) ) {
				continue;
			}
			++$count;
			$img = wp_get_attachment_image(
				$id,
				'large',
				false,
				array(
					'sizes' => '(min-width: 1024px) 380px, (min-width: 640px) 46vw, 92vw',
				)
			);

			if ( 'yes' === $s['lightbox'] ) {
				$full  = (string) wp_get_attachment_image_url( $id, 'full' );
				$alt   = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
				$label = 'Padidinti nuotrauką' . ( '' !== $alt ? ': ' . $alt : '' );
				$list .= '<li class="mg-gallery__item" data-mg-reveal><a href="' . esc_url( $full ) . '" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="' . esc_attr( $group ) . '" aria-label="' . esc_attr( $label ) . '">' . $img . '</a></li>';
			} else {
				$list .= '<li class="mg-gallery__item" data-mg-reveal>' . $img . '</li>';
			}
		}

		if ( '' === $list ) {
			echo $this->editor_notice( 'Pasirinkite nuotraukas.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		echo $this->open_section( $s, 'gallery' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container">';
		echo $this->heading_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ul class="mg-gallery__list mg-gallery__list--' . (int) min( 4, $count ) . '">' . $list . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div></section>';
	}
}
