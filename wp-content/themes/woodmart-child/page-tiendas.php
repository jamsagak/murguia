<?php
/**
 * Template Name: Tiendas
 * Template Post Type: page
 */
defined( 'ABSPATH' ) || exit;

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

function murg_store_image_from_row( $row ) {
	return ( is_array( $row ) && ! empty( $row['imagen'] ) ) ? $row['imagen'] : null;
}

function murg_store_img_id( $image ) {
	if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
		return (int) $image['ID'];
	}
	return is_numeric( $image ) ? (int) $image : 0;
}

function murg_store_map_src( $iframe ) {
	if ( ! is_string( $iframe ) || '' === trim( $iframe ) ) {
		return '';
	}
	if ( preg_match( '/src=[\"\']([^\"\']+)[\"\']/', $iframe, $matches ) ) {
		return esc_url_raw( html_entity_decode( $matches[1] ) );
	}
	return esc_url_raw( trim( $iframe ) );
}

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
			'id'               => $post->ID,
			'visible'          => 1,
			'nombre'           => murg_store_field( 'tienda_nombre', $post->ID, get_the_title( $post->ID ) ),
			'direccion'        => murg_store_field( 'tienda_direccion', $post->ID ),
			'telefono'         => murg_store_field( 'tienda_telefono', $post->ID ),
			'horario'          => murg_store_field( 'tienda_horario', $post->ID ),
			'imagen_principal' => murg_store_field( 'tienda_imagen_principal', $post->ID, get_post_thumbnail_id( $post->ID ) ),
			'galeria'          => murg_store_field( 'tienda_galeria', $post->ID, [] ),
			'whatsapp_texto'   => murg_store_field( 'tienda_whatsapp_texto', $post->ID ),
			'whatsapp_url'     => murg_store_field( 'tienda_whatsapp_url', $post->ID ),
			'maps_url'         => murg_store_field( 'tienda_maps_url', $post->ID ),
			'mapa_iframe'      => murg_store_field( 'tienda_mapa_iframe', $post->ID ),
			'orden'            => (int) murg_store_field( 'tienda_orden', $post->ID, $post->menu_order ),
		];
	}
	return $rows;
}

