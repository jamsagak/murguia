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
		'https://fonts.googleapis.com/css2?family=Tiro+Bangla&display=swap',
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
add_action( 'admin_post_murg_newsletter_subscribe',        'murguia_handle_newsletter_subscribe' );
add_action( 'admin_post_nopriv_murg_newsletter_subscribe', 'murguia_handle_newsletter_subscribe' );
add_action( 'admin_post_murg_reclamo',        'murguia_handle_reclamo' );
add_action( 'admin_post_nopriv_murg_reclamo', 'murguia_handle_reclamo' );
add_action( 'woocommerce_review_order_before_submit',      'murguia_checkout_newsletter_optin', 9 );
add_action( 'woocommerce_checkout_order_processed',        'murguia_capture_checkout_newsletter_optin', 10, 3 );

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

function murguia_handle_newsletter_subscribe() {
	if ( ! isset( $_POST['murg_nl_nonce'] ) || ! wp_verify_nonce( $_POST['murg_nl_nonce'], 'murg_newsletter' ) ) {
		wp_die( 'Solicitud no válida.', 403 );
	}

	$email = sanitize_email( $_POST['email'] ?? '' );
	if ( ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'newsletter', 'error', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	}

	// 1. Send email to admin as backup
	$to      = get_option( 'admin_email' );
	$subject = '[Murguía] Nueva suscripción al newsletter';
	$body    = sprintf( "Correo: %s\nOrigen: %s", $email, esc_url_raw( wp_get_referer() ?: home_url( '/' ) ) );
	wp_mail( $to, $subject, $body, [ 'Content-Type: text/plain; charset=UTF-8' ] );

	// 2. Background Mailchimp integration
	$action     = trim( (string) murguia_ajuste( 'hp_mailchimp_action', '', 'pagina-de-inicio' ) );
	$email_name = trim( (string) murguia_ajuste( 'hp_mailchimp_email_name', 'EMAIL', 'pagina-de-inicio' ) );

	if ( $action ) {
		$body_args = [
			$email_name => $email,
		];
		wp_remote_post( $action, [
			'method'    => 'POST',
			'body'      => $body_args,
			'timeout'   => 15,
			'headers'   => [
				'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
			],
		] );
	}

	wp_safe_redirect( add_query_arg( 'newsletter', 'ok', wp_get_referer() ?: home_url( '/' ) ) );
	exit;
}

function murguia_newsletter_form_config() {
	return [
		'action'     => esc_url( admin_url( 'admin-post.php' ) ),
		'method'     => 'post',
		'email_name' => 'email',
		'external'   => false,
	];
}

function murguia_checkout_newsletter_optin() {
	if ( ! function_exists( 'woocommerce_form_field' ) ) {
		return;
	}

	woocommerce_form_field( 'murg_newsletter_optin', [
		'type'  => 'checkbox',
		'class' => [ 'form-row-wide', 'murg-checkout-newsletter' ],
		'label' => 'Deseo recibir inspiracion, novedades y piezas seleccionadas de Murguia.',
	], false );
}

function murguia_capture_checkout_newsletter_optin( $order_id, $posted_data, $order ) {
	if ( empty( $_POST['murg_newsletter_optin'] ) || ! $order instanceof WC_Order ) {
		return;
	}

	$email = $order->get_billing_email();
	if ( ! is_email( $email ) ) {
		return;
	}

	// 1. Send email to admin as backup
	$subject = '[Murguia] Suscripcion newsletter desde checkout';
	$message = "Email: {$email}\nPedido: #{$order_id}\nOrigen: checkout";
	wp_mail( get_option( 'admin_email' ), $subject, $message );

	// 2. Background Mailchimp integration
	$action     = trim( (string) murguia_ajuste( 'hp_mailchimp_action', '', 'pagina-de-inicio' ) );
	$email_name = trim( (string) murguia_ajuste( 'hp_mailchimp_email_name', 'EMAIL', 'pagina-de-inicio' ) );

	if ( $action ) {
		$body_args = [
			$email_name => $email,
		];
		wp_remote_post( $action, [
			'method'    => 'POST',
			'body'      => $body_args,
			'timeout'   => 15,
			'headers'   => [
				'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
			],
		] );
	}
}

