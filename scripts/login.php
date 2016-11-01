<?php
	require_once "index.php";
	if (isset($_POST["email"]) && isset($_POST["password"])){
		$email = $_POST["email"];
		$password = $_POST["password"];
		LoginUser($email, $password);
	}
?>