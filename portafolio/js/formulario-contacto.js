/**
 * Envío por AJAX del formulario de contacto de la portada.
 *
 * Intercepta el submit, hace fetch() contra admin-ajax.php (endpoint
 * "portafolio_enviar_contacto" registrado en functions.php) y muestra el
 * resultado sin recargar la página: un mensaje general en el panel
 * (aria-live="polite", lo anuncia el lector de pantalla solo) y, si el
 * servidor devuelve errores de validación, el error específico junto a
 * cada campo.
 */
( function () {
	'use strict';

	var formulario = document.querySelector( '.formulario-contacto' );

	if ( ! formulario ) {
		return;
	}

	var mensaje = formulario.querySelector( '.formulario-mensaje' );
	var boton = formulario.querySelector( 'button[type="submit"]' );
	var textoBotonOriginal = boton.textContent;

	/**
	 * Quita errores previos (de un envío anterior) antes de procesar uno
	 * nuevo: campos marcados como inválidos y sus textos de error.
	 */
	function limpiarErroresCampos() {
		var spans = formulario.querySelectorAll( '.formulario-campo-error' );
		for ( var i = 0; i < spans.length; i++ ) {
			spans[ i ].textContent = '';
		}

		var invalidos = formulario.querySelectorAll( '[aria-invalid="true"]' );
		for ( var j = 0; j < invalidos.length; j++ ) {
			invalidos[ j ].removeAttribute( 'aria-invalid' );
		}
	}

	/**
	 * Escribe el mensaje general en la región aria-live y le aplica el
	 * color semántico (éxito/error). Vacío + sin clase = oculto (ver
	 * .formulario-mensaje:empty en style.css).
	 *
	 * @param {string} texto
	 * @param {string|null} tipo "exito", "error" o null.
	 */
	function mostrarMensaje( texto, tipo ) {
		mensaje.textContent = texto;
		mensaje.classList.remove( 'formulario-mensaje--exito', 'formulario-mensaje--error' );

		if ( tipo ) {
			mensaje.classList.add( 'formulario-mensaje--' + tipo );
		}
	}

	/**
	 * Muestra el error de cada campo devuelto por el servidor junto a su
	 * input (no un mensaje genérico), y lo marca aria-invalid para quien
	 * usa lector de pantalla.
	 *
	 * @param {Object} errores Mapa "name del campo" -> mensaje de error.
	 */
	function mostrarErroresCampos( errores ) {
		Object.keys( errores ).forEach( function ( nombreCampo ) {
			var campo = formulario.querySelector( '[name="' + nombreCampo + '"]' );

			if ( ! campo ) {
				return;
			}

			campo.setAttribute( 'aria-invalid', 'true' );

			var idError = campo.getAttribute( 'aria-describedby' );
			var span = idError ? document.getElementById( idError ) : null;

			if ( span ) {
				span.textContent = errores[ nombreCampo ];
			}
		} );
	}

	formulario.addEventListener( 'submit', function ( evento ) {
		evento.preventDefault();

		limpiarErroresCampos();
		mostrarMensaje( '', null );

		boton.disabled = true;
		boton.textContent = 'Enviando…';

		fetch( formulario.action, {
			method: 'POST',
			body: new FormData( formulario ),
			credentials: 'same-origin'
		} )
			.then( function ( respuesta ) {
				return respuesta.json();
			} )
			.then( function ( respuesta ) {
				var datos = respuesta.data || {};

				if ( respuesta.success ) {
					mostrarMensaje( datos.mensaje, 'exito' );
					formulario.reset();
					return;
				}

				if ( datos.errores ) {
					mostrarErroresCampos( datos.errores );
					mostrarMensaje( 'Revisa los campos señalados.', 'error' );
					return;
				}

				mostrarMensaje( datos.mensaje || 'No se pudo enviar el mensaje. Inténtalo de nuevo.', 'error' );
			} )
			.catch( function () {
				mostrarMensaje( 'No se pudo enviar el mensaje. Revisa tu conexión e inténtalo de nuevo.', 'error' );
			} )
			.finally( function () {
				boton.disabled = false;
				boton.textContent = textoBotonOriginal;
			} );
	} );
} )();
