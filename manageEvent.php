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

	$errorGeneral = '';
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$date = $_POST['fecha'];
		$nombre = filter($_POST['nombre']);
		$lugar = filter($_POST['lugar']);
		$hora = filter($_POST['hora']);
		$date .= ' '.$hora.':00';
		$sql = "insert into evento (nombre,fecha,hora,lugar) values ('$nombre','$date','$hora','$lugar')";
		$result = query($sql,2);
		if($result->success){
			messageAndRedirectAlert('Evento creado','admin.php');
		}else{
			$errorGeneral = 'Error general. '.$result->result.' '.$result->errno;
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

				<label for="fecha">Fecha: <span><em>(requerido)</em></span></label>
		      	<input type="date" value = "<?php if (isset($_POST['fecha'])) echo htmlspecialchars($_POST['fecha']); ?>" name="fecha" required /><br>

		      	<label for="lugar">Lugar: <span><em>(requerido)</em></span></label>
		      	<input type="text" name="lugar" value="<?php if (isset($_POST['lugar'])) echo htmlspecialchars($_POST['lugar']); ?>" required /><br>

		      	<label for="hora">Hora: <span><em>(FORMATO: HH:MM)</em></span></label>
		      	<input type="text" name="hora" value="<?php if (isset($_POST['hora'])) echo htmlspecialchars($_POST['hora']); ?>" required /><br>

		      	<input name="submit" type="submit" value="Crear evento" />
	    	</form>
		</main>
	</body>
</html>