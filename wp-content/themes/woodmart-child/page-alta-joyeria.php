<?php
/**
 * Template Name: Alta Joyería
 * Template Post Type: page
 *
 * Landing no transaccional para la colección de Alta Joyería.
 */
defined( 'ABSPATH' ) || exit;

function murg_aj( $key, $fallback = '' ) {
	return murguia_ajuste( $key, $fallback, 'alta-joyeria-page' );
}

$aj_img_base       = get_stylesheet_directory_uri() . '/assets/img/alta-joyeria/';
$aj_hero_fallback = $aj_img_base . 'altajoyeria-1.webp';
$aj_intro_fallback = $aj_img_base . 'altajoyeria-1.webp';
$aj_private_image = $aj_img_base . 'altajoyeria-2.webp';

$aj_hero_eyebrow = murg_aj( 'aj_hero_eyebrow', 'Alta Joyería' );
$aj_hero_titulo  = murg_aj( 'aj_hero_titulo', 'Joyas que trascienden <em>el tiempo</em>' );
$aj_hero_sub     = murg_aj( 'aj_hero_sub', 'Piezas excepcionales trabajadas a mano en oro de 18 quilates y engastadas con piedras preciosas de la más alta calidad.' );
$aj_hero_imagen  = murg_aj( 'aj_hero_imagen', [] );

$aj_intro_titulo = murg_aj( 'aj_intro_titulo', 'Excelencia artesanal, <em>materiales excepcionales</em>' );
$aj_intro_texto  = murg_aj( 'aj_intro_texto', 'En Joyería Murguía, cada joya nace del encuentro entre excelencia artesanal, materiales excepcionales y un diseño que celebra lo eterno. Nuestras piezas más exclusivas están trabajadas a mano en oro de 18 quilates y engastadas con piedras preciosas de la más alta calidad.' );
$aj_intro_imagen = murg_aj( 'aj_intro_imagen', [] );
$aj_intro_texto_2 = 'Cada diseño es el resultado de un proceso minucioso, donde se cuidan hasta los más pequeños detalles: desde la pureza del oro y el corte perfecto de cada gema, hasta el acabado impecable que convierte una joya en una verdadera obra de arte.';

$aj_whatsapp = murg_aj( 'aj_whatsapp', '' );
$aj_cita_url = murg_aj( 'aj_cita_url', home_url( '/contacto/' ) );
if ( ! $aj_whatsapp ) {
	$aj_whatsapp = murguia_ajuste( 'ct_whatsapp', '51114218800', 'contacto' );
}
$aj_whatsapp_clean = preg_replace( '/[^0-9]/', '', (string) $aj_whatsapp );

$aj_query = new WP_Query( [
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'tax_query'      => [ [
		'taxonomy' => 'product_cat',
		'field'    => 'slug',
		'terms'    => 'alta-joyeria',
	] ],
	'meta_key'       => 'total_sales',
	'orderby'        => 'meta_value_num',
	'order'          => 'DESC',
] );

$aj_products = [];
if ( $aj_query->have_posts() ) {
	while ( $aj_query->have_posts() ) {
		$aj_query->the_post();
		$pid = get_the_ID();
		$p   = wc_get_product( $pid );
		if ( ! $p ) {
			continue;
		}

		$img_id      = $p->get_image_id();
		$gallery_ids = $p->get_gallery_image_ids();
		$gallery_url = ! empty( $gallery_ids ) ? wp_get_attachment_image_url( $gallery_ids[0], 'large' ) : '';
		$sku         = $p->get_sku();
		$mat         = get_post_meta( $pid, '_murguia_material', true );
		$ref         = array_filter( [ $sku ? 'Ref. ' . $sku : '', $mat ] );
		$desc        = $p->get_short_description() ?: $p->get_description();

		$aj_products[] = [
			'id'   => $pid,
			'name' => $p->get_name(),
			'url'  => $p->get_permalink(),
			'img'  => $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '',
			'img2' => $gallery_url,
			'alt'  => $img_id ? get_post_meta( $img_id, '_wp_attachment_image_alt', true ) : $p->get_name(),
			'ref'  => $ref ? implode( ' · ', $ref ) : '',
			'desc' => $desc ? wp_strip_all_tags( $desc ) : '',
		];
	}
	wp_reset_postdata();
}

