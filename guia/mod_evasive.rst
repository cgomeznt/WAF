Apache Evasive Maneuvers Module
===============================

https://www.zdziarski.com/blog/?page_id=442

::

	# wget http://www.zdziarski.com/blog/wp-content/uploads/2010/02/mod_evasive_1.10.1.tar.gz

Manual en el README

APACHE v2.0
-----------
::

	1. Extract this archive

	2. Run $APACHE_ROOT/bin/apxs -i -a -c mod_evasive20.c

	3. The module will be built and installed into $APACHE_ROOT/modules, and loaded into your httpd.conf

	4. Restart Apache

APACHE v2.0
-----------
::

	http.conf

	LoadModule evasive20_module modules/mod_evasive20.so

	(This line is already added to your configuration by apxs)


APACHE v2.0
-----------
::

	<IfModule mod_evasive20.c>
		DOSHashTableSize    3097
		DOSPageCount        2
		DOSSiteCount        50
		DOSPageInterval     1
		DOSSiteInterval     1
		DOSBlockingPeriod   10
	</IfModule>

Optionally you can also add the following directives::

    DOSEmailNotify      you@yourdomain.com
    DOSSystemCommand    "su - someuser -c '/sbin/... %s ...'"
    DOSLogDir           "/var/lock/mod_evasive"

You will also need to add this line if you are building with dynamic support:


Requirido
------------
::

	# yum install -y at sudo

::

	# visudo
	## Allow root to run any commands anywhere
	root    ALL=(ALL)       ALL
	apache  ALL=(ALL) NOPASSWD:     ALL
	Defaults:apache !requiretty

Probamos 
----------
::

	# httpd -t -D DUMP_MODULES | grep evasive
	 evasive20_module (shared)
	Syntax OK


Creamos un script 
-----------------
::

	# vi /usr/local/bin/script_mod_evasive.sh

	#!/bin/bash

	IP=$1
	sudo /sbin/iptables -I INPUT -p tcp -m multiport --dport 80,8080,443 -s $IP -j DROP
	echo "/sbin/iptables -D INPUT -p tcp -m multiport --dport 80,8080,443 -s $IP -j DROP" | sudo at now + 1 minutes
	echo "sudo rm -rf /var/log/ddos/dos-$IP" | sudo at now + 1 minutes

Editamos el VHOST
-----------------
::

	# vi prueba.conf

	<IfModule mod_evasive20.c>
		DOSHashTableSize 3097
		DOSPageCount 2
		DOSSiteCount 2
		DOSPageInterval 2
		DOSSiteInterval 2
		DOSSystemCommand "/usr/bin/sudo /usr/local/bin/script_mod_evasive.sh %s"
		DOSLogDir /var/log/ddos
		DOSBlockingPeriod 60
	</IfModule>

Verificamos luego de simular un DDOS
------------------------------------
::

	# iptables -L
	Chain INPUT (policy ACCEPT)
	target     prot opt source               destination         
	DROP       tcp  --  debian               anywhere            multiport dports http,webcache,https 
	ACCEPT     all  --  anywhere             anywhere            state RELATED,ESTABLISHED 
	ACCEPT     icmp --  anywhere             anywhere            
	ACCEPT     all  --  anywhere             anywhere            
	ACCEPT     tcp  --  anywhere             anywhere            state NEW tcp dpt:ssh 
	ACCEPT     tcp  --  anywhere             anywhere            state NEW tcp dpt:http 
	ACCEPT     tcp  --  anywhere             anywhere            state NEW tcp dpt:https 
	ACCEPT     tcp  --  anywhere             anywhere            state NEW tcp dpt:980 
	ACCEPT     tcp  --  anywhere             anywhere            state NEW tcp dpt:cslistener 
	REJECT     all  --  anywhere             anywhere            reject-with icmp-host-prohibited 

	Chain FORWARD (policy ACCEPT)
	target     prot opt source               destination         
	REJECT     all  --  anywhere             anywhere            reject-with icmp-host-prohibited 

	Chain OUTPUT (policy ACCEPT)
	target     prot opt source               destination         
	[root@waf01 conf.d]# atq
	75	2017-01-09 16:34 a root
	74	2017-01-09 16:34 a root

::

	# ls -l /var/log/ddos/
	total 4
	-rw-r--r-- 1 apache apache 5 ene  9 16:33 dos-192.168.1.4

::

	# atq
	72	2017-01-09 16:33 a root
	73	2017-01-09 16:33 a root


Los parámetros más importantes que podemos agregar 
---------------------------------------------------

