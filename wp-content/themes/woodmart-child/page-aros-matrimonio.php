<?php
/**
 * Template Name: Aros de Matrimonio
 *
 * Landing consultiva para diseñar aros de matrimonio.
 */
defined( 'ABSPATH' ) || exit;

$_aml = 'aros-matrimonio-page';

$wa_url = murguia_ajuste( 'aro_whatsapp_url', '', $_aml );
if ( ! $wa_url ) {
	$wa_url = murguia_ajuste( 'ac_whatsapp_url', 'https://wa.me/51114218800', 'anillos-compromiso-page' );
}

$aml_hero_eyebrow = murguia_ajuste( 'aml_hero_eyebrow', 'Aros de matrimonio',                                       $_aml );
$aml_hero_titulo  = murguia_ajuste( 'aml_hero_titulo',  'Diseña un aro para todos los días de la historia.',        $_aml );
$aml_hero_intro   = murguia_ajuste( 'aml_hero_intro',   'Elige modelo, metal, talla y grabado con asesoría personalizada. Creamos una propuesta a medida para cada pareja.', $_aml );
$aml_hero_nota    = murguia_ajuste( 'aml_hero_nota',    'La propuesta se confirma por cotización privada. No hay precio final automático porque cada aro depende del metal, talla, ancho y grabado.', $_aml );

$aml_cta1_texto = murguia_ajuste( 'aml_cta1_texto', 'Diseña tu aro', $_aml );
$aml_cta1_url   = murguia_ajuste( 'aml_cta1_url',   '',              $_aml );
if ( ! $aml_cta1_url ) {
	$aml_cta1_url = home_url( '/disena-tu-aro/' );
}
$aml_cta2_texto = murguia_ajuste( 'aml_cta2_texto', 'Ver catálogo', $_aml );
$aml_cta2_url   = murguia_ajuste( 'aml_cta2_url',   '',             $_aml );
if ( ! $aml_cta2_url ) {
	$aml_cta2_url = home_url( '/shop/?product_cat=aros-de-matrimonio' );
}

$aml_pasos_default = [
	[ 'titulo' => 'Modelo',  'texto' => 'Clásico, media caña, plano, comfort fit o diseño personalizado.' ],
	[ 'titulo' => 'Metal',   'texto' => 'Oro amarillo, blanco, rosado o combinaciones especiales.' ],
	[ 'titulo' => 'Talla',   'texto' => 'Validamos medida y comodidad antes de confirmar la pieza final.' ],
	[ 'titulo' => 'Grabado', 'texto' => 'Iniciales, fechas o mensajes breves para una pieza personal.' ],
];
$aml_pasos_raw = murguia_ajuste( 'aml_pasos', [], $_aml );
$aml_pasos = [];
if ( is_array( $aml_pasos_raw ) && ! empty( $aml_pasos_raw ) ) {
	foreach ( $aml_pasos_raw as $row ) {
		$tit = isset( $row['titulo'] ) ? trim( (string) $row['titulo'] ) : '';
		$tex = isset( $row['texto'] )  ? trim( (string) $row['texto'] )  : '';
		if ( ! $tit && ! $tex ) continue;
		$aml_pasos[] = [ 'titulo' => $tit, 'texto' => $tex ];
	}
}
if ( empty( $aml_pasos ) ) $aml_pasos = $aml_pasos_default;

$aml_cta_titulo = murguia_ajuste( 'aml_cta_titulo', 'Comienza con una asesoría',                                       $_aml );
$aml_cta_texto  = murguia_ajuste( 'aml_cta_texto',  'Cuéntanos qué estilo buscan y prepararemos una propuesta para ambos aros.', $_aml );
$aml_cta_boton  = murguia_ajuste( 'aml_cta_boton',  'Cotizar por WhatsApp', $_aml );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-wedding-bands-page' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-design-flow murg-design-flow--bands">
	<section class="murg-design-flow__hero">
		<div class="murg-design-flow__inner" data-reveal>
			<p class="murg-eyebrow"><?php echo esc_html( $aml_hero_eyebrow ); ?></p>
			<h1><?php echo esc_html( $aml_hero_titulo ); ?></h1>
			<p><?php echo esc_html( $aml_hero_intro ); ?></p>
			<p class="murg-design-flow__note"><?php echo esc_html( $aml_hero_nota ); ?></p>
			<div class="murg-design-flow__actions">
				<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( $aml_cta1_url ); ?>"><?php echo esc_html( $aml_cta1_texto ); ?></a>
				<a class="murg-btn murg-btn--ghost" href="<?php echo esc_url( $aml_cta2_url ); ?>"><?php echo esc_html( $aml_cta2_texto ); ?></a>
			</div>
		</div>
	</section>

	<section class="murg-design-config" aria-label="Opciones para aros">
		<div class="murg-design-config__grid murg-design-config__grid--four">
			<?php foreach ( $aml_pasos as $_idx => $_paso ) : ?>
			<div class="murg-design-config__block" data-reveal>
				<span class="murg-design-config__step"><?php echo esc_html( str_pad( (string) ( $_idx + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
				<h2><?php echo esc_html( $_paso['titulo'] ); ?></h2>
				<?php if ( ! empty( $_paso['texto'] ) ) : ?><p><?php echo esc_html( $_paso['texto'] ); ?></p><?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="murg-design-flow__cta" data-reveal>
		<h2><?php echo esc_html( $aml_cta_titulo ); ?></h2>
		<p><?php echo esc_html( $aml_cta_texto ); ?></p>
		<a class="murg-btn murg-btn--dark" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $aml_cta_boton ); ?></a>
	</section>
</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
