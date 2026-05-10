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
		<div>Lima &middot; Perú</div>
	</div>
</section>

<!-- ============================================================
     02 COLECCIONES (grid estilo Ti Sento)
     ============================================================ -->
<?php
$col_eyebrow = murg_f( 'hp_col_eyebrow', 'Colecciones Destacadas' );
$col_titulo  = murg_f( 'hp_col_titulo',  'Piezas que <em>perduran</em>' );

$colecciones = [];
if ( function_exists( 'have_rows' ) && have_rows( 'hp_col_items', murguia_ajuste_id() ) ) {
	while ( have_rows( 'hp_col_items', murguia_ajuste_id() ) ) {
		the_row();
		$colecciones[] = [
			'decorativo' => (bool) get_sub_field( 'es_decorativo' ),
			'nombre'     => get_sub_field( 'nombre' )    ?: '',
			'imagen'     => get_sub_field( 'imagen' )    ?: [],
			'link'       => get_sub_field( 'link' )      ?: '',
			'cta_texto'  => get_sub_field( 'cta_texto' ) ?: '',
		];
	}
}

// Fallback demos si ACF está vacío
if ( empty( $colecciones ) ) {
	$colecciones = [
		[ 'decorativo' => false, 'nombre' => 'Anillos de Compromiso', 'imagen' => [], 'link' => home_url( '/shop/?product_cat=anillos-de-compromiso' ), 'cta_texto' => 'Ver Colección' ],
		[ 'decorativo' => true,  'nombre' => '',                      'imagen' => [], 'link' => '',                                                      'cta_texto' => '' ],
		[ 'decorativo' => false, 'nombre' => 'Aretes',                'imagen' => [], 'link' => home_url( '/shop/?product_cat=aretes' ),                 'cta_texto' => 'Ver Aretes' ],
		[ 'decorativo' => false, 'nombre' => 'Collares & Dijes',      'imagen' => [], 'link' => home_url( '/shop/?product_cat=collares-y-dijes' ),       'cta_texto' => 'Ver Collares' ],
		[ 'decorativo' => false, 'nombre' => 'Pulseras',              'imagen' => [], 'link' => home_url( '/shop/?product_cat=pulseras' ),               'cta_texto' => 'Ver Pulseras' ],
		[ 'decorativo' => false, 'nombre' => 'Relojes',               'imagen' => [], 'link' => home_url( '/shop/?product_cat=relojes' ),                'cta_texto' => 'Ver Relojes' ],
	];
}

$top_items    = array_slice( $colecciones, 0, 2 );
$bottom_items = array_slice( $colecciones, 2, 4 );
?>
<section class="murg-section murg-ts-section" id="colecciones" aria-label="Colecciones destacadas">

	<header class="murg-section__header">
		<div class="murg-eyebrow"><?php echo esc_html( $col_eyebrow ); ?></div>
		<h2 class="murg-section__title murg-serif"><?php echo wp_kses( $col_titulo, [ 'em' => [] ] ); ?></h2>
		<div class="murg-section__meta">
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Ver Todas →</a>
		</div>
	</header>

	<?php if ( ! empty( $top_items ) ) : ?>
	<!-- Fila grande: 2 bloques cuadrados -->
	<div class="murg-ts-grid murg-ts-grid--top">
		<?php foreach ( $top_items as $idx => $col ) :
			$is_deco  = $col['decorativo'];
			$has_link = ! empty( $col['link'] );
			$tag      = ( ! $is_deco && $has_link ) ? 'a' : 'div';
			$href     = ( $tag === 'a' ) ? ' href="' . esc_url( $col['link'] ) . '"' : '';
		?>
		<<?php echo $tag; ?> class="murg-ts-block<?php echo $is_deco ? ' murg-ts-block--deco' : ''; ?>"<?php echo $href; ?><?php if ( $col['nombre'] ) : ?> aria-label="<?php echo esc_attr( $col['nombre'] ); ?>"<?php endif; ?>>
			<div class="murg-ts-block__img">
				<?php if ( ! empty( $col['imagen']['url'] ) ) : ?>
					<img src="<?php echo esc_url( $col['imagen']['sizes']['large'] ?? $col['imagen']['url'] ); ?>"
					     alt="<?php echo esc_attr( $col['imagen']['alt'] ?? $col['nombre'] ); ?>"
					     loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>">
				<?php else : ?>
					<div class="murg-ts-block__placeholder">[ <?php echo esc_html( $col['nombre'] ?: 'Imagen' ); ?> ]</div>
				<?php endif; ?>
			</div>
			<?php if ( ! $is_deco && ( $col['nombre'] || $col['cta_texto'] ) ) : ?>
			<div class="murg-ts-block__content murg-ts-block__content--left">
				<?php if ( $col['nombre'] ) : ?>
					<h3 class="murg-ts-block__title"><?php echo esc_html( $col['nombre'] ); ?></h3>
				<?php endif; ?>
				<?php if ( $col['cta_texto'] ) : ?>
					<span class="murg-ts-block__cta"><?php echo esc_html( $col['cta_texto'] ); ?></span>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</<?php echo $tag; ?>>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $bottom_items ) ) : ?>
	<!-- Fila chica: hasta 4 bloques portrait -->
	<div class="murg-ts-grid murg-ts-grid--bottom">
		<?php foreach ( $bottom_items as $col ) :
			$is_deco  = $col['decorativo'];
			$has_link = ! empty( $col['link'] );
			$tag      = ( ! $is_deco && $has_link ) ? 'a' : 'div';
			$href     = ( $tag === 'a' ) ? ' href="' . esc_url( $col['link'] ) . '"' : '';
		?>
		<<?php echo $tag; ?> class="murg-ts-block<?php echo $is_deco ? ' murg-ts-block--deco' : ''; ?>"<?php echo $href; ?><?php if ( $col['nombre'] ) : ?> aria-label="<?php echo esc_attr( $col['nombre'] ); ?>"<?php endif; ?>>
			<div class="murg-ts-block__img">
				<?php if ( ! empty( $col['imagen']['url'] ) ) : ?>
					<img src="<?php echo esc_url( $col['imagen']['sizes']['medium_large'] ?? $col['imagen']['url'] ); ?>"
					     alt="<?php echo esc_attr( $col['imagen']['alt'] ?? $col['nombre'] ); ?>"
					     loading="lazy">
				<?php else : ?>
					<div class="murg-ts-block__placeholder">[ <?php echo esc_html( $col['nombre'] ?: 'Imagen' ); ?> ]</div>
				<?php endif; ?>
			</div>
			<?php if ( ! $is_deco && ( $col['nombre'] || $col['cta_texto'] ) ) : ?>
			<div class="murg-ts-block__content murg-ts-block__content--center">
				<?php if ( $col['nombre'] ) : ?>
					<h3 class="murg-ts-block__title"><?php echo esc_html( $col['nombre'] ); ?></h3>
				<?php endif; ?>
				<?php if ( $col['cta_texto'] ) : ?>
					<span class="murg-ts-block__cta"><?php echo esc_html( $col['cta_texto'] ); ?></span>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</<?php echo $tag; ?>>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

