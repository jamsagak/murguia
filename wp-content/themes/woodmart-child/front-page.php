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

$img_base   = get_stylesheet_directory_uri() . '/assets/img/home/';
$img_upload = content_url( 'uploads/2026/05/' );
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
$hero_eyebrow = '';
$hero_titulo  = 'El detalle es nuestra herencia. Tu historia, nuestro oficio.';
$hero_sub     = '';
$hero_cta_txt = 'Descubrir Colección';
$hero_cta_url = home_url( '/shop/' );

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
			'url'         => ! empty( $img['url'] ) ? $img['url'] : $img_upload . 'hero.jpg',
			'alt'         => $img['alt'] ?? 'Joyería Murguía',
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
	$hero_slides[] = [
		'tipo'        => 'imagen',
		'url'         => $img_upload . 'hero.jpg',
		'alt'         => 'Joyeria Murguia',
		'video_embed' => '', 'video_mp4' => '', 'video_inicio' => 0, 'video_fin' => 15,
		'intervalo'   => 5000,
		'eyebrow' => '', 'titulo' => '', 'subtitulo' => '', 'cta_texto' => '', 'cta_link' => '',
	];
}
?>
<section class="murg-hero" id="murg-hero-slider" aria-label="Hero">

	<!-- ── Slides: capas de fondo puras ─────────────────────── -->
	<?php foreach ( $hero_slides as $idx => $slide ) : ?>
	<div class="murg-hero__slide<?php echo $idx === 0 ? ' is-active' : ''; ?>"
	     data-intervalo="<?php echo (int) $slide['intervalo']; ?>"
	     data-titulo="<?php echo esc_attr( $slide['titulo'] ?: $hero_titulo ); ?>"
	     data-cta-texto="<?php echo esc_attr( $slide['cta_texto'] ?: $hero_cta_txt ); ?>"
	     data-cta-url="<?php echo esc_attr( $slide['cta_link'] ?: $hero_cta_url ); ?>"
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
		</div>
		<div class="murg-hero__vignette"></div>

	</div>
	<?php endforeach; ?>

	<!-- ── Overlay de contenido: directo en el section ──────── -->
	<?php
	$first     = $hero_slides[0];
	$s_titulo  = $first['titulo']    ?: $hero_titulo;
	$s_cta_txt = $first['cta_texto'] ?: $hero_cta_txt;
	$s_cta_url = $first['cta_link']  ?: $hero_cta_url;
	$n_slides  = count( $hero_slides );
	?>
	<div class="murg-hero__content" aria-live="polite">
		<h1 class="murg-hero__title"><?php echo esc_html( $s_titulo ); ?></h1>

		<a href="<?php echo esc_url( $s_cta_url ); ?>" class="murg-hero__cta">
			<?php echo esc_html( $s_cta_txt ); ?>
		</a>
		<span class="murg-hero__cta-line" aria-hidden="true"></span>

		<div class="murg-hero__dots-inline" role="tablist" aria-label="Slides">
			<?php for ( $d = 0; $d < $n_slides; $d++ ) : ?>
			<button class="murg-hero__dot-circle<?php echo $d === 0 ? ' is-active' : ''; ?>"
			        data-index="<?php echo $d; ?>"
			        role="tab"
			        aria-selected="<?php echo $d === 0 ? 'true' : 'false'; ?>"
			        aria-label="Slide <?php echo $d + 1; ?>"></button>
			<?php endfor; ?>
		</div>
	</div>

	<div class="murg-hero__progress" aria-hidden="true">
		<div class="murg-hero__progress-bar"></div>
	</div>

</section>

<!-- ============================================================
     02 ANILLOS DE COMPROMISO — imagen + texto + certificaciones
     (Fusiona antiguas secciones 02 Diamonds y 03 Novios)
     ============================================================ -->
<?php
$compromiso_titulo   = murg_f( 'hp_compromiso_titulo', 'Anillos de compromiso' );
$compromiso_desc     = murg_f( 'hp_compromiso_desc', 'Cada anillo de compromiso cuenta una historia única. Nuestros diamantes certificados internacionalmente garantizan la máxima calidad y autenticidad en cada pieza que creamos para ti.' );
$compromiso_cert_lbl = murg_f( 'hp_compromiso_cert_label', 'Certificados internacionales' );
$compromiso_cta_txt  = murg_f( 'hp_compromiso_cta_texto', 'Ver Colección' );
$compromiso_cta_url  = murg_f( 'hp_compromiso_cta_url', home_url( '/anillos-compromiso/' ) );
$compromiso_imagen   = murg_f( 'hp_compromiso_imagen', [] );
$compromiso_img_url  = ! empty( $compromiso_imagen['url'] ) ? $compromiso_imagen['url'] : $img_upload . 'compromiso-home.webp';

