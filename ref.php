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


	echo '<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="100%" bordercolordark="#5D6567" bordercolorlight="#5D6567">
		<tr>
			<td colspan="2">
				<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="100%" bordercolor="#5D6567" bgcolor="#2e2e2e">
					<tr>
					 	<td width="100%">
							<table border="0" cellpadding="10" cellspacing="0" style="border-collapse: collapse" width="100%">
								<tr>
									<td width="100%" colspan="2" align="left" valign="top">
										<p align="center">You have just clicked '.$clickname.'\'s link.
									</td>
								</tr>
								<tr>
									<td align="center" valign="top" rowspan="2" width="250">
										<img border="0" src="'.$image.'"><br/>
										<a href="profile.php?id='.$clickid.'">View Profile</a>
									</td>
									<td width="100%" align="left">
										<p align="center">By clicking on that link, you just made '.$clickname.'\'s </b> power increase by '.$click_increase.'</p>
									</td>
								</tr>
								<tr>
									<td width="100%" align="left">
										<p align="center"><font size="4" color="#D15A33"><b>WANT TO PLAY? CREATE YOUR OWN CHARACTER <img border="0" src="images/icon_arrowdown.gif" width="7" height="11"></b></font></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" width="100%">
				<table border="0" cellpadding="10" cellspacing="0" style="border-collapse: collapse" width="100%">
					<tr>
						<td width="5%" align="left" valign="top">&nbsp;
						</td>
						<td width="60%" align="left" valign="top">
							<p align="right"><b><a href="home.php?ref='.$clickid.'">CREATE YOUR CHARACTER</a> &gt;&gt;</b></p>
						</td>
						<td width="5%" align="left" valign="top">&nbsp;
						</td>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>';

//</td>
//</tr>
