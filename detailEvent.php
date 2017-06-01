<?php
	session_start();
	if(!isset($_SESSION['idusuario'])){
		header("Location: signin.php");
    	exit();
	}

	include_once dirname(__FILE__) . '/utils/util.php';

	$errorGeneral = '';
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if(!isset($_GET['idevento'])){
			header("Location: event.php");
    		exit();
		}else{
			$idevento = $_GET['idevento'];
			$sql = "select * from evento where idevento = $idevento";
			$result = query($sql,1);
			if($result->success){
				$row = $result->result->fetch_assoc();
				$nombre=$row['nombre'];
				if($row['idsala'] == null)
					$lugar=$row['lugar'];
				else{
					$idsala = $row['idsala'];
					$sqlLugar = "select * from sala where idsala = $idsala";
					$resultSala = query($sqlLugar,1);
					$lugar=$resultSala->result->fetch_assoc()['nombre'];
				}
				$fecha_inicio=$row['fecha_inicio'];
				$fecha_fin=$row['fecha_fin'];
				$idevento = $row['idevento'];
			}else{
				$errorGeneral = 'Error general. '.$result->result.' '.$result->errno;
			}
		}
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$idevento = $_POST['idevento'];
		$idusuario = $_SESSION['idusuario'];
		$sql = "insert into usuarioXevento (idusuario,idevento) values ($idusuario,$idevento)";
		$result = query($sql,2);
		if($result->success){
			messageAndRedirectAlert('Suscripción realizada.','event.php');
		}else
			$errorGeneral = 'Error general. '.$result->result.' '.$result->errno;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Suscripciones - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Eventos</h2>
			<form action="detailEvent.php" method="POST">
				<label class="error"><?php echo $errorGeneral; ?></label><br>

				<input hidden name="idevento" value="<?php if (isset($_POST['idevento'])) echo htmlspecialchars($_POST['idevento']); else echo $idevento; ?>" />

				<label for="nombre">Nombre: </label>
				<input type="text" readonly="true" name="nombre" value = "<?php if (isset($_POST['nombre'])) echo htmlspecialchars($_POST['nombre']); else echo $nombre; ?>" /><br>

				<label for="fecha_inicio">Fecha inicio: </label>
				<input type="text" readonly="true" name="fecha_inicio" value = "<?php if (isset($_POST['fecha_inicio'])) echo htmlspecialchars($_POST['fecha_inicio']); else echo $fecha_inicio; ?>" /><br>

				<label for="fecha_fin">Fecha fin: </label>
				<input type="text" readonly="true" name="fecha_fin" value = "<?php if (isset($_POST['fecha_fin'])) echo htmlspecialchars($_POST['fecha_fin']); else echo $fecha_fin; ?>" /><br>

				<label for="lugar">Lugar: </label>
				<input type="text" readonly="true" name="lugar" value = "<?php if (isset($_POST['lugar'])) echo htmlspecialchars($_POST['lugar']); else echo $lugar; ?>" /><br>

		      	<input name="submit" type="submit" value="Suscribirse" />
			</form>
		</main>
	</body>
</html>