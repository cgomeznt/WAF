Configurar WAF para un solo virtualhost
========================================



seguridad servidores Apache
++++++++++++++++++++++++++++
https://openwebinars.net/blog/consejos-seguridad-servidores-apache/
1. Ocultar versión y sistema
2. Desactivar listado de directorios
3. Mantenernos actualizados
4. Deshabilitar módulos innecesarios
5. Permitir o Denegar acceso a directorios
6. Usar Mod_Security y Mod_Evasive
7. Deshabilitar Enlaces Simbólicos
8. Limitar tamaño de peticiones

https://geekflare.com/apache-web-server-hardening-security/

https://www.tecmint.com/apache-security-tips/

http://blog.capacityacademy.com/2013/11/26/como-mejorar-la-seguridad-de-servidor-web-apache/


# Redirige las peticiones del puerto 80 http al 443 para usar SSL
# Atiende lo entregado en el puerto 443 bajo SSL
# Escritura de los logs Apache
# Peticiones son recibidas usando TLS v1.2 Con certificados de CA y del WebSite
# Configuraciones para los clientes
# Evita HotLinking de las imagenes y documentos PDF del portal
# PersonalizaciÃ³n de errores - Se crea customizaciÃ³n de errores para no usar las pÃ¡ginas por defecto del navegador / apache
# ModSecurity -
# Escritura de los logs modsecurity
# Excluir reglas
# Reglas personalizadas
# Se desactiva ModSecurity para el contexto de errores personalizados del WAF
# Mod_Evasive
# Rewrites -  Reglas de rewriting activas para el Sitio
# Black Lists
# Domain Deflectors
# Backend -  Configuracion del backend - ProxyPass


Nos aseguramos que tenemos bien el procedimiento de la instalacion, creamos el virtual host y le agregamos el modsecurity
Activamos NameVirtualHost *:80 y el NameVirtualHost *:443 en el http.conf.::

	# vi /etc/httpd/confd/http.conf
	NameVirtualHost *:80
	NameVirtualHost *:443

y creamos nuestros virtual hosts y lo deshabilitamos en mod_security.::

	# vi mod_security.conf
	   SecRuleEngine Off

::

	# vi /etc/httpd/conf/prueba.conf
	# Virtual Host - Redirige las peticiones del puerto 80 http al 443 para usar SSL
	<VirtualHost *:80>
		ServerName prueba.com
		Redirect "/" "https://prueba.com"
	</VirtualHost>

	<VirtualHost *:443>
		ServerAdmin webmaster@example.com
		DocumentRoot /var/www/html/prueba_html
		ServerName prueba.com
		ServerAlias prueba.com
		# Escritura de los logs de Apache
		ErrorLog logs/prueba_html_error.log
		CustomLog logs/prueba_html_requests.log common
		# Front Side - Peticiones son recibidas usando TLS v1.2 Con certificados de CA y del WebSite
		SSLEngine on
		SSLProtocol TLSv1.2
		SSLCACertificateFile /etc/httpd/conf.d/certs/ca.crt
		SSLCertificateFile /etc/httpd/conf.d/certs/srvcert.crt
		SSLCertificateKeyFile /etc/httpd/conf.d/certs/srvcert.key
		# Detecta el tipo de error para captura el ID y mostrarlo por el navegador, para tener mas control de los falsos positivos
		<LocationMatch "^/+$">
			Options -Indexes
			ErrorDocument 403 /403.php
			ErrorDocument 404 /404.php
		</LocationMatch>
		# Se activa el modsecurity
		<IfModule mod_security2.c>
		    # Default recommended configuration
			SecRuleEngine On
			SecRequestBodyAccess Off
			# Escritura de los logs del modsecurity
			SecDebugLog /var/log/httpd/modsec_debug_prueba.log
    		SecAuditLog /var/log/httpd/modsec_audit_prueba.log
			# Remover reglas por ID
			SecRuleRemoveById 960024
			SecRuleRemoveById 950103
			# Remover reglas por Mensajes
			SecRuleRemoveByMsg "Remote File Access Attempt"
			# Aplicar reglas personalizadas.
			SecRule ARGS "tutu" "t:normalisePathWin,id:99999,severity:0,deny,msg:'Drive Access'" 
		</IfModule>
	</VirtualHost>


.::

	# vi /etc/httpd/conf/public.conf
	<VirtualHost *:80>
		ServerAdmin webmaster@public.com
		DocumentRoot /var/www/html/public
		ServerName public.com
		ErrorLog logs/public.com-error_log
		CustomLog logs/public.com-access_log common
		<IfModule mod_security2.c>>
		    SecRuleEngine DetectionOnly
			SecDebugLog /var/log/httpd/modsec_debug_public.log
    		SecAuditLog /var/log/httpd/modsec_audit_public.log
			SecRule ARGS "cucu" "t:normalisePathWin,id:99990,severity:0,deny,msg:'Drive Access'
		</IfModule>
	</VirtualHost>


Los archivos de ERROR se colocan en donde tienes DocumentRoot y este es un ejemplo del 403.php.::

	<?php
	 $protocol = $_SERVER['SERVER_PROTOCOL'];
	 header("$protocol 403 Forbidden");
	 header("Status: 403 Forbidden");
	 header("Connection: close");
	 $msg = $_SERVER["UNIQUE_ID"];
	?>
	<HTML><HEAD>
	 <TITLE>You have no access to this resource (403)</TITLE>
	</HEAD><BODY>
	<P>An error occured. Please tell the admin the error code: <?php echo $msg; ?></P>
	</BODY></HTML>
