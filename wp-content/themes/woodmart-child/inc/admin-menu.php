<?php

/* ------------------------------------------------------------------
   ADMIN MENU — CPTs del plugin woodmart-core bajo xts_dashboard
   ------------------------------------------------------------------ */
add_action( 'admin_menu', 'murguia_reorganize_woodmart_cpts', 999 );

function murguia_reorganize_woodmart_cpts() {
	$cpts = [
		'cms_block'         => 'HTML Blocks',
		'woodmart_sidebar'  => 'Sidebars',
		'woodmart_slide'    => 'Slides',
		'woodmart_layout'   => 'Layouts',
		'portfolio'         => 'Portfolio',
		'wd_floating_block' => 'Floating Blocks',
		'wd_popup'          => 'Popups',
	];

	foreach ( $cpts as $post_type => $label ) {
		$menu_slug = 'edit.php?post_type=' . $post_type;
		remove_menu_page( $menu_slug );
		add_submenu_page( 'xts_dashboard', $label, $label, 'manage_options', $menu_slug );
	}
}
