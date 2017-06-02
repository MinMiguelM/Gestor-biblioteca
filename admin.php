<?php
	session_start();
	if(isset($_SESSION['idusuario'])){
		if($_SESSION['rol'] == 'user'){
			header("Location: index.php");
    		exit();
		}		
	}else{
		header("Location: signin.php");
    	exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Admin - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Panel de administracion</h2>
			<div class="flex-container">
				<div class="flex-item"><a href="manageObject.php"><strong>Creacion libros/equipos</strong></a></div>
			  	<div class="flex-item"><a href="report.php"><strong>Reportes</strong></a></div>
			  	<div class="flex-item"><a href="manageRequest.php"><strong>Centro de mensajes</strong></a></div>  
			</div>
			<div class="flex-container">
			  	<div class="flex-item"><a href="manageEvent.php"><strong>Creacion eventos</strong></a></div>  
			  	<div class="flex-item"><a href="manageRoom.php"><strong>Creacion salas</strong></a></div>  
			  	<div class="flex-item"><a href="manageSearch.php"><strong>Busqueda libros/equipos</stron></a></div> 
			</div>
		</main>
	</body>
</html>