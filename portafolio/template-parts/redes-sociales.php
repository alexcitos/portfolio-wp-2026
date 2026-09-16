<?php
/**
 * Enlaces a redes sociales (LinkedIn, GitHub, Instagram, Strava,
 * WhatsApp), leídos de "Datos del sitio" → "Redes sociales" (ver
 * portafolio_obtener_redes_sociales() en functions.php). Todos abren en
 * pestaña nueva. Se usa tanto en la barra flotante de todo el sitio (ver
 * header.php) como en la columna de contacto del home (ver
 * front-page.php); el 'clase' distingue el contexto para el CSS (ver
 * .redes-sociales--flotante/--contacto en style.css) y 'etiqueta' evita
 * que ambas instancias compartan el mismo nombre accesible cuando
 * coinciden en la misma página.
 *
 * Argumentos vía get_template_part( ..., array( 'clase' => ..., 'etiqueta' => ... ) ).
 *
 * @package Portafolio
 */

$portafolio_redes = portafolio_obtener_redes_sociales();

if ( ! $portafolio_redes ) {
	return;
}

$portafolio_clase_extra = isset( $args['clase'] ) ? ' ' . $args['clase'] : '';
$portafolio_etiqueta     = isset( $args['etiqueta'] ) ? $args['etiqueta'] : __( 'Redes sociales', 'portafolio' );
?>
<nav class="redes-sociales<?php echo esc_attr( $portafolio_clase_extra ); ?>" aria-label="<?php echo esc_attr( $portafolio_etiqueta ); ?>">
	<ul class="redes-sociales-lista">
		<?php foreach ( $portafolio_redes as $portafolio_red ) : ?>
			<li class="red-social red-social--<?php echo esc_attr( $portafolio_red['clave'] ); ?>">
				<a
					href="<?php echo esc_url( $portafolio_red['url'] ); ?>"
					class="red-social-enlace"
					target="_blank"
					rel="noopener noreferrer"
					aria-label="<?php echo esc_attr( $portafolio_red['nombre'] ); ?>"
				>
					<svg viewBox="<?php echo esc_attr( $portafolio_red['viewbox'] ); ?>" aria-hidden="true">
						<path d="<?php echo esc_attr( $portafolio_red['path'] ); ?>"></path>
					</svg>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
