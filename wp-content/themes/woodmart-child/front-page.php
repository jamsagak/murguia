<?php
/**
 * Front Page — Joyería Murguía
 *
 * Todo el contenido editable se lee desde el post "Página de Inicio"
 * (slug: pagina-de-inicio) del CPT murguia_ajustes.
 * Función de acceso: murguia_ajuste( 'campo', 'fallback' )
 * Repeaters:        have_rows( 'campo', murguia_ajuste_id() )
 */

// Atajo local: lee un campo del ajuste de la homepage.
function murg_f( $key, $fallback = '' ) {
	return murguia_ajuste( $key, $fallback );
}
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
$hero_eyebrow = murg_f( 'hp_hero_eyebrow',   'Colecci\xc3\xb3n Oto\xc3\xb1o \xc2\xb7 MMXXVI' );
$hero_titulo  = murg_f( 'hp_hero_titulo',     'Joyer\xc3\xada <em>Murgu\xc3\xada</em>' );
$hero_sub     = murg_f( 'hp_hero_subtitulo',  'Orfebrería peruana desde 1962' );
$hero_cta_txt = murg_f( 'hp_hero_cta_texto',  'Ver Colección' );
$hero_cta_url = murg_f( 'hp_hero_cta_link',   home_url( '/shop/' ) );

