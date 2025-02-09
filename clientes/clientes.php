<?php 
include '../conexionBD/conexionBD.php';
session_start();

$clientes = [];
$listadoVuelos = [];
$aeropuertos = [];

// Recuperar clientes
try {
    $sql = 'SELECT id, nombre FROM clientes';
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_NUM);

// Recuperar vuelos
    $sql = 'SELECT * FROM vuelos';
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $listadoVuelos = $stmt->fetchAll(PDO::FETCH_NUM);

// Recuperar aeropuertos
    $sql = 'SELECT * FROM aeropuertos';
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $aeropuertos = $stmt->fetchAll(PDO::FETCH_NUM);
} catch (\Throwable $th) {
    $_SESSION['errorFetchData'] = '<div class="alert alert-warning">Error al recuperar los datos de la BD: '.$th->getMessage().'</div>';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente = $_POST['cliente'];
    $vuelo = $_POST['vuelo'];

    try {
        // Comprobar si hay plazas disponibles
        $sql = 'SELECT disponibles, estado FROM vuelos WHERE id = :id_vuelo';
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_vuelo', $vuelo, PDO::PARAM_INT);
        $stmt->execute();
        $plazasDisponibles = $stmt->fetch(PDO::FETCH_NUM)[0];

        if ($plazasDisponibles > 0) {
            // Asignar cliente al vuelo
            $sql2 = 'INSERT INTO vuelos_clientes (id_vuelo, id_cliente) VALUES (:id_vuelo, :id_cliente)';
            $stmt2 = $conexion->prepare($sql2);
            $stmt2->bindParam(':id_vuelo', $vuelo, PDO::PARAM_INT);
            $stmt2->bindParam(':id_cliente', $cliente, PDO::PARAM_INT);
            $stmt2->execute();

            // Restar una plaza
            $sql3 = 'UPDATE vuelos SET disponibles = disponibles - 1 WHERE id = :id_vuelo';
            $stmt3 = $conexion->prepare($sql3);
            $stmt3->bindParam(':id_vuelo', $vuelo, PDO::PARAM_INT);
            $stmt3->execute();

        } else {
            $_SESSION['sinPlazas'] = '<div class="alert alert-warning">No hay plazas disponibles en este vuelo.</div>';
        }
    } catch (\Throwable $th) {
        $_SESION['errorAsignarCliente'] = '<div class="alert alert-danger">El cliente ya está asignado a este vuelo</div>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/gloabal.css">
</head>

<body>
    <?php include '../navbar/navBar.php'; ?>

    <div class="container">
        <?php if(isset($_SESSION['sinPlazas'])) { echo $_SESSION['sinPlazas']; unset($_SESSION['sinPlazas']); }?>
        <?php if(isset($_SESION['errorAsignarCliente'])) { echo $_SESION['errorAsignarCliente']; unset($_SESION['errorAsignarCliente']); }?>
        <?php if(isset($_SESSION['errorFetchData'])) { echo $_SESSION['errorFetchData']; unset($_SESSION['errorFetchData']); }?>
        <h1>Gestión de clientes</h1>
        <form action="clientes.php" method="post">
            <fieldset>
                <legend>Asigna un vuelo al cliente</legend>
                <div class="mb-3">
                    <label for="cliente" class="form-label">Cliente:</label>
                    <select name="cliente" class="form-select">
                        <?php 
                        foreach ($clientes as $c) {
                        ?>
                        <option value="<?=$c[0]?>"><?=$c[1]?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="vuelo" class="form-label">Vuelos disponibles: </label>
                    <select name="vuelo" class="form-select">
                        <?php 
                        foreach ($listadoVuelos as $v) {
                            if($v[6] !== 'Finalizado' && $v[1] >= 1) {
                                foreach ($aeropuertos as $a) {     
                                    if($v[4] === $a[0]) {
                                        echo '<option value="'.$v[0].'">Plazas: '.$v[1].' - Disponibles: <strong>'.$v[2].' - Fecha salida: '.$v[3].' - Origen: '.$a[1].'</strong></option>';
                                        break;
                                    }
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="submit" value="Asignar cliente" class="btn btn-primary">
                    <input type="reset" value="Limpiar" class="btn btn-secondary">
                </div>
            </fieldset>
        </form>
    </div>
</body>

</html>