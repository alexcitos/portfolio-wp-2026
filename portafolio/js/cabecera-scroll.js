/**
 * Añade la clase "sitio-cabecera--flotando" a la cabecera cuando la página
 * se ha desplazado, para mostrar el borde y la sombra sutiles solo mientras
 * la cabecera flota (sticky) sobre el contenido.
 *
 * @package Portafolio
 */
( function () {
	'use strict';

	var cabecera = document.querySelector( '.sitio-cabecera' );

	if ( ! cabecera ) {
		return;
	}

	var UMBRAL_SCROLL = 8;

	function actualizarEstadoCabecera() {
		cabecera.classList.toggle( 'sitio-cabecera--flotando', window.scrollY > UMBRAL_SCROLL );
	}

	actualizarEstadoCabecera();
	window.addEventListener( 'scroll', actualizarEstadoCabecera, { passive: true } );
} )();
