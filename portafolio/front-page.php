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
$portafolio_foto_sobre_mi = get_field( 'sobre_mi_foto', $portafolio_portada_id );

// Valores de reserva para cuando los campos aún no se han rellenado.
$portafolio_nombre      = $portafolio_nombre ? $portafolio_nombre : 'Nombre Apellido';
$portafolio_tagline     = $portafolio_tagline ? $portafolio_tagline : 'Desarrollo WordPress a medida, marketing digital e infraestructura IT para negocios que quieren crecer.';
$portafolio_boton_texto = $portafolio_boton_texto ? $portafolio_boton_texto : 'Hablemos de tu proyecto';
$portafolio_boton_url   = $portafolio_boton_url ? $portafolio_boton_url : '#contacto';
$portafolio_email       = $portafolio_email ? $portafolio_email : 'hola@ejemplo.com';
$portafolio_telefono    = $portafolio_telefono ? $portafolio_telefono : '+34 600 00 00 00';
$portafolio_ubicacion   = $portafolio_ubicacion ? $portafolio_ubicacion : 'Ciudad, País';

// Solo dígitos: formato que exige el enlace de WhatsApp (wa.me/<número>,
// sin "+" ni espacios). El enlace "tel:" de más abajo conserva su propio
// formato porque ahí sí acepta el "+".
$portafolio_telefono_whatsapp = preg_replace( '/[^0-9]/', '', $portafolio_telefono );
?>

