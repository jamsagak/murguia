<?php
/**
 * Front Page — Joyería Murguía (v2 · Figma Joyeria Murquia)
 *
 * Contenido editable via CPT murguia_ajustes (slug: pagina-de-inicio).
 * Función de acceso: murguia_ajuste( 'campo', 'fallback' )
 */

function murg_f( $key, $fallback = '' ) {
	return murguia_ajuste( $key, $fallback );
}

$img_base = get_stylesheet_directory_uri() . '/assets/img/home/';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '·', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home' ); ?>>
<?php wp_body_open(); ?>

<!-- ============================================================
     NAV
     ============================================================ -->
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<!-- ============================================================
     01 HERO
     ============================================================ -->
<?php
$hero_eyebrow = murg_f( 'hp_hero_eyebrow',  '' );
$hero_titulo  = murg_f( 'hp_hero_titulo',   'El detalle es nuestra herencia. Tu historia, nuestro oficio.' );
$hero_sub     = murg_f( 'hp_hero_subtitulo', '' );
$hero_cta_txt = murg_f( 'hp_hero_cta_texto', 'Descubrir Colección' );
$hero_cta_url = murg_f( 'hp_hero_cta_link',  home_url( '/shop/' ) );

$hero_slides = [];
if ( function_exists( 'have_rows' ) && have_rows( 'hp_hero_slides', murguia_ajuste_id() ) ) {
	while ( have_rows( 'hp_hero_slides', murguia_ajuste_id() ) ) {
		the_row();
		$tipo       = get_sub_field( 'tipo' ) ?: 'imagen';
		$img        = get_sub_field( 'imagen' );
		$video_url  = trim( (string) get_sub_field( 'video_url' ) );
		$vid_inicio = (int) ( get_sub_field( 'video_inicio' ) ?: 0 );
		$vid_fin    = (int) ( get_sub_field( 'video_fin' ) ?: 15 );

		if ( $tipo === 'video' && empty( $video_url ) ) continue;

		$video_embed = '';
		$video_mp4   = '';
		if ( $tipo === 'video' ) {
			if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video_url, $m ) ) {
				$vid = $m[1];
				$video_embed = 'https://www.youtube.com/embed/' . $vid
					. '?autoplay=1&mute=1&loop=1&playlist=' . $vid
					. '&controls=0&playsinline=1&rel=0&enablejsapi=1'
					. ( $vid_inicio > 0 ? '&start=' . $vid_inicio : '' );
			} elseif ( preg_match( '/vimeo\.com\/(\d+)/', $video_url, $m ) ) {
				$vid = $m[1];
				$video_embed = 'https://player.vimeo.com/video/' . $vid
					. '?autoplay=1&muted=1&loop=1&background=1'
					. ( $vid_inicio > 0 ? '#t=' . $vid_inicio . 's' : '' );
			} else {
				$video_mp4 = $video_url;
			}
		}

		$hero_slides[] = [
			'tipo'        => $tipo,
			'url'         => $img['url'] ?? '',
			'alt'         => $img['alt'] ?? '',
			'video_embed' => $video_embed,
			'video_mp4'   => $video_mp4,
			'video_inicio'=> $vid_inicio,
			'video_fin'   => $vid_fin,
			'intervalo'   => $tipo === 'video' ? max( ( $vid_fin - $vid_inicio ) * 1000, 2000 ) : 5000,
			'eyebrow'     => (string) get_sub_field( 'eyebrow' ),
			'titulo'      => (string) get_sub_field( 'titulo' ),
			'subtitulo'   => (string) get_sub_field( 'subtitulo' ),
			'cta_texto'   => (string) get_sub_field( 'cta_texto' ),
			'cta_link'    => (string) get_sub_field( 'cta_link' ),
		];
	}
}
if ( empty( $hero_slides ) ) {
	$legacy = murg_f( 'hp_hero_imagen', [] );
	$hero_slides[] = [
		'tipo'        => 'imagen',
		'url'         => $legacy['url'] ?? $img_base . 'hero.jpg',
		'alt'         => $legacy['alt'] ?? 'Joyería Murguía',
		'video_embed' => '', 'video_mp4' => '', 'video_inicio' => 0, 'video_fin' => 15,
		'intervalo'   => 5000,
		'eyebrow' => '', 'titulo' => '', 'subtitulo' => '', 'cta_texto' => '', 'cta_link' => '',
	];
}
?>
<section class="murg-hero" id="murg-hero-slider" aria-label="Hero">

	<?php foreach ( $hero_slides as $idx => $slide ) :
		$s_titulo  = $slide['titulo']    ?: $hero_titulo;
		$s_cta_txt = $slide['cta_texto'] ?: $hero_cta_txt;
		$s_cta_url = $slide['cta_link']  ?: $hero_cta_url;
	?>
	<div class="murg-hero__slide<?php echo $idx === 0 ? ' is-active' : ''; ?>"
	     data-intervalo="<?php echo (int) $slide['intervalo']; ?>"
	     <?php if ( $slide['tipo'] === 'video' ) : ?>
	     data-video-inicio="<?php echo (int) $slide['video_inicio']; ?>"
	     data-video-fin="<?php echo (int) $slide['video_fin']; ?>"
	     <?php endif; ?>
	     aria-hidden="<?php echo $idx === 0 ? 'false' : 'true'; ?>">

		<div class="murg-hero__bg">
			<?php if ( $slide['tipo'] === 'video' && $slide['video_embed'] ) : ?>
			<div class="murg-hero__video-wrap">
				<iframe class="murg-hero__video-iframe"
				        src="<?php echo esc_url( $slide['video_embed'] ); ?>"
				        frameborder="0" allow="autoplay; fullscreen" allowfullscreen
				        loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>"
				        data-video-iframe></iframe>
			</div>
			<?php elseif ( $slide['tipo'] === 'video' && $slide['video_mp4'] ) : ?>
			<video class="murg-hero__video-mp4"
			       src="<?php echo esc_url( $slide['video_mp4'] ); ?>"
			       autoplay muted playsinline data-video-mp4
			       data-inicio="<?php echo (int) $slide['video_inicio']; ?>"
			       data-fin="<?php echo (int) $slide['video_fin']; ?>"></video>
			<?php else : ?>
			<img class="murg-hero__img"
			     src="<?php echo esc_url( $slide['url'] ); ?>"
			     alt="<?php echo esc_attr( $slide['alt'] ); ?>"
			     <?php echo $idx > 0 ? 'loading="lazy"' : 'loading="eager"'; ?>>
			<?php endif; ?>
			<div class="murg-hero__vignette"></div>
		</div>

		<div class="murg-hero__content">
			<h1 class="murg-hero__title"><?php echo esc_html( $s_titulo ); ?></h1>
			<a href="<?php echo esc_url( $s_cta_url ); ?>" class="murg-hero__cta">
				<?php echo esc_html( $s_cta_txt ); ?>
			</a>
		</div>

	</div>
	<?php endforeach; ?>

	<?php if ( count( $hero_slides ) > 1 ) : ?>
	<div class="murg-hero__dots" aria-label="Slides" role="tablist">
		<?php foreach ( $hero_slides as $idx => $slide ) : ?>
		<button class="murg-hero__dot<?php echo $idx === 0 ? ' is-active' : ''; ?>"
		        data-index="<?php echo $idx; ?>"
		        role="tab"
		        aria-selected="<?php echo $idx === 0 ? 'true' : 'false'; ?>"
		        aria-label="Slide <?php echo $idx + 1; ?>">
		</button>
		<?php endforeach; ?>
	</div>
	<div class="murg-hero__progress" aria-hidden="true">
		<div class="murg-hero__progress-bar"></div>
	</div>
	<?php endif; ?>

