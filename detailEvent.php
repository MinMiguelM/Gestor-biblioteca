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
			header("Location: manageRequest.php");
    		exit();
		}else{
			$idevento = $_GET['idevento'];
			$sql = "select * from evento where idevento = $idevento";
			$result = query($sql,1);
			if($result->success){
				$row = $result->result->fetch_assoc();
				$nombre=$row['nombre'];
				$lugar=$row['lugar'];
				$fecha=$row['fecha'];
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

				<label for="fecha">Fecha: </label>
				<input type="text" readonly="true" name="fecha" value = "<?php if (isset($_POST['fecha'])) echo htmlspecialchars($_POST['fecha']); else echo $fecha; ?>" /><br>

				<label for="lugar">Lugar: </label>
				<input type="text" readonly="true" name="lugar" value = "<?php if (isset($_POST['lugar'])) echo htmlspecialchars($_POST['lugar']); else echo $lugar; ?>" /><br>

		      	<input name="submit" type="submit" value="Suscribirse" />
			</form>
		</main>
	</body>
</html>