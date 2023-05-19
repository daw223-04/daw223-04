<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;
use Throwable;

/**
 * Controlador que contientes todos los métodos relacionados con la creación de curriculums, tales como métodos a los que se acceden
 *      desde una URL, y funciones de apoyo para dichos métodos.
 * @version 1.0
 * @author Aitor Díez Arnáez
 * @access public
 */
class CreacionCurriculums extends FuncionesComunes
{

    /**
     * Variable que almacena la conexión al modelo 'CreacionCurriculumsModel', para así acceder a la BD.
     * @var \CreacionCurriculumsModel
     * @access private
     * @since 1.0
     */
    private $creacionCurriculumsModel;

    /**
     * Constructor de la clase, que permite inicializar el módelo que usaremos para conectarnos a la BD.
     * @access public
     * @since 1.0
     */
    public function __construct(){
        $this->creacionCurriculumsModel = model('CreacionCurriculumsModel');
    }
    
    ////////////////////
    ////// MÉTODOS QUE ACTUAN DE CONTROLADORES, A LOS CUALES SE ACCEDE A TRAVÉS DEL NAVEGADOR
    ////////////////////

    /**
     * Método que permite mostrar la página de creación del curriculum.
     * @return string Pantalla de creación de currículum que se mostrará en el navegador.
     * @since 1.0
     * @access public
     */
    public function crearCurriculum()
    {
        // Array que mandará los datos necesarios a la vista del cuerpo de la página
        $dataCuerpo = array();

        // Obtenemos los datos del currículum de la sesión, si es que existen
        $this->obtieneDatosPersonalesSesion($dataCuerpo, true);
        $this->obtieneDatosSesion('estudios', $dataCuerpo);
        $this->obtieneDatosSesion('experiencia', $dataCuerpo);
        $this->obtieneDatosSesion('idiomas', $dataCuerpo);
        $this->obtieneDatosSesion('datosInteres', $dataCuerpo);

        // Generamos la vista de la página
        return $this->generaVistaPrincipal('Creación de curriculum', ['assets/css/creacionCurriculum/crearCurriculum.css'], 
            ['assets/js/creacionCurriculum/crearCurriculum.js'], 'creacionCurriculum/crearCurriculum', true, true, true,
            'CreacionCurriculum', $dataCuerpo);
    }

    /**
     * Método que permite mostrar la página donde se seleccionará el orden de los datos en el curriculum.
     * @return string Pantalla de selección del orden de los elementos que se mostrará en el navegador.
     * @since 1.0
     * @access public
     */
    public function seleccionaOrdenElementosCurriculum(){

        // Si no hay ningun nombre almacenado en sesión, forzamos una redirección a la pantalla de creación de currículums
        if($this->session->get('nombre') === null){
            return redirect()->to(base_url().'CreacionCurriculum');
        }

        // Array que mandará los datos necesarios a la vista del cuerpo de la página
        $dataCuerpo = array();

        // Comprobamos si hay datos en sesión de los estudios, experiencia, idiomas y datos de interés, para guardarlos 
        //      en el array que se mandará a la vista del cuerpo de la página
        $this->obtieneNumeroDatosNoPersonalesSesion('estudios', 'conteoEstudios', $dataCuerpo);
        $this->obtieneNumeroDatosNoPersonalesSesion('experiencia', 'conteoExperiencia', $dataCuerpo);
        $this->obtieneNumeroDatosNoPersonalesSesion('idiomas', 'conteoIdiomas', $dataCuerpo);
        $this->obtieneNumeroDatosNoPersonalesSesion('datosInteres', 'conteoDatosInteres', $dataCuerpo);

        // Definimos un array de conteo del número de veces que se repiten los números de los datos que hay en estudios, 
        //      experiencia, idiomas y datos de interés
        $dataCuerpo['arrayConteos'] = array_count_values(array($dataCuerpo['conteoEstudios'], $dataCuerpo['conteoExperiencia'], 
            $dataCuerpo['conteoIdiomas'], $dataCuerpo['conteoDatosInteres']));

        // Comprobamos si hay algún dato de orden guardado en sesión
        if($this->session->get('orden1') !== null){

            // Obtenemos el número de veces que hay datos de algún orden en sesión
            $contador = 0;

            // Hacemos un bucle, en el que recorremos los datos del orden de la sesión, y comprobamos si poseen valor para
            //      aumentar el contador
            for($i = 1; $i < 5; $i++){
                if($this->session->get('orden'.$i) !== null){
                    $contador++;
                }
            }
            
            // Creamos una variable que actuará de bandera, y que permitirá poder ver si se han añadido nuevas secciones al 
            //      currículum
            $limite = 0;

            // Si hay algún valor con clave 0 en el array de conteos, lo asignamos a la variable de límite
            if(isset($dataCuerpo['arrayConteos'][0])){
                $limite = $dataCuerpo['arrayConteos'][0];
            }

            // Si el contador no es igual a la resta de 4 menos el límite, borramos todos los valores del orden de la sesión, para
            //      así evitar que pueda haber secciones que antes estaban en el currículum y ahora no aparecen
            if($contador != (4 - $limite)){
                $this->session->remove(['orden1', 'orden2', 'orden3', 'orden4']);
            }

            // Guardamos el contador en el array de datos que se mandarán al cuerpo
            $dataCuerpo['contador'] = $contador;
        }
        
        // Guardamos los datos del orden y de la cronología en el array de datos que se mandarán al cuerpo
        $dataOrdenElementos = array();
        for($i = 1; $i < 5; $i++){
            $this->obtieneDatosSesion('orden'.$i, $dataOrdenElementos);
        }
        $dataCuerpo['ordenElementos'] = $dataOrdenElementos;
        $this->obtieneDatosSesion('cronologia', $dataCuerpo);

        // Generamos la vista de la página
        return $this->generaVistaPrincipal('Orden de elementos', ['assets/css/creacionCurriculum/ordenElementos.css'], 
            ['assets/js/creacionCurriculum/ordenElementos.js'], 'creacionCurriculum/ordenElementos', true, true, true,
            'OrdenElementosCurriculum', $dataCuerpo);
    }

