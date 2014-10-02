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
    
    public static function uniqueSalt()
    {
        return substr(sha1(mt_rand()),0,22);
    }
    
    public static function hashPassword($password)
    {
        return crypt($password, self::$algo . self::$cost . '$' . self::uniqueSalt());
    }
    
    public static function checkPassword($hash, $password)
    {
        $salt = substr($hash, 0, 29);
        $new_hash = crypt($password, $salt);
        return ($hash == $new_hash);
    }
}
