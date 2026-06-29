<?php

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
