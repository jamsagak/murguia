<?php
/**
 * Template Name: Anillos de Compromiso
 * Template Post Type: page
 *
 * Landing editorial para anillos de compromiso.
 * Contenido editable desde: Ajustes de Diseno > Anillos de Compromiso
 * slug: anillos-compromiso-page | prefijo SCF/ACF: ac_
 */
defined( 'ABSPATH' ) || exit;

function murg_ac( $key, $fallback = '' ) {
	return murguia_ajuste( $key, $fallback, 'anillos-compromiso-page' );
}

function murg_ac_img_url( $image, $fallback = '' ) {
	return is_array( $image ) && ! empty( $image['url'] ) ? $image['url'] : $fallback;
}

function murg_ac_img_alt( $image, $fallback = '' ) {
	if ( is_array( $image ) && ! empty( $image['alt'] ) ) {
		return $image['alt'];
	}
	return $fallback;
}

function murg_ac_link_url( $link, $fallback = '' ) {
	if ( is_array( $link ) && ! empty( $link['url'] ) ) {
		return $link['url'];
	}
	return is_string( $link ) && $link ? $link : $fallback;
}

function murg_ac_link_title( $link, $fallback = '' ) {
	if ( is_array( $link ) && ! empty( $link['title'] ) ) {
		return $link['title'];
	}
	return $fallback;
}

function murg_ac_product_ring_style( $product, $index = 0 ) {
	$text = $product ? $product->get_name() : '';
	if ( $product ) {
		$cats = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'names' ] );
		$tags = wp_get_post_terms( $product->get_id(), 'product_tag', [ 'fields' => 'names' ] );
		$terms = array_merge(
			is_array( $cats ) ? $cats : [],
			is_array( $tags ) ? $tags : []
		);
		$text .= ' ' . implode( ' ', array_filter( $terms ) );
	}

	$text = strtolower( remove_accents( $text ) );
	if ( false !== strpos( $text, 'halo' ) ) {
		return 'halo';
	}
	if ( false !== strpos( $text, 'pave' ) || false !== strpos( $text, 'pav' ) ) {
		return 'pave';
	}
	if ( false !== strpos( $text, 'tres' ) || false !== strpos( $text, 'three' ) || false !== strpos( $text, 'trilog' ) ) {
		return 'tres-piedras';
	}
	if ( false !== strpos( $text, 'medida' ) || false !== strpos( $text, 'personaliz' ) ) {
		return 'a-medida';
	}

	$fallback_styles = [ 'solitario', 'halo', 'pave', 'tres-piedras', 'a-medida' ];
	return $fallback_styles[ $index % count( $fallback_styles ) ];
}

$img_base   = get_stylesheet_directory_uri() . '/assets/img/home/';
$img_upload = content_url( 'uploads/2026/05/' );

$hero_img = murg_ac( 'ac_hero_imagen', [] );
$hero_bg  = murg_ac_img_url( $hero_img, $img_base . 'hero.jpg' );

$hero_title = murg_ac( 'ac_hero_titulo', 'Anillos de compromiso' );
$hero_sub   = murg_ac( 'ac_hero_sub', 'Una pieza creada para pedir una vida juntos, con diamantes certificados y el oficio de Casa Murguia desde 1910.' );
$hero_cta_t = murg_ac( 'ac_hero_cta_texto', 'Agendar cita' );
$hero_cta_u = murg_ac( 'ac_hero_cta_url', home_url( '/contacto/' ) );
$hero_sec_t = murg_ac( 'ac_hero_cta_sec_texto', 'Ver anillos' );
$hero_sec_u = murg_ac( 'ac_hero_cta_sec_url', '#ac-productos' );

