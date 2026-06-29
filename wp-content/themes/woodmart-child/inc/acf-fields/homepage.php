<?php

/* ------------------------------------------------------------------
   ACF FIELD GROUP — Página de Inicio
   Ubicación: post_type == murguia_ajustes (aplica a todas las secciones).
   Para limitar a una sección específica cambia el location a:
     [ 'param' => 'post', 'operator' => '==', 'value' => murguia_ajuste_id('pagina-de-inicio') ]
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_homepage_fields' );

function murguia_register_homepage_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'             => 'group_murg_homepage',
		'title'           => 'Pagina de Inicio - Contenido',
		'location'        => [
			[ [ 'param' => 'post', 'operator' => '==', 'value' => murguia_ajuste_id( 'pagina-de-inicio' ) ] ],
		],
		'menu_order'      => 0,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'fields'          => [
			[
				'key'       => 'field_murg_tab_hero',
				'label'     => 'Hero',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'          => 'field_murg_hp_hero_slides',
				'label'        => 'Slides del Hero',
				'name'         => 'hp_hero_slides',
				'type'         => 'repeater',
				'min'          => 1,
				'max'          => 8,
				'layout'       => 'block',
				'button_label' => 'Agregar slide',
				'sub_fields'   => [
					[
						'key'           => 'field_murg_hp_hero_slide_tipo',
						'label'         => 'Tipo de fondo',
						'name'          => 'tipo',
						'type'          => 'radio',
						'choices'       => [ 'imagen' => 'Imagen', 'video' => 'Video' ],
						'default_value' => 'imagen',
						'layout'        => 'horizontal',
					],
					[
						'key'           => 'field_murg_hp_hero_slide_img',
						'label'         => 'Imagen de fondo',
						'name'          => 'imagen',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					],
					[
						'key'           => 'field_murg_hp_hero_slide_img_mobile',
						'label'         => 'Imagen mobile (opcional)',
						'name'          => 'imagen_mobile',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Opcional. Si se deja vacío, se usará la imagen de escritorio. Recomendado: proporción vertical (9:16 o similar).',
					],
					[
						'key'   => 'field_murg_hp_hero_slide_video_url',
						'label' => 'URL del video',
						'name'  => 'video_url',
						'type'  => 'url',
					],
					[
						'key'           => 'field_murg_hp_hero_slide_video_inicio',
						'label'         => 'Segundo de inicio',
						'name'          => 'video_inicio',
						'type'          => 'number',
						'default_value' => 0,
						'min'           => 0,
						'step'          => 1,
					],
					[
						'key'   => 'field_murg_hp_hero_slide_video_fin',
						'label' => 'Segundo de fin',
						'name'  => 'video_fin',
						'type'  => 'number',
						'min'   => 1,
						'step'  => 1,
					],
					[
						'key'   => 'field_murg_hp_hero_slide_titulo',
						'label' => 'Titulo',
						'name'  => 'titulo',
						'type'  => 'text',
					],
					[
						'key'   => 'field_murg_hp_hero_slide_cta_texto',
						'label' => 'Texto del boton',
						'name'  => 'cta_texto',
						'type'  => 'text',
					],
					[
						'key'   => 'field_murg_hp_hero_slide_cta_link',
						'label' => 'Destino del boton',
						'name'  => 'cta_link',
						'type'  => 'url',
					],
				],
			],

			[
				'key'       => 'field_murg_tab_diamonds',
				'label'     => 'Anillos de compromiso',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'   => 'field_murg_hp_diamond_titulo',
				'label' => 'Titulo',
				'name'  => 'hp_diamond_titulo',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_diamond_sub',
				'label' => 'Subtitulo',
				'name'  => 'hp_diamond_sub',
				'type'  => 'text',
			],

			[
				'key'       => 'field_murg_tab_novios',
				'label'     => 'Novios',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'   => 'field_murg_hp_novios_titulo',
				'label' => 'Titulo',
				'name'  => 'hp_novios_titulo',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_novios_sub',
				'label' => 'Subtitulo',
				'name'  => 'hp_novios_sub',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_novios_cta_texto',
				'label' => 'Texto del boton',
				'name'  => 'hp_novios_cta_texto',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_novios_cta_url',
				'label' => 'URL del boton',
				'name'  => 'hp_novios_cta_url',
				'type'  => 'url',
			],
			[
				'key'           => 'field_murg_hp_novios_imagen',
				'label'         => 'Imagen',
				'name'          => 'hp_novios_imagen',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			],
			[
				'key'          => 'field_murg_hp_novios_logos',
				'label'        => 'Logos de certificaciones',
				'name'         => 'hp_novios_logos',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Agregar logo',
				'sub_fields'   => [
					[
						'key'           => 'field_murg_novios_logo_img',
						'label'         => 'Imagen',
						'name'          => 'imagen',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
					],
				],
			],

			[
				'key'       => 'field_murg_tab_piezas',
				'label'     => 'Piezas que destacan',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'   => 'field_murg_hp_piezas_titulo',
				'label' => 'Titulo',
				'name'  => 'hp_piezas_titulo',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_piezas_cta_texto',
				'label' => 'Texto del boton',
				'name'  => 'hp_piezas_cta_texto',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_piezas_cta_url',
				'label' => 'URL del boton',
				'name'  => 'hp_piezas_cta_url',
				'type'  => 'url',
			],

			[
				'key'       => 'field_murg_tab_featured',
				'label'     => 'Producto destacado',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_murg_hp_feat_producto',
				'label'         => 'Producto destacado',
				'name'          => 'hp_feat_producto',
				'type'          => 'post_object',
				'post_type'     => [ 'product' ],
				'return_format' => 'object',
				'multiple'      => 0,
				'allow_null'    => 1,
			],
			[
				'key'          => 'field_murg_hp_feat_gallery',
				'label'        => 'Galería (opcional — si está vacía, usa las del producto)',
				'name'         => 'hp_feat_gallery',
				'type'         => 'gallery',
				'return_format' => 'array',
				'preview_size' => 'medium',
				'min'          => 0,
				'max'          => 12,
			],

			[
				'key'       => 'field_murg_tab_statement',
				'label'     => 'Statement',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'   => 'field_murg_hp_stmt_texto',
				'label' => 'Texto',
				'name'  => 'hp_stmt_texto',
				'type'  => 'textarea',
				'rows'  => 4,
			],
			[
				'key'   => 'field_murg_hp_stmt_atribucion',
				'label' => 'Atribucion',
				'name'  => 'hp_stmt_atribucion',
				'type'  => 'text',
			],
			[
				'key'           => 'field_murg_hp_stmt_imagen',
				'label'         => 'Imagen',
				'name'          => 'hp_stmt_imagen',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			],

			[
				'key'       => 'field_murg_tab_qantu',
				'label'     => 'QANTU',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'   => 'field_murg_hp_qantu_titulo',
				'label' => 'Titulo',
				'name'  => 'hp_qantu_titulo',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_qantu_sub',
				'label' => 'Subtitulo',
				'name'  => 'hp_qantu_sub',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_qantu_cta_texto',
				'label' => 'Texto del boton',
				'name'  => 'hp_qantu_cta_texto',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_qantu_cta_url',
				'label' => 'URL del boton',
				'name'  => 'hp_qantu_cta_url',
				'type'  => 'url',
			],
			[
				'key'           => 'field_murg_hp_qantu_imagen_1',
				'label'         => 'Imagen 1',
				'name'          => 'hp_qantu_imagen_1',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			],
			[
				'key'           => 'field_murg_hp_qantu_imagen_2',
				'label'         => 'Imagen 2',
				'name'          => 'hp_qantu_imagen_2',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			],
			[
				'key'           => 'field_murg_hp_qantu_imagen_3',
				'label'         => 'Imagen 3',
				'name'          => 'hp_qantu_imagen_3',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			],

			[
				'key'       => 'field_murg_tab_visita',
				'label'     => 'Agenda tu visita',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'   => 'field_murg_hp_visita_titulo',
				'label' => 'Titulo',
				'name'  => 'hp_visita_titulo',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_visita_sub',
				'label' => 'Subtitulo',
				'name'  => 'hp_visita_sub',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_visita_boutique',
				'label' => 'Etiqueta boutique',
				'name'  => 'hp_visita_boutique',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_visita_ubicacion',
				'label' => 'Ubicacion',
				'name'  => 'hp_visita_ubicacion',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_visita_virtual',
				'label' => 'Etiqueta videollamada',
				'name'  => 'hp_visita_virtual',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_visita_horario',
				'label' => 'Horario',
				'name'  => 'hp_visita_horario',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_visita_cita_texto',
				'label' => 'Texto boton cita',
				'name'  => 'hp_visita_cita_texto',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_visita_cita_url',
				'label' => 'URL cita',
				'name'  => 'hp_visita_cita_url',
				'type'  => 'url',
			],
			[
				'key'   => 'field_murg_hp_visita_wa_url',
				'label' => 'URL WhatsApp',
				'name'  => 'hp_visita_wa_url',
				'type'  => 'url',
			],
			[
				'key'           => 'field_murg_hp_visita_imagen',
				'label'         => 'Imagen',
				'name'          => 'hp_visita_imagen',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			],

			[
				'key'       => 'field_murg_tab_brands',
				'label'     => 'Marcas',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'          => 'field_murg_hp_brands_logos',
				'label'        => 'Logos',
				'name'         => 'hp_brands_logos',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Agregar logo',
				'sub_fields'   => [
					[
						'key'           => 'field_murg_brand_logo_img',
						'label'         => 'Imagen',
						'name'          => 'imagen',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
					],
					[
						'key'   => 'field_murg_brand_logo_alt',
						'label' => 'Alt',
						'name'  => 'alt',
						'type'  => 'text',
					],
				],
			],

			[
				'key'       => 'field_murg_tab_newsletter',
				'label'     => 'Newsletter',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'   => 'field_murg_hp_nl_titulo',
				'label' => 'Titulo',
				'name'  => 'hp_nl_titulo',
				'type'  => 'text',
			],
			[
				'key'   => 'field_murg_hp_nl_sub',
				'label' => 'Subtitulo opcional',
				'name'  => 'hp_nl_sub',
				'type'  => 'text',
			],
			[
				'key'          => 'field_murg_hp_mailchimp_action',
				'label'        => 'Mailchimp action URL',
				'name'         => 'hp_mailchimp_action',
				'type'         => 'url',
				'instructions' => 'Opcional. Pega aqui el action URL del formulario embebido de Mailchimp. Si esta vacio, el formulario enviara el correo al admin del sitio.',
			],
			[
				'key'           => 'field_murg_hp_mailchimp_email_name',
				'label'         => 'Nombre del campo email',
				'name'          => 'hp_mailchimp_email_name',
				'type'          => 'text',
				'default_value' => 'EMAIL',
				'instructions'  => 'Mailchimp suele usar EMAIL. Cambiar solo si el formulario externo usa otro nombre.',
			],
		],
	] );
	return;

}
