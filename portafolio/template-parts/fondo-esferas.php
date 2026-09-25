<?php
/**
 * Fondo decorativo compartido de la zona oscura: diez esferas difuminadas
 * en una capa fija del tamaño de la ventana (ver .portada-oscura-fondo y
 * .fondo-esfera en style.css). Al ser "fixed", el fondo no se desplaza con
 * el scroll: el contenido pasa por encima y siempre hay esferas detrás de
 * lo que se está viendo, sin cortes entre secciones y con un costo de
 * renderizado que no crece con el largo de la página. Puramente decorativo
 * (aria-hidden). Se usa una sola vez por página, en cualquier plantilla con
 * el fondo oscuro compartido (.portada-oscura): la portada, el archivo de
 * "proyectos", los archivos de categoría y la ficha de proyecto.
 *
 * La animación la arranca, pausa y reanuda js/animaciones-ambiente.js.
 *
 * @package Portafolio
 */
?>
<div class="portada-oscura-fondo" aria-hidden="true">
	<span class="fondo-esfera fondo-esfera--1"></span>
	<span class="fondo-esfera fondo-esfera--2"></span>
	<span class="fondo-esfera fondo-esfera--3"></span>
	<span class="fondo-esfera fondo-esfera--4"></span>
	<span class="fondo-esfera fondo-esfera--5"></span>
	<span class="fondo-esfera fondo-esfera--6"></span>
	<span class="fondo-esfera fondo-esfera--7"></span>
	<span class="fondo-esfera fondo-esfera--8"></span>
	<span class="fondo-esfera fondo-esfera--9"></span>
	<span class="fondo-esfera fondo-esfera--10"></span>
</div>