$formas_titulo = murg_ac( 'ac_formas_titulo', 'Elige la forma de tu diamante' );
$formas_sub    = murg_ac( 'ac_formas_sub', 'Cortes clasicos y contemporaneos para una pieza hecha a tu medida.' );
$diamond_shapes = [
	[ 'slug' => 'oval',              'ext' => 'webp', 'label' => 'Oval' ],
	[ 'slug' => 'round',             'ext' => 'webp', 'label' => 'Round' ],
	[ 'slug' => 'emerald',           'ext' => 'webp', 'label' => 'Emerald' ],
	[ 'slug' => 'marquise',          'ext' => 'webp', 'label' => 'Marquise' ],
	[ 'slug' => 'radiant',           'ext' => 'webp', 'label' => 'Radiant' ],
	[ 'slug' => 'pear',              'ext' => 'webp', 'label' => 'Pear' ],
	[ 'slug' => 'elongated-cushion', 'ext' => 'webp', 'label' => 'Elongated Cushion' ],
	[ 'slug' => 'cushion',           'ext' => 'webp', 'label' => 'Cushion' ],
	[ 'slug' => 'princess',          'ext' => 'webp', 'label' => 'Princess' ],
	[ 'slug' => 'asscher',           'ext' => 'webp', 'label' => 'Asscher' ],
];
$shapes_dir = get_stylesheet_directory_uri() . '/assets/img/diamond-shapes/';

$prod_title = murg_ac( 'ac_productos_titulo', 'Anillos de compromiso destacados' );
$prod_sub   = murg_ac( 'ac_productos_sub', 'Seleccionamos piezas listas para acompanar una propuesta inolvidable.' );
$prod_qty   = max( 9, min( 12, (int) murg_ac( 'ac_productos_cantidad', 9 ) ) );
$prod_cta_t = murg_ac( 'ac_productos_cta_texto', 'Ver tienda completa' );
$prod_cta_u = murg_ac( 'ac_productos_cta_url', home_url( '/shop/?product_cat=anillos-de-compromiso' ) );

$prod_cat = murg_ac( 'ac_productos_categoria', 0 );
$prod_cat_slug = 'anillos-de-compromiso';
if ( $prod_cat ) {
	$term = is_numeric( $prod_cat ) ? get_term( (int) $prod_cat, 'product_cat' ) : null;
	if ( $term && ! is_wp_error( $term ) ) {
		$prod_cat_slug = $term->slug;
	}
}

$products = [];
if ( function_exists( 'wc_get_products' ) ) {
	$products = wc_get_products( [
		'status'     => 'publish',
		'limit'      => $prod_qty,
		'category'   => [ $prod_cat_slug ],
		'orderby'    => 'menu_order',
		'order'      => 'ASC',
		'visibility' => 'catalog',
	] );
}

$ring_tabs = [
	[ 'label' => 'Todos', 'filter' => 'all' ],
	[ 'label' => 'Solitarios', 'filter' => 'solitario' ],
	[ 'label' => 'Halo', 'filter' => 'halo' ],
	[ 'label' => 'Pave', 'filter' => 'pave' ],
	[ 'label' => 'Tres piedras', 'filter' => 'tres-piedras' ],
	[ 'label' => 'A medida', 'filter' => 'a-medida' ],
];

$style_items = murg_ac( 'ac_estilos_items', [] );
if ( empty( $style_items ) || ! is_array( $style_items ) ) {
	$style_items = [
		[ 'titulo' => 'Solitario', 'texto' => 'Un diamante protagonista, limpio y atemporal.', 'url' => home_url( '/shop/?product_cat=anillos-de-compromiso&estilo=solitario' ) ],
		[ 'titulo' => 'Halo', 'texto' => 'Un centro rodeado de brillo para mayor presencia.', 'url' => home_url( '/shop/?product_cat=anillos-de-compromiso&estilo=halo' ) ],
		[ 'titulo' => 'Pave', 'texto' => 'Diamantes pequenos en el aro para una luz continua.', 'url' => home_url( '/shop/?product_cat=anillos-de-compromiso&estilo=pave' ) ],
		[ 'titulo' => 'Tres piedras', 'texto' => 'Pasado, presente y futuro en una misma pieza.', 'url' => home_url( '/shop/?product_cat=anillos-de-compromiso&estilo=tres-piedras' ) ],
		[ 'titulo' => 'A medida', 'texto' => 'Disenamos contigo una joya irrepetible.', 'url' => home_url( '/contacto/' ) ],
	];
}

