<?php
/**
 * Temos paruošimas: palaikomos funkcijos, meniu, stiliai ir scenarijai.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 120,
				'width'       => 360,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		register_nav_menus(
			array(
				'primary' => 'Pagrindinis meniu (antraštėje)',
				'footer'  => 'Poraštės meniu',
			)
		);

		// Elementor: turinio plotis sutampa su temos konteineriu.
		$GLOBALS['content_width'] = 1200;
	}
);

/** Failo versija pagal jo keitimo laiką — po atnaujinimo talpykla (LiteSpeed, naršyklė) ima naują failą. */
function mg_asset_version( string $relative ): string {
	$file = MG_DIR . '/' . ltrim( $relative, '/' );

	return is_readable( $file ) ? (string) filemtime( $file ) : MG_VERSION;
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'mg-fonts', MG_URI . '/assets/css/fonts.css', array(), mg_asset_version( 'assets/css/fonts.css' ) );
		wp_enqueue_style( 'mg-main', MG_URI . '/assets/css/main.css', array( 'mg-fonts' ), mg_asset_version( 'assets/css/main.css' ) );

		wp_enqueue_script(
			'mg-main',
			MG_URI . '/assets/js/main.js',
			array(),
			mg_asset_version( 'assets/js/main.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		$config = array(
			'ga4Event'   => (string) mg_get( 'ga4_event' ),
			'metaEvent'  => (string) mg_get( 'meta_event' ),
			'adsSendTo'  => (string) mg_get( 'gads_send_to' ),
			'trackCalls' => (bool) mg_get( 'track_calls' ),
		);
		wp_add_inline_script( 'mg-main', 'window.mgConfig = ' . wp_json_encode( $config ) . ';', 'before' );

		// Puslapiams, sukurtiems su Elementor, blokų redaktoriaus stilių nereikia.
		if ( mg_is_elementor_page() ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );
			wp_dequeue_style( 'classic-theme-styles' );
			wp_dequeue_style( 'global-styles' );
		}
	},
	20
);

/** Ar dabartinis puslapis sukurtas su Elementor. */
function mg_is_elementor_page( int $post_id = 0 ): bool {
	if ( ! $post_id ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = (int) get_queried_object_id();
	}
	if ( ! $post_id || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	$document = \Elementor\Plugin::$instance->documents->get( $post_id );

	return $document && $document->is_built_with_elementor();
}

/** Šriftų išankstinis įkėlimas: tekstas iškart rodomas tikru šriftu, be „šuoliuko“. */
add_action(
	'wp_head',
	static function () {
		$fonts = array( 'hanken-grotesk-latin.woff2', 'hanken-grotesk-latin-ext.woff2', 'marcellus-latin.woff2', 'marcellus-latin-ext.woff2' );
		if ( is_front_page() ) {
			$fonts[] = 'allura-latin.woff2';
			$fonts[] = 'allura-latin-ext.woff2';
		}
		foreach ( $fonts as $font ) {
			echo '<link rel="preload" href="' . esc_url( MG_URI . '/assets/fonts/' . $font ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
		}
	},
	2
);

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'mg-site';
		if ( mg_get( 'mobile_bar' ) ) {
			$classes[] = 'mg-has-mobilebar';
		}
		if ( mg_is_elementor_page() ) {
			$classes[] = 'mg-elementor-page';
		}

		return $classes;
	}
);

/** Naršyklės adreso juostos spalva telefonuose. */
add_action(
	'wp_head',
	static function () {
		echo '<meta name="theme-color" content="#100f0d">' . "\n";
	},
	3
);

/* -------------------------------------------------------------------------
 * Nereikalingų dalykų išjungimas (greitis)
 * ---------------------------------------------------------------------- */

add_action(
	'init',
	static function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
	}
);

/* -------------------------------------------------------------------------
 * Meniu
 * ---------------------------------------------------------------------- */

/** Meniu be WordPress „atsarginio“ puslapių sąrašo: jei meniu nepriskirtas — nieko nerodome. */
function mg_nav_menu( string $location, string $class ): string {
	if ( ! has_nav_menu( $location ) ) {
		return '';
	}

	return (string) wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => $class,
			'depth'          => 1,
			'fallback_cb'    => false,
			'echo'           => false,
		)
	);
}

/** Aktyviam meniu punktui — aria-current (ekrano skaitytuvams). */
add_filter(
	'nav_menu_link_attributes',
	static function ( array $atts, $item ): array {
		if ( ! empty( $item->current ) ) {
			$atts['aria-current'] = 'page';
		}

		return $atts;
	},
	10,
	2
);

/* -------------------------------------------------------------------------
 * Logotipas
 * ---------------------------------------------------------------------- */

/** Logotipas: įkeltas paveikslėlis arba pavadinimas tekstu. */
function mg_logo( string $context = 'header' ): string {
	$name    = (string) mg_get( 'brand_name' );
	$tagline = (string) mg_get( 'brand_tagline' );
	$label   = $name . ' – pradžios puslapis';

	$html = '<a class="mg-logo mg-logo--' . esc_attr( $context ) . '" href="' . esc_url( home_url( '/' ) ) . '" aria-label="' . esc_attr( $label ) . '">';

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id && wp_attachment_is_image( $logo_id ) ) {
		$html .= wp_get_attachment_image(
			$logo_id,
			'medium',
			false,
			array(
				'class' => 'mg-logo__img',
				'alt'   => '',
			)
		);
	} else {
		$html .= '<span class="mg-logo__name" aria-hidden="true">' . esc_html( $name ) . '</span>';
		if ( '' !== $tagline && 'footer' === $context ) {
			$html .= '<span class="mg-logo__tag" aria-hidden="true">' . esc_html( $tagline ) . '</span>';
		}
	}

	return $html . '</a>';
}

/* -------------------------------------------------------------------------
 * Administravimo stiliai
 * ---------------------------------------------------------------------- */

add_action(
	'admin_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'mg-admin', MG_URI . '/assets/css/admin.css', array(), mg_asset_version( 'assets/css/admin.css' ) );
	}
);
