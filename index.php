<?php
	$db = new mysqli("localhost","steamidconvert",null,"steamidconvert");
	
	$db -> query("UPDATE `views` SET `views` = `views` + 1");
	$views = $db -> query("SELECT * FROM `views`") -> fetch_array();
	$views = $views["views"];

	$steamid = "76561198040894045";
	$s = "";
	if (isset($_GET["q"])) {
		$_GET["q"] = urldecode($_GET["q"]);
		$_GET["q"] = preg_replace("/\"/","\"",$_GET["q"]);
		$steamid = $_GET["q"];
		$s = "$('#convert').click()";
	} else {
		$q = $db -> query("SELECT `input` FROM `saves` WHERE `ip`='".$db->real_escape_string($_SERVER["REMOTE_ADDR"])."'");
		while($row = $q -> fetch_assoc()) {
			$steamid = str_replace('"','\\"',$row["input"]);
		}
		$s = "$('#convert').click()";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>SteamID Converter</title>
		<link rel="stylesheet" type="text/css" href="/assets/css/steamid_convert.css">
		<script src="/assets/js/lib/jquery-2.2.0.min.js" type="text/javascript"></script>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body><div id="playerbg"></div><div id="container"><div id="container_2">
		<input type="text" id="input" value="<?php echo(htmlentities($steamid)); ?>" onkeyup="return on_enter(event)" style="width: 275px; border-right: 0px" placeholder="SteamID, SteamID64 or community URL"><button id="convert">Convert</button><br><br>
		<table id="output">
			<tr>
				<th colspan="2" id="interpreted">Enter above</th>
			</tr>
			<tr>
				<td style="text-align: center" colspan="2"><a id="avatar_link" target="_blank"><img id="loading" style="height: 184px"/></a></td>
			</tr>
			<tr>
				<td>Name</td>
				<td id="name"><textarea class="select-this"></textarea></td>
			</tr>
			<tr>
				<td>SteamID</td>
				<td id="steamid"><textarea class="select-this"></textarea></td>
			</tr>
			<tr>
				<td>SteamID64</td>
				<td id="steamid64"><textarea class="select-this"></textarea></td>
			</tr>
			<tr>
				<td>UserID</td>
				<td id="uid"><textarea class="select-this"></textarea></td>
			</tr>
		</table>
		
		<br><br>
		
		<table id="links">
			<tr><th colspan="2">Permalinks</th></tr>
			<tr><td>Steam</td><td><a target="_blank"><textarea class="select-this pointer" id="links_steam"></textarea></a></td></tr>
			<tr><td>ScriptFodder</td><td><a target="_blank"><textarea class="select-this pointer" id="links_sf"></textarea></a></td></tr>
			<tr><td>ScriptEnforcer</td><td><a target="_blank"><textarea class="select-this pointer" id="links_se"></textarea></a></td></tr>
			<tr><td>SteamRep</td><td><a target="_blank"><textarea class="select-this pointer" id="links_steamrep"></textarea></a></td></tr>
			<tr><td>Here</td><td><textarea class="select-this" id="links_here"></textarea></td></tr>
		</table>
		
		<script src="/assets/js/steamid_convert.js" type="text/javascript"></script>
		<script><?php echo($s); ?></script>
		
		<div id="anchor">
			<a rel="bookmark" href='javascript:var matches = /(?:id|users\/view|profiles|search)(?:\/|\?q=)(.*?)\/?$/g.exec(window.location); if (matches != undefined) {window.open("http://steamid.venner.io/?q=" + matches[1]);} else {alert("Could not find information in URL!");}'>SteamID Convert</a>
			<div class="spacer"></div>
			Drag/add the link above to your bookmarks for a bookmarklet!
			<div class="spacer"></div>
			steamid.venner.io
			<div class="spacer"></div>
			Just use Ctrl/⌘ + C to copy with your mouse hovered over the info.
			<div class="spacer"></div>
			<?php echo(number_format($views)); ?> visits :)
		</div>
	</div></div>
	
		<a href="https://github.com/WilliamVenner/steamid.venner.io" target="_blank" id="github"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/a6677b08c955af8400f44c6298f40e7d19cc5b2d/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f677261795f3664366436642e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_gray_6d6d6d.png"></a>
	
	</body>
</html>
