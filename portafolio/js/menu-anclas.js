/**
 * Base del menú de anclas: fija la altura real de la cabecera en la
 * variable CSS --cabecera-alto (usada por scroll-margin-top, ver
 * style.css) y resalta en el menú qué sección está visible mientras se
 * hace scroll (scrollspy).
 *
 * @package Portafolio
 */
( function () {
	'use strict';

	var cabecera = document.querySelector( '.sitio-cabecera' );

	if ( ! cabecera ) {
		return;
	}

	// --cabecera-alto: se recalcula en cada resize porque la altura de la
	// cabecera cambia con el ancho (el título y el botón hamburguesa se
	// reacomodan; ver .sitio-cabecera-interior en style.css).
	function actualizarAlturaCabecera() {
		document.documentElement.style.setProperty( '--cabecera-alto', cabecera.offsetHeight + 'px' );
	}

	actualizarAlturaCabecera();
	window.addEventListener( 'resize', actualizarAlturaCabecera );

	// Scrollspy: opcional, requiere soporte de IntersectionObserver.
	//
	// Las secciones a vigilar se leen directamente del DOM, no de los
	// enlaces del menú: WordPress marca el enlace "Inicio" con la URL de la
	// portada (p. ej. "http://sitio/"), no con un hash "#inicio" —es así
	// como resuelve un enlace personalizado a la página de inicio—, así que
	// nunca podríamos descubrir esa primera sección buscando "a[href^='#']".
	var secciones = document.querySelectorAll( '.portada-oscura > section[id]' );

	if ( ! secciones.length || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	// A la inversa: para saber qué enlace resaltar por cada sección, cada
	// enlace del menú se resuelve contra la URL actual. Un enlace con hash
	// apunta directo a su sección; uno sin hash que además coincide con la
	// página actual (como "Inicio") se entiende como "arriba del todo", es
	// decir, la primera sección. Un enlace a otra página distinta (si el
	// menú llega a crecer más adelante) se descarta: no participa del
	// scrollspy.
	var enlaces = document.querySelectorAll( '.sitio-navegacion a[href]' );
	var enlacePorId = {};
	var idPrimeraSeccion = secciones[ 0 ].id;

	enlaces.forEach( function ( enlace ) {
		var url = new URL( enlace.getAttribute( 'href' ), window.location.href );

		if ( url.pathname !== window.location.pathname || url.search !== window.location.search ) {
			return;
		}

		var id = url.hash ? url.hash.slice( 1 ) : idPrimeraSeccion;

		enlacePorId[ id ] = enlace;
	} );

	function marcarActivo( id ) {
		var enlaceActivo = enlacePorId[ id ];

		enlaces.forEach( function ( enlace ) {
			var esActivo = enlace === enlaceActivo;

			enlace.classList.toggle( 'menu-ancla-activo', esActivo );

			if ( esActivo ) {
				enlace.setAttribute( 'aria-current', 'true' );
			} else {
				enlace.removeAttribute( 'aria-current' );
			}
		} );
	}

	// Franja de detección: empieza justo debajo de la cabecera (para no
	// activar una sección que todavía está tapada) y termina al 60% del
	// alto de la ventana, así la sección "activa" es la que ocupa la
	// franja superior visible, no cualquiera que solo asome por abajo.
	var observador = new IntersectionObserver(
		function ( entradas ) {
			var visibles = entradas
				.filter( function ( entrada ) {
					return entrada.isIntersecting;
				} )
				.sort( function ( a, b ) {
					return a.boundingClientRect.top - b.boundingClientRect.top;
				} );

			if ( visibles.length ) {
				marcarActivo( visibles[ 0 ].target.id );
			}
		},
		{
			rootMargin: '-' + cabecera.offsetHeight + 'px 0px -60% 0px',
			threshold: 0,
		}
	);

	secciones.forEach( function ( seccion ) {
		observador.observe( seccion );
	} );
} )();