</section>

<!-- ============================================================
     02 ANILLOS DE COMPROMISO — formas de diamante
     ============================================================ -->
<?php
$diamond_titulo = murg_f( 'hp_diamond_titulo', 'Anillos de compromiso' );
$diamond_sub    = murg_f( 'hp_diamond_sub',    'forjada a mano por orfebres' );
$diamond_imagen = murg_f( 'hp_diamond_imagen', [] );
$diamond_img_url = ! empty( $diamond_imagen['url'] ) ? $diamond_imagen['url'] : $img_base . 'diamond-shapes.jpg';

$diamond_shapes = [
	[ 'slug' => 'oval',      'label' => 'Oval' ],
	[ 'slug' => 'round',     'label' => 'Round' ],
	[ 'slug' => 'emerald',   'label' => 'Emerald' ],
	[ 'slug' => 'marquise',  'label' => 'Marquise' ],
	[ 'slug' => 'radiant',   'label' => 'Radiant' ],
	[ 'slug' => 'pear',      'label' => 'Pear' ],
	[ 'slug' => 'cushion',   'label' => 'Elongated Cushion' ],
	[ 'slug' => 'princess',  'label' => 'Princess' ],
	[ 'slug' => 'asscher',   'label' => 'Asscher' ],
	[ 'slug' => 'heart',     'label' => 'Cushion' ],
];
$shapes_dir = get_stylesheet_directory_uri() . '/assets/img/diamond-shapes/';
?>
<section class="murg-diamonds" aria-label="<?php echo esc_attr( $diamond_titulo ); ?>">

	<div class="murg-diamonds__header">
		<h2 class="murg-diamonds__title"><?php echo esc_html( $diamond_titulo ); ?></h2>
		<p class="murg-diamonds__sub"><?php echo esc_html( $diamond_sub ); ?></p>
	</div>

	<div class="murg-diamonds__showcase">
		<img class="murg-diamonds__bg-img"
		     src="<?php echo esc_url( $diamond_img_url ); ?>"
		     alt="Selección de anillos"
		     loading="lazy">

		<div class="murg-diamonds__shapes-row murg-diamonds__shapes-row--top">
			<?php foreach ( array_slice( $diamond_shapes, 0, 5 ) as $shape ) :
				$icon_url = $shapes_dir . $shape['slug'] . '_new.png';
			?>
			<a href="<?php echo esc_url( home_url( '/shop/?product_cat=anillos-de-compromiso&forma=' . $shape['slug'] ) ); ?>"
			   class="murg-diamonds__shape">
				<img src="<?php echo esc_url( $icon_url ); ?>"
				     alt="<?php echo esc_attr( $shape['label'] ); ?>"
				     loading="lazy">
				<span><?php echo esc_html( $shape['label'] ); ?></span>
			</a>
			<?php endforeach; ?>
		</div>

		<div class="murg-diamonds__shapes-row murg-diamonds__shapes-row--bottom">
			<?php foreach ( array_slice( $diamond_shapes, 5 ) as $shape ) :
				$icon_url = $shapes_dir . $shape['slug'] . '_new.png';
			?>
			<a href="<?php echo esc_url( home_url( '/shop/?product_cat=anillos-de-compromiso&forma=' . $shape['slug'] ) ); ?>"
			   class="murg-diamonds__shape">
				<img src="<?php echo esc_url( $icon_url ); ?>"
				     alt="<?php echo esc_attr( $shape['label'] ); ?>"
				     loading="lazy">
				<span><?php echo esc_html( $shape['label'] ); ?></span>
			</a>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="murg-diamonds__cta-wrap">
		<a href="<?php echo esc_url( home_url( '/shop/?product_cat=anillos-de-compromiso' ) ); ?>"
		   class="murg-btn murg-btn--dark">
			<?php echo esc_html( murg_f( 'hp_diamond_cta', 'Ver Colecciónes' ) ); ?>
		</a>
	</div>

