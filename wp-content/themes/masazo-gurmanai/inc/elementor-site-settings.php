<?php
/**
 * Elementor → Svetainės nustatymai → „Masažo Gurmanai“.
 *
 * Antraštės, poraštės, telefono juostos ir kitų bendrų vietų turinys (kontaktai, darbo laikas, rezervacijos
 * nuoroda, įvertinimas, tekstai) redaguojamas tiesiai Elementor redaktoriuje. Reikšmės laikomos ten pat,
 * kur ir „Masažo Gurmanai → Nustatymai“ (nustatymas „mg_settings“), todėl abi vietos visada rodo tą patį.
 *
 * Elementor redaktoriuje paspaudus antraštę, poraštę ar telefono juostą, ši skiltis atsidaro pati.
 *
 * @package MasazoGurmanai
 */

defined( 'ABSPATH' ) || exit;

/** Nustatymų skiltys, rodomos Elementor (techniniai nustatymai lieka tik administravime). */
function mg_kit_sections(): array {
	return array( 'rezervacija', 'kontaktai', 'laikas', 'ivertinimas', 'zenklas', 'tekstai' );
}

add_action(
	'elementor/kit/register_tabs',
	static function ( $kit ) {
		if ( ! class_exists( '\Elementor\Core\Kits\Documents\Tabs\Tab_Base' ) ) {
			return;
		}
		require_once MG_DIR . '/inc/elementor-site-settings-tab.php';
		$kit->register_tab( MG_Kit_Tab::ID, MG_Kit_Tab::class );
	}
);

/**
 * Elementor įrašytas reikšmes (raktai „mg_…“) paverčia „mg_settings“ formos duomenimis.
 *
 * @param array $changed Pakeisti laukai iš Elementor.
 * @return array Visi nustatymai po pakeitimo (išvalyti).
 */
function mg_kit_apply( array $changed ): array {
	$current = mg_settings( true );
	$schema  = mg_settings_schema();
	$input   = array();

	foreach ( $schema as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$input[ $key ] = $current[ $key ] ?? '';
		}
	}

	foreach ( mg_kit_sections() as $section_id ) {
		foreach ( $schema[ $section_id ]['fields'] as $key => $field ) {
			if ( 'hours' === $field['type'] ) {
				foreach ( array_keys( mg_days() ) as $num ) {
					foreach ( array( 'open', 'close' ) as $part ) {
						$id = 'mg_hours_' . $num . '_' . $part;
						if ( array_key_exists( $id, $changed ) ) {
							$input['hours'][ $num ][ $part ] = (string) $changed[ $id ];
						}
					}
					$id = 'mg_hours_' . $num . '_closed';
					if ( array_key_exists( $id, $changed ) ) {
						$input['hours'][ $num ]['closed'] = 'yes' === $changed[ $id ] ? 1 : 0;
					}
				}
				continue;
			}

			$id = 'mg_' . $key;
			if ( ! array_key_exists( $id, $changed ) ) {
				continue;
			}

			$value = $changed[ $id ];
			if ( 'checkbox' === $field['type'] ) {
				$value = 'yes' === $value ? 1 : 0;
			} elseif ( is_array( $value ) ) {
				$value = (string) ( $value['url'] ?? '' );
			}
			$input[ $key ] = $value;
		}
	}

	return mg_sanitize_settings( $input );
}

/* -------------------------------------------------------------------------
 * Redaktoriuje: paspaudus antraštę / poraštę atidaroma ši skiltis.
 * ---------------------------------------------------------------------- */

add_action(
	'elementor/preview/enqueue_styles',
	static function () {
		$css = '.elementor-editor-active .mg-header,.elementor-editor-active .mg-footer,.elementor-editor-active .mg-mobilebar{cursor:pointer}'
			. '.elementor-editor-active .mg-header:hover,.elementor-editor-active .mg-footer:hover,.elementor-editor-active .mg-mobilebar:hover{outline:2px dashed var(--mg-gold,#d4b06a);outline-offset:-2px}'
			. '.mg-edit-hint{position:absolute;z-index:9999;top:6px;left:50%;transform:translateX(-50%);padding:4px 10px;border-radius:4px;background:#d4b06a;color:#111;font:600 12px/1.4 system-ui,sans-serif;white-space:nowrap;pointer-events:none;display:none}'
			. '.elementor-editor-active .mg-footer{position:relative}'
			. '.elementor-editor-active .mg-header:hover>.mg-edit-hint,.elementor-editor-active .mg-footer:hover>.mg-edit-hint{display:block}';
		wp_register_style( 'mg-elementor-preview', false, array(), MG_VERSION );
		wp_enqueue_style( 'mg-elementor-preview' );
		wp_add_inline_style( 'mg-elementor-preview', $css );
	}
);

add_action(
	'elementor/preview/enqueue_scripts',
	static function () {
		$js = <<<'JS'
(function () {
	var targets = '.mg-header, .mg-footer, .mg-mobilebar';
	function addHints() {
		document.querySelectorAll('.mg-header, .mg-footer').forEach(function (el) {
			if (el.querySelector(':scope > .mg-edit-hint')) { return; }
			var hint = document.createElement('span');
			hint.className = 'mg-edit-hint';
			hint.textContent = 'Spustelėkite, kad redaguotumėte (Svetainės nustatymai → Masažo Gurmanai)';
			el.appendChild(hint);
		});
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', addHints); } else { addHints(); }

	document.addEventListener('click', function (event) {
		var el = event.target.closest(targets);
		var parentE = window.parent && window.parent.$e;
		if (!el || !parentE) { return; }
		event.preventDefault();
		event.stopPropagation();
		var open = parentE.run('panel/global/open');
		Promise.resolve(open).then(function () {
			parentE.route('panel/global/mg-site');
		});
	}, true);
})();
JS;
		wp_register_script( 'mg-elementor-preview', false, array(), MG_VERSION, true );
		wp_enqueue_script( 'mg-elementor-preview' );
		wp_add_inline_script( 'mg-elementor-preview', $js );
	}
);
