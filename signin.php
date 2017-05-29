<?php
	session_start();
	if(isset($_SESSION['idusuario'])){
		header("Location: index.php");
    	exit();
	}

	include_once dirname(__FILE__) . '/utils/util.php';

	$errorName = $errorPassword = $errorGeneral = "";
	$continue = true;

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(empty($_POST["nombre"])){
			$errorName = "Nombre de usuario requerido";
			$continue = false;
        }
        if(empty($_POST["password"])){
        	$errorPassword = "Password es requerida";
        	$continue = false;
        }

        if($continue){
        	$nombre = filter($_POST['nombre']);
        	$password = filter($_POST['password']);
        	$sql = "select * from usuario where nombre_usuario = '$nombre' and password = '$password'";
        	$result = query($sql,1);
            if($result->success){
            	if($result->num_rows == 1){
            		$row = $result->result->fetch_assoc();
            		$_SESSION['idusuario']=$row['idusuario'];
            		$_SESSION['rol']=$row['rol'];
                    $_SESSION['email']=$row['email'];
            		header("Location: index.php");
            		exit();
            	}else{
            		$errorGeneral = "No se encuentra usuarios con esas credenciales.";
            	}
            }else{
            	$errorGeneral = "Error de comunicación. ".$result->result;
            }

        }
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Ingreso - Biblioteca</title>
	</head>
	<body>
        <h2>Ingreso</h2>
		<form action="signin.php" method="POST">
			<label class="error"><?php echo $errorGeneral; ?></label><br>
        	Nombre de usuario: <input type="text" name="nombre" value="<?php if (isset($_POST['nombre'])) echo htmlspecialchars($_POST['nombre']); ?>" required /> <br>
        	Password: <input type="password" name="password" value = "<?php if (isset($_POST['password'])) echo htmlspecialchars($_POST['password']); ?>" required /> <br>
        	<br><input type="submit" value="Ingresar"> <a href="signup.php"><input type="button" value="Registrarse"></a>
    	</form>
	</body>
</html>