    /**
     * Método que permite mostrar la vista donde se verá el curriculum ya generado.
     * @return string Pantalla de currículum generado que se mostrará en el navegador.
     * @since 1.0
     * @access public
     */
    public function muestraCurriculumGenerado(){

        // Si no hay ninguna foto almacenada en sesión, forzamos una redirección a la pantalla de creación de currículums
        // Si hay una foto almacenada en sesión, pero no un orden, forzamos una redirección a la pantalla de orden de elementos
        if($this->session->get('foto') === null){
            return redirect()->to(base_url().'CreacionCurriculum');
        }else if($this->session->get('orden1') === null){
            return redirect()->to(base_url().'OrdenElementosCurriculum');
        }

        // Array que mandará los datos necesarios a la vista del cuerpo de la página
        $dataCuerpo = array();

        // Obtenemos los datos personales del usuario.
        $this->obtieneDatosPersonalesSesion($dataCuerpo);

        // Obtenemos los datos de los estudios, experiencia, idiomas y datos de interés ordenados tal y como dijo el usuario.
        $datosCurriculum = array();
        $this->obtieneDatosNoPersonalesSesion('orden1', $datosCurriculum);
        $this->obtieneDatosNoPersonalesSesion('orden2', $datosCurriculum);
        $this->obtieneDatosNoPersonalesSesion('orden3', $datosCurriculum);
        $this->obtieneDatosNoPersonalesSesion('orden4', $datosCurriculum);
        $dataCuerpo['datosCurriculum'] = $datosCurriculum;

        // Obtenemos el orden cronológico indicado por el usuario
        $this->obtieneDatosSesion('cronologia', $dataCuerpo);

        // Comprobamos si existe el id del currículum en sesión, lo cual solo puede pasar si estamos modificando un currículum,
        //      para indicarlo en un dato que se meterá en el array, y meter también el nombre del currículum
        if($this->session->get('idCurriculum') != ''){
            $dataCuerpo['accionCurriculum'] = 'Modificar';
            $this->obtieneDatosSesion('nombreCurriculum', $dataCuerpo);
        }

        // Si hay un usuario logueado, también lo mandamos a la vista del cuerpo de la página
        $this->obtieneDatosSesion('usuario', $dataCuerpo);

        // Generamos la vista de la página
        return $this->generaVistaPrincipal('Curriculum creado', ['assets/css/creacionCurriculum/curriculumGenerado.css'], 
            ['assets/js/creacionCurriculum/curriculumGenerado.js'], 'creacionCurriculum/curriculumGenerado', true, true, true,
            'CurriculumGenerado', $dataCuerpo);
    }

    ////////////////////
    ////// MÉTODOS QUE ACTUAN DE CONTROLADORES, A LOS CUALES SE ACCEDE A TRAVÉS DE PETICIONES AJAX
    ////////////////////

    /**
     * Método que permite gestionar la subida de la foto seleccionada en el curriculum al servidor.
     * @since 1.0
     * @access public
     */
    public function gestionaSubidaFotoCurriculum(){

        // Guardamos en una variable la ruta de la foto
        $rutaFoto = "assets/imagenesCurriculum/";

        // Array que almacena los datos que se mandarán al navegador
        $array = array();

        // Comprobamos que se haya subido una foto
        if(!isset($_FILES['foto'])){

            // Guardamos un mensaje de error en el array
            $array['mensaje'] = 'Error en la validación de datos';
            $array['mensajeDetallado'] = 'No se ha indicado ninguna foto';
        }else{

            // Obtenemos la extensión de la foto.
            $arrayDatosFoto = explode('.', $_FILES['foto']['name']);
            $extensionFoto = strtoUpper($arrayDatosFoto[count($arrayDatosFoto) - 1]);

            // Comprobamos que la extensión sea correcta
            if($extensionFoto == 'PNG' || $extensionFoto == 'JPG' || $extensionFoto == 'JPEG'){

                // Bloque try-catch creado para evitar posibles errores con la subida de la foto
                try{

                    // Si ya existe una imagen asociada a la sesión, y no es una imagen que estemos obteniendo de BD, la cual empieza
                    //      por 'data:image', la borramos
                    if($this->session->get('foto') && substr($this->session->get('foto'), 0, 10) != 'data:image'){
                        unlink($this->session->get('foto'));
                    }

                    // Intentamos mover la imagen a una carpeta fija
                    if(move_uploaded_file($_FILES['foto']['tmp_name'], $rutaFoto.$_FILES['foto']['name'])){
                        
                        // Guardamos un mensaje en el array indicando que todo se ha realizado satisfactoriamente, además de guardar
                        //      la ruta de acceso a la foto
                        $array['mensaje'] = 'Completado';
                        $array['ruta'] = $rutaFoto.$_FILES['foto']['name'];

                        // Ponemos la ruta a la foto indicada en la sesión
                        $this->session->set('foto', $rutaFoto.$_FILES['foto']['name']);

                        // Guardamos la extensión de la foto en sesión
                        $this->session->set('extensionFoto', $extensionFoto);
                    }else{

                        // Guardamos un mensaje de error en el array
                        $array['mensaje'] = 'Error';
                    }

                }catch(Throwable $e){
                    
                    // Guardamos un mensaje de error en el array
                    $array['mensaje'] = 'Error';
                    $array['error'] = $e->getMessage();
                }
            }else{

                // Guardamos un mensaje de error en el array
                $array['mensaje'] = 'Error en la validación de datos';
                $array['mensajeDetallado'] = 'Solo se pueden poner como foto de currículum ficheros PNG, JPG o JPEG.';
            }
        }

        // Devolvemos el array en formato JSON
        echo json_encode($array);
    }

