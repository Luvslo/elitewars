<?php

class Users
{
    private $dbh;
    private static $algo = '$2a';
    private static $cost = '$10';

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
    
     /**
     * Register the user
     * @return bool 
     */
    public function doRegister()
    {
        $session_token = $_SESSION['registerToken'];
        $form_token = $_POST['registerToken'];
        $username = htmlentities($_POST['username']);
        $password = htmlentities($_POST['password']);
        $vpassword = htmlentities($_POST['password_verify']);
        $email = htmlentities($_POST['email']);
        $ip = htmlentities($_POST['ip']);
        $sec_question = htmlentities($_POST['sec_question']);
        $sec_answer = htmlentities($_POST['sec_answer']);
        if ($session_token != $form_token)
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
        elseif ($this->checkUsername($username) == false)
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
            //everything checked out, hash the password, insert the user and unset the register session token.
            $password = self::hashPassword($password);
            $query = $this->dbh->prepare('INSERT INTO `stats`
                (`username`,`email`,`password`,`ip`,`sec_question`,`sec_answer`) VALUES (?,?,?,?,?,?)')
            $query->execute(array($username, $email, $password, $ip, $sec_question, $sec_answer));
            unset($_SESSION['registerToken']);
            return true;
        }
    }
    
    
    public function doRegister()
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
        $userData = $this->checkUsername($username);
        $userid = $userData['id'];
        $user_pass = $userData['password'];
        if (!self::checkPassword($user_pass, $form_pass))
        {
            self::userSessionStart($userid, $agent);
            unset($_SESSION['loginToken']);
            return true;
        }
    }
    
     /**
     * Once upon a users login, the session array will be started.
     * @param int $userid
     * @param string $agent
     */
    public static function userSessionStart($userid, $agent)
    {
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['userid'] = $userid;
        $_SESSION['count'] = 5;
        $_SESSION['userAgent'] = $agent;
    }
    
    
     /**
     * Verifies if the user is logged in or not
     * @return bool
     */
    public static function isLoggedIn()
    {
        if (isset($_SESSION['userid']) 
            AND $_SESSION['logged_in'] == true 
            AND $_SESSION['userAgent'] == $_SERVER['HTTP_USER_AGENT'] 
            AND isset($_SESSION['userAgent'])
        )
        return true;
    }
    
     /**
     * Destroys the users session, and logs them out.
     */
    public static function logOut()
    {
        return session_destroy(); /** note: this will be updated */
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
