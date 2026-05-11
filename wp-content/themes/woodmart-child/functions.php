<?php
/**
 * Joyería Murguía — Child Theme Functions
 */



/* ------------------------------------------------------------------
   STYLES & SCRIPTS
   ------------------------------------------------------------------ */
function murguia_enqueue() {
	$css_path = get_stylesheet_directory() . '/style.css';
	$js_path  = get_stylesheet_directory() . '/assets/js/murguia.js';
	$css_ver  = file_exists( $css_path ) ? filemtime( $css_path ) : '1.0.0';
	$js_ver   = file_exists( $js_path )  ? filemtime( $js_path )  : '1.0.0';

	wp_enqueue_style(
		'murguia-fonts',
		'https://fonts.googleapis.com/css2?family=Vesper+Libre:wght@400;700&family=Tiro+Bangla&display=swap',
		[],
		null
	);

	wp_enqueue_style(
		'murguia-child',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'woodmart-style' ],
		$css_ver
	);

	wp_enqueue_script(
		'murguia-js',
		get_stylesheet_directory_uri() . '/assets/js/murguia.js',
		[],
		$js_ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'murguia_enqueue', 10010 );

/* ------------------------------------------------------------------
   THEME SUPPORTS & MENUS
   ------------------------------------------------------------------ */
add_action( 'after_setup_theme', function () {
	register_nav_menus( [
		'murguia-primary' => __( 'Murguía — Primario', 'woodmart' ),
		'murguia-footer'  => __( 'Murguía — Pie de página', 'woodmart' ),
	] );
} );

/* ------------------------------------------------------------------
   FORM HANDLER — Solicitud de cita
   ------------------------------------------------------------------ */
add_action( 'admin_post_murg_solicitar_cita',        'murguia_handle_cita' );
add_action( 'admin_post_nopriv_murg_solicitar_cita', 'murguia_handle_cita' );

function murguia_handle_cita() {
	if ( ! isset( $_POST['murg_nonce'] ) || ! wp_verify_nonce( $_POST['murg_nonce'], 'murg_cita' ) ) {
		wp_die( 'Solicitud no válida.', 403 );
	}

	$nombre   = sanitize_text_field( $_POST['nombre']   ?? '' );
	$correo   = sanitize_email( $_POST['correo']        ?? '' );
	$telefono = sanitize_text_field( $_POST['telefono'] ?? '' );
	$interes  = sanitize_text_field( $_POST['interes']  ?? '' );
	$mensaje  = sanitize_textarea_field( $_POST['mensaje'] ?? '' );

	if ( empty( $nombre ) || ! is_email( $correo ) ) {
		wp_safe_redirect( add_query_arg( 'cita', 'error', wp_get_referer() ) );
		exit;
	}

	$to      = get_option( 'admin_email' );
	$subject = sprintf( '[Murguía] Nueva solicitud de cita — %s', $nombre );
	$body    = sprintf(
		"Nombre: %s\nCorreo: %s\nTeléfono: %s\nInterés: %s\n\nMensaje:\n%s",
		$nombre, $correo, $telefono, $interes, $mensaje
	);
	$headers = [
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'Reply-To: %s <%s>', $nombre, $correo ),
	];

	wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'cita', 'ok', wp_get_referer() ) );
	exit;
}

/* ------------------------------------------------------------------
   HELPERS — Ajustes de Diseño
   ------------------------------------------------------------------ */

/**
 * Devuelve el ID del post de ajustes correspondiente a la sección dada.
 * Usa caché estático para no repetir queries.
 *
 * @param string $seccion  Post slug dentro de murguia_ajustes.
 * @return int|false       Post ID o false si no existe.
 */
function murguia_ajuste_id( $seccion = 'pagina-de-inicio' ) {
	static $cache = [];
	if ( ! array_key_exists( $seccion, $cache ) ) {
		$post          = get_page_by_path( $seccion, OBJECT, 'murguia_ajustes' );
		$cache[ $seccion ] = $post ? $post->ID : false;
	}
	return $cache[ $seccion ];
}

