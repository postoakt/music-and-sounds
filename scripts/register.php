<?php
	require_once "index.php";
	$username = $_POST["username"];
	$email = $_POST["email"];
	$password = $_POST["password"];
	RegisterUser($email, $username, $password);
	LoginUser($email, $password);
?>