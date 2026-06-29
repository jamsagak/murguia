<?php

/* ------------------------------------------------------------------
   ACF FIELD GROUP — Campos por producto (se editan desde el propio
   producto, no desde Ajustes de Diseño). Incluye guía de tallas
   personalizada por pieza.
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_per_product_fields' );

function murguia_register_per_product_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'             => 'group_murg_per_product',
		'title'           => 'Murguía — Datos de la pieza',
		'location'        => [
			[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'product' ] ],
		],
		'menu_order'      => 10,
		'position'        => 'side',
		'style'           => 'default',
		'label_placement' => 'top',
		'fields'          => [
			[
				'key'           => 'field_murg_guia_tallas',
				'label'         => 'Guía de tallas',
				'name'          => 'murg_guia_tallas',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'mime_types'    => 'jpg,jpeg,png,webp,svg',
				'instructions'  => 'Imagen que muestra la guía de tallas para esta pieza. Si se carga, aparece un botón "Guía de tallas" sobre el "Añadir al carrito" que la abre en un modal. Si se deja vacío, el botón no aparece. Formato recomendado: PNG/WebP, 1200x1200px máx, <300KB.',
			],
			[
				'key'           => 'field_murg_guia_tallas_titulo',
				'label'         => 'Título del modal de tallas',
				'name'          => 'murg_guia_tallas_titulo',
				'type'          => 'text',
				'placeholder'   => 'Guía de tallas',
				'instructions'  => 'Opcional. Título que aparece sobre la imagen en el modal.',
			],
		],
	] );
}
