<?php

namespace App\Model;

use CodeIgniter\Model;

/**
 * Clase que actúa de modelo del controlador 'AccionesUsuario' para acceder a la BD.
 * @version 1.0
 * @author Aitor Díez Arnáez
 * @access public
 */
class AccionesUsuarioModel extends Model{

    /**
     * Constructor del modelo, que inicia la conexión con la BD.
     * @since 1.0
     * @access public
     */
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    ////////////////////
    ////// MÉTODOS QUE SE PUEDEN LLAMAR EN LOS CONTROLADORES PARA ACCEDER A LA BD
    ////////////////////

    /**
     * Método que permite comprobar si existe una cuenta con el nombre de usuario y contraseña indicados.
     * @param string $nombreUsuario Nombre de usuario indicado.
     * @param string $contrasenia Contraseña indicada. Si no se quiere indicar ninguna, se puede pasar una cadena vacía.
     * @return object Objeto que contiene un único dato (nombre_usuario), con el conteo de veces que aparece la cuenta
     *      indicada en la BD.
     * @since 1.0
     * @access public
     */
    public function compruebaUsuario($nombreUsuario, $contrasenia){
        $builder = $this->db->table('usuario');
        $builder->selectCount('nombre_usuario');
        $builder->where('nombre_usuario', $nombreUsuario);
        if($contrasenia != ''){
            $builder->where('contrasenia', $contrasenia);
        }
        $query = $builder->get()->getRow(0);
        return $query;
    }

    /**
     * Método que permite comprobar si existe una cuenta con el id y nombre de usuario indicados.
     * @param string $id Identificador de usuario indicado.
     * @param string $nombreUsuario Nombre de usuario indicado.
     * @return object Objeto que contiene un único dato (id), con el conteo de veces que aparece la cuenta indicada en la BD.
     * @since 1.0
     * @access public
     */
    public function compruebaUsuarioPorIdYNombreUsuario($id, $nombreUsuario){
        $builder = $this->db->table('usuario');
        $builder->selectCount('id');
        $builder->where('id', $id);
        $builder->where('nombre_usuario', $nombreUsuario);
        $query = $builder->get()->getRow(0);
        return $query;
    }

    /**
     * Método que permite insertar los datos de una cuenta nueva en la BD.
     * @param string $nombre Nombre de la cuenta a insertar.
     * @param string $apellidos Apellidos de la cuenta a insertar.
     * @param string $nombreUser Nombre de usuario de la cuenta a insertar.
     * @param string $pass Contraseña de la cuenta a insertar.
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     * @since 1.0
     * @access public
     */
    public function insertaUsuario($nombre, $apellidos, $nombreUser, $pass){

        // Array de datos a insertar
        $data = array(
            'nombre' => $nombre, 
            'apellidos' => $apellidos, 
            'nombre_usuario' => $nombreUser, 
            'contrasenia' => $pass
        );

        // Inserción de datos
        $builder = $this->db->table('usuario');
        $builder->insert($data);
    }

    /**
     * Método que permite obtener el nombre, fecha de realización e id de los currículums creados por un usuario.
     * @param string $usuario Nombre del usuario del que se desean sus currículums.
     * @return array Array con los currículum del usuario, ordenados por fecha de realización en orden descendente.
     * @since 1.0
     * @access public
     */
    public function obtieneCurriculumsUsuario($usuario){
        $builder = $this->db->table('curriculum');
        $builder->select('curriculum.nombre, curriculum.fecha_realizacion, curriculum.id');
        $builder->join('usuario', 'curriculum.id_usuario = usuario.id');
        $builder->where('usuario.nombre_usuario', $usuario);
        $builder->orderBy('fecha_realizacion', 'DESC');
        $query = $builder->get();
        return $query->getResult('array');
    }

