<?php
/**
 * Template Name: Contacto
 * Template Post Type: page
 *
 * Página de contacto standalone de Joyería Murguía.
 * Asignar en Páginas > Contacto > Atributos de página > Plantilla: Contacto.
 */
defined( 'ABSPATH' ) || exit;

function murg_ct( $key, $fallback = '' ) {
	return murguia_ajuste( $key, $fallback, 'contacto' );
}

$ct_eyebrow   = murg_ct( 'ct_eyebrow',   'Visite el Atelier' );
$ct_titulo    = murg_ct( 'ct_titulo',    'Visítenos o <em>agende</em> una cita' );
$ct_texto     = murg_ct( 'ct_texto',     'Nuestro atelier está ubicado en el corazón de Miraflores. Le atendemos con cita previa o puede visitarnos durante nuestro horario de atención.' );
$ct_direccion = murg_ct( 'ct_direccion', "Av. Larco 1301\nMiraflores · Lima 18" );
$ct_horario   = murg_ct( 'ct_horario',  'Lun – Sáb · 10:00 – 19:00' );
$ct_telefono  = murg_ct( 'ct_telefono', '' );
$ct_email     = murg_ct( 'ct_email',    '' );
$ct_whatsapp  = murg_ct( 'ct_whatsapp', '' );
$ct_servicios = murg_ct( 'ct_servicios', "Diseño a medida\nRestauración\nGrabado y personalización" );
$ct_serv_sub  = murg_ct( 'ct_serv_sub',  'Presupuesto sin costo' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contacto · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-contact-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<!-- ============================================================
     CONTACTO
     ============================================================ -->
<section class="murg-contact murg-contact--page" id="contacto">
	<div class="murg-contact__grid">

		<!-- Info -->
		<div class="murg-contact__info">
			<div class="murg-eyebrow" style="color:var(--murg-gold-soft)">
				<?php echo esc_html( $ct_eyebrow ); ?>
			</div>
			<h1 class="murg-serif murg-contact__title">
				<?php echo wp_kses( $ct_titulo, [ 'em' => [] ] ); ?>
			</h1>
			<?php if ( $ct_texto ) : ?>
			<p class="murg-contact__text"><?php echo nl2br( esc_html( $ct_texto ) ); ?></p>
			<?php endif; ?>

			<dl class="murg-contact__details">
				<?php if ( $ct_direccion ) : ?>
				<dt>Dirección</dt>
				<dd><?php echo nl2br( esc_html( $ct_direccion ) ); ?></dd>
				<?php endif; ?>

				<?php if ( $ct_horario ) : ?>
				<dt>Horario</dt>
				<dd><?php echo esc_html( $ct_horario ); ?></dd>
				<?php endif; ?>

				<?php if ( $ct_telefono ) :
					$tel_raw = preg_replace( '/\s+/', '', $ct_telefono );
				?>
				<dt>Teléfono</dt>
				<dd><a href="tel:<?php echo esc_attr( $tel_raw ); ?>"><?php echo esc_html( $ct_telefono ); ?></a></dd>
				<?php endif; ?>

				<?php if ( $ct_email ) : ?>
				<dt>Email</dt>
				<dd><a href="mailto:<?php echo esc_attr( $ct_email ); ?>"><?php echo esc_html( $ct_email ); ?></a></dd>
				<?php endif; ?>
			</dl>

			<?php
			$servicios_arr = $ct_servicios
				? array_filter( array_map( 'trim', explode( "\n", $ct_servicios ) ) )
				: [];
			if ( ! empty( $servicios_arr ) ) :
			?>
			<div class="murg-contact__servicios">
				<div class="murg-eyebrow" style="margin-bottom:12px;">Servicios</div>
				<ul>
					<?php foreach ( $servicios_arr as $serv ) : ?>
					<li><?php echo esc_html( $serv ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $ct_serv_sub ) : ?>
				<p class="murg-contact__serv-sub"><?php echo esc_html( $ct_serv_sub ); ?></p>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div><!-- /.murg-contact__info -->

		<!-- Formulario -->
		<div class="murg-contact__form-wrap">
			<form class="murg-form"
			      method="post"
			      action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			      novalidate>
				<?php wp_nonce_field( 'murg_cita', 'murg_nonce' ); ?>
				<input type="hidden" name="action" value="murg_solicitar_cita">

				<div class="murg-field">
					<label for="murg-nombre">Nombre</label>
					<input id="murg-nombre" type="text" name="nombre"
					       placeholder="Su nombre completo" required>
				</div>
				<div class="murg-field">
					<label for="murg-correo">Correo electrónico</label>
					<input id="murg-correo" type="email" name="correo"
					       placeholder="correo@ejemplo.com" required>
				</div>
				<div class="murg-field">
					<label for="murg-telefono">Teléfono</label>
					<input id="murg-telefono" type="tel" name="telefono"
					       placeholder="+51 ___ ___ ___">
				</div>
				<div class="murg-field">
					<label for="murg-interes">Interés</label>
					<select id="murg-interes" name="interes">
						<option>Asesoría general</option>
						<option>Anillo de compromiso</option>
						<option>Diseño a medida</option>
						<option>Restauración</option>
					</select>
				</div>
				<div class="murg-field" style="align-items:start;">
					<label for="murg-mensaje" style="padding-top:6px;">Mensaje</label>
					<textarea id="murg-mensaje" name="mensaje" rows="4"
					          placeholder="Cuéntenos brevemente..."
					          style="resize:none;padding-top:4px;"></textarea>
				</div>

				<div class="murg-form__actions">
					<button type="submit" class="murg-btn">Solicitar Cita</button>
					<?php if ( $ct_whatsapp ) : ?>
					<a href="<?php echo esc_url( $ct_whatsapp ); ?>"
					   class="murg-btn murg-btn--gold"
					   target="_blank" rel="noopener noreferrer"
					   style="display:flex;align-items:center;justify-content:center;">
						<span class="murg-whatsapp-dot" aria-hidden="true"></span>
						WhatsApp
					</a>
					<?php endif; ?>
				</div>
			</form>
		</div><!-- /.murg-contact__form-wrap -->

	</div>
</section>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
