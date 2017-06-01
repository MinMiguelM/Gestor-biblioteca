<?php
	session_start();
	if(!isset($_SESSION['idusuario'])){
		header("Location: signin.php");
    	exit();
	}

	include_once dirname(__FILE__) . '/utils/util.php';

	$list = $errorGeneral = '';
	$sql = "select * from equipo";
	$result = query($sql,1);
	if($result->success){
		foreach($result->result as $row){
			$idequipo = $row['idequipo'];
			$listObject .= '<div class="objects">';
			$listObject .= '<img width="200px" height="200px" src="files/'.$row['imagen'].'"/>';
			$listObject .= '<br><br><label>'.$row['nombre'].'</label><br>';
			if($row['disponibles'] == 0){
				$sql2 = "select fecha_vencimiento from solicitud where idequipo = $idequipo and estado = 'aprobado' and estado_objeto<>'devuelto' and estado_objeto<>'danado' order by fecha_vencimiento asc limit 1";
				$result2 = query($sql2,1);
				if($result2->success){
					$row2 = $result2->result->fetch_assoc();
					$fecha_vencimiento = $row2['fecha_vencimiento'];
				}else{
					$listObject = '';
					$errorGeneral = "Error general. ".$result2->result.' '.$result2->errno;
					break;
				}
				$date = strtotime($fecha_vencimiento);
				$date = date("d/m/Y",$date);
				$listObject .= '<label>Se encontrara disponible en: '.$date.'</label><br><br>';
				$listObject .= '<button disabled>Solicitar</button>';
			}else{
				$listObject .= '<label>Disponibles: '.$row['disponibles'].'</label><br><br>';
				$listObject .= '<a href="requestDevice.php?idequipo='.$idequipo.'"><button>Solicitar</button></a>';
			}
			$listObject .= '</div>';
		}
	}else{
		$errorGeneral = "Error general. ".$result->result.' '.$result->errno;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Equipos - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Equipos</h2>
			<label class="error"><?php echo $errorGeneral; ?></label><br>
			<?php echo $listObject; ?>
		</main>
	</body>
</html>