<?php
	session_start();
	$nav .= '<li><a href="event.php">Eventos</a></li>';
	$nav .= '<li><a href="room.php">Salas</a></li>';
	$nav .= '<li><a href="device.php">Equipos</a></li>';
	$nav .= '<li><a href="book.php">Libros</a></li>';
	if($_SESSION['rol'] == 'admin'){
		$nav .= '<li><a href="admin.php">Administracion</a></li>';
	}
	$nav .= '<li><a href="logout.php">Cerrar sesion</a></li>';
?>
<!DOCTYPE html>
<html>
	<body>
		<ul>
			<?php echo $nav; ?>
		</ul>
	</body>
</html>