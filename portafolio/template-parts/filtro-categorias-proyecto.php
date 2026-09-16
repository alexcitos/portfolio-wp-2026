<?php
/**
 * Filtro de categorías del listado de proyectos: "Todos" + cada categoría
 * de "categoria_proyecto", en el orden de negocio (WordPress, Marketing
 * Digital, Infraestructura y Automatización) en vez del orden alfabético
 * que devuelve get_terms() por defecto. Resalta el enlace activo según la
 * página actual (archivo general o archivo de una categoría concreta). Se
 * usa en template-parts/listado-proyectos.php.
 *
 * @package Portafolio
 */

$portafolio_categorias_filtro = get_terms(
	array(
		'taxonomy'   => 'categoria_proyecto',
		'hide_empty' => true,
	)
);

if ( is_wp_error( $portafolio_categorias_filtro ) || ! $portafolio_categorias_filtro ) {
	return;
}

$portafolio_categorias_filtro   = portafolio_ordenar_categorias_proyecto( $portafolio_categorias_filtro );
$portafolio_categoria_actual_id = is_tax( 'categoria_proyecto' ) ? get_queried_object_id() : 0;
?>
<nav class="filtro-categorias" aria-label="<?php esc_attr_e( 'Filtrar proyectos por categoría', 'portafolio' ); ?>">
	<ul class="filtro-categorias-lista">
		<li class="filtro-categoria">
			<?php
			$portafolio_filtro_activo = ( 0 === $portafolio_categoria_actual_id );
			?>
			<a
				href="<?php echo esc_url( get_post_type_archive_link( 'proyectos' ) ); ?>"
				class="filtro-categoria-enlace<?php echo $portafolio_filtro_activo ? ' filtro-categoria-enlace--activo' : ''; ?>"
				<?php echo $portafolio_filtro_activo ? 'aria-current="page"' : ''; ?>
			>
				<?php esc_html_e( 'Todos', 'portafolio' ); ?>
			</a>
		</li>
		<?php foreach ( $portafolio_categorias_filtro as $portafolio_categoria ) : ?>
			<?php $portafolio_filtro_activo = ( $portafolio_categoria->term_id === $portafolio_categoria_actual_id ); ?>
			<li class="filtro-categoria">
				<a
					href="<?php echo esc_url( get_term_link( $portafolio_categoria ) ); ?>"
					class="filtro-categoria-enlace<?php echo $portafolio_filtro_activo ? ' filtro-categoria-enlace--activo' : ''; ?>"
					<?php echo $portafolio_filtro_activo ? 'aria-current="page"' : ''; ?>
				>
					<?php echo esc_html( $portafolio_categoria->name ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
