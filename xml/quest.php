<?php

// Loading the quest xml file.
// Debating on if this is going to be better than using a database..

$dbh = new Database();

$xmlQuests = simplexml_load_file('quests.xml');
$dbQuests = new Quests($dbh);

$questid = (int) $_GET['questid'];

$userid = Session::get('userid'); 


        /**
         * Get the player quest
         * @param int $questid
         * @param int $userid
         * @return bool : fetch
         */
        public function playerQuest($questid, $userid)
        {
            $query = $this->dbh->prepare('SELECT `step` FROM `playerquests` 
                WHERE `questid` = ?
                AND `userid` = ?');
            $query->execute(array($questid, $userid));
            
            // false = no quest active
            // true = fetches the results, as am object.
            return ($query->rowCount() == 0) ? false : $query->fetch(PDO::FETCH_OBJ);
       }


$playerQuest = $dbQuests->playerQuest($questid, $userid);
$step = $playerQuest->step;

if ($playerQuest->completed == 1)
{
    echo $xmlQuests->quests['questid']->completed;
}
else
{
    echo $xmlQuests->quests['questid']->step . $step;
}
