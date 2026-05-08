<?php
/**
 * Template Part: Navegación principal
 * Reads brand name from homepage ajustes (global branding values).
 *
 * Estructura (espejo de joyeriamurguia.com):
 * Izquierda: Novios | Alta Joyería | Catálogo | Marcas
 * Centro:    Logo
 * Derecha:   Citas | Cuenta | Buscar | Bolsa
 */
$_nav_marca    = murguia_ajuste( 'hp_foot_marca',   'Murguía' );
$_nav_logo_sub = murguia_ajuste( 'hp_nav_logo_sub', 'Joyería · Lima' );

$_shop_url    = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop/' );
$_account_url = function_exists( 'wc_get_page_id' ) ? get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) : home_url( '/mi-cuenta/' );
$_cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/carrito/' );
$_cart_count  = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;

// Subcategorías de Catálogo para el dropdown
$_cat_links = [
	[ 'label' => 'Anillos de Compromiso', 'url' => home_url( '/shop/?product_cat=anillos-de-compromiso' ) ],
	[ 'label' => 'Anillos',               'url' => home_url( '/shop/?product_cat=anillos' ) ],
	[ 'label' => 'Aretes',                'url' => home_url( '/shop/?product_cat=aretes' ) ],
	[ 'label' => 'Collares y Dijes',      'url' => home_url( '/shop/?product_cat=collares-y-dijes' ) ],
	[ 'label' => 'Pulseras',              'url' => home_url( '/shop/?product_cat=pulseras' ) ],
	[ 'label' => 'Relojes',               'url' => home_url( '/shop/?product_cat=relojes' ) ],
	[ 'label' => 'Bautizo y Confirmación','url' => home_url( '/shop/?product_cat=bautizo-y-confirmacion' ) ],
	[ 'label' => 'Permanent Jewelry',     'url' => home_url( '/shop/?product_cat=permanent-jewelry' ) ],
];

