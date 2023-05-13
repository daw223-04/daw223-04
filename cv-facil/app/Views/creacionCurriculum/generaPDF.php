<style>
    /****************************
    *****Estilos puestos directamente en el HTML, ya que si no, el PDF no los pilla
    *****************************/

    /*Estilos que se aplican a todo el HTML*/
    *{
        margin: 0;
        padding: 0;
        font-family: serif;
    }

    /*Estilos que se aplican a la tabla que contiene todos los datos del PDF*/
    table{
        font-size: 1.3em; 
        margin: 10px; 
        padding: 15px;
        width: 97%;
        border-collapse: collapse;
    }

    /*Estilos aplicados a los th de la tabla y a las celdas que contienen los iconos del currículum*/
    th, .icono{
        text-align: left;
    }

    /*Estilos aplicados a la celda que contiene la foto del currículum*/
    .foto{
        width: 240px;
        text-align: center;
    }

    /*Estilos aplicados a las celdas que contienen los iconos del currículum y al icono del correo*/
    .icono, .padding7{
        padding-top: 7px;
    }

    /*Estilos aplicados a las celdas que contienen los iconos del currículum*/
    .icono{
        width: 30px;
    }

    /*Estilos aplicados a la foto del currículum*/
    #imagenCurriculum{
        width: 210px;
        height: 210px;
        border-radius: 100%;
    }

    /*Estilos aplicados al nombre y apellidos del currículum*/
    #nombrePrincipal{
        font-size: 1.5em;
    }

    /*Estilos aplicados a las filas de los datos personales*/
    .filasDatosPersonales{
        height: 28.5px;
    }

    /*Estilos aplicados a los iconos del currículum*/
    .iconosCurriculum{
        width: 20px;
        height: 20px;
    }

    /*Estilos aplicados a las cabeceras de las secciones de Estudios/Experiencia laboral/Idiomas/Datos de interés*/
    .cabeceraListado{
        font-size: 1.4em;
    }

    /*Estilos aplicados a las líneas de separación de los elementos de Estudios/Experiencia laboral/Idiomas/Datos de interés*/
    hr{
        margin: 10px 0;
    }

    /*Estilos aplicados a la fila de corte entre páginas*/
    .filaCorte{
        page-break-after: always; /*Así hacemos que se salte de página al poner esta fila*/
        border: 0;
    }

    /*Estilos aplicados al icono del calendario y del teléfono*/
    .padding3{
        padding-top: 3px;
    }

    /*Estilos aplicados al icono del WhatsApp*/
    .padding5{
        padding-top: 5px;
    }
</style>
<?php 
    // Contador que almacena el número de filas de datos personales insertadas
    $contadorFilasDatosPersonales = 0;
?>
<table>
    <tr>
        <td rowspan="7" class="foto"><img src=<?="'".$foto."'"?> id="imagenCurriculum"/></td>
        <th colspan="2" id="nombrePrincipal"><?=$nombre." ".$apellidos?></th>
    </tr>
    <?php

        // Recorremos el array de iconos
        foreach($arrayIconos as $clave => $valor){

            // Solo creamos filas y columnas si el dato que estamos leyendo del array posee un valor
            if(isset($valor['valor'])){

                // Aumentamos el contadorFilasDatosPersonales
                $contadorFilasDatosPersonales++;
    ?>
                <tr class="filasDatosPersonales">';
                    <td class=<?='"'.$valor['claseColumna'].'"'?>><img src=<?='"data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="'.$valor['claseIcono'].
                        '" viewBox="0 0 16 16">'.$valor['path'].'</svg>').'"'?> class="iconosCurriculum"/>
                    </td>
                    <td><?=$valor['valor']?></td>
                </tr>
    <?php
            }
        }

        // Si el contador no es igual a 6, creamos tantas filas vacias como falten para tener 6 filas
        if($contadorFilasDatosPersonales != 6){
            for($i = 0; $i < (6 - $contadorFilasDatosPersonales); $i++){
    ?>
                <tr class="filasDatosPersonales">
                    <td></td>
                    <td></td>
                </tr>
    <?php
            }
        }

        // Variable que almacena el número de filas de los datos no personales insertadas
        $contadorFilasDatosNoPersonales = 0;

        // Variable que almacena la fila en la cual se hará un corte de página
        $contadorFilaCorte = 6;

        // Variable que almacenará el número de cabeceras que se ha puesto en una página
        $numeroCabecerasPagina = 0;

        // Variable que almacenará el número de datos de interés puestos en una página
        $númeroDatosInteresPagina = false;

        // Variable que almacenará un booleano indicando si estamos en la 1ª página o no
        $primeraPagina = true;

        // Recorremos el array de listado de elementos no personales
        foreach($listadoElementosNoPersonales as $listado){

            // Recorremos cada elemento del array
            foreach($listado as $inicio => $datosListado){

                // Si estamos leyendo los datos de interés, y estamos en la primera página, restamos uno a la fila del corte
                if($inicio == 'Datos de interés' && $primeraPagina){
                    $contadorFilaCorte--;
                }
            
                // Comprobamos si hay que hacer un corte de página
                compruebaSiHayCortePagina($contadorFilasDatosNoPersonales, $contadorFilaCorte, $numeroCabecerasPagina, $primeraPagina);

                // Comprobamos si ya no estamos en la 1ª página
                if(!$primeraPagina){

                    // Comprobamos si hay más de una cabecera en la página
                    if($numeroCabecerasPagina % 2 == 0){

                        // Restamos uno a la fila del corte
                        $contadorFilaCorte--;

                        // Comprobamos si hay que hacer un corte de página
                        compruebaSiHayCortePagina($contadorFilasDatosNoPersonales, $contadorFilaCorte, $numeroCabecerasPagina, $primeraPagina);
                    }

                    // Aumentamos el número de cabeceras que hay en la página
                    $numeroCabecerasPagina++;
                }
    ?>
                    <tr>
                        <br/>
                    </tr>
                    <tr>
                        <th colspan="3" class="cabeceraListado"><?=$inicio?></th>
                    </tr>
            <?php
                // Recorremos cada uno de los elementos de la sección
                foreach($datosListado as $clave => $datoListado){

                    // Comprobamos si hay que hacer un corte de página
                    compruebaSiHayCortePagina($contadorFilasDatosNoPersonales, $contadorFilaCorte, $numeroCabecerasPagina, $primeraPagina, $clave);
            ?>
                        <tr>
                            <td colspan="3"><hr/></td>
                        </tr>
            <?php
                    // Dependiendo de si el dato es un dato de interés o no, creamos 1 o 3 filas para introducir los datos
                    if($inicio == 'Datos de interés'){
            ?>
                        <tr>
                            <td colspan="3"><?=$datoListado['dato1']?></td>
                        </tr>
            <?php
                        // Si la clave es un número impar, aumentamos el número de filas de datos no personales, ya que 2 datos de interés ocupan, más
                        //      o menos, como un estudio, experiencia o idioma
                        if($clave % 2 != 0){
                            $contadorFilasDatosNoPersonales++;
                        }
                    }else{
            ?>
                        <tr>
                            <th colspan="3"><?=$datoListado['dato1']?></th>
                        </tr>
                        <tr>
                            <td colspan="3"><?=$datoListado['dato2']?></td>
                        </tr>
                        <tr>
                            <td colspan="3"><?=$datoListado['dato3']?></td>
                        </tr>
    <?php
                        // Aumentamos el número de filas de datos no personales
                        $contadorFilasDatosNoPersonales++;
                    }
                }

                // Si se han leido los datos de interés, y estos eran un núemro impar de elementos, aumentamos el número
                //      de filas de datos no personales
                if($inicio == 'Datos de interés' && count($datosListado) % 2 != 0){
                    $contadorFilasDatosNoPersonales++;
                }
            }
        }  
    ?>
