<?php

class Users
{
    private $dbh;
    
     /**
     * Constructor  
     */
    public function __construct($database)
    {
        $this->dbh = $database;
        
        if (isset($_POST['doRegister']))
        {
            $this->doRegister();
        }
        if (isset($_POST['doLogin']))
        {
            $this->doLogin();
        }
    }
    
     /**
     * Fetch the the users data
     * @param int $userid
     * @return fetched result : bool (false)
     */
    public function userData($userid)
    {
        $query = $this->dbh->prepare('SELECT * FROM `stats`
            WHERE `id` = ?');
        $query->execute(array($id));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }
    
     /**
     * Check to see if the username exists
     * @param string $username
     * @returns fetch result 'username' & 'id' : bool (false)
     */
    public function checkUsername($username)
    {
        $query = $this->dbh->prepare('SELECT `username`,`id` FROM `stats`
            WHERE `username` = ?');
        $query->execute(array($username));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }
    
    public function doRegister()
    {
    }
    
}
