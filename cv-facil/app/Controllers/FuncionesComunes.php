<?php

namespace App\Controllers;

use Dompdf\Dompdf;

/**
 * Controlador abstracto que sirve para poder declarar todas las funciones comunes de los controladores principales de la 
 *      aplicación.
 * @version 1.0
 * @author Aitor Díez Arnáez
 * @access public
 * @abstract
 */
abstract class FuncionesComunes extends BaseController
{
    
    ////////////////////
    ////// MÉTODOS QUE SE PUEDEN LLAMAR EN LOS CONTROLADORES HIJOS, Y USARSE PARA DETERMINADAS FUNCIONES
    ////////////////////

    /**
     * Método que permite generar la vista principal de la página con el cuerpo, título, CSS y JS asignados, además de
     *      permitir asignar más cosas si así se desea.
     * @param string $tituloPagina Titulo de la página principal.
     * @param array $listaCss Array que contiene todas las rutas de los ficheros CSS que se añadirán a la vista principal.
     * @param array $listaJs Array que contiene todas las rutas de los ficheros JS que se añadirán a la vista principal.
     * @param string $vistaCuerpo Ruta de acceso a la vista que será el cuerpo de la página.
     * @param boolean $asignaCabecera Booleano que permite indicar si se pondrá la cabecera o no en la vista. Tiene el valor 
     *      false por defecto.
     * @param boolean $asignaPaginaAnterior Booleano que permite indicar si se va a asignar una página anterior, o si, por el 
     *      contrario, se obtendrá el valor de la página anterior de sesión. Tiene el valor false por defecto.
     * @param boolean $obtieneUsuario Booleano que permite indicar si se obtendrá el usuario almacenado en sesión. Tiene el valor
     *      false por defecto.
     * @param string $paginaAnterior Cadena que se guardará en sesión, y que representa a la página anterior. Por defecto, tiene
     *      el valor de una cadena vacía.
     * @param array $dataCuerpo Array de datos que se mandarán a la vista que actua como cuerpo de la página.
     * @return string Cadena que representa la vista ya generada, y lista para mostrarse en el navegador.
     * @access protected
     * @since 1.0
     */
    protected function generaVistaPrincipal($tituloPagina, $listaCss, $listaJs, $vistaCuerpo, $asignaCabecera = false,
        $asignaPaginaAnterior = false, $obtieneUsuario = false, $paginaAnterior = '', $dataCuerpo = array()){

        // Array que mandará los datos necesarios a la vista principal
        $data = array();

        // Guardamos el cuerpo de la página, mirando antes si se debe mandar datos a este
        if(count($dataCuerpo) == 0){
            $data['cuerpoPagina'] = view($vistaCuerpo);
        }else{
            $data['cuerpoPagina'] = view($vistaCuerpo, $dataCuerpo);
        }

        // Guardamos el titulo, CSS y JS a mandar a la vista de la cabecera
        $data['titulo'] = $tituloPagina;
        $data['listCss'] = $listaCss;
        $data['listJs'] = $listaJs;

        // Si se recibe que se desea mostrar la cabecera, lo guardamos en el array
        if($asignaCabecera){
            $data['cabeceraTitulo'] = 'Si';
        }

        // Si se recibe que se quiere asignar una página anterior, se guarda en sesión
        // Si se recibe que no se quiere asignar una página anterior, la obtenemos de sesión
        if($asignaPaginaAnterior){
            $this->session->set('paginaAnterior', $paginaAnterior);
        }else{
            $this->obtieneDatosSesion('paginaAnterior', $data);
        }

        // Si se recibe que se quiere obtener el usuario de la sesión, lo obtenemos
        if($obtieneUsuario){
            $this->obtieneDatosSesion('usuario', $data);
        }

        // Retornamos la vista
        return view('vistaPrincipal', $data);
    }

    /**
     * Método que nos permite obtener el dato deseado de la sesión, además de guardarlo en el array que queramos.
     * @param string $datoObtener Nombre del dato a obtener, y con el que se guardará en el array.
     * @param array $data Array donde se guardará el dato de sesión obtenido. Se pasa por referencia.
     * @param boolean $condicionFecha Booleano que permite indicar si se va a leer una fecha de la sesión o no.
     *      Tiene el valor false por defecto.
     * @access protected
     * @since 1.0
     */
    protected function obtieneDatosSesion($datoObtener, &$data, $condicionFecha = false){
        if($this->session->get($datoObtener) !== null){
            if($condicionFecha){
                $data[$datoObtener] = $this->formateaFechaBarras($this->session->get($datoObtener));
            }else{
                $data[$datoObtener] = $this->session->get($datoObtener);
            }
        }
    }

