<?php
/**
 * Template Name: Libro de Reclamaciones
 * Template Post Type: page
 *
 * Formulario de reclamaciones virtuales — Ley peruana.
 */
defined( 'ABSPATH' ) || exit;

$departamentos = [
	'Amazonas','Áncash','Apurímac','Arequipa','Ayacucho','Cajamarca','Callao',
	'Cusco','Huancavelica','Huánuco','Ica','Junín','La Libertad','Lambayeque',
	'Lima','Loreto','Madre de Dios','Moquegua','Pasco','Piura','Puno',
	'San Martín','Tacna','Tumbes','Ucayali',
];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Libro de Reclamaciones · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-legal-page murg-reclamos-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-legal">
	<div class="murg-legal__container">
		<header class="murg-legal__header">
			<div class="murg-eyebrow"><?php bloginfo( 'name' ); ?></div>
			<div class="murg-reclamos__icon" aria-hidden="true">
				<svg width="180" height="50" viewBox="0 0 180 50" fill="none" class="murg-reclamos-regulatory-logo">
					<rect width="180" height="50" rx="4" fill="#0c2340" stroke="#C9A84C" stroke-width="1.5"/>
					<g transform="translate(12, 13)" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
						<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" fill="#C9A84C"/>
						<path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" fill="#C9A84C"/>
					</g>
					<text x="46" y="21" fill="white" font-family="'Inter', sans-serif" font-size="9" font-weight="600" letter-spacing="1">LIBRO DE</text>
					<text x="46" y="34" fill="white" font-family="'Inter', sans-serif" font-size="11" font-weight="800" letter-spacing="0.5">RECLAMACIONES</text>
					<text x="46" y="43" fill="#C9A84C" font-family="'Inter', sans-serif" font-size="7" font-weight="700" letter-spacing="0.5">VIRTUAL</text>
				</svg>
			</div>
			<h1 class="murg-legal__title">Libro de Reclamaciones</h1>
		</header>

		<form class="murg-reclamos" id="murg-reclamos-form" method="post">
			<?php wp_nonce_field( 'murg_reclamo', 'murg_reclamo_nonce' ); ?>

			<!-- 1. Identificación del consumidor -->
			<fieldset class="murg-reclamos__section">
				<legend class="murg-reclamos__legend">1. Identificación del Consumidor</legend>
				<div class="murg-reclamos__grid">
					<div class="murg-reclamos__field">
						<label for="rc-nombres">Nombre(s) *</label>
						<input type="text" id="rc-nombres" name="rc_nombres" required>
					</div>
					<div class="murg-reclamos__field">
						<label for="rc-apellidos">Apellido(s) *</label>
						<input type="text" id="rc-apellidos" name="rc_apellidos" required>
					</div>
					<div class="murg-reclamos__field">
						<label for="rc-email">Correo electrónico *</label>
						<input type="email" id="rc-email" name="rc_email" required>
					</div>
					<div class="murg-reclamos__field">
						<label for="rc-telefono">Teléfono *</label>
						<input type="tel" id="rc-telefono" name="rc_telefono" required>
					</div>
					<div class="murg-reclamos__field murg-reclamos__field--full">
						<label for="rc-direccion">Dirección</label>
						<input type="text" id="rc-direccion" name="rc_direccion">
					</div>
					<div class="murg-reclamos__field">
						<label for="rc-distrito">Distrito</label>
						<input type="text" id="rc-distrito" name="rc_distrito">
					</div>
					<div class="murg-reclamos__field">
						<label for="rc-provincia">Provincia</label>
						<input type="text" id="rc-provincia" name="rc_provincia">
					</div>
					<div class="murg-reclamos__field">
						<label for="rc-departamento">Departamento</label>
						<select id="rc-departamento" name="rc_departamento">
							<option value="">Seleccionar</option>
							<?php foreach ( $departamentos as $dep ) : ?>
							<option value="<?php echo esc_attr( $dep ); ?>"><?php echo esc_html( $dep ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="murg-reclamos__field">
						<label for="rc-tipo-doc">Tipo de documento</label>
						<select id="rc-tipo-doc" name="rc_tipo_doc">
							<option value="dni">D.N.I.</option>
							<option value="ce">C.E.</option>
						</select>
					</div>
					<div class="murg-reclamos__field">
						<label for="rc-num-doc">Número de documento *</label>
						<input type="text" id="rc-num-doc" name="rc_num_doc" required>
					</div>
				</div>
			</fieldset>

			<!-- 2. Identificación del bien contratado -->
			<fieldset class="murg-reclamos__section">
				<legend class="murg-reclamos__legend">2. Identificación del Bien Contratado</legend>
				<div class="murg-reclamos__grid">
					<div class="murg-reclamos__field">
						<label for="rc-tipo-bien">Tipo de bien</label>
						<select id="rc-tipo-bien" name="rc_tipo_bien">
							<option value="producto">Producto</option>
							<option value="servicio">Servicio</option>
						</select>
					</div>
					<div class="murg-reclamos__field">
						<label for="rc-num-pedido">Número del pedido</label>
						<input type="text" id="rc-num-pedido" name="rc_num_pedido">
					</div>
					<div class="murg-reclamos__field">
						<label for="rc-monto">Monto total del pedido</label>
						<input type="text" id="rc-monto" name="rc_monto">
					</div>
					<div class="murg-reclamos__field murg-reclamos__field--full">
						<label for="rc-descripcion-bien">Descripción del bien contratado *</label>
						<textarea id="rc-descripcion-bien" name="rc_descripcion_bien" rows="3" required></textarea>
					</div>
				</div>
			</fieldset>

			<!-- 3. Detalle de la reclamación -->
			<fieldset class="murg-reclamos__section">
				<legend class="murg-reclamos__legend">3. Detalle de la Reclamación</legend>
				<div class="murg-reclamos__grid">
					<div class="murg-reclamos__field murg-reclamos__field--full">
						<label>Tipo</label>
						<div class="murg-reclamos__radios">
							<label><input type="radio" name="rc_tipo" value="reclamo" checked> <strong>Reclamo:</strong> Disconformidad relacionada a los productos o servicios</label>
							<label><input type="radio" name="rc_tipo" value="queja"> <strong>Queja:</strong> Disconformidad no relacionada o malestar respecto a la atención al público</label>
						</div>
					</div>
					<div class="murg-reclamos__field murg-reclamos__field--full">
						<label for="rc-detalle">Descripción de la reclamación *</label>
						<textarea id="rc-detalle" name="rc_detalle" rows="5" required></textarea>
					</div>
					<div class="murg-reclamos__field murg-reclamos__field--full">
						<label for="rc-pedido-consumidor">Pedido del consumidor</label>
						<textarea id="rc-pedido-consumidor" name="rc_pedido_consumidor" rows="3"></textarea>
					</div>
				</div>
			</fieldset>

			<div class="murg-reclamos__footer">
				<label class="murg-reclamos__check">
					<input type="checkbox" name="rc_privacidad" required>
					He leído y acepto la <a href="<?php echo esc_url( home_url( '/politica-de-privacidad/' ) ); ?>" target="_blank">Política de Privacidad</a>
				</label>
				<button type="submit" class="murg-reclamos__submit">Enviar Reclamación</button>
			</div>
		</form>
	</div>
</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
