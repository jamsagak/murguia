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
			<span class="murg-rc-section__label">
				Talla:
				<button type="button" class="murg-rc-sizeguide-link" id="murg-rc-sizeguide-open" aria-label="Ver guía de tallas">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
					Guía de tallas
				</button>
			</span>
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

<!-- MODAL: Guía de Tallas -->
<div class="murg-sizeguide-modal" id="murg-sizeguide-modal" role="dialog" aria-modal="true" aria-label="Guía de tallas">
	<div class="murg-sizeguide-modal__overlay" id="murg-sizeguide-close-overlay"></div>
	<div class="murg-sizeguide-modal__panel">
		<div class="murg-sizeguide-modal__header">
			<h3 class="murg-sizeguide-modal__title">Guía de Tallas</h3>
			<button type="button" class="murg-sizeguide-modal__close" id="murg-sizeguide-close" aria-label="Cerrar">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
			</button>
		</div>
		<p class="murg-sizeguide-modal__desc">Mida el diámetro interior de un anillo que le quede bien, o consulte con nuestro equipo para una medición presencial.</p>
		<div class="murg-sizeguide-modal__grid">
			<?php
			$sizes = [
				['3.5', '14.4'], ['4', '14.8'], ['4.5', '15.2'], ['5', '15.6'],
				['5.5', '15.6'], ['6', '16.4'], ['6.5', '16.9'], ['7', '17.3'],
				['7.5', '17.7'], ['8', '18.2'], ['8.5', '18.6'], ['9', '19.0'],
				['9.5', '19.4'], ['10', '19.8'], ['10.5', '20.2'],
				['11', '20.6'], ['11.5', '21.0'], ['12', '21.4'],
			];
			foreach ( $sizes as $s ) : ?>
			<div class="murg-sizeguide-modal__item">
				<div class="murg-sizeguide-modal__ring">
					<span class="murg-sizeguide-modal__num"><?php echo $s[0]; ?></span>
				</div>
				<span class="murg-sizeguide-modal__mm"><?php echo $s[1]; ?> mm</span>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="murg-sizeguide-modal__tip">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--murg-gold)" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
			<span>¿No está seguro de su talla? Escríbanos por WhatsApp y le ayudamos.</span>
		</div>
	</div>
</div>
