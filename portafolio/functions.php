<?php
/**
 * Funciones y configuración del tema Portafolio.
 *
 * @package Portafolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Acceso directo no permitido.
}

/**
 * Soporte de características del tema.
 */
function portafolio_setup() {
	// Etiqueta <title> gestionada por WordPress.
	add_theme_support( 'title-tag' );

	// Imágenes destacadas.
	add_theme_support( 'post-thumbnails' );

	// Marcado HTML5 en los elementos del núcleo.
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// Menús de navegación.
	register_nav_menus(
		array(
			'principal' => __( 'Menú principal', 'portafolio' ),
		)
	);
}
add_action( 'after_setup_theme', 'portafolio_setup' );

/**
 * Encola estilos y scripts del tema.
 */
function portafolio_assets() {
	$version = wp_get_theme()->get( 'Version' );

	// Hoja de estilos principal.
	wp_enqueue_style( 'portafolio-style', get_stylesheet_uri(), array(), $version );
}
add_action( 'wp_enqueue_scripts', 'portafolio_assets' );
