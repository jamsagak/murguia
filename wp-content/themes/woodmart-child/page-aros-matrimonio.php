<?php
/**
 * Template Name: Aros de Matrimonio
 *
 * Landing consultiva para diseñar aros de matrimonio.
 */
defined( 'ABSPATH' ) || exit;

$wa_url = murguia_ajuste( 'ac_whatsapp_url', 'https://wa.me/51114218800', 'anillos-compromiso-page' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-wedding-bands-page' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-design-flow murg-design-flow--bands">
	<section class="murg-design-flow__hero">
		<div class="murg-design-flow__inner" data-reveal>
			<p class="murg-eyebrow">Aros de matrimonio</p>
			<h1>Diseña un aro para todos los días de la historia.</h1>
			<p>Elige modelo, metal, talla y grabado con asesoría personalizada. Creamos una propuesta a medida para cada pareja.</p>
			<p class="murg-design-flow__note">La propuesta se confirma por cotización privada. No hay precio final automático porque cada aro depende del metal, talla, ancho y grabado.</p>
			<div class="murg-design-flow__actions">
				<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( home_url( '/disena-tu-aro/' ) ); ?>">Diseña tu aro</a>
				<a class="murg-btn murg-btn--ghost" href="<?php echo esc_url( home_url( '/shop/?product_cat=aros-de-matrimonio' ) ); ?>">Ver catálogo</a>
			</div>
		</div>
	</section>

	<section class="murg-design-config" aria-label="Opciones para aros">
		<div class="murg-design-config__grid murg-design-config__grid--four">
			<div class="murg-design-config__block" data-reveal>
				<span class="murg-design-config__step">01</span>
				<h2>Modelo</h2>
				<p>Clásico, media caña, plano, comfort fit o diseño personalizado.</p>
			</div>
			<div class="murg-design-config__block" data-reveal>
				<span class="murg-design-config__step">02</span>
				<h2>Metal</h2>
				<p>Oro amarillo, blanco, rosado o combinaciones especiales.</p>
			</div>
			<div class="murg-design-config__block" data-reveal>
				<span class="murg-design-config__step">03</span>
				<h2>Talla</h2>
				<p>Validamos medida y comodidad antes de confirmar la pieza final.</p>
			</div>
			<div class="murg-design-config__block" data-reveal>
				<span class="murg-design-config__step">04</span>
				<h2>Grabado</h2>
				<p>Iniciales, fechas o mensajes breves para una pieza personal.</p>
			</div>
		</div>
	</section>

	<section class="murg-design-flow__cta" data-reveal>
		<h2>Comienza con una asesoría</h2>
		<p>Cuéntanos qué estilo buscan y prepararemos una propuesta para ambos aros.</p>
		<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">Cotizar por WhatsApp</a>
	</section>
</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
