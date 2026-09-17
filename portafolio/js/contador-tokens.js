/**
 * Animación de conteo ascendente del número de tokens en el pie de página
 * (ver .sitio-pie-tokens en footer.php). Se dispara una sola vez, cuando el
 * pie entra en el viewport, y respeta prefers-reduced-motion mostrando el
 * número final directo sin animar.
 *
 * @package Portafolio
 */
( function () {
	'use strict';

	var pie = document.querySelector( '.sitio-pie' );
	var elemento = pie ? pie.querySelector( '.sitio-pie-tokens' ) : null;

	if ( ! elemento ) {
		return;
	}

	// Texto final tal cual lo pintó footer.php (número formateado, o el
	// "Texto alternativo" cuando el contador está desactivado): se restaura
	// exacto al terminar la animación, así el formato/sufijo nunca cambia.
	var textoFinal = elemento.textContent;
	var coincidencia = textoFinal.match( /\d[\d.,]*\d|\d/ );

	var prefiereMenosMovimiento = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	// Sin parte numérica real (p. ej. "muchísimos") o con animaciones
	// desactivadas: el texto ya está puesto por PHP, no hay nada que hacer.
	if ( ! coincidencia || prefiereMenosMovimiento ) {
		return;
	}

	var numeroTexto = coincidencia[0];
	var prefijo = textoFinal.slice( 0, coincidencia.index );
	var sufijo = textoFinal.slice( coincidencia.index + numeroTexto.length );

	// "2.300.000" / "2,300,000": grupos de miles (separador seguido siempre
	// de grupos de 3 dígitos). Cualquier otro número con un solo separador
	// y pocos decimales ("2.3", "2,3") se trata como coma/punto decimal.
	var esMiles = /^\d{1,3}([.,]\d{3})+$/.test( numeroTexto );
	var coincidenciaDecimal = ! esMiles && numeroTexto.match( /^(\d+)[.,](\d+)$/ );

	var valorFinal;
	var decimales = 0;

	if ( esMiles ) {
		valorFinal = parseInt( numeroTexto.replace( /[.,]/g, '' ), 10 );
	} else if ( coincidenciaDecimal ) {
		decimales = coincidenciaDecimal[2].length;
		valorFinal = parseFloat( coincidenciaDecimal[1] + '.' + coincidenciaDecimal[2] );
	} else {
		valorFinal = parseInt( numeroTexto.replace( /\D/g, '' ), 10 );
	}

	if ( ! isFinite( valorFinal ) ) {
		return;
	}

	/**
	 * Formatea un valor intermedio de la animación con el mismo
	 * prefijo/sufijo que el texto final (p. ej. "M" en "2.3M", o nada).
	 */
	function formatearIntermedio( valor ) {
		var texto = decimales > 0
			? valor.toFixed( decimales )
			: Math.round( valor ).toLocaleString( 'es-ES' );

		return prefijo + texto + sufijo;
	}

	// Desaceleración hacia el final (ease-out cúbico) en vez de lineal.
	function easeOutCubic( progreso ) {
		return 1 - Math.pow( 1 - progreso, 3 );
	}

	var duracionMs = 2000;
	var inicio = null;

	function animar( marcaTiempo ) {
		if ( null === inicio ) {
			inicio = marcaTiempo;
		}

		var progreso = Math.min( ( marcaTiempo - inicio ) / duracionMs, 1 );

		elemento.textContent = formatearIntermedio( valorFinal * easeOutCubic( progreso ) );

		if ( progreso < 1 ) {
			window.requestAnimationFrame( animar );
		} else {
			// Último frame: se pisa con el texto original para no arrastrar
			// ninguna diferencia de redondeo frente al valor real.
			elemento.textContent = textoFinal;
		}
	}

	var observador = new IntersectionObserver( function ( entradas, obs ) {
		entradas.forEach( function ( entrada ) {
			if ( ! entrada.isIntersecting ) {
				return;
			}

			elemento.textContent = formatearIntermedio( 0 );
			window.requestAnimationFrame( animar );
			obs.unobserve( entrada.target );
		} );
	} );

	observador.observe( pie );
} )();
