<?php
/**
 * Base de SEO técnico del tema, sin ningún plugin: título dinámico, meta
 * descripción, Open Graph/Twitter Card, favicon y datos estructurados
 * (JSON-LD). La URL canónica NO se genera aquí: WordPress ya la emite por
 * defecto en cada página (rel_canonical(), hooked a wp_head desde núcleo),
 * así que añadir una propia solo arriesgaría duplicarla.
 *
 * @package Portafolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Acceso directo no permitido.
}

/**
 * Título "de contenido" de la página actual, sin el sufijo del sitio (lo
 * añade portafolio_titulo_documento_partes() vía el separador de
 * document_title_parts). Se reutiliza tal cual para og:title/twitter:title,
 * que no llevan ese sufijo (ya va aparte en og:site_name).
 *
 * En el home, "nombre" y "tagline" son los mismos campos de ACF que ya usa
 * front-page.php con los mismos valores de reserva — si cambian ahí, deben
 * cambiar también aquí.
 *
 * @return string
 */
function portafolio_titulo_pagina() {
	if ( is_front_page() ) {
		$portada_id = (int) get_option( 'page_on_front' );
		$nombre     = get_field( 'nombre', $portada_id );
		$tagline    = get_field( 'tagline', $portada_id );
		$nombre     = $nombre ? $nombre : 'Nombre Apellido';
		$tagline    = $tagline ? $tagline : 'Desarrollo WordPress a medida, marketing digital e infraestructura IT para negocios que quieren crecer.';

		return $nombre . ' — ' . $tagline;
	}

	if ( is_singular() ) {
		return get_the_title();
	}

	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}

	if ( is_tax() ) {
		return single_term_title( '', false );
	}

	return get_bloginfo( 'name' );
}

/**
 * Título del documento: "Título de la página | Portafolio Alex Rodríguez"
 * en cada página. El "site" se fija siempre al mismo texto (no al de
 * Ajustes → Generales), para que el sufijo no dependa de esa
 * configuración.
 *
 * @param array $partes Partes del título (ver document_title_parts).
 * @return array
 */
function portafolio_titulo_documento_partes( $partes ) {
	$partes['title'] = portafolio_titulo_pagina();
	$partes['site']  = 'Portafolio Alex Rodríguez';

	// La portada es una página estática (no el listado de entradas), así
	// que si WordPress llegara a añadir aquí la tagline de Ajustes →
	// Generales, sería redundante con la tagline de ACF que ya va dentro de
	// $partes['title'] arriba.
	unset( $partes['tagline'] );

	return $partes;
}
add_filter( 'document_title_parts', 'portafolio_titulo_documento_partes' );

/**
 * Separador del título: "|" en vez del "-" por defecto de WordPress.
 *
 * @return string
 */
function portafolio_titulo_documento_separador() {
	return '|';
}
add_filter( 'document_title_separator', 'portafolio_titulo_documento_separador' );

/**
 * Recorta un texto a un límite de caracteres sin cortar una palabra a la
 * mitad, y añade "…" cuando de verdad recorta. Usado por
 * portafolio_meta_descripcion() para respetar el límite de 150-160
 * caracteres recomendado para la meta descripción.
 *
 * @param string $texto  Texto a recortar (se limpia de HTML antes).
 * @param int    $limite Longitud máxima, en caracteres.
 * @return string
 */
function portafolio_recortar_texto( $texto, $limite ) {
	$texto = trim( wp_strip_all_tags( (string) $texto ) );

	if ( '' === $texto || mb_strlen( $texto ) <= $limite ) {
		return $texto;
	}

	$recortado      = mb_substr( $texto, 0, $limite - 1 );
	$ultimo_espacio = mb_strrpos( $recortado, ' ' );

	if ( false !== $ultimo_espacio ) {
		$recortado = mb_substr( $recortado, 0, $ultimo_espacio );
	}

	return rtrim( $recortado, " ,.;:-" ) . '…';
}

/**
 * Meta descripción de la página actual (150-160 caracteres como máximo, ya
 * recortada). Se reutiliza tal cual para og:description/twitter:description
 * y para la "description" del JSON-LD del proyecto (ver
 * portafolio_datos_estructurados_proyecto() más abajo), como única fuente
 * de verdad.
 *
 * @return string
 */
