<?php
/* 
Datos de Conexión a la Base de Datos
Database Connection Data
*/
$i=$j=$k=0;

// Severname
$db_host = 'localhost';
// Username
$db_user = 'root';
// Password DB
$db_password = '';
// DB Name
$db_name = 'midcenturywareho_psdb2';
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";

try{
   $conn = new PDO($dsn, $db_user, $db_password);
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   //echo "Connection EEE - ".$dsn;
}
catch (PDOException $e){
   echo "Connection failed - ".$e->getMessage();
}

?>