/**
 * Lee un field ACF del post de ajustes indicado.
 *
 * @param string $key      Nombre del field ACF.
 * @param mixed  $fallback Valor si el field está vacío o ACF no está activo.
 * @param string $seccion  Slug del post de ajustes (por defecto: homepage).
 * @return mixed
 */
function murguia_ajuste( $key, $fallback = '', $seccion = 'pagina-de-inicio' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}
	$post_id = murguia_ajuste_id( $seccion );
	if ( ! $post_id ) {
		return $fallback;
	}
	$val = get_field( $key, $post_id );
	return ( $val !== null && $val !== false && $val !== '' ) ? $val : $fallback;
}

/* ------------------------------------------------------------------
   CPTs — Ajustes de Diseño · Colecciones · Piezas
   ------------------------------------------------------------------ */
add_action( 'init', 'murguia_register_cpts' );

function murguia_register_cpts() {

	/* ---- Ajustes de Diseño (contenedor de settings, no público) ---- */
	register_post_type( 'murguia_ajustes', [
		'labels'             => [
			'name'               => 'Ajustes de Diseño',
			'singular_name'      => 'Ajuste de Diseño',
			'menu_name'          => 'Ajustes de Diseño',
			'all_items'          => 'Todas las Secciones',
			'add_new_item'       => 'Añadir Sección',
			'edit_item'          => 'Editar Sección',
		],
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-admin-appearance',
		'menu_position'      => 30,
		'supports'           => [ 'title' ],
		'has_archive'        => false,
		'rewrite'            => false,
		'show_in_rest'       => false,
		// Solo admins pueden crear nuevas secciones de ajustes.
		'capabilities'       => [ 'create_posts' => 'manage_options' ],
		'map_meta_cap'       => true,
	] );

	/* ---- Colecciones ---- */
	register_post_type( 'murguia_coleccion', [
		'labels'       => [
			'name'          => 'Colecciones',
			'singular_name' => 'Colección',
			'add_new_item'  => 'Añadir Colección',
			'edit_item'     => 'Editar Colección',
		],
		'public'       => true,
		'has_archive'  => true,
		'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
		'rewrite'      => [ 'slug' => 'colecciones' ],
		'show_in_menu' => 'xts_dashboard',
		'show_in_rest' => true,
	] );

	/* ---- Piezas destacadas ---- */
	register_post_type( 'murguia_pieza', [
		'labels'       => [
			'name'          => 'Piezas Destacadas',
			'singular_name' => 'Pieza',
			'add_new_item'  => 'Añadir Pieza',
			'edit_item'     => 'Editar Pieza',
		],
		'public'       => true,
		'has_archive'  => true,
		'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
		'rewrite'      => [ 'slug' => 'piezas' ],
		'show_in_menu' => 'xts_dashboard',
		'show_in_rest' => true,
	] );
}

/* ------------------------------------------------------------------
   AUTO-CREATE — Posts por defecto en murguia_ajustes
   Añade una entrada aquí cada vez que se cree una nueva sección del theme.
   ------------------------------------------------------------------ */
add_action( 'init', 'murguia_ensure_ajustes_defaults', 999 );

function murguia_ensure_ajustes_defaults() {
	if ( ! post_type_exists( 'murguia_ajustes' ) ) {
		return;
	}

	$secciones = [
		[ 'post_title' => 'Página de Inicio', 'post_name' => 'pagina-de-inicio' ],
		[ 'post_title' => 'Tienda',            'post_name' => 'tienda' ],
		[ 'post_title' => 'Producto',          'post_name' => 'producto' ],
		[ 'post_title' => 'Contacto',          'post_name' => 'contacto' ],
		[ 'post_title' => 'Anillos de Compromiso', 'post_name' => 'anillos-compromiso-page' ],
		[ 'post_title' => 'Alta Joyería',      'post_name' => 'alta-joyeria-page' ],
	];

	foreach ( $secciones as $data ) {
		if ( ! get_page_by_path( $data['post_name'], OBJECT, 'murguia_ajustes' ) ) {
			wp_insert_post( [
				'post_title'  => $data['post_title'],
				'post_name'   => $data['post_name'],
				'post_type'   => 'murguia_ajustes',
				'post_status' => 'publish',
			] );
		}
	}
}

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
		],
	] );
	return;

}

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

