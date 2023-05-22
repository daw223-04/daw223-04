<h2>Contenido del repositorio:</h2>
<ul>
        <li>cv_facil: Carpeta con el proyecto realizado, usando el framework MVC CodeIgniter, en su versión 4.3.2.</li>
        <li>docker_compose.yml: Fichero que permite la creación de los contenedores, así como vincularlos y ponerlos en una red privada de Docker. Los contenedores que se crean son:
        <ul>
                <li>Contenedor php-apache: Servidor de la aplicación, conectando el puerto 80 del contenedor con el 80 del ordenador. Usa como imagen base una imagen subida a DockerHub, la cual es la imagen de <a href="https://hub.docker.com/r/daw22304/servidor_web_pfc">servidor_web_pfc:1.0.0</a>.</li>
                <li>Contenedor mysql: BD de la aplicación, conectando el puerto 3306 del contenedor con el 3306 del ordenador. Usa como imagen base una imagen subida a DockerHub, la cual cual es la imagen de <a href="https://hub.docker.com/r/daw22304/bd_pfc">bd_pfc:1.0.0</a>.</li>
                <li>Contenedor phpmyadmin: Gestor gráfico de la BD, conectando el puerto 80 del contenedor con el 8080 del ordenador. Usa como imagen base la imagen de <a href="https://hub.docker.com/_/phpmyadmin">phpmyadmin:5.2.0</a> de DockerHub.
        </ul>
	</li>
        <li>variables_servidor_web.env: Fichero que contiene las variables de entorno puestas en el contenedor php-apache para que la aplicación funcione correctamente.</li>
</ul>

<h2>Manual de instalación</h2>

<h3>1. Consideraciones previas</h3>

<p>Antes de comenzar con el manual de instalación, cabe destacar que todo el proyecto se ha desarrollado y probado con las siguientes versiones de los programas:</p>
<ul>
	<li>Sistema operativo usado: Debian 11.5</li>
	<li>Versión de Docker: 20.10.19</li>
	<li>Versión de docker-compose: 2.16.0</li>
	<li>Versión de Git: 2.30.2</li>
</ul>
<p>No se asegura su funcionamiento con versiones diferentes de estos.</p>

<h3>2. Instalación y configuración de Docker y Git</h3>

<p>Primero, es necesario tener instalado y configurado, en el servidor donde se quiera hacer el despliegue de la aplicación, las siguientes aplicaciones:</p>
<ul>
	<li>Docker, con docker-compose habilitado. A continuación, se muestran los comandos para la instalación de ambos en Debian 11.5:
	<ul>
		<li>~$ sudo apt-get remove docker docker-engine docker.io containerd runc</li>
		<li>~$ sudo apt-get update</li>
		<li>~$ sudo apt-get install ca-certificates curl gnupg
		</li>
		<li>~$ sudo mkdir -m 0755 -p /etc/apt/keyrings</li>
		<li>~$ curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg</li>
		<li>~$ echo "deb [arch="$(dpkg --print-architecture)" signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian "$(. /etc/os-release && echo "$VERSION_CODENAME")" stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null</li>
		<li>~$ sudo apt-get update</li>
		<li>~$ sudo apt-get install docker-ce=5:20.10.19~3-0~debian-bullseye docker-ce-cli=5:20.10.19~3-0~debian-bullseye containerd.io docker-buildx-plugin docker-compose-plugin</li>
		<li>~$ sudo docker run hello-world <---- Este último comando es para probar que la instalación se ha hecho correctamente.</li>
	</ul>
	</li>
	<li>Git. A continuación, se muestran los comandos para su instalación y configuración en Debian 11.5:
	<ul>
		<li>~$ sudo apt update</li>
		<li>~$ sudo apt install git</li>
		<li>~$ sudo git config --global user.name nombre_usuario_deseado <---- Para configurar el nombre del usuario que usará Git.</li>
		<li>~$ sudo git config --global user.email correo_deseado <---- Para configurar el correo del usuario que usará Git.</li>
		<li>~$ sudo git config --global core.editor "nano -v" <---- Pone como editor para la configuración de git al editor nano.</li>
	</ul>
	</li>
</ul>

<h3>3. Clonado del repositorio de GitHub</h3>

<p>Tras instalar y configurar Docker y Git, se debe clonar el repositorio en el servidor, usando el siguiente comando en la consola de Git:</p>
<ul>
	<li>~$ git clone https://github.com/daw223-04/daw223-04.git</li>
</ul>

<h3>4. Aplicación de las variables de entorno correctas</h3>

<p>Una vez se tenga el repositorio clonado, se debe modificar una variable de entorno, la cual esta puesta en el fichero variables_servidor_web.env con el nombre de ip_pub, y que permite indicar, al código de la aplicación, cual es la dirección IP del servidor donde se lanza.</p>
<p>Entonces, los pasos para cambiar dicha variable son:</p>
<ul>
	<li>~$ nano daw223-04/variables_servidor_web.env</li>
	<li>ip_pub=IP <---- Se cambia el valor de la variable ip_pub, para que esta posea el valor que indiquemos donde pone IP.</li>
	<li>Guardamos los cambios hechos, pulsando las teclas Ctrl+O, y, tras ello, al Enter.</li>
	<li>Salimos del editor de texto pulsando las teclas Ctrl+X.</li>
</ul>
<p>Con esto, ya se tendrá correctamente configurada la variable de entorno necesaria para el contenedor del servidor web.</p>

<h3>5. Creación de los contenedores</h3>

<p>Cuando ya se haya cambiado el valor de la variable de entorno indicada, se podrán crear los contenedores.</p>
<p>Para ello, primero debemos ir a la raiz de nuestro repositorio clonado, usando el siguiente comando:</p>
<ul>
	<li>~$ cd daw223-04</li>
</ul>
<p>Una vez dentro, deberemos ejecutar uno de los siguientes comandos:</p>
<ul>
	<li>~$ docker compose up -d</li>
	<li>~$ docker-compose up -d</li>
</ul>

<p>Ambos comandos son muy parecidos, diferenciandose en tener un guión o un espacio entre las palabras docker y compose, pero, dependiendo de la versión que se instale de Docker, funcionará uno u otro comando. Por eso, se indican ambas posibilidades.</p>
<p>Con este comando, se descargarán las imagenes necesarias de DockerHub y se crearán los contenedores necesarios para desplegar la aplicación.</p>

<h3>6. Acceso a la aplicación</h3>

<p>Una vez se haya acabado la creación de los contenedores, se podrá acceder a la siguiente ruta via web para usar la aplicación:</p>
<ul>
	<li>http://IP</li>
</ul>

<p>Así mismo, también se podrá acceder a un gestor gráfico de la B.D., por si se necesita hacer alguna operación en ella. La ruta de acceso y credenciales son:</p>
<ul>
	<li>http://IP:8080</li>
	<li>Usuario: root</li>
	<li>Contraseña: admin</li>
</ul>

<p>Con esto, ya se tendrá la aplicación desplegada y lista para usarse.</p>

<h2>Fuentes consultadas para el manual de instalación</h2>

<ul>
	<li>Instalación de Docker y docker-compose en Debian: https://docs.docker.com/engine/install/debian/</li>
</ul>
