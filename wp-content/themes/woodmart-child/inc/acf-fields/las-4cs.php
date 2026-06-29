<?php

add_action( 'acf/init', 'murguia_register_4cs_fields' );
add_action( 'init', 'murguia_register_4cs_fields', 20 );

function murguia_register_4cs_fields() {
	static $registered = false;
	if ( $registered ) return;
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	$id = murguia_ajuste_id( 'las-4cs-page' );
	if ( ! $id ) return;
	$registered = true;

	acf_add_local_field_group( [
		'key' => 'group_murg_4cs',
		'title' => 'Las 4Cs - Contenido',
		'location' => [ [ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ] ],
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'show_in_rest' => 1,
		'fields' => [
			[ 'key' => 'field_murg_4cs_tab_hero', 'label' => 'Hero', 'name' => '', 'type' => 'tab' ],
			[ 'key' => 'field_murg_4cs_hero_eyebrow', 'label' => 'Eyebrow', 'name' => 'c4_hero_eyebrow', 'type' => 'text' ],
			[ 'key' => 'field_murg_4cs_hero_titulo', 'label' => 'Titulo', 'name' => 'c4_hero_titulo', 'type' => 'text' ],
			[ 'key' => 'field_murg_4cs_hero_subtitulo', 'label' => 'Subtitulo', 'name' => 'c4_hero_subtitulo', 'type' => 'textarea', 'rows' => 2 ],
			[ 'key' => 'field_murg_4cs_hero_intro', 'label' => 'Texto introductorio', 'name' => 'c4_hero_intro', 'type' => 'textarea', 'rows' => 4 ],
			[ 'key' => 'field_murg_4cs_hero_imagen', 'label' => 'Imagen hero', 'name' => 'c4_hero_imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'instructions' => 'Recomendado: imagen editorial de diamante o joya, minimo 1600x1000px.' ],
			[
				'key' => 'field_murg_4cs_secciones',
				'label' => 'Secciones 4Cs',
				'name' => 'c4_secciones',
				'type' => 'repeater',
				'layout' => 'block',
				'button_label' => 'Agregar seccion',
				'sub_fields' => [
					[ 'key' => 'field_murg_4cs_seccion_titulo', 'label' => 'Titulo de la C', 'name' => 'titulo', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_seccion_subtitulo', 'label' => 'Subtitulo opcional', 'name' => 'subtitulo', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_seccion_descripcion', 'label' => 'Descripcion', 'name' => 'descripcion', 'type' => 'textarea', 'rows' => 5 ],
					[ 'key' => 'field_murg_4cs_seccion_imagen', 'label' => 'Imagen principal', 'name' => 'imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
					[ 'key' => 'field_murg_4cs_seccion_imagen_sec', 'label' => 'Imagen secundaria opcional', 'name' => 'imagen_secundaria', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
					[ 'key' => 'field_murg_4cs_seccion_puntos', 'label' => 'Puntos destacados', 'name' => 'puntos', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'Un punto por linea.' ],
					[ 'key' => 'field_murg_4cs_seccion_orden', 'label' => 'Orden', 'name' => 'orden', 'type' => 'number' ],
				],
			],
			[ 'key' => 'field_murg_4cs_tab_escalas', 'label' => 'Escalas y ejemplos', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_murg_4cs_color_escala',
				'label' => 'Escala de color D-Z',
				'name' => 'c4_color_escala',
				'type' => 'repeater',
				'layout' => 'table',
				'button_label' => 'Agregar grado',
				'sub_fields' => [
					[ 'key' => 'field_murg_4cs_color_grado', 'label' => 'Grado', 'name' => 'grado', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_color_label', 'label' => 'Etiqueta', 'name' => 'etiqueta', 'type' => 'text' ],
				],
			],
			[
				'key' => 'field_murg_4cs_claridad_escala',
				'label' => 'Escala de claridad',
				'name' => 'c4_claridad_escala',
				'type' => 'repeater',
				'layout' => 'table',
				'button_label' => 'Agregar grado',
				'sub_fields' => [
					[ 'key' => 'field_murg_4cs_claridad_grado', 'label' => 'Grado', 'name' => 'grado', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_claridad_label', 'label' => 'Descripcion', 'name' => 'descripcion', 'type' => 'text' ],
				],
			],
			[
				'key' => 'field_murg_4cs_corte_conceptos',
				'label' => 'Conceptos de corte',
				'name' => 'c4_corte_conceptos',
				'type' => 'repeater',
				'layout' => 'block',
				'button_label' => 'Agregar concepto',
				'sub_fields' => [
					[ 'key' => 'field_murg_4cs_corte_concepto_titulo', 'label' => 'Titulo', 'name' => 'titulo', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_corte_concepto_texto', 'label' => 'Texto', 'name' => 'texto', 'type' => 'textarea', 'rows' => 2 ],
				],
			],
			[
				'key' => 'field_murg_4cs_carataje_ejemplos',
				'label' => 'Ejemplos de carataje',
				'name' => 'c4_carataje_ejemplos',
				'type' => 'repeater',
				'layout' => 'table',
				'button_label' => 'Agregar ejemplo',
				'sub_fields' => [
					[ 'key' => 'field_murg_4cs_carataje_valor', 'label' => 'Valor', 'name' => 'valor', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_carataje_label', 'label' => 'Etiqueta opcional', 'name' => 'etiqueta', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_carataje_imagen', 'label' => 'Imagen opcional', 'name' => 'imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ],
				],
			],
			[ 'key' => 'field_murg_4cs_tab_cta', 'label' => 'CTA', 'name' => '', 'type' => 'tab' ],
			[ 'key' => 'field_murg_4cs_cta_titulo', 'label' => 'Titulo CTA', 'name' => 'c4_cta_titulo', 'type' => 'text' ],
			[ 'key' => 'field_murg_4cs_cta_texto', 'label' => 'Texto CTA', 'name' => 'c4_cta_texto', 'type' => 'textarea', 'rows' => 2 ],
			[ 'key' => 'field_murg_4cs_cta_principal_texto', 'label' => 'Boton principal - texto', 'name' => 'c4_cta_principal_texto', 'type' => 'text' ],
			[ 'key' => 'field_murg_4cs_cta_principal_url', 'label' => 'Boton principal - URL', 'name' => 'c4_cta_principal_url', 'type' => 'url' ],
			[ 'key' => 'field_murg_4cs_cta_secundario_texto', 'label' => 'Boton secundario - texto', 'name' => 'c4_cta_secundario_texto', 'type' => 'text' ],
			[ 'key' => 'field_murg_4cs_cta_secundario_url', 'label' => 'Boton secundario - URL', 'name' => 'c4_cta_secundario_url', 'type' => 'url' ],
		],
	] );
}
