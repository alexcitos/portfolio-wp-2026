/**
 * Aviso de cookies (consentimiento para Google Analytics).
 *
 * Modo "básico" de Consent Mode v2: Google Analytics NO se carga hasta que
 * el visitante pulsa "Aceptar". La decisión se guarda en localStorage
 * (clave alexrope_consentimiento: "aceptado" o "rechazado"), no en una
 * cookie. El snippet del <head> (portafolio_google_analytics() en
 * functions.php) define window.portafolioCargarAnalitica() y la llama solo
 * si ya había un "aceptado" guardado; en local esa función no existe, así
 * que el aviso se puede probar sin enviar nada a Google.
 *
 * "Aceptar" y "Rechazar" tienen el mismo peso visual (exigencia del RGPD).
 * Cualquier elemento con [data-abrir-cookies] (enlace "Preferencias de
 * cookies" del pie) vuelve a mostrar el aviso para cambiar la decisión.
 */
( function () {
	'use strict';

	var CLAVE = 'alexrope_consentimiento';
	var aviso = document.getElementById( 'aviso-cookies' );

	if ( ! aviso ) {
		return;
	}

	function leerDecision() {
		try {
			return window.localStorage.getItem( CLAVE );
		} catch ( error ) {
			return null;
		}
	}

	function guardarDecision( valor ) {
		try {
			window.localStorage.setItem( CLAVE, valor );
		} catch ( error ) {
			// Almacenamiento bloqueado (modo privado estricto): la decisión
			// vale solo para esta visita.
		}
	}

	/**
	 * Borra las cookies de Google Analytics (_ga, _ga_<ID>) del dominio
	 * actual y de su dominio padre, por si el visitante las aceptó antes.
	 */
	function borrarCookiesAnalitica() {
		var host = window.location.hostname;
		var dominios = [ '', host, '.' + host.replace( /^www\./, '' ) ];

		document.cookie.split( ';' ).forEach( function ( par ) {
			var nombre = par.split( '=' )[ 0 ].trim();

			if ( 0 !== nombre.indexOf( '_ga' ) ) {
				return;
			}

			dominios.forEach( function ( dominio ) {
				document.cookie = nombre + '=; Max-Age=0; path=/' + ( dominio ? '; domain=' + dominio : '' );
			} );
		} );
	}

	function mostrarAviso() {
		aviso.hidden = false;
	}

	function ocultarAviso() {
		aviso.hidden = true;
	}

	function aplicar( valor ) {
		guardarDecision( valor );
		ocultarAviso();

		if ( 'aceptado' === valor ) {
			if ( 'function' === typeof window.portafolioCargarAnalitica ) {
				window.portafolioCargarAnalitica();
			}
			return;
		}

		if ( 'function' === typeof window.gtag ) {
			window.gtag( 'consent', 'update', { analytics_storage: 'denied' } );
		}
		borrarCookiesAnalitica();
	}

	aviso.querySelectorAll( '[data-consentimiento]' ).forEach( function ( boton ) {
		boton.addEventListener( 'click', function () {
			aplicar( boton.getAttribute( 'data-consentimiento' ) );
		} );
	} );

	document.querySelectorAll( '[data-abrir-cookies]' ).forEach( function ( enlace ) {
		enlace.addEventListener( 'click', function ( evento ) {
			evento.preventDefault();
			mostrarAviso();
			var primerBoton = aviso.querySelector( '[data-consentimiento]' );
			if ( primerBoton ) {
				primerBoton.focus();
			}
		} );
	} );

	if ( ! leerDecision() ) {
		mostrarAviso();
	}
} )();
