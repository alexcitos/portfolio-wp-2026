<?php
/**
 * Plantilla de entrada individual del CPT "proyectos".
 *
 * Muestra título, imagen destacada, contenido completo, la categoría de
 * proyecto asignada, las tecnologías usadas y el enlace a repositorio/demo
 * cuando existe.
 *
 * @package Portafolio
 */

get_header();

while ( have_posts() ) :
	the_post();

	$portafolio_categorias         = get_the_terms( get_the_ID(), 'categoria_proyecto' );
	$portafolio_tecnologias        = get_field( 'tecnologias_usadas' );
	$portafolio_enlace_repo_demo   = get_field( 'enlace_repositorio_demo' );
	?>

	<main id="contenido" class="sitio-contenido">
		<article <?php post_class( 'proyecto-single' ); ?>>

			<header class="proyecto-single-cabecera">
				<h1 class="proyecto-single-titulo"><?php the_title(); ?></h1>

				<?php if ( $portafolio_categorias && ! is_wp_error( $portafolio_categorias ) ) : ?>
					<ul class="proyecto-single-categorias">
						<?php foreach ( $portafolio_categorias as $portafolio_categoria ) : ?>
							<li class="proyecto-categoria">
								<a href="<?php echo esc_url( get_term_link( $portafolio_categoria ) ); ?>">
									<?php echo esc_html( $portafolio_categoria->name ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="proyecto-single-imagen">
					<?php the_post_thumbnail( 'large' ); ?>
				</figure>
			<?php endif; ?>

			<div class="proyecto-single-contenido">
				<?php the_content(); ?>
			</div>

			<?php if ( $portafolio_tecnologias || $portafolio_enlace_repo_demo ) : ?>
				<aside class="proyecto-single-detalles">
					<?php if ( $portafolio_tecnologias ) : ?>
						<p class="proyecto-single-tecnologias">
							<span class="proyecto-detalle-etiqueta"><?php esc_html_e( 'Tecnologías usadas:', 'portafolio' ); ?></span>
							<?php echo esc_html( $portafolio_tecnologias ); ?>
						</p>
					<?php endif; ?>

					<?php if ( $portafolio_enlace_repo_demo ) : ?>
						<p class="proyecto-single-enlace">
							<a class="boton boton-primario" href="<?php echo esc_url( $portafolio_enlace_repo_demo ); ?>" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( 'Ver repositorio / demo', 'portafolio' ); ?>
							</a>
						</p>
					<?php endif; ?>
				</aside>
			<?php endif; ?>

		</article>
	</main>

	<?php
endwhile;

get_footer();
