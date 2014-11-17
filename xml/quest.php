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
     * Clear Quest Log for repeatable quests.
     * @param int $questid
     * @param int $userid
     */
    public function clearPlayerQuest($questid, $userid)
    {
        $query = $this->dbh->prepare('DELETE FROM `playerquests`
            WHERE `questid` = ?
            AND `userid` = ?
            AND `completed` = 1');
        $query->execute(array($questid, $userid));
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
    
    
    /**
     * Get the quest rewards of a quest, based off the 'questid'
     * @param int $questid
     * @return false : fetchAll (obj)
     */
    public function questRewards($questid)
    {
        $query = $this->dbh->prepare('SELECT `experience`,`itemid`,`itemType`,`gold` FROM `quest_reward`
            WHERE `questid` = ?');
        $query->execute(array($questid));
        
        return ($query->rowCount() == 0) false : $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    
    /**
     * Quest Experience Reward
     * @param int $xpGain
     * @param int $userid
     */
    public function questRewardExp($xpGain, $userid)
    {
        $query = $this->dbh->prepare('UPDATE `stats` SET 
            `exp` = `exp`+?, 
            `ggain` = `ggain`+?, 
            `gtoday` = `gtoday`+?
            WHERE `id` = ?');
        $query->execute(array($xpGain, $xpGain, $xpGain, $userid));
    }
    
    
    /**
     * Give (insert) the reward item to the player
     * @param int $itemid
     * @param int $userid
     * @param string $type
     * @param int $expires
     */
    public function insertPlayerItem($itemid, $userid, $type, $expires)
    {
        $query = $this->dbh->prepare('INSERT INTO `playeritems`
            (`itemid`,`id`,`type`,`expires) VALUES (?,?,?,?)');
        $query->execute(array($itemid, $userid, $type, $expires));
    }
    
    
    
    /**
     * Remove playeritems (setting to dropped)
     * @param int $itemid
     * @param int $userid
     * @param int $amount - The limit on how many items to set as 'dropped'
     */
    public function clearQuestItems($itemid, $userid, $amount)
    {
        $query = $this->dbh->prepare('UPDATE `playeritems` SET `dropped` = 1
            WHERE `itemid` = :itemid
            AND `id` = :id
            LIMIT :limit');
        $query->bindValue(':itemid', $itemid, PDO::PARAM_INT);
        $query->bindValue(':id', $userid, PDO::PARAM_INT);
        $query->bindValue(':limit', (int)trim($amount), PDO::PARAM_INT);
        $query->execute();
    }
    
    
    
    /**
     * Remove quest kills
     * @param int $killid
     * @param int $userid
     * @param int $amount - The limit on how many kills to remove from the log.
     */
    public function clearQuestKills($killid, $userid, $questid, $itemid)
    {
        $query = $this->dbh->prepare('DELETE FROM `questkills` WHERE `realmobid` = :realmobid
            AND `id` = :id
            AND `questid` = :questid
            LIMIT :limit');
        $query->binValue(':realmobid', $killid, PDO::PARAM_INT);
        $query->bindValue(':id', $userid, PDO::PARAM_INT);
        $query->bindValue(':questid', $questid, PDO::PARAM_INT);
        $query->bindValue(':limit', (int)trim($amount), PDO::PARAM_INT);
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
        echo '<a style="font-weight:bold;" onclick="completeQuest(\''.$questid.'\')">Complete Quest</a>';   
        /*$dbQuests->questComplete($questid, $userid);
        if ($playerQuest->completed === 1)
        {
        }*/
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

if (isset($questid))
{
    $questActions = $quests->questActions($questid);
    if (!$questActions)
    {
        echo 'This quest does not exist.';
        exit;
    }
    
    $playerQuest = $quests->playerQuest($questid, $userid);
    
    // Check to see if the user has even started and/or completed the quest.
    if ($playerQuest->completed === 1)
    {
        echo $questActions->part4 . '<br /><br />';

        $quests->questComplete($questid, $userid);   
        
        // Repeatable Quest.
        if ($questActions->repeatable === 1)
        {
            clearPlayerQuest($questid, $userid);
        }
        
        // Quest Rewarding (exp, items, gold)
        foreach ($quests->questRewards($questid) as $rewards)
        {
            $exp = $rewards->experience;
            $itemid = $rewards->itemid;
            $itype = $rewards->itemType;
            $gold = $rewards->gold;
            
            // Item(s)
            if (isset($itemid))
            {
                $item = $items->itemData($itemid);
                $itemname = $item['name'];
                $duration = $item['duration'];
                $expires = ($itype != 'potions') ? 0 : $duration:
                $quests->insertPlayerItem($itemid, $userid, $itype, $expires); 
                $item_output .= '<span style="font-weight:bold;">You have received a ' . $itemname . '</span><br />';
            }
            
            // Experience
            if ($exp > 0)
            {
                $quests->questExpReward($exp, $userid);
                echo '<span style="font-weight:bold;">You have received '. number_format($exp) . ' experience.</span><br /><br />';
            }
            
            // Gold
            if ($gold > 0)
            {
                echo '<span style="font-weight:bold;">You have received '. number_format($gold) . ' experience.</span><br /><br />';
            }
        }
        
        echo $item_output . '<br /><br />';
        

        foreach ($quests->questObjectives($questid) as $objective)
        {
            $killid = $objective->killid;
            $itemid = $objective->itemid;
            $amount = $objective->amount;
                
            // Clearing the questkills.
            if (isset($killid))
            {
                $quests->clearQuestKills($killid, $userid, $questid, $amount);
            }
            if (isset($itemid))
            {
                // Remove quest items.
                if ($questActions->keepItems === 0)
                {
                    $quests->clearQuestItems($itemid, $userid, $amount);
                }
            }
        }
    }
    elseif ($playerQuest->completed === 0 OR !$playerQuest)
    {
        echo 'You have not completed this quest!';
    }
}
else
{
    echo 'Invalid quest.';
}


