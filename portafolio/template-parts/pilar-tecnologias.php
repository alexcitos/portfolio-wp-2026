<?php
/**
 * Fila de iconos de tecnología anclada al pie de una tarjeta de "Pilar"
 * (ver .pilar-tecnologias en style.css: margin-top: auto la empuja hacia
 * abajo dentro de la tarjeta flex, así queda alineada con las de las otras
 * tarjetas aunque cada descripción tenga distinta longitud).
 *
 * Argumento vía get_template_part( ..., array( 'tecnologias' => array( 'wordpress', 'elementor', ... ) ) ):
 * claves del catálogo a mostrar, en el orden dado (ver
 * portafolio_catalogo_iconos_tecnologia() en inc/iconos-tecnologia.php).
 *
 * @package Portafolio
 */

$portafolio_claves_tecnologia = isset( $args['tecnologias'] ) ? (array) $args['tecnologias'] : array();

if ( ! $portafolio_claves_tecnologia ) {
	return;
}

$portafolio_catalogo_tecnologia = portafolio_catalogo_iconos_tecnologia();
?>
<ul class="pilar-tecnologias">
	<?php foreach ( $portafolio_claves_tecnologia as $portafolio_clave ) : ?>
		<?php
		if ( ! isset( $portafolio_catalogo_tecnologia[ $portafolio_clave ] ) ) {
			continue;
		}
		$portafolio_tecnologia = $portafolio_catalogo_tecnologia[ $portafolio_clave ];
		?>
		<?php $portafolio_es_trazo = isset( $portafolio_tecnologia['tipo'] ) && 'trazo' === $portafolio_tecnologia['tipo']; ?>
		<?php // El <li> se deja "limpio" (sin role ni aria-label): role="img" puesto
			// directo en un <li> le pisa su rol implícito de "listitem" en el árbol
			// de accesibilidad, y entonces ul.pilar-tecnologias deja de tener solo
			// hijos "listitem" —justo el fallo de Lighthouse "Las listas no
			// contienen únicamente elementos <li>...", porque esa auditoría lee
			// roles, no solo nombres de etiqueta—. El nombre accesible de la
			// insignia ahora lo lleva el <span> de adentro, que es además quien
			// necesita position:relative para anclar el tooltip (ver
			// .pilar-tecnologia en style.css, sin cambios: los selectores ya
			// apuntaban a la clase, no a la etiqueta <li>). ?>
		<li>
			<span class="pilar-tecnologia<?php echo $portafolio_es_trazo ? ' pilar-tecnologia--trazo' : ''; ?>" tabindex="0" role="img" aria-label="<?php echo esc_attr( $portafolio_tecnologia['nombre'] ); ?>">
				<?php if ( isset( $portafolio_tecnologia['path'] ) ) : ?>
					<svg viewBox="<?php echo esc_attr( $portafolio_tecnologia['viewbox'] ); ?>" aria-hidden="true">
						<path d="<?php echo esc_attr( $portafolio_tecnologia['path'] ); ?>"></path>
					</svg>
				<?php elseif ( isset( $portafolio_tecnologia['paths'] ) ) : ?>
					<svg viewBox="<?php echo esc_attr( $portafolio_tecnologia['viewbox'] ); ?>" aria-hidden="true">
						<?php foreach ( $portafolio_tecnologia['paths'] as $portafolio_trazo ) : ?>
							<path d="<?php echo esc_attr( $portafolio_trazo ); ?>"></path>
						<?php endforeach; ?>
					</svg>
				<?php else : ?>
					<span class="pilar-tecnologia-texto" aria-hidden="true"><?php echo esc_html( $portafolio_tecnologia['texto'] ); ?></span>
				<?php endif; ?>
				<span class="pilar-tecnologia-etiqueta" aria-hidden="true"><?php echo esc_html( $portafolio_tecnologia['nombre'] ); ?></span>
			</span>
		</li>
	<?php endforeach; ?>
</ul>
