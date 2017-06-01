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

	$formObject = $listObject = '';
	$errorGeneralForm=$errorGeneralTable="";

	tableRoom();

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$nombre = filter($_POST['nombre']);
		$sql = "insert into sala (nombre,disponible) values ('$nombre',1)";
		$resultInsert = query($sql,2);
		if(!$resultInsert->success){
			$errorGeneralForm = "Error en la creacion.".$resultInsert->result;
		}else{
			messageAlert("Sala creada");
			tableRoom();
		}
	}

	function tableRoom(){
		global $listObject;
		$listObject = '<table id="list">';
		$listObject .= '<tr>';
		$listObject .= '<th>Nombre</th>';
		$listObject .= '<th>Disponibilidad</th>';
		$listObject .= '</tr>';

		$sql = "select * from sala";

		$result = query($sql,1);
		if($result->success){
			foreach($result->result as $row){
				$listObject .= '<tr>';
				$listObject .= '<td>'.$row['nombre'].'</td></a>';
				if($row['disponible'] == 1)
					$listObject .= '<td>Disponible</td>';
				else
					$listObject .= '<td>No Disponible</td>';
				$listObject .= '</tr>';
			}
		}else{
			$errorGeneralTable = "Error general.";
		}
		$listObject .= '</table>';
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Creacion - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Crear libro </h2>
			<form action="manageRoom.php" method="POST">
				<label class="error"><?php echo $errorGeneralForm; ?></label><br>

		      	<label for="nombre">Nombre: <span><em>(requerido)</em></span></label>
		      	<input type="text" name="nombre" value="<?php if (isset($_POST['nombre'])) echo htmlspecialchars($_POST['nombre']); ?>" required /><br>

		      	<input name="submit" type="submit" value="Crear" />
			</form>

			<h2>Lista de salas </h2>
			<?php echo $listObject; ?>
		</main>
	</body>
</html>