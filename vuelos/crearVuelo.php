<?php 
include '../conexionBD/conexionBD.php'; 
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

// Tripulacion
  $sql = 'SELECT * FROM tripulacion';
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  $listadoTripulacion = $stmt->fetchAll(PDO::FETCH_NUM);

// Aeropuertos
  $sql = 'SELECT * FROM aeropuertos';
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  $listadoAeropuertos = $stmt->fetchAll(PDO::FETCH_NUM);

// Traer tripulantes asociados a un vuelo
  $sql = 'SELECT * FROM vuelos_tripulacion';
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  $vuelos_tripulantes = $stmt->fetchAll(PDO::FETCH_NUM);
	
} catch (\Throwable $th) {
    $_SESSION['errorFetchData'] = $th->getMessage();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Recuperar datos del form
    $n_plazas = $_POST['numPlazas'];
    $fecha = $_POST['date'];
    $origen = $_POST['origen'];
    $destino = $_POST['destino'];
    $estado = $_POST['estado'];
    $tripulantes = $_POST['tripulantes'];
    $horaSalida = $_POST['horaSalida'].':00';
    $horaLlegada = $_POST['horaLlegada'].':00';
    $ultimoVuelo;
    // Asignacion de hora actual a la fecha y hora
    $horaActual = date('H:i:s');
    $date = "$fecha 0$horaActual";
    // Creacion de un vuelo
    $_SESSION['cantidadVuelosHoy'] = 0;
    $hoy = getdate();
    

		// Insertar el nuevo vuelo en la BD
    try {
      $sql = "INSERT INTO vuelos 
      (n_plazas, disponibles, fecha, id_origen, id_destino, estado,hora_llegada, hora_salida) VALUES (:n_plazas,:disponibles, :fecha, :origen, :destino, :estado, :hora_llegada,:hora_salida)";  
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':n_plazas', $n_plazas, PDO::PARAM_INT);
      $stmt->bindParam(':disponibles', $n_plazas, PDO::PARAM_INT);
      $stmt->bindParam(':fecha', $date, PDO::PARAM_STR);
      $stmt->bindParam(':origen', $origen, PDO::PARAM_STR);
      $stmt->bindParam(':destino', $destino, PDO::PARAM_STR);
      $stmt->bindParam(':estado',$estado,PDO::PARAM_STR);
      $stmt->bindParam(':hora_llegada', $horaLlegada, PDO::PARAM_STR);
      $stmt->bindParam(':hora_salida', $horaSalida, PDO::PARAM_STR);
      $stmt->execute();
    
      // Traer el ultimo ID o ultimo vuelo
      $sql = 'SELECT MAX(id) FROM vuelos';
      $stmt = $conexion->prepare($sql);
      $stmt->execute();
      $ultimoVuelo = $stmt->fetch(PDO::FETCH_NUM)[0];
    
    
    } catch (\Throwable $th) {
      $_SESSION['errorCrearVuelo'] = 'No se han podido crear el vuelo'.$th->getMessage();
    }
    
    $tripulantes = $_POST['tripulantes'] ?? [];
     // Asignar vuelo con tripulante
    try {
      foreach ($tripulantes as $id_tripulante) {
        $sql = 'INSERT INTO vuelos_tripulacion (id_vuelo, id_tripulante) VALUES (:id_vuelo, :id_tripulante)';
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_vuelo', $ultimoVuelo, PDO::PARAM_INT);
        $stmt->bindParam(':id_tripulante', $id_tripulante, PDO::PARAM_INT);
        $stmt->execute();
      }
    
    } catch (\Throwable $th) {
      $_SESSION['errorVueloTripulante'] = "No se ha podido asignar el tripulante al vuelo". $th->getMessage();
    }
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/styles.css">
    <title>Vuelos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/gloabal.css">
</head>

