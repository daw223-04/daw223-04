<header>
    <h2>Currículum generado correctamente</h2>
</header>
<section id="seccionCurriculum">
    <article id="articuloCurriculum">
        <article id="imagenArticuloCurriculum">
            <img src=<?="'".$foto."'"?> id="fotoCurriculum"/>
        </article>
        <article id="datosPersonales">
            <p class="letraNegrita"><?=$nombre.' '.$apellidos?></p>
            <p><i class="bi bi-calendar-fill"></i><?=$fechaNac?></p>
            <p><i class="bi bi-telephone-fill"></i><?=$telefono?></p>
            <p><i class="bi bi-geo-alt-fill"></i><?=$direccion?></p>
            <p><i class="bi bi-envelope-fill"></i><?=$correo?></p>
            <?php 

                // Mostramos el Whatsapp solo si se ha recibido por parámetro
                if(isset($whatsapp)){
            ?>
                    <p><i class="bi bi-whatsapp"></i><?=$whatsapp?></p>
            <?php
                }

                // Mostramos el LinkedIn solo si se ha recibido por parámetro
                if(isset($linkedin)){
            ?>
                    <p><i class="bi bi-linkedin"></i><?=$linkedin?></p>
            <?php
                }
            ?>
        </article>
        <article class="articuloContieneDatosNoPersonales">
            <?php

                // Mostramos los datos no personales (estudios, experiencias, idiomas o datos de interés) recibidos
                //      por parámetro
                foreach($datosCurriculum as $clave => $valor){
            ?>
                <article id=<?='articulo'.$clave?> class="articuloDatosNoPersonales">
                    <header>
                        <h3><?= $clave ?></h3>
                    </header>
                    <?php

                        // Recorremos cada uno de los estudios, experiencia, idiomas o datos de interes
                        foreach($valor as $claveValor => $valor2){
                    ?>
                        <hr/>
                        <article>
                            <?php

                                // Recorremos cada uno de los datos de los estudios, experiencia, idiomas o datos de interés,
                                //      poniendo a estos últimos una clase para que no salgan en negrita
                                foreach($valor2 as $dato){
                            ?>
                                    <p <?php if($clave == 'Datos de interés'){echo 'class="parrafoNoNegrita"';}?>><?=$dato?></p>
                            <?php   
                                }
                            ?>
                        </article>
                    <?php
                        } 
                    ?>
                </article>
            <?php
                }
            ?>
        </article>
    </article>
    <article id="articuloBotonesCurriculum">
        <article>
            <?php 

                // Si se ha recibido por parámetro una acción de currículum, mostramos el artículo de modificación de currículum
                if(isset($accionCurriculum)){
            ?>
                    <p>Actualice los datos del currículum que acaba de modificar.</p>
                    <article>
                        <button type="button" id="modificaCurriculum" class="botonPrincipal">Actualizar currículum</button>
                    </article>
                    <input type="hidden" name="nombreCurriculum" id="nombreCurriculum" value=<?='"'.$nombreCurriculum.'"'?>/>
            <?php
                }

                // Si no se ha recibido por parámetro un usuario, mostramos el artículo de inicio de sesión
                else if(!isset($usuario)){
            ?>
                    <p>Inicie sesión ahora mismo para poder guardar el currículum</p>
                    <article>
                        <a href=<?=base_url().'InicioSesion'?> id="creaCuentaGuardaCurriculum" class="botonEnlace botonPrincipal">Iniciar sesión</a>
                    </article>
            <?php
                }
                
                // Si no se ha recibido por parámetro una acción de currículum y sí se ha recibido un usuario, mostramos el artículo de
                //      guardar currículum
                else{
            ?>
                    <p>Guarde el currículum que acaba de generar si posee una cuenta en nuestra aplicación.</p>
                    <article>
                        <button type="button" id="guardaCurriculum" class="botonPrincipal">Guardar currículum</button>
                    </article>
            <?php
                }
            ?>
        </article>
        <article>
            <p>Descargue el currículum ahora si le gusta, o vuelva a la edición para modificar lo que desee.</p>
            <article>
                <a href=<?=base_url()."OrdenElementosCurriculum"?> id="volverEdicionCurriculum" class="botonEnlace botonSecundario">Volver a la edición</a>
                <button type="button" id="descargarCurriculum" class="botonPrincipal">Descargar currículum</button>
            </article>
        </article>
    </article>
</section>