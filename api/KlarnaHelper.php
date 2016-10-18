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
        $language = Language::SV;
        $country = Country::SE;
        $currency = Currency::SEK;
        switch($store["country"])
        {
            case "SE":
                $language = Language::SV;
                $country = Country::SE;
                $currency = Currency::SEK;
                break;
            case "NO":
                $language = Language::NB;
                $country = Country::NO;
                $currency = Currency::NOK;
                break;
            case "DE":
                $language = Language::DE;
                $country = Country::DE;
                $currency = Currency::EUR;
                break;
            case "FI":
                $language = Language::FI;
                $country = Country::FI;
                $currency = Currency::EUR;
                break;
            case "DK":
                $language = Language::DA;
                $country = Country::DK;
                $currency = Currency::DKK;
                break;
        }

        $k->config(
            $store["eid"],              // Merchant ID
            $store["shared"], // Shared secret
            $country,    // Purchase country
            $language,   // Purchase language
            $currency,  // Purchase currency
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