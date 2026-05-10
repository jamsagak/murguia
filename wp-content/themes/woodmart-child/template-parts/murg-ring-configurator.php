<?php
/**
 * Template Part: Configurador de Anillos de Compromiso
 *
 * Solo se muestra si el producto pertenece a la categoría
 * "anillos-de-compromiso". Incluye selectores de:
 * - Forma del diamante (Diamond Shape) — PNG images
 * - Quilates (Carat Size) — slider
 * - Metal — swatches con kilataje
 * - Origen del diamante (Natural / Lab Grown)
 *
 * Se obtiene el producto desde global (get_template_part no pasa scope).
 */
global $product;
if ( ! $product instanceof WC_Product ) {
	$product = wc_get_product( get_queried_object_id() );
}
if ( ! $product ) return;
$product_id = $product->get_id();

$cat_slugs = wp_get_post_terms( $product_id, 'product_cat', [ 'fields' => 'slugs' ] );
if ( ! is_array( $cat_slugs ) || ! in_array( 'anillos-de-compromiso', $cat_slugs, true ) ) {
	return;
}

// --- Leer atributos actuales del producto ---
$current_shape = '';
$current_metal = '';
$current_origin = 'natural';

foreach ( $product->get_attributes() as $attr ) {
	if ( ! is_object( $attr ) ) continue;
	$tax = $attr->get_taxonomy();
	$opts = $attr->get_options();
	if ( $tax === 'pa_forma-de-piedra' && ! empty( $opts ) ) {
		$t = get_term( $opts[0] );
		$current_shape = $t && ! is_wp_error( $t ) ? $t->slug : '';
	}
	if ( $tax === 'pa_color-de-oro' && ! empty( $opts ) ) {
		$t = get_term( $opts[0] );
		$current_metal = $t && ! is_wp_error( $t ) ? $t->slug : '';
	}
	if ( $tax === 'pa_origen-diamante' && ! empty( $opts ) ) {
		$t = get_term( $opts[0] );
		$current_origin = $t && ! is_wp_error( $t ) ? $t->slug : 'natural';
	}
}

$shop_url = get_permalink( wc_get_page_id( 'shop' ) );

// Formas de diamante con labels
$shapes = [
	'redondo'   => 'Redondo',
	'oval'      => 'Oval',
	'esmeralda' => 'Esmeralda',
	'cojin'     => 'Cojín',
	'pera'      => 'Pera',
	'radiante'  => 'Radiante',
	'princesa'  => 'Princesa',
	'marquesa'  => 'Marquesa',
	'asscher'   => 'Asscher',
	'corazon'   => 'Corazón',
];

// Slug → PNG filename mapping
$shape_images = [
	'redondo'   => 'round_new.png',
	'oval'      => 'oval_new.png',
	'esmeralda' => 'emerald_new.png',
	'cojin'     => 'cushion_new.png',
	'pera'      => 'pear_new.png',
	'radiante'  => 'radiant_new.png',
	'princesa'  => 'princess_new.png',
	'marquesa'  => 'marquise_new.png',
	'asscher'   => 'asscher_new.png',
	'corazon'   => 'heart_new.png',
];
$img_base = get_stylesheet_directory_uri() . '/assets/img/diamond-shapes/';

// Metales con swatches
$metals = [
	'amarillo' => [ 'label' => 'Oro Amarillo 18K', 'color' => '#D4A843' ],
	'blanco'   => [ 'label' => 'Oro Blanco 18K',   'color' => '#E8E4DC' ],
	'rosado'   => [ 'label' => 'Oro Rosado 18K',   'color' => '#E8B4A0' ],
	'platino'  => [ 'label' => 'Platino',           'color' => '#C0C0C8' ],
];
?>

<div class="murg-ring-config" id="murg-ring-config" data-product-id="<?php echo (int) $product_id; ?>">

	<!-- FORMA DEL DIAMANTE -->
	<div class="murg-rc-section">
		<div class="murg-rc-section__header">
			<span class="murg-rc-section__label">Forma del Diamante:</span>
			<span class="murg-rc-section__value" id="murg-rc-shape-val">
				<?php echo esc_html( $shapes[ $current_shape ] ?? 'Redondo' ); ?>
			</span>
		</div>
		<div class="murg-rc-shapes" role="radiogroup" aria-label="Forma del diamante">
			<?php foreach ( $shapes as $slug => $label ) : ?>
			<button type="button"
			        class="murg-rc-shape <?php echo $slug === $current_shape ? 'is-active' : ''; ?>"
			        data-shape="<?php echo esc_attr( $slug ); ?>"
			        aria-label="<?php echo esc_attr( $label ); ?>"
			        title="<?php echo esc_attr( $label ); ?>">
				<img src="<?php echo esc_url( $img_base . $shape_images[ $slug ] ); ?>"
				     alt="<?php echo esc_attr( $label ); ?>"
				     class="murg-rc-shape__img">
			</button>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- TALLA / RING SIZE -->
	<div class="murg-rc-section">
		<div class="murg-rc-section__header">
			<span class="murg-rc-section__label">Talla:</span>
			<span class="murg-rc-section__value" id="murg-rc-size-val">6</span>
		</div>
		<div class="murg-rc-carat">
			<span class="murg-rc-carat__min">4</span>
			<div class="murg-rc-carat__track">
				<div class="murg-rc-carat__fill" id="murg-rc-size-fill"></div>
				<input type="range"
				       class="murg-rc-carat__input"
				       id="murg-rc-size"
				       min="4"
				       max="13"
				       step="0.5"
				       value="6">
			</div>
			<span class="murg-rc-carat__max">13</span>
		</div>
	</div>

	<!-- METAL -->
	<div class="murg-rc-section">
		<div class="murg-rc-section__header">
			<span class="murg-rc-section__label">Metal:</span>
			<span class="murg-rc-section__value" id="murg-rc-metal-val">
				<?php echo esc_html( $metals[ $current_metal ]['label'] ?? 'Oro Amarillo 18K' ); ?>
			</span>
		</div>
		<div class="murg-rc-metals" role="radiogroup" aria-label="Metal">
			<?php foreach ( $metals as $slug => $meta ) : ?>
			<button type="button"
			        class="murg-rc-metal <?php echo $slug === $current_metal ? 'is-active' : ''; ?>"
			        data-metal="<?php echo esc_attr( $slug ); ?>"
			        data-label="<?php echo esc_attr( $meta['label'] ); ?>"
			        aria-label="<?php echo esc_attr( $meta['label'] ); ?>"
			        title="<?php echo esc_attr( $meta['label'] ); ?>">
				<span class="murg-rc-metal__swatch" style="background-color: <?php echo esc_attr( $meta['color'] ); ?>"></span>
			</button>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- ORIGEN DEL DIAMANTE -->
	<div class="murg-rc-section">
		<div class="murg-rc-section__header">
			<span class="murg-rc-section__label">Origen del Diamante:</span>
			<span class="murg-rc-section__value" id="murg-rc-origin-val">
				<?php echo $current_origin === 'laboratorio' ? 'Lab Grown' : 'Natural'; ?>
			</span>
		</div>
		<div class="murg-rc-origin" role="radiogroup" aria-label="Origen del diamante">
			<button type="button"
			        class="murg-rc-origin__btn <?php echo $current_origin !== 'laboratorio' ? 'is-active' : ''; ?>"
			        data-origin="natural">
				Natural
			</button>
			<button type="button"
			        class="murg-rc-origin__btn <?php echo $current_origin === 'laboratorio' ? 'is-active' : ''; ?>"
			        data-origin="laboratorio">
				Lab Grown
			</button>
		</div>
	</div>

</div>
