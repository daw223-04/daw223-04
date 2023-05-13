<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;

/**
 * Controlador que contientes todos los métodos relacionados con las acciones de un usuario, tales como métodos a los que se acceden
 *      desde una URL, y funciones de apoyo para dichos métodos.
 * @version 1.0
 * @author Aitor Díez Arnáez
 * @access public
 */
class AccionesUsuario extends FuncionesComunes
{

    /**
     * Variable que almacena la conexión al modelo 'AccionesUsuarioModel', para así acceder a la BD.
     * @var \AccionesUsuarioModel
     * @access private
     * @since 1.0
     */
    private $accionesUsuarioModel;

    /**
     * Constructor de la clase, que permite inicializar el módelo que usaremos para conectarnos a la BD.
     * @access public
     * @since 1.0
     */
    public function __construct(){
        $this->accionesUsuarioModel = model('AccionesUsuarioModel');
    }

    ////////////////////
    ////// MÉTODOS QUE ACTUAN DE CONTROLADORES, A LOS CUALES SE ACCEDE A TRAVÉS DEL NAVEGADOR
    ////////////////////

    /**
     * Método que permite mostrar la pantalla de inicio de sesión.
     * @return string Pantalla de inicio de sesión que se mostrará en el navegador.
     * @since 1.0
     * @access public
     */
    public function inicioSesion()
    {
        // Generamos la vista de la página
        return $this->generaVistaPrincipal('Inicio de sesión', ['assets/css/gestionUsuarios/inicioSesion.css'], 
            ['assets/js/gestionUsuarios/inicioSesion.js'], 'gestionUsuarios/inicioSesion');
    }

    /**
     * Método que permite mostrar la pantalla de crear cuenta.
     * @return string Pantalla de creación de cuenta que se mostrará en el navegador.
     * @since 1.0
     * @access public
     */
    public function creaCuenta(){
        
        // Generamos la vista de la página
        return $this->generaVistaPrincipal('Creación de cuenta', ['assets/css/gestionUsuarios/creaCuenta.css'], 
            ['assets/js/gestionUsuarios/creaCuenta.js'], 'gestionUsuarios/creaCuenta');
    }

    /**
     * Método que permite mostrar la pantalla con el listado de los currículums de un usuario.
     * @return string Pantalla de listado de currículums que se mostrará en el navegador.
     * @since 1.0
     * @access public
     */
    public function mostrarListadoCurriculumsUsuario(){

        // Si no hay ningún usuario introducido, forzamos una redirección a la pantalla de inicio de sesión
        if($this->session->get('usuario') === null){
            return redirect()->to(base_url().'InicioSesion');
        }

        // Generamos la vista de la página
        return $this->generaVistaPrincipal('Listado de curriculums', ['assets/css/gestionUsuarios/listadoCurriculumsUsuario.css'], 
            ['assets/js/gestionUsuarios/listadoCurriculumsUsuario.js'], 'gestionUsuarios/listadoCurriculumsUsuario', true, true, true,
            'ListadoCurriculums');
    }

    /**
     * Método que permite mostrar la pantalla de gestión de cuenta de un usuario.
     * @return string Pantalla de gestión de cuenta que se mostrará en el navegador.
     * @since 1.0
     * @access public
     */
    public function gestionaCuentaUsuario(){

        // Si no hay ningún usuario introducido, forzamos una redirección a la pantalla de inicio de sesión
        if($this->session->get('usuario') === null){
            return redirect()->to(base_url().'InicioSesion');
        }
        
        // Obtenemos los datos del usuario de la BD
        $datosUsuario = $this->accionesUsuarioModel->obtieneDatosUsuario($this->session->get('usuario'));

        // Guardamos los datos obtenidos de la BD en un array
        $dataCuerpo = array(
            'id' => $datosUsuario->id,
            'nombre' => $datosUsuario->nombre,
            'apellidos' => $datosUsuario->apellidos,
            'nombreUsuario' => $this->session->get('usuario'),
            'contrasenia' => $datosUsuario->contrasenia
        );

        // Generamos la vista de la página
        return $this->generaVistaPrincipal('Gestión de cuenta', ['assets/css/gestionUsuarios/gestionaCuentaUsuario.css'], 
            ['assets/js/gestionUsuarios/gestionaCuentaUsuario.js'], 'gestionUsuarios/gestionaCuentaUsuario', true, true, true,
            'GestionCuenta', $dataCuerpo);
    }

