<?php

/* ------------------------------------------------------------------
   ACF FIELD GROUP — Tienda
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_shop_fields' );

function murguia_register_shop_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$id = murguia_ajuste_id( 'tienda' );
	if ( ! $id ) {
		return;
	}

	acf_add_local_field_group( [
		'key'             => 'group_murg_shop',
		'title'           => 'Tienda — Configuración',
		'location'        => [
			[ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ],
		],
		'menu_order'      => 10,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'fields'          => [

			/* ---- TAB: Encabezado ---- */
			[
				'key'       => 'field_murg_sh_tab_header',
				'label'     => '🏪 Encabezado',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_sh_eyebrow',
				'label'       => 'Eyebrow',
				'name'        => 'sh_eyebrow',
				'type'        => 'text',
				'placeholder' => 'Toda la Tienda',
			],
			[
				'key'         => 'field_murg_sh_titulo',
				'label'       => 'Título',
				'name'        => 'sh_titulo',
				'type'        => 'text',
				'placeholder' => 'Nuestra Colección',
				'instructions'=> 'Usa <em></em> para itálica.',
			],
			[
				'key'         => 'field_murg_sh_subtitulo',
				'label'       => 'Subtítulo',
				'name'        => 'sh_subtitulo',
				'type'        => 'text',
				'placeholder' => 'Lima, Perú',
			],

			/* ---- TAB: Configuración ---- */
			[
				'key'       => 'field_murg_sh_tab_config',
				'label'     => '⚙️ Configuración',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'          => 'field_murg_sh_por_pagina',
				'label'        => 'Productos por página',
				'name'         => 'sh_por_pagina',
				'type'         => 'number',
				'default_value'=> 12,
				'min'          => 4,
				'max'          => 48,
				'step'         => 4,
			],
			[
				'key'           => 'field_murg_sh_filtros',
				'label'         => 'Mostrar filtros de categoría',
				'name'          => 'sh_mostrar_filtros',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
			],
		],
	] );
}
