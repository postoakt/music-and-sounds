<?php
	require_once "index.php";
	define("PER_PAGE", 12);
	
	function main(){
		$method = $_POST["method"];
		switch($method){
			case "IsUsernameInUse":
				IsUsernameInUse();
				break;
			case "IsEmailInUse":
				IsEmailInUse();
				break;
			case "IsValidCredentials":
				IsValidCredentials();
				break;
			case "IsLoggedIn":
				IsLoggedIn();
				break;
			case "LoadWords":
				LoadWords();
				break;
			case "LoadSounds":
				LoadSounds();
				break;
			case "LoadWordComments":
				LoadWordComments();
				break;
			case "SubmitWordComment":
				SubmitWordComment();
				break;
			case "GetWordCommentCount":
				GetWordCommentCount();
				break;
			case "LoadSoundComments":
				LoadSoundComments();
				break;
			case "SubmitSoundComment":
				SubmitSoundComment();
				break;
			case "GetSoundCommentCount":
				GetSoundCommentCount();
				break;
			case "LikeSound":
				LikeSound();
				break;
			case "LikePoem":
				LikePoem();
				break;
		}
	}
	
	function IsUsernameInUse(){
		$username = $_POST["username"];
		if (UsernameInUse($username)){
			echo 1;
		}
		else{
			echo 0;
		}
	}
	
	function LoadWords(){
		$per_page = 12;
		$typeid = $_POST["typeid"];
		$startindex = $_POST["startindex"];
		$orderby = "";
		switch($typeid){
			case 0:
				$orderby = "Hearts";
				break;
			case 1:
				$orderby = "Timestamp";
				break;
			case 2:
				$orderby = "RAND()";
				break;
		}
		$sql = "SELECT words.*, users.Username FROM words "
			 . "INNER JOIN users ON words.UserID = users.ID "
			 . "ORDER BY " . $orderby . " DESC LIMIT " . $startindex . ", " . PER_PAGE;
		$result = ExecuteSQL($sql);
		$response = "[";
		$num_rows = mysqli_num_rows($result);
		$counter = 1;
		while ($row = $result->fetch_assoc()){
			$id = encrypt_decrypt("encrypt", $row["Id"]);
			$sql = "SELECT * FROM wordcomments WHERE WordID = " . $row["Id"];
			$commentcount = GetRecordCount($sql);
			$userid = encrypt_decrypt("encrypt", $row["UserID"]);
			$username = $row["Username"];
			$title = $row["Title"];
			$text = $row["Text"];
			$hearts = $row["Hearts"];
			$timestamp = $row["Timestamp"];
			$response .= "{";
			$response .= "id: '" . $id . "', ";
			$response .= "userid: '" . $userid . "', ";
			$response .= "username: '" . $username . "', ";
			$response .= "title: '" . $title . "', ";
			$response .= "text: '" . $text . "', ";
			$response .= "hearts: " . $hearts . ", ";
			$response .= "comments: " . $commentcount . ", ";
			$response .= "timestamp: '" . $timestamp . "'";
			$response .= "}";
			$response .= iif($counter < $num_rows, ", ", "");
			$counter++;
		}
		$response .= "]";
		echo $response;
	}
	
	function LoadSounds(){
		$per_page = 12;
		$typeid = $_POST["typeid"];
		$startindex = $_POST["startindex"];
		$orderby = "";
		switch($typeid){
			case 0:
				$orderby = "Hearts";
				break;
			case 1:
				$orderby = "Timestamp";
				break;
			case 2:
				$orderby = "RAND()";
				break;
		}
		$sql = "SELECT audio.*, users.Username FROM audio "
			 . "INNER JOIN users ON audio.UserID = users.ID "
			 . "ORDER BY " . $orderby . " DESC LIMIT " . $startindex . ", " . PER_PAGE;
		$result = ExecuteSQL($sql);
		$response = "[";
		$num_rows = mysqli_num_rows($result);
		$counter = 1;
		while ($row = $result->fetch_assoc()){
			$id = encrypt_decrypt("encrypt", $row["id"]);
			$sql = "SELECT * FROM audiocomments WHERE AudioID = " . $row["id"];
			$commentcount = GetRecordCount($sql);
			$userid = encrypt_decrypt("encrypt", $row["UserID"]);
			$username = $row["Username"];
			$url = $row["URL"];
			$title = $row["Title"];
			$text = $row["Description"];
			$hearts = $row["Hearts"];
			$timestamp = $row["Timestamp"];
			$response .= "{";
			$response .= "id: '" . $id . "', ";
			$response .= "userid: '" . $userid . "', ";
			$response .= "username: '" . $username . "', ";
			$response .= "title: '" . $title . "', ";
			$response .= "text: '" . $text . "', ";
			$response .= "url: '" . $url . "', ";
			$response .= "hearts: " . $hearts . ", ";
			$response .= "comments: " . $commentcount . ", ";
			$response .= "timestamp: '" . $timestamp . "'";
			$response .= "}";
			$response .= iif($counter < $num_rows, ", ", "");
			$counter++;
		}
		$response .= "]";
		echo $response;
	}
	
	function LoadWordComments(){
		$wordid = encrypt_decrypt("decrypt", $_POST["wordid"]);
		$sql = "SELECT wordcomments.*, users.Username FROM wordcomments "
			 . "INNER JOIN users ON wordcomments.UserID = users.ID "
			 . "WHERE wordcomments.WordID = " . $wordid . " "
			 . "ORDER BY wordcomments.timestamp DESC";
		$result = ExecuteSQL($sql);
		$num_rows = mysqli_num_rows($result);
		$counter = 1;
		$response = "[";
		while ($row = $result->fetch_assoc()){
			$id = $row["id"];
			$userid = $row["UserID"];
			$text = $row["Text"];
			$timestamp = $row["Timestamp"];
			$username = $row["Username"];
			$response .= "{"
					   . "id: " . $id . ", "
					   . "userid: " . $userid . ", "
					   . "username: '" . $username . "', "
					   . "text: '" . $text . "', "
					   . "timestamp: '" . $timestamp . "'"
					   . "}"
					   . iif($counter < $num_rows, ", ", "");
			$counter++;
		}
		$response .= "]";
		echo $response;
	}
	
	function LoadSoundComments(){
		$audioid = encrypt_decrypt("decrypt", $_POST["audioid"]);
		$sql = "SELECT audiocomments.*, users.Username FROM audiocomments "
			 . "INNER JOIN users ON audiocomments.UserID = users.ID "
			 . "WHERE audiocomments.AudioID = " . $audioid . " "
			 . "ORDER BY audiocomments.timestamp DESC";
		$result = ExecuteSQL($sql);
		$num_rows = mysqli_num_rows($result);
		$counter = 1;
		$response = "[";
		while ($row = $result->fetch_assoc()){
			$id = $row["id"];
			$userid = $row["UserID"];
			$text = $row["Text"];
			$timestamp = $row["Timestamp"];
			$username = $row["Username"];
			$response .= "{"
					   . "id: " . $id . ", "
					   . "userid: " . $userid . ", "
					   . "username: '" . $username . "', "
					   . "text: '" . $text . "', "
					   . "timestamp: '" . $timestamp . "'"
					   . "}"
					   . iif($counter < $num_rows, ", ", "");
			$counter++;
		}
		$response .= "]";
		echo $response;
	}
	
	function SubmitSoundComment(){
		$userid = $_SESSION["UserID"];
		$loginid = $_SESSION["LoginID"];
		$audioid = encrypt_decrypt("decrypt", $_POST["audioid"]);
		$text = $_POST["text"];
		$sql = "INSERT INTO audiocomments (AudioID, UserID, LoginID, Text) "
		     . "VALUES (" . $audioid . ", " . $userid . ", " . $loginid . ", '" . $text . "')";
		ExecuteSQL($sql);
	}
	
	function GetSoundCommentCount(){
		$audioid = encrypt_decrypt("decrypt", $_POST["audioid"]);
		$sql = "SELECT * FROM audiocomments WHERE AudioID = " . $audioid;
		$count = GetRecordCount($sql);
		$result = "{Count: " . $count . "}";
		echo $result;
	}
	
	
	function SubmitWordComment(){
		$userid = $_SESSION["UserID"];
		$loginid = $_SESSION["LoginID"];
		$wordid = encrypt_decrypt("decrypt", $_POST["wordid"]);
		$text = $_POST["text"];
		$sql = "INSERT INTO wordcomments (WordID, UserID, LoginID, Text) "
		     . "VALUES (" . $wordid . ", " . $userid . ", " . $loginid . ", '" . $text . "')";
		ExecuteSQL($sql);
	}
	
	function GetWordCommentCount(){
		$wordid = encrypt_decrypt("decrypt", $_POST["wordid"]);
		$sql = "SELECT * FROM wordcomments WHERE WordID = " . $wordid;
		$count = GetRecordCount($sql);
		$result = "{Count: " . $count . "}";
		echo $result;
	}
	
	function LikeSound(){
		if (IsLoggedIn()){
			$userid = $_SESSION["UserID"];
			$audioid = $_POST["audioid"];
			$audioid = encrypt_decrypt("decrypt", $audioid);
			$sql = "SELECT LikedByIDs FROM audio WHERE id = " . $audioid;
			$result = ExecuteSQL($sql);
			$row = $result->fetch_assoc();
			$likedbyids = $row["LikedByIDs"];
			$likedbyarr = explode(";", $likedbyids);
			$alreadyliked = false;
			foreach ($likedbyarr as $i){
				if ($i == $userid){
					$alreadyliked = true;
				}
			}
			if (!$alreadyliked){
				if (sizeof($likedbyarr) > 0){
					$likedbyids .= ";" . $audioid;
				}
				else{
					$likedbyids .= $audioid;
				}
				$sql = "UPDATE audio SET LikedByIDs = '" . $likedbyids . "', Hearts = Hearts + 1 WHERE id = " . $audioid;
				ExecuteSQL($sql);
			}
		}
	}
	
	function LikePoem(){
		if (IsLoggedIn()){
			$userid = $_SESSION["UserID"];
			$wordid = $_POST["wordid"];
			$wordid = encrypt_decrypt("decrypt", $wordid);
			$sql = "SELECT LikedByIDs FROM words WHERE id = " . $wordid;
			$result = ExecuteSQL($sql);
			$row = $result->fetch_assoc();
			$likedbyids = $row["LikedByIDs"];
			$likedbyarr = explode(";", $likedbyids);
			$alreadyliked = false;
			foreach ($likedbyarr as $i){
				if ($i == $userid){
					$alreadyliked = true;
				}
			}
			if (!$alreadyliked){
				if (sizeof($likedbyarr) > 0){
					$likedbyids .= ";" . $wordid;
				}
				else{
					$likedbyids .= $wordid;
				}
				$sql = "UPDATE words SET LikedByIDs = '" . $likedbyids . "', Hearts = Hearts + 1 WHERE id = " . $wordid;
				ExecuteSQL($sql);
			}
		}
	}
	
	main();
?>
