<?php

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
