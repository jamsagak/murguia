<?php
/**
 * Template Part: Configurador de Anillos de Compromiso
 *
 * Solo se muestra si el producto pertenece a la categoría
 * "anillos-de-compromiso". Incluye selectores de:
 * - Forma del diamante (Diamond Shape)
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

// Extraer quilates del título (ej: "0.75Ct", "1.64ct", "1ct")
$current_carat = 0.5;
if ( preg_match( '/(\d+\.?\d*)\s*[Cc]t/i', $product->get_name(), $m ) ) {
	$current_carat = (float) $m[1];
}

// --- Contar productos disponibles por forma (para saber cuáles existen) ---
$shop_url = get_permalink( wc_get_page_id( 'shop' ) );

// Formas de diamante con SVG inline
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

// Metales con swatches
$metals = [
	'amarillo' => [ 'label' => 'Oro Amarillo 18K', 'color' => '#D4A843' ],
	'blanco'   => [ 'label' => 'Oro Blanco 18K',   'color' => '#E8E4DC' ],
	'rosado'   => [ 'label' => 'Oro Rosado 18K',   'color' => '#E8B4A0' ],
	'platino'  => [ 'label' => 'Platino',           'color' => '#C0C0C8' ],
];

// SVG paths para cada forma
function murg_diamond_svg( $shape ) {
	$svgs = [
		'redondo'   => '<circle cx="24" cy="24" r="18"/>',
		'oval'      => '<ellipse cx="24" cy="24" rx="14" ry="20"/>',
		'esmeralda' => '<path d="M12 6h24l4 4v28l-4 4H12l-4-4V10l4-4z"/>',
		'cojin'     => '<rect x="6" y="6" width="36" height="36" rx="10"/>',
		'pera'      => '<path d="M24 4C16 4 8 14 8 26c0 8 7 16 16 16s16-8 16-16C40 14 32 4 24 4z"/>',
		'radiante'  => '<path d="M14 4h20l10 10v20l-10 10H14L4 34V14L14 4z"/>',
		'princesa'  => '<rect x="6" y="6" width="36" height="36" rx="2"/>',
		'marquesa'  => '<ellipse cx="24" cy="24" rx="12" ry="22" transform="rotate(0 24 24)"/>',
		'asscher'   => '<path d="M14 4h20l10 10v20l-10 10H14L4 34V14L14 4z"/>',
		'corazon'   => '<path d="M24 40S4 28 4 16C4 9 9 4 15 4c4 0 7 2 9 5 2-3 5-5 9-5 6 0 11 5 11 12C44 28 24 40 24 40z"/>',
	];
	$path = $svgs[ $shape ] ?? '<circle cx="24" cy="24" r="18"/>';
	return '<svg class="murg-rc-shape__svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1">' . $path . '</svg>';
}
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
				<?php echo murg_diamond_svg( $slug ); ?>
			</button>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- QUILATES / CARAT SIZE -->
	<div class="murg-rc-section">
		<div class="murg-rc-section__header">
			<span class="murg-rc-section__label">Quilates:</span>
			<span class="murg-rc-section__value" id="murg-rc-carat-val">
				<?php echo number_format( $current_carat, 2 ); ?> Ct
			</span>
		</div>
		<div class="murg-rc-carat">
			<span class="murg-rc-carat__min">0.10 Ct</span>
			<div class="murg-rc-carat__track">
				<div class="murg-rc-carat__fill" id="murg-rc-carat-fill"></div>
				<input type="range"
				       class="murg-rc-carat__input"
				       id="murg-rc-carat"
				       min="0.10"
				       max="5"
				       step="0.01"
				       value="<?php echo esc_attr( $current_carat ); ?>">
			</div>
			<span class="murg-rc-carat__max">5 Ct</span>
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