function murguia_handle_reclamo() {
	$redirect_to = wp_get_referer() ?: home_url( '/libro-de-reclamaciones/' );

	if ( ! isset( $_POST['murg_reclamo_nonce'] ) || ! wp_verify_nonce( $_POST['murg_reclamo_nonce'], 'murg_reclamo' ) ) {
		wp_die( 'Solicitud no valida.', 403 );
	}

	if ( empty( $_POST['rc_privacidad'] ) ) {
		wp_safe_redirect( add_query_arg( 'reclamo', 'privacidad', $redirect_to ) );
		exit;
	}

	$fields = [
		'nombres'         => sanitize_text_field( wp_unslash( $_POST['rc_nombres']          ?? '' ) ),
		'apellidos'       => sanitize_text_field( wp_unslash( $_POST['rc_apellidos']        ?? '' ) ),
		'email'           => sanitize_email(      wp_unslash( $_POST['rc_email']            ?? '' ) ),
		'telefono'        => sanitize_text_field( wp_unslash( $_POST['rc_telefono']         ?? '' ) ),
		'direccion'       => sanitize_text_field( wp_unslash( $_POST['rc_direccion']        ?? '' ) ),
		'distrito'        => sanitize_text_field( wp_unslash( $_POST['rc_distrito']         ?? '' ) ),
		'provincia'       => sanitize_text_field( wp_unslash( $_POST['rc_provincia']        ?? '' ) ),
		'departamento'    => sanitize_text_field( wp_unslash( $_POST['rc_departamento']     ?? '' ) ),
		'tipo_doc'        => sanitize_text_field( wp_unslash( $_POST['rc_tipo_doc']         ?? '' ) ),
		'num_doc'         => sanitize_text_field( wp_unslash( $_POST['rc_num_doc']          ?? '' ) ),
		'tipo_bien'       => sanitize_text_field( wp_unslash( $_POST['rc_tipo_bien']        ?? '' ) ),
		'num_pedido'      => sanitize_text_field( wp_unslash( $_POST['rc_num_pedido']       ?? '' ) ),
		'monto'           => sanitize_text_field( wp_unslash( $_POST['rc_monto']            ?? '' ) ),
		'descripcion_bien'=> sanitize_textarea_field( wp_unslash( $_POST['rc_descripcion_bien'] ?? '' ) ),
		'tipo'            => sanitize_text_field( wp_unslash( $_POST['rc_tipo']             ?? '' ) ),
		'detalle'         => sanitize_textarea_field( wp_unslash( $_POST['rc_detalle']      ?? '' ) ),
		'pedido_consumidor' => sanitize_textarea_field( wp_unslash( $_POST['rc_pedido_consumidor'] ?? '' ) ),
	];

	$tipo_doc_allowed = [ 'dni', 'ce' ];
	if ( ! in_array( $fields['tipo_doc'], $tipo_doc_allowed, true ) ) {
		$fields['tipo_doc'] = 'dni';
	}
	$tipo_bien_allowed = [ 'producto', 'servicio' ];
	if ( ! in_array( $fields['tipo_bien'], $tipo_bien_allowed, true ) ) {
		$fields['tipo_bien'] = 'producto';
	}
	$tipo_allowed = [ 'reclamo', 'queja' ];
	if ( ! in_array( $fields['tipo'], $tipo_allowed, true ) ) {
		$fields['tipo'] = 'reclamo';
	}

	$required = [ 'nombres', 'apellidos', 'email', 'telefono', 'num_doc', 'descripcion_bien', 'detalle' ];
	foreach ( $required as $key ) {
		if ( empty( $fields[ $key ] ) ) {
			wp_safe_redirect( add_query_arg( 'reclamo', 'error', $redirect_to ) );
			exit;
		}
	}
	if ( ! is_email( $fields['email'] ) ) {
		wp_safe_redirect( add_query_arg( 'reclamo', 'error', $redirect_to ) );
		exit;
	}

	$post_id = wp_insert_post( [
		'post_type'   => 'murguia_reclamo',
		'post_status' => 'publish',
		'post_title'  => sprintf(
			'%s — %s %s (%s)',
			current_time( 'Y-m-d H:i' ),
			$fields['nombres'],
			$fields['apellidos'],
			strtoupper( $fields['tipo'] )
		),
	], true );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		wp_safe_redirect( add_query_arg( 'reclamo', 'error', $redirect_to ) );
		exit;
	}

	foreach ( $fields as $key => $value ) {
		update_post_meta( $post_id, '_rc_' . $key, $value );
	}
	update_post_meta( $post_id, '_rc_ip',         isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	update_post_meta( $post_id, '_rc_user_agent', isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '' );
	update_post_meta( $post_id, '_rc_estado',     'pendiente' );

	$correo_negocio = murguia_ajuste( 'ct_email_reclamos', '', 'contacto' );
	if ( ! is_email( $correo_negocio ) ) {
		$correo_negocio = murguia_ajuste( 'ct_email', '', 'contacto' );
	}
	if ( ! is_email( $correo_negocio ) ) {
		$correo_negocio = get_option( 'admin_email' );
	}

	$codigo_reclamo = sprintf( 'RC-%d-%s', $post_id, gmdate( 'Ymd' ) );
	update_post_meta( $post_id, '_rc_codigo', $codigo_reclamo );

	$subject_negocio = sprintf( '[Murguia] %s #%s — %s %s',
		ucfirst( $fields['tipo'] ),
		$codigo_reclamo,
		$fields['nombres'],
		$fields['apellidos']
	);
	$body_negocio = sprintf(
		"Nueva %s recibida desde el Libro de Reclamaciones virtual.\n\n" .
		"Codigo: %s\nFecha: %s\nTipo: %s\n\n" .
		"== CONSUMIDOR ==\n" .
		"Nombres: %s\nApellidos: %s\nDocumento: %s %s\n" .
		"Email: %s\nTelefono: %s\n" .
		"Direccion: %s\nDistrito: %s\nProvincia: %s\nDepartamento: %s\n\n" .
		"== BIEN CONTRATADO ==\n" .
		"Tipo: %s\nNumero de pedido: %s\nMonto: %s\nDescripcion: %s\n\n" .
		"== DETALLE ==\n%s\n\n" .
		"== PEDIDO DEL CONSUMIDOR ==\n%s\n\n" .
		"Ver registro en admin: %s",
		$fields['tipo'],
		$codigo_reclamo,
		current_time( 'd/m/Y H:i' ),
		ucfirst( $fields['tipo'] ),
		$fields['nombres'], $fields['apellidos'], strtoupper( $fields['tipo_doc'] ), $fields['num_doc'],
		$fields['email'], $fields['telefono'],
		$fields['direccion'] ?: '-', $fields['distrito'] ?: '-', $fields['provincia'] ?: '-', $fields['departamento'] ?: '-',
		ucfirst( $fields['tipo_bien'] ), $fields['num_pedido'] ?: '-', $fields['monto'] ?: '-', $fields['descripcion_bien'],
		$fields['detalle'],
		$fields['pedido_consumidor'] ?: '-',
		admin_url( 'post.php?action=edit&post=' . $post_id )
	);
	$headers_negocio = [
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'Reply-To: %s %s <%s>', $fields['nombres'], $fields['apellidos'], $fields['email'] ),
	];
	wp_mail( $correo_negocio, $subject_negocio, $body_negocio, $headers_negocio );

	$subject_consumidor = sprintf( 'Hemos recibido tu %s — Codigo %s', $fields['tipo'], $codigo_reclamo );
	$body_consumidor = sprintf(
		"Hola %s,\n\n" .
		"Hemos recibido tu %s en el Libro de Reclamaciones de Joyeria Murguia.\n\n" .
		"Codigo de seguimiento: %s\n" .
		"Fecha: %s\n\n" .
		"Conforme a la normativa peruana, tu solicitud sera atendida dentro de los plazos establecidos por INDECOPI.\n" .
		"Si necesitas comunicarte con nosotros referente a este caso, por favor menciona el codigo de seguimiento.\n\n" .
		"Gracias por escribirnos.\n\n" .
		"— Joyeria Murguia",
		$fields['nombres'],
		$fields['tipo'],
		$codigo_reclamo,
		current_time( 'd/m/Y H:i' )
	);
	wp_mail( $fields['email'], $subject_consumidor, $body_consumidor, [ 'Content-Type: text/plain; charset=UTF-8' ] );

	wp_safe_redirect( add_query_arg( [
		'reclamo' => 'ok',
		'codigo'  => $codigo_reclamo,
	], $redirect_to ) );
	exit;
}

