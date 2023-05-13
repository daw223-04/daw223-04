<header>
    <h2>Gestión de cuenta</h2>
</header>
<section id="seccionDatosActuales">
    <?php

        // Generamos los artículos con los textos de los datos actuales
        muestraDatoActual('Nombre actual:', $nombre, 'nombreActual');
        muestraDatoActual('Apellidos actuales: ', $apellidos, 'apellidosActual');
        muestraDatoActual('Nombre de usuario actual: ', $nombreUsuario, 'nombreUsuarioActual');
        muestraDatoActual('Contraseña actual: ', $contrasenia, 'contraseniaActual', 'articuloContraseniaActual', true);
    ?>
</section>
<section id="seccionModificaDatos">
    <header>
        <h3>Modificar datos</h3>
    </header>
    <p class="mensajeError" id="mensajeError"></p>
    <form id="articuloInputsModificacion">
        <input type="hidden" name="inputIdUsuario" id="inputIdUsuario" value=<?='"'.$id.'"'?>/>
        <?php 

            // Generamos los artículos con los inputs del formulario de modificar datos
            generaArticulosInputsModificaDatos('inputNombreNuevo', array('type' => 'text', 'value' => $nombre, 'maxlength' => '30',
                'placeholder' => 'Ej. Pepito', 'pattern' => '^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)?$'), 'Nombre nuevo:');
            generaArticulosInputsModificaDatos('inputApellidosNuevo', array('type' => 'text', 'value' => $apellidos, 'maxlength' => '50',
                'placeholder' => 'Ej. Gómez López', 'pattern' => '^(de\s(la\s)?|del\s)?[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+$'), 
                'Apellidos nuevos:');
            generaArticulosInputsModificaDatos('inputNombreUsuarioNuevo', array('type' => 'text', 'value' => $nombreUsuario, 'minlength' => '8',
                'maxlength' => '20', 'placeholder' => 'Ej. PepitoGL20', 'pattern' => '^[\w]{8,20}$'), 'Nombre de usuario nuevo:');
            generaArticulosInputsModificaDatos('inputContraseniaNuevo', array('type' => 'password', 'value' => $contrasenia, 'minlength' => '8', 
                'maxlength' => '20', 'placeholder' => 'Ej. Password1234', 'pattern' => '^[A-Za-z\d]{8,20}$'), 'Contraseña nueva:', 
                'articuloInputContrasenia');
            generaArticulosInputsModificaDatos('inputRepiteContraseniaNuevo', array('type' => 'password', 'value' => $contrasenia, 'minlength' => '8',
                'maxlength' => '20', 'placeholder' => 'Ej. Password1234', 'pattern' => '^[A-Za-z\d]{8,20}$'), 'Repita la contraseña nueva:', 
                'articuloInputContrasenia');
        ?>
        <footer>
            <input type="submit" id="btnModificacionCuenta" value="Modificar datos"/>
        </footer>
    </form>
</section>
<footer>
    <button type="button" class="botonSecundario" id="botonBorrarCuentaUsuario">Borrar cuenta</button>
</footer>

<?php
    /**
     * Función que permite generar un artículo donde se mostrará un dato actual.
     * @param string $texto Texto del parrafo que aparecerá antes del dato a mostrar.
     * @param string $dato Dato a mostrar.
     * @param string $idSpan Identificador del elemento que contiene el dato actual.
     * @param string $idArticulo Identificador del artículo donde se mostrará el dato. Por defecto, tiene el valor ''.
     * @param boolean $condicionContrasenia Condición que permite indicar si el dato a mostrar es una contraseña o no.
     *      Por defecto, tiene el valor false.
     * @since 1.0
     */
    function muestraDatoActual($texto, $dato, $idSpan, $idArticulo = '', $condicionContrasenia = false){
?>
        <article <?php 

            // Si se ha indicado un ID de un artículo, se lo añadimos a este
            if($idArticulo != ''){
        ?>
                id=<?='"'.$idArticulo.'"'?>
        <?php
            }
        ?>>
            <p><?=$texto?>
                <span id=<?='"'.$idSpan.'"'?>><?=$dato?></span>
                <?php

                    // Si se ha indicado que se va a poner una contraseña, ponemos la contraseña oculta
                    if($condicionContrasenia){
                ?>
                        <span id="contraseniaActualOculta">********</span>
                <?php
                    }
                ?>
            </p>
            <?php 

                // Si se ha indicado que se va a poner una contrasña, ponemos el icono de mostrar/ocultar contraseña
                if($condicionContrasenia){
            ?>
                    <i class="bi bi-eye-slash-fill" id="muestraContraseniaActual"></i>
            <?php
                }
            ?>
        </article>
<?php
    }

    /**
     * Función que permite generar los artículos de los inputs del cuadro de modificar datos.
     * @param string $idInput Identificador del input que se va a crear.
     * @param array $atributosInput Array asociativo con los atríbuots que se añadirán al input y sus valores.
     * @param string $textoLabel Texto del label que aparecerá al lado del input.
     * @param string $claseArticulo Clase del artículo que contendrá al label y al input. Por defecto, tiene el valor ''.
     * @since 1.0
     */
    function generaArticulosInputsModificaDatos($idInput, $atributosInput, $textoLabel, $claseArticulo = '',){
?>
        <article <?php 

            // Si se ha pasado una clase del artículo, la añadimos
            if($claseArticulo != ''){
        ?>
                class=<?='"'.$claseArticulo.'"'?>
        <?php
            }
        ?>>
            <label for=<?='"'.$idInput.'"'?>><?=$textoLabel?></label>
            <input name=<?='"'.$idInput.'"'?> id=<?='"'.$idInput.'"'?> <?php 

                // Recorremos el array de atributos del input, y los ponemos en el input
                foreach($atributosInput as $atributo => $valor){
                    echo $atributo.'="'.$valor.'" ';
                }
            ?> required/>
            <?php 

                // Si se ha indicado que es un input de tipo 'password', generamos el icono de mostrar/ocultar contraseña
                if($atributosInput['type'] == 'password'){
            ?>
                    <i class="bi bi-eye-slash-fill muestraInputContrasenia"></i>
            <?php
                }
            ?>
        </article>
<?php
    }
?>