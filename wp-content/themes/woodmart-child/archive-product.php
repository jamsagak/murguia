<?php
/**
 * Joyería Murguía — Tienda / Catálogo
 * archive-product.php — Layout con panel de filtros
 */
defined( 'ABSPATH' ) || exit;

$per_page = max( 4, (int) murguia_ajuste( 'sh_por_pagina', 12, 'tienda' ) );
$shop_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop/' );

// --- Leer filtros desde GET ---
$paged        = max( 1, (int) ( get_query_var( 'paged' ) ?: get_query_var( 'page' ) ?: 1 ) );
$f_cat        = isset( $_GET['cat'] ) ? sanitize_key( $_GET['cat'] ) : ( isset( $_GET['product_cat'] ) ? sanitize_key( $_GET['product_cat'] ) : '' );
$f_piedra     = isset( $_GET['piedra'] )   ? sanitize_key( $_GET['piedra'] )   : '';
$f_forma      = isset( $_GET['forma'] )    ? sanitize_key( $_GET['forma'] )    : '';
$f_color      = isset( $_GET['color'] )    ? sanitize_key( $_GET['color'] )    : '';
$f_min        = isset( $_GET['min'] )      ? (float) $_GET['min']              : '';
$f_max        = isset( $_GET['max'] )      ? (float) $_GET['max']              : '';
$f_orderby    = isset( $_GET['orderby'] )  ? sanitize_key( $_GET['orderby'] )  : 'date';
$f_search     = isset( $_GET['s'] )        ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

// --- Build WC query ---
$query_args = [
	'status'     => 'publish',
	'limit'      => -1,
	'return'     => 'ids',
	'visibility' => 'visible',
	'orderby'    => 'date',
	'order'      => 'DESC',
];
if ( $f_search !== '' ) {
	$query_args['s'] = $f_search;
}
if ( $f_cat ) {
	$query_args['category'] = [ $f_cat ];
}

$tax_query = [];
if ( $f_piedra ) {
	$tax_query[] = [
		'taxonomy' => 'pa_piedra',
		'field'    => 'slug',
		'terms'    => $f_piedra,
	];
}
if ( $f_forma ) {
	$tax_query[] = [
		'taxonomy' => 'pa_forma-de-piedra',
		'field'    => 'slug',
		'terms'    => $f_forma,
	];
}
if ( $f_color ) {
	$tax_query[] = [
		'taxonomy' => 'pa_color-de-oro',
		'field'    => 'slug',
		'terms'    => $f_color,
	];
}
if ( ! empty( $tax_query ) ) {
	$query_args['tax_query'] = $tax_query;
}

switch ( $f_orderby ) {
	case 'price-asc':
		$query_args['orderby'] = 'price';
		$query_args['order']   = 'ASC';
		break;
	case 'price-desc':
		$query_args['orderby'] = 'price';
		$query_args['order']   = 'DESC';
		break;
	case 'popularity':
		$query_args['orderby'] = 'popularity';
		break;
}

$all_ids = wc_get_products( $query_args );

// Price filter (post-query for WC compatibility)
if ( $f_min !== '' || $f_max !== '' ) {
	$all_ids = array_filter( $all_ids, function ( $id ) use ( $f_min, $f_max ) {
		$price = (float) get_post_meta( $id, '_price', true );
		if ( $f_min !== '' && $price < $f_min ) return false;
		if ( $f_max !== '' && $price > $f_max ) return false;
		return true;
	} );
	$all_ids = array_values( $all_ids );
}

$total       = count( $all_ids );
$total_pages = max( 1, (int) ceil( $total / $per_page ) );
$page_ids    = array_slice( $all_ids, ( $paged - 1 ) * $per_page, $per_page );
$products    = array_filter( array_map( 'wc_get_product', $page_ids ) );

// --- Sidebar data ---
$categories = get_terms( [
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'orderby'    => 'count',
	'order'      => 'DESC',
	'exclude'    => [ (int) get_option( 'default_product_cat' ) ],
] );
$categories = is_wp_error( $categories ) ? [] : $categories;