// Slider del hero — imágenes y/o videos
$hero_slides = [];
if ( function_exists( 'have_rows' ) && have_rows( 'hp_hero_slides', murguia_ajuste_id() ) ) {
	while ( have_rows( 'hp_hero_slides', murguia_ajuste_id() ) ) {
		the_row();
		$tipo        = get_sub_field( 'tipo' ) ?: 'imagen';
		$img         = get_sub_field( 'imagen' );
		$video_url   = trim( (string) get_sub_field( 'video_url' ) );
		$vid_inicio  = (int) ( get_sub_field( 'video_inicio' ) ?: 0 );
		$vid_fin_raw = get_sub_field( 'video_fin' );
		$vid_fin     = ( $vid_fin_raw !== '' && $vid_fin_raw !== null && $vid_fin_raw !== false )
		                ? (int) $vid_fin_raw : 15;

		// Descartar solo videos sin URL — las imagenes pueden estar vacias (placeholder)
		if ( $tipo === 'video' && empty( $video_url ) ) continue;

		// Parsear video: detectar YouTube, Vimeo o mp4 directo
		$video_embed = '';
		$video_mp4   = '';
		if ( $tipo === 'video' ) {
			if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video_url, $m ) ) {
				$vid = $m[1];
				$video_embed = 'https://www.youtube.com/embed/' . $vid
					. '?autoplay=1&mute=1&loop=1&playlist=' . $vid
					. '&controls=0&playsinline=1&rel=0&modestbranding=1&enablejsapi=1'
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

		// Duración en ms para el autoplay del slider
		$duracion_ms = $tipo === 'video' ? ( ( $vid_fin - $vid_inicio ) * 1000 ) : 5000;

		$hero_slides[] = [
			'tipo'        => $tipo,
			// Imagen
			'url'         => $img['url'] ?? '',
			'alt'         => $img['alt'] ?? '',
			// Video
			'video_embed' => $video_embed,
			'video_mp4'   => $video_mp4,
			'video_url'   => $video_url,
			'video_inicio'=> $vid_inicio,
			'video_fin'   => $vid_fin,
			// Timing
			'intervalo'   => $tipo === 'video' ? max( $duracion_ms, 2000 ) : 5000,
			// Contenido
			'eyebrow'     => (string) get_sub_field( 'eyebrow' ),
			'titulo'      => (string) get_sub_field( 'titulo' ),
			'subtitulo'   => (string) get_sub_field( 'subtitulo' ),
			'cta_texto'   => (string) get_sub_field( 'cta_texto' ),
			'cta_link'    => (string) get_sub_field( 'cta_link' ),
		];
	}
}
// Fallback: campo legado hp_hero_imagen
if ( empty( $hero_slides ) ) {
	$legacy = murg_f( 'hp_hero_imagen', [] );
	if ( ! empty( $legacy['url'] ) ) {
		$hero_slides[] = [
			'tipo' => 'imagen', 'url' => $legacy['url'], 'alt' => $legacy['alt'] ?? '',
			'video_embed' => '', 'video_mp4' => '', 'video_url' => '', 'intervalo' => 5000,
			'eyebrow' => '', 'titulo' => '', 'subtitulo' => '', 'cta_texto' => '', 'cta_link' => '',
		];
	}
}
?>
<section class="murg-hero" id="murg-hero-slider" aria-label="Hero">

	<?php foreach ( $hero_slides as $idx => $slide ) :
		// Contenido: usa el del slide si existe, si no el global
		$s_eyebrow   = $slide['eyebrow']   ?: $hero_eyebrow;
		$s_titulo    = $slide['titulo']    ?: $hero_titulo;
		$s_subtitulo = $slide['subtitulo'] ?: $hero_sub;
		$s_cta_txt   = $slide['cta_texto'] ?: $hero_cta_txt;
		$s_cta_url   = $slide['cta_link']  ?: $hero_cta_url;
	?>
	<div class="murg-hero__slide<?php echo $idx === 0 ? ' is-active' : ''; ?>"
	     data-intervalo="<?php echo (int) $slide['intervalo']; ?>"
	     <?php if ( $slide['tipo'] === 'video' ) : ?>
	     data-video-inicio="<?php echo (int) $slide['video_inicio']; ?>"
	     data-video-fin="<?php echo (int) $slide['video_fin']; ?>"
	     <?php endif; ?>
	     aria-hidden="<?php echo $idx === 0 ? 'false' : 'true'; ?>">

		<!-- Fondo -->
		<div class="murg-hero__bg">
			<?php if ( $slide['tipo'] === 'video' ) : ?>

				<?php if ( $slide['video_embed'] ) : ?>
				<div class="murg-hero__video-wrap">
					<iframe class="murg-hero__video-iframe"
					        src="<?php echo esc_url( $slide['video_embed'] ); ?>"
					        frameborder="0"
					        allow="autoplay; fullscreen"
					        allowfullscreen
					        loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>"
					        data-video-iframe></iframe>
				</div>
				<?php elseif ( $slide['video_mp4'] ) : ?>
				<video class="murg-hero__video-mp4"
				       src="<?php echo esc_url( $slide['video_mp4'] ); ?>"
				       autoplay muted playsinline
				       data-video-mp4
				       data-inicio="<?php echo (int) $slide['video_inicio']; ?>"
				       data-fin="<?php echo (int) $slide['video_fin']; ?>"></video>
				<?php endif; ?>

			<?php else : ?>
				<img class="murg-hero__img"
				     src="<?php echo esc_url( $slide['url'] ); ?>"
				     alt="<?php echo esc_attr( $slide['alt'] ); ?>"
				     <?php echo $idx > 0 ? 'loading="lazy"' : ''; ?>>
			<?php endif; ?>
			<div class="murg-hero__vignette"></div>
		</div>

		<!-- Contenido -->
		<div class="murg-hero__content">
			<div class="murg-eyebrow murg-hero__eyebrow"><?php echo esc_html( $s_eyebrow ); ?></div>
			<h1 class="murg-serif murg-hero__title"><?php echo wp_kses( $s_titulo, [ 'em' => [] ] ); ?></h1>
			<p class="murg-hero__sub"><?php echo esc_html( $s_subtitulo ); ?></p>
			<div class="murg-hero__divider" aria-hidden="true"></div>
			<a href="<?php echo esc_url( $s_cta_url ); ?>" class="murg-hero__cta">
				<?php echo esc_html( $s_cta_txt ); ?>
			</a>
		</div>

	</div>
	<?php endforeach; ?>

	<?php if ( empty( $hero_slides ) ) : ?>
	<div class="murg-hero__slide is-active" data-intervalo="5000" aria-hidden="false">
		<div class="murg-hero__bg"></div>
		<div class="murg-hero__content">
			<div class="murg-eyebrow murg-hero__eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></div>
			<h1 class="murg-serif murg-hero__title"><?php echo wp_kses( $hero_titulo, [ 'em' => [] ] ); ?></h1>
			<p class="murg-hero__sub"><?php echo esc_html( $hero_sub ); ?></p>
			<div class="murg-hero__divider" aria-hidden="true"></div>
			<a href="<?php echo esc_url( $hero_cta_url ); ?>" class="murg-hero__cta">
				<?php echo esc_html( $hero_cta_txt ); ?>
			</a>
		</div>
	</div>
	<?php endif; ?>

	<!-- Dots y progreso (fuera de los slides, siempre encima) -->
	<?php if ( count( $hero_slides ) > 1 ) : ?>
	<div class="murg-hero__dots" aria-label="Navegación de slides" role="tablist">
		<?php foreach ( $hero_slides as $idx => $slide ) : ?>
		<button class="murg-hero__dot<?php echo $idx === 0 ? ' is-active' : ''; ?>"
		        data-index="<?php echo $idx; ?>"
		        role="tab"
		        aria-selected="<?php echo $idx === 0 ? 'true' : 'false'; ?>"
		        aria-label="Slide <?php echo $idx + 1; ?> de <?php echo count( $hero_slides ); ?>">
			<span class="murg-hero__dot-line"></span>
		</button>
		<?php endforeach; ?>
	</div>
	<div class="murg-hero__progress" aria-hidden="true">
		<div class="murg-hero__progress-bar"></div>
	</div>
	<?php endif; ?>

	<div class="murg-hero__foot" aria-hidden="true">
		<div>Lima &middot; Per\xc3\xba</div>
	</div>
