Configurar WAF para todos los virtualhost
===========================================

Esto es basicamente igual cuando realizamos la instalacion, que por defecto queda para todos los virtual host, solo nos aseguramos de.::

	# cp modsecurity.conf-recommended /etc/httpd/conf.d/modsecurity.conf

	# cp unicode.mapping /etc/httpd/conf.d/

.::

	# vi /etc/httpd/conf/httpd.conf
	LoadModule unique_id_module modules/mod_unique_id.so
	LoadModule security2_module modules/mod_security2.so

.::

	# vi /etc/httpd/conf/prueba.conf
	<VirtualHost *:80>
		ServerAdmin webmaster@prueba.com
		DocumentRoot /var/www/html/prueba
		ServerName prueba.com
		ErrorLog logs/prueba.com-error_log
		CustomLog logs/prueba.com-access_log common
	</VirtualHost>

Si no se carga el modulo de mod_unique_id en le log de errores del virtual host lo veras.::

	# tail -f /var/log/httpd/prueba.com-error_log 
	[Sat Oct 22 13:27:24 2016] [error] ModSecurity: ModSecurity requires mod_unique_id to be installed.

Para estar seguros que cargo el modsecurity vemos el log.::

	# tail /var/log/httpd/error_log
	Starting httpd: [Sat Oct 22 15:26:09 2016] [notice] SELinux policy enabled; httpd running as context unconfined_u:system_r:httpd_t:s0
	[Sat Oct 22 15:26:09 2016] [notice] suEXEC mechanism enabled (wrapper: /usr/sbin/suexec)
	[Sat Oct 22 15:26:10 2016] [notice] ModSecurity for Apache/2.9.1 (http://www.modsecurity.org/) configured.
	[Sat Oct 22 15:26:10 2016] [notice] ModSecurity: APR compiled version="1.3.9"; loaded version="1.3.9"
	[Sat Oct 22 15:26:10 2016] [notice] ModSecurity: PCRE compiled version="7.8 "; loaded version="7.8 2008-09-05"
	[Sat Oct 22 15:26:10 2016] [notice] ModSecurity: LIBXML compiled version="2.7.6"
	[Sat Oct 22 15:26:10 2016] [notice] ModSecurity: StatusEngine call: "2.9.1,Apache/2.2.15 (CentOS),1.3.9/1.3.9,7.8/7.8 2008-09-05,(null),2.7.6,d54b1b562964f4ad77a762900da18b3f94b9346b"
	[Sat Oct 22 15:26:10 2016] [notice] ModSecurity: StatusEngine call successfully sent. For more information visit: http://status.modsecurity.org/
		                                                       [  OK  ]
	[Sat Oct 22 15:26:10 2016] [notice] Digest: generating secret for digest authentication ...
	[root@waf01 httpd]# [Sat Oct 22 15:26:10 2016] [notice] Digest: done
	[Sat Oct 22 15:26:11 2016] [notice] Apache/2.2.15 (Unix) DAV/2 configured -- resuming normal operations


Ya aquí tenemos el módulo de ModSecurity ejecutándose pero solo en modo DETECTION_ONLY


Tambien se puede ver el log de modsecurity.::

	# tail -f /var/log/modsec_audit.log &

