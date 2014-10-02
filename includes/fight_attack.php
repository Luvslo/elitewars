<?php

include '../classes/session.php';

Session::init();

include '../classes/users.php';
include '../classes/attack.php';

list ($targetname, $target_atk, $target_hp, $target_crit, 
      $username, $user_atk, $user_hp, $user_crit) = $attackLoad->defineAttack($targetname);
      
$i = 0;

$start_hp = array($user_hp, $target_hp);
$names_arr = array($username, $targetname);
$display_arr = array();
$attack_arr = array();
$hp_arr = array();

while ($user_hp > 0 OR $target_hp > 0)
{
    static $turn = 'user';
    if ($turn == 'user')
    {
        $display_arr = 'User attacks for '.$user_atk;
        $target_hp -= $user_atk;
        ++$i;
    }
    
    if ($user_hp <= 0)
    {
        $display_arr = 'User has won!';
        $winner = 'user';
        break;
    }
    
    $turn = 'target';
    if ($turn == 'target')
    {
        $display_arr = 'Target attacks for '.$target_atk;
        $user_hp -= $target_atk;
        ++$i;
    }
    
    if ($user_hp <= 0)
    {
        $display_arr = 'Target has won!';
        $winner = 'target';
        break;
    }
    
    $turn = 'user'; //redefine the users turn, the first define was static.
}

?>


<script type="text/javascript">

var timeout = 800;

var start_hp = <?php json_encode($start_hp); ?>;
var current_hp = <?php json_encode($start_hp); ?>;
var names = <?php json_encode($names_arr); ?>;
var output = <?php json_encode($display_arr); ?>;
var attack = <?php json_encode($attack_arr); ?>;
var hp = <?php json_encode($hp_arr); ?>;
var i = 0;

function displayAttack()
{
    var side = 1-(i%2);
    
    document.getElementById('side_'+(1-side)).innerHTML = '';
    innerContent = '&nbsp';
    
    if (attack[i] > 0)
    {
        innerContent = '<span style="color:#red;font-size:12pt;">'+attack[i]+'</span>';
        current_hp[side] = hp[i];
        if (current_hp[side] < 0)
            current_hp[side] = 0;
            document.getElementById('hp_'+side).width = 198 * ((current_hp[side] / start_hp[side]));
            document.getElementById('current_hp_'+side).innerHTML = current_hp[side]+'&nbsp;/&nbsp;'+start_hp[side];
    }
    
    html2='<table border=0 cellpadding=0 cellspacing=0 width=100 height=100><tr><td style="border:1px solid#333333;background-color:#333333;" align="center">'+innerContent+'</td></tr></table>';
    document.getElementById('side_'+side).innerHTML = html2;
   
	if (side == 0)
	{
		document.getElementById("reportDiv").innerHTML += '<div class="attacker-hit">'+output[i]+'</div><div style="clear:both"></div><br>';
	}
	else
	{
		document.getElementById("reportDiv").innerHTML += '<div class="defender-hit">'+output[i]+'</div><div style="clear:both"></div><br>';
	}

    ++i;

	if (i < length.output)
	    self.setTimeout('displayAttack()', timeout);
	else
		self.setTimeout('displayResult()', timeout);
}


function displayResult()
{
    document.getElementById('hp_0').width = 198 * ((current_hp[0] / start_hp[0]));
	document.getElementById('current_hp_0').innerHTML = current_hp[0]+"&nbsp;/&nbsp;"+start_hp[0];
	document.getElementById('hp_1').width = 198 * ((current_hp[1] / start_hp[1]));
	document.getElementById('current_hp_1').innerHTML = current_hp[1]+"&nbsp;/&nbsp;"+start_hp[1];
	
	if(winner == -1)
	{
		document.getElementById('side_0').innerHTML = '&nbsp;';
		document.getElementById('side_1').innerHTML = '&nbsp;';
	}
	else
	{
		document.getElementById('side_'+(1-winner)).innerHTML = '<img src="images/defeat.gif">';
		document.getElementById('side_'+winner).innerHTML = '<img src="images/victory.gif">';

	}
	document.getElementById('reportDiv').innerHTML += '<hr class="result-line"><span style="color:006666;"><strong>'+output[i]+'</strong>';

	if (winner == 0) /* only user gets the winReport display */
		self.setTimeout('winReport()', timeout);
}

function winReport()
{
    var xp_strip = <?php echo $xp_strip; ?>;
    if (xp_strip > 0)
			document.getElementById('reportDiv').innerHTML += '<span style="color:#009900;"><strong>You strip '+xp_strip+' experience from '+names[1]+'</strong>></span><br>';
}

self.setTimeout('displayAttack()', 400);

</script>

<!-- display results for now - attack animation coming soon -->
<div align="center">
<table border=0 cellpadding="0" cellspacing="0" width="450">
    <tr>
        <td height="10" colspan="3"></td>
    </tr>
    <tr>
	    <td colspan="3" align="center">
	        <div id="reportDiv" style="position:relative;"></div>
	    </td>
	</tr>
	<tr>
	    <td height="10" colspan="3"></td>
	</tr>
</table>
