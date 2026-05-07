<?php
/**
 * Joyería Murguía — Tienda / Catálogo
 * archive-product.php
 */
defined( 'ABSPATH' ) || exit;

// Pagination and filters
$paged    = max( 1, (int) ( get_query_var( 'paged' ) ?: get_query_var( 'page' ) ?: 1 ) );
$per_page = max( 4, (int) murguia_ajuste( 'sh_por_pagina', 12, 'tienda' ) );
$cat_slug = isset( $_GET['cat'] ) ? sanitize_key( $_GET['cat'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

// Get all matching IDs for pagination count (cheap: ids only)
$count_args = [
	'status'     => 'publish',
	'limit'      => -1,
	'return'     => 'ids',
	'visibility' => 'visible',
	'orderby'    => 'date',
	'order'      => 'DESC',
];
if ( $cat_slug ) {
	$count_args['category'] = [ $cat_slug ];
}
$all_ids     = wc_get_products( $count_args );
$total       = count( $all_ids );
$total_pages = max( 1, (int) ceil( $total / $per_page ) );
$page_ids    = array_slice( $all_ids, ( $paged - 1 ) * $per_page, $per_page );
$products    = array_filter( array_map( 'wc_get_product', $page_ids ) );

// Category filter
$show_filters = (bool) murguia_ajuste( 'sh_mostrar_filtros', true, 'tienda' );
$categories   = [];
if ( $show_filters ) {
	$cats = get_terms( [
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
		'exclude'    => [ (int) get_option( 'default_product_cat' ) ],
	] );
	$categories = is_wp_error( $cats ) ? [] : $cats;
}

// ACF
$sh_eyebrow   = murguia_ajuste( 'sh_eyebrow',   'Toda la Tienda', 'tienda' );
$sh_titulo    = murguia_ajuste( 'sh_titulo',     'Nuestra <em>Colección</em>', 'tienda' );
$sh_subtitulo = murguia_ajuste( 'sh_subtitulo',  '', 'tienda' );

$shop_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/tienda/' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php woocommerce_page_title(); ?> · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-shop woocommerce' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<!-- ============================================================
     ENCABEZADO DE PÁGINA
     ============================================================ -->
<header class="murg-page-header">
	<div class="murg-eyebrow"><?php echo esc_html( $sh_eyebrow ); ?></div>
	<h1 class="murg-serif murg-page-header__title">
		<?php echo wp_kses( $sh_titulo, [ 'em' => [] ] ); ?>
	</h1>
	<?php if ( $sh_subtitulo ) : ?>
	<p class="murg-page-header__sub"><?php echo esc_html( $sh_subtitulo ); ?></p>
	<?php endif; ?>
</header>

<!-- ============================================================
     FILTROS DE CATEGORÍA
     ============================================================ -->
<?php if ( $show_filters && ! empty( $categories ) ) : ?>
<nav class="murg-filter" aria-label="Filtrar por categoría">
	<a href="<?php echo esc_url( $shop_url ); ?>"
	   class="murg-filter__item <?php echo $cat_slug ? '' : 'is-active'; ?>">
		Todo
	</a>
	<?php foreach ( $categories as $cat ) : ?>
	<a href="<?php echo esc_url( add_query_arg( 'cat', $cat->slug, $shop_url ) ); ?>"
	   class="murg-filter__item <?php echo $cat_slug === $cat->slug ? 'is-active' : ''; ?>">
		<?php echo esc_html( $cat->name ); ?>
	</a>
	<?php endforeach; ?>
</nav>
<?php endif; ?>

<!-- ============================================================
     PRODUCTOS
     ============================================================ -->
<main class="murg-shop-main" id="main">
<?php if ( ! empty( $products ) ) : ?>

	<div class="murg-shop-grid">
		<?php foreach ( $products as $product ) :
			$img_id  = $product->get_image_id();
			$is_sale = $product->is_on_sale();
			$is_new  = $product->is_featured();
			$sku     = $product->get_sku();
		?>
		<article class="murg-product murg-product--grid">
			<a class="murg-product__link" href="<?php echo esc_url( $product->get_permalink() ); ?>">
				<div class="murg-product__img">
					<?php if ( $img_id ) :
						echo wp_get_attachment_image( $img_id, 'large', false, [
							'loading' => 'lazy',
							'alt'     => $product->get_name(),
						] );
					else : ?>
						<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>"
						     alt="<?php echo esc_attr( $product->get_name() ); ?>"
						     loading="lazy">
					<?php endif; ?>

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
				<?php if ( $sku ) : ?>
				<p class="murg-product__ref">
					<?php echo esc_html( murguia_ajuste( 'prod_ref_prefix', 'REF.', 'producto' ) ); ?>
					<?php echo esc_html( strtoupper( $sku ) ); ?>
				</p>
				<?php endif; ?>
			</a>
		</article>
		<?php endforeach; ?>
	</div>

	<?php if ( $total_pages > 1 ) : ?>
	<nav class="murg-pagination" aria-label="Paginación de tienda">
		<?php for ( $i = 1; $i <= $total_pages; $i++ ) :
			$url = $i === 1 ? $shop_url : trailingslashit( $shop_url ) . 'page/' . $i . '/';
			if ( $cat_slug ) {
				$url = add_query_arg( 'cat', $cat_slug, $url );
			}
		?>
		<a href="<?php echo esc_url( $url ); ?>"
		   class="murg-pagination__item <?php echo $i === $paged ? 'is-active' : ''; ?>"
		   <?php echo $i === $paged ? 'aria-current="page"' : ''; ?>>
			<?php echo $i; ?>
		</a>
		<?php endfor; ?>
	</nav>
	<?php endif; ?>

<?php else : ?>

	<div class="murg-shop-empty">
		<p class="murg-eyebrow">Sin resultados</p>
		<p style="margin-top:16px;color:var(--murg-muted)">No encontramos productos en esta categoría.</p>
		<a href="<?php echo esc_url( $shop_url ); ?>" class="murg-btn" style="margin-top:32px;display:inline-block;">
			Ver todo
		</a>
	</div>

<?php endif; ?>
</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