    /**
     * Método que permite gestionar la subida de todos los datos del curriculum que haya rellenado el usuario en la pantalla de 
     *      datos personales, estudios, etc.
     * @since 1.0
     * @access public
     */
    public function gestionaSubidaDatosCurriculum(){

        // Array que almacena los datos que se mandarán al navegador
        $array = array();

        // Si no se encuentra el fichero de la foto, guardamos un mensaje de error en el array
        // Si se encuentra, guardamos todos los datos recibidos en la sesión
        if(!file_exists($this->request->getPost('foto')) && substr($this->request->getPost('foto'), 0, 10) != 'data:image'){
            $array['mensaje'] = 'Error en la obtención de la foto';
            $array['error'] = 'No se ha encontrado la foto indicada por el usuario';
        }else{

            // Obtenemos todos los parámetros recibidos por POST
            $nombre = $this->request->getPost('nombre');
            $apellidos = $this->request->getPost('apellidos');
            $telefono = $this->request->getPost('telefono');
            $fechaNac = $this->request->getPost('fechaNac');
            $direccion = $this->request->getPost('direccion');
            $correo = $this->request->getPost('correo');
            $whatsapp = $this->request->getPost('whatsapp');
            $linkedin = $this->request->getPost('linkedin');

            // Array que almacenará el mensaje y la ruta que se devuelva al navegador
            $array = array();

            // Array que almacenará todas las validaciones a hacer
            $arrayValidacion = array();

            // Metemos los datos a validar en el array de validaciones
            $this->generaArrayValidacionDato('Normal', $nombre, 'El nombre no posee un formato de caracteres adecuado.', array('minimaLongitud' => 0, 
                'maximaLongitud' => 30, 'campo' => 'El nombre', 'patron' => '/^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)?$/'), $arrayValidacion);
            $this->generaArrayValidacionDato('Normal', $apellidos, 'Los apellidos no poseen un formato de caracteres adecuado.', array('minimaLongitud' => 0, 
                'maximaLongitud' => 50, 'campo' => 'Los apellidos', 'patron' => '/^(de\s(la\s)?|del\s)?[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+$/'), 
                $arrayValidacion);
            $this->generaArrayValidacionDato('Normal', $telefono, 'El teléfono no tiene un formato de caracteres adecuado.', array('minimaLongitud' => 9, 
                'maximaLongitud' => 9, 'campo' => 'El teléfono', 'patron' => '/^\d{9}$/'), $arrayValidacion);
            $this->generaArrayValidacionDato('Fecha', $fechaNac, 'La fecha de nacimiento no tiene un formato de caracteres adecuado.', array('minimo' => '1960-01-01', 
                'maximo' => date('Y-m-d', strtotime('-15 years', strtotime(date('Y-m-d'))))), $arrayValidacion);
            $this->generaArrayValidacionDato('Normal', $direccion, 'La dirección no tiene un formato de caracteres adecuado.', array('minimaLongitud' => 10, 
                'maximaLongitud' => 180, 'campo' => 'La dirección', 'patron' => 
                '/^(C\/|Avda\.|Plaza)\s(((de(\s(la|las|los))?\s|del\s|los\s|las\s)?[A-ZÁÉÍÓÚÑ][a-záéíóúñ]{3,})\s?)+,\s\d{1,3}(,\s\d{1,2}(º|°)\s[A-Z])?$/'), $arrayValidacion);
            $this->generaArrayValidacionDato('Normal', $correo, 'El correo no tiene un formato de caracteres adecuado.', array('minimaLongitud' => 15, 
                'maximaLongitud' => 120, 'campo' => 'El correo', 'patron' => '/^[\wÁÉÍÓÚÑáéíóúñ\.]+@[a-z]+(\.(es|com)){1,2}$/'), $arrayValidacion);
            
            // Metemos el Whatsapp y el LinkedIn al array de validaciones solo si es necesario
            if($whatsapp !== ''){
                $this->generaArrayValidacionDato('Normal', $whatsapp, 'El número de Whatsapp no tiene un formato de caracteres adecuado.', array('minimaLongitud' => 9, 
                    'maximaLongitud' => 9, 'campo' => 'El número de Whatsapp', 'patron' => '/^\d{9}$/'), $arrayValidacion);
            }
            if($linkedin !== ''){
                $this->generaArrayValidacionDato('Normal', $linkedin, 'El enlace de LinkedIn no tiene un formato de caracteres adecuado.', array('minimaLongitud' => 28, 
                    'maximaLongitud' => 150, 'campo' => 'El enlace de LinkedIn', 'patron' => '/^https:\/\/www.linkedin.com\/in\/[a-z\-]+\/$/'), $arrayValidacion);
            }

            // Creamos un array con los datos de los estudios, experiencia, idiomas o datos de interés
            $arrayDatosNoPersonales = array('Estudio' => $this->request->getPost('estudios'), 'Experiencia' => $this->request->getPost('experiencia'),
                'Idioma' => $this->request->getPost('idiomas'), 'DatosInteres' => $this->request->getPost('datosInteres'));

            // Bandera que permite saber si se ha indicado algún estudio, experiencia laboral, idioma o dato de interés
            $compruebaExistenciaDatosNoPersonales = false;

            // Recorremos el array de los estudios, experiencia, idiomas o datos de interés
            foreach($arrayDatosNoPersonales as $claveNP => $json){

                // Recorremos los datos, decodificando el JSON antes
                foreach(json_decode($json) as $valorNP){

                    // Cambiamos el valor de la bandera
                    $compruebaExistenciaDatosNoPersonales = true;

                    // Comprobamos si el dato que se va a mirar es un dato de interés o no
                    if($claveNP != 'DatosInteres'){

                        // Variable que almacenará el 1º patrón a mirar
                        $patron1 = '';

                        // Variable que almacenará el inicio del mensaje del título o puesto
                        $campoTituloPuesto = '';

                        // Variable que almacenará el inicio del mensaje del centro
                        $campoCentro = '';

                        // Asignamos el valor del patrón y del valor del inicio de mensaje de título o puesto dependiendo de si
                        //      es un idioma, una experiencia laboral o un estudio lo que se esta comprobando
                        if($claveNP == 'Idioma'){
                            $patron1 = '/^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\d\.]+\s?)+(de\s(la\s)?|en\s|del\s)?)+$/';
                            $campoTituloPuesto = 'El titulo del idioma';
                        }else{
                            $patron1 = '/^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\.]+\s?)+(de\s(la\s)?|en\s|del\s)?)+$/';
                            if($claveNP == 'Experiencia'){
                                $campoTituloPuesto = 'El puesto de trabajo';
                            }else{
                                $campoTituloPuesto = 'El titulo del estudio';
                            }
                        }

                        // Asignamos el valor del inicio de mensaje del centro o puesto dependiendo de si es una experiencia u otra cosa 
                        //      lo que se esta comprobando
                        if($claveNP == 'Experiencia'){
                            $campoCentro = 'El centro de trabajo';
                        }else{
                            $campoCentro = 'El centro de estudios';
                        }

                        // Guardamos los 3 primeros datos en el array de validaciones
                        $this->generaArrayValidacionDato($claveNP, $valorNP->dato1, $campoTituloPuesto.' no tiene un formato de caracteres adecuado.', 
                            array('tituloCentro' => 'Titulo', 'minimaLongitud' => 15, 'maximaLongitud' => 200, 'patrones' => array($patron1, 
                            '/\s([Dd]e(\s[Ll]a)?|[Dd]é(\s[Ll]a)?|[Dd]e(\s[Ll]á)?|[Dd]é(\s[Ll]á)?|[Dd]el|[Dd]él|[Ee]n|Én|én)\s?$/', 
                            '/\s(De(\s[Ll]a)?|Dé(\s[Ll]a)?|De(\s[Ll]á)?|Dé(\s[Ll]á)?|Del|Dél|En|Én|én)\s/')), $arrayValidacion); // Los patrones tienen que ser así, porque
                                                                                                                                 //  si no PHP no los pilla
                        $this->generaArrayValidacionDato($claveNP, $valorNP->dato2, $campoCentro.' no tiene un formato de caracteres adecuado.', 
                            array('tituloCentro' => 'Centro', 'minimaLongitud' => 5, 'maximaLongitud' => 100, 'patrones' => array(
                            '/^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\.]+\s?)+((de(\sla)?|la|del|en)\s)?)+$/', 
                            '/\s([Dd]e(\s[Ll]a)?|[Dd]é(\s[Ll]a)?|[Dd]e(\s[Ll]á)?|[Dd]é(\s[Ll]á)?|[Dd]el|[Dd]él|[Ee]n|Én|én)\s?$/', 
                            '/\s(De(\s[Ll]a)?|Dé(\s[Ll]a)?|De(\s[Ll]á)?|Dé(\s[Ll]á)?|Del|Dél|En|Én|én)\s/')), $arrayValidacion); // Los patrones tienen que ser así, porque
                                                                                                                                 //  si no PHP no los pilla
                        $this->generaArrayValidacionDato('Fecha', $valorNP->dato3, 'La fecha inicial del '.strtolower($claveNP).' no tiene un formato de caracteres adecuado.',
                            array('minimo' => '1980-01-01', 'maximo' => date('Y-m-d')), $arrayValidacion);

                        // Solamente guardamos el 4º dato si no contiene el texto 'Actualidad'
                        if($valorNP->dato4 != 'Actualidad'){
                            $this->generaArrayValidacionDato('Fecha', $valorNP->dato4, 'La fecha final del '.strtolower($claveNP).' no tiene un formato de caracteres adecuado.',
                                array('minimo' => '1980-01-01', 'maximo' => date('Y-m-d'), 'fechaInicio' => date('Y-m-d', strtotime($valorNP->dato3))), $arrayValidacion);
                        }
                        
                    }else{
                        $this->generaArrayValidacionDato('DatoInteres', $valorNP->dato1, 'El dato de interés no tiene un formato de caracteres adecuado.', 
                            array('patron' => '/^[A-ZÁÉÍÓÚÑa-záéíóúñ\d\.\s,-]+$/'), $arrayValidacion);
                    }
                    
                }
                
            }

            // Comprobamos que todos los datos sean válidos
            if($this->compruebaDatosRecibidosPost($arrayValidacion, $array)){

                if(!$compruebaExistenciaDatosNoPersonales){

                    $array['mensaje'] = 'Error en la validación de datos';
                    $array['mensajeDetallado'] = 'No ha indicado ningún dato aparte de los datos personales.';

                }else{

                    // Guardamos todos los datos personales en la sesión, mirando si se han mandado los opcionales, o
                    //      si ya estaban mandados de anteriores veces, para borrarlos
                    $this->session->set('nombre', $nombre);
                    $this->session->set('apellidos', $apellidos);
                    $this->session->set('telefono', $telefono);
                    $this->session->set('fechaNac', $this->formateaFechaGuiones($fechaNac));
                    $this->session->set('direccion', $direccion);
                    $this->session->set('correo', $correo);
                    if($whatsapp !== ''){
                        $this->session->set('whatsapp', $whatsapp);
                    }else if($this->session->get('whatsapp') !== null){
                        $this->session->remove('whatsapp');
                    }
                    if($linkedin !== ''){
                        $this->session->set('linkedin', $linkedin);
                    }else if($this->session->get('linkedin') !== null){
                        $this->session->remove('linkedin');
                    }
                    
                    // Guardamos los estudios, experiencia, idiomas y datos de interés en la sesión
                    $this->guardaDatosNoPersonalesEnSesion($arrayDatosNoPersonales['Estudio'], 'estudios');
                    $this->guardaDatosNoPersonalesEnSesion($arrayDatosNoPersonales['Experiencia'], 'experiencia');
                    $this->guardaDatosNoPersonalesEnSesion($arrayDatosNoPersonales['Idioma'], 'idiomas');
                    $this->guardaDatosNoPersonalesEnSesion($arrayDatosNoPersonales['DatosInteres'], 'datosInteres');

                    // Guardamos en el array un mensaje de que se ha completado todo satisfactoriamente, además de una ruta
                    //      que usará el navegador para redirigir al usuario
                    $array['mensaje'] = 'Completado';
                    $array['ruta'] = base_url().'OrdenElementosCurriculum';
                }

            } 
        }
        
        // Devolvemos el array en formato JSON
        echo json_encode($array);
    }

