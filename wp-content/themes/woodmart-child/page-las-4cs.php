<?php
/**
 * Template Name: Las 4Cs
 * Template Post Type: page
 *
 * Pagina educativa sobre las 4Cs del diamante.
 * slug esperado: las-4cs
 */
defined( 'ABSPATH' ) || exit;

$img_base = get_stylesheet_directory_uri() . '/assets/img/home/';

$cs = [
	[
		'letter' => 'Cut',
		'titulo' => 'Corte',
		'texto'  => 'El corte determina como la luz interactua con el diamante. Un corte excelente maximiza el brillo, el fuego y la centelleo de la piedra. Es el factor mas importante para la belleza visual del diamante.',
	],
	[
		'letter' => 'Color',
		'titulo' => 'Color',
		'texto'  => 'El color mide la ausencia de tonalidad en un diamante. La escala va de D (incoloro) a Z (amarillo claro). Los diamantes mas valiosos son los que presentan menos color, permitiendo que la luz pase sin obstaculos.',
	],
	[
		'letter' => 'Clarity',
		'titulo' => 'Claridad',
		'texto'  => 'La claridad evalua las inclusiones internas y marcas externas del diamante. La escala va desde FL (sin defectos) hasta I3 (inclusiones visibles). La mayoria de inclusiones son microscopicas y no afectan la belleza.',
	],
	[
		'letter' => 'Carat',
		'titulo' => 'Quilate',
		'texto'  => 'El quilate es la unidad de peso del diamante. Un quilate equivale a 0.2 gramos. El peso influye en el tamano aparente, pero el corte y la montura tambien afectan como se percibe la piedra.',
	],
];
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Las 4Cs del Diamante · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-4cs-page' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-4cs" id="contenido">

	<section class="murg-4cs__hero">
		<div class="murg-4cs__hero-inner" data-reveal>
			<p class="murg-ac-eyebrow">Guia del diamante</p>
			<h1>Las 4Cs del Diamante</h1>
			<p>Cut, Color, Clarity y Carat: los cuatro criterios universales para evaluar la calidad de un diamante. Conocerlos te ayudara a elegir con confianza.</p>
		</div>
	</section>

	<section class="murg-4cs__grid">
		<?php foreach ( $cs as $idx => $c ) : ?>
		<article class="murg-4cs__card" data-reveal>
			<div class="murg-4cs__card-letter"><?php echo esc_html( $c['letter'] ); ?></div>
			<h2 class="murg-4cs__card-title"><?php echo esc_html( $c['titulo'] ); ?></h2>
			<p class="murg-4cs__card-text"><?php echo esc_html( $c['texto'] ); ?></p>
		</article>
		<?php endforeach; ?>
	</section>

	<section class="murg-4cs__cta">
		<div data-reveal>
			<h2>Necesitas ayuda para elegir?</h2>
			<p>Nuestro equipo de gemologos te guiara en la seleccion del diamante perfecto para tu pieza.</p>
			<div class="murg-4cs__actions">
				<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">Agendar cita</a>
				<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( home_url( '/anillos-compromiso/' ) ); ?>">Ver anillos</a>
			</div>
		</div>
	</section>

</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
