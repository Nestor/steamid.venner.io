<?php
	if (isset($_GET["steamid64"])) {
		$profile = file_get_contents("http://steamcommunity.com/profiles/" . $_GET["steamid64"]);
		if (isset($profile)) {
			preg_match("/url\(\s?'((?:http:)?\/\/cdn\.akamai\.steamstatic\.com\/steamcommunity\/public\/images\/items\/.*\/.*)'\s?\)/",$profile,$matches);
			if (isset($matches)) {
				if (isset($matches[1])) {
					echo($matches[1]);
				}
			}
		}
	}
?>