$compromiso_logos = [];
if ( function_exists( 'have_rows' ) && have_rows( 'hp_compromiso_logos', murguia_ajuste_id() ) ) {
	while ( have_rows( 'hp_compromiso_logos', murguia_ajuste_id() ) ) {
		the_row();
		$compromiso_logos[] = [
			'imagen' => get_sub_field( 'imagen' ),
		];
	}
}
/* Fallback: reutilizar logos de hp_novios_logos si el nuevo repeater está vacío */
if ( empty( $compromiso_logos ) && function_exists( 'have_rows' ) && have_rows( 'hp_novios_logos', murguia_ajuste_id() ) ) {
	while ( have_rows( 'hp_novios_logos', murguia_ajuste_id() ) ) {
		the_row();
		$compromiso_logos[] = [
			'imagen' => get_sub_field( 'imagen' ),
		];
	}
}
?>
<section class="murg-compromiso" aria-label="<?php echo esc_attr( $compromiso_titulo ); ?>">
	<div class="murg-compromiso__inner">

		<!-- LEFT: imagen -->
		<div class="murg-compromiso__visual">
			<img src="<?php echo esc_url( $compromiso_img_url ); ?>"
			     alt="<?php echo esc_attr( $compromiso_titulo ); ?>"
			     loading="lazy"
			     class="murg-compromiso__img">
		</div>

		<!-- RIGHT: contenido -->
		<div class="murg-compromiso__content">
			<h2 class="murg-compromiso__title"><?php echo esc_html( $compromiso_titulo ); ?></h2>

			<p class="murg-compromiso__desc"><?php echo esc_html( $compromiso_desc ); ?></p>

			<p class="murg-compromiso__cert-label"><?php echo esc_html( $compromiso_cert_lbl ); ?></p>

			<?php if ( ! empty( $compromiso_logos ) ) : ?>
			<div class="murg-compromiso__logos" aria-label="Certificaciones">
				<?php foreach ( $compromiso_logos as $logo ) :
					if ( empty( $logo['imagen']['url'] ) ) continue;
				?>
				<img src="<?php echo esc_url( $logo['imagen']['url'] ); ?>"
				     alt="<?php echo esc_attr( $logo['imagen']['alt'] ?? '' ); ?>"
				     loading="lazy">
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<a href="<?php echo esc_url( $compromiso_cta_url ); ?>"
			   class="murg-btn murg-btn--dark murg-compromiso__cta">
				<?php echo esc_html( $compromiso_cta_txt ); ?>
			</a>
		</div>

	</div>
</section>

<!-- ============================================================
     03B ICONOS CATEGORIAS
     ============================================================ -->
<?php
$icon_strip_items = [
	[ 'slug' => 'diamante', 'label' => 'Diamantes', 'url' => home_url( '/shop/?product_cat=anillos-de-compromiso' ) ],
	[ 'slug' => 'pulsera',  'label' => 'Pulseras',  'url' => home_url( '/shop/?product_cat=pulseras' ) ],
	[ 'slug' => 'anillo',   'label' => 'Anillos',   'url' => home_url( '/shop/?product_cat=anillos' ) ],
	[ 'slug' => 'arete',    'label' => 'Aretes',    'url' => home_url( '/shop/?product_cat=aretes' ) ],
	[ 'slug' => 'collar',   'label' => 'Collares',  'url' => home_url( '/shop/?product_cat=collares-y-dijes' ) ],
	[ 'slug' => 'hogar',    'label' => 'Hogar',     'url' => home_url( '/hogar/' ) ],
];
?>
<section class="murg-icon-strip" aria-label="Categorías destacadas">
	<div class="murg-icon-strip__inner">
		<?php foreach ( $icon_strip_items as $item ) :
			$upload_dir = wp_upload_dir();
			$svg_path   = trailingslashit( $upload_dir['basedir'] ) . '2026/05/' . $item['slug'] . '.svg';
			$svg_url    = trailingslashit( $upload_dir['baseurl'] ) . '2026/05/' . $item['slug'] . '.svg';

			if ( ! file_exists( $svg_path ) ) {
				$att = get_posts( [
					'post_type'      => 'attachment',
					'name'           => $item['slug'],
					'post_mime_type' => 'image/svg+xml',
					'numberposts'    => 1,
					'post_status'    => 'inherit',
				] );
				if ( ! $att ) {
					$att = get_posts( [
						'post_type'   => 'attachment',
						'name'        => $item['slug'],
						'numberposts' => 1,
						'post_status' => 'inherit',
					] );
				}
				$svg_url = $att ? wp_get_attachment_url( $att[0]->ID ) : '';
			}
		?>
		<a href="<?php echo esc_url( $item['url'] ); ?>" class="murg-icon-strip__item">
			<?php if ( $svg_url ) : ?>
			<img src="<?php echo esc_url( $svg_url ); ?>"
			     alt=""
			     loading="lazy"
			     class="murg-icon-strip__icon"
			     aria-hidden="true">
			<?php endif; ?>
			<span class="murg-icon-strip__label"><?php echo esc_html( $item['label'] ); ?></span>
		</a>
		<?php endforeach; ?>
	</div>
