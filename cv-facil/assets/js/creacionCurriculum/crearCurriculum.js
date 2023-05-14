// Cuando se pulse el botón de seleccionar foto, forzamos a que se pulse el input de tipo 'file' preparado para ello.
document.querySelector('#botonInputFotoCurriculum').addEventListener('click', gestionaInputBotonFoto);

// Manejamos el evento de 'change' del input de tipo 'file' para la foto.
document.querySelector('#inputFotoCurriculum').addEventListener('change', subeFotoCurriculum);

// Creamos un array y lo recorremos para poder manejar todos los eventos de las secciones de añadir estudios, experiencia, idiomas
//      y datos de interés
['Estudio', 'Experiencia', 'Idioma', 'Datos'].forEach(el => {

    // Variable que almacena la cadena con el identificador del contador que se va a leer
    let contador;

    // Dependiendo de lo que estemos leyendo, recogemos el valor de un contador u otro
    if(el == 'Estudio'){
        contador = '#conteoArticulosEstudio';
    }else if(el == 'Experiencia'){
        contador = '#conteoArticulosExperiencia';
    }else if(el == 'Idioma'){
        contador = '#conteoArticulosIdioma';
    }else if(el = 'Datos'){
        contador = '#conteoArticulosDatos';
    }

    // Manejamos los eventos del enlace de 'Agregar', del botón de 'Descartar cambios' y del de 'Añadir datos' de cada uno
    //      de los cuadros de añadir datos
    document.querySelector('#enlaceAnadir' + el).addEventListener('click', () => muestraOcultaCuadroAnadir(el));
    document.querySelector('#descartarCambiosAnade' + el).addEventListener('click', () => descartaCambios(el));
    document.querySelector('#anadir' + el).addEventListener('click', () => agregaModificaElemento(el, contador));

    // Si el elemento que estamos leyendo no es 'Datos', manejamos el evento del botón de 'Actualidad' del cuadro de añadir
    //      datos
    if(el != 'Datos'){
        document.querySelector('#btn' + el + 'Actuales').addEventListener('click', () => habilitaDeshabilitaInputFinFecha(el));
    }
});

// Aplicamos el evento de submit al formulario
document.querySelector('form').addEventListener('submit', enviaContenidoCurriculum);

// Aplicamos el evento de borrado a todos los iconos de borrado que haya en la página, porque pueden habernos venido
//      datos a la vista, y que se hayan mostrado sin aplicarse los eventos
document.querySelectorAll('.imagenBorrado').forEach(e => {
    e.addEventListener('click', gestionaBorradoDato);
});

// Aplicamos el evento de modificación a todos los iconos de modificación que haya en la página, porque pueden habernos venido
//      datos a la vista, y que se hayan mostrado sin aplicarse los eventos
document.querySelectorAll('.imagenModificacion').forEach(el => {

    // Obtenemos el id del elemento que se ha añadido a la página
    let articulo = el.parentNode.parentNode.getAttribute('id');

    // Obtenemos un array con cada uno de los caracteres del id del elemento
    let palabraClave = articulo.split("");

    // Bandera creada para saber cuando hay que poner la letra en mayuscula o no
    let bandera = true;

    // Cadena que almacenará la palabra final
    let palabraFinal = "";

    // Recorremos el array de caracteres
    for(let letra of palabraClave){

        // Si la bandera es verdadera y el caracter es una letra, añadimos dicha letra en mayúscula a la cadena que almacena
        //      la palabra final
        // Si la bandera es falsa, pero el caracter es una letra, añadimos dicha letra a la cadena que almacena la palabra final
        if(bandera && isNaN(letra)){
            palabraFinal += letra.toUpperCase();
        }else if(isNaN(letra)){
            palabraFinal += letra;
        }

        // Ponemos la bandera a false
        bandera = false;
    }
    
    // Aplicamos el evento de click al icono de modificación
    el.addEventListener('click', (e) => gestionaModificacionDato(e, palabraFinal));
});

// Aplicamos el evento del botón de la ventana modal
document.querySelector('#btnPrincipalVentanaModal').addEventListener('click', () => {

    // Ocultamos la ventana modal
    ocultaVentanaModalCompleta();

    // Deshabilitamos los inputs de fecha de fin si esta marcado 'Actualidad' en alguno de ellos
    deshabilitaInputsFechaFinSiActualidad();
});

/**
 * Función que permite gestionar el input del botón de 'Seleccionar foto', que lo que hace es forzar el click del 
 *      input de tipo 'file' preparado para ello
 */
function gestionaInputBotonFoto(){
    document.querySelector('#inputFotoCurriculum').click();
}

/**
 * Función que permite subir una foto al servidor, y mostrarla en la pantalla del curriculum.
 * @param {MouseEvent} e Evento generado al pulsar sobre el input de tipo 'file'.
 */
function subeFotoCurriculum(e){

    // Obtenemos la foto puesta
    let foto = e.target.files[0];

    // Comprobamos si se ha seleccionado foto
    if(foto !== undefined){

        // Deshabilitamos toda la página
        deshabilitaPaginaEntera();

        // Obtenemos la extensión de la foto, para comprobar que no se haya subido un fichero que no sea ni PNG ni JPG/JPEG
        let arrayFoto = foto.name.split('.');
        let extensionFoto = arrayFoto[arrayFoto.length - 1].toLowerCase();

        // Si la extensión de la foto es PNG o JPG/JPEG, se sube al servidor
        // Si no es ninguna de estas extensiones, se muestra una advertencia al usuario
        if(extensionFoto == 'png' || extensionFoto == 'jpg' || extensionFoto == 'jpeg'){

            // Creamos un objeto FormData para poder enviar la foto al servidor
            let formData = new FormData();
            formData.append('foto', foto);

            // Hacemos una petición fetch para subir la imagen al servidor
            generaPeticionAjax('SubidaFotoCurriculum', (data) => {
                console.log(data);

                // Si se recibe un mensaje de que se ha completado la subida, ponemos la nueva imagen en el elemento 
                //      destinado para la foto del curriculum.
                // Si se recibe un mensaje de error, mostramos la ventana modal, avisando al usuario de dicha situación.
                // Si se recibe un mensaje de que ha habido un error en la validación de datos, mostramos un mensaje de error.
                // Si se recibe cualquier otro mensaje, mostramos un mensaje al usuario
                if(data.mensaje == 'Completado'){
                    document.querySelector('#fotoCurriculum').removeAttribute('src');
                    document.querySelector('#fotoCurriculum').setAttribute('src', data.ruta);

                    // Habilitamos toda la página
                    habilitaPaginaEntera();
                }else if(data.mensaje == 'Error'){
                    muestraVentanaModalError('al subir la foto al servidor.');
                }else if(data.mensaje == 'Error en la validación de datos'){
                    muestraVentanaModalParrafoYBoton(data.mensajeDetallado, 'Volver');
                }else{
                    muestraVentanaModalError('al obtener la foto.');
                }
            }, formData);

        }else{
            muestraVentanaModalParrafoYBoton('Solo se pueden poner como foto de currículum ficheros PNG, JPG o JPEG.', 'Volver');
        }
    }
}

/**
 * Función que permite descartar todos los cambios que se puedan haber realizado al modificar un estudio, experiencia 
 *      o idioma concreto. 
 * @param {string} cadenaPrincipal Cadena que permite identificar el tipo de dato que se esta modificando.
 */
function descartaCambios(cadenaPrincipal){

    // Obtenemos el id del artículo que se esta modificando
    let idArticulo = document.querySelector('#id' + cadenaPrincipal + "Modificacion").value;

    // Volvemos a mostrar el artículo que se esta modificando
    document.querySelector('#' + idArticulo).removeAttribute('style');
    let hr = obtieneLineaAnteriorArticuloModificacionBorrado(document.querySelector('#' + idArticulo));
    hr.removeAttribute('style');

    // Reseteamos todos los campos del cuadro de añadir
    reseteaCamposOcultacionCuadroAnadir(cadenaPrincipal);
}

/**
 * Función que permite mostrar y ocultar el cuadro de añadir datos en las secciones de estudios, experiencia e idiomas.
 * @param {string} cadenaPrincipal Cadena que permite identificar el cuadro que se va a mostrar u ocultar.
 */
