<?php 
$connec = mysqli_connect($db_config["db_host"], $db_config["db_username"], $db_config["db_password"], $db_config["db_name"]);
if(!$connec){
	die($lang["catfailuredb"] . mysqli_error());
}

function &md_connect_database(){
	global $connec;
	return $connec;
}

function md_register($input){
	global $lang;
	global $config;
	if ($input["security"] != "kakkis") return $lang["wrongseccode"];
	if ($input["password"] != $input["password2"]) return $lang["passwordnotmatch"];
	if (!filter_var($input["email"], FILTER_VALIDATE_EMAIL)) return $lang["wrongemail"];
	
	$connec = md_connect_database();
	if (mysqli_num_rows(mysqli_query($connec, "select username from users where username = '" . $input["username"] . "'")) > 0) return $lang["alreadyusername"];
	if (mysqli_num_rows(mysqli_query($connec, "select email from users where email = '" . $input["email"] . "'")) > 0) return $lang["alreadyemail"];
	
	if (mysqli_query($connec, "insert into users (username, passwrd, email, accounttype, lang, description, nickname, emailexist, imageexist) values ('" . $input["username"] . "', '" . hash("sha256", $input["password"]) . "', '" . $input["email"] . "', 2, '" . $input["lang"] . "', ' ', ' ', 0, 0);")) {
	$ooo = mysqli_insert_id($connec);
	$keything = mt_rand(1,999999999);
    if (mysqli_query($connec, "insert into email (id, emailkey) values (" . $ooo . ", " . $keything . ");")) {
	if ($config["sendemail"]) mail($input["email"], $lang["emailsubject"], $lang["emailplease"] . ' <a href="' . $lang["location"] .'/' . $keything . '/">' . $lang["location"] .'/' . $keything . '/</a>', '');
	
	$putni = mysqli_query($connec, "select regions.id, cities.location from regions left join cities on regions.id = cities.regionid where regions.citycount < 7 order by abs(cast(regions.x as signed) - " . (($config["worldwidth"]/2) + mt_rand(0, $config["citydispersal"])) . "), abs(cast(regions.y as signed) - " . (($config["worldheigth"]/2) + mt_rand(-$config["citydispersal"], $config["citydispersal"])) .") desc limit 10");
	$founding = false;
	while($iii = mysqli_fetch_assoc($putni)){
		if (($founding == false) && ($iii["location"] == null)){
			$location = 6;
			$region = $iii["id"];
			break;
		}
		$founding = true;
		if ($iii["location"] == null){
			for ($i = 1; $i < 7; $i++){
				if (in_array($i, $usedlocations)) continue;
				$location = $i;
				break;
			}
			break;
		}
		$usedlocations[] = $iii["location"];
		$region = $iii["id"];
	}
	
	
	md_create_city($ooo, $region, $location, "Pilsēta");
	
	mysqli_query($connec, "insert into messages (recipient, sender, subject, messagetext, seen, senddate) values (" . $ooo . ", 1, 'daudz olas', '" . $lang["welcomemessage"] . "', 0, FROM_UNIXTIME(" . time() . "))");
	
	return $lang["registrationsucc"];
	} else {
    return $lang["catfailure"] . mysqli_error($connec);
	}
	
	} else {
    return $lang["catfailure"] . mysqli_error($connec);
	}
}

function md_login($input){
	global $lang;
	$connec = md_connect_database();
	if ($loginquery = mysqli_query($connec, "select id, passwrd, lang from users where email = '" . $input["username"] . "' or username = '" . $input["username"] . "'")) {
	if (mysqli_num_rows($loginquery) == 0) return $lang["incorectemail"];
	if (mysqli_num_rows($loginquery) > 1) return $lang["catfailure"];
	$row = mysqli_fetch_assoc($loginquery);
	if (hash("sha256", $input["password"]) != $row["passwrd"]) return $lang["incorectpassword"];
	$_SESSION["pl_id"] = $row["id"];
	$_SESSION["lang"] = $row["lang"];
	return $lang["passwordaccept"];
	
	} else {
    return $lang["catfailure"] . mysqli_error($connec);
	}
}

function md_logout(){
	global $lang;
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}
	session_destroy();
	return $lang["logingouts"];
}

