<?php
/**
 * Poraštė.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

$mg_phone_href = mg_phone_href();
$mg_email      = (string) mg_get( 'email' );
$mg_map_url    = (string) mg_get( 'map_url' );
$mg_rating     = trim( (string) mg_get( 'rating_value' ) );
$mg_rating_url = (string) mg_get( 'rating_url' );
$mg_socials    = array_filter(
	array(
		'facebook-logo'  => array( (string) mg_get( 'facebook_url' ), 'Facebook' ),
		'instagram-logo' => array( (string) mg_get( 'instagram_url' ), 'Instagram' ),
	),
	static function ( $item ) {
		return '' !== $item[0];
	}
);

// Jei įdiegtas „Elementor Pro“ ir jame sukurta poraštė — rodome ją, kitaip temos poraštę.
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) :
	?>
	<footer class="mg-footer">
		<div class="mg-container">
			<div class="mg-footer__top">
				<div class="mg-footer__brand">
					<?php echo mg_logo( 'footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<p class="mg-footer__about"><?php echo esc_html( (string) mg_get( 'footer_about' ) ); ?></p>

					<?php if ( '' !== $mg_rating && '' !== $mg_rating_url ) : ?>
						<a class="mg-footer__rating"<?php echo mg_link_attrs( $mg_rating_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo mg_stars(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $mg_rating . ' iš 5, ' . mg_get( 'rating_count' ) . ' atsiliepimai „' . mg_get( 'rating_source' ) . '“' ); ?></span>
							<?php echo mg_sr( ' (atsidaro naujame skirtuke)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endif; ?>

					<?php if ( $mg_socials ) : ?>
						<ul class="mg-footer__social">
							<?php foreach ( $mg_socials as $mg_icon_name => $mg_social ) : ?>
								<li>
									<a<?php echo mg_link_attrs( $mg_social[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
										<?php echo mg_icon( $mg_icon_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php echo mg_sr( $mg_social[1] . ' (atsidaro naujame skirtuke)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<nav class="mg-footer__col" aria-labelledby="mg-footer-nav">
						<h2 class="mg-footer__title" id="mg-footer-nav">Nuorodos</h2>
						<?php echo mg_nav_menu( 'footer', 'mg-footer__list' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</nav>
				<?php endif; ?>

				<div class="mg-footer__col">
					<h2 class="mg-footer__title">Kontaktai</h2>
					<ul class="mg-footer__list mg-footer__list--contacts">
						<li>
							<?php echo mg_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php if ( '' !== $mg_map_url ) : ?>
								<a<?php echo mg_link_attrs( $mg_map_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php echo esc_html( mg_address_line() ); ?>
									<?php echo mg_sr( ' – atidaryti žemėlapyje (naujame skirtuke)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php else : ?>
								<span><?php echo esc_html( mg_address_line() ); ?></span>
							<?php endif; ?>
						</li>
						<?php if ( '' !== $mg_phone_href ) : ?>
							<li>
								<?php echo mg_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<a href="<?php echo esc_attr( $mg_phone_href ); ?>"><?php echo esc_html( mg_phone_display() ); ?></a>
							</li>
						<?php endif; ?>
						<?php if ( '' !== $mg_email ) : ?>
							<li>
								<?php echo mg_icon( 'envelope-simple' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<a href="<?php echo esc_attr( 'mailto:' . antispambot( $mg_email ) ); ?>"><?php echo esc_html( antispambot( $mg_email ) ); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				</div>

				<div class="mg-footer__col">
					<h2 class="mg-footer__title">Darbo laikas</h2>
					<?php echo mg_hours_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>

			<div class="mg-footer__bottom">
				<p>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . mg_get( 'brand_name' ) . '. ' . mg_get( 'owner_name' ) . '.' ); ?> Visos teisės saugomos.</p>
				<?php if ( get_privacy_policy_url() ) : ?>
					<p><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Privatumo politika</a></p>
				<?php endif; ?>
			</div>
		</div>
	</footer>
	<?php
endif;

if ( mg_get( 'mobile_bar' ) ) :
	?>
	<div class="mg-mobilebar" role="region" aria-label="Greita rezervacija">
		<?php if ( '' !== $mg_phone_href ) : ?>
			<a class="mg-mobilebar__call" href="<?php echo esc_attr( $mg_phone_href ); ?>">
				<?php echo mg_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span>Skambinti</span>
			</a>
		<?php endif; ?>
		<?php
		echo mg_booking_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'place' => 'mobili-juosta',
				'class' => 'mg-mobilebar__book',
				'icon'  => 'calendar-check',
			)
		);
		?>
	</div>
	<?php
endif;

wp_footer();
?>
</body>
</html>
