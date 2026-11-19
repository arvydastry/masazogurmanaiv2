<?php
/**
 * Antraštė.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

$mg_phone_href = mg_phone_href();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="mg-skip" href="#turinys">Pereiti prie turinio</a>

<?php
// Jei įdiegtas „Elementor Pro“ ir jame sukurta antraštė — rodome ją, kitaip temos antraštę.
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) :
	?>
	<div class="mg-header-sentinel" aria-hidden="true"></div>
	<header class="mg-header" id="mg-header">
		<div class="mg-container mg-header__in">
			<?php echo mg_logo( 'header' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<nav class="mg-nav" aria-label="Pagrindinis meniu">
				<?php echo mg_nav_menu( 'primary', 'mg-nav__list' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</nav>

			<div class="mg-header__actions">
				<?php if ( '' !== $mg_phone_href ) : ?>
					<a class="mg-header__phone" href="<?php echo esc_attr( $mg_phone_href ); ?>">
						<?php echo mg_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="mg-header__phone-num"><?php echo esc_html( mg_phone_display() ); ?></span>
						<?php echo mg_sr( 'Skambinti' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php endif; ?>

				<?php
				echo mg_booking_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'size'  => 'sm',
						'icon'  => '',
						'place' => 'antraste',
						'class' => 'mg-header__cta',
					)
				);
				?>

				<button class="mg-burger" type="button" aria-haspopup="dialog" aria-controls="mg-drawer" data-mg-drawer-open>
					<?php echo mg_icon( 'list' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="mg-sr">Atidaryti meniu</span>
				</button>
			</div>
		</div>
	</header>

	<dialog class="mg-drawer" id="mg-drawer" aria-label="Meniu">
		<div class="mg-drawer__head">
			<?php echo mg_logo( 'drawer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<button class="mg-drawer__close" type="button" data-mg-drawer-close autofocus>
				<?php echo mg_icon( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="mg-sr">Uždaryti meniu</span>
			</button>
		</div>

		<nav class="mg-drawer__nav" aria-label="Pagrindinis meniu">
			<?php echo mg_nav_menu( 'primary', 'mg-drawer__list' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</nav>

		<div class="mg-drawer__foot">
			<?php
			echo mg_booking_button( array( 'place' => 'meniu' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( '' !== $mg_phone_href ) {
				echo mg_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'label'    => mg_phone_display(),
						'url'      => $mg_phone_href,
						'style'    => 'outline',
						'icon'     => 'phone',
						'icon_pos' => 'start',
					)
				);
			}
			?>
			<p class="mg-drawer__meta">
				<?php echo esc_html( mg_address_line() ); ?><br>
				<?php echo esc_html( mg_hours_short() ); ?>
			</p>
		</div>
	</dialog>
	<?php
endif;
