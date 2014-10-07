<?php

class Items
{
    private $dbh;
    
    public function __construct($database)
    {
        Session::init();
        $this->dbh = $database;
        $this->userid = Session::get('userid');
    }
    
    /**
     * Fetches the item id of the actual item.
     * @param int $pitemid
     * @return fetch : bool
     */
    public function pItemData($pitemid)
    {
        $query = $this->dbh->prepare('SELECT `itemid` FROM `pitems`
            WHERE `pitemid` = ?');
        $query->execute(array($pitemid));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }
    
    /**
     * Fetches the actual item information
     * @param int $itemid
     * @return fetch : bool
     */
    public function itemData($itemid)
    {
        $query = $this->dbh->prepare('SELECT * FROM `items`
            WHERE `itemid` = ?');
        $query->execute(array($itemid));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }
    
    /**
     * Generate the increase for the item being viewed.
     * @param int $gems
     * @return int
     */
    private function gemData($gems)
    {
        switch ($gems)
        {
            case 0:
                $gem_increase = '1.00';
                break;
            case 1:
                $gem_increase = '1.25';
                break;
            case 2:
                $gem_increase = '1.50';
                break;
            case 3:
                $gem_increase = '1.75';
                break;
            case 4:
                $gem_increase = '2.00';
                break;
            default $gem_increase = '1.00';
        }
        return $gem_increase;
    }


    /**
     * Fetch info from the set table
     * @param int $setid
     * @param bool $type
     * @return fetch : rowCout
     */
    public function setData($setid, $type)
    {
        $query = $this->dbh->prepare('SELECT * FROM `set`
            WHERE `setid` = ?');
        $query->execute(array($setid));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }

    /**
     * Check how much/if the user is wearing a fullset, no set, or some set
     * @param int $setid
     * @return array
     */
    public function configSet($setid)
    {
        $setData = $this->setData($setid);
        $name = $setData['set_name'];
        $attack = $setData['attackbonus'];
        $hp = $setData['hpbonus'];

        //set parts.
        $feet = $set['setfeet'];
        $pants = $set['setpants'];
        $ring = $set['setring'];
        $belt = $setp['setbelt'];
        $body = $set['setchest'];
        $weapon = $set['setweap'];
        $shield = $set['setshield'];
        $head = $set['sethelm'];
        $neck = $set['setneck'];
        
        $set_parts = array();
        $parts = array($feet, $pants, $ring, $belt, $body, $weapon, $shield, $head, $neck);
        
        foreach ($parts as $key => $value)
        {
        	if ($value != 0)
        	{
        		$set_parts[] = $value;
        	}
        	usleep(10);
        }

        $total = count($set_parts);
        $qmarks = str_repeat('?,', count($set_parts) - 1) . '?';
        $query = $dbh->prepare("SELECT `equipped`,`itemid` FROM `pitems`
        	WHERE `itemid` IN ($qmarks)
        	AND `userid` = ?
        	AND `equipped` = 1");
        $query->execute($set_parts, $this->userid);

        $p_total = $query->rowCount();
        
        foreach ($query->fetchAll() as $pitem)
        {
	        $itemid = $pitem['itemid'];
	        $equipped = $pitem['equipped'];
	        $query = $dbh->prepare('SELECT `name` FROM `items`
	        	WHERE `itemid` = ?');
	       	$query->execute(array($itemid));
	       	while ($item_name = $query->fetchColumn())
	       	{
	       		$set_data .= $item_name;
	       		usleep(10);
	       	}
	       	$set_data .= ($equipped == 1) ? '<br />' : '';
        }
	    //echo $set_data; //debugging.
	    
	    if ($p_total > 1)
	    {
	        $percent = $p_total / $total;
	        $attack_set = round($attack * $percent);
	        $hp_set = round($hp * $percent);
	    }
	    return array ($set_name, $set_data, $attack_set, $hp_set);
    }
    
    public function pItemCount($itemid)
    {
        $query = $this->dbh->prepare('SELECT `itemid` FROM `pitems`
            WHERE `itemid` = ?
            AND `userid` = ?
            AND `dropped` = 0');
        $query->execute(array($itemid, $this->userid));
        return $query->rowCount();
    }
    
    public function levelRequired($level)
    {
        switch ($level)
        {
            case ($level > 0 OR $level < 10):
                $level_output = '<span style="color:#FF8000;">[Level Required - '.$level.']</span>';
                break;
            case ($level > 10 OR $level < 20):
                $level_output = '<span style="color:#66CD00;">[Level Required - '.$level.']</span>';
                break;
            default: '';
                break;
        }
        return $level_output;
    }

    
    public function createStats($stat, $aug_stat, $stat_text)
    {
        if ($stat > 0 OR $aug_stat > 0)
        {
            $item_layout .= '<br/>'.$stat;
            if ($aug_stat > 0)
            {
                $item_layout .= '<span style="color:#00FF00;">&nbsp;(+'.$aug_stat.')</span>';
            }
            $item_layout .= '&nbsp;'.$stat_text;
        }
        return $item_layout;
    }


}
    

//Script for viewing an onmouseover for an item.
if (isset($_GET['itemid']))
{
    $items = new Items($dbh);
    $pitemid = htmlentities((int)$_GET['itemid']);
    
    $pitem = $items->pItemData($pitemid);
    $itemid = $pitem['itemid'];
    $gems = $pitem['gems'];
    $userid = $pitem['userid'];
    $charges = $pitem['charges'];
    $expires = $pitem['expires'];
    $type = $pitem['type'];
    
    $item = $items->itemData($itemid);

    $slot = ($type == 'items') ? $item['slot'] : $type;
    
    $gem_increase = $items->gemData($gems);
    
    //quest quantity
    if ($type == 'quest' AND $userid != 0)
    {
        $item_count = $items->pItemCount($itemid);
    }

    $attack = round($item['attack'] * $gem_increase);
    $hp = round($item['hp'] * $gem_increase);
    $apt = round($item['apt'] * $gem_increase); //attacks per turn.
    $ept = round($item['ept'] * $gem_increase); //exp per turn.
    $max_attacks = round($item['max_attacks'] * $gem_increase);
    $critical = $item['critical'];
    $block = $item['block'];
    $level_req = $item['level'];
    $setid = $item['setid'];
    $aug_slots = $item['aug_slots'];
    $user_bound = $item['user_bound'];
    $duration = ($type != 'potions') ? $expires : $item['duration'];
    $item_name = $item['name'];
    $description = $item['description'];
    $info = $item['info'];
    $color = $item['color'];
    
    $image = (!empty($item['image'])) ? $item['image'] : 'images/imgerror.gif';
    

    //aug information (TODO: turn this into a function in the items class)
    foreach ($i = 0; $i < $aug_slots; ++$i)
    {
        $augid = $pitem["aug_slot$i"];
        if ($augid != 0)
        {
            $paugData = $items->pItemData($augid);
            $augid = $paug['itemid'];
            $augData = $items->itemData($augid);
            $aug[$i] = $augid;
            $aug_type = $augData['type'];
        }
        usleep(10);
    }
    $aug_attack += $augData['attack'];
    $aug_hp += $augData['hp'];
    $aug_ept += $augData['ept'];
    $aug_apt += $augData['apt'];
    $aug_max_attacks += $augData['max_attacks'];
    $aug_critical += $augData['critical'];
    $aug_block += $augData['block'];
    
    //set information.
    if ($setid != 0)
    {
        list ($set_name, $set_data, $attack_set, $hp_set) = $items->configSet($setid);
    }
    
    //construct the actual layout
    $item_layout .= $item_name;
    $item_layout .= ($user_bound > 0) ? '[Playerbound]' : '';
    $item_layout .= ($type != 'items' AND $type != 'orbs') ? '[Inventory]<br/>' : '';
    $item_layout .= '[Slot: '.$slot.'<br/>';
   
    //level required
    $item_layout .= $items->levelRequired($level_req);
    
    $item_layout .= ($type == 'quest' AND $item_count > 0) ? '[Quantity - '.$item_count.']' : '';
    
    //description
    if ($type != 'items')
    {
        if ($description != '')
        {
            $item_layout .= '<br/>'.$desc.'<br/>';
        }
    }
    
    //attack
    if ($attack != 0 OR $aug_attack != 0)
    {
        $item_layout .= '<br/>'.$attack;
        if ($aug_attack != 0)
        {
            $item_layout .= '<span style="color:#00FF00;"> (+'.$aug_attack.')</span>';
        }
        $item_layout .= '&nbsp;Attack';
    }
    
    //hp
    if ($hp != 0 OR $aug_hp != 0)
    {
        $item_layout .= '<br/>'.$hp;
        if ($aug_hp != 0)
        {
            $item_layout .= '<span style="color:#OOFFOO;"> (+'.$aug_hp.')</span>';
        }
        $item_layout .= '&nbsp;Hp';
    }

    //exp per turn
    if ($ept != 0 OR $aug_ept != 0)
    {
        $item_layout .= '<br/>'.$ept;
        if ($aug_ept != 0)
        {
            $item_layout .= '<span style="color:#00FF00;"> (+'.$aug_ept.')</span>';
        }
        $item_layout .= '&nbsp;Exp per turn'
    }
    
    
    $item_layout .= $items->createStats($attack, $aug_attack, 'Attack');
    $item_layout .= $items->createStats($hp, $aug_hp, 'Hp');
    $item_layout .= $items->createStats($ept, $aug_ept, 'Exp per turn');
    $item_layout .= $items->createStats($apt, $aug_apt, 'Attacks per turn');
    $item_layout .= $items->createStats($critical, $aug_critical, 'Critical');
    $item_layout .= $items->createStats($block, $aug_block, 'Block');
    $item_layout .= $items->createStats($max_attacks, $aug_max_attacks, 'Max Attacks');
    





