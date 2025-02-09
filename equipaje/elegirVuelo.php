<?php 
include '../conexionBD/conexionBD.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // id del cliente actual
    $cliente_selecccionado = $_POST['cliente'];
    $_SESSION['cliente'] = $cliente_selecccionado;
    // Recuperar los id de los vuelos del cliente
    $id_vuelosDelCliente;
		$vuelos;
		
    try {
    
			$sql = 'SELECT id_vuelo FROM vuelos_clientes WHERE id_cliente = :id_cliente';
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':id_cliente', $cliente_selecccionado);
      $stmt->execute();
      $id_vuelosDelCliente = $stmt->fetchAll(PDO::FETCH_NUM);

    // Recueprar todos los vuelos
      $sql2 = 'SELECT * FROM vuelos';
      $stmt2 = $conexion->prepare($sql2);
      $stmt2->execute();
      $vuelos = $stmt2->fetchAll(PDO::FETCH_NUM);

    // Recuperar id_vuelo_cliente
      $sql2 = "SELECT id_vuelo_cliente FROM vuelos_clientes WHERE id_vuelo = _id:vuelo AND id_cliente =  :id_cliente";
      $stmt2 = $conexion->prepare($sql2);
      $stmt2->bindParam(':id_cliente',$id_vuelo_cliente);
			
    } catch (\Throwable $th) {
        $_SESSION['errorFetchVuelos'] = 'Error: '. $th->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de equipajes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/gloabal.css">
</head>

<body>
    <?php include '../navbar/navBar.php'; ?>
    <div class="container">
        <?php 
        if(isset($_SESSION['errorFetchVuelos'])) { 
            echo '<div class="alert alert-danger">'.$_SESSION['errorFetchVuelos'].'</div>'; 
            unset($_SESSION['errorFetchVuelos']); 
        }?>
        <form action="asignarEquipaje.php" method="post">
            <h1>Gestión de equipajes</h1>
            <fieldset>
                <legend>Selecciona el vuelo</legend>
                <div class="mb-3">
                    <label class="form-label" for="cliente">Vuelos del cliente:</label>
                    <select class="form-control" name="idVuelo">
                        <?php 
                        // Mostrar informacion de los  vuelos del cliente
                        foreach ($vuelos as $vuelo) {
                            foreach ($id_vuelosDelCliente as $id) {
                                if($id[0] == $vuelo[0]) {
                                    if($vuelo[6] !== 'Finalizado') {
                                        echo '<option value="'.$vuelo[0].'"><strong>ID:</strong>'.$vuelo[0].' - Fecha salida: '.$vuelo[3].' - Hora salida:'.$vuelo[7].'</option>';
                                    }
                                }else {
                                    echo 'No hay vuelos del cliente';
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <legend>Características del equipaje</legend>
                <div class="mb-3">
                    <label class="form-label" for="peso">Peso:</label>
                    <input class="form-control" type="text" name="peso" placeholder="Peso en kg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="tamanho">Tamaño:</label>
                    <input class="form-control" type="text" name="tamanho" placeholder="Tamaño" required>
                </div>
                <div class="mb-3">
                    <input class="btn btn-primary" type="submit" value="Registrar Equipaje">
                    <a href="elegirCliente.php">Volver atrás</a>
                </div>
            </fieldset>
        </form>
    </div>
</body>

</html>