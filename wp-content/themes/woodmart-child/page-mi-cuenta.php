<?php
/**
 * Joyería Murguía — Mi Cuenta (WooCommerce)
 *
 * Template que envuelve el contenido de WooCommerce My Account
 * con nuestro nav y footer custom. WordPress usa este archivo
 * automáticamente porque la página tiene slug "mi-cuenta".
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-account' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<div class="murg-breadcrumb">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a>
	<span aria-hidden="true">·</span>
	<span>Mi Cuenta</span>
</div>

<!-- ============================================================
     PAGE HEADER
     ============================================================ -->
<header class="murg-page-header">
	<div class="murg-eyebrow">Mi Cuenta</div>
	<h1 class="murg-serif murg-page-header__title">
		<?php
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			printf(
				/* translators: %s = nombre del usuario */
				esc_html__( 'Hola, %s', 'woodmart' ),
				esc_html( $user->display_name )
			);
		} else {
			esc_html_e( 'Acceso', 'woodmart' );
		}
		?>
	</h1>
</header>

<!-- ============================================================
     WOCOMMERCE MY ACCOUNT
     ============================================================ -->
<div class="murg-account__content">
	<?php echo do_shortcode( '[woocommerce_my_account]' ); ?>
</div>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
