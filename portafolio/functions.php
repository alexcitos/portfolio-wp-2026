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

	// Hoja de estilos principal: version = fecha de modificación del archivo,
	// para que el navegador la recargue en cada cambio durante el desarrollo
	// sin depender de subir la versión del tema a mano.
	wp_enqueue_style( 'portafolio-style', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );

	// Muestra el borde/sombra de la cabecera sticky solo tras hacer scroll.
	wp_enqueue_script(
		'portafolio-cabecera-scroll',
		get_template_directory_uri() . '/js/cabecera-scroll.js',
		array(),
		$version,
		true
	);

	// Botón hamburguesa: abre/cierra la navegación principal en mobile.
	wp_enqueue_script(
		'portafolio-menu-movil',
		get_template_directory_uri() . '/js/menu-movil.js',
		array(),
		$version,
		true
	);
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
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
	);

	register_post_type( 'proyectos', $argumentos );
}
add_action( 'init', 'portafolio_registrar_cpt_proyectos' );

/**
 * Registra la taxonomía "categoria_proyecto" para el CPT "proyectos".
 */
function portafolio_registrar_taxonomia_categoria_proyecto() {
	$etiquetas = array(
		'name'              => __( 'Categorías de proyecto', 'portafolio' ),
		'singular_name'     => __( 'Categoría de proyecto', 'portafolio' ),
		'menu_name'         => __( 'Categorías', 'portafolio' ),
		'all_items'         => __( 'Todas las categorías', 'portafolio' ),
		'edit_item'         => __( 'Editar categoría', 'portafolio' ),
		'view_item'         => __( 'Ver categoría', 'portafolio' ),
		'update_item'       => __( 'Actualizar categoría', 'portafolio' ),
		'add_new_item'      => __( 'Añadir nueva categoría', 'portafolio' ),
		'new_item_name'     => __( 'Nombre de la nueva categoría', 'portafolio' ),
		'search_items'      => __( 'Buscar categorías', 'portafolio' ),
		'not_found'         => __( 'No se encontraron categorías', 'portafolio' ),
	);

	register_taxonomy(
		'categoria_proyecto',
		'proyectos',
		array(
			'labels'            => $etiquetas,
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'categoria-proyecto' ),
		)
	);
}
add_action( 'init', 'portafolio_registrar_taxonomia_categoria_proyecto' );

/**
 * Crea los términos iniciales de "categoria_proyecto" si aún no existen.
 *
 * Se comprueba con term_exists() para no duplicar términos en cada carga.
 */
function portafolio_crear_terminos_categoria_proyecto() {
	$terminos_iniciales = array( 'WordPress', 'Marketing Digital', 'Infraestructura y Automatización' );

	foreach ( $terminos_iniciales as $termino ) {
		if ( ! term_exists( $termino, 'categoria_proyecto' ) ) {
			wp_insert_term( $termino, 'categoria_proyecto' );
		}
	}
}
add_action( 'init', 'portafolio_crear_terminos_categoria_proyecto', 11 );

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
				array(
					'key'           => 'field_dds_sobre_mi_foto',
					'label'         => __( 'Foto', 'portafolio' ),
					'name'          => 'sobre_mi_foto',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'instructions'  => __( 'Foto redonda junto a la biografía. Si se deja vacía, la sección se muestra sin foto.', 'portafolio' ),
				),

				// --- Pie de página -------------------------------------------
				array(
					'key'   => 'field_dds_tab_pie',
					'label' => __( 'Pie de página', 'portafolio' ),
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_dds_tokens_usados',
					'label'        => __( 'Tokens de IA usados', 'portafolio' ),
					'name'         => 'tokens_usados',
					'type'         => 'number',
					'min'          => 0,
					'step'         => 1,
					'instructions' => __( 'Se muestra en el pie con separador de miles, p. ej. 1.234.567.', 'portafolio' ),
				),
				array(
					'key'             => 'field_dds_tokens_actualizado',
					'label'           => __( 'Tokens actualizado el', 'portafolio' ),
					'name'            => 'tokens_actualizado',
					'type'            => 'date_picker',
					'display_format'  => 'j/n/Y',
					'return_format'   => 'Ymd',
					'instructions'    => __( 'Fecha del último conteo. Si se deja vacía, la línea "actualizado…" no se muestra.', 'portafolio' ),
				),
			),
		)
	);
}
add_action( 'acf/init', 'portafolio_registrar_campos_datos_del_sitio' );