function muestraOcultaCuadroAnadir(cadenaPrincipal){

    // Comprobamos si el cuadro de añadir esta visible o no.
    if(document.querySelector('#cuadroAnadir' + cadenaPrincipal).hasAttribute('style')){

        // Reseteamos todos los campos del cuadro de añadir
        reseteaCamposOcultacionCuadroAnadir(cadenaPrincipal);
    }else{

        // Mostramos el cuadro de añadir, además de cambiar el texto del enlace que lo muestra.
        document.querySelector('#cuadroAnadir' + cadenaPrincipal).setAttribute('style', 'display: block;');
        document.querySelector('#enlaceAnadir' + cadenaPrincipal).textContent = '- Ocultar agregación de ' + 
            cadenaPrincipal.toLowerCase();

        // Ocultamos los botones de modificación y borrado de datos
        document.querySelectorAll(cadenaPrincipal.toLowerCase() + 'Anadido > article:last-child').forEach(e => 
            e.setAttribute('style', 'display: none;'));

        // Aplicamos el texto de la cabecera del cuadro de añadir y del botón de añadir
        document.querySelector('#cuadroAnadir' + cadenaPrincipal + ' header > h3').textContent = 'Añadir ' + 
            cadenaPrincipal.toLowerCase();
        document.querySelector('#anadir' + cadenaPrincipal).textContent = "Añadir " + cadenaPrincipal.toLowerCase();
    }
}

/**
 * Función que permite habilitar o deshabilitar el input de la fecha de fin de los cuadros de añadir estudios, experiencia e idiomas.
 * @param {string} cadenaPrincipal Cadena que permite identificar el cuadro sobre el que se va a actuar.
 */
function habilitaDeshabilitaInputFinFecha(cadenaPrincipal){
    
    // Si el input de la fecha de fin esta deshabilitado, lo habilitamos y quitamos el estilo al botón de 'Actualidad'.
    // Si no esta deshabilitado, lo deshabilitamos, quitando el valor que tenga, y ponemos un estilo al botón de 'Actualidad'.
    if(document.querySelector('#fechaFinAnade' + cadenaPrincipal).hasAttribute('disabled')){
        habilitaInputFechaFin(cadenaPrincipal);
    }else{
        document.querySelector('#fechaFinAnade' + cadenaPrincipal).setAttribute('disabled', 'disabled');
        document.querySelector('#fechaFinAnade' + cadenaPrincipal).value = '';
        document.querySelector('#btn' + cadenaPrincipal + 'Actuales').setAttribute('style', 'font-weight: bolder;');
    }
}

/**
 * Función que permite agregar un estudio, experiencia, idioma o dato de interés, o modificar uno añadido previamente.
 * @param {string} cadenaPrincipal Cadena que permite identificar el dato que se va a añadir o modificar.
 * @param {string} contador Contador que permite crear IDs unicos para cada dato a añadir o modificar.
 */
function agregaModificaElemento(cadenaPrincipal, contador){

    // Obtenemos los valores de los input del cuadro de añadir, comprobando que el input de la fecha de fin de estudios no 
    //      este deshabilitado, o que lo que se este añadiendo no sea un dato de interés
    let tituloAnadir = document.querySelector('#tituloAnade' + cadenaPrincipal).value;
    let centroAnadir;
    let fechaInicioAnadir;
    let fechaFinAnadir;
    let condicionActualAnadir = false;
    if(cadenaPrincipal != 'Datos'){
        centroAnadir = document.querySelector('#centroAnade' + cadenaPrincipal).value;
        fechaInicioAnadir = document.querySelector('#fechaInicioAnade' + cadenaPrincipal).value;
        fechaFinAnadir = document.querySelector('#fechaFinAnade' + cadenaPrincipal).value;
        if(document.querySelector('#fechaFinAnade' + cadenaPrincipal).hasAttribute('disabled')){
            condicionActualAnadir = true;
        }
    }

    // Quitamos el mensaje de error que pudiera haber en el cuadro de añadir o modificar datos
    document.querySelector('#mensajeErrorAnade' + cadenaPrincipal).setAttribute('style', 'display: none;');
    document.querySelector('#mensajeErrorAnade' + cadenaPrincipal).textContent = '';

    // Si lo que se esta añadiendo es un dato de interes, llamamos a la función de agregar o modificar datos de interés
    // Si lo que se esta añadiendo es un estudio, experiencia o idioma, llamamos a la función de agregar o modificar 
    //      estudios, experiencias o idiomas
    if(cadenaPrincipal == 'Datos'){
        agregaModificaDatoInteres(cadenaPrincipal, tituloAnadir, contador);
    }else{
        agregaModificaEstudioExperienciaIdioma(cadenaPrincipal, tituloAnadir, centroAnadir, fechaInicioAnadir, fechaFinAnadir, 
            condicionActualAnadir, contador);
    }
}

/**
 * Función que permite agregar o modificar un dato de interés deseado.
 * @param {string} cadenaPrincipal Cadena que permite identificar el dato que se va a añadir o modificar.
 * @param {string} tituloAnadir Cadena que contiene el dato de interés a añadir o modificar. 
 * @param {string} contador Contador que permite crear IDs unicos para cada dato a añadir o modificar.
 */
function agregaModificaDatoInteres(cadenaPrincipal, tituloAnadir, contador){

    // Si no se han rellenado el textarea o tiene un formato inadecuado, mostramos un mensaje de error
    if(tituloAnadir == ''){
        muestraMensajeErrorAgregaciones('Por favor, rellene todos los datos correctamente.', '#mensajeErrorAnade' + cadenaPrincipal);
    }else if(!/^[A-ZÁÉÍÓÚÑa-záéíóúñ\d\.\s,-]+$/.test(tituloAnadir)){
        muestraMensajeErrorAgregaciones('El dato de interés no tiene un formato adecuado.', '#mensajeErrorAnade' + cadenaPrincipal);
    }else{

        // Comprobamos si el botón tiene el texto de añadir o de confirmar cambios
        if(document.querySelector('#anadir' + cadenaPrincipal).textContent == 'Añadir ' + cadenaPrincipal.toLowerCase()){
                
            // Obtenemos el artículo con todos los datos añadidos del dato que se desea añadir
            let datosAnadidos = document.querySelector('#articulo' + cadenaPrincipal + 'Anadidos');

            // Creamos una línea de separación para el dato a añadir
            creaElementoCompleto('hr', datosAnadidos);

            // Creamos el artículo del dato a añadir
            let articuloDatoAnadir = creaElementoCompleto('article', datosAnadidos, '', {'class': cadenaPrincipal.toLowerCase()  + 'Anadido elementoAnadido', 'id': 
                cadenaPrincipal.toLowerCase() + document.querySelector(contador).value});

            // Creamos el artículo con los datos de lo que se desea añadir, con todos los elementos que tendrá dentro
            let articuloDatos = creaElementoCompleto('article', articuloDatoAnadir);
            creaElementoCompleto('p', articuloDatos, tituloAnadir.trim(), {'class':'tituloElemento'});

            // Creamos el artículo con los botones de modificación y borrado, con todos los elementos que tendrá dentro
            anadeBotonesModificacionBorradoDatoAnadido(cadenaPrincipal, articuloDatoAnadir);

            // Aumentamos la variable de contador
            document.querySelector(contador).value = parseInt(document.querySelector(contador).value) + 1

            // Reseteamos todos los campos del cuadro de añadir
            reseteaCamposOcultacionCuadroAnadir(cadenaPrincipal);
        }else if(document.querySelector('#anadir' + cadenaPrincipal).textContent == 'Confirmar cambios'){

            // Llamamos al método de aplicación de modificación de datos
            aplicaModificacionDatos(cadenaPrincipal, tituloAnadir);
        }
    }
}

