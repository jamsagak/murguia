<?php
/**
 * Joyería Murguía — Producto Individual
 * single-product.php
 */
defined( 'ABSPATH' ) || exit;

// Ensure WC product object is available.
global $product;
if ( ! $product instanceof WC_Product ) {
	$product = wc_get_product( get_queried_object_id() );
}
if ( ! $product || ! $product->is_visible() ) {
	wp_safe_redirect( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) );
	exit;
}

// Product data
$product_name  = $product->get_name();
$product_price = $product->get_price_html();
$product_desc  = $product->get_short_description();
$product_long  = $product->get_description();
$product_sku   = $product->get_sku();
$is_sale       = $product->is_on_sale();
$in_stock      = $product->is_in_stock();

$cats     = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'names' ] );
$cat_name = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? $cats[0] : '';

$main_img_id = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$all_img_ids = $main_img_id ? array_merge( [ $main_img_id ], $gallery_ids ) : $gallery_ids;

$ref_prefix  = murguia_ajuste( 'prod_ref_prefix',  'REF.',  'producto' );
$cita_texto  = murguia_ajuste( 'prod_cita_texto',  '¿Preguntas? Solicite una cita personal →', 'producto' );

// Related products
$related_ids = wc_get_related_products( $product->get_id(), 3 );
$related     = array_filter( array_map( 'wc_get_product', $related_ids ) );

$shop_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/tienda/' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( $product_name ); ?> · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-single-product woocommerce single-product' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<!-- ============================================================
     BREADCRUMB
     ============================================================ -->
<div class="murg-breadcrumb">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a>
	<span aria-hidden="true">·</span>
	<a href="<?php echo esc_url( $shop_url ); ?>">Tienda</a>
	<?php if ( $cat_name ) : ?>
	<span aria-hidden="true">·</span>
	<span><?php echo esc_html( $cat_name ); ?></span>
	<?php endif; ?>
	<span aria-hidden="true">·</span>
	<span><?php echo esc_html( $product_name ); ?></span>
</div>

<!-- ============================================================
     PRODUCTO
     ============================================================ -->