    /**
     * Método que permite comprobar los datos recibidos por POST al servidor.
     * @param array $arrayValidacion Array de arrays, en el que cada array contiene un dato a validar y los criterios de validación.
     *      La validación que se hará de cada dato dependerá del tipo de dato indicado en la variable tipoDato. Los tipos de datos que se pueden 
     *      indicar, y los datos que tendrá el array son:
     *          - Fecha:
     *              + dato: Fecha a validar.
     *              + minimo: Fecha mínima.
     *              + máximo: Fecha máxima.
     *              + mensaje: Mensaje de error que se mostrará en caso de no cumplirse el patrón.
     *              + fechaInicio: Valor opcional que nos permitirá determinar si una fecha es anterior a una dada.
     *          - Fijo (para selects y radio button):
     *              + dato: Dato a validar.
     *              + opciones: Array con las valores posibles del dato.
     *              + mensaje: Mensaje de error que se mostrará en caso de no cumplirse las condiciones.
     *          - Estudio, Idioma o Experiencia (para títulos de estudios o idiomas, puestos de trabajo o centros de estudio o trabajo):
     *              + dato: Dato a validar.
     *              + tituloCentro: Valor que permite determinar si se validará un titulo o un centro. Puede tener los valores de 'Titulo' o 'Centro'.
     *              + patrones: Array con los patrones de validación a comprobar.     
     *              + minimaLongitud: Longitud mínima del dato.
     *              + maximaLongitud: Longitud máxima del dato.
     *              + mensaje: Mensaje de error que se mostrará en caso de no cumplirse el patrón principal.
     *          - DatoInteres (para los datos de interés):
     *              + dato: Dato a validar.
     *              + patron: Patrón de validación.
     *              + mensaje: Mensaje de error que se mostrará en caso de no cumplirse el patrón.
     *          - Normal (el resto de los datos):
     *              + dato: Dato a validar.
     *              + patron: Patrón de validación.
     *              + minimaLongitud: Longitud mínima del dato.
     *              + maximaLongitud: Longitud máxima del dato.
     *              + campo: Nombre del campo a validar, para ponerlo en el mensaje de error.
     *              + mensaje: Mensaje de error que se mostrará en caso de no cumplirse el patrón.
     * @param array $array Array donde se guardará el mensaje de error que pueda dar. Se pasa por referencia.
     * @return boolean true si todos los datos cumplen los criterios, o false en caso contrario.
     * @access protected
     * @since 1.0
     */
    protected function compruebaDatosRecibidosPost($arrayValidacion, &$array){
        
        // Bandera que permite controlar si se cumplen los patrones o no
        $bandera = true;

        // Recorremos el array de datos
        foreach($arrayValidacion as $arrayCampo){

            // Dependiendo del tipo de dato indicado, se hace una validación diferente
            if($arrayCampo['tipoDato'] == 'Fecha'){

                // Validación de fechas
                if(!$this->validaFechasPost($arrayCampo['dato'], $arrayCampo['minimo'], $arrayCampo['maximo'])){
                    $array['mensaje'] = 'Error en la validación de datos';
                    $array['mensajeDetallado'] = $arrayCampo['mensaje'];
                    $bandera = false;
                    break;
                }

                // Comprobación de que la fecha indicada es anterior a la indicada de inicio
                if(isset($arrayCampo['fechaInicio']) && strtotime($arrayCampo['dato']) < strtotime($arrayCampo['fechaInicio'])){
                    $array['mensaje'] = 'Error en la validación de datos';
                    $array['mensajeDetallado'] = 'La fecha de fin no puede ser anterior a la de inicio.';
                    $bandera = false;
                    break;
                }
            }else if($arrayCampo['tipoDato'] == 'Fijo'){

                // Validación de selects/radio buttons
                if(!$this->validaCamposOpcionesPost($arrayCampo['opciones'], $arrayCampo['dato'])){
                    $array['mensaje'] = 'Error en la validación de datos';
                    $array['mensajeDetallado'] = $arrayCampo['mensaje'];
                    $bandera = false;
                    break;
                }
            }else if($arrayCampo['tipoDato'] == 'Estudio' || $arrayCampo['tipoDato'] == 'Experiencia' 
                || $arrayCampo['tipoDato'] == 'Idioma'){

                // Validación de títulos de estudios, experiencias laborales o idiomas
                if(!$this->validaTitulosCentrosEstudioExperienciaIdiomasPost($arrayCampo['patrones'], $arrayCampo['dato'], $arrayCampo['tipoDato'], 
                        $arrayCampo['tituloCentro'], $arrayCampo['minimaLongitud'], $arrayCampo['maximaLongitud'], $arrayCampo['mensaje'], $array)){
                    $bandera = false;
                    break;
                }

            }else if($arrayCampo['tipoDato'] == 'DatoInteres'){

                // Validación de datos de interes
                if(!preg_match($arrayCampo['patron'], $arrayCampo['dato'])){
                    $array['mensaje'] = 'Error en la validación de datos';
                    $array['mensajeDetallado'] = $arrayCampo['mensaje'];
                    $bandera = false;
                    break;
                }
            }else if($arrayCampo['tipoDato'] == 'Normal'){

                // Validación de campos generales
                if(!$this->validaCamposNormalesPost($arrayCampo['patron'], $arrayCampo['dato'], $arrayCampo['minimaLongitud'], $arrayCampo['maximaLongitud'],
                    $arrayCampo['campo'], $arrayCampo['mensaje'], $array)){
                    $bandera = false;
                    break;
                }
            }
        }

        // Retornamos la bandera
        return $bandera;
    }

