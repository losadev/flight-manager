<?php 
include '../conexionBD/conexionBD.php';
session_start();
// Recuperar los id_cleinte de vuelos_clientes

$id_clientes;
$clientes_nombres;
try {
  $sql = 'SELECT * FROM vuelos_clientes';
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  $id_clientes = $stmt->fetchAll(PDO::FETCH_NUM);

// Recuperar los nombres de los clientes
  $sql = 'SELECT id,nombre FROM clientes';
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  $clientes_nombres = $stmt->fetchAll(PDO::FETCH_NUM);
} catch (\Throwable $th) {
  $_SESSION['errorFetchClientes'] = "No se han podido los datos de los clientes".$th->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Equipajes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/gloabal.css">
</head>

<body>
    <?php include '../navbar/navBar.php'; ?>
    <div class="container">
        <?php 
        if(isset($_SESSION['errorFetchClientes'])) { 
            echo '<div class="alert alert-danger">'.$_SESSION['errorFetchClientes'].'</div>'; 
            unset($_SESSION['errorFetchClientes']); 
        }?>
        <form action="elegirVuelo.php" method="post">
            <h1>Gestión de equipajes</h1>
            <fieldset>
                <legend>Selecciona un cliente</legend>
                <div class="mb-3">
                    <label class="form-label" for="cliente">Cliente:</label>
                    <select class="form-control" name="cliente">
                        <?php 
                        foreach ($id_clientes as $c) {
                            foreach ($clientes_nombres as $nombre) {
                                if($c[2] == $nombre[0]) {
                                    echo "<option value='$nombre[0]'>$nombre[1]</option>";
                                }
                            }                  
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <input class="btn btn-primary" type="submit" value="Ver vuelos del cliente">
                </div>
            </fieldset>
        </form>
    </div>

</body>

</html>