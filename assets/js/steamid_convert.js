$("#input").focus();

window.onpopstate = function(e) {
	if (e.state) {
		location.reload();
	}
};

function fnDeSelect() {
	if (document.selection) document.selection.empty(); 
	else if (window.getSelection)
	window.getSelection().removeAllRanges();
}

function on_enter(e) {
	if (e.keyCode == 13) {
		$("#convert").click();
	}
	return false;
}

$("#input").focus(function() {
	$(this).select();
});
$(".select-this").mouseenter(function() {
	$(this).select();
});
$(".select-this").click(function() {
	$(this).select();
});
$(".select-this").focus(function() {
	$(this).select();
});
$(".select-this").mouseout(function() {
	fnDeSelect();
});

var curgetbg = undefined;

$("#convert").click(function() {
	$("#loading").attr("src","/assets/img/cubeload.gif");
	$("#loading").css("visibility","visible");
	$("#avatar_link").removeAttr("href");
	
	$("#name textarea").val("");
	$("#steamid textarea").val("");
	$("#steamid64 textarea").val("");
	$("#uid textarea").val("");
	$("#links_steam").val("");
	$("#links_sf").val("");
	$("#links_se").val("");
	$("#links_here").val("");
	$("#links_steamrep").val("");
	
	$("#playerbg").css("background-image","");
	$("#playerbg").stop().animate({
		"opacity": 0,
	},250);
	
	if ($("#input").val() == "") {$("#loading").attr("src","");return;}
	
	$.getJSON("/raw.php?input=" + $("#input").val(),function(htmlstuff) {
		if (htmlstuff.error) {
			$("#interpreted")[0].innerHTML = htmlstuff.error;
			$("#loading").attr("src","");
			$("#loading").css("visibility","hidden");
			$("#avatar_link").removeAttr("href");
		} else {
			if (htmlstuff.steamid64 === undefined) {
				$("#interpreted")[0].innerHTML = "Invalid profile.";
				$("#loading").attr("src","");
				$("#loading").css("visibility","hidden");
				$("#avatar_link").removeAttr("href");
			} else {
				if (curgetbg != undefined) {
					curgetbg.abort();
					curgetbg = undefined;
				}
				curgetbg = $.get("/background.php?steamid64=" + htmlstuff.steamid64,function(bg) {
					if (bg == "") {return;}
					$("#playerbg").stop();
					$("#playerbg").css("opacity","0");
					$("#playerbg").css("background-image","url(" + bg + ")");
					$("#playerbg").animate({
						"opacity": 1,
					},250);
				});
				$("#interpreted")[0].innerHTML = "Interpreted as " + htmlstuff.interpreted;
				$("#loading").attr("src",htmlstuff.avatar);
				$("#loading").css("visibility","visible");
				$("#avatar_link").attr("href","http://steamcommunity.com/profiles/" + htmlstuff.steamid64);
				$("#name textarea").val(htmlstuff.name);
				$("#steamid textarea").val(htmlstuff.steamid);
				$("#steamid64 textarea").val(htmlstuff.steamid64);
				$("#uid textarea").val("[U:1:" + htmlstuff.uid + "]");
				
				$("#links_steam").val("http://steamcommunity.com/profiles/" + htmlstuff.steamid64);
				$("#links_steam").parent().attr("href",$("#links_steam").val());
				
				$("#links_sf").val("http://scriptfodder.com/users/view/" + htmlstuff.steamid64);
				$("#links_sf").parent().attr("href",$("#links_sf").val());
				
				$("#links_se").val("http://scriptenforcer.net/users/view/" + htmlstuff.steamid64);
				$("#links_se").parent().attr("href",$("#links_se").val());
				
				$("#links_here").val("http://steamid.billyvenner.xyz/?q=" + htmlstuff.steamid64);
				
				$("#links_steamrep").val("http://steamrep.com/search?q=" + htmlstuff.steamid64);
				$("#links_steamrep").parent().attr("href",$("#links_steamrep").val());
				
				window.history.pushState("Lookup for " + htmlstuff.steamid64,"Lookup for " + htmlstuff.steamid64,"/?q=" + htmlstuff.steamid64);
			}
		}
	});
});