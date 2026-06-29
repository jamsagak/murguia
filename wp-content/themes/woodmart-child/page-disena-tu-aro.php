<?php
/**
 * Template Name: Diseña tu aro
 *
 * Configurador consultivo para aros de matrimonio bajo pedido.
 * Mismo patrón que page-disena-tu-anillo.php pero con menos variables:
 * modelo, metal, ancho, talla, grabado + tipografía.
 */
defined( 'ABSPATH' ) || exit;

$wa_url_raw = murguia_ajuste( 'aro_whatsapp_url', '', 'aros-matrimonio-page' );
if ( ! $wa_url_raw ) {
	$wa_url_raw = murguia_ajuste( 'ac_whatsapp_url', 'https://wa.me/51114218800', 'anillos-compromiso-page' );
}
$wa_number = preg_replace( '/[^0-9]/', '', $wa_url_raw );
if ( ! $wa_number ) {
	$wa_number = '51114218800';
}

$hero_eyebrow = murguia_ajuste( 'aro_hero_eyebrow', 'Diseña tu aro', 'aros-matrimonio-page' );
$hero_title   = murguia_ajuste( 'aro_hero_titulo',  'Aros de matrimonio hechos para ustedes.', 'aros-matrimonio-page' );
$hero_intro   = murguia_ajuste( 'aro_hero_intro',   'Configura el modelo, metal, ancho y grabado de tu aro. Al finalizar te enviamos la cotización por WhatsApp con todos los detalles.', 'aros-matrimonio-page' );
$hero_note    = murguia_ajuste( 'aro_hero_nota',    'Cada aro se trabaja a pedido. Los plazos de producción se confirman al cotizar.', 'aros-matrimonio-page' );

$models = [
	[
		'label' => 'Media caña',
		'desc'  => 'Superficie curva clásica, cómoda para uso diario.',
	],
	[
		'label' => 'Cinta',
		'desc'  => 'Perfil plano y arquitectónico, ideal para grabado.',
	],
];

$metals = [
	[ 'label' => 'Oro amarillo 18K', 'color' => '#d4a843' ],
	[ 'label' => 'Oro blanco 18K',   'color' => '#e8e4dc' ],
	[ 'label' => 'Oro rosado 18K',   'color' => '#e8b4a0' ],
];

$sizes = [ '4', '4.5', '5', '5.5', '6', '6.5', '7', '7.5', '8', '8.5', '9', '9.5', '10', '10.5', '11' ];

