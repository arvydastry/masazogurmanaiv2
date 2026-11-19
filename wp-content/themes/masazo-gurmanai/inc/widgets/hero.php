<?php
/**
 * Valdiklis „Hero“ — pradžios puslapio viršus: pavadinimas, šūkis, tekstas, mygtukai ir nuotrauka.
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

defined( 'ABSPATH' ) || exit;

class Hero extends Base {

	public function get_name() {
		return 'mg-hero';
	}

	public function get_title() {
		return 'Hero (pradžios viršus)';
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_keywords() {
		return array( 'hero', 'pradžia', 'viršus', 'masažo gurmanai' );
	}

	protected function register_controls() {
		$this->section( 'section_content', 'Turinys' );
		$this->text( 'name', 'Pavadinimas (rašytiniu šriftu)', 'Masažo Gurmanai' );
		$this->text( 'tagline', 'Šūkis po pavadinimu', 'Išskirtinė masažo patirtis' );
		$this->textarea( 'text', 'Tekstas', 'Profesionalūs, individualiai pritaikyti masažai Kaune. Juos atlieka masažuotojas Povilas Maliukevičius.', 'Trumpai: iki 20 žodžių.', 3 );
		$this->button_controls( 'btn1', 'Pagrindinis mygtukas', array( 'type' => 'booking' ) );
		$this->button_controls(
			'btn2',
			'Antras mygtukas',
			array(
				'type'  => 'custom',
				'label' => 'Paslaugos ir kainos',
				'url'   => '/paslaugos',
			)
		);
		$this->end_controls_section();

		$this->section( 'section_image', 'Nuotrauka' );
		$this->media( 'image', 'Nuotrauka', 'Geriausiai tinka portretas be fono (PNG arba WebP su permatomu fonu) arba vertikali nuotrauka.' );
		$this->text( 'image_alt', 'Nuotraukos aprašymas (neregintiems)', 'Masažuotojas Povilas Maliukevičius' );
		$this->select(
			'image_style',
			'Nuotraukos tipas',
			array(
				'cutout' => 'Portretas be fono (su auksiniu žiedu)',
				'photo'  => 'Įprasta nuotrauka (užapvalintais kampais)',
			),
			'cutout'
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$name = trim( (string) $s['name'] );
		$tag  = trim( (string) $s['tagline'] );
		$text = trim( (string) $s['text'] );

		$style = 'photo' === $s['image_style'] ? 'photo' : 'cutout';
		$image = mg_media_img(
			$s['image'],
			'large',
			array(
				'class' => 'mg-hero__img',
				'alt'   => (string) $s['image_alt'],
				'sizes' => '(min-width: 1024px) 520px, 80vw',
			)
		);

		echo '<section class="mg-section mg-hero mg-hero--' . esc_attr( $style ) . '">';
		echo '<div class="mg-container mg-hero__in">';

		echo '<div class="mg-hero__text">';
		if ( '' !== $name || '' !== $tag ) {
			echo '<h1 class="mg-hero__lockup">';
			if ( '' !== $name ) {
				echo '<span class="mg-hero__name">' . esc_html( $name ) . '</span> ';
			}
			if ( '' !== $tag ) {
				echo '<span class="mg-hero__tag">' . esc_html( $tag ) . '</span>';
			}
			echo '</h1>';
			echo '<span class="mg-ornament" aria-hidden="true"></span>';
		}
		if ( '' !== $text ) {
			echo '<p class="mg-hero__lead">' . nl2br( esc_html( $text ) ) . '</p>';
		}

		$buttons = $this->button_html( $s, 'btn1', array( 'size' => 'lg', 'place' => 'hero' ) )
			. $this->button_html( $s, 'btn2', array( 'style' => 'outline', 'size' => 'lg', 'place' => 'hero' ) );
		if ( '' !== $buttons ) {
			echo '<div class="mg-actions">' . $buttons . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';

		if ( '' !== $image ) {
			echo '<div class="mg-hero__media">';
			if ( 'cutout' === $style ) {
				echo '<span class="mg-hero__ring" aria-hidden="true"></span>';
			}
			echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
		} else {
			echo $this->editor_notice( 'Pasirinkite nuotrauką (skiltis „Nuotrauka“).' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div></section>';
	}
}