/**
 * Función que permite agregar o modificar un estudio, experiencia o idioma deseado.
 * @param {string} cadenaPrincipal Cadena que permite identificar el dato que se va a añadir o modificar.
 * @param {string} tituloAnadir Cadena que contiene el titulo del dato a añadir o modificar. 
 * @param {string} centroAnadir Cadena que contiene el centro del dato a añadir o modificar. 
 * @param {string} fechaInicioAnadir Cadena que contiene la fecha de inicio del dato a añadir o modificar. 
 * @param {string} fechaFinAnadir Cadena que contiene la fecha de fin del dato a añadir o modificar. 
 * @param {boolean} condicionActualAnadir Booleano que contiene el valor de si se esta introduciendo un dato actual o no.
 * @param {string} contador Contador que permite crear IDs unicos para cada dato a añadir o modificar.
 */
function agregaModificaEstudioExperienciaIdioma(cadenaPrincipal, tituloAnadir, centroAnadir, fechaInicioAnadir, fechaFinAnadir, 
    condicionActualAnadir, contador){

    // Comprobamos que todos los datos se hayan rellenado correctamente
    if(compruebaCamposIntroduceEstudioExperienciaIdioma(cadenaPrincipal, tituloAnadir, centroAnadir, fechaInicioAnadir, fechaFinAnadir, 
        condicionActualAnadir)){

        // Comprobamos si el botón tiene el texto de añadir o de confirmar cambios
        if(document.querySelector('#anadir' + cadenaPrincipal).textContent == 'Añadir ' + cadenaPrincipal.toLowerCase()){
                
            // Obtenemos los artículos con todos los datos añadidos del dato que se desea añadir
            let arrayDatosAnadidos = Array.from(document.querySelectorAll('.' + cadenaPrincipal.toLowerCase() + 'Anadido'));

            // Creamos el artículo del dato a añadir
            let articuloDatoAnadir = creaElementoCompleto('article', undefined, '', {'class': cadenaPrincipal.toLowerCase() + 
                'Anadido elementoAnadido', 'id': cadenaPrincipal.toLowerCase() + document.querySelector(contador).value});

            // Creamos el artículo con los datos de lo que se desea añadir, con todos los elementos que tendrá dentro, diferenciando
            //      entre experiencia y estudios o idiomas para algunas frases a mostrar
            let articuloDatos = creaElementoCompleto('article', articuloDatoAnadir);
            creaElementoCompleto('p', articuloDatos, tituloAnadir.trim(), {'class':'tituloElemento'});
            let parrafoCentro;
            if(cadenaPrincipal == 'Experiencia'){
                parrafoCentro = creaElementoCompleto('p', articuloDatos, 'Centro de trabajo: ');
            }else if(cadenaPrincipal == 'Estudio' || cadenaPrincipal == 'Idioma'){
                parrafoCentro = creaElementoCompleto('p', articuloDatos, 'Centro de estudio: ');
            }
            creaElementoCompleto('span', parrafoCentro, centroAnadir.trim(), {'class':'centroElemento'});
            let parrafoAnios = creaElementoCompleto('p', articuloDatos, 'Fecha de inicio y fin: ');
            creaElementoCompleto('span', parrafoAnios, formateaFechasGuiones(fechaInicioAnadir), {'class':'fechaInicioElemento'});
            parrafoAnios.appendChild(document.createTextNode(' - '));
            creaElementoCompleto('span', parrafoAnios, condicionActualAnadir ? 'Actualidad' : 
                formateaFechasGuiones(fechaFinAnadir), {'class':'fechaFinElemento'});

            // Creamos el artículo con los botones de modificación y borrado, con todos los elementos que tendrá dentro
            anadeBotonesModificacionBorradoDatoAnadido(cadenaPrincipal, articuloDatoAnadir);

            // Metemos el dato que se quiere añadir en el array de todos los datos
            arrayDatosAnadidos.push(articuloDatoAnadir);

            // Ordenamos el array
            arrayDatosAnadidos = reordenaElementosAnadidos(arrayDatosAnadidos);

            // Recorremos el array
            arrayDatosAnadidos.forEach(e => {

                // Si el elemento que se esta recorriendo no es el que se va a añadir, eliminamos sus elementos del DOM
                if(e.getAttribute('id') != cadenaPrincipal.toLowerCase() + document.querySelector(contador).value){
                    obtieneLineaAnteriorArticuloModificacionBorrado(e).remove();
                    e.remove();
                }

                // Añadimos el elemento al DOM
                creaElementoCompleto('hr', document.querySelector('#articulo' + cadenaPrincipal + 'Anadidos'));
                document.querySelector('#articulo' + cadenaPrincipal + 'Anadidos').appendChild(e)
            });

            // Aumentamos la variable de contador, dependiendo de cual se haya pulsado
            document.querySelector(contador).value = parseInt(document.querySelector(contador).value) + 1

            // Reseteamos todos los campos del cuadro de añadir
            reseteaCamposOcultacionCuadroAnadir(cadenaPrincipal);
        } else if(document.querySelector('#anadir' + cadenaPrincipal).textContent == 'Confirmar cambios'){

            // Llamamos al método de aplicación de modificación de datos
            aplicaModificacionDatos(cadenaPrincipal, tituloAnadir, centroAnadir, fechaInicioAnadir, fechaFinAnadir, 
                condicionActualAnadir);
        }
    }
}

/**
 * Función que permite comprobar los campos de añadir o modificar estudios, experiencia o idiomas.
 * @param {string} cadenaPrincipal Cadena que permite identificar el dato que se va a añadir o modificar.
 * @param {string} tituloAnadir Cadena que contiene el titulo del dato a añadir o modificar. 
 * @param {string} centroAnadir Cadena que contiene el centro del dato a añadir o modificar. 
 * @param {string} fechaInicioAnadir Cadena que contiene la fecha de inicio del dato a añadir o modificar. 
 * @param {string} fechaFinAnadir Cadena que contiene la fecha de fin del dato a añadir o modificar. 
 * @param {boolean} condicionActualAnadir Booleano que contiene el valor de si se esta introduciendo un dato actual o no.
 * @returns true si cumplen las restricciones, o false en caso contrario.
 */
