<?php
	define("MAX_SIZE", 1048576);
	require_once("index.php");
	
	$userid = $_SESSION["UserID"];
	$loginid = $_SESSION["LoginID"];
	$fileTmpLoc = $_FILES["audio-file"]["tmp_name"]; 
	$fileType = $_FILES["audio-file"]["type"]; 
	$fileSize = $_FILES["audio-file"]["size"];
	
	$title = $_POST["title"];
	$desc = $_POST["desc"];
	
	$sql = "INSERT INTO audio (UserID, LoginID, Title, Description) "
	 	 . "VALUES (" . $userid . ", " . $loginid . ", '" . $title . "', '" . $desc . "')";

	$id = GetID($sql);
	$encid = encrypt_decrypt("encrypt", $id);
	$filename = $encid . ".mp3";
	$filepath = "../uploads/audio/" . $filename;
	$sql = "UPDATE audio SET URL = '" . $filename . "' "
	     . "WHERE ID = " . $id . " LIMIT 1";
	ExecuteSQL($sql);
	
	if (!$fileTmpLoc) {
		echo "ERROR: Please browse for a file before clicking the upload button.";
		exit();
	}
	if(move_uploaded_file($fileTmpLoc, $filepath)){
		echo "{encid: '" . $encid .  "'}";
	} 
	else{
		echo "Upload failed.";
	}
?>