    ////////////////////
    ////// MÉTODOS QUE ACTUAN DE CONTROLADORES, A LOS CUALES SE ACCEDE A TRAVÉS DE PETICIONES AJAX
    ////////////////////

    /**
     * Método que permite iniciar sesión en la cuenta recibida por POST, comprobando primero que dicha cuenta existe.
     * @since 1.0
     * @access public
     */
    public function iniciaSesionCuenta(){

        // Recogemos los parámetros recibidos por POST
        $nombreUsuario = $this->request->getPost('nombreUsuario');
        $contrasenia = $this->request->getPost('contrasenia');

        // Array que almacenará el mensaje y la ruta que se devuelva al navegador
        $array = array();

        // Array que almacenará todas las validaciones a hacer
        $arrayValidacion = array();

        // Metemos los datos a validar en el array de validaciones
        $this->generaArrayValidacionDato('Normal', $nombreUsuario, 'El nombre de usuario debe tener entre 8 y 20 caracteres, y solo puede tener letras, números y guiones bajos.', 
            array('minimaLongitud' => 8, 'maximaLongitud' => 20, 'campo' => 'El nombre de usuario', 'patron' => '/^[\w]{8,20}$/'), $arrayValidacion);
        $this->generaArrayValidacionDato('Normal', $contrasenia, 'La contraseña debe tener entre 8 y 20 caracteres, y solo puede tener letras y números.', 
            array('minimaLongitud' => 8, 'maximaLongitud' => 20, 'campo' => 'La contraseña', 'patron' => '/^[A-Za-z\d]{8,20}$/'), $arrayValidacion);
        
        // Comprobamos si los datos cumplen los patrones especificados
        if($this->compruebaDatosRecibidosPost($arrayValidacion, $array)){

            // Comprobamos si existe el usuario indicado
            $numeroUsuariosMismoNombre = $this->accionesUsuarioModel->compruebaUsuario($nombreUsuario, $contrasenia);

            // Si no existe el usuario, guardamos un mensaje de error en el array
            // Si existe el usuario, lo asignamos a la sesión, y guardamos un mensaje de que se ha accedido satisfactoriamente en el array, además de una ruta
            //      a la cual se redirigirá tras iniciarse sesión, dependiendo esta ruta de si se viene de una página anterior o no
            if(intval($numeroUsuariosMismoNombre->nombre_usuario) == 0){
                $array['mensaje'] = 'Credenciales incorrectos';
            }else{
                $this->session->set('usuario', $nombreUsuario);
                $array['mensaje'] = 'Sesión iniciada';
                if($this->session->get('paginaAnterior') === null){
                    $array['ruta'] = base_url();
                }else{
                    $array['ruta'] = base_url().$this->session->get('paginaAnterior');
                }
            }
        }

        // Devolvemos el array en formato JSON
        echo json_encode($array);
    }

