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

/**
 * Registra el Custom Post Type "proyectos".
 */
function portafolio_registrar_cpt_proyectos() {
	$etiquetas = array(
		'name'                  => __( 'Proyectos', 'portafolio' ),
		'singular_name'         => __( 'Proyecto', 'portafolio' ),
		'menu_name'             => __( 'Proyectos', 'portafolio' ),
		'add_new'               => __( 'Añadir nuevo', 'portafolio' ),
		'add_new_item'          => __( 'Añadir nuevo proyecto', 'portafolio' ),
		'edit_item'             => __( 'Editar proyecto', 'portafolio' ),
		'new_item'              => __( 'Nuevo proyecto', 'portafolio' ),
		'view_item'             => __( 'Ver proyecto', 'portafolio' ),
		'view_items'            => __( 'Ver proyectos', 'portafolio' ),
		'search_items'          => __( 'Buscar proyectos', 'portafolio' ),
		'not_found'             => __( 'No se encontraron proyectos', 'portafolio' ),
		'not_found_in_trash'    => __( 'No hay proyectos en la papelera', 'portafolio' ),
		'all_items'             => __( 'Todos los proyectos', 'portafolio' ),
		'archives'              => __( 'Archivo de proyectos', 'portafolio' ),
		'featured_image'        => __( 'Imagen destacada del proyecto', 'portafolio' ),
		'set_featured_image'    => __( 'Establecer imagen destacada', 'portafolio' ),
		'remove_featured_image' => __( 'Quitar imagen destacada', 'portafolio' ),
		'use_featured_image'    => __( 'Usar como imagen destacada', 'portafolio' ),
	);

	$argumentos = array(
		'labels'       => $etiquetas,
		'public'       => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-portfolio',
		'rewrite'      => array( 'slug' => 'proyectos' ),
		'show_in_rest' => true,
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
	);

	register_post_type( 'proyectos', $argumentos );
}
add_action( 'init', 'portafolio_registrar_cpt_proyectos' );
