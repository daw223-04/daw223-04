<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <title><?=$titulo?></title>
        <link rel="icon" type="imagex-ico" href="assets/img/iconoPagina.ico"/>
        <link rel="stylesheet" href="assets/css/cssGeneral.css"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css"/>
        <script>
            let ip_pub = <?= '"'.$_ENV['ip_pub'].'"' ?>
        </script>
        <script src="assets/js/funcionesGenerales.js"></script>
        <?php

            // Si se recibe un parámetro que indique que se desea la cabecera del título, es que se quiere poner la cabecerá de la página, 
            //      por lo que hay que vincular el CSS y JS asociado a esta
            if(isset($cabeceraTitulo)){
        ?>
            <link rel="stylesheet" href="assets/css/cabeceraPrincipal.css"/>
            <script defer src="assets/js/cabeceraPrincipal.js"></script>
        <?php
            }

            // Vinculamos todos los CSS recibidos por parámetro
            foreach($listCss as $css){
        ?>
                <link rel="stylesheet" href=<?=$css?>/>
        <?php
            }
        ?>
        <?php

            // Vinculamos todos los JS recibidos por parámetro
            foreach($listJs as $js){
        ?>
                <script defer src=<?=$js?>></script>
        <?php
            }
        ?>
    </head>
    <body>
        <?php 

            // Si se recibe un parámetro la página anterior, lo guardamos en un input de tipo hidden para poder 
            //      obtenerlo si lo necesitamos desde JS
            if(isset($paginaAnterior)){
        ?>
                <input type="hidden" id="paginaAnterior" name="paginaAnterior" value=<?='"'.$paginaAnterior.'"'?>/>
        <?php
            }
        ?>
        <section id="ventanaModal">
            <article id="articuloVentanaModal">
                <p id="primerParrafoArticuloVentanaModal"></p>
                <p id="segundoParrafoArticuloVentanaModal"></p>
                <p id="mensajeErrorVentanaModal" class="mensajeError"></p>
                <input type="text" id="inputVentanaModal" name="inputVentanaModal"/>
                <input type="hidden" id="curriculumBorrar" name="curriculumBorrar"/>
                <button type="button" id="btnPrincipalVentanaModal" class="botonPrincipal"></button>
                <article id="articuloDosBotonesVentanaModal">
                    <button type="button" id="primerBotonArticuloDosBotonesVentanaModal" class="botonSecundario"></button>
                    <button type="button" id="segundoBotonArticuloDosBotonesVentanaModal" class="botonPrincipal"></button>
                </article>
            </article>
        </section>
        <?php 

            // Si se recibe un parámetro que indique que se desea la cabecera del título, la mostramos
            if(isset($cabeceraTitulo)){
        ?>
        <header>
            <nav id="cabecera">
                <h2><a href=<?=base_url()?>>CV-FACIL</a></h2>
                <i class="bi bi-person"></i>
            </nav>
            <section id="cuadroIniciaSesion">
                <p id="textoCuadroIniciaSesion"><?= isset($usuario) ? "Sesión iniciada como ".$usuario : "No hay ninguna sesión iniciada"?></p>
                <?php 

                    // Si hay una sesión iniciada, mostramos los enlaces de ver currículums y de gestión de cuenta
                    if(isset($usuario)){
                ?>
                        <a href=<?=base_url()."ListadoCurriculums"?> id="enlaceListadoCurriculums">Ver curriculums creados</a>
                        <a href=<?=base_url()."GestionCuenta"?> id="enlaceGestionCuenta">Gestionar cuenta</a>
                <?php
                    }
                ?>
                <button type="button" id="botonCuadroIniciaSesion" class="botonPrincipal"><?= isset($usuario) ? "Cerrar sesión" : "Iniciar sesión"?></button>
            </section>
        </header>
        <?php
            }
        ?>
        <main>
            <?=$cuerpoPagina?>
        </main>
    </body>
</html>