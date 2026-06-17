<?php
/**
 * Template Name: Contacto
 * Template Post Type: page
 *
 * Página de contacto standalone de Joyería Murguía.
 * Asignar en Páginas > Contacto > Atributos de página > Plantilla: Contacto.
 */
defined( 'ABSPATH' ) || exit;

// Helper function to fetch CPT fields
function murg_store_field( $key, $post_id, $fallback = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, $post_id );
		if ( $value !== null && $value !== false && $value !== '' && $value !== [] ) {
			return $value;
		}
	}
	$value = get_post_meta( $post_id, $key, true );
	return ( $value !== '' && $value !== [] ) ? $value : $fallback;
}

// Fetch physical stores dynamically
function murg_store_rows_from_cpt() {
	$posts = get_posts( [
		'post_type'      => 'murguia_tienda',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
		'order'          => 'ASC',
		'meta_query'     => [
			'relation' => 'OR',
			[
				'key'     => 'tienda_visible',
				'value'   => '0',
				'compare' => '!=',
			],
			[
				'key'     => 'tienda_visible',
				'compare' => 'NOT EXISTS',
			],
		],
	] );

	$rows = [];
	foreach ( $posts as $post ) {
		$rows[] = [
			'id'             => $post->ID,
			'nombre'         => murg_store_field( 'tienda_nombre', $post->ID, get_the_title( $post->ID ) ),
			'direccion'      => murg_store_field( 'tienda_direccion', $post->ID ),
			'telefono'       => murg_store_field( 'tienda_telefono', $post->ID ),
			'horario'        => murg_store_field( 'tienda_horario', $post->ID ),
			'whatsapp_url'   => murg_store_field( 'tienda_whatsapp_url', $post->ID ),
			'maps_url'       => murg_store_field( 'tienda_maps_url', $post->ID ),
			'orden'          => (int) murg_store_field( 'tienda_orden', $post->ID, $post->menu_order ),
		];
	}
	return $rows;
}

$stores = murguia_ajuste( 'tt_tiendas', [], 'tiendas' );
$stores = is_array( $stores ) && $stores ? $stores : murg_store_rows_from_cpt();
usort( $stores, function ( $a, $b ) {
	return (int) ( $a['orden'] ?? 0 ) <=> (int) ( $b['orden'] ?? 0 );
} );

