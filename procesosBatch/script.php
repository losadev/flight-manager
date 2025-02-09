<?php 
include_once '../conexionBD/conexionBD.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idVuelo = $_POST['idVuelo'];

    try {
        // Actualizar el estado a Finalizado
        $sql = 'UPDATE vuelos SET estado = "Finalizado" WHERE id = :idVuelo';
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':idVuelo', $idVuelo);
        $stmt->execute();
        
        $sql = 'SELECT estado FROM vuelos WHERE id = :idVuelo';
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':idVuelo', $idVuelo);
        $stmt->execute();
        $estadoVuelo = $stmt->fetch(PDO::FETCH_NUM);
        
    } catch (\Throwable $th) {
        throw $th;
    }
    
    $clientesVuelo;
    if($estadoVuelo[0] == 'Finalizado') {
        echo 'Hola';
        // Recuperar los id de los clientes asociados a ese vuelo en la tabla vuelos_clientes
        try {
            $sql2 = 'SELECT id_cliente FROM vuelos_clientes WHERE id_vuelo = :idVuelo';
            $stmt2 = $conexion->prepare($sql2);
            $stmt2->bindParam(':idVuelo', $idVuelo);
            $stmt2->execute();
            $clientesVuelo = $stmt2->fetchAll(PDO::FETCH_NUM);

            // modificar la localizacion del equipaje al id_destino
                $sql3 = 'UPDATE equipajes SET aeropuerto = 
                (SELECT id_destino FROM vuelos WHERE id = :idVuelo) 
                WHERE id_vuelo_cliente IN (SELECT id FROM vuelos_clientes WHERE id_vuelo = :idVuelo)';
                $stmt3 = $conexion->prepare($sql3);
                $stmt3->bindParam(':idVuelo', $idVuelo);
                $stmt3->execute();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    $contador = 0;
    
    try {
        $conexion->beginTransaction();
        // Ahora recorro los ids recuperados y le sumo +10 a cada cliente en la tabla clientes
        foreach($clientesVuelo as $idCliente) {
            try {
                $sql3 = 'UPDATE clientes SET puntos = puntos + 10 WHERE id = :idCliente';
                $stmt3 = $conexion->prepare($sql3);
                $stmt3->bindParam(':idCliente', $idCliente[0]);
                $stmt3->execute();

                file_put_contents('registroExitoso.txt', "Cliente ID $idCliente[0]: Puntos sumados correctamente\n", FILE_APPEND);

                $contador++;

                // Al llegar a 10 registros procesados, ejecutar un commit
                if ($contador == 10) {
                    $conexion->commit();
                    $conexion->beginTransaction();
                    $contador = 0; 
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        $conexion->commit();
    } catch (\Throwable $th) {
        $conexion->rollBack(); // por si falla que deshaga los cambios
        file_put_contents('errores.txt', "Error procesando cliente ID $idCliente[0]: " . $th->getMessage() . "\n", FILE_APPEND);

    }

    header('Location: ../vuelos/vuelos.php');
}

?>