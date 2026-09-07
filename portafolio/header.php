<?php
/**
 * Cabecera del documento: apertura de <html>, <head> y cabecera del sitio.
 *
 * @package Portafolio
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="salto-contenido screen-reader-text" href="#contenido">
	<?php esc_html_e( 'Saltar al contenido', 'portafolio' ); ?>
</a>

<header class="sitio-cabecera">
	<p class="sitio-titulo">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
	</p>

	<nav class="sitio-navegacion" aria-label="<?php esc_attr_e( 'Menú principal', 'portafolio' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'principal',
				'menu_id'        => 'menu-principal',
				'menu_class'     => 'menu',
				'container'      => false,
				'fallback_cb'    => false,
			)
		);
		?>
	</nav>
</header>
