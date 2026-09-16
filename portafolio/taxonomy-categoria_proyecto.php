<?php
/**
 * Archivo de la taxonomía "categoria_proyecto" (p. ej. /categoria-proyecto/wordpress/).
 *
 * Reutiliza template-parts/listado-proyectos.php, igual que el archivo
 * general de "proyectos" (archive-proyectos.php): la consulta principal ya
 * viene filtrada por WordPress a esta categoría, así que aquí solo cambia
 * el título. El filtro de categorías (dentro de listado-proyectos.php)
 * resalta la categoría actual.
 *
 * @package Portafolio
 */

get_header();

get_template_part(
	'template-parts/listado-proyectos',
	null,
	array( 'titulo' => single_term_title( '', false ) )
);

get_footer();
