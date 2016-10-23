Configurar WAF para un solo virtualhost
========================================

Nos aseguramos que tenemos bien el procedimiento de la instalacion, creamos el virtual host y le agregamos el modsecurity
Activamos NameVirtualHost *:80 y el NameVirtualHost *:443 en el http.conf.:

	# vi /etc/httpd/confd/http.conf
	NameVirtualHost *:80
	NameVirtualHost *:443

y creamos nuestros virtual hosts.:

	# vi /etc/httpd/conf/prueba.conf
	<VirtualHost *:80>
		ServerAdmin webmaster@prueba.com
		DocumentRoot /var/www/html/prueba
		ServerName prueba.com
		ErrorLog logs/prueba.com-error_log
		CustomLog logs/prueba.com-access_log common
		<IfModule security2_module>
		    SecRuleEngine On
		</IfModule>
	</VirtualHost>

.:

	# vi /etc/httpd/conf/prueba.conf
	<VirtualHost *:80>
		ServerAdmin webmaster@prueba.com
		DocumentRoot /var/www/html/prueba
		ServerName prueba.com
		ErrorLog logs/prueba.com-error_log
		CustomLog logs/prueba.com-access_log common
		<IfModule security2_module>
		    SecRuleEngine DetectionOnly
		</IfModule>
	</VirtualHost>
