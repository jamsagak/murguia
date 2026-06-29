<?php

/* ------------------------------------------------------------------
   ACF FIELD GROUP — Sobre Nosotros
   Prefijo: ab_   |   Ajuste slug: nosotros
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_about_fields' );

function murguia_register_about_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	$id = murguia_ajuste_id( 'nosotros' );
	if ( ! $id ) return;

	acf_add_local_field_group( [
		'key'             => 'group_murg_about',
		'title'           => 'Sobre Nosotros — Contenido',
		'location'        => [
			[ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ],
		],
		'menu_order'      => 0,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'fields'          => [
			// ── Tab Hero ──
			[
				'key'   => 'field_ab_tab_hero',
				'label' => '🎬 Hero',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_ab_hero_imagen',
				'label'         => 'Imagen de fondo',
				'name'          => 'ab_hero_imagen',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Imagen de banner para la sección superior. Recomendado: WebP/JPG de 1920×1080px.',
			],

			// ── Tab Historia ──
			[
				'key'   => 'field_ab_tab_historia',
				'label' => '📖 Historia',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'          => 'field_ab_history_blocks',
				'label'        => 'Bloques de Historia',
				'name'         => 'ab_history_blocks',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Agregar bloque',
				'sub_fields'   => [
					[
						'key'           => 'field_ab_historia_img',
						'label'         => 'Imagen',
						'name'          => 'imagen',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					],
					[
						'key'   => 'field_ab_historia_alt',
						'label' => 'Alt de imagen',
						'name'  => 'alt',
						'type'  => 'text',
					],
					[
						'key'   => 'field_ab_historia_caption',
						'label' => 'Subtítulo / Leyenda de imagen',
						'name'  => 'caption',
						'type'  => 'text',
					],
					[
						'key'         => 'field_ab_history_copy',
						'label'       => 'Texto descriptivo',
						'name'        => 'copy',
						'type'        => 'textarea',
						'rows'        => 6,
						'instructions'=> 'Puedes separar párrafos con saltos de línea.',
					],
				],
			],

			// ── Tab Valores (Misión y Visión) ──
			[
				'key'   => 'field_ab_tab_valores',
				'label' => '✦ Misión y Visión',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'          => 'field_ab_values',
				'label'        => 'Misión y Visión',
				'name'         => 'ab_values',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Agregar bloque de valor',
				'sub_fields'   => [
					[
						'key'   => 'field_ab_valor_titulo',
						'label' => 'Título',
						'name'  => 'titulo',
						'type'  => 'text',
					],
					[
						'key'           => 'field_ab_valor_img',
						'label'         => 'Imagen',
						'name'          => 'imagen',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					],
					[
						'key'   => 'field_ab_valor_copy',
						'label' => 'Texto descriptivo',
						'name'  => 'copy',
						'type'  => 'textarea',
						'rows'  => 4,
					],
				],
			],
		],
	] );
}
