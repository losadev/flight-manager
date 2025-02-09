<?php 
include '../conexionBD/conexionBD.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $pwd = $_POST['pwd'];
    $usuarios;

    try {
        // Consulta solo el usuario que coincide
        $sql = 'SELECT pwd,pwdCambiada FROM usuarios WHERE nombre = :nombre';
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_NUM);
    } catch (\Throwable $th) {
    throw $th;
    }

    foreach ($usuarios as $pwdHashed) {
    
        if(password_verify($pwd, $pwdHashed[0]) && $pwdHashed[1] == 0) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['pwd'] = $pwd;   
            header("Location: cambioPwd.php");
            exit;
        }else if(password_verify($pwd, $pwdHashed[0]) && $pwdHashed[1] == 1){
            $_SESSION['usuarioLogueado'] = $nombre;
            header("Location: ../vuelos/vuelos.php");
        }else {
            $_SESSION['errorLogin'] = 'Contraseña o usuario incorrectos';
            header('Location: login.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/gloabal.css">
    </style>
</head>

<body>
    <div class="container" styles="background-color: --bs-secondary-color;">

        <form class="mb-3" action="login.php" method="post">
            <fieldset>
                <legend>Formulario de inicio de sesión</legend>

                <div class="mb-3">
                    <label for="form-label"><strong>Nombre:</strong></label>
                    <input class="form-control" placeholder="Username" aria-label="Username"
                        aria-describedby="basic-addon1" type="text" name="nombre">
                </div>

                <div class="mb-3">
                    <label for="form-label"><strong>Contraseña:</strong></label>
                    <input class="form-control" placeholder="Contraseña" aria-label="Contraseña"
                        aria-describedby="basic-addon1" type="password" name="pwd">
                </div>
                <?php 
                if(isset($_SESSION['errorLogin'])) echo "<p style='color:red;'>".$_SESSION['errorLogin']."</p>";
                ?>
                <div class="mb-3">
                    <button class="btn btn-primary" type="submit">Iniciar sesión</button>
                </div>
            </fieldset>

        </form>
    </div>
</body>

</html>