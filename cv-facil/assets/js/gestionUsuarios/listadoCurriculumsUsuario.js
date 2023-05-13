// Generamos una petición asíncrona para obtener los currículums
generaPeticionAjax('ObtieneCurriculumsUsuario', (data) => {
    console.log(data);
    
    // Si no se ha recibido ningún dato, mostramos un mensaje al usuario indicando dicha situación
    // Si se ha recibido algún dato, ocultamos el mensaje que avisa de que se están obteniendo los currículums
    if(data.length == 0){
        document.querySelector('#mensajeAdvertencia').textContent = 'No hay ningún currículum guardado actualmente.';
    }else{
        document.querySelector('#mensajeAdvertencia').setAttribute('style', 'display: none;');
    }

    // Recorremos los datos recibidos, creando los elementos necesarios para que los datos de los curriculums recibidos
    //      aparezcan correctamente en el listado
    data.forEach(e => {
        creaElementoCompleto('p', document.querySelector('#seccionListadoCurriculum'), e.nombre, 
            {'id':'nombreCurriculum_' + e.id, 'class':'elementoListado'});
        creaElementoCompleto('p', document.querySelector('#seccionListadoCurriculum'), e.fechaRealizacion, 
            {'id':'fechaCurriculum_' + e.id, 'class':'elementoListado'});
        let articuloAcciones = creaElementoCompleto('article', document.querySelector('#seccionListadoCurriculum'), '', 
            {'id':'articuloAccionesCurriculum_' + e.id, 'class':'articuloAcciones'});
        creaElementoCompleto('i', articuloAcciones, '', {'class':'bi bi-file-earmark-pdf', 'id':'generaCurriculum_' + e.id}, 
            generaPdfCurriculum);
        creaElementoCompleto('img', articuloAcciones, '', {'src':'assets/img/iconoModificacion.png', 
            'id':'modificacionCurriculum_' + e.id}, modificaCurriculum);
        creaElementoCompleto('img', articuloAcciones, '', {'src':'assets/img/iconoBorrado.png', 
            'id':'borradoCurriculum_' + e.id}, borradoCurriculum);
        creaElementoCompleto('hr', document.querySelector('#seccionListadoCurriculum'), '', 
            {'id':'hrCurriculum_' + e.id});
    });
});

// Gestionamos el evento de click del botón principal de la ventana modal
document.querySelector('#btnPrincipalVentanaModal').addEventListener('click', ocultaVentanaModalCompleta);

// Gestionamos el evento de click del botón de 'No borrar' de la ventana modal
document.querySelector('#primerBotonArticuloDosBotonesVentanaModal').addEventListener('click', () => {

    // Ocultamos la ventana modal
    ocultaVentanaModalCompleta();

    // Habilitamos toda la página
    habilitaPaginaEntera();
});

// Gestionamos el evento de click del botón de 'Borrar' de la ventana modal
document.querySelector('#segundoBotonArticuloDosBotonesVentanaModal').addEventListener('click', gestionaBorradoCurriculumUsuario);

/**
 * Función que permite generar el PDF del currículum deseado.
 * @param {MouseEvent} e Evento generado al hacer click sobre el icono del PDF de un currículum.
 */
function generaPdfCurriculum(e){

    // Obtenemos el ID del currículum
    let id = e.target.getAttribute('id');
    let idCurriculum = id.split('_')[1];

    // Redirigimos al usuario a la URL de generación del PDF
    window.location = 'http://' + ip_pub + '/GeneraPDFBD/' + idCurriculum;
}

/**
 * Función que permite acceder a la modificación de un currículum.
 * @param {MouseEvent} e Evento que se genera al hacer click sobre el icono de modificación de un currículum.
 */
