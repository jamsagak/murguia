<?php

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

			/* ---- TAB: Sección Cita (form) ---- */
			[ 'key' => 'field_murg_ct_tab_form', 'label' => '🗓️ Sección Cita', 'name' => '', 'type' => 'tab', 'placement' => 'top' ],
			[ 'key' => 'field_murg_ct_form_eyebrow', 'label' => 'Eyebrow', 'name' => 'ct_form_eyebrow', 'type' => 'text', 'default_value' => 'Agende una cita' ],
			[ 'key' => 'field_murg_ct_form_titulo',  'label' => 'Título',  'name' => 'ct_form_titulo',  'type' => 'text', 'default_value' => 'Planifique su visita' ],
			[ 'key' => 'field_murg_ct_form_texto',   'label' => 'Texto',   'name' => 'ct_form_texto',   'type' => 'textarea', 'rows' => 3, 'default_value' => 'Le sugerimos agendar una cita previa para brindarle una atención exclusiva, privada y sin prisas en nuestro atelier. Especialmente recomendado para el diseño de anillos de compromiso, aros de matrimonio y piezas de alta joyería a medida.' ],
			[
				'key' => 'field_murg_ct_perks', 'label' => 'Beneficios destacados', 'name' => 'ct_perks',
				'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar beneficio',
				'sub_fields' => [
					[ 'key' => 'field_murg_ct_perk_titulo', 'label' => 'Título', 'name' => 'titulo', 'type' => 'text' ],
					[ 'key' => 'field_murg_ct_perk_texto',  'label' => 'Texto',  'name' => 'texto',  'type' => 'textarea', 'rows' => 2 ],
				],
			],

			/* ---- TAB: Boutiques ---- */
			[ 'key' => 'field_murg_ct_tab_boutiques', 'label' => '🏬 Boutiques', 'name' => '', 'type' => 'tab', 'placement' => 'top' ],
			[ 'key' => 'field_murg_ct_boutiques_eyebrow', 'label' => 'Eyebrow', 'name' => 'ct_boutiques_eyebrow', 'type' => 'text', 'default_value' => 'Nuestras boutiques' ],
			[ 'key' => 'field_murg_ct_boutiques_titulo',  'label' => 'Título',  'name' => 'ct_boutiques_titulo',  'type' => 'text', 'default_value' => 'Puntos de encuentro' ],
			[ 'key' => 'field_murg_ct_boutiques_texto',   'label' => 'Texto',   'name' => 'ct_boutiques_texto',   'type' => 'textarea', 'rows' => 2, 'default_value' => 'Visite nuestros espacios físicos para conocer las colecciones de cerca y recibir asistencia directa de nuestros asesores.' ],
		],
	] );
}
