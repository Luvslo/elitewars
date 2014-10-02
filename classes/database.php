<?php


 /**
 * Database class, creates a PDO connection.
 */

class Database extends PDO
{
    public function __construct()
    {
        $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
        parent::__construct(DB_TYPE . ':host' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS, $options);
    }
}

?>
