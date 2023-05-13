// Gestionamos el evento de submit del formulario
document.querySelector('#formularioCreaCuenta').addEventListener('submit', gestionaSubmitFormulario);

// Recorremos todos los iconos de mostrar contraseñas, y gestionamos sus eventos de click
document.querySelectorAll('.botonMuestraContrasenia').forEach(el => {
    el.addEventListener('click', (e) => muestraOcultaInputsContrasenia(e, 'botonMuestraContrasenia'));
});

// Gestionamos el evento de click del botón de la ventana modal
document.querySelector('#btnPrincipalVentanaModal').addEventListener('click', ocultaVentanaModalCompleta);

/**
 * Función que permite comprobar los datos indicados por el usuario, y subirlos al servidor para crear la cuenta deseada.
 * @param {SubmitEvent} e Evento generado al hacer submit en el formulario.
 */
function gestionaSubmitFormulario(e){

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
        formData.append('nombre', e.target['inputNombre'].value.trim());
        formData.append('apellidos', e.target['inputApellidos'].value.trim());
        formData.append('nombreUsuario', e.target['inputNombreUsuario'].value.trim());
        formData.append('contrasenia', e.target['inputContrasenia'].value.trim())

        // Hacemos una petición asíncrona al servidor
        generaPeticionAjax('CreacionNuevaCuenta', (data) => {
            console.log(data);
            
            // Si se recibe un mensaje de usuario repetido, mostramos un mensaje de error al usuario
            // Si se recibe un mensaje de que ha habido un error en la validación de datos, mostramos un mensaje de error al usuario.
            // Si se recibe un mensaje de cuenta creada, mostramos una ventana modal al usuario, indicando que la creación de
            //      cuenta se ha completado
            // Si se recibe un mensaje de error en BD, mostramos una ventana modal al usuario indicándole dicha situación
            // Si se recibe cualquier otro mensaje, mostramos una ventana modal al usuario indicándole dicha situación
            if(data.mensaje == 'Nombre de usuario repetido'){
                document.querySelector('#mensajeError').textContent = 'El nombre de usuario introducido ya existe. Elija otro.';

                // Habilitamos toda la página
                habilitaPaginaEntera();
            }else if(data.mensaje == 'Error en la validación de datos'){
                document.querySelector('#mensajeError').textContent = data.mensajeDetallado;

                // Habilitamos toda la página
                habilitaPaginaEntera();
            }else if(data.mensaje == 'Cuenta creada'){

                // Cambiamos el evento del botón de la ventana modal
                document.querySelector('#btnPrincipalVentanaModal').removeEventListener('click', ocultaVentanaModalCompleta);
                document.querySelector('#btnPrincipalVentanaModal').addEventListener('click', () => redigirePaginaUsuario(data));

                // Generamos una ventana modal u otra, dependiendo de si hay un elemento con ID 'paginaAnterior' creado en la página
                if(document.querySelector('#paginaAnterior')){
                    muestraVentanaModalParrafoYBoton('Cuenta creada correctamente.', 'Volver');
                }else{
                    muestraVentanaModalParrafoYBoton('Cuenta creada correctamente.', 'Volver al menú principal');
                }
            }else if(data.mensaje == 'Error en BD'){
                muestraVentanaModalError('al crear la cuenta.');
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
    if(e.target['inputNombre'].value == '' || e.target['inputApellidos'].value == '' || e.target['inputNombreUsuario'].value == '' || 
        e.target['inputContrasenia'].value == '' || e.target['inputRepitaContrasenia'].value == ''){

        // Campos vacios
        document.querySelector('#mensajeError').textContent = 'Por favor, rellene todos los datos para crear su cuenta.';
        bandera = false;
    }else if(e.target['inputNombre'].value.length > 30){

        // Nombre con longitud incorrecta
        document.querySelector('#mensajeError').textContent = 'El nombre no puede exceder de 30 caracteres.';
        bandera = false;
    }else if(e.target['inputApellidos'].value.length > 50){

        // Apellidos con longitud incorrecta
        document.querySelector('#mensajeError').textContent = 'Los apellidos no puede exceder de 50 caracteres.';
        bandera = false;
    }else if(e.target['inputNombreUsuario'].value.length > 20 || e.target['inputNombreUsuario'].value.length < 8){

        // Nombre de usuario con longitud incorrecta
        document.querySelector('#mensajeError').textContent = 'El nombre de usuario debe tener entre 8 y 20 caracteres.';
        bandera = false;
    }else if(e.target['inputContrasenia'].value.length > 20 || e.target['inputContrasenia'].value.length < 8){

        // Contraseña con longitud incorrecta
        document.querySelector('#mensajeError').textContent = 'La contraseña debe tener entre 8 y 20 caracteres.';
        bandera = false;
    }else if(e.target['inputContrasenia'].value != e.target['inputRepitaContrasenia'].value){

        // Las 2 contraseñas no son iguales
        document.querySelector('#mensajeError').textContent = 'Las contraseñas introducidas no son iguales.';
        bandera = false;
    }else if(!/^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)?$/.test(e.target['inputNombre'].value)){

        // El nombre no cumple el patrón indicado
        document.querySelector('#mensajeError').textContent = 'El nombre no posee un formato de caracteres adecuado.';
        bandera = false;
    }else if(!/^(de\s(la\s)?|del\s)?[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+$/.test(e.target['inputApellidos'].value)){

        // Los apellidos no cumplen el patrón indicado
        document.querySelector('#mensajeError').textContent = 'Los apellidos no poseen un formato de caracteres adecuado.';
        bandera = false;
    }else if(!/^[\w]{8,20}$/.test(e.target['inputNombreUsuario'].value)){

        // El nombre de usuario no cumple el patrón indicado
        document.querySelector('#mensajeError').textContent = 'El nombre de usuario solo puede tener letras, números y guiones bajos.';
        bandera = false;
    }else if(!/^[A-Za-z\d]{8,20}$/.test(e.target['inputContrasenia'].value)){

        // La contraseña no cumple el patrón indicado
        document.querySelector('#mensajeError').textContent = 'La contraseña solo puede tener letras y números.';
        bandera = false;
    }else if(!/^[A-Za-z\d]{8,20}$/.test(e.target['inputRepitaContrasenia'].value)){

        // La repetición de la contraseña no cumple el patrón indicado
        document.querySelector('#mensajeError').textContent = 'La repetición de la contraseña solo puede tener letras y números.';
        bandera = false;
    }

    // Retornamos la bandera
    return bandera;
}