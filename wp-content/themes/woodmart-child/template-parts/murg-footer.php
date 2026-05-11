<?php
/**
 * Template Part: Footer
 * Reads footer content from homepage ajustes (global branding values).
 */
$_foot_marca     = murguia_ajuste( 'hp_foot_marca',     'Murguia' );
$_foot_marca_sub = murguia_ajuste( 'hp_foot_marca_sub', 'Joyeria · Lima · Desde 1910' );
$_foot_tagline   = murguia_ajuste( 'hp_foot_tagline',   '<strong>Desde 1910</strong> brindando momentos especiales representados en piezas unicas que adquieren sentimiento y valor en sus vidas.' );
$_foot_copyright = murguia_ajuste( 'hp_foot_copyright', '© Novios Murguia S.A.C.' );

$_shop_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop/' );

$_foot_redes = [];
if ( function_exists( 'have_rows' ) && have_rows( 'hp_foot_redes', murguia_ajuste_id() ) ) {
	while ( have_rows( 'hp_foot_redes', murguia_ajuste_id() ) ) {
		the_row();
		$_foot_redes[] = [
			'nombre' => get_sub_field( 'red_nombre' ) ?: '',
			'url'    => get_sub_field( 'red_url' )    ?: '#',
		];
	}
}
?>
<footer class="murg-footer" role="contentinfo">
	<div class="murg-footer__grid">
		<div class="murg-footer__intro">
			<div class="murg-footer__brand">
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/Logo-murguia.png' ); ?>"
					alt="<?php echo esc_attr( $_foot_marca ); ?>"
					class="murg-footer__brand-img"
					loading="lazy"
					width="207"
					height="98">
			</div>
			<div class="murg-footer__brand-sub"><?php echo esc_html( $_foot_marca_sub ); ?></div>
			<p class="murg-footer__tagline"><?php echo wp_kses_post( $_foot_tagline ); ?></p>
		</div>

		<div class="murg-footer__col">
			<h5>Nuestra Empresa</h5>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/nosotros/' ) ); ?>">Nosotros</a></li>
				<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Politica de privacidad</a></li>
				<li><a href="<?php echo esc_url( home_url( '/politica-de-cookies/' ) ); ?>">Politica de cookies</a></li>
				<li><a href="<?php echo esc_url( home_url( '/terminos-y-condiciones/' ) ); ?>">Terminos y condiciones</a></li>
				<li><a href="<?php echo esc_url( home_url( '/recojos-y-envios/' ) ); ?>">Recojos y envios</a></li>
				<li><a href="<?php echo esc_url( home_url( '/libro-de-reclamaciones/' ) ); ?>">Libro de reclamaciones</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">Contacte con nosotros</a></li>
				<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Blog</a></li>
			</ul>
		</div>

		<div class="murg-footer__col">
			<h5>Tiendas</h5>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">San Isidro</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">Miraflores</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">Surco · Jockey Plaza</a></li>
			</ul>
			<p class="murg-footer__legal">Novios Murguia S.A.C<br>20605052194</p>
		</div>

		<div class="murg-footer__col">
			<h5>Servicio</h5>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">Citas</a></li>
				<li><a href="<?php echo esc_url( home_url( '/recojos-y-envios/' ) ); ?>">Envios</a></li>
				<li><a href="<?php echo esc_url( $_shop_url ); ?>">Cuidado</a></li>
				<li><a href="<?php echo esc_url( home_url( '/garantia/' ) ); ?>">Garantia</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">Contacto</a></li>
			</ul>
			<h5 class="murg-footer__social-title">Siguenos</h5>
			<div class="murg-footer__redes">
				<?php if ( ! empty( $_foot_redes ) ) : ?>
					<?php foreach ( $_foot_redes as $red ) : ?>
						<a href="<?php echo esc_url( $red['url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $red['nombre'] ); ?>
						</a>
					<?php endforeach; ?>
				<?php else : ?>
					<a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer">Instagram</a>
					<a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer">Facebook</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="murg-footer__bottom">
		<div><?php echo esc_html( $_foot_copyright ); ?></div>
		<div>Lima · Peru</div>
		<div>
			<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color:inherit;text-decoration:none;">Privacidad</a>
			·
			<a href="<?php echo esc_url( home_url( '/terminos-y-condiciones/' ) ); ?>" style="color:inherit;text-decoration:none;">Terminos</a>
		</div>
	</div>
</footer>
