<?php
/**
 * Pie del documento.
 *
 * @package Portafolio
 */
?>

<footer class="sitio-pie">
	<p>
		<?php
		/* translators: %s: año actual. */
		printf( esc_html__( '© %s — Portafolio', 'portafolio' ), esc_html( gmdate( 'Y' ) ) );
		?>
	</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
