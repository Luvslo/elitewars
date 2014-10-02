<?php

include '../classes/session.php';

Session::init();

include '../classes/database.php';
include '../classes/users.php';
include '../classes/attack.php';


$targetname = htmlentities($_POST['targetname']);
$attack_cost = htmlentities((int)$_POST['attack_cost']);

if (isset($targetname) AND isset($attack_cost))
{
    if ($attackLoad->checkAttack($targetname, $attack_cost) == true)
    {
        include_once 'fight_attack.php';    
    }
    else
    {
        if (Session::get('error_message'))
        {
            $error = Session::get('error_message');
            unset(Session::get('error_message'));
            include_once '../error.php';
            exit;
        }
    }
}


?>