function compruebaCamposIntroduceEstudioExperienciaIdioma(cadenaPrincipal, tituloAnadir, centroAnadir, fechaInicioAnadir, fechaFinAnadir, 
    condicionActualAnadir){

    // Bandera que sirve para saber si se cumplen o no las restricciones
    let bandera = true;

    // Vamos comprobando las restricciones de los campos, poniendo un mensaje de error y cambiando el valor de la bandera
    //      si no se cumplen dichas restricciones
    if(tituloAnadir == '' || centroAnadir == '' || fechaInicioAnadir == '' || 
        (fechaFinAnadir == '' && !condicionActualAnadir) || (fechaFinAnadir != '' && condicionActualAnadir)){
        
        // No se han rellenado todos los input correctamente
        muestraMensajeErrorAgregaciones('Por favor, rellene todos los datos correctamente.', '#mensajeErrorAnade' + cadenaPrincipal);
        bandera = false;
    }else if((cadenaPrincipal == 'Estudio' || cadenaPrincipal == 'Experiencia') && 
        !/^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\.]+\s?)+((de(\sla)?|en|del)\s)?)+$/.test(tituloAnadir)){

        // Se ha puesto caracteres no válidos en el puesto o título del estudio o experiencia
        if(cadenaPrincipal == 'Experiencia'){
            muestraMensajeErrorAgregaciones('Se han puesto caracteres no válidos en el puesto.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }else if(cadenaPrincipal == 'Estudio'){
            muestraMensajeErrorAgregaciones('Se han puesto caracteres no válidos en el título.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }
        bandera = false;
    }else if(cadenaPrincipal == 'Idioma' && !/^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\d\.]+\s?)+(de\s(la\s)?|en\s|del\s)?)+$/.test(tituloAnadir)){

        // Se ha puesto caracteres no válidos en el título del idioma
        muestraMensajeErrorAgregaciones('Se han puesto caracteres no válidos en el título.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        bandera = false;
    }else if(cadenaPrincipal == 'Idioma' && /\d/.test(tituloAnadir) && !/\sde [A-Z]\d\s/.test(tituloAnadir)){

        // Se ha puesto un número donde no se debe en el título del idioma
        muestraMensajeErrorAgregaciones('El título se debe indicar similar al ejemplo: Título de B2 en Inglés.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        bandera = false;
    }else if(!/^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\.]+\s?)+((de(\sla)?|la|del|en)\s)?)+$/.test(centroAnadir)){

        // Se ha puesto caracteres no válidos en el centro de estudios o trabajo
        if(cadenaPrincipal == 'Experiencia'){
            muestraMensajeErrorAgregaciones('Se han puesto caracteres no válidos en el centro de trabajo.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }else if(cadenaPrincipal == 'Estudio' || cadenaPrincipal == 'Idioma'){
            muestraMensajeErrorAgregaciones('Se han puesto caracteres no válidos en el centro de estudios.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }
        bandera = false;
    }else if(/\s([Dd][EeÉé](\s[Ll][AaÁá])?|[Dd][EeÉé][Ll]|[EeÉé][Nn])\s?$/.test(tituloAnadir)){

        // El título o puesto de trabajo esta terminando por de, de la, del o en
        if(cadenaPrincipal == 'Experiencia'){
            muestraMensajeErrorAgregaciones('El puesto de trabajo no puede acabar por de, de la, del o en.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }else if(cadenaPrincipal == 'Estudio' || cadenaPrincipal == 'Idioma'){
            muestraMensajeErrorAgregaciones('El título no puede acabar por de, de la, del o en.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }
        bandera = false;
    }else if(/\s([Dd][EeÉé](\s[Ll][AaÁá])?|[Ll][AaÁá]|[Dd][EeÉé][Ll]|[EeÉé][Nn])\s?$/.test(centroAnadir)){

        // El centro de estudio o trabajo esta terminado por de, de la, la, del, en
        if(cadenaPrincipal == 'Experiencia'){
            muestraMensajeErrorAgregaciones('El centro de trabajo no puede acabar por de, de la, la, del o en.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }else if(cadenaPrincipal == 'Estudio' || cadenaPrincipal == 'Idioma'){
            muestraMensajeErrorAgregaciones('El centro de estudios no puede acabar por de, de la, la, del o en.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }
        bandera = false;
    }else if(/\s(D[EeÉé](\s[Ll][AaÁá])?|D[EeÉé][Ll]|[EÉ][Nn])\s/.test(tituloAnadir)){

        // El título o puesto de trabajo contiene las palabras De, De La, De la, Del o En
        muestraMensajeErrorAgregaciones('De, De la, De La, Del o En deben ir en minúsculas.', 
            '#mensajeErrorAnade' + cadenaPrincipal);
        
        bandera = false;
    }else if(/\s(D[EeÉé](\s[Ll][AaÁá])?|L[AaÁá]|D[EeÉé][Ll]|[EÉ][Nn])\s/.test(centroAnadir)){

        // El centro de estudio o trabajo contiene las palabras De, De La, De la, La, Del o En
        muestraMensajeErrorAgregaciones('De, De la, De La, La, Del o En deben ir en minúsculas.', 
            '#mensajeErrorAnade' + cadenaPrincipal);
        bandera = false;
    }else if(tituloAnadir.length < 15 || tituloAnadir.length > 200){

        // Longitud incorrecta de caracteres en el título/puesto
        if(cadenaPrincipal == 'Experiencia'){
            muestraMensajeErrorAgregaciones('La longitud del puesto de trabajo debe estar entre 15 y 200 caracteres.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }else if(cadenaPrincipal == 'Estudio' || cadenaPrincipal == 'Idioma'){
            muestraMensajeErrorAgregaciones('La longitud del título debe estar entre 15 y 200 caracteres.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }
        bandera = false;
    }else if(centroAnadir.length < 5 || centroAnadir.length > 100){
        
        // Longitud incorrecta de caracteres en el centro de trabajo/estudios
        if(cadenaPrincipal == 'Experiencia'){
            muestraMensajeErrorAgregaciones('La longitud del centro de trabajo debe estar entre 5 y 100 caracteres.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }else if(cadenaPrincipal == 'Estudio' || cadenaPrincipal == 'Idioma'){
            muestraMensajeErrorAgregaciones('La longitud del centro de estudios debe estar entre 5 y 100 caracteres.', 
                '#mensajeErrorAnade' + cadenaPrincipal);
        }
        bandera = false;
    }else if(!compruebaFechasIntroducidas(fechaInicioAnadir) || 
        (!condicionActualAnadir && !compruebaFechasIntroducidas(fechaFinAnadir))){

        // No se han rellenado las fechas correctamente
        muestraMensajeErrorAgregaciones('Por favor, ponga una fecha válida o que no sea posterior a la actual.', 
            '#mensajeErrorAnade' + cadenaPrincipal);
        bandera = false;
    }else if(Date.parse(fechaInicioAnadir) < Date.parse('1980-01-01') || Date.parse(fechaInicioAnadir) > Date.now()){

        // Se indicado una fecha de inicio que no es válida
        muestraMensajeErrorAgregaciones('La fecha de inicio debe estar entre el 1/1/1980 y el ' + new Date(Date.now()).toLocaleDateString() + '.', 
            '#mensajeErrorAnade' + cadenaPrincipal);
        bandera = false;
    }else if(Date.parse(fechaFinAnadir) < Date.parse('1980-01-01') || Date.parse(fechaFinAnadir) > Date.now()){

        // Se indicado una fecha de fin que no es válida
        muestraMensajeErrorAgregaciones('La fecha de fin debe estar entre el 1/1/1980 y el ' + new Date(Date.now()).toLocaleDateString() + '.', 
            '#mensajeErrorAnade' + cadenaPrincipal);
        bandera = false;
    }else if(!condicionActualAnadir && new Date(fechaInicioAnadir) > new Date(fechaFinAnadir)){

        // Se ha seleccionado una fecha de inicio posterior a la fin
        muestraMensajeErrorAgregaciones('Por favor, ponga una fecha de finalización posterior a la de inicio.', '#mensajeErrorAnade' + cadenaPrincipal);
        bandera = false;
    }

    // Retornamos la bandera
    return bandera;
}

/**
 * Función que permite agregar los botones de modificación y borrado a un estudio, experiencia, idioma o dato de interés que 
 *      estamos añadiendo.
 * @param {string} cadenaPrincipal Cadena que permite identificar el dato que se esta añadiendo.
 * @param {Element} articuloDatoAnadir Artículo sobre el que se añadirán los botones de modificación y borrado.
 */
function anadeBotonesModificacionBorradoDatoAnadido(cadenaPrincipal, articuloDatoAnadir){

    // Creamos el artículo con los botones de modificación y borrado, con todos los elementos que tendrá dentro
    let articuloBotonesEstudio = creaElementoCompleto('article', articuloDatoAnadir);
    creaElementoCompleto('img', articuloBotonesEstudio, '', {'src':'assets/img/iconoModificacion.png', 'class':'imagenModificacion'},
        (e) => gestionaModificacionDato(e, cadenaPrincipal));
    creaElementoCompleto('img', articuloBotonesEstudio, '', {'src':'assets/img/iconoBorrado.png', 'class':'imagenBorrado'},
        gestionaBorradoDato);
}

/**
 * Función que permite modificar un estudio, experiencia, idioma o dato de interés deseado.
 * @param {string} cadenaPrincipal Cadena que permite identificar el dato que se va a modificar.
 * @param {string} tituloAnadir Cadena que contiene el titulo del dato a modificar. 
 * @param {string} centroAnadir Cadena que contiene el centro del dato a modificar. Por defecto, su valor es ''.
 * @param {string} fechaInicioAnadir Cadena que contiene la fecha de inicio del dato a modificar. Por defecto, su valor es ''.
 * @param {string} fechaFinAnadir Cadena que contiene la fecha de fin del dato a modificar. Por defecto, su valor es ''.
 * @param {boolean} condicionActualAnadir Booleano que contiene el valor de si se esta modificando un dato actual o no. Por defecto, su valor
 *      es false.
 */
function aplicaModificacionDatos(cadenaPrincipal, tituloAnadir, centroAnadir = '', fechaInicioAnadir = '', fechaFinAnadir = '', 
    condicionActualAnadir = false){

    // Obtenemos el id del dato a modificar
    let idArticulo = document.querySelector('#id' + cadenaPrincipal + 'Modificacion').value;

    // Ponemos todos los datos de los input del cuadro de añadir en sus espacios correspondientes, comprobando que no sea un dato de 
    //      interés lo que se ha modificado, porque, si es un dato de interés, solo hay que obtener 1 dato
    document.querySelector('#' + idArticulo + ' .tituloElemento').textContent = tituloAnadir.trim();
    if(cadenaPrincipal != 'Datos'){
        document.querySelector('#' + idArticulo + ' .centroElemento').textContent = centroAnadir.trim();
        document.querySelector('#' + idArticulo + ' .fechaInicioElemento').textContent = formateaFechasGuiones(fechaInicioAnadir.trim());
        document.querySelector('#' + idArticulo + ' .fechaFinElemento').textContent = condicionActualAnadir ? 'Actualidad' : 
            formateaFechasGuiones(fechaFinAnadir.trim());
    
    }

    // Mostramos el artículo del dato que se ha modificado
    document.querySelector('#' + idArticulo).removeAttribute('style');
    let hrArticulo = obtieneLineaAnteriorArticuloModificacionBorrado(document.querySelector('#' + idArticulo));
    hrArticulo.removeAttribute('style');

    // Reseteamos todos los campos del cuadro de añadir
    reseteaCamposOcultacionCuadroAnadir(cadenaPrincipal);
    
    // Aplicamos el texto del botón de Añadir datos
    document.querySelector('#anadir' + cadenaPrincipal).textContent = 'Añadir ' + cadenaPrincipal.toLowerCase();

    // Comprobamos que no se este modificando un dato de interés
    if(cadenaPrincipal != 'Datos'){

        // Ordenamos el array que contiene a todos los artículos de los datos que se desean modificar
        arrayDatosAnadidos = reordenaElementosAnadidos(Array.from(document.querySelectorAll('.' + cadenaPrincipal.toLowerCase() + 'Anadido')));

        // Recorremos el array
        arrayDatosAnadidos.forEach(e => {

            // Borramos el artículo y la línea anterior a este
            obtieneLineaAnteriorArticuloModificacionBorrado(e).remove();
            e.remove();

            // Añadimos el elemento al DOM
            creaElementoCompleto('hr', document.querySelector('#articulo' + cadenaPrincipal + 'Anadidos'));
            document.querySelector('#articulo' + cadenaPrincipal + 'Anadidos').appendChild(e)
        });
    }
}


/**
 * Función que permite ordenar un array de estudios, experiencia o idiomas por la fecha de finalización de estos.
 * @param {Array} arrayElementos Array de elementos a ordenar.
 * @returns Array ordenado.
 */
function reordenaElementosAnadidos(arrayElementos){

    // Ordenamos el array según un criterio de ordenación propio
    arrayElementos.sort((a, b) => {

        // Si la fecha de finalización de uno de los elementos contiene la palabra 'Actualidad', ordenamos dependiendo de cual sea
        //      en la que pone dicha palabra, y si los 2 terminan al mismo tiempo, ordenamos por fecha de inicio
        // Si las fechas de finalización de ambos elementos son distintas de 'Actualidad', comprobamos cual sucede antes y cual después, y si
        //      los 2 terminan al mismo tiempo, ordenamos por fecha de inicio.
        if(a.querySelector('.fechaFinElemento').textContent == 'Actualidad' || b.querySelector('.fechaFinElemento').textContent == 'Actualidad'){
            if(a.querySelector('.fechaFinElemento').textContent == 'Actualidad' && b.querySelector('.fechaFinElemento').textContent == 'Actualidad'){

                // Comprobamos la fecha de inicio de los elementos, para devolver un número u otro que indique que los elementos estan 
                //      ordenados por su fecha de inicio
                if(new Date(formateaFechasBarras(a.querySelector('.fechaInicioElemento').textContent)) > 
                    new Date(formateaFechasBarras(b.querySelector('.fechaInicioElemento').textContent))){
                    return 1;
                }else if(new Date(formateaFechasBarras(a.querySelector('.fechaInicioElemento').textContent)) < 
                    new Date(formateaFechasBarras(b.querySelector('.fechaInicioElemento').textContent))){
                    return -1;
                }else{
                    return 0;
                }
            }else if(a.querySelector('.fechaFinElemento').textContent == 'Actualidad'){
                return 1;
            }else{
                return -1;
            }
        }else{
            if(new Date(formateaFechasBarras(a.querySelector('.fechaFinElemento').textContent)) > 
                new Date(formateaFechasBarras(b.querySelector('.fechaFinElemento').textContent))){
                return 1;
            }else if(new Date(formateaFechasBarras(a.querySelector('.fechaFinElemento').textContent)) < 
                new Date(formateaFechasBarras(b.querySelector('.fechaFinElemento').textContent))){
                return -1;
            }else{

                // Comprobamos la fecha de inicio de los elementos, para devolver un número u otro que indique que los elementos estan 
                //      ordenados por su fecha de inicio
                if(new Date(formateaFechasBarras(a.querySelector('.fechaInicioElemento').textContent)) > 
                    new Date(formateaFechasBarras(b.querySelector('.fechaInicioElemento').textContent))){
                    return 1;
                }else if(new Date(formateaFechasBarras(a.querySelector('.fechaInicioElemento').textContent)) < 
                    new Date(formateaFechasBarras(b.querySelector('.fechaInicioElemento').textContent))){
                    return -1;
                }else{
                    return 0;
                }
            }
        }
    });

    // Retornamos el array
    return arrayElementos;
}

/**
 * Función que permite gestionar el evento de modificación de un dato.
 * @param {MouseEvent} e Evento que se genera al pulsar sobre el botón de modificación.
 * @param {string} cadenaPrincipal Cadena que permite identificar el tipo de dato a modificar.
 */
function gestionaModificacionDato(e, cadenaPrincipal){

    // Obtenemos el id del dato a modificar y de la línea que hay antes del dato a modificar
    let articuloModificar = e.target.parentNode.parentNode;
    let idArticuloModificar = articuloModificar.getAttribute('id');
    let hrArticuloModificar = obtieneLineaAnteriorArticuloModificacionBorrado(articuloModificar);

    // Obtenemos los datos del dato que se desea modificar, comprobando si es un dato de interés o no
    let tituloModificar = document.querySelector('#' + idArticuloModificar + ' .tituloElemento').textContent;
    let centroModificar;
    let fechaInicioModificar;
    let fechaFinModificar;
    if(cadenaPrincipal != 'Datos'){
        centroModificar = document.querySelector('#' + idArticuloModificar + ' .centroElemento').textContent;
        fechaInicioModificar = document.querySelector('#' + idArticuloModificar + ' .fechaInicioElemento').textContent;
        fechaFinModificar = document.querySelector('#' + idArticuloModificar + ' .fechaFinElemento').textContent;
    }

    // Ocultamos el artículo a modificar y la línea que hay antes de este
    articuloModificar.setAttribute('style', 'display: none;');
    hrArticuloModificar.setAttribute('style', 'display: none');

    // Añadimos los datos del dato a modificar en los inputs del cuadro de añadir, comprobando si es un dato de interés o no
    //      lo que se desea modificar, y si tiene como fecha de fin 'Actualidad'
    document.querySelector('#tituloAnade' + cadenaPrincipal).value = tituloModificar;
    if(cadenaPrincipal != 'Datos'){
        document.querySelector('#centroAnade' + cadenaPrincipal).value = centroModificar;
        document.querySelector('#fechaInicioAnade' + cadenaPrincipal).value = formateaFechasBarras(fechaInicioModificar);
        if(fechaFinModificar != 'Actualidad'){
            document.querySelector('#fechaFinAnade' + cadenaPrincipal).value = formateaFechasBarras(fechaFinModificar);
        }else{
            document.querySelector('#fechaFinAnade' + cadenaPrincipal).setAttribute('disabled', 'disabled');
            document.querySelector('#btn' + cadenaPrincipal + 'Actuales').setAttribute('style', 'font-weight: bolder;');
        }
    }

    // Mostramos el cuadro de añadir y el botón de descartar cambios
    document.querySelector('#cuadroAnadir' + cadenaPrincipal).setAttribute('style', 'display: block;');
    document.querySelector('#descartarCambiosAnade' + cadenaPrincipal).setAttribute('style', 'display: inline-block;');

    // Ponemos el texto del botón de confirmar cambios y de la cabecera del cuadro de añadir
    document.querySelector('#anadir' + cadenaPrincipal).textContent = 'Confirmar cambios';
    document.querySelector('#cuadroAnadir' + cadenaPrincipal + ' > header > h3').textContent = 'Modificar ' + cadenaPrincipal.toLowerCase();

    // Ponemos en el input de tipo hidden el dato a modificar
    document.querySelector('#id' + cadenaPrincipal + 'Modificacion').value = idArticuloModificar;

    // Ocultamos el enlace para añadir nuevos datos
    document.querySelector('#enlaceAnadir' + cadenaPrincipal).setAttribute('style', 'display: none;');

}

/**
 * Función que permite obtener la línea que esta justo antes de los estudios, experiencia, idiomas o datos de interés añadidos.
 * @param {Element} articulo Artículo que esta justo después de la línea que se desea obtener
 * @returns Elemento que contiene la línea que se desea obtener.
 */
function obtieneLineaAnteriorArticuloModificacionBorrado(articulo){

    // Obtenemos el elemento anterior al artículo
    let hr = articulo.previousSibling;

    // Mientras que no sea un <hr>, seguimos obteniendo los elementos anteriores
    while(hr.nodeName != 'HR'){
        hr = hr.previousSibling;
    }

    // Devolvemos el elemento obtenido
    return hr;
}

/**
 * Función que permite gestionar el evento de borrado de un dato.
 * @param {MouseEvent} e Evento que se genera al pulsar sobre el botón de borrado.
 */
function gestionaBorradoDato(e){

    // Obtenemos el artículo a borrar, y la línea anterior a este
    let articuloBorrar = e.target.parentNode.parentNode;
    let hrArticuloBorrar = obtieneLineaAnteriorArticuloModificacionBorrado(articuloBorrar);

    // Borramos el artículo y la línea
    articuloBorrar.remove();
    hrArticuloBorrar.remove();
}

/**
 * Función que permite resetear todos los campos involucrados en la ocultación del cuadro de añadir estudios, experiencias, idiomas o datos de
 *      interés.
 * @param {string} cadenaPrincipal Cadena que permite identificar los datos a ocultar.
 */
function reseteaCamposOcultacionCuadroAnadir(cadenaPrincipal){

    // Reseteamos los valores de los inputs del cuadro de añadir, comprobando si se va a resetear el cuadro de añadir datos de interés o alguno de
    //      los otros.
    document.querySelector('#tituloAnade' + cadenaPrincipal).value = '';
    if(cadenaPrincipal != 'Datos'){
        document.querySelector('#centroAnade' + cadenaPrincipal).value = '';
        document.querySelector('#fechaInicioAnade' + cadenaPrincipal).value = '';
        document.querySelector('#fechaFinAnade' + cadenaPrincipal).value = '';
    
        // Si el botón de estudios actuales esta activado, lo desactivamos, además de habilitar el input
        //      de la fecha de fin de estudios.
        if(document.querySelector('#btn' + cadenaPrincipal + 'Actuales').hasAttribute('style')){
            habilitaInputFechaFin(cadenaPrincipal);
        }
    }

    // Ocultamos el mensaje de error y el cuadro de agregaciones, además de cambiar el texto del enlace que permite mostrarlo.
    document.querySelector('#mensajeErrorAnade' + cadenaPrincipal,).removeAttribute('style');
    document.querySelector('#cuadroAnadir' + cadenaPrincipal).removeAttribute('style');
    document.querySelector('#descartarCambiosAnade' + cadenaPrincipal).removeAttribute('style');
    document.querySelector('#enlaceAnadir' + cadenaPrincipal).removeAttribute('style');
    document.querySelector('#enlaceAnadir' + cadenaPrincipal).textContent = '+ Agregar ' + cadenaPrincipal.toLowerCase();

    // Volvemos a poner visibles los botones de modificación y borrado de datos
    document.querySelectorAll(cadenaPrincipal.toLowerCase() + 'Anadido > article:last-child').forEach(e => e.removeAttribute('style'));
}

/**
 * Función que permite habilitar el input de fecha de fin del cuadro de agregación, además de eliminar el estilo del botón de 'Actualidad'.
 * @param {string} cadenaPrincipal Cadena que permite identificar el dato que se va a habilitar
 */
function habilitaInputFechaFin(cadenaPrincipal){

    // Quitamos el estilo al botón de 'Actualidad'
    document.querySelector('#btn' + cadenaPrincipal + 'Actuales').removeAttribute('style');

    // Habilitamos el input de fecha de fin
    document.querySelector('#fechaFinAnade' + cadenaPrincipal).removeAttribute('disabled');
}

/**
 * Función que permite mostrar el mensaje deseado de error en el cuadro de agregación.
 * @param {string} texto Mensaje de error.
 * @param {string} id Id del elemento donde se mostrará el mensaje de error.
 */
function muestraMensajeErrorAgregaciones(texto, id){

    // Ponemos el mensaje de error
    document.querySelector(id).textContent = texto;

    // Mostramos el mensaje de error
    document.querySelector(id).setAttribute('style', 'display: block;');
}

/**
 * Función que permite comprobar si una fecha es correcta por varios criterios: Si es un año posterior al actual, si es un
 *      mes incorrecto, si es un día incorrecto según el mes, teniendo en cuenta años bisiestos, y si es una fecha posterior
 *      a la actual.
 * @param {string} fecha Fecha a revisar, con el formato YYYY-MM-DD.
 * @returns true si la fecha es válida, y false en caso contrario.
 */
function compruebaFechasIntroducidas(fecha){
    let arrayFecha = fecha.split('-');

    // Comprobación del año
    if(parseInt(arrayFecha[0]) > new Date(Date.now()).getFullYear()){
        return false;
    }

    // Comprobación del mes
    if(parseInt(arrayFecha[1]) > 12 || parseInt(arrayFecha[1]) < 0){
        return false;
    }

    // Comprobación del día, según mes y año, este último solo en años bisiestos
    if(parseInt(arrayFecha[1]) == 1 || parseInt(arrayFecha[1]) == 3 || parseInt(arrayFecha[1]) == 5 || parseInt(arrayFecha[1]) == 7 
        || parseInt(arrayFecha[1]) == 8 || parseInt(arrayFecha[1]) == 10 || parseInt(arrayFecha[1]) == 12){
        
        // Mes de 31 días
        if(parseInt(arrayFecha[2]) < 0 || parseInt(arrayFecha[2]) > 31){
            return false;
        }
    }else if(parseInt(arrayFecha[1]) == 4 || parseInt(arrayFecha[1]) == 6 || parseInt(arrayFecha[1]) == 9 || parseInt(arrayFecha[1]) == 11){
    
        // Mes de 30 días
        if(parseInt(arrayFecha[2]) < 0 || parseInt(arrayFecha[2]) > 30){
            return false;
        }
    }else if(parseInt(arrayFecha[1]) == 2){

        // Mes de 28 o 29 días
        let numeroDias = 28;

        // Comprobación de año bisiesto
        if((parseInt(arrayFecha[0]) % 100 != 0 && parseInt(arrayFecha[0]) % 4 == 0) || parseInt(arrayFecha[0]) % 400 == 0){
            numeroDias = 29;
        }

        if(parseInt(arrayFecha[2]) < 0 || parseInt(arrayFecha[2]) > numeroDias){
            return false;
        }
    }

    // Comprobación de si la fecha es posterior a la actual
    if(Date.parse(fecha) > Date.now()){
        return false;
    }

    // Si todo es correcto, retornamos true
    return true;
}

/**
 * Función que permite formatear una fecha en formato DD/MM/YYYY a formato YYYY-MM-DD.
 * @param {string} fecha Fecha a formatear.
 * @returns Fecha formateada.
 */
function formateaFechasBarras(fecha){

    // Hacemos un split para separar los elementos de la fecha
    let arrayFecha = fecha.split('/');

    // Creamos la fecha formateada, poniendola de inicio el año
    let fechaFormateada = arrayFecha[2] + "-";

    // Si el mes es menor de 10, le añadimos un 0 delante, y lo agregamos a la fecha formateada
    // Si el mes es igual o mayor de 10, lo agregamos a la fecha formateada
    if(arrayFecha[1].length == 1 && parseInt(arrayFecha[1]) < 10){
        fechaFormateada += '0' + arrayFecha[1] + '-';
    }else{
        fechaFormateada += arrayFecha[1] + '-';
    }

    // Si el dia es menor de 10, le añadimos un 0 delante, y lo agregamos a la fecha formateada
    // Si el dia es igual o mayor de 10, lo agregamos a la fecha formateada
    if(arrayFecha[0].length == 1 && parseInt(arrayFecha[0]) < 10){
        fechaFormateada += '0' + arrayFecha[0];
    }else{
        fechaFormateada += arrayFecha[0];
    }

    // Retornamos la fecha formateada
    return fechaFormateada;
}

/**
 * Función que permite formatear una fecha en formato YYYY-MM-DD a formato DD/MM/YYYY.
 * @param {string} fecha Fecha a formatear.
 * @returns Fecha formateada.
 */
function formateaFechasGuiones(fecha){

    // Hacemos un split para separar los elementos de la fecha
    let arrayFecha = fecha.split('-');

    // Creamos la fecha formateada, poniendola de inicio el año
    let fechaFormateada = arrayFecha[2] + "/";

    // Si el mes es menor de 10, le añadimos un 0 delante, y lo agregamos a la fecha formateada
    // Si el mes es igual o mayor de 10, lo agregamos a la fecha formateada
    if(arrayFecha[1].length == 1 && parseInt(arrayFecha[1]) < 10){
        fechaFormateada += '0' + arrayFecha[1] + '/';
    }else{
        fechaFormateada += arrayFecha[1] + '/';
    }

    // Si el dia es menor de 10, le añadimos un 0 delante, y lo agregamos a la fecha formateada
    // Si el dia es igual o mayor de 10, lo agregamos a la fecha formateada
    if(arrayFecha[0].length == 1 && parseInt(arrayFecha[0]) < 10){
        fechaFormateada += '0' + arrayFecha[0];
    }else{
        fechaFormateada += arrayFecha[0];
    }

    // Retornamos la fecha formateada
    return fechaFormateada;
}

/**
 * Función que permite gestionar el evento de submit del formulario de creación de curriculum.
 * @param {SubmitEvent} e Evento generado al dar al botón de submit
 */
function enviaContenidoCurriculum(e){

    // Impedimos el evento por defecto
    e.preventDefault();

    // Obtenemos los estudios, experiencia, idiomas y datos añadidos
    let estudiosAnadidos = document.querySelectorAll('.estudioAnadido');
    let experienciaAnadidas = document.querySelectorAll('.experienciaAnadido');
    let idiomasAnadidos = document.querySelectorAll('.idiomaAnadido');
    let datosAnadidos = document.querySelectorAll('.datosAnadido');

    // Deshabilitamos toda la página
    deshabilitaPaginaEntera();

    // Comprobamos todos los inputs de los datos personales y los estudios, experiencias, idiomas y datos añadidos antes de hacer nada
    if(compruebaInputsFormularioYDatosNoPersonales(e, estudiosAnadidos, experienciaAnadidas, idiomasAnadidos, datosAnadidos)){

        // Mostramos la ventana modal de 'Cargando...'
        mostradoVentanaModalCargando();

        // Creamos un objeto para almacenar todos los datos que se mandarán al servidor
        let formData = new FormData();

        // Guardamos los datos personales
        formData.append('foto', document.querySelector('#fotoCurriculum').getAttribute('src'));
        formData.append('nombre', e.target['idNombre'].value.trim());
        formData.append('apellidos', e.target['idApellidos'].value.trim());
        formData.append('telefono', e.target['idTelefono'].value.trim());
        formData.append('fechaNac', e.target['idFechaNac'].value.trim());
        formData.append('direccion', e.target['idDireccion'].value.trim());
        formData.append('correo', e.target['idCorreo'].value.trim());
        formData.append('whatsapp', e.target['idWhatsapp'].value.trim());
        formData.append('linkedin', e.target['idLinkedIn'].value.trim());

        // Guardamos los estudios, experiencia, idiomas y datos de interes
        formData.append('estudios', JSON.stringify(obtieneDatosNoPersonalesCurriculum(estudiosAnadidos)));
        formData.append('experiencia', JSON.stringify(obtieneDatosNoPersonalesCurriculum(experienciaAnadidas)));
        formData.append('idiomas', JSON.stringify(obtieneDatosNoPersonalesCurriculum(idiomasAnadidos)));
        formData.append('datosInteres', JSON.stringify(obtieneDatosNoPersonalesCurriculum(datosAnadidos)));

        // Hacemos una petición fetch para subir los datos del currículum al servidor
        generaPeticionAjax('SubidaDatosCurriculum', (data) => {
            console.log(data);

            // Si se completa la petición correctamente, redirigimos a la ruta que se ha recibido del servidor.
            // Si se completa un mensaje de error con la foto, es que ha habido un error al obtener la foto del currículum.
            // Si se recibe un mensaje de que ha habido un error en la validación de datos, mostramos un mensaje de error.
            // Si se recibe cualquier otro mensaje, mostramos un mensaje de error
            if(data.mensaje == 'Completado'){
                window.location = data.ruta;
            }else if(data.mensaje == 'Error en la obtención de la foto'){
                muestraVentanaModalError('al obtener la foto indicada para el currículum.');
            }else if(data.mensaje == 'Error en la validación de datos'){
                muestraVentanaModalParrafoYBoton(data.mensajeDetallado, 'Volver');
            }else{
                muestraVentanaModalError('al cargar la página.');
            }
        }, formData);
    }
}

/**
 * Función que permite comprobar todos los inputs de los datos personales, además de comprobar los datos no personales (estudios, experiencia,
 *      idiomas y datos de interés).
 * @param {SubmitEvent} e Objeto del evento de submit del formulario, que contiene todos los inputs de los datos personales.
 * @param {Array} estudiosAnadidos Array que contiene los estudios que se han añadido.
 * @param {Array} experienciaAnadidas Array que contiene las experiencias laborales que se han añadido.
 * @param {Array} idiomasAnadidos Array que contiene los idiomas que se han añadido.
 * @param {Array} datosAnadidos Array que contiene los datos de interés que se han añadido.
 * @returns true si se han cumplido las restricciones, y false en caso contrario
 */
function compruebaInputsFormularioYDatosNoPersonales(e, estudiosAnadidos, experienciaAnadidas, idiomasAnadidos, datosAnadidos){

    // Bandera que sirve para saber si se cumplen o no las restricciones
    let bandera = true;

    // Vamos comprobando las restricciones de los inputs y de los arrays y elementos pasados, mostrando un mensaje de error y 
    //      cambiando el valor de la bandera si no se cumplen dichas restricciones
    if(document.querySelector('#fotoCurriculum').getAttribute('src') == 'assets/img/fotoInicialCurriculum.png/' || e.target['idNombre'].value == '' || 
        e.target['idApellidos'].value == '' || e.target['idTelefono'].value == '' || e.target['idFechaNac'].value == '' || 
        e.target['idDireccion'].value == '' || e.target['idCorreo'].value == ''){

        // Campos obligatorios no rellenados
        muestraVentanaModalParrafoYBoton('Rellene todos los campos obligatorios con datos válidos.', 'Volver');
        bandera = false;
    }else if(e.target['idNombre'].value.length > 30){

        // Nombre con longitud incorrecta
        muestraVentanaModalParrafoYBoton('El nombre no puede ocupar más de 30 caracteres.', 'Volver');
        bandera = false;
    }else if(e.target['idApellidos'].value.length > 50){
        
        // Apellidos con longitud incorrecta
        muestraVentanaModalParrafoYBoton('Los apellidos no pueden ocupar más de 50 caracteres.', 'Volver');
        bandera = false;
    }else if(e.target['idTelefono'].value.length != 9){
        
        // Teléfono con longitud incorrecta
        muestraVentanaModalParrafoYBoton('El teléfono debe ocupar 9 caracteres.', 'Volver');
        bandera = false;
    }else if(!compruebaFechasIntroducidas(e.target['idFechaNac'].value)){

        // Se indicado una fecha de nacimiento que no es válida
        muestraVentanaModalParrafoYBoton('La fecha de nacimiento no es correcta.', 'Volver');
        bandera = false;
    }
    else if(Date.parse(e.target['idFechaNac'].value) < Date.parse('1960-01-01') || 
        Date.parse(e.target['idFechaNac'].value) > Date.parse(e.target['idFechaNac'].getAttribute('max'))){

        // Se indicado una fecha de nacimiento que no está en el rango adecuado
        muestraVentanaModalParrafoYBoton('La fecha de nacimiento debe estar entre el 1/1/1960 y el ' + 
            new Date(e.target['idFechaNac'].getAttribute('max')).toLocaleDateString()  + '.', 'Volver');
        bandera = false;
    }else if(e.target['idDireccion'].value.length < 10 || e.target['idDireccion'].value.length > 180){
        
        // Dirección con longitud incorrecta
        muestraVentanaModalParrafoYBoton('La dirección debe ocupar entre 10 y 180 caracteres.', 'Volver');
        bandera = false;
    }else if(e.target['idCorreo'].value.length < 15 || e.target['idCorreo'].value.length > 120){
        
        // Correo con longitud incorrecta
        muestraVentanaModalParrafoYBoton('El correo debe ocupar entre 15 y 120 caracteres.', 'Volver');
        bandera = false;
    }else if(e.target['idWhatsapp'].value != '' && e.target['idWhatsapp'].value.length != 9){

        // Whatsapp con longitud incorrecta, si es que se ha indicado
        muestraVentanaModalParrafoYBoton('El teléfono del Whatsapp debe ocupar 9 caracteres.', 'Volver');
        bandera = false;
    }else if(e.target['idLinkedIn'].value != '' && (e.target['idLinkedIn'].value.length < 28 || e.target['idLinkedIn'].value.length > 150)){
        
        // LinkedIn con longitud incorrecta, si es que se ha indicado
        muestraVentanaModalParrafoYBoton('El enlace de Linkedin debe ocupar entre 28 y 150 caracteres.', 'Volver');
        bandera = false;
    }else if(!/^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)?$/.test(e.target['idNombre'].value)){
        
        // Nombre con patrón incorrecto
        muestraVentanaModalParrafoYBoton('El nombre no tiene un formato válido.', 'Volver');
        bandera = false;
    }else if(!/^(de\s(la\s)?|del\s)?[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+$/.test(e.target['idApellidos'].value)){
        
        // Apellidos con patrón incorrecto
        muestraVentanaModalParrafoYBoton('Los apellidos no tienen un formato válido.', 'Volver');
        bandera = false;
    }else if(!/^\d{9}$/.test(e.target['idTelefono'].value)){
        
        // Teléfono con patrón incorrecto
        muestraVentanaModalParrafoYBoton('El teléfono no tiene un formato válido.', 'Volver');
        bandera = false;
    }else if(!/^(C\/|Avda\.|Plaza)\s(((de(\s(la|las|los))?\s|del\s|los\s|las\s)?[A-ZÁÉÍÓÚÑ][a-záéíóúñ]{3,})\s?)+,\s\d{1,3}(,\s\d{1,2}º\s[A-Z])?$/.test
        (e.target['idDireccion'].value)){
        
        // Dirección con patrón incorrecto
        muestraVentanaModalParrafoYBoton('La dirección no tiene un formato válido.', 'Volver');
        bandera = false;
    }else if(!/^[\wÁÉÍÓÚÑáéíóúñ\.]+@[a-z]+(\.(es|com)){1,2}$/.test(e.target['idCorreo'].value)){
        
        // Correo con patrón incorrecto
        muestraVentanaModalParrafoYBoton('El correo no tiene un formato válido.', 'Volver');
        bandera = false;
    }else if(e.target['idWhatsapp'].value != '' && !/^\d{9}$/.test(e.target['idWhatsapp'].value)){
        
        // Whatsapp con patrón incorrecto, si es que se ha indicado
        muestraVentanaModalParrafoYBoton('El teléfono del Whatsapp no tiene un formato válido.', 'Volver');
        bandera = false;
    }else if(e.target['idLinkedIn'].value != '' && !/^https:\/\/www.linkedin.com\/in\/[a-z\-]+\/$/.test(e.target['idLinkedIn'].value)){
        
        // LinkedIn con patrón incorrecto, si es que se ha indicado
        muestraVentanaModalParrafoYBoton('El enlace de Linkedin no tiene un formato válido.', 'Volver');
        bandera = false;
    }else if(estudiosAnadidos.length == 0 && experienciaAnadidas.length == 0 && idiomasAnadidos.length == 0 && datosAnadidos.length == 0){

        // No se han indicado estudios, experiencia, idiomas ni datos de interés
        muestraVentanaModalDosParrafosYBoton('No ha indicado ningún dato aparte de los datos personales.', 
            'Debe indicar algún estudio, experiencia, idioma y/o dato de interes para poder crear su curriculum.', 'Volver');
        bandera = false;
    }else if(document.querySelector('#cuadroAnadirEstudio').hasAttribute('style') || document.querySelector('#cuadroAnadirExperiencia').hasAttribute('style') 
        || document.querySelector('#cuadroAnadirIdioma').hasAttribute('style') || document.querySelector('#cuadroAnadirDatos').hasAttribute('style')){
        
        // Se ha dejado algún cuadro de añadir o modificar abierto
        muestraVentanaModalDosParrafosYBoton('Ha dejado alguna adicción o modificación de datos sin terminar.', 
            'Por favor, terminelas antes de seguir.', 'Volver');
        bandera = false;
    }

    // Retornamos la bandera
    return bandera;
}

/**
 * Función que permite obtener los estudios, experiencia, idiomas o datos de interes en el formato deseado para mandarlos
 *      al servidor.
 * @param {Array} arrayRecorrer Array que se va a recorrer.
 * @returns Array en el formato deseado para mandar al servidor.
 */
function obtieneDatosNoPersonalesCurriculum(arrayRecorrer){

    // Array donde se guardarán los datos
    let arrayDatos = [];

    // Recorremos el array recibido por parámetro
    arrayRecorrer.forEach(e => {

        // Si el elemento a leer tiene un dato de 'centro', guardamos los datos en el array de una manera, ya que significa
        //      que se esta recorriendo un estudio, experiencia o idioma
        // Si el elemento a leer no tiene un dato de 'centro', guardamos los datos en el array de otra manera, ya que 
        //      significa que se esta recorriendo un dato de interés
        if(e.querySelector('.centroElemento')){
            arrayDatos.push({
                'dato1':e.querySelector('.tituloElemento').textContent,
                'dato2': e.querySelector('.centroElemento').textContent,
                'dato3': formateaFechasBarras(e.querySelector('.fechaInicioElemento').textContent),
                'dato4': e.querySelector('.fechaFinElemento').textContent == 'Actualidad' ? 'Actualidad' : 
                    formateaFechasBarras(e.querySelector('.fechaFinElemento').textContent)
            });
        }else{
            arrayDatos.push({
                'dato1':e.querySelector('.tituloElemento').textContent
            });
        }
    });

    // Retornamos el array de datos
    return arrayDatos;
}

/**
 * Función que permite recorrer los botones de 'Actualidad' de los cuadros de estudio, experiencia laboral e idiomas,
 *         y deshabilitar los inputs de fecha de fin asociados a estos si es que los botones estan activados.
 */
function deshabilitaInputsFechaFinSiActualidad(){

    // Creamos un array y lo recorremos para poder obtener los botones de 'Actualidad' y los campos de la fecha de fin
    ['Estudio', 'Experiencia', 'Idioma'].forEach(el => {

        // Si el botón de 'Actualidad' posee un atributo de estilo, es que esta marcado, por lo que debemos deshabilitar
        //      el input de fecha de fin asociado al botón, además de poner el valor por defecto de este
        if(document.querySelector('#btn' + el + 'Actuales').hasAttribute('style')){
            document.querySelector('#fechaFinAnade' + el).setAttribute('disabled', 'disabled');
            document.querySelector('#fechaFinAnade' + el).value = '';
        }
    });
}