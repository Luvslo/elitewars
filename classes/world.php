<?php

class World
{
    private $dbh;
    
    public function __construct($database)
    {
        $this->dbh = $database;
        $this->users = new Users($this->dbh);
        Session::init();
    }
    
    public function roomData()
    {
        $user = $this->users->userData(Session::get('userid'));
        $roomid = $user['roomid'];
        $query = $this->dbh->prepare('SELECT `x`,`y`,`zone` FROM `rooms`
            WHERE `roomid` = ?');
        $query->execute(array($roomid));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }
}

$world = new World($dbh);
$room = $world->roomData();
