<?php

/** handles all the cron jobs for the game, everything is static.*/

class CronJobs
{
        private static $dbh;
        private static $date = date('M\-m\-d H\:i\:s');
        
        public function __construct($database)
        {
                self::$dbh = $database;
        }
        
        
        /**
         * Mob updates (1 minute)
         */
        public static function mobs()
        {
                $query = self::$dbh->query('UPDATE `mobkills` SET `killed` = `killed`-1 WHERE `killed` > 0');
                $query = self::$dbh->query('DELETE FROM `mobkills` WHERE `killed` = 0');
        }
        
       
        /**
         * Raid updates (1 minute)
         */
        public static function raids()
        {
                $query = self::$dbh->prepare('SELECT `godid`,`godhp` FROM `god`
                        WHERE `timespawn` <= ?
                        AND `dead` = 1');
                $query->execute(array(self::$date));
                foreach ($query->fetchAll() as $god)
                {
                        $godid = $god['godid'];
                        $hp = $god['godhp'];
                        $query = self::$dbh->prepare('UPDATE `god` SET `dead` = 0,
                                `godhpremaining` = ?
                                WHERE `godid` = ?');
                        $query->execute(array($hp, $godid));
                }
        }
        
        
        /**
         * Potions update (1 minute)
         */
        public static function potions()
        {
                $query = self::$dbh->query('SELECT `castid` FROM `potscast` WHERE `duration` > 0');
                while ($castid = $query->fetchColumn())
                {
                        $query = self::$dbh->prepare('UPDATE `potscast` SET `duration` = `duration` - 1
                                WHERE `castid` = ?');
                        $query->execute(array($castid));
                        
                        $query = self::$dbh->prepare('UPDATE `potscast` SET `expired` = 1
                                WHERE `duration` = 0');
                }
        }
        


        /**
         * Skill updates (1 minute)
         */
        public static function skills()
        {
                $query = self::$dbh->query('UPDATE `castskills` SET `duration` = `duration`-1 WHERE `duration` > 0');
                $query = self::$dbh->query('UPDATE `castskills` SET `expired` = 1 WHERE `duration` = 0');
                $query = self::$dbh->query('UPDATE `castskills` SET `recharge` = `recharge`-1	 where `recharge` > 0');
                $query = self::$dbh->query('UPDATE `castskills` SET `recharged` = 1 WHERE `recharge = 0');
                $query = self::$dbh->query('UPDATE `stats SET `active` = `active` - 1 WHERE `active` > 0');
                
                //update 
                $query = self::$dbh->query('SELECT `playerid`,`skillid` FROM `castskills` WHERE `recharged` = 1');
                foreach ($query->fetchAll() as $skill)
                {
                	$userid = $row['playerid'];
                	$skillid = $row['skillid'];
                	$query = self::$dbh->prepare('UPDATE `playerskills` SET `charged` = 1 
                		WHERE `playerid` = ?
                		AND `skillid` = ?');
                	$query->execute(array($userid, $skillid));
                }
        }
}
