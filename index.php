<?php 
	require_once "scripts/index.php"; 
	if (isset($_GET["r"])){
		$r = iif($_GET["r"] == "1", true, false);
	}
	else{
		$r = 0;
	}
	if (isset($_SESSION["Username"])){
		Redirect("gallery.php");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title></title>
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css/animations.css">
        <link rel="stylesheet" href="css/index.css">
        <script type="text/javascript" src="js/jquery.1.9.js"></script>
        <script type="text/javascript" src="js/index.js"></script>
        <script type="text/javascript" src="js/utility.js"></script>
        <script>
			var bShowingLogin = iif(<?php echo $r; ?>, false, true);
			var ValidUsername = false;
			var ValidEmail = false;
			var ValidPassword = false;
			var PasswordsMatch = false;
			
			function ToggleLoginRegister(){
				if (bShowingLogin){
					$("#login-div").removeClass("rotate-y-end");
					$("#login-div").addClass("rotate-y-start");
					function done(){
						$("#login-div").css("display", "none");
						$("#register-div").css("display", "block");
						$("#register-div").addClass("rotate-y-end");
					}
					setTimeout(done, 250);
				}
				else{
					$("#register-div").removeClass("rotate-y-end");
					$("#register-div").addClass("rotate-y-start");
					function done(){
						$("#register-div").css("display", "none");
						$("#login-div").css("display", "block");
						$("#login-div").addClass("rotate-y-end");
					}
					setTimeout(done, 250);
				}
				bShowingLogin = !bShowingLogin;
			}
			
			function LoginFormKeyDown(e){
				if (e.keyCode == 13){
					LoginSubmit();
				}
			}
			
			function LoginSubmit(){
				var e = $("#errbox");
				e.hide();
				var email = $("#login-email").val();
				var password = $("#login-password").val();
				var url = "scripts/ajax.php";
				$("#register-submit-btn").css("pointer-events", "none");
				var postdata = {
					method: "IsValidCredentials",
					email: email,
					password: password
				};
				var posting = $.post(url, postdata);
				posting.done(function(data){
					if (data == "1"){
						var form = $("<form method='post' action='scripts/login.php'>"
								 + "<input type='hidden' name='email' value='" + email + "'>"
								 + "<input type='hidden' name='password' value='" + password + "'>"
								 + "</form>");
						$(document.body).append(form);
						form.submit();
					}
					else{
						e.fadeIn(200);
						e.html("Login failed.");
						$("#register-submit-btn").css("pointer-events", "auto");
						
					}
				});
				posting.fail(function(){
					alert("There was an error processing your request.");
				});
			}
			
			function RegisterFormKeyDown(e){
				if (e.keyCode == 13){
					RegisterSubmit();
				}
			}
			
			function RegisterSubmit(){
				var username = $("#username").val();
				var email = $("#email").val();
				var password = $("#password1").val();
				var e = $("#errbox");
				e.hide();
				$("#register-submit-btn").css("pointer-events", "none");
				if (!ValidUsername){
					e.fadeIn(200);
					e.html("That username is already in use.");
					$("#register-submit-btn").css("pointer-events", "auto");
				}
				else if(!ValidEmail){
					e.fadeIn(200);
					e.html("That email address is already in use.");
					$("#register-submit-btn").css("pointer-events", "auto");
				}
				else if(!ValidPassword){
					e.fadeIn(200);
					e.html("Password must contain at least 8 characters.");
					$("#register-submit-btn").css("pointer-events", "auto");
				}
				else if(!PasswordsMatch){
					e.fadeIn(200);
					e.html("Passwords do not match.");
					$("#register-submit-btn").css("pointer-events", "auto");
				}
				else if(!ValidateEmail(email)){
					e.fadeIn(200);
					e.html("Please enter a valid email address.");
					$("#register-submit-btn").css("pointer-events", "auto");
				}
				else{
					var form = $("<form method='post' action='scripts/register.php'>"
				 			 + "<input type='hidden' name='username' value='" + username + "'>"
							 + "<input type='hidden' name='email' value='" + email + "'>"
							 + "<input type='hidden' name='password' value='" + password + "'>" 
							 + "</form>");
					$(document.body).append(form);
					form.submit();
				}
				
			}
			
			function ValidatePassword(password){
				ValidPassword = iif(password.length >= 6 && password.length < 64, true, false);
			}
			
			function CheckIfPasswordsMatch(){
				var p1 = $("#password1").val();
				var p2 = $("#password2").val();
				PasswordsMatch = iif(p1 === p2, true, false);
			}
			
			function IsUsernameInUse(username){
				var url = "scripts/ajax.php";
				var postdata = {
					method: "IsUsernameInUse",
					username: username
				};
				var posting = $.post(url, postdata);
				posting.done(function(data){
					if (data == "1"){
						ValidUsername = false;
					}
					else{
						ValidUsername = true;
					}
				});
				posting.fail(function(data){
					ValidUsername = false;
				});
			}
			
			function IsEmailInUse(email){
				var url = "scripts/ajax.php";
				var postdata = {
					method: "IsEmailInUse",
					email: email
				};
				var posting = $.post(url, postdata);
				posting.done(function(data){
					if (data == "1"){
						ValidEmail = false;
					}
					else{
						ValidEmail = true;
					}
				});
				posting.fail(function(data){
					ValidEmail = false;
				});
			}
			
			function main(){
				var imageIndex = RandomInteger(1, 4);
				var imgUrl = "url(img/stock/" + imageIndex + ".jpg)";
				$(".register-container").css("background-image", imgUrl);
			}
			
			$(document).ready(main);
		</script>
	</head>
	<body class="noselect">
    	<div class="container">
        	<div class="content">
            	<div class="register-container">
                	<div class="register-top-container">
                    	<a href="gallery.php"><div class="home-btn">Gallery</div></a>
                    </div>
                    <div class="welcome-message-container"></div>
                    <div class="register-form" id="login-div" style="display:<?php echo iif(!$r, "block", "none"); ?>;">
                    	<form autocomplete="off" style="width:512px;">
                        	<input type="text" placeholder="Email" name="Email" class="input-top"  id="login-email">
                            <input type="password" placeholder="Password" name="password" class="input-middle" id="login-password" onkeydown="LoginFormKeyDown(event)">
                        </form>
                        <div class="register-submit-btn" onclick="LoginSubmit()">Log In</div>
                        <div id="errbox" class="opaque-box" style="display:none;margin-bottom:16px;color:white;border:1px solid red;"></div>
                        <div class="opaque-box">
                            <div class="already-registered">Not a member? <span class="login-link" onclick="ToggleLoginRegister()">Sign Up</span></div>
                            <div class="already-registered">Don't want to sign up? You may still view the <a href="gallery.php"><span class="login-link">Gallery</span></a>.</div>
                        </div>
                    </div>
                    <div class="register-form" id="register-div" style="display:<?php echo iif($r, "block", "none"); ?>;">
        				<form autocomplete="off" style="width:512px;">
             				<input type="text" placeholder="Username" name="Username" class="input-top" id="username" onkeyup="IsUsernameInUse(this.value)">
                			<input type="text" placeholder="Email" name="email" class="input-middle" id="email" style="border-bottom: none;" onkeyup="IsEmailInUse(this.value)">
                			<input type="password" placeholder="Password" name="password" id="password1" class="input-middle" onkeyup="ValidatePassword(this.value)" maxlength="32">
                			<input type="password" placeholder="Confirm Password" id="password2" class="input-bottom" onkeyup="CheckIfPasswordsMatch()" onkeydown="RegisterFormKeyDown(event)" maxlength="32">
       					</form>
                        <div class="register-submit-btn" onclick="RegisterSubmit()">Submit</div>
                        <div id="errbox" class="opaque-box" style="display:none;margin-bottom:16px;color:white;border:1px solid red;"></div>
                        <div class="opaque-box">
                            <div class="already-registered">Already Registered? <span class="login-link" onclick="ToggleLoginRegister()">Log In</span></div>
                            <div class="already-registered">Don't want to sign up? You may still view the <a href="gallery.php"><span class="login-link">Gallery</span></a>.</div>
                        </div>
                </div>
            </div>
        </div>
	</body>
</html>