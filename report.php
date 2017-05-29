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

	$errorGeneral = $formReport = '';
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		$continue = true;
		$nombre_usuario = filter($_GET['search']);
		$type = filter($_GET['type']);
		$sql = "select * from usuario where nombre_usuario = '$nombre_usuario'";
		$result = query($sql,1);
		if($result->success && $result->num_rows > 0){
			$elementos = array();
			$row = $result->result->fetch_assoc();
			$idusuario = $row['idusuario'];
			if($type == 'libro'){
				$sql = "select u.nombre_usuario as nombre_usuario,s.idsolicitud as idsolicitud, s.idlibro as idobjeto from solicitud s, usuario u where u.idusuario = s.idusuario and u.idusuario = $idusuario and s.estado = 'aprobado' and s.estado_objeto <> 'devuelto' and s.idlibro IS NOT NULL";
				$result2 = query($sql,1);
				if($result2->success){
					if($result2->num_rows > 0){
						foreach ($result2->result as $libro) {
							$idlibro = $libro['idobjeto'];
							$sql2 = "select titulo from libro where idlibro = $idlibro";
							$nombre = query($sql2,1);
							if(!$nombre->success){
								$errorGeneral = "Error general. ".$nombre->result.' '.$nombre->errno;
								$continue = false;
								break;
							}
							$nombre = $nombre->result->fetch_assoc();
							$item = new ObjectReport();
							$item->nombre = $nombre['titulo'];
							$item->idsolicitud = $libro['idsolicitud'];
							array_push($elementos,$item);
						}
					}else{
						$formReport = 'No hay elementos asociados a este usuario.';
						$continue = false;
					}
				}else{
					$errorGeneral = "Error general. ".$result2->result.' '.$result2->errno;
					$continue = false;
				}
			}else{
				$sql = "select u.nombre_usuario as nombre_usuario,s.idsolicitud as idsolicitud, s.idequipo as idobjeto from solicitud s, usuario u where u.idusuario = s.idusuario and u.idusuario = $idusuario and s.estado = 'aprobado' and s.estado_objeto <> 'devuelto' and s.idequipo IS NOT NULL";
				$result2 = query($sql,1);
				if($result2->success){
					if($result2->num_rows > 0){
						foreach ($result2->result as $equipo) {
							$idequipo = $equipo['idobjeto'];
							$sql2 = "select nombre from equipo where idequipo = $idequipo";
							$nombre = query($sql2,1);
							if(!$nombre->success){
								$errorGeneral = "Error general. ".$nombre->result.' '.$nombre->errno;
								$continue = false;
								break;
							}
							$nombre = $nombre->result->fetch_assoc();
							$item = new ObjectReport();
							$item->nombre = $nombre['nombre'];
							$item->idsolicitud = $equipo['idsolicitud'];
							array_push($elementos,$item);
						}
					}else{
						$formReport = 'No hay elementos asociados a este usuario.';
						$continue = false;
					}
				}else{
					$errorGeneral = "Error general. ".$result2->result.' '.$result2->errno;
					$continue = false;
				}
			}

			if($continue)
				fillFormReport($elementos,$nombre_usuario);
		}else{
			$errorGeneral = "Usuario no encontrado.";
		}
	}

	$errorGeneralReport = '';
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$idsolicitud = $_POST['elemento'];
		$estado = $_POST['estado'];
		$comment = $_POST['comment'];
		$sql = "select * from solicitud where idsolicitud = $idsolicitud";
		$result = query($sql,1);
		if($result->success){
			$type ='';
			$idobjeto = '';
			$row = $result->result->fetch_assoc();
			if($row['idlibro'] != null){
				$type = 'libro';
				$idobjeto = $row['idlibro'];
			}else{
				$type = 'equipo';
				$idobjeto = $row['idequipo'];
			}
			$estado_objeto = $row['estado_objeto'];
			if($estado == 'devuelto' && $estado_objeto == 'danado'){
				$errorGeneral = 'No se puede generar reporte para ese elemento, ya que no puede pasar de un estado de deterioro a devuelto.';
			}else{
				$sql = "update solicitud set estado_objeto = '$estado' where idsolicitud = $idsolicitud";
				$sql2 = "insert into reporte(estado,comentarios,idsolicitud) values ('$estado','$comment',$idsolicitud)";
				query($sql,3);
				query($sql2,4);
				if($estado == 'devuelto'){
					if($type == 'libro')
						$sql = "update libro set disponibles = disponibles + 1 where idlibro = $idobjeto";
					else
						$sql = "update equipo set disponibles = disponibles + 1 where idequipo = $idobjeto";
					query($sql,3);
				}
				messageAndRedirectAlert('Reporte generado.','index.php');
			}
		}else{
			$errorGeneral = 'Error general. '.$result->result.' '.$result->errno;
		}
	}

	function fillFormReport($elements,$nombre_usuario){
		global $formReport;
		$formReport = '<label class="error"><?php echo $errorGeneralReport; ?></label><br>
		<form action="report.php" method = "POST">
			<label for="nombre_usuario">Nombre de usuario: </label>
			<input readonly="true" type="text" name="nombre_usuario" value="'.$nombre_usuario.'" required/><br>

			<label for="elemento">Elemento: </label>
			<select name="elemento">';
		foreach ($elements as $key) {
			$formReport .= '<option value="'.$key->idsolicitud.'">'.$key->nombre.'</option>';
		}
		$formReport .= '</select><br>

			<label for="estado">Estado: </label>
			<select name="estado" >
				<option value="excelente">Excelente</option>
				<option value="bueno">Bueno</option>
				<option value="regular">Regular</option>
				<option value="danado">Deteriorado</option>
				<option value="devuelto">Devuelto</option>
			</select><br>

			<label for="comentario">Comentarios: </label>
			<textarea name="comment" required></textarea><br>
			<input name="submit" type="submit" value="Registrar Reporte" />
		</form>';


	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Reportes - Biblioteca</title>
	</head>
	<body>
		<nav>
			<?php include 'static/header.php'; ?>
		</nav>
		<main>
			<label class="error"><?php echo $errorGeneral; ?></label><br>
			<form action="report.php" method = "GET">
				<input type="text" class="search" name="search" placeholder="Nombre de usuario..." required/>
				<select name="type">
					<option value="libro">Libro</option>
					<option value="equipo">Equipo</option>
				</select>
				<input type="submit" name="submit" value="Buscar" />
			</form>

			<?php echo $formReport; ?>
		</main>
	</body>
</html>