</section>

<!-- ============================================================
     02 COLECCIONES
     ============================================================ -->
<?php
$col_eyebrow = murg_f( 'hp_col_eyebrow', 'Colecciones Destacadas' );
$col_titulo  = murg_f( 'hp_col_titulo',  'Piezas que <em>perduran</em>' );

$colecciones = [];
if ( function_exists( 'have_rows' ) && have_rows( 'hp_col_items', murguia_ajuste_id() ) ) {
	while ( have_rows( 'hp_col_items', murguia_ajuste_id() ) ) {
		the_row();
		$colecciones[] = [
			'nombre'      => get_sub_field( 'nombre' )      ?: '',
			'descripcion' => get_sub_field( 'descripcion' ) ?: '',
			'numero'      => get_sub_field( 'numero' )      ?: '',
			'link'        => get_sub_field( 'link' )        ?: '#',
			'imagen'      => get_sub_field( 'imagen' )      ?: [],
		];
	}
}

// Demos predefinidos (se usan completos si ACF está vacío, o como relleno si ACF tiene menos de 3).
$demo_colecciones = [
	[ 'nombre' => 'Aurea',     'descripcion' => 'Anillos en oro 18k con incrustaciones',         'numero' => 'N° I',   'link' => '#', 'imagen' => [] ],
	[ 'nombre' => 'Pacha',     'descripcion' => 'Collares y gargantillas de inspiración andina', 'numero' => 'N° II',  'link' => '#', 'imagen' => [] ],
	[ 'nombre' => 'Solsticio', 'descripcion' => 'Brazaletes y pulseras de edición limitada',     'numero' => 'N° III', 'link' => '#', 'imagen' => [] ],
];

if ( empty( $colecciones ) ) {
	$colecciones = $demo_colecciones;
} else {
	// Completa hasta 3 con demos para que la sección nunca se vea incompleta.
	while ( count( $colecciones ) < 3 ) {
		$colecciones[] = $demo_colecciones[ count( $colecciones ) ];
	}
}

