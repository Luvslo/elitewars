<?php

class Users
{
    private $dbh;
    private static $algo = '$2a';
    private static $cost = '$10';

     /**
     * Constructor  
     */
    public function __construct(Database $database)
    {
        $this->dbh = $database;
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
    
     /**
     * Perform registration proccess.
     * @return bool 
     */
    public function doRegister()
    {
        $sess_token = $_SESSION['registerToken'];
        $form_token = $_POST['registerToken'];
        $username = htmlentities($_POST['username']);
        $password = htmlentities($_POST['password']);
        $vpassword = htmlentities($_POST['password_verify']);
        $email = htmlentities($_POST['email']);
        $ip = htmlentities($_POST['ip']);
        $sec_question = htmlentities($_POST['sec_question']);
        $sec_answer = htmlentities($_POST['sec_answer']);
        if ($sess_token != $form_token)
        {
            return false;
        }
        elseif (!preg_match('/^[a-z\d]{2,64}$/i', $username))
        {
            return false;
        }
        elseif (!isset($username) OR !isset($password) OR !isset($vpassword) OR !isset($email) OR !isset($ip) OR !isset($sec_question) OR !isset($sec_answer))
        {
            return false;
        }
        elseif (strlen($username) < 3 OR strlen($username) > 50)
        {
            return false;
        }
        elseif ($password != $vpassword)
        {
            return false;
        }
        elseif (strlen($password) < 4)
        {
            return false;
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
        elseif ($this->checkUsername($username) != false)
        {
            return false;
        }
        elseif (isset($username) 
            AND isset($password) 
            AND isset($vpassword) 
            AND isset($email) 
            AND isset($ip) 
            AND isset($sec_question) 
            AND isset($sec_answer)
            AND filter_var($email, FILTER_VALIDATE_EMAIL)
            AND preg_match('/^[a-z\d]{2,64}$/i', $username)
            AND strlen($username) > 2 AND strlen($username) < 50
            AND strlen($password) > 3)
        {
            //everything checked, now hash the password and insert the user.
            $password = self::hashPassword($password);
            $query = $this->dbh->prepare('INSERT INTO `stats`
                (`username`,`email`,`password`,`ip`,`sec_question`,`sec_answer`) VALUES (?,?,?,?,?,?)')
            $query->execute(array($username, $email, $password, $ip, $sec_question, $sec_answer));
    
            //remove the register session token and return true (bool) registration successful.
            unset($_SESSION['registerToken']);
            return true;
        }
    }
    
     /**
     * Perform login proccess.
     * @return bool
     */
    public function doLogin()
    {
        $username = htmlentities($_POST['username']);
        $password = htmlentities($_POST['password']);
        $form_token = $_POST['loginToken'];
        $sess_token = $_SESSION['loginToken'];

        $time = time() - 10*60; //now - 10 mins
        
        $query = $this->dbh->prepare('SELECT `ip` FROM `failed_logins`
            WHERE `time` > ?');
        $query->execute(array($time));
        if ($query->rowCount() >= 3)
        {
            return false; //the user must wait 10 minutes to try logging in again.
        }
        elseif (!self::checkToken($form_token, $sess_token))
        {
            return false;
        }
        elseif (!isset($username) OR !isset($password))
        {
            return false;
        }
        elseif (!preg_match('/^[a-z\d]{2,64}$/i', $username))
        {
            return false;
        }
        elseif ($this->checkUsername($username) == false)
        {
            return false;
        }
        //define user const
        $userData = $this->checkUsername($username);
        $userid = $userData['id'];
        $user_pass = $userData['password'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!self::checkPassword($user_pass, $form_pass))
        {
            
            //update the users status to active
            $query = $this->dbh->prepare('UPDATE `stats` SET `login_timestamp` = ?
                WHERE `id` = ?');
            $query->execute(array(time(),$userid));
            
            //initiate session, and set them.
            Session::init();
            Session::set('logged_in', true);
            Session::set('userid', $userid);
            Session::set('agent', $agent);
            Session::set('count', 5);
            unset($_SESSION['loginToken']); //remove the login session token.
            
            return true; //login successful, return true (bool) login successful.
        }
    }
    
     /**
     * Verifies if the user is logged in or not
     * @return bool
     */
    public function isLoggedIn()
    {
        $check = 0;
        if (Session::get('logged_in') == true)
        {
            ++$check;    
        }
        if (Session::get('userid'))
        {
            ++$check;
        }
        if (Session::get('userAgent'))
        {
            ++$check;
        }
        if (Session::get('userAgent') == $_SERVER['HTTP_USER_AGENT'])
        {
            ++$check;
        }
        return ($check == 4) ? true : false;
    }
    
     /**
     * Session count starts at 5. Every page load 1 is subtracted.
     * When the count hits 0, an expired session is created
     * and a regenerate_session_id(false) is called.
     * 
     * Next the checkSessExpire function is called,
     * which calls regenerates_session_id(true)
     * when the count is 3 and expired is set.
     * 
     * This is for ajax & added security.
     */
    public static function checkSessCount()
    {
        if ((Session::get('count') -= 1) == 0)
        {
            Session::set('count', 5);
            Session::set('expired', true);
            Session::reg(false); //regenerate the session id (false)
        }
        self::checkSessExpire();
    }
    
    public static function checkSessExpire()
    {
        if (Session::get('count') == 3 AND Session::get('expired'))
        {
            unset(Session::get('expires')); //unset expired session.
            Session::reg(true); //regenerate the session id (true)
        }
    }
    
     /**
     * Destroys the users session, and logs them out.
     */
    public static function logOut()
    {
        Session::destroy();
    }

    /**
     * Create a random salt
     * @returns mixed
     */
    public static function uniqueSalt()
    {
        return substr(sha1(mt_rand()),0,22);
    }
    
     /**
     * Hash the password
     * @param string $password
     * @return hashed password
     */
    public static function hashPassword($password)
    {
        return crypt($password, self::$algo . self::$cost . '$' . self::uniqueSalt());
    }
    
     /**
     * Verify password
     * @param string $hash
     * @param string $password
     * @return bool
     */
    public static function checkPassword($hash, $password)
    {
        $salt = substr($hash, 0, 29);
        $new_hash = crypt($password, $salt);
        return ($hash == $new_hash);
    }
    
     /**
     * Verify form tokens
     * @param string $form
     * @param string $session
     * @return bool
     */
    public static function checkToken($form, $session)
    {
        return ($form == $session);
    }
}
