<?php
	session_start();
	if(!isset($_SESSION['idusuario'])){
		header("Location: signin.php"); 
    	exit();
	}


	include_once dirname(__FILE__) . '/utils/util.php';

	$formSearch = $formSearch = '';
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

		$formSearch='<input type="text" placeholder="Nombre..." name = "xnombre"/>
				<input type="number" placeholder="Disponibles..." name = "xdisponibles"/>
				<input type="text" placeholder="Fabricante..." name = "xfabricante"/>
				<input type="submit" value="Buscar" name ="buscar"/>';
		$where="";
			if (isset($_POST['buscar']))
			{

			if (!empty($_POST['xnombre']))
			{
				if(empty($where))
				{
					$where="where nombre like'%".$_POST['xnombre']."%'";	
				}else
				{
					$where.=" and nombre like'%".$_POST['xnombre']."%'";
				}
			}
			if (!empty($_POST['xfabricante']))
			{
				if(empty($where))
				{
					$where="where fabricante like'%".$_POST['xfabricante']."%'";	
				}else
				{
					$where.=" and fabricante like'%".$_POST['xfabricante']."%'";
				}
			}
			if (!empty($_POST['xdisponibles']))
			{
				if(empty($where))
				{
					$where="where disponibles >= ".$_POST['xdisponibles'];	
				}else
				{
					$where.=" and disponibles >= ".$_POST['xdisponibles'];	
				}
			}
		}
		$sql = "select * from equipo $where";
		$result = query($sql,1);
		$listObject = "";
		$result = query($sql,1);
		if($result->success){
			foreach($result->result as $row){
				$idequipo = $row['idequipo'];
				$listObject .= '<div class="objects">';
				$listObject .= '<img width="200px" height="200px" src="files/'.$row['imagen'].'"/>';
				$listObject .= '<br><br><label>'.$row['nombre'].'</label><br>';
				$listObject .= '<label>Fabricante: '.$row['fabricante'].'</label><br>';
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
			$errorGeneralForm = "Error general. ".$result->result.' '.$result->errno;
		}
	}

	if($type == 'libro'){

		$formSearch ='<input type="text" placeholder="Titulo..." name = "xtitulo"/>
				<input type="number" placeholder="Disponibles..." name = "xdisponibles"/>
				<input type="text" placeholder="Autor..." name = "xautor"/>
				<input type="text" placeholder="Editorial..." name = "xeditorial"/>
				<input type="submit" value="Buscar" name ="buscar"/>';
		$where="";
		if (isset($_POST['buscar']))
		{

			if (!empty($_POST['xautor']))
			{
				if(empty($where))
				{
					$where="where autor like'%".$_POST['xautor']."%'";	
				}else
				{
					$where.=" and autor like'%".$_POST['xautor']."%'";
				}
			}
			if (!empty($_POST['xtitulo']))
			{
				if(empty($where))
				{
					$where="where titulo like'%".$_POST['xtitulo']."%'";	
				}else
				{
					$where.=" and titulo like'%".$_POST['xtitulo']."%'";
				}
			}
			if (!empty($_POST['xdisponibles']))
			{
				if(empty($where))
				{
					$where="where disponibles >= ".$_POST['xdisponibles'];	
				}else
				{
					$where.=" and disponibles >= ".$_POST['xdisponibles'];	
				}
			}
			if (!empty($_POST['xeditorial']))
			{
				if(empty($where))
				{
					$where="where editorial like'%".$_POST['xeditorial']."%'";	
				}else
				{
					$where.=" and editorial like'%".$_POST['xeditorial']."%'";
				}
			}
		}
		$sql = "select * from libro $where";
		$result = query($sql,1);
		$listObject = "";
		if($result->success){
			foreach($result->result as $row){
				$idlibro = $row['idlibro'];
				$listObject .= '<div class="objects">';
				$listObject .= '<img width="200px" height="200px" src="files/'.$row['imagen'].'"/>';
				$listObject .= '<br><br><label>'.$row['titulo'].'</label>';
				$listObject .= '<br><label>'.$row['autor'].'</label><br>';
				$listObject .= '<label>Editorial: '.$row['editorial'].'</label><br>';
				if($row['disponibles'] == 0){
					$sql2 = "select fecha_vencimiento from solicitud where idlibro = $idlibro and estado = 'aprobado' and estado_objeto<>'devuelto' and estado_objeto<>'danado' order by fecha_vencimiento asc limit 1";
					$result2 = query($sql2,1);
					if($result2->success){
						$row2 = $result2->result->fetch_assoc();
						$fecha_vencimiento = $row2['fecha_vencimiento'];
					}else{
						$listObject = '';
						$errorGeneral = "Error general. ".$result2->result.' '.$result2->errno;
						break;
					}
					$listObject .= '<label>Se encontrara disponible en: '.$fecha_vencimiento.'</label><br><br>';
					$listObject .= '<button disabled>Solicitar</button>';
				}else{
					$listObject .= '<label>Disponibles: '.$row['disponibles'].'</label><br><br>';
					$listObject .= '<a href="requestBook.php?idlibro='.$idlibro.'"><button>Solicitar</button></a>';
				}
				$listObject .= '</div>';
			}
		}else{
			$errorGeneralForm = "Error general. ".$result->result.' '.$result->errno;
		}

	}


?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Busqueda - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<form action="manageSearch.php" method="GET">
				<select name="type">
					<option value="equipo" >Equipo</option>
					<option value="libro" >Libro</option>
				</select>
				<input type="submit" value="Cargar" />
			</form>

			<h2>Buscar <?php echo $type; ?> </h2>
			<form action="manageSearch.php" method="POST" enctype='multipart/form-data'>
				<label class="error"><?php echo $errorGeneralForm; ?></label><br>

				<input hidden name="type" value="<?php if (isset($_GET['type'])) echo htmlspecialchars($_GET['type']); ?>" />

				<?php echo $formSearch; ?>
			</form>
			<?php echo $listObject; ?>
		</main>
	</body>
</html>