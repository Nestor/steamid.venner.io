<?php
	$db = new mysqli("localhost","steamidconvert",null,"steamidconvert");
	$apikey = "";

	function toCommunityID($id) {
		if (preg_match("/^STEAM_/",$id)) {
			$parts = explode(":",$id);
			return bcadd(bcadd(bcmul($parts[2],"2"),"76561197960265728"),$parts[1]);
		} elseif (is_numeric($id) && strlen($id) < 16) {
			return bcadd($id,"76561197960265728");
		}
	}
	function toSteamID($id) {
		if (is_numeric($id) && strlen($id) >= 16) {
			$z = bcdiv(bcsub($id,"76561197960265728"),"2");
		} elseif (is_numeric($id)) {
			$z = bcdiv($id,"2");
		}
		$y = bcmod($id, '2');
		return 'STEAM_0:' . $y . ':' . floor($z);
	}
	function toUserID($id) {
		$split = explode(":",$id);
		return $split[2] * 2 + $split[1];
	}
	
	function file_get_json($url) {
		$data = file_get_contents($url);
		return json_decode($data,true);
	}
	function file_get_xml($url) {
		$xml = file_get_contents($url);
		$p = xml_parser_create();
		xml_parse_into_struct($p,$xml,$vals,$index);
		xml_parser_free($p);
		return $vals;
	}
	
	$output = [];
	if (isset($_GET["input"])) {
		$_GET["input"] = urldecode($_GET["input"]);
		
		preg_match("/STEAM_\d:\d:\d+/i",$_GET["input"],$steamid);
		preg_match("/7656119\d+/",$_GET["input"],$steamid64);
		preg_match("/\[U:\d:\d+]/i",$_GET["input"],$uid);
		
		if (count($steamid) > 0) {
			$output["interpreted"] = "SteamID";
			$data = file_get_json("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=" . toCommunityID($_GET["input"]));
			if (isset($data["response"]["players"][0])) {
				$ply = $data["response"]["players"][0];
				$output["avatar"] = $ply["avatarfull"];
				$output["name"] = $ply["personaname"];
				$output["steamid"] = toSteamID($ply["steamid"]);
				$output["steamid64"] = $ply["steamid"];
				$output["uid"] = toUserID(toSteamID($ply["steamid"]));
			}
		} elseif (count($steamid64) > 0) {
			$output["interpreted"] = "SteamID64";
			$data = file_get_json("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=" . $_GET["input"]);
			if (isset($data["response"]["players"][0])) {
				$ply = $data["response"]["players"][0];
				$output["avatar"] = $ply["avatarfull"];
				$output["name"] = $ply["personaname"];
				$output["steamid"] = toSteamID($ply["steamid"]);
				$output["steamid64"] = $ply["steamid"];
				$output["uid"] = toUserID(toSteamID($ply["steamid"]));
			}
		} elseif (count($uid) > 0) {
			$output["interpreted"] = "UserID";
			$_GET["input"] = preg_replace("/\[U:\d:(\d+)]/","$1",$_GET["input"]);
			$data = file_get_json("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=" . toCommunityID($_GET["input"]));
			if (isset($data["response"]["players"][0])) {
				$ply = $data["response"]["players"][0];
				$output["avatar"] = $ply["avatarfull"];
				$output["name"] = $ply["personaname"];
				$output["steamid"] = toSteamID($ply["steamid"]);
				$output["steamid64"] = $ply["steamid"];
				$output["uid"] = toUserID(toSteamID($ply["steamid"]));
			}
		} else {
			$output["interpreted"] = "URL";
			$_GET["input"] = preg_replace("/(https?:\/\/)?steamcommunity\.com\/id\//","",$_GET["input"]);
			
			$vals = file_get_xml("http://steamcommunity.com/id/" . $_GET["input"] . "?xml=1");
			if ($vals[1]["tag"] === "ERROR") {
				$output["error"] = "Profile not found.";
			} else {
				
				$data = file_get_json("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=" . $vals[1]["value"]);
				if (isset($data["response"]["players"][0])) {
					$ply = $data["response"]["players"][0];
					$output["avatar"] = $ply["avatarfull"];
					$output["name"] = $ply["personaname"];
					$output["steamid"] = toSteamID($ply["steamid"]);
					$output["steamid64"] = $ply["steamid"];
					$output["uid"] = toUserID(toSteamID($ply["steamid"]));
				}
				
			}
		}
	
		if (isset($output["steamid64"])) {
			if ($output["steamid64"] != "76561198040894045") {
				$db -> query("INSERT INTO `saves`(`ip`,`input`) VALUES ('".$db->real_escape_string($_SERVER["REMOTE_ADDR"])."','".$db->real_escape_string($output["steamid64"])."') ON DUPLICATE KEY UPDATE `input`='".$db->real_escape_string($output["steamid64"])."'");
			} else {
				$db -> query("DELETE IGNORE FROM `saves` WHERE `ip`='" . $_SERVER["REMOTE_ADDR"] . "'");
			}
		}
	}
	echo(json_encode($output));
?>