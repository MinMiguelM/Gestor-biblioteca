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

	$listObject = '<table id="list">';
	$listObject .= '<tr>';
	$listObject .= '<th>Fecha inicio Prestamo</th>';
	$listObject .= '<th>Nombre usuario</th>';
	$listObject .= '<th>Elemento</th>';
	$listObject .= '</tr>';
	$sql = "select s.idsolicitud as idsolicitud, s.fecha_inicial as fecha_inicial, u.nombre_usuario as nombre_usuario, s.idequipo as idequipo, s.idsala as idsala,s.idlibro as idlibro from solicitud s, usuario u where s.estado = 'espera' and s.idusuario = u.idusuario order by s.fecha_inicial desc";
	$result = query($sql,1);
	if($result->success){
		foreach($result->result as $row){
			$nombre = '';
			if(!empty($row['idequipo'])){
				$idequipo = $row['idequipo'];
				$type=1;
				$idobjeto = $idequipo;
				$sql2 = "select nombre from equipo where idequipo = $idequipo";
			}
			if(!empty($row['idsala'])){
				$idsala = $row['idsala'];
				$type=2;
				$idobjeto = $idsala;
				$sql2 = "select nombre from sala where idsala = $idsala";
			}
			if(!empty($row['idlibro'])){
				$idlibro = $row['idlibro'];
				$type=3;
				$idobjeto = $idlibro;
				$sql2 = "select titulo as nombre from libro where idlibro = $idlibro";
			}
			$select = query($sql2,1);
			if($select->success){
				$nombre = $select->result->fetch_assoc();
				$listObject .= '<tr>';
				$listObject .= '<td><a href="detailRequest.php?idsolicitud='.$row['idsolicitud'].'&type='.$type.'&idobjeto='.$idobjeto.'">'.$row['fecha_inicial'].'</td></a>';
				$listObject .= '<td>'.$row['nombre_usuario'].'</td>';
				$listObject .= '<td>'.$nombre['nombre'].'</td>';
				$listObject .= '</tr>';
			}
		}
	}else
		$errorGeneral = 'Erro general.'.$result->result.' '.$result->errno;
	$listObject .= '</table>';

?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Centro de mensajes - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<h2>Centro de mensajes</h2>
			<label class="error"><?php echo $errorGeneral; ?></label><br>
			<?php echo $listObject; ?>
		</main>
	</body>
</html>