function md_change_lang($lang){
	$_SESSION["lang"] = $lang;
}

function md_getcity($citystuff){
	$connec = md_connect_database();
	if (isset($_SESSION["current_city"])){
		$city = mysqli_fetch_assoc(mysqli_query($connec, "select id, userid from cities where id = '" . $_SESSION["current_city"] . "'"));
		if ($city["userid"] != $_SESSION["pl_id"]) return array();
	} else {
		$city = mysqli_fetch_assoc(mysqli_query($connec, "select id from cities where userid = '" . $_SESSION["pl_id"] . "'"));
		$_SESSION["current_city"] = $city["id"];
	}
	
	$iiiuuuiii = mysqli_query($connec, "select buildings.id as id, buildings.buildingtype as buildingtype, buildings.location as location, events.endtime as eventtype from buildings left join events on buildings.id = events.options and events.eventtype = 5 where buildings.cityid = '" . $city["id"] . "'");
	while($building = mysqli_fetch_assoc($iiiuuuiii)) {
        $buildings[$building["location"]] = array($building["id"], $building["buildingtype"], $building["eventtype"]);
    }
	for ($i = 1; $i < 7; $i++) if (!isset($buildings[$i])) $buildings[$i] = array("0", "0", null);
	return $buildings;
}

function md_getregion($gettings){
	$connec = md_connect_database();
	if (!isset($_SESSION["current_city"])) return array();
	$region = array();
	if (isset($gettings["id"])){
		$iiiuuuiii = mysqli_query($connec, "select cities.id, cities.userid, cities.location, cities.name, users.nickname, users.username, cities.regionid from cities join users on cities.userid = users.id where regionid = " . explode ("p", $gettings["id"])[0] ." order by cities.location");
	} else {
		$iiiuuuiii = mysqli_query($connec, "select cities.id, cities.userid, cities.location, cities.name, users.nickname, users.username, cities.regionid from cities join users on cities.userid = users.id where regionid = (select regionid from cities where id = " . $_SESSION["current_city"] . " limit 1) order by cities.location");
	}
	while($aaa = mysqli_fetch_assoc($iiiuuuiii)){
		$region[$aaa["location"]] = $aaa;
	};
	return $region;
}

function md_getworld(){
	$connec = md_connect_database();
	$world = array();
	$iiiuuuiii = mysqli_query($connec, "select * from regions");
	while($aaa = mysqli_fetch_assoc($iiiuuuiii)){
		$region[$aaa["x"]][$aaa["y"]] = array("id" => $aaa["id"], "type" => $aaa["regiontype"], "cities" => $aaa["citycount"]);
	}
	return $region;
}

function md_getresources($limit = 255){
		$connec = md_connect_database();
		if (isset($_SESSION["current_city"])){
			$resourcequery = mysqli_query($connec, "select resourcetype, ammount from resources where cityid = '" . $_SESSION["current_city"] . "' order by resourcetype asc limit " . $limit);
		} else {
			$city = mysqli_fetch_assoc(mysqli_query($connec, "select id from cities where userid = '" . $_SESSION["pl_id"] . "'"));
			$_SESSION["current_city"] = $city["id"];
			$resourcequery = mysqli_query($connec, "select resourcetype, ammount from resources where cityid = '" . $city["id"] . "' order by resourcetype asc limit " . $limit);
		}
	
	while($iiiuuuiii = mysqli_fetch_assoc($resourcequery)) {
        $resources[$iiiuuuiii["resourcetype"]] = $iiiuuuiii["ammount"];
    }
	return $resources;
}

function md_changecity($postage){
	$connec = md_connect_database();
	if (isset($_SESSION["current_city"])){
		$city = mysqli_fetch_assoc(mysqli_query($connec, "select id, userid from cities where id = '" . $postage["city"] . "'"));
		if ($city["userid"] != $_SESSION["pl_id"]) return false;
			else $_SESSION["current_city"] = $city["id"];
	} else {
		$city = mysqli_fetch_assoc(mysqli_query($connec, "select id from cities where userid = '" . $_SESSION["pl_id"] . "'"));
		$_SESSION["current_city"] = $city["id"];
	}
	return true;
}


