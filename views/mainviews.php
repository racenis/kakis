<?php
function vw_main_page(){
	global $lang;
	vw_top();
	?>
	<div class="side" id="left">
	<h3><?php echo $lang["register"]; ?>:</h3>
	<form action="register/" method="post">
		<p><?php echo $lang["username"]; ?>:</p>
		<input type="text" name="username"/>
		<p><?php echo $lang["email"]; ?>:</p>
		<input type="text" name="email"/>
		<p><?php echo $lang["password"]; ?>:</p>
		<input type="password" name="password"/>
		<p><?php echo $lang["passwordagain"]; ?>:</p>
		<input type="password" name="password2"/>
		<p><?php echo $lang["language"]; ?>:</p>
		<select name="lang">
		<option value="lv">Latviski</option>
		<option value="en">English</option>
		</select>
		<p><?php echo $lang["writingsecurity"]; ?> &laquo;sikkak&raquo; <?php echo $lang["butotherwise"]; ?>:</p>
		<input type="text" name="security"/>
		<input type="hidden" name="type" value="register"/>
		<p><?php echo $lang["withregistrationaccept"]; ?> <a href="./noteikumi/"><?php echo $lang["inrules"]; ?></a>!</p>
		<input type="submit" value="<?php echo $lang["register"]; ?>!"/>
	</form>
	
	</div>
	<div class="side" id="right">
	<span class="right"><form action="login/" method="post" style="margin-bottom: 0;">
		<?php echo $lang["username"]; ?>:
		<input type="text" name="username"/>
		<?php echo $lang["password"]; ?>:
		<input type="password" name="password"/>
		<input type="hidden" name="type" value="login"/>
		<input type="submit" value="<?php echo $lang["goin"]; ?>"/>
	</form></span>
	<p><span class="right">
	<?php echo $lang["selectlanguage"]; ?>:
	<a href="changelanguage/lv/"><img src="images/latvia.gif" alt="aaa" title="Latvieski"/></a>
	<a href="changelanguage/en/"><img src="images/english.gif" alt="aaa" title="English"/></a>
	</span></p>
	<span class="center"><img alt="bilde" src="images/logo.gif"/></span><br/><br/>
	
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_rules(){
	global $lang;
	vw_top();
	?>
	<div class="side" id="full">
	<ol>
		<li><?php echo $lang["rule1"]; ?></li>
		<li><?php echo $lang["rule2"]; ?></li>
	
	
	</ol>
	<span class="center"><a href="../"><?php echo $lang["goback"]; ?></a></span><br/><br/>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_city($city, $citylist, $gettings){
	global $lang;
	$addcss = "";
	for ($i = 1; $i < 7; $i++){
		$addcss .= "#building" . $i . ":hover{background-image: url('../images/buildings/" . (($city[$i][2] == null) ? $city[$i][1] : 'b') . "s.gif');}";
	}
	vw_top($addcss);
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	<span class="leftbigtext"><?php echo $lang["city"]; ?></span>
	<p><a href="../resources/"><?php echo $lang["viewallresources"]; ?></a></p>
	<p><a href="../units/"><?php echo $lang["viewallunits"]; ?></a></p>
	<?php echo $lang["changecity"]; ?>:
	<form method="post">
		<select onchange="this.form.submit()" name="city">
		<?php 
		foreach ($citylist as $uuu)
			echo '<option value="', $uuu["id"], '">&nbsp;', $uuu["name"], '&nbsp;</option>';
		?>
		</select>
		<br/>
		<noscript><input type="submit" value="Bliezt iekšā!"/></noscript>
	</form>
	<?php echo $lang["of_doings"];?>
	</div>
	<div class="side" id="right" style="background-image: url('../images/city_back.gif');">
	<?php
	for ($i = 1; $i < 7; $i++){
		if ($city[$i][1] != 0){
			echo '<a href="../building/', $city[$i][0], '/"><img class="building" id="building', $i, '" src="../images/buildings/', ($city[$i][2] == null) ? $city[$i][1] : 'b', '.gif" alt="maja"/></a>';
			if($city[$i][2] != null){
				echo '<div class="buildingtext" id="buildingtext'. $i. '">' . $city[$i][2] . '</div>';
				vw_countdownscript($city[$i][2], 'buildingtext'. $i, "&mdash;", $i);
			}
		}else
			echo '<a href="../build/', $i, '/"><img class="building" id="building', $i, '" src="../images/buildings/0.gif"/></a>';
	}
	?>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_region($region, $gettings){
	global $lang;
	$addcss = "";

	vw_top($addcss);
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	<?php
	if (isset($gettings["id"])){
		$janis = explode("p", $gettings["id"]);
		if (isset($janis[1]) && $region[$janis[1]]["name"] != "-"){
			echo '<span class="leftbigtext">', $region[$janis[1]]["name"] , '</span><br/>';
			echo $lang["user"], ':<br/><b><a href="', vw_paththing(), 'user/', $region[$janis[1]]["userid"], '/">', $region[$janis[1]]["username"], '</a></b><br/><br/>';
			echo $lang["options"], ':<br/>';
			echo '<a href="', vw_paththing(), 'sendmessage/', $region[$janis[1]]["userid"], '/">', $lang["sendmessage"],'</a><br/>';
			echo '<a href="', vw_paththing(), 'sendresource/', $region[$janis[1]]["id"], '/">', $lang["sendresource"], '</a><br/>';
			echo '<a href="', vw_paththing(), 'sendunits/', $region[$janis[1]]["id"], '/">', $lang["pillage"], '</a><br/>';
		} else echo $lang["infounavailable"];
	}
	?>
	</div>
	<div class="side" id="right" style="background-image: url('<?php vw_paththing(); ?>images/other/regionback.gif');">
	<?php
	if (isset($gettings["id"]))
		$regionid = explode("p", $gettings["id"])[0];
	else
		for ($i = 1; $i < 7; $i++)
			if (isset($region[$i]["regionid"])) $regionid = $region[$i]["regionid"];
	for ($i = 1; $i < 7; $i++){
		if (isset($region[$i]))
			echo '<a href="', vw_paththing(), 'region/', $regionid, "p", $region[$i]["location"], '/"><img class="city" id="city', $i, '" src="', vw_paththing(), 'images/other/', ($region[$i]["name"] != "-") ? 'city' : 'citybuild', '.gif" alt="aaa"/></a>',
			'<div class="citytext" id="citytext', $i, '">', $region[$i]["name"], '</div>';
		else
			echo '<a href="', vw_paththing(), 'buildcity/', $regionid, 'p', $i, '/"><img class="city" id="city', $i, '" src="', vw_paththing(), 'images/other/nocity.gif"/></a>';
	}
	?>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_world($world){
	global $config;
	global $lang;
	$addcss = "";

	vw_top($addcss);
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
		<?php echo $lang["edgetext"]; ?>
	</div>
	<div class="side" id="right" style="background-image: url('<?php vw_paththing(); ?>images/other/worldback.gif'); line-height: 0px;">
	<?php
	for ($y = 1; $y <= $config["worldheigth"]; $y++){
		for ($x = 1; $x <= $config["worldwidth"]; $x++){
			if (isset($world[$x][$y]))
				echo '<div class="worldthing"><a href="', vw_paththing(), 'region/', $world[$x][$y]["id"], '/"><img class="worldthing" src="', vw_paththing(), '/images/other/world', $world[$x][$y]["type"], '.gif" alt="olas"/></a><div class="worldtext"><a href="', vw_paththing(), 'region/', $world[$x][$y]["id"], '/">', $world[$x][$y]["cities"] ,'</a></div></div>';
			else
				echo '<img class="worldthing" src="', vw_paththing(), '/images/other/world0.gif" alt="olas"/>';
		}
		echo '<br/>';
	}
	?>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_build($location, $city){
	global $config;
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	
	</div>
	<div class="side" id="right">
	<?php
	for ($i = 1; $i <= $config["buildingcount"]; $i++){
		echo '<img class="smallpic" src="../../images/buildings/', $i , '.gif"/>';
		echo "<h3>", $lang["building" . $i . "name"] , "</h3>";
		echo "<p>", $lang["building" . $i . "desc"] , "</p><b>Izmaksas: </b>";
		
		for ($j = 1; $j <= $config["building" . $i . "rescount"]; $j++)
		echo '<img src="', vw_paththing(), "images/resources/res", $config["building" . $i . "res" . $j], '.gif" alt="' , $lang["res" . $config["building" . $i . "res" . $j] . "name"], '" title="', $lang["res" . $config["building" . $i . "res" . $j] . "name"], '"/>&nbsp;', ($config["building" . $i . "res" . $j . "lvl1"] * $config["buildingpricemodifier"]), " ";
		
			echo '<form class="makebutton" action="', vw_paththing(),'build/" method="post">
			<input type="hidden" name="buildingtype" value="' . $i . '"/>
			<input type="hidden" name="location" value="' . $location["id"] . '"/>
			<input type="submit" class="makebutton" value="' . $lang["build"] . '"/>
			</form><br/>';
		if ($i < $config["buildingcount"]) echo "<hr/>";
	}
	?>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_building($building){
	global $config;
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
		<span class="leftbigtext"><?php echo $lang["building"]; ?></span><br/>
		<span class="leftleveltext"><?php echo $building["buildinglevel"]; ?>.</span><br/>
		<?php echo $lang["level"]; ?><br/>
		<?php 
		if ($building["buildinglevel"] == $config["building" . $building["buildingtype"] . "maxlvl"])
			echo $lang["highestleveldone"];
		elseif($building["eventtype"] != null)
			echo $lang["buildingbuilding"];
		else {
			echo $lang["nextlevelreq"], ":<br/>";
			for ($j = 1; $j <= $config["building" . $building["buildingtype"] . "rescount"]; $j++){
			echo '<img src="', vw_paththing(), "images/resources/res", $config["building" . $building["buildingtype"] . "res" . $j], '.gif" alt="' , $lang["res" . $config["building" . $building["buildingtype"] . "res" . $j] . "name"], '" title="', $lang["res" . $config["building" . $building["buildingtype"] . "res" . $j] . "name"], '"/>&nbsp;', ($config["building" . $building["buildingtype"] . "res" . $j . "lvl" . ($building["buildinglevel"] + 1)] * $config["buildingpricemodifier"]), " ";
				
			}
			echo '<p><a href="', vw_paththing(), 'upgrade/', $building["id"] , '/">', $lang["upgrade"], '</a></p>';
			
		}
		
		if($building["eventtype"] == null)
		echo '<p><a href="', vw_paththing(), 'downgrade/', $building["id"] , '/">', $lang["demolish"], '</a></p>';
		?>
	</div>
	<div class="side" id="right">
	<?php
		switch ($building["buildingtype"]){
			case 1: ?>
					<div class="egg"><img src="<?php vw_paththing(); ?>images/buildings/1.gif" alt="PAgastmāj" class="smallpic"/><h2><?php echo $lang["building1name"]; ?></h2><p><?php echo $lang["building1desc"]; ?></p>
					</div>
					<div class="egg">
						<form method="post" action="<?php vw_paththing(); ?>options/">
						<p>
						<?php echo $lang["changecityname"]; ?>:
						<input type="hidden" name="type" value="cityname"/>
						<input type="hidden" name="buildingid" value="<?php echo $building["id"];?>"/>
						<input type="text" name="name" value="Pilsēta"/>
						<input type="submit" value="<?php echo $lang["putin"]; ?>"/>
						</p>
						</form>
					</div>
					<div class="egg">
					<?php echo $lang["otheroptions"]; ?>: <a href="<?php vw_paththing(); ?>deletecity/<?php echo $building["cityid"]; ?>/"><?php echo $lang["demolishcity"]; ?></a>
					</div>
					
					
					<?php
				break;
			case 2: ?>
					<div class="egg"><h2><?php echo $lang["building2name"]; ?></h2><p><?php echo $lang["building2desc2"]; ?></p></div>
					<?php
				break;
			case 3: ?>
					<div class="egg">
					<img src="<?php vw_paththing(); ?>images/buildings/3.gif" alt="LIELĀ OLA" class="smallpic"/>
					<h2><?php echo $lang["building3name"]; ?></h2><p><?php echo $lang["building3desc2"]; ?></p></div>
					<div class="egg">
					<?php vw_resource_slider(4, $building["id"], $building["buildingnumber"], 1, 1); ?>
					</div>
					<?php
				break;
			case 4: ?>
					<div class="egg"><h2>Olas</h2><p>Lielas olas</p></div>
					<?php
				break;
			case 5: ?>
					<div class="egg">
					<img src="<?php vw_paththing(); ?>images/buildings/5.gif" alt="LIELĀ OLA" class="smallpic"/>
					<h2><?php echo $lang["building5name"]; ?></h2><p><?php echo $lang["building5desc2"]; ?></p></div>
					<div class="egg">
					<?php vw_resource_slider(6, $building["id"], $building["buildingnumber"], 1); ?>
					</div>
					<div class="egg">
					<?php vw_resource_slider(5, $building["id"], /*$building["buildingnumber"]*/0, 2); ?>
					</div>
					<?php
				break;
			case 6: ?>
					<div class="egg">
					<img src="<?php vw_paththing(); ?>images/buildings/6.gif" alt="LIELĀ OLA" class="smallpic"/>
					<h2><?php echo $lang["building6name"]; ?></h2><p><?php echo $lang["building6desc2"]; ?></p></div>
					<div class="egg">
					Šim dīķim nav nekādas funkcionalitātes.
					</div>
					<?php
				break;
			case 7: ?>
					<div class="egg">
					<img src="<?php vw_paththing(); ?>images/buildings/7.gif" alt="LIELĀ OLA" class="smallpic"/>
					<h2><?php echo $lang["building7name"]; ?></h2><p><?php echo $lang["building7desc2"]; ?></p></div>
					<div class="egg">
					<?php vw_resource_slider(13, $building["id"], $building["buildingnumber"], 1, 2); ?>
					</div>
					<?php
				break;
			case 8: ?>
					<div class="egg">
					<img src="<?php vw_paththing(); ?>images/buildings/8.gif" alt="LIELĀ OLA" class="smallpic"/>
					<h2><?php echo $lang["building8name"]; ?></h2><p><?php echo $lang["building8desc2"]; ?></p></div>
					<div class="egg">
					<?php vw_resource_slider(7, $building["id"], $building["buildingnumber"], 1, 1); ?>
					</div>
					<?php
				break;
			case 9: ?>
					<div class="egg">
					<img src="<?php vw_paththing(); ?>images/resources/res6l.gif" alt="LIELĀ OLA" class="smallpic"/>
					<h2><?php echo $lang["building9name"]; ?></h2><p><?php echo $lang["building9desc2"]; ?></p></div>
					<div class="egg">
					<?php vw_resource_slider(8, $building["id"], $building["buildingnumber"], 1, 1); ?>
					</div>
					<?php
				break;
			case 10: ?>
					<div class="egg">
					<img src="<?php vw_paththing(); ?>images/buildings/10.gif" alt="LIELĀ OLA" class="smallpic"/>
					<h2><?php echo $lang["building10name"]; ?></h2><p><?php echo $lang["building10desc2"]; ?></p></div>
					<div class="egg">
					<?php vw_resource_slider(101, $building["id"], $building["buildingnumber"], 1, 3); ?>
					</div>
					<div class="egg">
					<?php vw_resource_slider(102, $building["id"], $building["buildingnumber"], 2, 3); ?>
					</div>
					<div class="egg">
					<?php vw_resource_slider(103, $building["id"], $building["buildingnumber"], 3, 3); ?>
					</div>
					<?php
				break;
			case 11: ?>
					<div class="egg">
					<img src="<?php vw_paththing(); ?>images/buildings/11.gif" alt="LIELĀ OLA" class="smallpic"/>
					<h2><?php echo $lang["building11name"]; ?></h2><p><?php echo $lang["building11desc2"]; ?></p></div>
					<div class="egg">
					<?php  vw_resource_slider(9,  $building["id"], 0, 1, 2); ?>
					</div>
					<div class="egg">
					<?php  vw_resource_slider(10,  $building["id"], 0, 2, 2); ?>
					</div>
					<div class="egg">
					<?php  vw_resource_slider(11,  $building["id"], 0, 3, 2); ?>
					</div>
					<div class="egg">
					<?php  vw_resource_slider(12,  $building["id"], 0, 4, 2); ?>
					</div>
					<?php
				break;
			case 12: ?>
					<div class="egg">
					<img src="<?php vw_paththing(); ?>images/buildings/12.gif" alt="LIELĀ OLA" class="smallpic"/>
					<h2><?php echo $lang["building12name"]; ?></h2><p><?php echo $lang["building12desc2"]; ?></p></div>
					<div class="egg">
					<?php vw_resource_slider(3, $building["id"], $building["buildingnumber"], 1, 1); ?>
					</div>
					<?php
				break;
			default:
				echo $lang["buildingtypeunrec"];
			
			
			
			
		}
	
	?>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_resources($resources, $aaa = 1){
	global $config;
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	
	</div>
	<div class="side" id="right">
	<table>
		<?php
		$none = true;
		if ($aaa == 1){
			for ($i = $config["mainrescount"] + 1; $i <= $config["rescount"]; $i++)
				if (isset($resources[$i])){
					echo '<tr><td><img src="', vw_paththing() , "images/resources/res" , $i, '.gif" alt="', $lang["res" . $i . "name"], '"/></td><td><i>', $lang["res" . $i . "name"], '</i></td><td><b>', $resources[$i], "</b></td></tr>";
					$none = false;
				}
		} else if ($aaa == 2){
			for ($i = $config["res2start"] + 1; $i <= $config["res2count"] + $config["res2start"] + 1; $i++)
				if (isset($resources[$i])){
					echo '<tr><td><img src="', vw_paththing() , "images/resources/res" , $i, 'l.gif" alt="', $lang["res" . $i . "name"], '"/></td><td><i>', $lang["res" . $i . "name"], '</i></td><td>', $lang["res" . $i . "desc"], '</td><td><b>', $resources[$i], "</b></td></tr>";
					$none = false;
				}
		}
		if ($none) echo '<tr><td>', $lang["nothinghere"], '</td></tr>';
		?> 
	</table>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_send_resources($resources, $reppipient, $uwu = 1){
	global $config;
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	
	if($uwu == 1) {?>
	<div class="side" id="left">
		<?php echo $lang["choosesendresource"]; ?>
	</div>
	<div class="side" id="right">
	<form method="post">
	<table>
		<?php
			for ($i = 1; $i <= $config["rescount"]; $i++)
				if (isset($resources[$i]))
					echo '<tr><td><img src="', vw_paththing() , "images/resources/res" , $i, '.gif" alt="', $lang["res" . $i . "name"], '"/></td><td><i>', $lang["res" . $i . "name"], '</i></td><td><input id="slider', $i, '" style="width: 200px;" type="range" name="ammount', $i, '" min="0" max="', $resources[$i], '" value="0"/></td><td><b id="aaaa', $i, '">0</b></td></tr>';

		?>		
	<tr><td colspan="4"><input type="submit" value="<?php echo $lang["send"]; ?>"/></td></tr>
	<input type="hidden" name="recipient" value="<?php echo $reppipient; ?>"/>
		<script>
			<?php
				for ($i = 1; $i <= $config["rescount"]; $i++)
					if (isset($resources[$i]))
						echo'document.getElementById("slider', $i, '").oninput = function() {
						document.getElementById("aaaa', $i, '").innerHTML = this.value;}
						';
			?>
		</script>
	</table>
	<?php } else {?>
		<div class="side" id="left">
			<?php echo $lang["attackwarning"]; ?>
		</div>
		<div class="side" id="right">
		<form method="post">
		<table>
		<?php
			for ($i = $config["res2start"]; $i <= $config["res2start"] + $config["res2count"]; $i++)
				if (isset($resources[$i]))
					echo '<tr><td><img src="', vw_paththing() , "images/resources/res" , $i, '.gif" alt="', $lang["res" . $i . "name"], '"/></td><td><i>', $lang["res" . $i . "name"], '</i></td><td><input id="slider', $i, '" style="width: 200px;" type="range" name="ammount', $i, '" min="0" max="', $resources[$i], '" value="0"/></td><td><b id="aaaa', $i, '">0</b></td></tr>';

		?> 
	<tr><td colspan="4"><input type="submit" value="<?php echo $lang["send"]; ?>"/></td></tr>
	<input type="hidden" name="recipient" value="<?php echo $reppipient; ?>"/>
		<script>
			<?php
				for ($i = $config["res2start"]; $i <= $config["res2start"] + $config["res2count"]; $i++)
					if (isset($resources[$i]))
						echo'document.getElementById("slider', $i, '").oninput = function() {
						document.getElementById("aaaa', $i, '").innerHTML = this.value;}
						';
			?>
		</script>
	</table>
	<?php } ?>
	</form>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_messages($messages, $which = "all"){
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	</div>
	<div class="side" id="right">
	<?php
	if ($which == "all"){
		echo '<table><tr><td>', $lang["from"], '</td><td>', $lang["subject"], '</td><td>', $lang["time"], '</td></tr>';
		foreach($messages as $yeet)
			echo '<tr style="', $yeet["seen"] ? '':'font-weight: bold;', '"><td>', $yeet["username"], '</td><td><a href="', vw_paththing(), 'messages/', $yeet["id"], '/">', $yeet["subject"], '</a></td><td>', $yeet["senddate"], '</td></tr>';
		echo '</table>';
	} elseif ($which == "one"){
		echo '<div class="egg">', $lang["sender"], ': <a href="', vw_paththing(), 'user/', $messages["sender"], '/">', $messages["username"] , '</a><br/>';
		echo $lang["receiver"], ': <a href="', vw_paththing(), 'user/', $messages["recipient"], '/">', $messages["you"] , '</a><br/>';
		echo $lang["sendingtime"], ': ', $messages["senddate"] , '</div>';
		echo '<div class="egg">', $messages["messagetext"] , '</div>';
		echo '<div class="egg"><a href="', vw_paththing(), 'sendmessage/', $messages["sender"], '/">', $lang["reply"],'</a></div>';
	} else {
		echo '<form id="aa" action="', vw_paththing(),'sendmessage/" method="post">';
		echo '<div class="egg">', $lang["receiver"], ': <a href="', vw_paththing(), 'user/', $messages["id"], '/">', $messages["username"] , '</a><br/>';
		echo ' <input type="hidden" name="recipient" value="', $messages["id"], '"/>';
		echo $lang["subject"], ': <input name="subject" type="text" style="width: 300px;"/> </div>';
		echo '<div class="egg"><textarea name="messagetext" style="width: 537px; height: 300px;"></textarea></div>';
		echo '<div class="egg"><a href="#" onclick="document.getElementById(', "'aa'", ').submit();">', $lang["send"], '</a><noscript><input type="submit" value="', $lang["putin"],'"/></noscript></div></form>';
		
		
		
	}
	
	?>
	
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_view_user($user, $uuu = 0){
	global $lang;
	global $config;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	<a href="<?php vw_paththing(); ?>sendmessage/<?php echo $user["id"]; ?>/"><?php echo $lang["sendmessage"]; ?></a><br/>
	<?php if($uuu == $config["bepisnumber"]){?>
	<a href="<?php vw_paththing(); ?>deleteuser/<?php echo $user["id"]; ?>/"><?php echo $lang["deletuser"]; ?></a>
	<?php } ?>
	</div>
	<div class="side" id="right">
	<div class="egg">
	<?php
	if ($user["imageexist"])
		echo '<a href="', vw_paththing(), 'images/userimages/', $user["id"], '.gif"><img src="', vw_paththing(), 'images/userimages/', $user["id"], '.gif" class="smallpic" alt="olas"/></a>';
	else
		echo '<img src="', vw_paththing(), 'images/other/nopic.gif" class="smallpic" alt="olas"/>';
	echo '<h2>', $user["username"];
	if ($user["nickname"] != "" && $user["nickname"] != " ") echo '(<i>', $user["nickname"], '</i>)';
	echo '</h2>';
	echo '<p>', $user["description"], '</p>';
	?>
	<br/><br/><br/>
	</div>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_options($options, $which){
	global $config;
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	</div>
	<div class="side" id="right">
	<?php
	if ($which == 1){
		echo '<div class="egg">', $lang["passwordchanging"], ' <a href="2/">', $lang["here"], '</a></div>';
		echo '<div class="egg">', $lang["imagechanging"], ' <a href="3/">', $lang["here"], '</a></div>';
		echo '<form method="post">';
		echo '<div class="egg">', $lang["username"], ': <br/><input name="username" type="text" value="', $options["username"], '"/><br/>';
		echo $lang["email"], ': <br/><input name="email" type="text" value="', $options["email"], '"/><br/>';
		echo $lang["language"], ': <br/><select name="lang">;
		<option value="lv" ', (($options["lang"] == "lv") ? 'selected' : ''), '>Latviski</option>
		<option value="en" ', (($options["lang"] == "en") ? 'selected' : ''), '>English</option>
		<option value="lat" ', (($options["lang"] == "la") ? 'selected' : ''), '>Latvāniski</option>
		</select><br/>';
		echo $lang["nickname"], ': <br/><input name="nickname" type="text" value="', $options["nickname"], '"/><br/>';
		echo $lang["description"], ':<br/><textarea name="description">', $options["description"], '</textarea><br/>';
		echo '<input type="hidden" name="type" value="options"/>';
		echo '<input type="submit" value="', $lang["putin"], '"/></div></form>';
	} elseif ($which == 2) {
		echo '<form method="post">';
		echo '<div class="egg">', $lang["oldpassword"], ': <br/><input name="oldpassword" type="password" value=""/><br/>';
		echo $lang["newpassword"], ': <br/><input name="newpassword" type="password" value=""/><br/>';
		echo '<input type="hidden" name="type" value="password"/>';
		echo '<input type="submit" value="', $lang["putin"], '"/></div></form>';
	} else {
		if ($config["uploadpicture"]){
			echo '<form method="post" enctype="multipart/form-data">';
			echo '<div class="egg">', $lang["chooseimage"], ': <br/><br/><input name="failis" type="file" value="" accept="image/gif,image/jpeg,image/png"/><br/><br/>';
			echo '<input type="hidden" name="type" value="userimage"/>';
			echo '<input type="submit" value="', $lang["putin"], '"/></div></form>';
		} else {
			echo $lang["nopictures"];
		}
	}
	
	?>
	
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_notifications($messages){
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	</div>
	<div class="side" id="right">
	<?php
		echo '<table><tr><td>', $lang["event"], '</td><td>', $lang["time"], '</td></tr>';
		foreach($messages as $yeet)
			echo '<tr style="', $yeet["seen"] ? '':'font-weight: bold;', '"><td>', $yeet["message"], '</td><td>', $yeet["senddate"], '</td></tr>';
		echo '</table>';
	?>
	
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_delete_city($bepis){
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	</div>
	<div class="side" id="right">
	<?php echo $lang["warningdeletcity"]; ?>
	
	<form method="post">
	<input type="hidden" name="delet" value="<?php echo $bepis; ?>"/>
	<input type="submit" value="<?php echo $lang["deletcity"]; ?>"/>
	</form>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_events($events){
	global $config;
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	
	</div>
	<div class="side" id="right">
	<table>
		<tr><td><?php echo $lang["what"]; ?></td><td><?php echo $lang["fromwhere"]; ?></td><td><?php echo $lang["towhere"]; ?></td><td><?php echo $lang["whenhappen"]; ?></td><td><?php echo $lang["howlongstill"]; ?></td></tr>
		<?php
			$i = 0;
			foreach($events as $bepis){
				if($bepis["eventtype"] == 5) continue;
				echo '<tr><td>';
				echo $lang["event" . $bepis["eventtype"] . "name"];
				echo '</td><td>';
				echo $bepis["originname"];
				echo '</td><td>';
				echo $bepis["destinationname"];
				echo '</td><td>';
				echo $bepis["endtime"];
				echo '</td><td>';
				echo '<span id="eventing', $i,'"></span>';
				echo vw_countdownscript($bepis["endtime"], 'eventing' . $i, "&mdash;", $i);
				
				echo '</td></tr>';
				$i++;
			}
		?> 
	</table>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_build_city($regionlocation){
	global $config;
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	
	</div>
	<div class="side" id="right">
		<div class="egg">
		<img class="smallpic" src="<?php vw_paththing(); ?>images/other/city.gif"/>
		<p><?php echo $lang["citybuilddesc"]; ?></p>
		<div class="resourcelist">
		<?php 
		for ($j = 1; $j <= $config["newcityrescount"]; $j++){
			echo '<img src="', vw_paththing(), "images/resources/res", $config["newcityres" . $j], '.gif" alt="' , $lang["res" . $config["newcityres" . $j] . "name"], '" title="', $lang["res" . $config["newcityres" . $j] . "name"], '"/>&nbsp;', $config["newcityres" . $j . "lvl1"], " ";
		}
		
		?>
		</div>
		<form method="post">
		<p>
		<?php echo $lang["citybuilddesc2"]; ?>
		<input type="hidden" name="region" value="<?php echo $regionlocation[0]; ?>"/>
		<input type="hidden" name="location" value="<?php echo $regionlocation[1]; ?>"/>
		<input type="submit" value="<?php echo $lang["buildent"]; ?>"/>
		</p>
		</form>
		</div>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}

function vw_help($section){
	global $config;
	global $lang;
	vw_top();
	vw_header();
	vw_stats(md_getstats());
	?>
	<div class="side" id="left">
	<?php echo $lang["subjects"]; ?>:
	<i><?php echo $lang["none"]; ?></i>
	</div>
	<div class="side" id="right">
		<?php echo $lang["helpnotincluded"]; ?>
	</div>
	<?php
	vw_footer();
	vw_bottom();
}
?>