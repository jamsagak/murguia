<?php

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
		[ 'post_title' => 'Tiendas',           'post_name' => 'tiendas' ],
		[ 'post_title' => 'Anillos de Compromiso', 'post_name' => 'anillos-compromiso-page' ],
		[ 'post_title' => 'Aros de Matrimonio', 'post_name' => 'aros-matrimonio-page' ],
		[ 'post_title' => 'Alta Joyería',      'post_name' => 'alta-joyeria-page' ],
		[ 'post_title' => 'Las 4Cs',            'post_name' => 'las-4cs-page' ],
		[ 'post_title' => 'Sobre Nosotros',    'post_name' => 'nosotros' ],
	];

	// Version-keyed gate: only runs the queries when this list changes.
	// Bumping the section list automatically invalidates the cached fingerprint.
	$fingerprint = md5( wp_json_encode( wp_list_pluck( $secciones, 'post_name' ) ) );
	$marker_key  = 'murguia_ajustes_defaults_ok';
	if ( get_option( $marker_key ) === $fingerprint ) {
		return;
	}

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

	update_option( $marker_key, $fingerprint, true );
}

add_action( 'init', 'murguia_seed_tiendas_y_4cs', 1000 );
add_action( 'init', 'murguia_ensure_custom_pages', 1001 );

function murguia_ensure_custom_pages() {
	$pages = [
		[
			'title' => 'Tiendas',
			'slug' => 'tiendas',
			'template' => 'page-tiendas.php',
			'content' => 'Conoce nuestras boutiques en Lima y encuentra el espacio mas cercano para recibir asesoria personalizada.',
		],
		[
			'title' => 'Las 4Cs',
			'slug' => 'las-4cs',
			'template' => 'page-las-4cs.php',
			'content' => '',
		],
		[
			'title' => 'Diseña tu anillo',
			'slug' => 'disena-tu-anillo',
			'template' => 'page-disena-tu-anillo.php',
			'content' => 'Configura un anillo de compromiso con asesoria privada y cotizacion personalizada.',
		],
		[
			'title' => 'Aros de Matrimonio',
			'slug' => 'aros-matrimonio',
			'template' => 'page-aros-matrimonio.php',
			'content' => 'Disena tus aros de matrimonio con modelo, metal, talla y grabado personalizado.',
		],
	];

	foreach ( $pages as $page ) {
		$post = get_page_by_path( $page['slug'], OBJECT, 'page' );
		if ( $post ) {
			if ( get_post_meta( $post->ID, '_wp_page_template', true ) !== $page['template'] ) {
				update_post_meta( $post->ID, '_wp_page_template', $page['template'] );
			}
			continue;
		}

		$post_id = wp_insert_post( [
			'post_title' => $page['title'],
			'post_name' => $page['slug'],
			'post_content' => $page['content'],
			'post_type' => 'page',
			'post_status' => 'publish',
		] );

		if ( ! is_wp_error( $post_id ) && $post_id ) {
			update_post_meta( $post_id, '_wp_page_template', $page['template'] );
		}
	}
}

function murguia_seed_store_media_fields( $post_id, $gallery ) {
	$gallery = is_array( $gallery ) ? array_values( array_filter( $gallery ) ) : [];
	if ( empty( $gallery ) ) return;

	$principal = function_exists( 'get_field' ) ? get_field( 'tienda_imagen_principal', $post_id ) : get_post_meta( $post_id, 'tienda_imagen_principal', true );
	if ( $principal ) return;

	$principal_id = (int) ( $gallery[0]['imagen'] ?? 0 );
	if ( ! $principal_id ) return;

	murguia_update_editable_field( 'tienda_imagen_principal', $principal_id, $post_id, false );
	set_post_thumbnail( $post_id, $principal_id );

	if ( count( $gallery ) > 1 ) {
		murguia_update_editable_field( 'tienda_galeria', array_slice( $gallery, 1 ), $post_id, false );
	}
}

