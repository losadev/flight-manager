<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NavBar</title>
</head>
<body>
<nav class="navbar" style="background-color: #004080; padding: 1rem;">
  <div class="container-fluid" style="display: flex; justify-content: space-between; align-items: center;">
    <ul style="list-style: none; display: flex; gap: 1rem; margin: 0; padding: 0;">
			<li><a class="navbar-brand" href="../vuelos/crearVuelo.php" style="color: white; text-decoration: none; font-weight: bold;">Crear vuelo</a></li>
			<li><a class="navbar-brand" href="../vuelos/vuelos.php" style="color: white; text-decoration: none; font-weight: bold;">Vuelos</a></li>
      <li><a class="navbar-brand" href="../clientes/clientes.php" style="color: white; text-decoration: none; font-weight: bold;">Clientes</a></li>
			<li><a class="navbar-brand" href="../equipaje/elegirCliente.php" style="color: white; text-decoration: none; font-weight: bold;">Equipajes</a></li>
    </ul>
    <div class="logout">
    	<span style="color: white;">
				<?php
				if(isset($_SESSION['usuarioLogueado'])) echo $_SESSION['usuarioLogueado'];
				else echo "Nada";										 
				?>
			</span>		
			<span>
				<img src="../img/icons8-usuario-masculino-en-círculo-50.png" alt="user-profile">
			</span>	
    	<a class="navbar-brand" href="../login/logout.php" style="color: white; text-decoration: none; font-weight: bold;"><img src="../img/icons8-salida-48.png" alt="Logout"></a>
  	</div>
  </div>
</nav>
</body>
</html>
