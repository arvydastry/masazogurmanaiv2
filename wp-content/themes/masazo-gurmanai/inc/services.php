<?php
/**
 * Paslaugos: įrašų tipas, laukai, administravimo sąrašas ir atvaizdavimas.
 *
 * Paslaugos laikomos vienoje vietoje (meniu „Paslaugos“), o puslapiuose jas rodo
 * Elementor valdiklis „Paslaugos“. Pakeitus kainą čia — ji pasikeičia visur.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

const MG_SERVICE_TYPE = 'mg_paslauga';

/** Paslaugos laukai: raktas => [etiketė, tipas, paaiškinimas]. */
function mg_service_fields(): array {
	return array(
		'_mg_type'        => array(
			'label'   => 'Tipas',
			'type'    => 'select',
			'options' => array(
				'single'  => 'Masažas (vienas vizitas)',
				'program' => 'Programa (keli vizitai)',
			),
		),
		'_mg_duration'    => array(
			'label' => 'Trukmė',
			'type'  => 'text',
			'desc'  => 'Pvz. „60 min“ arba „10 vizitų po 45 min“.',
		),
		'_mg_price'       => array(
			'label' => 'Kaina',
			'type'  => 'text',
			'desc'  => 'Pvz. „39,00 €“.',
		),
		'_mg_old_price'   => array(
			'label' => 'Ankstesnė kaina (akcijai)',
			'type'  => 'text',
			'desc'  => 'Jei vyksta akcija — įrašykite įprastą kainą, ji bus rodoma perbraukta. Akcijai pasibaigus šį lauką ištrinkite.',
		),
		'_mg_promo_note'  => array(
			'label' => 'Akcijos pastaba',
			'type'  => 'text',
			'desc'  => 'Pvz. „Naujiems klientams iki gruodžio 31 d.“',
		),
		'_mg_desc'        => array(
			'label' => 'Aprašymas',
			'type'  => 'textarea',
			'rows'  => 5,
		),
		'_mg_bullets'     => array(
			'label' => 'Nauda (po vieną punktą eilutėje)',
			'type'  => 'textarea',
			'rows'  => 4,
		),
		'_mg_cta'         => array(
			'label'   => 'Mygtukas',
			'type'    => 'select',
			'options' => array(
				'book' => 'Rezervuoti („Treatwell“)',
				'call' => 'Skambinti (paslaugos „Treatwell“ nėra)',
				'none' => 'Be mygtuko',
			),
		),
		'_mg_booking_url' => array(
			'label' => 'Rezervacijos nuoroda (šiai paslaugai)',
			'type'  => 'url',
			'desc'  => 'Paprastai paliekama tuščia — tada naudojama bendroji rezervacijos nuoroda iš nustatymų.',
		),
		'_mg_icon'        => array(
			'label'   => 'Ikona',
			'type'    => 'icon',
		),
		'_mg_featured'    => array(
			'label' => 'Rodyti pradžios puslapyje (tarp populiariausių)',
			'type'  => 'checkbox',
		),
	);
}

