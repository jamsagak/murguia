<?php
/**
 * Murguia — Login / Register form
 * Override de WoodMart: markup limpio, sin clases wd-*
 */
defined( 'ABSPATH' ) || exit;

$show_register = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
?>

<div class="woocommerce-notices-wrapper"></div>

<div class="murg-auth-grid" id="customer_login">

	<div class="murg-auth-col">

		<h2 class="murg-auth-title"><?php esc_html_e( 'Acceder', 'woodmart' ); ?></h2>

		<form method="post" class="woocommerce-form woocommerce-form-login login">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="form-row">
				<label for="username"><?php esc_html_e( 'Usuario o correo electrónico', 'woodmart' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
			</p>

			<p class="form-row">
				<label for="password"><?php esc_html_e( 'Contraseña', 'woodmart' ); ?>&nbsp;<span class="required">*</span></label>
				<span class="password-input">
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
					<button type="button" class="show-password-input" aria-label="Mostrar contraseña" aria-describedby="password"></button>
				</span>
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="murg-auth-actions">
				<button type="submit" class="woocommerce-Button button" name="login" value="<?php esc_attr_e( 'Acceder', 'woodmart' ); ?>"><?php esc_html_e( 'Acceder', 'woodmart' ); ?></button>
				<label class="murg-auth-remember">
					<input name="rememberme" type="checkbox" id="rememberme" value="forever" />
					<span><?php esc_html_e( 'Recordarme', 'woodmart' ); ?></span>
				</label>
			</p>

			<p class="murg-auth-lost-password">
				<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php esc_html_e( '¿Olvidó su contraseña?', 'woodmart' ); ?></a>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

	</div>

	<?php if ( $show_register ) : ?>
	<div class="murg-auth-col">

		<h2 class="murg-auth-title"><?php esc_html_e( 'Registrarse', 'woodmart' ); ?></h2>

		<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<p class="form-row">
				<label for="reg_email"><?php esc_html_e( 'Correo electrónico', 'woodmart' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" />
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
			<p class="form-row">
				<label for="reg_password"><?php esc_html_e( 'Contraseña', 'woodmart' ); ?>&nbsp;<span class="required">*</span></label>
				<span class="password-input">
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
					<button type="button" class="show-password-input" aria-label="Mostrar contraseña" aria-describedby="reg_password"></button>
				</span>
			</p>
			<?php else : ?>
			<p class="murg-auth-info"><?php esc_html_e( 'Se enviará un enlace para establecer una nueva contraseña.', 'woodmart' ); ?></p>
			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="murg-auth-actions">
				<button type="submit" class="woocommerce-Button button" name="register" value="<?php esc_attr_e( 'Registrarse', 'woodmart' ); ?>"><?php esc_html_e( 'Registrarse', 'woodmart' ); ?></button>
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>
	<?php endif; ?>

</div>
