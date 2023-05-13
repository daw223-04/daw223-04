<header>
    <h2>Orden de los elementos del curriculum</h2>
</header>
<form id="formularioOrdenaciones">
    <section id="seccionContieneOrdenaciones">
        <?php 
            if(!isset($arrayConteos[0]) || $arrayConteos[0] < 3){
        ?>
                <article class="articulosOrdenacion" id="articuloOrdenacionApartados">
                    <header>
                        <h3>Indique como quiere que se ordenen los apartados del currículum</h3>
                    </header>
                    <?php
                        $limite = 0;
                        if(isset($arrayConteos[0])){
                            $limite = $arrayConteos[0];
                        }
                        for($i = 0; $i < (4 - $limite); $i++){
                    ?>
                            <article>
                                <label><?= $i + 1 ?>º</label>
                                <select id=<?='"selectDato'.($i + 1).'"';?> name=<?='"selectDato'.($i + 1).'"';?> required>
                                    <option value='-1'>----- Seleccione una opción -----</option>
                                    <?php 

                                        // Generamos las opciones del select, seleccionando alguna por defecto si así lo indica el orden
                                        //      de los elementos
                                        generaOpcionesSelect($i, $conteoEstudios, 'Estudios', 'Estudios', $ordenElementos);
                                        generaOpcionesSelect($i, $conteoExperiencia, 'Experiencia', 'Experiencia laboral', $ordenElementos);
                                        generaOpcionesSelect($i, $conteoIdiomas, 'Idiomas', 'Idiomas', $ordenElementos);
                                        generaOpcionesSelect($i, $conteoDatosInteres, 'DatosInteres', 'Datos de interés', $ordenElementos);
                                        
                                    ?>
                                </select>
                            </article>
                    <?php
                        }
                    ?>
                </article>
        <?php
            }
        ?>
        <article class="articulosOrdenacion" id="articuloOrdenacionCronologica">
            <header>
                <h3>Indique como quiere que se ordenen los diferentes datos en los apartados del currículum</h3>
            </header>
            <?php 

                // Comprobamos si se ha recibido la cronología por parámetro, para crear los radiobutton de una manera u otra
                if(isset($cronologia)){
                    generaRadioButtonCronologia('Ordenación cronológica', 'inputOrdenCronologico', 'Directo', $cronologia);
                    generaRadioButtonCronologia('Ordenación cronológica inversa', 'inputOrdenCronologicoInverso', 'Inverso', $cronologia);
                }else{
                    generaRadioButtonCronologia('Ordenación cronológica', 'inputOrdenCronologico', 'Directo', false);
                    generaRadioButtonCronologia('Ordenación cronológica inversa', 'inputOrdenCronologicoInverso', 'Inverso', false);
                }
            ?>
        </article>
    </section>
    <footer>
        <a href=<?=base_url()."CreacionCurriculum"?> id="btnVolverAtras" class="botonEnlace botonSecundario">Volver atrás</a>
        <input id="btnCrearCurriculum" type="submit" value="Crear currículum"/>
    </footer>
</form>

<?php
    
    /**
     * Función que permite generar las opciones del select, además de preseleccionarlas si lo indica así el orden.
     * @param integer $i Indice del select que se esta generando actualmente.
     * @param integer $conteo Conteo de elementos de la opción que se va a generar, y que si es igual a 0, no se genera la
     *      opción.
     * @param string $clave Clave de la opción.
     * @param string $texto Texto que se mostrará en la opción.
     * @param array $ordenElementos Array que contiene el orden de los elementos en el currículum.
     * @since 1.0
     */        
    function generaOpcionesSelect($i, $conteo, $clave, $texto, $ordenElementos){
        
        // Comprobamos el valor del conteo
        if($conteo > 0){
?>
            <option value=<?='"'.$clave.'"'?> <?php 

                // Vamos comprobando el indice actual y el orden que podría asociarse a él, viendo si este
                //      último es igual a la clave para indicar que la opción estará seleccionada
                if($i == 0 && isset($ordenElementos['orden1']) && $ordenElementos['orden1'] == $clave){
                    echo 'selected';
                }else if($i == 1 && isset($ordenElementos['orden2']) && $ordenElementos['orden2'] == $clave){
                    echo 'selected';
                }else if($i == 2 && isset($ordenElementos['orden3']) && $ordenElementos['orden3'] == $clave){
                    echo 'selected';
                }else if($i == 3 && isset($ordenElementos['orden4']) && $ordenElementos['orden4'] == $clave){
                    echo 'selected';
                }
            ?>><?=$texto?></option>
<?php
        }
    }

    /**
     * Función que permite generar los radiobutton de la cronología.
     * @param string $label Texto que se mostrará al lado del input.
     * @param string $idInput Identificador del input.
     * @param string $value Valor del input.
     * @param false|string $cronologia Valor de la cronología recibido por parámetro, o false si no se ha recibido
     *      ninguno
     */
    function generaRadioButtonCronologia($label, $idInput, $value, $cronologia){
?>
        <article>
            <input type="radio" name="orden" value=<?='"'.$value.'"'?> id=<?='"'.$idInput.'"'?> 
                <?php if($cronologia && $cronologia == $value){echo 'checked';}?> required/>
            <label><?=$label?></label>
        </article>
<?php
    }

?>