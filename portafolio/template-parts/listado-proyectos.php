<?php
/**
 * Listado de proyectos con el mismo lenguaje visual del home (zona oscura
 * con esferas + tarjetas de vidrio): cabecera, filtro de categorías y la
 * rejilla de proyectos paginada. Comparten esta plantilla el archivo
 * general de "proyectos" (archive-proyectos.php) y los archivos de
 * categoría (taxonomy-categoria_proyecto.php); ambos ya dejan la consulta
 * principal filtrada por WordPress, así que aquí solo cambia el título.
 *
 * Argumento vía get_template_part( ..., array( 'titulo' => ... ) ).
 *
 * @package Portafolio
 */

$portafolio_titulo_listado = isset( $args['titulo'] ) ? $args['titulo'] : post_type_archive_title( '', false );
?>
<main id="contenido" class="portada-oscura">

	<?php get_template_part( 'template-parts/fondo-esferas' ); ?>

	<section class="portada-proyectos" aria-labelledby="listado-proyectos-titulo">
		<div class="proyectos-contenedor">
			<header class="proyectos-cabecera">
				<p class="proyectos-eyebrow">// proyectos</p>
				<h1 id="listado-proyectos-titulo" class="proyectos-titulo"><?php echo esc_html( $portafolio_titulo_listado ); ?></h1>
			</header>

			<?php get_template_part( 'template-parts/filtro-categorias-proyecto' ); ?>

			<?php if ( have_posts() ) : ?>
				<ul class="proyectos-lista">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/tarjeta-proyecto', null, array( 'etiqueta_titulo' => 'h2' ) );
					endwhile;
					?>
				</ul>

				<?php
				the_posts_pagination(
					array(
						'prev_text' => __( 'Proyectos anteriores', 'portafolio' ),
						'next_text' => __( 'Proyectos siguientes', 'portafolio' ),
						'class'     => 'proyectos-paginacion',
					)
				);
			else :
				?>
				<p class="proyectos-vacio">
					<?php esc_html_e( 'Todavía no hay proyectos que mostrar.', 'portafolio' ); ?>
				</p>
				<?php
			endif;
			?>
		</div>
	</section>
</main>