    /**
     * Método que permite meter un dato en el array de validaciones para poder validarlo luego.
     * @param string $tipoDato Tipo de dato que se va a comprobar.
     * @param string $dato Valor del dato que se va a comprobar.
     * @param string $mensaje Mensaje que se mostrará al usuario en caso de que no se cumpla el 1º patrón indicado.
     * @param array $condicionesExtra Array asociativo que permite añadir más condiciones de validación.
     * @param array $arrayValidaciones Array de validaciones donde se guardará el dato a validar. Se pasa por referencia.
     * @access protected
     * @since 1.0
     */
    protected function generaArrayValidacionDato($tipoDato, $dato, $mensaje, $condicionesExtra, &$arrayValidaciones){

        // Generamos un array donde guardaremos las condiciones a validar del dato
        $arrayValidar = array();

        // Indicamos el tipo de dato, dato y mensaje en el array
        $arrayValidar['tipoDato'] = $tipoDato;
        $arrayValidar['dato'] = $dato;
        $arrayValidar['mensaje'] = $mensaje;

        // Metemos dentro del array las condiciones extra
        foreach($condicionesExtra as $clave => $valor){
            $arrayValidar[$clave] = $valor;
        }

        // Metemos el array a validar dentro del array de validaciones
        array_push($arrayValidaciones, $arrayValidar);
    }

    /**
     * Método que permite borrar todos los datos del currículum que pueda haber en sesión.
     * @access protected
     * @since 1.0
     */
    protected function borraDatosSesionCurriculum(){
        $this->session->remove(['accionCurriculum', 'orden1', 'orden2', 'orden3', 'orden4', 'cronologia', 'foto', 'nombre', 'apellidos', 'fechaNac',
            'correo', 'telefono', 'whatsapp', 'linkedin', 'direccion', 'estudios', 'experiencia', 'idiomas', 'datosInteres', 'idCurriculum', 'extensionFoto', 
            'nombreCurriculum']);
    }

    /**
     * Función que permite formatear una fecha en formato 'DD/MM/YYYY' a formato 'YYYY-MM-DD'.
     * @param string $fecha Fecha a formatear.
     * @return string Fecha formateada.
     * @access protected
     * @since 1.0
     */
    protected function formateaFechaBarras($fecha){
        $arrayFecha = explode('/', $fecha);
        return $arrayFecha[2].'-'.$arrayFecha[1].'-'.$arrayFecha[0];
    }

