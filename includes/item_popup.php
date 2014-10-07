<?php

include '../classes/items.php';

//Script for viewing an onmouseover for an item.
if (isset($_GET['itemid']))
{
    $items = new Items($dbh);
    $pitemid = htmlentities((int)$_GET['itemid']);
    
    //quick little error check.
    if ($pitemid == 0 OR !isset($pitemid) OR !is_numeric($pitemid)) //make sure the link is valid.
    {
        exit;
    }
    
    elseif ($items->pitemData($pitemid) == false) //make sure the item exists.
    {
        exit;
    }

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
            $aug_itemid = $paug['itemid'];
            $augData = $items->itemData($aug_itemid);
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
        list ($set_name, $set_data, $attack_set, $hp_set, $pset_items) = $items->configSet($setid);
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
    
    //configure all the items stats into the layout.
    $item_layout .= $items->createStats($attack, $aug_attack, 'Attack');
    $item_layout .= $items->createStats($hp, $aug_hp, 'Hp');
    $item_layout .= $items->createStats($ept, $aug_ept, 'Exp per turn');
    $item_layout .= $items->createStats($apt, $aug_apt, 'Attacks per turn');
    $item_layout .= $items->createStats($critical, $aug_critical, 'Critical');
    $item_layout .= $items->createStats($block, $aug_block, 'Block');
    $item_layout .= $items->createStats($max_attacks, $aug_max_attacks, 'Max Attacks');
    
    if ($setid != 0)
    {
    	$item_layout .= '<br/><br/>'.$set_name.' Set <br/>';
    	$item_layout .= '+'.$attack_set.' Attack / +'.$hp_set.' Hp)<br/>';
    	$item_layout .= $set_data;
    	if ($pset_items > 1)
    	{
    		$item_layout .= '(+'.$attack_set.' Attack / +'.$hp_set.')';
    	}
    }
    $item_layout .= '<img src="'.$image.'">';
    
    //gems
    if ($type == 'items')
    {
    	$item_layout .= '<br/><img src="'.$gem1.'"><img src="'.$gem2.'"><img src="'.$gem3.'"><img src="'.$gem4.'"><br/>';
    
    	//augs
    	foreach ($i = 0; $i < $aug_slots; ++$i)
    	{
    		$augid = $aug[$i];
    		if ($augid != '')
    		{
    			$paugData = $items->pitemData($augid);
    			$aug_itemid $paugData['itemid'];
    			$augData = $items->itemData($aug_itemid);
    			$aug_image = $augData['image'];
    			$item_layout .= '<img src="'.$aug_image.'">';
    		}
    		else
    		{
    			$aug_image = 'images/equipment/augslot.jpg';
    			$item_layout .= '<img src="'.$aug_image.'">';
    		}
    		usleep(10);
    	}
    	
    	$item_layout .= ($charges != 0) ? '<br/>Charges: '.$charges : '';
    	$item_layout .= ($duration > 1) ? '<br/>Duration: '.$duration : '';
    	
    	$item_layout .= '<br/><br/>'.$info;
    	
}