$hero_bg = ! empty( $aj_hero_imagen['url'] ) ? $aj_hero_imagen['url'] : $aj_hero_fallback;
$intro_img = ! empty( $aj_intro_imagen['url'] ) ? $aj_intro_imagen['url'] : $aj_intro_fallback;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-aj-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<section class="murg-aj-hero" style="background-image: url('<?php echo esc_url( $hero_bg ); ?>')">
	<div class="murg-aj-hero__vignette"></div>
	<div class="murg-aj-hero__content">
		<div class="murg-eyebrow murg-aj-hero__eyebrow"><?php echo esc_html( $aj_hero_eyebrow ); ?></div>
		<h1 class="murg-aj-hero__titulo murg-serif"><?php echo wp_kses( $aj_hero_titulo, [ 'em' => [] ] ); ?></h1>
		<div class="murg-gold-line murg-aj-hero__line" aria-hidden="true"></div>
		<p class="murg-aj-hero__sub"><?php echo esc_html( $aj_hero_sub ); ?></p>
		<?php if ( $aj_whatsapp_clean ) : ?>
			<a href="<?php echo esc_url( 'https://wa.me/' . $aj_whatsapp_clean . '?text=' . rawurlencode( 'Hola, quisiera agendar una cita privada para Alta Joyería.' ) ); ?>" class="murg-btn murg-btn--gold murg-aj-hero__cta" target="_blank" rel="noopener noreferrer">
				<span class="murg-whatsapp-dot" aria-hidden="true"></span>
				Agendar cita privada
			</a>
		<?php endif; ?>
	</div>
</section>

<section class="murg-aj-intro">
	<div class="murg-aj-intro__grid">
		<div class="murg-aj-intro__text" data-reveal>
			<div class="murg-eyebrow" style="color: var(--murg-gold); margin-bottom: 24px;">Nuestra filosofía</div>
			<h2 class="murg-aj-intro__titulo murg-serif"><?php echo wp_kses( $aj_intro_titulo, [ 'em' => [] ] ); ?></h2>
			<div class="murg-gold-line" style="margin: 28px 0;"></div>
			<p class="murg-aj-intro__p"><?php echo esc_html( $aj_intro_texto ); ?></p>
			<p class="murg-aj-intro__p"><?php echo esc_html( $aj_intro_texto_2 ); ?></p>
		</div>
		<div class="murg-aj-intro__img" data-reveal>
			<img src="<?php echo esc_url( $intro_img ); ?>" alt="<?php echo esc_attr( $aj_intro_imagen['alt'] ?? 'Alta Joyería Murguía' ); ?>" loading="lazy">
		</div>
	</div>
</section>

