<?php
/**
 * Template Name: Nosotros
 * Template Post Type: page
 *
 * Página institucional con la historia, misión y visión de Joyería Murguía.
 */
defined( 'ABSPATH' ) || exit;

$about_img_base = home_url( '/img/cms/nosotros/' );

$history_blocks = [
	[
		'image'   => $about_img_base . 'new-nosotros-2.jpg',
		'alt'     => 'Zettel y Murguía en Jirón de la Unión',
		'caption' => 'Zettel y Murguía (Jirón de la Unión)',
		'copy'    => [
			'En el año 1910, hace ya más de 100 años, fue fundada la Joyería por Don Manuel Murguía.',
			'Inicialmente se llamó Joyería La Esmeralda y luego alrededor de los años 20 se creó la sociedad Zettle – Murguía. En 1936, en el Jirón de la Unión, que era ya, para ese entonces, el centro del comercio y vida social de Lima, fue creada M. Murguía S. A.',
		],
	],
	[
		'image'   => $about_img_base . 'new-nosotros-3.jpg',
		'alt'     => 'José Jiménez Casabone',
		'caption' => 'José Jiménez Casabone',
		'copy'    => [
			'En el año 1956 fallece Don Manuel Murguía y la joyería queda a cargo del señor José Jiménez Casabonne. En esta etapa se importan desde Europa marcas tan importantes como cristales Baccarat, Lalique, Daum; porcelanas Heinrich, las famosas perlas Mikimoto y joyería de Italia y Francia.',
			'Es tal el éxito que en los años 60 se construye un nuevo local de 5 pisos en el Jirón de la Unión. Alrededor de los años 70 el Centro de Lima comenzó a declinar y el auge empezó en los nuevos distritos de San Isidro y Miraflores. Joyería Murguía abrió 3 nuevas tiendas; 2 de ellas en los distritos mencionados y otra en la urbanización Chacarilla.',
			'Actualmente Joyería Murguía es reconocida en Lima, no solo por sus años de trayectoria, sino también por estar a la vanguardia con sus novedosos diseños en joyería, relojes y artículos para regalo.',
		],
	],
];

$values = [
	[
		'title' => 'Nuestra Misión',
		'image' => $about_img_base . 'new-nosotros-4.jpg',
		'alt'   => 'Misión de Joyería Murguía',
		'copy'  => 'Brindar a nuestros clientes momentos especiales, representados en finas piezas de joyería que representarán sentimientos importantes de su vida.',
	],
	[
		'title' => 'Nuestra Visión',
		'image' => $about_img_base . 'new-nosotros-5.jpg',
		'alt'   => 'Visión de Joyería Murguía',
		'copy'  => 'Mantenernos como la primera joyería del Perú para nuestros clientes y ser la primera opción para las nuevas generaciones de compradores.',
	],
];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-about-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-about">

	<section class="murg-about-hero" aria-label="Nosotros">
		<img src="<?php echo esc_url( $about_img_base . 'new-nosotros-1.jpg' ); ?>" alt="Joyería Murguía" class="murg-about-hero__img" fetchpriority="high">
	</section>

	<section class="murg-about-section">
		<div class="murg-about-container">
			<header class="murg-about-heading">
				<p class="murg-about-heading__label">Nuestra historia</p>
			</header>

			<div class="murg-about-history">
				<?php foreach ( $history_blocks as $index => $block ) : ?>
					<article class="murg-about-history__item <?php echo 1 === $index ? 'murg-about-history__item--reverse' : ''; ?>">
						<figure class="murg-about-history__figure">
							<img src="<?php echo esc_url( $block['image'] ); ?>" alt="<?php echo esc_attr( $block['alt'] ); ?>" loading="lazy">
							<figcaption><?php echo esc_html( $block['caption'] ); ?></figcaption>
						</figure>
						<div class="murg-about-history__copy">
							<?php foreach ( $block['copy'] as $paragraph ) : ?>
								<p><?php echo esc_html( $paragraph ); ?></p>
							<?php endforeach; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php foreach ( $values as $value ) : ?>
		<section class="murg-about-section murg-about-section--compact">
			<div class="murg-about-container">
				<header class="murg-about-heading">
					<p class="murg-about-heading__label"><?php echo esc_html( $value['title'] ); ?></p>
				</header>
				<article class="murg-about-value">
					<figure class="murg-about-value__figure">
						<img src="<?php echo esc_url( $value['image'] ); ?>" alt="<?php echo esc_attr( $value['alt'] ); ?>" loading="lazy">
					</figure>
					<div class="murg-about-value__copy">
						<p><?php echo esc_html( $value['copy'] ); ?></p>
					</div>
				</article>
			</div>
		</section>
	<?php endforeach; ?>

</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
