<?php
/**
 * Puslapis nerastas.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

get_header();

$mg_services_page = get_page_by_path( 'paslaugos' );
?>
<main id="turinys" class="mg-main" tabindex="-1">
	<section class="mg-section mg-notfound">
		<div class="mg-container mg-container--narrow">
			<h1 class="mg-pagehero__title"><?php echo esc_html( mg_text( 'notfound_title' ) ); ?></h1>
			<?php if ( '' !== mg_text( 'notfound_text' ) ) : ?>
				<p class="mg-pagehero__lead"><?php echo nl2br( esc_html( mg_text( 'notfound_text' ) ) ); ?></p>
			<?php endif; ?>
			<div class="mg-actions">
				<?php
				echo mg_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'label' => mg_text( 'notfound_home_label' ),
						'url'   => home_url( '/' ),
					)
				);
				if ( $mg_services_page ) {
					echo mg_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label' => mg_text( 'services_link_label' ),
							'url'   => (string) get_permalink( $mg_services_page ),
							'style' => 'outline',
						)
					);
				}
				?>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
