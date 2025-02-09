<?php
try {
  $servidor = "127.0.0.1";
  $username = "root";
  $pwd = "";
  $dbname = "ryanair2";

  $conexion = new PDO("mysql:host=$servername", $username, $pwd);
  $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $estructura = file_get_contents("estructura.sql");
  $conexion->exec($estructura);

  $datosIniciales = file_get_contents("carga_inicial.sql");
  $conexion->exec($datosIniciales);

  // Redirigir al login.php
  header("Location: http://127.0.0.1/Tarea6Ryanair/login/login.php"); // URL completa
  exit();

} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}
?>