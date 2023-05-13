// Si existe el botón de guardado del currículum, le aplicamos el evento de gestión del click
if(document.querySelector('#guardaCurriculum')){
    document.querySelector('#guardaCurriculum').addEventListener('click', () => 
        gestionaVentanaModalGuardaActualizaCurriculum('Indique el nombre del currículum a guardar'));
}

// Si existe el botón de actualización del currículum, le aplicamos el evento de gestión del click
if(document.querySelector('#modificaCurriculum')){
    document.querySelector('#modificaCurriculum').addEventListener('click', () => 
        gestionaVentanaModalGuardaActualizaCurriculum('Indique el nuevo nombre para el currículum', 
            document.querySelector('#nombreCurriculum').value));
}

// Gestionamos el evento de click sobre el botón de 'No guardar' de la ventana modal
document.querySelector('#primerBotonArticuloDosBotonesVentanaModal').addEventListener('click', ocultaVentanaModalCompleta);

// Gestionamos el evento de click del botón de 'Descargar currículum'
document.querySelector('#descargarCurriculum').addEventListener('click', generaPDFCurriculum);

// Gestionamos el evento de click del botón principal de la ventana modal que aparecerá al dar error al subir los datos
document.querySelector('#btnPrincipalVentanaModal').addEventListener('click', ocultaVentanaModalCompleta);

// Gestionamos el evento de click del botón de 'Guardar' de la ventana modal
document.querySelector('#segundoBotonArticuloDosBotonesVentanaModal').addEventListener('click', actualizaGuardaCurriculum);

/**
 * Función que permite generar la ventana modal de inserción del nombre del currículum que se va a guardar o actualizar.
 * @param {string} textoParrafo Texto que se mostrará en la ventana modal.
 * @param {string} valueInput Valor del input que aparecerá en la ventana modal. Por defecto, tiene el valor ''.
 */
function gestionaVentanaModalGuardaActualizaCurriculum(textoParrafo, valueInput = ''){

    // Array con los campos que se mostrarán en la ventana modal y sus textos
    let arrayDatosVentanaModal = {
        '#primerParrafoArticuloVentanaModal':textoParrafo,
        '#inputVentanaModal':valueInput,
        '#articuloDosBotonesVentanaModal':'',
        '#primerBotonArticuloDosBotonesVentanaModal':'No guardar',
        '#segundoBotonArticuloDosBotonesVentanaModal':'Guardar'
    };

    // Mostramos la ventana modal
    muestraVentanaModal(arrayDatosVentanaModal);
}

/**
 * Función que permite actualizar o guardar el currículum en la BD.
 */
function actualizaGuardaCurriculum(){

    // Obtenemos el nombre indicado para el currículum
    let nombreCurriculum = document.querySelector('#inputVentanaModal').value;

    // Quitamos el mensaje de error que pudiera haber en pantalla
    document.querySelector('#mensajeErrorVentanaModal').setAttribute('style', 'display: none;');
    document.querySelector('#mensajeErrorVentanaModal').textContent = '';

    // Deshabilitamos toda la página
    deshabilitaPaginaEntera();

    // Comprobamos que nombre del currículum se ha indicado
    if(compruebaInputNombreCurriculum(nombreCurriculum)){

        // Creamos un objeto para mandar todos los datos al servidor
        let formData = new FormData();

        // Guardamos los datos a mandar al servidor
        formData.append('nombreCurriculum', nombreCurriculum.trim());

        // Dependiendo de que botón se muestre, haremos una u otra subida a servidor
        if(document.querySelector('#guardaCurriculum')){
            subeDatosActualizaGuardaCurriculum('GuardaCurriculum', 'Currículum guardado correctamente.', 'Ir al listado de curriculums',
                'al guardar el currículum.', formData);
        }else if(document.querySelector('#modificaCurriculum')){
            subeDatosActualizaGuardaCurriculum('ModificaCurriculum', 'Currículum actualizado correctamente.', 'Volver al listado',
                'al modificar los datos del currículum.', formData);
        }
    }else{

        // Habilitamos toda la página
        habilitaPaginaEntera();
    }
}

/**
 * Función que permite comprobar si los inputs del formulario cumplen todas las restricciones impuestas.
 * @param {Element} nombreCurriculum Elemento que representa el input de la ventana modal
 * @returns true si cumplen las restricciones, o false en caso contrario.
 */
