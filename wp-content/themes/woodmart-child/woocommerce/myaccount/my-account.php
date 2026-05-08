<?php
/**
 * Murguía — My Account Dashboard
 * Override de WoodMart: markup limpio, sin iconos, sin redundancia
 */
defined( 'ABSPATH' ) || exit;

$user = wp_get_current_user();
$display_name = $user->display_name;
?>

<div class="murg-dashboard">
	<nav class="murg-dashboard__nav" aria-label="Navegación de cuenta">
		<?php
		$menu_items = wc_get_account_menu_items();
		$current    = ( isset( $_GET['page'] ) ) ? 'page=' . sanitize_key( $_GET['page'] ) : '';
		?>
		<?php foreach ( $menu_items as $endpoint => $label ) :
			$url       = wc_get_account_endpoint_url( $endpoint );
			$is_active = wc_get_endpoint_url( $endpoint, '', wc_get_page_permalink( 'myaccount' ) ) === $url;
			// Dashboard (endpoint vacío) se marca activo solo si no hay ningún endpoint en la URL
			if ( 'dashboard' === $endpoint && ! is_wc_endpoint_url() ) {
				$is_active = true;
			}
			if ( is_wc_endpoint_url( $endpoint ) ) {
				$is_active = true;
			}
		?>
		<a class="murg-dashboard__nav-link <?php echo $is_active ? 'is-active' : ''; ?>"
		   href="<?php echo esc_url( $url ); ?>"
		   <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
			<?php echo esc_html( $label ); ?>
		</a>
		<?php endforeach; ?>
		<a class="murg-dashboard__nav-link murg-dashboard__nav-link--logout"
		   href="<?php echo esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) ); ?>">
			<?php esc_html_e( 'Cerrar sesión', 'woodmart' ); ?>
		</a>
	</nav>

	<div class="murg-dashboard__content">
		<?php
		/**
		 * My Account content.
		 * WooCommerce hooks into this to render: dashboard, orders, downloads, addresses, etc.
		 */
		do_action( 'woocommerce_account_content' );
		?>
	</div>
</div>
<?php
// Prevent WoodMart from rendering its own dashboard after ours
remove_action( 'woocommerce_account_content', 'woodmart_my_account_content', 10 );
