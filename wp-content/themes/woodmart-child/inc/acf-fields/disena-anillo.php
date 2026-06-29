<?php
/**
 * SCF/ACF field group — Diseña tu Anillo (configurador de anillos de compromiso).
 *
 * Adjunto al post de ajustes "anillos-compromiso-page" para que el cliente
 * encuentre todo lo relacionado a anillos en una sola pantalla del admin.
 *
 * Prefijo: da_  (Diseña tu Anillo)
 */
defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', 'murguia_register_disena_anillo_fields' );

function murguia_register_disena_anillo_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$id = murguia_ajuste_id( 'anillos-compromiso-page' );
	if ( ! $id ) {
		return;
	}

	acf_add_local_field_group( [
		'key'             => 'group_murg_disena_anillo',
		'title'           => 'Diseña tu Anillo — Configurador',
		'location'        => [ [ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ] ],
		'menu_order'      => 5,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'fields'          => [
			/* Hero */
			[ 'key' => 'field_da_tab_hero', 'label' => 'Hero', 'name' => '', 'type' => 'tab' ],
			[ 'key' => 'field_da_hero_eyebrow', 'label' => 'Eyebrow',  'name' => 'da_hero_eyebrow', 'type' => 'text',     'default_value' => 'Diseña tu anillo' ],
			[ 'key' => 'field_da_hero_titulo',  'label' => 'Título',   'name' => 'da_hero_titulo',  'type' => 'textarea', 'rows' => 2, 'default_value' => 'Configura una pieza para una historia única.' ],
			[ 'key' => 'field_da_hero_intro',   'label' => 'Intro',    'name' => 'da_hero_intro',   'type' => 'textarea', 'rows' => 3, 'default_value' => 'Selecciona los elementos base de tu anillo de compromiso. Al final verás un resumen para solicitar una cotización privada con Murguía.' ],
			[ 'key' => 'field_da_hero_nota',    'label' => 'Nota legal','name' => 'da_hero_nota',   'type' => 'textarea', 'rows' => 2, 'default_value' => 'No mostramos precio final en línea. La cotización depende de disponibilidad de diamante, metal, talla y taller.' ],
			[ 'key' => 'field_da_wa_url',       'label' => 'WhatsApp URL (cotización)', 'name' => 'da_wa_url', 'type' => 'url',
			  'instructions' => 'Si está vacío, usa el URL de Anillos de Compromiso (ac_whatsapp_url).' ],

			/* Modelos */
			[ 'key' => 'field_da_tab_modelos', 'label' => 'Modelos', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_da_modelos', 'label' => 'Modelos del anillo', 'name' => 'da_modelos',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Agregar modelo',
				'sub_fields' => [
					[ 'key' => 'field_da_modelo_label', 'label' => 'Nombre', 'name' => 'label', 'type' => 'text' ],
				],
			],

			/* Formas de diamante */
			[ 'key' => 'field_da_tab_formas', 'label' => 'Formas de diamante', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_da_formas', 'label' => 'Formas de diamante', 'name' => 'da_formas',
				'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar forma',
				'sub_fields' => [
					[ 'key' => 'field_da_forma_label', 'label' => 'Nombre',  'name' => 'label', 'type' => 'text' ],
					[ 'key' => 'field_da_forma_img',   'label' => 'Imagen PNG', 'name' => 'imagen', 'type' => 'image',
					  'return_format' => 'array', 'preview_size' => 'thumbnail',
					  'instructions' => 'PNG con fondo transparente, ~160x160px.' ],
				],
			],

			/* Metales */
			[ 'key' => 'field_da_tab_metales', 'label' => 'Metales', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_da_metales', 'label' => 'Metales disponibles', 'name' => 'da_metales',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Agregar metal',
				'sub_fields' => [
					[ 'key' => 'field_da_metal_label', 'label' => 'Nombre', 'name' => 'label', 'type' => 'text' ],
					[ 'key' => 'field_da_metal_color', 'label' => 'Color (hex)', 'name' => 'color', 'type' => 'text',
					  'instructions' => 'Ej: #d4a843 (oro amarillo).' ],
				],
			],

			/* Tallas y quilates */
			[ 'key' => 'field_da_tab_medidas', 'label' => 'Tallas y quilates', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_da_tallas', 'label' => 'Tallas disponibles', 'name' => 'da_tallas',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Agregar talla',
				'sub_fields' => [
					[ 'key' => 'field_da_talla_valor', 'label' => 'Talla', 'name' => 'valor', 'type' => 'text' ],
				],
			],
			[
				'key' => 'field_da_origenes', 'label' => 'Orígenes de diamante', 'name' => 'da_origenes',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Agregar origen',
				'sub_fields' => [
					[ 'key' => 'field_da_origen_label', 'label' => 'Nombre', 'name' => 'label', 'type' => 'text' ],
				],
			],
			[ 'key' => 'field_da_quilates_min',     'label' => 'Quilates mínimo',  'name' => 'da_quilates_min',     'type' => 'number', 'min' => 0.1, 'max' => 10, 'step' => 0.05, 'default_value' => 0.30 ],
			[ 'key' => 'field_da_quilates_max',     'label' => 'Quilates máximo',  'name' => 'da_quilates_max',     'type' => 'number', 'min' => 0.1, 'max' => 10, 'step' => 0.05, 'default_value' => 3.00 ],
			[ 'key' => 'field_da_quilates_default', 'label' => 'Quilates default', 'name' => 'da_quilates_default', 'type' => 'number', 'min' => 0.1, 'max' => 10, 'step' => 0.05, 'default_value' => 1.00 ],
			[ 'key' => 'field_da_quilates_step',    'label' => 'Quilates paso',    'name' => 'da_quilates_step',    'type' => 'number', 'min' => 0.01, 'max' => 1, 'step' => 0.01, 'default_value' => 0.10 ],
		],
	] );
}
