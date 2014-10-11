<?php

//$godid and $crewid gets sent to this page on the include.
//select the god, based off it.
//select the users joined in the raid based off crewid and raidid

//the loop.
while (!empty($attackers) OR $god_hp > 0)
{
    static $turn = 'crew';
    if ($turn == 'crew')
    {
        $c_total_attack = 0; //crew total attack.
        foreach ($attackers as $key => &$value)
        {
            echo $value['username'].' hits for '.$value['attack'].'<br/>';
            $c_total_attack += $value['attack'];
        }
    }
    $god_hp -= $c_total_attack;
    if ($god_hp <= 0)
    {
        echo 'Crew has won!';
        $winner = 'crew';
        break;
    }
        
    $turn = 'god';
    if ($turn == 'god')
    {
        echo 'Godname attacks for '.$god_attack.'<br/>';

        foreach ($attackers as $key => &$value)
        {
            $value['hp'] -= $god_attack;
            if ($value['hp'] <= 0)
            {
                unset($attackers[$key]);
            }
        }
    }
        
    if (empty($attackers))
    {
        echo 'Godname has won!';
        $winner = 'god';
        break;
    }
        
    $turn = 'crew';
}





