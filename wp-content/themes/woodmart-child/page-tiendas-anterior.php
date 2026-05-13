<?php
/**
 * Template Name: Tiendas
 * Template Post Type: page
 *
 * Pagina de tiendas / locales de Joyeria Murguia.
 * slug esperado: tiendas
 */
defined( 'ABSPATH' ) || exit;

$img_base = get_stylesheet_directory_uri() . '/assets/img/home/';

$tiendas = [
	[
		'nombre'    => 'San Isidro',
		'direccion' => 'Av. Conquistadores 600, San Isidro',
		'horario'   => 'Lunes a Sabado 10:00 - 19:00',
		'telefono'  => '(01) 421-8800',
		'maps'      => 'https://maps.google.com/?q=Joyeria+Murguia+San+Isidro',
	],
	[
		'nombre'    => 'Miraflores',
		'direccion' => 'Av. La Paz 550, Miraflores',
		'horario'   => 'Lunes a Sabado 10:00 - 19:00',
		'telefono'  => '(01) 421-8800',
		'maps'      => 'https://maps.google.com/?q=Joyeria+Murguia+Miraflores',
	],
	[
		'nombre'    => 'Jockey Plaza',
		'direccion' => 'Jockey Plaza, Nivel 1',
		'horario'   => 'Lunes a Domingo 11:00 - 21:00',
		'telefono'  => '(01) 421-8800',
		'maps'      => 'https://maps.google.com/?q=Joyeria+Murguia+Jockey+Plaza',
	],
];
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tiendas · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-tiendas-page' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-tiendas" id="contenido">

	<section class="murg-tiendas__hero">
		<div class="murg-tiendas__hero-inner" data-reveal>
			<p class="murg-ac-eyebrow">Visitanos</p>
			<h1>Nuestras Tiendas</h1>
			<p>Conoce nuestras boutiques en Lima. Agenda una visita para recibir asesoria personalizada.</p>
		</div>
	</section>

	<section class="murg-tiendas__grid">
		<?php foreach ( $tiendas as $tienda ) : ?>
		<article class="murg-tiendas__card" data-reveal>
			<h2 class="murg-tiendas__card-name"><?php echo esc_html( $tienda['nombre'] ); ?></h2>
			<p class="murg-tiendas__card-detail"><?php echo esc_html( $tienda['direccion'] ); ?></p>
			<p class="murg-tiendas__card-detail"><?php echo esc_html( $tienda['horario'] ); ?></p>
			<p class="murg-tiendas__card-detail"><?php echo esc_html( $tienda['telefono'] ); ?></p>
			<a class="murg-tiendas__card-link" href="<?php echo esc_url( $tienda['maps'] ); ?>" target="_blank" rel="noopener noreferrer">Ver en Google Maps</a>
		</article>
		<?php endforeach; ?>
	</section>

	<section class="murg-tiendas__cta">
		<div data-reveal>
			<h2>Agenda tu visita</h2>
			<p>Recibe atencion personalizada de nuestro equipo de asesores.</p>
			<div class="murg-tiendas__actions">
				<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">Contactar</a>
				<a class="murg-btn murg-btn--dark" href="https://wa.me/51114218800" target="_blank" rel="noopener noreferrer">WhatsApp</a>
			</div>
		</div>
	</section>

</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