    /**
     * Método que permite formatear una fecha en formato 'YYYY-MM-DD' a 'DD/MM/YYYY'.
     * @param string $fecha Fecha a formatear.
     * @param boolean $dosCifras Booleano que permite indicar si queremos que la fecha formateada tenga 2 cifras en todos los campos o no.
     *      Por defecto, tiene el valor true.
     * @return string Fecha formateada.
     * @access protected
     * @since 1.0
     */
    protected function formateaFechaGuiones($fecha, $dosCifras = true){
        $arrayFecha = explode('-', $fecha);
        if($dosCifras){
            return $arrayFecha[2].'/'.$arrayFecha[1].'/'.$arrayFecha[0];
        }else{
            return intval($arrayFecha[2]).'/'.intval($arrayFecha[1]).'/'.intval($arrayFecha[0]);
        }
    }

    /**
     * Método que permite formatear los estudios, experiencia, idiomas o datos de interés de manera que se muestren correctamente en el PDF.
     * @param array $arrayDatos Array de datos a formatear
     * @param array|object $arrayOrdenCronologia Array u objeto que almacena el orden de los elementos y de la cronología.
     * @param string $claveOrden Clave del orden que esta relacionada con el array que se formateará
     * @param string $tituloDatoLeer Título que se mostrará encima de los datos del array en el PDF.
     * @param array $arrayDatosLeer Array que contiene las claves que se van a leer del array de datos.
     * @param array $data Array de datos donde se guardará el array formateado. Se pasa por referencia.
     * @param string $origenDatos Origen de los datos que se estan recorriendo. Puede tener los valores de SESSION o ARRAY. Por defecto, tiene
     *      el valor 'SESSION'.
     * @param boolean $formateaGuiones Booleano que permite indicar si las fechas vienen en formato 'YYYY-MM-DD' o 'DD/MM/YYYY'. Por defecto, 
     *      tiene el valor false.
     * @access protected
     * @since 1.0
     */
    protected function formateaDatosNoPersonalesPDF($arrayDatos, $arrayOrdenCronologia, $claveOrden, $tituloDatoLeer, 
        $arrayDatosLeer, &$data, $origenDatos = 'SESSION', $formateaGuiones = false){

        // Array auxiliar donde se guardará la información formateada
        $arrayAux = array();

        // Recorremos el array de datos recibido por parámetro, guardando los datos en el array
        foreach($arrayDatos as $k => $dato){
            $arrayAux[$k]['dato1'] = $dato[$arrayDatosLeer[0]];

            // Si no estamos leyendo datos de interés, guardamos los datos de la experiencia, estudios o idiomas, comprobando antes
            //      si se esta leyendo una experiencia, si se esta leyendo un dato con fecha de fin 'Actualidad' o si se esta leyendo
            //      una fecha con guiones o con barras
            if($tituloDatoLeer != 'Datos de interés'){
                $arrayAux[$k]['dato2'] = $tituloDatoLeer == 'Experiencia laboral' ? 'Centro de trabajo: '.$dato[$arrayDatosLeer[1]] : 
                    'Centro de estudio: '.$dato[$arrayDatosLeer[1]];
                if($dato[$arrayDatosLeer[3]] == 'Actualidad'){
                    if($tituloDatoLeer == 'Experiencia laboral'){
                        $arrayAux[$k]['dato3'] = $formateaGuiones ? 'Actualmente trabajando desde '.$this->formateaFechaGuiones($dato[$arrayDatosLeer[2]]) : 
                            'Actualmente trabajando desde '.$dato[$arrayDatosLeer[2]];
                    }else{
                        $arrayAux[$k]['dato3'] = $formateaGuiones ? 'Actualmente estudiando desde '.$this->formateaFechaGuiones($dato[$arrayDatosLeer[2]]) : 
                            'Actualmente estudiando desde '.$dato[$arrayDatosLeer[2]];
                    }
                }else{
                    $arrayAux[$k]['dato3'] = $formateaGuiones ? 'Desde '.$this->formateaFechaGuiones($dato[$arrayDatosLeer[2]]).' hasta '.
                        $this->formateaFechaGuiones($dato[$arrayDatosLeer[3]]) : 'Desde '.$dato[$arrayDatosLeer[2]].' hasta '.$dato[$arrayDatosLeer[3]];
                }
            }
        }

        // Dependiendo del origen de datos, comprobamos el orden y la cronologia de un array o de un objeto, y metemos el array formateado
        //      en el listado de datos no personales preparado en el array pasado por referencia al método, teniendo en cuenta si la cronología
        //      es inversa o no, para invertir el orden del array o no invertirlo.
        if($origenDatos == 'SESSION'){
            for($i = 1; $i < 5; $i++){
                if($arrayOrdenCronologia->get('orden'.$i) !== null && $arrayOrdenCronologia->get('orden'.$i) == $claveOrden){
                    if($tituloDatoLeer == 'Datos de interés'){
                        $data['listadoElementosNoPersonales'][$i-1] = array($tituloDatoLeer => $arrayAux);
                    }else{
                        $data['listadoElementosNoPersonales'][$i-1] = array($tituloDatoLeer => $arrayOrdenCronologia->get('cronologia') == 
                            'Inverso' ? array_reverse($arrayAux) : $arrayAux);
                    }
                }
            }
        }else if($origenDatos == 'ARRAY'){
            for($i = 1; $i < 5; $i++){
                if(isset($arrayOrdenCronologia['orden'.$i]) && $arrayOrdenCronologia['orden'.$i] == $claveOrden){
                    if($tituloDatoLeer == 'Datos de interés'){
                        $data['listadoElementosNoPersonales'][$i-1] = array($tituloDatoLeer => $arrayAux);
                    }else{
                        $data['listadoElementosNoPersonales'][$i-1] = array($tituloDatoLeer => $arrayOrdenCronologia['cronologia'] == 
                            'Inverso' ? array_reverse($arrayAux) : $arrayAux);
                    }
                }
            }
        }
    }

