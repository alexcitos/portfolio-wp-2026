<?php
/**
 * Portada del sitio (front-page.php).
 *
 * Estructura de secciones: hero, pilares/servicios, proyectos destacados,
 * sobre mí y contacto. El texto es de ejemplo; el contenido real se
 * ajustará más adelante.
 *
 * @package Portafolio
 */

get_header();
?>

<main id="contenido" class="portada">

	<?php // ---------------------------------------------------------------
		// Hero: sección a sangre completa con un panel de vidrio sobre
		// formas difuminadas que flotan de fondo. Las formas y las capas
		// "eco" del panel son puramente decorativas (aria-hidden).
		// --------------------------------------------------------------- ?>
	<section class="portada-hero" aria-labelledby="hero-titulo">

		<div class="hero-fondo" aria-hidden="true">
			<span class="hero-forma hero-forma--1"></span>
			<span class="hero-forma hero-forma--2"></span>
			<span class="hero-forma hero-forma--3"></span>
			<span class="hero-forma hero-forma--4"></span>
		</div>

		<div class="hero-panel">
			<span class="hero-panel-eco hero-panel-eco--1" aria-hidden="true"></span>
			<span class="hero-panel-eco hero-panel-eco--2" aria-hidden="true"></span>

			<div class="hero-panel-marco">
				<div class="hero-panel-vidrio">
					<p class="hero-eyebrow">// portafolio</p>
					<h1 id="hero-titulo" class="hero-nombre">Nombre Apellido</h1>
					<p class="hero-tagline">
						Desarrollo WordPress a medida, marketing digital e
						infraestructura IT para negocios que quieren crecer.
					</p>
					<p class="hero-accion">
						<a class="boton boton-primario" href="#contacto">Hablemos de tu proyecto</a>
					</p>
				</div>
			</div>
		</div>

	</section>

	<div class="portada-cuerpo">

		<?php // -----------------------------------------------------------
			// Pilares / servicios
			// ----------------------------------------------------------- ?>
		<section class="portada-pilares" aria-labelledby="pilares-titulo">
			<h2 id="pilares-titulo" class="seccion-titulo">Lo que hago</h2>

			<ul class="pilares-lista">
				<li class="pilar pilar-wordpress">
					<h3 class="pilar-titulo">WordPress</h3>
					<p class="pilar-descripcion">
						Temas y sitios construidos desde cero, sin page builders:
						código limpio, rápido y mantenible.
					</p>
				</li>
				<li class="pilar pilar-marketing">
					<h3 class="pilar-titulo">Marketing Digital</h3>
					<p class="pilar-descripcion">
						Estrategia de contenidos, SEO técnico y campañas medibles
						enfocadas en resultados.
					</p>
				</li>
				<li class="pilar pilar-infraestructura">
					<h3 class="pilar-titulo">Infraestructura IT</h3>
					<p class="pilar-descripcion">
						Despliegue en VPS, contenedores con Docker, copias de
						seguridad y monitorización.
					</p>
				</li>
			</ul>
		</section>

		<?php // -----------------------------------------------------------
			// Proyectos destacados (últimos 4 del CPT "proyectos")
			// ----------------------------------------------------------- ?>
		<section class="portada-proyectos" aria-labelledby="proyectos-titulo">
			<h2 id="proyectos-titulo" class="seccion-titulo">Proyectos destacados</h2>

			<?php
			$proyectos_destacados = new WP_Query(
				array(
					'post_type'           => 'proyectos',
					'posts_per_page'      => 4,
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);

			if ( $proyectos_destacados->have_posts() ) :
				?>
				<ul class="proyectos-lista">
					<?php
					while ( $proyectos_destacados->have_posts() ) :
						$proyectos_destacados->the_post();
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

								<h3 class="proyecto-titulo">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h3>

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
				wp_reset_postdata();
			else :
				?>
				<p class="proyectos-vacio">
					Todavía no hay proyectos publicados. Añade algunos desde el
					panel de administración.
				</p>
				<?php
			endif;
			?>
		</section>

		<?php // -----------------------------------------------------------
			// Sobre mí
			// ----------------------------------------------------------- ?>
		<section class="portada-sobre-mi" aria-labelledby="sobre-mi-titulo">
			<h2 id="sobre-mi-titulo" class="seccion-titulo">Sobre mí</h2>
			<p class="sobre-mi-bio">
				Soy un profesional con experiencia en desarrollo web, marketing
				digital e infraestructura. Este es un texto de ejemplo que se
				sustituirá por una biografía real: formación, trayectoria y la
				forma en la que me gusta trabajar con los clientes.
			</p>
		</section>

		<?php // -----------------------------------------------------------
			// Contacto
			// ----------------------------------------------------------- ?>
		<section id="contacto" class="portada-contacto" aria-labelledby="contacto-titulo">
			<h2 id="contacto-titulo" class="seccion-titulo">Contacto</h2>
			<p class="contacto-intro">
				¿Tienes un proyecto en mente? Escríbeme y te respondo pronto.
			</p>
			<ul class="contacto-datos">
				<li class="contacto-dato contacto-email">
					<span class="contacto-etiqueta">Email:</span>
					<a href="mailto:hola@ejemplo.com">hola@ejemplo.com</a>
				</li>
				<li class="contacto-dato contacto-telefono">
					<span class="contacto-etiqueta">Teléfono:</span>
					<a href="tel:+34600000000">+34 600 00 00 00</a>
				</li>
				<li class="contacto-dato contacto-ubicacion">
					<span class="contacto-etiqueta">Ubicación:</span>
					<span>Ciudad, País</span>
				</li>
			</ul>
		</section>

	</div><?php // Fin .portada-cuerpo ?>

</main>

<?php
get_footer();
