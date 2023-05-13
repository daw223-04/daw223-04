// Gestionamos el evento de click del icono de mostrar contraseña actual
document.querySelector('#muestraContraseniaActual').addEventListener('click', muestraOcultaContraseniaActual);

// Recorremos todos los iconos de mostrar contraseñas, y gestionamos sus eventos de click
document.querySelectorAll('.muestraInputContrasenia').forEach(el => {
    el.addEventListener('click', (e) => muestraOcultaInputsContrasenia(e, 'muestraInputContrasenia'));
});

// Gestionamos el evento de click del botón de 'Borrar cuenta'
document.querySelector('#botonBorrarCuentaUsuario').addEventListener('click', () => {

    // Deshabilitamos toda la página
    deshabilitaPaginaEntera();

    // Mostramos una ventana modal de confirmación de borrado
    muestraVentanaModalParrafoYDosBotones('¿Desea borrar la cuenta? Perderá todos los datos almacenados', 'No borrar', 'Borrar');
});

// Gestionamos el evento de click del botón principal de la ventana modal
document.querySelector('#btnPrincipalVentanaModal').addEventListener('click', ocultaVentanaModalCompleta);

// Gestionamos el evento de click del botón de 'No borrar' de la ventana modal
document.querySelector('#primerBotonArticuloDosBotonesVentanaModal').addEventListener('click', () => {

    // Ocultamos toda la ventana modal
    ocultaVentanaModalCompleta();

    // Habilitamos toda la página
    habilitaPaginaEntera();
});

// Gestionamos el evento de click del botón de 'Borrar' de la ventana modal
document.querySelector('#segundoBotonArticuloDosBotonesVentanaModal').addEventListener('click', borradoUsuario);

// Gestionamos el evento de submit del formulario de modificación de datos del usuario
document.querySelector('#articuloInputsModificacion').addEventListener('submit', gestionaSubidaDatosUsuario);

/**
 * Función que permite mostrar u ocultar la contraseña actual del usuario.
 * @param {MouseEvent} e Evento que se genera cuando se hace click sobre el icono de mostrar u ocultar contraseña
 */
function muestraOcultaContraseniaActual(e){

    // Comprobamos si el elemento que contiene la contraseña actual tiene algún estilo o no
    if(document.querySelector('#contraseniaActual').hasAttribute('style')){

        // Eliminamos los estilos de la contraseña actual y de la contraseña actual oculta
        document.querySelector('#contraseniaActual').removeAttribute('style');
        document.querySelector('#contraseniaActualOculta').removeAttribute('style');

        // Ponemos el icono de ojo tachado en el icono de mostrar u ocultar contraseña
        e.target.setAttribute('class', 'bi bi-eye-slash-fill');
    }else{

        // Mostramos la contraseña actual y ocultamos la oculta
        document.querySelector('#contraseniaActual').setAttribute('style', 'display: inline-block;');
        document.querySelector('#contraseniaActualOculta').setAttribute('style', 'display: none;');

        // Ponemos el icono de ojo normal en el icono de mostrar u ocultar contraseña
        e.target.setAttribute('class', 'bi bi-eye-fill');
    }
}

/**
 * Función que permite gestionar la subida de los datos modificados al servidor.
 * @param {SubmitEvent} e Evento que se genera al hacer submit en el formulario.
 */
