<?php

namespace App\Controllers;

/**
 * Controlador que permite tener el método con la vista principal de la aplicación.
 * @version 1.0
 * @author Aitor Díez Arnáez
 * @access public
 */
class Principal extends FuncionesComunes
{
    ////////////////////
    ////// MÉTODOS QUE ACTUAN DE CONTROLADORES, A LOS CUALES SE ACCEDE A TRAVÉS DEL NAVEGADOR
    ////////////////////

    /**
     * Método que permite retornar la vista principal de la aplicación.
     * @return string Pantalla principal que se mostrará en el navegador.
     * @since 1.0
     * @access public
     */
    public function muestraPaginaPrincipal(){

        // Eliminamos la página anterior de la sesión
        $this->session->remove('paginaAnterior');

        // Eliminamos todos los datos relacionados con la creación de currículum de la sesión
        $this->borraDatosSesionCurriculum();

        // Generamos la vista de la página
        return $this->generaVistaPrincipal('CV-FACIL', ['assets/css/principal.css'], ['assets/js/principal.js'], 'principal', 
            true, false, true);
    }

    ////////////////////
    ////// MÉTODOS QUE ACTUAN DE CONTROLADORES, A LOS CUALES SE ACCEDE A TRAVÉS DE PETICIONES AJAX
    ////////////////////

    /**
     * Función que permite poder finalizar la sesión del usuario deseado.
     * @since 1.0
     * @access public
     */
    public function finalizaSesionUsuario(){

        // Borramos el dato del usuario de la sesión
        $this->session->remove('usuario');

        // Borramos todos los datos del currículum que se esta modificando, si es que se esta modificando alguno, lo cual
        //      sabremos si hay un dato de sesión con clave 'idCurriculum'
        if($this->session->get('idCurriculum') !== null){
            $this->borraDatosSesionCurriculum();
        }

        // Creamos un array con el mensaje de completado y una ruta que referencie a la página anterior, si es que
        //      existe una página anterior en la sesión y no es ni la de listado de currículums ni la de gestión de cuenta
        $array = array('mensaje' => 'Completado');
        if($this->session->get('paginaAnterior') === null || $this->session->get('paginaAnterior') == 'ListadoCurriculums' 
            || $this->session->get('paginaAnterior') == 'GestionCuenta'){
            $array['ruta'] = base_url();
        }else{
            $array['ruta'] = base_url().$this->session->get('paginaAnterior');
        }

        // Imprimimos el array como un JSON
       	echo json_encode($array);
    }
}
