<?php
function vw_paththing(){if (isset($_GET["view"])) echo "../"; if (isset($_GET["id"])) echo "../";}
function vw_top($add_style = ""){
	?>
	<!doctype html>
	<html>
	<head>
	<link rel="stylesheet" href="<?php vw_paththing(); ?>style.css">
	<title>Kaķi</title>
	<style>
	
	body {font-size: 14px; background-image: url("<?php vw_paththing(); ?>images/beige132.gif"); background-color: #e9dfd0;}
	#main {width: 800px; margin: 10px auto 10px auto; background-image: url("<?php vw_paththing(); ?>images/beige005.jpg"); background-color: #e9dfd0; border: 1px solid #cc9900;}
	
	#stats1:hover{background-image: url('<?php vw_paththing(); ?>images/icons/atten2.gif');}
	#stats2:hover{background-image: url('<?php vw_paththing(); ?>images/icons/dots2.gif');}
	#stats3:hover{background-image: url('<?php vw_paththing(); ?>images/icons/message2.gif');}
	
	<?php echo $add_style; ?>
	
	</style>
	</head>
	<body>
	<div id="main">
	<?php
}

function vw_bottom(){
	?>
	</div>
	</body>
	</html>
	<?php
}

function vw_header(){
	global $lang;
	?>
	<div id="header"><a href="<?php vw_paththing(); ?>options/"><?php echo $lang["options"]; ?></a> | <a href="<?php vw_paththing(); ?>help/"><?php echo $lang["help"]; ?></a> | <a href="<?php vw_paththing(); ?>logout/"><?php echo $lang["logout"]; ?></a></div>
	<?php
}

function vw_footer(){
	?>
	<div id="footer">lapa (c) lapas veidotajs</div>
	<?php
}

function vw_resourcepic($restype){
	global $lang;
	echo '<img src="', vw_paththing() , "images/resources/res" , $restype, '.gif" alt="', $lang["res" . $restype . "name"], '" title="', $lang["res" . $restype . "name"], '"/>&nbsp;';
}

function vw_resource_slider($restype, $buildingid, $buildingnumber, $ola = 1, $kak = 1){
	global $config;
	global $lang;
	if ($kak == 1){
	?>
	<form class="makeform" action="<?php vw_paththing(); ?>resourcegeneration/" method="post">
	<b><?php echo $lang["workerammount"]; ?>: </b>
	<input type="submit" value="<?php echo $lang["accept"]; ?>"/>
	<input type="hidden" name="resource" value="<?php echo $restype; ?>"/>
	<input type="hidden" name="buildingid" value="<?php echo $buildingid; ?>"/>
	<input id="slider<?php echo $ola; ?>" style="width: 200px;" type="range" name="ammount" value="<?php echo $buildingnumber; ?>" min="0" max="10"/> 
	<br/><?php echo $lang["thatmeans"]; ?> <span id="eggvalue<?php echo $ola; ?>"><?php echo $buildingnumber; ?></span> <?php echo $lang["workersmake"]; ?> <span id="eggvalue2<?php echo $ola; ?>"><?php echo floor($buildingnumber * $config["res" . $restype ."productivity"] / $config["timemodifier"] / $config["ticktime"]); ?></span> <?php vw_resourcepic($restype); ?> <?php echo $lang["anhour"]; ?>.
	<script>
	document.getElementById("slider<?php echo $ola; ?>").oninput = function() {
	document.getElementById("eggvalue<?php echo $ola; ?>").innerHTML = this.value;
	document.getElementById("eggvalue2<?php echo $ola; ?>").innerHTML = Math.floor((this.value * <?php echo $config["res" . $restype ."productivity"], ") / ", $config["timemodifier"], " / ", $config["ticktime"]; ?>);}
	</script>
	</form>
	<?php
	} else {
	?>
	<form class="makeform" action="<?php vw_paththing(); ?>resourcecreation/" method="post">
	<b><?php echo $lang["res" . $restype . "name"]?></b>
	<input type="submit" value="<?php echo $lang["accept"]; ?>"/>
	<input type="hidden" name="resource" value="<?php echo $restype; ?>"/>
	<input type="hidden" name="buildingid" value="<?php echo $buildingid; ?>"/>
	<input id="slider<?php echo $ola; ?>" style="width: 200px;" type="range" name="ammount" value="<?php echo $buildingnumber; ?>" min="0" max="10"/> 
	<br/><?php echo $lang["thatmeans"]; ?> <?php vw_resourcepic($restype); ?><span id="eggvalue<?php echo $ola; ?>">0</span> <?php echo $lang["for"]; ?> <br/><div class="resourcelist"><?php 
	$additionalscript = "";
	if($config["res" . $restype . "rescount"] == 0){
		echo $lang["nothing"];
	} else {
		for ($i = 1; $i <= $config["res" . $restype . "rescount"]; $i++){
		echo vw_resourcepic($config["res" . $restype . "res" . $i]), '&nbsp;<span id="eggvalue2', $ola,'p', $i, '">0</span>&nbsp;&nbsp;';
		$additionalscript .= 'document.getElementById("eggvalue2' . $ola . 'p' . $i . '").innerHTML = Math.floor(this.value * ' . $config["res" . $restype . "res" . $i . "count"] * $config["creationpricemodifier"] . ');';
		}
	}
	echo '<script>document.getElementById("slider', $ola, '").oninput = function() {
	document.getElementById("eggvalue', $ola, '").innerHTML = this.value;', $additionalscript, '} </script>';
	?>
	</div>
	</form>
	<?php
	}
}

function vw_loggings($logintext, $linktext = "Atpakaļināties", $linklink = ""){
	vw_top();
	?>
	<div class="side" id="full"><span class="center"><h2>
	<?php echo $logintext; ?>
	</h2><a href="<?php vw_paththing(); echo $linklink; ?>"><?php echo $linktext; ?></a></span><br/><br/>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_stats($resources){
	global $config;
	global $lang;
	?>
	<div id="stats"> <?php
	for ($i = 1; $i <= $config["mainrescount"]; $i++) echo '<img src="', vw_paththing() , "images/resources/res" , $i, '.gif" alt="', $lang["res" . $i . "name"], '" title="', $lang["res" . $i . "name"], '"/>&nbsp;', $resources[$i], "&nbsp;&nbsp;&nbsp;";
	?> 
	
	<a href="<?php vw_paththing(); ?>city/"><img src="<?php vw_paththing(); ?>images/icons/house.gif" alt="Pilsēta" title="<?php echo $lang["city"]; ?>"/></a>&nbsp;
	<a href="<?php vw_paththing(); ?>region/"><img src="<?php vw_paththing(); ?>images/icons/region.gif" alt="Reģions" title="<?php echo $lang["region"]; ?>"/></a>&nbsp;
	<a href="<?php vw_paththing(); ?>world/"><img src="<?php vw_paththing(); ?>images/icons/world.gif" alt="Pasaule" title="<?php echo $lang["world"]; ?>"/></a>

	<a href="<?php vw_paththing(); ?>notifications/"><img src="<?php vw_paththing(); ?>images/icons/atten.gif" id="stats1" alt="Jaunumiņi" title="<?php echo $lang["news"]; ?>"/></a>
	<a href="<?php vw_paththing(); ?>events/"><img src="<?php vw_paththing(); ?>images/icons/dots.gif" id="stats2" alt="Notikumi" title="<?php echo $lang["events"]; ?>"/></a>
	<a href="<?php vw_paththing(); ?>messages/"><img src="<?php vw_paththing(); ?>images/icons/message.gif" id="stats3" alt="Ziņas" title="<?php echo $lang["messages"]; ?>"/></a></div>
	<?php
}

function vw_countdownscript($date, $element, $endtext, $eee = 1){
	echo '<script>
var a', $eee,' = new Date("', $date,'").getTime();
function yeet', $eee,'() {
var b', $eee,' = new Date().getTime();
var distance = a', $eee,' - b', $eee,';
var d', $eee,' = Math.floor(distance / (1000 * 60 * 60 * 24));
var e', $eee,' = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
var f', $eee,' = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
var g', $eee,' = Math.floor((distance % (1000 * 60)) / 1000);
var h', $eee,' = "";
if(d', $eee,' > 0) h', $eee,' += d', $eee,' + "d ";
if(e', $eee,' > 0) h', $eee,' += e', $eee,' + "h ";
if(f', $eee,' > 0) h', $eee,' += f', $eee,' + "m ";
h', $eee,' += g', $eee,' + "s ";
document.getElementById("', $element, '").innerHTML = h', $eee,';
if (distance < 0) {clearInterval(c', $eee,');
document.getElementById("', $element, '").innerHTML = "', $endtext, '";}}
var c', $eee,' = setInterval(function(){yeet', $eee,'();}, 1000);
yeet', $eee,'();
</script>';
}
?>