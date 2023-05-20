<header>
    <h2>Datos del currículum</h2>
</header>
<p>*: Campos obligatorios</p>
<form>
    <section class="cuadroCurriculum" id="cuadroDatosPersonales">
        <header>
            <h3>1. Datos personales</h3>
        </header>
        <article id="gridDatosPersonales">
            <article id="articuloFoto">
                <img id="fotoCurriculum" src=<?= isset($foto) ? '"'.$foto.'"' : "assets/img/fotoInicialCurriculum.png"?>/>
                <input type="file" accept=".png, .jpg, .jpeg" id="inputFotoCurriculum" name="inputFotoCurriculum"/>
                <button type="button" id="botonInputFotoCurriculum">Seleccionar foto*</button>
            </article>
            <?php 

                // Generamos el artículo del input del nombre, comprobando si se ha recibido por parámetro
                $array = array('required' => 'required', 'pattern' => '^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)?$', 
                    'placeholder' => 'Ej. Pepito', 'maxlength' => '30');
                if(isset($nombre)){
                    $array['value'] = $nombre;
                }
                generaArticleLabelInput('Nombre', 'Nombre*:', 'text', $array);

                // Generamos el artículo del input de los apellidos, comprobando si se han recibido por parámetro
                $array = array('required' => 'required', 'pattern' => '^(de\s(la\s)?|del\s)?[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\s[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+$', 
                    'placeholder' => 'Ej. Gómez López', 'maxlength' => '50');
                if(isset($apellidos)){
                    $array['value'] = $apellidos;
                }
                generaArticleLabelInput('Apellidos', 'Apellidos*:', 'text', $array);

                // Generamos el artículo del input del teléfono, comprobando si se ha recibido por parámetro
                $array = array('required' => 'required', 'pattern' => '^\d{9}$', 'placeholder' => 'Ej. 123456789', 
                    'minlength' => '9', 'maxlength' => '9');
                if(isset($telefono)){
                    $array['value'] = $telefono;
                }
                generaArticleLabelInput('Telefono', 'Nº Teléfono*:', 'text', $array);

                // Generamos el artículo del input de la fecha de nacimiento, comprobando si se ha recibido por parámetro
                $array = array('required' => 'required', 'min' => '1960-01-01', 'max' => date('Y-m-d', strtotime('-15 years', strtotime(date('Y-m-d')))));
                if(isset($fechaNac)){
                    $array['value'] = $fechaNac;
                }
                generaArticleLabelInput('FechaNac', 'Fecha nac.*:', 'date', $array);

                // Generamos el artículo del input de la dirección, comprobando si se ha recibido por parámetro
                $array = array('required' => 'required', 'pattern' => '^(C\/|Avda\.|Plaza)\s(((de(\s(la|las|los))?\s|del\s|los\s|las\s)?[A-ZÁÉÍÓÚÑ][a-záéíóúñ]{3,})\s?)+,\s\d{1,3}(,\s\d{1,2}(º|°)\s[A-Z])?$',
                    'placeholder' => 'Ej. C/ Falsa, 123', 'minlength' => '10', 'maxlength' => '180');
                if(isset($direccion)){
                    $array['value'] = $direccion;
                }
                generaArticleLabelInput('Direccion', 'Direccion*:', 'text', $array);

                // Generamos el artículo del input del correo, comprobando si se ha recibido por parámetro
                $array = array('required' => 'required', 'pattern' => '^[\wÁÉÍÓÚÑáéíóúñ\.]+@[a-z]+(\.(es|com)){1,2}$', 'placeholder' => 'Ej. pepito_gomez@gmail.com', 
                    'minlength' => '15', 'maxlength' => '120');
                if(isset($correo)){
                    $array['value'] = $correo;
                }
                generaArticleLabelInput('Correo', 'Correo elec.*:', 'text', $array);

                // Generamos el artículo del input del teléfono de WhatsApp, comprobando si se ha recibido por parámetro
                $array = array('pattern' => '^\d{9}$', 'placeholder' => 'Ej. 123456789', 'minlength' => '9', 'maxlength' => '9');
                if(isset($whatsapp)){
                    $array['value'] = $whatsapp;
                }
                generaArticleLabelInput('Whatsapp', 'Whatsapp:', 'text', $array);

                // Generamos el artículo del input del enlace de LinkedIn, comprobando si se ha recibido por parámetro
                $array = array('pattern' => '^https:\/\/www.linkedin.com\/in\/[a-z\-]+\/$', 'placeholder' => 'Ej. https://www.linkedin.com/in/pepito-gomez-lopez', 
                    'minlength' => '28', 'maxlength' => '150');
                if(isset($linkedin)){
                    $array['value'] = $linkedin;
                }
                generaArticleLabelInput('LinkedIn', 'LinkedIn:', 'text', $array);
            ?>
        </article>
    </section>
    <?php 

        // Generamos el artículo de los estudios, comprobando antes si se ha recibido estudios por parámetro
        if(isset($estudios)){
            generaSeccionEstudiosExperienciaIdiomas("2", "Estudio", "Estudios", "Estudios añadidos", 'Ej. Titulo de la E.S.O.', 
                "Ej: I.E.S. Ramon y Cajal", "^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\.]+\s?)+((de(\sla)?|en|del)\s)?)+$", $estudios);
        }else{
            generaSeccionEstudiosExperienciaIdiomas("2", "Estudio", "Estudios", "Estudios añadidos", "Ej. Titulo de la E.S.O.", 
                "Ej: I.E.S. Ramon y Cajal", "^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\.]+\s?)+((de(\sla)?|en|del)\s)?)+$");
        }

        // Generamos el artículo de las experiencias laborales, comprobando antes si se ha recibido experiencias por parámetro
        if(isset($experiencia)){
            generaSeccionEstudiosExperienciaIdiomas("3", "Experiencia", "Experiencia laboral", "Experiencias laborables añadidas", 
                "Ej. Profesor de Matemáticas", "Ej: Universidad de Valladolid", "^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\.]+\s?)+((de(\sla)?|en|del)\s)?)+$", 
                $experiencia);
        }else{
            generaSeccionEstudiosExperienciaIdiomas("3", "Experiencia", "Experiencia laboral", "Experiencias laborables añadidas", 
                "Ej. Profesor de Matemáticas", "Ej: Universidad de Valladolid", "^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\.]+\s?)+((de(\sla)?|en|del)\s)?)+$");
        }

        // Generamos el artículo de los idiomas, comprobando antes si se ha recibido idiomas por parámetro
        if(isset($idiomas)){
            generaSeccionEstudiosExperienciaIdiomas("4", "Idioma", "Idiomas", "Idiomas añadidos", "Ej. Titulo de B2 en Inglés", 
                "Ej: Escuela Oficial de Idiomas", "^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\d\.]+\s?)+(de\s(la\s)?|en\s|del\s)?)+$", $idiomas);
        }else{
            generaSeccionEstudiosExperienciaIdiomas("4", "Idioma", "Idiomas", "Idiomas añadidos", "Ej. Titulo en B2 de Inglés", 
                "Ej: Escuela Oficial de Idiomas", "^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\d\.]+\s?)+(de\s(la\s)?|en\s|del\s)?)+$");
        }

        // Generamos el artículo de los datos de interés, comprobando antes si se ha recibido datos de interés por parámetro
        if(isset($datosInteres)){
            generaSeccionEstudiosExperienciaIdiomas("5", "Datos", "Datos de interés", "Datos de interés añadidos", 
                "Ej. Persona activa y trabajadora", "", "", $datosInteres);
        }else{
            generaSeccionEstudiosExperienciaIdiomas("5", "Datos", "Datos de interés", "Datos de interés añadidos", 
                "Ej. Persona activa y trabajadora");
        }
    ?>
    <section id="seccionBotonEnvio">
        <input type="submit" value="Continuar"/>
    </section>
