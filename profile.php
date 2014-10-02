<?php

/**
* Profile, using $_GET['userid'];
*/

include 'classes/session.php';
include 'classes/database.php';
include 'classes/users.php';

Session::init();

$userid = htmlentities((int)$_GET['userid']); //make sure the userid is an integer.

$userLoad = new Users(Database $dbh);

//check to see if the user exists.
if ($userLoad->userData($userid) == false)
{
    $error = 'Error: This user does not exist.';
    include_once 'error.php';
    exit;
}
else
{
    //user exists.
    $userData = $userLoad->userData($userid);
}

$username = $userData['username'];

echo 'Viewing the profile of '.$username;

?>