</section>
	
	
	
	
	
<!-- ============================================================
     03 BESTSELLERS (WooCommerce)
     ============================================================ -->
<?php
$best_eyebrow  = murg_f( 'hp_best_eyebrow',   'Más Codiciados' );
$best_titulo   = murg_f( 'hp_best_titulo',     'Los <em>esenciales</em>' );
$best_temporada = murg_f( 'hp_best_temporada', 'Otoño MMXXVI' );
$bestseller_ids = murg_f( 'hp_best_productos', [] );

// Fallback automático: un producto por categoría (el más vendido de cada una).
if ( empty( $bestseller_ids ) && function_exists( 'wc_get_page_id' ) ) {

	// Whitelist: solo estas categorías aparecen en bestsellers
	$cats_permitir = [ 'anillos-de-compromiso', 'anillos', 'aretes', 'pulseras', 'alta-joyeria' ];
	$target_total  = 12;

	$bestseller_ids = [];
	$usados_ids     = [];

	// Ronda 1: el más vendido de cada categoría (1 por cat)
	foreach ( $cats_permitir as $cat_slug ) {
		$cat_query = new WP_Query( [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 10,
			'tax_query'      => [ [
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $cat_slug,
			] ],
			'meta_key'       => 'total_sales',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'fields'         => 'ids',
		] );

		if ( $cat_query->have_posts() ) {
			foreach ( $cat_query->posts as $pid ) {
				if ( in_array( $pid, $usados_ids, true ) ) continue;
				$p = wc_get_product( $pid );
				if ( ! $p || ! $p->is_in_stock() || ! $p->is_visible() ) continue;
				$bestseller_ids[] = $pid;
				$usados_ids[]     = $pid;
				break;
			}
		}
		wp_reset_postdata();
	}

	// Ronda 2+: seguir llenando desde las mismas categorías hasta completar 12
	$ronda = 0;
	while ( count( $bestseller_ids ) < $target_total && $ronda < 10 ) {
		$ronda++;
		$added_this_round = false;

		foreach ( $cats_permitir as $cat_slug ) {
			if ( count( $bestseller_ids ) >= $target_total ) break;

			$cat_query = new WP_Query( [
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => $target_total,
				'tax_query'      => [ [
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $cat_slug,
				] ],
				'meta_key'       => 'total_sales',
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
				'fields'         => 'ids',
				'post__not_in'   => $usados_ids,
			] );

			if ( $cat_query->have_posts() ) {
				foreach ( $cat_query->posts as $pid ) {
					if ( count( $bestseller_ids ) >= $target_total ) break;
					if ( in_array( $pid, $usados_ids, true ) ) continue;
					$p = wc_get_product( $pid );
					if ( ! $p || ! $p->is_in_stock() || ! $p->is_visible() ) continue;
					$bestseller_ids[] = $pid;
					$usados_ids[]     = $pid;
					$added_this_round = true;
				}
			}
			wp_reset_postdata();
		}

		// Si no se agregó nada en esta ronda, no hay más productos disponibles
		if ( ! $added_this_round ) break;
	}
	
	
	
	
	
	// Si por alguna razón quedaron menos de 3, completar con los más vendidos globales
	if ( count( $bestseller_ids ) < 3 ) {
		$fill_query = new WP_Query( [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => 'total_sales',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'post__not_in'   => $bestseller_ids,
		] );
		foreach ( $fill_query->posts as $pid ) {
			if ( in_array( $pid, $bestseller_ids, true ) ) continue;
			$bestseller_ids[] = $pid;
		}
		wp_reset_postdata();
	}
}

$size_classes    = [ 'murg-product--primary', 'murg-product--secondary', 'murg-product--tertiary' ];
$per_slide       = 3;
$all_product_ids = $bestseller_ids; // sin límite — todas las categorías disponibles

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
								<img src="<?php echo esc_url( $it['img'] ); ?>" alt="<?php echo esc_attr( $it['alt'] ); ?>" loading="lazy" draggable="false">
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
	<h2 class="murg-certifications__title murg-serif"><?php echo esc_html( $cert_titulo ); ?></h2>
	<div class="murg-certifications__carousel" id="cert-carousel">
		<div class="murg-certifications__track">
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
