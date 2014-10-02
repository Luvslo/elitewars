<?php


include '../classes/session.php';

Session::init();

include '../classes/users.php';
include '../classes/attack.php';

$userLoad = new Users(Database $dbh);

$targetFetch = $userLoad->checkUsername($targetname);
$targetid = $targetFetch['id'];
$targetData = $userLoad->userData($targetid);

$target_atk = $targetData['attack'];
$target_hp = $targetData['hp'];
$target_crit = $targetData['critical'];

$user_atk = $userData['attack'];
$user_hp = $userData['hp'];
$user_crit = $userData['critical'];






?>
