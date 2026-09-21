<?php
/**
 * Plantilla principal (fallback de la Template Hierarchy).
 *
 * Implementa el Loop estándar de WordPress mostrando el título y el
 * extracto de cada entrada.
 *
 * @package Portafolio
 */

get_header();
?>

<main id="contenido" class="sitio-contenido">
	<?php // h1 único de la plantilla (búsqueda, 404 o cualquier otro caso sin
		// plantilla propia): el título de cada entrada del Loop, más abajo,
		// va en h2 —subordinado a este— para no repetir h1 en cada entrada. ?>
	<h1 class="sitio-contenido-titulo">
		<?php
		if ( is_search() ) {
			printf(
				/* translators: %s: término buscado. */
				esc_html__( 'Resultados de búsqueda: %s', 'portafolio' ),
				'&#8220;' . get_search_query() . '&#8221;'
			);
		} elseif ( is_404() ) {
			esc_html_e( 'Página no encontrada', 'portafolio' );
		} else {
			esc_html_e( 'Entradas', 'portafolio' );
		}
		?>
	</h1>
	<?php
	if ( have_posts() ) :

		// El Loop: recorre cada entrada de la consulta principal.
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'entrada' ); ?>>
				<header class="entrada-cabecera">
					<h2 class="entrada-titulo">
						<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
					</h2>
				</header>

				<div class="entrada-extracto">
					<?php the_excerpt(); ?>
				</div>

				<a class="entrada-enlace" href="<?php the_permalink(); ?>">
					<?php esc_html_e( 'Leer más', 'portafolio' ); ?>
				</a>
			</article>
			<?php
		endwhile;

		// Paginación entre listados de entradas.
		the_posts_navigation(
			array(
				'prev_text' => __( 'Entradas anteriores', 'portafolio' ),
				'next_text' => __( 'Entradas siguientes', 'portafolio' ),
			)
		);

	else :
		?>
		<p class="sin-resultados"><?php esc_html_e( 'No hay contenido que mostrar.', 'portafolio' ); ?></p>
		<?php
	endif;
	?>
</main>

<?php
get_footer();