// Asigna un shape rotativo a cualquier colección sin imagen real.
$shape_cycle = [ 'ring', 'necklace', 'bracelet' ];
$label_cycle = [ 'Anillo · cierre macro', 'Collar · estudio', 'Brazalete · detalle' ];
foreach ( $colecciones as $i => &$col ) {
	if ( empty( $col['imagen']['url'] ) ) {
		if ( empty( $col['shape'] ) ) $col['shape'] = $shape_cycle[ $i % 3 ];
		if ( empty( $col['label'] ) ) $col['label'] = $label_cycle[ $i % 3 ];
	}
}
unset( $col );
?>
<section class="murg-section" id="colecciones" aria-label="Colecciones destacadas">
	<header class="murg-section__header">
		<div class="murg-eyebrow"><?php echo esc_html( $col_eyebrow ); ?></div>
		<h2 class="murg-section__title murg-serif"><?php echo wp_kses( $col_titulo, [ 'em' => [] ] ); ?></h2>
		<div class="murg-section__meta">
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Ver Todas →</a>
		</div>
	</header>

	<div class="murg-collections">
		<?php foreach ( $colecciones as $col ) :
			$shape = isset( $col['shape'] ) ? $col['shape'] : '';
			$label = isset( $col['label'] ) ? $col['label'] : '';
		?>
		<a class="murg-collection" href="<?php echo esc_url( $col['link'] ); ?>">
			<div class="murg-collection__img">
				<?php if ( ! empty( $col['imagen']['url'] ) ) : ?>
					<img src="<?php echo esc_url( $col['imagen']['url'] ); ?>"
					     alt="<?php echo esc_attr( $col['imagen']['alt'] ?? $col['nombre'] ); ?>">
				<?php elseif ( $shape ) : ?>
					<div class="murg-collection__placeholder murg-collection__placeholder--<?php echo esc_attr( $shape ); ?>" aria-hidden="true">
						<span class="murg-collection__shape"></span>
					</div>
					<?php if ( $label ) : ?>
						<span class="murg-collection__placeholder-label">[ <?php echo esc_html( $label ); ?> ]</span>
					<?php endif; ?>
				<?php endif; ?>
				<div class="murg-collection__num"><?php echo esc_html( $col['numero'] ); ?></div>
			</div>
			<div class="murg-collection__meta">
				<span class="murg-collection__name murg-serif"><?php echo esc_html( $col['nombre'] ); ?></span>
				<span class="murg-collection__arrow" aria-hidden="true">→</span>
			</div>
			<p class="murg-collection__desc"><?php echo esc_html( $col['descripcion'] ); ?></p>
		</a>
		<?php endforeach; ?>
	</div>
</section>

<!-- ============================================================
     03 BESTSELLERS (WooCommerce)
     ============================================================ -->
<?php
$best_eyebrow  = murg_f( 'hp_best_eyebrow',   'Más Codiciados' );
$best_titulo   = murg_f( 'hp_best_titulo',     'Los <em>esenciales</em>' );
$best_temporada = murg_f( 'hp_best_temporada', 'Otoño MMXXVI' );
$bestseller_ids = murg_f( 'hp_best_productos', [] );

// Fallback automático: productos más vendidos de WooCommerce.
if ( empty( $bestseller_ids ) && function_exists( 'wc_get_page_id' ) ) {
	$bs_query       = new WP_Query( [
		'post_type'      => 'product',
		'posts_per_page' => 9,
		'meta_key'       => 'total_sales',
		'orderby'        => 'meta_value_num',
		'order'          => 'DESC',
		'post_status'    => 'publish',
	] );
	$bestseller_ids = wp_list_pluck( $bs_query->posts, 'ID' );
	wp_reset_postdata();
}

$size_classes    = [ 'murg-product--primary', 'murg-product--secondary', 'murg-product--tertiary' ];
$per_slide       = 3;
$all_product_ids = array_slice( $bestseller_ids, 0, 9 );

