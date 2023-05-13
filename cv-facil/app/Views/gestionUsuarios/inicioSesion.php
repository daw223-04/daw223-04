<section>
    <header>
        <h2>Inicio de sesión</h2>
    </header>
    <p id="mensajeError" class="mensajeError"></p>
    <form id="formularioInicioSesion">
        <?php 
            generaArticulosInputsInicioSesion('inputNombreUsuario', array('type' => 'text', 'minlength' => '8', 'maxlength' => '20', 
                'pattern' => '^[\w]{8,20}$'), 'Nombre de usuario:');
            generaArticulosInputsInicioSesion('inputContraseniaUsuario', array('type' => 'password', 'minlength' => '8', 'maxlength' => '20',
                'pattern' => '^[A-Za-z\d]{8,20}$'), 'Contraseña:', 'articulosInputsContrasenia');
        ?>
        <input type="submit" id="btnIniciarSesion" value="Iniciar sesión"/>
    </form>
    <p>o</p>
    <a id="enlaceCrearCuenta" href=<?= base_url().'CreacionCuenta' ?>>Crear una cuenta</a>
</section>

<?php

    /**
     * Función que permite generar los artículos de los inputs de inicio de sesión.
     * @param string $idInput Identificador del input que se va a crear.
     * @param array $atributosInput Array asociativo con los atríbuots que se añadirán al input y sus valores.
     * @param string $textoLabel Texto del label que aparecerá al lado del input.
     * @param string $claseArticulo Clase del artículo que contendrá al label y al input. Por defecto, tiene el valor ''.
     * @since 1.0
     */
    function generaArticulosInputsInicioSesion($idInput, $atributosInput, $textoLabel, $claseArticulo = '',){
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
                    <i class="bi bi-eye-slash-fill" id="botonMuestraContrasenia"></i>
            <?php
                }
            ?>
        </article>
<?php
    }
?>