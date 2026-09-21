/**
 * Desplazamiento animado al pulsar un enlace de ancla interno del menú
 * (incluido "Inicio", que enlaza a la portada sin hash) o cualquier otro
 * enlace href="#id" de la página, más lento y controlado que el salto
 * instantáneo por defecto del navegador.
 *
 * El enlace "saltar al contenido" (.salto-contenido) queda fuera a
 * propósito: por accesibilidad, ese salto debe seguir siendo instantáneo.
 *
 * @package Portafolio
 */
( function () {
	'use strict';

	var cabecera = document.querySelector( '.sitio-cabecera' );

	var DURACION_MINIMA_MS = 400;
	var DURACION_MAXIMA_MS = 900;

	var prefiereMovimientoReducido = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function suavizarProgreso( progreso ) {
		// easeInOutCubic: arranque y frenada más marcados que un ease
		// cuadrático (el que había antes), para que el desplazamiento se
		// sienta deliberado en vez de apenas perceptible.
		return progreso < 0.5
			? 4 * progreso * progreso * progreso
			: 1 - Math.pow( -2 * progreso + 2, 3 ) / 2;
	}

	// Duración según la distancia a recorrer (raíz cuadrada, no lineal, para
	// que un salto de punta a punta de la página no se dispare de más): un
	// salto corto entre secciones vecinas se siente ágil, uno largo se
	// siente con más recorrido, y ninguno baja de 400ms ni pasa de 900ms.
	function calcularDuracion( distancia ) {
		var duracion = 300 + Math.sqrt( Math.abs( distancia ) ) * 12;

		return Math.min( DURACION_MAXIMA_MS, Math.max( DURACION_MINIMA_MS, duracion ) );
	}

	function desplazarA( destinoY ) {
		var inicioY = window.scrollY;
		var distancia = destinoY - inicioY;
		var duracion = calcularDuracion( distancia );
		var inicioTiempo = null;

		function paso( marcaTiempo ) {
			if ( null === inicioTiempo ) {
				inicioTiempo = marcaTiempo;
			}

			var progreso = Math.min( ( marcaTiempo - inicioTiempo ) / duracion, 1 );

			window.scrollTo( 0, inicioY + distancia * suavizarProgreso( progreso ) );

			if ( progreso < 1 ) {
				window.requestAnimationFrame( paso );
			}
		}

		window.requestAnimationFrame( paso );
	}

	document.addEventListener( 'click', function ( evento ) {
		var enlace = evento.target.closest( '.sitio-navegacion a, a[href^="#"]' );

		if ( ! enlace || enlace.classList.contains( 'salto-contenido' ) ) {
			return;
		}

		var url = new URL( enlace.getAttribute( 'href' ), window.location.href );

		if ( url.pathname !== window.location.pathname || url.search !== window.location.search ) {
			return; // Enlace a otra página: que el navegador navegue normal.
		}

		var destino = url.hash ? document.getElementById( url.hash.slice( 1 ) ) : null;

		if ( url.hash && ! destino ) {
			return; // Hash sin sección correspondiente: no interceptar.
		}

		// Altura de la cabecera sticky: la misma que scroll-margin-top usa
		// en CSS (--cabecera-alto, ver style.css), para que el destino no
		// quede tapado a medias. Sin destino (p. ej. "Inicio"): volver
		// arriba del todo, que ya coincide con esta misma cuenta dando 0.
		var destinoY = destino
			? destino.getBoundingClientRect().top + window.scrollY - ( cabecera ? cabecera.offsetHeight : 0 )
			: 0;

		evento.preventDefault();

		if ( window.history && window.history.pushState ) {
			window.history.pushState( null, '', enlace.getAttribute( 'href' ) );
		}

		if ( prefiereMovimientoReducido ) {
			window.scrollTo( 0, destinoY );
			return;
		}

		desplazarA( destinoY );
	} );
} )();
