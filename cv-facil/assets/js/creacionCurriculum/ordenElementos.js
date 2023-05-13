// Gestionamos el evento de submit del formulario
document.querySelector('#formularioOrdenaciones').addEventListener('submit', gestionaSubmitFormulario);

// Aplicamos el evento del botón de la ventana modal
document.querySelector('#btnPrincipalVentanaModal').addEventListener('click', ocultaVentanaModalCompleta);

/**
 * Función que permite gestionar el evento de submit del formulario.
 * @param {SubmitEvent} e Evento que se genera al hacer el submit del formulario.
 */
function gestionaSubmitFormulario(e){

    // Impedimos el evento por defecto
    e.preventDefault();

    // Creamos una variable que almacenara el posible mensaje de error que pueda dar al revisar los campos
    let cadenaError = '';

    // Comprobamos si existe la sección de ordenación de apartados
    if(document.querySelector('#articuloOrdenacionApartados')){

        // Comprobamos que se haya indicado un valor en cada uno de los selects
        cadenaError = compruebaValoresSelectsFormulario(e);

        // Comprobamos que en cada select haya un valor diferente, si es que no se ha detectado todavía ningún error
        if(cadenaError == '' && compruebaDatosNoRepetidosSelects()){
            cadenaError = 'No puede repetir ningún valor de los desplegables en el orden del currículum.';
        }
    }

    // Comprobamos que se haya seleccionado algún dato de orden cronológico si no he detectado todavía ningún error
    if(cadenaError == '' && e.target['orden'].value == ''){
        cadenaError = 'Indique el tipo de ordenación de los diferentes datos.';
    }

    // Deshabilitamos toda la página
    deshabilitaPaginaEntera();

    // Si la cadena de error contiene un mensaje, es que algo no se ha indicado bien, y hay que avisar al usuario de ello.
    // Si la cadena de error no contiene ningún mensaje, es que los datos están bien intorducidos, y podemos subirlos a 
    //      servidor
    if(cadenaError != ''){

        // Mostramos la ventana modal con el mensaje de error al usuario
        muestraVentanaModalParrafoYBoton(cadenaError, 'Volver');
    }else{

        // Mostramos la ventana modal de 'Cargando...'
        mostradoVentanaModalCargando();

        // Creamos un objeto para mandar todos los datos al servidor
        let formData = new FormData();

        // Guardamos los datos a mandar, si existen en el caso de las ordenaciones
        for(let i = 1; i < 5; i++){
            if(document.querySelector('#selectDato' + i)){
                formData.append('orden' + i, e.target['selectDato' + i].value);
            }
        }
        formData.append('cronologia', e.target['orden'].value);

        // Hacemos una petición asíncrona al servidor
        generaPeticionAjax('SubeOrden', (data) => {
            console.log(data);

            // Si se recibe un mensaje de que ha habido un error en la validación de datos, mostramos un mensaje de error.
            // Si se completa la petición correctamente, redirigimos al usuario a la página recibida
            //      desde el servidor.
            // Si se recibe cualquier otro mensaje, mostramos un mensaje de error
            if(data.mensaje == 'Error en la validación de datos'){
                muestraVentanaModalParrafoYBoton(data.mensajeDetallado, 'Volver');
            }else if(data.mensaje == 'Completado'){
                window.location = data.ruta;
            }else{
                muestraVentanaModalError('al cargar la página.');
            }
        }, formData);
    }
}

/**
 * Función que permite comprobar que todos los selects del formulario tienen un valor seleccionado.
 * @returns Cadena vacía si todos los select tienen un valor seleccionado, o cadena con un mensaje de error
 *      en caso de que haya algún select sin valor.
 */
function compruebaValoresSelectsFormulario(e){

    // Creamos la variable que almacenará la cadena de error
    let cadenaError = '';

    // Hacemos un bucle para mirar los valores de todos los select del formulario, comprobando previamente que 
    //      existan el select que se mirará en cada iteracción del bucle
    for(let i = 0; i < 5; i++){
        if(document.querySelector('#selectDato' + i) && e.target['selectDato' + i].value == '-1'){
            cadenaError = 'Seleccione el ' + i + 'º apartado del orden del currículum.';
            break;
        }
    }

    // Retornamos la cadena de error
    return cadenaError;
}

/**
 * Función que permite comprobar que no se ha repetido ningún valor en los select de ordenación.
 * @returns true si no hay ningún valor repetido, y false en caso contrario.
 */
function compruebaDatosNoRepetidosSelects(){

    // Array auxiliar para poder guardar los datos no repetidos
    let arrayDatos = [];

    // Bandera para comprobar si hay un valor repetido
    let bandera = false;

    // Recorremos el array de selects, mirando si el valor a leer ya esta en el array auxiliar, y si no esta,
    //      guardándolo en el array auxiliar
    document.querySelectorAll('select').forEach(e => {
        if(arrayDatos.length > 0 && arrayDatos.indexOf(e.value) != '-1'){
            bandera = true;
        }else{
            arrayDatos.push(e.value);
        }
    });

    // Retornamos la bandera
    return bandera;
}