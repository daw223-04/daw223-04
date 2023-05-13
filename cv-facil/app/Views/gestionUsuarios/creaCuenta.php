<section>
    <header>
        <h2>Creación de cuenta</h2>
    </header>
    <p id="mensajeError" class="mensajeError"></p>
    <form id="formularioCreaCuenta">
        <?php 

            // Generamos los artículos con los inputs del formulario de crear cuenta
            generaArticulosInputsCreaCuenta('inputNombre', array('type' => 'text', 'maxlength' => '30', 'placeholder' => 'Ej. Pepito', 
                'pattern' => '^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)?$'), 'Nombre:');
            generaArticulosInputsCreaCuenta('inputApellidos', array('type' => 'text', 'maxlength' => '50', 'placeholder' => 'Ej. Gómez López', 
                'pattern' => '^(de\s(la\s)?|del\s)?[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+$'), 'Apellidos:');
            generaArticulosInputsCreaCuenta('inputNombreUsuario', array('type' => 'text', 'minlength' => '8', 'maxlength' => '20',
                'placeholder' => 'Ej. PepitoGL20', 'pattern' => '^[\w]{8,20}$'), 'Nombre de usuario:');
            generaArticulosInputsCreaCuenta('inputContrasenia', array('type' => 'password', 'minlength' => '8', 'maxlength' => '20',
                'placeholder' => 'Ej. Password1234', 'pattern' => '^[A-Za-z\d]{8,20}$'), 'Contraseña:', 
                'articulosInputsContrasenia');
            generaArticulosInputsCreaCuenta('inputRepitaContrasenia', array('type' => 'password', 'minlength' => '8', 'maxlength' => '20',
                'placeholder' => 'Ej. Password1234', 'pattern' => '^[A-Za-z\d]{8,20}$'), 'Repita contraseña:', 'articulosInputsContrasenia');
        ?>
        <input type="submit" id="btnCreacionCuenta" value="Crear cuenta"/>
    </form>
</section>

<?php

    /**
     * Función que permite generar los artículos de los inputs de creación de cuenta.
     * @param string $idInput Identificador del input que se va a crear.
     * @param array $atributosInput Array asociativo con los atríbuots que se añadirán al input y sus valores.
     * @param string $textoLabel Texto del label que aparecerá al lado del input.
     * @param string $claseArticulo Clase del artículo que contendrá al label y al input. Por defecto, tiene el valor ''.
     * @since 1.0
     */
    function generaArticulosInputsCreaCuenta($idInput, $atributosInput, $textoLabel, $claseArticulo = '',){
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
                    <i class="bi bi-eye-slash-fill botonMuestraContrasenia"></i>
            <?php
                }
            ?>
        </article>
<?php
    }
?>