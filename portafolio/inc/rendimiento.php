<?php
/**
 * Optimizaciones de rendimiento (WPO) que no encajan en ningún otro archivo
 * de inc/: desactivar funcionalidad de núcleo que este tema no usa.
 *
 * @package Portafolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Acceso directo no permitido.
}

/**
 * Desactiva la detección de emojis de WordPress: el script inline que
 * prueba si el navegador soporta los emojis más nuevos (dibujándolos en un
 * <canvas> oculto) y, si no los soporta, carga wp-emoji-release.min.js para
 * sustituirlos por imágenes (Twemoji). Ninguna plantilla del tema usa
 * emojis fuera de texto ya escrito a mano en footer.php (💻🤖☕), que se ven
 * bien con el emoji nativo del sistema operativo sin necesidad de este
 * script — así que toda esta carga es innecesaria en cada carga de página.
 */
function portafolio_desactivar_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	add_filter( 'tiny_mce_plugins', 'portafolio_desactivar_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'portafolio_desactivar_emojis_dns_prefetch', 10, 2 );
}
add_action( 'init', 'portafolio_desactivar_emojis' );

/**
 * Quita el plugin "wpemoji" de la lista de plugins de TinyMCE (editor
 * clásico), para que tampoco intente cargar el detector ahí.
 *
 * @param string[] $plugins Plugins de TinyMCE activos.
 * @return string[]
 */
function portafolio_desactivar_emojis_tinymce( $plugins ) {
	return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
}

/**
 * Quita el dns-prefetch hacia s.w.org que WordPress añade solo para
 * precargar la conexión hacia donde vive wp-emoji-release.min.js: sin el
 * script, ese aviso al navegador ya no tiene ningún propósito.
 *
 * @param string[] $urls          URLs de "resource hints".
 * @param string   $tipo_de_pista Tipo de pista ('dns-prefetch', 'preconnect', etc.).
 * @return string[]
 */
function portafolio_desactivar_emojis_dns_prefetch( $urls, $tipo_de_pista ) {
	if ( 'dns-prefetch' === $tipo_de_pista ) {
		$urls = array_diff( $urls, array( '//s.w.org' ) );
	}

	return $urls;
}