    /**
     * Método que permite guardar en sesión el orden de elementos y la cronología elegidos por el usuario.
     * @since 1.0
     * @access public
     */
    public function subeOrdenElementos(){

        // Recogemos los parámetros recibidos por POST
        $orden1 = $this->request->getPost('orden1');
        $orden2 = $this->request->getPost('orden2');
        $orden3 = $this->request->getPost('orden3');
        $orden4 = $this->request->getPost('orden4');
        $cronologia = $this->request->getPost('cronologia');

        // Array que almacenará el mensaje y la ruta que se devuelva al navegador
        $array = array();

        // Creamos el array de comprobaciones de valores
        $arrayValidacion = array();

        // Creamos un array con los ordenes del currículum
        $arrayOrden = array(1 => $orden1, 2 => $orden2, 3 => $orden3, 4 => $orden4);

        // Recorremos el array
        foreach($arrayOrden as $clave => $orden){

            // Si el orden no es nulo, lo añadimos en el array de validación
            if($orden !== null){
                $this->generaArrayValidacionDato('Fijo', $orden, 'No se ha indicado el '.$clave.'º apartado del currículum.', array('opciones' => 
                    array('Estudios', 'Experiencia', 'Idiomas', 'DatosInteres')), $arrayValidacion);
            }
        }

        // Metemos la cronología en el array de validaciones
        $this->generaArrayValidacionDato('Fijo', $cronologia, 'No se ha indicado ninguna cronología para los datos del currículum.', array('opciones' => 
            array('Directo', 'Inverso')), $arrayValidacion);

        // Comprobamos si los datos cumplen los patrones especificados
        if($this->compruebaDatosRecibidosPost($arrayValidacion, $array)){

            // Array auxiliar para guardar los datos indicados en la ordenación de apartados
            $arrayAux = array();

            // Bandera para comprobar que no se haya repetido ningún valor en la ordenación de apartados
            $bandera = true;

            // Recorremos los ordenes puestos
            $arrayOrden = array($orden1, $orden2, $orden3, $orden4);
            foreach($arrayOrden as $valor){

                // Si el array auxiliar tiene algún valor, y este esta en el array, cambiamos el valor de la bandera y metemos un mensaje
                //      de error en el array
                if(count($arrayAux) > 0 && $valor !== null && array_search($valor, $arrayAux) !== false){
                    $bandera = false;
                    $array['mensaje'] = 'Error en la validación de datos';
                    $array['mensajeDetallado'] = 'No puede repetir ningún valor de los desplegables en el orden del currículum.';
                    break;
                }else{

                    // Si el valor no es nulo, lo metemos en el array
                    if($valor !== null){
                        array_push($arrayAux, $valor);
                    }
                }
            }

            // Comprobamos el valor de la bandera
            if($bandera){

                // Si no se ha recibido ningún orden, es porque solo se ha introducido un dato diferente a los datos personales en la
                //      creación del currículum
                // Si se han recibido datos del orden, los guardamos en sesión
                if($orden1 === null && $orden2 === null && $orden3 === null && $orden4 === null){

                    // Creamos un array que almacene la busqueda de las claves en sesión, y el valor que se guardaría en el orden
                    $array = array('estudios' => 'Estudios', 'experiencia' => 'Experiencia', 'idiomas' => 'Idiomas', 
                        'datosInteres' => 'DatosInteres');

                    // Recorremos el array creado, mirando si existe el dato en sesión, y si existe, asignandoselo al orden1
                    foreach($array as $tablaPrincipal => $valor){
                        if($this->session->get($tablaPrincipal) !== null && count($this->session->get($tablaPrincipal)) > 0){
                            $this->session->set('orden1', $valor);
                        }
                    }
                }else{
                    for($i = 1; $i < 5; $i++){
                        if($this->request->getPost('orden'.$i) !== null){
                            $this->session->set('orden'.$i, $this->request->getPost('orden'.$i));
                        }
                    }
                }

                // Guardamos el orden cronológico en la sesión
                $this->session->set('cronologia', $cronologia);

                // Guardamos un mensaje en el array diciendo que todo se ha completado correctamente, además de una ruta a la cual redirigirá
                //      el navegador al usuario
                $array['mensaje'] = 'Completado';
                $array['ruta'] = base_url().'CurriculumGenerado';
            }
        }

        // Imprimimos el array
       	echo json_encode($array);

    }