// Construye un array unificado de items; usa demos si no hay productos WC.
$items = [];
foreach ( $all_product_ids as $pid ) {
	$p = wc_get_product( $pid );
	if ( ! $p ) continue;
	$img_id  = $p->get_image_id();
	$tags    = wc_get_product_terms( $pid, 'product_tag', [ 'fields' => 'names' ] );
	$sku     = $p->get_sku();
	$mat     = get_post_meta( $pid, '_murguia_material', true );
	$ref_lin = array_filter( [ $sku ? 'Ref. ' . $sku : '', $mat ] );
	$items[] = [
		'name'  => $p->get_name(),
		'price' => $p->get_price_html(),
		'url'   => $p->get_permalink(),
		'img'   => $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '',
		'alt'   => $img_id ? get_post_meta( $img_id, '_wp_attachment_image_alt', true ) : $p->get_name(),
		'tag'   => ! empty( $tags ) ? $tags[0] : '',
		'ref'   => $ref_lin ? implode( ' · ', $ref_lin ) : '',
		'shape' => '',
	];
}

// Fallback demo: 6 piezas con placeholders geométricos.
if ( empty( $items ) ) {
	$items = [
		[ 'name' => 'Aurea',     'price' => 'S/ 2,400', 'url' => '#', 'img' => '', 'alt' => '', 'tag' => 'Nuevo',   'ref' => 'Ref. MG-001 · Oro 18k',     'shape' => 'ring' ],
		[ 'name' => 'Pacha',     'price' => 'S/ 3,800', 'url' => '#', 'img' => '', 'alt' => '', 'tag' => '',        'ref' => 'Ref. MG-014 · Oro · Spondylus', 'shape' => 'necklace' ],
		[ 'name' => 'Solsticio', 'price' => 'S/ 1,950', 'url' => '#', 'img' => '', 'alt' => '', 'tag' => 'Edición', 'ref' => 'Ref. MG-022 · Plata 950',  'shape' => 'bracelet' ],
		[ 'name' => 'Luna',      'price' => 'S/ 1,200', 'url' => '#', 'img' => '', 'alt' => '', 'tag' => '',        'ref' => 'Ref. MG-031 · Oro 18k',    'shape' => 'earring' ],
		[ 'name' => 'Inca',      'price' => 'S/ 980',   'url' => '#', 'img' => '', 'alt' => '', 'tag' => '',        'ref' => 'Ref. MG-040 · Plata',      'shape' => 'cufflink' ],
		[ 'name' => 'Ñusta',     'price' => 'S/ 2,100', 'url' => '#', 'img' => '', 'alt' => '', 'tag' => 'Bodas',   'ref' => 'Ref. MG-052 · Oro · Perla', 'shape' => 'pendant' ],
	];
}

