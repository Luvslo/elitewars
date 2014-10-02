<?php

class Session
{
     /**
     * Starts the session if no session is started yet.
     */ 
    public static function init()
    {
        if (empty(session_id) OR session_id() == '')
        {
            session_start();
        }
    }
    
     /**
     * Sets the session
     * @param mixed $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
     /**
     * Returns the session
     * @param mixed $key
     * @return mixed
     */
    public static function get($key)
    {
        if (isset($_SESSION[$key]))
        {
            return $_SESSION[$key];
        }
    }
    
     /**
     * Regenerates the session_id
     * @param bool (true) or (false)
     * @returns session_regenerate_id
     */
    public static function reg($value)
    {
         session_regenerate_id($value);
    }
    
    
     /**
     * Deletes the session/logs the user out.
     */ 
    public static function destroy()
    {
        session_destroy();
    }

}
?>
