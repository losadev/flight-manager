<?php
include_once '../conexionBD/conexionBD.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'];
    $pwd = password_hash($_POST['pwd'], PASSWORD_DEFAULT);

    try {
        $sql = "UPDATE usuarios SET pwd = :pwd, pwdCambiada = true WHERE nombre = :nombre";
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':pwd', $pwd, PDO::PARAM_STR);

        $stmt->execute();
        $_SESSION['usuarioLogueado'] = $nombre;
        header("Location: ../vuelos/vuelos.php");
    } catch (\Throwable $th) {

    echo "Error: " . $th->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/gloabal.css">
</head>

<body>
    <div class="container">

        <form action="cambioPwd.php" method="post">

            <fieldset>
                <legend>Cambia tu contraseña</legend>
                <div class="mb-3">
                    <input class="form-control" placeholder="Nombre" aria-label="Nombre" aria-describedby="basic-addon1"
                        type="text" name="nombre">
                </div>

                <div class="mb-3">
                    <input class="form-control" placeholder="Contraseña" type="pwd" name="pwd">
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary" type="submit">Cambiar contraseña</button>
                </div>
            </fieldset>

        </form>

    </div>
</body>

</html>