</form>

<?php

    /**
     * Función que permite generar un artículo con un label y un input en su interior
     * @param string $nombreCampo Nombre identificativo del input a crear.
     * @param string $textoCampo Texto que se mostrará en el label.
     * @param string $tipoInput Tipo del input a crear.
     * @param array $atributos Atributos a añadir al input.
     */
    function generaArticleLabelInput($nombreCampo, $textoCampo, $tipoInput, $atributos = array()){
?>
        <article id=<?= "articulo".$nombreCampo; ?>>
            <label for=<?= "id".$nombreCampo; ?>><?= $textoCampo ?></label>
            <input type=<?= '"'.$tipoInput.'"';?> id=<?= "id".$nombreCampo; ?> name=<?= "id".$nombreCampo; ?> 
                <?php 

                    // Recorremos el array de atributos recibidos, y los metemos en el input
                    foreach($atributos as $atributo => $valor){
                        echo $atributo."='".$valor."' ";
                    }
                ?>
            />
        </article>
<?php 
    }

    /**
     * Función que permite generar una sección completa de estudio, experiencia, idioma para introducir los datos de la 
     *      creación del curriculum.
     * @param string $contador Número que aparecerá junto a la cabecera para indicar el orden de la sección.
     * @param string $palabraPrincipal Palabra clave que se usará para poder crear todos los IDs y cadenas que se mostrarán en la 
     *      pantalla, y que serán los que permitan diferenciar una sección de otra.
     * @param string $cabecera Cabecera de la sección del cuadro del currículum.
     * @param string $cabeceraAnadidos Cabecera del artículo que almacenará todos los datos que se añadan.
     * @param string $textoEjemploTitulo Texto que se pondrá de ejemplo en el input del título, puesto de trabajo o textarea de dato
     *      de interés.
     * @param string $textoEjemploCentro Texto que se pondrá de ejemplo en el input del centro de trabajo o estudio. Tiene como valor
     *      por defecto ''.
     * @param string $patronTituloPuesto Patrón que deben respetar los títulos o puestos indicados. Tiene como valor por defecto ''.
     * @param string $datosIniciales Datos iniciales que aparecerán al cargar la página en el artículo de datos añadidos. Tiene como
     *      valor por defecto un array vacío.
     */
    function generaSeccionEstudiosExperienciaIdiomas($contador, $palabraPrincipal, $cabecera, $cabeceraAnadidos, $textoEjemploTitulo, 
        $textoEjemploCentro = '', $patronTituloPuesto = '', $datosIniciales = array()){
?>
    <section class="cuadroCurriculum">
        <header>
            <h3><?= $contador.'. '.$cabecera;?></h3>
        </header>
        <article class="articuloEnlaceDatosNoPersonales">
            <p id=<?= '"enlaceAnadir'.$palabraPrincipal.'"' ?> class="enlaceAnadirDatosNoPersonales"><?= "+ Agregar ".strtolower($palabraPrincipal); ?></p>
        </article>
        <article class="cuadroAnadirDatosNoPersonales" id=<?= '"cuadroAnadir'.$palabraPrincipal.'"'; ?>>
            <header>
                <h3><?= "Añadir ".strtolower($palabraPrincipal); ?></h3>
            </header>
            <p id=<?= '"mensajeErrorAnade'.$palabraPrincipal.'"'; ?> class="mensajeError"></p>
            <input type="hidden" name=<?= '"id'.$palabraPrincipal.'Modificacion"'?> id=<?= '"id'.$palabraPrincipal.'Modificacion"'; ?>/>
            <input type="hidden" name=<?= '"conteoArticulos'.$palabraPrincipal.'"'?> id=<?= '"conteoArticulos'.$palabraPrincipal.'"'?>
                value=<?= (count($datosIniciales) + 1) ?> />
            <?php 

                // Creamos un cuadro de añadir datos diferente para los datos de interés que para los estudios/experiencia/idiomas
                if($palabraPrincipal == 'Datos'){
            ?>
                    <article>
                        <label for=<?= '"tituloAnade'.$palabraPrincipal.'"' ?> id=<?='"tituloAnade'.$palabraPrincipal.'"'?>>Indique el dato de interés a añadir:</label>
                        <textarea name=<?= '"tituloAnade'.$palabraPrincipal.'"' ?> id=<?= '"tituloAnade'.$palabraPrincipal.'"' ?> 
                            placeholder=<?='"'.$textoEjemploTitulo.'"'?> pattern="^[A-ZÁÉÍÓÚÑa-záéíóúñ\d\.\s,-]+$"></textarea>
                    </article>
            <?php
                }else{

                    // Generamos todos los elementos del cuadro de añadir datos
                    generaArticulosIntroduceTituloPuestoCentro($palabraPrincipal, 'tituloAnade', '15', '200', 
                        $patronTituloPuesto, $textoEjemploTitulo);
                    generaArticulosIntroduceTituloPuestoCentro($palabraPrincipal, 'centroAnade', '5', '100', 
                        '^(([A-ZÁÉÍÓÚÑ][a-záéíóúñ\.]+\s?)+((de(\sla)?|la|del|en)\s)?)+$', $textoEjemploCentro);
                    generaArticulosFecha($palabraPrincipal, 'fechaInicioAnade', 'Indique la fecha de inicio: ');
                    generaArticulosFecha($palabraPrincipal, 'fechaFinAnade', 'Indique la fecha de fin: ');
                }
            ?>
            <article class="articuloBotonesCuadroAnadir">
                <button type="button" id=<?= '"descartarCambiosAnade'.$palabraPrincipal.'"' ?> class="btnDescartaCambios botonSecundario">Descartar cambios</button>
                <button type="button" id=<?= '"anadir'.$palabraPrincipal.'"'; ?> class="btnAnadeElemento botonPrincipal"><?= "Añadir ".strtolower($palabraPrincipal); ?></button>
            </article>
        </article>
        <article id=<?= '"articulo'.$palabraPrincipal.'Anadidos"'; ?> class="datosNoPersonalesAnadidos">
            <header>
                <h4><?= $cabeceraAnadidos; ?></h4>
            </header>
            <?php

                // Si se han recibido estudios/experiencias/idiomas/datos de interés, los mostramos en su artículo correspondiente
                if(count($datosIniciales) != 0){
                    foreach($datosIniciales as $keyDato => $dato){
            ?>
                        <hr/>
                        <article class=<?= '"'.strtolower($palabraPrincipal).'Anadido elementoAnadido"'?> 
                            id=<?= '"'.strtolower($palabraPrincipal).($keyDato + 1).'"'?>>
                            <article>
                                <p class="tituloElemento"><?= $dato['dato1'];?></p>
                                <?php 
                                    if(isset($dato['dato2'])){
                                ?>
                                    <p><?= $palabraPrincipal == 'Experiencia' ? 'Centro de trabajo:' : 'Centro de estudio:'?>
                                        <span class="centroElemento"><?= $dato['dato2'] ?></span>
                                    </p>
                                    <p>Fecha de inicio y fin: 
                                        <span class="fechaInicioElemento"><?= $dato['dato3'] ?></span>
                                        - 
                                        <span class="fechaFinElemento"><?= $dato['dato4'] == 'Actualidad' ? 'Actualidad' : $dato['dato4'] ?></span>
                                    </p>
                                <?php
                                    }
                                ?>
                            </article>
                            <article>
                                <img src="assets/img/iconoModificacion.png" class="imagenModificacion"/>
                                <img src="assets/img/iconoBorrado.png" class="imagenBorrado"/>
                            </article>
                        </article>
            <?php
                    }
                }
            ?>
        </article>
    </section>
<?php
    }

    /**
     * Función que permite generar el artículo con el input del titulo/puesto o el del centro de estudio/trabajo que 
     *      se ven en los cuadros de añadir estudios, experiencia e idiomas.
     * @param string $palabraPrincipal Palabra clave que se usará para poder crear todos los IDs y cadenas que se mostrarán en la 
     *      pantalla, y que serán los que permitan diferenciar una sección de otra.
     * @param string $idInput Identificador del input que se va a generar.
     * @param string $minLength Longitud mínima del dato que se pondrá en el input.
     * @param string $maxLength Longitud máxima del dato que se pondrá en el input.
     * @param string $pattern Patrón que se pondrá en el input.
     * @param string $placeholder Texto de ejemplo que se pondrá en el input.
     */
    function generaArticulosIntroduceTituloPuestoCentro($palabraPrincipal, $idInput, $minLength, $maxLength, $pattern, $placeholder){
?>
        <article class="filaDosDatosCuadroAnadir">
            <label for=<?= '"'.$idInput.$palabraPrincipal.'"' ?>>
                <?php 

                    // Diferenciamos por palabra principal y por el identificador del input para mostrar uno u otro texto
                    if($palabraPrincipal == 'Experiencia'){
                        if($idInput == 'tituloAnade'){
                            echo 'Indique el puesto de trabajo:';
                        }else{
                            echo 'Indique el centro de trabajo:';
                        }
                    }else{
                        if($idInput == 'tituloAnade'){
                            echo 'Indique el titulo obtenido:';
                        }else{
                            echo 'Indique el centro de estudios:';
                        }
                    }
                ?>
            </label>
            <input type="text" name=<?= '"'.$idInput.$palabraPrincipal.'"' ?> id=<?= '"'.$idInput.$palabraPrincipal.'"' ?>
                minlength=<?='"'.$minLength.'"'?> maxlength=<?='"'.$maxLength.'"'?> pattern=<?='"'.$pattern.'"'?>
                placeholder=<?='"'.$placeholder.'"'?>/>
        </article>
<?php
    }

    /**
     * Función que permite generar los artículos con los inputs de fecha de inicio y de fin de los cuadros de añadir
     *      estudios, experiencia e idiomas.
     * @param string $palabraPrincipal Palabra clave que se usará para poder crear todos los IDs y cadenas que se mostrarán en la 
     *      pantalla, y que serán los que permitan diferenciar una sección de otra.
     * @param string $idInput Identificador del input que se va a generar.
     * @param string $texto Texto que se mostrará en el label que se pondrá al lado del input.
     */
    function generaArticulosFecha($palabraPrincipal, $idInput, $texto){
?>
        <article class=<?=$idInput == 'fechaInicioAnade' ? '"filaDosDatosCuadroAnadir"' : '"filaTresDatosCuadroAnadir"'?>>
            <label for=<?= '"'.$idInput.$palabraPrincipal.'"' ?>><?=$texto?></label>
            <input type="date" name=<?= '"'.$idInput.$palabraPrincipal.'"'?> id=<?= '"'.$idInput.$palabraPrincipal.'"' ?> min="1980-01-01" 
                max=<?= '"'.date('Y-m-d').'"' ?>/>
            <?php 

                // Si se esta generando la fecha de fin, creamos también un botón de 'Actualidad'
                if($idInput == 'fechaFinAnade'){
            ?>
                    <button type="button" id=<?= '"btn'.$palabraPrincipal.'Actuales"'; ?> class="btnActualidad">Actualidad</button>
            <?php
                }
            ?>
        </article>
<?php
    }
?>