<?php
/**
 * Joyería Murguía — Mi Cuenta (WooCommerce)
 *
 * Template que envuelve el contenido de WooCommerce My Account
 * con nuestro nav y footer custom. WordPress usa este archivo
 * automáticamente porque la página tiene slug "mi-cuenta".
 */
defined( 'ABSPATH' ) || exit;

$is_logged = is_user_logged_in();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-account' . ( $is_logged ? ' murg-account--logged' : ' murg-account--guest' ) ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<!-- ============================================================
     PAGE HEADER — solo cuando NO está logueado (Acceso/Registro)
     ============================================================ -->
<?php if ( ! $is_logged ) : ?>
<header class="murg-page-header">
	<div class="murg-eyebrow">Mi Cuenta</div>
	<h1 class="murg-serif murg-page-header__title">
		<?php esc_html_e( 'Acceso', 'woodmart' ); ?>
	</h1>
</header>
<?php endif; ?>

<!-- ============================================================
     WOOCOMMERCE MY ACCOUNT
     ============================================================ -->
<div class="murg-account__content">
	<?php echo do_shortcode( '[woocommerce_my_account]' ); ?>
</div>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
