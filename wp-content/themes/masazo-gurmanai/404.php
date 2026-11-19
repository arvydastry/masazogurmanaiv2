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
			<h1 class="mg-pagehero__title">Puslapis nerastas</h1>
			<p class="mg-pagehero__lead">Tokio adreso svetainėje nėra. Gali būti, kad puslapis buvo perkeltas.</p>
			<div class="mg-actions">
				<?php
				echo mg_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'label' => 'Į pradžios puslapį',
						'url'   => home_url( '/' ),
					)
				);
				if ( $mg_services_page ) {
					echo mg_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label' => 'Paslaugos ir kainos',
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
