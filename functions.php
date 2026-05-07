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
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Inter:wght@200;300;400;500&display=swap',
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
		'title'           => 'Página de Inicio — Contenido',
		'location'        => [
			[ [ 'param' => 'post', 'operator' => '==', 'value' => murguia_ajuste_id( 'pagina-de-inicio' ) ] ],
		],
		'menu_order'      => 0,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'fields'          => [

			/* ========================================================
			   TAB: HERO
			   ======================================================== */
			[
				'key'       => 'field_murg_tab_hero',
				'label'     => '🎯 Hero',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_hp_hero_eyebrow',
				'label'       => 'Eyebrow',
				'name'        => 'hp_hero_eyebrow',
				'type'        => 'text',
				'placeholder' => 'Colección Otoño · MMXXVI',
			],
			[
				'key'         => 'field_murg_hp_hero_titulo',
				'label'       => 'Título principal',
				'name'        => 'hp_hero_titulo',
				'type'        => 'text',
				'placeholder' => 'Joyería Murguía',
				'instructions'=> 'Escribe en texto plano. Rodea con <em></em> la parte en itálica.',
			],
			[
				'key'         => 'field_murg_hp_hero_subtitulo',
				'label'       => 'Subtítulo',
				'name'        => 'hp_hero_subtitulo',
				'type'        => 'text',
				'placeholder' => 'Orfebrería peruana desde 1962',
			],
			[
				'key'         => 'field_murg_hp_hero_cta_texto',
				'label'       => 'Texto del botón',
				'name'        => 'hp_hero_cta_texto',
				'type'        => 'text',
				'placeholder' => 'Ver Colección',
			],
			[
				'key'   => 'field_murg_hp_hero_cta_link',
				'label' => 'Link del botón',
				'name'  => 'hp_hero_cta_link',
				'type'  => 'url',
			],
			[
				'key'          => 'field_murg_hp_hero_imagen',
				'label'        => 'Imagen de fondo',
				'name'         => 'hp_hero_imagen',
				'type'         => 'image',
				'return_format'=> 'array',
				'preview_size' => 'medium',
				'instructions' => 'Recomendado: 1920×1080px o mayor. Se muestra oscurecida con vignette.',
			],

			/* ========================================================
			   TAB: COLECCIONES
			   ======================================================== */
			[
				'key'       => 'field_murg_tab_col',
				'label'     => '🖼 Colecciones',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_hp_col_eyebrow',
				'label'       => 'Eyebrow de sección',
				'name'        => 'hp_col_eyebrow',
				'type'        => 'text',
				'placeholder' => 'Colecciones Destacadas',
			],
			[
				'key'         => 'field_murg_hp_col_titulo',
				'label'       => 'Título de sección',
				'name'        => 'hp_col_titulo',
				'type'        => 'text',
				'placeholder' => 'Piezas que perduran',
				'instructions'=> 'Usa <em></em> para itálica.',
			],
			[
				'key'          => 'field_murg_hp_col_items',
				'label'        => 'Colecciones',
				'name'         => 'hp_col_items',
				'type'         => 'repeater',
				'min'          => 1,
				'max'          => 6,
				'layout'       => 'block',
				'button_label' => 'Añadir Colección',
				'sub_fields'   => [
					[
						'key'   => 'field_murg_col_nombre',
						'label' => 'Nombre',
						'name'  => 'nombre',
						'type'  => 'text',
					],
					[
						'key'   => 'field_murg_col_descripcion',
						'label' => 'Descripción corta',
						'name'  => 'descripcion',
						'type'  => 'text',
					],
					[
						'key'         => 'field_murg_col_numero',
						'label'       => 'Número (ej: N° I)',
						'name'        => 'numero',
						'type'        => 'text',
						'placeholder' => 'N° I',
					],
					[
						'key'   => 'field_murg_col_link',
						'label' => 'Link',
						'name'  => 'link',
						'type'  => 'url',
					],
					[
						'key'          => 'field_murg_col_imagen',
						'label'        => 'Imagen',
						'name'         => 'imagen',
						'type'         => 'image',
						'return_format'=> 'array',
						'preview_size' => 'thumbnail',
					],
				],
			],

			/* ========================================================
			   TAB: BESTSELLERS
			   ======================================================== */
			[
				'key'       => 'field_murg_tab_best',
				'label'     => '⭐ Bestsellers',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_hp_best_eyebrow',
				'label'       => 'Eyebrow de sección',
				'name'        => 'hp_best_eyebrow',
				'type'        => 'text',
				'placeholder' => 'Más Codiciados',
			],
			[
				'key'         => 'field_murg_hp_best_titulo',
				'label'       => 'Título de sección',
				'name'        => 'hp_best_titulo',
				'type'        => 'text',
				'placeholder' => 'Los esenciales',
				'instructions'=> 'Usa <em></em> para itálica.',
			],
			[
				'key'         => 'field_murg_hp_best_temporada',
				'label'       => 'Temporada / época',
				'name'        => 'hp_best_temporada',
				'type'        => 'text',
				'placeholder' => 'Otoño MMXXVI',
			],
			[
				'key'           => 'field_murg_hp_best_productos',
				'label'         => 'Productos destacados',
				'name'          => 'hp_best_productos',
				'type'          => 'post_object',
				'post_type'     => [ 'product' ],
				'return_format' => 'id',
				'multiple'      => 1,
				'allow_null'    => 1,
				'ui'            => 1,
				'instructions'  => 'Selecciona hasta 3 productos. Si no se selecciona ninguno, se usan los más vendidos automáticamente.',
			],

			/* ========================================================
			   TAB: STATEMENT
			   ======================================================== */
			[
				'key'       => 'field_murg_tab_stmt',
				'label'     => '✦ Statement',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_hp_stmt_eyebrow',
				'label'       => 'Eyebrow',
				'name'        => 'hp_stmt_eyebrow',
				'type'        => 'text',
				'placeholder' => 'Nuestra Casa',
			],
			[
				'key'         => 'field_murg_hp_stmt_texto',
				'label'       => 'Cita / texto principal',
				'name'        => 'hp_stmt_texto',
				'type'        => 'textarea',
				'rows'        => 4,
				'instructions'=> 'Usa <em></em> para resaltar palabras en dorado.',
				'placeholder' => '"Cada pieza nace en nuestro taller en Lima..."',
			],
			[
				'key'         => 'field_murg_hp_stmt_atribucion',
				'label'       => 'Atribución',
				'name'        => 'hp_stmt_atribucion',
				'type'        => 'text',
				'placeholder' => '— Casa Murguía, Fundada en 1962',
			],
			[
				'key'          => 'field_murg_hp_stmt_imagen',
				'label'        => 'Imagen de fondo (opcional)',
				'name'         => 'hp_stmt_imagen',
				'type'         => 'image',
				'return_format'=> 'array',
				'preview_size' => 'thumbnail',
				'instructions' => 'Si se deja vacío, se usa el fondo negro por defecto.',
			],

			/* ========================================================
			   TAB: CONTACTO
			   ======================================================== */
			[
				'key'       => 'field_murg_tab_cont',
				'label'     => '📍 Contacto',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_hp_cont_eyebrow',
				'label'       => 'Eyebrow',
				'name'        => 'hp_cont_eyebrow',
				'type'        => 'text',
				'placeholder' => 'Visite el Atelier',
			],
			[
				'key'         => 'field_murg_hp_cont_titulo',
				'label'       => 'Título',
				'name'        => 'hp_cont_titulo',
				'type'        => 'text',
				'placeholder' => 'Visítanos o agenda una cita',
				'instructions'=> 'Usa <em></em> para itálica.',
			],
			[
				'key'   => 'field_murg_hp_cont_texto',
				'label' => 'Texto introductorio',
				'name'  => 'hp_cont_texto',
				'type'  => 'textarea',
				'rows'  => 3,
			],
			[
				'key'   => 'field_murg_hp_cont_direccion',
				'label' => 'Dirección',
				'name'  => 'hp_cont_direccion',
				'type'  => 'textarea',
				'rows'  => 2,
			],
			[
				'key'         => 'field_murg_hp_cont_horario',
				'label'       => 'Horario',
				'name'        => 'hp_cont_horario',
				'type'        => 'text',
				'placeholder' => 'Lun – Sáb · 10:00 – 19:00',
			],
			[
				'key'         => 'field_murg_hp_cont_telefono',
				'label'       => 'Teléfono',
				'name'        => 'hp_cont_telefono',
				'type'        => 'text',
				'placeholder' => '+51 1 421 8800',
			],
			[
				'key'   => 'field_murg_hp_cont_email',
				'label' => 'Email',
				'name'  => 'hp_cont_email',
				'type'  => 'email',
			],
			[
				'key'         => 'field_murg_hp_cont_whatsapp',
				'label'       => 'WhatsApp URL',
				'name'        => 'hp_cont_whatsapp',
				'type'        => 'url',
				'placeholder' => 'https://wa.me/51...',
			],
			[
				'key'         => 'field_murg_hp_cont_servicios',
				'label'       => 'Servicios (uno por línea)',
				'name'        => 'hp_cont_servicios',
				'type'        => 'textarea',
				'rows'        => 3,
				'placeholder' => "Diseño a medida\nRestauración",
			],
			[
				'key'         => 'field_murg_hp_cont_serv_sub',
				'label'       => 'Servicios · nota al pie',
				'name'        => 'hp_cont_serv_sub',
				'type'        => 'text',
				'placeholder' => 'Presupuesto sin costo',
			],

			/* ========================================================
			   TAB: FOOTER
			   ======================================================== */
			[
				'key'       => 'field_murg_tab_foot',
				'label'     => '🔗 Footer',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
			],
			[
				'key'         => 'field_murg_hp_foot_marca',
				'label'       => 'Nombre de marca',
				'name'        => 'hp_foot_marca',
				'type'        => 'text',
				'placeholder' => 'Murguía',
				'instructions'=> 'Se usa en el logo del nav y en el footer.',
			],
			[
				'key'         => 'field_murg_hp_foot_marca_sub',
				'label'       => 'Subtítulo de marca (footer)',
				'name'        => 'hp_foot_marca_sub',
				'type'        => 'text',
				'placeholder' => 'Joyería · Lima · MCMLXII',
			],
			[
				'key'         => 'field_murg_hp_nav_logo_sub',
				'label'       => 'Subtítulo de marca (nav)',
				'name'        => 'hp_nav_logo_sub',
				'type'        => 'text',
				'placeholder' => 'Joyería · Lima',
			],
			[
				'key'   => 'field_murg_hp_foot_tagline',
				'label' => 'Tagline / descripción',
				'name'  => 'hp_foot_tagline',
				'type'  => 'textarea',
				'rows'  => 2,
			],
			[
				'key'         => 'field_murg_hp_foot_copyright',
				'label'       => 'Texto de copyright',
				'name'        => 'hp_foot_copyright',
				'type'        => 'text',
				'placeholder' => '© MMXXVI Joyería Murguía S.A.C.',
			],
			[
				'key'          => 'field_murg_hp_foot_redes',
				'label'        => 'Redes Sociales',
				'name'         => 'hp_foot_redes',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Añadir Red Social',
				'sub_fields'   => [
					[
						'key'         => 'field_murg_red_nombre',
						'label'       => 'Nombre',
						'name'        => 'red_nombre',
						'type'        => 'text',
						'placeholder' => 'Instagram',
					],
					[
						'key'   => 'field_murg_red_url',
						'label' => 'URL',
						'name'  => 'red_url',
						'type'  => 'url',
					],
				],
			],
		],
	] );
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