    /**
     * Función que comprueba si existe un currículum en la BD.
     * @param integer $idCurriculum Identificador del currículum.
     * @return boolean true si el currículum existe, y false en caso contrario.
     * @since 1.0
     * @access public
     */
    public function compruebaCurriculum($idCurriculum){

        // Obtención del id del currículum
        $builder = $this->db->table('curriculum');
        $builder->select('id');
        $builder->where('id', $idCurriculum);
        $query = $builder->get()->getRow(0);

        // Comprobamos que el currículum existe
        if($query == ''){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Método que permite obtener los datos de un currículum y sus datos personales.
     * @param integer $curriculum Identificador del currículum del que se desean obtener los datos.
     * @return object Objeto con los datos del currículum y sus datos personales.
     * @since 1.0
     * @access public
     */
    public function obtieneDatosCurriculum($curriculum){

        // Obtención de los datos del currículum y de los datos personales de este
        $builder = $this->db->table('curriculum c');
        $builder->select('c.nombre as nombreCurriculum, c.elemento_1, c.elemento_2, c.elemento_3, c.elemento_4, c.cronologia, '.
            'dp.foto, dp.extension_foto, dp.nombre as nombreDatoPersonal, dp.apellidos, dp.fecha_nac, dp.direccion, dp.correo, '.
            'dp.telefono, dp.whatsapp, dp.linkedin');
        $builder->join('dato_personal dp', 'c.id_dato = dp.id');
        $builder->where('c.id', $curriculum);
        $query = $builder->get()->getRow(0);
        
        return $query;
    }

    /**
     * Método que permite obtener los estudios, experiencia, idiomas o datos de interés de un currículum.
     * @param integer $curriculum Identificador del currículum.
     * @param string $tabla Tabla de la que se desean obtener los datos.
     * @param string $tablaRelacion Tabla que almacena la relación de los datos a obtener y el currículum, sin indicar
     *      la parte final de esta ('_cur').
     * @param string $select Campos, separados por comas, que se desean obtener de la tabla.
     * @param string $tipoDato Tipo de dato que se desea obtener de la BD. Se utiliza en la tabla de 'est_exp_idi'.
     *      Si no se quiere obtener ningún tipo de dato concreto, se puede pasar una cadena vacía
     * @return array Array con los datos de la BD.
     * @since 1.0
     * @access public
     */
    public function obtieneDatosRelacionadosCurriculum($curriculum, $tabla, $tablaRelacion, $select, $tipoDato){

        // Obtención de los datos de la BD
        $builder = $this->db->table($tabla);
        $builder->select($select);
        $builder->join($tablaRelacion.'_cur', $tabla.'.id = '.$tablaRelacion.'_cur.id_'.$tabla);
        $builder->where($tablaRelacion.'_cur.id_curriculum', $curriculum);
        if($tipoDato != ''){
            $builder->where($tabla.'.tipo_dato', $tipoDato);
        }
        $query = $builder->get()->getResult('array');

        return $query;
    }

    /**
     * Método que permite obtener los datos de un usuario a partir de su nombre de usuario.
     * @param string $nombreUsuario Nombre del usuario del que se desean sus datos.
     * @return object Objeto que contiene los datos del usuario.
     * @since 1.0
     * @access public
     */
    public function obtieneDatosUsuario($nombreUsuario){
        $builder = $this->db->table('usuario');
        $builder->select('id, nombre, apellidos, contrasenia');
        $builder->where('nombre_usuario', $nombreUsuario);
        $query = $builder->get()->getRow(0);
        return $query;
    }

    /**
     * Método que permite modificar los datos de un usuario deseado.
     * @param integer $id Id del usuario a modificar.
     * @param string $nombre Nombre nuevo del usuario.
     * @param string $apellidos Apellidos nuevos del usuario
     * @param string $nombreUsuario Nombre de usuario nuevo del usuario.
     * @param string $contrasenia Contraseña nueva del usuario.
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     * @since 1.0
     * @access public
     */
    public function modificaUsuario($id, $nombre, $apellidos, $nombreUsuario, $contrasenia){

        // Array con los datos nuevos del usuario
        $dataUsuario = array(
            'nombre' => $nombre, 
            'apellidos' => $apellidos, 
            'nombre_usuario' => $nombreUsuario, 
            'contrasenia' => $contrasenia
        );

        // Actualización de los datos
        $builder = $this->db->table('usuario');
        $builder->where('id', $id);
        $builder->update($dataUsuario);
    }

    /**
     * Método que permite borrar todos los datos de un usuario o de un currículum
     * @param string $clave Clave que permite identificar el dato a borrar.
     * @param string $tipoBorrado Tipo de dato que se va a borrar. Los posibles datos son 'Usuario' y 'Curriculum'.
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     * @since 1.0
     * @access public
     */
    public function borraUsuarioOCurriculum($clave, $tipoBorrado){

        // Borrado de datos
        if($tipoBorrado == 'Usuario'){
            $builder = $this->db->table('usuario');
            $builder->where('nombre_usuario', $clave);
            $builder->delete();
        }else{
            $builder = $this->db->table('curriculum');
            $builder->where('id', $clave);
            $builder->delete();
        }

        // Borrado de todos los datos relacionados con los currículums creados por el usuario
        $this->borraDatosCurriculum('dato_personal', 'curriculum', 'id_dato');
        $this->borraDatosCurriculum('est_exp_idi', 'est_exp_idi_cur', 'id_est_exp_idi');
        $this->borraDatosCurriculum('dato_interes', 'dat_int_cur', 'id_dato_interes');
    }

    ////////////////////
    ////// MÉTODOS QUE SIRVEN DE APOYO PARA REALIZAR DETERMINADAS FUNCIONES
    ////////////////////

    /**
     * Método que permite borrar los datos relacionados con un currículum, si estos ya no tienen ninguna relación.
     * @param string $tablaOrigen Tabla de origen de los datos a borrar.
     * @param string $tablaRelacion Tabla donde los datos a borrar están relacionados con los del currículum.
     * @param string $nombreCampoTablaRelacion Nombre del campo que actúa como FK en la tabla de la relación.
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     * @since 1.0
     * @access private
     */
    private function borraDatosCurriculum($tablaOrigen, $tablaRelacion, $nombreCampoTablaRelacion){

        // Obtenemos el número de apariencias de los datos a borrar en la tabla de la relación
        $builder = $this->db->table($tablaOrigen);
        $builder->select($tablaOrigen.'.id, COUNT('.$tablaRelacion.'.'.$nombreCampoTablaRelacion.') AS conteo');
        $builder->join($tablaRelacion, $tablaOrigen.'.id = '.$tablaRelacion.'.'.$nombreCampoTablaRelacion, 'left');
        $builder->groupBy($tablaOrigen.'.id');
        $query = $builder->get()->getResult('array');

        // Recorremos el array que nos ha devuelto la consulta
        foreach($query as $value){

            // Si el dato no aparece ninguna vez en la tabla de la relación, lo borramos
            if($value['conteo'] == 0){
                $builder = $this->db->table($tablaOrigen);
                $builder->where('id', $value['id']);
                $builder->delete();
            }
        }
    }
}