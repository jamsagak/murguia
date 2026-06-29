<?php

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
