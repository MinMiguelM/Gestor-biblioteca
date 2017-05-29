<?php

	include_once dirname(__FILE__) . '/config.php';

	class ResultSet {
	    public $result;
	    public $num_rows;
	    public $success;
	    public $errno;
	}

	class ObjectReport{
		public $nombre;
		public $idsolicitud;
	}

	function filter($data){
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	/**
	 * type: SELECT: 1
	 * type: INSERT: 2
	 * type: UPDATE: 3
	 * type: DELETE: 4
	 */
	function query($sql, $type){
		$resultSet = new ResultSet();
		$con = mysqli_connect(HOST, USER_DB, PASSWORD_DB, DB);
		$result = mysqli_query($con,$sql);
		if($result){
			switch ($type) {
				case 1:
					$resultSet->result = $result;
					$resultSet->num_rows = mysqli_num_rows($result);
					$resultSet->success = true;
					break;
				case 2:
				case 3:
				case 4:
					$resultSet->num_rows = mysqli_affected_rows($con);
					$resultSet->success = true;
					break;
				default:
					break;
			}
		}else{
			$resultSet->success = false;
			$resultSet->result = mysqli_error($con);
			$resultSet->errno = mysqli_errno($con);
			mysqli_rollback($con);
		}
		mysqli_close($con);
		return $resultSet;
	}

	function convertErrno($errno){
		if($errno == 1062)
			return "Ya se encuentra registrado.";
		return null;
	}

	function messageAlert($msg){
		echo ("<SCRIPT LANGUAGE='JavaScript'>
			window.alert('$msg');
			</SCRIPT>");
	}

	function messageAndRedirectAlert($msg,$redirect){
		echo ("<SCRIPT LANGUAGE='JavaScript'>
			window.alert('$msg')
			window.location.href='$redirect';
			</SCRIPT>");
	}

	function sendMail($from,$to,$subject,$message){
		mail($to,$subject,$message,'From: $from\n');
	}

?>