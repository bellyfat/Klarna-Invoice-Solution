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
    function logInformation()
    {

    }
    function logError()
    {

    }
    private function log($string,$level)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $user = getUserID();
        mysqli_query($this->dblink,"INSERT into log (`message`,`user`,`browser`) VALUES ('$string',''$user,'$useragent')");
    }
}