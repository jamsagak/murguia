<?php
/**
 * Template Name: Alta Joyería
 * Template Post Type: page
 *
 * Landing de experiencia para la colección de Alta Joyería.
 * Asignar en Páginas > Alta Joyería > Atributos > Plantilla: Alta Joyería.
 * Contenido editable desde: Ajustes de Diseño > Alta Joyería (slug: alta-joyeria-page)
 * Prefijo ACF: aj_
 */
defined( 'ABSPATH' ) || exit;

function murg_aj( $key, $fallback = '' ) {
	return murguia_ajuste( $key, $fallback, 'alta-joyeria-page' );
}

// ── Campos del hero ──────────────────────────────────────────
$aj_hero_eyebrow  = murg_aj( 'aj_hero_eyebrow',  'Desde 1962' );
$aj_hero_titulo   = murg_aj( 'aj_hero_titulo',   'Alta <em>Joyería</em>' );
$aj_hero_sub      = murg_aj( 'aj_hero_sub',      'Piezas únicas concebidas para quienes entienden la diferencia entre poseer una joya y atesorar una obra de arte.' );
$aj_hero_imagen   = murg_aj( 'aj_hero_imagen',   [] );

// ── Campos de la intro editorial ────────────────────────────
$aj_intro_titulo  = murg_aj( 'aj_intro_titulo',  'Cada piedra, <em>una historia</em>' );
$aj_intro_texto   = murg_aj( 'aj_intro_texto',   'Seleccionamos personalmente cada diamante, zafiro, rubí y esmeralda. Trabajamos únicamente con oro de 18 quilates y nuestros orfebres crean cada pieza a mano en nuestro taller de San Isidro. No producimos en serie — cada creación es irrepetible.' );
$aj_intro_imagen  = murg_aj( 'aj_intro_imagen',  [] );

// ── Campos de consulta ──────────────────────────────────────
$aj_whatsapp      = murg_aj( 'aj_whatsapp',      '' );
$aj_email         = murg_aj( 'aj_email',         '' );
$aj_cita_url      = murg_aj( 'aj_cita_url',      home_url( '/contact-us/' ) );

// ── Productos de la categoría Alta Joyería ──────────────────
$aj_query = new WP_Query( [
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'tax_query'      => [ [
		'taxonomy' => 'product_cat',
		'field'    => 'slug',
		'terms'    => 'alta-joyeria',
	] ],
	'meta_key' => 'total_sales',
	'orderby'  => 'meta_value_num',
	'order'    => 'DESC',
] );

