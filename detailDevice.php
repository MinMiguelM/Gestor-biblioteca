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

	$errorGeneralForm = '';
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if(!isset($_GET['idequipo'])){
			header("Location: manageObject.php");
    		exit();
		}else{
			$idequipo = filter($_GET['idequipo']);
			$sql = "select * from equipo where idequipo = $idequipo";
			$result = query($sql,1);
			if($result->success){
				$row = $result->result->fetch_assoc();
				$nombre = $row['nombre'];
				$fabricante = $row['fabricante'];
				$num_serie = $row['num_serie'];
				$total = $row['total'];
				$idequipo = $row['idequipo'];
			}
		}
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){

		if($_POST['submit'] == 'Actualizar'){
			$nombre = filter($_POST['nombre']);
			$fabricante = filter($_POST['fabricante']);
			$num_serie = filter($_POST['num_serie']);
			$total = $_POST['total'];
			$idequipo = $_POST['idequipo'];

			//validar cantidad total con respecto a los ya prestados

			$sql = "select * from solicitud where estado='aprobado' and estado_objeto <> 'devuelto' and idequipo = $idequipo";
			$result = query($sql,1);
			if($result->success){
				$num = $result->num_rows;
				if($num > $total)
					$errorGeneralForm = "La cantidad total no puede estar por debajo de los articulos que ya estan prestados.";
				else{
					$disponible = $total - $num;
					$sql = "update equipo set nombre='$nombre', fabricante='$fabricante',num_serie='$num_serie', total=$total, disponibles=$disponible where idequipo = $idequipo";

					$update = query($sql,3);
					if($update->success){
						messageAndRedirectAlert('Equipo actualizado.','manageObject.php?type=equipo');
					}else
						$errorGeneralForm = 'Error actualizando. '.$update->result.' '.$update->errno;

				}
			}else
				$errorGeneralForm = "Error general. ".$result->result.' '.$result->errno;
		}

		if($_POST['submit'] == 'Eliminar'){
			$idequipo = $_POST['idequipo'];
			$sql = "delete from equipo where idequipo = $idequipo";
			$delete = query($sql,4);
			if($delete->success){
				messageAndRedirectAlert('Equipo eliminado.','manageObject.php?type=equipo');
			}else{
				$errorGeneralForm = 'Error eliminando. '.$delete->result.' '.$delete->errno;
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Detalle equipo - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Crear <?php echo $type; ?> </h2>
			<form action="detailDevice.php" method="POST">
				<label class="error"><?php echo $errorGeneralForm; ?></label><br>

				<input hidden name="idequipo" value="<?php if (isset($_POST['idequipo'])) echo htmlspecialchars($_POST['idequipo']); else echo $idequipo; ?>" />

		      	<label for="nombre">Nombre: </label>
		      	<input type="text" name="nombre" value="<?php if (isset($_POST['nombre'])) echo htmlspecialchars($_POST['nombre']); else echo $nombre; ?>" required /><br>

		      	<label for="fabricante">Fabricante: </label>
		      	<input type="text" name="fabricante" value="<?php if (isset($_POST['fabricante'])) echo htmlspecialchars($_POST['fabricante']); else echo $fabricante; ?>" required /><br>

		      	<label for="num_serie">Numero de serie: </label>
		      	<input type="text" name="num_serie" value="<?php if (isset($_POST['num_serie'])) echo htmlspecialchars($_POST['num_serie']); else echo $num_serie; ?>" required /><br>

		      	<label for="total">Total de equipos: </label>
		      	<input type="number" name="total" value="<?php if (isset($_POST['total'])) echo htmlspecialchars($_POST['total']); else echo $total; ?>" required /><br>

		      	<input name="submit" type="submit" value="Actualizar" />
		      	<input name="submit" type="submit" value="Eliminar" />
			</form>
		</main>
	</body>
</html>