$benefits = murg_ac( 'ac_beneficios_items', [] );
if ( empty( $benefits ) || ! is_array( $benefits ) ) {
	$benefits = [
		[ 'titulo' => 'Diamantes certificados', 'texto' => 'Piedras GIA, HRD o IGI seleccionadas con criterio experto.' ],
		[ 'titulo' => 'Oro de 18k', 'texto' => 'Materiales nobles para una pieza que acompana generaciones.' ],
		[ 'titulo' => 'Taller propio', 'texto' => 'Orfebres en Lima cuidan cada detalle del proceso.' ],
		[ 'titulo' => 'Asesoria privada', 'texto' => 'Te guiamos en corte, quilataje, montura y presupuesto.' ],
		[ 'titulo' => 'Garantia Murguia', 'texto' => 'Servicio postventa y acompanamiento de la casa.' ],
		[ 'titulo' => 'Desde 1910', 'texto' => 'Mas de un siglo creando joyas para historias familiares.' ],
	];
}

$hist_img   = murg_ac( 'ac_historia_imagen', [] );
$hist_title = murg_ac( 'ac_historia_titulo', 'Una joya hecha con oficio, no con prisa' );
$hist_text  = murg_ac( 'ac_historia_texto', 'Cada anillo nace en el dialogo entre quien regala, quien lo recibe y el orfebre que convierte esa historia en una pieza de oro y diamante. En Casa Murguia cuidamos la proporcion, la luz y la comodidad para que el anillo se sienta tan personal como el momento.' );
$hist_cta   = murg_ac( 'ac_historia_cta', [] );

$cita_img = murg_ac( 'ac_cita_imagen', [] );
$cita_title = murg_ac( 'ac_cita_titulo', 'Agenda tu visita' );
$cita_text  = murg_ac( 'ac_cita_texto', 'Recibe asesoria privada en boutique o por videollamada para elegir o disenar el anillo ideal.' );
$cita_url   = murg_ac( 'ac_cita_url', home_url( '/contacto/' ) );
$wa_url     = murg_ac( 'ac_whatsapp_url', 'https://wa.me/51114218800' );

$testimonios = murg_ac( 'ac_testimonios_items', [] );
if ( empty( $testimonios ) || ! is_array( $testimonios ) ) {
	$testimonios = [
		[ 'frase' => 'Nos ayudaron a elegir un diamante que tenia sentido para nuestra historia.', 'autor' => 'Cliente Murguia' ],
		[ 'frase' => 'La asesoria fue clara, elegante y sin apuro. Eso hizo toda la diferencia.', 'autor' => 'Novios Murguia' ],
		[ 'frase' => 'El anillo quedo exactamente como lo imaginamos, pero mas fino.', 'autor' => 'Casa Murguia' ],
	];
}

