<?php

session_start();
require '../database.connection.php';
require '../classes/users.class.php';

if (isset($_GET['skillid']))
{
	$skillLoad = new Skills($dbh);
	$userLoad = new Users($dbh);
	
	$userData = $userLoad->userData($_SESSION['userid']);
	$skillid = htmlentities((int)$_GET['skillid']);
	$userid = $userData['id'];
	foreach ($skillLoad->fetchSkillDataAsc($skillid) as $skillData)
	{
		$name = $skillData['name'];
		$image = $skillData['image'];
		$lvlreq = $skillData['lvlreq'];
		$skillid = $skillData['skillid'];
		$duration = $skillData['duration'];
		$recharge = $skillData['recharge'];
		$rage = $skillData['ragecost'];
		$desc = $skillData['desc'];
	}
	if ($skillLoad->playerSkillLevel($skillid, $userid) != false)
	{
		$level = $skillLoad->playerSkillLevel($skillid, $userid);
		
		foreach ($skillLoad->fetchSkillDataDesc($skillid) as $skillDataDesc)
		{
			$max_level = $skillDataDesc['level'];
		}
		$skill_level = ($level > $max_level) ? $max_level : $level;
		$skill_desc = $skillLoad->skillDescData($skillid, $skill_level);
		$nextlevel_desc = $skillLoad->skillDescData($skillid, ++$skill_level); //+1
	}
	else
	{
		//user does not have the skill, everything holds default values.
		$level = 0;
		$skill_desc = $desc;
		$noskill = true;
	}
	
	$echo_level = ($level == 0) ? 1 : $level;
	echo '<div id="castSkilldiv">';
        echo '<div id="trainSkilldiv">';
	echo '<table style="width:300px;">
		<tr>
			<td valign="top" style="padding:5px;width:75px;" align="center" valign="middle">
				<img src="'.$image.'">
			</td>
			<td valign="top" style="padding:5px;">
				<span style="font-weight:bold;" font-size:12pt;">
					'.$name.' Level '.$echo_level.'
					<br />
					Level Required: '.$lvlreq.'
					</span>
					<br />
				<span style="font-size:8pt;">
					<p>'.$skill_desc.'</p>
				</span>
			</td>
		</tr>
	</table>
	<br />
	<span style="text-align:center;">Attack Cost '.$rage.'
		<br />
		Cooldown: '.$recharge.'
		<br />
		Duration: '.$duration.'
	</span>
	<br />';
	if ($skillLoad->castRecharge($userid, $skillid) != false)
	{
		$recharge = $skillLoad->castRecharge($userid, $skillid);
		echo 'This skill is recharging for '.$recharge.' minutes.';
	}
	if ($noskill == true)
	{
		echo '<br /><a href="#" onclick="trainSkill(\''.$skillid.'\')">Learn Skill</a>';
	}
	if ($noskill != true AND $skillLoad->castRecharge($userid, $skillid) == false)
	{
		echo '<br /><a href="#" onclick="castSkill(\''.$skillid.'\')">Cast Skill</a>';
	}
	echo '</div></div>';
}

?>
<script>
/** cast skill */
function castSkill(skillid)
{
	$('#loading').html('Loading');
	$.ajax({
		type: 'GET',
		cache: false,
		url: 'includes/castSkill.php?skillid='+skillid,
		success: function (html)
		{
			$('#castSkilldiv').html(html);
		}
	});
}
/** train skill */
function trainSkill(skillid)
{
	$('#loading').html('Loading');
	$.ajax({
		type: 'GET',
		cache: false,
		url: 'includes/learnSkill.php?skillid='+skillid,
		success: function (html)
		{
			$('#trainSkilldiv').html(html);
		}
	});
}
</script>
