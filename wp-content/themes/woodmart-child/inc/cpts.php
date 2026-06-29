<?php

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

	register_post_type( 'murguia_tienda', [
		'labels'       => [
			'name'               => 'Tiendas',
			'singular_name'      => 'Tienda',
			'menu_name'          => 'Tiendas',
			'all_items'          => 'Todas las Tiendas',
			'add_new_item'       => 'Anadir Tienda',
			'edit_item'          => 'Editar Tienda',
			'new_item'           => 'Nueva Tienda',
			'view_item'          => 'Ver Tienda',
			'search_items'       => 'Buscar Tiendas',
			'not_found'          => 'No se encontraron tiendas',
			'featured_image'     => 'Imagen principal',
			'set_featured_image' => 'Asignar imagen principal',
		],
		'public'       => true,
		'has_archive'  => false,
		'supports'     => [ 'title', 'thumbnail', 'page-attributes' ],
		'rewrite'      => [ 'slug' => 'local' ],
		'show_in_menu' => 'xts_dashboard',
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-store',
	] );

	register_post_type( 'murguia_reclamo', [
		'labels'       => [
			'name'               => 'Libro de Reclamaciones',
			'singular_name'      => 'Reclamacion',
			'menu_name'          => 'Reclamaciones',
			'all_items'          => 'Todas las Reclamaciones',
			'edit_item'          => 'Ver Reclamacion',
			'view_item'          => 'Ver Reclamacion',
			'search_items'       => 'Buscar Reclamaciones',
			'not_found'          => 'No se encontraron reclamaciones',
		],
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => 'xts_dashboard',
		'show_in_rest'        => false,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'supports'            => [ 'title' ],
		'menu_icon'           => 'dashicons-clipboard',
		'capabilities'        => [
			'create_posts' => 'do_not_allow',
		],
		'map_meta_cap' => true,
	] );
}
