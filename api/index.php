<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-12
 * Time: 13:48
 */

require 'vendor/autoload.php';
include ("../db.php");
include ('KlarnaHelper.php');
include ('../KlarnaLogger.php');
include ('../functions.php');
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Klarna\XMLRPC\Klarna;
use Klarna\XMLRPC\Address;
use Klarna\XMLRPC\Country;
use Klarna\XMLRPC\Language;
use Klarna\XMLRPC\Flags;
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);
global $dblink;
$logger = new KlarnaLogger($dblink);
$app->get('/{store}/adress/{pno}', function (Request $request, Response $response) {
    global $dblink;
    global $logger;
    $klarnaHelper = new KlarnaHelper($dblink);
    $storeid = $request->getAttribute('store');
    $k = $klarnaHelper->getConfigForStore($storeid);


    $pno = $request->getAttribute('pno');
    $logger->logInformation("Looking up adress for ".$pno);
    $addrs = $k->getAddresses($pno);
    foreach($addrs as $ad)
    {

        $res[] = array("firstname"=>utf8_encode($ad->getFirstName()),
            "lastname"=>utf8_encode($ad->getLastName()),
            "street"=>utf8_encode($ad->getStreet()),
            "city"=>utf8_encode($ad->getCity()),
            "postal"=>utf8_encode($ad->getZipCode()),
            "company" =>utf8_encode($ad->getCompanyName()));
    }
    $logger->logInformation("Found adresses at ".json_encode($res));
    $newResponse = $response->withJson($res);
    return $newResponse;
});
$app->post('/{store}/buy', function (Request $request, Response $response) {
    global $dblink;
    global $logger;
    $logger->logInformation("starting purchase");
    $storeid = $request->getAttribute('store');
    $klarnaHelper = new KlarnaHelper($dblink);
    $k = $klarnaHelper->getConfigForStore($storeid);
    $data = $request->getParsedBody();
    $logger->logInformation("starting purchase for storeID ".$storeid." with data ".json_encode($data));
    $pno = $data["pno"];

    $email = $data["customer"]["email"];
    $pclass = $data["pclass"];
    $address = new Address($data['customer']['email'],
        "",
        $data['customer']['cellno'],
        $data['customer']['fname'],
        $data['customer']['lname'],
        "",
        $data['customer']['street'],
        $data['customer']['zip'],
        $data['customer']['city'],$k->getCountry());
    $orderLines = $data['orderlines'];
    $totalAmount =0;
    $firstname = $address->getFirstName();
    $lastname =$address->getLastName();
    $street = $address->getStreet();
    $postal = $address->getZipCode();
    $city = $address->getCity();
    $country = $address->getCountry();
    $company = $address->getCompanyName();
    //Adding adress to DB
    mysqli_query($dblink,"INSERT into `address` (firstname,lastname,street,postal,city,country,company) VALUES ('$firstname','$lastname','$street','$postal','$city','$country','$company')");
    $shippingAddressId = mysqli_insert_id($dblink);
    mysqli_query($dblink,"INSERT into `address` (firstname,lastname,street,postal,city,country,company) VALUES ('$firstname','$lastname','$street','$postal','$city','$country','$company')");
    $billingAddressId = mysqli_insert_id($dblink);

    $items = array();
    for($i = 0; $i < count($orderLines["artno"]); $i++)
    {

        if($orderLines["qty"][$i] !== "" &&  $orderLines["artno"][$i] !== "" &&  $orderLines["price"][$i] !== "")
        {
            $k->addArticle(
                $orderLines["qty"][$i],              // Quantity
                $orderLines["artno"][$i],     // Article number
                $orderLines["title"][$i], // Article name/title
                $orderLines["price"][$i],          // Price
                $orderLines["vat"][$i],             // 25% VAT
                0,              // Discount
                Flags::INC_VAT
            );
            $items[] = array(
                "sku" => $orderLines["artno"][$i],
                "title" => $orderLines["title"][$i],
                "qty" =>$orderLines["qty"][$i],
                "incvat" => $orderLines["price"][$i],
                "vat"=> $orderLines["vat"][$i]
            );
            $totalAmount += $orderLines["price"][$i];
        }
    }
    $k->setClientIP($_SERVER['REMOTE_ADDR']);
    $k->setAddress(Flags::IS_BILLING, $address);
    $k->setAddress(Flags::IS_SHIPPING, $address);

    try{
        $result =$k->reserveAmount($pno,null,$totalAmount,Flags::RSRV_SEND_BY_EMAIL, $pclass);
    }
    catch(\Klarna\XMLRPC\Exception\KlarnaException $e)
    {
        $logger->logError("Could not place order recieved error ".$e->getMessage());
        mysqli_query($dblink,"DELETE FROM `address` WHERE `id` = $billingAddressId OR `id` = $shippingAddressId");
        $error = mb_convert_encoding($e->__toString(),"utf-8","ISO-8859-1");
        $res = array("status" =>"failed", "message" => $error);
        $newResponse = $response->withJson($res);
        return $newResponse->withStatus(400);
    }

    $logger->logInformation("completed purchase for pno ".$pno);

    $inv = $result[1];
    $status = $result[0];
    if(strlen($result[0]) > $inv)
    {
        $inv = $result[0];
        $status = $result[1];
    }
    //Creating Order
    $pno = str_replace('-','',$pno);
    $pno = str_replace(' ','',$pno);
    mysqli_query($dblink,"INSERT INTO `order` (reservation,billing,shipping,storeid,pno,email) VALUES ('$inv',$billingAddressId,$shippingAddressId,$storeid,'$pno','$email')");
    echo mysqli_error($dblink);
    $orderID = mysqli_insert_id($dblink);
    foreach($items as $item)
    {
        $name = $item["title"];
        $sku = $item["sku"];
        $qt = $item["qty"];
        $incvat = $item["incvat"];
        $vat = $item["vat"];
        $vatcalc = ($vat / 100) + 1;
        $vatcalc = (($vatcalc - $incvat) * -1);

        mysqli_query($dblink,"INSERT into `orderitem` (orderid,name,artno,incvat,exvat,vat,quantity) VALUES ($orderID,'$name','$sku',$incvat,$vatcalc,$vat,$qt)");
        echo mysqli_error($dblink);
    }
    $res = array("status" =>$status, "invno" => $inv,"amount"=> $totalAmount);
    $newResponse = $response->withJson($res);
    return $newResponse;
});
$app->get('/{storeid}/methods', function (Request $request, Response $response) {
    $storeid = $request->getAttribute('storeid');
    global $dblink;
    $storeinfo = mysqli_query($dblink,"SELECT * FROM `store` WHERE `id` = $storeid");
     $store = mysqli_fetch_assoc($storeinfo);
    switch($store["country"])
    {
        case "SE":
            $language = "sv_se";
            $currency = "SEK";
            break;
        case "NO":
            $language = "nb_no";
            $currency = "NOK";
            break;
        case "DE":
            $language = "de_de";
            $currency = "EUR";
            break;
        case "FI":
            $language = "fi_fi";
            $currency = "EUR";
            break;
        case "DK":
            $language = "da_dk";
            $currency = "DKK";
            break;
    }
    $shared = $store["shared"];
    $env = $store["testmode"] == 0 ? "": "-test";
    $eid = $store["eid"];
    $ch = curl_init();
    $digest = base64_encode(pack("H*",(hash("sha256",($eid.":".$currency.":".$shared)))));

    // set url
    curl_setopt($ch, CURLOPT_URL, "https://api".$env.".klarna.com/touchpoint/checkout/?merchant_id=".$eid."&currency=".$currency."&locale=".$language."&total_price=500000");


    //return the transfer as a string
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/vnd.klarna.touchpoint-checkout.payment-methods-v1+json",
        "Authorization: xmlrpc-4.2 ".$digest
    ));

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);
    $newResponse = $response->withHeader('Content-type', 'application/json');
    $newResponse = $newResponse->write($output);
    return $newResponse;
});
$app->get('/orders/{status}[/from/{fromdate}/to/{todate}]', function (Request $request, Response $response) {
    $userID= $_SESSION["user"];
    $fromdate = $request->getAttribute('fromdate')." 00:00";
    $todate = $request->getAttribute('todate')." 23:59";
    $status = $request->getAttribute('status');
    switch($status)
    {
        case "processing";
            $sql = "SELECT *,(SELECT SUM(orderitem.incvat * orderitem.quantity) FROM orderitem WHERE orderitem.orderid = order.id) as sum FROM `order` WHERE `invoice` IS NULL AND (`status` = 'Reserved' OR `status` = 'Pending') AND `datetime` >= '$fromdate' AND `datetime` <='$todate' AND storeid IN (SELECT storeid from user_stores WHERE userid=$userID)";
            break;
        case "complete":
            $sql = "SELECT *,(SELECT SUM(orderitem.incvat * orderitem.quantity) FROM orderitem WHERE orderitem.orderid = order.id) as sum FROM `order` WHERE `invoice` IS NOT NULL AND `status` = 'Complete' AND `datetime` >= '$fromdate' AND `datetime` <='$todate' AND storeid IN (SELECT storeid from user_stores WHERE userid=$userID)";
            break;
        case "cancelled":
            $sql = "SELECT *,(SELECT SUM(orderitem.incvat * orderitem.quantity) FROM orderitem WHERE orderitem.orderid = order.id) as sum FROM `order` WHERE `status` = 'Cancelled' AND `datetime` >= '$fromdate' AND `datetime` <='$todate' AND storeid IN (SELECT storeid from user_stores WHERE userid=$userID)";
            break;
        default:
            $sql = "SELECT *,(SELECT SUM(orderitem.incvat * orderitem.quantity) FROM orderitem WHERE orderitem.orderid = order.id) as sum FROM `order` WHERE `datetime` >= '$fromdate' AND `datetime` <='$todate' AND storeid IN (SELECT storeid from user_stores WHERE userid=$userID)";
    }
    global $dblink;
    $orders = mysqli_query($dblink,$sql);
    $res = array();
    while($ord = mysqli_fetch_assoc($orders))
    {
        $res[] = $ord;
    }
    $newResponse = $response->withHeader('Content-type', 'application/json');
    $newResponse = $newResponse->write(json_encode($res));
    return $newResponse;
});
$app->post('/{store}/credit', function (Request $request, Response $response) {
    global $dblink;
    $storeid = $request->getAttribute('store');
    $data = $request->getParsedBody();
    $orderid = $data["id"];
    $storeid = $request->getAttribute('store');
    $klarnaHelper = new KlarnaHelper($dblink);
    $k = $klarnaHelper->getConfigForStore($storeid);
    $ref = $request->getHeader("Referer");
    try
    {
        $k->creditInvoice($orderid);
    }
    catch(Exception $e)
    {
        $newResponse = $response->withStatus(404);
        return $newResponse;
    }
    mysqli_query($dblink,"UPDATE `order` SET `status` = 'Cancelled' WHERE `invoice` = '$orderid' ");
    $newResponse = $response->withStatus(302)->withHeader("Location",$ref);
    return $newResponse;
});
$app->post('/{store}/activate', function (Request $request, Response $response) {
    global $dblink;
    $storeid = $request->getAttribute('store');
    $data = $request->getParsedBody();
    $orderid = $data["id"];
    $storeid = $request->getAttribute('store');
    $klarnaHelper = new KlarnaHelper($dblink);
    $k = $klarnaHelper->getConfigForStore($storeid);
    $ref = $request->getHeader("Referer");
    try
    {
        $res = $k->activate($orderid);
    }
    catch(Exception $e)
    {
        $newResponse = $response->withStatus(404);
        return $newResponse;
    }
    mysqli_query($dblink,"UPDATE `order` SET `status` = 'Shipped', `invoice` = '$res[1]' WHERE `reservation` = '$orderid' ");
    $newResponse = $response->withStatus(302)->withHeader("Location",$ref);
    return $newResponse;
});
$app->run();