</section>

<!-- ============================================================
     04 PIEZAS QUE DESTACAN - tabs categoria + productos reales
     ============================================================ -->
<?php
$piezas_titulo = murg_f( 'hp_piezas_titulo', 'Piezas que Destacan' );
$piezas_cta_txt = murg_f( 'hp_piezas_cta_texto', 'Ver tienda completa' );
$piezas_cta_url = murg_f( 'hp_piezas_cta_url', home_url( '/shop/' ) );

$piezas_category_slugs = [ 'anillos', 'aretes', 'pulseras', 'collares-y-dijes', 'bebes' ];
$tabs = [];

foreach ( $piezas_category_slugs as $slug ) {
	$term = taxonomy_exists( 'product_cat' ) ? get_term_by( 'slug', $slug, 'product_cat' ) : false;
	if ( ! $term || is_wp_error( $term ) ) {
		continue;
	}

	$products = [];
	if ( function_exists( 'wc_get_products' ) ) {
		$wc_piezas = wc_get_products( [
			'limit'    => 3,
			'status'   => 'publish',
			'category' => [ $slug ],
			'orderby'  => 'menu_order',
			'order'    => 'ASC',
		] );

		foreach ( $wc_piezas as $p ) {
			$img_id = $p->get_image_id();
			$img    = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '';
			$alt    = $img_id ? get_post_meta( $img_id, '_wp_attachment_image_alt', true ) : '';

			$products[] = [
				'name'  => $p->get_name(),
				'price' => $p->get_price_html(),
				'url'   => $p->get_permalink(),
				'img'   => $img,
				'alt'   => $alt ?: $p->get_name(),
			];
		}
	}

	$tabs[] = [
		'slug'     => $slug,
		'label'    => $term->name,
		'url'      => get_term_link( $term ),
		'products' => $products,
	];
}
?>
<section class="murg-piezas" aria-label="<?php echo esc_attr( $piezas_titulo ); ?>">

	<nav class="murg-piezas__tabs" aria-label="Categorias">
		<?php foreach ( $tabs as $i => $tab ) : ?>
		<button type="button"
		        class="murg-piezas__tab<?php echo $i === 0 ? ' is-active' : ''; ?>"
		        data-target="murg-piezas-<?php echo esc_attr( $tab['slug'] ); ?>"
		        aria-controls="murg-piezas-<?php echo esc_attr( $tab['slug'] ); ?>"
		        aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>">
			<?php echo esc_html( $tab['label'] ); ?>
		</button>
		<?php endforeach; ?>
	</nav>

	<?php foreach ( $tabs as $i => $tab ) : ?>
	<div id="murg-piezas-<?php echo esc_attr( $tab['slug'] ); ?>"
	     class="murg-piezas__panel<?php echo $i === 0 ? ' is-active' : ''; ?>"
	     <?php echo $i === 0 ? '' : 'hidden'; ?>>
		<?php if ( ! empty( $tab['products'] ) ) : ?>
		<div class="murg-piezas__grid">
			<?php foreach ( $tab['products'] as $it ) : ?>
			<article class="murg-pieza">
				<a href="<?php echo esc_url( $it['url'] ); ?>" class="murg-pieza__link">
					<div class="murg-pieza__img">
						<?php if ( $it['img'] ) : ?>
						<img src="<?php echo esc_url( $it['img'] ); ?>"
						     alt="<?php echo esc_attr( $it['alt'] ); ?>"
						     loading="lazy">
						<?php elseif ( function_exists( 'wc_placeholder_img' ) ) : ?>
							<?php echo wc_placeholder_img( 'woocommerce_single' ); ?>
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
		<?php else : ?>
		<p class="murg-piezas__empty">No hay productos publicados en esta categoria.</p>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>

	<div class="murg-piezas__cta-wrap">
		<a href="<?php echo esc_url( $piezas_cta_url ); ?>" class="murg-btn murg-btn--dark">
			<?php echo esc_html( $piezas_cta_txt ); ?>
		</a>
	</div>

