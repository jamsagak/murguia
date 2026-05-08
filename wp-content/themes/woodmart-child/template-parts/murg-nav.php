<?php
/**
 * Template Part: Navegación principal
 * Reads brand name from homepage ajustes (global branding values).
 */
$_nav_marca    = murguia_ajuste( 'hp_foot_marca',   'Murguía' );
$_nav_logo_sub = murguia_ajuste( 'hp_nav_logo_sub', 'Joyería · Lima' );
?><nav class="murg-nav" id="murg-nav" role="navigation" aria-label="Navegación principal">
	<div class="murg-nav__left">
		<?php if ( function_exists( 'wc_get_page_id' ) ) : ?>
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Colecciones</a>
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Joyería</a>
		<?php else : ?>
			<a href="#">Colecciones</a>
			<a href="#">Joyería</a>
		<?php endif; ?>
		<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Bodas</a>
		<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Atelier</a>
	</div>
	<a class="murg-nav__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/Logo-murguia-blanco.png' ); ?>"
			alt="<?php echo esc_attr( $_nav_marca ); ?>"
			class="murg-nav__logo-img murg-nav__logo-img--blanco"
			loading="eager"
			width="120"
			height="auto">
		<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/Logo-murguia.png' ); ?>"
			alt="<?php echo esc_attr( $_nav_marca ); ?>"
			class="murg-nav__logo-img murg-nav__logo-img--oscuro"
			loading="eager"
			width="120"
			height="auto">
	</a>
	<div class="murg-nav__right">
		<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Citas</a>
		<?php if ( function_exists( 'wc_get_page_id' ) ) : ?>
			<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">Cuenta</a>
			<button type="button"
			        class="murg-nav__search-trigger"
			        id="murg-search-open"
			        aria-haspopup="dialog"
			        aria-controls="murg-search"
			        aria-label="Abrir buscador">Buscar</button>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
				Bolsa (<?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : '0'; ?>)
			</a>
		<?php endif; ?>
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
