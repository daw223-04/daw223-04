// Gestionamos el evento de click del icono de la persona de la barra superior
document.querySelector('.bi-person').addEventListener('click', () => aplicaPosicionCuadroIniciaSesion(true));

// Gestionamos el evento de redimensionamiento de la ventana
window.addEventListener('resize', () => aplicaPosicionCuadroIniciaSesion(false));

// Gestionamos el evento de click del botón del cuadro de inicio de sesión
document.querySelector('#botonCuadroIniciaSesion').addEventListener('click', function(e){

    // Si el botón tiene el texto de 'Iniciar sesión', redirigimos a la URL del input oculto del cuadro de inicio de 
    //      sesión
    // Si el botón tiene el texto de 'Cerrar sesión', hacemos una petición AJAX para poder eliminar la sesión, y
    //      redirigimos a la URL que recibamos en el mensaje
    // Si se recibe cualquier otro mensaje, mostramos un mensaje de error
    if(e.target.textContent == 'Iniciar sesión'){
        window.location = 'http://' + ip_pub + '/InicioSesion';
    }
    else if(e.target.textContent == 'Cerrar sesión'){
        generaPeticionAjax('CierreSesion', (data) => {
            console.log(data);
            if(data.mensaje == 'Completado'){
                window.location = data.ruta;
            }else{
                muestraVentanaModalError('al cerrar sesión.');
            }
        });
    }
});

// Aplicamos el posicionamiento del cuadro de inicio de sesión
aplicaPosicionCuadroIniciaSesion(false);

/**
 * Función que permite aplicar el posicionamiento del cuadro de inicio de sesión.
 * @param {boolean} bandera Bandera que permite indicar si se debe ocultar el cuadro al tener una distribución aplicada,
 *      o si no se debe ocultar.
 */
function aplicaPosicionCuadroIniciaSesion(bandera){

    // Obtenemos el cuadro de inicio de sesión
    let cuadroInicioSesion = document.querySelector('#cuadroIniciaSesion');

    // Obtenemos el ancho de la ventana
    let widthVentana = window.innerWidth;

    // Comprobamos si el cuadro de inicio de sesión tiene un estilo, y si dicho estilo empieza por la palabra 'display'
    if(cuadroInicioSesion.hasAttribute('style') && cuadroInicioSesion.getAttribute('style').startsWith('display')){

        // Si se ha indicado en la bandera true, ocultamos el cuadro y solo lo posicionamos
        // Si se ha indicado en la bandera false, mostramos el cuadro y lo posicionamos
        if(bandera){
            posicionaCuadroIniciaSesion(widthVentana, cuadroInicioSesion);
        }else{
            posicionaYMuestraCuadroIniciaSesion(widthVentana, cuadroInicioSesion);
        }
    }else{

        // Si se ha indicado en la bandera true, mostramos el cuadro y lo posicionamos
        // Si se ha indicado en la bandera false, ocultamos el cuadro y solo lo posicionamos
        if(bandera){
            posicionaYMuestraCuadroIniciaSesion(widthVentana, cuadroInicioSesion);
        }else{
            posicionaCuadroIniciaSesion(widthVentana, cuadroInicioSesion);
        }
    }
}

/**
 * Función que permite posicionar el cuadro de inicio de sesión en la página.
 * @param {number} widthVentana Ancho de la ventana donde se esta mostrando la página.
 * @param {Element} cuadroIniciaSesion Elemento que representa el cuadro de inicio de sesión.
 */
function posicionaCuadroIniciaSesion(widthVentana, cuadroIniciaSesion){

    // Si el alto del cuerpo de la página es mayor que el de la pestaña, posicionamos la ventana un 
    //      poco más a la izquierda
    // Si el alto del cuerpo de la página es menor o igual que el de la pestaña, posicionamos la 
    //      ventana un poco más a la derecha
    if(document.querySelector('body').clientHeight > window.innerHeight){

        // Dependiendo de en que sistema se este mostrando la página, y, en determinados casos, también en función del navegador,
        //      posicionamos el cuadro de inicio de sesión más a la derecha o a la izquierda
        if(navigator.userAgent.indexOf('Android') != -1){
            cuadroIniciaSesion.setAttribute('style', 'left: ' + (widthVentana - 253) + 'px;');
        }else if(navigator.userAgent.indexOf('Windows') != -1){
            cuadroIniciaSesion.setAttribute('style', 'left: ' + (widthVentana - 269) + 'px;');
        }else if(navigator.userAgent.indexOf('Linux') != -1){
            if(navigator.userAgent.indexOf('Firefox') != -1){
                cuadroIniciaSesion.setAttribute('style', 'left: ' + (widthVentana - 253) + 'px;');
            }else{
                cuadroIniciaSesion.setAttribute('style', 'left: ' + (widthVentana - 269) + 'px;');
            }
        }else{
            cuadroIniciaSesion.setAttribute('style', 'left: ' + (widthVentana - 253) + 'px;');
        }
    }else{
        cuadroIniciaSesion.setAttribute('style', 'left: ' + (widthVentana - 253) + 'px;');
    }
}

/**
 * Función que permite posicionar y mostrar el cuadro de inicio de sesión en la página
 * @param {number} widthVentana Ancho de la ventana donde se esta mostrando la página.
 * @param {Element} cuadroIniciaSesion Elemento que representa el cuadro de inicio de sesión.
 */
function posicionaYMuestraCuadroIniciaSesion(widthVentana, cuadroIniciaSesion){

    // Si el alto del cuerpo de la página es mayor que el de la pestaña, posicionamos la ventana un 
    //      poco más a la izquierda
    // Si el alto del cuerpo de la página es menor o igual que el de la pestaña, posicionamos la 
    //      ventana un poco más a la derecha
    if(document.querySelector('body').clientHeight > window.innerHeight){

        // Dependiendo de en que sistema se este mostrando la página, y, en determinados casos, también en función del navegador,
        //      posicionamos el cuadro de inicio de sesión más a la derecha o a la izquierda
        if(navigator.userAgent.indexOf('Android') != -1){
            cuadroIniciaSesion.setAttribute('style', 'display: flex; left: ' + (widthVentana - 253) + 'px;');
        }else if(navigator.userAgent.indexOf('Windows') != -1){
            cuadroIniciaSesion.setAttribute('style', 'display: flex; left: ' + (widthVentana - 269) + 'px;');
        }else if(navigator.userAgent.indexOf('Linux') != -1){
            if(navigator.userAgent.indexOf('Firefox') != -1){
                cuadroIniciaSesion.setAttribute('style', 'display: flex; left: ' + (widthVentana - 253) + 'px;');
            }else{
                cuadroIniciaSesion.setAttribute('style', 'display: flex; left: ' + (widthVentana - 269) + 'px;');
            }
        }else{
            cuadroIniciaSesion.setAttribute('style', 'display: flex; left: ' + (widthVentana - 253) + 'px;');
        }
    }else{
        cuadroIniciaSesion.setAttribute('style', 'display: flex; left: ' + (widthVentana - 253) + 'px;');
    }
}