$total_products = count( $items );
$total_slides   = max( 1, (int) ceil( $total_products / $per_slide ) );
?>
<section class="murg-section murg-bestsellers" id="bestsellers" aria-label="Más vendidos">
	<header class="murg-section__header">
		<div class="murg-eyebrow"><?php echo esc_html( $best_eyebrow ); ?></div>
		<h2 class="murg-section__title murg-serif"><?php echo wp_kses( $best_titulo, [ 'em' => [] ] ); ?></h2>
		<div class="murg-section__meta">
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Toda la Tienda →</a>
		</div>
	</header>

	<div class="murg-products" data-slider="bestsellers">
		<div class="murg-products__track" id="murg-bs-track" data-total="<?php echo (int) $total_products; ?>">
			<?php
			$slides = array_chunk( $items, $per_slide );
			foreach ( $slides as $slide_idx => $slide_items ) :
				$slide_variant = $slide_idx === 0 ? 'asymmetric' : 'uniform';
			?>
			<div class="murg-products__slide murg-products__slide--<?php echo esc_attr( $slide_variant ); ?>">
				<?php foreach ( $slide_items as $pos => $it ) :
					$size_class = $slide_idx === 0 ? ( $size_classes[ $pos ] ?? '' ) : '';
				?>
				<article class="murg-product <?php echo esc_attr( $size_class ); ?>">
					<a href="<?php echo esc_url( $it['url'] ); ?>" class="murg-product__link">
						<div class="murg-product__img">
							<?php if ( $it['img'] ) : ?>
								<img src="<?php echo esc_url( $it['img'] ); ?>" alt="<?php echo esc_attr( $it['alt'] ); ?>" loading="lazy">
							<?php elseif ( ! empty( $it['shape'] ) ) : ?>
								<div class="murg-product__placeholder murg-product__placeholder--<?php echo esc_attr( $it['shape'] ); ?>" aria-hidden="true">
									<span class="murg-product__shape"></span>
								</div>
							<?php endif; ?>
							<?php if ( $it['tag'] ) : ?>
								<div class="murg-product__tag"><?php echo esc_html( $it['tag'] ); ?></div>
							<?php endif; ?>
						</div>
						<div class="murg-product__meta">
							<span class="murg-product__name murg-serif"><?php echo esc_html( $it['name'] ); ?></span>
							<span class="murg-product__price"><?php echo wp_kses_post( $it['price'] ); ?></span>
						</div>
						<?php if ( $it['ref'] ) : ?>
							<div class="murg-product__ref"><?php echo esc_html( $it['ref'] ); ?></div>
						<?php endif; ?>
					</a>
				</article>
				<?php endforeach; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="murg-bestsellers__foot">
		<div class="murg-eyebrow">
			<span id="murg-bs-info">1–<?php echo min( $per_slide, $total_products ); ?> de <?php echo $total_products; ?></span>
			piezas seleccionadas<?php if ( $best_temporada ) : ?> · <?php echo esc_html( $best_temporada ); ?><?php endif; ?>
		</div>
		<div class="murg-nav-arrows">
			<button class="murg-arrow-btn" id="murg-bs-prev" aria-label="Anterior"
			        <?php echo $total_slides <= 1 ? 'disabled' : ''; ?>>←</button>
			<button class="murg-arrow-btn" id="murg-bs-next" aria-label="Siguiente"
			        <?php echo $total_slides <= 1 ? 'disabled' : ''; ?>>→</button>
		</div>
	</div>
</section>

<!-- ============================================================
     04 STATEMENT
     ============================================================ -->
<?php
$stmt_eyebrow   = murg_f( 'hp_stmt_eyebrow',   'Nuestra Casa' );
$stmt_texto     = murg_f( 'hp_stmt_texto',      '"Cada pieza nace en nuestro taller en Lima, forjada a mano por orfebres que han pasado el oficio <em>de padres a hijos</em> durante seis décadas."' );
$stmt_atribucion = murg_f( 'hp_stmt_atribucion', '— Casa Murguía, Fundada en 1962' );
$stmt_imagen    = murg_f( 'hp_stmt_imagen',     [] );
?>
<section class="murg-statement" aria-label="<?php echo esc_attr( $stmt_eyebrow ); ?>"
	<?php if ( ! empty( $stmt_imagen['url'] ) ) : ?>
	style="background-image: url('<?php echo esc_url( $stmt_imagen['url'] ); ?>'); background-size: cover; background-position: center;"
	<?php endif; ?>
>
	<div class="murg-gold-line" aria-hidden="true"></div>
	<span class="murg-eyebrow"><?php echo esc_html( $stmt_eyebrow ); ?></span>
	<p class="murg-statement__quote murg-serif">
		<?php echo wp_kses( $stmt_texto, [ 'em' => [] ] ); ?>
	</p>
	<div class="murg-statement__attr"><?php echo esc_html( $stmt_atribucion ); ?></div>
	<div class="murg-gold-line" aria-hidden="true"></div>
</section>

<!-- ============================================================
     04.5 CERTIFICACIONES
     ============================================================ -->