$aj_products = [];
if ( $aj_query->have_posts() ) {
	while ( $aj_query->have_posts() ) {
		$aj_query->the_post();
		$pid    = get_the_ID();
		$p      = wc_get_product( $pid );
		if ( ! $p ) continue;
		$img_id = $p->get_image_id();
		// Galería de imágenes adicionales
		$gallery_ids = $p->get_gallery_image_ids();
		$gallery_url = ! empty( $gallery_ids )
			? wp_get_attachment_image_url( $gallery_ids[0], 'large' )
			: '';
		$sku  = $p->get_sku();
		$mat  = get_post_meta( $pid, '_murguia_material', true );
		$ref  = array_filter( [ $sku ? 'Ref. ' . $sku : '', $mat ] );
		$desc = $p->get_short_description() ?: $p->get_description();
		$aj_products[] = [
			'id'          => $pid,
			'name'        => $p->get_name(),
			'url'         => $p->get_permalink(),
			'img'         => $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '',
			'img2'        => $gallery_url,
			'alt'         => $img_id ? get_post_meta( $img_id, '_wp_attachment_image_alt', true ) : $p->get_name(),
			'ref'         => $ref ? implode( ' · ', $ref ) : '',
			'desc'        => $desc ? wp_strip_all_tags( $desc ) : '',
		];
	}
	wp_reset_postdata();
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Alta Joyería · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-aj-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<!-- ============================================================
     HERO — imagen de fondo + título sobre negro
     ============================================================ -->
<section class="murg-aj-hero"
	<?php if ( ! empty( $aj_hero_imagen['url'] ) ) : ?>
	style="background-image: url('<?php echo esc_url( $aj_hero_imagen['url'] ); ?>')"
	<?php endif; ?>>
	<div class="murg-aj-hero__vignette"></div>
	<div class="murg-aj-hero__content">
		<div class="murg-eyebrow murg-aj-hero__eyebrow"><?php echo esc_html( $aj_hero_eyebrow ); ?></div>
		<h1 class="murg-aj-hero__titulo murg-serif">
			<?php echo wp_kses( $aj_hero_titulo, [ 'em' => [] ] ); ?>
		</h1>
		<div class="murg-gold-line murg-aj-hero__line" aria-hidden="true"></div>
		<p class="murg-aj-hero__sub"><?php echo esc_html( $aj_hero_sub ); ?></p>
		<a href="#aj-piezas" class="murg-aj-hero__scroll" aria-label="Ver piezas">
			<span class="murg-aj-hero__scroll-line"></span>
		</a>
	</div>
</section>

<!-- ============================================================
     INTRO EDITORIAL — texto + imagen de atelier
     ============================================================ -->
<section class="murg-aj-intro">
	<div class="murg-aj-intro__grid">
		<div class="murg-aj-intro__text" data-reveal>
			<div class="murg-eyebrow" style="color: var(--murg-gold); margin-bottom: 24px;">Nuestra Filosofía</div>
			<h2 class="murg-aj-intro__titulo murg-serif">
				<?php echo wp_kses( $aj_intro_titulo, [ 'em' => [] ] ); ?>
			</h2>
			<div class="murg-gold-line" style="margin: 28px 0;"></div>
			<p class="murg-aj-intro__p"><?php echo esc_html( $aj_intro_texto ); ?></p>
			<a href="<?php echo esc_url( $aj_cita_url ); ?>" class="murg-btn murg-btn--gold murg-aj-intro__cta">
				Agendar una visita
			</a>
		</div>
		<?php if ( ! empty( $aj_intro_imagen['url'] ) ) : ?>
		<div class="murg-aj-intro__img" data-reveal>
			<img src="<?php echo esc_url( $aj_intro_imagen['url'] ); ?>"
			     alt="<?php echo esc_attr( $aj_intro_imagen['alt'] ?? 'Atelier Murguía' ); ?>"
			     loading="lazy">
		</div>
		<?php else : ?>
		<div class="murg-aj-intro__img murg-aj-intro__img--placeholder" data-reveal aria-hidden="true">
			<div class="murg-aj-intro__placeholder-inner">
				<div class="murg-gold-line" style="width: 40px; margin: 0 auto 20px;"></div>
				<span class="murg-serif" style="font-size: 13px; color: rgba(245,240,232,0.4); letter-spacing: 0.15em;">ATELIER · SAN ISIDRO</span>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>

<!-- ============================================================
     PIEZAS — landing tipo lookbook, una por una
     ============================================================ -->
<section class="murg-aj-piezas" id="aj-piezas">

	<?php if ( ! empty( $aj_products ) ) : ?>

		<?php foreach ( $aj_products as $idx => $piece ) :
			// Alternar orientación: par → imagen izquierda | impar → imagen derecha
			$flip = ( $idx % 2 !== 0 ) ? ' murg-aj-pieza--flip' : '';
		?>
		<article class="murg-aj-pieza<?php echo esc_attr( $flip ); ?>" data-reveal>

			<!-- Imagen -->
			<div class="murg-aj-pieza__img-wrap">
				<?php if ( $piece['img'] ) : ?>
				<div class="murg-aj-pieza__img-inner">
					<img class="murg-aj-pieza__img murg-aj-pieza__img--main"
					     src="<?php echo esc_url( $piece['img'] ); ?>"
					     alt="<?php echo esc_attr( $piece['alt'] ); ?>"
					     loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>">
					<?php if ( $piece['img2'] ) : ?>
					<img class="murg-aj-pieza__img murg-aj-pieza__img--hover"
					     src="<?php echo esc_url( $piece['img2'] ); ?>"
					     alt="<?php echo esc_attr( $piece['alt'] ); ?> — detalle"
					     loading="lazy"
					     aria-hidden="true">
					<?php endif; ?>
				</div>
				<div class="murg-aj-pieza__num" aria-hidden="true"><?php printf( '%02d', $idx + 1 ); ?></div>
				<?php endif; ?>
			</div>

			<!-- Info -->
			<div class="murg-aj-pieza__info">
				<div class="murg-eyebrow murg-aj-pieza__eyebrow">Alta Joyería · Murguía</div>
				<h3 class="murg-aj-pieza__nombre murg-serif">
					<a href="<?php echo esc_url( $piece['url'] ); ?>"><?php echo esc_html( $piece['name'] ); ?></a>
				</h3>
				<?php if ( $piece['ref'] ) : ?>
				<div class="murg-aj-pieza__ref"><?php echo esc_html( $piece['ref'] ); ?></div>
				<?php endif; ?>
				<?php if ( $piece['desc'] ) : ?>
				<p class="murg-aj-pieza__desc"><?php echo esc_html( $piece['desc'] ); ?></p>
				<?php endif; ?>
				<div class="murg-aj-pieza__divider" aria-hidden="true"></div>
				<div class="murg-aj-pieza__precio-label">Precio bajo consulta</div>
				<div class="murg-aj-pieza__actions">
					<?php if ( $aj_whatsapp ) : ?>
					<a href="<?php echo esc_url( 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $aj_whatsapp ) . '?text=' . rawurlencode( 'Hola, me interesa la pieza: ' . $piece['name'] . ' (' . $piece['ref'] . ')' ) ); ?>"
					   class="murg-btn murg-btn--gold murg-aj-pieza__btn-wa"
					   target="_blank" rel="noopener noreferrer">
						<span class="murg-whatsapp-dot" aria-hidden="true"></span>
						Consultar por WhatsApp
					</a>
					<?php else : ?>
					<a href="<?php echo esc_url( $aj_cita_url . '?pieza=' . rawurlencode( $piece['name'] ) ); ?>"
					   class="murg-btn murg-btn--gold murg-aj-pieza__btn-wa">
						Consultar esta pieza
					</a>
					<?php endif; ?>
					<a href="<?php echo esc_url( $piece['url'] ); ?>" class="murg-aj-pieza__btn-detail">
						Ver ficha completa →
					</a>
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

<!-- ============================================================
     CIERRE — CTA para agendar cita
     ============================================================ -->
<section class="murg-aj-cta-final" data-reveal>
	<div class="murg-gold-line" aria-hidden="true"></div>
	<div class="murg-eyebrow murg-aj-cta-final__eyebrow">Experiencia Personalizada</div>
	<h2 class="murg-aj-cta-final__titulo murg-serif">
		Una pieza hecha <em>para usted</em>
	</h2>
	<p class="murg-aj-cta-final__sub">
		Recibimos a nuestros clientes con cita previa para una atención íntima y sin apuros en nuestro atelier de San Isidro.
	</p>
	<div class="murg-aj-cta-final__btns">
		<a href="<?php echo esc_url( $aj_cita_url ); ?>" class="murg-btn murg-btn--gold">
			Agendar una cita
		</a>
		<?php if ( $aj_whatsapp ) : ?>
		<a href="<?php echo esc_url( 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $aj_whatsapp ) ); ?>"
		   class="murg-btn" target="_blank" rel="noopener noreferrer"
		   style="color: var(--murg-cream); border-color: rgba(245,240,232,0.3);">
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
