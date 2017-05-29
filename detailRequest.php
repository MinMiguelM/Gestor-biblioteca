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
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if(!isset($_GET['idequipo']) && !isset($_GET['type']) && !isset($_GET['idobjeto'])){
			header("Location: manageRequest.php");
    		exit();
		}else{
			$idsolicitud = filter($_GET['idsolicitud']);
			$type = filter($_GET['type']);
			$idobjeto = filter($_GET['idobjeto']);
			$sql = "select s.*,u.nombre_usuario from solicitud s join usuario u where s.idsolicitud = $idsolicitud";
			$result = query($sql,1);
			if($result->success){
				$row = $result->result->fetch_assoc();
				$fecha_inicio = $row['fecha_inicial'];
				$nombre_usuario = $row['nombre_usuario'];
				switch($type){
					case 1:
						$sql2 = "select nombre from equipo where idequipo = $idobjeto";
						break;
					case 2:
						$sql2 = "select nombre from sala where idsala = $idobjeto";
						break;
					case 3:
						$sql2 = "select titulo as nombre from libro where idlibro = $idobjeto";
						break;
					default:
						break;
				}
				$result2 = query($sql2,1);
				if($result2->success){
					$row2 = $result2->result->fetch_assoc();
					$elemento = $row2['nombre'];
				}else{
					$errorGeneral = 'Error general. '.$result2->result.' '.$result2->errno;	
				}
			}else{
				$errorGeneral = 'Error general. '.$result->result.' '.$result->errno;
			}
		}
	}

	$denegacionPart = $errorGeneralDenegacion = '';
	$continue = true;
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if($_POST['submit'] == 'Aprobar'){
			if(empty($_POST['fecha_final'])){
				$continue = false;
				$errorGeneral = "Es necesario llnar todos los campos.";
			}
			if(empty($_POST['report'])){
				$continue = false;
				$errorGeneral = "Es necesario llnar todos los campos.";
			}
			if($continue){
				$fecha_inicial = $_POST['fecha_inicio'];
				$idsolicitud = $_POST['idsolicitud'];
				$fecha_final = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$2-$1 $4', $_POST['fecha_final']).' 00:00:00';
				$report = $_POST['report'];
				$type = $_POST['type'];
				$idobjeto = $_POST['idobjeto'];
				switch($type){
					case 1:
						$sql2 = "update equipo set disponibles = disponibles - 1 where idequipo = $idobjeto and disponibles > 0";
						break;
					case 2:
						$sql2 = "update sala set disponibles = disponibles - 1 where idsala = $idobjeto and disponibles > 0";
						break;
					case 3:
						$sql2 = "update libro set disponibles = disponibles - 1 where idlibro = $idobjeto and disponibles > 0";
						break;
					default:
						break;
				}
				$result2 =query($sql2,3);
				if($result2->success && $result2->num_rows > 0){
					$sql = "update solicitud set estado = 'aprobado',fecha_vencimiento='$fecha_final',reporte = $report where idsolicitud = $idsolicitud";
					$result = query($sql,3);
					if($result->success){
						messageAndRedirectAlert('La solicitud ha sido aceptada.','manageRequest.php');
					}else{
						$errorGeneral = "Error general. ".$result->result.' '.$result->errno;
					}
				}else{
					if($result2->num_rows == 0)
						$errorGeneral = "No hay copias disponibles para este elemento. ";
					else
						$errorGeneral = "Error general. ".$result2->result.' '.$result2->errno;
				}
			}
		}
		if($_POST['submit'] == 'Denegar'){
			$idsolicitud = filter($_POST['idsolicitud']);
			$denegacionPart = '<h2> Denegacion </h2>
			<form action="detailRequest.php" method="POST">
				<label class="error"><?php echo $errorGeneralDenegacion; ?></label><br>

				<input hidden name="idsolicitud" value="'.$idsolicitud.'" />

				<label for="comment">Comentario: </label>
				<textarea name="comment" required></textarea><br>
				<input name="submit" type="submit" value="Denegar solicitud" />
			</form>';
		}
		if($_POST['submit'] == 'Denegar solicitud'){
			$idsolicitud = filter($_POST['idsolicitud']);
			$sql = "update solicitud set estado='denegado' where idsolicitud = $idsolicitud";
			$result = query($sql,1);
			if($result->success){
				messageAndRedirectAlert('La solicitud ha sido denegada','manageRequest.php');
			}else{
				$errorGeneralDenegacion = "Error general. ".$result->result.' '.$result->errno;
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Centro de aprobaciones - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Centro de aprobacion</h2>
			<form action="detailRequest.php" method="POST">
				<label class="error"><?php echo $errorGeneral; ?></label><br>

				<input hidden name="idsolicitud" value="<?php if (isset($_POST['idsolicitud'])) echo htmlspecialchars($_POST['idsolicitud']); else echo $idsolicitud; ?>" />
				<input hidden name="type" value="<?php if (isset($_POST['type'])) echo htmlspecialchars($_POST['type']); else echo $type; ?>" />
				<input hidden name="idobjeto" value="<?php if (isset($_POST['idobjeto'])) echo htmlspecialchars($_POST['idobjeto']); else echo $idobjeto; ?>" />

				<label for="fecha_inicio">Fecha inico prestamo: </label>
				<input type="text" readonly="true" name="fecha_inicio" value = "<?php if (isset($_POST['fecha_inicio'])) echo htmlspecialchars($_POST['fecha_inicio']); else echo $fecha_inicio; ?>" /><br>

				<label for="nombre_usuario">Nombre de usuario: </label>
				<input type="text" readonly="true" name="nombre_usuario" value = "<?php if (isset($_POST['nombre_usuario'])) echo htmlspecialchars($_POST['nombre_usuario']); else echo $nombre_usuario; ?>" /><br>

				<label for="elemento">Elemento: </label>
				<input type="text" readonly="true" name="elemento" value = "<?php if (isset($_POST['elemento'])) echo htmlspecialchars($_POST['elemento']); else echo $elemento; ?>" /><br>

				<label for="fecha_final">Fecha final prestamo: <span><em>(requerido)</em></span></label>
				<input type="date" name="fecha_final" value = "<?php if (isset($_POST['fecha_final'])) echo htmlspecialchars($_POST['fecha_final']); ?>" /><br>

				<label for="report">Reporte cada: <span><em>(dias)</em></span></label>
				<input type="number" name="report" min=1 value = "<?php if (isset($_POST['report'])) echo htmlspecialchars($_POST['report']); ?>" /><br>

		      	<input name="submit" type="submit" value="Aprobar" />
		      	<input name="submit" type="submit" value="Denegar" />
			</form>

			<?php echo $denegacionPart; ?>
		</main>
	</body>
</html>