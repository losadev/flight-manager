<?php 
$dsn = 'mysql:dbname=ryanair2;host=127.0.0.1';
$usuario = 'root'; 
$contrasenia = '';

try {
  $conexion = new PDO($dsn, $usuario, $contrasenia);
  $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch(Throwable $ex) {
  echo "Error en la conexion con la BD:".$ex->getMessage();
}

?>