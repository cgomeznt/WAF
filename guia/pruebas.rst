Hacer pruebas
===================

::

	http://ejemplo.com/?var=1' or '1'='1


	http://192.168.1.20/?var=1' or '1'='1


	http://192.168.1.20/?var=/../


	http://192.168.1.20/?var=/etc/passwd


Puede crear una regla asi::

	SecRule ARGS "tutu" "t:normalisePathWin,id:99999,severity:0,deny,msg:'Drive Access'"

y lo verifica asi::

	http://ejemplo.com/?var=tutu
