<?php

/**
 * profession_recipies
 * id (1)
 * profession_id (1)
 * level_req (1)
 * recipie (1,2,3,4,5)
 */
 
class Professions
{
    private $dbh;
    public function __construct($database)
    {
        $this->dbh = $database;
    }
    
    public function getRecipie($professionid)
    {
        $query = $this->dbh->prepare('SELECT `recipie`,`level_req`,`amount` FROM `profession_recipies`
            WHERE `profession_id` = ?
            ORDER BY `level_req`');
        $query->execute(array($professionid));
        return ($query->rowCount() > 0) ? $query->fetchAll() : false;
    }
    
    public function playerItems()
    {
        $query = $this->dbh->prepare('SELECT `pitemid` FROM `pitems`
            WHERE `userid` = ?
            AND `itemid` = ?')
    }
    
}

include 'classes/session.php';
Session::init();
include 'database.php';
include 'classes/professions.php';
include 'classes/items.php';

$professions = new Professions($dbh);
$items = new Items($dbh);
$userid = Session::get('userid');

foreach ($professions->getRecipie($professionid) as $recipeData)
{
    $level_req = $recipieData['level_req'];
    if ($user_level >= $level_req)
    {
        $recipie_arr = array();
        $recipie_arr[] = $recipieData;
        foreach ($recipie_arr as $key => $ids)
        {
            $id = $ids
        }
        $qmarks = str_repeat('?,', count($id) - 1) . '?';
        $query = $dbh->prepare("SELECT `pitemid`,`itemid` FROM `pitems`
            WHERE `itemid` IN ($qmarks)
            AND `userid` = ?");
        $query->execute($id, $userid);
        
        $i = 0;
        $item_arr = array();
        foreach ($query->fetchAll() as $pitem)
        {
            $itemid = $pitem['itemid'];
            $pitemid = $pitem['pitemid'];
            $query = $dbh->prepare('SELECT `pitemid` FROM `pitems`
                WHERE `itemid` = ?
                AND `userid` = ?');
            $query->execute(array($itemid, $userid));

            $item_arr[$i]["total"] = $query->rowCount();
            $item_arr[$i]["itemid"] = $itemid;
            $item_arr[$i]["pitemid"] = $pitemid;
            ++$i;
            usleep(10);
        }
        $recipie_id = $recipieData['recipie'];
        $recipie_amount = $recipieData['amount'];
        $recipie_ids = explode(',', $recipie_id);
        $recipie_amounts = explode(',', $recipie_amount);
        $amount = count($recipie_id);
        $craft_obj = 0;
        for ($i = 0; $i < $amount; ++$i)
        {
            $r_amount = $recipie_amounts[$i];
            $p_amount = $item_arr[$i]["total"];
            if ($p_amount >= $amount)
            {
                ++$craft_obj;
                $craft_layout .= $amount .' / '. $item_arr[$i]["itemid"]; 
            }
        }
        
        if ($craft_obj == $amount)
        {
            echo 'You have all the required items!<br/>';
        }
        echo $craft_layout;
        

    }
    else
    {
        echo 'not the required level.';
    }
}