function portafolio_meta_descripcion() {
	if ( is_singular( 'proyectos' ) ) {
		$extracto = get_the_excerpt();

		if ( '' === trim( wp_strip_all_tags( $extracto ) ) ) {
			$extracto = sprintf(
				/* translators: %s: título del proyecto. */
				__( 'Proyecto "%s": desarrollo WordPress, marketing digital e infraestructura IT por Alex Rodríguez.', 'portafolio' ),
				get_the_title()
			);
		}

		return portafolio_recortar_texto( $extracto, 160 );
	}

	if ( is_tax( 'categoria_proyecto' ) ) {
		return portafolio_recortar_texto(
			sprintf(
				/* translators: %s: nombre de la categoría de proyecto. */
				__( 'Proyectos de %s: desarrollo WordPress, marketing digital e infraestructura IT realizados por Alex Rodríguez.', 'portafolio' ),
				single_term_title( '', false )
			),
			160
		);
	}

	if ( is_post_type_archive( 'proyectos' ) ) {
		return portafolio_recortar_texto(
			__( 'Proyectos de desarrollo WordPress, marketing digital e infraestructura IT realizados por Alex Rodríguez: casos reales, con las tecnologías usadas en cada uno.', 'portafolio' ),
			160
		);
	}

	if ( is_front_page() ) {
		$portada_id = (int) get_option( 'page_on_front' );
		$tagline    = get_field( 'tagline', $portada_id );
		$tagline    = $tagline ? $tagline : 'Desarrollo WordPress a medida, marketing digital e infraestructura IT para negocios que quieren crecer.';

		return portafolio_recortar_texto( $tagline, 160 );
	}

	return portafolio_recortar_texto( get_bloginfo( 'description' ), 160 );
}

/**
 * URL de la página actual, para og:url/twitter (canónica "manual" para no
 * depender de wp_get_canonical_url(), que solo cubre lo singular). Cubre
 * los tipos de vista que existen en este tema; cualquier otro caso (p. ej.
 * una búsqueda) cae en el último "return" con la URL tal cual la resolvió
 * WordPress en $wp->request.
 *
 * @return string
 */
function portafolio_url_actual() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}

	if ( is_singular() ) {
		return get_permalink();
	}

	if ( is_post_type_archive() ) {
		return get_post_type_archive_link( get_query_var( 'post_type' ) );
	}

	if ( is_tax() ) {
		return get_term_link( get_queried_object() );
	}

	global $wp;

	return home_url( $wp->request ? $wp->request : '' );
}

/**
 * Imagen para og:image/twitter:image: la imagen destacada real del
 * proyecto (tamaño "large") cuando existe, o la imagen genérica de
 * respaldo del tema (img/og-default.png, 1200×630) en cualquier otro caso.
 *
 * @return array{url: string, ancho: int, alto: int}
 */
function portafolio_imagen_og() {
	if ( is_singular( 'proyectos' ) && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );

		if ( $src ) {
			return array(
				'url'   => $src[0],
				'ancho' => (int) $src[1],
				'alto'  => (int) $src[2],
			);
		}
	}

	return array(
		'url'   => get_template_directory_uri() . '/img/og-default.png',
		'ancho' => 1200,
		'alto'  => 630,
	);
}

/**
 * Imprime la meta descripción y las etiquetas Open Graph/Twitter Card de la
 * página actual.
 */
function portafolio_meta_seo() {
	$descripcion = portafolio_meta_descripcion();
	$titulo      = portafolio_titulo_pagina();
	$imagen      = portafolio_imagen_og();
	$url         = portafolio_url_actual();
	$tipo_og     = is_singular( 'proyectos' ) ? 'article' : 'website';
	?>
	<meta name="description" content="<?php echo esc_attr( $descripcion ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $tipo_og ); ?>">
	<meta property="og:site_name" content="Portafolio Alex Rodríguez">
	<meta property="og:title" content="<?php echo esc_attr( $titulo ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $descripcion ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $imagen['url'] ); ?>">
	<meta property="og:image:width" content="<?php echo esc_attr( $imagen['ancho'] ); ?>">
	<meta property="og:image:height" content="<?php echo esc_attr( $imagen['alto'] ); ?>">
	<meta property="og:locale" content="<?php echo esc_attr( str_replace( '-', '_', get_locale() ) ); ?>">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $titulo ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $descripcion ); ?>">
	<meta name="twitter:image" content="<?php echo esc_url( $imagen['url'] ); ?>">
	<?php
}
add_action( 'wp_head', 'portafolio_meta_seo', 1 );

/**
 * Imprime los <link> del favicon (favicon.ico, PNG en varios tamaños,
 * apple-touch-icon, site.webmanifest) y el theme-color. Los archivos viven
 * en img/ (generados a partir de la paleta del sitio, ver
 * img/generar-favicon.py) y en la raíz del tema (site.webmanifest).
 */