function md_getcitylist(){
	$connec = md_connect_database();
	$iiiuuuiii = mysqli_query($connec, "select id, name from cities where userid = '" . $_SESSION["pl_id"] . "'");
	$peteris[0] = null;
	while($janis = mysqli_fetch_assoc($iiiuuuiii)){
		if ($janis["id"] == $_SESSION["current_city"])
			$peteris[0] = $janis;
		else
			$peteris[] = $janis;
    }
	return $peteris;
}

function md_unset_city(){
	global $lang;
	unset($_SESSION["current_city"]);
	return $lang["nothapsendhelp"];
}

function md_getstats(){
	global $config;
	return md_getresources($config["mainrescount"]);
}

function md_building_access($gettings){
	$connec = md_connect_database();
	$query = mysqli_fetch_assoc(mysqli_query($connec, "select cities.userid from buildings inner join cities on cities.id = buildings.cityid where buildings.id = '" . $gettings["id"] . "'"));
	if ($_SESSION["pl_id"] == $query["userid"]) return true; else return false;
}

function md_building_info($gettings){
	return mysqli_fetch_assoc(mysqli_query(md_connect_database(), "select buildings.id as id, buildings.cityid as cityid, buildings.buildingtype as buildingtype, buildings.buildinglevel as buildinglevel, buildings.buildingnumber as buildingnumber, buildings.location as location, events.eventtype as eventtype from buildings left join events on buildings.id = events.options where buildings.id = '" . $gettings . "'"));
}

function md_user_info($gettings){
	return mysqli_fetch_assoc(mysqli_query(md_connect_database(), "select * from users where id = " . $gettings));
}

function md_addresource($cityid, $resourcetype, $ammount){
	mysqli_query(md_connect_database(), "insert into resources (cityid, resourcetype, ammount) values (" . $cityid . ", " . $resourcetype . ", " . $ammount . ") on duplicate key update ammount = ammount + values(ammount)");
}

function md_addnotification($recipient, $message){
	mysqli_query(md_connect_database(), "insert into notifications (recipient, message, seen, senddate) values (" . $recipient . ", '" . $message . "', 0, FROM_UNIXTIME(" . time() . "))");
}

function md_create_city($userid, $regionid, $location, $name){
	global $config;
	$connec = md_connect_database();
	mysqli_query($connec, "insert into cities (regionid, userid, location, name) values (" . $regionid . ", " . $userid . ", " . $location . ", '" . $name . "')");
	$cityid = mysqli_insert_id($connec);
	mysqli_query($connec, "insert into resources (cityid, resourcetype, ammount) values (" . $cityid . ", 1, " . $config["newcityres1lvl1"] . "), (" . $cityid . ", 2, " . $config["newcityres2lvl1"] . "), (" . $cityid . ", 3, " . $config["newcityres3lvl1"] . "), (" . $cityid . ", 4, " . $config["newcityres4lvl1"] . "), (" . $cityid . ", 5, " . $config["newcityres5lvl1"] . "), (" . $cityid . ", 6, " . $config["newcityres6lvl1"] . "), (" . $cityid . ", 7, " . $config["newcityres7lvl1"] . "), (" . $cityid . ", 8, " . $config["newcityres8lvl1"] . ")");
	mysqli_query($connec, "insert into buildings (cityid, buildingtype, buildinglevel, buildingnumber, location) values (" . $cityid . ", 1, 1, 0, 1)");
	mysqli_query($connec, "update regions join (select regionid, count(regionid) as olas from cities group by regionid) as janis on janis.regionid = regions.id set citycount = janis.olas where regions.id = janis.regionid");
	return $cityid;
}

function md_getmessages($userid){
	$connec = md_connect_database();
	$query = mysqli_query($connec, "select messages.*, users.username from messages join users on users.id = messages.sender where recipient = " . $userid . " order by messages.senddate desc");
	$messages = array();
	while ($janispeterispirmais = mysqli_fetch_assoc($query)){
		$messages[] = $janispeterispirmais;
	}
	return $messages;
}

