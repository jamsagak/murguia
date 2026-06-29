<?php

/* ------------------------------------------------------------------
   THEME SUPPORTS & MENUS
   ------------------------------------------------------------------ */
add_action( 'after_setup_theme', function () {
	register_nav_menus( [
		'murguia-primary' => __( 'Murguía — Primario', 'woodmart' ),
		'murguia-footer'  => __( 'Murguía — Pie de página', 'woodmart' ),
	] );
} );