$tipografias = [
	[ 'slug' => 'imprenta', 'label' => 'Imprenta', 'sample' => 'AMOR' ],
	[ 'slug' => 'cursiva',  'label' => 'Cursiva',  'sample' => 'amor' ],
];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-design-ring-page murg-design-aro-page' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-design-flow">
	<section class="murg-design-flow__hero">
		<div class="murg-design-flow__inner" data-reveal>
			<p class="murg-eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
			<h1><?php echo esc_html( $hero_title ); ?></h1>
			<p><?php echo esc_html( $hero_intro ); ?></p>
			<p class="murg-design-flow__note"><?php echo esc_html( $hero_note ); ?></p>
			<div class="murg-design-flow__actions">
				<a class="murg-btn murg-btn--dark" href="#murg-aro-builder">Empezar diseño</a>
				<a class="murg-btn murg-btn--ghost" href="<?php echo esc_url( 'https://wa.me/' . $wa_number ); ?>" target="_blank" rel="noopener noreferrer">Escribir por WhatsApp</a>
			</div>
		</div>
	</section>

	<section class="murg-ring-builder" id="murg-aro-builder" data-wa-number="<?php echo esc_attr( $wa_number ); ?>" aria-label="Configurador de aro de matrimonio">
		<div class="murg-ring-builder__layout">
			<div class="murg-ring-builder__steps">
				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">01</span>
					<h2>Modelo del aro</h2>
					<div class="murg-builder-options murg-builder-options--two" data-builder-group="Modelo">
						<?php foreach ( $models as $index => $model ) : ?>
						<button type="button" class="murg-builder-option<?php echo 0 === $index ? ' is-selected' : ''; ?>" data-value="<?php echo esc_attr( $model['label'] ); ?>">
							<span class="murg-builder-option__label"><?php echo esc_html( $model['label'] ); ?></span>
							<span class="murg-builder-option__desc"><?php echo esc_html( $model['desc'] ); ?></span>
						</button>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">02</span>
					<h2>Metal</h2>
					<div class="murg-builder-metals" data-builder-group="Metal">
						<?php foreach ( $metals as $index => $metal ) : ?>
						<button type="button" class="murg-builder-metal<?php echo 0 === $index ? ' is-selected' : ''; ?>" data-value="<?php echo esc_attr( $metal['label'] ); ?>">
							<span style="background: <?php echo esc_attr( $metal['color'] ); ?>"></span>
							<?php echo esc_html( $metal['label'] ); ?>
						</button>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">03</span>
					<div class="murg-builder-range__head">
						<h2>Ancho del aro</h2>
						<strong><span data-builder-output="Ancho">4.0</span> mm</strong>
					</div>
					<input class="murg-builder-range" type="range" min="2.0" max="10.0" step="0.5" value="4.0" data-builder-range="Ancho" data-decimals="1" data-suffix=" mm">
					<div class="murg-builder-range__scale"><span>2.0 mm</span><span>10.0 mm</span></div>
					<p class="murg-builder-help">El ancho típico va de 3 a 6 mm. Anchos mayores son frecuentes en aros masculinos.</p>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">04</span>
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
						<?php foreach ( $sizes as $size ) : ?>
						<button type="button" class="murg-builder-option" data-value="<?php echo esc_attr( $size ); ?>">
							<?php echo esc_html( $size ); ?>
						</button>
						<?php endforeach; ?>
					</div>
					<p class="murg-builder-help">No es obligatoria. Si no conoces la talla, podemos confirmarla durante la asesoría — igual envíanos la cotización.</p>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">05</span>
					<h2>Grabado interior</h2>
					<div class="murg-builder-engrave">
						<input type="text" class="murg-builder-engrave__input" maxlength="32" placeholder="Ej. Para siempre — 14/02/2027" data-builder-engraving="Grabado">
						<p class="murg-builder-engrave__hint">Hasta 32 caracteres. Opcional.</p>
						<div class="murg-builder-options murg-builder-options--two" data-builder-group="Tipografia">
							<?php foreach ( $tipografias as $index => $tipo ) : ?>
							<button type="button" class="murg-builder-option<?php echo 0 === $index ? ' is-selected' : ''; ?>" data-value="<?php echo esc_attr( $tipo['slug'] ); ?>">
								<span class="murg-builder-option__label"><?php echo esc_html( $tipo['label'] ); ?></span>
								<span class="murg-builder-option__sample murg-builder-option__sample--<?php echo esc_attr( $tipo['slug'] ); ?>"><?php echo esc_html( $tipo['sample'] ); ?></span>
							</button>
							<?php endforeach; ?>
						</div>
						<div class="murg-builder-engrave-preview is-empty" data-engrave-preview data-placeholder="Tu grabado aparecerá aquí" data-font="imprenta" aria-live="polite">
							Tu grabado aparecerá aquí
						</div>
					</div>
				</section>

				<section class="murg-ring-builder__step" data-reveal>
					<span class="murg-design-config__step">06</span>
					<h2>Notas opcionales</h2>
					<textarea class="murg-builder-notes" rows="4" data-builder-notes placeholder="Ej. fecha aproximada de matrimonio, presupuesto referencial, talla del otro aro."></textarea>
				</section>
			</div>

			<aside class="murg-builder-summary" aria-label="Resumen de selección">
				<p class="murg-eyebrow">Resumen</p>
				<h2>Tu aro</h2>
				<dl>
					<div><dt>Modelo</dt><dd data-summary="Modelo">Media caña</dd></div>
					<div><dt>Metal</dt><dd data-summary="Metal">Oro amarillo 18K</dd></div>
					<div><dt>Ancho</dt><dd data-summary="Ancho" data-summary-suffix=" mm">4.0 mm</dd></div>
					<div><dt>Talla</dt><dd data-summary="Talla">por confirmar</dd></div>
					<div><dt>Grabado</dt><dd data-summary="Grabado">-</dd></div>
					<div><dt>Tipografía</dt><dd data-summary="Tipografia">Imprenta</dd></div>
				</dl>
				<div class="murg-builder-summary__notes" data-summary-notes hidden></div>
				<a class="murg-btn murg-btn--dark murg-builder-summary__cta" href="<?php echo esc_url( 'https://wa.me/' . $wa_number ); ?>" target="_blank" rel="noopener noreferrer" data-builder-whatsapp>
					Solicitar cotización
				</a>
				<p class="murg-builder-summary__fine">Un asesor confirma disponibilidad, plazos y precio final.</p>
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
				Mide el diámetro interior de un aro que te quede bien, o consulta con nuestro equipo para una medición presencial.
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