    /**
     * Método que permite guardar los datos de un currículum en la BD.
     * @since 1.0
     * @access public
     */
    public function guardaCurriculumBD(){
        
        // Recogemos los parámetros recibidos por POST
        $nombreCurriculum = $this->request->getPost('nombreCurriculum');

        // Array que almacenará el mensaje y la ruta que se devuelva al navegador
        $array = array();

        // Creamos el array de comprobaciones de valores
        $arrayValidacion = array();

        // Creamos el array de comprobaciones de valores
        $this->generaArrayValidacionDato('Normal', $nombreCurriculum, 'El nombre del currículum no posee un formato de caracteres válido.', array('minimaLongitud' => 5, 
            'maximaLongitud' => 30, 'patron' => '/^[\wÁÉÍÓÚÑáéíóúñ\.\s\-]+$/','campo' => 'El nombre del currículum'), $arrayValidacion);
        
        // Comprobamos si los datos cumplen los patrones especificados
        if($this->compruebaDatosRecibidosPost($arrayValidacion, $array)){
            
            // Rotamos la imagen si es necesario
            $imagen = $this->rotaImagenesCurriculum();

            // Bloque try-catch creado para evitar posibles errores con la BD
            try{

                // Insertamos los datos personales del currículum en la BD
                $idDatosPersonales = $this->creacionCurriculumsModel->insertaDatosPersonales($imagen, $this->session->get('extensionFoto'), 
                    $this->session->get('nombre'), $this->session->get('apellidos'), $this->formateaFechaBarras($this->session->get('fechaNac')), 
                    $this->session->get('direccion'), $this->session->get('correo'), $this->session->get('telefono'), $this->session->get('whatsapp'), 
                    $this->session->get('linkedin'));

                // Insertamos los datos del currículum en la BD
                $idCurriculum = $this->creacionCurriculumsModel->insertaDatosCurriculum($nombreCurriculum, date('Y-m-d'), 
                    $this->session->get('usuario'), $idDatosPersonales, $this->session->get('orden1'), $this->session->get('orden2'), 
                    $this->session->get('orden3'), $this->session->get('orden4'), $this->session->get('cronologia'));

                // Comprobamos si hay datos de estudios, experiencia, idiomas o datos de interés en sesión, para guardarlos en BD
                $this->guardaDatosNoPersonalesCurriculumBD($idCurriculum, 'estudios');
                $this->guardaDatosNoPersonalesCurriculumBD($idCurriculum, 'experiencia');
                $this->guardaDatosNoPersonalesCurriculumBD($idCurriculum, 'idiomas');
                $this->guardaDatosNoPersonalesCurriculumBD($idCurriculum, 'datosInteres');

                // Eliminamos todos los datos relacionados con la creación de currículum de la sesión
                $this->borraDatosSesionCurriculum();

                // Gurdamos un mensaje en el array indicando que todo se ha realizado satisfactoriamente, además de la ruta a la que se
                //      redigirirá el usuario tras hacer click en la ventana modal que le aparezca
                $array['mensaje'] = 'Completado';
                $array['ruta'] = base_url().'ListadoCurriculums';

            }catch(DatabaseException $e){

                // Si da alguna excepción, mostramos un mensaje de error, además de poner que error ha dado
                $array['mensaje'] = 'Error en BD';
                $array['error'] = 'Error en la BD: '.$e->getMessage();
            }
        }

        // Devolvemos el array en formato JSON
        echo json_encode($array);
    }