<body>
    <?php include '../navbar/navBar.php'; ?>
    <div class="container mt-5">
        <!--ERRORES A LA HORA DE RECUPERAR E INSERTAR DATOS-->
        <?php 
        if (isset($_SESSION['maxVuelosTripulante'])) {
					echo '<div class="alert alert-danger">' . $_SESSION['maxVuelosTripulante'] . '</div>';
        	unset($_SESSION['maxVuelosTripulante']);
        }
        ?>
        <?php 
				if(isset($_SESSION['errorFetchData'])) {
					echo '<div class="alert alert-danger">' . $_SESSION['errorFetchData'] . '</div>';
					unset($_SESSION['errorFetchData']);
				};
				?>
        <?php
				if(isset($_SESSION['errorCrearVuelo'])) {
					echo '<div class="alert alert-danger">' . $_SESSION['errorCrearVuelo'] . '</div>';
					unset($_SESSION['errorCrearVuelo']);
				};
				?>
        <?php
				if(isset($_SESSION['errorVueloTripulante'])) {
					echo '<div class="alert alert-danger">' . $_SESSION['errorVueloTripulante'] . '</div>';
					unset($_SESSION['errorVueloTripulante']);
				};
				?>

        <form class="m-5" action="crearVuelo.php" method="post">
            <fieldset>
                <legend>Crear nuevo vuelo</legend>

                <div class="mb-3">
                    <label class="form-label" for="numPlazas"><strong>Número de plazas: </strong></label>
                    <input class="form-control" type="number" name="numPlazas">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="fecha"><strong>Fecha:</strong></label>
                    <input class="form-control" type="date" name="date" id="date">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="origen"><strong>Origen:</strong></label>
                    <select class="form-select" name="origen">
                        <?php 
                    foreach ($listadoAeropuertos as $a) {
                        echo "<option value='".$a[0]."'>".$a[1]."</option>";
                    }
                    ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="destino"><strong>Origen:</strong></label>
                    <select class="form-select" name="destino">
                        <?php 
                        foreach ($listadoAeropuertos as $a) {
                            echo "<option value='".$a[0]."'>".$a[1]."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="pilotos"><strong>Pilotos:</strong></label>
                    <select class="form-select" name="tripulantes[]" multiple>
                        <?php 
                        foreach ($listadoTripulacion as $tripulante) { 
                            if($tripulante[3] === 'Piloto'){
                                echo "<option value='".$tripulante[0]."'>".$tripulante[1]."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="copilotos"><strong>Copilotos:</strong></label>
                    <select class="form-select" name="tripulantes[]" multiple>
                        <?php 
                        foreach ($listadoTripulacion as $tripulante) { 
                            if($tripulante[3] === 'Copiloto'){
                                echo "<option value='".$tripulante[0]."'>".$tripulante[1]."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="asistentes"><strong>Asitentes de vuelo:</strong></label>
                    <select class="form-select" name="tripulantes[]" multiple>
                        <?php 
                        foreach ($listadoTripulacion as $tripulante) { 
                            if($tripulante[3] === 'Asistente de vuelo'){
                                echo "<option value='".$tripulante[0]."'>".$tripulante[1]."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="estado"><strong>Estado:</strong></label>
                    <select class="form-select" name="estado">
                        <option value="En Hora">En Hora</option>
                        <option value="Cancelado">Cancelado</option>
                        <option value="Retrasado">Retrasado</option>
                        <option value="Volando">Volando</option>
                        <option value="Finalizado">Finalizado</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="horaSalida"><strong>Hora salida:</strong></label>
                    <input class="form-control" type="time" name="horaSalida">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="horaSalida"><strong>Hora llegada:</strong></label>
                    <input class="form-control" type="time" name="horaLlegada">
                </div>

                <div class="mb-3">
                    <button class="btn btn-primary" type="submit">Registrar vuelo</button>
                </div>
                <a href="vuelos.php" class="btn btn-secondary">Ver vuelos</a>

            </fieldset>

        </form>
    </div>

</body>

</html>