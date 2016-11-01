<?php 
	require_once "scripts/index.php";
	$bSound = "0";
	if (isset($_GET["s"])){
		$bSound = $_GET["s"];
	}
	$bSound = iif($bSound == "1", true, false);
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
        <script type="text/javascript" src="js/index.js"></script>
        <script type="text/javascript" src="js/utility.js"></script>
        <script type="text/javascript" src="js/moment.js"></script>
        <script type="text/javascript" src="js/sweetalert/sweetalert.min.js"></script>
        <script>
			var bShowingWords = true;
			var bSound = <?php echo iif($bSound, "true", "false"); ?>;
			var currentIndex = 0;
			var currentTypeId = 0;
			var bCanLoadMore = true;
			
			function FormKeyDown(e){
				if (e.keyCode == 13){
					LoginValidate();
				}
			}
			
			function WordsOrSoundsToggle(){
				if (bShowingWords){
					$(".words-content-container").fadeOut(100, function(){
						$(".sounds-content-container").fadeIn(100);
					});
				}
				else{
					$(".sounds-content-container").fadeOut(100, function(){
						$(".words-content-container").fadeIn(100);
					});
				}
				bShowingWords = !bShowingWords;
			}
			
			function ChangeCategory(index){
				switch (index){
					case 0:
						$(".type-sel-underline").css("margin-left", "4%");
						if (bShowingWords){
							LoadWords(0, 0);
						}
						else{
							LoadSounds(0, 0);
						}
						break;
					case 1:
						$(".type-sel-underline").css("margin-left", "35%");
						if (bShowingWords){
							LoadWords(1, 0);
						}
						else{
							LoadSounds(1, 0);
						}
						break;
					case 2: 
						$(".type-sel-underline").css("margin-left", "72%");
						if (bShowingWords){
							LoadWords(2, 0);
						}
						else{
							LoadSounds(2, 0);
						}
						break;
					default:
						break;
				}
				ccurrentTypeId = index;
			}
			
			function LoadWordForm(){
				var success = function(){
					bMakingNew = true;
					$(".words-content-container, .type-sel-menu").fadeOut(100, function(){
						$(".new-words-container").fadeIn(100);
					});
				}
				var fail = function(){
					location.href = "index.php";
				}
				
				IsLoggedIn(success, fail);
			}
			
			function LoadSoundForm(){
				var success = function(){
					bMakingNew = true;
					$(".sounds-content-container, .type-sel-menu").fadeOut(100, function(){
						$(".new-sounds-container").fadeIn(100);
					});
				}
				var fail = function(){
					location.href = "index.php";
				}
				
				IsLoggedIn(success, fail);
			}
			
			function LoginValidate(){
				var email = $("#login-email").val();
				var password =  $("#login-password").val();
				if (ValidateEmail(email) && password.length >= 1){
					var form = $("<form action='scripts/login.php' method='post'>"
						     + "<input type='hidden' name='email' value='" + email + "'>"
							 + "<input type='hidden' name='password' value='" + password + "'>"
							 + "</form>");
					$(document.body).append(form);
					form.submit();
				}
			}
			
			function ValidatePoemSubmit(){
				var title = $("#word-title").val();
				var text = $("#word-body").val();
				if (text.length > 0){
					var form = $("<form action='scripts/submitpoem.php' method='post'>"
							 + "<input type='hidden' name='poem-title' value='" + title + "'>"
							 + "<input type='hidden' name='poem-text' value='" + text + "'>"
							 + "</form>");
					$(document.body).append(form);
					form.submit();
				}
			}
			
			
			function LoadWordGallery(){
				$(".new-words-container").fadeOut(100, function(){
					$(".words-content-container").fadeIn(100);
					$(".type-sel-menu").fadeIn(100);
					bMakingNew = false;
				});
			}
			
			function LoadSoundGallery(){
				$(".new-sounds-container").fadeOut(100, function(){
					$(".sounds-content-container").fadeIn(100);
					$(".type-sel-menu").fadeIn(100);
					bMakingNew = false;
				});
			}
			
			function ValidateSoundUpload(){
				var file = $("#audio-file-input").val();
				var title = $("#sound-title").val();
				var desc = $("#sound-desc").val();
				if (file.length < 1){
					alert("You must select an audio file.");
				}
				else if (title.length < 1){
					alert("You must enter a title.");
				}
				else if (desc.length < 1){
					alert("You must enter a description.");
				}
				else{
					UploadAudio();
				}
			}
			
			function UploadAudio(){
				var form = $("#audio-upload-form")[0];
				var formdata = new FormData(form);
				var ajax = new XMLHttpRequest();
				ajax.upload.addEventListener("progress", UploadProgress, false);
				ajax.addEventListener("load", UploadComplete, false);
				ajax.addEventListener("error", UploadError, false);
				ajax.open("POST", "scripts/uploadaudio.php");
				ajax.send(formdata);	
			}
			
			function UploadProgress(e){
				var ratio = e.loaded / e.total;
				var percentage = Math.floor(ratio * 100) + "%";
				console.log(percentage);
				var done = iif(ratio >= 1, true, false);
				var text = "<span id='upload-status'>Uploading...</span>"
					     + "<div class='progress-bar-container'><div id='progress-bar'></div></div>"
						 + "<div class='percentage " + iif(done, "green", "") + "'>" + percentage + "</div>";
				var obj = {
					html: true,
					title: "",
					text: text,
					showCanelButton: false,
					showConfirmButton: done
				};
				
				function sw_close(){
					location.href = "mysounds";
				}
				
				swal(obj, sw_close);
				$("#progress-bar").css("width", percentage);
				$("#progress-bar").css("border-top-right-radius", "3px");
				$("#progress-bar").css("border-bottom-right-radius", "3px");
			}
			
			function UploadComplete(e){
				$("#upload-status").fadeOut(100, function(){
					$("#upload-status").html("Done.");
					$("#upload-status").fadeIn(100);
				})
				$("#progress-bar").css("border-radius", "8px");
			}
			
			function UploadError(){
				swal("", "There was an error processing your request.");
			}
			
			function LoadMoreWords(){
				LoadWords(currentTypeId, currentIndex, true);
			}
			
			function LoadMoreSounds(){
				LoadSounds(currentTypeId, currentIndex, true);
			}
			
			$(document).ready(GalleryInit);
		</script>
    </head>
    <body class="noselect">
    	<div class="container">
        	<div class="header">
            	<ul class="nav-list">
             		<li class="word-anime">words</li>
                    <li class="sound-anime">sounds</li>
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
				<ul class="type-sel-menu">
                	<li onclick="ChangeCategory(0)">Hot</li>
                    <li onclick="ChangeCategory(1)">Latest</li>
                    <li onclick="ChangeCategory(2)">Random</li>
                    <div class="type-sel-underline"></div>
                </ul>
                <div class="words-content-container">
                	<div class="new-upload-btn" onclick="LoadWordForm()">Submit a Poem</div>
                    <div class="preloader-wrapper">
                    	<i class="preloader-icon icon-spin5 animate-spin"></i>
                    </div>
                    <div id="words-gallery"></div>
                    <div class="loadmore-container">
                    	<div class="loadmore-btn" onclick="LoadMoreWords()" style="display:none;">Load More</div>
                        <div class="loadmore-preloader" style="display:none;">
                        	<i class="preloader-icon icon-spin5 animate-spin"></i>
                        </div>
                    </div>
                </div>
                <div class="sounds-content-container">
                	<div class="new-upload-btn" onclick="LoadSoundForm()">Upload a Sound</div>
                    <div class="preloader-wrapper">
                    	<i class="preloader-icon icon-spin5 animate-spin"></i>
                    </div>
                    <div id="sounds-gallery"></div>
                    <div class="loadmore-container">
                    	<div class="loadmore-btn" onclick="LoadMoreSounds()" style="display:none;">Load More</div>
                    </div>
                   <div class="loadmore-preloader" style="display:none;">
                   		<i class="preloader-icon icon-spin5 animate-spin"></i>
                   </div>
                </div>
                <div class="new-words-container">
                    <div class="word-upload-back" onclick="LoadWordGallery()"><i class="icon-left-big"></i>Back</div>
                    <input class="new-word-title" id="word-title" placeholder="Enter the title here">
                    <textarea class="new-word-body" id="word-body" placeholder="Type your poem here"></textarea>
                    <div class="register-submit-btn" style="float:right;" onclick="ValidatePoemSubmit()">Submit</div>
                </div>
                <div class="new-sounds-container">
                	<div class="word-upload-back" onclick="LoadSoundGallery()"><i class="icon-left-big"></i>Back</div>
                    <form id="audio-upload-form" autocomplete="off" enctype="multipart/form-data" method="post" action="scripts/uploadaudio.php">
                        <div id="upload-file-container">
                            <span>Choose a file to upload</span>
                            <input type="file" id="audio-file-input" accept="audio/mpeg" name="audio-file">
                        </div>
                        <input class="new-word-title" placeholder="Enter the title here" id="sound-title" name="title">
                        <textarea class="new-word-body" placeholder="Enter description / lyrics here" id="sound-desc" name="desc"></textarea>
                        <div class="register-submit-btn" style="float:right;" onclick="ValidateSoundUpload()">Upload</div>
                    </form>
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
