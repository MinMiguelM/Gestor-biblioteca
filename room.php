<?php
	session_start();
	if(!isset($_SESSION['idusuario'])){
		header("Location: signin.php");
    	exit();
	}

	include_once dirname(__FILE__) . '/utils/util.php';

	$errorGeneral = '';

	$listObject = '<table id="list">';
	$listObject .= '<tr>';
	$listObject .= '<th>Nombre</th>';
	$listObject .= '</tr>';

	$sql = "select * from sala where disponible = 1";

	$result = query($sql,1);
	if($result->success){
		foreach($result->result as $row){
			$listObject .= '<tr>';
			$listObject .= '<td><a href="detailRoom.php?idsala='.$row['idsala'].'">'.$row['nombre'].'</td></a>';
			$listObject .= '</tr>';
		}
	}
	$listObject .= '</table>';

?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Salas - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Salas</h2>
			<label class="error"><?php echo $errorGeneral; ?></label><br>
			<?php echo $listObject; ?>
		</main>
	</body>
</html>