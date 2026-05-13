<?php
/**
 * Template Name: Las 4Cs
 * Template Post Type: page
 */
defined( 'ABSPATH' ) || exit;

function murg_4cs( $key, $fallback = '' ) {
	return murguia_ajuste( $key, $fallback, 'las-4cs-page' );
}

function murg_4cs_img( $image, $size = 'large', $alt = '', $eager = false ) {
	if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
		return wp_get_attachment_image( (int) $image['ID'], $size, false, [
			'loading' => $eager ? 'eager' : 'lazy',
			'alt'     => $image['alt'] ?: $alt,
		] );
	}
	if ( is_numeric( $image ) ) {
		return wp_get_attachment_image( (int) $image, $size, false, [
			'loading' => $eager ? 'eager' : 'lazy',
			'alt'     => get_post_meta( (int) $image, '_wp_attachment_image_alt', true ) ?: $alt,
		] );
	}
	return '';
}

$hero_eyebrow = murg_4cs( 'c4_hero_eyebrow' );
$hero_title   = murg_4cs( 'c4_hero_titulo', get_the_title() );
$hero_sub     = murg_4cs( 'c4_hero_subtitulo' );
$hero_intro   = murg_4cs( 'c4_hero_intro' );
$hero_image   = murg_4cs( 'c4_hero_imagen', [] );
$sections     = murg_4cs( 'c4_secciones', [] );
$color_scale  = murg_4cs( 'c4_color_escala', [] );
$clarity      = murg_4cs( 'c4_claridad_escala', [] );
$cut_terms    = murg_4cs( 'c4_corte_conceptos', [] );
$carats       = murg_4cs( 'c4_carataje_ejemplos', [] );

