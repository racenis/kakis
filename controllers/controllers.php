<?php
function ct_main_page(){
	vw_main_page();
}

function ct_rules(){
	vw_rules();
}

function ct_change_lang($lang){
	md_change_lang($lang);
	header("Location: ../../");
	echo '<a href="../../">sakumins</a>';
	die();
}

function ct_register(){
	vw_loggings(md_register($_POST), "Atpakaļināties");
}

function ct_login(){
	vw_loggings(md_login($_POST), "Turpināties");
}

function ct_logout(){
	vw_loggings(md_logout(), "Atpakaļināties");
}

function ct_view_city(){
	global $lang;
	md_do_events();
	if (isset($_GET["id"]) && $_GET["id"] == "uwu"){
		vw_loggings(md_unset_city());
		die();
	}
	if (isset($_POST["city"])){
		if(!md_changecity($_POST)){
			vw_loggings($lang["notfindcity"], $lang["backtofirstown"], "city/uwu/");
			die();
		}
	}
	$city = md_getcity($_GET);
	$citylist = md_getcitylist();
	if (empty($city))
		vw_loggings($lang["notfindcity"], $lang["backtofirstown"], "city/uwu/");
	else
		vw_city($city, $citylist, $_SESSION);
}

function ct_region(){
	$region = md_getregion($_GET);
	vw_region($region, $_GET);
}

function ct_world(){
	vw_world(md_getworld());
}

function ct_resources(){
	global $config;
	$resources = md_getresources($config["res2start"]);
	if (empty($resources))
		vw_loggings($lang["catfailure"]);
	else
		vw_resources($resources);
}

function ct_units(){
	global $config;
	$resources = md_getresources($config["res2start"] + $config["res2count"]);
	if (empty($resources))
		vw_loggings($lang["catfailure"]);
	else
		vw_resources($resources, 2);
}

function ct_build(){
	if(isset($_POST["buildingtype"])){
		vw_loggings(md_build_building(1, $_POST["buildingtype"], $_POST["location"], $_SESSION["current_city"]));
	} else
	vw_build($_GET, $_SESSION);
}

function ct_upgrade_building(){
		if(isset($_GET["id"])){
		$building = md_building_info($_GET["id"]);
		vw_loggings(md_build_building($building["buildinglevel"] + 1, $building["buildingtype"], $building["location"], $building["cityid"], $building["id"]));
		} else vw_loggings("eeee?");
}

function ct_downgrade_building(){
		if(isset($_GET["id"])){
		$building = md_building_info($_GET["id"]);
		vw_loggings(md_unbuild_building($building["buildinglevel"], $building["buildingtype"], $building["location"], $building["cityid"], $building["id"]));
		} else vw_loggings("eeee?");
}

function ct_resource_generation(){
		if(isset($_POST["buildingid"])){
			vw_loggings(md_resource_generation($_POST["buildingid"], $_POST["ammount"],  $_POST["resource"]));
		} else vw_loggings($lang["eeequest"]);
}

function ct_resource_creation(){
		global $lang;
		if(isset($_POST["buildingid"])){
			vw_loggings(md_resource_creation($_POST["buildingid"], $_POST["ammount"],  $_POST["resource"]));
		} else vw_loggings($lang["eeequest"]);
}

function ct_building(){
	if(md_building_access($_GET)){
		vw_building(md_building_info($_GET["id"]));
	} else {
		vw_loggings($lang["notfindbuilding"]);
	}
	
}

function ct_notifications(){
	vw_notifications(md_getnotifications($_SESSION["pl_id"]));
}

function ct_events(){
	vw_events(md_getevents());
}

function ct_messages(){
	if(isset($_GET["id"])){
		vw_messages(md_getmessage($_GET["id"]), "one");
	} else {
		vw_messages(md_getmessages($_SESSION["pl_id"]));
	}
	
}

function ct_send_message(){
	if (isset($_POST["recipient"])){
		vw_loggings(md_sendmessage($_POST["recipient"], $_SESSION["pl_id"], $_POST["subject"], $_POST["messagetext"]));
	} else {
		vw_messages(md_user_info($_GET["id"]), "three");
	}
}

function ct_send_resource(){
	if (isset($_POST["recipient"])){
		vw_loggings(md_send_resource($_POST));
	} else {
		global $config;
		$resources = md_getresources($config["res2start"]);
		vw_send_resources($resources, $_GET["id"]);
	}
}

function ct_send_units(){
	if (isset($_POST["recipient"])){
		vw_loggings(md_send_units($_POST));
	} else {
		global $config;
		$resources = md_getresources($config["res2start"] + $config["res2count"]);
		vw_send_resources($resources, $_GET["id"], 2);
	}
}

function ct_accept_email(){
	vw_loggings(md_accept_email($_GET["id"]));
}

function ct_build_city(){
	if (isset($_POST["region"]))
		vw_loggings(md_build_city(explode("p", $_GET["id"])));
	else
		vw_build_city(explode("p", $_GET["id"]));
}

function ct_delete_user(){
	vw_loggings(md_delete_user($_GET["id"]));
}

function ct_delete_city(){
	if (isset($_POST["delet"])){
		vw_loggings(md_delete_city($_POST["delet"]));
	} else {
		vw_delete_city($_GET["id"]);
	}
}

function ct_help(){
	if (isset($_GET["id"]))
		vw_help($_GET["id"]);
	else
		vw_help(0);
}

function ct_view_user(){
	if (isset($_GET["id"])){
		vw_view_user(md_user_info($_GET["id"]), $_SESSION["pl_id"]);
	} else {
		vw_loggings($lang["catfailure"]);
	}
}

function ct_options(){
	if (isset($_POST["type"]))
		vw_loggings(md_setoptions($_POST, $_FILES));
	elseif (isset($_GET["id"])){
		vw_options(0, $_GET["id"]);
	} else
	vw_options(md_getuseroptions(), 1);
}
?>