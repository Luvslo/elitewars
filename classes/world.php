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
        $query = $this->dbh->prepare('SELECT `x`,`y`,`z` FROM `rooms`
            WHERE `roomid` = ?');
        $query->execute(array($roomid));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }
    
    public function roomMap()
    {
        $query = $this->dbh->prepare('SELECT `image` FROM `roommaps`
            WHERE `z` = ?');
        $query->execute(array($z));
        return ($query->rowCount() > 0) ? $image = $query->fetchColumn() : false;
    }
    
    /**
     * Loads the room info. (ajax will be calling this function each map move.)
     * @return array
     */
    public function createWorld()
    {
        $room = $this->roomData();
        $x = $room['x'];
        $y = $room['y'];
        $z = $room['z'];
        $image = $this->roomMap();
        return array($x, $y, $z, $image);
    }
}




