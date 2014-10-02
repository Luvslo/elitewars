<?php

include 'classes/session.php';

Session::init();

include 'database.php';
include 'classes/users.php';

$userLoad = new Users($dbh);

if ($userLoad->isLoggedIn() == true);
{
    header('Location: members.php');
}

if (isset($_POST['doLogin']))
{
    if ($userLoad->doLogin() == true)
    {
        header('Location: members.php');
    }
    else
    {
        echo 'Error: Login failed. Please try again.';
    }
}

?>
<!-- begin login form -->
<form method="POST">
    <label for="username">Username</label>
    <input required type="text" name="username" pattern="[a-zA-Z0-9]{2,50}">

    <label for="password">Password</label>
    <input required type="password" name="password" pattern=".{4,}" autocomplete="off">

    <input type="submit" name="doLogin" value="Login">
</form>
<!-- end login form -->


