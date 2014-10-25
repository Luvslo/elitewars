<?php

class CreateItem
{
        private $dbh;
        
        /**
         * Construct
         * @param $database
         */
        public function __construct($database)
        {
                $this->dbh = $database;
                $this->users = new Users($this->dbh);
        }
        
        
        $stat_rand = rand(1,10);
        $stat_arr = ['attack', 'hp', 'rpt', 'ept', 'rampage', 'criticalhit', 'block', 'maxrage'];
        foreach ($stat_arr as $key => $stat)
        {
                switch ($stat)
                {
                        case 'attack':
                                $item_atk = round((rand(1,70) / 2) * $decrease);
                                break;
                        case 'hp':
                                $item_hp = round((rand(1,90) / 2) * $decrease);
                                break;
                        case 'rpt':
                                $item_rpt = round((rand(1,49) / 2) * $decrease);
                                break;
                        case 'ept':
                                $item_ept = round((rand(1,49) / 2) * $decrease);
                                break;
                        case 'rampage':
                                $item_ramp = round((rand(1,10) / 2) * $decrease);
                                break;
                        case 'criticalhit':
                                $item_crit = round((rand(1,5) / 2)* $decrease);
                                break;
                        case 'block':
                                $item_block = round((rand(2,10) / 2) * $decrease) * 50;
                                break;
                        case 'maxrage':
                                $item_maxrage = round((rand(2,10) / 2) * $decrease) * 50;
                                break;
                }
        }
        
        
        
        
        
        public static function createStats($userid, $mob_level)
        {
                $user = $this->users->userData($userid);
                $bp_space = $user['bpspace'];
                $bpup_space = $user['subp'];
                
                $decrease = $mob_level / (20 + $mob_level);
                $stats = rand(1,10);
                $item_stat = 0;
                for ($i = 1; $i <= $stats; ++$i)
                {
                        $stat = rand(1,10);
                        
                        //Setting the items hp or attack.
                        if ($stat <= 5 AND $stat_done[1] == 0)
                        {
                                $atkhp = rand(1,2);
                                switch ($atkhp)
                                {
                                        case 1:
                                                $item_atk = round((rand(1,70) / 2) * $decrease);
                                                break;
                                        case 2:
                                                $item_hp = round((rand(1,90) / 2) * $decrease);
                                                break;
                                }
                                $stat_done[1] = 1;
                                ++$item_stat;
                        }
                }
        }
        
        
        public static function createStats($userid, $mob_level, $bp_space, )
}

		$decrease = $mobLevel/(20+$mobLevel);
			$stats = rand(1,10);
			$item_stat = 0;
			for ($i=1; $i <= $stats; $i++) 
			{ 
				$stat = rand(1,10);
		
				if ($stat <= 5 and $stat_done[1] == 0) 
				{
					$atkhp = rand(1,2);
					if ($atkhp == 1) 
					{
						$item_atk = round((rand(1,70)/2)*$decrease);
					}
					else if($atkhp == 2) 
					{
				  		$item_hp = round((rand(1,90)/2)*$decrease);
					}
					$stat_done[1] = 1;
					$item_stat++;
				} 
				else if($stat == 6 and $stat_done[$stat] == 0) 
				{
					$chance = rand(1,100);
					$chance2 = rand(1,75);
					if ($chance >= $chance2) 
					{
						$item_rpt = round((rand(1,49)/2)*$decrease);
					}
					$stat_done[$stat] = 1;
					$item_stat++;
				} 
				else if ($stat == 7 and $stat_done[$stat] == 0) 
				{
					$chance = rand(1,100);
					$chance2 = rand(1,75);
					if ($chance >= $chance2) 
					{
						$item_ept = round((rand(1,49)/2)*$decrease);
					}
					$stat_done[$stat] = 1;
					$item_stat++;
				}
				else if ($stat == 8 and $stat_done[$stat] == 0) 
				{
					$chance = rand(1,75);
					$chance2 = rand(1,100);
					if ($chance >= $chance2) 
					{
						$item_crit = round((rand(1,5)/2)*$decrease);
					}
					$stat_done[$stat] = 1;
					$item_stat++;
				}
				else if ($stat == 9 and $stat_done[$stat] == 0) 
				{
					$chance = rand(1,100);
					$chance2 = rand(1,75);
					if ($chance >= $chance2) 
					{
						$item_ramp = round((rand(1,10)/2)*$decrease);
					}
					$stat_done[$stat] = 1;
					$item_stat++;
				}
				else if ($stat == 10 and $stat_done[$stat] == 0) 
				{
					$chance = rand(1,100);
					$chance2 = rand(1,75);
					if ($chance >= $chance2) 
					{
						$item_maxrage = round((rand(2,10)/2)*$decrease)*50;
					}
					$stat_done[$stat] = 1;
					$item_stat++;
				}
				else if ($stat == 10 and $stat_done[$stat] == 0) 
				{
					$chance = rand(1,100);
					$chance2 = rand(1,75);
					if ($chance >= $chance2) 
					{
						$item_block = round((rand(2,10)/2)*$decrease)*50;
					}
					$stat_done[$stat] = 1;
					$item_stat++;
				}
			}
