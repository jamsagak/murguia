<?php
/**
 * Template Part: Footer
 * Reads footer content from homepage ajustes (global branding values).
 */
$_foot_marca     = murguia_ajuste( 'hp_foot_marca',     'Murguía' );
$_foot_marca_sub = murguia_ajuste( 'hp_foot_marca_sub', 'Joyería · Lima · MCMLXII' );
$_foot_tagline   = murguia_ajuste( 'hp_foot_tagline',   'Tres generaciones de orfebres dedicados al diseño y manufactura de piezas únicas en oro y plata. Hecho en Perú.' );
$_foot_copyright = murguia_ajuste( 'hp_foot_copyright', '© MMXXVI Joyería Murguía S.A.C.' );

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
		<div>
			<div class="murg-footer__brand">
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/Logo-murguia-blanco.png' ); ?>"
					alt="<?php echo esc_attr( $_foot_marca ); ?>"
					class="murg-footer__brand-img"
					loading="lazy"
					width="140"
					height="auto">
			</div>
			<div class="murg-footer__brand-sub"><?php echo esc_html( $_foot_marca_sub ); ?></div>
			<p class="murg-footer__tagline"><?php echo wp_kses_post( $_foot_tagline ); ?></p>

			<?php if ( ! empty( $_foot_redes ) ) : ?>
			<div class="murg-footer__redes" style="margin-top:24px; display:flex; gap:20px;">
				<?php foreach ( $_foot_redes as $red ) : ?>
					<a href="<?php echo esc_url( $red['url'] ); ?>"
					   target="_blank" rel="noopener noreferrer"
					   style="color:rgba(245,240,232,.7); font-size:11px; letter-spacing:.2em; text-transform:uppercase; text-decoration:none; transition:color .3s;"
					   onmouseover="this.style.color='var(--murg-gold)'"
					   onmouseout="this.style.color='rgba(245,240,232,.7)'">
						<?php echo esc_html( $red['nombre'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>

		<div class="murg-footer__col">
			<h5>Tienda</h5>
			<ul>
				<li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Anillos</a></li>
				<li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Collares</a></li>
				<li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Aretes</a></li>
				<li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Brazaletes</a></li>
				<li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Compromiso</a></li>
			</ul>
		</div>

		<div class="murg-footer__col">
			<h5>Casa</h5>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Historia</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Atelier</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Sostenibilidad</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Prensa</a></li>
			</ul>
		</div>

		<div class="murg-footer__col">
			<h5>Servicio</h5>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Citas</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Envíos</a></li>
				<li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Cuidado</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Garantía</a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Contacto</a></li>
			</ul>
		</div>
	</div>

	<div class="murg-footer__bottom">
		<div><?php echo esc_html( $_foot_copyright ); ?></div>
		<div>Lima · Perú</div>
		<div>
			<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color:inherit;text-decoration:none;">Privacidad</a>
			·
			<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" style="color:inherit;text-decoration:none;">Términos</a>
		</div>
	</div>
</footer>
