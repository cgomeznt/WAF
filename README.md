## WAF (Web Application Firewall)

WAF (Web Application Firewall)son modulos para los servidores HTTP llamado ModSecurity y ModEvasive, implementado como un módulo para diferentes servidores HTTP (Apache, NGINX y Microsoft IIS) multiplataforma de código abierto (open source). Esta herramienta permite ganar visibilidad dentro del tráfico HTTP(S) y provee un lenguaje de reglas y una API para implementar protecciones avanzadas. Esto significa que es posible filtrar tráfico HTTP, directamente en el servidor Web, según el contenido de las peticiones de los clientes, lo cual permite detectar y bloquear ataques de tipo XSS (Cross Site Scripting), SQLi (SQL injection), session hijacking, etc.
Las características principales de ModSecurity son su capacidad de log y filtrado. El log de auditoría permite almacenar el detalle de cada petición en un archivo de log, incluyendo los payloads de los POST HTTP. Los pedidos entrantes a su vez pueden ser analizados, y los pedidos ofensivos rechazados (o simplemente registrados en el log, de acuerdo a cómo se configure). De esta forma es posible incluso permitir que se ejecuten aplicaciones inseguras en nuestros servidores Web (en caso de que no quede otra alternativa claro está, los sysadmins conocen bien este tipo de escenarios) ya que están siendo protegidas por ModSecurity.
Mod Security trabaja con sets de reglas especializadas y personalizables, que podemos cargar o excluir según virtualhost o directorio. Estas reglas trabajan filtrando ataques por Cross Scripting o XSS, inyecciones SQL, anomalías en protocolos, Robots maliciosos, Trojanos, inclusión de archivos (LFI), etc y recientemente se incorpora un set de reglas especificas (slr_rules) para CMS como Joomla, Wordpress o PHPBB.


* [Instalar WAF en CentOS 6](guia/instalar.rst)
* [Configurar WAF para todos los virtualhost](guia/waffallvirtualhost.rst)
* [Configurar WAF para un solo virtualhost](guia/waffvirtualhost.rst)
* [Hacer pruebas](guia/pruebas.rst)
* [Configurar WAF mod_evasive](guia/mod_evasive.rst)
* [Instalar WAF en CentOS 7](guia/instalar7.rst)
* [Configurar WAF Centos 7 para un solo virtualhost](guia/waffvirtualhost7.rst)