/* ------------------------------------------------------------------
   ACF FIELD GROUP — Contacto (página de contacto independiente)
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_contact_fields' );

function murguia_register_contact_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$id = murguia_ajuste_id( 'contacto' );
	if ( ! $id ) {
		return;
	}

	acf_add_local_field_group( [
		'key'             => 'group_murg_contact',
		'title'           => 'Contacto — Contenido',
		'location'        => [
			[ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ],
		],
		'menu_order'      => 30,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'fields'          => [

			/* ---- TAB: Encabezado ---- */
			[
				'key'       => 'field_murg_ct_tab_header',
				'label'     => '📍 Encabezado',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_ct_eyebrow',
				'label'       => 'Eyebrow',
				'name'        => 'ct_eyebrow',
				'type'        => 'text',
				'placeholder' => 'Visite el Atelier',
			],
			[
				'key'         => 'field_murg_ct_titulo',
				'label'       => 'Título',
				'name'        => 'ct_titulo',
				'type'        => 'text',
				'placeholder' => 'Visítenos o agende una cita',
				'instructions'=> 'Usa <em></em> para itálica.',
			],
			[
				'key'   => 'field_murg_ct_texto',
				'label' => 'Texto introductorio',
				'name'  => 'ct_texto',
				'type'  => 'textarea',
				'rows'  => 3,
			],

			/* ---- TAB: Datos ---- */
			[
				'key'       => 'field_murg_ct_tab_datos',
				'label'     => '📞 Datos de contacto',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'   => 'field_murg_ct_direccion',
				'label' => 'Dirección',
				'name'  => 'ct_direccion',
				'type'  => 'textarea',
				'rows'  => 2,
			],
			[
				'key'         => 'field_murg_ct_horario',
				'label'       => 'Horario',
				'name'        => 'ct_horario',
				'type'        => 'text',
				'placeholder' => 'Lun – Sáb · 10:00 – 19:00',
			],
			[
				'key'         => 'field_murg_ct_telefono',
				'label'       => 'Teléfono',
				'name'        => 'ct_telefono',
				'type'        => 'text',
				'placeholder' => '+51 1 421 8800',
			],
			[
				'key'   => 'field_murg_ct_email',
				'label' => 'Email',
				'name'  => 'ct_email',
				'type'  => 'email',
			],
			[
				'key'         => 'field_murg_ct_whatsapp',
				'label'       => 'WhatsApp URL',
				'name'        => 'ct_whatsapp',
				'type'        => 'url',
				'placeholder' => 'https://wa.me/51...',
			],

			/* ---- TAB: Servicios ---- */
			[
				'key'       => 'field_murg_ct_tab_servicios',
				'label'     => '✦ Servicios',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_ct_servicios',
				'label'       => 'Servicios (uno por línea)',
				'name'        => 'ct_servicios',
				'type'        => 'textarea',
				'rows'        => 4,
				'placeholder' => "Diseño a medida\nRestauración\nGrabado y personalización",
			],
			[
				'key'         => 'field_murg_ct_serv_sub',
				'label'       => 'Nota al pie de servicios',
				'name'        => 'ct_serv_sub',
				'type'        => 'text',
				'placeholder' => 'Presupuesto sin costo',
			],
		],
	] );
}

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

/* ------------------------------------------------------------------
   TEMPLATE OVERRIDE — Forzar nuestro archive-product.php sobre WoodMart
   ------------------------------------------------------------------ */