$wa_general = murguia_ajuste( 'ac_whatsapp_url', 'https://wa.me/51114218800', 'anillos-compromiso-page' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contacto · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-contact-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-contact-layout">

	<!-- ── 1. Hero Section ── -->
	<section class="murg-contact-hero">
		<div class="murg-contact-hero__inner" data-reveal>
			<p class="murg-eyebrow">Atención Exclusiva</p>
			<h1>Visítenos o agende una cita</h1>
			<p class="murg-contact-hero__sub">Le recibimos en nuestras boutiques en Lima para brindarle una asesoría personalizada y a la medida de sus momentos especiales.</p>
		</div>
	</section>

	<!-- ── 2. Appointment / Contact Form Section ── -->
	<section class="murg-contact-form-section" id="formulario-cita">
		<div class="murg-contact-form-grid">
			
			<div class="murg-contact-form-info" data-reveal>
				<p class="murg-eyebrow">Agende una cita</p>
				<h2>Planifique su visita</h2>
				<p class="murg-contact-form-info__text">Le sugerimos agendar una cita previa para brindarle una atención exclusiva, privada y sin prisas en nuestro atelier. Especialmente recomendado para el diseño de anillos de compromiso, aros de matrimonio y piezas de alta joyería a medida.</p>
				
				<div class="murg-contact-form-info__perks">
					<div class="murg-perk-item">
						<h4>Asesoría GIA</h4>
						<p>Evaluación y selección de diamantes con certificación internacional.</p>
					</div>
					<div class="murg-perk-item">
						<h4>Diseños Exclusivos</h4>
						<p>Conceptualización y modelado en 3D de su pieza soñada.</p>
					</div>
				</div>
			</div>

			<div class="murg-contact-form-card" data-reveal>
				<form class="murg-contact-form"
				      method="post"
				      action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
				      novalidate>
					<?php wp_nonce_field( 'murg_cita', 'murg_nonce' ); ?>
					<input type="hidden" name="action" value="murg_solicitar_cita">

					<div class="murg-contact-field">
						<label for="murg-nombre">Nombre completo</label>
						<input id="murg-nombre" type="text" name="nombre"
						       placeholder="Su nombre" required>
					</div>
					
					<div class="murg-contact-field">
						<label for="murg-correo">Correo electrónico</label>
						<input id="murg-correo" type="email" name="correo"
						       placeholder="correo@ejemplo.com" required>
					</div>
					
					<div class="murg-contact-field">
						<label for="murg-telefono">Teléfono / Celular</label>
						<input id="murg-telefono" type="tel" name="telefono"
						       placeholder="+51 ___ ___ ___">
					</div>
					
					<div class="murg-contact-field">
						<label for="murg-interes">Motivo de interés</label>
						<select id="murg-interes" name="interes">
							<option>Asesoría general</option>
							<option>Anillo de compromiso</option>
							<option>Aros de matrimonio</option>
							<option>Alta joyería</option>
							<option>Diseño a medida</option>
							<option>Restauración</option>
						</select>
					</div>
					
					<div class="murg-contact-field murg-contact-field--textarea">
						<label for="murg-mensaje">Mensaje o consulta</label>
						<textarea id="murg-mensaje" name="mensaje" rows="4"
						          placeholder="Describa brevemente lo que busca..." required></textarea>
					</div>

					<div class="murg-contact-form__actions">
						<button type="submit" class="murg-btn murg-btn--dark">Solicitar Cita</button>
						<?php if ( $wa_general ) : ?>
						<a href="<?php echo esc_url( $wa_general ); ?>"
						   class="murg-btn murg-btn--gold"
						   target="_blank" rel="noopener noreferrer">
							<span class="murg-whatsapp-dot" aria-hidden="true"></span>
							WhatsApp
						</a>
						<?php endif; ?>
					</div>
				</form>
			</div>

		</div>
	</section>

	<!-- ── 3. Boutiques Section (Physical Stores Reference) ── -->
	<section class="murg-contact-stores" aria-label="Nuestras Boutiques">
		<div class="murg-contact-stores__head" data-reveal>
			<p class="murg-eyebrow">Nuestras boutiques</p>
			<h2>Puntos de encuentro</h2>
			<p>Visite nuestros espacios físicos para conocer las colecciones de cerca y recibir asistencia directa de nuestros asesores.</p>
		</div>
		
		<div class="murg-contact-stores__grid">
			<?php foreach ( $stores as $index => $store ) :
				$name    = $store['nombre'] ?? '';
				$address = $store['direccion'] ?? '';
				$phone   = $store['telefono'] ?? '';
				$hours   = $store['horario'] ?? '';
				$wa_url  = $store['whatsapp_url'] ?? '';
				$map_url = $store['maps_url'] ?? '';
				
				// Resolve image URL dynamically
				$img_url = '';
				if ( ! empty( $store['imagen_principal'] ) ) {
					if ( is_array( $store['imagen_principal'] ) && ! empty( $store['imagen_principal']['url'] ) ) {
						$img_url = $store['imagen_principal']['url'];
					} elseif ( is_numeric( $store['imagen_principal'] ) ) {
						$img_url = wp_get_attachment_image_url( $store['imagen_principal'], 'large' );
					} elseif ( is_string( $store['imagen_principal'] ) ) {
						$img_url = $store['imagen_principal'];
					}
				}
				
				// Fallback to CPT field if needed
				if ( ! $img_url && ! empty( $store['id'] ) ) {
					$cpt_img_id = murg_store_field( 'tienda_imagen_principal', $store['id'] );
					if ( $cpt_img_id ) {
						$img_url = wp_get_attachment_image_url( $cpt_img_id, 'large' );
					}
				}
			?>
			<div class="murg-contact-store-card" data-reveal>
				<?php if ( $img_url ) : ?>
				<div class="murg-contact-store-card__img-wrap">
					<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="murg-contact-store-card__img" loading="lazy">
				</div>
				<?php endif; ?>
				
				<div class="murg-contact-store-card__content">
					<div>
						<span class="murg-contact-store-card__num"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<h3><?php echo esc_html( $name ); ?></h3>
						<div class="murg-contact-store-card__details">
							<p class="murg-contact-store-card__address"><strong>Dirección:</strong> <?php echo esc_html( $address ); ?></p>
							<?php if ( $phone ) : ?>
							<p class="murg-contact-store-card__phone"><strong>Teléfono:</strong> <a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
							<?php endif; ?>
							<p class="murg-contact-store-card__hours"><strong>Horario:</strong> <?php echo nl2br( esc_html( $hours ) ); ?></p>
						</div>
					</div>
					
					<div class="murg-contact-store-card__actions">
						<?php if ( $wa_url ) : ?>
						<a href="<?php echo esc_url( $wa_url ); ?>" class="murg-contact-store-card__link" target="_blank" rel="noopener noreferrer">
							WhatsApp
						</a>
						<?php endif; ?>
						<?php if ( $map_url ) : ?>
						<a href="<?php echo esc_url( $map_url ); ?>" class="murg-contact-store-card__link" target="_blank" rel="noopener noreferrer">
							Ver mapa
						</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</section>

</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
