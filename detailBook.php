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
		if(!isset($_GET['idlibro'])){
			header("Location: manageObject.php");
    		exit();
		}else{
			$idlibro = filter($_GET['idlibro']);
			$sql = "select * from libro where idlibro = $idlibro";
			$result = query($sql,1);
			if($result->success){
				$row = $result->result->fetch_assoc();
				$titulo = $row['titulo'];
				$autor = $row['autor'];
				$edicion = $row['edicion'];
				$editorial = $row['editorial'];
				$paginas = $row['paginas'];
				$isbn = $row['ISBN'];
				$total = $row['total'];
				$idlibro = $row['idlibro'];
			}
		}
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){

		if($_POST['submit'] == 'Actualizar'){
			$titulo = filter($_POST['titulo']);
			$autor = filter($_POST['autor']);
			$edicion = filter($_POST['edicion']);
			$editorial = filter($_POST['editorial']);
			$paginas = $_POST['paginas'];
			$isbn = filter($_POST['isbn']);
			$total = $_POST['total'];
			$idlibro = $_POST['idlibro'];

			//validar cantidad total con respecto a los ya prestados

			$sql = "select * from solicitud where estado='aprobado' and estado_objeto <> 'devuelto' and idlibro = $idlibro";
			$result = query($sql,1);
			if($result->success){
				$num = $result->num_rows;
				if($num > $total)
					$errorGeneralForm = "La cantidad total no puede estar por debajo de los articulos que ya estan prestados.";
				else{
					$disponible = $total - $num;
					$sql = "update libro set titulo='$titulo', autor='$autor',edicion='$edicion',editorial='$editorial', paginas='$paginas', isbn='$isbn', total=$total, disponibles=$disponible where idlibro = $idlibro";

					$update = query($sql,3);
					if($update->success){
						messageAndRedirectAlert('Libro actualizado.','manageObject.php?type=libro');
					}else
						$errorGeneralForm = 'Error actualizando. '.$update->result.' '.$update->errno;

				}
			}else
				$errorGeneralForm = "Error general. ".$result->result.' '.$result->errno;
		}

		if($_POST['submit'] == 'Eliminar'){
			$idlibro = $_POST['idlibro'];
			$sql = "delete from libro where idlibro = $idlibro";
			$delete = query($sql,4);
			if($delete->success){
				messageAndRedirectAlert('Libro eliminado.','manageObject.php?type=libro');
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
		<title>Detalle libro - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Crear <?php echo $type; ?> </h2>
			<form action="detailBook.php" method="POST">
				<label class="error"><?php echo $errorGeneralForm; ?></label><br>

				<input hidden name="idlibro" value="<?php if (isset($_POST['idlibro'])) echo htmlspecialchars($_POST['idlibro']); else echo $idlibro; ?>" />

		      	<label for="titulo">Titulo: </label>
		      	<input type="text" name="titulo" value="<?php if (isset($_POST['titulo'])) echo htmlspecialchars($_POST['titulo']); else echo $titulo; ?>" required /><br>

		      	<label for="autor">Autor: </label>
		      	<input type="text" name="autor" value="<?php if (isset($_POST['autor'])) echo htmlspecialchars($_POST['autor']); else echo $autor; ?>" required /><br>

		      	<label for="edicion">Edicion: </label>
		      	<input type="text" name="edicion" value="<?php if (isset($_POST['edicion'])) echo htmlspecialchars($_POST['edicion']); else echo $edicion; ?>" required /><br>

		      	<label for="editorial">Editorial: </label>
		      	<input type="text" name="editorial" value="<?php if (isset($_POST['editorial'])) echo htmlspecialchars($_POST['editorial']); else echo $editorial; ?>" required /><br>

		      	<label for="paginas">Paginas: </label>
		      	<input type="number" name="paginas" value="<?php if (isset($_POST['paginas'])) echo htmlspecialchars($_POST['paginas']); else echo $paginas; ?>" required /><br>

		      	<label for="isbn">ISBN: </label>
		      	<input type="text" name="isbn" value="<?php if (isset($_POST['isbn'])) echo htmlspecialchars($_POST['isbn']); else echo $isbn; ?>" required /><br>

		      	<label for="total">Total de libros: </label>
		      	<input type="number" name="total" value="<?php if (isset($_POST['total'])) echo htmlspecialchars($_POST['total']); else echo $total; ?>" required /><br>

		      	<input name="submit" type="submit" value="Actualizar" />
		      	<input name="submit" type="submit" value="Eliminar" />
			</form>
		</main>
	</body>
</html>