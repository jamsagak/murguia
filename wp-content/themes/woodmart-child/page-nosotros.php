<?php
/**
 * Template Name: Nosotros
 * Template Post Type: page
 *
 * Página "Sobre Nosotros" — historia, misión, visión, tiendas.
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nosotros · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-about-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-about">

	<!-- Hero -->
	<section class="murg-about__hero">
		<div class="murg-about__hero-inner">
			<div class="murg-eyebrow">Desde 1910</div>
			<h1 class="murg-about__hero-title">Nuestra <em>Historia</em></h1>
		</div>
	</section>

	<!-- Historia -->
	<section class="murg-about__section">
		<div class="murg-about__container">
			<div class="murg-about__text-block">
				<p>La empresa fue fundada en 1910 por Don Manuel Murguía. Inicialmente se denominaba "Joyería La Esmeralda" y aproximadamente en los años 20 se constituyó la sociedad "Zettle – Murguía".</p>
				<p>En 1936 se estableció "M. Murguía S. A." en el Jirón de la Unión, que era el centro comercial y social de Lima.</p>
				<p>En 1956, tras la muerte de Don Manuel Murguía, José Jiménez Casabonne asumió la dirección. Durante este período se importaron marcas europeas prestigiosas como cristales Baccarat, Lalique y Daum; porcelanas Heinrich; perlas Mikimoto y joyería de Italia y Francia.</p>
				<p>En los años 60 se construyó un nuevo local de 5 pisos en el Jirón de la Unión. Alrededor de los años 70, el Centro de Lima comenzó a declinar y la expansión se enfocó en San Isidro, Miraflores y la urbanización Chacarilla.</p>
			</div>
		</div>
	</section>

	<!-- Misión y Visión -->
	<section class="murg-about__section murg-about__section--dark">
		<div class="murg-about__container">
			<div class="murg-about__grid-2">
				<div class="murg-about__card">
					<h2 class="murg-about__card-title">Nuestra Misión</h2>
					<p class="murg-about__card-text">Brindar a nuestros clientes momentos especiales, representados en finas piezas de joyería que representarán sentimientos importantes de su vida.</p>
				</div>
				<div class="murg-about__card">
					<h2 class="murg-about__card-title">Nuestra Visión</h2>
					<p class="murg-about__card-text">Mantenernos como la primera joyería del Perú para nuestros clientes y ser la primera opción para las nuevas generaciones de compradores.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Tiendas -->
	<section class="murg-about__section">
		<div class="murg-about__container">
			<div class="murg-eyebrow" style="text-align:center;">Visítenos</div>
			<h2 class="murg-about__section-title">Nuestras <em>Tiendas</em></h2>
			<div class="murg-about__grid-3">
				<div class="murg-about__store">
					<h3 class="murg-about__store-name">San Isidro</h3>
					<p class="murg-about__store-addr">Av. Pardo y Aliaga 572</p>
					<p class="murg-about__store-tel">+51 719-5359</p>
					<p class="murg-about__store-hours">L–V 10:00am – 7:00pm<br>Sábados 10:00am – 5:00pm</p>
				</div>
				<div class="murg-about__store">
					<h3 class="murg-about__store-name">Miraflores</h3>
					<p class="murg-about__store-addr">Av. La Paz 1198</p>
					<p class="murg-about__store-tel">+01 652-6666</p>
					<p class="murg-about__store-hours">L–V 10:30am – 7:00pm<br>Sábados 10:30am – 5:00pm</p>
				</div>
				<div class="murg-about__store">
					<h3 class="murg-about__store-name">Jockey Plaza</h3>
					<p class="murg-about__store-addr">Av. Javier Prado Este 4200, Surco</p>
					<p class="murg-about__store-tel">+01 279-4393</p>
					<p class="murg-about__store-hours">L–D 11:00am – 9:15pm</p>
				</div>
			</div>
		</div>
	</section>

</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
