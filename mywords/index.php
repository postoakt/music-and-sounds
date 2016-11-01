<?php 
	require_once "../scripts/index.php";

	if (!isset($_SESSION["Username"])){
		Redirect("../index.php");
	}
?>
<!DOCTYPE html>
<html>
	<head>
    	<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    	<title></title>
        
        <link rel="stylesheet" href="../css/index.css">
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet" type="text/css">
        <link href="../fontello/css/fontello.css" rel="stylesheet" type="text/css">
        <link href="../fontello/css/animation.css" rel="stylesheet" type="text/css">
        <link href="../js/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
        
        <script type="text/javascript" src="../js/jquery.1.9.js"></script>
        <script type="text/javascript" src="../js/index.js"></script>
        <script type="text/javascript" src="../js/utility.js"></script>
        <script type="text/javascript" src="../js/moment.js"></script>
        <script type="text/javascript" src="../js/sweetalert/sweetalert.min.js"></script>
        <script>
			function main(){
				UserMenuInit();
			}
			
			$(document).ready(main);
		</script>
    </head>
    <body>
    <div class="container">
        	<div class="header">
            	<ul class="nav-list">
             		<li class="word-anime"><a href="../index.php">words</a></li>
                    <li class="sound-anime"><a href="../index.php">sounds</a></li>
                    <div class="nav-list-underline"></div>
                </ul>
                <ul class="side-menu">
                	<?php 
						if (isset($_SESSION["Username"])){
							echo "<div class='user-container'>"
							   . "<div class='username'>" . $_SESSION["Username"] . "</div>"
							   . "<div class='drop-triangle'></div>"
							   . "</div>";
						}
					?>
                </ul>
            </div>
            <div class="content">
            	<div class="user-dd-menu">
                	<div class='arrow' style='top:-7px;'></div>
                    <ul>
                        <li><a href='mywords'>My Words</a></li>
                        <li><a href='index.php'>My Sounds</a></li>
                        <li><a href='../scripts/logout.php'>Logout</a></li>
                    </ul>
                </div>
                <div class="my-sounds-title">My Words</div>
            	<div class="my-sounds-container">
					<?php  
						$userid = $_SESSION["UserID"];
                        $sql = "SELECT * FROM words "
                             . "WHERE UserID = " . $userid;
                        $result = ExecuteSQL($sql);
                        
                        while ($row = $result->fetch_assoc()){
							$dt = FormatSqlDate($row["Timestamp"]);
							$sql = "SELECT * FROM wordcomments WHERE WordID = " . $row["Id"];
							$comments = GetRecordCount($sql);
							$encid = encrypt_decrypt("encrypt", $row["Id"]);
							
							echo "<div class='user-sound-container'>"
							   . "<div class='user-sound-title'><a href='../poem.php?v=" . $encid . "'>" . $row["Title"] . "</a></div>"
							   . "<div class='user-sound-description'>" . $row["Text"] . "</div>"
							   . "<div class='user-sound-timestamp'>" . $dt . "</div>"
							   . "<div class='user-sound-data'>"
							   . "<div class='icon-heart'>" . $row["Hearts"] . "</div>"
							   . "<div class='icon-comment'>" . $comments . "</div>"
							   . "</div>"
							   . "</div>";
                        }                      
             	    ?>
       		 	</div>	
        </div>
    </body>
</html>