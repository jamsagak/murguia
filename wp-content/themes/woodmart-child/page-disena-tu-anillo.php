<?php
/**
 * Template Name: Diseña tu anillo
 *
 * Landing consultiva para configurar anillos de compromiso bajo pedido.
 */
defined( 'ABSPATH' ) || exit;

$wa_url = murguia_ajuste( 'ac_whatsapp_url', 'https://wa.me/51114218800', 'anillos-compromiso-page' );

$shape_dir = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/diamond-shapes/';
$shapes = [
	[ 'label' => 'Redondo',   'img' => 'round_new.png' ],
	[ 'label' => 'Oval',      'img' => 'oval_new.png' ],
	[ 'label' => 'Esmeralda', 'img' => 'emerald_new.png' ],
	[ 'label' => 'Cojín',     'img' => 'cushion_new.png' ],
	[ 'label' => 'Pera',      'img' => 'pear_new.png' ],
	[ 'label' => 'Princesa',  'img' => 'princess_new.png' ],
];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-design-ring-page' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-design-flow">
	<section class="murg-design-flow__hero">
		<div class="murg-design-flow__inner" data-reveal>
			<p class="murg-eyebrow">Diseña tu anillo</p>
			<h1>Un anillo creado para una historia única.</h1>
			<p>Elige modelo, metal y diamante con asesoría privada de Murguía. Nuestro equipo prepara una cotización personalizada según las características elegidas.</p>
			<p class="murg-design-flow__note">Este flujo no muestra precio final en línea. Cada selección se cotiza de forma privada según disponibilidad de diamante, metal y taller.</p>
			<div class="murg-design-flow__actions">
				<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">Solicitar cotización</a>
				<a class="murg-btn murg-btn--ghost" href="<?php echo esc_url( home_url( '/las-4cs/' ) ); ?>">Conoce Las 4Cs</a>
			</div>
		</div>
	</section>

	<section class="murg-design-config" aria-label="Opciones de diseño">
		<div class="murg-design-config__grid">
			<div class="murg-design-config__block" data-reveal>
				<span class="murg-design-config__step">01</span>
				<h2>Forma del diamante</h2>
				<div class="murg-design-shapes">
					<?php foreach ( $shapes as $shape ) : ?>
					<div class="murg-design-shape">
						<img src="<?php echo esc_url( $shape_dir . $shape['img'] ); ?>" alt="<?php echo esc_attr( $shape['label'] ); ?>" loading="lazy">
						<span><?php echo esc_html( $shape['label'] ); ?></span>
					</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="murg-design-config__block" data-reveal>
				<span class="murg-design-config__step">02</span>
				<h2>Metal y acabado</h2>
				<div class="murg-design-options">
					<span>Oro amarillo</span>
					<span>Oro blanco</span>
					<span>Oro rosado</span>
					<span>Platino</span>
				</div>
			</div>

			<div class="murg-design-config__block" data-reveal>
				<span class="murg-design-config__step">03</span>
				<h2>Diamante y origen</h2>
				<p>Selecciona un diamante natural o de laboratorio. La cotización se define según talla, claridad, color, corte y disponibilidad.</p>
			</div>
		</div>
	</section>

	<section class="murg-design-flow__cta" data-reveal>
		<h2>Agenda una asesoría privada</h2>
		<p>Te acompañamos desde la elección del diseño hasta la entrega de la pieza final.</p>
		<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">Hablar por WhatsApp</a>
	</section>
</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
