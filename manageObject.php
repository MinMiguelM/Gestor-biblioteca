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

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(empty($_POST['type']))
			$type = 'equipo';
		else
			$type = $_POST['type'];
	}

	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if(empty($_GET['type']))
			$type = 'equipo';
		else
			$type = $_GET['type'];
	}

	if($type == 'equipo'){

		formDevice();

		tableDevice();

		$continue = true;
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if($_FILES['image']['error'] > 0){
				$errorGeneralForm = "Error: ".$_FILES['image']['error'];
				$continue = false;
			}

			if($continue){
				$nombre = filter($_POST['nombre']);
				$fabricante = filter($_POST['fabricante']);
				$num_serie = filter($_POST['num_serie']);
				$cantidad = $_POST['cantidad'];
				$image = $_FILES['image']['name'];
				$sql = "insert into equipo (nombre,fabricante,num_serie,imagen,total,disponibles) values ('$nombre','$fabricante','$num_serie','$image','$cantidad','$cantidad')";

				/*$error = false;
				for($i = 1;$i <= $cantidad;$i++){
					$resultInsert = query($sql,2);
					if(!$resultInsert->success){
						$error = true;
						break;
					}
				}*/

				$resultInsert = query($sql,2);
				if(!$resultInsert->success){
					$errorGeneralForm = "Error en la creacion.".$resultInsert->result;
				}else{
					$target_dir = "files/";
					$target_file = $target_dir . basename($image);
					move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
					messageAlert("$type creado");
					tableDevice();
				}
			}
		}
	}

	if($type == 'libro'){
		formBook();

		tableBook();

		$continue = true;
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if($_FILES['image']['error'] > 0){
				$errorGeneralForm = "Error: ".$_FILES['image']['error'];
				$continue = false;
			}

			if($continue){
				$nombre = filter($_POST['nombre']);
				$autor = filter($_POST['autor']);
				$edicion = filter($_POST['edicion']);
				$editorial = filter($_POST['editorial']);
				$isbn = filter($_POST['isbn']);
				$cantidad = $_POST['cantidad'];
				$paginas = $_POST['paginas'];
				$image = $_FILES['image']['name'];
				$sql = "insert into libro (titulo,autor,edicion,editorial,paginas,isbn,imagen,total,disponibles) values ('$nombre','$autor','$edicion','$editorial','$paginas','$isbn','$image','$cantidad','$cantidad')";

				/*$error = false;
				for($i = 1;$i <= $cantidad;$i++){
					$resultInsert = query($sql,2);
					if(!$resultInsert->success){
						$error = true;
						break;
					}
				}*/

				$resultInsert = query($sql,2);
				if(!$resultInsert->success){
					$errorGeneralForm = "Error en la creacion.".$resultInsert->result;
				}else{
					$target_dir = "files/";
					$target_file = $target_dir . basename($image);
					move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
					messageAlert("$type creado");
					tableBook();
				}
			}
		}

	}

	function tableDevice(){
		global $listObject;
		$listObject = '<table id="list">';
		$listObject .= '<tr>';
		$listObject .= '<th>Nombre</th>';
		$listObject .= '<th>Disponibles</th>';
		$listObject .= '<th>Numero de serie</th>';
		$listObject .= '<th>Fabricante</th>';
		$listObject .= '</tr>';

		$sql = "select * from equipo";

		$result = query($sql,1);
		if($result->success){
			foreach($result->result as $row){
				$listObject .= '<tr>';
				$listObject .= '<td><a href="detailDevice.php?idequipo='.$row['idequipo'].'">'.$row['nombre'].'</td></a>';
				$listObject .= '<td>'.$row['idequipo'].'</td>';
				$listObject .= '<td>'.$row['num_serie'].'</td>';
				$listObject .= '<td>'.$row['fabricante'].'</td>';
				$listObject .= '</tr>';
			}
		}else{
			$errorGeneralTable = "Error general.";
		}
		$listObject .= '</table>';
	}

	function tableBook(){
		global $listObject;
		$listObject = '<table id="list">';
		$listObject .= '<tr>';
		$listObject .= '<th>Titulo</th>';
		$listObject .= '<th>Autor</th>';
		$listObject .= '<th>Edicion</th>';
		$listObject .= '<th>Editorial</th>';
		$listObject .= '<th>Paginas</th>';
		$listObject .= '<th>ISBN</th>';
		$listObject .= '</tr>';

		$errorGeneralForm=$errorGeneralTable="";
		$sql = "select * from libro";

		$result = query($sql,1);
		if($result->success){
			foreach($result->result as $row){
				$listObject .= '<tr>';
				$listObject .= '<td><a href="detailBook.php?idlibro='.$row['idlibro'].'">'.$row['titulo'].'</td></a>';
				$listObject .= '<td>'.$row['autor'].'</td>';
				$listObject .= '<td>'.$row['edicion'].'</td>';
				$listObject .= '<td>'.$row['editorial'].'</td>';
				$listObject .= '<td>'.$row['paginas'].'</td>';
				$listObject .= '<td>'.$row['ISBN'].'</td>';
				$listObject .= '</tr>';
			}
		}else{
			$errorGeneralTable = "Error general.";
		}
		$listObject .= '</table>';
	}

	function formDevice(){
		global $formObject; 
		$formObject = '<label for="nombre">Nombre: <span><em>(requerido)</em></span></label>
		<input type="text" name="nombre" required /><br>

		<label for="fabricante">Fabricante: <span><em>(requerido)</em></span></label>
		<input type="text" name="fabricante" required /><br>

		<label for="num_serie">Numero de serie: <span><em>(requerido)</em></span></label>
		<input type="text" name="num_serie" required /><br>';
	}

	function formBook(){
		global $formObject; 
		$formObject = '<label for="nombre">Titulo: <span><em>(requerido)</em></span></label>
		<input type="text" name="nombre" required /><br>

		<label for="autor">Autor: <span><em>(requerido)</em></span></label>
		<input type="text" name="autor" required /><br>

		<label for="edicion">Edicion: <span><em>(requerido)</em></span></label>
		<input type="text" name="edicion" required /><br>

		<label for="editorial">Editorial: <span><em>(requerido)</em></span></label>
		<input type="text" name="editorial" required /><br>

		<label for="paginas">Paginas: <span><em>(requerido)</em></span></label>
		<input type="number" name="paginas" required /><br>

		<label for="isbn">ISBN: <span><em>(requerido)</em></span></label>
		<input type="text" name="isbn" required /><br>';


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
			<form action="manageObject.php" method="GET">
				<select name="type">
					<option value="equipo" >Equipo</option>
					<option value="libro" >Libro</option>
				</select>
				<input type="submit" value="Cargar" />
			</form>

			<h2>Crear <?php echo $type; ?> </h2>
			<form action="manageObject.php" method="POST" enctype='multipart/form-data'>
				<label class="error"><?php echo $errorGeneralForm; ?></label><br>

				<input hidden name="type" value="<?php if (isset($_GET['type'])) echo htmlspecialchars($_GET['type']); ?>" />

				<?php echo $formObject; ?>

		      	<label for="image">Imagen: <span><em>(requerido)</em></span></label>
		      	<input type="file" accept="image/*" name="image" required /><br>

		      	<label for="cantidad">Cantidad a insertar: <span><em>(requerido)</em></span></label>
		      	<input type="number" max=50 min=1 name="cantidad" value="<?php if (isset($_POST['cantidad'])) echo htmlspecialchars($_POST['cantidad']); ?>" required /><br>

		      	<input name="submit" type="submit" value="Crear" />
			</form>

			<h2>Lista de <?php echo $type.'s'; ?> </h2>
			<?php echo $listObject; ?>
		</main>
	</body>
</html>