Instalar WAF en CentOS
=========================

Descargamos el fuente desde.

http://modsecurity.org/download.html

Apache ya debe estar instalado.

Instalamos los paquetes requeridos para poder realizar la compilacion.::

	# yum --disablerepo=\* --enablerepo=c6-media install gcc make httpd-devel libxml2 pcre-devel libxml2-devel curl-devel git

Instalamos prerequisitos.::

	# yum --disablerepo=\* --enablerepo=c6-media install libxml2 libxml2-devel libtool

	# yum check-update libcurl

.::

	# tar -xvzf modsecurity-2.9.1.tar.gz

	# cd modsecurity-2.9.1

	# ./autogen.sh

	# ./configure

	# make

	# make install

Este paso es solo si no existe en dicha ruta.::

	# cp /usr/local/modsecurity/lib/mod_security2.so /usr/lib64/httpd/modules/

Descargamos las reglas de modsecurity.::

	# cd /etc/httpd

	# git clone https://github.com/SpiderLabs/owasp-modsecurity-crs.git

	# mv owasp-modsecurity-crs/ modsecurity-crs

	# cd modsecurity-crs/

	# mv odsecurity-crs/modsecurity_crs_10_setup.conf.example conf.d/modsecurity_crs_10_config.conf

	# ls
	activated_rules  CHANGES             INSTALL  lua                           optional_rules  slr_rules
	base_rules       experimental_rules  LICENSE  modsecurity_crs_10_conf.conf  README.md       util

	# cd ..

Le decimos al apache que solo carge las reglas base, tiene mas...!!!.:

	# vi conf/httpd.conf
	Include modsecurity-crs/*.conf
	Include modsecurity-crs/base_rules/*.conf

.:

	# service httpd restart

Listo si vemos los logs modsec_audit.log 

Para estar seguros que cargo el modsecurity vemos el log.:

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

Tambien se puede ver el log de modsecurity que se va registrar todo.:

	# tail -f /var/log/modsec_audit.log &



