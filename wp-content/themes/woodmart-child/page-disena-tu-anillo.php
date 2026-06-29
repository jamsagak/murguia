<?php
/**
 * Template Name: Diseña tu anillo
 *
 * Configurador consultivo para anillos de compromiso bajo pedido.
 */
defined( 'ABSPATH' ) || exit;

$wa_url_raw = murguia_ajuste( 'ac_whatsapp_url', 'https://wa.me/51114218800', 'anillos-compromiso-page' );
$wa_number  = preg_replace( '/[^0-9]/', '', $wa_url_raw );
if ( ! $wa_number ) {
	$wa_number = '51114218800';
}

$shape_dir = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/diamond-shapes/';
$shapes = [
	[ 'label' => 'Redondo',   'img' => 'round_new.png' ],
	[ 'label' => 'Oval',      'img' => 'oval_new.png' ],
	[ 'label' => 'Esmeralda', 'img' => 'emerald_new.png' ],
	[ 'label' => 'Cojín',     'img' => 'cushion_new.png' ],
	[ 'label' => 'Pera',      'img' => 'pear_new.png' ],
	[ 'label' => 'Princesa',  'img' => 'princess_new.png' ],
	[ 'label' => 'Marquesa',  'img' => 'marquise_new.png' ],
	[ 'label' => 'Asscher',   'img' => 'asscher_new.png' ],
];

$models = [
	'Solitario clásico',
	'Hidden halo',
	'Halo',
	'Tres piedras',
	'Pavé',
	'Diseño personalizado',
];

$metals = [
	[ 'label' => 'Oro amarillo 18K', 'color' => '#d4a843' ],
	[ 'label' => 'Oro blanco 18K',   'color' => '#e8e4dc' ],
	[ 'label' => 'Oro rosado 18K',   'color' => '#e8b4a0' ],
	[ 'label' => 'Platino',          'color' => '#c9c9c9' ],
];

$sizes = [ '4', '4.5', '5', '5.5', '6', '6.5', '7', '7.5', '8', '8.5', '9', '9.5', '10', '10.5', '11' ];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-design-ring-page' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-design-flow">
	<section class="murg-design-flow__hero">
		<div class="murg-design-flow__inner" data-reveal>
			<p class="murg-eyebrow">Diseña tu anillo</p>
			<h1>Configura una pieza para una historia única.</h1>
			<p>Selecciona los elementos base de tu anillo de compromiso. Al final verás un resumen para solicitar una cotización privada con Murguía.</p>
			<p class="murg-design-flow__note">No mostramos precio final en línea. La cotización depende de disponibilidad de diamante, metal, talla y taller.</p>
			<div class="murg-design-flow__actions">
				<a class="murg-btn murg-btn--dark" href="#murg-ring-builder">Empezar diseño</a>
				<a class="murg-btn murg-btn--ghost" href="<?php echo esc_url( home_url( '/las-4cs/' ) ); ?>">Conoce Las 4Cs</a>
			</div>
		</div>
	</section>

	<section class="murg-ring-builder" id="murg-ring-builder" data-wa-number="<?php echo esc_attr( $wa_number ); ?>" aria-label="Configurador de anillo">
		<div class="murg-ring-builder__layout">
			<div class="murg-ring-builder__steps">
				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">01</span>
					<h2>Modelo del anillo</h2>
					<div class="murg-builder-options" data-builder-group="Modelo">
						<?php foreach ( $models as $index => $model ) : ?>
						<button type="button" class="murg-builder-option<?php echo 0 === $index ? ' is-selected' : ''; ?>" data-value="<?php echo esc_attr( $model ); ?>">
							<?php echo esc_html( $model ); ?>
						</button>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">02</span>
					<h2>Forma del diamante</h2>
					<div class="murg-design-shapes murg-design-shapes--builder" data-builder-group="Forma">
						<?php foreach ( $shapes as $index => $shape ) : ?>
						<button type="button" class="murg-design-shape<?php echo 0 === $index ? ' is-selected' : ''; ?>" data-value="<?php echo esc_attr( $shape['label'] ); ?>">
							<img src="<?php echo esc_url( $shape_dir . $shape['img'] ); ?>" alt="<?php echo esc_attr( $shape['label'] ); ?>" loading="lazy">
							<span><?php echo esc_html( $shape['label'] ); ?></span>
						</button>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">03</span>
					<h2>Metal</h2>
					<div class="murg-builder-metals" data-builder-group="Metal">
						<?php foreach ( $metals as $index => $metal ) : ?>
						<button type="button" class="murg-builder-metal<?php echo 1 === $index ? ' is-selected' : ''; ?>" data-value="<?php echo esc_attr( $metal['label'] ); ?>">
							<span style="background: <?php echo esc_attr( $metal['color'] ); ?>"></span>
							<?php echo esc_html( $metal['label'] ); ?>
						</button>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">04</span>
					<div class="murg-builder-range__head">
						<h2>Quilates aproximados</h2>
						<strong><span data-builder-output="Quilates">1.00</span> ct</strong>
					</div>
					<input class="murg-builder-range" type="range" min="0.30" max="3.00" step="0.10" value="1.00" data-builder-range="Quilates" data-suffix=" ct">
					<div class="murg-builder-range__scale"><span>0.30 ct</span><span>3.00 ct</span></div>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">05</span>
					<h2>Origen del diamante</h2>
					<div class="murg-builder-options murg-builder-options--two" data-builder-group="Origen">
						<button type="button" class="murg-builder-option is-selected" data-value="Natural">Natural</button>
						<button type="button" class="murg-builder-option" data-value="Laboratorio">Laboratorio</button>
					</div>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">06</span>
					<div class="murg-builder-step-head">
						<h2>Talla estimada</h2>
						<button type="button" class="murg-builder-sizeguide-link" data-target="murg-sizeguide" aria-haspopup="dialog" aria-controls="murg-sizeguide">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
								<path d="M3 8h18v8H3z"/>
								<path d="M7 8v4M11 8v6M15 8v4M19 8v6"/>
							</svg>
							Guía de tallas
						</button>
					</div>
					<div class="murg-builder-options murg-builder-options--sizes" data-builder-group="Talla">
						<?php foreach ( $sizes as $index => $size ) : ?>
						<button type="button" class="murg-builder-option<?php echo 4 === $index ? ' is-selected' : ''; ?>" data-value="<?php echo esc_attr( $size ); ?>">
							<?php echo esc_html( $size ); ?>
						</button>
						<?php endforeach; ?>
					</div>
					<p class="murg-builder-help">Si no conoces la talla, podemos confirmarla durante la asesoría.</p>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">07</span>
					<h2>Notas opcionales</h2>
					<textarea class="murg-builder-notes" rows="4" data-builder-notes placeholder="Ej. fecha aproximada de entrega, presupuesto referencial, inspiración o detalle especial."></textarea>
				</section>
			</div>

			<aside class="murg-builder-summary" aria-label="Resumen de seleccion">
				<p class="murg-eyebrow">Resumen</p>
				<h2>Tu anillo</h2>
				<div class="murg-builder-preview" data-ring-preview data-model="Solitario clásico" data-shape="Redondo" data-metal="oro-blanco" data-carat="1.00" aria-label="Vista previa del anillo">
					<div class="murg-builder-preview__stage">
						<div class="murg-builder-preview__band" aria-hidden="true"></div>
						<div class="murg-builder-preview__pave" aria-hidden="true"></div>
						<div class="murg-builder-preview__side-stones" aria-hidden="true">
							<span></span><span></span>
						</div>
						<div class="murg-builder-preview__halo" aria-hidden="true"></div>
						<div class="murg-builder-preview__stone" aria-hidden="true"></div>
					</div>
					<p class="murg-builder-preview__caption">Vista referencial para cotización</p>
				</div>
				<dl>
					<div><dt>Modelo</dt><dd data-summary="Modelo">Solitario clásico</dd></div>
					<div><dt>Forma</dt><dd data-summary="Forma">Redondo</dd></div>
					<div><dt>Metal</dt><dd data-summary="Metal">Oro blanco 18K</dd></div>
					<div><dt>Quilates</dt><dd data-summary="Quilates">1.00 ct</dd></div>
					<div><dt>Origen</dt><dd data-summary="Origen">Natural</dd></div>
					<div><dt>Talla</dt><dd data-summary="Talla">6</dd></div>
				</dl>
				<div class="murg-builder-summary__notes" data-summary-notes hidden></div>
				<a class="murg-btn murg-btn--dark murg-builder-summary__cta" href="<?php echo esc_url( 'https://wa.me/' . $wa_number ); ?>" target="_blank" rel="noopener noreferrer" data-builder-whatsapp>
					Solicitar cotización
				</a>
				<p class="murg-builder-summary__fine">Un asesor revisará tus selecciones y te responderá con disponibilidad y próximos pasos.</p>
			</aside>
		</div>
	</section>
