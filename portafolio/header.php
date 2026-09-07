<?php
/**
 * Cabecera del documento.
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

<a class="salto-contenido" href="#contenido"><?php esc_html_e( 'Saltar al contenido', 'portafolio' ); ?></a>

<header class="sitio-cabecera">
	<p class="sitio-titulo">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
	</p>

	<nav class="sitio-navegacion" aria-label="<?php esc_attr_e( 'Menú principal', 'portafolio' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'principal',
				'container'      => false,
			)
		);
		?>
	</nav>
</header>
