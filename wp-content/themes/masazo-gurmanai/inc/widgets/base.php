<?php
/**
 * Bendra visų „Masažo Gurmanai“ valdiklių dalis: pasikartojantys laukai ir HTML.
 *
 * @package MasazoGurmanai
 */

namespace MG\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

abstract class Base extends Widget_Base {

	public function get_categories() {
		return array( 'masazo-gurmanai' );
	}

	/** Valdikliai patys išveda <section>, papildomo Elementor apvalkalo nereikia. */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/* ---------------------------------------------------------------------
	 * Laukų kūrimo trumpiniai
	 * ------------------------------------------------------------------ */

	protected function section( string $id, string $label ): void {
		$this->start_controls_section(
			$id,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
	}

	protected function text( string $id, string $label, string $default = '', string $desc = '' ): void {
		$this->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => Controls_Manager::TEXT,
				'default'     => $default,
				'label_block' => true,
				'description' => $desc,
				'ai'          => array( 'active' => false ),
			)
		);
	}

	protected function textarea( string $id, string $label, string $default = '', string $desc = '', int $rows = 4 ): void {
		$this->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => $default,
				'rows'        => $rows,
				'description' => $desc,
				'ai'          => array( 'active' => false ),
			)
		);
	}

	protected function wysiwyg( string $id, string $label, string $default = '' ): void {
		$this->add_control(
			$id,
			array(
				'label'   => $label,
				'type'    => Controls_Manager::WYSIWYG,
				'default' => $default,
				'ai'      => array( 'active' => false ),
			)
		);
	}

	protected function media( string $id, string $label, string $desc = '' ): void {
		$this->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'id'  => 0,
					'url' => '',
				),
				'description' => $desc,
				'ai'          => array( 'active' => false ),
			)
		);
	}

	protected function select( string $id, string $label, array $options, string $default = '', string $desc = '' ): void {
		$this->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => Controls_Manager::SELECT,
				'options'     => $options,
				'default'     => $default,
				'description' => $desc,
			)
		);
	}

	protected function switcher( string $id, string $label, bool $default = false, string $desc = '' ): void {
		$this->add_control(
			$id,
			array(
				'label'        => $label,
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Taip',
				'label_off'    => 'Ne',
				'return_value' => 'yes',
				'default'      => $default ? 'yes' : '',
				'description'  => $desc,
			)
		);
	}

	protected function note( string $id, string $html ): void {
		$this->add_control(
			$id,
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => $html,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info mg-panel-note',
			)
		);
	}

	/** Ikonos pasirinkimas (kartotinio elemento laukui). */
	protected static function icon_control( string $default = '' ): array {
		return array(
			'label'   => 'Ikona',
			'type'    => Controls_Manager::SELECT,
			'options' => mg_icon_options(),
			'default' => $default,
		);
	}

	protected static function repeater_text( string $label, string $default = '', bool $textarea = false ): array {
		return array(
			'label'       => $label,
			'type'        => $textarea ? Controls_Manager::TEXTAREA : Controls_Manager::TEXT,
			'default'     => $default,
			'label_block' => true,
			'ai'          => array( 'active' => false ),
		);
	}

	/* ---------------------------------------------------------------------
	 * Antraštės grupė: (mažas užrašas), antraštė, įžanga
	 * ------------------------------------------------------------------ */

	/**
	 * @param array $defaults     Numatytosios reikšmės.
	 * @param bool  $with_eyebrow Ar rodyti lauką mažam užrašui virš antraštės.
	 * @param bool  $with_items   Ar valdiklis turi vidinių antraščių (kortelių, punktų pavadinimų).
	 */
	protected function heading_controls( array $defaults = array(), bool $with_eyebrow = false, bool $with_items = false ): void {
		$d = wp_parse_args(
			$defaults,
			array(
				'eyebrow' => '',
				'title'   => '',
				'lead'    => '',
				'tag'     => 'h2',
				'align'   => '',
			)
		);

		$this->section( 'section_heading', 'Antraštė' );

		if ( $with_eyebrow ) {
			$this->text( 'h_eyebrow', 'Mažas užrašas virš antraštės', $d['eyebrow'] );
		}
		$this->text( 'h_title', 'Antraštė', $d['title'], 'Žodžius tarp žvaigždučių, pvz. *ramybė*, galima išskirti auksine spalva.' );
		$this->textarea( 'h_lead', 'Įžanginis tekstas', $d['lead'], '', 3 );
		$this->select(
			'h_tag',
			'Antraštės lygis',
			array(
				'h1' => 'H1 (pagrindinė puslapio antraštė)',
				'h2' => 'H2 (sekcijos antraštė)',
				'h3' => 'H3',
			),
			$d['tag'],
			'Puslapyje turi būti tik viena H1 antraštė.'
		);
		$this->select(
			'h_align',
			'Lygiavimas',
			array(
				''       => 'Kairėje',
				'center' => 'Centre',
			),
			$d['align']
		);
		if ( $with_items ) {
			$this->select(
				'h_items',
				'Punktų pavadinimų lygis',
				array(
					''   => 'Automatiškai (vienu lygiu žemiau antraštės)',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
				),
				'',
				'Svarbu ekrano skaitytuvams: lygiai turi eiti iš eilės (H1 → H2 → H3), nepraleidžiant.'
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Vidinių antraščių (kortelių, punktų pavadinimų) lygis: 2, 3 arba 4.
	 * Automatiškai — vienu lygiu žemiau valdiklio antraštės; jei jos nėra — H2.
	 */
	protected function item_level( array $s ): int {
		$forced = (string) ( $s['h_items'] ?? '' );
		if ( in_array( $forced, array( 'h2', 'h3', 'h4' ), true ) ) {
			return (int) substr( $forced, 1 );
		}
		if ( '' === trim( (string) ( $s['h_title'] ?? '' ) ) ) {
			return 2;
		}
		$tag = (string) ( $s['h_tag'] ?? 'h2' );

		return 'h1' === $tag ? 2 : ( 'h3' === $tag ? 4 : 3 );
	}

	protected function heading_html( array $s, string $id = '' ): string {
		$title   = trim( (string) ( $s['h_title'] ?? '' ) );
		$lead    = trim( (string) ( $s['h_lead'] ?? '' ) );
		$eyebrow = trim( (string) ( $s['h_eyebrow'] ?? '' ) );

		if ( '' === $title && '' === $lead && '' === $eyebrow ) {
			return '';
		}

		$tag   = in_array( $s['h_tag'] ?? 'h2', array( 'h1', 'h2', 'h3' ), true ) ? $s['h_tag'] : 'h2';
		$class = 'mg-head' . ( 'center' === ( $s['h_align'] ?? '' ) ? ' mg-head--center' : '' );

		$html = '<header class="' . esc_attr( $class ) . '">';
		if ( '' !== $eyebrow ) {
			$html .= '<p class="mg-eyebrow">' . esc_html( $eyebrow ) . '</p>';
		}
		if ( '' !== $title ) {
			$html .= '<' . $tag . ' class="mg-title"' . ( '' !== $id ? ' id="' . esc_attr( $id ) . '"' : '' ) . '>' . mg_format_title( $title ) . '</' . $tag . '>';
		}
		if ( '' !== $lead ) {
			$html .= '<p class="mg-lead">' . nl2br( esc_html( $lead ) ) . '</p>';
		}

		return $html . '</header>';
	}

	/* ---------------------------------------------------------------------
	 * Mygtuko grupė
	 * ------------------------------------------------------------------ */

	/**
	 * @param string $prefix   Laukų priešdėlis, pvz. „btn“.
	 * @param string $label    Grupės pavadinimas skydelyje.
	 * @param array  $defaults type (booking|phone|custom|none), label, url.
	 */
	protected function button_controls( string $prefix, string $label, array $defaults = array() ): void {
		$d = wp_parse_args(
			$defaults,
			array(
				'type'  => 'booking',
				'label' => '',
				'url'   => '',
			)
		);

		$this->add_control(
			$prefix . '_heading',
			array(
				'label'     => $label,
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->select(
			$prefix . '_type',
			'Kur veda mygtukas',
			array(
				'booking' => 'Rezervacija („Treatwell“)',
				'phone'   => 'Skambutis telefonu',
				'custom'  => 'Kita nuoroda',
				'none'    => 'Mygtuko nerodyti',
			),
			$d['type']
		);
		$this->add_control(
			$prefix . '_label',
			array(
				'label'       => 'Mygtuko tekstas',
				'type'        => Controls_Manager::TEXT,
				'default'     => $d['label'],
				'label_block' => true,
				'description' => 'Palikus tuščią: rezervacijai — tekstas iš nustatymų, skambučiui — telefono numeris.',
				'ai'          => array( 'active' => false ),
				'condition'   => array( $prefix . '_type!' => 'none' ),
			)
		);
		$this->add_control(
			$prefix . '_url',
			array(
				'label'       => 'Nuoroda',
				'type'        => Controls_Manager::URL,
				'options'     => false,
				'placeholder' => '/paslaugos',
				'default'     => array( 'url' => $d['url'] ),
				'condition'   => array( $prefix . '_type' => 'custom' ),
			)
		);
	}

	/**
	 * Mygtuko HTML pagal nustatymus.
	 *
	 * @param array  $s      Valdiklio nustatymai.
	 * @param string $prefix Laukų priešdėlis.
	 * @param array  $args   Papildomi mg_button() argumentai (style, size, place…).
	 */
	protected function button_html( array $s, string $prefix, array $args = array() ): string {
		$type  = (string) ( $s[ $prefix . '_type' ] ?? 'none' );
		$label = trim( (string) ( $s[ $prefix . '_label' ] ?? '' ) );

		switch ( $type ) {
			case 'booking':
				$args['url']   = mg_booking_url();
				$args['label'] = '' !== $label ? $label : (string) mg_get( 'booking_label' );
				$args['icon']  = $args['icon'] ?? 'arrow-up-right';
				break;

			case 'phone':
				$args['url']      = mg_phone_href();
				$args['label']    = '' !== $label ? $label : mg_phone_display();
				$args['icon']     = 'phone';
				$args['icon_pos'] = 'start';
				break;

			case 'custom':
				$url           = is_array( $s[ $prefix . '_url' ] ?? null ) ? (string) ( $s[ $prefix . '_url' ]['url'] ?? '' ) : '';
				$args['url']   = mg_local_url( $url );
				$args['label'] = $label;
				$args['icon']  = $args['icon'] ?? ( mg_is_external( $args['url'] ) ? 'arrow-up-right' : 'arrow-right' );
				break;

			default:
				return '';
		}

		return mg_button( $args );
	}

	/* ---------------------------------------------------------------------
	 * Sekcijos apvalkalas: fonas, tarpai, inkaras
	 * ------------------------------------------------------------------ */

	protected function layout_controls( array $defaults = array() ): void {
		$d = wp_parse_args(
			$defaults,
			array(
				'tone'  => '',
				'space' => '',
			)
		);

		$this->section( 'section_layout', 'Sekcijos išvaizda' );
		$this->select(
			'l_tone',
			'Fonas',
			array(
				''    => 'Pagrindinis (tamsus)',
				'alt' => 'Šviesesnis (sekcijai išskirti)',
			),
			$d['tone']
		);
		$this->select(
			'l_space',
			'Tarpai viršuje ir apačioje',
			array(
				''        => 'Įprasti',
				'compact' => 'Mažesni',
				'flush'   => 'Be tarpo viršuje',
			),
			$d['space']
		);
		$this->text( 'l_anchor', 'Inkaras (ID nuorodoms)', '', 'Pvz. įrašius „programos“, į šią sekciją ves nuoroda /paslaugos#programos. Tik mažosios raidės, be tarpų.' );
		$this->end_controls_section();
	}

	/** Sekcijos atidarymo žymė. */
	protected function open_section( array $s, string $name, string $extra_class = '', string $tag = 'section', string $attrs = '' ): string {
		$classes = array( 'mg-section', 'mg-' . $name );
		if ( 'alt' === ( $s['l_tone'] ?? '' ) ) {
			$classes[] = 'mg-section--alt';
		}
		if ( in_array( $s['l_space'] ?? '', array( 'compact', 'flush' ), true ) ) {
			$classes[] = 'mg-section--' . $s['l_space'];
		}
		if ( '' !== $extra_class ) {
			$classes[] = $extra_class;
		}

		$anchor = sanitize_title( (string) ( $s['l_anchor'] ?? '' ) );

		return '<' . $tag . ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' . ( '' !== $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '' ) . $attrs . '>';
	}

	/** Ar valdiklis šiuo metu piešiamas Elementor redaktoriuje. */
	protected function in_editor(): bool {
		return \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode();
	}

	/** Pranešimas, matomas tik redaktoriuje (pvz. kai valdiklis dar tuščias). */
	protected function editor_notice( string $text ): string {
		if ( ! $this->in_editor() ) {
			return '';
		}

		return '<div class="mg-editor-notice">' . esc_html( $text ) . '</div>';
	}
}
