<?php
/**
 * Murguía — My Account Dashboard
 * Override del template de WoodMart.
 *
 * Estructura:
 *   - Sidebar nav (wc_get_account_menu_items, endpoint activo detectado)
 *   - Content area con el endpoint que corresponda
 *
 * Removemos el dashboard basura de WoodMart y WC core para el endpoint
 * "dashboard", y renderizamos nuestro propio saludo minimalista.
 */
defined( 'ABSPATH' ) || exit;

// Evitar que WoodMart inyecte su lista wd-my-account-links con iconos rotos
remove_all_actions( 'woocommerce_account_dashboard' );

/**
 * Determinar endpoint activo (dashboard si no hay ninguno).
 */
function murg_get_current_account_endpoint() {
	global $wp;
	foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
		if ( isset( $wp->query_vars[ $endpoint ] ) ) {
			return $endpoint;
		}
	}
	return 'dashboard';
}
$current_endpoint = murg_get_current_account_endpoint();

/**
 * Dashboard custom minimalista (solo si estamos en el endpoint principal)
 */
function murg_render_dashboard() {
	$user          = wp_get_current_user();
	$first_name    = $user->first_name ?: $user->display_name;
	$account_email = $user->user_email;

	$recent_orders = wc_get_orders( [
		'customer' => get_current_user_id(),
		'limit'    => 3,
		'orderby'  => 'date',
		'order'    => 'DESC',
	] );
	?>
	<div class="murg-dashboard__greeting">
		<p class="murg-dashboard__hello">Hola, <em><?php echo esc_html( $first_name ); ?></em></p>
		<p class="murg-dashboard__email"><?php echo esc_html( $account_email ); ?></p>
	</div>

	<div class="murg-dashboard__blurb">
		<p>Desde aquí puede gestionar sus pedidos, direcciones de envío y los detalles de su cuenta.</p>
	</div>

	<?php if ( ! empty( $recent_orders ) ) : ?>
	<section class="murg-dashboard__section">
		<h2 class="murg-dashboard__section-title">Pedidos recientes</h2>
		<ul class="murg-dashboard__orders">
			<?php foreach ( $recent_orders as $order ) : ?>
			<li>
				<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="murg-dashboard__order">
					<span class="murg-dashboard__order-ref">#<?php echo esc_html( $order->get_order_number() ); ?></span>
					<span class="murg-dashboard__order-date"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></span>
					<span class="murg-dashboard__order-status"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
					<span class="murg-dashboard__order-total"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="murg-dashboard__see-all">Ver todos los pedidos →</a>
	</section>
	<?php endif; ?>
	<?php
}
?>

<div class="murg-dashboard">
	<nav class="murg-dashboard__nav" aria-label="Navegación de cuenta">
		<?php
		foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
			$is_active = ( $endpoint === $current_endpoint );
		?>
		<a class="murg-dashboard__nav-link <?php echo $is_active ? 'is-active' : ''; ?>"
		   href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
		   <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
			<?php echo esc_html( $label ); ?>
		</a>
		<?php endforeach; ?>
	</nav>

	<div class="murg-dashboard__content">
		<div class="woocommerce-notices-wrapper"></div>

		<?php if ( 'dashboard' === $current_endpoint ) : ?>
			<?php murg_render_dashboard(); ?>
		<?php else : ?>
			<?php
			// Para los otros endpoints (orders, downloads, edit-address, etc.)
			// dejamos que WC renderice su contenido normal.
			do_action( 'woocommerce_account_' . $current_endpoint . '_endpoint', get_query_var( $current_endpoint ) );
			?>
		<?php endif; ?>
	</div>
</div>
