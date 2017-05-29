<?php
	session_start();
	if(isset($_SESSION['idusuario'])){
		if($_SESSION['rol'] == 'admin'){
			header("Location: admin.php");
    		exit();
		}

		if($_SESSION['rol'] == 'user'){
			header("Location: device.php");
    		exit();
		}		
	}else{
		header("Location: signin.php");
    	exit();
	}
?>
