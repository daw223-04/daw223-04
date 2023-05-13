<h2>Contenido del repositorio:</h2>
<ul>
        <li>cv_facil: Carpeta con el proyecto realizado, usando el framework MVC CodeIgniter, en su versión 4.3.2.</li>
        <li>docker_compose.yml: Fichero que permite la creación de los contenedores, así como vincularlos y ponerlos en una red privada de Docker. Los contenedores que se crean son:
        <ul>
                <li>Contenedor php-apache: Servidor de la aplicación, conectando el puerto 80 del contenedor con el 80 del ordenador. Usa como imagen base la imagen subida a DockerHub, la c>
                <li>Contenedor mysql: BD de la aplicación, conectando el puerto 3306 del contenedor con el 3306 del ordenador. Usa como imagen base la imagen subida a DockerHub, la cual cua>
                <li>Contenedor phpmyadmin: Gestor gráfico de la BD, conectando el puerto 80 del contenedor con el 8080 del ordenador. Usa como imagen base la imagen de <a href="https://hub.>
        </ul>
        <li>variables_servidor_web.env: Fichero que contiene las variables de entorno puestas en el contenedor php-apache para que la aplicación funcione correctamente.</li>
        </li>
</ul>
