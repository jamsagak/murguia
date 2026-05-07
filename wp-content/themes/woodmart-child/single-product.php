<?php
/**
 * Joyería Murguía — Producto Individual
 * single-product.php
 *
 * Layout luxury-minimalista:
 *   [breadcrumb]
 *   [galería vertical]            [info: eyebrow / nombre / precio / desc
 *                                        specs table / ATC / trust bar /
 *                                        tabs (Descripción / Detalles / Cuidado) ]
 *   [productos relacionados — slider horizontal]
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

// -----------------------------------------------------------------
// Product data
// -----------------------------------------------------------------
$product_id    = $product->get_id();
$product_name  = $product->get_name();
$product_price = $product->get_price_html();
$product_desc  = $product->get_short_description();
$product_long  = $product->get_description();
$product_sku   = $product->get_sku();
$is_sale       = $product->is_on_sale();
$in_stock      = $product->is_in_stock();

$cats     = wp_get_post_terms( $product_id, 'product_cat', [ 'fields' => 'names' ] );
$cat_name = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? $cats[0] : '';

$main_img_id = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$all_img_ids = $main_img_id ? array_merge( [ $main_img_id ], $gallery_ids ) : $gallery_ids;

$ref_prefix  = murguia_ajuste( 'prod_ref_prefix',  'REF.',  'producto' );
$cita_texto  = murguia_ajuste( 'prod_cita_texto',  '¿Preguntas? Solicite una cita personal →', 'producto' );

// -----------------------------------------------------------------
// Specs del producto (atributos visibles)
// Se excluyen los que solo son para filtros internos.
// -----------------------------------------------------------------
$specs = [];

// Atributos de taxonomía (pa_*)
foreach ( $product->get_attributes() as $attr_key => $attr ) {
	if ( ! is_object( $attr ) || ! $attr->get_visible() ) {
		continue;
	}
	$values = [];
	foreach ( $attr->get_options() as $val ) {
		if ( is_numeric( $val ) ) {
			$term = get_term( $val );
			if ( $term && ! is_wp_error( $term ) ) {
				$values[] = $term->name;
			}
		} else {
			$values[] = $val;
		}
	}
	if ( ! empty( $values ) ) {
		$label_name = wc_attribute_label( $attr->get_name(), $product );
		$specs[] = [
			'label' => $label_name,
			'value' => implode( ', ', $values ),
		];
	}
}

// Peso y dimensiones si están configurados
$weight = $product->get_weight();
if ( $weight && $weight !== '' ) {
	$specs[] = [
		'label' => esc_html__( 'Peso', 'woodmart' ),
		'value' => $weight . ' ' . get_option( 'woocommerce_weight_unit', 'g' ),
	];
}
if ( $product->has_dimensions() ) {
	$specs[] = [
		'label' => esc_html__( 'Dimensiones', 'woodmart' ),
		'value' => wc_format_dimensions( $product->get_dimensions( false ) ),
	];
}

// -----------------------------------------------------------------
// Trust bar (editable desde ACF, con fallbacks)
// -----------------------------------------------------------------
$trust_items = [
	[
		'icon'  => 'shipping',
		'text'  => murguia_ajuste( 'prod_trust_1', 'Envío seguro a todo el Perú', 'producto' ),
	],
	[
		'icon'  => 'case',
		'text'  => murguia_ajuste( 'prod_trust_2', 'Estuche de presentación incluido', 'producto' ),
	],
	[
		'icon'  => 'shield',
		'text'  => murguia_ajuste( 'prod_trust_3', 'Garantía de por vida', 'producto' ),
	],
	[
		'icon'  => 'certificate',
		'text'  => murguia_ajuste( 'prod_trust_4', 'Certificado de autenticidad', 'producto' ),
	],
];
// Filtrar los que el cliente vació explícitamente (string vacío)
$trust_items = array_values( array_filter( $trust_items, function ( $t ) {
	return ! empty( trim( $t['text'] ) );
} ) );

// -----------------------------------------------------------------
// Tabs (Descripción / Detalles / Cuidado). La descripción usa el WC
// long description si no hay una ACF específica; el resto solo aparece
// si el campo tiene contenido.
// -----------------------------------------------------------------
$tabs = [];

if ( trim( wp_strip_all_tags( (string) $product_long ) ) !== '' ) {
	$tabs[] = [
		'id'    => 'desc',
		'label' => murguia_ajuste( 'prod_tab_desc_label', 'Descripción', 'producto' ),
		'html'  => wp_kses_post( $product_long ),
	];
}

$detalles_texto = murguia_ajuste( 'prod_detalles_texto', '', 'producto' );
if ( trim( wp_strip_all_tags( (string) $detalles_texto ) ) !== '' ) {
	$tabs[] = [
		'id'    => 'detalles',
		'label' => murguia_ajuste( 'prod_tab_detalles_label', 'Detalles técnicos', 'producto' ),
		'html'  => wp_kses_post( wpautop( $detalles_texto ) ),
	];
}

$cuidado_texto = murguia_ajuste( 'prod_cuidado_texto', '', 'producto' );
if ( trim( wp_strip_all_tags( (string) $cuidado_texto ) ) !== '' ) {
	$tabs[] = [
		'id'    => 'cuidado',
		'label' => murguia_ajuste( 'prod_tab_cuidado_label', 'Cuidado de la pieza', 'producto' ),
		'html'  => wp_kses_post( wpautop( $cuidado_texto ) ),
	];
}

// -----------------------------------------------------------------
// Related products — hasta 6, slider horizontal
// -----------------------------------------------------------------
$related_limit = max( 3, (int) murguia_ajuste( 'prod_related_cantidad', 6, 'producto' ) );
$related_ids   = wc_get_related_products( $product_id, $related_limit );
$related       = array_filter( array_map( 'wc_get_product', $related_ids ) );

$shop_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/tienda/' );

// -----------------------------------------------------------------
// Guía de tallas — imagen subida por producto (ACF)
// -----------------------------------------------------------------
$guia_tallas        = function_exists( 'get_field' ) ? get_field( 'murg_guia_tallas', $product_id ) : null;
$guia_tallas_titulo = function_exists( 'get_field' ) ? get_field( 'murg_guia_tallas_titulo', $product_id ) : '';
$has_guia_tallas    = ! empty( $guia_tallas ) && ! empty( $guia_tallas['url'] );
if ( empty( $guia_tallas_titulo ) ) {
	$guia_tallas_titulo = 'Guía de tallas';
}

// -----------------------------------------------------------------
// Iconos SVG del trust bar (inline, theme-scoped)
// -----------------------------------------------------------------
function murg_prod_trust_icon( $name ) {
	$icons = [
		'shipping'    => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M2 7h12v9H2zM14 10h5l3 3v3h-8zM6 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM18 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg>',
		'case'        => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M3 7h18v12H3zM9 7V5a3 3 0 0 1 6 0v2M3 11h18"/></svg>',
		'shield'      => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3zM9 12l2 2 4-4"/></svg>',
		'certificate' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><circle cx="12" cy="10" r="6"/><path d="M9 15l-2 6 5-3 5 3-2-6M12 7v3l2 1"/></svg>',
	];
	return $icons[ $name ] ?? '';
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
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
	<span class="murg-breadcrumb__current"><?php echo esc_html( $product_name ); ?></span>
</div>

<!-- ============================================================
     PRODUCTO
     ============================================================ -->
<main class="murg-product-detail" id="main" itemscope itemtype="https://schema.org/Product">

	<!-- ==== GALERÍA ==== -->
	<div class="murg-product-detail__gallery">
		<div class="murg-pdgallery <?php echo count( $all_img_ids ) <= 1 ? 'murg-pdgallery--single' : ''; ?>" data-total="<?php echo (int) count( $all_img_ids ); ?>">

			<!-- Thumbs (verticales en desktop / horizontales en mobile) -->
			<?php if ( count( $all_img_ids ) > 1 ) : ?>
			<div class="murg-pdgallery__thumbs" role="list">
				<?php foreach ( $all_img_ids as $i => $img_id ) :
					$thumb_url = wp_get_attachment_image_url( $img_id, 'medium' );
					$full_url  = wp_get_attachment_image_url( $img_id, 'large' );
					if ( ! $thumb_url ) continue;
				?>
				<button class="murg-pdgallery__thumb <?php echo 0 === $i ? 'is-active' : ''; ?>"
				        role="listitem"
				        type="button"
				        data-full="<?php echo esc_url( $full_url ); ?>"
				        data-index="<?php echo (int) $i; ?>"
				        aria-label="Imagen <?php echo $i + 1; ?> de <?php echo count( $all_img_ids ); ?>">
					<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy">
				</button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<!-- Main image -->
			<div class="murg-pdgallery__main">
				<?php if ( ! empty( $all_img_ids ) ) : ?>
					<?php echo wp_get_attachment_image( $all_img_ids[0], 'large', false, [
						'id'       => 'murg-pdg-main-img',
						'class'    => 'murg-pdgallery__main-img',
						'loading'  => 'eager',
						'alt'      => $product_name,
						'itemprop' => 'image',
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

				<?php if ( count( $all_img_ids ) > 0 ) : ?>
				<button class="murg-pdgallery__zoom" id="murg-pdg-zoom"
				        type="button"
				        aria-label="Ampliar imagen">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
						<circle cx="11" cy="11" r="7"/><path d="M16 16l5 5M9 11h4M11 9v4"/>
					</svg>
				</button>
				<?php endif; ?>
			</div>

		</div>
	</div>

	<!-- ==== INFO ==== -->
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

		<!-- Specs table -->
		<?php
		// Construir la lista completa (atributos + SKU + disponibilidad)
		$specs_display = $specs;
		if ( $product_sku ) {
			$specs_display[] = [
				'label' => $ref_prefix,
				'value' => strtoupper( $product_sku ),
			];
		}
		$specs_display[] = [
			'label' => 'Disponibilidad',
			'value' => $in_stock
				? __( 'En stock', 'woodmart' )
				: murguia_ajuste( 'prod_badge_agotado', 'Agotado', 'producto' ),
		];
		?>
		<?php if ( ! empty( $specs_display ) ) : ?>
		<dl class="murg-product-detail__specs">
			<?php foreach ( $specs_display as $spec ) : ?>
			<div class="murg-product-detail__spec">
				<dt class="murg-product-detail__spec-label"><?php echo esc_html( $spec['label'] ); ?></dt>
				<dd class="murg-product-detail__spec-value"><?php echo esc_html( $spec['value'] ); ?></dd>
			</div>
			<?php endforeach; ?>
		</dl>
		<?php endif; ?>

		<div class="murg-product-detail__divider" aria-hidden="true"></div>

		<!-- Guía de tallas (solo si el producto tiene imagen cargada en ACF) -->
		<?php if ( $has_guia_tallas ) : ?>
		<button class="murg-sizeguide-btn" type="button"
		        data-target="murg-sizeguide"
		        aria-haspopup="dialog"
		        aria-controls="murg-sizeguide">
			<svg class="murg-sizeguide-btn__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
				<path d="M3 8h18v8H3z"/>
				<path d="M7 8v4M11 8v6M15 8v4M19 8v6"/>
			</svg>
			<span><?php echo esc_html( $guia_tallas_titulo ); ?></span>
			<svg class="murg-sizeguide-btn__arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
				<path d="M9 5l7 7-7 7"/>
			</svg>
		</button>
		<?php endif; ?>

		<!-- Add to cart (WooCommerce) -->
		<div class="murg-product-detail__atc">
			<?php
			do_action( 'woocommerce_before_add_to_cart_form' );
			woocommerce_template_single_add_to_cart();
			do_action( 'woocommerce_after_add_to_cart_form' );
			?>
		</div>

		<!-- Trust bar -->
		<?php if ( ! empty( $trust_items ) ) : ?>
		<ul class="murg-trust" aria-label="Servicios incluidos">
			<?php foreach ( $trust_items as $t ) : ?>
			<li class="murg-trust__item">
				<span class="murg-trust__icon" aria-hidden="true"><?php echo murg_prod_trust_icon( $t['icon'] ); // phpcs:ignore ?></span>
				<span class="murg-trust__text"><?php echo esc_html( $t['text'] ); ?></span>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>

		<!-- Cita link -->
		<?php if ( $cita_texto ) : ?>
		<a href="<?php echo esc_url( home_url( '/#contacto' ) ); ?>" class="murg-product-detail__cita">
			<?php echo esc_html( $cita_texto ); ?>
		</a>
		<?php endif; ?>

	</div><!-- /.murg-product-detail__info -->
</main>

<!-- ============================================================
     TABS (Descripción / Detalles / Cuidado)
     ============================================================ -->
<?php if ( ! empty( $tabs ) ) : ?>
<section class="murg-ptabs" aria-label="Información del producto">
	<div class="murg-ptabs__nav" role="tablist">
		<?php foreach ( $tabs as $i => $tab ) : ?>
		<button class="murg-ptabs__trigger <?php echo 0 === $i ? 'is-active' : ''; ?>"
		        role="tab"
		        type="button"
		        id="murg-ptab-trg-<?php echo esc_attr( $tab['id'] ); ?>"
		        aria-controls="murg-ptab-<?php echo esc_attr( $tab['id'] ); ?>"
		        aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
		        data-target="<?php echo esc_attr( $tab['id'] ); ?>">
			<?php echo esc_html( $tab['label'] ); ?>
		</button>
		<?php endforeach; ?>
	</div>
	<div class="murg-ptabs__panels">
		<?php foreach ( $tabs as $i => $tab ) : ?>
		<div class="murg-ptabs__panel <?php echo 0 === $i ? 'is-active' : ''; ?>"
		     role="tabpanel"
		     id="murg-ptab-<?php echo esc_attr( $tab['id'] ); ?>"
		     aria-labelledby="murg-ptab-trg-<?php echo esc_attr( $tab['id'] ); ?>"
		     <?php echo 0 === $i ? '' : 'hidden'; ?>>
			<?php echo $tab['html']; // phpcs:ignore — ya sanitizado arriba ?>
		</div>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>

<!-- ============================================================
     PRODUCTOS RELACIONADOS — slider horizontal
     ============================================================ -->
<?php if ( ! empty( $related ) ) : ?>
<section class="murg-section murg-related" aria-label="Piezas similares">
	<header class="murg-section__header murg-related__header">
		<div>
			<div class="murg-eyebrow murg-section__eyebrow">También puede interesarle</div>
			<h2 class="murg-serif murg-section__title">Piezas <em>similares</em></h2>
		</div>
		<?php if ( count( $related ) > 3 ) : ?>
		<div class="murg-related__nav" aria-label="Navegación de productos relacionados">
			<button class="murg-related__arrow" id="murg-rel-prev" type="button" aria-label="Anterior">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M15 5l-7 7 7 7"/></svg>
			</button>
			<button class="murg-related__arrow" id="murg-rel-next" type="button" aria-label="Siguiente">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M9 5l7 7-7 7"/></svg>
			</button>
		</div>
		<?php endif; ?>
	</header>

	<div class="murg-related__viewport">
		<div class="murg-related__track" id="murg-rel-track" data-total="<?php echo (int) count( $related ); ?>">
			<?php foreach ( $related as $rel ) :
				$rel_img = $rel->get_image_id();
				$rel_sku = $rel->get_sku();
			?>
			<article class="murg-product murg-product--grid murg-related__item">
				<a class="murg-product__link" href="<?php echo esc_url( $rel->get_permalink() ); ?>">
					<div class="murg-product__img">
						<?php if ( $rel_img ) :
							echo wp_get_attachment_image( $rel_img, 'medium_large', false, [
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
	</div>
</section>
<?php endif; ?>

<!-- ============================================================
     LIGHTBOX (hidden hasta que se active)
     ============================================================ -->
<?php if ( count( $all_img_ids ) > 0 ) : ?>
<div class="murg-lightbox" id="murg-lightbox" aria-hidden="true" role="dialog" aria-label="Imagen ampliada">
	<button class="murg-lightbox__close" id="murg-lightbox-close" type="button" aria-label="Cerrar">&times;</button>
	<?php if ( count( $all_img_ids ) > 1 ) : ?>
	<button class="murg-lightbox__nav murg-lightbox__nav--prev" id="murg-lightbox-prev" type="button" aria-label="Anterior">
		<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M15 5l-7 7 7 7"/></svg>
	</button>
	<button class="murg-lightbox__nav murg-lightbox__nav--next" id="murg-lightbox-next" type="button" aria-label="Siguiente">
		<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M9 5l7 7-7 7"/></svg>
	</button>
	<?php endif; ?>
	<div class="murg-lightbox__stage">
		<img class="murg-lightbox__img" id="murg-lightbox-img" alt="">
	</div>
	<div class="murg-lightbox__caption" id="murg-lightbox-caption" aria-live="polite"></div>
	<script type="application/json" id="murg-lightbox-data"><?php
		echo wp_json_encode( array_values( array_filter( array_map( function ( $id ) {
			$full = wp_get_attachment_image_url( $id, 'full' );
			return $full ? [ 'src' => $full ] : null;
		}, $all_img_ids ) ) ) );
	?></script>
</div>
<?php endif; ?>

<!-- ============================================================
     MODAL DE GUÍA DE TALLAS (solo si hay imagen ACF cargada)
     ============================================================ -->
<?php if ( $has_guia_tallas ) : ?>
<div class="murg-sizeguide"
     id="murg-sizeguide"
     role="dialog"
     aria-modal="true"
     aria-labelledby="murg-sizeguide-title"
     aria-hidden="true">
	<div class="murg-sizeguide__backdrop" data-close="murg-sizeguide" aria-hidden="true"></div>
	<div class="murg-sizeguide__panel" role="document">
		<header class="murg-sizeguide__header">
			<h2 class="murg-sizeguide__title murg-serif" id="murg-sizeguide-title">
				<?php echo esc_html( $guia_tallas_titulo ); ?>
			</h2>
			<button class="murg-sizeguide__close"
			        type="button"
			        data-close="murg-sizeguide"
			        aria-label="Cerrar guía de tallas">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
					<path d="M6 6l12 12M6 18L18 6"/>
				</svg>
			</button>
		</header>
		<div class="murg-sizeguide__body">
			<img class="murg-sizeguide__img"
			     src="<?php echo esc_url( $guia_tallas['url'] ); ?>"
			     alt="<?php echo esc_attr( $guia_tallas['alt'] ?: $guia_tallas_titulo ); ?>"
			     <?php if ( ! empty( $guia_tallas['width'] ) && ! empty( $guia_tallas['height'] ) ) : ?>
			     width="<?php echo (int) $guia_tallas['width']; ?>"
			     height="<?php echo (int) $guia_tallas['height']; ?>"
			     <?php endif; ?>
			     loading="lazy">
		</div>
	</div>
</div>
<?php endif; ?>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