$nl_title = murg_ac( 'ac_newsletter_titulo', '-10% en tu primera compra.' );
$nl_sub   = murg_ac( 'ac_newsletter_sub', 'Recibe novedades de colecciones, guias y beneficios Murguia.' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '-', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-ac-page' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-ac" id="contenido">
	<section class="murg-ac-hero" aria-label="Anillos de compromiso">
		<div class="murg-ac-hero__media">
			<img src="<?php echo esc_url( $hero_bg ); ?>"
			     alt="<?php echo esc_attr( murg_ac_img_alt( $hero_img, 'Anillo de compromiso Murguia' ) ); ?>"
			     loading="eager"
			     fetchpriority="high">
		</div>
	</section>

	<section class="murg-ac-engagement" id="ac-productos">
		<div class="murg-ac-engagement__media" data-reveal>
			<img src="<?php echo esc_url( $img_base . 'novios.jpg' ); ?>" alt="Anillos de compromiso Murguia" loading="lazy">
		</div>
		<div class="murg-ac-engagement__copy" data-reveal>
			<p class="murg-ac-eyebrow">Anillos de compromiso</p>
			<h2>Anillos de<br>compromiso</h2>
			<p>Disenados pieza por pieza en nuestro taller. Diamantes certificados por GIA, HRD e IGI. Acompanamiento personalizado de inicio a fin.</p>
			<div class="murg-ac-actions">
				<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( home_url( '/disena-tu-anillo/' ) ); ?>">Disenar mi anillo</a>
				<a class="murg-ac-link" href="<?php echo esc_url( home_url( '/las-4cs/' ) ); ?>">Conocer las 4Cs</a>
			</div>
			<div class="murg-ac-trust">
				<span>4,500+ parejas</span>
				<span>Certificacion GIA</span>
				<span>Garantia de por vida</span>
			</div>
		</div>
		<div class="murg-ac-style-grid murg-ac-engagement__styles">
			<?php foreach ( array_slice( $style_items, 0, 5 ) as $item ) :
				$item_img = $item['imagen'] ?? [];
				$item_url = $item['url'] ?? home_url( '/shop/?product_cat=anillos-de-compromiso' );
			?>
			<a class="murg-ac-style" href="<?php echo esc_url( $item_url ); ?>" data-reveal>
				<?php if ( murg_ac_img_url( $item_img ) ) : ?>
				<img src="<?php echo esc_url( murg_ac_img_url( $item_img ) ); ?>" alt="<?php echo esc_attr( murg_ac_img_alt( $item_img, $item['titulo'] ?? 'Estilo de anillo' ) ); ?>" loading="lazy">
				<?php endif; ?>
				<span><?php echo esc_html( $item['titulo'] ?? '' ); ?></span>
				<p><?php echo esc_html( $item['texto'] ?? '' ); ?></p>
			</a>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- Formas de diamante -->
	<section class="murg-ac-diamonds" aria-label="Formas de diamante">
		<header class="murg-ac-section-head" data-reveal>
			<p class="murg-ac-eyebrow">Formas del diamante</p>
			<h2><?php echo esc_html( $formas_titulo ); ?></h2>
			<p><?php echo esc_html( $formas_sub ); ?></p>
		</header>
		<div class="murg-ac-diamond-grid" data-reveal>
			<?php foreach ( $diamond_shapes as $shape ) :
				$shape_src = $shapes_dir . $shape['slug'] . '_new.png';
			?>
			<a class="murg-ac-diamond" href="<?php echo esc_url( home_url( '/shop/?product_cat=anillos-de-compromiso&forma=' . $shape['slug'] ) ); ?>">
				<div class="murg-ac-diamond__img">
					<img src="<?php echo esc_url( $shape_src ); ?>"
					     alt="<?php echo esc_attr( $shape['label'] ); ?>"
					     loading="lazy"
					     width="80" height="80">
				</div>
				<span class="murg-ac-diamond__label"><?php echo esc_html( $shape['label'] ); ?></span>
			</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="murg-ac-categories" aria-label="Categorias destacadas">
		<div class="murg-ac-categories__box">
			<header class="murg-ac-section-head" data-reveal>
				<p class="murg-ac-eyebrow">Explora por estilo</p>
				<h2>Anillos para la propuesta</h2>
				<p>Una seleccion de compromiso: piezas listas, diamantes certificados y diseno a medida.</p>
			</header>

			<div class="murg-ac-ring-tabs" role="tablist" aria-label="Estilos de anillos de compromiso" data-reveal>
				<?php foreach ( $ring_tabs as $idx => $tab ) : ?>
				<button class="<?php echo 0 === $idx ? 'is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo 0 === $idx ? 'true' : 'false'; ?>" data-ring-filter="<?php echo esc_attr( $tab['filter'] ); ?>"><?php echo esc_html( $tab['label'] ); ?></button>
				<?php endforeach; ?>
			</div>

			<?php if ( $products ) : ?>
			<div class="murg-ac-ring-showcase">
				<?php foreach ( array_slice( $products, 0, 9 ) as $idx => $product ) :
					$img_id = $product->get_image_id();
					$ring_style = murg_ac_product_ring_style( $product, $idx );
				?>
				<a class="murg-ac-ring-card" href="<?php echo esc_url( $product->get_permalink() ); ?>" data-ring-style="<?php echo esc_attr( $ring_style ); ?>" data-reveal>
					<div class="murg-ac-ring-card__media">
						<?php
						if ( $img_id ) {
							echo wp_get_attachment_image( $img_id, 'large', false, [
								'loading' => $idx === 0 ? 'eager' : 'lazy',
								'alt'     => get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: $product->get_name(),
							] );
						}
						?>
					</div>
					<h3><?php echo esc_html( $product->get_name() ); ?></h3>
					<div class="murg-ac-ring-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
				</a>
				<?php endforeach; ?>
			</div>
			<p class="murg-ac-ring-empty" hidden>No hay piezas visibles para este estilo por ahora. Prueba con Todos o agenda una asesoria para disenar una a medida.</p>
			<?php else : ?>
			<div class="murg-ac-empty" data-reveal>
				<p>La seleccion de anillos de compromiso estara disponible pronto.</p>
			</div>
			<?php endif; ?>

			<div class="murg-ac-center" data-reveal>
				<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( $prod_cta_u ); ?>"><?php echo esc_html( $prod_cta_t ); ?></a>
			</div>
		</div>
	</section>

	<section class="murg-ac-benefits">
		<div class="murg-ac-benefits__inner">
			<header class="murg-ac-section-head murg-ac-section-head--dark" data-reveal>
				<p class="murg-ac-eyebrow">Por que Murguia</p>
				<h2>Un compromiso merece cuidado experto</h2>
			</header>
			<div class="murg-ac-benefit-grid">
				<?php foreach ( array_slice( $benefits, 0, 6 ) as $benefit ) : ?>
				<article class="murg-ac-benefit" data-reveal>
					<div class="murg-ac-benefit__icon" aria-hidden="true">
						<?php echo ! empty( $benefit['icono_svg'] ) ? wp_kses( $benefit['icono_svg'], [ 'svg' => [ 'viewBox' => true, 'xmlns' => true, 'fill' => true, 'stroke' => true ], 'path' => [ 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ], 'circle' => [ 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true ] ] ) : 'M'; ?>
					</div>
					<h3><?php echo esc_html( $benefit['titulo'] ?? '' ); ?></h3>
					<p><?php echo esc_html( $benefit['texto'] ?? '' ); ?></p>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="murg-ac-products">
		<header class="murg-ac-section-head" data-reveal>
			<p class="murg-ac-eyebrow">Productos destacados</p>
			<h2><?php echo esc_html( $prod_title ); ?></h2>
			<p><?php echo esc_html( $prod_sub ); ?></p>
		</header>
		<?php if ( $products ) : ?>
		<div class="murg-ac-product-grid">
			<?php foreach ( array_slice( $products, 0, 4 ) as $idx => $product ) :
				$img_id = $product->get_image_id();
			?>
			<a class="murg-ac-product" href="<?php echo esc_url( $product->get_permalink() ); ?>" data-reveal="scale">
				<div class="murg-ac-product__media">
					<?php
					if ( $img_id ) {
						echo wp_get_attachment_image( $img_id, 'large', false, [
							'loading' => $idx === 0 ? 'eager' : 'lazy',
							'alt'     => get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: $product->get_name(),
						] );
					}
					?>
				</div>
				<h3><?php echo esc_html( $product->get_name() ); ?></h3>
				<div class="murg-ac-product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			</a>
			<?php endforeach; ?>
		</div>
		<?php else : ?>
		<div class="murg-ac-empty" data-reveal>
			<p>La seleccion de anillos de compromiso estara disponible pronto.</p>
		</div>
		<?php endif; ?>
		<div class="murg-ac-center" data-reveal>
			<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( $prod_cta_u ); ?>"><?php echo esc_html( $prod_cta_t ); ?></a>
		</div>
	</section>

	<?php /* Sección murg-ac-story oculta por decisión de diseño
	<section class="murg-ac-story">
		<div class="murg-ac-story__media" data-reveal>
			<img src="<?php echo esc_url( murg_ac_img_url( $hist_img, $img_base . 'statement-bg.jpg' ) ); ?>" alt="<?php echo esc_attr( murg_ac_img_alt( $hist_img, 'Taller Murguia' ) ); ?>" loading="lazy">
		</div>
		<div class="murg-ac-story__copy" data-reveal>
			<p class="murg-ac-eyebrow">Taller Murguia</p>
			<h2><?php echo esc_html( $hist_title ); ?></h2>
			<p><?php echo esc_html( $hist_text ); ?></p>
			<?php if ( murg_ac_link_url( $hist_cta ) ) : ?>
			<a class="murg-ac-link murg-ac-link--light" href="<?php echo esc_url( murg_ac_link_url( $hist_cta ) ); ?>"><?php echo esc_html( murg_ac_link_title( $hist_cta, 'Conocer mas' ) ); ?></a>
			<?php endif; ?>
		</div>
	</section>
	*/ ?>

	<section class="murg-ac-appointment">
		<div class="murg-ac-appointment__media" data-reveal>
			<img src="<?php echo esc_url( murg_ac_img_url( $cita_img, $img_base . 'appointment.jpg' ) ); ?>" alt="<?php echo esc_attr( murg_ac_img_alt( $cita_img, 'Agenda tu visita' ) ); ?>" loading="lazy">
		</div>
		<div class="murg-ac-appointment__copy" data-reveal>
			<p class="murg-ac-eyebrow">Asesoria privada</p>
			<h2><?php echo esc_html( $cita_title ); ?></h2>
			<p><?php echo esc_html( $cita_text ); ?></p>
			<div class="murg-ac-appointment__facts">
				<span>San Isidro - Miraflores - Jockey Plaza</span>
				<span>Lunes a Viernes 10:00 a 19:00</span>
			</div>
			<div class="murg-ac-actions">
				<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( $cita_url ); ?>">Reservar cita</a>
				<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
			</div>
		</div>
	</section>

	<section class="murg-ac-testimonials">
		<header class="murg-ac-section-head" data-reveal>
			<p class="murg-ac-eyebrow">Historias reales</p>
			<h2>Momentos que empiezan con una pieza</h2>
		</header>
		<div class="murg-ac-testimonial-grid">
			<?php foreach ( array_slice( $testimonios, 0, 3 ) as $testimonio ) : ?>
			<article class="murg-ac-testimonial" data-reveal>
				<span aria-hidden="true">&ldquo;</span>
				<p><?php echo esc_html( $testimonio['frase'] ?? '' ); ?></p>
				<cite><?php echo esc_html( $testimonio['autor'] ?? '' ); ?></cite>
			</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="murg-newsletter murg-ac-newsletter" aria-label="Newsletter">
		<div class="murg-newsletter__inner">
			<h2 class="murg-newsletter__title"><?php echo esc_html( $nl_title ); ?></h2>
			<?php if ( $nl_sub ) : ?><p class="murg-newsletter__sub"><?php echo esc_html( $nl_sub ); ?></p><?php endif; ?>
			<form class="murg-newsletter__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'murg_newsletter', 'murg_nl_nonce' ); ?>
				<input type="hidden" name="action" value="murg_newsletter_subscribe">
				<div class="murg-newsletter__field">
					<input type="email" name="email" placeholder="<?php esc_attr_e( 'Tu correo electronico', 'woodmart-child' ); ?>" required>
					<button type="submit"><?php esc_html_e( 'Suscribirme', 'woodmart-child' ); ?></button>
				</div>
			</form>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