function md_getmessage($messageid){
	$connec = md_connect_database();
	$message = mysqli_fetch_assoc(mysqli_query($connec, "select messages.*, recipient.username as 'you', sender.username from messages join users sender on sender.id = messages.sender join users recipient on recipient.id = messages.recipient where messages.id = " . $messageid . " and messages.recipient = " . $_SESSION["pl_id"]));
	mysqli_query($connec, "update messages set seen = 1 where messages.id = " . $messageid . " and messages.recipient = " . $_SESSION["pl_id"]);
	return $message;
}

function md_getnotifications($userid){
	$connec = md_connect_database();
	$query = mysqli_query($connec, "select * from notifications where recipient = " . $userid . " order by senddate desc");
	$messages = array();
	while ($janispeterispirmais = mysqli_fetch_assoc($query)){
		$messages[] = $janispeterispirmais;
	}
	mysqli_query($connec, "update notifications set seen = 1 where recipient = " . $userid);
	return $messages;
}

function md_sendmessage($recipient, $sender, $subject, $messagetext){
	global $lang;
	$e = md_connect_database();
	mysqli_query($e, "insert into messages (recipient, sender, subject, messagetext, seen, senddate) values (" . $recipient . ", " . $sender . ", '" . $subject . "', '" . $messagetext ."', 0, FROM_UNIXTIME(" . time() . "))");
	return $lang["sented"];
}

function md_addevent($eventtype, $origin, $destination, $starttime, $endtime, $options = "NULL"){
	$connec = md_connect_database();
	mysqli_query($connec, "insert into events (eventtype, origin, destination, starttime, endtime, options) values (" . $eventtype . ", " . $origin . ", " . $destination . ", FROM_UNIXTIME(" . $starttime . "), FROM_UNIXTIME(" . $endtime . "), '" . $options . "')");
	return mysqli_insert_id($connec);
}

function md_build_building($level, $buildingtype, $location, $city, $buildingid = 0){
	global $lang;
	global $config;
	
	$buildable = true;
	$resources = md_getresources($config["rescount"]);
	for ($i = 1; $i <= $config["building" . $buildingtype . "rescount"]; $i++)
		if (($config["building" . $buildingtype . "res" . $i . "lvl" . $level] * $config["buildingpricemodifier"]) > $resources[$config["building" . $buildingtype . "res" . $i]])
			$buildable = false;
	if ($buildable == false) return $lang["notenoughres"];
	
	for ($i = 1; $i <= $config["building" . $buildingtype . "rescount"]; $i++)
		md_addresource($city, $config["building" . $buildingtype . "res" . $i], -($config["building" . $buildingtype . "res" . $i . "lvl" . $level] * $config["buildingpricemodifier"]));
	
	if ($level == 1){
		$connec = md_connect_database();
		mysqli_query($connec, "insert into buildings (cityid, buildingtype, buildinglevel, location) values(" . $city . ", " . $buildingtype . ", 0, " . $location . ")");
		$buildingid = mysqli_insert_id($connec);
	}
	
	md_addevent(5, $city, $city, time(), time() + ($config["building" . $buildingtype . "timelvl" . $level] * $config["timemodifier"]) / $config["ticktime"], $buildingid);
	
	return "EEE";
}

function md_unbuild_building($level, $buildingtype, $location, $city, $buildingid){
	global $lang;
	global $config;
	for ($i = 1; $i <= $config["building" . $buildingtype . "rescount"]; $i++)
		md_addresource($city, $config["building" . $buildingtype . "res" . $i], ($config["building" . $buildingtype . "res" . $i . "lvl" . $level] * $config["buildingpricemodifier"]) / 2);
	
	$connec = md_connect_database();
	
	if ($level == 1)
		mysqli_query($connec, "delete from buildings where id = " . $buildingid);
	else
		mysqli_query($connec, "update buildings set buildinglevel = buildinglevel - 1 where id = " . $buildingid);
	
	return $lang["largeeee"];
}