</section>
<!-- ============================================================
     05 PRODUCTO DESTACADO — galería de un solo producto
     ============================================================ -->
<?php
$feat_product   = murg_f( 'hp_feat_producto', null );
$feat_gallery   = murg_f( 'hp_feat_gallery', [] );
$feat_title     = '';
$feat_sub       = '';
$feat_price_html = '';
$feat_url       = '';
$feat_images    = [];

if ( $feat_product ) {
	$wc = wc_get_product( $feat_product->ID );
	if ( $wc ) {
		$feat_title      = $wc->get_name();
		$feat_sub        = wp_strip_all_tags( $wc->get_short_description() );
		$feat_price_html = $wc->get_price_html();
		$feat_url        = $wc->get_permalink();

		// Si ACF tiene galería personalizada, úsala. Si no, usa la del producto.
		if ( ! empty( $feat_gallery ) ) {
			foreach ( $feat_gallery as $img ) {
				if ( ! empty( $img['url'] ) ) $feat_images[] = $img['url'];
			}
		} else {
			$main_id = $wc->get_image_id();
			if ( $main_id ) $feat_images[] = wp_get_attachment_image_url( $main_id, 'full' );
			foreach ( $wc->get_gallery_image_ids() as $gid ) {
				$feat_images[] = wp_get_attachment_image_url( $gid, 'full' );
			}
		}
	}
}

// Fallback estático
if ( empty( $feat_images ) ) {
	$feat_imagen = murg_f( 'hp_feat_imagen', [] );
	$feat_title      = $feat_title      ?: murg_f( 'hp_feat_titulo', 'Collar con diamantes y ónix' );
	$feat_sub        = $feat_sub        ?: murg_f( 'hp_feat_sub',    'COLLAR EN ORO BLANCO 18KT. CON DIAMANTES CORTE BRILLANTE 2.67CT.' );
	$feat_price_html = $feat_price_html ?: murg_f( 'hp_feat_precio', 'USD 3,708.00' );
	$feat_images[]   = ! empty( $feat_imagen['url'] ) ? $feat_imagen['url'] : $img_upload . 'featured-collar.jpg';
}

$n_feat = count( $feat_images );
?>
<section class="murg-featured" id="murg-featured-slider" aria-label="<?php echo esc_attr( $feat_title ); ?>">

	<h2 class="murg-featured__section-title"><?php echo esc_html( $piezas_titulo ); ?></h2>

	<div class="murg-featured__text">
		<div class="murg-featured__copy">
			<h3 class="murg-featured__title"><?php echo esc_html( $feat_title ); ?></h3>
			<p class="murg-featured__sub"><?php echo esc_html( $feat_sub ); ?></p>
		</div>
		<p class="murg-featured__price"><?php echo wp_kses_post( $feat_price_html ); ?></p>
	</div>

	<div class="murg-featured__img-wrap">
		<a class="murg-featured__img-link" href="<?php echo $feat_url ? esc_url( $feat_url ) : '#'; ?>">
			<?php foreach ( $feat_images as $idx => $img_src ) : ?>
			<img class="murg-featured__gimg<?php echo $idx === 0 ? ' is-active' : ''; ?>"
			     src="<?php echo esc_url( $img_src ); ?>"
			     alt="<?php echo esc_attr( $feat_title ); ?>"
			     loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>"
			     data-index="<?php echo $idx; ?>">
			<?php endforeach; ?>
		</a>

		<?php if ( $n_feat > 1 ) : ?>
		<div class="murg-featured__dots" role="tablist" aria-label="Galería">
			<?php for ( $i = 0; $i < $n_feat; $i++ ) : ?>
			<button class="murg-featured__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"
			        data-index="<?php echo $i; ?>"
			        role="tab"
			        aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
			        aria-label="Imagen <?php echo $i + 1; ?>"></button>
			<?php endfor; ?>
		</div>
		<?php else : ?>
		<div class="murg-featured__dots" aria-hidden="true">
			<?php for ( $i = 0; $i < 5; $i++ ) : ?>
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
$stmt_img_url   = ! empty( $stmt_imagen['url'] ) ? $stmt_imagen['url'] : $img_upload .'statement-bg.jpg';

