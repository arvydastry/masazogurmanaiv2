<?php
/**
 * Straipsnių skilties režimai ir senų adresų nukreipimai.
 *
 * Režimas keičiamas: Masažo Gurmanai → Nustatymai → Straipsniai.
 *   on     – skiltis rodoma;
 *   hidden – sąrašo ir meniu punkto nėra, bet jau parašyti straipsniai lieka pasiekiami
 *            per tiesioginę nuorodą ir „Google“ (neprarandami lankytojai iš paieškos);
 *   off    – visi straipsnių adresai laikinai nukreipiami į pradžios puslapį.
 *
 * Straipsniai niekada neištrinami — skiltį galima bet kada vėl įjungti.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

/** Dabartinis straipsnių režimas. */
function mg_blog_mode(): string {
	$mode = (string) mg_get( 'blog_mode' );

	return in_array( $mode, array( 'on', 'hidden', 'off' ), true ) ? $mode : 'hidden';
}

/** Straipsnių sąrašo puslapio ID (nustatytas „Nuostatos → Skaitymas“ arba puslapis „straipsniai“). */
function mg_blog_page_id(): int {
	$id = (int) get_option( 'page_for_posts' );
	if ( $id ) {
		return $id;
	}

	$page = get_page_by_path( 'straipsniai' );

	return $page ? (int) $page->ID : 0;
}

/** Nukreipimų sąrašas iš nustatymų: [ '/senas' => '/naujas' ]. */
function mg_redirect_map(): array {
	$map = array();

	foreach ( mg_lines( (string) mg_get( 'redirects' ) ) as $line ) {
		$parts = explode( '=>', $line, 2 );
		if ( 2 !== count( $parts ) ) {
			continue;
		}
		$from = '/' . trim( strtolower( trim( $parts[0] ) ), '/' );
		$to   = trim( $parts[1] );
		if ( '/' !== $from && '' !== $to ) {
			$map[ $from ] = $to;
		}
	}

	return $map;
}

add_action(
	'template_redirect',
	static function () {
		if ( is_admin() || is_preview() || is_customize_preview() ) {
			return;
		}

		// 1. Rankiniai nukreipimai (Nustatymai → Nukreipimai).
		$map = mg_redirect_map();
		if ( $map ) {
			$request = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
			$home    = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
			if ( '' !== $home && '/' !== $home && 0 === strpos( $request, $home ) ) {
				$request = substr( $request, strlen( rtrim( $home, '/' ) ) );
			}
			$request = '/' . trim( strtolower( $request ), '/' );

			if ( isset( $map[ $request ] ) ) {
				$target = $map[ $request ];
				$target = preg_match( '#^https?://#i', $target ) ? $target : home_url( $target );
				wp_redirect( esc_url_raw( $target ), 302 ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- adresus įveda tik administratorius.
				exit;
			}
		}

		// 2. Straipsnių skiltis.
		$mode = mg_blog_mode();
		if ( 'on' === $mode ) {
			return;
		}

		$blog_page = mg_blog_page_id();
		$is_list   = is_home() || is_category() || is_tag() || is_date() || is_author() || ( $blog_page && is_page( $blog_page ) );
		$is_post   = is_singular( 'post' );

		if ( ( $is_list && ! is_front_page() ) || ( 'off' === $mode && $is_post ) ) {
			wp_safe_redirect( home_url( '/' ), 302 );
			exit;
		}
	}
);

/** Kai skiltis nerodoma — jos punktas dingsta iš visų meniu (net jei meniu jis paliktas). */
add_filter(
	'wp_nav_menu_objects',
	static function ( array $items ): array {
		if ( 'on' === mg_blog_mode() ) {
			return $items;
		}

		$blog_page = mg_blog_page_id();
		$blog_path = $blog_page ? untrailingslashit( (string) wp_parse_url( (string) get_permalink( $blog_page ), PHP_URL_PATH ) ) : '';

		return array_filter(
			$items,
			static function ( $item ) use ( $blog_page, $blog_path ) {
				if ( $blog_page && 'page' === $item->object && (int) $item->object_id === $blog_page ) {
					return false;
				}
				// Meniu punktas galėjo būti įdėtas ir kaip „savo nuoroda“ į tą patį adresą.
				if ( '' !== $blog_path && untrailingslashit( (string) wp_parse_url( (string) $item->url, PHP_URL_PATH ) ) === $blog_path ) {
					return false;
				}
				if ( in_array( $item->object, array( 'category', 'post_tag', 'post' ), true ) && 'off' === mg_blog_mode() ) {
					return false;
				}

				return true;
			}
		);
	}
);

/** Svetainės žemėlapis (wp-sitemap.xml): neįtraukiame to, kas nukreipiama. */
add_filter(
	'wp_sitemaps_post_types',
	static function ( array $types ): array {
		if ( 'off' === mg_blog_mode() ) {
			unset( $types['post'] );
		}

		return $types;
	}
);

add_filter(
	'wp_sitemaps_taxonomies',
	static function ( array $taxonomies ): array {
		if ( 'on' !== mg_blog_mode() ) {
			unset( $taxonomies['category'], $taxonomies['post_tag'] );
		}

		return $taxonomies;
	}
);

add_filter( 'wp_sitemaps_add_provider', static function ( $provider, string $name ) {
	return 'users' === $name ? false : $provider;
}, 10, 2 );

add_filter(
	'wp_sitemaps_posts_query_args',
	static function ( array $args, string $post_type ): array {
		if ( 'page' !== $post_type ) {
			return $args;
		}

		$exclude = array();
		if ( 'on' !== mg_blog_mode() && mg_blog_page_id() ) {
			$exclude[] = mg_blog_page_id();
		}
		foreach ( array_keys( mg_redirect_map() ) as $path ) {
			$page = get_page_by_path( trim( $path, '/' ) );
			if ( $page ) {
				$exclude[] = (int) $page->ID;
			}
		}

		if ( $exclude ) {
			$existing             = isset( $args['post__not_in'] ) && is_array( $args['post__not_in'] ) ? $args['post__not_in'] : array();
			$args['post__not_in'] = array_values( array_unique( array_merge( $existing, $exclude ) ) );
		}

		return $args;
	},
	10,
	2
);

/** Kai skiltis nerodoma — antraštėje neskelbiame naujienų srautų (RSS). */
add_action(
	'wp',
	static function () {
		if ( 'on' !== mg_blog_mode() ) {
			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}
	}
);

/** Komentarai svetainėje nenaudojami. */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