function md_resource_generation($janis, $peteris, $martins){
	global $lang;
	global $config;
	$connec = md_connect_database();
	$buildinginfo = md_building_info($janis);
	$resources = md_getresources($config["mainrescount"]);
	if ($buildinginfo["buildingnumber"] == $peteris) return;
	$ammount = $buildinginfo["buildingnumber"] - $peteris;
	if ($ammount + $resources[8] < 0) return $lang["notenoughcats"];
	mysqli_query($connec, "insert into resource_generation (buildingid, resourcetype, ammount) values (" . $janis . ", " . $martins . ", " . ($peteris * $config["res" . $martins . "productivity"] / $config["timemodifier"] / $config["ticktime"]) . ") on duplicate key update ammount = values(ammount)");
	if ($martins == 8){
		mysqli_query($connec, "insert into resource_generation (buildingid, resourcetype, ammount) values (" . $janis . ", 6, " . -($peteris * $config["res" . $martins . "productivity"] / $config["timemodifier"] / $config["ticktime"])*4 . ") on duplicate key update ammount = values(ammount)");
	} elseif ($martins == 6){
		mysqli_query($connec, "insert into resource_generation (buildingid, resourcetype, ammount) values (" . $janis . ", 5, " . -($peteris * $config["res" . $martins . "productivity"] / $config["timemodifier"] / $config["ticktime"])*4 . ") on duplicate key update ammount = values(ammount)");
	}
	mysqli_query($connec, "update buildings set buildingnumber = " . $peteris . " where id = " . $buildinginfo["id"]);
	md_addresource($buildinginfo["cityid"], 8, $ammount);
	return "ok";
}

function md_resource_creation($buildingid, $ammount, $resource){
	global $lang;
	global $config;
	$connec = md_connect_database();
	$citystuff = mysqli_fetch_assoc(mysqli_query($connec, "select * from cities join buildings on cities.id = buildings.cityid where buildings.id = " . $buildingid));
	
	$buildable = true;
	$resources = md_getresources($config["rescount"]);
	for ($i = 1; $i <= $config["res" . $resource . "rescount"]; $i++)
		if (($config["res" . $resource . "res" . $i . "count"] * $config["creationpricemodifier"] * $ammount) > $resources[$config["res" . $resource . "res" . $i]])
			$buildable = false;
	if ($buildable == false) return $lang["notenoughres"];
	
	for ($i = 1; $i <= $config["res" . $resource . "rescount"]; $i++)
		md_addresource($citystuff["cityid"], $config["res" . $resource . "res" . $i], -($config["res" . $resource . "res" . $i . "count"] * $config["creationpricemodifier"] * $ammount));
	
	$event = md_addevent(10, $citystuff["cityid"], $citystuff["cityid"], time(), time() + ($config["res" . $resource . "time"] * $config["timemodifier"] * $ammount), $options = "NULL");
	mysqli_query($connec, "insert into event_resources (eventid, resourcetype, ammount) values (" . $event . ", " . $resource . ", " . $ammount . ")");
}

function md_gettraveltime($city1id, $city2id){
	global $config;
	$connec = md_connect_database();
	$city1 = mysqli_fetch_assoc(mysqli_query($connec, "select regions.x, regions.y from cities join regions on cities.regionid = regions.id where cities.id = " . $city1id));
	$city2 = mysqli_fetch_assoc(mysqli_query($connec, "select regions.x, regions.y from cities join regions on cities.regionid = regions.id where cities.id = " . $city2id));
	$traveltime = (sqrt(pow($city1["x"] - $city2["x"], 2) + pow($city1["y"] - $city2["y"], 2)) + 1) * $config["shippingspeed"] * $config["timemodifier"];
	return $traveltime;
}

function md_send_resource($ola){
	global $config;
	$connec = md_connect_database();
	$traveltime = md_gettraveltime($_SESSION["current_city"], $ola["recipient"]);
	$eee = md_addevent(6, $_SESSION["current_city"], $ola["recipient"], time(), time() + $traveltime, $options = "NULL");
	array_pop($ola);
	foreach($ola as $kak => $kakola){
		if ($kakola == 0) continue;
		mysqli_query($connec, "insert into event_resources (eventid, resourcetype, ammount) values (" . $eee . ", " . substr($kak, 7) . ", " . $kakola . ")");
	}
	foreach($ola as $kak => $kakola){
		if ($kakola == 0) continue;
		md_addresource($_SESSION["current_city"], substr($kak, 7), -$kakola);
	}
}