</section>

<!-- ============================================================
     03 NOVIOS — imagen + texto + logos marcas
     ============================================================ -->
<?php
$novios_titulo  = murg_f( 'hp_novios_titulo', 'Novios' );
$novios_sub     = murg_f( 'hp_novios_sub',    'Anillos de Compromiso / Anillos de Matrimonio / Las 4Cs' );
$novios_cta_txt = murg_f( 'hp_novios_cta_texto', 'Ver Colecciónes' );
$novios_cta_url = murg_f( 'hp_novios_cta_url', home_url( '/shop/?product_cat=novios' ) );
$novios_imagen  = murg_f( 'hp_novios_imagen', [] );
$novios_img_url = ! empty( $novios_imagen['url'] ) ? $novios_imagen['url'] : $img_base . 'novios.jpg';

$novios_logos = [];
if ( function_exists( 'have_rows' ) && have_rows( 'hp_novios_logos', murguia_ajuste_id() ) ) {
	while ( have_rows( 'hp_novios_logos', murguia_ajuste_id() ) ) {
		the_row();
		$novios_logos[] = [
			'imagen' => get_sub_field( 'imagen' ),
			'link'   => get_sub_field( 'link' ) ?: '',
		];
	}
}
?>
<section class="murg-novios" aria-label="<?php echo esc_attr( $novios_titulo ); ?>">

	<div class="murg-novios__img-wrap">
		<img src="<?php echo esc_url( $novios_img_url ); ?>"
		     alt="<?php echo esc_attr( $novios_titulo ); ?>"
		     loading="lazy">
	</div>

	<div class="murg-novios__content">
		<h2 class="murg-novios__title"><?php echo esc_html( $novios_titulo ); ?></h2>
		<p class="murg-novios__sub"><?php echo esc_html( $novios_sub ); ?></p>

		<a href="<?php echo esc_url( $novios_cta_url ); ?>"
		   class="murg-btn murg-btn--dark">
			<?php echo esc_html( $novios_cta_txt ); ?>
		</a>

		<?php if ( ! empty( $novios_logos ) ) : ?>
		<div class="murg-novios__logos">
			<?php foreach ( $novios_logos as $logo ) :
				if ( empty( $logo['imagen']['url'] ) ) continue;
			?>
			<?php if ( $logo['link'] ) : ?>
			<a href="<?php echo esc_url( $logo['link'] ); ?>" target="_blank" rel="noopener">
			<?php endif; ?>
				<img src="<?php echo esc_url( $logo['imagen']['url'] ); ?>"
				     alt="<?php echo esc_attr( $logo['imagen']['alt'] ?? '' ); ?>"
				     loading="lazy">
			<?php if ( $logo['link'] ) : ?></a><?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

