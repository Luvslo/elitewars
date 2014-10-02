<?php

class Attack
{
    private $dbh;
    
    public function __construct(Database $dbh);
    {
        $this->dbh = $dbh;
        $this->users = new Users($this->dbh);
        Session::init();
    }
    
    public function attackCheck($targetname, $attack_cost)
    {
        if ($attack_cost < 1 OR $attack_cost > 50)
        {
            Session::set('error_message', 'You may only user 1-50 attacks per attack.');
            return false;
        }
        elseif (!is_numeric($attack_cost))
        {
            Session::set('error_message', 'You entered a non numerical amount of attacks to use.');
            return false;
        }
        elseif (!isset($targename) OR !isset($attack_cost))
        {
            Session::set('error_message', 'You may not leave any fields from the attack form blank.');
            return false;
        }
        elseif ($this->users->checkUsername($targetname) == false)
        {
            Session::set('error_message', 'The user you are trying to attack does not exist.');
            return false;
        }
        else
        {
            /**
             * Todo: Only allow attack on user once per hour, 
             *      or create an upgrade like more attacks per hour on a user.
             */
            return true;
        }
    }
    
    /**
     * Create an array of the targets stats and the users stats.
     * @param string $targetname
     * @return array
     */
    public function defineAttack($targetname)
    {
        //define the target
        $targetFetch = $this->users->checkUsername($targetname);
        $targetid = $targetFetch['id']; 
        $targetData = $this->users->userData($targetid);
        $target_atk = $targetData['attack'];
        $target_hp = $targetData['hp'];
        $target_crit = $targetData['critical'];

        //define the attacker
        $userData = $this->users->userData(Session::get('userid'));
        $user_atk = $userData['attack'];
        $user_hp = $userData['hp'];
        $user_crit = $userData['critical'];
        
        return array(
                        $targetid,
                        $target_atk,
                        $target_hp,
                        $target_crit,
                        $user_atk,
                        $user_hp,
                        $user_crit
                    );
    }
    
}

?>