    /**
     * Método que permite generar el PDF del currículum, y descargarlo en el ordenador del usuario.
     * @param array $data Array de datos que se mostrarán en el currículum.
     * @access protected
     * @since 1.0
     */
    protected function generaPdf($data){

        // Generamos el objeto del PDF
        $dompdf = new Dompdf();

        // Obtenemos los iconos que se usarán en el PDF
        $data['arrayIconos'] = $this->generaArrayIconos($data);

        // Generamos el HTML que aparecerá en el PDF
        $html = view('creacionCurriculum/generaPDF.php',$data);

        // Cargamos el HTML en el PDF
        $dompdf->loadHtml($html);

        // Renderizamos el currículum
        $dompdf->render();

        // Descargamos el currículum con el nombre recibido por parámetro
        $dompdf->stream($data['nombreCurriculum'].'.pdf');
    }

    ////////////////////
    ////// MÉTODOS QUE SIRVEN DE APOYO PARA REALIZAR DETERMINADAS FUNCIONES
    ////////////////////

    /**
     * Método que permite validar el valor de los selects o de los radio button recibidos por POST.
     * @param array $opciones Array de opciones posibles para el select o el radio button.
     * @param string $dato Dato a validar.
     * @return boolean true si cumple la validación, y false en caso contrario.
     * @access private
     * @since 1.0
     */
    private function validaCamposOpcionesPost($opciones, $dato){

        // Variable booleana que permite comprobar si la opción es correcta o no
        $comprobadorOpcion = false;

        // Recorremos las opciones del array
        foreach($opciones as $opcion){

            // Si el dato es igual a la opción que se esta recorriendo, cambiamos el valor de la bandera, y paramos el bucle
            if($dato == $opcion){
                $comprobadorOpcion = true;
                break;
            }
        }

        // Retornamos la bandera
        return $comprobadorOpcion;
    }