function portafolio_favicon_enlaces() {
	$base = get_template_directory_uri() . '/img/';
	?>
	<link rel="icon" href="<?php echo esc_url( $base . 'favicon.ico' ); ?>" sizes="any">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( $base . 'favicon-16x16.png' ); ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( $base . 'favicon-32x32.png' ); ?>">
	<link rel="icon" type="image/png" sizes="192x192" href="<?php echo esc_url( $base . 'icon-192.png' ); ?>">
	<link rel="apple-touch-icon" href="<?php echo esc_url( $base . 'apple-touch-icon.png' ); ?>">
	<link rel="manifest" href="<?php echo esc_url( get_template_directory_uri() . '/site.webmanifest' ); ?>">
	<meta name="theme-color" content="#030711">
	<?php
}
add_action( 'wp_head', 'portafolio_favicon_enlaces', 2 );

/**
 * JSON-LD de la portada: Person (Alex Rodríguez, con enlaces a LinkedIn y
 * GitHub — los mismos campos de ACF que ya alimentan la barra de redes
 * sociales, ver portafolio_obtener_redes_sociales() en functions.php) +
 * WebSite, combinados en un único @graph.
 */
function portafolio_datos_estructurados_home() {
	if ( ! is_front_page() ) {
		return;
	}

	$portada_id = (int) get_option( 'page_on_front' );
	$nombre     = get_field( 'nombre', $portada_id );
	$nombre     = $nombre ? $nombre : 'Alex Rodríguez';

	$mismo_que = array_values(
		array_filter(
			array(
				get_field( 'red_linkedin', $portada_id ),
				get_field( 'red_github', $portada_id ),
			)
		)
	);

	$persona = array(
		'@type'    => 'Person',
		'@id'      => home_url( '/#persona' ),
		'name'     => $nombre,
		'jobTitle' => 'Desarrollador WordPress, especialista en marketing digital e infraestructura IT',
		'url'      => home_url( '/' ),
	);

	if ( $mismo_que ) {
		$persona['sameAs'] = $mismo_que;
	}

	$sitio = array(
		'@type'      => 'WebSite',
		'@id'        => home_url( '/#sitio' ),
		'name'       => 'Portafolio Alex Rodríguez',
		'url'        => home_url( '/' ),
		'inLanguage' => 'es',
		'publisher'  => array( '@id' => home_url( '/#persona' ) ),
	);

	$datos = array(
		'@context' => 'https://schema.org',
		'@graph'   => array( $persona, $sitio ),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $datos, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'portafolio_datos_estructurados_home' );

/**
 * JSON-LD de cada proyecto: CreativeWork con nombre, descripción (misma que
 * portafolio_meta_descripcion(), como única fuente de verdad), las
 * tecnologías reales del checkbox de ACF como "keywords", el enlace real
 * del proyecto como "url" (repositorio/demo si existe, si no el permalink)
 * y, cuando el proyecto es de cliente y tiene nombre_cliente, una
 * referencia al cliente vía "sourceOrganization" (la propiedad de
 * schema.org para "la organización para la que se hizo el trabajo").
 */
function portafolio_datos_estructurados_proyecto() {
	if ( ! is_singular( 'proyectos' ) ) {
		return;
	}

	$id                 = get_the_ID();
	$claves_tecnologias = (array) get_field( 'tecnologias_usadas', $id );
	$tecnologias_otras  = get_field( 'tecnologias_otras', $id );
	$enlace_real        = get_field( 'enlace_repositorio_demo', $id );
	$contexto           = get_field( 'contexto_proyecto', $id );
	$nombre_cliente     = get_field( 'nombre_cliente', $id );

	$catalogo  = portafolio_catalogo_iconos_tecnologia();
	$keywords  = array();

	foreach ( $claves_tecnologias as $clave ) {
		if ( isset( $catalogo[ $clave ]['nombre'] ) ) {
			$keywords[] = $catalogo[ $clave ]['nombre'];
		}
	}

	if ( $tecnologias_otras ) {
		foreach ( array_map( 'trim', explode( ',', $tecnologias_otras ) ) as $otra ) {
			if ( '' !== $otra ) {
				$keywords[] = $otra;
			}
		}
	}

	$obra = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'CreativeWork',
		'name'             => get_the_title(),
		'description'      => portafolio_meta_descripcion(),
		'url'              => $enlace_real ? $enlace_real : get_permalink(),
		'mainEntityOfPage' => get_permalink(),
		'creator'          => array(
			'@type' => 'Person',
			'name'  => 'Alex Rodríguez',
		),
	);

	if ( $keywords ) {
		$obra['keywords'] = implode( ', ', $keywords );
	}

	if ( has_post_thumbnail() ) {
		$obra['image'] = get_the_post_thumbnail_url( $id, 'large' );
	}

	if ( 'cliente' === $contexto && $nombre_cliente ) {
		$obra['sourceOrganization'] = array(
			'@type' => 'Organization',
			'name'  => $nombre_cliente,
		);
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $obra, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'portafolio_datos_estructurados_proyecto' );
