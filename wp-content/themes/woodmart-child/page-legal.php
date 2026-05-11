<?php
/**
 * Template Name: Legal
 * Template Post Type: page
 *
 * Template genérico para páginas de contenido legal/institucional.
 * Usar para: Política de Privacidad, Política de Cookies,
 * Términos y Condiciones, Recojos y Envíos.
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php the_title(); ?> · <?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'murg-legal-page' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/murg-nav' ); ?>

<main class="murg-legal">
	<div class="murg-legal__container">
		<header class="murg-legal__header">
			<div class="murg-eyebrow"><?php bloginfo( 'name' ); ?></div>
			<h1 class="murg-legal__title"><?php the_title(); ?></h1>
		</header>
		<div class="murg-legal__content">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
</main>

<?php get_template_part( 'template-parts/murg-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