add_action(
	'init',
	static function () {
		register_post_type(
			MG_SERVICE_TYPE,
			array(
				'labels'              => array(
					'name'               => 'Paslaugos',
					'singular_name'      => 'Paslauga',
					'menu_name'          => 'Paslaugos',
					'add_new'            => 'Pridėti paslaugą',
					'add_new_item'       => 'Pridėti paslaugą',
					'edit_item'          => 'Redaguoti paslaugą',
					'new_item'           => 'Nauja paslauga',
					'view_item'          => 'Peržiūrėti paslaugą',
					'search_items'       => 'Ieškoti paslaugų',
					'not_found'          => 'Paslaugų nerasta',
					'not_found_in_trash' => 'Šiukšlinėje paslaugų nėra',
					'all_items'          => 'Visos paslaugos',
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
				'show_in_rest'        => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'menu_position'       => 4,
				'menu_icon'           => 'dashicons-list-view',
				'supports'            => array( 'title', 'page-attributes' ),
				'capability_type'     => 'page',
				'map_meta_cap'        => true,
			)
		);
	}
);

/* -------------------------------------------------------------------------
 * Redagavimo langas
 * ---------------------------------------------------------------------- */

add_filter(
	'enter_title_here',
	static function ( string $text, $post ): string {
		return ( $post && MG_SERVICE_TYPE === $post->post_type ) ? 'Paslaugos pavadinimas' : $text;
	},
	10,
	2
);

add_action(
	'add_meta_boxes_' . MG_SERVICE_TYPE,
	static function () {
		add_meta_box( 'mg-service', 'Paslaugos informacija', 'mg_service_metabox', MG_SERVICE_TYPE, 'normal', 'high' );
	}
);

/** Paslaugos laukų forma. */
function mg_service_metabox( WP_Post $post ): void {
	wp_nonce_field( 'mg_service_save', 'mg_service_nonce' );
	$is_new = 'auto-draft' === $post->post_status;
	?>
	<table class="form-table" role="presentation">
		<tbody>
		<?php foreach ( mg_service_fields() as $key => $field ) : ?>
			<?php
			$id    = 'mg-svc' . $key;
			$value = (string) get_post_meta( $post->ID, $key, true );
			$desc  = $field['desc'] ?? '';

			if ( $is_new && '_mg_type' === $key ) {
				$value = 'single';
			}
			if ( $is_new && '_mg_cta' === $key ) {
				$value = 'book';
			}
			?>
			<tr>
				<?php if ( 'checkbox' === $field['type'] ) : ?>
					<th scope="row">Pradžios puslapis</th>
					<td>
						<label for="<?php echo esc_attr( $id ); ?>">
							<input type="checkbox" value="1" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php checked( '1', $value ); ?>>
							<?php echo esc_html( $field['label'] ); ?>
						</label>
					</td>
				<?php else : ?>
					<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
					<td>
						<?php if ( 'textarea' === $field['type'] ) : ?>
							<textarea class="large-text" rows="<?php echo esc_attr( (string) ( $field['rows'] ?? 4 ) ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
						<?php elseif ( 'select' === $field['type'] || 'icon' === $field['type'] ) : ?>
							<?php $options = 'icon' === $field['type'] ? mg_icon_options() : $field['options']; ?>
							<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>">
								<?php foreach ( $options as $opt_value => $opt_label ) : ?>
									<option value="<?php echo esc_attr( (string) $opt_value ); ?>" <?php selected( $value, (string) $opt_value ); ?>><?php echo esc_html( $opt_label ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php else : ?>
							<input type="<?php echo esc_attr( 'url' === $field['type'] ? 'url' : 'text' ); ?>" class="<?php echo esc_attr( 'url' === $field['type'] ? 'large-text' : 'regular-text' ); ?>"
								id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo $desc ? ' aria-describedby="' . esc_attr( $id ) . '-desc"' : ''; ?>>
						<?php endif; ?>
						<?php if ( $desc ) : ?>
							<p class="description" id="<?php echo esc_attr( $id ); ?>-desc"><?php echo esc_html( $desc ); ?></p>
						<?php endif; ?>
					</td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p class="description">Eiliškumą svetainėje lemia laukas „Rikiavimas“ dešinėje, skiltyje „Įrašo atributai“ (mažesnis skaičius – aukščiau).</p>
	<?php
}

add_action(
	'save_post_' . MG_SERVICE_TYPE,
	static function ( int $post_id ) {
		if ( ! isset( $_POST['mg_service_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['mg_service_nonce'] ) ), 'mg_service_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( mg_service_fields() as $key => $field ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- valoma žemiau pagal lauko tipą.
			$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
			$raw = is_string( $raw ) ? $raw : '';

			update_post_meta( $post_id, $key, mg_sanitize_service_field( $key, $raw ) );
		}
	}
);

/** Vieno paslaugos lauko reikšmės valymas. */
function mg_sanitize_service_field( string $key, string $raw ): string {
	$fields = mg_service_fields();
	$field  = $fields[ $key ] ?? array( 'type' => 'text' );

	switch ( $field['type'] ) {
		case 'checkbox':
			return '' !== $raw && '0' !== $raw ? '1' : '';
		case 'textarea':
			return sanitize_textarea_field( $raw );
		case 'url':
			return esc_url_raw( trim( $raw ) );
		case 'select':
			$value = sanitize_key( $raw );
			return isset( $field['options'][ $value ] ) ? $value : (string) array_key_first( $field['options'] );
		case 'icon':
			$value = preg_replace( '/[^a-z0-9\-]/', '', strtolower( $raw ) );
			return array_key_exists( (string) $value, mg_icon_options() ) ? (string) $value : '';
		default:
			return sanitize_text_field( $raw );
	}
}

/* -------------------------------------------------------------------------
 * Administravimo sąrašas
 * ---------------------------------------------------------------------- */

add_filter(
	'manage_' . MG_SERVICE_TYPE . '_posts_columns',
	static function ( array $columns ): array {
		$out = array();
		foreach ( $columns as $key => $label ) {
			if ( 'date' === $key ) {
				continue;
			}
			$out[ $key ] = $label;
			if ( 'title' === $key ) {
				$out['mg_price']    = 'Kaina';
				$out['mg_duration'] = 'Trukmė';
				$out['mg_type']     = 'Tipas';
				$out['mg_cta']      = 'Mygtukas';
				$out['mg_featured'] = 'Pradžioje';
				$out['mg_order']    = 'Rikiavimas';
			}
		}

		return $out;
	}
);

add_action(
	'manage_' . MG_SERVICE_TYPE . '_posts_custom_column',
	static function ( string $column, int $post_id ) {
		switch ( $column ) {
			case 'mg_price':
				$old = (string) get_post_meta( $post_id, '_mg_old_price', true );
				echo esc_html( (string) get_post_meta( $post_id, '_mg_price', true ) );
				if ( '' !== $old ) {
					echo ' <span style="color:#996800">(akcija, buvo ' . esc_html( $old ) . ')</span>';
				}
				break;
			case 'mg_duration':
				echo esc_html( (string) get_post_meta( $post_id, '_mg_duration', true ) );
				break;
			case 'mg_type':
				echo 'program' === get_post_meta( $post_id, '_mg_type', true ) ? 'Programa' : 'Masažas';
				break;
			case 'mg_cta':
				$cta = (string) get_post_meta( $post_id, '_mg_cta', true );
				if ( 'call' === $cta ) {
					echo 'Skambinti';
				} elseif ( 'none' === $cta ) {
					echo 'Be mygtuko';
				} else {
					echo '' !== (string) get_post_meta( $post_id, '_mg_booking_url', true ) ? 'Treatwell (tiesioginė nuoroda)' : 'Treatwell (bendroji nuoroda)';
				}
				break;
			case 'mg_featured':
				echo '1' === get_post_meta( $post_id, '_mg_featured', true ) ? 'Taip' : '—';
				break;
			case 'mg_order':
				echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
				break;
		}
	},
	10,
	2
);

/** Administravimo sąraše paslaugos rikiuojamos taip pat, kaip svetainėje. */
add_action(
	'pre_get_posts',
	static function ( WP_Query $query ) {
		if ( is_admin() && $query->is_main_query() && MG_SERVICE_TYPE === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		}
	}
);

/* -------------------------------------------------------------------------
 * Duomenys
 * ---------------------------------------------------------------------- */

/**
 * Paslaugų sąrašas.
 *
 * @param array $args type (all|single|program), featured (bool), limit (int).
 * @return array<int, array<string, mixed>>
 */
function mg_get_services( array $args = array() ): array {
	$args = wp_parse_args(
		$args,
		array(
			'type'     => 'all',
			'featured' => false,
			'limit'    => -1,
		)
	);

	$meta_query = array();
	if ( in_array( $args['type'], array( 'single', 'program' ), true ) ) {
		$meta_query[] = array(
			'key'   => '_mg_type',
			'value' => $args['type'],
		);
	}
	if ( $args['featured'] ) {
		$meta_query[] = array(
			'key'   => '_mg_featured',
			'value' => '1',
		);
	}

	$posts = get_posts(
		array(
			'post_type'        => MG_SERVICE_TYPE,
			'post_status'      => 'publish',
			'numberposts'      => (int) $args['limit'] > 0 ? (int) $args['limit'] : -1,
			'orderby'          => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'meta_query'       => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- keliolika įrašų.
			'suppress_filters' => false,
		)
	);

	return array_map( 'mg_service_data', $posts );
}

/** Vienos paslaugos duomenys patogiu pavidalu. */
function mg_service_data( WP_Post $post ): array {
	$meta = static function ( string $key ) use ( $post ): string {
		return trim( (string) get_post_meta( $post->ID, $key, true ) );
	};

	$cta = $meta( '_mg_cta' );
	$cta = in_array( $cta, array( 'book', 'call', 'none' ), true ) ? $cta : 'book';

	if ( 'book' === $cta ) {
		$url = mg_booking_url( $meta( '_mg_booking_url' ) );
	} elseif ( 'call' === $cta ) {
		$url = mg_phone_href();
	} else {
		$url = '';
	}

	return array(
		'id'         => $post->ID,
		'title'      => get_the_title( $post ),
		'type'       => 'program' === $meta( '_mg_type' ) ? 'program' : 'single',
		'desc'       => $meta( '_mg_desc' ),
		'bullets'    => mg_lines( $meta( '_mg_bullets' ) ),
		'duration'   => $meta( '_mg_duration' ),
		'price'      => $meta( '_mg_price' ),
		'old_price'  => $meta( '_mg_old_price' ),
		'promo_note' => $meta( '_mg_promo_note' ),
		'cta'        => $cta,
		'url'        => $url,
		'icon'       => $meta( '_mg_icon' ),
		'featured'   => '1' === $meta( '_mg_featured' ),
	);
}

/* -------------------------------------------------------------------------
 * Atvaizdavimas
 * ---------------------------------------------------------------------- */

/** Kainos HTML (su perbraukta ankstesne kaina, jei vyksta akcija). */
function mg_service_price_html( array $svc ): string {
	if ( '' === $svc['price'] ) {
		return '';
	}

	$html = '<span class="mg-price">' . mg_sr( 'Kaina: ' ) . esc_html( $svc['price'] ) . '</span>';
	if ( '' !== $svc['old_price'] ) {
		$html .= ' <span class="mg-price-old">' . mg_sr( 'Įprasta kaina: ' ) . '<s>' . esc_html( $svc['old_price'] ) . '</s></span>';
	}

	return $html;
}

/** Paslaugos mygtukas: „Rezervuoti“ (Treatwell) arba „Skambinti“. */
function mg_service_button( array $svc, string $place, string $style = 'outline' ): string {
	if ( '' === $svc['url'] || 'none' === $svc['cta'] ) {
		return '';
	}

	if ( 'call' === $svc['cta'] ) {
		return mg_button(
			array(
				'label'      => 'Skambinti',
				'url'        => $svc['url'],
				'style'      => $style,
				'size'       => 'sm',
				'icon'       => 'phone',
				'icon_pos'   => 'start',
				'aria_label' => 'Skambinti dėl paslaugos: ' . $svc['title'],
			)
		);
	}

	return mg_button(
		array(
			'label'      => 'Rezervuoti',
			'url'        => $svc['url'],
			'style'      => $style,
			'size'       => 'sm',
			'icon'       => 'arrow-up-right',
			'service'    => $svc['title'] . ( '' !== $svc['duration'] ? ' (' . $svc['duration'] . ')' : '' ),
			'place'      => $place,
			'aria_label' => 'Rezervuoti: ' . $svc['title'] . ( '' !== $svc['duration'] ? ', ' . $svc['duration'] : '' ),
		)
	);
}

/**
 * Paslaugos kortelė (pradžios puslapio tinklelis).
 *
 * @param array $svc   Paslaugos duomenys.
 * @param int   $level Pavadinimo antraštės lygis (2–4).
 */
function mg_render_service_card( array $svc, int $level = 3 ): string {
	$h     = 'h' . max( 2, min( 4, $level ) );
	$html  = '<li class="mg-svc-card" data-mg-reveal>';
	$html .= '<div class="mg-svc-card__head">';
	if ( '' !== $svc['icon'] ) {
		$html .= '<span class="mg-svc-card__icon">' . mg_icon( $svc['icon'] ) . '</span>';
	}
	$html .= '<div>';
	$html .= '<' . $h . ' class="mg-svc-card__title">' . esc_html( $svc['title'] ) . '</' . $h . '>';
	if ( '' !== $svc['duration'] ) {
		$html .= '<p class="mg-svc-card__dur">' . mg_sr( 'Trukmė: ' ) . esc_html( $svc['duration'] ) . '</p>';
	}
	$html .= '</div></div>';

	if ( '' !== $svc['desc'] ) {
		$html .= '<p class="mg-svc-card__desc">' . esc_html( $svc['desc'] ) . '</p>';
	}

	$html .= '<div class="mg-svc-card__foot">';
	$html .= '<p class="mg-svc-card__price">' . mg_service_price_html( $svc );
	if ( '' !== $svc['old_price'] && '' !== $svc['promo_note'] ) {
		$html .= '<span class="mg-svc-card__promo">' . esc_html( $svc['promo_note'] ) . '</span>';
	}
	$html .= '</p>';
	$html .= mg_service_button( $svc, 'korteles' );
	$html .= '</div>';

	return $html . '</li>';
}

/** Paslaugos eilutė kainoraštyje (aprašymas išskleidžiamas). */
function mg_render_service_row( array $svc, bool $open = false ): string {
	$has_body = '' !== $svc['desc'] || $svc['bullets'];

	$summary  = '';
	$summary .= '<span class="mg-svc__icon">' . ( '' !== $svc['icon'] ? mg_icon( $svc['icon'] ) : '' ) . '</span>';
	$summary .= '<span class="mg-svc__heading">';
	$summary .= '<span class="mg-svc__title">' . esc_html( $svc['title'] ) . '</span>';
	$summary .= '<span class="mg-svc__meta">';
	if ( '' !== $svc['duration'] ) {
		$summary .= '<span class="mg-svc__dur">' . mg_sr( 'Trukmė: ' ) . esc_html( $svc['duration'] ) . '</span>';
	}
	if ( '' !== $svc['old_price'] ) {
		$summary .= '<span class="mg-tag">Akcija</span>';
	}
	$summary .= '</span></span>';
	$summary .= '<span class="mg-svc__price">' . mg_service_price_html( $svc ) . '</span>';

	$html = '<li class="mg-svc" id="paslauga-' . (int) $svc['id'] . '">';

	if ( $has_body ) {
		$html .= '<details class="mg-svc__details"' . ( $open ? ' open' : '' ) . '>';
		$html .= '<summary class="mg-svc__summary">' . $summary . '<span class="mg-svc__chev">' . mg_icon( 'caret-down' ) . mg_sr( 'Rodyti aprašymą' ) . '</span></summary>';
		$html .= '<div class="mg-svc__body">';
		if ( '' !== $svc['old_price'] && '' !== $svc['promo_note'] ) {
			$html .= '<p class="mg-svc__promo">' . mg_icon( 'tag' ) . '<span>' . esc_html( $svc['promo_note'] ) . '</span></p>';
		}
		if ( '' !== $svc['desc'] ) {
			$html .= '<p>' . esc_html( $svc['desc'] ) . '</p>';
		}
		if ( $svc['bullets'] ) {
			$html .= '<ul class="mg-checks">';
			foreach ( $svc['bullets'] as $bullet ) {
				$html .= '<li>' . mg_icon( 'check' ) . '<span>' . esc_html( $bullet ) . '</span></li>';
			}
			$html .= '</ul>';
		}
		$html .= '</div></details>';
	} else {
		$html .= '<div class="mg-svc__details"><div class="mg-svc__summary mg-svc__summary--static">' . $summary . '</div></div>';
	}

	$html .= '<div class="mg-svc__action">' . mg_service_button( $svc, 'kainorastis' ) . '</div>';

	return $html . '</li>';
}