function compruebaInputNombreCurriculum(nombreCurriculum){

    // Bandera que sirve para saber si se cumplen o no las restricciones
    let bandera = true;

    // Vamos comprobando las restricciones de input,  poniendo un mensaje de error y cambiando el valor de la bandera
    //      si no se cumplen dichas restricciones
    if(nombreCurriculum == ''){

        // No se ha indicado nombre
        document.querySelector('#mensajeErrorVentanaModal').textContent = 'Por favor, indique un nombre para '+
            'el curriculum.';
        document.querySelector('#mensajeErrorVentanaModal').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(!/^[\wÁÉÍÓÚÑáéíóúñ\.\s\-]+$/.test(nombreCurriculum)){

        // El nombre no cumple con el conjunto de caracteres pedidos
        document.querySelector('#mensajeErrorVentanaModal').textContent = 'Solo puede indicar letras, números, puntos, guiones ' + 
            'y espacios en el nombre del currículum.';
        document.querySelector('#mensajeErrorVentanaModal').setAttribute('style', 'display: block;');
        bandera = false;
    }else if(nombreCurriculum.length < 5 || nombreCurriculum.length > 30){

        // El nombre no tiene entre 5 y 30 caracteres
        document.querySelector('#mensajeErrorVentanaModal').textContent = 'El nombre del currículum debe tener entre 5 y 30 caracteres';
        document.querySelector('#mensajeErrorVentanaModal').setAttribute('style', 'display: block;');
        bandera = false;
    }

    // Retornamos la bandera
    return bandera;
}

/**
 * Función que permite subir los datos al servidor de la actualización o del guardado de un currículum.
 * @param {string} rutaPeticion Ruta a la cual se hara la petición de subida de datos.
 * @param {string} textoSubidaCompletada Texto de la ventana modal que aparecerá en caso de que la subida se haya completado
 *      satisfactoriamente.
 * @param {string} textoBotonSubidaCompletada Texto del botón de la ventana modal que aparecerá en caso de que la subida se
 *      haya completado satisfactoriamente.
 * @param {string} textoErrorSubida Texto del mensaje de error que aparecerá si no se ha podido completar la subida.
 * @param {FormData} formData Objeto que almacena los datos que se subirán al servidor.
 */
function subeDatosActualizaGuardaCurriculum(rutaPeticion, textoSubidaCompletada, textoBotonSubidaCompletada, textoErrorSubida, 
    formData){

    // Hacemos una petición asíncrona al servidor
    generaPeticionAjax(rutaPeticion, (data) => {
        console.log(data);

        // Si se recibe un mensaje de que ha habido un error en la validación de datos, mostramos un mensaje de error.
        // Si se recibe un mensaje de que se ha completado la subida, mostramos una ventana modal al usuario, la cual redirigirá
        //      al listado de currículums.
        // Si se recibe un mensaje de error, mostramos una ventana modal indicando dicha situación al usuario.
        // Si se recibe cualquier otro mensaje, mostramos un mensaje de error
        if(data.mensaje == 'Error en la validación de datos'){
            document.querySelector('#mensajeErrorVentanaModal').textContent = data.mensajeDetallado;
            document.querySelector('#mensajeErrorVentanaModal').setAttribute('style', 'display: block;');

            // Habilitamos toda la página
            habilitaPaginaEntera();
        }else if(data.mensaje == 'Completado'){

            // Ocultamos los elementos de la ventana modal
            ocultaVentanaModal(false);

            // Cambiamos el evento de click del botón principal de la ventana modal
            document.querySelector('#btnPrincipalVentanaModal').removeEventListener('click', ocultaVentanaModalCompleta);
            document.querySelector('#btnPrincipalVentanaModal').addEventListener('click', () => redigirePaginaUsuario(data));

            // Mostramos la ventana modal con los datos deseados
            muestraVentanaModalParrafoYBoton(textoSubidaCompletada, textoBotonSubidaCompletada);
        }else if(data.mensaje == 'Error en BD'){

            // Mostramos la ventana modal con el mensaje de error
            muestraVentanaModalError(textoErrorSubida);
        }else{

            // Mostramos la ventana modal con el mensaje de error
            muestraVentanaModalError('al subir los datos al servidor.');
        }
    }, formData);

}

/**
 * Función que permite redirigir al usuario a la pantalla de generación del PDF del currículum
 */
function generaPDFCurriculum(){
    window.location = 'http://' + ip_pub + '/GeneraPDFSesion';
}