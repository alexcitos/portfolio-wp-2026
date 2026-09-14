<?php
/**
 * Archivo del CPT "proyectos".
 *
 * Listado de todos los proyectos con imagen, título y extracto, igual que
 * la sección de destacados del home pero mostrando el listado completo
 * paginado en lugar de solo 4.
 *
 * @package Portafolio
 */

get_header();
?>

<main id="contenido" class="sitio-contenido">
	<header class="archivo-cabecera">
		<h1 class="archivo-titulo"><?php post_type_archive_title(); ?></h1>
	</header>

	<?php if ( have_posts() ) : ?>

		<ul class="proyectos-lista">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<li <?php post_class( 'proyecto' ); ?>>
					<article>
						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="proyecto-imagen">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium_large' ); ?>
								</a>
							</figure>
						<?php endif; ?>

						<h2 class="proyecto-titulo">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<div class="proyecto-extracto">
							<?php the_excerpt(); ?>
						</div>
					</article>
				</li>
				<?php
			endwhile;
			?>
		</ul>

		<?php
		the_posts_pagination(
			array(
				'prev_text' => __( 'Proyectos anteriores', 'portafolio' ),
				'next_text' => __( 'Proyectos siguientes', 'portafolio' ),
			)
		);

	else :
		?>
		<p class="proyectos-vacio">
			<?php esc_html_e( 'Todavía no hay proyectos publicados.', 'portafolio' ); ?>
		</p>
		<?php
	endif;
	?>
</main>

<?php
get_footer();