// Slider dots decorativos
?>
<section class="murg-statement-v2" aria-label="Nuestra historia">

	<div class="murg-statement-v2__img-wrap">
		<img src="<?php echo esc_url( $stmt_img_url ); ?>"
		     alt="Taller Murguía"
		     loading="lazy">

		<div class="murg-statement-v2__dots" aria-hidden="true">
			<?php for ( $i = 0; $i < 5; $i++ ) : ?>
			<span class="murg-statement-v2__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"></span>
			<?php endfor; ?>
		</div>
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
	</header>

	<div class="murg-qantu__gallery">
		<?php foreach ( $qantu_imgs as $i => $img ) :
			$url = ! empty( $img['url'] ) ? $img['url'] : $img_upload . $qantu_fallbacks[ $i ];
			$alt = ! empty( $img['alt'] ) ? $img['alt'] : $qantu_titulo;
		?>
		<div class="murg-qantu__img">
			<img src="<?php echo esc_url( $url ); ?>"
			     alt="<?php echo esc_attr( $alt ); ?>"
			     loading="lazy">
		</div>
		<?php endforeach; ?>
	</div>

	<div class="murg-qantu__cta-wrap">
		<a href="<?php echo esc_url( $qantu_cta_url ); ?>" class="murg-btn murg-btn--dark">
			<?php echo esc_html( $qantu_cta_txt ); ?>
		</a>
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
<section class="murg-brands" aria-label="Marcas">
	<div class="murg-brands__track">
		<?php if ( empty( $brands ) ) : ?>
		<span class="murg-brands__wordmark">Djula</span>
		<span class="murg-brands__wordmark">Ti Sento</span>
		<span class="murg-brands__wordmark">Moraglione</span>
		<span class="murg-brands__wordmark">Perrelet</span>
		<span class="murg-brands__wordmark">Victorinox</span>
		<span class="murg-brands__wordmark">Baccarat</span>
		<?php else : ?>
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
		<?php endif; ?>
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
$visita_img_url  = ! empty( $visita_imagen['url'] ) ? $visita_imagen['url'] : $img_upload .'appointment.jpg';
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
     10 NEWSLETTER — -10% primera compra
     ============================================================ -->
<?php
$nl_titulo = murg_f( 'hp_nl_titulo', 'Recibe inspiración Murguía.' );
$nl_sub    = murg_f( 'hp_nl_sub',   'Historias, piezas seleccionadas y novedades de la casa, enviadas con calma y criterio.' );
$nl_form   = function_exists( 'murguia_newsletter_form_config' ) ? murguia_newsletter_form_config() : [
	'action'     => admin_url( 'admin-post.php' ),
	'method'     => 'post',
	'email_name' => 'email',
	'external'   => false,
];
?>
<section class="murg-newsletter" aria-label="Newsletter">
	<div class="murg-newsletter__inner">
		<h2 class="murg-newsletter__title"><?php echo esc_html( $nl_titulo ); ?></h2>
		<?php if ( $nl_sub ) : ?>
		<p class="murg-newsletter__sub"><?php echo esc_html( $nl_sub ); ?></p>
		<?php endif; ?>
		<form class="murg-newsletter__form" method="<?php echo esc_attr( $nl_form['method'] ); ?>" action="<?php echo esc_url( $nl_form['action'] ); ?>">
			<?php if ( empty( $nl_form['external'] ) ) : ?>
				<?php wp_nonce_field( 'murg_newsletter', 'murg_nl_nonce' ); ?>
				<input type="hidden" name="action" value="murg_newsletter_subscribe">
			<?php endif; ?>
			<div class="murg-newsletter__field">
				<input type="email" name="<?php echo esc_attr( $nl_form['email_name'] ); ?>"
				       placeholder="<?php esc_attr_e( 'Tu correo electrónico', 'woodmart-child' ); ?>"
				       required>
				<button type="submit"><?php esc_html_e( 'Suscribirme', 'woodmart-child' ); ?></button>
			</div>
			<?php if ( isset( $_GET['newsletter'] ) && 'ok' === $_GET['newsletter'] ) : ?>
			<p class="murg-newsletter__message">Gracias. Pronto recibirás novedades de Murguía.</p>
			<?php endif; ?>
		</form>
	</div>
</section>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
