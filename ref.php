<?php

include 'classes/users.php';
include 'database.php';
$users = new Users($dbh);

if (!isset($_GET['x']))
{
    echo 'Error: You must be referring someone.';
    exit;
}


$ip = $_SERVER['REMOTE_ADDR'];
$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$ref = getenv('HTTP_REFERER');
$agent = $_SERVER['HTTP_USER_AGENT'];
    
$query = $dbh->prepare('INSERT INTO `ip_log` (`ip`,`host`,`refer`,`browser`)
    VALUES (?,?,?,?)');
$query->execute(array($ip, $host, $ref, $agent))

if (isset($_GET['x'])
{
    $x = htmlentities((int)$_GET['x']);

    $query = $dbh->prepare('SELECT `ip` FROM `refer` WHERE `ip` = ?');
    $query->execute(array($ip));
    if ($query->rowCount() != 0)
    {
        echo 'Error: You have already clicked this link today.';
        exit;
    }
    if ($users->userData($x) == false)
    {
        echo 'Error: This user does not exist.';
        exit;
    }
    else
    {
        $user = $users->userData($x);
        $username = $user['username'];
        echo 'You have clicked on '.$username.'\'s link and helped them gain 50 experience.';
        
    }
}