function md_send_units($ola){
	global $config;
	$connec = md_connect_database();
	$traveltime = md_gettraveltime($_SESSION["current_city"], $ola["recipient"]);
	$recipient = $ola["recipient"];
	$strenght = 0;
	array_pop($ola);
	foreach($ola as $kak => $kakola){
		if ($kakola == 0) continue;
		$strenght += $config["res" . substr($kak, 7) . "str"] * $kakola;
	}
	$eee = md_addevent(8, $_SESSION["current_city"], $recipient, time(), time() + $traveltime, $strenght);
	foreach($ola as $kak => $kakola){
		if ($kakola == 0) continue;
		mysqli_query($connec, "insert into event_resources (eventid, resourcetype, ammount) values (" . $eee . ", " . substr($kak, 7) . ", " . $kakola . ")");
	}
	foreach($ola as $kak => $kakola){
		if ($kakola == 0) continue;
		md_addresource($_SESSION["current_city"], substr($kak, 7), -$kakola);
	}
}

function md_getevents(){
	$connec = md_connect_database();
	$events = array();
	$janis = mysqli_query($connec, "select eventings.*, origins.name as originname, destinations.name as destinationname from (select * from events where events.destination = " . $_SESSION["current_city"] . " or events.origin = " . $_SESSION["current_city"] . ") as eventings join cities as origins on origins.id = eventings.origin join cities as destinations on destinations.id = eventings.destination");
	while($bepis = mysqli_fetch_assoc($janis))
		$events[] = $bepis;
	return $events;
}

function md_build_city($uwu){
	global $lang;
	global $config;
	
	$buildable = true;
	$resources = md_getresources($config["rescount"]);
	for ($i = 1; $i <= $config["newcityrescount"]; $i++)
		if (($config["newcityres" . $i . "lvl1"]) > $resources[$config["newcityres" . $i]])
			$buildable = false;
	if ($buildable == false) return $lang["notenoughres"];
	
	for ($i = 1; $i <= $config["newcityrescount"]; $i++)
		md_addresource($_SESSION["current_city"], $config["newcityres" . $i], -($config["newcityres" . $i . "lvl1"]));
	$aaa = md_create_city(1, $uwu[0], $uwu[1], "-");
	md_addevent(7, $_SESSION["current_city"], $aaa, time(), time() + md_gettraveltime($aaa, $_SESSION["current_city"]), $_SESSION["pl_id"]);
	return $lang["uncertainhappen"];
}

function md_getuseroptions(){
	return mysqli_fetch_assoc(mysqli_query(md_connect_database(), "select * from users where id = " . $_SESSION["pl_id"]));
}

function md_accept_email($code){
	global $lang;
	$connec = md_connect_database();
	$user = mysqli_fetch_assoc(mysqli_query($connec, "select * from email join users on users.id = email.id where emailkey = " . $code));
	mysqli_query($connec, "update users set emailexist = 1 where id = " . $user["id"]);
	mysqli_query($connec, "delete from email where emailkey = " . $code);
	return $lang["emailconfirm"];
}

