// Gestionamos el evento de submit del formulario
document.querySelector('#formularioInicioSesion').addEventListener('submit', gestionaInicioSesion);

// Gestionamos el evento de click del icono de mostrar contraseña
document.querySelector('#botonMuestraContrasenia').addEventListener('click', (e) => muestraOcultaInputsContrasenia(e));

// Gestionamos el evento de click del botón de la ventana modal
document.querySelector('#btnPrincipalVentanaModal').addEventListener('click', ocultaVentanaModalCompleta);

/**
 * Función que permite comprobar si los campos de inicio de sesión se han rellenado correctamente, y preguntar a servidor
 *      si existe un usuario con dichos credenciales.
 * @param {SubmitEvent} e Evento que se produce al hacer un submit en el formulario. 
 */
function gestionaInicioSesion(e){

    // Impedimos la acción por defecto del evento
    e.preventDefault();

    // Quitamos el mensaje de error que pudiera haber en pantalla
    document.querySelector('#mensajeError').textContent = '';

    // Deshabilitamos toda la página
    deshabilitaPaginaEntera();

    // Comprobamos todos los inputs del formulario antes de hacer nada
    if(compruebaInputsFormulario(e)){

        // Creamos un objeto para mandar todos los datos al servidor
        let formData = new FormData();

        // Metemos los datos a enviar en el objeto
        formData.append('nombreUsuario', e.target['inputNombreUsuario'].value.trim());
        formData.append('contrasenia', e.target['inputContraseniaUsuario'].value.trim())
        
        // Hacemos una petición asíncrona al servidor
        generaPeticionAjax('IniciarSesionCuenta', (data) => {
            console.log(data);

            // Si se recibe un mensaje de credenciales incorrectos, mostramos un mensaje de error al usuario.
            // Si se recibe un mensaje de que ha habido un error en la validación de datos, mostramos un mensaje de error al usuario.
            // Si se recibe un mensaje de sesión iniciada, redirigimos al usuario a la ruta recibida por parámetro.
            // Si se recibe cualquier otro mensaje, mostramos un mensaje de error
            if(data.mensaje == 'Credenciales incorrectos'){
                document.querySelector('#mensajeError').textContent = 'El nombre de usuario y/o contraseña son incorrectos.';

                // Habilitamos toda la página
                habilitaPaginaEntera();
            }else if(data.mensaje == 'Error en la validación de datos'){
                document.querySelector('#mensajeError').textContent = data.mensajeDetallado;

                // Habilitamos toda la página
                habilitaPaginaEntera();
            }else if(data.mensaje == 'Sesión iniciada'){
                window.location = data.ruta;
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
 * Función que permite comprobar si los inputs del formulario cumplen todas las restricciones impuestas.
 * @param {SubmitEvent} e Objeto que almacena todos los datos del formulario, incluidos los inputs.
 * @returns true si cumplen las restricciones, o false en caso contrario.
 */
function compruebaInputsFormulario(e){

    // Bandera que sirve para saber si se cumplen o no las restricciones
    let bandera = true;

    // Vamos comprobando las restricciones de los campos, poniendo un mensaje de error y cambiando el valor de la bandera
    //      si no se cumplen dichas restricciones
    if(e.target['inputNombreUsuario'].value == '' || e.target['inputContraseniaUsuario'].value == ''){

        // Campos vacios
        document.querySelector('#mensajeError').textContent = 'Por favor, rellene todos los datos para iniciar sesión.';
        bandera = false;
    }else if(e.target['inputNombreUsuario'].value.length > 20 || e.target['inputNombreUsuario'].value.length < 8){

        // Nombre de usuario con longitud incorrecta
        document.querySelector('#mensajeError').textContent = 'El nombre de usuario debe tener entre 8 y 20 caracteres.';
        bandera = false;
    }else if(e.target['inputContraseniaUsuario'].value.length > 20 || e.target['inputContraseniaUsuario'].value.length < 8){

        // Contraseña con longitud incorrecta
        document.querySelector('#mensajeError').textContent = 'La contraseña debe tener entre 8 y 20 caracteres.';
        bandera = false;
    }else if(!/^[\w]{8,20}$/.test(e.target['inputNombreUsuario'].value)){

        // El nombre de usuario no cumple el patrón indicado
        document.querySelector('#mensajeError').textContent = 'El nombre de usuario solo puede tener letras, números y guiones bajos.';
        bandera = false;
    }else if(!/^[A-Za-z\d]{8,20}$/.test(e.target['inputContraseniaUsuario'].value)){

        // La contraseña no cumple el patrón indicado
        document.querySelector('#mensajeError').textContent = 'La contraseña solo puede tener letras y números.';
        bandera = false
    }

    // Retornamos la bandera
    return bandera;
}