<main id="contenido" class="portada">

	<?php // ---------------------------------------------------------------
		// Zona oscura (Hero + Pilares + Proyectos + Sobre mí + Contacto):
		// toda la portada comparte un único fondo y las mismas esferas
		// animadas, concentradas arriba y desvanecidas hacia abajo con una
		// máscara, para que no haya corte entre secciones. Las esferas y
		// las capas "eco" del panel son puramente decorativas (aria-hidden).
		// --------------------------------------------------------------- ?>
	<div class="portada-oscura">

		<div class="portada-oscura-fondo" aria-hidden="true">
			<span class="hero-forma hero-forma--1"></span>
			<span class="hero-forma hero-forma--2"></span>
			<span class="hero-forma hero-forma--3"></span>
			<span class="hero-forma hero-forma--4"></span>
			<span class="hero-forma hero-forma--5"></span>
			<span class="hero-forma hero-forma--6"></span>
			<span class="hero-forma hero-forma--7"></span>
			<span class="hero-forma hero-forma--8"></span>
			<span class="hero-forma hero-forma--9"></span>
			<span class="hero-forma hero-forma--10"></span>
			<span class="hero-forma hero-forma--11"></span>
			<span class="hero-forma hero-forma--12"></span>
			<span class="hero-forma hero-forma--13"></span>
			<span class="hero-forma hero-forma--14"></span>
			<span class="hero-forma hero-forma--15"></span>
			<span class="hero-forma hero-forma--16"></span>
			<span class="hero-forma hero-forma--17"></span>
			<span class="hero-forma hero-forma--18"></span>
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

		<?php // -----------------------------------------------------------
			// Proyectos destacados (últimos 4 del CPT "proyectos"): tercera
			// sección dentro de la misma zona oscura que Hero y Pilares. Sin
			// fondo ni esferas propias —usa las de .portada-oscura-fondo—;
			// debe quedar anidada aquí, no como hermana suelta después de
			// .portada-oscura (ver comentario en .portada-oscura, en
			// style.css).
			// ----------------------------------------------------------- ?>
		<section class="portada-proyectos" aria-labelledby="proyectos-titulo">

			<div class="proyectos-contenedor">
				<div class="proyectos-cabecera">
					<p class="proyectos-eyebrow">// proyectos</p>
					<h2 id="proyectos-titulo" class="proyectos-titulo">Proyectos destacados</h2>
				</div>

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

									<div class="proyecto-contenido">
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
									</div>
								</article>
							</li>
							<?php
						endwhile;
						?>
					</ul>
					<p class="proyectos-cta">
						<a class="boton boton-primario" href="<?php echo esc_url( get_post_type_archive_link( 'proyectos' ) ); ?>">Ver todos los proyectos</a>
					</p>
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
			</div>
		</section>

		<?php // -----------------------------------------------------------
			// Sobre mí: cuarta sección de la misma zona oscura. Sin
			// eyebrow+título visible clásico: la tarjeta (foto + bio) ya se
			// explica sola, así que el <h2> queda oculto visualmente y solo
			// sirve de nombre accesible para la sección (aria-labelledby).
			// ----------------------------------------------------------- ?>
		<section class="portada-sobre-mi" aria-labelledby="sobre-mi-titulo">
			<h2 id="sobre-mi-titulo" class="screen-reader-text">Sobre mí</h2>
			<p class="sobre-mi-eyebrow">// sobre mí</p>

			<div class="sobre-mi-tarjeta">
				<?php // El anillo con resplandor se muestra siempre —con la foto real
					// o, mientras no se suba ninguna desde ACF ("Datos del
					// sitio" → "Sobre mí" → "Foto"), con un marcador— para que
					// la tarjeta nunca se vea a medio terminar. ?>
				<div class="sobre-mi-foto">
					<?php if ( $portafolio_foto_sobre_mi ) : ?>
						<img
							src="<?php echo esc_url( $portafolio_foto_sobre_mi['sizes']['medium'] ?? $portafolio_foto_sobre_mi['url'] ); ?>"
							alt="<?php echo esc_attr( $portafolio_foto_sobre_mi['alt'] ? $portafolio_foto_sobre_mi['alt'] : $portafolio_nombre ); ?>"
							width="250"
							height="250"
						>
					<?php else : ?>
						<div class="sobre-mi-foto-marcador" role="img" aria-label="<?php echo esc_attr( $portafolio_nombre ); ?>">
							<svg width="88" height="88" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
						</div>
					<?php endif; ?>
				</div>

				<div class="sobre-mi-texto">
					<?php if ( $portafolio_sobre_mi ) : ?>
						<?php echo wp_kses_post( $portafolio_sobre_mi ); ?>
					<?php else : ?>
						<p>
							Soy un profesional con experiencia en desarrollo web, marketing
							digital e infraestructura. Este es un texto de ejemplo que se
							sustituirá por una biografía real: formación, trayectoria y la
							forma en la que me gusta trabajar con los clientes.
						</p>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<?php // -----------------------------------------------------------
			// Contacto: última sección, misma zona oscura. La tarjeta reúne
			// los datos de contacto (con icono, sin etiqueta visible —el
			// icono ya la transmite; queda como texto para lectores de
			// pantalla) y un formulario real que envía correo por
			// wp_mail() vía portafolio_procesar_formulario_contacto() en
			// functions.php (patrón Post-Redirect-Get: el estado del envío
			// llega como ?contacto=enviado|error en la URL).
			// ----------------------------------------------------------- ?>
		<section id="contacto" class="portada-contacto" aria-labelledby="contacto-titulo">
			<div class="contacto-tarjeta">

				<div class="contacto-info">
					<p class="contacto-eyebrow">// contacto</p>
					<h2 id="contacto-titulo" class="contacto-titulo">Hablemos de tu proyecto</h2>

					<ul class="contacto-datos">
						<li class="contacto-dato">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m2 6 8.4 6a3 3 0 0 0 3.2 0L22 6"></path></svg>
							<span class="screen-reader-text">Email:</span>
							<a href="mailto:<?php echo antispambot( $portafolio_email ); ?>"><?php echo antispambot( $portafolio_email ); ?></a>
						</li>
						<li class="contacto-dato">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
							<span class="screen-reader-text">Teléfono:</span>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $portafolio_telefono ) ); ?>"><?php echo esc_html( $portafolio_telefono ); ?></a>
						</li>
						<?php // Mismo número que el teléfono de arriba, como enlace directo a
							// WhatsApp (wa.me): así queda claro que también se puede escribir
							// por ahí, sin pedir un dato de contacto nuevo. ?>
						<li class="contacto-dato contacto-dato--whatsapp">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.6 6.32A7.85 7.85 0 0 0 12.05 4a7.94 7.94 0 0 0-6.9 11.89L4 20l4.24-1.11a7.9 7.9 0 0 0 3.79.97 7.95 7.95 0 0 0 7.94-7.94 7.88 7.88 0 0 0-2.37-5.6zm-5.55 12.21a6.6 6.6 0 0 1-3.37-.92l-.24-.14-2.51.66.67-2.44-.16-.25a6.6 6.6 0 0 1 10.24-8.2A6.56 6.56 0 0 1 18.58 12a6.6 6.6 0 0 1-6.53 6.53zm3.6-4.93c-.2-.1-1.17-.58-1.35-.64s-.32-.1-.45.1-.5.64-.62.77-.23.15-.43.05a5.4 5.4 0 0 1-1.59-.98 6 6 0 0 1-1.1-1.37c-.12-.2 0-.3.09-.4s.2-.23.29-.35a1.3 1.3 0 0 0 .2-.33.37.37 0 0 0 0-.35c-.05-.1-.45-1.08-.61-1.48s-.33-.33-.45-.34h-.38a.74.74 0 0 0-.53.25 2.24 2.24 0 0 0-.7 1.67 3.9 3.9 0 0 0 .82 2.06 8.9 8.9 0 0 0 3.4 3 3.9 3.9 0 0 0 2.39.5 2 2 0 0 0 1.33-.94 1.65 1.65 0 0 0 .11-.94c-.05-.08-.18-.13-.38-.23z"></path></svg>
							<span class="screen-reader-text">WhatsApp:</span>
							<a href="https://wa.me/<?php echo esc_attr( $portafolio_telefono_whatsapp ); ?>?text=<?php echo rawurlencode( 'Hola, me gustaría hablar sobre un proyecto.' ); ?>" target="_blank" rel="noopener noreferrer">Escribir por WhatsApp</a>
						</li>
						<li class="contacto-dato">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
							<span class="screen-reader-text">Ubicación:</span>
							<span><?php echo esc_html( $portafolio_ubicacion ); ?></span>
						</li>
					</ul>
				</div>

				<form class="formulario-contacto" method="post" action="<?php echo esc_url( home_url( '/#contacto' ) ); ?>">
					<?php wp_nonce_field( 'portafolio_contacto', 'portafolio_contacto_nonce' ); ?>
					<input type="hidden" name="portafolio_contacto_enviado" value="1">

					<p class="formulario-honeypot" aria-hidden="true">
						<label for="portafolio_web">No rellenar este campo</label>
						<input type="text" id="portafolio_web" name="portafolio_web" tabindex="-1" autocomplete="off">
					</p>

					<?php if ( isset( $_GET['contacto'] ) && 'enviado' === $_GET['contacto'] ) : ?>
						<p class="formulario-mensaje formulario-mensaje--exito" role="status">
							Gracias, tu mensaje se envió correctamente. Te responderé pronto.
						</p>
					<?php elseif ( isset( $_GET['contacto'] ) && 'error' === $_GET['contacto'] ) : ?>
						<p class="formulario-mensaje formulario-mensaje--error" role="alert">
							No se pudo enviar el mensaje. Revisa los datos e inténtalo de nuevo.
						</p>
					<?php endif; ?>

					<p class="formulario-campo">
						<label class="screen-reader-text" for="contacto-nombre">Nombre</label>
						<input type="text" id="contacto-nombre" name="contacto_nombre" placeholder="Nombre" required>
					</p>
					<p class="formulario-campo">
						<label class="screen-reader-text" for="contacto-email">Email</label>
						<input type="email" id="contacto-email" name="contacto_email" placeholder="Email" required>
					</p>
					<p class="formulario-campo">
						<label class="screen-reader-text" for="contacto-mensaje">Mensaje</label>
						<textarea id="contacto-mensaje" name="contacto_mensaje" rows="4" placeholder="Mensaje" required></textarea>
					</p>
					<button type="submit" class="boton boton-primario">Enviar mensaje</button>
				</form>

			</div>
		</section>

	</div><?php // Fin .portada-oscura ?>

</main>

<?php
get_footer();