    /**
     * Método que permite modificar los datos de un currículum almacenado en BD.
     * @since 1.0
     * @access public
     */
    public function modificaDatosCurriculum(){

        // Obtenemos los datos recibidos por POST
        $nombreCurriculum = $this->request->getPost('nombreCurriculum');
        $idCurriculum = $this->session->get('idCurriculum');

        // Array que almacenará el mensaje y la ruta que se devuelva al navegador
        $array = array();

        // Creamos el array de comprobaciones de valores
        $arrayValidacion = array();

        // Creamos el array de comprobaciones de valores
        $this->generaArrayValidacionDato('Normal', $nombreCurriculum, 'El nombre del currículum no posee un formato de caracteres válido.', array('minimaLongitud' => 5, 
            'maximaLongitud' => 30, 'patron' => '/^[\wÁÉÍÓÚÑáéíóúñ\.\s\-]+$/','campo' => 'El nombre del currículum'), $arrayValidacion);

        // Comprobamos si los datos cumplen los patrones especificados
        if($this->compruebaDatosRecibidosPost($arrayValidacion, $array)){

            // Obtenemos el contenido del fichero de la imagen del currículum
            $imagen = file_get_contents($this->session->get('foto'));

            // Si la imagen no viene de BD, comprobamos si hace falta girarla. Para saber si viene de BD, comprobamos si empieza por
            //      'data:image', que es como representamos las fotos que vienen de BD en el currículum
            if(substr($this->session->get('foto'), 0, 10) != 'data:image'){
                $imagen = $this->rotaImagenesCurriculum();
            }

            // Bloque try-catch creado para evitar posibles errores con la BD
            try{

                // Comprobamos si el currículum existe
                $comprobadorExistenciaCurriculum = $this->creacionCurriculumsModel->compruebaCurriculum($idCurriculum);

                // Si no existe, arrojamos una excepción
                if(!$comprobadorExistenciaCurriculum){
                    throw new DatabaseException('Currículum inexistente');
                }

                // Insertamos los datos personales del currículum en la BD, si no están insertados ya
                $idDatoPersonal = $this->creacionCurriculumsModel->insertaDatosPersonales($imagen, $this->session->get('extensionFoto'), 
                    $this->session->get('nombre'), $this->session->get('apellidos'), $this->formateaFechaBarras($this->session->get('fechaNac')), 
                    $this->session->get('direccion'), $this->session->get('correo'), $this->session->get('telefono'), $this->session->get('whatsapp'), 
                    $this->session->get('linkedin'));

                // Actualizamos los datos del currículum en la BD
                $this->creacionCurriculumsModel->actualizaCurriculum($idCurriculum, $nombreCurriculum, date('Y-m-d'), $this->session->get('orden1'), 
                    $this->session->get('orden2'), $this->session->get('orden3'), $this->session->get('orden4'), $this->session->get('cronologia'), 
                    $idDatoPersonal);

                // Comprobamos si hay datos de estudios, experiencia, idiomas o datos de interés en sesión, para guardarlos en BD, 
                //      si no estan guardados ya
                $this->guardaDatosNoPersonalesCurriculumBD($idCurriculum, 'estudios');
                $this->guardaDatosNoPersonalesCurriculumBD($idCurriculum, 'experiencia');
                $this->guardaDatosNoPersonalesCurriculumBD($idCurriculum, 'idiomas');
                $this->guardaDatosNoPersonalesCurriculumBD($idCurriculum, 'datosInteres');

                // Revisamos que no se hayan quedado datos no personales del currículum sin ninguna relación, para borrarlos de la BD
                $this->creacionCurriculumsModel->borraDatosCurriculum('est_exp_idi', 'est_exp_idi_cur', 'id_est_exp_idi');
                $this->creacionCurriculumsModel->borraDatosCurriculum('dato_interes', 'dat_int_cur', 'id_dato_interes');

                // Eliminamos todos los datos relacionados con la creación de currículum de la sesión
                $this->borraDatosSesionCurriculum();

                // Gurdamos un mensaje en el array indicando que todo se ha realizado satisfactoriamente, además de la ruta a la que se
                //      redigirirá el usuario tras hacer click en la ventana modal que le aparezca
                $array['mensaje'] = 'Completado';
                $array['ruta'] = base_url().'ListadoCurriculums';

            }catch(DatabaseException $e){

                // Si da alguna excepción, mostramos un mensaje de error, además de poner que error ha dado
                $array['mensaje'] = 'Error en BD';
                $array['error'] = 'Error en la BD: '.$e->getMessage();
            }
        }

        // Devolvemos el array en formato JSON
        echo json_encode($array);
    }

    /**
     * Método que permite generar el PDF de un currículum cuyos datos estan almacenados en la sesión activa.
     * @since 1.0
     * @access public
     */
    public function generaPdfCurriculumDeSesion(){

        // Si no hay ningun nombre almacenado en sesión, forzamos una redirección a la pantalla de creación de currículums
        // Si hay un nombre almacenada en sesión, pero no un orden, forzamos una redirección a la pantalla de orden de elementos
        if($this->session->get('nombre') === null){
            return redirect()->to(base_url().'CreacionCurriculum');
        }else if($this->session->get('orden1') === null){
            return redirect()->to(base_url().'OrdenElementosCurriculum');
        }

        // Array que contendrá todos los datos del currículum, formateados para generar el PDF de manera sencilla
        $data = array();

        // Array que contendrá todos los listados de estudios, experiencia, idiomas y datos de interés
        $data['listadoElementosNoPersonales'] = array();

        // Rotamos la imagen del currículum si hace falta
        $imagen = $this->rotaImagenesCurriculum();

        // Guardamos la foto que se mostrará en el PDF en el array
        $data['foto'] = 'data:image/'.strtolower($this->session->get('extensionFoto')).';base64,'.base64_encode($imagen);

        // Guardamos todos los datos personales que haya en la sesión en el array
        $this->obtieneDatosPersonalesSesionSinFoto($data);

        // Si hay estudios en la sesión, formateamos el array de estudios para que aparezca correctamente en el PDF
        if($this->session->get('estudios') !== null){
            $this->formateaDatosNoPersonalesPDF($this->session->get('estudios'), $this->session, 'Estudios', 'Estudios', 
                ['dato1', 'dato2', 'dato3', 'dato4'], $data);
        }

        // Si hay experiencia laboral en la sesión, formateamos el array de experiencia para que aparezca correctamente en el PDF
        if($this->session->get('experiencia') !== null){
            $this->formateaDatosNoPersonalesPDF($this->session->get('experiencia'), $this->session, 'Experiencia', 
                'Experiencia laboral', ['dato1', 'dato2', 'dato3', 'dato4'], $data);
        }

        // Si hay idiomas en la sesión, formateamos el array de idiomas para que aparezca correctamente en el PDF
        if($this->session->get('idiomas') !== null){
            $this->formateaDatosNoPersonalesPDF($this->session->get('idiomas'), $this->session, 'Idiomas', 'Idiomas', 
                ['dato1', 'dato2', 'dato3', 'dato4'], $data);
        }

        // Si hay datos de interés en la sesión, formateamos el array de datos de interés para que aparezca correctamente en el PDF
        if($this->session->get('datosInteres') !== null){
            $this->formateaDatosNoPersonalesPDF($this->session->get('datosInteres'), $this->session, 'DatosInteres', 
                'Datos de interés', ['dato1'], $data);
        }

        // Ordenamos el array de datos no personales
        ksort($data['listadoElementosNoPersonales']);

        // Guardamos en el array el nombre del currículum a generar
        $data['nombreCurriculum'] = 'Curriculum';

        // Generamos el PDF
        $this->generaPdf($data);
    }

    ////////////////////
    ////// MÉTODOS QUE SIRVEN DE APOYO PARA REALIZAR DETERMINADAS FUNCIONES
    ////////////////////

