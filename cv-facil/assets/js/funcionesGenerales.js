///////////////////////////
///// FICHERO CON FUNCIONES DE USO GENERAL, QUE SE PRECARGA ANTES DE CARGAR O EJECUTAR CUALQUIER SCRIPT
///////////////////////////

/**
 * Función que permite generar una petición asíncrona al servidor, recibiendo los datos de este en formato JSON.
 * @param {string} url URL a la que se le hará la petición. Se une a la URL de base del programa.
 * @param {Function} funcionDatosObtenidos Función que se ejecutará cuando se obtengan los datos en formato JSON.
 * @param {Object} cuerpoPeticion Objeto con el cuerpo de la petición. Por defecto, se pasa un objeto FormData vacio.
 */
function generaPeticionAjax(url, funcionDatosObtenidos, cuerpoPeticion = new FormData()){
    fetch('http://' + ip_pub + '/' + url, {method: 'POST', body: cuerpoPeticion})
    .then(response => response.json())
    .then(data => funcionDatosObtenidos(data))
    .catch(err => console.log(err));
}

/**
 * Función que permite crear un elemento, e indicar, si se desea, padre, texto y atributos del mismo.
 * @param {string} selector Selector del elemento a crear.
 * @param {Element} padre Elemento padre del elemento que se creara. Si no se quiere poner ninguno, se puede pasar 
 *      undefined para no indicarlo (es el valor por defecto).
 * @param {string} texto Texto del elemento que se creará. Si no se quiere poner texto, se puede pasar una cadena 
 *      vacia para no ponerlo (es el valor por defecto).
 * @param {Array} atributos Array asociativo que contendrá los atributos del elemento a crear. Si no se quiere indicar 
 *      ningún atributo, se puede pasar un array vacio, como este: {} (es el valor por defecto).
 * @param {Function} funcionClick Funcion que se aplicará al evento de click del elemento. Si no se quiere indicar nada, se 
 *      puede pasar una cadena vacia (es el valor por defecto)
 * @returns Elemento creado, y con todos los datos deseados.
 */
function creaElementoCompleto(selector, padre = undefined, texto = '', atributos = {}, funcionClick = ''){

    // Creación del elemento
    let element = document.createElement(selector);

    // Aplicación del texto
    if(texto != ''){
        element.textContent = texto;
    }

    // Aplicación de los atributos
    for(let clave in atributos){
        element.setAttribute(clave, atributos[clave]);
    }

    // Agregación al DOM
    if(padre != undefined){
        padre.appendChild(element);
    }

    // Aplicación del evento de click
    if(funcionClick != ''){
        element.addEventListener('click', funcionClick);
    }

    // Retorno del elemento
    return element;
}

/**
 * Función que permite mostrar la ventana modal, y todos los elementos que queramos que estan dentro de ella, a los cuales 
 *      podemos indicar si queremos que tengan un texto dentro.
 * @param {Array} arrayDatos Array asociativo, que contiene las claves de los elementos que se mostrarán y el texto que se
 *      pondrá en cada uno de ellos. Si no se desea poner texto en algun elemento, se puede pasar un texto vacío.
 */
function muestraVentanaModal(arrayDatos){

    // Recorremos el array recibido
    for(let dato in arrayDatos){

        // Si se ha recibido un input, lo mostramos con el texto recibido como valor del input
        // Si se ha recibido texto en el elemento a mostrar, mostramos el elemento con el texto pasado
        // Si no se ha recibido texto en el elemento a mostrar, hay 2 opciones:
        //      1-Si el elemento es un ARTICLE, se pone una distribución flex
        //      2-Si el elemento no es un ARTICLE, se pone una distribución block
        if(document.querySelector(dato).nodeName == 'INPUT'){
            document.querySelector(dato).setAttribute('style', 'display: block;');
            document.querySelector(dato).value = arrayDatos[dato];
        }else if(arrayDatos[dato] != ''){
            document.querySelector(dato).textContent = arrayDatos[dato];
            document.querySelector(dato).setAttribute('style', 'display: block;');
        }else if(document.querySelector(dato).nodeName == 'ARTICLE'){
            document.querySelector(dato).setAttribute('style', 'display: flex;');
        }else{
            document.querySelector(dato).setAttribute('style', 'display: block;');
        }
    }

    // Ponemos una distribución flex al artículo de la ventana modal y a la ventana modal
    document.querySelector('#articuloVentanaModal').setAttribute('style', 'display: flex;');
    document.querySelector('#ventanaModal').setAttribute('style', 'display: flex');
}

