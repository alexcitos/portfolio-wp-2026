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

/**
 * Registra por código el grupo de campos "Datos del sitio".
 *
 * Contenido único y global del sitio (identidad, contacto, biografía). No
 * es un CPT porque no es un listado de entradas, sino un único conjunto de
 * campos.
 *
 * Las Options Pages de ACF son exclusivas de ACF PRO, así que en la versión
 * gratuita el grupo se ancla a la portada (page_type == front_page): se
 * edita desde el editor de la página de Inicio. Se define en PHP —no en la
 * interfaz de ACF— para que el esquema viva en el repositorio.
 *
 * Lectura en plantillas: get_field( 'nombre', get_option( 'page_on_front' ) ),
 * que apunta siempre a la página marcada como portada en Ajustes → Lectura,
 * sin depender de un ID fijo.
 */
function portafolio_registrar_campos_datos_del_sitio() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_datos_del_sitio',
			'title'    => __( 'Datos del sitio', 'portafolio' ),
			'location' => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'menu_order'      => 0,
			'active'          => true,
			'description'     => __( 'Contenido único y global del sitio.', 'portafolio' ),
			'fields'          => array(

				// --- Identidad -------------------------------------------------
				array(
					'key'   => 'field_dds_tab_identidad',
					'label' => __( 'Identidad', 'portafolio' ),
					'type'  => 'tab',
				),
				array(
					'key'      => 'field_dds_nombre',
					'label'    => __( 'Nombre', 'portafolio' ),
					'name'     => 'nombre',
					'type'     => 'text',
					'required' => 1,
				),
				array(
					'key'          => 'field_dds_tagline',
					'label'        => __( 'Tagline', 'portafolio' ),
					'name'         => 'tagline',
					'type'         => 'textarea',
					'rows'         => 2,
					'new_lines'    => '', // Sin envoltura: la plantilla decide el marcado.
					'instructions' => __( 'Frase breve bajo el nombre en el hero.', 'portafolio' ),
				),

				// --- Botón de contacto --------------------------------------
				array(
					'key'   => 'field_dds_tab_boton',
					'label' => __( 'Botón de contacto', 'portafolio' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_dds_boton_texto',
					'label'         => __( 'Texto del botón', 'portafolio' ),
					'name'          => 'boton_contacto_texto',
					'type'          => 'text',
					'default_value' => __( 'Hablemos de tu proyecto', 'portafolio' ),
				),
				array(
					'key'          => 'field_dds_boton_enlace',
					'label'        => __( 'Enlace del botón', 'portafolio' ),
					'name'         => 'boton_contacto_enlace',
					'type'         => 'url',
					'instructions' => __( 'URL completa o ancla interna, p. ej. #contacto.', 'portafolio' ),
				),

				// --- Contacto ----------------------------------------------
				array(
					'key'   => 'field_dds_tab_contacto',
					'label' => __( 'Contacto', 'portafolio' ),
					'type'  => 'tab',
				),
				array(
					'key'   => 'field_dds_email',
					'label' => __( 'Email', 'portafolio' ),
					'name'  => 'email',
					'type'  => 'email',
				),
				array(
					'key'   => 'field_dds_telefono',
					'label' => __( 'Teléfono', 'portafolio' ),
					'name'  => 'telefono',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_dds_ubicacion',
					'label' => __( 'Ubicación', 'portafolio' ),
					'name'  => 'ubicacion',
					'type'  => 'text',
				),

				// --- Sobre mí --------------------------------------------------
				array(
					'key'   => 'field_dds_tab_sobre_mi',
					'label' => __( 'Sobre mí', 'portafolio' ),
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_dds_sobre_mi_bio',
					'label'        => __( 'Biografía', 'portafolio' ),
					'name'         => 'sobre_mi_bio',
					'type'         => 'wysiwyg',
					'tabs'         => 'visual',
					'toolbar'      => 'basic',
					'media_upload' => 0,
					'instructions' => __( 'Texto de la sección "Sobre mí".', 'portafolio' ),
				),
			),
		)
	);
}
add_action( 'acf/init', 'portafolio_registrar_campos_datos_del_sitio' );