DOSHashTableSize valor 
--------------------------

Establece el número de nodos a almacenar para cada proceso de peticiones de la tabla hash (contenedor asociativo de recuperación de peticiones por medio de claves que agiliza las respuestas del servidor). Si aplicamos un número alto a este parámetro obtendremos un rendimiento mayor, ya que las iteraciones necesarias para obtener un registro de la tabla son menores. Por contra, y de forma evidente, aumenta el consumo de memoria necesario para el almacenamiento de una tabla mayor. Se hace necesario incrementar este parámetro si el servidor atiende un número abultado de peticiones, aunque puede no servir de nada si la memoria de la máquina es escasa.


DOSPageCount valor 
--------------------------

Indica el valor del umbral para el número de peticiones de una misma página (o URI) dentro del intervalo definido en DOSPageInterval. Cuando el valor del parámetro es excedido, la IP del cliente se añade a la lista de bloqueos.


DOSSiteCount valor 
--------------------------

Cuenta cuántas peticiones de cualquier tipo puede hacer un cliente dentro del intervalo definido en DOSSiteInterval. Si se excede dicho valor, el cliente queda añadido a la lista de bloqueos.


DOSPageInterval valor 
--------------------------

El intervalo, en segundos, para el umbral de petición de páginas.


DOSSiteInterval valor 
--------------------------

El intervalo, en segundos, para el umbral de petición de objetos de cualquier tipo.


DOSBlockingPeriod valor 
--------------------------

Establece el tiempo, en segundos, que un cliente queda bloqueado una vez que ha sido añadido a la lista de bloqueos. Como ya se indicó unas líneas atrás, todo cliente bloqueado recibirá una respuesta del tipo 403 (Forbidden) a cualquier petición que realice durante este periodo.


DOSEmailNotify e-mail 
--------------------------

Un e-mail será enviado a la dirección especificada cuando una dirección IP quede bloqueada. La configuración del proceso de envío se establece en el fichero mod_evasive.c de la forma /bin/mail -t %s, siendo %s el parámetro que queda configurado en este parámetro. Será necesario cambiar el proceso si usamos un método diferente de envío de e-mails y volver a compilar el módulo con apxs (por ejemplo, la opción t ha quedado obsoleta en las últimas versiones del comando).


DOSSystemCommand comando 
--------------------------

El comando reflejado se ejecutará cuando una dirección IP quede bloqueada. Se hace muy útil en llamadas a herramientas de filtrado o firewalls. Usaremos %s para especificar la dirección IP implicada. Por ejemplo, podemos establecer su uso con iptables de la forma siguiente: 
DOSSystemCommand “/sbin/iptables –I INPUT –p tcp –dport 80 –s %s –j DROP”


DOSLogDir ruta 
--------------------------

Establece una ruta para el directorio temporal. Por defecto, dicha ruta queda establecida en /tmp, lo cual puede originar algunos agujeros de seguridad si el sistema resulta violado.


DOSWhitelist IP 
--------------------------

La dirección IP indicada como valor del parámetro no será tenida en cuenta por el módulo en ningún caso. Para cada dirección IP a excluir ha de añadirse una nueva línea con el parámetro. Por ejemplo, dejaremos fuera del chequeo del módulo a un posible bot que use los siguientes rangos de direcciones: 
DOSWhitelist 66.249.65.* 
DOSWhitelist 66.249.66.*


Probar
---------

El módulo mod_evasive viene acompañado de un script en lenguaje Perl llamado test.pl que nos permitirá comprobar si el funcionamiento del módulo es todo lo correcto que debiera. El funcionamiento del script es bien sencillo: manda cien peticiones del tipo GET /?número HTTP/1.0 seguidas al puerto 80 de la máquina local, que deberán ser bloqueadas por el módulo si éste está bien configurado. La salida presentada al ejecutar el script debería ser algo similar a esto::

	# perl test.pl 

	HTTP/1.1 200 OK 
	HTTP/1.1 200 OK 
	HTTP/1.1 200 OK 
	[...] 
	HTTP/1.1 403 Forbidden 
	HTTP/1.1 403 Forbidden 
	HTTP/1.1 403 Forbidden 
	HTTP/1.1 403 Forbidden 
	[...]


La respuesta 403 Forbidden aparecerá después de unas veinte peticiones y nos indicará que el funcionamiento del módulo es correcto. Si hemos establecido la ejecución de iptables siguiendo el ejemplo del apartado de configuración, podemos ejecutar el siguiente comando y observar si la dirección IP desde la cual hemos ejecutado el script ha quedado bloqueada: 


