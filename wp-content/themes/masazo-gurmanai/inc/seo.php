<?php
/**
 * Paieškos sistemoms: puslapio antraštė, aprašymas, Open Graph ir struktūriniai duomenys (JSON-LD).
 *
 * Jei įdiegtas SEO įskiepis (Yoast, Rank Math, AIOSEO, SEOPress, The SEO Framework),
 * šis modulis nieko neišveda — kad žymos nesidubliuotų.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

/** Ar SEO žymas jau tvarko įskiepis. */
function mg_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| function_exists( 'the_seo_framework' );
}

/* -------------------------------------------------------------------------
 * Laukai puslapio redagavimo lange
 * ---------------------------------------------------------------------- */

add_action(
	'add_meta_boxes',
	static function () {
		if ( mg_seo_plugin_active() ) {
			return;
		}
		foreach ( array( 'page', 'post' ) as $type ) {
			add_meta_box( 'mg-seo', 'Paieškos sistemoms (SEO)', 'mg_seo_metabox', $type, 'normal', 'low' );
		}
	}
);

/** SEO laukų forma. */
function mg_seo_metabox( WP_Post $post ): void {
	wp_nonce_field( 'mg_seo_save', 'mg_seo_nonce' );
	$title = (string) get_post_meta( $post->ID, '_mg_seo_title', true );
	$desc  = (string) get_post_meta( $post->ID, '_mg_seo_desc', true );
	?>
	<p>
		<label for="mg-seo-title"><strong>Antraštė „Google“ rezultatuose</strong></label><br>
		<input type="text" class="large-text" id="mg-seo-title" name="_mg_seo_title" value="<?php echo esc_attr( $title ); ?>" aria-describedby="mg-seo-title-desc">
		<span class="description" id="mg-seo-title-desc">Iki 60 ženklų. Palikus tuščią — naudojamas puslapio pavadinimas.</span>
	</p>
	<p>
		<label for="mg-seo-desc"><strong>Aprašymas „Google“ rezultatuose</strong></label><br>
		<textarea class="large-text" rows="3" id="mg-seo-desc" name="_mg_seo_desc" aria-describedby="mg-seo-desc-desc"><?php echo esc_textarea( $desc ); ?></textarea>
		<span class="description" id="mg-seo-desc-desc">Iki 160 ženklų: kas šiame puslapyje ir kodėl verta jį atverti.</span>
	</p>
	<?php
}