</table>

<?php

/**
 * Función que permite comprobar si hay que hacer un salto de página o no en el PDF.
 * @param integer $contadorFilas Contador de las filas que se han recorrido. Se pasa por referencia.
 * @param integer $contadorCorte Variable que almacena cuando hay que hacer un corte de página. Se pasa por referencia.
 * @param integer $numeroCabecerasPagina Número de cabeceras puestas en la página. Se pasa por referencia.
 * @param boolean $primeraPagina Booleano que permite determinar si estamos en la 1ª página o no. Se pasa por referencia.
 * @param integer $clave Clave del dato que se esta leyendo. Por defecto, posee el valor 1.
 * @since 1.0
 */
function compruebaSiHayCortePagina(&$contadorFilas, &$contadorCorte, &$numeroCabecerasPagina, &$primeraPagina, $clave = 1){

    // Si se recibe una clave igual a 0, es que debemos comprobar que sea distinta de ella además de que el contador de filas sea
    //      igual o superior al contador de corte
    // Si se recibe una clave diferente de 0, solo debemos comprobar que el contador de filas sea igual o superior al contador de 
    //      corte
    if($clave == 0){
        if($contadorFilas >= $contadorCorte && $clave != 0){
            generaCodigoSaltoPagina($contadorFilas, $contadorCorte, $numeroCabecerasPagina, $primeraPagina);
        }
    }else{
        if($contadorFilas >= $contadorCorte){
            generaCodigoSaltoPagina($contadorFilas, $contadorCorte, $numeroCabecerasPagina, $primeraPagina);
        }
    }
}

/**
 * Función que permite generar el HTML con la fila de salto de página, además de poner valores nuevos en las variables
 *      pasadas al método.
 * @param integer $contadorFilas Contador de las filas que se han recorrido. Se pasa por referencia.
 * @param integer $contadorCorte Variable que almacena cuando hay que hacer un corte de página. Se pasa por referencia.
 * @param integer $numeroCabecerasPagina Número de cabeceras puestas en la página. Se pasa por referencia.
 * @param boolean $primeraPagina Booleano que permite determinar si estamos en la 1ª página o no. Se pasa por referencia.
 * @since 1.0
 */
function generaCodigoSaltoPagina(&$contadorFilas, &$contadorCorte, &$numeroCabecerasPagina, &$primeraPagina){
?>
    <tr class="filaCorte">
        <br/>
    </tr>
    <tr>
        <br/>
    </tr>
    <tr>
        <br/>
    </tr>
<?php

    // Ponemos nuevos valores en las variables recibidas por parámetro, para así poder tener los valores adecuados
    //      para las nuevas páginas del currículum
    $numeroCabecerasPagina = 0;
    $contadorFilas = 0;
    $contadorCorte = 9;
    $primeraPagina = false;
}
?>