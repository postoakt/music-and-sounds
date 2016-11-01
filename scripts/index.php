<?php
	session_start();
	require_once "db.php";
	require_once "encrypt.php";
	
	function UsernameInUse($username){
		$sql = "SELECT * FROM users WHERE Username = '" . $username . "'";
		$result = ExecuteSQL($sql);
		$row_count = $result->num_rows;
		return iif($row_count < 1, "0", "1");
	}
	
	function EmailInUse($email){
		$sql = "SELECT * FROM users WHERE Email = '" . $email . "'";
		$result = ExecuteSQL($sql);
		$row_count = $result->num_rows;
		return iif($row_count < 1, "0", "1");
	}
	
	function iif($b, $t, $f){
		return ($b ? $t : $f);
	}
	
	function RegisterUser($email, $username, $password){
		$sql = "INSERT INTO users "
			 . "(Email, Username, Password) "
			 . "VALUES('" . $email . "', '" . $username . "', '" . $password . "')";
		ExecuteSQL($sql);
	}
	
	function ValidCredentials($email, $password){
		$sql = "SELECT * FROM users " 
			 . "WHERE Email = '" . $email . "' AND BINARY Password = '" . $password . "'";
		$result = ExecuteSQL($sql);
		if ($result->num_rows >= 1){
			return true;
		}
		else{
			return false;
		}
	}
	
	function LoginUser($email, $password){
		$sql = "SELECT * FROM users " 
			 . "WHERE Email = '" . $email . "' AND Password = '" . $password . "'";
		$result = ExecuteSQL($sql);
		if ($result->num_rows >= 1){
			$row = mysqli_fetch_assoc($result);
			$_SESSION["Username"] = $row["Username"];
			$_SESSION["Email"] = $row["Email"];
			$_SESSION["UserID"] = $row["ID"];
			$uid = $row["ID"];
			$browser = $_SERVER["HTTP_USER_AGENT"];
			$ip = $_SERVER["REMOTE_ADDR"];
			$sql = "INSERT INTO logins (UserID, Browser, IP) "
				 . "VALUES (" . $uid . ", '" . $browser . "', '" . $ip . "')";
			$login_id = GetID($sql);
			$_SESSION["LoginID"] = $login_id;
			Redirect("../gallery.php");
		}
		else{
			Redirect("../index.php");
		}
	}
	
	function IsEmailInUse(){
		$email = $_POST["email"];
		if (EmailInUse($email)){
			echo 1;
		}
		else{
			echo 0;
		}
	}
	
	function IsValidCredentials(){
		$email = $_POST["email"];
		$password = $_POST["password"];

		if (ValidCredentials($email, $password)){
			echo 1;
		}
		else{
			echo 0;
		}
	}
	
	function IsLoggedIn(){
		echo iif(isset($_SESSION["Username"]), "1", "0");
	}
	
	function SubmitPoem($title, $text){
		$user_id = $_SESSION["UserID"];
		$login_id = $_SESSION["LoginID"];
		$sql = "INSERT INTO words (UserID, LoginID, Title, Text) "
		     . "VALUES (" . $user_id . ", " . $login_id . ", '" . $title . "', '" . $text . "')";
		$poem_id = GetID($sql);
		$poem_id = encrypt_decrypt("encrypt", $poem_id);
		Redirect("../poem.php?v=" . $poem_id);
	}
	
	function Logout(){
		session_unset();
	}
	
	function Redirect($url){
		header("location: " . $url);
		exit();
	}
	
	function ExecuteSQL($sql){
		$conn = new mysqli(SERVER, USERNAME, PASSWORD, DB);
		if ($conn->connect_errno){
			return "Failed to connect to database.";
		}
		$result = $conn->query($sql);
		if (!$result){
			return "Statement failed to execute. (" . $conn->error . ")";
		}
		$conn->close();
		return $result;
	}
	
	function GetID($sql){
		$conn = new mysqli(SERVER, USERNAME, PASSWORD, DB);
		if ($conn->connect_errno){
			return "Failed to connect to database.";
		}
		$result = $conn->query($sql);
		if (!$result){
			return "Statement failed to execute. (" . $conn->error . ")";
		}
		$id = $conn->insert_id;
		$conn->close();
		return $id;
	}
	
	function GetRecordCount($sql){
		$conn = new mysqli(SERVER, USERNAME, PASSWORD, DB);
		$result = $conn->query($sql);
		return $result->num_rows;
	}
	
	function FormatSqlDate($sqlDate){
		$phpdate = strtotime($sqlDate);
		return date("F j, Y, g:i a", $phpdate);
	}
	
	function IsSoundLiked($audioid){
		$userid = $_SESSION["UserID"];
		$sql = "SELECT LikedByIDs FROM audio WHERE ID = " . $audioid;
		$result = ExecuteSQL($sql);
		$row = $result->fetch_assoc();
		$likedbystr = $row["LikedByIDs"];
		$likedbyarr = explode(";", $likedbystr);
		$bIsLiked = false;
		foreach ($likedbyarr as $val){
			if ($userid == $val){
				$bIsLiked = true;
			}
		}
		return $bIsLiked;
	}
	
	function IsPoemLiked($wordid){
		$userid = $_SESSION["UserID"];
		$sql = "SELECT LikedByIDs FROM words WHERE ID = " . $wordid;
		$result = ExecuteSQL($sql);
		$row = $result->fetch_assoc();
		$likedbystr = $row["LikedByIDs"];
		$likedbyarr = explode(";", $likedbystr);
		$bIsLiked = false;
		foreach ($likedbyarr as $val){
			if ($userid == $val){
				$bIsLiked = true;
			}
		}
		return $bIsLiked;
	}
?>