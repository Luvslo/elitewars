<script>

// Complete Quest
function completeQuest(questid)
{
    $.ajax({
       type: 'GET',
       cache: false,
       url: 'includes/questComplete.php?questid='+questid,
       success: function (html)
       {
           $('#completeQuest').html(html);
       }
    });
}

// Accept Quest
function acceptQuest(questid)
{
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'includes/questAccept.php?questid='+questid,
        success: function (html)
        {
            $('#acceptQuest').html(html);
        }
    });
}

</script>

<?php

error_reporting(E_ALL);

class Quests
{
    public function __construct(Database $dbh)
    {
        $this->dbh = $dbh;
    }
    

    /**
     * Get the quest actions of a quest, based on the 'questid'
     * @param int $questid
     * @return bool (false) : fetch (obj)
     */
    public function questActions($questid)
    {
        $query = $this->dbh->prepare('SELECT `step1`,`step2`,`step3`,`step4`,`step5`,
            `requiredLevel`,`requiredQuest`,`questname`,`mobid` FROM `quest_actions`
            WHERE `questid` = ?');
        $query->execute(array($questid));
        
        // false = non existing quest
        // true = fetches the actions as an object
        return ($query->rowCount() == 0) ? false : $query->fetch(PDO::FETCH_OBJ);
    }
    

    /**
     * Get the player quest
     * @param int $questid
     * @param int $userid
     * @return bool (false) : fetch (obj)
     */
    public function playerQuest($questid, $userid)
    {
        $query = $this->dbh->prepare('SELECT `completed`,`dateStarted` FROM `playerquests` 
            WHERE `questid` = ?
            AND `userid` = ?');
        $query->execute(array($questid, $userid));
            
        // false = no quest active
        // true = fetches the players quest log for the result quest as am object.
        return ($query->rowCount() == 0) ? false : $query->fetch(PDO::FETCH_OBJ);
    }
       

    /**
     * Get the quest objectives of a quest, based off the 'questid'
     * @param int $questid
     * @return bool (false) : fetchAll (obj)
     */
    public function questObjectives($questid)
    {
        // type (enum) 'kill,collect,talk'
        $query = $this->dbh->prepare('SELECT `killid`,`collectid`,`amount` FROM `quest_objectives`
            WHERE `questid` = ?');
        $query->execute(array($questid))
            
        // false = no quest
        // true = fetches all the objectives (kills, items, talking) as an object.
        return ($query->rowCount() == 0) ? false : $query->fetchAll(PDO::FETCH_OBJ);
    }
        

    /**
     * Get the total amount of 'kills'
     * @param int $questid
     * @param int $userid
     * @param date $date
     * @return int fetchColumn - The total number of rows
     */
    public function playerKills($questid, $userid, $date)
    {
        $query = $this->dbh->prepare('SELECT COUNT(`attackid`) FROM `questkills`
            WHERE `questid` = ?
            AND `userid` = ?
            AND `date` > ?');
        $query->execute(array($questid, $userid, $date));
            
        return $query->fetchColumn();
    }
       

    /**
     * Set the current players quest to completed.
     * @param int $questid
     * @param int $userid
     */
    public function questComplete($questid, $userid)
    {
        $query = $this->dbh->prepare('UPDATE `playerquests` SET `completed` = 1
            WHERE `questid` = ?
            AND `userid` = ?');
        $query->execute(array($questid, $userid));
    }
    
    
    /**
     * Accept the quest - Insert into `playerquests`
     * @param int $questid
     * @param int $userid
     * @param date $date
     */
    public function acceptQuest($questid, $userid, $date)
    {
        if (!$this->playerQuest($questid, $userid))
        {
            $query = $this->dbh->prepare('INSERT INTO `playerquests` (`questid`,`userid`,`dateStarted`) VALUES (?,?,?)');
            $query->execute(array($questid, $userid, $date));
        }
    }
    

    /**
     * Check if a required quest has been completed, based off the 'required' questid.
     * @param int $questid
     * @param int $userid
     * @return bool
     */
    public function checkRequiredQuest($questid, $userid)
    {
        // Is there a required quest?
        if (isset($questid))
        {
            if (!$this->playerQuest($questid, $userid))
            {
                return false; // No Quest found.
            }
            else
            {
                $playerQuest = $this->playerQuest($questid, $userid);
                if ($playerQuest->completed === 0)
                {
                    return false; // Quest incomplete.
                }
                elseif ($playerQuest->completed === 1)
                {
                    return true; // Quest complete.
                }
            }
        }
        else
        {
            return true; // No required Quest.
        }
    }
}

// Quest.php

$dbh = new Database();
$dbQuests = new Quests($dbh);
$items = new Items($dbh);

$userid = Session::get('userid'); 
$questid = (int) $_GET['questid'];

$questActions = $dbQuests->questActions($questid);

// Does the Quest exist
if (!$questActions OR !isset($questid))
{
    echo 'Error: This quest does not exist!';
    exit;
}

$playerQuest = $dbQuests->playerQuest($questid, $userid);

// Quest needs to be accepted.
if (!$playerQuest)
{
    echo $questActions->step1 . '<br /><br />';
    echo '<a style="font-weight:bold;" onclick="acceptQuest(\''.$questid.'\')">Accept Quest</a>';
}

// Quest completed.
elseif ($playerQuest->completed === 1) 
{
    echo $questActions->step5;
}

// Quest in progress.
elseif ($playerQuest->completed === 0)
{
    echo $questActions->step3;
    
    $totalObjectives = 0;
    $totalCompleted = 0;
    foreach ($dbQuests->questObjectives($questid) as $objective)
    {
        $killid = $objective->killid;
        $collectid = $objective->collectid;
        $amount = $objective->amount;

        // Display kills
        if (isset($killid))
        {
            $mob = $dbQuests->mobData($killid);
            $mobname = $mob->name;
                
            ++$totalObjectives;
                
            // Determine the amount of kills the player has on the 'kill' mob.
            // Any kill that was after the questStarted date is a kill.
            $dateStarted = $playerQuest->dateStarted;
            $playerKills = $dbQuests->playerKills($questid, $userid, $dateStarted);
            if ($playerKills >= $amount)
            {
                ++$totalComplete;
            }
                
            echo 'Killed: ' . $playerKills . ' / ' . $amount . ' ' . $mobname . '<br />';
        }
            
        // Display items 
        if (isset($collectid))
        {
            $item = $items->itemData($collectid);
            $itemname = $item['name'];
                
            ++$totalObjectives;
                
            if ($playerItems >= $amount)
            {
                ++$totalComplete;
            }
                
            echo 'Collected: 0 / ' . $amount . ' ' . $itemname . '<br />';
        }
    }
        
    // Complete Quest (ajax onclick request.)
    if ($totalComplete >= $totalObjectives)
    {
        $dbQuests->questComplete($questid, $userid);
        if ($playerQuest->completed === 1)
        {
            echo '<a onclick="completeQuest(\''.$questid.'\')">Complete Quest</a>';   
        }
    }
}
else
{
    echo 'An unknown error occurred.';
}

// Accept Quest (ajax page load)

require 'core/classes/session.class.php';
Session::init();
require_once 'core/classes/database.class.php';
require_once 'core/classes/item.class.php';
require_once 'core/classes/quest.class.php';

$dbh = new Database();
$userid = Session::get('userid');
$questid = (int) $_GET['questid'];

$quests = new Quests($dbh);

if (isset($questid))
{
    $questActions = $quests->questActions($questid);
    if (!$questActions)
    {
        echo 'This quest does not exist.';
        exit;
    }
    
    $playerQuest = $quests->playerQuest($questid, $userid);
    
    if (!$playerQuest)
    {
        // Check required quest.
        $requiredQuest = $questActions->requiredQuest;
        if ($quests->checkRequiredQuest($requiredQuest, $userid))
        {
            if ($playerQuest->completed === 0 OR $playerQuest->completed === 1)
            {
                echo 'You have either already accepted, or completed this quest! [Glitch found].';
            }
            
            // Insert the newly accepted quest!
            $now = new DateTime();
    	    $date = $now->format('Y-m-d h:i:s');
            $quests->acceptQuest($questid, $userid, $date);
            
            echo $questActions->step2;
            
        }
        else
        {
            echo 'You must complete the required quest before accepting this quest.';
        }
    }
    else
    {
        echo 'You have already accepted this quest!';
    }
}


// Complete Quest (ajax page load)

require 'core/classes/session.class.php';
Session::init();
require_once 'core/classes/database.class.php';
require_once 'core/classes/item.class.php';
require_once 'core/classes/quest.class.php';

$dbh = new Database();
$userid = Session::get('userid');
$questid = (int) $_GET['questid'];

$quests = new Quests($dbh);

$questActions = $quests->questActions($questid);


