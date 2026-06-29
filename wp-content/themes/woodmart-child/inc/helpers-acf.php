<?php

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