add_filter( 'template_include', 'murguia_override_shop_template', 99999 );

function murguia_override_shop_template( $template ) {
	$is_product_search = is_search() && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'];

	if ( is_shop() || is_product_taxonomy() || $is_product_search ) {
		$custom = get_stylesheet_directory() . '/archive-product.php';
		if ( file_exists( $custom ) ) {
			return $custom;
		}
	}

	// Página con plantilla "Alta Joyería"
	if ( is_page() && 'page-alta-joyeria.php' === get_page_template_slug() ) {
		$custom = get_stylesheet_directory() . '/page-alta-joyeria.php';
		if ( file_exists( $custom ) ) return $custom;
	}
	// Fallback por slug
	if ( is_page( 'alta-joyeria' ) ) {
		$custom = get_stylesheet_directory() . '/page-alta-joyeria.php';
		if ( file_exists( $custom ) ) return $custom;
	}

	if ( is_page() && 'page-anillos-compromiso.php' === get_page_template_slug() ) {
		$custom = get_stylesheet_directory() . '/page-anillos-compromiso.php';
		if ( file_exists( $custom ) ) return $custom;
	}
	if ( is_page( 'anillos-compromiso' ) ) {
		$custom = get_stylesheet_directory() . '/page-anillos-compromiso.php';
		if ( file_exists( $custom ) ) return $custom;
	}

	// Checkout — forzar nuestro template con murg-nav / murg-footer
	if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url() ) {
		$custom = get_stylesheet_directory() . '/page-checkout.php';
		if ( file_exists( $custom ) ) return $custom;
	}

	return $template;
}

/* ------------------------------------------------------------------
   ADMIN MENU — CPTs del plugin woodmart-core bajo xts_dashboard
   ------------------------------------------------------------------ */
add_action( 'admin_menu', 'murguia_reorganize_woodmart_cpts', 999 );

function murguia_reorganize_woodmart_cpts() {
	$cpts = [
		'cms_block'         => 'HTML Blocks',
		'woodmart_sidebar'  => 'Sidebars',
		'woodmart_slide'    => 'Slides',
		'woodmart_layout'   => 'Layouts',
		'portfolio'         => 'Portfolio',
		'wd_floating_block' => 'Floating Blocks',
		'wd_popup'          => 'Popups',
	];

	foreach ( $cpts as $post_type => $label ) {
		$menu_slug = 'edit.php?post_type=' . $post_type;
		remove_menu_page( $menu_slug );
		add_submenu_page( 'xts_dashboard', $label, $label, 'manage_options', $menu_slug );
	}
}

/* ==================================================================
   FRONTEND CLEANUP — Quitar assets y clases de WoodMart/Elementor que
   no usamos en nuestros templates custom (.murg-*).
   Mantenemos woodmart-style (parent) + jQuery + WC essentials para
   que header/footer administrativos, carrito y login sigan funcionando.
   ================================================================== */

/**
 * Detecta si el request actual usa uno de nuestros templates custom.
 * Se usa para aplicar cleanup solo en esas páginas.
 */
function murguia_is_custom_template() {
	// Home custom
	if ( is_front_page() ) {
		return true;
	}
	// Mi Cuenta (WooCommerce)
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return true;
	}
	// Shop y archivos de productos
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		return true;
	}
	// Producto individual (tenemos single-product.php)
	if ( function_exists( 'is_product' ) && is_product() ) {
		return true;
	}
	// Página con plantilla "Contacto" (page-contact.php)
	if ( is_page() && 'page-contact.php' === get_page_template_slug() ) {
		return true;
	}
	// Página de contacto por slug, si no se asignó la plantilla
	if ( is_page( 'contacto' ) ) {
		return true;
	}
	// Página con plantilla "Alta Joyería"
	if ( is_page() && 'page-alta-joyeria.php' === get_page_template_slug() ) {
		return true;
	}
	if ( is_page( 'alta-joyeria' ) ) {
		return true;
	}
	if ( is_page() && 'page-anillos-compromiso.php' === get_page_template_slug() ) {
		return true;
	}
	if ( is_page( 'anillos-compromiso' ) ) {
		return true;
	}
	// Checkout
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		return true;
	}
	// Carrito
	if ( function_exists( 'is_cart' ) && is_cart() ) {
		return true;
	}
	return false;
}