function md_setoptions($options, $files){
	global $lang;
	$connec = md_connect_database();
	if ($options["type"] == "options"){
		mysqli_query($connec, "update users set username = '" . $options["username"] ."', email = '" . $options["email"] ."', description = '" . $options["description"] ."', nickname = '" . $options["nickname"] ."', lang = '" . $options["lang"] ."' where id = " . $_SESSION["pl_id"]);
		$_SESSION["lang"] = $options["lang"];
		return "ok";
	} elseif ($options["type"] == "password"){
		$password = mysqli_fetch_assoc(mysqli_query($connec, "select passwrd from users where id = " . $_SESSION["pl_id"]));
	if(hash("sha256", $options["oldpassword"]) == $password["passwrd"]){
			mysqli_query($connec, "update users set passwrd = '" . hash("sha256", $options["newpassword"]) ."' where id = " . $_SESSION["pl_id"]);
			return $lang["passwordputin"];
		} else
			return $lang["passwordnewnomatch"];
	} elseif ($options["type"] == "cityname"){
		if ($options["name"] != "-"){
			mysqli_query($connec, "update cities join buildings on cities.id = buildings.cityid set cities.name = '" . $options["name"] ."' where buildings.id = " . $options["buildingid"]);
			return $lang["putinsmoll"];
		} else {
			return $lang["othername"];
		}
	} elseif ($options["type"] == "userimage"){
		$target_dir = "images/userimages/";
		$target_file = $target_dir . $_SESSION["pl_id"] . "." . strtolower(pathinfo(basename($_FILES["failis"]["name"]),PATHINFO_EXTENSION));
		$uploadings = 1;
		$imagetyping = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["failis"]["tmp_name"]);
			if($check !== false) {
				$uploadings = 1;
			} else {
				return $lang["pictureproblem"];
				$uploadings = 0;
			}
		}
		if ($_FILES["failis"]["size"] > 50000) {
			return $lang["picturetoobig"];
			$uploadings = 0;
		}
		if ($imagetyping != "jpg" && $imagetyping != "png" && $imagetyping != "jpeg"
		&& $imagetyping != "gif"){
			return $lang["picturewrongtype"];
			$uploadings = 0;
		}
		if ($uploadings == 0) {
			return $lang["catfailure"];
		} else {
			if (move_uploaded_file($_FILES["failis"]["tmp_name"], $target_file)) {
				if ($imagetyping != "gif"){
					if ($imagetyping == "jpg"){
						$image = imagecreatefromjpeg($target_file);
					} else {
						$image = imagecreatefrompng($target_file);
					}
				}
				imagetruecolortopalette($image, true, 64);
				imagegif($image, $target_dir . $_SESSION["pl_id"] . ".gif");
				mysqli_query($connec, "update users set imageexist = 1 where id = " . $_SESSION["pl_id"]);
				
				
			} else {
				return $lang["catfailure"];
			}
		}
	}
	
	return $lang["notunderstanding"];
}

function md_delete_user($delet){
	global $lang;
	global $config;
	$connec = md_connect_database();
	if ($config["bepisnumber"] == $_SESSION["pl_id"]){
		mysqli_query($connec, "delete from users where id = " . $delet);
		return $lang["deletedet"];
	} else {
		return $lang["sometingwong"];
	}
	
}

function md_delete_city($delet){
	global $lang;
	global $config;
	$connec = md_connect_database();
	$user = mysqli_fetch_assoc(mysqli_query($connec, "select userid from cities where id = " . $delet));
	if ($config["bepisnumber"] == $_SESSION["pl_id"] || $user["userid"] == $_SESSION["pl_id"]){
		mysqli_query($connec, "delete from cities where id = " . $delet);
		mysqli_query($connec, "update regions join (select regionid, count(regionid) as olas from cities group by regionid) as janis on janis.regionid = regions.id set citycount = janis.olas where regions.id = janis.regionid");
		return $lang["deletedet"];
	} else {
		return $lang["sometingwong"];
	}
	
}