    /**
     * Método que permite validar los títulos o centros de los estudios. experiencias laborales o idiomas recibidos por POST.
     * @param array $patrones Array con los patrones con los que se revisará el dato.
     * @param string $dato Dato a revisar.
     * @param string $tipoDato Tipo de dato a revisar. Puede tener los valores de 'Estudio', 'Experiencia' o 'Idioma'.
     * @param string $tituloCentro Variable que permite determinar si se va a revisar un título o un centro. Puede tener los valores de
     *      'Título' o de 'Centro'.
     * @param integer $minimaLongitud Mínima longitud del dato que se va a revisar.
     * @param integer $maximaLongitud Máxima longitud del dato que se va a revisar.
     * @param string $mensaje Mensaje que se mostrará si da fallo el 1º patrón.
     * @param array $array Array donde se almacenará el mensaje de error. Se pasa por referencia.
     * @return boolean true si las validaciones son correctas, o false en caso contrario.
     * @access private
     * @since 1.0
     */
    private function validaTitulosCentrosEstudioExperienciaIdiomasPost($patrones, $dato, $tipoDato, $tituloCentro, $minimaLongitud,
        $maximaLongitud, $mensaje, &$array){
        
        // Comprobamos si el dato cumple el 1º patrón
        if(!preg_match($patrones[0], $dato)){
            $array['mensaje'] = 'Error en la validación de datos';
            $array['mensajeDetallado'] = $mensaje;

            // Retornamos false
            return false;
        }

        // Si se ha indicado que se esta leyendo un título de idioma, tenemos que hacer una comprobación previa
        if($tituloCentro == 'Titulo' && $tipoDato == 'Idioma'){

            // Comprobamos si el idioma contiene un número precedido por ' de [A-Z]'
            if(preg_match('/\d/', $dato) && !preg_match('/\sde [A-Z]\d\s/', $dato)){
                $array['mensaje'] = 'Error en la validación de datos';
                $array['mensajeDetallado'] = 'El título se debe indicar similar al ejemplo: Título de B2 en Inglés.';

                // Retornamos false
                return false;
            }

        }

        // Comprobamos si el dato cumple el 2º patrón
        if(preg_match($patrones[1], $dato)){
            $array['mensaje'] = 'Error en la validación de datos';

            $mensajeTitulo = 'El título no puede acabar por de, de la,';
            if($tipoDato == 'Idioma'){
                $mensajeTitulo .= ' la,';
            }
            $mensajeTitulo .= ' del o en.';

            $this->indicaMensajeDetalladoEstudiosExperienciaIdiomasPost($tipoDato, $tituloCentro, 
                array('El puesto de trabajo no puede acabar por de, de la, del o en.', 'El centro de trabajo no puede acabar por de, de la, la, del o en.',
                $mensajeTitulo, 'El centro de estudios no puede acabar por de, de la, del o en.'), $array);
            
            // Retornamos false
            return false;
        }

        /*
        echo preg_match($patrones[2], $dato);
        echo $patrones[2];
        echo $dato;
        */

        // Comprobamos si el dato cumple el 3º patrón
        if(preg_match($patrones[2], $dato)){
            $array['mensaje'] = 'Error en la validación de datos';

            $mensajeTitulo = 'De, De la, De La,';
            if($tipoDato == 'Idioma'){
                $mensajeTitulo .= ' La,';
            }
            $mensajeTitulo .= ' Del o En deben ir en minúsculas.';
            $this->indicaMensajeDetalladoEstudiosExperienciaIdiomasPost($tipoDato, $tituloCentro, 
                array('De, De la, De La, La, Del o En deben ir en minúsculas.', 'De, De la, De La, Del o En deben ir en minúsculas.',
                $mensajeTitulo, 'De, De la, De La, Del o En deben ir en minúsculas.'), $array);
            
            // Retornamos false
            return false;
        }

        // Comprobamos si el dato cumple las longitudes. Si no lo cumple, se mete el error en el array y se retorna false
        if(strlen($dato) > $maximaLongitud || strlen($dato) < $minimaLongitud){
            $array['mensaje'] = 'Error en la validación de datos';
            $this->indicaMensajeDetalladoEstudiosExperienciaIdiomasPost($tipoDato, $tituloCentro, 
                array('La longitud del puesto de trabajo debe estar entre '.$minimaLongitud.' y '.$maximaLongitud.' carácteres.', 
                'La longitud del centro de trabajo debe estar entre '.$minimaLongitud.' y '.$maximaLongitud.' carácteres.',
                'La longitud del título debe estar entre '.$minimaLongitud.' y '.$maximaLongitud.' carácteres.', 
                'La longitud del centro de estudios debe estar entre '.$minimaLongitud.' y '.$maximaLongitud.' carácteres.'), $array);

            // Retornamos false
            return false;
        }

        // Retornamos true
        return true;
    }