    /**
     * Método que permite guardar en la BD los estudios, experiencia, idiomas y datos de interés almacenados en sesión,
     *      formateandolos previamente para que se guarden correctamente en BD.
     * @param integer $idCurriculum Identificador del currículum asociado a los datos.
     * @param string $claveSesion Clave con la que estan almacenados los datos en la sesión.
     * @since 1.0
     * @access private
     */
    private function guardaDatosNoPersonalesCurriculumBD($idCurriculum, $claveSesion){

        // Creamos 2 variables que almacenarán la tabla principal donde se insertarán los datos, y la tabla de la relación con
        //      la tabla de currículums
        $tablaPrincipal = '';
        $tablaRelacion = '';

        // Comprobamos primero que haya datos en la sesión de lo que se quiere guardar en BD
        if($this->session->get($claveSesion) != ''){

            // Recorremos los datos de la sesión
            foreach($this->session->get($claveSesion) as $valor){

                // Array que guardará los datos formateados
                $array = array();

                // Si se estan leyendo los datos de interés, solo debemos formatear 1 dato
                // Si se estan leyendo los estudios, experiencia o idiomas, debemos formatear entre 5 y 6 datos, dependiendo de
                //      la situación
                if($claveSesion == 'datosInteres'){
                    $array['dato'] = $valor['dato1'];

                    // Indicamos los valores de la tabla principal y la de la relación
                    $tablaPrincipal = 'dato_interes';
                    $tablaRelacion = 'dat_int';

                }else if($claveSesion == 'estudios' || $claveSesion == 'experiencia' || $claveSesion == 'idiomas'){

                    // Formateamos los 3 primeros datos
                    $array['titulo_puesto'] = $valor['dato1'];
                    $array['centro'] = $valor['dato2'];
                    $array['fecha_inicio'] = $this->formateaFechaBarras($valor['dato3']);

                    // Si el 4º dato es igual a 'Actualidad', lo reflejamos en el array en un campo especial diseñado en la BD
                    // Si el 4º dato no es igual a actualidad, lo reflejamos en el array guardándolo de forma normal, además de en el
                    //      campo especial diseñado en la BD
                    if($valor['dato4'] == 'Actualidad'){
                        $array['condicion_actual'] = 1;
                    }else{
                        $array['fecha_fin'] = $this->formateaFechaBarras($valor['dato4']);
                        $array['condicion_actual'] = 0;
                    }

                    // Indicamos el tipo de dato a introducir en la BD, ya que los estudios, experiencia e idiomas se guardan todos
                    //      en la misma tablaRelacion$tablaRelacion
                    switch($claveSesion){
                        case 'estudios':
                            $array['tipo_dato'] = 'Estudio';
                            break;
                        case 'experiencia':
                            $array['tipo_dato'] = 'Experiencia';
                            break;
                        case 'idiomas':
                            $array['tipo_dato'] = 'Idioma';
                            break;
                    }

                    // Indicamos los valores de la tabla principal y la de la relación
                    $tablaPrincipal = 'est_exp_idi';
                    $tablaRelacion = 'est_exp_idi';
                }

                // Insertamos el array de datos en la BD
                $this->creacionCurriculumsModel->insertaDatosNoPersonalesCurriculum($idCurriculum, $array, $tablaPrincipal, 
                    $tablaRelacion);
            }
        }
    }

    /**
     * Método que permite obtener los datos personales almacenados en la sesión.
     * @param array $data Array donde se guardarán los datos de sesión obtenidos. Se pasa por referencia.
     * @param boolean $condicionFechaBarras Booleano que permite indicar si se va a querer la fecha con barras (/), para lo que se debe indicar
     *      true, o con guiones (-), para lo que se debe indicar false. Tiene el valor false por defecto.
     * @since 1.0
     * @access private
     */
    private function obtieneDatosPersonalesSesion(&$data, $condicionFechaBarras = false){

        // Obtenemos la foto de la sesión
        $this->obtieneDatosSesion('foto', $data);

        // Obtenemos el resto de datos de la sesión
        $this->obtieneDatosPersonalesSesionSinFoto($data, $condicionFechaBarras);
    }

    /**
     * Método que permite obtener los datos personales almacenados en la sesión, excluyendo a la foto.
     * @param array $data Array donde se guardarán los datos de sesión obtenidos. Se pasa por referencia.
     * @param boolean $condicionFechaBarras Booleano que permite indicar si se va a querer la fecha con barras (/), para lo que se debe indicar
     *      true, o con guiones (-), para lo que se debe indicar false. Tiene el valor false por defecto.
     * @since 1.0
     * @access private
     */
    private function obtieneDatosPersonalesSesionSinFoto(&$data, $condicionFechaBarras = false){

        // Obtenemos los datos deseados de la sesión
        $this->obtieneDatosSesion('nombre', $data);
        $this->obtieneDatosSesion('apellidos', $data);
        $this->obtieneDatosSesion('telefono', $data);
        $this->obtieneDatosSesion('fechaNac', $data, $condicionFechaBarras);
        $this->obtieneDatosSesion('correo', $data);
        $this->obtieneDatosSesion('whatsapp', $data);
        $this->obtieneDatosSesion('linkedin', $data);
        $this->obtieneDatosSesion('direccion', $data);
    }

    /**
     * Método que permite guardar los arrays de estudios, experiencia, idiomas y datos de interés en la sesión con un formato 
     *      determinado.
     * @param string $arrayDatos Array de datos a guardar, en formato JSON.
     * @param string $claveSesion Clave con la que se guardará el array en sesión.
     * @since 1.0
     * @access private
     */
    private function guardaDatosNoPersonalesEnSesion($arrayDatos, $claveSesion){

        // Generamos un array y un contador para guardar los datos
        $array = array();
        $contador = 0;

        // Recorremos el array de datos
        foreach(json_decode($arrayDatos) as $value){

            // Guardamos los datos del array de datos en el array principal, mirando cuantos datos tiene el array de datos,
            //      y formateandolos si son fechas
            $array[$contador]['dato1'] = $value->dato1;
            if(isset($value->dato2)){
                $array[$contador]['dato2'] = $value->dato2;
                $array[$contador]['dato3'] = $this->formateaFechaGuiones($value->dato3);
                $array[$contador]['dato4'] = $value->dato4 == 'Actualidad' ? 'Actualidad' : $this->formateaFechaGuiones($value->dato4);
            }

            // Aumentamos el contador
            $contador++;
        }

        // Si hay un dato en sesión del dato que estamos intentando guardar, y este no posee datos, lo removemos de la sesión
        if($this->session->get($claveSesion) && count($array) == 0){
            $this->session->remove($claveSesion);
        }

        // Si el array formateado tiene más de un dato, guardamos el array en la sesión con la clave deseada
        if(count($array) > 0){
            $this->session->set($claveSesion, $array);
        }

    }

    /**
     * Método que permite obtener el número de elementos que hay en lso estudios, experiencia, idiomas o datos de interés, 
     *      y guardarlo en un array, o guardar 0 si no hay ningún dato.
     * @param string $claveSesion Clave de sesión de la que se consultará el array que contiene.
     * @param string $claveData Clave con la que se guardará el número de datos que se obtenga en el array.
     * @param array $data Array donde se guardarán el dato obtenido. Se pasa por referencia.
     * @since 1.0
     * @access private
     */
    private function obtieneNumeroDatosNoPersonalesSesion($claveSesion, $claveData, &$data){

        // Comprobamos si hay datos en la sesión del dato que queremos buscar
        if($this->session->get($claveSesion) !== null){
            $data[$claveData] = count($this->session->get($claveSesion));
        }else{
            $data[$claveData] = 0;
        }
    }

