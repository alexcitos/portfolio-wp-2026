/**
 * Desplazamiento animado al pulsar un enlace de ancla interno (href="#id"),
 * más lento y suave que el salto instantáneo por defecto del navegador.
 *
 * El enlace "saltar al contenido" (.salto-contenido) queda fuera a
 * propósito: por accesibilidad, ese salto debe seguir siendo instantáneo.
 *
 * @package Portafolio
 */
( function () {
	'use strict';

	var DURACION_MS = 900;

	var prefiereMovimientoReducido = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function suavizarProgreso( progreso ) {
		// easeInOutQuad: arranca y termina suave, acelera en el medio.
		return progreso < 0.5
			? 2 * progreso * progreso
			: 1 - Math.pow( -2 * progreso + 2, 2 ) / 2;
	}

	function desplazarA( destinoY ) {
		var inicioY = window.scrollY;
		var distancia = destinoY - inicioY;
		var inicioTiempo = null;

		function paso( marcaTiempo ) {
			if ( null === inicioTiempo ) {
				inicioTiempo = marcaTiempo;
			}

			var progreso = Math.min( ( marcaTiempo - inicioTiempo ) / DURACION_MS, 1 );

			window.scrollTo( 0, inicioY + distancia * suavizarProgreso( progreso ) );

			if ( progreso < 1 ) {
				window.requestAnimationFrame( paso );
			}
		}

		window.requestAnimationFrame( paso );
	}

	document.addEventListener( 'click', function ( evento ) {
		var enlace = evento.target.closest( 'a[href^="#"]:not(.salto-contenido)' );

		if ( ! enlace || enlace.getAttribute( 'href' ).length < 2 ) {
			return;
		}

		var destino = document.getElementById( enlace.getAttribute( 'href' ).slice( 1 ) );

		if ( ! destino ) {
			return;
		}

		evento.preventDefault();

		if ( window.history && window.history.pushState ) {
			window.history.pushState( null, '', enlace.getAttribute( 'href' ) );
		}

		if ( prefiereMovimientoReducido ) {
			destino.scrollIntoView();
			return;
		}

		desplazarA( destino.getBoundingClientRect().top + window.scrollY );
	} );
} )();
