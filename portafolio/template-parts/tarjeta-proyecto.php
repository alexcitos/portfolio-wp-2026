<?php
/**
 * Tarjeta de proyecto (tarjeta de vidrio): imagen, categorías (en el orden
 * de negocio, ver portafolio_obtener_categorias_proyecto() en
 * functions.php), título y extracto. Reutilizada dentro del Loop en la
 * portada (proyectos destacados) y en template-parts/listado-proyectos.php
 * (archivo de "proyectos" y archivos de categoría).
 *
 * Argumento opcional vía get_template_part( ..., array( 'etiqueta_titulo' => 'h2' ) ):
 * nivel de encabezado del título del proyecto, para no romper la
 * jerarquía de encabezados de cada página (por defecto "h3").
 *
 * @package Portafolio
 */

$portafolio_etiqueta_titulo    = isset( $args['etiqueta_titulo'] ) ? $args['etiqueta_titulo'] : 'h3';
$portafolio_categorias_tarjeta = portafolio_obtener_categorias_proyecto( get_the_ID() );
?>
<li <?php post_class( 'proyecto' ); ?>>
	<article>
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="proyecto-imagen">
				<a href="<?php the_permalink(); ?>">
					<?php
					// portafolio-tarjeta/-2x (ver add_image_size() en functions.php):
					// recorte 16:10, el mismo que fuerza .proyecto-imagen por CSS, en
					// dos anchos para que WordPress arme el srcset con una variante
					// 1x y otra retina/2x. loading="lazy" forzado (en vez de dejarlo
					// en la heurística automática de WordPress, que por defecto no
					// aplica lazy a las primeras imágenes del documento sin mirar si
					// de verdad están a la vista): estas tarjetas nunca son lo
					// primero visible de la página, van después del hero+pilares en
					// el home y del título+filtro en el archivo.
					the_post_thumbnail( 'portafolio-tarjeta', array( 'loading' => 'lazy' ) );
					?>
				</a>
			</figure>
		<?php endif; ?>

		<div class="proyecto-contenido">
			<?php if ( $portafolio_categorias_tarjeta ) : ?>
				<ul class="proyecto-categorias">
					<?php foreach ( $portafolio_categorias_tarjeta as $portafolio_categoria ) : ?>
						<li class="proyecto-categoria">
							<a href="<?php echo esc_url( get_term_link( $portafolio_categoria ) ); ?>">
								<?php echo esc_html( $portafolio_categoria->name ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php
			printf(
				'<%1$s class="proyecto-titulo"><a href="%2$s">%3$s</a></%1$s>',
				tag_escape( $portafolio_etiqueta_titulo ),
				esc_url( get_permalink() ),
				esc_html( get_the_title() )
			);
			?>

			<div class="proyecto-extracto">
				<?php the_excerpt(); ?>
			</div>
		</div>
	</article>
</li>