/**
 * Dequeue selectivo de CSS/JS innecesarios en páginas custom.
 * Priority 9999 para correr después de todos los enqueue.
 */
function murguia_dequeue_unused_assets() {
	if ( ! murguia_is_custom_template() ) {
		return;
	}

	// Handles de estilos de WoodMart que no usamos (shop widgets, Elementor, etc).
	$styles_to_drop = [
		// Shop widgets / layouts de WoodMart — tenemos nuestro propio shop layout
		'wd-widget-active-filters',
		'wd-woo-shop-predefined',
		'wd-shop-title-categories',
		'wd-woo-categories-loop-nav-mobile-accordion',
		'wd-woo-shop-el-products-per-page',
		'wd-woo-shop-page-title',
		'wd-woo-mod-shop-loop-head',
		'wd-woo-shop-el-order-by',
		'wd-woo-shop-el-products-view',
		'wd-woo-mod-shop-attributes',
		'wd-woo-opt-coming-soon',

		// Header / toolbar de WoodMart — tenemos nuestro nav custom
		'wd-bottom-toolbar',
		'wd-mod-sticky-sidebar-opener',
		'wd-mod-tools',
		'wd-header-elements-base',
		'wd-shop-off-canvas-sidebar',
		'wd-header-cart-side',
		'wd-header-cart',
		'wd-header-my-account',

		// Integración con Elementor (no usamos Elementor en templates custom)
		'wd-helpers-wpb-elem',
		'wd-elementor-base',

		// WordPress blocks (Gutenberg) — no hay bloques en nuestros templates PHP
		'wd-wp-blocks',

		// Star ratings — nuestros templates no muestran valoraciones
		'wd-mod-star-rating',

		// Fuentes de WoodMart (Lora, Marcellus SC) — usamos Cormorant+Inter
		'xts-google-fonts',

		// CSS dinámicos generados por WoodMart Options (header builder + theme settings)
		'xts-style-header_562797',
		'xts-style-theme_settings_default',

		// WooCommerce blocks (Gutenberg) — no los usamos en estos templates
		'wc-blocks-style',
		'wc-blocks-vendors-style',
		'wp-block-library',

		// Elementor — nuestras páginas custom no usan Elementor
		'elementor-frontend',
		'elementor-icons',
		'elementor-gallery',
		'elementor-wp-admin-bar',
		'elementor-post-8',
		'elementor-post-2830',
		'base-desktop', // Elementor kit base (wp-content/uploads/elementor/css/base-desktop.css)
	];

	foreach ( $styles_to_drop as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}

	// Dequeue por prefijo dinámico (xts-style-header_*, xts-style-theme_settings_*, elementor-post-*).
	// Estos handles cambian de nombre según config/post, así que los buscamos.
	global $wp_styles;
	if ( isset( $wp_styles ) && is_object( $wp_styles ) ) {
		foreach ( (array) $wp_styles->registered as $handle => $_ ) {
			if ( 0 === strpos( $handle, 'xts-style-header_' )
				|| 0 === strpos( $handle, 'xts-style-theme_settings_' )
				|| preg_match( '/^elementor-post-\d+$/', $handle ) ) {
				wp_dequeue_style( $handle );
				wp_deregister_style( $handle );
			}
		}
	}

	// Scripts innecesarios
	$scripts_to_drop = [
		'elementor-frontend',
		'elementor-frontend-modules',
		'elementor-webpack-runtime',
		'elementor-pro-frontend',
		'elementor-waypoints',
	];
	foreach ( $scripts_to_drop as $handle ) {
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'murguia_dequeue_unused_assets', 99999 );

/**
 * Filtrar body_class para quitar ruido de WoodMart/Elementor en nuestros
 * templates custom. Preserva woocommerce* porque WC depende de ellas para
 * AJAX de carrito. También preserva logged-in/admin-bar para coherencia.
 */
function murguia_filter_body_class( $classes ) {
	if ( ! murguia_is_custom_template() ) {
		return $classes;
	}

	$drop_prefixes = [ 'elementor-', 'wd-', 'xts-', 'woodmart-ajax-shop-', 'categories-accordion-', 'sticky-toolbar-' ];
	$drop_exact    = [
		'woodmart-archive-shop',
		'wrapper-full-width',
		'woocommerce-no-js', // WC añade 'woocommerce-js' si hay JS, no nos sirve la negación
		'theme-woodmart',    // nos quedamos con 'wp-theme-woodmart' que es la canónica de WP
	];

	$classes = array_filter( $classes, function ( $class ) use ( $drop_prefixes, $drop_exact ) {
		if ( in_array( $class, $drop_exact, true ) ) {
			return false;
		}
		foreach ( $drop_prefixes as $prefix ) {
			if ( 0 === strpos( $class, $prefix ) ) {
				return false;
			}
		}
		return true;
	} );

	return array_values( $classes );
}
add_filter( 'body_class', 'murguia_filter_body_class', 9999 );

/**
 * Remover hooks de wp_body_open y wp_footer de WoodMart en nuestras
 * páginas custom (skip-links, sticky toolbar móvil con Shop/Cart/Account,
 * toolbar bottom, etc). Mantenemos wp_body_open() y wp_footer() en los
 * templates para compatibilidad con otros plugins (admin bar, WC scripts).
 */
function murguia_clean_wp_hooks() {
	if ( ! murguia_is_custom_template() ) {
		return;
	}

	// Toolbar sticky inferior móvil (wd-toolbar con Shop/Cart/My account)
	remove_action( 'wp_footer', 'woodmart_sticky_toolbar_template' );

	// Acciones propias del header de WoodMart
	remove_all_actions( 'woodmart_before_header_action' );
	remove_all_actions( 'woodmart_after_header_action' );

	// Barrido de callbacks de WoodMart/XTS enganchados a wp_body_open y wp_footer.
	// Preservamos los de WP core y WooCommerce para que AJAX de carrito, admin
	// bar y demás plugins sigan funcionando.
	global $wp_filter;
	$hooks_to_clean = [ 'wp_body_open', 'wp_footer' ];

	foreach ( $hooks_to_clean as $hook_name ) {
		if ( ! isset( $wp_filter[ $hook_name ] ) ) {
			continue;
		}
		foreach ( $wp_filter[ $hook_name ]->callbacks as $priority => $callbacks ) {
			foreach ( $callbacks as $id => $cb ) {
				// Conservar core de WP (admin bar, scripts, pingbacks, etc.)
				if ( false !== strpos( $id, 'wp_admin_bar_render' )
				  || false !== strpos( $id, 'wp_print_footer_scripts' )
				  || false !== strpos( $id, '_wp_footer_scripts' )
				  || false !== strpos( $id, 'wp_maybe_inline_styles' )
				  || false !== strpos( $id, 'wp_auth_check_html' ) ) {
					continue;
				}

				$target = $cb['function'];

				// Callback tipo [objeto, método]
				if ( is_array( $target ) && is_object( $target[0] ) ) {
					$class = get_class( $target[0] );
					if ( false !== stripos( $class, 'woodmart' ) || false !== stripos( $class, 'XTS' ) ) {
						remove_action( $hook_name, $target, $priority );
					}
				// Callback tipo "nombre_funcion"
				} elseif ( is_string( $target )
					&& ( 0 === stripos( $target, 'woodmart_' )
					  || 0 === stripos( $target, 'xts_' )
					  || 0 === stripos( $target, 'wd_' ) ) ) {
					remove_action( $hook_name, $target, $priority );
				}
			}
		}
	}
}
add_action( 'wp', 'murguia_clean_wp_hooks', 99 );

/* ------------------------------------------------------------------
   ACF FIELD GROUP — Alta Joyería (página de experiencia)
   Prefijo: aj_   |   Ajuste slug: alta-joyeria-page
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_altajoyeria_fields' );

function murguia_register_altajoyeria_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	$id = murguia_ajuste_id( 'alta-joyeria-page' );
	if ( ! $id ) return;

	acf_add_local_field_group( [
		'key'      => 'group_murg_altajoyeria',
		'title'    => 'Alta Joyería — Contenido',
		'location' => [ [ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ] ],
		'menu_order' => 0,
		'fields'   => [

			// ── Tab Hero ────────────────────────────────────────
			[
				'key'   => 'field_aj_tab_hero',
				'label' => '🎬 Hero',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'          => 'field_aj_hero_imagen',
				'label'        => 'Imagen de fondo',
				'name'         => 'aj_hero_imagen',
				'type'         => 'image',
				'return_format' => 'array',
				'instructions' => 'Formato: WebP/JPG. Mínimo 1920×1080px. Peso máx. 400KB.',
			],
			[
				'key'          => 'field_aj_hero_eyebrow',
				'label'        => 'Eyebrow',
				'name'         => 'aj_hero_eyebrow',
				'type'         => 'text',
				'default_value' => 'Desde 1962',
			],
			[
				'key'          => 'field_aj_hero_titulo',
				'label'        => 'Título (acepta <em>)',
				'name'         => 'aj_hero_titulo',
				'type'         => 'text',
				'default_value' => 'Alta <em>Joyería</em>',
			],
			[
				'key'          => 'field_aj_hero_sub',
				'label'        => 'Subtítulo',
				'name'         => 'aj_hero_sub',
				'type'         => 'textarea',
				'rows'         => 3,
			],

			// ── Tab Intro ────────────────────────────────────────
			[
				'key'   => 'field_aj_tab_intro',
				'label' => '📖 Intro editorial',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'          => 'field_aj_intro_titulo',
				'label'        => 'Título intro (acepta <em>)',
				'name'         => 'aj_intro_titulo',
				'type'         => 'text',
				'default_value' => 'Cada piedra, <em>una historia</em>',
			],
			[
				'key'          => 'field_aj_intro_texto',
				'label'        => 'Texto editorial',
				'name'         => 'aj_intro_texto',
				'type'         => 'textarea',
				'rows'         => 5,
			],
			[
				'key'          => 'field_aj_intro_imagen',
				'label'        => 'Imagen del atelier',
				'name'         => 'aj_intro_imagen',
				'type'         => 'image',
				'return_format' => 'array',
				'instructions' => 'Formato: WebP/JPG. Ratio 3:4 (vertical). Mínimo 800×1067px.',
			],

			// ── Tab Contacto ─────────────────────────────────────
			[
				'key'   => 'field_aj_tab_contacto',
				'label' => '📞 Contacto',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'          => 'field_aj_whatsapp',
				'label'        => 'Número WhatsApp',
				'name'         => 'aj_whatsapp',
				'type'         => 'text',
				'instructions' => 'Solo números con código de país. Ej: 51934413662',
				'placeholder'  => '51934413662',
			],
			[
				'key'          => 'field_aj_email',
				'label'        => 'Email de consultas',
				'name'         => 'aj_email',
				'type'         => 'email',
			],
			[
				'key'          => 'field_aj_cita_url',
				'label'        => 'URL de agendar cita',
				'name'         => 'aj_cita_url',
				'type'         => 'url',
				'default_value' => '/contact-us/',
			],
		],
	] );
}

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
