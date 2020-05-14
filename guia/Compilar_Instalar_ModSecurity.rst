Compilar Instalar ModSecurity 2.9.3
=====================================

Instalar estos requisitos::

	# yum install libxml2.i686 libxml2-devel.i686

Descargar ModSecurity desde aqui. https://www.modsecurity.org/tarball/2.9.3/modsecurity-2.9.3.tar.gz

Descomprimir::

	# tar -xvzf modsecurity-2.9.3.tar.gz
	# cd modsecurity-2.9.3

Compilar el ModSecurity::

	# ./configure \
	--with-apxs=/orasoft/product/apache/2.4.43/bin/apxs \
	--with-apr=/orasoft/product/apr/ \
	--with-apu=/orasoft/product/apr-util/ 
	# make
	# make install
	
Configuramos el ModSecurity con sus reglas de seguridad::

	# wget https://github.com/SpiderLabs/owasp-modsecurity-crs/tarball/master
	# mv master master.tar.gz
	# tar xvzf master.tar.gz
	# mv SpiderLabs-owasp-modsecurity-crs-56cad3a/ /orasoft/product/apache/2.4.43/conf/crs
	# cd /orasoft/product/apache/2.4.43/conf/crs/
	# mv modsecurity_crs_10_setup.conf.example modsecurity_crs_10_setup.conf
	# cp modsecurity_crs_10_setup.conf activated_rules/
	# for f in $(ls base_rules/) ; do ln -s /orasoft/product/apache/2.4.43/conf/crs/base_rules/$f activated_rules/$f ; done
	# for f in $(ls optional_rules/) ; do ln -s /orasoft/product/apache/2.4.43/conf/crs/optional_rules/$f activated_rules/$f ;done
	# cd
	# cp modsecurity-2.9.3/modsecurity.conf-recommended /etc/modsec/modsecurity.conf
	# cp modsecurity-2.9.3/unicode.mapping /etc/modsec/
	# vim /etc/modsec/whitelist.conf
	
En el archivo de configuracion agregamos el modulo de ModSecurity para que este habilitado y habilitamos el mod_unique_id::

	# vi conf/httpd.conf
	....
	LoadModule security2_module modules/mod_security2.so
	LoadModule unique_id_module modules/mod_unique_id.so
	....

Reiniciar el apache y verificamos que el modulo este en memoria::

	# bin/apachectl -M | egrep "security2|unique"

Habilitamos el ModSecurity en el virtual host::

	# vi conf/httpd.conf
	### Esto lo colocamos al final del archivo ###
	<IfModule security2_module>
	Include /etc/modsec/modsecurity.conf
	Include conf/crs/activated_rules/*.conf
	Include /etc/modsec/whitelist.conf
	SecRuleEngine On
	SecRule ARGS "mod_security_test" "t:normalisePathWin,id:99999,severity:4,msg:'Drive Access'"
	</IfModule>

Estos conf de ModSecurity me dieron error y los borre para apurar las pruebas::

	rm /orasoft/product/apache/2.4.43/conf/crs/activated_rules/modsecurity_crs_40_generic_attacks.conf
	rm /orasoft/product/apache/2.4.43/conf/crs/activated_rules/modsecurity_crs_41_sql_injection_attacks.conf
	rm /orasoft/product/apache/2.4.43/conf/crs/activated_rules/modsecurity_crs_41_xss_attacks.conf
	
Reiniciar el Apache

Abrir un navegador o con curl y probar. IMPORTANTE si colocas la IP ya automaticamente ModSecurity te bloqueara::

	http://www.cursoinfraestructura.com.ve/?test=mod_security_test

Debera aparecer lo siguiente::

	Forbidden
	You don't have permission to access this resource.



En el log debera estar identificado el ataque::

	# tail -f logs/error_log

	[Wed May 13 22:55:42.314068 2020] [:error] [pid 8542:tid 3044006720] [client 192.168.1.4:49575] [client 192.168.1.4] ModSecurity: Access denied with code 403 (phase 2). Pattern match "mod_security_test" at ARGS:test. [file "/orasoft/product/apache/2.4.43/conf/httpd.conf"] [line "510"] [id "99999"] [msg "Drive Access"] [severity "WARNING"] [hostname "www.cursoinfraestructura.com.ve"] [uri "/"] [unique_id "XryzLmciy4pBEs1YssZtHQAAAAI"]


	
