<?php
/**
 * Portada del sitio (front-page.php).
 *
 * Estructura de secciones: hero, pilares/servicios, proyectos destacados,
 * sobre mí y contacto.
 *
 * El contenido editable (nombre, tagline, botón, datos de contacto y
 * biografía) vive en el grupo de campos ACF "Datos del sitio", anclado a la
 * página configurada como portada en Ajustes → Lectura. Se lee con
 * get_field( 'campo', get_option( 'page_on_front' ) ) para no depender de un
 * ID fijo. Cada campo tiene un valor de reserva mientras esté vacío.
 *
 * @package Portafolio
 */

get_header();

$portafolio_portada_id = (int) get_option( 'page_on_front' );

$portafolio_nombre       = get_field( 'nombre', $portafolio_portada_id );
$portafolio_tagline      = get_field( 'tagline', $portafolio_portada_id );
$portafolio_boton_texto  = get_field( 'boton_contacto_texto', $portafolio_portada_id );
$portafolio_boton_url    = get_field( 'boton_contacto_enlace', $portafolio_portada_id );
$portafolio_email        = get_field( 'email', $portafolio_portada_id );
$portafolio_telefono     = get_field( 'telefono', $portafolio_portada_id );
$portafolio_ubicacion    = get_field( 'ubicacion', $portafolio_portada_id );
$portafolio_sobre_mi     = get_field( 'sobre_mi_bio', $portafolio_portada_id );

// Valores de reserva para cuando los campos aún no se han rellenado.
$portafolio_nombre      = $portafolio_nombre ? $portafolio_nombre : 'Nombre Apellido';
$portafolio_tagline     = $portafolio_tagline ? $portafolio_tagline : 'Desarrollo WordPress a medida, marketing digital e infraestructura IT para negocios que quieren crecer.';
$portafolio_boton_texto = $portafolio_boton_texto ? $portafolio_boton_texto : 'Hablemos de tu proyecto';
$portafolio_boton_url   = $portafolio_boton_url ? $portafolio_boton_url : '#contacto';
$portafolio_email       = $portafolio_email ? $portafolio_email : 'hola@ejemplo.com';
$portafolio_telefono    = $portafolio_telefono ? $portafolio_telefono : '+34 600 00 00 00';
$portafolio_ubicacion   = $portafolio_ubicacion ? $portafolio_ubicacion : 'Ciudad, País';
?>

