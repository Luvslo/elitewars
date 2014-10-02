<?php

function errorHandle($errno, $errstr, $errfile, $errline)
{
    static Database $dbh;
    
    $query = $dbh->prepare('INSERT INTO `error_log`
        (`severity`,`message`,`filename`,`lineo`,`time`) VALUES (?,?,?,?, NOW())');
    
    switch ($errno)
    {
        case E_NOTICE;
        case E_USER_NOTICE;
        case E_DEPRECATED;
        case E_USER_DEPRECATED;
        case E_STRICT;
            $query->execute(array('NOTICE', $errstr, $errfile, $errline));
            break;
            
        case E_WARNING;
        case E_USER_WARNING;
            $query->execute(array('WARNING', $errstr, $errfile, $errline));
            break;
            
        case E_ERROR;
        case E_USER_ERROR;
            $query->execute(array('FATAL', $errstr, $errfile, $errline));
            echo 'FATAL error'. $errstr.' at '.$errfile.':'.$errline;
            exit;
            
        default:
                echo 'Unkown error at '.$errfile.':'.$errline;
                exit;
    }
}

set_error_handler("errorHandle");
