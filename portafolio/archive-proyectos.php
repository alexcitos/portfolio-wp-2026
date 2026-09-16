<?php
/**
 * Archivo del CPT "proyectos" (/proyectos/).
 *
 * Reutiliza template-parts/listado-proyectos.php: mismo lenguaje visual
 * del home (zona oscura con esferas + tarjetas de vidrio), con el filtro
 * de categorías y la rejilla completa de proyectos, paginada.
 *
 * @package Portafolio
 */

get_header();

get_template_part(
	'template-parts/listado-proyectos',
	null,
	array( 'titulo' => post_type_archive_title( '', false ) )
);

get_footer();