<main id="contenido" class="portada">

	<?php // ---------------------------------------------------------------
		// Zona oscura (Hero + Pilares): comparten un único fondo y las
		// mismas esferas animadas, concentradas arriba y desvanecidas hacia
		// abajo con una máscara, para que no haya corte entre ambas
		// secciones. Las esferas y las capas "eco" del panel son
		// puramente decorativas (aria-hidden).
		// --------------------------------------------------------------- ?>
	<div class="portada-oscura">

		<div class="portada-oscura-fondo" aria-hidden="true">
			<span class="hero-forma hero-forma--1"></span>
			<span class="hero-forma hero-forma--2"></span>
			<span class="hero-forma hero-forma--3"></span>
			<span class="hero-forma hero-forma--4"></span>
		</div>

		<section class="portada-hero" aria-labelledby="hero-titulo">

			<div class="hero-panel">
				<span class="hero-panel-eco hero-panel-eco--1" aria-hidden="true"></span>
				<span class="hero-panel-eco hero-panel-eco--2" aria-hidden="true"></span>

				<div class="hero-panel-marco">
					<div class="hero-panel-vidrio">
						<p class="hero-eyebrow">// portafolio</p>
						<h1 id="hero-titulo" class="hero-nombre"><?php echo esc_html( $portafolio_nombre ); ?></h1>
						<p class="hero-tagline"><?php echo esc_html( $portafolio_tagline ); ?></p>
						<p class="hero-accion">
							<a class="boton boton-primario" href="<?php echo esc_url( $portafolio_boton_url ); ?>"><?php echo esc_html( $portafolio_boton_texto ); ?></a>
						</p>
					</div>
				</div>
			</div>

		</section>

		<?php // -----------------------------------------------------------
			// Pilares / servicios: mismas secciones dentro de la zona
			// oscura, con las tarjetas de vidrio de cada pilar. Los iconos
			// también son decorativos (aria-hidden) porque el título ya
			// identifica el pilar.
			// ----------------------------------------------------------- ?>
		<section class="portada-pilares" aria-labelledby="pilares-titulo">

			<div class="pilares-fondo" aria-hidden="true">
				<span class="pilares-forma pilares-forma--1"></span>
				<span class="pilares-forma pilares-forma--2"></span>
				<span class="pilares-forma pilares-forma--3"></span>
			</div>

			<div class="pilares-contenedor">
				<div class="pilares-cabecera">
					<p class="pilares-eyebrow">// pilares</p>
					<h2 id="pilares-titulo" class="pilares-titulo">Construyo, escalo, automatizo</h2>
				</div>

				<ul class="pilares-lista">
					<li class="pilar pilar-wordpress">
						<span class="pilar-icono" aria-hidden="true">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<polyline points="8 6 3 12 8 18"></polyline>
								<polyline points="16 6 21 12 16 18"></polyline>
							</svg>
						</span>
						<h3 class="pilar-titulo">WordPress</h3>
						<p class="pilar-descripcion">
							Temas y sitios construidos desde cero, sin page builders:
							código limpio, rápido y mantenible.
						</p>
					</li>
					<li class="pilar pilar-marketing">
						<span class="pilar-icono" aria-hidden="true">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<polyline points="3 17 9 11 13 15 21 6"></polyline>
								<polyline points="15 6 21 6 21 12"></polyline>
							</svg>
						</span>
						<h3 class="pilar-titulo">Marketing Digital</h3>
						<p class="pilar-descripcion">
							Estrategia de contenidos, SEO técnico y campañas medibles
							enfocadas en resultados.
						</p>
					</li>
					<li class="pilar pilar-infraestructura">
						<span class="pilar-icono" aria-hidden="true">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="4" width="18" height="6" rx="1"></rect>
								<rect x="3" y="14" width="18" height="6" rx="1"></rect>
								<line x1="7" y1="7" x2="7" y2="7"></line>
								<line x1="7" y1="17" x2="7" y2="17"></line>
							</svg>
						</span>
						<h3 class="pilar-titulo">Infraestructura IT</h3>
						<p class="pilar-descripcion">
							Despliegue en VPS, contenedores con Docker, copias de
							seguridad, monitorización y automatización de procesos
							con n8n.
						</p>
					</li>
				</ul>
			</div>
		</section>

	</div><?php // Fin .portada-oscura ?>

	<div class="portada-cuerpo">

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
						<?php $portafolio_categoria_proyecto = get_the_terms( get_the_ID(), 'categoria_proyecto' ); ?>
						<li <?php post_class( 'proyecto' ); ?>>
							<article>
								<?php if ( has_post_thumbnail() ) : ?>
									<figure class="proyecto-imagen">
										<a href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail( 'medium_large' ); ?>
										</a>
									</figure>
								<?php endif; ?>

								<?php if ( $portafolio_categoria_proyecto && ! is_wp_error( $portafolio_categoria_proyecto ) ) : ?>
									<ul class="proyecto-categorias">
										<?php foreach ( $portafolio_categoria_proyecto as $portafolio_categoria ) : ?>
											<li class="proyecto-categoria">
												<a href="<?php echo esc_url( get_term_link( $portafolio_categoria ) ); ?>">
													<?php echo esc_html( $portafolio_categoria->name ); ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
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
			<?php if ( $portafolio_sobre_mi ) : ?>
				<div class="sobre-mi-bio">
					<?php echo wp_kses_post( $portafolio_sobre_mi ); ?>
				</div>
			<?php else : ?>
				<p class="sobre-mi-bio">
					Soy un profesional con experiencia en desarrollo web, marketing
					digital e infraestructura. Este es un texto de ejemplo que se
					sustituirá por una biografía real: formación, trayectoria y la
					forma en la que me gusta trabajar con los clientes.
				</p>
			<?php endif; ?>
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
					<a href="mailto:<?php echo antispambot( $portafolio_email ); ?>"><?php echo antispambot( $portafolio_email ); ?></a>
				</li>
				<li class="contacto-dato contacto-telefono">
					<span class="contacto-etiqueta">Teléfono:</span>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $portafolio_telefono ) ); ?>"><?php echo esc_html( $portafolio_telefono ); ?></a>
				</li>
				<li class="contacto-dato contacto-ubicacion">
					<span class="contacto-etiqueta">Ubicación:</span>
					<span><?php echo esc_html( $portafolio_ubicacion ); ?></span>
				</li>
			</ul>
		</section>

	</div><?php // Fin .portada-cuerpo ?>

</main>

<?php
get_footer();
