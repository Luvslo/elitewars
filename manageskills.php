<?php

include 'database.connect.php';

$skillLoad = new Skills($dbh);
$showidArr = array(1,2,3,4); //for now, to limit the skills allowed in-game.

?>
<br />
<div align="center">
	<!-- left -->
	<div class="trade_left">
	<?php
	foreach ($skillLoad->fetchSkills($showidArr) as $skillData)
	{
		$skillid = $skillData['skillid'];
		$desc = $skillData['desc'];
		$name = $skillData['name'];
		$image = $skillData['image'];	
		if (strlen($desc) > 50)
		{
		    //cut the description text.
            $descCut = substr($desc, 0, 50);
        	$desc = substr($descCut, 0, strrpos($descCut, ' ')).'...'; 
		}
		echo '<table>
			<tr>
				<td style="width:5%;"><img height="35" width="35" src="'.$image.'"></td>
				<td style="width:85%;">'.$name.'<br />'.$desc.'</td>
				<td><a href="#" onclick="loadSkill(\''.$skillid.'\')">View</a></td>
			</tr>';
		echo "\n"; //for neatless of source.
	}
	?>
	</table>
	</div>
	
	<div class="trade_right">
		<div id="loadSkilldiv"> <!-- ajax load -->
		</div>
	</div>
</div>

<script>
/** skill info ajax retreive. */
function loadSkill(skillid)
{
	$('loading').html('Loading skill...');
	$.ajax({
		type: 'GET',
		cache: false,
		url: 'includes/loadSkill.php?skillid='+skillid,
		success: function (html)
		{
			$('#loadSkilldiv').html(html);
		}
	});
}
</script>
