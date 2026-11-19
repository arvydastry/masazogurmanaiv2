<?php
/**
 * Elementor integracija: valdiklių kategorija, valdikliai, šriftai, globalūs stiliai.
 *
 * Valdikliai (inc/widgets/) redaktoriuje matomi kategorijoje „Masažo Gurmanai“.
 * Kiekvienas valdiklis — viena puslapio sekcija su savo laukais (tekstai, nuotraukos, mygtukai).
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

/** Valdiklių sąrašas: failas => klasė. */
function mg_elementor_widgets(): array {
	return array(
		'hero'      => 'Hero',
		'page-hero' => 'Page_Hero',
		'facts'     => 'Facts',
		'services'  => 'Services',
		'steps'     => 'Steps',
		'split'     => 'Split',
		'statement' => 'Statement',
		'features'  => 'Features',
		'reviews'   => 'Reviews',
		'tiles'     => 'Tiles',
		'vouchers'  => 'Vouchers',
		'gallery'   => 'Gallery',
		'contact'   => 'Contact',
		'cta'       => 'Cta',
		'text'      => 'Text',
	);
}

add_action(
	'elementor/elements/categories_registered',
	static function ( $elements_manager ) {
		$elements_manager->add_category(
			'masazo-gurmanai',
			array(
				'title' => 'Masažo Gurmanai',
				'icon'  => 'eicon-heart',
			)
		);

		// Perkeliame kategoriją į sąrašo pradžią, kad svetainės blokai būtų matomi iškart.
		// Elementor tam neturi viešos funkcijos, todėl tvarka keičiama atsargiai: jei kas nors
		// ateityje pasikeistų, kategorija tiesiog liks sąrašo gale.
		try {
			$property = new \ReflectionProperty( $elements_manager, 'categories' );
			$all      = $property->getValue( $elements_manager );
			if ( is_array( $all ) && isset( $all['masazo-gurmanai'] ) ) {
				$mine = array( 'masazo-gurmanai' => $all['masazo-gurmanai'] );
				unset( $all['masazo-gurmanai'] );
				$property->setValue( $elements_manager, $mine + $all );
			}
		} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Paliekame numatytąją tvarką.
		}
	}
);

add_action(
	'elementor/widgets/register',
	static function ( $widgets_manager ) {
		require_once MG_DIR . '/inc/widgets/base.php';

		foreach ( mg_elementor_widgets() as $file => $class ) {
			require_once MG_DIR . '/inc/widgets/' . $file . '.php';
			$fqcn = '\\MG\\Widgets\\' . $class;
			$widgets_manager->register( new $fqcn() );
		}
	}
);

/** Temos šriftai Elementor šriftų sąraše (įkeliami iš temos, ne iš „Google Fonts“). */
add_filter(
	'elementor/fonts/additional_fonts',
	static function ( array $fonts ): array {
		$fonts['Marcellus']      = 'system';
		$fonts['Hanken Grotesk'] = 'system';
		$fonts['Allura']         = 'system';

		return $fonts;
	}
);

/** „Elementor Pro“ (jei bus įdiegtas): leidžiame jame kurti antraštę ir poraštę. */
add_action(
	'elementor/theme/register_locations',
	static function ( $theme_manager ) {
		$theme_manager->register_all_core_location();
	}
);

/** Redaktoriaus skydelyje — šiek tiek stiliaus mūsų valdiklių ikonoms. */
add_action(
	'elementor/editor/after_enqueue_styles',
	static function () {
		wp_add_inline_style(
			'elementor-editor',
			'#elementor-panel-category-masazo-gurmanai .elementor-element .icon i{color:#a8854a}'
			. '.elementor-panel .mg-panel-note{line-height:1.45}'
		);
	}
);

