<?php
/**
 * Pie del documento.
 *
 * Créditos + datos de "Datos del sitio" → "Pie de página" (tokens_usados,
 * tokens_actualizado), anclados a la portada igual que el resto de campos
 * ACF del tema (ver front-page.php).
 *
 * @package Portafolio
 */

$portafolio_pie_tokens_usados      = get_field( 'tokens_usados', get_option( 'page_on_front' ) );
$portafolio_pie_tokens_actualizado = get_field( 'tokens_actualizado', get_option( 'page_on_front' ) );

$portafolio_pie_tokens_usados_texto = number_format_i18n( $portafolio_pie_tokens_usados ? (int) $portafolio_pie_tokens_usados : 0 );

$portafolio_pie_actualizado_texto = '';
if ( $portafolio_pie_tokens_actualizado ) {
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
				esc_html( $portafolio_pie_tokens_usados_texto ),
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
