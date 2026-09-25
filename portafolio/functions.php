<?php
/**
 * Funciones y configuración del tema Portafolio.
 *
 * @package Portafolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Acceso directo no permitido.
}

// Catálogo de iconos de tecnología para las tarjetas de "Pilares" del home
// (ver template-parts/pilar-tecnologias.php). Aparte porque son datos
// (SVG de terceros), no lógica del tema.
require get_template_directory() . '/inc/iconos-tecnologia.php';

// Catálogo de iconos de redes sociales (ver template-parts/redes-sociales.php
// y portafolio_obtener_redes_sociales() más abajo). Misma razón que el
// require anterior: son datos, no lógica del tema.
require get_template_directory() . '/inc/redes-sociales.php';

// Base de SEO técnico (título, meta descripción, Open Graph/Twitter,
// favicon, datos estructurados). Aparte porque es un bloque de
// funcionalidad propio, no configuración general del tema.
require get_template_directory() . '/inc/seo.php';

// Optimizaciones de rendimiento (WPO) que no encajan en otro archivo de
// inc/ (por ahora, desactivar la detección de emojis de núcleo).
require get_template_directory() . '/inc/rendimiento.php';

/**
 * Soporte de características del tema.
 */
function portafolio_setup() {
	// Etiqueta <title> gestionada por WordPress.
	add_theme_support( 'title-tag' );

	// Imágenes destacadas.
	add_theme_support( 'post-thumbnails' );

	// Tamaños de imagen ajustados al tamaño real en pantalla de cada uso
	// (ver style.css para las medidas exactas), en vez de los tamaños
	// genéricos de núcleo (medium/medium_large/large): menos bytes que
	// bajar y sin depender de que el recorte por CSS (object-fit: cover)
	// tenga que recortar de más.
	// - "Sobre mí" (front-page.php): círculo fijo de 250×250 (.sobre-mi-foto).
	add_image_size( 'portafolio-avatar', 250, 250, true );
	// - Tarjeta de proyecto (template-parts/tarjeta-proyecto.php): recorte
	//   16:10 (.proyecto-imagen), en dos anchos para que el srcset cubra
	//   tanto 1x como pantallas retina/2x.
	add_image_size( 'portafolio-tarjeta', 640, 400, true );
	add_image_size( 'portafolio-tarjeta-2x', 1280, 800, true );
	// - Imagen destacada del proyecto individual (single-proyectos.php):
	//   sin recortar (mismo alto/ancho que el original, solo limitado en
	//   ancho), porque .proyecto-single-imagen no fuerza un aspect-ratio
	//   propio — el contenedor mide como mucho 48rem (768px).
	add_image_size( 'portafolio-proyecto', 768, 0, false );

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
 * Fecha de modificación de un archivo del tema, para usar como "?ver=" al
 * encolarlo (mismo criterio que ya usaba style.css, ver más abajo):
 * cambia solo cuando el archivo cambia, así el navegador lo recarga en
 * cada edición sin depender de subir la versión del tema a mano — y, al
 * revés, permite cachear ese archivo por mucho tiempo en el navegador
 * (ver .htaccess) sin miedo a servir una versión vieja, porque cualquier
 * cambio ya viene con una URL distinta.
 *
 * @param string $ruta_relativa Ruta relativa a la carpeta del tema (p. ej. "/js/menu-movil.js").
 * @return int
 */
function portafolio_version_activo( $ruta_relativa ) {
	return filemtime( get_theme_file_path( $ruta_relativa ) );
}

/**
 * Encola estilos y scripts del tema.
 */
function portafolio_assets() {
	// Hoja de estilos principal: se sirve minificada (style.min.css, generado
	// desde style.css por bin/minificar.py — sin build tools, así que hay
	// que volver a correr ese script a mano tras editar style.css). version
	// = fecha de modificación del propio .min.css, no del fuente: así el
	// "?ver=" siempre refleja lo que de verdad se está sirviendo.
	wp_enqueue_style(
		'portafolio-style',
		get_stylesheet_directory_uri() . '/style.min.css',
		array(),
		portafolio_version_activo( '/style.min.css' )
	);

	// Todos los scripts del tema comparten la misma estrategia de carga:
	// defer (además de ir en el footer). "defer" dejar al navegador
	// descargarlos en paralelo mientras sigue parseando el HTML —a
	// diferencia de un <script> normal en el footer, que no empieza a
	// bajar hasta llegar ahí— y ejecutarlos en orden justo después de
	// parsear el DOM, sin bloquear el renderizado. Ninguno toca el DOM
	// antes de que exista, así que defer es seguro en los siete.
	//
	// Igual que style.min.css: cada uno se sirve minificado (js/*.min.js,
	// generado desde el .js fuente por bin/minificar.py) y versionado por
	// la fecha de modificación de ese .min.js, no del fuente.
	$portafolio_estrategia_scripts = array(
		'strategy'  => 'defer',
		'in_footer' => true,
	);

	// Muestra el borde/sombra de la cabecera sticky solo tras hacer scroll.
	wp_enqueue_script(
		'portafolio-cabecera-scroll',
		get_template_directory_uri() . '/js/cabecera-scroll.min.js',
		array(),
		portafolio_version_activo( '/js/cabecera-scroll.min.js' ),
		$portafolio_estrategia_scripts
	);

	// Botón hamburguesa: abre/cierra la navegación principal en mobile.
	wp_enqueue_script(
		'portafolio-menu-movil',
		get_template_directory_uri() . '/js/menu-movil.min.js',
		array(),
		portafolio_version_activo( '/js/menu-movil.min.js' ),
		$portafolio_estrategia_scripts
	);

	// Desplazamiento animado al pulsar enlaces de ancla internos (p. ej. el
	// botón de contacto del hero, que apunta a #contacto).
	wp_enqueue_script(
		'portafolio-scroll-suave',
		get_template_directory_uri() . '/js/scroll-suave.min.js',
		array(),
		portafolio_version_activo( '/js/scroll-suave.min.js' ),
		$portafolio_estrategia_scripts
	);

	// Base del menú de anclas: fija --cabecera-alto (altura real de la
	// cabecera, usada por scroll-margin-top en style.css) y resalta en el
	// menú la sección visible mientras se hace scroll (scrollspy).
	wp_enqueue_script(
		'portafolio-menu-anclas',
		get_template_directory_uri() . '/js/menu-anclas.min.js',
		array(),
		portafolio_version_activo( '/js/menu-anclas.min.js' ),
		$portafolio_estrategia_scripts
	);

	// Conteo ascendente del número de tokens en el pie (ver footer.php):
	// pie de página global, así que se encola en todas las plantillas, no
	// solo en portada.
	wp_enqueue_script(
		'portafolio-contador-tokens',
		get_template_directory_uri() . '/js/contador-tokens.min.js',
		array(),
		portafolio_version_activo( '/js/contador-tokens.min.js' ),
		$portafolio_estrategia_scripts
	);

	// Arranca, pausa y reanuda las animaciones infinitas decorativas (esferas
	// del fondo fijo de template-parts/fondo-esferas.php, capas eco del hero,
	// iconos de pilares y foto de "Sobre mí"): corren solo tras la carga y
	// mientras hay interacción, y se detienen tras unos segundos de
	// inactividad. El fondo se usa en portada, archivo y single de
	// proyectos, así que va global.
	wp_enqueue_script(
		'portafolio-animaciones-ambiente',
		get_template_directory_uri() . '/js/animaciones-ambiente.min.js',
		array(),
		portafolio_version_activo( '/js/animaciones-ambiente.min.js' ),
		$portafolio_estrategia_scripts
	);

	// Envío por AJAX del formulario de contacto (solo existe en portada).
	// Ver portafolio_ajax_enviar_contacto() más abajo.
	if ( is_front_page() ) {
		wp_enqueue_script(
			'portafolio-formulario-contacto',
			get_template_directory_uri() . '/js/formulario-contacto.min.js',
			array(),
			portafolio_version_activo( '/js/formulario-contacto.min.js' ),
			$portafolio_estrategia_scripts
		);

		// La URL de admin-ajax.php se pasa por wp_localize_script() en vez
		// de leerla del propio <form> con formulario.action: el formulario
		// tiene un <input type="hidden" name="action" ...> (obligatorio
		// para que admin-ajax.php enrute a la acción correcta) y, por las
		// "named form controls", un control con name="action" tapa la
		// propiedad nativa HTMLFormElement.action —formulario.action deja
		// de devolver la URL como texto y pasa a devolver ese <input>—. Ver
		// js/formulario-contacto.js.
		wp_localize_script(
			'portafolio-formulario-contacto',
			'portafolioContacto',
			array(
				'urlAjax' => admin_url( 'admin-ajax.php' ),
			)
		);
	}
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
 * Ordena términos de "categoria_proyecto" según la prioridad de negocio
 * (WordPress > Marketing Digital > Infraestructura y Automatización) en
 * vez del orden alfabético que devuelve WordPress por defecto (get_terms()
 * y get_the_terms() ordenan por "name"), que dejaría "Infraestructura..."
 * primero.
 *
 * @param WP_Term[] $terminos Términos a ordenar.
 * @return WP_Term[] Mismos términos, reordenados.
 */
function portafolio_ordenar_categorias_proyecto( array $terminos ) {
	$orden_prioridad = array( 'WordPress', 'Marketing Digital', 'Infraestructura y Automatización' );

	usort(
		$terminos,
		static function ( $a, $b ) use ( $orden_prioridad ) {
			$posicion_a = array_search( $a->name, $orden_prioridad, true );
			$posicion_b = array_search( $b->name, $orden_prioridad, true );

			$posicion_a = false === $posicion_a ? count( $orden_prioridad ) : $posicion_a;
			$posicion_b = false === $posicion_b ? count( $orden_prioridad ) : $posicion_b;

			return $posicion_a <=> $posicion_b;
		}
	);

	return $terminos;
}

/**
 * Términos de "categoria_proyecto" de una entrada del CPT "proyectos", ya
 * ordenados con portafolio_ordenar_categorias_proyecto(). Usado por
 * template-parts/tarjeta-proyecto.php y single-proyectos.php para que las
 * etiquetas de categoría se muestren siempre en el mismo orden.
 *
 * @param int $post_id ID de la entrada.
 * @return WP_Term[] Términos ordenados, o un array vacío si no tiene.
 */
function portafolio_obtener_categorias_proyecto( $post_id ) {
	$terminos = get_the_terms( $post_id, 'categoria_proyecto' );

	if ( ! $terminos || is_wp_error( $terminos ) ) {
		return array();
	}

	return portafolio_ordenar_categorias_proyecto( $terminos );
}

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
					'type'         => 'text',
					'instructions' => __( 'URL completa o ancla interna, p. ej. #contacto.', 'portafolio' ),
				),

				// --- Contacto ----------------------------------------------
				array(
					'key'   => 'field_dds_tab_contacto',
					'label' => __( 'Contacto', 'portafolio' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_dds_email',
					'label'         => __( 'Email', 'portafolio' ),
					'name'          => 'email',
					'type'          => 'email',
					// Provisional hasta que se configure uno real: mismo valor de
					// reserva que usan front-page.php (dato de contacto) y
					// portafolio_obtener_redes_sociales() (icono de email).
					'default_value' => 'alitos.rope@gmail.com',
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

				// --- Redes sociales ------------------------------------------
				// Se muestran en la barra flotante de todo el sitio (ver
				// header.php), a través de template-parts/redes-sociales.php
				// y portafolio_obtener_redes_sociales() más abajo. Una red se
				// deja de mostrar en cuanto su campo queda vacío.
				array(
					'key'   => 'field_dds_tab_redes',
					'label' => __( 'Redes sociales', 'portafolio' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_dds_red_linkedin',
					'label'         => __( 'LinkedIn', 'portafolio' ),
					'name'          => 'red_linkedin',
					'type'          => 'url',
					'default_value' => 'https://www.linkedin.com/in/alexrodriguezp/',
				),
				array(
					'key'           => 'field_dds_red_github',
					'label'         => __( 'GitHub', 'portafolio' ),
					'name'          => 'red_github',
					'type'          => 'url',
					'default_value' => 'https://github.com/alexcitos',
				),
				array(
					'key'           => 'field_dds_red_instagram',
					'label'         => __( 'Instagram', 'portafolio' ),
					'name'          => 'red_instagram',
					'type'          => 'url',
					'default_value' => 'https://www.instagram.com/alex_rope/',
				),
				array(
					'key'           => 'field_dds_red_strava',
					'label'         => __( 'Strava', 'portafolio' ),
					'name'          => 'red_strava',
					'type'          => 'url',
					'default_value' => 'https://www.strava.com/athletes/125816005',
				),
				array(
					'key'           => 'field_dds_red_whatsapp',
					'label'         => __( 'WhatsApp', 'portafolio' ),
					'name'          => 'red_whatsapp',
					'type'          => 'text',
					'default_value' => '+573114411916',
					'instructions'  => __( 'Número con código de país (admite espacios, guiones o paréntesis: se limpia solo). Genera un enlace a wa.me, igual que el WhatsApp de la sección Contacto.', 'portafolio' ),
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
					'key'           => 'field_dds_tokens_mostrar_contador',
					'label'         => __( 'Mostrar contador de tokens', 'portafolio' ),
					'name'          => 'tokens_mostrar_contador',
					'type'          => 'true_false',
					'default_value' => 1,
					'ui'            => 1,
					'instructions'  => __( 'Activado: el pie muestra el número de "Tokens de IA usados". Desactivado: muestra el "Texto alternativo" en su lugar.', 'portafolio' ),
				),
				array(
					// Tipo "text" (no "number"): un <input type="number"> nativo
					// no admite el punto como separador de miles al escribir
					// (ni "1.234.567" ni pegarlo), así que aquí se acepta el
					// número con o sin separadores y se limpia al guardar (ver
					// portafolio_sanear_tokens_usados() más abajo); en el pie
					// siempre se formatea con number_format_i18n().
					'key'               => 'field_dds_tokens_usados',
					'label'             => __( 'Tokens de IA usados', 'portafolio' ),
					'name'              => 'tokens_usados',
					'type'              => 'text',
					'instructions'      => __( 'Solo números; puedes escribirlo con puntos de miles (1.234.567) o sin ellos, se guarda y se muestra igual.', 'portafolio' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_dds_tokens_mostrar_contador',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
				),
				array(
					'key'               => 'field_dds_tokens_texto_alternativo',
					'label'             => __( 'Texto alternativo', 'portafolio' ),
					'name'              => 'tokens_texto_alternativo',
					'type'              => 'text',
					'default_value'     => __( 'muchísimos', 'portafolio' ),
					'instructions'      => __( 'Sustituye al número cuando el contador está desactivado, en el mismo lugar de la frase: "... junto con Claude Code, [este texto] tokens de IA...".', 'portafolio' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_dds_tokens_mostrar_contador',
								'operator' => '==',
								'value'    => '0',
							),
						),
					),
				),
				array(
					'key'               => 'field_dds_tokens_actualizado',
					'label'             => __( 'Tokens actualizado el', 'portafolio' ),
					'name'              => 'tokens_actualizado',
					'type'              => 'date_picker',
					'display_format'    => 'j/n/Y',
					'return_format'     => 'Ymd',
					'instructions'      => __( 'Fecha del último conteo. Si se deja vacía, la línea "actualizado…" no se muestra.', 'portafolio' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_dds_tokens_mostrar_contador',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'portafolio_registrar_campos_datos_del_sitio' );

/**
 * Redes sociales + email configurados en "Datos del sitio" (pestañas
 * "Redes sociales" y "Contacto" → campo Email), listos para pintar:
 * combina portafolio_catalogo_redes_sociales() (icono, en
 * inc/redes-sociales.php) con la URL guardada en cada campo. Usada por
 * template-parts/redes-sociales.php en la barra flotante de todo el sitio
 * (header.php); la sección Contacto del home (front-page.php) no repite
 * esta fila de iconos porque la barra flotante ya cubre esa función.
 *
 * Orden de aparición: WhatsApp, Email, LinkedIn, GitHub, Instagram, Strava.
 *
 * Una red solo aparece en la lista si su campo tiene un valor; así, dejar
 * un campo vacío la quita de la barra (el email usa además el mismo valor
 * de reserva provisional que front-page.php mientras no se configure uno
 * real).
 *
 * @return array<int, array{clave: string, nombre: string, url: string, viewbox: string, path: string}>
 */
function portafolio_obtener_redes_sociales() {
	$portada_id = (int) get_option( 'page_on_front' );
	$catalogo   = portafolio_catalogo_redes_sociales();

	// Mismo orden en el que deben aparecer los botones.
	$campos = array(
		'whatsapp'  => get_field( 'red_whatsapp', $portada_id ),
		'email'     => get_field( 'email', $portada_id ),
		'linkedin'  => get_field( 'red_linkedin', $portada_id ),
		'github'    => get_field( 'red_github', $portada_id ),
		'instagram' => get_field( 'red_instagram', $portada_id ),
		'strava'    => get_field( 'red_strava', $portada_id ),
	);

	// Igual que $portafolio_email en front-page.php: provisional hasta que
	// se configure un email real en "Datos del sitio" → "Contacto".
	if ( '' === trim( (string) $campos['email'] ) ) {
		$campos['email'] = 'alitos.rope@gmail.com';
	}

	$redes = array();

	foreach ( $campos as $clave => $valor ) {
		$valor = trim( (string) $valor );

		if ( '' === $valor || ! isset( $catalogo[ $clave ] ) ) {
			continue;
		}

		if ( 'whatsapp' === $clave ) {
			// Se guarda como número de teléfono (con o sin espacios, guiones
			// o paréntesis), no como URL: aquí se convierte en enlace a
			// wa.me, igual que el WhatsApp de la sección Contacto (ver
			// front-page.php).
			$url = 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $valor );
		} elseif ( 'email' === $clave ) {
			// antispambot(): misma ofuscación que ya usa el dato de contacto
			// "Email" en front-page.php, para no exponer la dirección en
			// texto plano a los rastreadores de spam.
			$url = 'mailto:' . antispambot( $valor );
		} else {
			$url = $valor;
		}

		$redes[] = array_merge( $catalogo[ $clave ], array( 'url' => $url ) );
	}

	return $redes;
}

/**
 * Valida "Enlace del botón" (boton_contacto_enlace).
 *
 * El campo es de tipo Texto (no URL) precisamente para admitir anclas
 * internas como "#contacto", que la validación nativa de un campo URL de
 * ACF rechaza por no ser una URL completa. Aquí se reimplementa a mano lo
 * que ese tipo de campo perdía: solo se acepta una ancla interna (empieza
 * por "#") o una URL completa (empieza por "http://" o "https://").
 *
 * @param bool|string $valid Resultado de validación hasta ahora.
 * @param mixed        $value Valor enviado para el campo.
 * @return bool|string
 */
function portafolio_validar_boton_contacto_enlace( $valid, $value ) {
	if ( true !== $valid ) {
		return $valid;
	}

	$value = trim( (string) $value );

	if ( '' === $value || str_starts_with( $value, '#' ) ) {
		return $valid;
	}

	if ( str_starts_with( $value, 'http://' ) || str_starts_with( $value, 'https://' ) ) {
		return $valid;
	}

	return __( 'Introduce una URL completa (empezando por http:// o https://) o una ancla interna (empezando por #).', 'portafolio' );
}
add_filter( 'acf/validate_value/name=boton_contacto_enlace', 'portafolio_validar_boton_contacto_enlace', 10, 2 );

/**
 * Valida "Tokens de IA usados" (tokens_usados).
 *
 * El campo es de tipo Texto (no Número): un <input type="number"> nativo
 * no deja escribir ni pegar el punto de los miles ("1.234.567"), que es
 * justo el formato en el que se piensa este dato. Aquí se acepta el número
 * con o sin separadores de miles (punto, coma o espacio) y solo se
 * rechaza si, quitándolos, queda algo que no son dígitos.
 *
 * @param bool|string $valid Resultado de validación hasta ahora.
 * @param mixed        $value Valor enviado para el campo.
 * @return bool|string
 */
function portafolio_validar_tokens_usados( $valid, $value ) {
	if ( true !== $valid ) {
		return $valid;
	}

	$value = trim( (string) $value );

	if ( '' === $value ) {
		return $valid;
	}

	$solo_digitos = str_replace( array( '.', ',', ' ' ), '', $value );

	if ( ! ctype_digit( $solo_digitos ) ) {
		return __( 'Introduce solo números, con o sin puntos de miles (p. ej. 1.234.567).', 'portafolio' );
	}

	return $valid;
}
add_filter( 'acf/validate_value/name=tokens_usados', 'portafolio_validar_tokens_usados', 10, 2 );

/**
 * Guarda "Tokens de IA usados" (tokens_usados) sin los separadores de
 * miles con los que se haya escrito, para que el valor almacenado sea
 * siempre un número limpio y portafolio_pie_tokens_usados_texto()
 * (footer.php) lo formatee con number_format_i18n() sin sorpresas.
 *
 * @param mixed $value Valor a guardar.
 * @return string
 */
function portafolio_sanear_tokens_usados( $value ) {
	return str_replace( array( '.', ',', ' ' ), '', trim( (string) $value ) );
}
add_filter( 'acf/update_value/name=tokens_usados', 'portafolio_sanear_tokens_usados' );

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
					// 'choices' generado desde el mismo catálogo de
					// template-parts/pilar-tecnologias.php (ver
					// inc/iconos-tecnologia.php): así una tecnología nueva
					// solo se da de alta una vez, con icono incluido. "DNS"
					// se deja fuera a propósito —es un concepto genérico sin
					// logo de marca— y se recoge aparte en
					// tecnologias_otras, como etiqueta de texto simple.
					'key'          => 'field_dp_tecnologias_usadas',
					'label'        => __( 'Tecnologías usadas', 'portafolio' ),
					'name'         => 'tecnologias_usadas',
					'type'         => 'checkbox',
					'choices'      => wp_list_pluck( portafolio_catalogo_iconos_tecnologia(), 'nombre' ),
					'layout'       => 'horizontal',
					'instructions' => __( 'Se muestran como insignias con icono en la ficha del proyecto. Para tecnologías sin logo de marca (p. ej. "DNS"), usa el campo de texto de abajo.', 'portafolio' ),
				),
				array(
					'key'          => 'field_dp_tecnologias_otras',
					'label'        => __( 'Otras tecnologías (sin icono)', 'portafolio' ),
					'name'         => 'tecnologias_otras',
					'type'         => 'text',
					'instructions' => __( 'Conceptos genéricos sin logo de marca propio, separados por comas (p. ej. "DNS"). Se muestran como etiqueta de texto simple, sin icono.', 'portafolio' ),
				),
				array(
					'key'          => 'field_dp_enlace_repositorio_demo',
					'label'        => __( 'Enlace a repositorio o demo', 'portafolio' ),
					'name'         => 'enlace_repositorio_demo',
					'type'         => 'url',
					'instructions' => __( 'Para proyectos sin sitio público (p. ej. automatizaciones de n8n), puede apuntar a un repositorio de GitHub con el JSON exportado del workflow.', 'portafolio' ),
				),
				array(
					'key'           => 'field_dp_contexto_proyecto',
					'label'         => __( 'Contexto del proyecto', 'portafolio' ),
					'name'          => 'contexto_proyecto',
					'type'          => 'select',
					'choices'       => array(
						'personal' => __( 'Proyecto personal', 'portafolio' ),
						'cliente'  => __( 'Cliente/Empresa', 'portafolio' ),
					),
					'default_value' => 'personal',
					'allow_null'    => 0,
					'ui'            => 1,
				),
				array(
					'key'               => 'field_dp_nombre_cliente',
					'label'             => __( 'Nombre del cliente', 'portafolio' ),
					'name'              => 'nombre_cliente',
					'type'              => 'text',
					'instructions'      => __( 'Se muestra como "Cliente: [nombre]" debajo del título del proyecto.', 'portafolio' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_dp_contexto_proyecto',
								'operator' => '==',
								'value'    => 'cliente',
							),
						),
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'portafolio_registrar_campos_detalles_proyecto' );

/**
 * Envío SMTP del formulario de contacto.
 *
 * En local: Mailpit (activado explícitamente con PORTAFOLIO_USE_MAILPIT,
 * no con WP_DEBUG — WP_DEBUG también está activo en producción para logs
 * de errores, así que no sirve como señal de entorno).
 * En producción: relay real por Gmail SMTP usando una App Password,
 * inyectada por variables de entorno del contenedor (GMAIL_USER /
 * GMAIL_APP_PASSWORD) — nunca hardcodeada, este repo es público.
 *
 * @param PHPMailer $phpmailer Instancia de PHPMailer, por referencia.
 */
function portafolio_smtp_mailpit( $phpmailer ) {
	if ( defined( 'PORTAFOLIO_USE_MAILPIT' ) && PORTAFOLIO_USE_MAILPIT ) {
		$phpmailer->isSMTP();
		$phpmailer->Host        = 'mailpit';
		$phpmailer->Port        = 1025;
		$phpmailer->SMTPAuth    = false;
		$phpmailer->SMTPSecure  = '';
		$phpmailer->SMTPAutoTLS = false;
		return;
	}

	$gmail_user     = getenv( 'GMAIL_USER' );
	$gmail_password = getenv( 'GMAIL_APP_PASSWORD' );

	if ( ! $gmail_user || ! $gmail_password ) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host        = 'smtp.gmail.com';
	$phpmailer->Port        = 587;
	$phpmailer->SMTPAuth    = true;
	$phpmailer->SMTPSecure  = 'tls';
	$phpmailer->Username    = $gmail_user;
	$phpmailer->Password    = $gmail_password;
}
add_action( 'phpmailer_init', 'portafolio_smtp_mailpit' );

/**
 * Acompaña a portafolio_smtp_mailpit(): remitente por defecto según entorno.
 * En local, home_url() es "localhost" (sin punto), que PHPMailer rechaza
 * como dirección inválida — se usa un dominio falso con punto para pasar
 * esa validación. En producción, se usa la cuenta real de Gmail configurada.
 *
 * @param string $correo_remitente Remitente por defecto de wp_mail().
 * @return string
 */
function portafolio_smtp_mailpit_from( $correo_remitente ) {
	if ( defined( 'PORTAFOLIO_USE_MAILPIT' ) && PORTAFOLIO_USE_MAILPIT ) {
		return 'wordpress@portafolio.test';
	}

	$gmail_user = getenv( 'GMAIL_USER' );

	return $gmail_user ? $gmail_user : $correo_remitente;
}
add_filter( 'wp_mail_from', 'portafolio_smtp_mailpit_from' );

/**
 * Procesa el envío del formulario de contacto de la portada vía AJAX.
 *
 * Endpoint elegido: admin-ajax.php con las acciones wp_ajax_nopriv_* /
 * wp_ajax_* (en vez de una ruta REST personalizada), porque no hace falta
 * autenticación ni un formato de recurso propio: es un único endpoint de
 * "acción" que valida, sanitiza y envía correo, justo el caso de uso para
 * el que admin-ajax.php existe. No depende de ningún plugin de
 * formularios: el tema entero está construido sin dependencias más allá de
 * ACF (ver CLAUDE.md).
 *
 * El JavaScript (js/formulario-contacto.js) intercepta el submit del
 * formulario y hace fetch() contra esta acción; la respuesta siempre es
 * JSON vía wp_send_json_success()/wp_send_json_error(), con:
 * - éxito: { mensaje: string }
 * - error de validación: { errores: { <name-del-campo>: string } }
 * - error genérico (nonce caducado, fallo de wp_mail): { mensaje: string }
 */
function portafolio_ajax_enviar_contacto() {
	$nonce_valido = isset( $_POST['portafolio_contacto_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['portafolio_contacto_nonce'] ) ), 'portafolio_contacto' );

	// Honeypot: campo oculto con CSS (ver .formulario-honeypot en
	// style.css, clip en vez de display:none) que un humano nunca rellena.
	// Si viene relleno, respondemos como si hubiera ido bien —sin enviar
	// el correo— para no delatarle el filtro al bot.
	$es_spam = ! empty( $_POST['portafolio_web'] );

	if ( $es_spam ) {
		wp_send_json_success(
			array( 'mensaje' => __( 'Gracias, tu mensaje se envió correctamente. Te responderé pronto.', 'portafolio' ) )
		);
	}

	if ( ! $nonce_valido ) {
		wp_send_json_error(
			array( 'mensaje' => __( 'Tu sesión caducó. Recarga la página e inténtalo de nuevo.', 'portafolio' ) ),
			403
		);
	}

	$nombre  = isset( $_POST['contacto_nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['contacto_nombre'] ) ) : '';
	$email   = isset( $_POST['contacto_email'] ) ? sanitize_email( wp_unslash( $_POST['contacto_email'] ) ) : '';
	$mensaje = isset( $_POST['contacto_mensaje'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contacto_mensaje'] ) ) : '';

	// Errores por campo (no un mensaje genérico): la clave es el "name" del
	// input, para que el JS los asocie al campo correspondiente. Mismo
	// texto que la validación en vivo de js/formulario-contacto.js, para
	// que el mensaje no cambie según si lo atrapó el cliente o el
	// servidor (defensa en profundidad: el JS ya filtra estos casos antes
	// de enviar, esto cubre a quien tenga JavaScript desactivado o
	// manipule la petición).
	$errores = array();

	if ( '' === $nombre ) {
		$errores['contacto_nombre'] = __( 'Este campo es obligatorio.', 'portafolio' );
	}

	if ( '' === $email ) {
		$errores['contacto_email'] = __( 'Este campo es obligatorio.', 'portafolio' );
	} elseif ( ! is_email( $email ) ) {
		$errores['contacto_email'] = __( 'Ingresa un email válido.', 'portafolio' );
	}

	if ( '' === $mensaje ) {
		$errores['contacto_mensaje'] = __( 'Este campo es obligatorio.', 'portafolio' );
	}

	if ( ! empty( $errores ) ) {
		wp_send_json_error( array( 'errores' => $errores ), 422 );
	}

	$portada_id = (int) get_option( 'page_on_front' );
	$destino    = get_field( 'email', $portada_id );
	$destino    = $destino ? $destino : get_option( 'admin_email' );

	$asunto = __( 'Solicitud del portafolio web', 'portafolio' );

	$cuerpo  = '<p><strong>' . esc_html__( 'Nombre:', 'portafolio' ) . '</strong> ' . esc_html( $nombre ) . '</p>';
	$cuerpo .= '<p><strong>' . esc_html__( 'Correo:', 'portafolio' ) . '</strong> ' . esc_html( $email ) . '</p>';
	$cuerpo .= '<p><strong>' . esc_html__( 'Mensaje:', 'portafolio' ) . '</strong><br>' . nl2br( esc_html( $mensaje ) ) . '</p>';

	// Reply-To (no From): que el remitente real sea del propio dominio
	// evita que servidores de correo rechacen o marquen como spam un From
	// con un dominio ajeno. Content-Type en HTML: necesario para que las
	// etiquetas <strong> se vean en negrita en vez de como texto literal.
	$cabeceras = array(
		'Content-Type: text/html; charset=UTF-8',
		'Reply-To: ' . $nombre . ' <' . $email . '>',
	);

	$enviado = wp_mail( $destino, $asunto, $cuerpo, $cabeceras );

	if ( ! $enviado ) {
		wp_send_json_error(
			array( 'mensaje' => __( 'No se pudo enviar el mensaje. Inténtalo de nuevo en unos minutos.', 'portafolio' ) ),
			500
		);
	}

	wp_send_json_success(
		array( 'mensaje' => __( 'Gracias, tu mensaje se envió correctamente. Te responderé pronto.', 'portafolio' ) )
	);
}
add_action( 'wp_ajax_portafolio_enviar_contacto', 'portafolio_ajax_enviar_contacto' );
add_action( 'wp_ajax_nopriv_portafolio_enviar_contacto', 'portafolio_ajax_enviar_contacto' );

/**
 * Inserta el snippet de Google Analytics (GA4) en el <head>.
 * Solo en producción: PORTAFOLIO_ENTORNO_LOCAL se define como true
 * únicamente en el docker-compose.yml local, nunca en el de producción,
 * así que por defecto (sin la constante definida) el snippet SÍ carga.
 */
function portafolio_google_analytics() {
	if ( defined( 'PORTAFOLIO_ENTORNO_LOCAL' ) && PORTAFOLIO_ENTORNO_LOCAL ) {
		return;
	}
	?>
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-TCBT35N7BE"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'G-TCBT35N7BE');
	</script>
	<?php
}
add_action( 'wp_head', 'portafolio_google_analytics', 1 );