// Subcategorías de Marcas
$_marca_links = [
	[ 'label' => 'Ti Sento',   'url' => home_url( '/shop/?product_cat=ti-sento' ) ],
	[ 'label' => 'Christofle', 'url' => home_url( '/shop/?product_cat=christofle' ) ],
	[ 'label' => 'Baccarat',   'url' => home_url( '/shop/?product_cat=baccarat' ) ],
	[ 'label' => 'Moraglione', 'url' => home_url( '/shop/?product_cat=moraglione' ) ],
	[ 'label' => 'Oris',       'url' => home_url( '/shop/?product_cat=oris' ) ],
	[ 'label' => 'Perrelet',   'url' => home_url( '/shop/?product_cat=perrelet' ) ],
	[ 'label' => 'Victorinox', 'url' => home_url( '/shop/?product_cat=victorinox' ) ],
	[ 'label' => 'Djula',      'url' => home_url( '/shop/?product_cat=djula' ) ],
];
?><nav class="murg-nav" id="murg-nav" role="navigation" aria-label="Navegación principal">

	<!-- ── Izquierda ─────────────────────────────────────────── -->
	<div class="murg-nav__left">

		<!-- Novios — dropdown -->
		<div class="murg-nav__item murg-nav__item--drop">
			<a href="<?php echo esc_url( home_url( '/shop/?product_cat=anillos-de-compromiso' ) ); ?>"
			   class="murg-nav__link">Novios</a>
			<div class="murg-nav__dropdown">
				<a href="<?php echo esc_url( home_url( '/shop/?product_cat=anillos-de-compromiso' ) ); ?>">Anillos de Compromiso</a>
				<a href="<?php echo esc_url( home_url( '/shop/?product_cat=aros' ) ); ?>">Aros de Matrimonio</a>
				<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Diseño a Medida</a>
			</div>
		</div>

		<!-- Alta Joyería — destacado -->
		<a href="<?php echo esc_url( home_url( '/alta-joyeria/' ) ); ?>"
		   class="murg-nav__link murg-nav__link--highlight">Alta Joyería</a>

		<!-- Catálogo — dropdown -->
		<div class="murg-nav__item murg-nav__item--drop">
			<a href="<?php echo esc_url( $_shop_url ); ?>"
			   class="murg-nav__link">Catálogo</a>
			<div class="murg-nav__dropdown">
				<?php foreach ( $_cat_links as $cl ) : ?>
				<a href="<?php echo esc_url( $cl['url'] ); ?>"><?php echo esc_html( $cl['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Marcas — dropdown -->
		<div class="murg-nav__item murg-nav__item--drop">
			<a href="<?php echo esc_url( home_url( '/shop/?product_cat=marcas' ) ); ?>"
			   class="murg-nav__link">Marcas</a>
			<div class="murg-nav__dropdown">
				<?php foreach ( $_marca_links as $ml ) : ?>
				<a href="<?php echo esc_url( $ml['url'] ); ?>"><?php echo esc_html( $ml['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>

	</div>

	<!-- ── Logo central ──────────────────────────────────────── -->
	<a class="murg-nav__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/Logo-murguia-blanco.png' ); ?>"
		     alt="<?php echo esc_attr( $_nav_marca ); ?>"
		     class="murg-nav__logo-img murg-nav__logo-img--blanco"
		     loading="eager" width="120" height="auto">
		<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/Logo-murguia.png' ); ?>"
		     alt="<?php echo esc_attr( $_nav_marca ); ?>"
		     class="murg-nav__logo-img murg-nav__logo-img--oscuro"
		     loading="eager" width="120" height="auto">
	</a>

	<!-- ── Derecha ───────────────────────────────────────────── -->
	<div class="murg-nav__right">
		<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="murg-nav__link">Citas</a>
		<a href="<?php echo esc_url( $_account_url ); ?>" class="murg-nav__link">Cuenta</a>
		<button type="button"
		        class="murg-nav__search-trigger murg-nav__link"
		        id="murg-search-open"
		        aria-haspopup="dialog"
		        aria-controls="murg-search"
		        aria-label="Abrir buscador">Buscar</button>
		<a href="<?php echo esc_url( $_cart_url ); ?>" class="murg-nav__link murg-nav__cart">
			Bolsa<?php if ( $_cart_count > 0 ) : ?> <span class="murg-nav__cart-count"><?php echo (int) $_cart_count; ?></span><?php endif; ?>
		</a>
	</div>

</nav>

<!-- ============================================================
     OVERLAY BUSCADOR
     ============================================================ -->
<div class="murg-search"
     id="murg-search"
     role="dialog"
     aria-modal="true"
     aria-labelledby="murg-search-title"
     aria-hidden="true">
	<div class="murg-search__backdrop" data-close="murg-search" aria-hidden="true"></div>
	<div class="murg-search__panel" role="document">
		<button class="murg-search__close"
		        type="button"
		        data-close="murg-search"
		        aria-label="Cerrar buscador">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
				<path d="M6 6l12 12M6 18L18 6"/>
			</svg>
		</button>
		<div class="murg-search__inner">
			<div class="murg-eyebrow">Buscar en la colección</div>
			<h2 class="murg-serif murg-search__title" id="murg-search-title">¿Qué pieza busca?</h2>
			<form class="murg-search__form"
			      role="search"
			      method="get"
			      action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="hidden" name="post_type" value="product">
				<label for="murg-search-input" class="screen-reader-text">Buscar productos</label>
				<input type="search"
				       id="murg-search-input"
				       class="murg-search__input"
				       name="s"
				       placeholder="Anillos, collares, piedras..."
				       autocomplete="off"
				       value="<?php echo esc_attr( get_search_query() ); ?>">
				<button type="submit" class="murg-search__submit" aria-label="Buscar">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
						<circle cx="11" cy="11" r="7"/><path d="M16 16l5 5"/>
					</svg>
				</button>
			</form>
			<p class="murg-search__hint">Pulse Enter para ver resultados</p>
		</div>
	</div>
</div>