function murguia_store_seed_to_repeater_row( $store ) {
	$gallery = is_array( $store['gallery'] ?? [] ) ? array_values( $store['gallery'] ) : [];
	return [
		'visible'          => 1,
		'nombre'           => $store['name'] ?? '',
		'direccion'        => $store['address'] ?? '',
		'telefono'         => $store['phone'] ?? '',
		'horario'          => $store['hours'] ?? '',
		'imagen_principal' => (int) ( $gallery[0]['imagen'] ?? 0 ),
		'galeria'          => array_slice( $gallery, 1 ),
		'whatsapp_texto'   => 'Contactar por WhatsApp',
		'whatsapp_url'     => 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $store['phone'] ?? '' ),
		'maps_url'         => $store['maps'] ?? '',
		'mapa_iframe'      => '',
		'orden'            => (int) ( $store['order'] ?? 0 ),
	];
}

function murguia_seed_tiendas_y_4cs() {
	if ( ! post_type_exists( 'murguia_tienda' ) ) return;

	$stores = [
		[
			'name' => 'San Isidro',
			'slug' => 'san-isidro',
			'address' => 'Av. Pardo y Aliaga 572, San Isidro. Lima, Peru',
			'phone' => '+51 719-5359',
			'hours' => 'Lunes a Viernes de 10:00am a 7:00pm y Sabados de 10:00am a 5:00pm',
			'maps' => 'https://www.google.com/maps/search/?api=1&query=Av.%20Pardo%20y%20Aliaga%20572%2C%20San%20Isidro%2C%20Lima%2C%20Peru',
			'gallery' => murguia_store_gallery_seed( 'San-isidro', 1, 14 ),
			'order' => 10,
		],
		[
			'name' => 'Miraflores',
			'slug' => 'miraflores',
			'address' => 'Av. La Paz 1198, Miraflores, Lima, Peru',
			'phone' => '+01 652 - 6666',
			'hours' => 'Lunes a Viernes de 10:30am a 7:00pm y Sabados de 10:30am a 5:00pm',
			'maps' => 'https://www.google.com/maps/search/?api=1&query=Av.%20La%20Paz%201198%2C%20Miraflores%2C%20Lima%2C%20Peru',
			'gallery' => murguia_store_gallery_seed( 'Miraflores', 1, 8 ),
			'order' => 20,
		],
	];

	foreach ( $stores as $store ) {
		$post = get_page_by_path( $store['slug'], OBJECT, 'murguia_tienda' );
		if ( ! $post ) {
			$post_id = wp_insert_post( [
				'post_title' => $store['name'],
				'post_name' => $store['slug'],
				'post_type' => 'murguia_tienda',
				'post_status' => 'publish',
				'menu_order' => $store['order'],
			] );
			if ( is_wp_error( $post_id ) || ! $post_id ) continue;
			murguia_update_editable_field( 'tienda_nombre', $store['name'], $post_id, false );
			murguia_update_editable_field( 'tienda_direccion', $store['address'], $post_id, false );
			murguia_update_editable_field( 'tienda_telefono', $store['phone'], $post_id, false );
			murguia_update_editable_field( 'tienda_horario', $store['hours'], $post_id, false );
			murguia_update_editable_field( 'tienda_imagen_principal', (int) ( $store['gallery'][0]['imagen'] ?? 0 ), $post_id, false );
			murguia_update_editable_field( 'tienda_galeria', array_slice( $store['gallery'], 1 ), $post_id, false );
			murguia_update_editable_field( 'tienda_whatsapp_texto', 'Contactar por WhatsApp', $post_id, false );
			murguia_update_editable_field( 'tienda_whatsapp_url', 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $store['phone'] ), $post_id, false );
			murguia_update_editable_field( 'tienda_maps_url', $store['maps'], $post_id, false );
			murguia_update_editable_field( 'tienda_orden', $store['order'], $post_id, false );
			murguia_update_editable_field( 'tienda_visible', 1, $post_id, false );
			if ( ! has_post_thumbnail( $post_id ) && ! empty( $store['gallery'][0]['imagen'] ) ) {
				set_post_thumbnail( $post_id, (int) $store['gallery'][0]['imagen'] );
			}
		} else {
			murguia_seed_store_media_fields( $post->ID, $store['gallery'] );
		}
	}

	$tt_id = murguia_ajuste_id( 'tiendas' );
	if ( $tt_id ) {
		murguia_update_editable_field( 'tt_titulo', 'Tiendas', $tt_id );
		murguia_update_editable_field( 'tt_intro', 'Conoce nuestras boutiques en Lima y encuentra el espacio mas cercano para recibir asesoria personalizada.', $tt_id );
		murguia_update_editable_field( 'tt_tiendas', array_map( 'murguia_store_seed_to_repeater_row', $stores ), $tt_id );
	}

	$c4_id = murguia_ajuste_id( 'las-4cs-page' );
	if ( ! $c4_id ) return;

	murguia_update_editable_field( 'c4_hero_eyebrow', 'Guia del diamante', $c4_id );
	murguia_update_editable_field( 'c4_hero_titulo', 'Las 4Cs del Diamante', $c4_id );
	murguia_update_editable_field( 'c4_hero_subtitulo', 'Color, claridad, corte y carataje: los criterios universales para elegir un diamante con confianza.', $c4_id );
	murguia_update_editable_field( 'c4_hero_intro', 'Cada diamante se entiende mejor cuando se mira desde sus cuatro dimensiones esenciales. Esta guia resume las 4Cs para ayudarte a reconocer la belleza, el valor y el caracter de cada piedra.', $c4_id );
	murguia_update_editable_field( 'c4_secciones', [
		[ 'titulo' => 'Color', 'subtitulo' => 'La ausencia de color como signo de pureza', 'descripcion' => 'La evaluacion del color de la mayoria de los diamantes de calidad se basa en la ausencia de color. Un diamante quimicamente puro y estructuralmente perfecto no tiene matiz, como una gota de agua pura, y por eso alcanza un valor mas alto. La escala mas aceptada va de D a Z: D es el grado mas incoloro y la presencia de color aumenta gradualmente hasta Z. Muchas diferencias son sutiles para el ojo no entrenado, pero influyen en la calidad y el precio.', 'puntos' => "Escala internacional de D a Z\nMenor color, mayor rareza\nEl matiz impacta calidad y precio", 'orden' => 10 ],
		[ 'titulo' => 'Claridad', 'subtitulo' => 'Inclusiones y pequenos rasgos naturales', 'descripcion' => 'Los diamantes naturales nacen bajo calor y presion extremos en lo profundo de la tierra. Ese proceso puede producir caracteristicas internas llamadas inclusiones y caracteristicas externas llamadas defectos. La claridad evalua su numero, tamano, relieve, naturaleza y posicion, asi como su efecto sobre la apariencia general. Aunque ningun diamante natural es absolutamente puro, cuanto mas se acerca a esa pureza, mayor es su valor.', 'puntos' => "Evalua inclusiones y defectos\nConsidera numero, tamano y posicion\nLa pureza visual aumenta el valor", 'orden' => 20 ],
		[ 'titulo' => 'Corte', 'subtitulo' => 'La arquitectura de la luz', 'descripcion' => 'Los diamantes son reconocidos por su capacidad de transmitir luz y destellos intensos. El grado de corte describe que tan bien interactuan las facetas con la luz. Es una de las 4Cs mas complejas porque combina belleza visual y precision tecnica: brillo, fuego y centello describen la apariencia boca arriba, mientras que proporcion, durabilidad, pulido y simetria hablan del diseno y la artesania.', 'puntos' => "Define brillo, fuego y centello\nDepende de proporciones y simetria\nEs clave para la belleza visual", 'orden' => 30 ],
		[ 'titulo' => 'Carataje', 'subtitulo' => 'Peso, presencia y equilibrio', 'descripcion' => 'El peso en quilates mide cuanto pesa un diamante. Un quilate equivale a 200 miligramos. El precio suele aumentar con el peso porque los diamantes grandes son mas raros, pero dos diamantes del mismo carataje pueden tener valores muy distintos segun color, claridad y corte. El valor de un diamante se determina por el equilibrio de las 4Cs, no solo por su peso.', 'puntos' => "1 quilate equivale a 200 mg\nEl peso no determina todo el valor\nDebe leerse junto con las otras 3Cs", 'orden' => 40 ],
	], $c4_id );
	murguia_update_editable_field( 'c4_color_escala', [
		[ 'grado' => 'D', 'etiqueta' => 'Incoloro' ], [ 'grado' => 'E', 'etiqueta' => 'Incoloro' ], [ 'grado' => 'F', 'etiqueta' => 'Incoloro' ],
		[ 'grado' => 'G', 'etiqueta' => 'Casi incoloro' ], [ 'grado' => 'H', 'etiqueta' => 'Casi incoloro' ], [ 'grado' => 'I', 'etiqueta' => 'Casi incoloro' ], [ 'grado' => 'J', 'etiqueta' => 'Casi incoloro' ],
		[ 'grado' => 'K-Z', 'etiqueta' => 'Color perceptible' ],
	], $c4_id );
	murguia_update_editable_field( 'c4_claridad_escala', [
		[ 'grado' => 'IF', 'descripcion' => 'Internamente perfecto' ],
		[ 'grado' => 'VVS1', 'descripcion' => 'Inclusion muy muy ligera' ],
		[ 'grado' => 'VVS2', 'descripcion' => 'Inclusion muy muy ligera' ],
		[ 'grado' => 'VS1', 'descripcion' => 'Inclusion muy ligera' ],
		[ 'grado' => 'VS2', 'descripcion' => 'Inclusion muy ligera' ],
		[ 'grado' => 'SI1', 'descripcion' => 'Inclusion ligera' ],
		[ 'grado' => 'SI2', 'descripcion' => 'Inclusion ligera' ],
	], $c4_id );
	murguia_update_editable_field( 'c4_corte_conceptos', [
		[ 'titulo' => 'Brillo', 'texto' => 'Luz blanca interna y externa reflejada desde un diamante.' ],
		[ 'titulo' => 'Fuego', 'texto' => 'Dispersion de la luz blanca en todos los colores del arco iris.' ],
		[ 'titulo' => 'Centello', 'texto' => 'Destellos y patron de areas claras y oscuras causadas por los reflejos internos.' ],
	], $c4_id );
	murguia_update_editable_field( 'c4_carataje_ejemplos', [
		[ 'valor' => '.25', 'etiqueta' => '0.25 ct' ], [ 'valor' => '.50', 'etiqueta' => '0.50 ct' ], [ 'valor' => '1.00', 'etiqueta' => '1.00 ct' ],
		[ 'valor' => '1.50', 'etiqueta' => '1.50 ct' ], [ 'valor' => '2.00', 'etiqueta' => '2.00 ct' ], [ 'valor' => '2.50', 'etiqueta' => '2.50 ct' ],
	], $c4_id );
	murguia_update_editable_field( 'c4_cta_titulo', 'Elige con asesoria experta', $c4_id );
	murguia_update_editable_field( 'c4_cta_texto', 'Nuestro equipo puede ayudarte a comparar diamantes y encontrar el equilibrio adecuado entre belleza, rareza y presupuesto.', $c4_id );
	murguia_update_editable_field( 'c4_cta_principal_texto', 'Agendar cita', $c4_id );
	murguia_update_editable_field( 'c4_cta_principal_url', home_url( '/contacto/' ), $c4_id );
	murguia_update_editable_field( 'c4_cta_secundario_texto', 'Ver anillos', $c4_id );
	murguia_update_editable_field( 'c4_cta_secundario_url', home_url( '/anillos-compromiso/' ), $c4_id );
}

