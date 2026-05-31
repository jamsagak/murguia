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
$_uploads = wp_upload_dir();
$_footer_asset_url = trailingslashit( $_uploads['baseurl'] ) . '2026/05/';

$_payment_logos = [
	[ 'file' => 'visa.svg',         'alt' => 'Visa' ],
	[ 'file' => 'master.svg',       'alt' => 'Mastercard' ],
	[ 'file' => 'america.svg',      'alt' => 'American Express' ],
	[ 'file' => 'diners.svg',       'alt' => 'Diners Club' ],
	[ 'file' => 'pagoefectivo.svg', 'alt' => 'PagoEfectivo' ],
];

$_social_links = [
	[ 'file' => 'IG.svg', 'alt' => 'Instagram', 'url' => 'https://www.instagram.com/joyeriamurguia/' ],
	[ 'file' => 'fb.svg', 'alt' => 'Facebook',  'url' => 'https://web.facebook.com/JoyeriaMurguia' ],
	[ 'file' => 'yt.svg', 'alt' => 'YouTube',   'url' => 'https://www.youtube.com/channel/UCw0lqbvcCZ4BbMd57wj3XPg' ],
];

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
				<li><a href="<?php echo esc_url( home_url( '/tiendas/' ) ); ?>">Tiendas</a></li>
				<li><a href="<?php echo esc_url( home_url( '/politica-de-privacidad/' ) ); ?>">Política de privacidad</a></li>
				<li><a href="<?php echo esc_url( home_url( '/politica-de-cookies/' ) ); ?>">Política de cookies</a></li>
				<li><a href="<?php echo esc_url( home_url( '/terminos-y-condiciones/' ) ); ?>">Términos y condiciones</a></li>
				<li><a href="<?php echo esc_url( home_url( '/recojos-y-envios/' ) ); ?>">Recojos y envíos</a></li>
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
			<h5>Medios de Pago</h5>
			<div class="murg-footer__payments" aria-label="Medios de pago">
				<?php foreach ( $_payment_logos as $logo ) : ?>
					<img src="<?php echo esc_url( $_footer_asset_url . $logo['file'] ); ?>"
					     alt="<?php echo esc_attr( $logo['alt'] ); ?>"
					     loading="eager">
				<?php endforeach; ?>
			</div>
			<h5 class="murg-footer__social-title">Síguenos</h5>
			<div class="murg-footer__social" aria-label="Redes sociales">
				<?php foreach ( $_social_links as $link ) : ?>
					<a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $link['alt'] ); ?>">
						<img src="<?php echo esc_url( $_footer_asset_url . $link['file'] ); ?>"
						     alt=""
						     loading="eager">
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="murg-footer__bottom">
		<div><?php echo esc_html( $_foot_copyright ); ?></div>
		<div>Lima · Peru</div>
		<div>
			<a href="<?php echo esc_url( home_url( '/politica-de-privacidad/' ) ); ?>" style="color:inherit;text-decoration:none;">Privacidad</a>
			·
			<a href="<?php echo esc_url( home_url( '/terminos-y-condiciones/' ) ); ?>" style="color:inherit;text-decoration:none;">Términos</a>
		</div>
	</div>
</footer>
