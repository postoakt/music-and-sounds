var onwords = true;
var showing_dd = false;
var bMakingNew = false;

function GalleryInit(){
	
	LoginMenuInit();
	UserMenuInit();
	
	$(".word-anime").click(function(){
		if (bMakingNew){
			location.href = "gallery.php";
		}
		else{
			if (!onwords){
				$(".nav-list-underline").css("margin-left", "0");
				WordsOrSoundsToggle();
				onwords = !onwords;
				$(".type-sel-underline").css("margin-left", "0");
				ChangeCategory(0);
				LoadWords(0, 0, false);
			}
		}
	});
	
	$(".sound-anime").click(function(){
		if (bMakingNew){
			location.href = "gallery.php?s=1";
		}
		else{
			if (onwords){
				$(".nav-list-underline").css("margin-left", "50%");
				WordsOrSoundsToggle();
				onwords = !onwords;
				$(".type-sel-underline").css("margin-left", "0");
				ChangeCategory(0);
				LoadSounds(0, 0, false);
			}
		}
	});
	
	$("#audio-file-input").change(function(e){
		var filename = this.value;
		var arr = filename.split(".");
		if (arr[1] == "mp3"){
			$("#upload-file-container span").html(this.value);
		}
		else{
			e.preventDefault();
			alert("Currently the only file type that is supported is mp3.");
		}
	});
	
	if(bSound){
		$(".nav-list-underline").css("margin-left", "50%");
		WordsOrSoundsToggle()
		onwords = false;
		LoadSounds(0, 0, false);
	}
	else{
		onwords = true;
		LoadWords(0, 0, false);
	}
}

function LoginMenuInit(){
	$(".show-login-menu").click(function(){
		var bShowing = $(".login-container").attr("bShowing")
		bShowing = iif(bShowing == "1", true, false);
		if (bShowing){
			$(".login-container").hide();
			$(".login-container").attr("bShowing", "0");
		}
		else{
			$(".login-container").show();
			$(".login-container").attr("bShowing", "1");
		}
	});
}

function UserMenuInit(){
	$(".user-container").click(function(){
		if (showing_dd){
			$(".user-dd-menu").hide();
		}
		else{
			$(".user-dd-menu").show();
		}
		showing_dd = !showing_dd;
	});
}

function LoadWords(TypeId, sIndex, append){
	if (append){
		$(".loadmore-btn").hide();
		$(".loadmore-preloader").show();
	}
	else{
		$("#words-gallery").hide();
		$(".preloader-wrapper").show();
	}
	var url = "scripts/ajax.php";
	var postdata = {
		method: "LoadWords",
		typeid: TypeId,
		startindex: sIndex
	};
	var posting = $.post(url, postdata);
	posting.done(function(data){
		var obj = eval("(" + data + ")");
		var content = "";
		$.each(obj, function(i, val){
			var poemtext = val.text;
			poemtext = iif(poemtext.length > 150, poemtext.substring(0, 150) + "...", poemtext);
			content += "<div class='poem-wrapper'>"
				     + "<div class='poem-title'>" + val.title + "</div>"
					 + "<div class='poem-text'>" + poemtext + "</div>"
					 + "<div class='sticky-bottom' style='left:50%;'>"
					 + "<div style='position:relative;left:-50%;'>"
					 + "<div class='view-more'><a href='poem.php?v=" + val.id + "'>View More</a></div>"
					 + "<div class='poem-author'>" + val.username + "<br> " + moment(val.timestamp).calendar() + "</div>"
					 + "<div class='poem-data'>"
					 + "<div class='icon-heart'>" + val.hearts + "</div><div class='icon-comment'>" + val.comments + "</div>"
					 + "</div>"
					 + "</div>"
					 + "</div>"
			         + "</div>";
		});
		currentIndex = sIndex + obj.length;
		bCanLoadMore = iif(obj.length > 0, true, false);
		if (bCanLoadMore){
			$(".loadmore-btn").show();
		}
		else{
			$(".loadmore-btn").hide();
		}
		
		if (append){
			$(".loadmore-preloader").hide();
			$("#words-gallery").append(content);
		}
		else{
			$("#words-gallery").html(content);
		}
		$(".preloader-wrapper").fadeOut(100, function(){
			$("#words-gallery").fadeIn(100);
		});
	});
}

function LoadSounds(TypeId, sIndex, append){
	if (append){
		$(".loadmore-btn").hide();
		$(".loadmore-preloader").show();
	}
	else{
		$("#words-gallery").hide();
		$(".preloader-wrapper").show();
	}
	var url = "scripts/ajax.php";
	var postdata = {
		method: "LoadSounds",
		typeid: TypeId,
		startindex: sIndex
	};
	var posting = $.post(url, postdata);
	posting.done(function(data){
		var obj = eval("(" + data + ")");
		var content = "";
		$.each(obj, function(i, val){
			var soundtext = val.text;
			soundtext = iif(soundtext.length > 150, soundtext.substring(0, 150) + "...", soundtext);
			content += "<div class='poem-wrapper'>"
				     + "<div class='poem-title'>" + val.title + "</div>"
					 + "<div class='poem-text'>" + soundtext + "</div>"
					 + "<div class='sticky-bottom' style='left:50%;'>"
					 + "<div style='position:relative;left:-50%;'>"
					 + "<a href='sound.php?s=" + val.id + "'><div class='sound-play-btn'><div class='play-triangle'></div></div></a>"
					 + "<div class='poem-author'>" + val.username + "<br> " + moment(val.timestamp).calendar() + "</div>"
					 + "<div class='poem-data'>"
					 + "<div class='icon-heart'>" + val.hearts + "</div><div class='icon-comment'>" + val.comments + "</div>"
					 + "</div>"
					 + "</div>"
					 + "</div>"
			         + "</div>";
		});
		currentIndex = sIndex + obj.length;
		bCanLoadMore = iif(obj.length > 0, true, false);
		if (bCanLoadMore){
			$(".loadmore-btn").show();
		}
		else{
			$(".loadmore-btn").hide();
		}
		
		if (append){
			$(".loadmore-preloader").hide();
			$("#sounds-gallery").append(content);
		}
		else{
			$("#sounds-gallery").html(content);
		}
		$(".preloader-wrapper").fadeOut(100, function(){
			$("#sounds-gallery").fadeIn(100);
		});
	});
}

function IsLoggedIn(success, fail){
	var url = "scripts/ajax.php";
	var postdata = {
		method: "IsLoggedIn"
	};
	var posting = $.post(url, postdata);
	posting.done(function(data){
		if (data == "1"){
			success();
		}
		else{
			fail();
		}
	});
}

function ValidateEmail(email){
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

$(document).delegate(".new-word-body", "keydown", function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode == 9) {
    e.preventDefault();
    var start = $(this).get(0).selectionStart;
    var end = $(this).get(0).selectionEnd;

    $(this).val($(this).val().substring(0, start)
                + "\t"
                + $(this).val().substring(end));

    $(this).get(0).selectionStart =
    $(this).get(0).selectionEnd = start + 1;
  }
});