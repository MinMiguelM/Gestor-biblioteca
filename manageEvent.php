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

	include_once dirname(__FILE__) . '/utils/util.php';

	$errorGeneral = $selectSalas = '';
	$sqlSalas = "select * from sala where disponible = 1";
	$resultSalas = query($sqlSalas,1);
	if($resultSalas->success && $resultSalas->num_rows > 0){
		$selectSalas = '<select name="lugar_intern">';
		foreach ($resultSalas->result as $row) {
			$selectSalas .= '<option value="'.$row['idsala'].'">'.$row['nombre'].'</option>';
		}
		$selectSalas .= '</select>';
	}else{
		if(!$resultSalas->success)
			$errorGeneral = 'Error general. '.$resultSalas->result.' '.$resultSalas->errno;
		else
			$selectSalas = 'No hay salas disponibles.';
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$continue = true;
		$fecha_inicio = $_POST['fecha_inicio'];
		$fecha_fin = $_POST['fecha_fin'];
		$nombre = filter($_POST['nombre']);
		$hora_inicio = filter($_POST['hora_inicio']);
		$hora_fin = filter($_POST['hora_fin']);
		$fecha_inicio .= ' '.$hora_inicio.':00';
		$fecha_fin .= ' '.$hora_fin.':00';

		$dt_inicio = new DateTime($fecha_inicio);
		$dt_fin = new DateTime($fecha_fin);
		if($dt_inicio < $dt_fin){
			if($_POST['type'] == 'extern'){
				if(empty($_POST['lugar_extern'])){
					$continue = false;
					$errorGeneral = 'Debe llenar el campo para evento externo.';
				}else{
					$lugar = filter($_POST['lugar_extern']);
					$sql = "insert into evento (nombre,fecha_inicio,fecha_fin,lugar) values ('$nombre','$fecha_inicio','$fecha_fin','$lugar_extern')";
				}
			}else{
				if(isset($_POST['lugar_intern'])){
					$idsala = $_POST['lugar_intern'];
					$sqlUpdate = "update sala set disponible = 0 where idsala = $idsala";
					$sql = "insert into evento (nombre,fecha_inicio,fecha_fin,idsala) values ('$nombre','$fecha_inicio','$fecha_fin','$idsala')";
					$resultUpdate = query($sqlUpdate,3);
					if(!$resultUpdate->success)
						$continue = false;
				}else{
					$continue = false;
					$errorGeneral = 'No se puede hacer un evento interno si no hay salas disponibles';
				}
			}

			if($continue){
				$result = query($sql,2);
				if($result->success){
					messageAndRedirectAlert('Evento creado','admin.php');
				}else{
					$errorGeneral = 'Error general. '.$result->result.' '.$result->errno;
				}
			}
		}else{
			$errorGeneral = 'La fecha de inicio es posterior a la fecha fin.';
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Eventos - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Creacion de eventos</h2>
			<form action="manageEvent.php" method = "POST">
				<label class="error"><?php echo $errorGeneral; ?></label><br>

				<label for="nombre">Nombre: <span><em>(requerido)</em></span></label>
				<input type="text" name="nombre" value = "<?php if (isset($_POST['nombre'])) echo htmlspecialchars($_POST['nombre']); ?>" required /><br>

				<label for="fecha_inicio">Fecha inicio: <span><em>(requerido)</em></span></label>
		      	<input type="date" value = "<?php if (isset($_POST['fecha_inicio'])) echo htmlspecialchars($_POST['fecha_inicio']); ?>" name="fecha_inicio" required /><br>

		      	<label for="fecha_fin">Fecha fin: <span><em>(requerido)</em></span></label>
		      	<input type="date" value = "<?php if (isset($_POST['fecha_fin'])) echo htmlspecialchars($_POST['fecha_fin']); ?>" name="fecha_fin" required /><br>

		      	<label for="hora_inicio">Hora inicio: <span><em>(FORMATO: HH:MM)</em></span></label>
		      	<input type="text" name="hora_inicio" value="<?php if (isset($_POST['hora_inicio'])) echo htmlspecialchars($_POST['hora_inicio']); ?>" required /><br>

		      	<label for="hora_fin">Hora fin: <span><em>(FORMATO: HH:MM)</em></span></label>
		      	<input type="text" name="hora_fin" value="<?php if (isset($_POST['hora_fin'])) echo htmlspecialchars($_POST['hora_fin']); ?>" required /><br>

		      	<input type="radio" name="type" value="intern" checked>Evento interno<br>
				<input type="radio" name="type" value="extern">Evento externo<br>

		      	<label for="lugar_extern">Lugar: (si el evento es externo)</label>
		      	<input type="text" name="lugar_extern" value="<?php if (isset($_POST['lugar_extern'])) echo htmlspecialchars($_POST['lugar_extern']); ?>" /><br>

		      	<label for="lugar_intern">Lugar: (si el evento es interno)</label><br>
		      	<?php echo $selectSalas; ?>

		      	<input name="submit" type="submit" value="Crear evento" />
	    	</form>
		</main>
	</body>
</html>