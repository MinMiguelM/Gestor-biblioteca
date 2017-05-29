<?php
	session_start();
	if(isset($_SESSION['idusuario'])){
		header("Location: index.php");
    	exit();
	}

	include_once dirname(__FILE__) . '/utils/util.php';

	$errorGeneral = "";

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$email = $_POST['email'];
		$confirm_email = $_POST['confirm_email'];
		if($email == $confirm_email){
			$nombre = filter($_POST['nombre']);
			$password = filter($_POST['password']);
			$sql = "insert into usuario (nombre_usuario,email,password) values ('$nombre','$email','$password')";
			$result = query($sql,2);
			if($result->success){
				messageAndRedirectAlert('Usuario creado','signin.php');
    			exit();
			}else{
				$error = convertErrno($result->errno);
				if($error)
					$errorGeneral = $error;
				else
					$errorGeneral = $result->result;
			}
		}else{
			$errorGeneral = "El email no coincide con su confirmacion.";
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="static/style.css">
		<title>Registro - Biblioteca</title>
	</head>
	<body>
		<h2>Registro</h2>
		<form action="signup.php" method = "POST">
			<label class="error"><?php echo $errorGeneral; ?></label><br>

			<label for="nombre">Nombre de usuario: <span><em>(requerido)</em></span></label>
			<input type="text" name="nombre" value = "<?php if (isset($_POST['nombre'])) echo htmlspecialchars($_POST['nombre']); ?>" required /><br>

			<label for="password">Password: <span><em>(requerido)</em></span></label>
	      	<input type="password" value = "<?php if (isset($_POST['password'])) echo htmlspecialchars($_POST['password']); ?>" name="password" required /><br>

	      	<label for="email">Email: <span><em>(requerido)</em></span></label>
	      	<input type="email" name="email" value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>" required /><br>

	      	<label for="confirm_email">Confirmar Email: <span><em>(requerido)</em></span></label>
	      	<input type="email" name="confirm_email" value="<?php if (isset($_POST['confirm_email'])) echo htmlspecialchars($_POST['confirm_email']); ?>" required /><br>

	      	<input name="submit" type="submit" value="Registrarse" />
    	</form>
    </body>
</html>
