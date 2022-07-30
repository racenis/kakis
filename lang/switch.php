<?php
session_start();
if (!isset($_SESSION["lang"])) $_SESSION["lang"] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
switch ($_SESSION["lang"]){
	case "lv":
		include "lang/lv.php";
		break;
	case "en":
		include "lang/en.php";
		break;
	case "lat":
		include "lang/lat.php";
		break;
	default:
		include "lang/lv.php";
}
?>