function gestionaSubidaDatosUsuario(e){

    // Impedimos el evento por defecto
    e.preventDefault();

    // Quitamos el mensaje de error que pudiera haber en pantalla
    document.querySelector('#mensajeError').setAttribute('style', 'display: none;');
    document.querySelector('#mensajeError').textContent = '';

    // Deshabilitamos toda la página
    deshabilitaPaginaEntera();

    // Comprobamos todos los inputs del formulario antes de hacer nada
    if(compruebaInputsFormulario(e)){

        // Creamos un objeto para mandar todos los datos al servidor
        let formData = new FormData();

        // Guardamos los datos a enviar en el objeto
        formData.append('id', e.target['inputIdUsuario'].value);
        formData.append('nombre', e.target['inputNombreNuevo'].value.trim());
        formData.append('apellidos', e.target['inputApellidosNuevo'].value.trim());
        formData.append('nombreUsuario', e.target['inputNombreUsuarioNuevo'].value.trim());
        formData.append('contrasenia', e.target['inputContraseniaNuevo'].value.trim())

        // Hacemos una petición asíncrona al servidor
        generaPeticionAjax('ModificacionDatosCuenta', (data) => {
            console.log(data);

            // Si se recibe un mensaje de usuario repetido, mostramos un mensaje de error al usuario.
            // Si se recibe un mensaje de que ha habido un error en la validación de datos, mostramos un mensaje de error al usuario.
            // Si se recibe un mensaje de que la modificación se ha completado satisfactoriamente, mostramos una ventana modal
            //      al usuario avisando de dicha situación.
            // Si se recibe un mensaje de error de BD, mostramos una ventana modal al usuario indicándole de dicha situación
            // Si se recibe cualquier otro mensaje, mostramos una ventana modal al usuario indicándole dicha situación
            if(data.mensaje == 'Nombre de usuario repetido'){
                document.querySelector('#mensajeError').textContent = 'El nombre de usuario introducido ya existe. Elija otro.';
                document.querySelector('#mensajeError').setAttribute('style', 'display: block;');

                // Habilitamos toda la página
                habilitaPaginaEntera();
            }else if(data.mensaje == 'Error en la validación de datos'){
                document.querySelector('#mensajeError').textContent = data.mensajeDetallado;
                document.querySelector('#mensajeError').setAttribute('style', 'display: block;');

                // Habilitamos toda la página
                habilitaPaginaEntera();
            }else if(data.mensaje == 'Completado'){

                // Mostramos la ventana modal con el aviso de que se ha modificado la cuenta
                muestraVentanaModalParrafoYBoton('Cuenta modificada correctamente.', 'Volver');

                // Ponemos los nuevos datos del usuario en los textos reservados para ello
                document.querySelector('#nombreActual').textContent = e.target['inputNombreNuevo'].value.trim();
                document.querySelector('#apellidosActual').textContent = e.target['inputApellidosNuevo'].value.trim();
                document.querySelector('#nombreUsuarioActual').textContent = e.target['inputNombreUsuarioNuevo'].value.trim();
                document.querySelector('#contraseniaActual').textContent = e.target['inputContraseniaNuevo'].value.trim();

                // Ponemos el nuevo nombre de usuario en el cuadro de inicio de sesión
                document.querySelector('#textoCuadroIniciaSesion').textContent = 'Sesión iniciada como ' + e.target['inputNombreUsuarioNuevo'].value;

                // Ocultamos el mensaje de error del cuadro de modificación
                document.querySelector('#mensajeError').removeAttribute('style');

                // Ocultamos la contraseña actual, si es que esta visible
                if(document.querySelector('#muestraContraseniaActual').getAttribute('class') != 'bi bi-eye-slash-fill'){
                    document.querySelector('#muestraContraseniaActual').click();
                }

                // Ocultamos los campos de contraseñas del cuadro de modificación, si es que están visibles
                document.querySelectorAll('.muestraInputContrasenia').forEach(el => {
                    if(el.getAttribute('class') != 'bi bi-eye-slash-fill muestraInputContrasenia'){
                        el.click();
                    }
                });
            }else if(data.mensaje == 'Error en BD'){
                muestraVentanaModalError('al modificar los datos del usuario.');
            }else{
                muestraVentanaModalError('al conectar con el servidor.');
            }
        }, formData);
    }else{

        // Habilitamos toda la página
        habilitaPaginaEntera();
    }
}

/**
 * Función que permite borrar la cuenta del usuario.
 */
function borradoUsuario(){

    // Deshabilitamos toda la página
    deshabilitaPaginaEntera();

    // Creamos un objeto para mandar todos los datos al servidor
    let formData = new FormData();

    // Guardamos los datos a enviar en el objeto
    formData.append('id', document.querySelector('#inputIdUsuario').value);

    // Hacemos una petición asíncrona al servidor
    generaPeticionAjax('BorradoCuenta', (data) => {
        console.log(data);

        // Si se recibe un mensaje de que la modificación se ha completado satisfactoriamente, mostramos una ventana modal
        //      al usuario avisando de dicha situación
        // Si se recibe un mensaje de error de BD, mostramos una ventana modal al usuario indicándole de dicha situación
        if(data.mensaje == 'Completado'){
            // Cambiamos el evento del botón de la ventana modal
            document.querySelector('#btnPrincipalVentanaModal').removeEventListener('click', ocultaVentanaModalCompleta);
            document.querySelector('#btnPrincipalVentanaModal').addEventListener('click', () => redigirePaginaUsuario(data));

            // Mostramos la ventana modal con el aviso de que se ha borrado la cuenta
            muestraVentanaModalParrafoYBoton('Cuenta borrada correctamente.', 'Volver');
        }else if(data.mensaje == 'Error de BD'){
            muestraVentanaModalError('al borrar la cuenta.');
        }else{
            muestraVentanaModalError('al conectar con el servidor.');
        }
    }, formData);
}

