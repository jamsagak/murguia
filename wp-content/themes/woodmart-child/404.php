<?php
/**
 * 404 — Página no encontrada.
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Página no encontrada · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-404-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-404">
	<div class="murg-404__container">
		<span class="murg-404__code">404</span>
		<h1 class="murg-404__title">Página no encontrada</h1>
		<p class="murg-404__text">Lo sentimos, la página que buscas no existe o fue movida.</p>
		<div class="murg-404__actions">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="murg-404__btn">Volver al inicio</a>
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="murg-404__btn murg-404__btn--outline">Ver tienda</a>
		</div>
	</div>
</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
