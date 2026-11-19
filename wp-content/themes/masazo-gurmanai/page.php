<?php
/**
 * Puslapis.
 *
 * Jei puslapis sukurtas su Elementor — rodomas tik jo turinys (per visą plotį).
 * Kitu atveju — antraštė ir paprastas tekstas (pvz. privatumo politikai).
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

		if ( mg_is_elementor_page( get_the_ID() ) ) :
			the_content();
		else :
			?>
			<header class="mg-section mg-pagehero">
				<div class="mg-container">
					<h1 class="mg-pagehero__title"><?php the_title(); ?></h1>
				</div>
			</header>
			<div class="mg-section mg-section--tight">
				<div class="mg-container mg-container--narrow mg-prose">
					<?php the_content(); ?>
				</div>
			</div>
			<?php
		endif;
	endwhile;
	?>
</main>
<?php
get_footer();
