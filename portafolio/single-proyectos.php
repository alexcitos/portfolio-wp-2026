<?php
/**
 * Plantilla de entrada individual del CPT "proyectos".
 *
 * Mismo lenguaje visual del home (zona oscura con esferas + tarjetas de
 * vidrio): cabecera con categorías (en el orden de negocio, ver
 * portafolio_obtener_categorias_proyecto() en functions.php) y contexto
 * del proyecto (personal o cliente/empresa), imagen destacada, contenido
 * completo dentro de una tarjeta de vidrio, y las tecnologías usadas y el
 * enlace a repositorio/demo cuando existen.
 *
 * @package Portafolio
 */

get_header();

while ( have_posts() ) :
	the_post();

	$portafolio_categorias        = portafolio_obtener_categorias_proyecto( get_the_ID() );
	$portafolio_tecnologias       = get_field( 'tecnologias_usadas' );
	$portafolio_tecnologias_otras = get_field( 'tecnologias_otras' );
	$portafolio_enlace_repo_demo  = get_field( 'enlace_repositorio_demo' );
	$portafolio_contexto_proyecto = get_field( 'contexto_proyecto' );
	$portafolio_nombre_cliente    = get_field( 'nombre_cliente' );
	?>

	<main id="contenido" class="portada-oscura">

		<?php get_template_part( 'template-parts/fondo-esferas' ); ?>

		<article <?php post_class( 'proyecto-single' ); ?>>
			<div class="proyecto-single-contenedor">

				<header class="proyecto-single-cabecera">
					<?php if ( $portafolio_categorias ) : ?>
						<ul class="proyecto-single-categorias">
							<?php foreach ( $portafolio_categorias as $portafolio_categoria ) : ?>
								<li class="proyecto-single-categoria">
									<a href="<?php echo esc_url( get_term_link( $portafolio_categoria ) ); ?>">
										<?php echo esc_html( $portafolio_categoria->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<h1 class="proyecto-single-titulo"><?php the_title(); ?></h1>

					<?php if ( $portafolio_contexto_proyecto ) : ?>
						<p class="proyecto-single-contexto">
							<?php if ( 'cliente' === $portafolio_contexto_proyecto && $portafolio_nombre_cliente ) : ?>
								<?php
								printf(
									/* translators: %s: nombre del cliente o empresa. */
									esc_html__( 'Cliente: %s', 'portafolio' ),
									esc_html( $portafolio_nombre_cliente )
								);
								?>
							<?php elseif ( 'cliente' === $portafolio_contexto_proyecto ) : ?>
								<?php esc_html_e( 'Cliente/Empresa', 'portafolio' ); ?>
							<?php else : ?>
								<?php esc_html_e( 'Proyecto personal', 'portafolio' ); ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="proyecto-single-imagen">
						<?php the_post_thumbnail( 'large' ); ?>
					</figure>
				<?php endif; ?>

				<div class="proyecto-single-panel">
					<div class="proyecto-single-contenido">
						<?php the_content(); ?>
					</div>

					<?php if ( $portafolio_tecnologias || $portafolio_tecnologias_otras || $portafolio_enlace_repo_demo ) : ?>
						<aside class="proyecto-single-detalles">
							<?php if ( $portafolio_tecnologias || $portafolio_tecnologias_otras ) : ?>
								<div class="proyecto-single-tecnologias">
									<span class="proyecto-detalle-etiqueta"><?php esc_html_e( 'Tecnologías usadas:', 'portafolio' ); ?></span>

									<?php // Mismas insignias (icono + nombre) que las tarjetas de
										// "Pilares" del home: mismo template-part, mismas clases
										// .pilar-tecnologias/.pilar-tecnologia (ver style.css). ?>
									<?php
									get_template_part(
										'template-parts/pilar-tecnologias',
										null,
										array( 'tecnologias' => (array) $portafolio_tecnologias )
									);
									?>

									<?php if ( $portafolio_tecnologias_otras ) : ?>
										<?php // Conceptos genéricos sin logo de marca (p. ej. "DNS"): etiqueta
											// de texto simple, sin icono, separada de las insignias de
											// arriba. ?>
										<ul class="proyecto-tecnologias-simples">
											<?php foreach ( array_filter( array_map( 'trim', explode( ',', $portafolio_tecnologias_otras ) ) ) as $portafolio_tecnologia_simple ) : ?>
												<li class="proyecto-tecnologia-simple"><?php echo esc_html( $portafolio_tecnologia_simple ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
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
				</div>

				<p class="proyecto-single-volver">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'proyectos' ) ); ?>">
						&larr; <?php esc_html_e( 'Volver a todos los proyectos', 'portafolio' ); ?>
					</a>
				</p>

			</div>
		</article>

	</main>

	<?php
endwhile;

get_footer();
