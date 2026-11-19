<?php
/**
 * Straipsnių sąrašas (ir visi kiti rinkiniai: kategorijos, paieška).
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	$mg_title = 'Paieškos rezultatai: „' . get_search_query() . '“';
	$mg_lead  = '';
} elseif ( is_home() ) {
	$mg_posts_page = (int) get_option( 'page_for_posts' );
	$mg_title      = $mg_posts_page ? get_the_title( $mg_posts_page ) : 'Straipsniai';
	$mg_lead       = 'Naudingi patarimai ir informacija apie masažą, kūno priežiūrą ir atsipalaidavimą.';
} else {
	$mg_title = wp_strip_all_tags( get_the_archive_title() );
	$mg_lead  = wp_strip_all_tags( get_the_archive_description() );
}
?>
<main id="turinys" class="mg-main" tabindex="-1">
	<header class="mg-section mg-pagehero">
		<div class="mg-container">
			<h1 class="mg-pagehero__title"><?php echo esc_html( $mg_title ); ?></h1>
			<?php if ( '' !== $mg_lead ) : ?>
				<p class="mg-pagehero__lead"><?php echo esc_html( $mg_lead ); ?></p>
			<?php endif; ?>
		</div>
	</header>

	<div class="mg-section mg-section--tight">
		<div class="mg-container">
			<?php if ( have_posts() ) : ?>
				<ul class="mg-posts">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<li class="mg-posts__item">
							<article>
								<p class="mg-posts__date"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
								<h2 class="mg-posts__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p class="mg-posts__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 32, '…' ) ); ?></p>
							</article>
						</li>
						<?php
					endwhile;
					?>
				</ul>

				<?php
				the_posts_pagination(
					array(
						'mid_size'           => 1,
						'prev_text'          => 'Ankstesni',
						'next_text'          => 'Kiti',
						'screen_reader_text' => 'Straipsnių puslapiai',
						'class'              => 'mg-pagination',
					)
				);
				?>
			<?php else : ?>
				<p>Kol kas čia nieko nėra.</p>
			<?php endif; ?>
		</div>
	</div>
</main>
<?php
get_footer();
