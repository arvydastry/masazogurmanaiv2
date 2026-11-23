<?php
/**
 * Valdiklis „Kontaktai ir kaip rasti“ — adresas, telefonas, el. paštas, darbo laikas (iš nustatymų),
 * kelio aprašymas, įėjimo nuotrauka ir žemėlapis.
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

defined( 'ABSPATH' ) || exit;

class Contact extends Base {

	public function get_name() {
		return 'mg-contact';
	}

	public function get_title() {
		return 'Kontaktai ir kaip rasti';
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	public function get_keywords() {
		return array( 'kontaktai', 'adresas', 'žemėlapis', 'darbo laikas', 'telefonas' );
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_controls() {
		$this->heading_controls( array( 'title' => 'Kaip mus rasti' ), false, true );

		$this->section( 'section_content', 'Turinys' );
		$this->note( 'info', 'Adresas, telefonas, el. paštas ir darbo laikas keičiami vienoje vietoje: <strong>Svetainės nustatymai → Masažo Gurmanai</strong> (☰ meniu viršuje kairėje) arba administravime <strong>Masažo Gurmanai → Nustatymai</strong>. Jie rodomi ir poraštėje.' );
		$this->wysiwyg( 'route', 'Kaip atvykti' );
		$this->media( 'photo', 'Nuotrauka (pvz. įėjimas į pastatą)' );
		$this->text( 'photo_alt', 'Nuotraukos aprašymas (neregintiems)', '' );
		$this->switcher( 'show_map', 'Rodyti mygtuką „Rodyti žemėlapį“', true, 'Žemėlapis įkeliamas tik paspaudus mygtuką — taip puslapis atsidaro greičiau, o „Google“ slapukai neįrašomi be lankytojo veiksmo.' );
		$this->button_controls( 'btn1', 'Pirmas mygtukas', array( 'type' => 'booking' ) );
		$this->button_controls( 'btn2', 'Antras mygtukas', array( 'type' => 'phone' ) );
		$this->end_controls_section();

		$this->section( 'section_labels', 'Užrašai' );
		$this->text( 'label_address', 'Adreso užrašas', 'Adresas' );
		$this->text( 'label_phone', 'Telefono užrašas', 'Telefonas' );
		$this->text( 'label_email', 'El. pašto užrašas', 'El. paštas' );
		$this->text( 'label_hours', 'Darbo laiko užrašas', 'Darbo laikas' );
		$this->text( 'label_map_open', 'Nuoroda į žemėlapį', 'Atidaryti žemėlapyje' );
		$this->text( 'label_map_show', 'Žemėlapio mygtukas', 'Rodyti žemėlapį' );
		$this->text( 'label_map_hint', 'Pastaba po žemėlapio mygtuku', 'Žemėlapį pateikia „Google Maps“.' );
		$this->end_controls_section();

		$this->layout_controls();
	}

	/** Užrašas iš valdiklio laukų (jei laukas niekada nekeistas — numatytasis). */
	private function label( array $s, string $key, string $fallback ): string {
		return isset( $s[ $key ] ) ? trim( (string) $s[ $key ] ) : $fallback;
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$phone   = mg_phone_href();
		$email   = (string) mg_get( 'email' );
		$map_url = (string) mg_get( 'map_url' );
		$embed   = (string) mg_get( 'map_embed' );
		$note    = trim( (string) mg_get( 'address_note' ) );
		$zip     = trim( (string) mg_get( 'address_zip' ) );
		$h       = 'h' . $this->item_level( $s );

		echo $this->open_section( $s, 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="mg-container mg-contact__in">';

		// Kairė: kontaktai.
		echo '<div class="mg-contact__info">';
		echo $this->heading_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo '<ul class="mg-contact__list">';

		echo '<li class="mg-contact__item"><span class="mg-contact__icon">' . mg_icon( 'map-pin' ) . '</span><div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<' . $h . ' class="mg-contact__label">' . esc_html( $this->label( $s, 'label_address', 'Adresas' ) ) . '</' . $h . '>';
		echo '<p class="mg-contact__value">' . esc_html( mg_address_line() . ( '' !== $zip ? ', ' . $zip : '' ) ) . '</p>';
		if ( '' !== $note ) {
			echo '<p class="mg-contact__sub">' . esc_html( $note ) . '</p>';
		}
		if ( '' !== $map_url ) {
			echo mg_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label' => $this->label( $s, 'label_map_open', 'Atidaryti žemėlapyje' ),
					'url'   => $map_url,
					'style' => 'link',
					'icon'  => 'arrow-up-right',
				)
			);
		}
		echo '</div></li>';

		if ( '' !== $phone ) {
			echo '<li class="mg-contact__item"><span class="mg-contact__icon">' . mg_icon( 'phone' ) . '</span><div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<' . $h . ' class="mg-contact__label">' . esc_html( $this->label( $s, 'label_phone', 'Telefonas' ) ) . '</' . $h . '>';
			echo '<p class="mg-contact__value"><a href="' . esc_attr( $phone ) . '">' . esc_html( mg_phone_display() ) . '</a></p>';
			echo '</div></li>';
		}

		if ( '' !== $email ) {
			echo '<li class="mg-contact__item"><span class="mg-contact__icon">' . mg_icon( 'envelope-simple' ) . '</span><div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<' . $h . ' class="mg-contact__label">' . esc_html( $this->label( $s, 'label_email', 'El. paštas' ) ) . '</' . $h . '>';
			echo '<p class="mg-contact__value"><a href="' . esc_attr( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a></p>';
			echo '</div></li>';
		}

		$hours = mg_hours_html();
		if ( '' !== $hours ) {
			echo '<li class="mg-contact__item"><span class="mg-contact__icon">' . mg_icon( 'clock' ) . '</span><div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<' . $h . ' class="mg-contact__label">' . esc_html( $this->label( $s, 'label_hours', 'Darbo laikas' ) ) . '</' . $h . '>';
			echo $hours; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div></li>';
		}

		echo '</ul>';

		$buttons = $this->button_html( $s, 'btn1', array( 'place' => 'kontaktai' ) )
			. $this->button_html( $s, 'btn2', array( 'style' => 'outline', 'place' => 'kontaktai' ) );
		if ( '' !== $buttons ) {
			echo '<div class="mg-actions">' . $buttons . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';

		// Dešinė: kaip atvykti, nuotrauka, žemėlapis.
		$route = trim( (string) $s['route'] );
		$photo = mg_media_img(
			$s['photo'],
			'large',
			array(
				'alt'   => (string) $s['photo_alt'],
				'sizes' => '(min-width: 1024px) 520px, 92vw',
			)
		);

		echo '<div class="mg-contact__side">';
		if ( '' !== $photo ) {
			echo '<figure class="mg-contact__photo" data-mg-reveal>' . $photo . '</figure>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( '' !== $route ) {
			echo '<div class="mg-contact__route mg-prose">' . wp_kses_post( $this->parse_text_editor( $route ) ) . '</div>';
		}
		if ( 'yes' === $s['show_map'] && '' !== $embed ) {
			echo '<div class="mg-map" data-mg-map="' . esc_url( $embed ) . '" data-mg-map-title="' . esc_attr( 'Žemėlapis: ' . mg_address_line() ) . '">';
			echo '<button class="mg-btn mg-btn--outline" type="button">' . mg_icon( 'map-trifold', 'mg-btn__icon' ) . '<span class="mg-btn__label">' . esc_html( $this->label( $s, 'label_map_show', 'Rodyti žemėlapį' ) ) . '</span></button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$hint = $this->label( $s, 'label_map_hint', '' );
			if ( '' !== $hint ) {
				echo '<p class="mg-map__hint">' . esc_html( $hint ) . '</p>';
			}
			echo '</div>';
		}
		echo '</div>';

		echo '</div></section>';
	}
}