    /**
     * Método que permite crear una cuenta nueva a partir de los datos recibidos por POST, comprobando primero que el nombre de 
     *      usuario indicado no esta ya en uso.
     * @since 1.0
     * @access public
     */
    public function creacionCuentaNueva(){

        // Recogemos los parámetros recibidos por POST
        $nombre = $this->request->getPost('nombre');
        $apellidos = $this->request->getPost('apellidos');
        $nombreUsuario = $this->request->getPost('nombreUsuario');
        $contrasenia = $this->request->getPost('contrasenia');

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
        $this->generaArrayValidacionDato('Normal', $nombreUsuario, 'El nombre de usuario debe tener entre 8 y 20 caracteres, y solo puede tener letras, números y guiones bajos.', 
            array('minimaLongitud' => 8, 'maximaLongitud' => 20, 'campo' => 'El nombre de usuario', 'patron' => '/^[\w]{8,20}$/'), $arrayValidacion);
        $this->generaArrayValidacionDato('Normal', $contrasenia, 'La contraseña debe tener entre 8 y 20 caracteres, y solo puede tener letras y números.', 
            array('minimaLongitud' => 8, 'maximaLongitud' => 20, 'campo' => 'La contraseña', 'patron' => '/^[A-Za-z\d]{8,20}$/'), $arrayValidacion);

        // Comprobamos si los datos cumplen los patrones especificados
        if($this->compruebaDatosRecibidosPost($arrayValidacion, $array)){

            // Comprobamos si el nombre de usuario ya esta en uso
            $numeroUsuariosMismoNombre = $this->accionesUsuarioModel->compruebaUsuario($nombreUsuario, '');

            // Bloque try-catch creado para evitar posibles errores con la BD
            try{

                // Si el nombre de usuario ya esta en uso, guardamos un mensaje de error en el array
                // Si el nombre de usuario no esta en uso, guardamos al nuevo usuario en la BD, lo asignamos a la sesión, y guardamos un mensaje 
                //      de que se ha accedido satisfactoriamente en el array, además de una ruta a la cual se redirigirá tras iniciarse sesión, 
                //      dependiendo esta ruta de si se viene de una página anterior o no
                if(intval($numeroUsuariosMismoNombre->nombre_usuario) > 0){
                    $array['mensaje'] = 'Nombre de usuario repetido';
                }else{
                    $this->accionesUsuarioModel->insertaUsuario($nombre, $apellidos, $nombreUsuario, $contrasenia);
                    $this->session->set('usuario', $nombreUsuario);
                    $array['mensaje'] = 'Cuenta creada';
                    $array['ruta'] = base_url().$this->session->get('paginaAnterior');
                }
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
     * Método que permite obtener los currículums de un usuario, y devolverlos dentro de un JSON. Se obtienen mediante una petición AJAX para evitar
     *      problemas de carga de páginas, ya que el usuario puede tener muchos currículums creados.
     * @since 1.0
     * @access public
     */
    public function obtieneCurriculumsUsuario(){

        // Obtenemos los currículums asociados a un usuario
        $arrayCurriculums = $this->accionesUsuarioModel->obtieneCurriculumsUsuario($this->session->get('usuario'));

        // Array que almacenará los currículums del usuario
        $arrayCurriculumsFinal = array();

        // Recorremos los currículums del usuario y los guardamos en el array, formateando los datos si hace falta
        foreach($arrayCurriculums as $clave => $valor){
            $arrayCurriculumsFinal[$clave]['nombre'] = $valor['nombre'];
            $arrayCurriculumsFinal[$clave]['fechaRealizacion'] = $this->formateaFechaGuiones($valor['fecha_realizacion']);
            $arrayCurriculumsFinal[$clave]['id'] = $valor['id'];
        }

        // Devolvemos el array en formato JSON
        echo json_encode($arrayCurriculumsFinal);
    }

    /**
     * Método que permite borrar un currículum del usuario.
     * @since 1.0
     * @access public
     */
    public function borraCurriculumUsuario(){

        // Recogemos los parámetros recibidos por POST
        $idCurriculum = $this->request->getPost('idCurriculum');

        // Array que almacenará el mensaje y la ruta que se devuelva al navegador
        $array = array();

        // Bloque try-catch creado para evitar posibles errores con la BD
        try{

            // Comprobamos si el currículum existe
            $comprobadorExistenciaCurriculum = $this->accionesUsuarioModel->compruebaCurriculum($idCurriculum);

            // Si no existe, arrojamos una excepción
            if(!$comprobadorExistenciaCurriculum){
                throw new DatabaseException('Currículum inexistente');
            }

            // Borramos el currículum deseado
            $this->accionesUsuarioModel->borraUsuarioOCurriculum($idCurriculum, 'Curriculum');

            // Guardamos un mensaje de que se ha completado el borrado en el array
            $array['mensaje'] = 'Completado';
        }catch(DatabaseException $e){

            // Si da alguna excepción, mostramos un mensaje de error, además de poner que error ha dado
            $array['mensaje'] = 'Error en BD';
            $array['error'] = 'Error en la BD: '.$e->getMessage();
        }

        // Devolvemos el array en formato JSON
       	echo json_encode($array);
    }

    /**
     * Método que permite obtener los datos de un currículum para acceder a la pantalla de modificación de estos.
     * @since 1.0
     * @access public
     */
    public function modificaCurriculumUsuario(){

        // Recogemos los parámetros recibidos por POST
        $idCurriculum = $this->request->getPost('idCurriculum');

        // Array que almacenará el mensaje y la ruta que se devuelva al navegador
        $array = array();

        // Obtenemos los datos del currículum
        $arrayDatosCurriculum = $this->formateaDatosCurriculumBD($idCurriculum);

        if($arrayDatosCurriculum !== null){

            // Recorremos los datos del currículum para guardarlos en sesión
            foreach($arrayDatosCurriculum as $clave => $valor){
                if($clave == 'foto'){

                    // Si la clave es la foto, guardamos la foto en base64 en la sesión
                    $this->session->set('foto', 'data:image/'.strtolower($arrayDatosCurriculum['extensionFoto']).';base64,'.base64_encode($valor));
                }else if($clave == 'fechaNac'){

                    // Si la clave es la fecha de nacimiento, la formateamos antes de guardarla en sesión
                    $this->session->set('fechaNac', $this->formateaFechaGuiones($valor));
                }else if($clave == 'estudios' || $clave == 'experiencia' || $clave == 'idiomas' || $clave == 'datosInteres'){

                    // Si la clave es estudios, experiencia, idiomas o datos de interés, formateamos los datos que contienen
                    $this->formateaDatosNoPersonalesModificaCurriculum($valor, $clave);
                }else{

                    // Si no es ninguna de las claves anteriores, la guardamos directamente en la sesión
                    $this->session->set($clave, $valor);
                }
            }

            // Guardamos en el array un mensaje de que se ha completado la obtención de datos, además de una ruta a donde se redirigirá al usuario
            $array['mensaje'] = 'Completado';
            $array['ruta'] = base_url().'CreacionCurriculum';
        }else{

            // Guardamos en el array un mensaje de error que indique que el currículum buscado es inexistente
            $array['mensaje'] = 'Error';
            $array['mensajeDetallado'] = 'Currículum inexistente'; 
        }

        // Devolvemos el array en formato JSON
       	echo json_encode($array);
    }

    /**
     * Método que permite modificar los datos de la cuenta de un usuario.
     * @since 1.0
     * @access public
     */
    public function modificaCuentaUsuario(){

        // Recogemos los parámetros recibidos por POST
        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');
        $apellidos = $this->request->getPost('apellidos');
        $nombreUsuario = $this->request->getPost('nombreUsuario');
        $contrasenia = $this->request->getPost('contrasenia');

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
        $this->generaArrayValidacionDato('Normal', $nombreUsuario, 'El nombre de usuario debe tener entre 8 y 20 caracteres, y solo puede tener letras, números y guiones bajos.', 
            array('minimaLongitud' => 8, 'maximaLongitud' => 20, 'campo' => 'El nombre de usuario', 'patron' => '/^[\w]{8,20}$/'), $arrayValidacion);
        $this->generaArrayValidacionDato('Normal', $contrasenia, 'La contraseña debe tener entre 8 y 20 caracteres, y solo puede tener letras y números.', 
            array('minimaLongitud' => 8, 'maximaLongitud' => 20, 'campo' => 'La contraseña', 'patron' => '/^[A-Za-z\d]{8,20}$/'), $arrayValidacion);


        // Comprobamos si los datos cumplen los patrones especificados
        if($this->compruebaDatosRecibidosPost($arrayValidacion, $array)){
        
            // Bloque try-catch creado para evitar posibles errores con la BD
            try{

                // Comprobamos si el usuario existe
                $comprobadorExistenciaUsuario = $this->accionesUsuarioModel->compruebaUsuarioPorIdYNombreUsuario($id, $this->session->get('usuario'));

                // Si no existe, arrojamos una excepción
                if(intval($comprobadorExistenciaUsuario->id) == 0){
                    throw new DatabaseException('Usuario inexistente');
                }

                // Si el nombre de usuario indicado es distinto al que tenemos en la sesión, miramos a ver si es un nombre repetido o no
                // Si el nombre de usuario indicado es el mismo que tenemos en la sesión, modificamos directamente los datos del usuario
                if($nombreUsuario != $this->session->get('usuario')){

                    // Comprobamos si el nombre de usuario ya esta en uso
                    $numeroUsuariosMismoNombre = $this->accionesUsuarioModel->compruebaUsuario($nombreUsuario, '');
        
                    // Si el nombre de usuario ya esta en uso, guardamos un mensaje de error en el array
                    // Si el nombre de usuario no esta en uso, guardamos al nuevo usuario en la BD, lo asignamos a la sesión, y guardamos un mensaje 
                    //      en el array de que se han modificado los datos 
                    if(intval($numeroUsuariosMismoNombre->nombre_usuario) > 0){
                        $array = array('mensaje' => 'Nombre de usuario repetido');
                    }else{
                        $this->accionesUsuarioModel->modificaUsuario($id, $nombre, $apellidos, $nombreUsuario, $contrasenia);
                        $this->session->set('usuario', $nombreUsuario);
                        $array['mensaje'] = 'Completado';
                    }
                }else{

                    // Modificamos los datos del usuario, además de guardar un mensaje en el array para indicar que se han modificado los datos
                    $this->accionesUsuarioModel->modificaUsuario($id, $nombre, $apellidos, $nombreUsuario, $contrasenia);
                    $array['mensaje'] = 'Completado';
                }
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
     * Méotdo que permite borrar la cuenta de un usuario.
     * @since 1.0
     * @access public
     */
    public function borraCuentaUsuario(){

        // Recogemos los parámetros recibidos por POST
        $id = $this->request->getPost('id');

        // Array que almacenará el mensaje y la ruta que se devuelva al navegador
        $array = array();

        // Bloque try-catch creado para evitar posibles errores con la BD
        try{

            // Comprobamos si el usuario existe
            $comprobadorExistenciaUsuario = $this->accionesUsuarioModel->compruebaUsuarioPorIdYNombreUsuario($id, $this->session->get('usuario'));

            // Si no existe, arrojamos una excepción
            if(intval($comprobadorExistenciaUsuario->id) == 0){
                throw new DatabaseException('Usuario inexistente');
            }

            // Borramos al usuario de la BD y de la sesión, y guardamos un mensaje en el array de que se ha borrado correctamente, además
            //      de una ruta a la cual se redirigirá el usuario
            $this->accionesUsuarioModel->borraUsuarioOCurriculum($this->session->get('usuario'), 'Usuario');
            $this->session->remove('usuario');
            $array['mensaje'] = 'Completado';
            $array['ruta'] = base_url();
        }catch(DatabaseException $e){

            // Si da alguna excepción, mostramos un mensaje de error, además de poner que error ha dado
            $array['mensaje'] = 'Error en BD';
            $array['error'] = 'Error en la BD: '.$e->getMessage();
        }

        // Devolvemos el array en formato JSON
        echo json_encode($array);
    }

    /**
     * Método que permite generar el PDF de un currículum almacenado en BD
     * @param integer $idCurriculum Identificador del currículum que se va a exportar a PDF.
     * @since 1.0
     * @access public
     */
    public function generaPdfCurriculumDeBD($idCurriculum){

        // Si no hay ningún usuario introducido, forzamos una redirección a la pantalla de inicio de sesión
        if($this->session->get('usuario') === null){
            return redirect()->to(base_url().'InicioSesion');
        }

        // Obtenemos todos los datos del currículum
        $arrayDatosCurriculum = $this->formateaDatosCurriculumBD($idCurriculum);

        // Si se ha obtenido un currículum de BD, generamos el PDF asociado a él
        if($arrayDatosCurriculum !== null){
            
            // Array que contendrá todos los datos del currículum, formateados para generar el PDF de manera sencilla
            $data = array();

            // Array que contendrá todos los listados de estudios, experiencia, idiomas y datos de interés
            $data['listadoElementosNoPersonales'] = array();

            // Recorremos los datos del currículum
            foreach($arrayDatosCurriculum as $clave => $valor){
                if($clave == 'foto'){

                    // Si la clave es la foto, guardamos la foto en base64 en el array
                    $data['foto'] = 'data:image/'.strtolower($arrayDatosCurriculum['extensionFoto']).';base64,'.base64_encode($valor);
                }else if($clave == 'fechaNac'){

                    // Si la clave es la fecha de nacimiento, la formateamos antes de guardarla en el array
                    $data['fechaNac'] = $this->formateaFechaGuiones($valor);
                }else if($clave == 'estudios' || $clave == 'experiencia' || $clave == 'idiomas' || $clave == 'datosInteres'){

                    // Si la clave es estudios, experiencia, idiomas o datos de interés, debemos formatear los datos que contienen

                    // Array que guardará los datos que queremos obtener del array de datos que vamos a leer
                    $arrayCampos = [];

                    // Cadena que contendrá la clave del orden del dato que vamos a leer
                    $claveOrden = '';

                    // Cadena que contendrá el titulo de la sección que vamos a leer
                    $titulo = '';

                    // Hacemos un switch, filtrando por la clave que estamos recorriendo, para asignar los valores de las variables
                    //      de control declaradas previamente
                    switch($clave){
                        case 'estudios':
                            $arrayCampos = ['titulo', 'centro', 'fechaInicio', 'fechaFin'];
                            $claveOrden = 'Estudios';
                            $titulo = 'Estudios';
                            break;
                        case 'experiencia':
                            $arrayCampos = ['puesto', 'centro', 'fechaInicio', 'fechaFin'];
                            $claveOrden = 'Experiencia';
                            $titulo = 'Experiencia laboral';
                            break;
                        case 'idiomas':
                            $arrayCampos = ['titulo', 'centro', 'fechaInicio', 'fechaFin'];
                            $claveOrden = 'Idiomas';
                            $titulo = 'Idiomas';
                            break;
                        case 'datosInteres':
                            $arrayCampos = ['dato'];
                            $claveOrden = 'DatosInteres';
                            $titulo = 'Datos de interés';
                            break;
                    }

                    // Formatamos el array de datos, y lo guardamos en el array
                    $this->formateaDatosNoPersonalesPDF($valor, $arrayDatosCurriculum, $claveOrden, $titulo, $arrayCampos,
                        $data, 'ARRAY', true);
                }else if($clave != 'extensionFoto' && $clave != 'cronologia' && $clave != 'orden1' && $clave != 'orden2' && $clave != 'orden3'
                    && $clave != 'orden4'){

                    // Si la clave no es la extensión de la foto, o la cronología, o algún valor del orden, lo guardamos directamente en el array
                    $data[$clave] = $valor;
                }
            }

            // Ordenamos el array de datos no personales
            ksort($data['listadoElementosNoPersonales']);

            // Generamos el PDF
            $this->generaPdf($data);
        }else{

            // Si no se ha encontrado ningún currículum, mostramos un mensaje de error al usuario
            echo json_encode(array('mensaje' => 'Error', 'mensajeDetallado' => 'Curriculum inexistente'));
        }
    }

    ////////////////////
    ////// MÉTODOS QUE SIRVEN DE APOYO PARA REALIZAR DETERMINADAS FUNCIONES
    ////////////////////

    /**
     * Método que permite formatear los estudios, experiencia, idiomas o datos de interés para poder tenerlos
     *      en el formato adecuado para modificar los datos de un currículum, y los guarda en sesión.
     * @param array $arrayDatos Array de datos a formatear.
     * @param string $claveSesion Cadena que representa la clave con la que se guardarán los datos en sesión.
     * @since 1.0
     * @access private
     */
    private function formateaDatosNoPersonalesModificaCurriculum($arrayDatos, $claveSesion){

        // Array auxiliar para guardar los datos
        $arrayAux = array();

        // Recorremos el array recibido por parámetro
        foreach($arrayDatos as $k => $dato){

            // Si estamos recorriendo los datos de interés, solo hay que formatear un dato
            if($claveSesion == 'datosInteres'){
                $arrayAux[$k]['dato1'] = $dato['dato'];
            }else if($claveSesion == 'estudios' || $claveSesion == 'experiencia' || $claveSesion == 'idiomas'){

                // Si estamos recorriendo los estudios, experiencia o idiomas, hay que formatear 4 datos, teniendo en cuenta
                //      que el 1º se obtiene de forma diferente en la experiencia que en el resto, y que el 4º depende de 
                //      si almacena una fecha o no para formatearlo de una manera u otra
                if($claveSesion == 'experiencia'){
                    $arrayAux[$k]['dato1'] = $dato['puesto'];
                }else if($claveSesion == 'estudios' || $claveSesion == 'idiomas'){
                    $arrayAux[$k]['dato1'] = $dato['titulo'];
                }
                $arrayAux[$k]['dato2'] = $dato['centro'];
                $arrayAux[$k]['dato3'] = $this->formateaFechaGuiones($dato['fechaInicio']);
                $arrayAux[$k]['dato4'] = $dato['fechaFin'] == 'Actualidad' ? 'Actualidad' : $this->formateaFechaGuiones($dato['fechaFin']);
            }
        }

        // Metemos el array formateado en sesión
        $this->session->set($claveSesion, $arrayAux);
    }

    /**
     * Método que permite obtener todos los datos de un currículum, y formatearlos metiendolos dentro de un array
     *      para devolverlos en un formato adecuado para el resto de métodos del controlador.
     * @param integer $curriculum Identificador del currículum del que se desean obtener datos.
     * @return array Array que contiene todos los datos del currículum formateados.
     * @since 1.0
     * @access private
     */
    private function formateaDatosCurriculumBD($curriculum){

        // Array que contendrá todos los datos del currículum formateados
        $array = array();

        // Obtenemos los datos del currículum y sus datos personales
        $arrayBDCurriculum = $this->accionesUsuarioModel->obtieneDatosCurriculum($curriculum);

        if($arrayBDCurriculum == null){
            return null;
        }

        // Guardamos los datos del currículum y sus datos personales en el array
        $array['idCurriculum'] = $curriculum;
        $array['nombreCurriculum'] = $arrayBDCurriculum->nombreCurriculum;
        $array['orden1'] = $arrayBDCurriculum->elemento_1;
        $array['orden2'] = $arrayBDCurriculum->elemento_2;
        $array['orden3'] = $arrayBDCurriculum->elemento_3;
        $array['orden4'] = $arrayBDCurriculum->elemento_4;
        $array['cronologia'] = $arrayBDCurriculum->cronologia;
        $array['foto'] = $arrayBDCurriculum->foto;
        $array['extensionFoto'] = $arrayBDCurriculum->extension_foto;
        $array['nombre'] = $arrayBDCurriculum->nombreDatoPersonal;
        $array['apellidos'] = $arrayBDCurriculum->apellidos;
        $array['fechaNac'] = $arrayBDCurriculum->fecha_nac;
        $array['direccion'] = $arrayBDCurriculum->direccion;
        $array['correo'] = $arrayBDCurriculum->correo;
        $array['telefono'] = $arrayBDCurriculum->telefono;
        $array['whatsapp'] = $arrayBDCurriculum->whatsapp;
        $array['linkedin'] = $arrayBDCurriculum->linkedin;

        // Guardamos los estudios, experiencia, idiomas y datos de interés en el array
        $array['estudios'] = $this->convierteDatosNoPersonalesCurriculumBD($curriculum, 'est_exp_idi', 
            'titulo_puesto, centro, fecha_inicio, fecha_fin, condicion_actual', 'Estudio');
        $array['experiencia'] = $this->convierteDatosNoPersonalesCurriculumBD($curriculum, 'est_exp_idi', 
            'titulo_puesto, centro, fecha_inicio, fecha_fin, condicion_actual', 'Experiencia');
        $array['idiomas'] = $this->convierteDatosNoPersonalesCurriculumBD($curriculum, 'est_exp_idi', 
            'titulo_puesto, centro, fecha_inicio, fecha_fin, condicion_actual', 'Idioma');
        $array['datosInteres'] = $this->convierteDatosNoPersonalesCurriculumBD($curriculum, 'dato_interes', 'dato');

        // Retornamos el array
        return $array;
    }

    /**
     * Método que permite obtener los estudios, experiencia, idiomas o datos de interés de un currículum concreto de la BD, y 
     *      formatearlos dentro de un array en un formato adecuado para el resto de métodos del controlador.
     * @param integer $curriculum Identificador del currículum del que se desean los datos.
     * @param string $tabla Tabla de la que se obtendrán los datos.
     * @param string $select Columnas que se desean obtener, separadas por comas.
     * @param string $tipoDato Tipo de dato a recuperar de la BD. Por defecto, tiene el valor ''.
     * @return array Array con los datos de la BD formateados.
     * @since 1.0
     * @access private
     */
    private function convierteDatosNoPersonalesCurriculumBD($curriculum, $tabla, $select, $tipoDato = ''){

        // Obtenemos los datos deseados, teniendo en cuenta de que tabla se desean obtener
        $arrayBD = array();
        if($tabla == 'dato_interes'){
            $arrayBD = $this->accionesUsuarioModel->obtieneDatosRelacionadosCurriculum($curriculum, $tabla, 'dat_int', $select, 
                $tipoDato);
        }else{
            $arrayBD = $this->accionesUsuarioModel->obtieneDatosRelacionadosCurriculum($curriculum, $tabla, 'est_exp_idi', $select, 
                $tipoDato);
        }

        // Array que contendrá todos los datos formateados
        $array = array();

        // Array que contendrá las columnas que se desean obtener
        $clavesSelect = explode(',', $select);

        // Recorremos el array obtenido de BD
        foreach($arrayBD as $clave => $valor){

            // Si estamos recorriendo los datos de interés, solo hay que formatear un dato
            if($tabla == 'dato_interes'){
                $array[$clave]['dato'] = $valor[trim($clavesSelect[0])];
            }else if($tabla == 'est_exp_idi'){

                // Si estamos recorriendo los estudios, experiencia o idiomas, hay que formatear 4 datos, teniendo en cuenta
                //      que el 1º se guarda de forma diferente en la experiencia que en el resto, y que el 4º depende de 
                //      si el 5º dato tiene valor 0 o 1 para formatearlo de una manera u otra
                if($tipoDato == 'Experiencia'){
                    $array[$clave]['puesto'] = $valor[trim($clavesSelect[0])];
                }else if($tipoDato == 'Estudio' || $tipoDato == 'Idioma'){
                    $array[$clave]['titulo'] = $valor[trim($clavesSelect[0])];
                }
                $array[$clave]['centro'] = $valor[trim($clavesSelect[1])];
                $array[$clave]['fechaInicio'] = $valor[trim($clavesSelect[2])];
                $array[$clave]['fechaFin'] = $valor[trim($clavesSelect[4])] == 1 ? 'Actualidad' : $valor[trim($clavesSelect[3])];
            }
        }

        // Retornamos el array
        return $array;
    }
}