// Categorías con orden manual: joyas primero, hogar/bautizo/bebés al final.
$ordered_cat_slugs = [
	'anillos-de-compromiso',
	'anillos',
	'aretes',
	'collares-y-dijes',
	'collares',
	'dijes',
	'pulseras',
	'alta-joyeria',
	'permanent-jewelry',
	'relojes',
	// --- No-joyería (al final) ---
	'hogar',
	'bautizo-y-confirmacion',
	'bebes',
];
$cat_index = array_flip( $ordered_cat_slugs );
$categories = array_filter( $categories, function( $c ) use ( $cat_index ) {
	return isset( $cat_index[ $c->slug ] );
} );
usort( $categories, function( $a, $b ) use ( $cat_index ) {
	return ( $cat_index[ $a->slug ] ?? 999 ) - ( $cat_index[ $b->slug ] ?? 999 );
} );

// Categorías que NO son joyería (no muestran filtro de piedra ni metal).
$non_jewelry_cats = [ 'hogar', 'bautizo-y-confirmacion', 'bebes', 'relojes' ];
$show_jewelry_filters = ! $f_cat || ! in_array( $f_cat, $non_jewelry_cats, true );

// Conteos contextuales: contar atributos solo dentro de los productos filtrados.
// Así al seleccionar una categoría, los conteos de piedra/metal reflejan esa selección.
function murg_contextual_terms( $taxonomy, $product_ids ) {
	if ( empty( $product_ids ) ) return [];
	global $wpdb;
	$ids_str = implode( ',', array_map( 'intval', $product_ids ) );
	$rows = $wpdb->get_results( $wpdb->prepare(
		"SELECT t.term_id, t.name, t.slug, COUNT(DISTINCT tr.object_id) AS count
		 FROM {$wpdb->term_relationships} tr
		 JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
		 JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
		 WHERE tt.taxonomy = %s
		   AND tr.object_id IN ({$ids_str})
		 GROUP BY t.term_id
		 ORDER BY count DESC",
		$taxonomy
	) );
	// Convertir a objetos con ->count int para compatibilidad con el template
	foreach ( $rows as &$r ) {
		$r->count    = (int) $r->count;
		$r->term_id  = (int) $r->term_id;
	}
	return $rows ?: [];
}

$piedras     = murg_contextual_terms( 'pa_piedra', $all_ids );
$formas      = murg_contextual_terms( 'pa_forma-de-piedra', $all_ids );
$colores_oro = murg_contextual_terms( 'pa_color-de-oro', $all_ids );

// Price range for slider
$price_min = 0;
$price_max = 45000;

// ACF
$sh_eyebrow   = murguia_ajuste( 'sh_eyebrow',   'Toda la Tienda', 'tienda' );
$sh_titulo    = murguia_ajuste( 'sh_titulo',     'Nuestra <em>Colección</em>', 'tienda' );
$sh_subtitulo = murguia_ajuste( 'sh_subtitulo',  '', 'tienda' );

// Active filters for display
$active_filters = [];
if ( $f_cat ) {
	$term = get_term_by( 'slug', $f_cat, 'product_cat' );
	if ( $term ) $active_filters[] = [ 'label' => $term->name, 'param' => 'cat' ];
}
if ( $f_piedra ) {
	$term = get_term_by( 'slug', $f_piedra, 'pa_piedra' );
	if ( $term ) $active_filters[] = [ 'label' => $term->name, 'param' => 'piedra' ];
}
if ( $f_forma ) {
	$term = get_term_by( 'slug', $f_forma, 'pa_forma-de-piedra' );
	if ( $term ) $active_filters[] = [ 'label' => $term->name, 'param' => 'forma' ];
}
if ( $f_color ) {
	$term = get_term_by( 'slug', $f_color, 'pa_color-de-oro' );
	if ( $term ) $active_filters[] = [ 'label' => $term->name, 'param' => 'color' ];
}
if ( $f_min !== '' || $f_max !== '' ) {
	$price_label = 'S/ ' . ( $f_min !== '' ? number_format( $f_min, 0 ) : '0' ) . ' – S/ ' . ( $f_max !== '' ? number_format( $f_max, 0 ) : number_format( $price_max, 0 ) );
	$active_filters[] = [ 'label' => $price_label, 'param' => 'price' ];
}
if ( $f_search ) {
	$active_filters[] = [ 'label' => 'Búsqueda: ' . $f_search, 'param' => 's' ];
}

