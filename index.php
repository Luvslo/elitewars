<?php

session_start();
include 'database.connect.php';
include 'classes/users.php';

$userLoad = new Users($dbh);

if($userLoad->isLoggedIn() == true)
{
    header('Location: members.php');
}

?>


<form method="POST">
    <label for="username">Username</label>
    <input required type="text" name="username" pattern="[a-zA-Z0-9]{2,50}">

    <label for="password">Password</label>
    <input required type="password" name="password" pattern=".{4,}" autocomplete="off">

    <input type="submit" name="doLogin" value="Login">
</form>


