<?php 
include_once '../conexionBD/conexionBD.php';
session_start();

$listadoVuelos;
$listadoTripulacion;
$listadoAeropuertos;
$vuelos_tripulantes;

try {
	// Traer vuelos
  $sql = 'SELECT * FROM vuelos';
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  $listadoVuelos = $stmt->fetchAll(PDO::FETCH_NUM);

// Aeropuertos
  $sql = 'SELECT * FROM aeropuertos';
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  $listadoAeropuertos = $stmt->fetchAll(PDO::FETCH_NUM);


// Traer tripulantes asociados a un vuelo
  $sql = 'SELECT id_vuelo, id_tripulante FROM vuelos_tripulacion';
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  $vuelos_tripulantes = $stmt->fetchAll(PDO::FETCH_NUM);

// Tripulacion
  $sql = 'SELECT * FROM tripulacion';
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  $listadoTripulacion = $stmt->fetchAll(PDO::FETCH_NUM);
	
} catch (\Throwable $th) {
  $_SESSION['errorFetchData'] = "No se han podido recuperar los datos de la BD: ".$th->getMessage();
}

// Filtro de vuelos por fecha, aeropuerto origen, y aeropuerto destino
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
    $filtroFecha = $_POST['filtroFecha'];
    $filtroAeropuertoOrigen = $_POST['filtroAeropuertoOrigen'];
    $filtroAeropuertoDestino = $_POST['filtroAeropuertoDestino'];
		
    // Si hay filtros, hacemos el query con los filtros, sino, traemos todos los vuelos con el WHERE 1=1  (todas las filas)
    $sqlFiltro = '';
    try {
        // 1 = 1 es por si clickas en aplicar filtros pero no indicaste ningun filtro, asi te muestra todos los vuelos
        $sqlFiltro = 'SELECT * FROM vuelos WHERE 1=1';
        $filtros = [];
    
        if (!empty($filtroFecha)) {
            $sqlFiltro .= ' AND fecha = :fecha';
            $filtros[':fecha'] = $filtroFecha;
        }
        if (!empty($filtroAeropuertoOrigen)) {
            $sqlFiltro .= ' AND id_origen = :origen';
            $filtros[':origen'] = $filtroAeropuertoOrigen;
        }
        if (!empty($filtroAeropuertoDestino)) {
            $sqlFiltro .= ' AND id_destino = :destino;';
            $filtros[':destino'] = $filtroAeropuertoDestino;
        }
    
        $stmt = $conexion->prepare($sqlFiltro);
        foreach ($filtros as $key => $value) {
          $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        $listadoVuelos = $stmt->fetchAll(PDO::FETCH_NUM);
				
        
    }catch (\Exception $e) {
        $_SESSION['errorFiltros'] = "Error al filtrar los datos: ". $e->getMessage();
    }


}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de vuelos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/vuelos.css">
</head>