// Build current filter URL
function murg_filter_url( $params_to_set = [], $params_to_remove = [] ) {
	global $shop_url;
	$current = $_GET;
	foreach ( $params_to_remove as $p ) {
		unset( $current[ $p ] );
	}
	unset( $current['paged'] );
	$current = array_merge( $current, $params_to_set );
	$current = array_filter( $current, function( $v ) { return $v !== '' && $v !== null; } );
	return empty( $current ) ? $shop_url : add_query_arg( $current, $shop_url );
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-shop woocommerce' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<!-- ============================================================
     SHOP LAYOUT
     ============================================================ -->
<div class="murg-shop-wrap">
<div class="murg-shop-layout">

	<!-- FILTER PANEL -->
	<aside class="murg-sidebar murg-sidebar--panel" id="murg-sidebar" aria-hidden="true">
		<div class="murg-sidebar__header">
			<h2 class="murg-sidebar__title">Filtros</h2>
			<button class="murg-sidebar__close" id="murg-sidebar-close" type="button" aria-label="Cerrar filtros">&times;</button>
		</div>

		<div class="murg-sidebar__count">
			<?php echo (int) $total; ?> Resultado<?php echo $total !== 1 ? 's' : ''; ?>
		</div>

		<?php if ( ! empty( $active_filters ) ) : ?>
		<div class="murg-active-filters">
			<?php foreach ( $active_filters as $af ) : ?>
			<a href="<?php echo esc_url( murg_filter_url( [], [ $af['param'], $af['param'] === 'price' ? 'min' : '', $af['param'] === 'price' ? 'max' : '' ] ) ); ?>"
			   class="murg-active-filter">
				<?php echo esc_html( $af['label'] ); ?> <span>&times;</span>
			</a>
			<?php endforeach; ?>
			<?php if ( count( $active_filters ) > 1 ) : ?>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="murg-active-filter murg-active-filter--clear">
				Limpiar todo
			</a>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<!-- Precio -->
		<div class="murg-filter-group murg-filter-group--static">
			<h3 class="murg-filter-group__title">Precio</h3>
			<div class="murg-filter-group__body">
				<div class="murg-price-slider" id="murg-price-slider">
					<div class="murg-price-slider__track">
						<div class="murg-price-slider__range" id="murg-price-range"></div>
						<input type="range" class="murg-price-slider__input" id="murg-price-min"
						       min="<?php echo (int) $price_min; ?>" max="<?php echo (int) $price_max; ?>"
						       step="50"
						       value="<?php echo $f_min !== '' ? (int) $f_min : (int) $price_min; ?>">
						<input type="range" class="murg-price-slider__input" id="murg-price-max"
						       min="<?php echo (int) $price_min; ?>" max="<?php echo (int) $price_max; ?>"
						       step="50"
						       value="<?php echo $f_max !== '' ? (int) $f_max : (int) $price_max; ?>">
					</div>
					<div class="murg-price-slider__values">
						<span id="murg-price-min-val">S/ <?php echo $f_min !== '' ? number_format( $f_min, 0 ) : number_format( $price_min, 0 ); ?></span>
						<span id="murg-price-max-val">S/ <?php echo $f_max !== '' ? number_format( $f_max, 0 ) : number_format( $price_max, 0 ); ?></span>
					</div>
				</div>
			</div>
		</div>

		<!-- Categoría: solo se muestra cuando NO hay categoría seleccionada -->
		<?php if ( ! $f_cat && ! empty( $categories ) ) : ?>
		<details class="murg-filter-group" open>
			<summary class="murg-filter-group__title">Categoría</summary>
			<div class="murg-filter-group__body">
				<?php foreach ( $categories as $cat ) : ?>
				<label class="murg-filter-check">
					<input type="checkbox"
					       name="cat"
					       value="<?php echo esc_attr( $cat->slug ); ?>"
					       <?php checked( $f_cat, $cat->slug ); ?>
					       onchange="murgApplyFilter('cat', this.checked ? this.value : '')">
					<span class="murg-filter-check__box"></span>
					<span class="murg-filter-check__label"><?php echo esc_html( $cat->name ); ?></span>
					<span class="murg-filter-check__count"><?php echo (int) $cat->count; ?></span>
				</label>
				<?php endforeach; ?>
			</div>
		</details>
		<?php endif; ?>

		<?php
		// Subcategorías: si hay categoría activa, mostrar sus hijas como filtro
		if ( $f_cat ) :
			$active_term = get_term_by( 'slug', $f_cat, 'product_cat' );
			$subcategories = [];
			if ( $active_term && ! is_wp_error( $active_term ) ) {
				$subcategories = get_terms( [
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'parent'     => $active_term->term_id,
					'orderby'    => 'name',
					'order'      => 'ASC',
				] );
				if ( is_wp_error( $subcategories ) ) $subcategories = [];
			}
			if ( ! empty( $subcategories ) ) :
		?>
		<details class="murg-filter-group" open>
			<summary class="murg-filter-group__title"><?php echo esc_html( $active_term->name ); ?></summary>
			<div class="murg-filter-group__body">
				<?php foreach ( $subcategories as $subcat ) : ?>
				<label class="murg-filter-check">
					<input type="checkbox"
					       name="cat"
					       value="<?php echo esc_attr( $subcat->slug ); ?>"
					       <?php checked( $f_cat, $subcat->slug ); ?>
					       onchange="murgApplyFilter('cat', this.checked ? this.value : '')">
					<span class="murg-filter-check__box"></span>
					<span class="murg-filter-check__label"><?php echo esc_html( $subcat->name ); ?></span>
					<span class="murg-filter-check__count"><?php echo (int) $subcat->count; ?></span>
				</label>
				<?php endforeach; ?>
			</div>
		</details>
		<?php endif; endif; ?>

		<!-- Forma de piedra -->
		<?php if ( ! empty( $formas ) && $show_jewelry_filters ) : ?>
		<details class="murg-filter-group" <?php echo ( $f_cat || $f_forma ) ? 'open' : ''; ?>>
			<summary class="murg-filter-group__title">Forma</summary>
			<div class="murg-filter-group__body murg-filter-group__body--scroll">
				<?php foreach ( $formas as $forma ) : ?>
				<label class="murg-filter-check">
					<input type="checkbox"
					       name="forma"
					       value="<?php echo esc_attr( $forma->slug ); ?>"
					       <?php checked( $f_forma, $forma->slug ); ?>
					       onchange="murgApplyFilter('forma', this.checked ? this.value : '')">
					<span class="murg-filter-check__box"></span>
					<span class="murg-filter-check__label"><?php echo esc_html( $forma->name ); ?></span>
					<span class="murg-filter-check__count"><?php echo (int) $forma->count; ?></span>
				</label>
				<?php endforeach; ?>
			</div>
		</details>
		<?php endif; ?>

		<!-- Piedra (solo si la categoría es joyería o no hay categoría) -->
		<?php if ( ! empty( $piedras ) && $show_jewelry_filters ) : ?>
		<details class="murg-filter-group" <?php echo ( $f_cat || $f_piedra ) ? 'open' : ''; ?>>
			<summary class="murg-filter-group__title">Piedra</summary>
			<div class="murg-filter-group__body murg-filter-group__body--scroll">
				<?php foreach ( $piedras as $piedra ) : ?>
				<label class="murg-filter-check">
					<input type="checkbox"
					       name="piedra"
					       value="<?php echo esc_attr( $piedra->slug ); ?>"
					       <?php checked( $f_piedra, $piedra->slug ); ?>
					       onchange="murgApplyFilter('piedra', this.checked ? this.value : '')">
					<span class="murg-filter-check__box"></span>
					<span class="murg-filter-check__label"><?php echo esc_html( $piedra->name ); ?></span>
					<span class="murg-filter-check__count"><?php echo (int) $piedra->count; ?></span>
				</label>
				<?php endforeach; ?>
			</div>
		</details>
		<?php endif; ?>

		<!-- Color de Oro (solo si la categoría es joyería) -->
		<?php if ( ! empty( $colores_oro ) && $show_jewelry_filters ) : ?>
		<details class="murg-filter-group" <?php echo ( $f_cat || $f_color ) ? 'open' : ''; ?>>
			<summary class="murg-filter-group__title">Metal</summary>
			<div class="murg-filter-group__body">
				<?php
				$color_swatches = [
					'amarillo'  => '#D4A843',
					'blanco'    => '#E8E4DC',
					'rosado'    => '#E8B4A0',
					'combinado' => 'linear-gradient(135deg, #D4A843 50%, #E8E4DC 50%)',
				];
				foreach ( $colores_oro as $color ) :
					$swatch = $color_swatches[ $color->slug ] ?? '#ccc';
					$is_gradient = strpos( $swatch, 'gradient' ) !== false;
				?>
				<label class="murg-filter-swatch <?php echo $f_color === $color->slug ? 'is-active' : ''; ?>">
					<input type="checkbox"
					       name="color"
					       value="<?php echo esc_attr( $color->slug ); ?>"
					       <?php checked( $f_color, $color->slug ); ?>
					       onchange="murgApplyFilter('color', this.checked ? this.value : '')"
					       class="murg-sr-only">
					<span class="murg-filter-swatch__circle"
					      style="<?php echo $is_gradient ? 'background:' . $swatch : 'background-color:' . $swatch; ?>">
					</span>
					<span class="murg-filter-swatch__label"><?php echo esc_html( $color->name ); ?></span>
				</label>
				<?php endforeach; ?>
			</div>
		</details>
		<?php endif; ?>

		<!-- Ordenar -->
		<details class="murg-filter-group" <?php echo $f_cat ? 'open' : ''; ?>>
			<summary class="murg-filter-group__title">Ordenar por</summary>
			<div class="murg-filter-group__body">
				<?php
				$sort_options = [
					'date'       => 'Más recientes',
					'popularity' => 'Más vendidos',
					'price-asc'  => 'Precio: menor a mayor',
					'price-desc' => 'Precio: mayor a menor',
				];
				foreach ( $sort_options as $val => $label ) :
				?>
				<label class="murg-filter-check">
					<input type="radio"
					       name="orderby"
					       value="<?php echo esc_attr( $val ); ?>"
					       <?php checked( $f_orderby, $val ); ?>
					       onchange="murgApplyFilter('orderby', this.value)">
					<span class="murg-filter-check__radio"></span>
					<span class="murg-filter-check__label"><?php echo esc_html( $label ); ?></span>
				</label>
				<?php endforeach; ?>
			</div>
		</details>

	</aside>

	<!-- MAIN CONTENT -->
	<div class="murg-shop-content">

		<!-- Top bar -->
		<div class="murg-shop-topbar">
			<div class="murg-shop-topbar__left">
				<button class="murg-filter-toggle" id="murg-filter-toggle" aria-label="Mostrar filtros">
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M1 3h14M3 8h10M5 13h6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
					Filtros
				</button>
				<span class="murg-shop-topbar__count"><?php echo (int) $total; ?> producto<?php echo $total !== 1 ? 's' : ''; ?></span>
			</div>
			<div class="murg-shop-topbar__right">
				<div class="murg-eyebrow">
					<?php if ( $f_search ) : ?>
						Búsqueda: «<?php echo esc_html( $f_search ); ?>»
					<?php else : ?>
						<?php echo esc_html( $sh_eyebrow ); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Products grid -->
		<?php if ( ! empty( $products ) ) : ?>
		<div class="murg-shop-grid">
			<?php foreach ( $products as $product ) :
				$img_id     = $product->get_image_id();
				$gallery    = $product->get_gallery_image_ids();
				$hover_img  = ! empty( $gallery ) ? (int) $gallery[0] : 0;
				$has_hover  = $hover_img && $hover_img !== $img_id;
				$is_sale    = $product->is_on_sale();
				$is_new     = $product->is_featured();
				$img_classes = 'murg-product__img' . ( $has_hover ? ' murg-product__img--has-hover' : '' );
			?>
			<article class="murg-product murg-product--grid">
				<a class="murg-product__link" href="<?php echo esc_url( $product->get_permalink() ); ?>">
					<div class="<?php echo esc_attr( $img_classes ); ?>">
						<?php if ( $img_id ) :
							echo wp_get_attachment_image( $img_id, 'full', false, [
								'loading' => 'lazy',
								'alt'     => $product->get_name(),
								'sizes'   => '(max-width: 480px) 100vw, (max-width: 1024px) 50vw, 33vw',
								'class'   => 'murg-product__img-main',
							] );
						else : ?>
							<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>"
							     alt="<?php echo esc_attr( $product->get_name() ); ?>"
							     loading="lazy"
							     class="murg-product__img-main">
						<?php endif; ?>

						<?php if ( $has_hover ) :
							echo wp_get_attachment_image( $hover_img, 'full', false, [
								'loading' => 'lazy',
								'alt'     => '',
								'aria-hidden' => 'true',
								'sizes'   => '(max-width: 480px) 100vw, (max-width: 1024px) 50vw, 33vw',
								'class'   => 'murg-product__img-hover',
							] );
						endif; ?>

						<?php if ( $is_sale ) : ?>
						<span class="murg-product__tag">
							<?php echo esc_html( murguia_ajuste( 'prod_badge_oferta', 'Oferta', 'producto' ) ); ?>
						</span>
						<?php elseif ( $is_new ) : ?>
						<span class="murg-product__tag">
							<?php echo esc_html( murguia_ajuste( 'prod_badge_nuevo', 'Nuevo', 'producto' ) ); ?>
						</span>
						<?php endif; ?>
					</div>

					<div class="murg-product__meta">
						<h2 class="murg-product__name"><?php echo esc_html( $product->get_name() ); ?></h2>
						<div class="murg-product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
					</div>
				</a>
			</article>
			<?php endforeach; ?>
		</div>

		<?php if ( $total_pages > 1 ) :
			// Base URL preservando filtros activos
			$base_args = array_filter( [
				'cat'     => $f_cat,
				'piedra'  => $f_piedra,
				'forma'   => $f_forma,
				'color'   => $f_color,
				'min'     => $f_min !== '' ? $f_min : null,
				'max'     => $f_max !== '' ? $f_max : null,
				'orderby' => $f_orderby !== 'date' ? $f_orderby : null,
			], function( $v ) { return $v !== null && $v !== ''; } );

			$pag_links = paginate_links( [
				'base'      => trailingslashit( $shop_url ) . 'page/%#%/',
				'format'    => '',
				'current'   => $paged,
				'total'     => $total_pages,
				'mid_size'  => 1,
				'end_size'  => 1,
				'prev_text' => '&lsaquo;',
				'next_text' => '&rsaquo;',
				'type'      => 'array',
				'add_args'  => $base_args,
			] );
		?>
		<?php if ( $pag_links ) : ?>
		<nav class="murg-pagination" aria-label="Paginación de tienda">
			<?php foreach ( $pag_links as $link ) :
				// paginate_links() genera class="prev page-numbers" | "page-numbers current" | "next page-numbers" | "page-numbers dots"
				// Normalizamos a BEM .murg-pagination__item + modifiers.
				$link = preg_replace_callback(
					'/class="([^"]*)"/',
					function ( $m ) {
						$raw     = $m[1];
						$modifiers = [];
						if ( false !== strpos( $raw, 'current' ) ) $modifiers[] = 'is-active';
						if ( false !== strpos( $raw, 'dots' ) )    $modifiers[] = 'murg-pagination__item--dots';
						if ( false !== strpos( $raw, 'prev' ) )    $modifiers[] = 'murg-pagination__item--prev';
						if ( false !== strpos( $raw, 'next' ) )    $modifiers[] = 'murg-pagination__item--next';
						$all = array_merge( [ 'murg-pagination__item' ], $modifiers );
						return 'class="' . implode( ' ', $all ) . '"';
					},
					$link
				);
				echo $link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — output controlado por paginate_links()
			endforeach; ?>
		</nav>
		<?php endif; endif; ?>

		<?php else : ?>
		<div class="murg-shop-empty">
			<p class="murg-eyebrow">Sin resultados</p>
			<p style="margin-top:16px;color:var(--murg-muted)">No encontramos productos con los filtros seleccionados.</p>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="murg-btn" style="margin-top:32px;display:inline-block;width:auto;padding:14px 40px;">
				Limpiar filtros
			</a>
		</div>
		<?php endif; ?>

	</div>
</div>
</div>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