/**
 * Registra por código el grupo de campos "Detalles del proyecto".
 *
 * Asociado al CPT "proyectos" (location: Post Type == proyectos).
 */
function portafolio_registrar_campos_detalles_proyecto() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'         => 'group_detalles_proyecto',
			'title'       => __( 'Detalles del proyecto', 'portafolio' ),
			'location'    => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'proyectos',
					),
				),
			),
			'menu_order'  => 0,
			'active'      => true,
			'description' => __( 'Información adicional del proyecto.', 'portafolio' ),
			'fields'      => array(
				array(
					'key'   => 'field_dp_tecnologias_usadas',
					'label' => __( 'Tecnologías usadas', 'portafolio' ),
					'name'  => 'tecnologias_usadas',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_dp_enlace_repositorio_demo',
					'label'        => __( 'Enlace a repositorio o demo', 'portafolio' ),
					'name'         => 'enlace_repositorio_demo',
					'type'         => 'url',
					'instructions' => __( 'Para proyectos sin sitio público (p. ej. automatizaciones de n8n), puede apuntar a un repositorio de GitHub con el JSON exportado del workflow.', 'portafolio' ),
				),
			),
		)
	);
}
add_action( 'acf/init', 'portafolio_registrar_campos_detalles_proyecto' );

/**
 * Procesa el envío del formulario de contacto de la portada.
 *
 * Patrón Post-Redirect-Get: valida nonce y honeypot, envía el correo con
 * wp_mail() y redirige de vuelta a #contacto con un parámetro de estado en
 * la URL — así, recargar la página tras enviar no vuelve a reenviar el
 * formulario. No depende de ningún plugin de formularios: el tema entero
 * está construido sin dependencias más allá de ACF (ver CLAUDE.md).
 */
function portafolio_procesar_formulario_contacto() {
	if ( ! is_front_page() || ! isset( $_POST['portafolio_contacto_enviado'] ) ) {
		return;
	}

	$nonce_valido = isset( $_POST['portafolio_contacto_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['portafolio_contacto_nonce'] ) ), 'portafolio_contacto' );

	// Honeypot: campo oculto (ver formulario-honeypot en style.css) que un
	// humano nunca rellena. Si viene relleno, respondemos como si hubiera
	// ido bien para no delatarle el filtro al bot.
	$es_spam = ! empty( $_POST['portafolio_web'] );

	if ( $es_spam ) {
		wp_safe_redirect( home_url( '/?contacto=enviado#contacto' ) );
		exit;
	}

	if ( ! $nonce_valido ) {
		wp_safe_redirect( home_url( '/?contacto=error#contacto' ) );
		exit;
	}

	$nombre  = isset( $_POST['contacto_nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['contacto_nombre'] ) ) : '';
	$email   = isset( $_POST['contacto_email'] ) ? sanitize_email( wp_unslash( $_POST['contacto_email'] ) ) : '';
	$mensaje = isset( $_POST['contacto_mensaje'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contacto_mensaje'] ) ) : '';

	if ( '' === $nombre || ! is_email( $email ) || '' === $mensaje ) {
		wp_safe_redirect( home_url( '/?contacto=error#contacto' ) );
		exit;
	}

	$portada_id = (int) get_option( 'page_on_front' );
	$destino    = get_field( 'email', $portada_id );
	$destino    = $destino ? $destino : get_option( 'admin_email' );

	$asunto = sprintf(
		/* translators: %s: nombre de quien escribe. */
		__( 'Nuevo mensaje de contacto de %s', 'portafolio' ),
		$nombre
	);
	$cuerpo = sprintf(
		"%1\$s\n\n— %2\$s <%3\$s>",
		$mensaje,
		$nombre,
		$email
	);
	// Reply-To (no From): que el remitente real sea del propio dominio
	// evita que servidores de correo rechacen o marquen como spam un From
	// con un dominio ajeno.
	$cabeceras = array( 'Reply-To: ' . $nombre . ' <' . $email . '>' );

	$enviado = wp_mail( $destino, $asunto, $cuerpo, $cabeceras );

	wp_safe_redirect( home_url( '/?contacto=' . ( $enviado ? 'enviado' : 'error' ) . '#contacto' ) );
	exit;
}
add_action( 'template_redirect', 'portafolio_procesar_formulario_contacto' );
