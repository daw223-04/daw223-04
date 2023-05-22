<?php

namespace App\Model;

use CodeIgniter\Model;

/**
 * Clase que actúa de modelo del controlador 'CreacionCurriculums' para acceder a la BD.
 * @version 1.0
 * @author Aitor Díez Arnáez
 * @access public
 */
class CreacionCurriculumsModel extends Model{

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
     * Método que permite insertar los datos personales indicados, si no existe un dato personal con dichos datos, y devolver el 
     *      identificador del dato personal deseado.
     * @param string $foto Cadena que contiene todos los datos de la foto a guardar.
     * @param string $extensionFoto Extensión de la foto a guardar.
     * @param string $nombre Nombre del dato personal a guardar.
     * @param string $apellidos Apellidos del dato personal a guardar.
     * @param string $fechaNac Fecha de nacimiento del dato personal a guardar.
     * @param string $direccion Dirección del dato personal a guardar.
     * @param string $correo Correo del dato personal a guardar.
     * @param string $telefono Telefono del dato personal a guardar.
     * @param string $whatsapp Whatsapp del dato personal a guardar.
     * @param string $linkedin LinkedIn del dato personal a guardar.
     * @return integer Id del dato personal a insertar, o del que tiene los mismos datos que el que se desea insertar.
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     * @since 1.0
     * @access public
     */
    public function insertaDatosPersonales($foto, $extensionFoto, $nombre, $apellidos, $fechaNac, $direccion, $correo, $telefono, 
        $whatsapp, $linkedin){

        // Array de datos a insertar
        $data = array(
            'foto' => $foto,
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nac' => $fechaNac,
            'direccion' => $direccion,
            'correo' => $correo,
            'telefono' => $telefono,
            'extension_foto' => $extensionFoto,
        );

        // Comprobamos si se ha pasado un whatsapp o linkedin para meterlos al array, o meter un valor nulo
        if($whatsapp != ''){
            $data['whatsapp'] = $whatsapp;
        }else{
            $data['whatsapp'] = null;
        }
        if($linkedin != ''){
            $data['linkedin'] = $linkedin;
        }else{
            $data['linkedin'] = null;
        }

        // Consultamos a la BD si hay un dato personal con los datos que se desean insertar
        $builder = $this->db->table('dato_personal');
        $builder->select('id');
        $builder->where($data);
        $query = $builder->get()->getRow(0);

        // Si la query contiene algo, devolvemos el id obtenido
        // Si la query no contiene nada, insertamos el dato personal, además de obtener su id asociado durante la inserción
        if($query != ''){
            return $query->id;
        }else{
            $builder->insert($data);

            $builder->select('id');
            $builder->orderBy('id', 'DESC');
            $builder->limit(1);
            $query = $builder->get()->getRow(0);
            return $query->id;
        }
    }

    /**
     * Método que permite insertar los datos del currículum indicados.
     * @param string $nombre Nombre del currículum a insertar.
     * @param string $fechaRealizacion Fecha de creación del currículum.
     * @param string $usuario Nombre del usuario que creo el currículum.
     * @param integer $dato Id del dato personal que esta relacionado con el currículum.
     * @param string $elemento1 Clave del 1º elemento que aparecerá en el currículum.
     * @param string $elemento2 Clave del 2º elemento que aparecerá en el currículum.
     * @param string $elemento3 Clave del 3º elemento que aparecerá en el currículum.
     * @param string $elemento4 Clave del 4º elemento que aparecerá en el currículum.
     * @param string $cronologia Clave que identifica la cronología de los elementos del currículum.
     * @return integer Identificador del currículum que acabamos de insertar.
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     * @since 1.0
     * @access public
     */
    public function insertaDatosCurriculum($nombre, $fechaRealizacion, $usuario, $dato, $elemento1, $elemento2, $elemento3, 
        $elemento4, $cronologia){
        
        // Obtenemos la tablaPrincipal del usuario que creó el currículum
        $builder = $this->db->table('usuario');
        $builder->select('id');
        $builder->where('nombre_usuario', $usuario);
        $query = $builder->get()->getRow(0);
        $idUsuario = $query->id;

        // Array de datos a insertar
        $data = array(
            'nombre' => $nombre,
            'fecha_realizacion' => $fechaRealizacion,
            'elemento_1' => $elemento1,
            'elemento_2' => $elemento2,
            'elemento_3' => $elemento3,
            'elemento_4' => $elemento4,
            'cronologia' => $cronologia,
            'id_usuario' => $idUsuario,
            'id_dato' => $dato
        );

        // Inserción del currículum
        $builder = $this->db->table('curriculum');
        $builder->insert($data);

        // Obtención del ID del currículum insertado
        $builder->select('id');
        $builder->orderBy('id', 'DESC');
        $builder->limit(1);
        $query = $builder->get()->getRow(0);
        return $query->id;
    }

