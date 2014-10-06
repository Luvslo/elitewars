<?php

/**
 * The actual fight script.
 */

include '../classes/session.php';

Session::init();

include '../classes/users.php';
include '../classes/attack.php';

$attackLoad = new Attack($dbh);

list ($targetname, $target_atk, $target_hp, $target_crit, 
      $username, $user_atk, $user_hp, $user_crit) = $attackLoad->defineAttack($targetname);
   
//keep the output as an arry, later I will update this to use jquery for decent battle animations.
$output_arr = array();

$crit_chance = rand(1,200);

while ($user_hp > 0 OR $target_hp > 0) //loop results until a winner is found.
{
	static $turn = 'user';
	if ($turn == 'user')
	{
		//very basic critical hit example.
		if ($user_crit >= rand(1,200))
		{
			$crit = 'CRITICAL';
			$user_atk *= 2; //just for the tutorials sake, I multiplied the current attack by 2.
		}
		$output_arr = $username.' attacks for '.$user_atk.' '. $crit;
		$target_hp -= $user_atk;
	}
	
	if ($target_hp <= 0)
	{
		$output_arr = $username.' wins!';
		$winner = 'user';
		break;
	}
	
	$turn = 'target';
	if ($turn == 'target')
	{
		$output_arr = $targetname.' attacks for '.$target_atk;
		$user_hp -= $target_atk;
	}
	
	if ($user_hp <= 0)
	{
		$output_arr = $targetname.' wins!';
		$winner = 'target';
		break;
	}
	usleep(10);
	$turn = 'user'; //reset the turn, since the original value was a static.
}


//display the output
foreach ($output_arr as $key => $value)
{
	echo $value.'<br />';
	usleep(10);
}

if ($winner == 'user')
{
	//xp gains
	//xp strips
	//gold gains
	//attack log
	//update stats (exp, gold)
}
elseif ($winner == 'target')
{
	//attack log
}