add_action( 'wp_footer', 'murguia_render_floating_whatsapp', 30 );

function murguia_render_floating_whatsapp() {
	if ( is_admin() ) return;
	$url = murguia_ajuste( 'ct_whatsapp', '', 'contacto' );
	if ( ! $url ) {
		$url = 'https://wa.me/51114218800';
	}
	?>
	<a class="murg-floating-whatsapp" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Contactar por WhatsApp">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" aria-hidden="true"><path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.635.78-.12.136-.232.149-.432.05-.2-.099-.878-.308-1.662-.962-.61-.503-1.026-1.15-1.14-1.352-.115-.203-.02-.32.096-.437.104-.103.235-.29.356-.437.121-.147.158-.29.236-.483.078-.192.032-.284-.019-.413-.05-.129-.444-1.15-.607-1.575-.156-.404-.316-.344-.432-.35h-.385c-.133 0-.348.05-.526.244-.18.197-.693.677-.693 1.649 0 .972.705 1.913.803 2.045.097.133 1.38 2.127 3.352 2.98.396.174.69.289.931.37.487.153.924.128 1.272.08.349-.048 1.17-.4 1.34-.791.17-.39.17-.723.113-.79-.057-.067-.19-.125-.396-.227z"/></svg>
		<span>WhatsApp</span>
	</a>
	<?php
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

	if ( function_exists( 'is_shop' ) && ( is_shop() || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) || $is_product_search ) ) {
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

	if ( is_page() && 'page-disena-tu-anillo.php' === get_page_template_slug() ) {
		$custom = get_stylesheet_directory() . '/page-disena-tu-anillo.php';
		if ( file_exists( $custom ) ) return $custom;
	}
	if ( is_page( 'disena-tu-anillo' ) ) {
		$custom = get_stylesheet_directory() . '/page-disena-tu-anillo.php';
		if ( file_exists( $custom ) ) return $custom;
	}

	if ( is_page() && 'page-aros-matrimonio.php' === get_page_template_slug() ) {
		$custom = get_stylesheet_directory() . '/page-aros-matrimonio.php';
		if ( file_exists( $custom ) ) return $custom;
	}
	if ( is_page( 'aros-matrimonio' ) ) {
		$custom = get_stylesheet_directory() . '/page-aros-matrimonio.php';
		if ( file_exists( $custom ) ) return $custom;
	}

	// Las 4Cs
	if ( is_page() && 'page-las-4cs.php' === get_page_template_slug() ) {
		$custom = get_stylesheet_directory() . '/page-las-4cs.php';
		if ( file_exists( $custom ) ) return $custom;
	}
	if ( is_page( 'las-4cs' ) ) {
		$custom = get_stylesheet_directory() . '/page-las-4cs.php';
		if ( file_exists( $custom ) ) return $custom;
	}

	// Tiendas
	if ( is_page() && 'page-tiendas.php' === get_page_template_slug() ) {
		$custom = get_stylesheet_directory() . '/page-tiendas.php';
		if ( file_exists( $custom ) ) return $custom;
	}
	if ( is_page( 'tiendas' ) ) {
		$custom = get_stylesheet_directory() . '/page-tiendas.php';
		if ( file_exists( $custom ) ) return $custom;
	}

	// Checkout — forzar nuestro template con murg-nav / murg-footer
	if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url() ) {
		$custom = get_stylesheet_directory() . '/page-checkout.php';
		if ( file_exists( $custom ) ) return $custom;
	}

	// Sobre Nosotros
	if ( is_page() && 'page-nosotros.php' === get_page_template_slug() ) {
		$custom = get_stylesheet_directory() . '/page-nosotros.php';
		if ( file_exists( $custom ) ) return $custom;
	}
	if ( is_page( 'nosotros' ) ) {
		$custom = get_stylesheet_directory() . '/page-nosotros.php';
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
	if ( function_exists( 'is_shop' ) && ( is_shop() || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) ) ) {
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
	// Página con plantilla "Nosotros" (page-nosotros.php)
	if ( is_page() && 'page-nosotros.php' === get_page_template_slug() ) {
		return true;
	}
	if ( is_page( 'nosotros' ) ) {
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
	if ( is_page() && 'page-disena-tu-anillo.php' === get_page_template_slug() ) {
		return true;
	}
	if ( is_page( 'disena-tu-anillo' ) ) {
		return true;
	}
	if ( is_page() && 'page-aros-matrimonio.php' === get_page_template_slug() ) {
		return true;
	}
	if ( is_page( 'aros-matrimonio' ) ) {
		return true;
	}
	if ( is_page() && 'page-las-4cs.php' === get_page_template_slug() ) {
		return true;
	}
	if ( is_page( 'las-4cs' ) ) {
		return true;
	}
	if ( is_page() && 'page-tiendas.php' === get_page_template_slug() ) {
		return true;
	}
	if ( is_page( 'tiendas' ) ) {
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

/* ------------------------------------------------------------------
   AROS DE MATRIMONIO - Configurador "Diseña tu aro"
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_aros_matrimonio_fields' );

function murguia_register_aros_matrimonio_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	$id = murguia_ajuste_id( 'aros-matrimonio-page' );
	if ( ! $id ) return;

	acf_add_local_field_group( [
		'key'      => 'group_murg_aros_matrimonio',
		'title'    => 'Aros de Matrimonio - Contenido',
		'location' => [ [ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ] ],
		'menu_order' => 0,
		'position'   => 'normal',
		'style'      => 'default',
		'label_placement' => 'top',
		'fields'   => [
			[ 'key' => 'field_aro_hero_eyebrow', 'label' => 'Hero - Eyebrow', 'name' => 'aro_hero_eyebrow', 'type' => 'text', 'default_value' => 'Disena tu aro' ],
			[ 'key' => 'field_aro_hero_titulo', 'label' => 'Hero - Titulo', 'name' => 'aro_hero_titulo', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Aros de matrimonio hechos para ustedes.' ],
			[ 'key' => 'field_aro_hero_intro', 'label' => 'Hero - Intro', 'name' => 'aro_hero_intro', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Configura el modelo, metal, ancho y grabado de tu aro. Al finalizar te enviamos la cotizacion por WhatsApp con todos los detalles.' ],
			[ 'key' => 'field_aro_hero_nota', 'label' => 'Hero - Nota legal', 'name' => 'aro_hero_nota', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Cada aro se trabaja a pedido. Los plazos de produccion se confirman al cotizar.' ],
			[ 'key' => 'field_aro_whatsapp_url', 'label' => 'WhatsApp URL', 'name' => 'aro_whatsapp_url', 'type' => 'url', 'instructions' => 'Si se deja vacio usa el de Anillos de Compromiso.' ],
		],
	] );
}

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

add_action( 'acf/init', 'murguia_register_4cs_fields' );
add_action( 'init', 'murguia_register_4cs_fields', 20 );

function murguia_register_4cs_fields() {
	static $registered = false;
	if ( $registered ) return;
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	$id = murguia_ajuste_id( 'las-4cs-page' );
	if ( ! $id ) return;
	$registered = true;

	acf_add_local_field_group( [
		'key' => 'group_murg_4cs',
		'title' => 'Las 4Cs - Contenido',
		'location' => [ [ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ] ],
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'show_in_rest' => 1,
		'fields' => [
			[ 'key' => 'field_murg_4cs_tab_hero', 'label' => 'Hero', 'name' => '', 'type' => 'tab' ],
			[ 'key' => 'field_murg_4cs_hero_eyebrow', 'label' => 'Eyebrow', 'name' => 'c4_hero_eyebrow', 'type' => 'text' ],
			[ 'key' => 'field_murg_4cs_hero_titulo', 'label' => 'Titulo', 'name' => 'c4_hero_titulo', 'type' => 'text' ],
			[ 'key' => 'field_murg_4cs_hero_subtitulo', 'label' => 'Subtitulo', 'name' => 'c4_hero_subtitulo', 'type' => 'textarea', 'rows' => 2 ],
			[ 'key' => 'field_murg_4cs_hero_intro', 'label' => 'Texto introductorio', 'name' => 'c4_hero_intro', 'type' => 'textarea', 'rows' => 4 ],
			[ 'key' => 'field_murg_4cs_hero_imagen', 'label' => 'Imagen hero', 'name' => 'c4_hero_imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'instructions' => 'Recomendado: imagen editorial de diamante o joya, minimo 1600x1000px.' ],
			[
				'key' => 'field_murg_4cs_secciones',
				'label' => 'Secciones 4Cs',
				'name' => 'c4_secciones',
				'type' => 'repeater',
				'layout' => 'block',
				'button_label' => 'Agregar seccion',
				'sub_fields' => [
					[ 'key' => 'field_murg_4cs_seccion_titulo', 'label' => 'Titulo de la C', 'name' => 'titulo', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_seccion_subtitulo', 'label' => 'Subtitulo opcional', 'name' => 'subtitulo', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_seccion_descripcion', 'label' => 'Descripcion', 'name' => 'descripcion', 'type' => 'textarea', 'rows' => 5 ],
					[ 'key' => 'field_murg_4cs_seccion_imagen', 'label' => 'Imagen principal', 'name' => 'imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
					[ 'key' => 'field_murg_4cs_seccion_imagen_sec', 'label' => 'Imagen secundaria opcional', 'name' => 'imagen_secundaria', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
					[ 'key' => 'field_murg_4cs_seccion_puntos', 'label' => 'Puntos destacados', 'name' => 'puntos', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'Un punto por linea.' ],
					[ 'key' => 'field_murg_4cs_seccion_orden', 'label' => 'Orden', 'name' => 'orden', 'type' => 'number' ],
				],
			],
			[ 'key' => 'field_murg_4cs_tab_escalas', 'label' => 'Escalas y ejemplos', 'name' => '', 'type' => 'tab' ],
			[
				'key' => 'field_murg_4cs_color_escala',
				'label' => 'Escala de color D-Z',
				'name' => 'c4_color_escala',
				'type' => 'repeater',
				'layout' => 'table',
				'button_label' => 'Agregar grado',
				'sub_fields' => [
					[ 'key' => 'field_murg_4cs_color_grado', 'label' => 'Grado', 'name' => 'grado', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_color_label', 'label' => 'Etiqueta', 'name' => 'etiqueta', 'type' => 'text' ],
				],
			],
			[
				'key' => 'field_murg_4cs_claridad_escala',
				'label' => 'Escala de claridad',
				'name' => 'c4_claridad_escala',
				'type' => 'repeater',
				'layout' => 'table',
				'button_label' => 'Agregar grado',
				'sub_fields' => [
					[ 'key' => 'field_murg_4cs_claridad_grado', 'label' => 'Grado', 'name' => 'grado', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_claridad_label', 'label' => 'Descripcion', 'name' => 'descripcion', 'type' => 'text' ],
				],
			],
			[
				'key' => 'field_murg_4cs_corte_conceptos',
				'label' => 'Conceptos de corte',
				'name' => 'c4_corte_conceptos',
				'type' => 'repeater',
				'layout' => 'block',
				'button_label' => 'Agregar concepto',
				'sub_fields' => [
					[ 'key' => 'field_murg_4cs_corte_concepto_titulo', 'label' => 'Titulo', 'name' => 'titulo', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_corte_concepto_texto', 'label' => 'Texto', 'name' => 'texto', 'type' => 'textarea', 'rows' => 2 ],
				],
			],
			[
				'key' => 'field_murg_4cs_carataje_ejemplos',
				'label' => 'Ejemplos de carataje',
				'name' => 'c4_carataje_ejemplos',
				'type' => 'repeater',
				'layout' => 'table',
				'button_label' => 'Agregar ejemplo',
				'sub_fields' => [
					[ 'key' => 'field_murg_4cs_carataje_valor', 'label' => 'Valor', 'name' => 'valor', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_carataje_label', 'label' => 'Etiqueta opcional', 'name' => 'etiqueta', 'type' => 'text' ],
					[ 'key' => 'field_murg_4cs_carataje_imagen', 'label' => 'Imagen opcional', 'name' => 'imagen', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ],
				],
			],
			[ 'key' => 'field_murg_4cs_tab_cta', 'label' => 'CTA', 'name' => '', 'type' => 'tab' ],
			[ 'key' => 'field_murg_4cs_cta_titulo', 'label' => 'Titulo CTA', 'name' => 'c4_cta_titulo', 'type' => 'text' ],
			[ 'key' => 'field_murg_4cs_cta_texto', 'label' => 'Texto CTA', 'name' => 'c4_cta_texto', 'type' => 'textarea', 'rows' => 2 ],
			[ 'key' => 'field_murg_4cs_cta_principal_texto', 'label' => 'Boton principal - texto', 'name' => 'c4_cta_principal_texto', 'type' => 'text' ],
			[ 'key' => 'field_murg_4cs_cta_principal_url', 'label' => 'Boton principal - URL', 'name' => 'c4_cta_principal_url', 'type' => 'url' ],
			[ 'key' => 'field_murg_4cs_cta_secundario_texto', 'label' => 'Boton secundario - texto', 'name' => 'c4_cta_secundario_texto', 'type' => 'text' ],
			[ 'key' => 'field_murg_4cs_cta_secundario_url', 'label' => 'Boton secundario - URL', 'name' => 'c4_cta_secundario_url', 'type' => 'url' ],
		],
	] );
}

/* ------------------------------------------------------------------
   ACF FIELD GROUP — Sobre Nosotros
   Prefijo: ab_   |   Ajuste slug: nosotros
   ------------------------------------------------------------------ */
add_action( 'acf/init', 'murguia_register_about_fields' );

function murguia_register_about_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	$id = murguia_ajuste_id( 'nosotros' );
	if ( ! $id ) return;

	acf_add_local_field_group( [
		'key'             => 'group_murg_about',
		'title'           => 'Sobre Nosotros — Contenido',
		'location'        => [
			[ [ 'param' => 'post', 'operator' => '==', 'value' => $id ] ],
		],
		'menu_order'      => 0,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'fields'          => [
			// ── Tab Hero ──
			[
				'key'   => 'field_ab_tab_hero',
				'label' => '🎬 Hero',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_ab_hero_imagen',
				'label'         => 'Imagen de fondo',
				'name'          => 'ab_hero_imagen',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Imagen de banner para la sección superior. Recomendado: WebP/JPG de 1920×1080px.',
			],

			// ── Tab Historia ──
			[
				'key'   => 'field_ab_tab_historia',
				'label' => '📖 Historia',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'          => 'field_ab_history_blocks',
				'label'        => 'Bloques de Historia',
				'name'         => 'ab_history_blocks',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Agregar bloque',
				'sub_fields'   => [
					[
						'key'           => 'field_ab_historia_img',
						'label'         => 'Imagen',
						'name'          => 'imagen',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					],
					[
						'key'   => 'field_ab_historia_alt',
						'label' => 'Alt de imagen',
						'name'  => 'alt',
						'type'  => 'text',
					],
					[
						'key'   => 'field_ab_historia_caption',
						'label' => 'Subtítulo / Leyenda de imagen',
						'name'  => 'caption',
						'type'  => 'text',
					],
					[
						'key'         => 'field_ab_history_copy',
						'label'       => 'Texto descriptivo',
						'name'        => 'copy',
						'type'        => 'textarea',
						'rows'        => 6,
						'instructions'=> 'Puedes separar párrafos con saltos de línea.',
					],
				],
			],

			// ── Tab Valores (Misión y Visión) ──
			[
				'key'   => 'field_ab_tab_valores',
				'label' => '✦ Misión y Visión',
				'name'  => '',
				'type'  => 'tab',
			],
			[
				'key'          => 'field_ab_values',
				'label'        => 'Misión y Visión',
				'name'         => 'ab_values',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Agregar bloque de valor',
				'sub_fields'   => [
					[
						'key'   => 'field_ab_valor_titulo',
						'label' => 'Título',
						'name'  => 'titulo',
						'type'  => 'text',
					],
					[
						'key'           => 'field_ab_valor_img',
						'label'         => 'Imagen',
						'name'          => 'imagen',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					],
					[
						'key'   => 'field_ab_valor_copy',
						'label' => 'Texto descriptivo',
						'name'  => 'copy',
						'type'  => 'textarea',
						'rows'  => 4,
					],
				],
			],
		],
	] );
}

function murguia_update_editable_field( $selector, $value, $post_id, $only_if_empty = true ) {
	if ( $only_if_empty && function_exists( 'get_field' ) ) {
		$current = get_field( $selector, $post_id );
		if ( $current !== null && $current !== false && $current !== '' && $current !== [] ) return;
	}
	if ( function_exists( 'update_field' ) ) {
		update_field( $selector, $value, $post_id );
		return;
	}
	update_post_meta( $post_id, $selector, $value );
}

function murguia_find_attachment_id_by_stem( $stem ) {
	global $wpdb;
	static $cache = [];

	if ( isset( $cache[ $stem ] ) ) return $cache[ $stem ];

	$like_file = '%' . $wpdb->esc_like( '/' . $stem . '.' ) . '%';
	$id = (int) $wpdb->get_var( $wpdb->prepare(
		"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s LIMIT 1",
		$like_file
	) );

	if ( ! $id ) {
		$id = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_title = %s LIMIT 1",
			$stem
		) );
	}

	$cache[ $stem ] = $id;
	return $id;
}

function murguia_store_gallery_seed( $prefix, $from, $to ) {
	$rows = [];
	for ( $i = $from; $i <= $to; $i++ ) {
		$id = murguia_find_attachment_id_by_stem( sprintf( '%s%05d', $prefix, $i ) );
		if ( $id ) {
			$rows[] = [ 'imagen' => $id ];
		}
	}
	return $rows;
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
