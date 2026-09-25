/**
 * Control de las animaciones infinitas "de ambiente" del sitio: esferas del
 * fondo fijo (template-parts/fondo-esferas.php), capas eco del hero, iconos
 * de los pilares y anillo de la foto de "Sobre mí".
 *
 * En CSS todas arrancan pausadas y solo corren mientras <html> tiene la
 * clase "ambiente-activo" (ver html:not(.ambiente-activo) en style.css).
 * Este script:
 * - no la pone hasta que la página terminó de cargar y el navegador quedó
 *   libre (evento load + requestIdleCallback), para que la primera pintura
 *   y la ventana en la que se mide el LCP ocurran sin nada animándose;
 * - la mantiene mientras hay interacción (scroll, puntero, teclado, toque)
 *   y la quita tras INACTIVIDAD_MS sin ninguna: el navegador llega a reposo
 *   cuando nadie usa la página, en vez de animar sin fin;
 * - la vuelve a poner con la siguiente interacción (no es un apagado
 *   definitivo);
 * - la quita en cuanto la pestaña se oculta.
 *
 * No usa IntersectionObserver (a diferencia de js/contador-tokens.js): el
 * fondo es una capa fija del tamaño de la ventana, siempre "visible", así
 * que no habría nada que observar.
 *
 * Respeta prefers-reduced-motion: si está activo, no hace nada (el CSS ya
 * desactiva esas animaciones con animation:none).
 *
 * @package Portafolio
 */
( function () {
	'use strict';

	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	var raiz = document.documentElement;
	var CLASE_ACTIVO = 'ambiente-activo';
	var INACTIVIDAD_MS = 8000;

	var listo = false;
	var activo = false;
	var ultimaActividad = 0;
	var temporizador = null;

	function pausar() {
		window.clearTimeout( temporizador );
		temporizador = null;
		activo = false;
		raiz.classList.remove( CLASE_ACTIVO );
	}

	// Un solo temporizador que, al vencer, mira cuánto hace de la última
	// interacción: si hubo alguna mientras tanto, se reprograma por el tiempo
	// que falta. Así los eventos frecuentes (scroll, pointermove) solo
	// guardan una marca de tiempo, sin crear y cancelar un temporizador en
	// cada uno.
	function revisarInactividad() {
		var restante = INACTIVIDAD_MS - ( Date.now() - ultimaActividad );

		if ( restante > 0 ) {
			temporizador = window.setTimeout( revisarInactividad, restante );
			return;
		}

		pausar();
	}

	function registrarActividad() {
		ultimaActividad = Date.now();

		if ( ! listo || activo || document.hidden ) {
			return;
		}

		activo = true;
		raiz.classList.add( CLASE_ACTIVO );
		temporizador = window.setTimeout( revisarInactividad, INACTIVIDAD_MS );
	}

	[ 'scroll', 'pointermove', 'pointerdown', 'keydown', 'touchstart' ].forEach( function ( tipo ) {
		window.addEventListener( tipo, registrarActividad, { passive: true } );
	} );

	document.addEventListener( 'visibilitychange', function () {
		if ( document.hidden ) {
			pausar();
		}
	} );

	// Primer arranque: tras la carga completa y con el navegador libre. Sin
	// requestIdleCallback (Safari), un margen corto tras el load.
	function arrancar() {
		listo = true;
		registrarActividad();
	}

	function programarArranque() {
		if ( 'requestIdleCallback' in window ) {
			window.requestIdleCallback( arrancar, { timeout: 2000 } );
		} else {
			window.setTimeout( arrancar, 200 );
		}
	}

	if ( 'complete' === document.readyState ) {
		programarArranque();
	} else {
		window.addEventListener( 'load', programarArranque, { once: true } );
	}
} )();