$page_id    = get_queried_object_id();
$page_title = murguia_ajuste( 'tt_titulo', get_the_title( $page_id ), 'tiendas' );
$intro_text = murguia_ajuste( 'tt_intro', '', 'tiendas' );
$intro      = $intro_text ? wpautop( $intro_text ) : apply_filters( 'the_content', get_post_field( 'post_content', $page_id ) );
$stores     = murguia_ajuste( 'tt_tiendas', [], 'tiendas' );
$stores     = is_array( $stores ) && $stores ? $stores : murg_store_rows_from_cpt();
usort( $stores, function ( $a, $b ) {
	return (int) ( $a['orden'] ?? 0 ) <=> (int) ( $b['orden'] ?? 0 );
} );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '-', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-home murg-tiendas-page' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-tiendas" id="contenido">
	<section class="murg-tiendas-hero">
		<div class="murg-tiendas-hero__inner" data-reveal>
			<h1><?php echo esc_html( $page_title ); ?></h1>
			<?php if ( trim( wp_strip_all_tags( $intro ) ) ) : ?>
			<div class="murg-tiendas-hero__text"><?php echo wp_kses_post( $intro ); ?></div>
			<?php endif; ?>
		</div>
	</section>

	<section class="murg-tiendas-list" aria-label="<?php echo esc_attr( $page_title ); ?>">
		<?php foreach ( $stores as $index => $store ) :
			if ( isset( $store['visible'] ) && ! $store['visible'] ) continue;
			$store_id      = ! empty( $store['id'] ) ? (int) $store['id'] : ( $index + 1 );
			$name          = $store['nombre'] ?? '';
			$address       = $store['direccion'] ?? '';
			$phone         = $store['telefono'] ?? '';
			$hours         = $store['horario'] ?? '';
			$main_image    = $store['imagen_principal'] ?? [];
			$gallery       = $store['galeria'] ?? [];
			$wa_text       = $store['whatsapp_texto'] ?? '';
			$wa_url        = $store['whatsapp_url'] ?? '';
			$map_url       = $store['maps_url'] ?? '';
			$map_src       = murg_store_map_src( $store['mapa_iframe'] ?? '' );
			$main_id       = murg_store_img_id( $main_image );
			$gallery_items = [];

			if ( ! $main_id ) {
				$main_id = (int) get_post_thumbnail_id( $store_id );
			}

			if ( is_array( $gallery ) ) {
				foreach ( $gallery as $row ) {
					$item = murg_store_image_from_row( $row );
					if ( $item ) {
						$gallery_items[] = $item;
					}
				}
			}
		?>
		<article class="murg-store-card" data-reveal>
			<div class="murg-store-card__media">
				<?php if ( $main_id ) : ?>
					<?php
					echo wp_get_attachment_image( $main_id, 'large', false, [
						'loading' => 0 === $index ? 'eager' : 'lazy',
						'alt'     => get_post_meta( $main_id, '_wp_attachment_image_alt', true ) ?: $name,
					] );
					?>
				<?php else : ?>
				<div class="murg-store-card__placeholder" aria-hidden="true"><span>M</span></div>
				<?php endif; ?>
			</div>

			<div class="murg-store-card__info">
				<p class="murg-ac-eyebrow"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></p>
				<h2><?php echo esc_html( $name ); ?></h2>
				<div class="murg-store-card__facts">
					<?php if ( $address ) : ?><p><span><?php esc_html_e( 'Direccion', 'woodmart-child' ); ?></span><?php echo esc_html( $address ); ?></p><?php endif; ?>
					<?php if ( $phone ) : ?><p><span><?php esc_html_e( 'Telefono', 'woodmart-child' ); ?></span><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p><?php endif; ?>
					<?php if ( $hours ) : ?><p><span><?php esc_html_e( 'Horario', 'woodmart-child' ); ?></span><?php echo nl2br( esc_html( $hours ) ); ?></p><?php endif; ?>
				</div>
				<div class="murg-store-card__actions">
					<?php if ( $gallery_items ) : ?><button class="murg-btn murg-btn--dark" type="button" data-store-modal-open="gallery-<?php echo esc_attr( $store_id ); ?>"><?php esc_html_e( 'Ver galeria', 'woodmart-child' ); ?></button><?php endif; ?>
					<?php if ( $map_src ) : ?><button class="murg-btn murg-btn--dark" type="button" data-store-modal-open="map-<?php echo esc_attr( $store_id ); ?>"><?php esc_html_e( 'Ver mapa', 'woodmart-child' ); ?></button><?php elseif ( $map_url ) : ?><a class="murg-btn murg-btn--dark" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Ver mapa', 'woodmart-child' ); ?></a><?php endif; ?>
					<?php if ( $wa_url && $wa_text ) : ?><a class="murg-ac-link" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $wa_text ); ?></a><?php endif; ?>
				</div>
			</div>
		</article>

		<?php if ( $gallery_items ) : ?>
		<div class="murg-store-modal" id="gallery-<?php echo esc_attr( $store_id ); ?>" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $name ); ?>">
			<div class="murg-store-modal__overlay" data-store-modal-close></div>
			<div class="murg-store-modal__panel murg-store-modal__panel--gallery">
				<button class="murg-store-modal__close" type="button" data-store-modal-close aria-label="<?php esc_attr_e( 'Cerrar', 'woodmart-child' ); ?>">x</button>
				<h2><?php echo esc_html( $name ); ?></h2>
				<div class="murg-store-mosaic">
					<?php foreach ( $gallery_items as $item ) :
						$item_id = murg_store_img_id( $item );
						if ( ! $item_id ) continue;
					?>
					<figure><?php echo wp_get_attachment_image( $item_id, 'large', false, [ 'loading' => 'lazy', 'alt' => get_post_meta( $item_id, '_wp_attachment_image_alt', true ) ?: $name ] ); ?></figure>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( $map_src ) : ?>
		<div class="murg-store-modal" id="map-<?php echo esc_attr( $store_id ); ?>" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $name ); ?>">
			<div class="murg-store-modal__overlay" data-store-modal-close></div>
			<div class="murg-store-modal__panel murg-store-modal__panel--map">
				<button class="murg-store-modal__close" type="button" data-store-modal-close aria-label="<?php esc_attr_e( 'Cerrar', 'woodmart-child' ); ?>">x</button>
				<h2><?php echo esc_html( $name ); ?></h2>
				<iframe src="<?php echo esc_url( $map_src ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
			</div>
		</div>
		<?php endif; ?>
		<?php endforeach; ?>
	</section>
</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
