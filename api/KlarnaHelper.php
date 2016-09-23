<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-23
 * Time: 14:19
 */
class KlarnaHelper
{
    private $db;
    function __construct ($dblink)
    {
    $db = $dblink;
    }
    function getConfigForStore($storeid)
    {
        $k = new Klarna();
        $getstore = mysqli_query($this->db,"SELECT * FROM `store` where `id` = $storeid");
        $store= mysqli_fetch_assoc($getstore);
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
}