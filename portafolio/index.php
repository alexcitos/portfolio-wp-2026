<?php
/**
 * Plantilla principal (fallback de la Template Hierarchy).
 *
 * @package Portafolio
 */

get_header();
?>

<main id="contenido" class="sitio-contenido">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h2><?php the_title(); ?></h2>
				<div class="entrada-contenido">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;

		the_posts_navigation();
	else :
		?>
		<p><?php esc_html_e( 'No hay contenido que mostrar.', 'portafolio' ); ?></p>
		<?php
	endif;
	?>
</main>

<?php
get_footer();
