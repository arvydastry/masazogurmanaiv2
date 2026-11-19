<?php
/**
 * Reklamos sekimas.
 *
 * 1) „Google Ads“ žyma (anksčiau buvo įrašyta senosios temos header.php faile).
 * 2) Rezervacijos nuorodos paspaudimo įvykiai — siunčia assets/js/main.js
 *    (GA4, „Google Ads“ konversija, „Meta Pixel“), jei tos sistemos svetainėje įdiegtos.
 *
 * Žymos įterpiamos tik tikroje (production) svetainėje — vietinėje ir bandomojoje
 * aplinkoje reklamos statistika neteršiama.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

/** Ar galima įterpti sekimo žymas. */
function mg_tracking_allowed(): bool {
	return 'production' === wp_get_environment_type() && ! is_preview() && ! is_customize_preview();
}

add_action(
	'wp_head',
	static function () {
		$id = trim( (string) mg_get( 'gads_id' ) );
		if ( '' === $id || ! preg_match( '/^AW-[0-9]+$/', $id ) || ! mg_tracking_allowed() ) {
			return;
		}
		?>
<!-- Google Ads (Masažo Gurmanai tema) -->
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', <?php echo wp_json_encode( $id ); ?>);
(function () {
	// Biblioteką įkeliame tik tada, jei jos dar neįkėlė kitas įskiepis (pvz. „Site Kit“).
	function load() {
		if (document.querySelector('script[src*="googletagmanager.com/gtag/js"]')) { return; }
		var s = document.createElement('script');
		s.async = true;
		s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(<?php echo wp_json_encode( $id ); ?>);
		document.head.appendChild(s);
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', load); } else { load(); }
})();
</script>
		<?php
	},
	40
);