<main class="murg-product-detail" id="main" itemscope itemtype="https://schema.org/Product">

	<!-- Galería -->
	<div class="murg-product-detail__gallery">
		<div class="murg-pdgallery">
			<div class="murg-pdgallery__main">
				<?php if ( ! empty( $all_img_ids ) ) : ?>
					<?php echo wp_get_attachment_image( $all_img_ids[0], 'large', false, [
						'id'      => 'murg-pdg-main-img',
						'class'   => 'murg-pdgallery__main-img',
						'loading' => 'eager',
						'alt'     => $product_name,
						'itemprop'=> 'image',
					] ); ?>
				<?php else : ?>
					<img id="murg-pdg-main-img"
					     class="murg-pdgallery__main-img"
					     src="<?php echo esc_url( wc_placeholder_img_src( 'large' ) ); ?>"
					     alt="<?php echo esc_attr( $product_name ); ?>"
					     loading="eager">
				<?php endif; ?>

				<?php if ( $is_sale ) : ?>
				<span class="murg-product__tag">
					<?php echo esc_html( murguia_ajuste( 'prod_badge_oferta', 'Oferta', 'producto' ) ); ?>
				</span>
				<?php endif; ?>
			</div>

			<?php if ( count( $all_img_ids ) > 1 ) : ?>
			<div class="murg-pdgallery__thumbs" role="list">
				<?php foreach ( $all_img_ids as $i => $img_id ) :
					$thumb_url = wp_get_attachment_image_url( $img_id, 'medium' );
					$full_url  = wp_get_attachment_image_url( $img_id, 'large' );
					if ( ! $thumb_url ) {
						continue;
					}
				?>
				<button class="murg-pdgallery__thumb <?php echo 0 === $i ? 'is-active' : ''; ?>"
				        role="listitem"
				        data-full="<?php echo esc_url( $full_url ); ?>"
				        aria-label="Imagen <?php echo $i + 1; ?> de <?php echo count( $all_img_ids ); ?>">
					<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy">
				</button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Info -->
	<div class="murg-product-detail__info">

		<?php if ( $cat_name ) : ?>
		<div class="murg-eyebrow murg-product-detail__cat"><?php echo esc_html( $cat_name ); ?></div>
		<?php endif; ?>

		<h1 class="murg-serif murg-product-detail__name" itemprop="name">
			<?php echo esc_html( $product_name ); ?>
		</h1>

		<div class="murg-product-detail__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
			<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>">
			<meta itemprop="price" content="<?php echo esc_attr( $product->get_price() ); ?>">
			<?php echo wp_kses_post( $product_price ); ?>
		</div>

		<?php if ( $product_desc ) : ?>
		<div class="murg-product-detail__desc"><?php echo wp_kses_post( $product_desc ); ?></div>
		<?php endif; ?>

		<div class="murg-product-detail__divider" aria-hidden="true"></div>

		<!-- Add to cart (WooCommerce) -->
		<div class="murg-product-detail__atc">
			<?php
			do_action( 'woocommerce_before_add_to_cart_form' );
			woocommerce_template_single_add_to_cart();
			do_action( 'woocommerce_after_add_to_cart_form' );
			?>
		</div>

		<!-- Meta -->
		<dl class="murg-product-detail__meta-list">
			<?php if ( $product_sku ) : ?>
			<div class="murg-product-detail__meta-item">
				<dt class="murg-product-detail__meta-label"><?php echo esc_html( $ref_prefix ); ?></dt>
				<dd><?php echo esc_html( strtoupper( $product_sku ) ); ?></dd>
			</div>
			<?php endif; ?>
			<div class="murg-product-detail__meta-item">
				<dt class="murg-product-detail__meta-label">Disponibilidad</dt>
				<dd><?php echo $in_stock ? esc_html__( 'En stock', 'woodmart' ) : esc_html( murguia_ajuste( 'prod_badge_agotado', 'Agotado', 'producto' ) ); ?></dd>
			</div>
		</dl>

		<?php if ( $product_long ) : ?>
		<details class="murg-product-detail__details">
			<summary class="murg-product-detail__details-toggle">Descripción completa</summary>
			<div class="murg-product-detail__details-body"><?php echo wp_kses_post( $product_long ); ?></div>
		</details>
		<?php endif; ?>

		<?php if ( $cita_texto ) : ?>
		<a href="<?php echo esc_url( home_url( '/#contacto' ) ); ?>" class="murg-product-detail__cita">
			<?php echo esc_html( $cita_texto ); ?>
		</a>
		<?php endif; ?>

	</div><!-- /.murg-product-detail__info -->
</main>

<!-- ============================================================
     PRODUCTOS RELACIONADOS
     ============================================================ -->
<?php if ( ! empty( $related ) ) : ?>
<section class="murg-section murg-related" aria-label="Piezas similares">
	<header class="murg-section__header">
		<div class="murg-eyebrow murg-section__eyebrow">También puede interesarle</div>
		<h2 class="murg-serif murg-section__title">Piezas <em>similares</em></h2>
	</header>
	<div class="murg-shop-grid">
		<?php foreach ( $related as $rel ) :
			$rel_img = $rel->get_image_id();
			$rel_sku = $rel->get_sku();
		?>
		<article class="murg-product murg-product--grid">
			<a class="murg-product__link" href="<?php echo esc_url( $rel->get_permalink() ); ?>">
				<div class="murg-product__img">
					<?php if ( $rel_img ) :
						echo wp_get_attachment_image( $rel_img, 'medium', false, [
							'loading' => 'lazy',
							'alt'     => $rel->get_name(),
						] );
					else : ?>
						<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>"
						     alt="<?php echo esc_attr( $rel->get_name() ); ?>"
						     loading="lazy">
					<?php endif; ?>
				</div>
				<div class="murg-product__meta">
					<h3 class="murg-product__name"><?php echo esc_html( $rel->get_name() ); ?></h3>
					<div class="murg-product__price"><?php echo wp_kses_post( $rel->get_price_html() ); ?></div>
				</div>
				<?php if ( $rel_sku ) : ?>
				<p class="murg-product__ref"><?php echo esc_html( $ref_prefix . ' ' . strtoupper( $rel_sku ) ); ?></p>
				<?php endif; ?>
			</a>
		</article>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
