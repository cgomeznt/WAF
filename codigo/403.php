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