function md_do_events(){
	global $lang;
	global $config;
	$connec = md_connect_database();
	$time = mysqli_fetch_row(mysqli_query($connec, "select * from timepassed"));
	if ($time[0] + $config["ticktime"] > time()) return;
	mysqli_query($connec, "update timepassed set lasttime = " . time());
	$timesincecheck = (time() - $time[0])/100;
	$time = time();
	$events = mysqli_query($connec, "select * from events left join event_resources on event_resources.eventid = events.id where events.endtime < FROM_UNIXTIME(" . $time . ") order by events.endtime asc");
	$already = 0;
	$already2 = 0;
	while ($event = mysqli_fetch_assoc($events))
		switch($event["eventtype"]){
			case 0:
				break;
			case 5:
				mysqli_query($connec, "update buildings set buildinglevel = buildinglevel + 1 where id = " . $event["options"]);
				break;
			case 6:
				$aaa = mysqli_fetch_assoc(mysqli_query($connec, "select userid from cities where id = ".$event["destination"]. " limit 1"));
				$eee = mysqli_fetch_assoc(mysqli_query($connec, "select userid from cities where id = ".$event["origin"]. " limit 1"));
				md_addnotification($aaa["userid"], $lang["newcargoarriv"]);
				md_addnotification($eee["userid"], $lang["newcargosent"]);
				md_addresource($event["destination"], $event["resourcetype"], $event["ammount"]);
				break;
			case 7:
				mysqli_query($connec, "update cities set userid = " . $event["options"] . ", name = 'Pilsēta' where id = " . $event["destination"]);
				md_addnotification($event["options"], $lang["newcitybuilt"]);
				break;
			case 8:
				if ($already == $event["id"]){
					mysqli_query($connec, "insert into event_resources (eventid, resourcetype, ammount) values (" . $already2 . ", " . $event["resourcetype"] . ", " . $event["ammount"] . ")");
					break;
				}
				$already = $event["id"];
				$aaa = mysqli_fetch_assoc(mysqli_query($connec, "select userid from cities where id = ".$event["destination"]. " limit 1"));
				$eee = mysqli_fetch_assoc(mysqli_query($connec, "select userid from cities where id = ".$event["origin"]. " limit 1"));
				$resursequery = mysqli_query($connec, "select resourcetype, ammount from resources where cityid = '" . $event["destination"] . "' order by resourcetype asc");
				while($iiiuuuiii = mysqli_fetch_assoc($resursequery)) {
					$resurse[$iiiuuuiii["resourcetype"]] = $iiiuuuiii["ammount"];
				}
				$strenght = 0;
				$resamounts = 0;
				foreach($resurse as $kak => $ola){
					if ($kak < $config["res2start"]){
						$resamounts++;
						continue;
					}
					$strenght += $config["res" . $kak . "str"] * $ola;
				}
				if ($strenght > $event["options"]){
					md_addnotification($aaa["userid"], $lang["attackrepeled"]);
					md_addnotification($eee["userid"], $lang["attacklost"]);
				} else {
					$traveltime = md_gettraveltime($event["destination"], $event["origin"]);
					$uuu = md_addevent(9, $event["destination"], $event["origin"], time(), time() + $traveltime, $options = "NULL");
					$aaa11 = floor($event["options"] / $resamounts);
					foreach($resurse as $kak => $ola){
						if ($kak > $config["res2start"]){
							md_addresource($event["destination"], $kak, -$ola);
							continue;
						}
						if ($ola < $aaa11){
							mysqli_query($connec, "insert into event_resources (eventid, resourcetype, ammount) values (" . $uuu . ", " . $kak . ", " . $ola . ")");
							md_addresource($event["destination"], $kak, -$ola);
						} else {
							mysqli_query($connec, "insert into event_resources (eventid, resourcetype, ammount) values (" . $uuu . ", " . $kak . ", " . $aaa11 . ")");
							md_addresource($event["destination"], $kak, -$aaa11);
						}
					}
					mysqli_query($connec, "insert into event_resources (eventid, resourcetype, ammount) values (" . $uuu . ", " . $event["resourcetype"] . ", " . $event["ammount"] . ")");
					$already2 = $uuu;
					
					
					md_addnotification($aaa["userid"], $lang["defencelost"]);
					md_addnotification($eee["userid"], $lang["attacksucc"]);
				}
				break;
			case 9:
				$aaa = mysqli_fetch_assoc(mysqli_query($connec, "select userid from cities where id = ".$event["destination"]. " limit 1"));
				md_addnotification($aaa["userid"], $lang["attackreturn"]);
				md_addresource($event["destination"], $event["resourcetype"], $event["ammount"]);
				break;
			case 10:
				md_addresource($event["destination"], $event["resourcetype"], $event["ammount"]);
				break;
			default:
				echo "ananas";
			
			
			
		}
	
	mysqli_query($connec, "delete from events where events.endtime < FROM_UNIXTIME(" . $time . ")");
	
	for ($i = 1; $i <= $config["rescount"]; $i++){
		mysqli_query($connec, "update resources join (select buildings.cityid, sum(resource_generation.ammount) as ammount from resource_generation join buildings on resource_generation.buildingid = buildings.id where resourcetype = " . $i ." group by buildings.cityid) as janis on janis.cityid = resources.cityid set resources.ammount = resources.ammount + ceiling(janis.ammount * " . $timesincecheck . ") where resources.cityid = janis.cityid and resources.resourcetype = " . $i);
		
		
	}
}
?>