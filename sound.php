<?php 
	require_once "scripts/index.php";
	
	$encid = $_GET["s"];
	$audioid = encrypt_decrypt("decrypt", $encid);
	$sql = "SELECT audio.*, users.Username "
	     . "FROM audio INNER JOIN users "
		 . "ON audio.UserID = users.ID "
		 . " WHERE audio.id = " . $audioid . " LIMIT 1";
	$result = ExecuteSQL($sql);
	$row = $result->fetch_assoc();
	$userid = $row["UserID"];
	$url =  "uploads/audio/" . $row["URL"];
	$title = $row["Title"];
	$username = $row["Username"];
	$description = $row["Description"];
	$plays = $row["Plays"];
	$hearts = $row["Hearts"];
	$timestamp = FormatSqlDate($row["Timestamp"]);
	$sql = "SELECT * FROM audiocomments WHERE AudioID = " . $audioid;
	$commentcount = GetRecordCount($sql);
	$bAlreadyLiked = IsSoundLiked($audioid);
	$bLoggedIn = IsLoggedIn();
?>
<!DOCTYPE html>
<html>
	<head>
    	<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    	<title></title>
        
        <link rel="stylesheet" href="css/index.css">
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet" type="text/css">
        <link href="fontello/css/fontello.css" rel="stylesheet" type="text/css">
        <link href="fontello/css/animation.css" rel="stylesheet" type="text/css">
        <link href="js/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
        
        <script type="text/javascript" src="js/jquery.1.9.js"></script>
        <script type="text/javascript" src="js/jplayer/jplayer.js"></script>
        <script type="text/javascript" src="js/index.js"></script>
        <script type="text/javascript" src="js/utility.js"></script>
        <script type="text/javascript" src="js/moment.js"></script>
        <script type="text/javascript" src="js/sweetalert/sweetalert.min.js"></script>
        <script>
		
			var bAlreadyLiked = <?php echo iif($bAlreadyLiked, "true", "false"); ?>;
			var bLoggedIn = <?php echo iif($bLoggedIn, "true", "false"); ?>;
		
			function main(){
				LoginMenuInit();
				UserMenuInit();
				LoadSoundComments();
				if (!bAlreadyLiked && bLoggedIn){
					$(".icon-heart").attr("onclick", "LikeSound()");
				}
			}
			
			function LoadSoundComments(){
				var url = "scripts/ajax.php";
				var postdata = {
					method: "LoadSoundComments",
					audioid: "<?php echo $encid; ?>"
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
						console.log(content);
						$("#audio-comments-container").html(content);
						$("#audio-comments-container").fadeIn(100);
					});
				});
			}
			
			function SubmitSoundComment(){
				var text = $(".comment-textarea").val();
				console.log(text.length);
				if (text.length > 0){
					$(".register-submit-btn").addClass("no-click");
					var url = "scripts/ajax.php";
					var audioid = $("#encid").val();
					var postdata = {
						method: "SubmitSoundComment",
						audioid: audioid,
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
			
			function GetSoundCommentCount(){
				var url = "scripts/ajax.php";
				var audioid = $("#encid").val();
				var postdata = {
					method: "GetSoundCommentCount",
					audioid: audioid
				};
				var posting = $.post(url, postdata);
				posting.done(function(data){
					var obj = eval("(" + data + ")");
					var count = obj.Count;
					$(".icon-comment").html(count);
				})
			}
			
			function LikeSound(){
				var audioid = $("#encid").val();
				var url = "scripts/ajax.php";
				var postdata = {
					method: "LikeSound",
					audioid: audioid
				};
				$(".icon-heart").attr("onclick", "").unbind("click");
				var posting = $.post(url, postdata);
				posting.done(function(data){
					$(".icon-heart").css("color", "red");
				});
			}
			
			function ReloadComments(){
				$("#audio-comments-container").html("");
				$(".preloader-wrapper").show();
				LoadSoundComments();		
				GetSoundCommentCount();
			}
			
			function LikeSound(){
				var audioid = $("#encid").val();
				alert("test");
			}
			
			$(document).ready(main);
		</script>
    </head>
    <body>
    <div class="container">
        	<div class="header">
            	<ul class="nav-list">
             		<li class="word-anime"><a href="gallery.php">words</a></li>
                    <li class="sound-anime"><a href="gallery.php?s=1">sounds</a></li>
                    <div class="nav-list-underline" style="margin-left:50%;"></div>
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
            <div class="content" style="padding-top:16px">
            	<div class="user-dd-menu">
                	<div class='arrow' style='top:-7px;'></div>
                    <ul>
                        <li><a href='mywords'>My Words</a></li>
                        <li><a href="index.php">My Sounds</a></li>
                        <li><a href="../scripts/logout.php">Logout</a></li>
                    </ul>
                </div>
                <div class="play-sound-container">
                	<div class="play-sound-title"><?php echo $title; ?></div>
                    <div class="play-sound-description"><?php echo $description; ?></div>
                    <div class="play-audio-timestamp"><?php echo $timestamp; ?></div>
                    <div class="play-audio-wrapper">
                        <div class="audio-container">
                            <audio controls>
                                <source src="<?php echo $url; ?>" type="audio/mp3">
                            </audio>
                        </div>
                        <div class="play-sound-data">
                            <div class="icon-heart"><?php echo $hearts; ?></div>
                            <div class="icon-comment"><?php echo $commentcount; ?></div>
                            <div class="icon-play-circled"><?php echo $plays; ?></div>
                        </div>
                    </div>
                </div>
                <div class="input-comment-container">
                	<textarea class="comment-textarea" placeholder="Enter your comment here"></textarea>
                	<input type="hidden" id="encid" value="<?php echo $encid; ?>">
                	<div class="register-submit-btn" style="width:128px;float:right;font-size:20px;" onclick="SubmitSoundComment()">Submit</div>
                </div>
                <div class="audio-comments-wrapper">
                	<div class="preloader-wrapper">
                    	<i class="preloader-icon icon-spin5 animate-spin"></i>
                    </div>
                    <div id="audio-comments-container" style="display:none;"></div>
                </div>
        </div>
    </body>
</html>