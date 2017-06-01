<?php
	session_start();
	if(!isset($_SESSION['idusuario'])){
		header("Location: signin.php");
    	exit();
	}

	include_once dirname(__FILE__) . '/utils/util.php';

	$errorGeneral = '';
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if(!isset($_GET['idsala'])){
			header("Location: room.php");
    		exit();
		}else{
			$idsala = $_GET['idsala'];
			$sql = "select * from sala where idsala = $idsala";
			$result = query($sql,1);
			if($result->success){
				$row = $result->result->fetch_assoc();
				$nombre=$row['nombre'];
				$idsala = $row['idsala'];
			}else{
				$errorGeneral = 'Error general. '.$result->result.' '.$result->errno;
			}
		}
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$idsala = $_POST['idsala'];
		$idusuario = $_SESSION['idusuario'];
		$date = $_POST['date'];
		$sql = "insert into solicitud (fecha_inicial,idusuario,idsala) values ('$date','$idusuario','$idsala')";
		$result = query($sql,2);
		if($result->success){
			messageAndRedirectAlert('Solicitud enviada.','room.php');
		}else
			$errorGeneral = 'Error general. '.$result->result.' '.$result->errno;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Reserva - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Sala</h2>
			<form action="detailRoom.php" method="POST">
				<label class="error"><?php echo $errorGeneral; ?></label><br>

				<input hidden name="idsala" value="<?php if (isset($_POST['idsala'])) echo htmlspecialchars($_POST['idsala']); else echo $idsala; ?>" />

				<label for="nombre">Nombre: </label>
				<input type="text" readonly="true" name="nombre" value = "<?php if (isset($_POST['nombre'])) echo htmlspecialchars($_POST['nombre']); else echo $nombre; ?>" /><br>

				<label for="date">Fecha: </label>
		      	<input type="text" readonly="true" name="date" value="<?php echo (new DateTime())->format('Y-m-d H:i:s'); ?>" /><br>

		      	<input name="submit" type="submit" value="Reservar" />
			</form>
		</main>
	</body>
</html>