<?php
$cert_titulo = murg_f( 'hp_cert_titulo', 'Certificados Internacionales' );
$cert_logos = [];
if ( function_exists( 'have_rows' ) && have_rows( 'hp_cert_logos', murguia_ajuste_id() ) ) {
	while ( have_rows( 'hp_cert_logos', murguia_ajuste_id() ) ) {
		the_row();
		$cert_logos[] = [
			'imagen' => get_sub_field( 'imagen' ),
			'link'   => get_sub_field( 'link' )
		];
	}
}
?>
<?php if ( ! empty( $cert_logos ) || current_user_can('edit_theme_options') ) : ?>
<section class="murg-certifications" aria-label="<?php echo esc_attr( $cert_titulo ); ?>">
	<div class="murg-eyebrow murg-certifications__title"><?php echo esc_html( $cert_titulo ); ?></div>
	<div class="murg-certifications__logos">
		<?php if ( ! empty( $cert_logos ) ) : ?>
			<?php foreach ( $cert_logos as $logo ) : ?>
				<?php if ( ! empty( $logo['imagen']['url'] ) ) : ?>
					<?php if ( ! empty( $logo['link'] ) ) : ?>
						<a href="<?php echo esc_url( $logo['link'] ); ?>" target="_blank" rel="noopener noreferrer" class="murg-certifications__logo">
					<?php else : ?>
						<div class="murg-certifications__logo">
					<?php endif; ?>
					
					<img src="<?php echo esc_url( $logo['imagen']['url'] ); ?>" alt="<?php echo esc_attr( $logo['imagen']['alt'] ?? '' ); ?>" loading="lazy">
					
					<?php if ( ! empty( $logo['link'] ) ) : ?>
						</a>
					<?php else : ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="murg-certifications__placeholder">[ Agrega los logos desde Ajustes Murguía > Inicio ]</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<!-- ============================================================
     05 CONTACTO
     ============================================================ -->
<?php
$cont_eyebrow  = murg_f( 'hp_cont_eyebrow',  'Visite el Atelier' );
$cont_titulo   = murg_f( 'hp_cont_titulo',   'Visítanos o<br/>agenda una <em>cita</em>' );
$cont_texto    = murg_f( 'hp_cont_texto',    'Recibimos a nuestros clientes con cita previa para una experiencia íntima y personalizada en el corazón de San Isidro. Servicio de diseño a medida disponible.' );
$cont_direccion = murg_f( 'hp_cont_direccion','Av. Camino Real 348<br/>San Isidro, Lima 27' );
$cont_horario  = murg_f( 'hp_cont_horario',  'Lun – Sáb · 10:00 – 19:00' );
$cont_telefono = murg_f( 'hp_cont_telefono', '+51 1 421 8800' );
$cont_email    = murg_f( 'hp_cont_email',    'atelier@murguia.pe' );
$cont_whatsapp = murg_f( 'hp_cont_whatsapp', 'https://wa.me/51114218800' );
$cont_servicios = murg_f( 'hp_cont_servicios', "Diseño a medida\nRestauración" );
$cont_serv_sub = murg_f( 'hp_cont_serv_sub', 'Presupuesto sin costo' );

