<?php
/**
 * Template Part: Navegación principal (Figma v2)
 *
 * Estructura:
 * 1. Barra verde: "DIAMANTES CERTIFICADOS GIA · HRD · IGI"
 * 2. Fila superior: ES.EN (izq) | Logo (centro) | iconos (der)
 * 3. Fila inferior: NOVIOS · CATÁLOGO · Marcas · Alta Joyería · Hogar
 */
$_nav_marca   = murguia_ajuste( 'hp_foot_marca', 'Murguía' );
$_nav_banner  = murguia_ajuste( 'hp_nav_banner', 'DIAMANTES CERTIFICADOS GIA · HRD · IGI' );

$_shop_url    = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop/' );
$_account_url = function_exists( 'wc_get_page_id' ) ? get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) : home_url( '/mi-cuenta/' );
$_cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/carrito/' );
$_cart_count  = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;

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
?>
<nav class="murg-nav" id="murg-nav" role="navigation" aria-label="Navegación principal">

	<!-- ── Barra verde superior ────────────────────────────── -->
	<div class="murg-topbar">
		<span class="murg-topbar__text"><?php echo esc_html( $_nav_banner ); ?></span>
	</div>

	<!-- ── Fila 1: idioma · logo · iconos ──────────────────── -->
	<div class="murg-nav__row murg-nav__row--top">

		<div class="murg-nav__lang"></div>

		<a class="murg-nav__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/Logo-murguia-blanco.png' ); ?>"
			     alt="<?php echo esc_attr( $_nav_marca ); ?>"
			     class="murg-nav__logo-img murg-nav__logo-img--blanco"
			     loading="eager" width="180" height="auto">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/Logo-murguia.png' ); ?>"
			     alt="<?php echo esc_attr( $_nav_marca ); ?>"
			     class="murg-nav__logo-img murg-nav__logo-img--oscuro"
			     loading="eager" width="180" height="auto">
		</a>

		<div class="murg-nav__icons">
			<button type="button"
			        class="murg-nav__icon-btn"
			        id="murg-search-open"
			        aria-haspopup="dialog"
			        aria-controls="murg-search"
			        aria-label="Buscar">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
					<circle cx="11" cy="11" r="7"/><path d="M16 16l5 5"/>
				</svg>
			</button>

			<a href="<?php echo esc_url( $_account_url ); ?>" class="murg-nav__icon-btn" aria-label="Mi cuenta">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
					<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
					<circle cx="12" cy="7" r="4"/>
				</svg>
			</a>

			<a href="<?php echo esc_url( $_cart_url ); ?>" class="murg-nav__icon-btn murg-nav__cart" aria-label="Bolsa">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
					<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
					<line x1="3" y1="6" x2="21" y2="6"/>
					<path d="M16 10a4 4 0 0 1-8 0"/>
				</svg>
				<?php if ( $_cart_count > 0 ) : ?>
				<span class="murg-nav__cart-count"><?php echo (int) $_cart_count; ?></span>
				<?php endif; ?>
			</a>
		</div>

	</div>

	<!-- ── Fila 2: menú principal ──────────────────────────── -->
	<div class="murg-nav__row murg-nav__row--menu">

		<?php
		$_current_url = home_url( $_SERVER['REQUEST_URI'] );
		$_nav_items = [
			[ 'label' => 'Anillos de Compromiso', 'url' => home_url( '/anillos-compromiso/' ), 'drop' => false ],
			[ 'label' => 'Catálogo',              'url' => $_shop_url,                          'drop' => $_cat_links ],
			[ 'label' => 'Marcas',                'url' => home_url( '/shop/?product_cat=marcas' ), 'drop' => $_marca_links ],
			[ 'label' => 'Alta Joyería',          'url' => home_url( '/shop/?product_cat=alta-joyeria' ), 'drop' => false ],
			[ 'label' => 'Tiendas',               'url' => home_url( '/tiendas/' ), 'drop' => false ],
		];
		foreach ( $_nav_items as $_item ) :
			$_is_active = ( rtrim( $_current_url, '/' ) === rtrim( $_item['url'], '/' ) );
			$_active_class = $_is_active ? ' is-active' : '';

			if ( $_item['drop'] && is_array( $_item['drop'] ) ) : ?>
			<div class="murg-nav__item murg-nav__item--drop">
				<a href="<?php echo esc_url( $_item['url'] ); ?>" class="murg-nav__link<?php echo $_active_class; ?>"><?php echo esc_html( $_item['label'] ); ?></a>
				<div class="murg-nav__dropdown">
					<?php foreach ( $_item['drop'] as $_dl ) : ?>
					<a href="<?php echo esc_url( $_dl['url'] ); ?>"><?php echo esc_html( $_dl['label'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
			<?php else : ?>
			<a href="<?php echo esc_url( $_item['url'] ); ?>"
			   class="murg-nav__link<?php echo $_active_class; ?>"><?php echo esc_html( $_item['label'] ); ?></a>
			<?php endif;
		endforeach; ?>

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
