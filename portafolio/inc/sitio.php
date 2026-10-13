<?php
/**
 * Ajustes generales del sitio que no son SEO ni privacidad de usuarios:
 *
 * - El portafolio no usa el blog nativo de WordPress (entradas, categorías,
 *   etiquetas, archivos por fecha). Se redirigen esos archivos a la portada
 *   y se quitan de los sitemaps, para que Google no rastree páginas vacías
 *   como /category/sin-categoria/. Si algún día se agrega un blog, basta con
 *   quitar el require de este archivo en functions.php (o estas funciones).
 * - Se desactivan las contraseñas de aplicación: el sitio no las usa y son
 *   una vía de acceso por API que conviene cerrar si no hace falta.
 *
 * @package Portafolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Acceso directo no permitido.
}

/**
 * Redirige con 301 a la portada los archivos del blog nativo: categorías,
 * etiquetas y archivos por fecha. Prioridad 1, igual que la redirección de
 * archivos de autor en inc/privacidad.php. No afecta a la taxonomía propia
 * "categoria_proyecto" (is_category() solo es verdadero para "category").
 */
function portafolio_redirigir_archivos_blog() {
	if ( is_admin() ) {
		return;
	}

	if ( is_category() || is_tag() || is_date() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'portafolio_redirigir_archivos_blog', 1 );

/**
 * Quita las entradas del blog ("post") de los sitemaps nativos.
 *
 * @param array $tipos Tipos de contenido incluidos en el sitemap.
 * @return array
 */
function portafolio_sitemap_sin_entradas( $tipos ) {
	unset( $tipos['post'] );

	return $tipos;
}
add_filter( 'wp_sitemaps_post_types', 'portafolio_sitemap_sin_entradas' );

/**
 * Quita las categorías y etiquetas del blog de los sitemaps nativos. La
 * taxonomía propia "categoria_proyecto" se mantiene.
 *
 * @param array $taxonomias Taxonomías incluidas en el sitemap.
 * @return array
 */
function portafolio_sitemap_sin_taxonomias_blog( $taxonomias ) {
	unset( $taxonomias['category'], $taxonomias['post_tag'] );

	return $taxonomias;
}
add_filter( 'wp_sitemaps_taxonomies', 'portafolio_sitemap_sin_taxonomias_blog' );

/**
 * Desactiva por completo las contraseñas de aplicación (Usuarios → Perfil →
 * Contraseñas de aplicación y la autenticación por API que habilitan).
 */
add_filter( 'wp_is_application_passwords_available', '__return_false' );
