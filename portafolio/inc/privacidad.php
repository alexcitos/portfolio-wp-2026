<?php
/**
 * Privacidad de usuarios: evitar que un visitante pueda descubrir el nombre
 * de usuario de WordPress (user_login / user_nicename) por las vías
 * públicas que el núcleo deja abiertas por defecto — API REST, archivos de
 * autor (incluido /?author=N), sitemaps nativos y respuestas oEmbed.
 *
 * @package Portafolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Acceso directo no permitido.
}

/**
 * Quita los endpoints REST de usuarios (/wp/v2/users y
 * /wp/v2/users/<id>) para visitantes sin sesión: el listado público expone
 * el "slug" (nicename) de cada autor. Con sesión iniciada se mantienen,
 * porque el editor de bloques los usa (selector de autor, etc.).
 *
 * @param array $endpoints Rutas REST registradas.
 * @return array
 */
function portafolio_ocultar_endpoints_usuarios( $endpoints ) {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}

	unset( $endpoints['/wp/v2/users'] );
	unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );

	return $endpoints;
}
add_filter( 'rest_endpoints', 'portafolio_ocultar_endpoints_usuarios' );

/**
 * Redirige con 301 a la portada cualquier archivo de autor, incluido
 * /?author=N. Prioridad 1 para adelantarse a redirect_canonical()
 * (prioridad 10), que de lo contrario respondería con una redirección a
 * /author/<nicename>/ y revelaría el nombre de usuario en la cabecera
 * Location. El tema no tiene plantilla de autor, así que no se pierde nada.
 */
function portafolio_redirigir_archivos_autor() {
	if ( is_admin() || ! is_author() ) {
		return;
	}

	wp_safe_redirect( home_url( '/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'portafolio_redirigir_archivos_autor', 1 );

/**
 * Quita el proveedor "users" de los sitemaps nativos de WordPress
 * (/wp-sitemap-users-1.xml lista las URL /author/<nicename>/).
 *
 * @param WP_Sitemaps_Provider|false $proveedor Instancia del proveedor.
 * @param string                     $nombre    Nombre del proveedor.
 * @return WP_Sitemaps_Provider|false
 */
function portafolio_quitar_sitemap_usuarios( $proveedor, $nombre ) {
	if ( 'users' === $nombre ) {
		return false;
	}

	return $proveedor;
}
add_filter( 'wp_sitemaps_add_provider', 'portafolio_quitar_sitemap_usuarios', 10, 2 );

/**
 * Quita author_name y author_url de las respuestas oEmbed
 * (/wp-json/oembed/1.0/embed): el primero es el nombre visible del autor y
 * el segundo enlaza a /author/<nicename>/.
 *
 * @param array $datos Datos de la respuesta oEmbed.
 * @return array
 */
function portafolio_quitar_autor_oembed( $datos ) {
	unset( $datos['author_name'], $datos['author_url'] );

	return $datos;
}
add_filter( 'oembed_response_data', 'portafolio_quitar_autor_oembed' );
