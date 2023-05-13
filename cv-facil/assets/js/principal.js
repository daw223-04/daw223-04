/*Aplicamos un evento de redimensionamiento a la ventana, para así poder calcular dinámicamente la altura del main*/
window.addEventListener('resize', calculaAlturaMain);

/*Calculamos la altura del main*/
calculaAlturaMain();

/**
 * Función que permite calcular la altura del main, restando la altura de la ventana a la de la cabecera de la página, y poniendosela al main.
 */
function calculaAlturaMain(){

    // Calculamos las alturas de la cabecera y de la página
    let heightCabecera = document.querySelector('body > header').clientHeight;
    let heightCuerpo = window.innerHeight;

    // Ponemos la diferencia de altura en el main
    document.querySelector('main').setAttribute('style', 'height: ' + (heightCuerpo - heightCabecera) + 'px');
}