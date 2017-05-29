<?php
	session_start();
	if(!isset($_SESSION['idusuario'])){
		header("Location: signin.php");
    	exit();
	}

	include_once dirname(__FILE__) . '/utils/util.php';

	$errorGeneral = $nombre = $fabricante = $num_serie = '';
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if(isset($_GET['idequipo'])){
			$idequipo = filter($_GET['idequipo']);
			$sql = "select * from equipo where idequipo = $idequipo";
			$result = query($sql,1);
			if($result->success){
				$row = $result->result->fetch_assoc();
				$nombre = $row['nombre'];
				$fabricante = $row['fabricante'];
				$num_serie = $row['num_serie'];
			}else{
				$errorGeneral = "Error general. ".$result->result;
			}
		}
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$date = $_POST['date'];
		$idequipo = $_POST['idequipo'];
		$idusuario = $_SESSION['idusuario'];
		$sql = "insert into solicitud (fecha_inicial,idusuario,idequipo) values ('$date','$idusuario','$idequipo')";
		$result = query($sql,2);
		if($result->success){
			messageAndRedirectAlert('Solicitud realizada.','device.php');
		}else{
			$errorGeneral = "Error general. ".$result->result." ".$result->errno;
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Solicitar - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Solicitud de equipo</h2>
			<label class="error"> <?php echo $errorGeneral; ?> </label>
			<form action="requestDevice.php" method="POST">
				<label for="idequipo">ID: </label>
				<input type="text" readonly="true" name="idequipo" value="<?php echo $idequipo; ?>" /><br>

				<label for="nombre">Nombre: </label>
		      	<input type="text" readonly="true" name="nombre" value="<?php echo $nombre; ?>" /><br>

		      	<label for="fabricante">Fabricante: </label>
		      	<input type="text" readonly="true" name="fabricante" value="<?php echo $fabricante; ?>" /><br>

		      	<label for="num_serie">Numero de serie: </label>
		      	<input type="text" readonly="true" name="num_serie" value="<?php echo $num_serie; ?>" /><br>

		      	<label for="date">Fecha: </label>
		      	<input type="text" readonly="true" name="date" value="<?php echo (new DateTime())->format('Y-m-d H:i:s'); ?>" /><br>

		      	<input name="submit" type="submit" value="Solicitar" />
			</form>
		</main>
	</body>
</html>