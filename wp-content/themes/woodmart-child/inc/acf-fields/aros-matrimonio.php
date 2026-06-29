<?php

/* ------------------------------------------------------------------
   AROS DE MATRIMONIO - Configurador "Diseña tu aro"
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_aros_matrimonio_fields' );

function murguia_register_aros_matrimonio_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	$id = murguia_ajuste_id( 'aros-matrimonio-page' );
	if ( ! $id ) return;

	acf_add_local_field_group( [
		'key'      => 'group_murg_aros_matrimonio',
		'title'    => 'Aros de Matrimonio - Contenido',
		'location' => [ [ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ] ],
		'menu_order' => 0,
		'position'   => 'normal',
		'style'      => 'default',
		'label_placement' => 'top',
		'fields'   => [
			/* ── Configurador "Diseña tu aro" ── */
			[ 'key' => 'field_aro_tab_config', 'label' => 'Configurador', 'name' => '', 'type' => 'tab' ],
			[ 'key' => 'field_aro_hero_eyebrow', 'label' => 'Hero - Eyebrow', 'name' => 'aro_hero_eyebrow', 'type' => 'text', 'default_value' => 'Disena tu aro' ],
			[ 'key' => 'field_aro_hero_titulo', 'label' => 'Hero - Titulo', 'name' => 'aro_hero_titulo', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Aros de matrimonio hechos para ustedes.' ],
			[ 'key' => 'field_aro_hero_intro', 'label' => 'Hero - Intro', 'name' => 'aro_hero_intro', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Configura el modelo, metal, ancho y grabado de tu aro. Al finalizar te enviamos la cotizacion por WhatsApp con todos los detalles.' ],
			[ 'key' => 'field_aro_hero_nota', 'label' => 'Hero - Nota legal', 'name' => 'aro_hero_nota', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Cada aro se trabaja a pedido. Los plazos de produccion se confirman al cotizar.' ],
			[ 'key' => 'field_aro_whatsapp_url', 'label' => 'WhatsApp URL', 'name' => 'aro_whatsapp_url', 'type' => 'url', 'instructions' => 'Si se deja vacio usa el de Anillos de Compromiso.' ],

			/* ── Landing /aros-matrimonio/ ── */
			[ 'key' => 'field_aml_tab_hero', 'label' => 'Landing — Hero', 'name' => '', 'type' => 'tab' ],
			[ 'key' => 'field_aml_hero_eyebrow', 'label' => 'Eyebrow', 'name' => 'aml_hero_eyebrow', 'type' => 'text',     'default_value' => 'Aros de matrimonio' ],
			[ 'key' => 'field_aml_hero_titulo',  'label' => 'Título',  'name' => 'aml_hero_titulo',  'type' => 'textarea', 'rows' => 2, 'default_value' => 'Diseña un aro para todos los días de la historia.' ],
			[ 'key' => 'field_aml_hero_intro',   'label' => 'Intro',   'name' => 'aml_hero_intro',   'type' => 'textarea', 'rows' => 3, 'default_value' => 'Elige modelo, metal, talla y grabado con asesoría personalizada. Creamos una propuesta a medida para cada pareja.' ],
			[ 'key' => 'field_aml_hero_nota',    'label' => 'Nota',    'name' => 'aml_hero_nota',    'type' => 'textarea', 'rows' => 2, 'default_value' => 'La propuesta se confirma por cotización privada. No hay precio final automático porque cada aro depende del metal, talla, ancho y grabado.' ],
			[ 'key' => 'field_aml_cta1_texto', 'label' => 'CTA principal — texto', 'name' => 'aml_cta1_texto', 'type' => 'text', 'default_value' => 'Diseña tu aro' ],
			[ 'key' => 'field_aml_cta1_url',   'label' => 'CTA principal — URL',   'name' => 'aml_cta1_url',   'type' => 'url',
			  'instructions' => 'Si está vacío, usa /disena-tu-aro/.' ],
			[ 'key' => 'field_aml_cta2_texto', 'label' => 'CTA secundario — texto', 'name' => 'aml_cta2_texto', 'type' => 'text', 'default_value' => 'Ver catálogo' ],
			[ 'key' => 'field_aml_cta2_url',   'label' => 'CTA secundario — URL',   'name' => 'aml_cta2_url',   'type' => 'url',
			  'instructions' => 'Si está vacío, usa el shop filtrado por categoría aros-de-matrimonio.' ],

			[ 'key' => 'field_aml_tab_pasos', 'label' => 'Landing — Pasos', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_aml_pasos', 'label' => 'Pasos de configuración', 'name' => 'aml_pasos',
				'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar paso',
				'sub_fields' => [
					[ 'key' => 'field_aml_paso_titulo', 'label' => 'Título', 'name' => 'titulo', 'type' => 'text' ],
					[ 'key' => 'field_aml_paso_texto',  'label' => 'Texto',  'name' => 'texto',  'type' => 'textarea', 'rows' => 2 ],
				],
			],

			[ 'key' => 'field_aml_tab_cta_final', 'label' => 'Landing — CTA final', 'name' => '', 'type' => 'tab' ],
			[ 'key' => 'field_aml_cta_titulo', 'label' => 'Título',   'name' => 'aml_cta_titulo', 'type' => 'text',     'default_value' => 'Comienza con una asesoría' ],
			[ 'key' => 'field_aml_cta_texto',  'label' => 'Texto',    'name' => 'aml_cta_texto',  'type' => 'textarea', 'rows' => 2, 'default_value' => 'Cuéntanos qué estilo buscan y prepararemos una propuesta para ambos aros.' ],
			[ 'key' => 'field_aml_cta_boton',  'label' => 'Texto botón', 'name' => 'aml_cta_boton', 'type' => 'text',  'default_value' => 'Cotizar por WhatsApp' ],
		],
	] );
}
