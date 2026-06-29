<?php

/* ------------------------------------------------------------------
   ACF FIELD GROUP - Anillos de Compromiso (landing)
   Prefijo: ac_   |   Ajuste slug: anillos-compromiso-page
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_anillos_compromiso_fields' );

function murguia_register_anillos_compromiso_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	$id = murguia_ajuste_id( 'anillos-compromiso-page' );
	if ( ! $id ) return;

	$cta_fields = function( $prefix, $label ) {
		return [
			[
				'key'   => 'field_' . $prefix . '_texto',
				'label' => $label . ' - Texto',
				'name'  => $prefix . '_texto',
				'type'  => 'text',
			],
			[
				'key'   => 'field_' . $prefix . '_url',
				'label' => $label . ' - URL',
				'name'  => $prefix . '_url',
				'type'  => 'url',
			],
		];
	};

	acf_add_local_field_group( [
		'key'      => 'group_murg_anillos_compromiso',
		'title'    => 'Anillos de Compromiso - Contenido',
		'location' => [ [ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ] ],
		'menu_order' => 0,
		'position'   => 'normal',
		'style'      => 'default',
		'label_placement' => 'top',
		'fields'   => array_merge(
			[
				[ 'key' => 'field_ac_tab_hero', 'label' => 'Hero', 'name' => '', 'type' => 'tab' ],
				[
					'key' => 'field_ac_hero_imagen', 'label' => 'Imagen hero', 'name' => 'ac_hero_imagen',
					'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium',
					'instructions' => 'Formato WebP/JPG. Minimo 1920x1080px.',
				],
				[ 'key' => 'field_ac_hero_titulo', 'label' => 'Titulo hero', 'name' => 'ac_hero_titulo', 'type' => 'textarea', 'rows' => 2 ],
				[ 'key' => 'field_ac_hero_sub', 'label' => 'Bajada hero', 'name' => 'ac_hero_sub', 'type' => 'textarea', 'rows' => 3 ],
			],
			$cta_fields( 'ac_hero_cta', 'CTA principal hero' ),
			$cta_fields( 'ac_hero_cta_sec', 'CTA secundario hero' ),
			[
				[ 'key' => 'field_ac_tab_formas', 'label' => 'Formas de diamante', 'name' => '', 'type' => 'tab' ],
				[ 'key' => 'field_ac_formas_titulo', 'label' => 'Titulo', 'name' => 'ac_formas_titulo', 'type' => 'text' ],
				[ 'key' => 'field_ac_formas_sub', 'label' => 'Subtitulo', 'name' => 'ac_formas_sub', 'type' => 'text' ],

				[ 'key' => 'field_ac_tab_productos', 'label' => 'Coleccion destacada', 'name' => '', 'type' => 'tab' ],
				[ 'key' => 'field_ac_productos_titulo', 'label' => 'Titulo', 'name' => 'ac_productos_titulo', 'type' => 'text' ],
				[ 'key' => 'field_ac_productos_sub', 'label' => 'Subtitulo', 'name' => 'ac_productos_sub', 'type' => 'textarea', 'rows' => 2 ],
				[
					'key' => 'field_ac_productos_categoria', 'label' => 'Categoria WooCommerce', 'name' => 'ac_productos_categoria',
					'type' => 'taxonomy', 'taxonomy' => 'product_cat', 'field_type' => 'select',
					'return_format' => 'id', 'allow_null' => 1,
				],
				[ 'key' => 'field_ac_productos_cantidad', 'label' => 'Cantidad de productos', 'name' => 'ac_productos_cantidad', 'type' => 'number', 'min' => 4, 'max' => 8, 'default_value' => 4 ],
				[ 'key' => 'field_ac_productos_cta_texto', 'label' => 'Texto CTA', 'name' => 'ac_productos_cta_texto', 'type' => 'text' ],
				[ 'key' => 'field_ac_productos_cta_url', 'label' => 'URL CTA', 'name' => 'ac_productos_cta_url', 'type' => 'url' ],

				[ 'key' => 'field_ac_tab_estilos', 'label' => 'Estilos de anillo', 'name' => '', 'type' => 'tab' ],
				[
					'key' => 'field_ac_estilos_items', 'label' => 'Estilos', 'name' => 'ac_estilos_items',
					'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar estilo',
					'sub_fields' => [
						[ 'key' => 'field_ac_estilo_imagen', 'label' => 'Imagen', 'name' => 'imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
						[ 'key' => 'field_ac_estilo_titulo', 'label' => 'Titulo', 'name' => 'titulo', 'type' => 'text' ],
						[ 'key' => 'field_ac_estilo_texto', 'label' => 'Texto', 'name' => 'texto', 'type' => 'textarea', 'rows' => 3 ],
						[ 'key' => 'field_ac_estilo_url', 'label' => 'URL', 'name' => 'url', 'type' => 'url' ],
					],
				],

				[ 'key' => 'field_ac_tab_beneficios', 'label' => 'Beneficios', 'name' => '', 'type' => 'tab' ],
				[
					'key' => 'field_ac_beneficios_items', 'label' => 'Beneficios', 'name' => 'ac_beneficios_items',
					'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar beneficio',
					'sub_fields' => [
						[ 'key' => 'field_ac_ben_icono', 'label' => 'Icono SVG', 'name' => 'icono_svg', 'type' => 'textarea', 'rows' => 3 ],
						[ 'key' => 'field_ac_ben_titulo', 'label' => 'Titulo', 'name' => 'titulo', 'type' => 'text' ],
						[ 'key' => 'field_ac_ben_texto', 'label' => 'Texto', 'name' => 'texto', 'type' => 'textarea', 'rows' => 3 ],
					],
				],

				[ 'key' => 'field_ac_tab_historia', 'label' => 'Historia / Taller', 'name' => '', 'type' => 'tab' ],
				[ 'key' => 'field_ac_historia_imagen', 'label' => 'Imagen', 'name' => 'ac_historia_imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
				[ 'key' => 'field_ac_historia_titulo', 'label' => 'Titulo', 'name' => 'ac_historia_titulo', 'type' => 'text' ],
				[ 'key' => 'field_ac_historia_texto', 'label' => 'Texto', 'name' => 'ac_historia_texto', 'type' => 'textarea', 'rows' => 5 ],
				[ 'key' => 'field_ac_historia_cta', 'label' => 'CTA historia', 'name' => 'ac_historia_cta', 'type' => 'link', 'return_format' => 'array' ],

				[ 'key' => 'field_ac_tab_cita', 'label' => 'Agenda tu visita', 'name' => '', 'type' => 'tab' ],
				[ 'key' => 'field_ac_cita_imagen', 'label' => 'Imagen', 'name' => 'ac_cita_imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
				[ 'key' => 'field_ac_cita_titulo', 'label' => 'Titulo', 'name' => 'ac_cita_titulo', 'type' => 'text' ],
				[ 'key' => 'field_ac_cita_texto', 'label' => 'Texto', 'name' => 'ac_cita_texto', 'type' => 'textarea', 'rows' => 3 ],
				[ 'key' => 'field_ac_cita_url', 'label' => 'URL agendar cita', 'name' => 'ac_cita_url', 'type' => 'url' ],
				[ 'key' => 'field_ac_whatsapp_url', 'label' => 'URL WhatsApp', 'name' => 'ac_whatsapp_url', 'type' => 'url' ],

				[ 'key' => 'field_ac_tab_testimonios', 'label' => 'Testimonios', 'name' => '', 'type' => 'tab' ],
				[
					'key' => 'field_ac_testimonios_items', 'label' => 'Testimonios', 'name' => 'ac_testimonios_items',
					'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar testimonio',
					'sub_fields' => [
						[ 'key' => 'field_ac_tes_frase', 'label' => 'Frase', 'name' => 'frase', 'type' => 'textarea', 'rows' => 3 ],
						[ 'key' => 'field_ac_tes_autor', 'label' => 'Autor', 'name' => 'autor', 'type' => 'text' ],
					],
				],

				[ 'key' => 'field_ac_tab_newsletter', 'label' => 'Newsletter', 'name' => '', 'type' => 'tab' ],
				[ 'key' => 'field_ac_newsletter_titulo', 'label' => 'Titulo', 'name' => 'ac_newsletter_titulo', 'type' => 'text' ],
				[ 'key' => 'field_ac_newsletter_sub', 'label' => 'Subtitulo', 'name' => 'ac_newsletter_sub', 'type' => 'text' ],
			]
		),
	] );
}
