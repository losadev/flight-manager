<?php 
include_once '../conexionBD/conexionBD.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {

        $vuelo_seleccionado = $_POST['idVuelo'];
        $id_cliente = $_SESSION['cliente'];

        // Recuperar el aeropuerto
        $sql = 'SELECT id_origen, id_destino, estado FROM vuelos WHERE id = :id_vuelo';
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_vuelo', $vuelo_seleccionado, PDO::PARAM_INT);
        $stmt->execute();
        $aeropuerto = $stmt->fetch(PDO::FETCH_NUM);

        if (!$aeropuerto) {
            throw new Exception('No se encontró información del aeropuerto.');
        }

        // Recuperar el id de vuelo_cliente
        $sql = 'SELECT id FROM vuelos_clientes WHERE id_vuelo = :id_vuelo AND id_cliente = :id_cliente';
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_vuelo', $vuelo_seleccionado, PDO::PARAM_INT);
        $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $stmt->execute();
        $id_vuelo_cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$id_vuelo_cliente) {
            throw new Exception('No se encontró el vuelo para este cliente.');
        }

        // Insertar en la tabla equipajes
        $sql2 = 'INSERT INTO equipajes (id_vuelo_cliente, peso, tamaño, aeropuerto) 
                VALUES (:id_vuelo_cliente, :peso, :tamano, :aeropuerto)';
        $stmt2 = $conexion->prepare($sql2);
        $stmt2->bindParam(':id_vuelo_cliente', $id_vuelo_cliente['id'], PDO::PARAM_INT);
        $stmt2->bindParam(':peso', $_POST['peso'], PDO::PARAM_INT);
        $stmt2->bindParam(':tamano', $_POST['tamanho'], PDO::PARAM_INT);
        if($aeropuerto[2] == 'Finalizado') {
            $stmt2->bindParam(':aeropuerto', $aeropuerto[1], PDO::PARAM_INT);
        }else {
            $stmt2->bindParam(':aeropuerto', $aeropuerto[0], PDO::PARAM_INT);
        }
        $stmt2->execute();
        header("Location: ../equipaje/elegirVuelo.php");
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>