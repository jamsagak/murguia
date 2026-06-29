<?php

/* ------------------------------------------------------------------
   TIENDAS Y LAS 4CS - CPT, SCF y contenido inicial editable
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_tiendas_fields' );
add_action( 'init', 'murguia_register_tiendas_fields', 20 );
add_action( 'acf/init', 'murguia_register_tiendas_page_fields' );
add_action( 'init', 'murguia_register_tiendas_page_fields', 20 );

function murguia_register_tiendas_page_fields() {
	static $registered = false;
	if ( $registered ) return;
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	$id = murguia_ajuste_id( 'tiendas' );
	if ( ! $id ) return;
	$registered = true;

	acf_add_local_field_group( [
		'key' => 'group_murg_tiendas_page',
		'title' => 'Tiendas - Pagina',
		'location' => [ [ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ] ],
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'show_in_rest' => 1,
		'fields' => [
			[ 'key' => 'field_murg_tt_titulo', 'label' => 'Titulo', 'name' => 'tt_titulo', 'type' => 'text' ],
			[ 'key' => 'field_murg_tt_intro', 'label' => 'Texto introductorio', 'name' => 'tt_intro', 'type' => 'textarea', 'rows' => 3 ],
			[
				'key' => 'field_murg_tt_tiendas',
				'label' => 'Cards de tiendas',
				'name' => 'tt_tiendas',
				'type' => 'repeater',
				'layout' => 'block',
				'button_label' => 'Agregar tienda',
				'instructions' => 'Edita aqui las tiendas visibles en la pagina. Este es el panel principal para direccion, imagen principal, galeria y mapa.',
				'sub_fields' => [
					[ 'key' => 'field_murg_tt_tienda_visible', 'label' => 'Visible', 'name' => 'visible', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ],
					[ 'key' => 'field_murg_tt_tienda_nombre', 'label' => 'Nombre de tienda', 'name' => 'nombre', 'type' => 'text', 'required' => 1 ],
					[ 'key' => 'field_murg_tt_tienda_direccion', 'label' => 'Direccion', 'name' => 'direccion', 'type' => 'textarea', 'rows' => 2 ],
					[ 'key' => 'field_murg_tt_tienda_telefono', 'label' => 'Telefono', 'name' => 'telefono', 'type' => 'text' ],
					[ 'key' => 'field_murg_tt_tienda_horario', 'label' => 'Horario', 'name' => 'horario', 'type' => 'textarea', 'rows' => 3 ],
					[
						'key' => 'field_murg_tt_tienda_imagen_principal',
						'label' => 'Imagen principal',
						'name' => 'imagen_principal',
						'type' => 'image',
						'return_format' => 'array',
						'preview_size' => 'medium',
						'instructions' => 'Imagen del card. Se muestra completa, sin recorte. Recomendado: todas las tiendas con el mismo tamano/proporcion.',
					],
					[
						'key' => 'field_murg_tt_tienda_galeria',
						'label' => 'Galeria adicional',
						'name' => 'galeria',
						'type' => 'repeater',
						'layout' => 'block',
						'button_label' => 'Agregar imagen',
						'sub_fields' => [
							[ 'key' => 'field_murg_tt_tienda_galeria_imagen', 'label' => 'Imagen', 'name' => 'imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
						],
					],
					[ 'key' => 'field_murg_tt_tienda_whatsapp_texto', 'label' => 'Texto boton WhatsApp/contacto', 'name' => 'whatsapp_texto', 'type' => 'text' ],
					[ 'key' => 'field_murg_tt_tienda_whatsapp_url', 'label' => 'URL WhatsApp/contacto', 'name' => 'whatsapp_url', 'type' => 'url' ],
					[ 'key' => 'field_murg_tt_tienda_maps_url', 'label' => 'URL Google Maps fallback', 'name' => 'maps_url', 'type' => 'url' ],
					[
						'key' => 'field_murg_tt_tienda_mapa_iframe',
						'label' => 'Mapa embebido de Google Maps',
						'name' => 'mapa_iframe',
						'type' => 'textarea',
						'rows' => 4,
						'instructions' => 'Pega aqui el iframe de Google Maps o solo el valor src del iframe. Se abre en popup.',
					],
					[ 'key' => 'field_murg_tt_tienda_orden', 'label' => 'Orden', 'name' => 'orden', 'type' => 'number', 'default_value' => 0 ],
				],
			],
		],
	] );
}

function murguia_register_tiendas_fields() {
	static $registered = false;
	if ( $registered ) return;
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	$registered = true;

	acf_add_local_field_group( [
		'key' => 'group_murg_tiendas',
		'title' => 'Tienda - Datos del local',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'murguia_tienda' ] ] ],
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'show_in_rest' => 1,
		'fields' => [
			[ 'key' => 'field_murg_tienda_nombre', 'label' => 'Nombre de tienda', 'name' => 'tienda_nombre', 'type' => 'text', 'required' => 1 ],
			[ 'key' => 'field_murg_tienda_direccion', 'label' => 'Direccion', 'name' => 'tienda_direccion', 'type' => 'textarea', 'rows' => 2 ],
			[ 'key' => 'field_murg_tienda_telefono', 'label' => 'Telefono', 'name' => 'tienda_telefono', 'type' => 'text' ],
			[ 'key' => 'field_murg_tienda_horario', 'label' => 'Horario', 'name' => 'tienda_horario', 'type' => 'textarea', 'rows' => 3 ],
			[
				'key' => 'field_murg_tienda_imagen_principal',
				'label' => 'Imagen principal',
				'name' => 'tienda_imagen_principal',
				'type' => 'image',
				'return_format' => 'array',
				'preview_size' => 'medium',
				'instructions' => 'Imagen que aparece junto a los datos. Se muestra completa, sin recorte. Sube las 3 principales con el mismo tamano/proporcion.',
			],
			[
				'key' => 'field_murg_tienda_galeria',
				'label' => 'Galeria de imagenes adicionales',
				'name' => 'tienda_galeria',
				'type' => 'repeater',
				'layout' => 'block',
				'button_label' => 'Agregar imagen',
				'instructions' => 'Formato WebP/JPG. Recomendado: 1600x1000px o mayor, optimizado.',
				'sub_fields' => [
					[ 'key' => 'field_murg_tienda_galeria_imagen', 'label' => 'Imagen', 'name' => 'imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
				],
			],
			[ 'key' => 'field_murg_tienda_whatsapp_texto', 'label' => 'Texto boton WhatsApp/contacto', 'name' => 'tienda_whatsapp_texto', 'type' => 'text' ],
			[ 'key' => 'field_murg_tienda_whatsapp_url', 'label' => 'URL WhatsApp/contacto', 'name' => 'tienda_whatsapp_url', 'type' => 'url' ],
			[ 'key' => 'field_murg_tienda_maps_url', 'label' => 'URL Google Maps', 'name' => 'tienda_maps_url', 'type' => 'url' ],
			[
				'key' => 'field_murg_tienda_mapa_iframe',
				'label' => 'Mapa embebido de Google Maps',
				'name' => 'tienda_mapa_iframe',
				'type' => 'textarea',
				'rows' => 4,
				'instructions' => 'Pega aqui el iframe de Google Maps o solo el valor src del iframe. Se abre en popup.',
			],
			[ 'key' => 'field_murg_tienda_orden', 'label' => 'Orden', 'name' => 'tienda_orden', 'type' => 'number', 'default_value' => 0 ],
			[ 'key' => 'field_murg_tienda_visible', 'label' => 'Visible en la pagina', 'name' => 'tienda_visible', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ],
		],
	] );
}