</section>

<!-- ============================================================
     04 PIEZAS QUE DESTACAN — tabs categoría + 3 productos
     ============================================================ -->
<?php
$piezas_titulo = murg_f( 'hp_piezas_titulo', 'Piezas que Destacan' );
$piezas_cta_txt = murg_f( 'hp_piezas_cta_texto', 'Ver tienda completa' );
$piezas_cta_url = murg_f( 'hp_piezas_cta_url', home_url( '/shop/' ) );

$tabs = [
	[ 'slug' => 'anillos',       'label' => 'Anillos' ],
	[ 'slug' => 'aretes',        'label' => 'Aretes' ],
	[ 'slug' => 'pulseras',      'label' => 'Pulseras' ],
	[ 'slug' => 'collares',      'label' => 'Collares y Dijes' ],
	[ 'slug' => 'bebes',         'label' => 'Bebés' ],
];

// Consulta 3 productos destacados de WooCommerce
$piezas_items = [];
if ( function_exists( 'wc_get_products' ) ) {
	$wc_piezas = wc_get_products( [
		'limit'    => 3,
		'status'   => 'publish',
		'orderby'  => 'popularity',
		'order'    => 'DESC',
	] );
	foreach ( $wc_piezas as $p ) {
		$img_id = $p->get_image_id();
		$piezas_items[] = [
			'name'  => $p->get_name(),
			'price' => $p->get_price_html(),
			'url'   => $p->get_permalink(),
			'img'   => $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '',
			'alt'   => $img_id ? get_post_meta( $img_id, '_wp_attachment_image_alt', true ) : $p->get_name(),
		];
	}
}
// Fallback con imágenes de Figma
if ( empty( $piezas_items ) ) {
	$piezas_items = [
		[ 'name' => 'Anillo de compromiso con brillante 0.44ct', 'price' => '<span>USD 3,708.00</span>', 'url' => home_url('/shop/'), 'img' => $img_base . 'product-1.jpg', 'alt' => 'Anillo de compromiso' ],
		[ 'name' => 'Anillo de compromiso 0.19ct',              'price' => '<span>USD 1,790.00</span>', 'url' => home_url('/shop/'), 'img' => $img_base . 'product-2.jpg', 'alt' => 'Anillo de compromiso' ],
		[ 'name' => 'Anillo con diamantes en oro amarillo',     'price' => '<span>USD 3,708.00</span>', 'url' => home_url('/shop/'), 'img' => $img_base . 'product-3.jpg', 'alt' => 'Anillo diamantes oro' ],
	];
}
?>
<section class="murg-piezas" aria-label="<?php echo esc_attr( $piezas_titulo ); ?>">

	<nav class="murg-piezas__tabs" aria-label="Categorías">
		<?php foreach ( $tabs as $i => $tab ) : ?>
		<a href="<?php echo esc_url( home_url( '/shop/?product_cat=' . $tab['slug'] ) ); ?>"
		   class="murg-piezas__tab<?php echo $i === 0 ? ' is-active' : ''; ?>">
			<?php echo esc_html( $tab['label'] ); ?>
		</a>
		<?php endforeach; ?>
	</nav>

	<header class="murg-piezas__header">
		<h2 class="murg-piezas__title"><?php echo esc_html( $piezas_titulo ); ?></h2>
	</header>

	<div class="murg-piezas__grid">
		<?php foreach ( $piezas_items as $it ) : ?>
		<article class="murg-pieza">
			<a href="<?php echo esc_url( $it['url'] ); ?>" class="murg-pieza__link">
				<div class="murg-pieza__img">
					<?php if ( $it['img'] ) : ?>
					<img src="<?php echo esc_url( $it['img'] ); ?>"
					     alt="<?php echo esc_attr( $it['alt'] ); ?>"
					     loading="lazy">
					<?php endif; ?>
				</div>
				<div class="murg-pieza__meta">
					<span class="murg-pieza__name"><?php echo esc_html( $it['name'] ); ?></span>
					<span class="murg-pieza__price"><?php echo wp_kses_post( $it['price'] ); ?></span>
				</div>
			</a>
		</article>
		<?php endforeach; ?>
	</div>

	<div class="murg-piezas__cta-wrap">
		<a href="<?php echo esc_url( $piezas_cta_url ); ?>" class="murg-btn murg-btn--dark">
			<?php echo esc_html( $piezas_cta_txt ); ?>
		</a>
	</div>