/** Jei Elementor neįjungtas — primename, kad tema be jo nerodys puslapių turinio. */
add_action(
	'admin_notices',
	static function () {
		if ( did_action( 'elementor/loaded' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$url = admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' );
		echo '<div class="notice notice-warning"><p><strong>Masažo Gurmanai:</strong> puslapiai sukurti su „Elementor“. Įdiekite ir įjunkite įskiepį „Elementor“ — <a href="' . esc_url( $url ) . '">atidaryti įskiepių paiešką</a>.</p></div>';
	}
);

/**
 * Elementor globalūs stiliai (Site Settings) pagal temos spalvas ir šriftus.
 * Kviečiama iš pradinio turinio sukūrimo. Esamų reikšmių neperrašo, jei $force = false.
 */
function mg_elementor_apply_kit( bool $force = false ): bool {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
	if ( ! $kit || ! $kit->get_id() ) {
		return false;
	}

	if ( ! $force && get_option( 'mg_kit_applied' ) ) {
		return false;
	}

	$typo = static function ( string $family, string $weight, array $extra = array() ): array {
		return array_merge(
			array(
				'typography_typography'  => 'custom',
				'typography_font_family' => $family,
				'typography_font_weight' => $weight,
			),
			$extra
		);
	};

	$settings = array(
		// Elementor valdikliai pagal nutylėjimą ima: antraštėms — „primary“, tekstui — „text“,
		// mygtukams ir aktyviems elementams — „accent“, antriniam tekstui — „secondary“.
		'system_colors'     => array(
			array( '_id' => 'primary', 'title' => 'Antraštės (šviesi)', 'color' => '#F2ECE0' ),
			array( '_id' => 'secondary', 'title' => 'Antrinis tekstas', 'color' => '#CBC3B5' ),
			array( '_id' => 'text', 'title' => 'Tekstas', 'color' => '#CBC3B5' ),
			array( '_id' => 'accent', 'title' => 'Auksas', 'color' => '#D4B272' ),
		),
		'custom_colors'     => array(
			array( '_id' => 'mggold2', 'title' => 'Šviesus auksas', 'color' => '#E6CD97' ),
			array( '_id' => 'mgbg', 'title' => 'Fonas (juoda)', 'color' => '#100F0D' ),
			array( '_id' => 'mgbgalt', 'title' => 'Fonas (šviesesnis)', 'color' => '#171512' ),
			array( '_id' => 'mgsurface', 'title' => 'Kortelės fonas', 'color' => '#1D1A16' ),
			array( '_id' => 'mgmuted', 'title' => 'Pritemdytas tekstas', 'color' => '#A39B8D' ),
			array( '_id' => 'mgongold', 'title' => 'Tekstas ant aukso', 'color' => '#16130D' ),
		),
		'system_typography' => array(
			array_merge( array( '_id' => 'primary', 'title' => 'Antraštės' ), $typo( 'Marcellus', '400' ) ),
			array_merge( array( '_id' => 'secondary', 'title' => 'Paantraštės' ), $typo( 'Hanken Grotesk', '600' ) ),
			array_merge( array( '_id' => 'text', 'title' => 'Tekstas' ), $typo( 'Hanken Grotesk', '400' ) ),
			array_merge( array( '_id' => 'accent', 'title' => 'Akcentas' ), $typo( 'Hanken Grotesk', '600' ) ),
		),
		'container_width'   => array(
			'unit'  => 'px',
			'size'  => 1200,
			'sizes' => array(),
		),

		// Įprasti Elementor mygtukai — kaip temos pagrindinis mygtukas (auksinis, apvalus).
		'button_typography_typography'       => 'custom',
		'button_typography_font_family'      => 'Hanken Grotesk',
		'button_typography_font_weight'      => '600',
		'button_typography_font_size'        => array(
			'unit'  => 'px',
			'size'  => 16,
			'sizes' => array(),
		),
		'button_text_color'                  => '#16130D',
		'button_background_background'       => 'classic',
		'button_background_color'            => '#D4B272',
		'button_hover_text_color'            => '#16130D',
		'button_hover_background_background' => 'classic',
		'button_hover_background_color'      => '#E6CD97',
		'button_border_radius'               => array(
			'unit'     => 'px',
			'top'      => '999',
			'right'    => '999',
			'bottom'   => '999',
			'left'     => '999',
			'isLinked' => true,
		),
		'button_padding'                     => array(
			'unit'     => 'px',
			'top'      => '14',
			'right'    => '26',
			'bottom'   => '14',
			'left'     => '26',
			'isLinked' => false,
		),
	);

	$kit->update_settings( $settings );
	update_option( 'mg_kit_applied', MG_VERSION );

	// Šriftai — iš temos, „Google Fonts“ nenaudojami.
	update_option( 'elementor_google_font', '0' );
	// Konteineriai (Flexbox) — jais sudėlioti visi puslapiai.
	update_option( 'elementor_experiment-container', 'active' );
	// Su Elementor redaguojami tik puslapiai.
	update_option( 'elementor_cpt_support', array( 'page' ) );

	\Elementor\Plugin::$instance->files_manager->clear_cache();

	return true;
}
