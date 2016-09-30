<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-23
 * Time: 14:19
 */
use Klarna\XMLRPC\Klarna;
use Klarna\XMLRPC\Address;
use Klarna\XMLRPC\Country;
use Klarna\XMLRPC\Language;
use Klarna\XMLRPC\Currency;
class KlarnaHelper
{
    private $db;
    function __construct ($dblink)
    {
    $this->db = $dblink;
    }
    function getConfigForStore($storeid)
    {
        $k = new Klarna();
        $userID = $_SESSION["user"];
        if(!$this->verifyUserForStore($userID,$storeid))
        {
            die("No access on store");
        }
        $getstore = mysqli_query($this->db,"SELECT * FROM `store` where `id` = $storeid");
        $store= mysqli_fetch_assoc($getstore);
        $server =   Klarna::BETA;
        if($store["testmode"] == 0)
        {
            $server =   Klarna::LIVE;
        }

        $k->config(
            $store["eid"],              // Merchant ID
            $store["shared"], // Shared secret
            Country::SE,    // Purchase country
            Language::SV,   // Purchase language
            Currency::SEK,  // Purchase currency
            $server        // Server
        );
        return $k;
    }
    function verifyUserForStore($user,$store)
    {
        $res = mysqli_query($this->db,"SELECT * FROM `user_stores` WHERE `userid` = $user AND `storeid` = $store");
        if(!$res)
        {
           return false;
        }
        return true;
    }
}