</section>

<!-- ============================================================
     05 PRODUCTO DESTACADO — collar/pieza editorial
     ============================================================ -->
<?php
$feat_titulo   = murg_f( 'hp_feat_titulo',   'Collar con diamantes y ónix' );
$feat_sub      = murg_f( 'hp_feat_sub',      'COLLAR EN ORO BLANCO 18KT. CON DIAMANTES CORTE BRILLANTE 2.67CT.' );
$feat_cta_txt  = murg_f( 'hp_feat_cta_texto', 'Ver tienda completa' );
$feat_cta_url  = murg_f( 'hp_feat_cta_url',   home_url( '/shop/' ) );
$feat_imagen   = murg_f( 'hp_feat_imagen', [] );
$feat_img_url  = ! empty( $feat_imagen['url'] ) ? $feat_imagen['url'] : $img_base . 'featured-collar.jpg';

// Slider dots (controlados desde ACF o estático)
$feat_slides_count = (int) murg_f( 'hp_feat_slides', 5 );
?>
<section class="murg-featured" aria-label="<?php echo esc_attr( $feat_titulo ); ?>">

	<div class="murg-featured__text">
		<h2 class="murg-featured__title"><?php echo esc_html( $feat_titulo ); ?></h2>
		<p class="murg-featured__sub"><?php echo esc_html( $feat_sub ); ?></p>
	</div>

	<div class="murg-featured__img-wrap">
		<img src="<?php echo esc_url( $feat_img_url ); ?>"
		     alt="<?php echo esc_attr( $feat_titulo ); ?>"
		     loading="lazy">

		<?php if ( $feat_slides_count > 1 ) : ?>
		<div class="murg-featured__dots" aria-hidden="true">
			<?php for ( $i = 0; $i < $feat_slides_count; $i++ ) : ?>
			<span class="murg-featured__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"></span>
			<?php endfor; ?>
		</div>
		<?php endif; ?>
	</div>

</section>

<!-- ============================================================
     06 STATEMENT — cita + imagen (fondo oscuro)
     ============================================================ -->
<?php
$stmt_texto     = murg_f( 'hp_stmt_texto',     '"Cada pieza nace en nuestro taller en Lima, forjada a mano por orfebres que han pasado el oficio de padres a hijos durante más de un siglo."' );
$stmt_atribucion = murg_f( 'hp_stmt_atribucion', 'CASA MURGUÍA, FUNDADA EN 1910' );
$stmt_imagen    = murg_f( 'hp_stmt_imagen', [] );
$stmt_img_url   = ! empty( $stmt_imagen['url'] ) ? $stmt_imagen['url'] : $img_base . 'statement-bg.jpg';