<section class="murg-aj-private">
	<div class="murg-aj-private__grid">
		<figure class="murg-aj-private__media" data-reveal>
			<img src="<?php echo esc_url( $aj_private_image ); ?>" alt="Selección de Alta Joyería Murguía" loading="lazy">
		</figure>
		<div class="murg-aj-private__copy" data-reveal>
			<div class="murg-eyebrow">Selección privada</div>
			<h2 class="murg-serif">Joyas pensadas para heredarse</h2>
			<p>Estas piezas están pensadas para quienes valoran lo extraordinario, para quienes saben que algunas joyas no solo se lucen, sino que se heredan.</p>
			<p>Explora nuestra selección y descubre el alma de Murguía en cada creación. ¿Deseas conocer más? Agenda una cita privada y déjate guiar por una experiencia de lujo personalizada.</p>
			<?php if ( $aj_whatsapp_clean ) : ?>
				<a href="<?php echo esc_url( 'https://wa.me/' . $aj_whatsapp_clean . '?text=' . rawurlencode( 'Hola, quisiera agendar una cita privada para Alta Joyería.' ) ); ?>" class="murg-btn murg-btn--gold" target="_blank" rel="noopener noreferrer">
					<span class="murg-whatsapp-dot" aria-hidden="true"></span>
					Agendar cita privada
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( $aj_cita_url ); ?>" class="murg-btn murg-btn--gold">Agendar cita privada</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="murg-aj-piezas" id="aj-piezas">
	<div class="murg-aj-piezas__intro" data-reveal>
		<div class="murg-eyebrow">Consulta privada</div>
		<h2 class="murg-serif">Piezas seleccionadas bajo atención personalizada</h2>
		<p>Alta Joyería se presenta como una experiencia privada. Cada pieza se revisa con un asesor para confirmar disponibilidad, detalles de gemas y condiciones de entrega.</p>
	</div>

	<?php if ( ! empty( $aj_products ) ) : ?>
		<?php foreach ( $aj_products as $idx => $piece ) : ?>
			<?php $flip = ( $idx % 2 !== 0 ) ? ' murg-aj-pieza--flip' : ''; ?>
			<article class="murg-aj-pieza<?php echo esc_attr( $flip ); ?>" data-reveal>
				<div class="murg-aj-pieza__img-wrap">
					<?php if ( $piece['img'] ) : ?>
						<div class="murg-aj-pieza__img-inner">
							<img class="murg-aj-pieza__img murg-aj-pieza__img--main" src="<?php echo esc_url( $piece['img'] ); ?>" alt="<?php echo esc_attr( $piece['alt'] ); ?>" loading="<?php echo 0 === $idx ? 'eager' : 'lazy'; ?>">
							<?php if ( $piece['img2'] ) : ?>
								<img class="murg-aj-pieza__img murg-aj-pieza__img--hover" src="<?php echo esc_url( $piece['img2'] ); ?>" alt="<?php echo esc_attr( $piece['alt'] ); ?> — detalle" loading="lazy" aria-hidden="true">
							<?php endif; ?>
						</div>
						<div class="murg-aj-pieza__num" aria-hidden="true"><?php printf( '%02d', $idx + 1 ); ?></div>
					<?php endif; ?>
				</div>
				<div class="murg-aj-pieza__info">
					<div class="murg-eyebrow murg-aj-pieza__eyebrow">Alta Joyería · Murguía</div>
					<h3 class="murg-aj-pieza__nombre murg-serif"><?php echo esc_html( $piece['name'] ); ?></h3>
					<?php if ( $piece['ref'] ) : ?>
						<div class="murg-aj-pieza__ref"><?php echo esc_html( $piece['ref'] ); ?></div>
					<?php endif; ?>
					<?php if ( $piece['desc'] ) : ?>
						<p class="murg-aj-pieza__desc"><?php echo esc_html( $piece['desc'] ); ?></p>
					<?php endif; ?>
					<div class="murg-aj-pieza__divider" aria-hidden="true"></div>
					<div class="murg-aj-pieza__precio-label">Cotización privada</div>
					<div class="murg-aj-pieza__actions">
						<?php if ( $aj_whatsapp_clean ) : ?>
							<a href="<?php echo esc_url( 'https://wa.me/' . $aj_whatsapp_clean . '?text=' . rawurlencode( 'Hola, me interesa la pieza: ' . $piece['name'] . ( $piece['ref'] ? ' (' . $piece['ref'] . ')' : '' ) ) ); ?>" class="murg-btn murg-btn--gold murg-aj-pieza__btn-wa" target="_blank" rel="noopener noreferrer">
								<span class="murg-whatsapp-dot" aria-hidden="true"></span>
								Solicitar cotización privada
							</a>
						<?php else : ?>
							<a href="<?php echo esc_url( $aj_cita_url . '?pieza=' . rawurlencode( $piece['name'] ) ); ?>" class="murg-btn murg-btn--gold murg-aj-pieza__btn-wa">Solicitar cotización privada</a>
						<?php endif; ?>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="murg-aj-empty">
			<p class="murg-serif" style="font-size: 28px; color: rgba(245,240,232,0.4);">La colección estará disponible pronto.</p>
			<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
				<p style="font-size: 11px; color: rgba(245,240,232,0.3); margin-top: 12px;">[ Admin: agrega productos a la categoría WooCommerce "alta-joyeria" ]</p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</section>

<section class="murg-aj-cta-final" data-reveal>
	<div class="murg-gold-line" aria-hidden="true"></div>
	<div class="murg-eyebrow murg-aj-cta-final__eyebrow">Experiencia personalizada</div>
	<h2 class="murg-aj-cta-final__titulo murg-serif">Una pieza hecha <em>para usted</em></h2>
	<p class="murg-aj-cta-final__sub">Recibimos a nuestros clientes con cita previa para una atención íntima y sin apuros en nuestro atelier de San Isidro.</p>
	<div class="murg-aj-cta-final__btns">
		<a href="<?php echo esc_url( $aj_cita_url ); ?>" class="murg-btn murg-btn--gold">Agendar una cita</a>
		<?php if ( $aj_whatsapp_clean ) : ?>
			<a href="<?php echo esc_url( 'https://wa.me/' . $aj_whatsapp_clean ); ?>" class="murg-btn" target="_blank" rel="noopener noreferrer" style="color: var(--murg-cream); border-color: rgba(245,240,232,0.3);">
				<span class="murg-whatsapp-dot" aria-hidden="true"></span>
				WhatsApp
			</a>
		<?php endif; ?>
	</div>
	<div class="murg-gold-line" style="margin-top: 56px;" aria-hidden="true"></div>
</section>

<?php get_template_part( 'template-parts/murg-footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