/* ------------------------------------------------------------------
   SEED — Configuradores (Diseña tu Anillo + Diseña tu Aro)
   Precarga repeaters con los valores actuales para que el cliente
   los vea desde el primer login. Las imágenes de formas se omiten
   porque dependen de attachments propios — el template usa el
   fallback hardcoded mientras estén vacías.
   ------------------------------------------------------------------ */
add_action( 'init', 'murguia_seed_configuradores', 1010 );

function murguia_seed_configuradores() {
	if ( ! post_type_exists( 'murguia_ajustes' ) ) return;

	$fingerprint = 'v1-2026-06-29';
	$marker      = 'murguia_seed_configuradores_ok';
	if ( get_option( $marker ) === $fingerprint ) return;

	/* ── Diseña tu Anillo ── */
	$da_id = murguia_ajuste_id( 'anillos-compromiso-page' );
	if ( $da_id ) {
		murguia_update_editable_field( 'da_modelos', [
			[ 'label' => 'Solitario clásico' ],
			[ 'label' => 'Hidden halo' ],
			[ 'label' => 'Halo' ],
			[ 'label' => 'Tres piedras' ],
			[ 'label' => 'Pavé' ],
			[ 'label' => 'Diseño personalizado' ],
		], $da_id );
		murguia_update_editable_field( 'da_metales', [
			[ 'label' => 'Oro amarillo 18K', 'color' => '#d4a843' ],
			[ 'label' => 'Oro blanco 18K',   'color' => '#e8e4dc' ],
			[ 'label' => 'Oro rosado 18K',   'color' => '#e8b4a0' ],
			[ 'label' => 'Platino',          'color' => '#c9c9c9' ],
		], $da_id );
		murguia_update_editable_field( 'da_tallas', array_map( function ( $v ) {
			return [ 'valor' => $v ];
		}, [ '4', '4.5', '5', '5.5', '6', '6.5', '7', '7.5', '8', '8.5', '9', '9.5', '10', '10.5', '11' ] ), $da_id );
		murguia_update_editable_field( 'da_origenes', [
			[ 'label' => 'Natural' ],
			[ 'label' => 'Laboratorio' ],
		], $da_id );
		murguia_update_editable_field( 'da_quilates_min',     0.30, $da_id );
		murguia_update_editable_field( 'da_quilates_max',     3.00, $da_id );
		murguia_update_editable_field( 'da_quilates_default', 1.00, $da_id );
		murguia_update_editable_field( 'da_quilates_step',    0.10, $da_id );
	}

	/* ── Diseña tu Aro ── */
	$dar_id = murguia_ajuste_id( 'aros-matrimonio-page' );
	if ( $dar_id ) {
		murguia_update_editable_field( 'dar_modelos', [
			[ 'label' => 'Media caña', 'desc' => 'Superficie curva clásica, cómoda para uso diario.' ],
			[ 'label' => 'Cinta',      'desc' => 'Perfil plano y arquitectónico, ideal para grabado.' ],
		], $dar_id );
		murguia_update_editable_field( 'dar_metales', [
			[ 'label' => 'Oro amarillo 18K', 'color' => '#d4a843' ],
			[ 'label' => 'Oro blanco 18K',   'color' => '#e8e4dc' ],
			[ 'label' => 'Oro rosado 18K',   'color' => '#e8b4a0' ],
		], $dar_id );
		murguia_update_editable_field( 'dar_tallas', array_map( function ( $v ) {
			return [ 'valor' => $v ];
		}, [ '4', '4.5', '5', '5.5', '6', '6.5', '7', '7.5', '8', '8.5', '9', '9.5', '10', '10.5', '11' ] ), $dar_id );
		murguia_update_editable_field( 'dar_tipografias', [
			[ 'slug' => 'imprenta', 'label' => 'Imprenta', 'sample' => 'AMOR' ],
			[ 'slug' => 'cursiva',  'label' => 'Cursiva',  'sample' => 'amor' ],
		], $dar_id );
		murguia_update_editable_field( 'dar_ancho_min',     2.0,  $dar_id );
		murguia_update_editable_field( 'dar_ancho_max',     10.0, $dar_id );
		murguia_update_editable_field( 'dar_ancho_default', 4.0,  $dar_id );
		murguia_update_editable_field( 'dar_ancho_step',    0.5,  $dar_id );
		murguia_update_editable_field( 'dar_grabado_max',   32,   $dar_id );
		murguia_update_editable_field( 'dar_grabado_placeholder', 'Ej. Para siempre — 14/02/2027', $dar_id );

		/* Pasos de la landing /aros-matrimonio/ */
		murguia_update_editable_field( 'aml_pasos', [
			[ 'titulo' => 'Modelo',  'texto' => 'Clásico, media caña, plano, comfort fit o diseño personalizado.' ],
			[ 'titulo' => 'Metal',   'texto' => 'Oro amarillo, blanco, rosado o combinaciones especiales.' ],
			[ 'titulo' => 'Talla',   'texto' => 'Validamos medida y comodidad antes de confirmar la pieza final.' ],
			[ 'titulo' => 'Grabado', 'texto' => 'Iniciales, fechas o mensajes breves para una pieza personal.' ],
		], $dar_id );
	}

	/* Beneficios del bloque de cita en /contacto/ */
	$ct_id = murguia_ajuste_id( 'contacto' );
	if ( $ct_id ) {
		murguia_update_editable_field( 'ct_perks', [
			[ 'titulo' => 'Asesoría GIA',       'texto' => 'Evaluación y selección de diamantes con certificación internacional.' ],
			[ 'titulo' => 'Diseños Exclusivos', 'texto' => 'Conceptualización y modelado en 3D de su pieza soñada.' ],
		], $ct_id );
	}

	update_option( $marker, $fingerprint, true );
}