// Slider dots decorativos
$stmt_slides_count = (int) murg_f( 'hp_stmt_slides', 5 );
?>
<section class="murg-statement-v2" aria-label="Nuestra historia">

	<div class="murg-statement-v2__img-wrap">
		<img src="<?php echo esc_url( $stmt_img_url ); ?>"
		     alt="Taller Murguía"
		     loading="lazy">

		<?php if ( $stmt_slides_count > 1 ) : ?>
		<div class="murg-statement-v2__dots" aria-hidden="true">
			<?php for ( $i = 0; $i < $stmt_slides_count; $i++ ) : ?>
			<span class="murg-statement-v2__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"></span>
			<?php endfor; ?>
		</div>
		<?php endif; ?>
	</div>

	<div class="murg-statement-v2__content">
		<blockquote class="murg-statement-v2__quote">
			<?php echo esc_html( $stmt_texto ); ?>
		</blockquote>
		<p class="murg-statement-v2__attr"><?php echo esc_html( $stmt_atribucion ); ?></p>
	</div>

</section>

<!-- ============================================================
     07 COLECCIÓN QANTU — galería editorial
     ============================================================ -->
<?php
$qantu_titulo  = murg_f( 'hp_qantu_titulo',   'Colección QANTU' );
$qantu_sub     = murg_f( 'hp_qantu_sub',      'Donde florece el tiempo' );
$qantu_cta_txt = murg_f( 'hp_qantu_cta_texto', 'Ver Colección' );
$qantu_cta_url = murg_f( 'hp_qantu_cta_url',   home_url( '/shop/' ) );

$qantu_imgs = [
	murg_f( 'hp_qantu_imagen_1', [] ),
	murg_f( 'hp_qantu_imagen_2', [] ),
	murg_f( 'hp_qantu_imagen_3', [] ),
];
$qantu_fallbacks = [ 'qantu-left.jpg', 'qantu-1.jpg', 'qantu-right.jpg' ];
?>
<section class="murg-qantu" aria-label="<?php echo esc_attr( $qantu_titulo ); ?>">

	<header class="murg-qantu__header">
		<h2 class="murg-qantu__title"><?php echo esc_html( $qantu_titulo ); ?></h2>
		<p class="murg-qantu__sub"><?php echo esc_html( $qantu_sub ); ?></p>
		<a href="<?php echo esc_url( $qantu_cta_url ); ?>" class="murg-btn murg-btn--dark">
			<?php echo esc_html( $qantu_cta_txt ); ?>
		</a>
	</header>

	<div class="murg-qantu__gallery">
		<?php foreach ( $qantu_imgs as $i => $img ) :
			$url = ! empty( $img['url'] ) ? $img['url'] : $img_base . $qantu_fallbacks[ $i ];
			$alt = ! empty( $img['alt'] ) ? $img['alt'] : $qantu_titulo;
		?>
		<div class="murg-qantu__img">
			<img src="<?php echo esc_url( $url ); ?>"
			     alt="<?php echo esc_attr( $alt ); ?>"
			     loading="lazy">
		</div>
		<?php endforeach; ?>
	</div>

</section>

<!-- ============================================================
     08 AGENDA TU VISITA
     ============================================================ -->
