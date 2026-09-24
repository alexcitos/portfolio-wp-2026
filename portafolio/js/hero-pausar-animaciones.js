/**
 * Pausa las animaciones infinitas de fondo del hero (.hero-forma--N)
 * después de unos segundos. El movimiento inicial da la impresión de
 * "vida" al cargar la página; mantenerlo para siempre solo consume CPU
 * sin aportar nada perceptible, y evita que el navegador llegue a un
 * estado de reposo (relevante para mediciones de rendimiento como LCP).
 * Respeta prefers-reduced-motion: si ya está activo, no hace falta nada,
 * las animaciones ni siquiera se están ejecutando.
 */
( function () {
	'use strict';

	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	window.setTimeout( function () {
		document.body.classList.add( 'animaciones-hero-pausadas' );
	}, 8000 );
} )();
