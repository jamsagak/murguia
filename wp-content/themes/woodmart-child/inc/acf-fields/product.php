<?php

/* ------------------------------------------------------------------
   ACF FIELD GROUP — Producto (ajustes globales de producto)
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_product_fields' );

function murguia_register_product_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$id = murguia_ajuste_id( 'producto' );
	if ( ! $id ) {
		return;
	}

	acf_add_local_field_group( [
		'key'             => 'group_murg_product',
		'title'           => 'Producto — Ajustes Globales',
		'location'        => [
			[ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ],
		],
		'menu_order'      => 20,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'fields'          => [

			/* ---- TAB: Etiquetas ---- */
			[
				'key'       => 'field_murg_prod_tab_badges',
				'label'     => '🏷 Etiquetas',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_prod_badge_nuevo',
				'label'       => 'Etiqueta "Nuevo"',
				'name'        => 'prod_badge_nuevo',
				'type'        => 'text',
				'placeholder' => 'Nuevo',
			],
			[
				'key'         => 'field_murg_prod_badge_oferta',
				'label'       => 'Etiqueta "Oferta"',
				'name'        => 'prod_badge_oferta',
				'type'        => 'text',
				'placeholder' => 'Oferta',
			],
			[
				'key'         => 'field_murg_prod_badge_agotado',
				'label'       => 'Etiqueta "Agotado"',
				'name'        => 'prod_badge_agotado',
				'type'        => 'text',
				'placeholder' => 'Agotado',
			],

			/* ---- TAB: Textos ---- */
			[
				'key'       => 'field_murg_prod_tab_textos',
				'label'     => '✏️ Textos',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_prod_ref_prefix',
				'label'       => 'Prefijo de referencia',
				'name'        => 'prod_ref_prefix',
				'type'        => 'text',
				'placeholder' => 'REF.',
				'instructions'=> 'Aparece antes del SKU del producto. Ej: "REF. MG-001"',
			],
			[
				'key'         => 'field_murg_prod_cita_texto',
				'label'       => 'Texto del enlace de cita',
				'name'        => 'prod_cita_texto',
				'type'        => 'text',
				'placeholder' => '¿Preguntas? Solicite una cita personal →',
			],

			/* ---- TAB: Confianza (trust bar) ---- */
			[
				'key'       => 'field_murg_prod_tab_trust',
				'label'     => '✦ Barra de confianza',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_prod_trust_1',
				'label'       => 'Ítem 1 (envío)',
				'name'        => 'prod_trust_1',
				'type'        => 'text',
				'placeholder' => 'Envío seguro a todo el Perú',
				'instructions'=> 'Vacío = ocultar. El icono es un camión de entrega.',
			],
			[
				'key'         => 'field_murg_prod_trust_2',
				'label'       => 'Ítem 2 (estuche)',
				'name'        => 'prod_trust_2',
				'type'        => 'text',
				'placeholder' => 'Estuche de presentación incluido',
				'instructions'=> 'Vacío = ocultar. Icono de caja.',
			],
			[
				'key'         => 'field_murg_prod_trust_3',
				'label'       => 'Ítem 3 (garantía)',
				'name'        => 'prod_trust_3',
				'type'        => 'text',
				'placeholder' => 'Garantía de por vida',
				'instructions'=> 'Vacío = ocultar. Icono de escudo.',
			],
			[
				'key'         => 'field_murg_prod_trust_4',
				'label'       => 'Ítem 4 (certificado)',
				'name'        => 'prod_trust_4',
				'type'        => 'text',
				'placeholder' => 'Certificado de autenticidad',
				'instructions'=> 'Vacío = ocultar. Icono de medalla.',
			],

			/* ---- TAB: Pestañas de información ---- */
			[
				'key'       => 'field_murg_prod_tab_tabs',
				'label'     => '📑 Pestañas de información',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'          => 'field_murg_prod_tab_desc_label',
				'label'        => 'Etiqueta "Descripción"',
				'name'         => 'prod_tab_desc_label',
				'type'         => 'text',
				'placeholder'  => 'Descripción',
				'instructions' => 'Nombre visible de la pestaña de descripción (el contenido sale de la descripción larga de WooCommerce).',
			],
			[
				'key'          => 'field_murg_prod_tab_detalles_label',
				'label'        => 'Etiqueta "Detalles técnicos"',
				'name'         => 'prod_tab_detalles_label',
				'type'         => 'text',
				'placeholder'  => 'Detalles técnicos',
			],
			[
				'key'          => 'field_murg_prod_detalles_texto',
				'label'        => 'Contenido "Detalles técnicos"',
				'name'         => 'prod_detalles_texto',
				'type'         => 'textarea',
				'rows'         => 6,
				'placeholder'  => "Oro de 18 quilates. Pureza certificada.\nPiedras naturales engastadas a mano.\nDiseño exclusivo de la Casa Murguía.",
				'instructions' => 'Si se deja vacío, la pestaña no aparece. Se muestran saltos de línea automáticamente.',
			],
			[
				'key'          => 'field_murg_prod_tab_cuidado_label',
				'label'        => 'Etiqueta "Cuidado de la pieza"',
				'name'         => 'prod_tab_cuidado_label',
				'type'         => 'text',
				'placeholder'  => 'Cuidado de la pieza',
			],
			[
				'key'          => 'field_murg_prod_cuidado_texto',
				'label'        => 'Contenido "Cuidado de la pieza"',
				'name'         => 'prod_cuidado_texto',
				'type'         => 'textarea',
				'rows'         => 6,
				'placeholder'  => "Limpie con un paño suave y seco.\nEvite el contacto con perfumes y químicos.\nGuarde en su estuche para evitar rayones.",
				'instructions' => 'Si se deja vacío, la pestaña no aparece.',
			],

			/* ---- TAB: Relacionados ---- */
			[
				'key'       => 'field_murg_prod_tab_related',
				'label'     => '↔ Productos relacionados',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'          => 'field_murg_prod_related_cantidad',
				'label'        => 'Cantidad de productos relacionados',
				'name'         => 'prod_related_cantidad',
				'type'         => 'number',
				'default_value'=> 6,
				'min'          => 3,
				'max'          => 12,
				'instructions' => 'Entre 3 y 12. Si son más de 3, aparecen controles de slider.',
			],
		],
	] );
}