    /**
     * Método que permite insertar los estudios, experiencia, idiomas o datos de interés de un currículum.
     * @param integer $curriculum Id del currículum al que pertenecen los datos.
     * @param array $arrayDatos Array con los datos del elemento que se desea insertar.
     * @param string $tablaPrincipal Tabla principal en la que se insertarán los datos.
     * @param string $tablaRelacion Tabla de la relación entre el dato a insertar y el currículum. Se indica
     *      omitiendo la parte del final del nombre de la tabla ('_cur').
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     * @since 1.0
     * @access public
     */
    public function insertaDatosNoPersonalesCurriculum($curriculum, $arrayDatos, $tablaPrincipal, $tablaRelacion){

        // Comprobamos que el dato no exista ya en la tablaRelacion
        $builder = $this->db->table($tablaPrincipal);
        $builder->select('id');
        $builder->where($arrayDatos);
        $query = $builder->get()->getRow(0);

        // Si el dato existe en la tablaRelacion, simplemente insertamos la relación de este
        // Si el dato no existe en la tablaRelacion, lo insertamos tanto en esta como en la tablaRelacion de la relación
        if($query != ''){
            $array = array(
                'id_'.$tablaPrincipal => $query->id,
                'id_curriculum' => $curriculum,
            );
            $builder = $this->db->table($tablaRelacion.'_cur');
            $builder->insert($array);
        }else{

            // Inserción del dato
            $builder->insert($arrayDatos);

            // Obtención del ID con el que se ha insertado el dato
            $builder->select('id');
            $builder->orderBy('id', 'DESC');
            $builder->limit(1);
            $query = $builder->get()->getRow(0);

            // Inserción de la relación
            $array = array(
                'id_'.$tablaPrincipal => $query->id,
                'id_curriculum' => $curriculum,
            );
            $builder = $this->db->table($tablaRelacion.'_cur');
            $builder->insert($array);
        }
        
    }

    /**
     * Método que permite actualizar los datos de un currículum.
     * @param integer $idCurriculum Id del currículum a actualizar.
     * @param string $nombreCurriculum Nombre nuevo del currículum.
     * @param string $fechaRealización Fecha de realización de la modificación del currículum.
     * @param string $elemento1 Clave nueva del 1º elemento que aparecerá en el currículum.
     * @param string $elemento2 Clave mueva del 2º elemento que aparecerá en el currículum.
     * @param string $elemento3 Clave nueva del 3º elemento que aparecerá en el currículum.
     * @param string $elemento4 Clave nueva del 4º elemento que aparecerá en el currículum.
     * @param string $cronologia Clave nueva que identifica la cronología de los elementos del currículum.
     * @param integer $dato Id del dato personal nuevo que esta relacionado con el currículum.
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     * @since 1.0
     * @access public
     */
    public function actualizaCurriculum($idCurriculum, $nombreCurriculum, $fechaRealizacion, $elemento1, $elemento2, $elemento3, 
        $elemento4, $cronologia, $idDatoPersonal){

        // Array con los datos nuevos del currículum
        $dataCurriculum = array(
            'nombre' => $nombreCurriculum,
            'fecha_realizacion' => $fechaRealizacion,
            'elemento_1' => $elemento1,
            'elemento_2' => $elemento2,
            'elemento_3' => $elemento3,
            'elemento_4' => $elemento4,
            'cronologia' => $cronologia,
            'id_dato' => $idDatoPersonal,
        );

        // Actualización de los datos del currículum
        $builder = $this->db->table('curriculum');
        $builder->where('id', $idCurriculum);
        $builder->update($dataCurriculum);

        // Borrado de los datos personales que ya no tengan relación con ningún currículum
        $this->borraDatosCurriculum('dato_personal', 'curriculum', 'id_dato');
        $this->borraRelacionesDatosNoPersonalesCurriculum($idCurriculum, 'est_exp_idi');
        $this->borraRelacionesDatosNoPersonalesCurriculum($idCurriculum, 'dat_int');
    }


    /**
     * Método que permite borrar los datos relacionados con un currículum, si estos ya no tienen ninguna relación.
     * @param string $tablaOrigen Tabla de origen de los datos a borrar.
     * @param string $tablaRelacion Tabla donde los datos a borrar están relacionados con los del currículum.
     * @param string $nombreCampoTablaRelacion Nombre del campo que actúa como FK en la tablaRelacion de la relación.
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     * @since 1.0
     * @access public
     */
    public function borraDatosCurriculum($tablaOrigen, $tablaRelacion, $nombreCampoTablaRelacion){

        // Obtenemos el número de apariencias de los datos a borrar en la tablaRelacion de la relación
        $builder = $this->db->table($tablaOrigen);
        $builder->select($tablaOrigen.'.id, COUNT('.$tablaRelacion.'.'.$nombreCampoTablaRelacion.') AS conteo');
        $builder->join($tablaRelacion, $tablaOrigen.'.id = '.$tablaRelacion.'.'.$nombreCampoTablaRelacion, 'left');
        $builder->groupBy($tablaOrigen.'.id');
        $query = $builder->get()->getResult('array');

        // Recorremos el array que nos ha devuelto la consulta
        foreach($query as $value){

            // Si el dato no aparece ninguna vez en la tablaRelacion de la relación, lo borramos
            if($value['conteo'] == 0){
                $builder = $this->db->table($tablaOrigen);
                $builder->where('id', $value['id']);
                $builder->delete();
            }
        }
    }

    ////////////////////
    ////// MÉTODOS QUE SIRVEN DE APOYO PARA REALIZAR DETERMINADAS FUNCIONES
    ////////////////////

    /**
     * Método que permite borrar las relaciones de estudios, experiencia, idiomas o datos de interés con un currículum.
     * @param integer $idCurriculum Identificador del currículum.
     * @param string $tablaRelacion Tabla de la que se desean borrar las relaciones, a la cual se añadirá la cadena '_cur' al final.
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     * @since 1.0
     * @access private
     */
    private function borraRelacionesDatosNoPersonalesCurriculum($idCurriculum, $tablaRelacion){
        $builder = $this->db->table($tablaRelacion.'_cur');
        $builder->where('id_curriculum', $idCurriculum);
        $builder->delete();
    }
}