    /**
     * Método que permite indicar el mensaje detallado que se mostrará al usuario en el caso de errores de validación con los títulos, 
     *      puestos de trabajo o centros de los estudios, experiencias o idiomas.
     * @param string $tipoDato Tipo de dato que se esta leyendo. Puede tener los valores de 'Estudio', 'Experiencia' o 'Idioma'.
     * @param string $tituloCentro Variable que permite determinar si se va a revisar un título o un centro. Puede tener los valores de
     *      'Título' o de 'Centro'.
     * @param array $mensajes Array de posibles mensajes que se mostrarán al usuario.
     * @param array $array Array donde se almacenará el mensaje de error. Se pasa por referencia.
     * @access private
     * @since 1.0
     */
    private function indicaMensajeDetalladoEstudiosExperienciaIdiomasPost($tipoDato, $tituloCentro, $mensajes, &$array){

        // Comprobamos el tipo de dato
        if($tipoDato == 'Experiencia'){

            // Comprobamos si se esta leyendo un título o un centro
            if($tituloCentro == 'Titulo'){
                $array['mensajeDetallado'] = $mensajes[0];
            }else if($tituloCentro == 'Centro'){
                $array['mensajeDetallado'] = $mensajes[1];
            }
        }else if($tipoDato == 'Estudio' || $tipoDato == 'Idioma'){

            // Comprobamos si se esta leyendo un título o un centro
            if($tituloCentro == 'Titulo'){
                $array['mensajeDetallado'] = $mensajes[2];
            }else if($tituloCentro == 'Centro'){
                $array['mensajeDetallado'] = $mensajes[3];
            }
        }
    }

    /**
     * Método que permite validar los campos normales recibidos por POST.
     * @param string $patron Patrón que debe cumplir el dato a revisar.
     * @param string $dato Dato a revisar.
     * @param integer $minimaLongitud Mínima longitud que debe tener el dato.
     * @param integer $maximaLongitud Máxima longitud que debe tener el dato.
     * @param string $campo Cadena que permite identificar el campo que estamos leyendo.
     * @param string $mensaje Mensaje que se mostrará si no se válida el dato con el patrón.
     * @param array $array Array donde se guardarán los datos de error. Se pasa por referencia.
     * @return boolean true si las validaciones son correctas, o false en caso contrario.
     * @access private
     * @since 1.0
     */
    private function validaCamposNormalesPost($patron, $dato, $minimaLongitud, $maximaLongitud, $campo, $mensaje, &$array){

        // Comprobamos si el dato cumple el patrón. Si no lo cumple, se mete el error en el array, y se retorna false
        if(!preg_match($patron, $dato)){
            $array['mensaje'] = 'Error en la validación de datos';
            $array['mensajeDetallado'] = $mensaje;
            return false;
        }

        // Comprobamos si el dato cumple las longitudes. Si no lo cumple, se mete el error en el array y se retorna false
        if(strlen($dato) > $maximaLongitud || strlen($dato) < $minimaLongitud){
            $array['mensaje'] = 'Error en la validación de datos';

            // El mensaje de error variará dependiendo de si se tiene indicada una mínima longitud diferente de 0, o si
            //      se tienen la misma longitud mínima y máxima
            if($minimaLongitud == 0 || $minimaLongitud == $maximaLongitud){
                $array['mensajeDetallado'] = $campo.' no puede ocupar más de '.$maximaLongitud.
                    ' caracteres.';
            }else{
                $array['mensajeDetallado'] = $campo.' debe ocupar entre '.$minimaLongitud.' y '.
                    $maximaLongitud.' caracteres.';
            }
            return false;
        }

        // Retornamos true
        return true;
    }

