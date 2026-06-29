<?php

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

