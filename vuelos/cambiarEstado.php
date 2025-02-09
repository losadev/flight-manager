<?php 
include_once '../conexionBD/conexionBD.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Asegúrate de usar 'POST' en mayúsculas
    // Traer vuelos
    $listadoVuelos;

    try {
        $sql = 'SELECT * FROM vuelos';
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $listadoVuelos = $stmt->fetchAll(PDO::FETCH_NUM);
    } catch (\Throwable $th) {
        $_SESSION['errorCambioEstado'] = "No se ha podido cambiar el estado del vuelo";
        exit;
    }

    // Cambiar el estado del vuelo
    $now = new DateTime(); // Fecha y hora actual
    $horaActual = $now->format('H:i:s'); // Obtener solo la hora actual
    $sqlCambioEstado = "";
    $nuevoEstado = "";
    $idVuelo = $_POST['idVuelo'];

    foreach ($listadoVuelos as $vuelo) {
        // Extraer horas de llegada y salida desde la base de datos
        $horaSalida = trim($vuelo[7]);
        $horaLlegada = trim($vuelo[8]);
        $fechaActual = trim($vuelo[3]);

        if ($idVuelo == $vuelo[0]) { // Verifica si es el vuelo seleccionado
            if ($fechaActual == $now->format('Y-m-d')) { // Verifica si es el día de hoy
                if ($horaActual <= $horaLlegada && $horaActual >= $horaSalida) {
                    $nuevoEstado = 'Volando';
                }
            }
        }
    }

    try {
        if (!empty($nuevoEstado)) {
            $sqlCambioEstado = "UPDATE vuelos SET estado = :estado WHERE id = :id";
            $stmtEstado = $conexion->prepare($sqlCambioEstado);
            $stmtEstado->bindParam(':estado', $nuevoEstado, PDO::PARAM_STR);
            $stmtEstado->bindParam(':id', $idVuelo, PDO::PARAM_INT);
            $stmtEstado->execute();
            echo "Estado del vuelo actualizado correctamente.<br>";
            if ($horaActual > $horaLlegada) {
                $nuevoEstado = 'Finalizado';
                if($nuevoEstado == 'Finalizado') {
                    // Ejecutar script batch
                    include '../procesosBatch/script.php';
                }
            }
            header("Location: vuelos.php");
        } else {
            $_SESSION['errorCambioEstado'] = "No se ha podido cambiar el estado del vuelo";
            header("Location: vuelos.php");
        }
    } catch (\Throwable $th) {
        $_SESSION['errorActualizarVuelo'] = "Error al actualizar el estado del vuelo: " . $th->getMessage();
    }
}

?>