function modificaCurriculum(e){

    // Deshabilitamos toda la página
    deshabilitaPaginaEntera();

    // Mostramos la pantalla de 'Cargando...'
    mostradoVentanaModalCargando();

    // Obtenemos el id del currículum que se desea
    let id = e.target.getAttribute('id');
    let idCurriculum = id.split('_')[1];

    // Creamos un objeto para mandar todos los datos al servidor
    let formData = new FormData();

    // Guardamos los datos a mandar
    formData.append('idCurriculum', idCurriculum);

    // Hacemos una petición asíncrona al servidor
    generaPeticionAjax('ObtieneCurriculum', (data) => {
        console.log(data);
        
        // Si se recibe que se ha completado la petición correctamente, redirigimos al usuario a la pantalla
        //      recibida desde el servidor
        // Si se recibe cualquier otro mensaje, mostramos un mensaje de error
        if(data.mensaje == 'Completado'){
            window.location = data.ruta;
        }else{
            muestraVentanaModalError('al obtener los datos del currículum.');
        }
    }, formData);
}

/**
 * Función que permite generar la ventana modal que permitirá decidir al usuario si realmente quiere borrar el 
 *      currículum que ha seleccionado o no.
 * @param {MouseEvent} e Evento que se genera al hacer click sobre el icono de borrado de un currículum.
 */
function borradoCurriculum(e){

    // Deshabilitamos toda la página
    deshabilitaPaginaEntera();

    // Obtenemos el id del currículum que se desea
    let id = e.target.getAttribute('id');
    let idCurriculum = id.split('_')[1];

    // Mostramos una ventana modal al usuario indicando si de verdad quiere borrar el currículum que ha seleccionado
    muestraVentanaModalParrafoYDosBotones('¿Desea borrar el curriculum seleccionado?', 'No borrar', 'Borrar');

    // Guardamos el valor del currículum que se desea borrar en un input de tipo 'hidden' que tiene la ventana
    //      modal
    document.querySelector('#curriculumBorrar').setAttribute('value', 'curriculum_' + idCurriculum);
}

/**
 * Función que permite gestionar el borrado de un currículum por el usuario.
 */
function gestionaBorradoCurriculumUsuario(){

    // Creamos un objeto para mandar todos los datos al servidor
    let formData = new FormData();

    // Obtenemos el ID del currículum y lo guardamos en el objeto a mandar
    let idCurriculum = document.querySelector('#curriculumBorrar').value.split('_')[1];
    formData.append('idCurriculum', idCurriculum);

    // Realizamos una petición asincrona al servidor
    generaPeticionAjax('BorraCurriculum', (data) => {
        console.log(data);
        
        // Si se recibe un mensaje diciendo que todo se ha completado satisfactoriamente, mostramos una ventana modal al 
        //      usuario indicando dicha situación
        // Si se recibe un mensaje diciendo que ha dado un error en BD, mostramos una ventana modal al usuario indicándole
        //      dicho error
        if(data.mensaje == 'Completado'){

            // Mostramos la ventana modal con el mensaje de de que se ha borrado el currículum
            muestraVentanaModalParrafoYBoton('Curriculum borrado satisfactoriamente.', 'Volver');

            // Eliminamos todos los elementos del currículum del listado
            document.querySelector('#nombreCurriculum_' + idCurriculum).remove();
            document.querySelector('#fechaCurriculum_' + idCurriculum).remove();
            document.querySelector('#articuloAccionesCurriculum_' + idCurriculum).remove();
            document.querySelector('#hrCurriculum_' + idCurriculum).remove();

            // Si no queda ningún elemento en el listado de currículum, mostramos un mensaje al usuario indicando dicha 
            //      situación
            if(document.querySelectorAll('.elementoListado').length == 0){
                document.querySelector('#mensajeAdvertencia').textContent = 'No hay ningún currículum guardado actualmente.';
                document.querySelector('#mensajeAdvertencia').removeAttribute('style');
            }
        }else if(data.mensaje == 'Error en BD'){
            muestraVentanaModalError('al borrar el currículum.');
        }
    }, formData);
}