add_action(
	'save_post',
	static function ( int $post_id ) {
		if ( ! isset( $_POST['mg_seo_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['mg_seo_nonce'] ) ), 'mg_seo_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$title = isset( $_POST['_mg_seo_title'] ) ? sanitize_text_field( wp_unslash( $_POST['_mg_seo_title'] ) ) : '';
		$desc  = isset( $_POST['_mg_seo_desc'] ) ? sanitize_textarea_field( wp_unslash( $_POST['_mg_seo_desc'] ) ) : '';

		update_post_meta( $post_id, '_mg_seo_title', $title );
		update_post_meta( $post_id, '_mg_seo_desc', $desc );
	}
);

/* -------------------------------------------------------------------------
 * Antraštė ir aprašymas
 * ---------------------------------------------------------------------- */

/** Dabartinio puslapio SEO antraštė (jei nurodyta). */
function mg_seo_title(): string {
	if ( is_singular() ) {
		return trim( (string) get_post_meta( get_queried_object_id(), '_mg_seo_title', true ) );
	}

	return '';
}

/** Dabartinio puslapio aprašymas. */
function mg_seo_description(): string {
	$desc = '';

	if ( is_singular() ) {
		$post_id = (int) get_queried_object_id();
		$desc    = trim( (string) get_post_meta( $post_id, '_mg_seo_desc', true ) );

		if ( '' === $desc && is_singular( 'post' ) ) {
			$desc = wp_strip_all_tags( get_the_excerpt( $post_id ) );
		}
	}

	if ( '' === $desc ) {
		$desc = (string) mg_get( 'seo_description' );
	}

	$desc = trim( (string) preg_replace( '/\s+/', ' ', $desc ) );

	return mb_strlen( $desc ) > 200 ? rtrim( mb_substr( $desc, 0, 197 ) ) . '…' : $desc;
}

add_filter(
	'pre_get_document_title',
	static function ( $title ) {
		if ( mg_seo_plugin_active() ) {
			return $title;
		}
		$custom = mg_seo_title();

		return '' !== $custom ? $custom : $title;
	},
	20
);

/** Numatytoji antraštė: „Puslapis – Masažo Gurmanai“ (vietoje domeno pavadinimo). */
add_filter(
	'document_title_parts',
	static function ( array $parts ): array {
		if ( mg_seo_plugin_active() ) {
			return $parts;
		}

		$brand = (string) mg_get( 'brand_name' );
		if ( is_front_page() ) {
			$parts['title']   = $brand . ' – masažas Kaune';
			$parts['tagline'] = (string) mg_get( 'owner_name' );
			unset( $parts['site'] );
		} else {
			$parts['site'] = $brand;
		}

		return $parts;
	}
);

add_filter(
	'document_title_separator',
	static function () {
		return '–';
	}
);

/** Paveikslėlis dalijantis nuoroda. */
function mg_seo_image(): string {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_url( (int) get_post_thumbnail_id(), 'large' );
		if ( $src ) {
			return (string) $src;
		}
	}

	return (string) mg_get( 'seo_share_image' );
}

add_action(
	'wp_head',
	static function () {
		if ( mg_seo_plugin_active() ) {
			return;
		}

		$desc  = mg_seo_description();
		$title = wp_get_document_title();
		$image = mg_seo_image();
		$url   = is_singular() ? (string) get_permalink() : home_url( '/' );

		if ( '' !== $desc ) {
			echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		}

		echo '<meta property="og:locale" content="lt_LT">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( (string) mg_get( 'brand_name' ) ) . '">' . "\n";
		echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		if ( '' !== $desc ) {
			echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		if ( '' !== $image ) {
			echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
			echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		}
	},
	5
);

/* -------------------------------------------------------------------------
 * Struktūriniai duomenys (JSON-LD)
 * ---------------------------------------------------------------------- */

/** Kaina tekstu („39,00 €“) → skaičius (39.0) arba null. */
function mg_price_number( string $price ): ?float {
	if ( preg_match( '/(\d+(?:[.,]\d{1,2})?)/', str_replace( ' ', '', $price ), $m ) ) {
		return (float) str_replace( ',', '.', $m[1] );
	}

	return null;
}

/** Darbo laikas schema.org formatu (dienos su tomis pačiomis valandomis sugrupuojamos). */
function mg_schema_hours(): array {
	$hours  = mg_get( 'hours' );
	$hours  = is_array( $hours ) ? $hours : array();
	$groups = array();

	foreach ( mg_days() as $num => $names ) {
		$day = isset( $hours[ $num ] ) && is_array( $hours[ $num ] ) ? $hours[ $num ] : array();
		if ( ! empty( $day['closed'] ) || empty( $day['open'] ) || empty( $day['close'] ) ) {
			continue;
		}
		$key                = $day['open'] . '-' . $day['close'];
		$groups[ $key ]   ??= array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array(),
			'opens'     => (string) $day['open'],
			'closes'    => (string) $day['close'],
		);
		$groups[ $key ]['dayOfWeek'][] = $names[2];
	}

	return array_values( $groups );
}

/** Verslo aprašas schema.org formatu. */
function mg_schema_business(): array {
	$brand = (string) mg_get( 'brand_name' );
	$data  = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'HealthAndBeautyBusiness',
		'@id'         => home_url( '/#verslas' ),
		'name'        => $brand,
		'url'         => home_url( '/' ),
		'description' => (string) mg_get( 'seo_description' ),
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => (string) mg_get( 'address_street' ),
			'addressLocality' => (string) mg_get( 'address_city' ),
			'postalCode'      => (string) mg_get( 'address_zip' ),
			'addressCountry'  => 'LT',
		),
	);

	$phone = (string) preg_replace( '/[^0-9+]/', '', (string) mg_get( 'phone' ) );
	if ( '' !== $phone ) {
		$data['telephone'] = $phone;
	}
	if ( '' !== (string) mg_get( 'email' ) ) {
		$data['email'] = (string) mg_get( 'email' );
	}
	if ( '' !== (string) mg_get( 'seo_share_image' ) ) {
		$data['image'] = (string) mg_get( 'seo_share_image' );
	}
	if ( '' !== (string) mg_get( 'owner_name' ) ) {
		$data['employee'] = array(
			'@type'    => 'Person',
			'name'     => (string) mg_get( 'owner_name' ),
			'jobTitle' => 'Masažuotojas',
		);
	}

	$hours = mg_schema_hours();
	if ( $hours ) {
		$data['openingHoursSpecification'] = $hours;
	}

	$same_as = array_values( array_filter( array( (string) mg_get( 'rating_url' ), (string) mg_get( 'facebook_url' ), (string) mg_get( 'instagram_url' ) ) ) );
	if ( $same_as ) {
		$data['sameAs'] = $same_as;
	}

	if ( '' !== mg_booking_url() ) {
		$data['potentialAction'] = array(
			'@type'  => 'ReserveAction',
			'target' => mg_booking_url(),
			'name'   => (string) mg_get( 'booking_label' ),
		);
	}

	// Paslaugos ir kainos.
	$offers = array();
	$prices = array();
	foreach ( mg_get_services( array( 'type' => 'single' ) ) as $svc ) {
		$number = mg_price_number( $svc['price'] );
		if ( null === $number ) {
			continue;
		}
		$prices[] = $number;
		$offers[] = array(
			'@type'         => 'Offer',
			'price'         => number_format( $number, 2, '.', '' ),
			'priceCurrency' => 'EUR',
			'itemOffered'   => array(
				'@type' => 'Service',
				'name'  => $svc['title'] . ( '' !== $svc['duration'] ? ' (' . $svc['duration'] . ')' : '' ),
			),
		);
	}
	if ( $offers ) {
		$data['priceRange']      = number_format( min( $prices ), 0 ) . '–' . number_format( max( $prices ), 0 ) . ' €';
		$data['hasOfferCatalog'] = array(
			'@type'           => 'OfferCatalog',
			'name'            => 'Masažo paslaugos',
			'itemListElement' => $offers,
		);
	}

	return $data;
}

add_action(
	'wp_head',
	static function () {
		if ( mg_seo_plugin_active() ) {
			return;
		}

		$schemas = array();

		if ( is_front_page() || is_page( 'kontaktai' ) ) {
			$schemas[] = mg_schema_business();
		}

		if ( is_singular( 'post' ) ) {
			$post_id   = (int) get_queried_object_id();
			$schemas[] = array(
				'@context'         => 'https://schema.org',
				'@type'            => 'Article',
				'headline'         => get_the_title( $post_id ),
				'description'      => mg_seo_description(),
				'datePublished'    => get_post_time( 'c', true, $post_id ),
				'dateModified'     => get_post_modified_time( 'c', true, $post_id ),
				'mainEntityOfPage' => get_permalink( $post_id ),
				'author'           => array(
					'@type' => 'Person',
					'name'  => (string) mg_get( 'owner_name' ),
				),
				'publisher'        => array(
					'@type' => 'Organization',
					'name'  => (string) mg_get( 'brand_name' ),
					'url'   => home_url( '/' ),
				),
			);
		}

		foreach ( $schemas as $schema ) {
			echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
		}
	},
	30
);
