<?php

$connect = new \PDO("mysql:host=".db_host."; dbname=".db_nombre, db_usuario, db_pasword);
$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$connect->exec("SET CHARACTER SET utf8");

return $connect;