<?php
$visita_titulo   = murg_f( 'hp_visita_titulo',   'Agenda tu visita' );
$visita_sub      = murg_f( 'hp_visita_sub',      'Una asesoría privada con nuestros especialistas.' );
$visita_boutique = murg_f( 'hp_visita_boutique', 'En Boutique' );
$visita_ubicacion = murg_f( 'hp_visita_ubicacion', 'San Isidro, Miraflores, Jockey Plaza' );
$visita_virtual  = murg_f( 'hp_visita_virtual',  'Por Videollamada' );
$visita_horario  = murg_f( 'hp_visita_horario',  'Lunes a Viernes  10:00 a 19:00' );
$visita_cita_url = murg_f( 'hp_visita_cita_url', home_url( '/contacto/' ) );
$visita_wa_url   = murg_f( 'hp_visita_wa_url',   'https://wa.me/51114218800' );
$visita_imagen   = murg_f( 'hp_visita_imagen', [] );
$visita_img_url  = ! empty( $visita_imagen['url'] ) ? $visita_imagen['url'] : $img_base . 'appointment.jpg';
?>
<section class="murg-visita" id="contacto" aria-label="<?php echo esc_attr( $visita_titulo ); ?>">

	<div class="murg-visita__img-wrap">
		<img src="<?php echo esc_url( $visita_img_url ); ?>"
		     alt="Boutique Murguía"
		     loading="lazy">
	</div>

	<div class="murg-visita__content">
		<h2 class="murg-visita__title"><?php echo esc_html( $visita_titulo ); ?></h2>
		<p class="murg-visita__sub"><?php echo esc_html( $visita_sub ); ?></p>

		<div class="murg-visita__info">
			<div class="murg-visita__row">
				<span class="murg-visita__label"><?php echo esc_html( $visita_boutique ); ?></span>
				<span class="murg-visita__value"><?php echo esc_html( $visita_ubicacion ); ?></span>
			</div>
			<div class="murg-visita__divider"></div>
			<div class="murg-visita__row">
				<span class="murg-visita__label"><?php echo esc_html( $visita_virtual ); ?></span>
				<span class="murg-visita__value"><?php echo esc_html( $visita_horario ); ?></span>
			</div>
			<div class="murg-visita__divider"></div>
		</div>

		<div class="murg-visita__btns">
			<a href="<?php echo esc_url( $visita_cita_url ); ?>" class="murg-btn murg-btn--dark">
				<?php echo esc_html( murg_f( 'hp_visita_cita_texto', 'Reservar cita' ) ); ?>
			</a>
			<a href="<?php echo esc_url( $visita_wa_url ); ?>"
			   class="murg-btn murg-btn--dark"
			   target="_blank" rel="noopener noreferrer">
				WHATSAPP
			</a>
		</div>
	</div>

</section>

<!-- ============================================================
     09 MARCAS — logos
     ============================================================ -->
<?php
$brands = [];
if ( function_exists( 'have_rows' ) && have_rows( 'hp_brands_logos', murguia_ajuste_id() ) ) {
	while ( have_rows( 'hp_brands_logos', murguia_ajuste_id() ) ) {
		the_row();
		$brands[] = [
			'imagen' => get_sub_field( 'imagen' ),
			'link'   => get_sub_field( 'link' ) ?: '',
			'alt'    => get_sub_field( 'alt' ) ?: '',
		];
	}
}
?>
<?php if ( ! empty( $brands ) ) : ?>
<section class="murg-brands" aria-label="Marcas">
	<div class="murg-brands__track">
		<?php foreach ( $brands as $brand ) :
			if ( empty( $brand['imagen']['url'] ) ) continue;
		?>
		<?php if ( $brand['link'] ) : ?>
		<a href="<?php echo esc_url( $brand['link'] ); ?>" target="_blank" rel="noopener" class="murg-brands__logo">
		<?php else : ?>
		<div class="murg-brands__logo">
		<?php endif; ?>
			<img src="<?php echo esc_url( $brand['imagen']['url'] ); ?>"
			     alt="<?php echo esc_attr( $brand['alt'] ?: ( $brand['imagen']['alt'] ?? '' ) ); ?>"
			     loading="lazy">
		<?php echo $brand['link'] ? '</a>' : '</div>'; ?>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>

<!-- ============================================================
     10 NEWSLETTER — -10% primera compra
     ============================================================ -->
<?php
$nl_titulo = murg_f( 'hp_nl_titulo', '-10% en tu primera compra.' );
$nl_sub    = murg_f( 'hp_nl_sub',   '' );
?>
<section class="murg-newsletter" aria-label="Newsletter">
	<div class="murg-newsletter__inner">
		<h2 class="murg-newsletter__title"><?php echo esc_html( $nl_titulo ); ?></h2>
		<?php if ( $nl_sub ) : ?>
		<p class="murg-newsletter__sub"><?php echo esc_html( $nl_sub ); ?></p>
		<?php endif; ?>
		<form class="murg-newsletter__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'murg_newsletter', 'murg_nl_nonce' ); ?>
			<input type="hidden" name="action" value="murg_newsletter_subscribe">
			<div class="murg-newsletter__field">
				<input type="email" name="email"
				       placeholder="<?php esc_attr_e( 'Tu correo electrónico', 'woodmart-child' ); ?>"
				       required>
				<button type="submit"><?php esc_html_e( 'Suscribirme', 'woodmart-child' ); ?></button>
			</div>
		</form>
	</div>
</section>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