    /**
     * Método que permite validar las fechas recibidas por POST.
     * @param string $fecha Fecha a validar.
     * @param string $minimo Fecha mínima que se podría indicar.
     * @param string $maximo Fecha máxima que se podría indicar.
     * @return boolean true si la fecha es valida, y false en caso contrario.
     * @access private
     * @since 1.0
     */
    private function validaFechasPost($fecha, $minimo, $maximo){

        // Comprobamos si la fecha tiene un formato adecuado
        if(!preg_match('/\d{4}[-\d{2}]{2}/', $fecha)){
            return false;
        }


        $arrayFecha = explode('-', $fecha);

        // Comprobación del año
        if(intval($arrayFecha[0]) > date('Y')){
            return false;
        }

        // Comprobación del mes
        if(intval($arrayFecha[1]) > 12 || intval($arrayFecha[1]) < 0){
            return false;
        }

        // Comprobación del día, según mes y año, este último solo en años bisiestos
        if(intval($arrayFecha[1]) == 1 || intval($arrayFecha[1]) == 3 || intval($arrayFecha[1]) == 5 || intval($arrayFecha[1]) == 7 
            || intval($arrayFecha[1]) == 8 || intval($arrayFecha[1]) == 10 || intval($arrayFecha[1]) == 12){

            // Mes de 31 días
            if(intval($arrayFecha[2]) < 0 || intval($arrayFecha[2]) > 31){
                return false;
            }
        }else if(intval($arrayFecha[1]) == 4 || intval($arrayFecha[1]) == 6 || intval($arrayFecha[1]) == 9 || intval($arrayFecha[1]) == 11){
            
            // Mes de 30 días
            if(intval($arrayFecha[2]) < 0 || intval($arrayFecha[2]) > 30){
                return false;
            }
        }else if(intval($arrayFecha[1]) == 2){

            // Mes de 28 o 29 días
            $numeroDias = 28;

            // Comprobación de año bisiesto
            if((intval($arrayFecha[0]) % 100 != 0 && intval($arrayFecha[0]) % 4 == 0) || intval($arrayFecha[0]) % 400 == 0){
                $numeroDias = 29;
            }

            if(intval($arrayFecha[2]) < 0 || intval($arrayFecha[2]) > $numeroDias){
                return false;
            }
        }

        // Comprobación de si la fecha es posterior a la actual
        if(strtotime($fecha) > time()){
            return false;
        }

        // Comprobación de que la fecha esta entre el mínimo y máximo esperado
        if(strtotime($fecha) < strtotime($minimo) || strtotime($fecha) > strtotime($maximo)){
            return false;
        }

        // Si todo es correcto, retornamos true
        return true;
    }


    /**
     * Método que nos permite obtener al array de iconos que se usará en el PDF, además de asociarlo con el valor que se mostrará al
     *      lado del icono.
     * @param array $datos Datos que se mostrarán en el PDF.
     * @return array Array con los iconos que aparecerán en el PDF, formateados para que se muestren tal y como se desea.
     * @access private
     * @since 1.0
     */
    private function generaArrayIconos($datos){

        // Generamos el array de iconos, junto con algunos datos importantes que debemos tener de cada uno de ellos
        $array = array(
            'Calendario' => array(
                'claseIcono' => 'bi bi-calendar-fill', 
                'path' => '<path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V5h16V4H0V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5z"/>',
                'valor' => $datos['fechaNac'],
                'claseColumna' => 'icono padding3'
            ),
            'Telefono' => array(
                'claseIcono' => 'bi bi-telephone-fill', 
                'path' => '<path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>',
                'valor' => $datos['telefono'], 
                'claseColumna' => 'icono padding3'
            ),
            'Direccion' => array(
                'claseIcono' => 'bi bi-geo-alt-fill', 
                'path' => '<path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>',
                'valor' => $datos['direccion'],
                'claseColumna' => 'icono'
            ),
            'Correo' => array(
                'claseIcono' => 'bi bi-envelope-fill', 
                'path' => '<path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>',
                'valor' => $datos['correo'],
                'claseColumna' => 'icono padding7'
            ),
            'Whatsapp' => array(
                'claseIcono' => 'bi bi-whatsapp', 
                'path' => '<path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>',
                'claseColumna' => 'icono padding5'
            ),
            'Linkedin' => array(
                'claseIcono' => 'bi bi-linkedin', 
                'path' => '<path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>',
                'claseColumna' => 'icono'
            )
        );
    
        // Si alguna de las variables personales opciones tiene valor, lo guardamos en el array de iconos.
        if(isset($datos['whatsapp'])){
            $array['Whatsapp']['valor'] = $datos['whatsapp'];
        }
        if(isset($datos['linkedin'])){
            $array['Linkedin']['valor'] = $datos['linkedin'];
        }

        // Retornamos el array
        return $array;
    }
}