</main>

<div class="murg-sizeguide"
     id="murg-sizeguide"
     role="dialog"
     aria-modal="true"
     aria-labelledby="murg-sizeguide-title"
     aria-hidden="true">
	<div class="murg-sizeguide__backdrop" data-close="murg-sizeguide" aria-hidden="true"></div>
	<div class="murg-sizeguide__panel" role="document">
		<header class="murg-sizeguide__header">
			<h2 class="murg-sizeguide__title murg-serif" id="murg-sizeguide-title">Guía de Tallas</h2>
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
			<p style="margin:0 0 24px;font-family:'Inter',sans-serif;font-size:14px;color:#666;line-height:1.6;">
				Mide el diámetro interior de un anillo que te quede bien, o consulta con nuestro equipo para una medición presencial.
			</p>
			<div class="murg-sizeguide-grid">
				<?php
				$guide_sizes = [
					[ '3.5', '14.4' ], [ '4', '14.8' ], [ '4.5', '15.2' ], [ '5', '15.6' ],
					[ '5.5', '16.0' ], [ '6', '16.4' ], [ '6.5', '16.9' ], [ '7', '17.3' ],
					[ '7.5', '17.7' ], [ '8', '18.2' ], [ '8.5', '18.6' ], [ '9', '19.0' ],
					[ '9.5', '19.4' ], [ '10', '19.8' ], [ '10.5', '20.2' ],
					[ '11', '20.6' ], [ '11.5', '21.0' ], [ '12', '21.4' ],
				];
				foreach ( $guide_sizes as $guide_size ) : ?>
				<div class="murg-sizeguide-grid__item">
					<div class="murg-sizeguide-grid__ring">
						<span class="murg-sizeguide-grid__num"><?php echo esc_html( $guide_size[0] ); ?></span>
					</div>
					<span class="murg-sizeguide-grid__mm"><?php echo esc_html( $guide_size[1] ); ?> mm</span>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="murg-sizeguide-grid__tip">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--murg-gold)" stroke-width="1.5" aria-hidden="true"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
				<span>¿No estás seguro de tu talla? Escríbenos por WhatsApp y te ayudamos.</span>
			</div>
		</div>
	</div>
</div>

<?php get_template_part( 'template-parts/murg-footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
