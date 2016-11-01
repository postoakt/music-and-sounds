<?php
	require_once "scripts/index.php";
	if (!isset($_GET["v"])){
		Redirect("gallery.php");
	}
	$id = encrypt_decrypt("decrypt", $_GET["v"]);
	$sql = "SELECT * FROM words WHERE id = " . $id;
	$result = ExecuteSQL($sql);
	$row = mysqli_fetch_assoc($result);
	$userid = $row["UserID"];
	$loginid = $row["LoginID"];
	$title = $row["Title"];
	$text = $row["Text"];
	$timestamp = $row["Timestamp"];
	$hearts = $row["Hearts"];
	$sql = "SELECT * FROM wordcomments WHERE WordID = " . $id;
	$comments = GetRecordCount($sql);
	$sql = "SELECT Username FROM users WHERE ID = " . $userid;
	$result = ExecuteSQL($sql);
	$row = mysqli_fetch_assoc($result);
	$username = $row["Username"];
	$bAlreadyLiked = IsPoemLiked($id);
	$bLoggedIn = IsLoggedIn();
?>
<!DOCTYPE html>
<html>
	<head>
    	<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    	<title><?php echo $title; ?></title>
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet" type="text/css">
        <link href="fontello/css/fontello.css" rel="stylesheet" type="text/css">
        <link href="fontello/css/animation.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css/index.css">
        <script type="text/javascript" src="js/jquery.1.9.js"></script>
        <script type="text/javascript" src="js/index.js"></script>
        <script type="text/javascript" src="js/utility.js"></script>
        <script type="text/javascript" src="js/moment.js"></script>
        <script>
		
			var bAlreadyLiked = <?php echo iif($bAlreadyLiked, "true", "false"); ?>;
			var bLoggedIn = <?php echo iif($bLoggedIn, "true", "false"); ?>;
		
			function main(){
				LoginMenuInit();
				UserMenuInit();
				LoadWordComments();
				if (!bAlreadyLiked && bLoggedIn){
					$(".icon-heart").attr("onclick", "LikePoem()");
				}
				else{
					$(".icon-heart").css("color", "red");
				}
			}
			
			function LoadWordComments(){
				var url = "scripts/ajax.php";
				var postdata = {
					method: "LoadWordComments",
					wordid: "<?php echo encrypt_decrypt("encrypt", $id); ?>"
				}
				var posting = $.post(url, postdata);
				posting.done(function(data){
					var content = "";
					var obj = eval("(" + data + ")");
					$.each(obj, function(i, val){
						content += "<div class='comment-wrapper'>"
								 + "<div class='comment-text'>" + val.text + "</div>"
								 + "<div class='comment-data'> - " + val.username + ", " + moment(val.timestamp).calendar() + "</div>"
								 + "</div>";
					});
					$(".preloader-wrapper").fadeOut(100, function(data){
						$("#poem-comments-container").html(content);
						$("#poem-comments-container").fadeIn(100);
					});
				});
			}
			
			function SubmitWordComment(){
				var text = $(".comment-textarea").val();
				console.log(text.length);
				if (text.length > 0){
					$(".register-submit-btn").addClass("no-click");
					var url = "scripts/ajax.php";
					var wordid = $("#encid").val();
					var postdata = {
						method: "SubmitWordComment",
						wordid: wordid,
						text: text
					};
					var posting = $.post(url, postdata);
					posting.always(function(data){
						$(".comment-textarea").val("");
						$(".register-submit-btn").removeClass("no-click");
						ReloadComments();
					});
				}
			}
			
			function GetWordCommentCount(){
				var url = "scripts/ajax.php";
				var wordid = $("#encid").val();
				var postdata = {
					method: "GetWordCommentCount",
					wordid: wordid
				};
				var posting = $.post(url, postdata);
				posting.done(function(data){
					var obj = eval("(" + data + ")");
					var count = obj.Count;
					$(".icon-comment").html(count);
				})
			}
			
			function ReloadComments(){
				$("#poem-comments-container").html("");
				$(".preloader-wrapper").show();
				LoadWordComments();		
				GetWordCommentCount();
			}
			
			function LikePoem(){
				var wordid = $("#encid").val();
				var url = "scripts/ajax.php";
				var postdata = {
					method: "LikePoem",
					wordid: wordid
				};
				$(".icon-heart").attr("onclick", "").unbind("click");
				var posting = $.post(url, postdata);
				posting.done(function(data){
					$(".icon-heart").css("color", "red");
				});
			}
			
			$(document).ready(main);
		</script>
	</head>
<body class="noselect">
    	<div class="container">
        	<div class="header">
            	<ul class="nav-list">
             		<li class="word-anime"><a href="gallery.php">words</a></li>
                    <li class="sound-anime"><a href="gallery.php?s=1">sounds</a></li>
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
						else{
							echo "<li class='show-login-menu' id='login-word'>Log In</li>"
							   . "<a href='index.php?r=1'><li class='register-btn'>Sign Up</li></a>";
						}
					?>
                </ul>
            </div>
            <div class="content">
            	<?php
					if (isset($_SESSION["Username"])){
						echo "<div class='user-dd-menu'>"
						   . "<div class='arrow' style='top:-7px;'></div>"
						   . "<ul>"
						   . "<li><a href='mywords'>My Words</a></li>"
						   . "<li><a href='mysounds'>My Sounds</a></li>"
						   . "<li><a href='scripts/logout.php'>Logout</a></li>"
						   . "</ul>"
						   . "</div>";
					}	
				?>
                <div style="max-width:768px;margin:0px auto;overflow:hidden;">
                	<div class="word-upload-back" margin="8px auto;">
                    	<a href="gallery.php"><i class="icon-left-big"></i>Back</a>
                    </div>
                </div>
                <div class="full-poem-container">
                	<div class="full-poem-title"><?php echo $title; ?></div>
                    <div class="full-poem-body"><?php echo $text; ?></div>
                    <div class="full-poem-data">
                    	<div class="full-poem-author"> - <?php echo $username . ", " . "<script>document.write(moment('" . $timestamp . "').calendar());</script>"; ?></div>
                    	<div class="icon-heart" style="padding-top:4px;"><?php echo $hearts; ?></div>
                        <div class="icon-comment" style="padding-top:4px;float:left;"><?php echo $comments; ?></div>
                    </div>
                </div>
                <div class="input-comment-container">
                	<textarea class="comment-textarea" placeholder="Enter your comment here"></textarea>
                    <input type="hidden" id="encid" value="<?php echo encrypt_decrypt("encrypt", $id); ?>">
                    <div class="register-submit-btn" style="width:128px;float:right;font-size:20px;" onclick="SubmitWordComment()">Submit</div>
                </div>
                <div class="poem-comments-wrapper">
                	<div class="preloader-wrapper">
                    	<i class="preloader-icon icon-spin5 animate-spin"></i>
                    </div>
                    <div id="poem-comments-container" style="display:none;"></div>
                </div>
            </div>
        </div>
        <div class="login-container" bShowing="0">
        	<div class="arrow"></div>
            <div class="login-form">
            	<form autocomplete="off">
                    <table>
                        <tr><td><input type="text" id="login-email" class="login-input" placeholder="email"></td></tr>
                        <tr><td><input type="password" id="login-password" class="login-input" placeholder="password" onkeydown="FormKeyDown(event)"></td></tr>
                        <tr><td><div id="login-submit" class="login-btn" onclick="LoginValidate()">Log In</div></td></tr>
                    </table>
                </form>
            </div>
        </div>
    </body>
</html>