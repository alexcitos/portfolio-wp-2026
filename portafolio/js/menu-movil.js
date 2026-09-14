/**
 * Menú móvil: alterna la navegación principal con el botón hamburguesa
 * bajo 768px. Gestiona aria-expanded y la etiqueta accesible del botón
 * (abrir/cerrar), cierra con la tecla Escape devolviendo el foco al botón,
 * y cierra automáticamente al pulsar un enlace del menú.
 *
 * @package Portafolio
 */
( function () {
	'use strict';

	var boton = document.querySelector( '.menu-movil-boton' );
	var nav = document.getElementById( 'sitio-navegacion' );

	if ( ! boton || ! nav ) {
		return;
	}

	var CLASE_ABIERTA = 'sitio-navegacion--abierta';

	function estaAbierto() {
		return boton.getAttribute( 'aria-expanded' ) === 'true';
	}

	function abrirMenu() {
		nav.classList.add( CLASE_ABIERTA );
		boton.setAttribute( 'aria-expanded', 'true' );
		boton.setAttribute( 'aria-label', boton.dataset.etiquetaCerrar );
	}

	function cerrarMenu( devolverFoco ) {
		nav.classList.remove( CLASE_ABIERTA );
		boton.setAttribute( 'aria-expanded', 'false' );
		boton.setAttribute( 'aria-label', boton.dataset.etiquetaAbrir );

		if ( devolverFoco ) {
			boton.focus();
		}
	}

	boton.addEventListener( 'click', function () {
		if ( estaAbierto() ) {
			cerrarMenu( false );
		} else {
			abrirMenu();
		}
	} );

	// Escape cierra el menú desde cualquier punto (botón o dentro del nav).
	document.addEventListener( 'keydown', function ( evento ) {
		if ( 'Escape' === evento.key && estaAbierto() ) {
			cerrarMenu( true );
		}
	} );

	// Cierre automático al pulsar cualquier enlace del menú.
	nav.addEventListener( 'click', function ( evento ) {
		if ( evento.target.closest( 'a' ) ) {
			cerrarMenu( false );
		}
	} );
} )();
