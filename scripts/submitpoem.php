<?php
	require_once("index.php");
	
	$title = $_POST["poem-title"];
	$text = $_POST["poem-text"];
	$poem_id = SubmitPoem($title, $text);
	$enc_id = encrypt_decrypt("encrypt", $poem_id);
	Redirect("../poem.php?v=" . $enc_id);
?>