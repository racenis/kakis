<?php
// programma (c) [github.com/racenis] 2019
// neatļauta kopēšana nav atļauta, bet ir piedodama
include "config.php";
include "models/model.php";
include "lang/switch.php";
include "views/views.php";
include "views/mainviews.php";
include "controllers/controllers.php";

if (!(isset($_SESSION["pl_id"]))){
	if ($_SERVER["REQUEST_METHOD"] === "POST"){
		if ($_POST["type"] == "login"){
			ct_login();
		} else if ($_POST["type"] == "register") {
			ct_register();
		}
	} else if (isset ($_GET["view"]) && ($_GET["view"] == "noteikumi")){
		ct_rules();
	} else if (isset ($_GET["view"]) && ($_GET["view"] == "changelanguage")){
		ct_change_lang($_GET["id"]);
	} else if (isset($_GET["view"]) && ($_GET["view"] == "acceptemail")){
		ct_accept_email();
	} else {
		ct_main_page();
	}
} else {
	switch ($_GET["view"]){
		case "city":
			ct_view_city();
			break;
		case "region":
			ct_region();
			break;
		case "world":
			ct_world();
			break;
		case "notifications":
			ct_notifications();
			break;
		case "events":
			ct_events();
			break;
		case "messages":
			ct_messages();
			break;
		case "build":
			ct_build();
			break;
		case "building":
			ct_building();
			break;
		case "resources":
			ct_resources();
			break;
		case "units":
			ct_units();
			break;
		case "resourcegeneration":
			ct_resource_generation();
			break;
		case "resourcecreation":
			ct_resource_creation();
			break;
		case "upgrade":
			ct_upgrade_building();
			break;
		case "downgrade":
			ct_downgrade_building();
			break;
		case "sendmessage":
			ct_send_message();
			break;
		case "sendresource":
			ct_send_resource();
			break;
		case "sendunits":
			ct_send_units();
			break;
		case "buildcity":
			ct_build_city();
			break;
		case "user":
			ct_view_user();
			break;
		case "deleteuser":
			ct_delete_user();
			break;
		case "acceptemail":
			ct_accept_email();
			break;
		case "deletecity":
			ct_delete_city();
			break;
		case "options":
			ct_options();
			break;
		case "help":
			ct_help();
			break;
		case "logout":
			ct_logout();
			break;
		default:
			header("Location: city/");
			echo '<a href="city/">sakumins</a>';
			die();
	}
}
?>
