/**
 * Envío por AJAX del formulario de contacto de la portada.
 *
 * Valida en vivo (obligatoriedad + formato de email) antes de enviar nada,
 * intercepta el submit y hace fetch() contra admin-ajax.php (endpoint
 * "portafolio_enviar_contacto" registrado en functions.php) sin recargar
 * la página. El servidor vuelve a validar todo (ver
 * portafolio_ajax_enviar_contacto() en functions.php): esta validación en
 * el navegador es solo para dar el error al instante, no reemplaza esa
 * comprobación.
 *
 * Dos canales de mensaje separados:
 * - .formulario-mensaje (aria-live="polite"): éxito o error general del
 *   envío (fallo de red, sesión caducada). Nunca errores de un campo.
 * - .formulario-campo-error, uno por campo: el error específico de ESE
 *   campo, asociado por aria-describedby y con aria-invalid="true"
 *   mientras el error siga activo.
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
	var campos = formulario.querySelectorAll( '.formulario-campo input, .formulario-campo textarea' );

	/**
	 * Mensaje de validación de un campo, o cadena vacía si es válido.
	 * Reutiliza la Constraint Validation API nativa del navegador
	 * (campo.validity) solo para DETECTAR el problema —required/type=email—,
	 * pero el texto que se muestra es siempre el nuestro, nunca el globo
	 * nativo del navegador (el <form novalidate> de front-page.php lo
	 * desactiva).
	 *
	 * @param {HTMLInputElement|HTMLTextAreaElement} campo
	 * @return {string}
	 */
	function obtenerErrorCampo( campo ) {
		if ( '' === campo.value.trim() ) {
			return 'Este campo es obligatorio.';
		}

		if ( ! campo.checkValidity() ) {
			return 'Ingresa un email válido.';
		}

		return '';
	}

	/**
	 * Marca un campo como inválido: aria-invalid (borde de alerta vía CSS)
	 * + el texto en su span asociado por aria-describedby.
	 *
	 * @param {HTMLInputElement|HTMLTextAreaElement} campo
	 * @param {string} texto
	 */
	function mostrarErrorCampo( campo, texto ) {
		campo.setAttribute( 'aria-invalid', 'true' );

		var idError = campo.getAttribute( 'aria-describedby' );
		var span = idError ? document.getElementById( idError ) : null;

		if ( span ) {
			span.textContent = texto;
		}
	}

	/**
	 * Quita el error de un campo (borde y texto), típicamente porque el
	 * usuario ya lo corrigió.
	 *
	 * @param {HTMLInputElement|HTMLTextAreaElement} campo
	 */
	function limpiarErrorCampo( campo ) {
		campo.removeAttribute( 'aria-invalid' );

		var idError = campo.getAttribute( 'aria-describedby' );
		var span = idError ? document.getElementById( idError ) : null;

		if ( span ) {
			span.textContent = '';
		}
	}

	function limpiarTodosLosErrores() {
		campos.forEach( limpiarErrorCampo );
	}

	/**
	 * Valida todos los campos en el navegador antes de enviar nada.
	 *
	 * @return {HTMLInputElement|HTMLTextAreaElement|null} El primer campo
	 *   inválido (para moverle el foco), o null si todos son válidos.
	 */
	function validarFormulario() {
		var primerCampoInvalido = null;

		campos.forEach( function ( campo ) {
			var error = obtenerErrorCampo( campo );

			if ( error ) {
				mostrarErrorCampo( campo, error );

				if ( ! primerCampoInvalido ) {
					primerCampoInvalido = campo;
				}
			} else {
				limpiarErrorCampo( campo );
			}
		} );

		return primerCampoInvalido;
	}

	// Al salir de un campo (blur): se valida entero, muestre o quite el
	// error según corresponda.
	//
	// Mientras se escribe (input): solo se QUITA el error si el campo ya
	// quedó válido —no se muestran errores nuevos en cada tecla, sería
	// molesto—, tal como pide la corrección: el borde/mensaje desaparecen
	// en cuanto el valor es válido, sin esperar a otro intento de envío.
	campos.forEach( function ( campo ) {
		campo.addEventListener( 'blur', function () {
			var error = obtenerErrorCampo( campo );

			if ( error ) {
				mostrarErrorCampo( campo, error );
			} else {
				limpiarErrorCampo( campo );
			}
		} );

		campo.addEventListener( 'input', function () {
			if ( 'true' === campo.getAttribute( 'aria-invalid' ) && ! obtenerErrorCampo( campo ) ) {
				limpiarErrorCampo( campo );
			}
		} );
	} );

	/**
	 * Escribe el mensaje GENERAL (éxito o error de envío/conexión) en la
	 * región aria-live. Nunca errores de un campo concreto —esos van con
	 * mostrarErrorCampo()—. Vacío + sin clase = oculto (ver
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

	formulario.addEventListener( 'submit', function ( evento ) {
		evento.preventDefault();

		mostrarMensaje( '', null );

		var primerCampoInvalido = validarFormulario();

		if ( primerCampoInvalido ) {
			primerCampoInvalido.focus();
			return;
		}

		limpiarTodosLosErrores();
		boton.disabled = true;
		boton.textContent = 'Enviando…';

		// portafolioContacto.urlAjax viene de wp_localize_script()
		// (functions.php): a propósito NO se usa formulario.action, que
		// aquí devolvería el <input type="hidden" name="action"> del
		// propio formulario en vez de la URL —un control con
		// name="action" tapa la propiedad nativa HTMLFormElement.action—.
		fetch( window.portafolioContacto.urlAjax, {
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
					var primerCampoConErrorServidor = null;

					Object.keys( datos.errores ).forEach( function ( nombreCampo ) {
						var campo = formulario.querySelector( '[name="' + nombreCampo + '"]' );

						if ( ! campo ) {
							return;
						}

						mostrarErrorCampo( campo, datos.errores[ nombreCampo ] );

						if ( ! primerCampoConErrorServidor ) {
							primerCampoConErrorServidor = campo;
						}
					} );

					if ( primerCampoConErrorServidor ) {
						primerCampoConErrorServidor.focus();
					}

					// Sin mensaje general aquí a propósito: esos errores ya
					// quedan junto a cada campo (ver comentario al inicio
					// del archivo).
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