// Renderiza los servicios: un item por línea.
$servicios_lines = array_filter( array_map( 'trim', explode( "\n", $cont_servicios ) ) );
$servicios_html  = implode( '<br/>', array_map( 'esc_html', $servicios_lines ) );
?>
<section class="murg-contact" id="contacto" aria-label="Contacto y citas">
	<div class="murg-contact__grid">

		<!-- INFO -->
		<div>
			<div class="murg-eyebrow" style="color:var(--murg-gold-soft); margin-bottom:24px;">
				<?php echo esc_html( $cont_eyebrow ); ?>
			</div>
			<h2 class="murg-contact__title murg-serif">
				<?php echo wp_kses( $cont_titulo, [ 'em' => [], 'br' => [] ] ); ?>
			</h2>
			<p class="murg-contact__lede"><?php echo wp_kses_post( $cont_texto ); ?></p>

			<div class="murg-contact__info">
				<div class="murg-info-block">
					<div class="murg-eyebrow">Dirección</div>
					<div class="murg-info-block__text murg-serif">
						<?php echo wp_kses( $cont_direccion, [ 'br' => [] ] ); ?>
					</div>
					<div class="murg-info-block__sub"><?php echo esc_html( $cont_horario ); ?></div>
				</div>
				<div class="murg-info-block">
					<div class="murg-eyebrow">Contacto</div>
					<div class="murg-info-block__text murg-serif">
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^+\d]/', '', $cont_telefono ) ); ?>" style="color:inherit;text-decoration:none;">
							<?php echo esc_html( $cont_telefono ); ?>
						</a><br/>
						<a href="mailto:<?php echo esc_attr( $cont_email ); ?>" style="color:inherit;text-decoration:none;">
							<?php echo esc_html( $cont_email ); ?>
						</a>
					</div>
					<div class="murg-info-block__sub">Respondemos en 24h</div>
				</div>
				<?php if ( $servicios_html ) : ?>
				<div class="murg-info-block">
					<div class="murg-eyebrow">Servicios</div>
					<div class="murg-info-block__text murg-serif"><?php echo $servicios_html; ?></div>
					<?php if ( $cont_serv_sub ) : ?>
						<div class="murg-info-block__sub"><?php echo esc_html( $cont_serv_sub ); ?></div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<div class="murg-info-block">
					<div class="murg-eyebrow">Newsletter</div>
					<div class="murg-info-block__text murg-serif">Boletín trimestral<br/>de la Casa</div>
					<div class="murg-info-block__sub">Próxima edición · Junio</div>
				</div>
			</div>
		</div>

		<!-- FORM -->
		<form class="murg-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'murg_cita', 'murg_nonce' ); ?>
			<input type="hidden" name="action" value="murg_solicitar_cita">

			<div class="murg-form__title murg-serif">Reserve su cita</div>
			<div class="murg-form__sub">Le contactaremos para confirmar el día y hora.</div>

			<div class="murg-field">
				<label for="murg-nombre">Nombre</label>
				<input id="murg-nombre" type="text" name="nombre" placeholder="Su nombre completo" required>
			</div>
			<div class="murg-field">
				<label for="murg-correo">Correo</label>
				<input id="murg-correo" type="email" name="correo" placeholder="ejemplo@correo.com" required>
			</div>
			<div class="murg-field">
				<label for="murg-telefono">Teléfono</label>
				<input id="murg-telefono" type="tel" name="telefono" placeholder="+51 ___ ___ ___">
			</div>
			<div class="murg-field">
				<label for="murg-interes">Interés</label>
				<select id="murg-interes" name="interes">
					<option>Asesoría general</option>
					<option>Anillo de compromiso</option>
					<option>Diseño a medida</option>
					<option>Restauración</option>
				</select>
			</div>
			<div class="murg-field" style="align-items:start;">
				<label for="murg-mensaje" style="padding-top:6px;">Mensaje</label>
				<textarea id="murg-mensaje" name="mensaje" rows="2"
				          placeholder="Cuéntenos brevemente..."
				          style="resize:none;padding-top:4px;"></textarea>
			</div>

			<div class="murg-form__actions">
				<button type="submit" class="murg-btn">Solicitar Cita</button>
				<?php if ( $cont_whatsapp ) : ?>
				<a href="<?php echo esc_url( $cont_whatsapp ); ?>"
				   class="murg-btn murg-btn--gold"
				   target="_blank" rel="noopener noreferrer"
				   style="display:flex;align-items:center;justify-content:center;">
					<span class="murg-whatsapp-dot" aria-hidden="true"></span>
					WhatsApp
				</a>
				<?php endif; ?>
			</div>
		</form>

	</div>
</section>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
