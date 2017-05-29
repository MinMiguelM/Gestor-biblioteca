<?php
	session_start();
	if(isset($_SESSION['idusuario'])){
	    unset($_SESSION['idusuario']);
	    unset($_SESSION['rol']);
	}
	header("Location: index.php");
	exit();
?>