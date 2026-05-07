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
		<a href="#">Bodas</a>
		<a href="#">Atelier</a>
	</div>
	<a class="murg-nav__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<img src="<?php echo esc_url( 'http://murgia.local/wp-content/uploads/2026/04/Logo-murguia-blanco.png' ); ?>"
			alt="<?php echo esc_attr( $_nav_marca ); ?>"
			class="murg-nav__logo-img murg-nav__logo-img--blanco"
			loading="eager"
			width="120"
			height="auto">
		<img src="<?php echo esc_url( 'http://murgia.local/wp-content/uploads/2026/04/Logo-murguia.png' ); ?>"
			alt="<?php echo esc_attr( $_nav_marca ); ?>"
			class="murg-nav__logo-img murg-nav__logo-img--oscuro"
			loading="eager"
			width="120"
			height="auto">
	</a>
	<div class="murg-nav__right">
		<a href="<?php echo esc_url( home_url( '/#contacto' ) ); ?>">Citas</a>
		<?php if ( function_exists( 'wc_get_page_id' ) ) : ?>
			<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">Cuenta</a>
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Buscar</a>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
				Bolsa (<?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : '0'; ?>)
			</a>
		<?php endif; ?>
	</div>
</nav>
