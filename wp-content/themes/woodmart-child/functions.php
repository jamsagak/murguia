<?php
/**
 * Functions — entry point for the woodmart-child theme.
 *
 * The implementation is split across inc/ and inc/acf-fields/ to keep
 * concerns separated. Order matters: helpers and theme setup load first
 * so the rest can rely on them.
 */
defined( 'ABSPATH' ) || exit;

$murg_inc = __DIR__ . '/inc';

/* Helpers and bootstrap (load order matters) */
require_once $murg_inc . '/helpers.php';
require_once $murg_inc . '/helpers-acf.php';
require_once $murg_inc . '/theme-setup.php';
require_once $murg_inc . '/enqueues.php';

/* Domain pieces */
require_once $murg_inc . '/cpts.php';
require_once $murg_inc . '/ensure-defaults.php';
require_once $murg_inc . '/form-handlers.php';
require_once $murg_inc . '/whatsapp.php';
require_once $murg_inc . '/template-overrides.php';
require_once $murg_inc . '/admin-menu.php';

/* ACF field groups — one file per page/section */
$murg_acf_files = [
	'homepage.php',
	'shop.php',
	'product.php',
	'contact.php',
	'per-product.php',
	'altajoyeria.php',
	'anillos-compromiso.php',
	'aros-matrimonio.php',
	'tiendas.php',
	'las-4cs.php',
	'nosotros.php',
];
foreach ( $murg_acf_files as $_file ) {
	require_once $murg_inc . '/acf-fields/' . $_file;
}
unset( $murg_inc, $murg_acf_files, $_file );