/**
 * Función que permite comprobar si los inputs del formulario cumplen todas las restricciones impuestas.
 * @param {SubmitEvent} e Objeto que almacena todos los datos del formulario, incluidos los inputs.
 * @returns true si cumplen las restricciones, o false en caso contrario.
 */
function compruebaInputsFormulario(e){

    // Bandera que sirve para saber si se cumplen o no las restricciones
    let bandera = true;

    // Vamos comprobando las restricciones de los campos, poniendo un mensaje de error y cambiando el valor de la bandera
    //      si no se cumplen dichas restricciones
    if(e.target['inputNombreNuevo'].value == document.querySelector('#nombreActual').textContent && 
        e.target['inputApellidosNuevo'].value == document.querySelector('#apellidosActual').textContent &&
        e.target['inputNombreUsuarioNuevo'].value == document.querySelector('#nombreUsuarioActual').textContent &&
        e.target['inputContraseniaNuevo'].value == document.querySelector('#contraseniaActual').textContent && 
        e.target['inputRepiteContraseniaNuevo'].value == document.querySelector('#contraseniaActual').textContent){

        // Los campos del formulario tienen los mismos valores que los campos sin modificar
        document.querySelector('#mensajeError').textContent = 'Por favor, cambie algún dato para poder hacer la modificación.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(e.target['inputNombreNuevo'].value == '' || e.target['inputApellidosNuevo'].value == '' || 
        e.target['inputNombreUsuarioNuevo'].value == '' || e.target['inputContraseniaNuevo'].value == '' || 
        e.target['inputRepiteContraseniaNuevo'].value == ''){

        // Campos vacios
        document.querySelector('#mensajeError').textContent = 'Por favor, rellene todos los datos para modificar su cuenta.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(e.target['inputNombreNuevo'].value.length > 30){

        // Nombre con longitud incorrecta
        document.querySelector('#mensajeError').textContent = 'El nombre no puede exceder de 30 caracteres.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(e.target['inputApellidosNuevo'].value.length > 50){

        // Apellidos con longitud incorrecta
        document.querySelector('#mensajeError').textContent = 'Los apellidos no puede exceder de 50 caracteres.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(e.target['inputNombreUsuarioNuevo'].value.length > 20 || e.target['inputNombreUsuarioNuevo'].value.length < 8){

        // Nombre de usuario con longitud incorrecta
        document.querySelector('#mensajeError').textContent = 'El nombre de usuario debe tener entre 8 y 20 caracteres.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(e.target['inputContraseniaNuevo'].value.length > 20 || e.target['inputContraseniaNuevo'].value.length < 8){

        // Contraseña con longitud incorrecta
        document.querySelector('#mensajeError').textContent = 'La contraseña debe tener entre 8 y 20 caracteres.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(e.target['inputContraseniaNuevo'].value != e.target['inputRepiteContraseniaNuevo'].value){

        // Las 2 contraseñas no son iguales
        document.querySelector('#mensajeError').textContent = 'Las contraseñas introducidas no son iguales.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(!/^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)?$/.test(e.target['inputNombreNuevo'].value)){

        // El nombre no cumple el patrón indicado
        document.querySelector('#mensajeError').textContent = 'El nombre no posee un formato de caracteres adecuado.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(!/^(de\s(la\s)?|del\s)?[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+$/.test(e.target['inputApellidosNuevo'].value)){

        // Los apellidos no cumplen el patrón indicado
        document.querySelector('#mensajeError').textContent = 'Los apellidos no poseen un formato de caracteres adecuado.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(!/^[\w]{8,20}$/.test(e.target['inputNombreUsuarioNuevo'].value)){

        // El nombre de usuario no cumple el patrón indicado
        document.querySelector('#mensajeError').textContent = 'El nombre de usuario solo puede tener letras, números y guiones bajos.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(!/^[A-Za-z\d]{8,20}$/.test(e.target['inputContraseniaNuevo'].value)){

        // La contraseña no cumple el patrón indicado
        document.querySelector('#mensajeError').textContent = 'La contraseña solo puede tener letras y números.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(!/^[A-Za-z\d]{8,20}$/.test(e.target['inputRepiteContraseniaNuevo'].value)){

        // La repetición de la contraseña no cumple el patrón indicado
        document.querySelector('#mensajeError').textContent = 'La repetición de la contraseña solo puede tener letras y números.';
        document.querySelector('#mensajeError').setAttribute('style', 'display: block;');
        bandera = false;
    }

    // Retornamos la bandera
    return bandera;
}