<?php

/**
 * Created by PhpStorm.
 * User: matti
 * Date: 2016-10-16
 * Time: 14:51
 */
class KlarnaLogger
{
    private $dblink;
    function __construct($dblink)
    {
        $this->dblink = $dblink;
    }
    public function logInformation($message)
    {
        $this->log($message,1);
    }
    public function logError($message)
    {
        $this->log($message,2);
    }
    private function log($string,$level)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $user = getUserID();
        mysqli_query($this->dblink,"INSERT into log (`message`,`user`,`browser`,`level`) VALUES ('$string',$user,'$useragent',$level)");
        echo mysqli_error($this->dblink);
    }
}