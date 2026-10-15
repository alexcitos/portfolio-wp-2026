<?php
/**
 * Plantilla de páginas sueltas (p. ej. Política de tratamiento de datos y
 * Política de cookies).
 *
 * Sin esta plantilla, las páginas caían en index.php, que muestra un
 * listado de "Entradas" con extractos. Reutiliza a propósito el mismo
 * lenguaje visual de la ficha de proyecto (single-proyectos.php): zona
 * oscura con esferas, título centrado y el contenido completo dentro de
 * una tarjeta de vidrio, con las mismas clases .proyecto-single-* para no
 * duplicar estilos. La portada no pasa por aquí: front-page.php tiene
 * prioridad en la Template Hierarchy.
 *
 * @package Portafolio
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<main id="contenido" class="portada-oscura">

		<?php get_template_part( 'template-parts/fondo-esferas' ); ?>

		<article <?php post_class( 'proyecto-single pagina-legal' ); ?>>
			<div class="proyecto-single-contenedor">

				<header class="proyecto-single-cabecera">
					<h1 class="proyecto-single-titulo"><?php the_title(); ?></h1>
				</header>

				<div class="proyecto-single-panel">
					<div class="proyecto-single-contenido">
						<?php the_content(); ?>
					</div>
				</div>

				<p class="proyecto-single-volver">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						&larr; <?php esc_html_e( 'Volver al inicio', 'portafolio' ); ?>
					</a>
				</p>

			</div>
		</article>

	</main>

	<?php
endwhile;

get_footer();
