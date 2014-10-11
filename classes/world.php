<?php

class World
{
    private $dbh;
    private $userid;
    
    public function __construct($database)
    {
        $this->dbh = $database;
        $this->users = new Users($this->dbh);
        $this->userid = Session::get('userid');
    }
    
    /**
     * Get the room information, x,y,z based off the users roomid.
     * @return fetch : bool
     */
    public function roomData()
    {
        $user = $this->users->userData($this->userid);
        $roomid = $user['roomid'];
        $query = $this->dbh->prepare('SELECT `x`,`y`,`z` FROM `room`
            WHERE `roomid` = ?');
        $query->execute(array($roomid));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }
    
    /**
     * Get the image of the map, based off its zone.
     * @return $image : bool
     */
    public function roomMap()
    {
        $room = $this->roomData();
        $z = $room['z'];
        $query = $this->dbh->prepare('SELECT `image` FROM `roommap`
            WHERE `z` = ?');
        $query->execute(array($z));
        return ($query->rowCount() > 0) ? $image = $query->fetchColumn() : false;
    }
    
    /**
     * Set up the x y coordinates for the map.
     * @return array - $x, $y
     */
    public static function configXY()
    {
        $room = $this->roomData();
        $z = $room['z'];
        if ($z == 1)
        {
            $x = $room['x'] - 1.5;
            $y = $room['y'] - 1.5;
        }
        else
        {
            $x = $room['x'] - 3.5;
            $y = $room['y'] - 3.5;
        }
        $x *= -32;
        $y *= -32;
        return array($x, $y);
    }

include 'core/classes/world.class.php';
$world = new World($dbh);
$image = $world->roomMap();
list ($x, $y) = $world->configXY();

//the positioning of the map image.
echo '<div id="worldMap" 
            style="border:1px solid#000; 
            background-color:#505050; 
            width:249px; height:249px; 
            overflow:none; 
            background-image:url('.$image.'); 
            background-position: '.$x.'px;'.$y.'px;
            background-repeat:no-repeat;">
        <div id="worldDot"><img src="images/map/redDot.png"></div>
    </div>';
  