<body>
    <?php include '../navbar/navBar.php'; ?>
    <div class="container mt-5">
        <?php 
				if(isset($_SESSION['errorFetchData'])) { 
					echo '<div class="alert alert-danger">'.$_SESSION['errorFetchData']. '</div>'; 
					unset($_SESSION['errorFetchData']); }
				?>
        <?php 
				if(isset($_SESSION['errorFiltros'])) { 
					echo '<div class="alert alert-danger">'.$_SESSION['errorFiltros']. '</div>'; 
					unset($_SESSION['errorFiltros']); }
				?>
        <?php 
				if (isset($_SESSION['errorActualizarVuelo'])) {
					echo '<div class="alert alert-danger">' . $_SESSION['errorActualizarVuelo'] . '</div>';
        	unset($_SESSION['errorActualizarVuelo']);
        }
				?>
        <h2 class="mb-4">Vuelos programados</h2>

        <div class="filters-container">
            <form action="vuelos.php" method="post">
                <div class="row">
                    <div class="col-md-4">
                        <label for="filtroAeropuertoOrigen" class="form-label">Filtrar aeropuerto de origen:</label>
                        <select name="filtroAeropuertoOrigen" class="form-select">
                            <option value="">Selecciona origen</option>
                            <?php 
                                foreach ($listadoAeropuertos as $a) {
                                    echo "<option value=".$a[0].">".$a[1]."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtroAeropuertoDestino" class="form-label">Filtrar aeropuerto de destino:</label>
                        <select name="filtroAeropuertoDestino" class="form-select">
                            <option value="">Selecciona destino</option>
                            <?php 
                                foreach ($listadoAeropuertos as $a) {
                                    echo "<option value=".$a[0].">".$a[1]."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtroFecha" class="form-label">Filtrar por fecha:</label>
                        <input type="date" name="filtroFecha" class="form-control">
                    </div>
                </div>
                <div class="mt-3">
                    <input type="submit" value="Aplicar filtros" class="btn btn-primary">
                    <a href="vuelos.php" class="btn btn-secondary ms-2">Limpiar filtros</a>
                </div>
            </form>
        </div>
        <p>** El botón para cambiar el estado a Volando aparecerá cuando el vuelo esté entre la hora de salida y
            llegada, cuando pase la hora de llegada aparece el de Finalizar Vuelo **
        </p>
        <table class="table table-striped table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Número de plazas</th>
                    <th>Disponibles</th>
                    <th>Fecha</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Estado</th>
                    <th>Hora Salida</th>
                    <th>Hora Llegada</th>
                    <th>Tripulacion</th>
                    <th>Cambiar estado</th>
                </tr>
            </thead>

            <tbody>
                <?php 
        foreach ($listadoVuelos as $v) {
            // Obtener la hora actual
            $now = new DateTime();
            $horaActual = $now->format('H:i:s');
            $horaLlegada = trim($v[8]);
            $estadoVuelo = $v[6];

            $mostrarBotonFinalizar = ($horaActual > $horaLlegada && $estadoVuelo != 'Finalizado');
            $mostrarBotonCambiarEstado = ($horaActual <= $horaLlegada);
    ?>
                <tr>
                    <td><?= $v[1] ?></td>
                    <td><?= $v[2]?></td>
                    <td><?= $v[3] ?></td>
                    <td>
                        <?php
                foreach ($listadoAeropuertos as $a) {
                    if ($a[0] == $v[4]) echo $a[1];
                }
            ?>
                    </td>
                    <td>
                        <?php
                foreach ($listadoAeropuertos as $a) {
                    if ($a[0] == $v[5]) echo $a[1];
                }
            ?>
                    </td>
                    <td><?= $v[6] ?></td>
                    <td><?= $v[7] ?></td>
                    <td><?= $v[8] ?></td>
                    <td>
                        <?php
                foreach ($vuelos_tripulantes as $v_t) {
                    if ($v[0] == $v_t[0]) {
                        foreach ($listadoTripulacion as $t) {
                            if ($t[0] == $v_t[1]) echo "<strong>".$t[1]." ".$t[2].'</strong> ('.$t[3].')';
                        }
                        echo ' | ';
                    }
                }
            ?>
                    </td>
                    <td>
                        <?php if ($mostrarBotonCambiarEstado) { ?>
                        <form action='cambiarEstado.php' method='post'>
                            <input type='submit' value='Cambiar estado' class="btn btn-warning btn-sm mt-1">
                            <input type='hidden' name='idVuelo' value='<?= $v[0] ?>'>
                        </form>
                        <?php } ?>

                        <?php if ($mostrarBotonFinalizar) { ?>
                        <form action='../procesosBatch/script.php' method='post'>
                            <input type='submit' value='Vuelo Finalizado' class="btn btn-danger btn-sm mt-1">
                            <input type='hidden' name='idVuelo' value='<?= $v[0] ?>'>
                        </form>
                        <?php } ?>
                    </td>
                </tr>
                <?php
        }
    ?>
            </tbody>

        </table>
    </div>
</body>

</html>