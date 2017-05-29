<?php
	session_start();
	if(!isset($_SESSION['idusuario'])){
		header("Location: signin.php");
    	exit();
	}

	include_once dirname(__FILE__) . '/utils/util.php';

	$errorGeneral = $titulo = $autor = $editorial = '';
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if(isset($_GET['idlibro'])){
			$idlibro = filter($_GET['idlibro']);
			$sql = "select * from libro where idlibro = $idlibro";
			$result = query($sql,1);
			if($result->success){
				$row = $result->result->fetch_assoc();
				$titulo = $row['titulo'];
				$autor = $row['autor'];
				$editorial = $row['editorial'];
			}else{
				$errorGeneral = "Error general. ".$result->result;
			}
		}
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$date = $_POST['date'];
		$idlibro = $_POST['idlibro'];
		$idusuario = $_SESSION['idusuario'];
		$sql = "insert into solicitud (fecha_inicial,idusuario,idlibro) values ('$date','$idusuario','$idlibro')";
		$result = query($sql,2);
		if($result->success){
			messageAndRedirectAlert('Solicitud realizada.','book.php');
		}else{
			$errorGeneral = "Error general. ".$result->result." ".$result->errno;
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Solicitar - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Solicitud de libro</h2>
			<label class="error"> <?php echo $errorGeneral; ?> </label>
			<form action="requestBook.php" method="POST">
				<label for="idlibro">ID: </label>
				<input type="text" readonly="true" name="idlibro" value="<?php echo $idlibro; ?>" /><br>

				<label for="titulo">Titulo: </label>
		      	<input type="text" readonly="true" name="titulo" value="<?php echo $titulo; ?>" /><br>

		      	<label for="editorial">Editorial: </label>
		      	<input type="text" readonly="true" name="editorial" value="<?php echo $editorial; ?>" /><br>

		      	<label for="autor">Autor: </label>
		      	<input type="text" readonly="true" name="autor" value="<?php echo $autor; ?>" /><br>

		      	<label for="date">Fecha: </label>
		      	<input type="text" readonly="true" name="date" value="<?php echo (new DateTime())->format('Y-m-d H:i:s'); ?>" /><br>

		      	<input name="submit" type="submit" value="Solicitar" />
			</form>
		</main>
	</body>
</html>