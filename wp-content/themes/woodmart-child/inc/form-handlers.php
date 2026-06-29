<?php

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
