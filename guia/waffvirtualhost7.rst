Configurar WAF para un solo virtualhost
========================================

Nos aseguramos que tenemos bien el procedimiento de la instalacion, creamos el virtual host y le agregamos el modsecurity
Activamos NameVirtualHost *:80 y el NameVirtualHost *:443 en el http.conf.::

	# vi /etc/httpd/confd/http.conf
	NameVirtualHost *:80
	NameVirtualHost *:443

y creamos nuestros virtual hosts y lo deshabilitamos en mod_security.::

	# vi mod_security.conf
	   SecRuleEngine Off

	# vi /etc/httpd/conf/prueba.conf
	<VirtualHost *:80>
		ServerAdmin webmaster@prueba.com
		DocumentRoot /var/www/html/prueba
		ServerName prueba.com
		ErrorLog logs/prueba.com-error_log
		CustomLog logs/prueba.com-access_log common
		ErrorDocument 404 /error.html
		ErrorDocument 500 /error.html
		ErrorDocument 502 /error.html
		ErrorDocument 503 /error.html
		ErrorDocument 504 /error.html
		<IfModule mod_security2.c>
			# Default recommended configuration
			SecRuleEngine On
			SecRequestBodyAccess Off
			SecRuleRemoveById 960024
			SecRuleRemoveById 950103
			SecRuleRemoveByMsg "Remote File Access Attempt"
			SecRule ARGS "tutu" "t:normalisePathWin,id:99999,severity:0,deny,msg:'Drive Access'"
		</IfModule>
	</VirtualHost>

.::

	# vi /etc/httpd/conf/prueba.conf
	<VirtualHost *:80>
		ServerAdmin webmaster@prueba.com
		DocumentRoot /var/www/html/prueba
		ServerName prueba.com
		ErrorLog logs/prueba.com-error_log
		CustomLog logs/prueba.com-access_log common
		<IfModule mod_security2.c>>
		    SecRuleEngine DetectionOnly
		</IfModule>
	</VirtualHost>
