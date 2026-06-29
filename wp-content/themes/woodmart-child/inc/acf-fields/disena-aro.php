<?php
/**
 * SCF/ACF field group — Diseña tu Aro (configurador de aros de matrimonio).
 *
 * Adjunto al post de ajustes "aros-matrimonio-page" para que el cliente
 * encuentre todo lo relacionado a aros en una sola pantalla del admin.
 *
 * Prefijo: dar_  (Diseña tu ARo)
 */
defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', 'murguia_register_disena_aro_fields' );

function murguia_register_disena_aro_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$id = murguia_ajuste_id( 'aros-matrimonio-page' );
	if ( ! $id ) {
		return;
	}

	acf_add_local_field_group( [
		'key'             => 'group_murg_disena_aro',
		'title'           => 'Diseña tu Aro — Configurador',
		'location'        => [ [ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ] ],
		'menu_order'      => 5,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'fields'          => [
			/* Modelos */
			[ 'key' => 'field_dar_tab_modelos', 'label' => 'Modelos', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_dar_modelos', 'label' => 'Modelos del aro', 'name' => 'dar_modelos',
				'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar modelo',
				'sub_fields' => [
					[ 'key' => 'field_dar_modelo_label', 'label' => 'Nombre',     'name' => 'label', 'type' => 'text' ],
					[ 'key' => 'field_dar_modelo_desc',  'label' => 'Descripción', 'name' => 'desc',  'type' => 'textarea', 'rows' => 2 ],
				],
			],

			/* Metales */
			[ 'key' => 'field_dar_tab_metales', 'label' => 'Metales', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_dar_metales', 'label' => 'Metales disponibles', 'name' => 'dar_metales',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Agregar metal',
				'sub_fields' => [
					[ 'key' => 'field_dar_metal_label', 'label' => 'Nombre',      'name' => 'label', 'type' => 'text' ],
					[ 'key' => 'field_dar_metal_color', 'label' => 'Color (hex)', 'name' => 'color', 'type' => 'text',
					  'instructions' => 'Ej: #d4a843 (oro amarillo).' ],
				],
			],

			/* Tallas y ancho */
			[ 'key' => 'field_dar_tab_medidas', 'label' => 'Tallas y ancho', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_dar_tallas', 'label' => 'Tallas disponibles', 'name' => 'dar_tallas',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Agregar talla',
				'sub_fields' => [
					[ 'key' => 'field_dar_talla_valor', 'label' => 'Talla', 'name' => 'valor', 'type' => 'text' ],
				],
			],
			[ 'key' => 'field_dar_ancho_min',     'label' => 'Ancho mínimo (mm)',  'name' => 'dar_ancho_min',     'type' => 'number', 'min' => 1, 'max' => 30, 'step' => 0.5, 'default_value' => 2.0 ],
			[ 'key' => 'field_dar_ancho_max',     'label' => 'Ancho máximo (mm)',  'name' => 'dar_ancho_max',     'type' => 'number', 'min' => 1, 'max' => 30, 'step' => 0.5, 'default_value' => 10.0 ],
			[ 'key' => 'field_dar_ancho_default', 'label' => 'Ancho default (mm)', 'name' => 'dar_ancho_default', 'type' => 'number', 'min' => 1, 'max' => 30, 'step' => 0.5, 'default_value' => 4.0 ],
			[ 'key' => 'field_dar_ancho_step',    'label' => 'Ancho paso (mm)',    'name' => 'dar_ancho_step',    'type' => 'number', 'min' => 0.1, 'max' => 5, 'step' => 0.1, 'default_value' => 0.5 ],

			/* Tipografías de grabado */
			[ 'key' => 'field_dar_tab_tipografias', 'label' => 'Grabado', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_dar_tipografias', 'label' => 'Tipografías de grabado', 'name' => 'dar_tipografias',
				'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar tipografía',
				'sub_fields' => [
					[ 'key' => 'field_dar_tipo_slug',   'label' => 'Slug (clase CSS)', 'name' => 'slug',   'type' => 'text',
					  'instructions' => 'Solo letras minúsculas, sin espacios. Ej: imprenta, cursiva.' ],
					[ 'key' => 'field_dar_tipo_label',  'label' => 'Nombre visible', 'name' => 'label', 'type' => 'text' ],
					[ 'key' => 'field_dar_tipo_sample', 'label' => 'Muestra (texto corto)', 'name' => 'sample', 'type' => 'text' ],
				],
			],
			[ 'key' => 'field_dar_grabado_max',     'label' => 'Máximo caracteres grabado', 'name' => 'dar_grabado_max',     'type' => 'number', 'min' => 4, 'max' => 80, 'default_value' => 32 ],
			[ 'key' => 'field_dar_grabado_placeholder', 'label' => 'Placeholder del input', 'name' => 'dar_grabado_placeholder', 'type' => 'text', 'default_value' => 'Ej. Para siempre — 14/02/2027' ],
		],
	] );
}
