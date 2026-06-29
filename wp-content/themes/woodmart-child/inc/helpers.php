<?php

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
