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
	if ( is_shop() || is_product_taxonomy() ) {
		$custom = get_stylesheet_directory() . '/archive-product.php';
		if ( file_exists( $custom ) ) {
			return $custom;
		}
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
 * Remover hooks de wp_body_open de WoodMart en nuestras páginas custom
 * (skip-links, toolbar bottom, etc). Mantenemos wp_body_open() en los
 * templates para compatibilidad con otros plugins (admin bar, etc).
 */
function murguia_clean_body_open_hooks() {
	if ( ! murguia_is_custom_template() ) {
		return;
	}
	// WoodMart imprime .wd-skip-links vía get_template_part en su header.
	// El HTML aparece porque WoodMart tiene hooks en wp_body_open → los removemos.
	remove_all_actions( 'woodmart_before_header_action' );
	remove_all_actions( 'woodmart_after_header_action' );
	// Cualquier callback anónimo enganchado directamente a wp_body_open por WoodMart:
	// no podemos targetearlo por nombre, pero quitamos todos los handlers que no
	// sean de WP core (priority 10 = wp_admin_bar_render).
	global $wp_filter;
	if ( isset( $wp_filter['wp_body_open'] ) ) {
		foreach ( $wp_filter['wp_body_open']->callbacks as $priority => $callbacks ) {
			foreach ( $callbacks as $id => $cb ) {
				// Conservar admin bar y cualquier callback de core.
				if ( false !== strpos( $id, 'wp_admin_bar_render' ) ) {
					continue;
				}
				// Identificar callbacks de WoodMart por su función/clase.
				$target = $cb['function'];
				if ( is_array( $target ) && is_object( $target[0] ) ) {
					$class = get_class( $target[0] );
					if ( false !== stripos( $class, 'woodmart' ) || false !== stripos( $class, 'XTS' ) ) {
						remove_action( 'wp_body_open', $target, $priority );
					}
				} elseif ( is_string( $target ) && ( 0 === stripos( $target, 'woodmart_' ) || 0 === stripos( $target, 'xts_' ) ) ) {
					remove_action( 'wp_body_open', $target, $priority );
				}
			}
		}
	}
}
add_action( 'wp', 'murguia_clean_body_open_hooks', 99 );