/**
 * Función que permite ocultar los elementos de la ventana modal, y también la ventana modal si lo deseamos.
 * @param {boolean} ocultacionCompleta Booleano que permite indicar si se quiere ocultar la ventana modal o no.
 */
function ocultaVentanaModal(ocultacionCompleta){

    // Recorremos todos los elementos de la ventana modal, comprobando si no son nodos de texto y si tienen un 
    //      estilo, para quitárselo
    Array.from(document.querySelector('#articuloVentanaModal').childNodes).forEach(e => {
        if(e.nodeName != '#text' && e.getAttribute('style')){
            e.removeAttribute('style');

            // Si el dato que estamos leyendo es un input, reseteamos el valor de este
            if(e.nodeName == 'INPUT'){
                e.value = '';
            }
        }
    });

    // Si se ha indicado que se oculta la ventana modal, le quitamos el estilo
    if(ocultacionCompleta){
        document.querySelector('#ventanaModal').removeAttribute('style');
    }
}

/**
 * Función que permite mostrar la ventana modal de 'Cargando'.
 */
function mostradoVentanaModalCargando(){

    // Ocultamos los posibles elementos de la ventana modal que hubiera antes
    ocultaVentanaModal(false);

    // Indicamos los elementos de la ventana modal que se van a mostrar, con su texto
    let arrayDatosVentanaModal = {
        '#primerParrafoArticuloVentanaModal':'Cargando.',
        '#segundoParrafoArticuloVentanaModal':'Espere un momento...'
    };

    // Mostramos la ventana modal
    muestraVentanaModal(arrayDatosVentanaModal);
}

/**
 * Función que permite mostrar la ventana modal con un parrafo y un botón.
 * @param {string} textoParrafo Texto que aparecerá en el parrafo.
 * @param {string} textoBoton Texto que aparecerá en el botón.
 */
function muestraVentanaModalParrafoYBoton(textoParrafo, textoBoton){

    // Ocultamos los posibles elementos de la ventana modal que hubiera antes
    ocultaVentanaModal(false);

    // Indicamos los datos de la ventana modal a mostrar, con sus textos
    let arrayDatosVentanaModal = {
        '#primerParrafoArticuloVentanaModal':textoParrafo, 
        '#btnPrincipalVentanaModal':textoBoton
    };

    // Mostramos la ventana modal
    muestraVentanaModal(arrayDatosVentanaModal);
}

/**
 * Función que permite mostrar la ventana modal con dos parrafo y un botón.
 * @param {string} textoParrafo1 Texto que aparecerá en el 1º parrafo.
 * @param {string} textoParrafo2 Texto que aparecerá en el 2º parrafo.
 * @param {string} textoBoton Texto que aparecerá en el botón.
 */
function muestraVentanaModalDosParrafosYBoton(textoParrafo1, textoParrafo2, textoBoton){

    // Ocultamos los posibles elementos de la ventana modal que hubiera antes
    ocultaVentanaModal(false);

    // Indicamos los datos de la ventana modal a mostrar, con sus textos
    let arrayDatosVentanaModal = {
        '#primerParrafoArticuloVentanaModal':textoParrafo1, 
        '#segundoParrafoArticuloVentanaModal': textoParrafo2,
        '#btnPrincipalVentanaModal':textoBoton
    };

    // Mostramos la ventana modal
    muestraVentanaModal(arrayDatosVentanaModal);
}

/**
 * Función que permite mostrar una ventana modal con un parrafo y 2 botones
 * @param {string} textoParrafo Texto que aparecerá en el parrafo.
 * @param {string} textoBoton1 Texto que aparecerá en el 1º botón.
 * @param {string} textoBoton2 Texto que aparecerá en el 2º botón.
 */
function muestraVentanaModalParrafoYDosBotones(textoParrafo, textoBoton1, textoBoton2){

    // Ocultamos los posibles elementos de la ventana modal que hubiera antes
    ocultaVentanaModal(false);

    // Indicamos los elementos de la ventana modal que se van a mostrar, con su texto
    let arrayDatosVentanaModal = {
        '#primerParrafoArticuloVentanaModal':textoParrafo,
        '#articuloDosBotonesVentanaModal':'',
        '#primerBotonArticuloDosBotonesVentanaModal':textoBoton1,
        '#segundoBotonArticuloDosBotonesVentanaModal':textoBoton2
    };

    // Mostramos la ventana modal a borrar
    muestraVentanaModal(arrayDatosVentanaModal);
}

