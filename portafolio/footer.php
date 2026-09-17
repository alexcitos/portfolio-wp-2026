<?php
/**
 * Pie del documento.
 *
 * Créditos + datos de "Datos del sitio" → "Pie de página" (tokens_usados,
 * tokens_mostrar_contador, tokens_texto_alternativo, tokens_actualizado),
 * anclados a la portada igual que el resto de campos ACF del tema (ver
 * front-page.php). Con "Mostrar contador de tokens" desactivado, la frase
 * del pie no cambia de estructura: solo el número de tokens se sustituye
 * por "Texto alternativo".
 *
 * @package Portafolio
 */

$portafolio_pie_portada_id         = get_option( 'page_on_front' );
$portafolio_pie_mostrar_contador   = get_field( 'tokens_mostrar_contador', $portafolio_pie_portada_id );
$portafolio_pie_tokens_usados      = get_field( 'tokens_usados', $portafolio_pie_portada_id );
$portafolio_pie_tokens_texto_alt   = get_field( 'tokens_texto_alternativo', $portafolio_pie_portada_id );
$portafolio_pie_tokens_actualizado = get_field( 'tokens_actualizado', $portafolio_pie_portada_id );

// Antes de guardar nunca la casilla (sitio recién creado, o creado antes
// de que esta casilla existiera), get_field() devuelve null en vez de su
// default_value: se trata igual que "activado", que es el comportamiento
// que tenía el pie antes de que existiera esta casilla.
$portafolio_pie_mostrar_contador = ( null === $portafolio_pie_mostrar_contador || '' === $portafolio_pie_mostrar_contador )
	? true
	: (bool) $portafolio_pie_mostrar_contador;

if ( $portafolio_pie_mostrar_contador ) {
	// number_format_i18n() ya separa los miles con el punto del idioma del
	// sitio; tokens_usados se guarda siempre limpio de separadores (ver
	// portafolio_sanear_tokens_usados() en functions.php).
	$portafolio_pie_tokens_valor = number_format_i18n( $portafolio_pie_tokens_usados ? (int) $portafolio_pie_tokens_usados : 0 );
} else {
	$portafolio_pie_tokens_valor = $portafolio_pie_tokens_texto_alt ? $portafolio_pie_tokens_texto_alt : __( 'muchísimos', 'portafolio' );
}

// La fecha de "actualizado el" solo tiene sentido junto al contador; si
// está desactivado (se muestra el texto alternativo), se omite también.
$portafolio_pie_actualizado_texto = '';
if ( $portafolio_pie_mostrar_contador && $portafolio_pie_tokens_actualizado ) {
	// return_format 'Ymd' del campo ACF -> fecha localizada al idioma del sitio.
	$portafolio_pie_actualizado_texto = date_i18n( 'j \d\e F \d\e Y', strtotime( $portafolio_pie_tokens_actualizado ) );
}
?>

<footer class="sitio-pie">
	<div class="sitio-pie-interior">
		<p class="sitio-pie-texto">
			<?php
			printf(
				/* translators: 1: laptop emoji, 2: robot emoji, 3: número de tokens, 4: taza de café emoji, 5: año actual. */
				esc_html__( 'Hecho con %1$s por Alex Rodríguez, junto con %2$s Claude Code, %3$s tokens de IA y mucho %4$s · © %5$s Alex Rodríguez', 'portafolio' ),
				'<span aria-hidden="true">💻</span>',
				'<span aria-hidden="true">🤖</span>',
				// Envuelto en su propio <span> para que js/contador-tokens.js
				// pueda animarlo sin tocar el resto de la frase (ver
				// wp_enqueue_script() en functions.php).
				'<span class="sitio-pie-tokens">' . esc_html( $portafolio_pie_tokens_valor ) . '</span>',
				'<span aria-hidden="true">☕</span>',
				esc_html( gmdate( 'Y' ) )
			);
			?>
		</p>
		<?php if ( $portafolio_pie_actualizado_texto ) : ?>
			<p class="sitio-pie-actualizado">
				<?php
				/* translators: %s: fecha del último conteo de tokens. */
				printf( esc_html__( 'actualizado %s', 'portafolio' ), esc_html( $portafolio_pie_actualizado_texto ) );
				?>
			</p>
		<?php endif; ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
