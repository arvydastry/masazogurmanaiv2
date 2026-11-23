<?php
/**
 * Masažo Gurmanai — temos paleidimas.
 *
 * Kaip sudaryta:
 *   inc/helpers.php        pagalbinės funkcijos (ikonos, mygtukai, telefonas, darbo laikas)
 *   inc/settings.php       nustatymai: „Masažo Gurmanai → Nustatymai“ (kontaktai, Treatwell nuoroda, darbo laikas…)
 *   inc/setup.php          temos palaikymas, meniu, stiliai ir scenarijai
 *   inc/services.php       įrašų tipas „Paslaugos“ (pavadinimas, kaina, trukmė, Treatwell nuoroda)
 *   inc/blog.php           straipsnių skilties režimai (rodoma / paslėpta / išjungta) ir nukreipimai
 *   inc/seo.php            antraštė, aprašymas, Open Graph ir JSON-LD (jei nėra SEO įskiepio)
 *   inc/tracking.php       „Google Ads“ žyma ir rezervacijos paspaudimų įvykiai
 *   inc/elementor.php      Elementor integracija ir valdikliai (inc/widgets/)
 *   inc/setup-content.php  pradinio turinio sukūrimas („Masažo Gurmanai → Pradinis turinys“ arba `wp mg setup`)
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

define( 'MG_VERSION', '2.0.2' );
define( 'MG_DIR', get_template_directory() );
define( 'MG_URI', get_template_directory_uri() );

require MG_DIR . '/inc/helpers.php';
require MG_DIR . '/inc/settings.php';
require MG_DIR . '/inc/setup.php';
require MG_DIR . '/inc/services.php';
require MG_DIR . '/inc/blog.php';
require MG_DIR . '/inc/seo.php';
require MG_DIR . '/inc/tracking.php';
require MG_DIR . '/inc/elementor.php';
require MG_DIR . '/inc/setup-content.php';
require MG_DIR . '/inc/migrate.php';