    /**
     * Método que permite obtener los datos de los estudios, experiencia, idiomas o datos de interés, dependiendo del orden pasado por parámetro,
     *      y lo guarda en un array para poderlos mostrar en el currículum generado.
     * @param string $orden Clave del valor de la orden que se desea obtener.
     * @param array $arrayGuardado Array donde se guardarán los datos. Se pasa por referencia.
     * @since 1.0
     * @access private
     */
    private function obtieneDatosNoPersonalesSesion($orden, &$arrayGuardado){

        // Comprobamos si hay un dato en sesión guardado con la clave de orden indicada
        if($this->session->get($orden) !== null){

            // Dependiendo de que clave de orden sea, guardamos uno u otro dato de sesión en el array, y, si lo que se obtienen son estudios, experiencia 
            //      o idiomas, miramos la cronología almacenada en sesión para ver si debemos guardar el dato de sesión en el orden que esta almacenado
            //      o en orden inverso
            if($this->session->get($orden) == 'Estudios'){
                $arrayGuardado['Estudios'] = $this->session->get('cronologia') == 'Inverso' ? array_reverse($this->formateaDatosNoPersonalesSesion
                    ($this->session->get('estudios'))) : $this->formateaDatosNoPersonalesSesion($this->session->get('estudios'));
            }else if($this->session->get($orden) == 'Experiencia'){
                $arrayGuardado['Experiencia laboral'] = $this->session->get('cronologia') == 'Inverso' ? array_reverse
                    ($this->formateaDatosNoPersonalesSesion ($this->session->get('experiencia'), true)) : 
                    $this->formateaDatosNoPersonalesSesion($this->session->get('experiencia'), true);
            }else if($this->session->get($orden) == 'Idiomas'){
                $arrayGuardado['Idiomas'] = $this->session->get('cronologia') == 'Inverso' ? array_reverse($this->formateaDatosNoPersonalesSesion
                ($this->session->get('idiomas'))) : $this->formateaDatosNoPersonalesSesion($this->session->get('idiomas'));
            }else if($this->session->get($orden) == 'DatosInteres'){
                $arrayGuardado['Datos de interés'] = $this->formateaDatosNoPersonalesSesion($this->session->get('datosInteres'));
            }
        }
    }

    /**
     * Método que permite formatear los datos de estudios, experiencia, idiomas y datos de interes almacenados en sesión, para que se manden a la
     *      vista de currículum generado con un formato que entienda la vista
     * @param array $array Array a convertir.
     * @param boolean $condicionTrabajando Booleano que permite indicar si lo que se va a convertir es el array de experiencia o no. Por defecto,
     *      tiene el valor false.
     * @return array Array convertido para mostrarse en el currículum directamente.
     * @since 1.0
     * @access private
     */
    private function formateaDatosNoPersonalesSesion($array, $condicionTrabajando = false){

        // Generamos un array principal y un contador para guardar los datos
        $arrayFormateado = array();
        $contador = 0;

        // Recorremos el array recibido por parámetro
        foreach($array as $valor){

            // Guardamos el 1º dato
            $arrayFormateado[$contador]['dato1'] = $valor['dato1'];

            // Guardamos el 2º dato solo si existe
            if(isset($valor['dato2'])){
                $arrayFormateado[$contador]['dato2'] = $valor['dato2'];
            }

            // Si existe el 4º dato, lo guardamos junto al 3º, dependiendo de si el 4º es 'Actualidad' o no, y del valor
            //      del booleano recibido por parámetro
            if(isset($valor['dato4'])){
                if($valor['dato4'] == 'Actualidad'){
                    if($condicionTrabajando){
                        $arrayFormateado[$contador]['dato3'] = 'Actualmente trabajando desde '.$valor['dato3'];
                    }else{
                        $arrayFormateado[$contador]['dato3'] = 'Actualmente estudiando desde '.$valor['dato3'];
                    }
                }else{
                    $arrayFormateado[$contador]['dato3'] = 'Desde '.$valor['dato3'].' hasta '.$valor['dato4'];
                }
            }

            // Aumentamos el contador
            $contador++;
        }

        // Retornamos el array principal
        return $arrayFormateado;
    }

    /**
     * Método que permite comprobar si la imagen del currículum es un JPEG, y, si lo es, rotarla para que aparezca correctamente
     *      en la BD y en el PDF que generemos.
     * @return string Imagen formateada, de manera que aparezca correctamente en todos los posibles casos de uso.
     * @since 1.0
     * @access private
     */
    private function rotaImagenesCurriculum(){

        // Obtenemos el contenido de la imagen
        $imagen = file_get_contents($this->session->get('foto'));

        // Comprobamos si la imagen es un JPEG
        if(exif_imagetype($this->session->get('foto')) == IMAGETYPE_JPEG){

            // Obtenemos los metadatos de la imagen
            $metadatosFoto = exif_read_data($this->session->get('foto'));

            // Comprobamos si la imagen tiene un metadato de orientación, y si este no es igual a 1, que es el valor que devuelve
            //      el dato de orientación si la imagen esta bien orientada
            if(isset($metadatosFoto['Orientation']) && $metadatosFoto['Orientation'] != 1){

                // Creamos la imagen a partir del contenido de la imagen
                $imagen = imagecreatefromstring($imagen);

                // Hacemos un switch por el metadato de la orientación, y rotamos la foto tantos grados como haga falta para ponerla
                //      bien orientada
                switch($metadatosFoto['Orientation']){
                    case 3:
                        $imagen = imagerotate($imagen, 180, 0); // Imagen boca abajo - Rotación de 180 grados
                        break;
                    case 6:
                        $imagen = imagerotate($imagen, -90, 0); // Imagen girada a la derecha - Rotación de -90 grados
                        break;
                    case 8:
                        $imagen = imagerotate($imagen, 90, 0); // Imagen girada a la izquierda - Rotación de 90 grados
                        break;
                }

                // Abrimos un bufer de salida para poder obtener la imagen como una cadena
                ob_start();

                // Imprimimos en el bufer de salida la imagen buena a partir de la imagen que hemos rotado
                imagejpeg($imagen);

                // Obtenemos el contenido del bufer
                $imagen = ob_get_contents();

                // Cerramos el bufer de salida
                ob_end_clean();
            }
        }

        // Devolvemos la imagen
        return $imagen;
    }
}