<?php
/**
 * Straipsnis.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="turinys" class="mg-main" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'mg-article' ); ?>>
			<header class="mg-section mg-pagehero">
				<div class="mg-container mg-container--narrow">
					<p class="mg-pagehero__meta">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					</p>
					<h1 class="mg-pagehero__title"><?php the_title(); ?></h1>
				</div>
			</header>

			<div class="mg-section mg-section--tight">
				<div class="mg-container mg-container--narrow">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="mg-article__image">
							<?php the_post_thumbnail( 'large' ); ?>
						</figure>
					<?php endif; ?>

					<div class="mg-prose">
						<?php the_content(); ?>
					</div>

					<aside class="mg-article__cta" aria-label="Rezervacija">
						<h2 class="mg-article__cta-title"><?php echo esc_html( mg_text( 'post_cta_title' ) ); ?></h2>
						<?php if ( '' !== mg_text( 'post_cta_text' ) ) : ?>
							<p><?php echo nl2br( esc_html( mg_text( 'post_cta_text' ) ) ); ?></p>
						<?php endif; ?>
						<div class="mg-actions">
							<?php
							echo mg_booking_button( array( 'place' => 'straipsnis' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

							$mg_services_page = get_page_by_path( 'paslaugos' );
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
					</aside>
				</div>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
