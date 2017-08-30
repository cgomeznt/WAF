Instalar WAF en CentOS 7
=========================

First of all, I would like to verify the server settings, mainly the present Apache version and the modules installed.::

	# httpd -V
	Server version: Apache/2.4.6 (CentOS)
	Server built:   Apr 12 2017 21:03:28
	Server's Module Magic Number: 20120211:24
	Server loaded:  APR 1.4.8, APR-UTIL 1.5.2
	Compiled using: APR 1.4.8, APR-UTIL 1.5.2
	Architecture:   64-bit
	Server MPM:     prefork
	  threaded:     no
		forked:     yes (variable process count)
	Server compiled with....
	 -D APR_HAS_SENDFILE
	 -D APR_HAS_MMAP
	 -D APR_HAVE_IPV6 (IPv4-mapped addresses enabled)
	 -D APR_USE_SYSVSEM_SERIALIZE
	 -D APR_USE_PTHREAD_SERIALIZE
	 -D SINGLE_LISTEN_UNSERIALIZED_ACCEPT
	 -D APR_HAS_OTHER_CHILD
	 -D AP_HAVE_RELIABLE_PIPED_LOGS
	 -D DYNAMIC_MODULE_LIMIT=256
	 -D HTTPD_ROOT="/etc/httpd"
	 -D SUEXEC_BIN="/usr/sbin/suexec"
	 -D DEFAULT_PIDLOG="/run/httpd/httpd.pid"
	 -D DEFAULT_SCOREBOARD="logs/apache_runtime_status"
	 -D DEFAULT_ERRORLOG="logs/error_log"
	 -D AP_TYPES_CONFIG_FILE="conf/mime.types"
	 -D SERVER_CONFIG_FILE="conf/httpd.conf"

You can use these command to identify the dynamically compiled modules enabled with Apache.::

	# httpd -M
	Loaded Modules:
	core_module (static)
	so_module (static)
	http_module (static)
	access_compat_module (shared)
	actions_module (shared)
	alias_module (shared)
	allowmethods_module (shared)
	auth_basic_module (shared)
	auth_digest_module (shared)
	[.....]
	systemd_module (shared)
	cgi_module (shared)
	php5_module (shared)

Installation
++++++++++++++++++
::

	# yum install mod_security -y

his will install the mod_security on your server. Now we need to configure it on our server.

Check and confirm the integration of the module to Apache
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

Check for the configuration file generated with the default set of rules. The configuration file will be located inside the Apache custom modules folder "/etc/httpd/conf.d/".::

	# pwd
	/etc/httpd/conf.d
	# ls mod_security.conf
	-rw-r--r-- 1 root root 2225 ago 29 20:44 mod_security.conf
	]# httpd -M | grep security
	security2_module (shared)

Now restart the Apache and verify whether the Mod_security module is loaded on restart in the Apache logs.::

	# tail -f /etc/httpd/logs/error_log
	[Tue Aug 29 21:05:02.153837 2017] [mpm_prefork:notice] [pid 25316] AH00170: caught SIGWINCH, shutting down gracefully
	[Tue Aug 29 21:05:05.730580 2017] [suexec:notice] [pid 25362] AH01232: suEXEC mechanism enabled (wrapper: /usr/sbin/suexec)
	[Tue Aug 29 21:05:05.730770 2017] [:notice] [pid 25362] ModSecurity for Apache/2.7.3 (http://www.modsecurity.org/) configured.
	[Tue Aug 29 21:05:05.730785 2017] [:notice] [pid 25362] ModSecurity: APR compiled version="1.4.8"; loaded version="1.4.8"
	[Tue Aug 29 21:05:05.730794 2017] [:notice] [pid 25362] ModSecurity: PCRE compiled version="8.32 "; loaded version="8.32 2012-11-30"
	[Tue Aug 29 21:05:05.730800 2017] [:notice] [pid 25362] ModSecurity: LUA compiled version="Lua 5.1"
	[Tue Aug 29 21:05:05.730825 2017] [:notice] [pid 25362] ModSecurity: LIBXML compiled version="2.9.1"
	AH00558: httpd: Could not reliably determine the server's fully qualified domain name, using fe80::a00:27ff:fec2:ca4c. Set the 'ServerName' directive globally to suppress this message
	[Tue Aug 29 21:05:06.740661 2017] [auth_digest:notice] [pid 25362] AH01757: generating secret for digest authentication ...
	[Tue Aug 29 21:05:06.741865 2017] [lbmethod_heartbeat:notice] [pid 25362] AH02282: No slotmem from mod_heartmonitor
	[Tue Aug 29 21:05:06.746462 2017] [mpm_prefork:notice] [pid 25362] AH00163: Apache/2.4.6 (CentOS) configured -- resuming normal operations
	[Tue Aug 29 21:05:06.746658 2017] [core:notice] [pid 25362] AH00094: Command line: '/usr/sbin/httpd -D FOREGROUND'
	[root@srv-vccs-haproxywaf01 conf.d]# vi prueba.conf 
	[root@srv-vccs-haproxywaf01 conf.d]# httpd -V
	AH00558: httpd: Could not reliably determine the server's fully qualified domain name, using fe80::a00:27ff:fec2:ca4c. Set the 'ServerName' directive globally to suppress this message

From the logs, you can identify the ModSecurity version loaded and other details.

Identifying the Nature
++++++++++++++++++++++++

We need to go through the ModSecurity configuration file to identify the include path for the custom rules which we can add for customization and also identify the log file path for further analysis.

We can add the custom rules inside this path according to the configuration.::

	# ModSecurity Core Rules Set configuration
	IncludeOptional modsecurity.d/*.conf
	IncludeOptional modsecurity.d/activated_rules/*.conf

	# pwd
	/etc/httpd/modsecurity.d
	# ls
	total 4
	drwxr-xr-x 2 root root 4096 Jun 10 2014 activated_rules
	And we can inspect the log file at /var/log/httpd/modsec_audit.log

Customizing ModSecurity with the Core rule sets
+++++++++++++++++++++++++++++++++++++++++++++++++

We can get the custom rule sets from the official repo. These rule sets are automatically symlinked to the activated rules and make it effective on install by default.::

	# yum -y install mod_security_crs




