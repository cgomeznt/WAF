Apache Evasive Maneuvers Module
===============================

https://www.zdziarski.com/blog/?page_id=442

::

	# wget http://www.zdziarski.com/blog/wp-content/uploads/2010/02/mod_evasive_1.10.1.tar.gz

Manual en el RAEDME

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