if ( is_array( $sections ) ) {
	usort( $sections, function( $a, $b ) {
		return (int) ( $a['orden'] ?? 0 ) <=> (int) ( $b['orden'] ?? 0 );
	} );
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '-', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-4cs-page' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-4cs" id="contenido">
	<section class="murg-4cs-hero">
		<div class="murg-4cs-hero__copy" data-reveal>
			<?php if ( $hero_eyebrow ) : ?><p class="murg-ac-eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p><?php endif; ?>
			<h1><?php echo esc_html( $hero_title ); ?></h1>
			<?php if ( $hero_sub ) : ?><p class="murg-4cs-hero__sub"><?php echo esc_html( $hero_sub ); ?></p><?php endif; ?>
			<?php if ( $hero_intro ) : ?><div class="murg-4cs-hero__intro"><?php echo wp_kses_post( wpautop( $hero_intro ) ); ?></div><?php endif; ?>
		</div>
		<div class="murg-4cs-hero__media" data-reveal>
			<?php echo murg_4cs_img( $hero_image, 'large', $hero_title, true ); ?>
			<?php if ( ! murg_4cs_img( $hero_image, 'large', $hero_title, true ) ) : ?><div class="murg-4cs-hero__placeholder" aria-hidden="true"><span>4C</span></div><?php endif; ?>
		</div>
	</section>

	<?php if ( $sections ) : ?>
	<section class="murg-4cs-sections">
		<?php foreach ( $sections as $index => $section ) :
			$title = $section['titulo'] ?? '';
			$points = array_filter( array_map( 'trim', explode( "\n", $section['puntos'] ?? '' ) ) );
		?>
		<article class="murg-4cs-section<?php echo $index % 2 ? ' murg-4cs-section--flip' : ''; ?>" data-reveal>
			<div class="murg-4cs-section__media">
				<?php
				$main_image = murg_4cs_img( $section['imagen'] ?? [], 'large', $title );
				if ( $main_image ) {
					echo $main_image;
				} else {
					echo '<div class="murg-4cs-section__placeholder" aria-hidden="true"><span>' . esc_html( mb_substr( $title, 0, 1 ) ) . '</span></div>';
				}
				?>
			</div>
			<div class="murg-4cs-section__copy">
				<p class="murg-ac-eyebrow"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></p>
				<h2><?php echo esc_html( $title ); ?></h2>
				<?php if ( ! empty( $section['subtitulo'] ) ) : ?><h3><?php echo esc_html( $section['subtitulo'] ); ?></h3><?php endif; ?>
				<?php if ( ! empty( $section['descripcion'] ) ) : ?><div class="murg-4cs-section__text"><?php echo wp_kses_post( wpautop( $section['descripcion'] ) ); ?></div><?php endif; ?>
				<?php if ( $points ) : ?>
				<ul class="murg-4cs-section__points">
					<?php foreach ( $points as $point ) : ?><li><?php echo esc_html( $point ); ?></li><?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>
		</article>
		<?php endforeach; ?>
	</section>
	<?php endif; ?>

	<section class="murg-4cs-scales">
		<div class="murg-4cs-scales__inner">
			<?php if ( $color_scale ) : ?>
			<div class="murg-4cs-scale" data-reveal>
				<h2><?php esc_html_e( 'Escala de color', 'woodmart-child' ); ?></h2>
				<div class="murg-4cs-color-scale">
					<?php foreach ( $color_scale as $item ) : ?>
					<div>
						<strong><?php echo esc_html( $item['grado'] ?? '' ); ?></strong>
						<span><?php echo esc_html( $item['etiqueta'] ?? '' ); ?></span>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( $clarity ) : ?>
			<div class="murg-4cs-scale" data-reveal>
				<h2><?php esc_html_e( 'Escala de claridad', 'woodmart-child' ); ?></h2>
				<div class="murg-4cs-clarity">
					<?php foreach ( $clarity as $item ) : ?>
					<div><strong><?php echo esc_html( $item['grado'] ?? '' ); ?></strong><span><?php echo esc_html( $item['descripcion'] ?? '' ); ?></span></div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( $cut_terms ) : ?>
			<div class="murg-4cs-scale murg-4cs-scale--wide" data-reveal>
				<h2><?php esc_html_e( 'Conceptos de corte', 'woodmart-child' ); ?></h2>
				<div class="murg-4cs-cut">
					<?php foreach ( $cut_terms as $item ) : ?>
					<article><h3><?php echo esc_html( $item['titulo'] ?? '' ); ?></h3><p><?php echo esc_html( $item['texto'] ?? '' ); ?></p></article>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( $carats ) : ?>
			<div class="murg-4cs-scale murg-4cs-scale--wide" data-reveal>
				<h2><?php esc_html_e( 'Ejemplos de carataje', 'woodmart-child' ); ?></h2>
				<div class="murg-4cs-carats">
					<?php foreach ( $carats as $item ) : ?>
					<div>
						<?php echo murg_4cs_img( $item['imagen'] ?? [], 'thumbnail', $item['valor'] ?? '' ); ?>
						<strong><?php echo esc_html( $item['valor'] ?? '' ); ?></strong>
						<?php if ( ! empty( $item['etiqueta'] ) ) : ?><span><?php echo esc_html( $item['etiqueta'] ); ?></span><?php endif; ?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="murg-4cs-cta" data-reveal>
		<h2><?php echo esc_html( murg_4cs( 'c4_cta_titulo' ) ); ?></h2>
		<?php if ( murg_4cs( 'c4_cta_texto' ) ) : ?><p><?php echo esc_html( murg_4cs( 'c4_cta_texto' ) ); ?></p><?php endif; ?>
		<div class="murg-4cs-cta__actions">
			<?php if ( murg_4cs( 'c4_cta_principal_url' ) && murg_4cs( 'c4_cta_principal_texto' ) ) : ?><a class="murg-btn murg-btn--dark" href="<?php echo esc_url( murg_4cs( 'c4_cta_principal_url' ) ); ?>"><?php echo esc_html( murg_4cs( 'c4_cta_principal_texto' ) ); ?></a><?php endif; ?>
			<?php if ( murg_4cs( 'c4_cta_secundario_url' ) && murg_4cs( 'c4_cta_secundario_texto' ) ) : ?><a class="murg-ac-link" href="<?php echo esc_url( murg_4cs( 'c4_cta_secundario_url' ) ); ?>"><?php echo esc_html( murg_4cs( 'c4_cta_secundario_texto' ) ); ?></a><?php endif; ?>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