/**
 * Función que permite mostrar la ventana modal del mensaje de error, indicando un mensaje que se añadirá
 *      a la frase 'Ha sucedido un error ' para indicar al usuario en que ha sucedido error.
 * @param {string} mensaje Mensaje que se mostrará en la ventana modal, poniendosé detrás de 'Ha sucedido un
 *      error '.
 */
function muestraVentanaModalError(mensaje){
    muestraVentanaModalDosParrafosYBoton('Ha sucedido un error ' + mensaje, 'Por favor, inténtelo más tarde.', 
        'Volver');
}

/**
 * Función que permite ocultar la ventana modal totalmente, además de hacer que todos los inputs de la página se habiliten.
 */
function ocultaVentanaModalCompleta(){
    ocultaVentanaModal(true);
    habilitaPaginaEntera();
}

/**
 * Función que permite mostrar u ocultar el contenido de los inputs de tipo 'password' usados en la
 *      aplicación.
 * @param {MouseEvent} e Evento que se genera al hacer click sobre el icono de ocultar o mostrar contraseña.
 * @param {string} claseExtra Clase que se añadirá a la del icono para generar la clase correcta. Por defecto,
 *      tiene el valor ''.
 */
function muestraOcultaInputsContrasenia(e, claseExtra = ''){

    // Generamos la clase del icono para cuando se ve la contraseña y para cuando se oculta
    let claseVisibleContrasenia = ('bi bi-eye-fill ' + claseExtra).trim();
    let claseOcultaContrasenia = ('bi bi-eye-slash-fill ' + claseExtra).trim();

    // Obtenemos el elemento del icono y del input de la contraseña
    let inputContrasenia = e.target.previousElementSibling;
    let iconoContrasenia = e.target;

    // Si la contraseña tiene la clase oculta, se la cambiamos a la visible, y cambiamos el tipo del input a text
    // Si la contraseña tiene la clase visible, se la cambiamos a la oculta, y cambiamos el tipo del input a password
    if(iconoContrasenia.getAttribute('class') == claseOcultaContrasenia){
        iconoContrasenia.setAttribute('class', claseVisibleContrasenia);
        inputContrasenia.setAttribute('type', 'text');
    }else{
        iconoContrasenia.setAttribute('class', claseOcultaContrasenia);
        inputContrasenia.setAttribute('type', 'password');
    }
}

/**
 * Función que permite redirigir a la página que se indica en el objeto recibido por parámetro.
 * @param {Object} data Objeto que contiene una propiedad, llamada ruta, con la URL a la que se redirigirá al 
 *      usuario.
 */
function redigirePaginaUsuario(data){
    window.location = data.ruta;
}

/**
 * Función que permite deshabilitar todos los inputs, selects, enlaces y botones de la página, menos los botones de la 
 *      ventana modal, ya que estos deben estar siempre disponibles para su uso.
 */
function deshabilitaPaginaEntera(){

    // Deshabilitamos todos los elementos de la página
    deshabilitaDatos('input');
    deshabilitaDatos('select');
    deshabilitaDatos('button');
    document.querySelectorAll('a').forEach(e => e.setAttribute('style', 'pointer-events: none;'));

    // Recorremos el array de botones que no hay que deshabilitar
    ['#btnPrincipalVentanaModal', '#primerBotonArticuloDosBotonesVentanaModal', '#segundoBotonArticuloDosBotonesVentanaModal'].forEach(e => 
        document.querySelector(e).removeAttribute('disabled'));
}

/**
 * Función que permite deshabilitar los inputs indicados por el selector.
 * @param {string} selector Selector que permite obtener todos los inputs deseados.
 */
function deshabilitaDatos(selector){
    document.querySelectorAll(selector).forEach(e => e.setAttribute('disabled', 'disabled'));
}

/**
 * Función que permite habilitar todos los inputs, selects, enlaces y botones de la página.
 */
function habilitaPaginaEntera(){
    habilitaDatos('input');
    habilitaDatos('select');
    habilitaDatos('button');
    document.querySelectorAll('a').forEach(e => e.removeAttribute('style'));
}

/**
 * Función que permite habilitar los inputs indicados por el selector.
 * @param {string} selector Selector que permite obtener todos los inputs deseados.
 */
function habilitaDatos(selector){
    document.querySelectorAll(selector).forEach(e => e.removeAttribute('disabled'));
}