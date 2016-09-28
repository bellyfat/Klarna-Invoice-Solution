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
$app->get('/{store}/adress/{pno}', function (Request $request, Response $response) {
    global $dblink;
    $klarnaHelper = new KlarnaHelper($dblink);
    $storeid = $request->getAttribute('store');
    $k = $klarnaHelper->getConfigForStore($storeid);

    $pno = $request->getAttribute('pno');

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
    $newResponse = $response->withJson($res);
    return $newResponse;
});
$app->post('/{store}/buy', function (Request $request, Response $response) {
    global $dblink;
    $storeid = $request->getAttribute('store');
    $klarnaHelper = new KlarnaHelper($dblink);
    $k = $klarnaHelper->getConfigForStore($storeid);

    $data = $request->getParsedBody();
    $ticket_data = [];
    $pclass = $data["pclass"];
    $address = new Address($data['customer']['email'],
        "",
        $data['customer']['cellno'],
        $data['customer']['fname'],
        $data['customer']['lname'],
        "",
        $data['customer']['street'],
        $data['customer']['zip'],
        $data['customer']['city'],209);
    $orderLines = $data['orderlines'];
    $totalAmount =0;

    for($i = 0; $i < count($orderLines["artno"]); $i++)
    {

        if($orderLines["qty"][$i] !== "" &&  $orderLines["artno"][$i] !== "" &&  $orderLines["price"][$i] !== "")

        $k->addArticle(
            $orderLines["qty"][$i],              // Quantity
            $orderLines["artno"][$i],     // Article number
            $orderLines["title"][$i], // Article name/title
            $orderLines["price"][$i],          // Price
            $orderLines["vat"][$i],             // 25% VAT
            0,              // Discount
            Flags::INC_VAT
        );
        $totalAmount += $orderLines["price"][$i];
    }
    $k->setClientIP("192.0.2.9");
    $k->setAddress(Flags::IS_BILLING, $address);
    $k->setAddress(Flags::IS_SHIPPING, $address);
    $result =$k->reserveAmount("8803071797",null,$totalAmount,Flags::RSRV_SEND_BY_EMAIL);

   /* $result = $k->reserveAmount(
        '4103219202', // PNO (Date of birth for AT/DE/NL)
        null, // KlarnaFlags::MALE, KlarnaFlags::FEMALE (AT/DE/NL only)
        $pclass,   // Automatically calculate and reserve the cart total amount
        Flags::NO_FLAG,
        PClass::INVOICE
    );*/
    $inv = $result[1];
    $status = $result[0];
    if(strlen($result[0]) > $inv)
    {
        $inv = $result[0];
        $status = $result[1];
    }
    $res = array("status" =>$status, "invno" => $inv,"amount"=> $totalAmount);
    $newResponse = $response->withJson($res);
    return $newResponse;
});
$app->get('/methods', function (Request $request, Response $response) {
    $ch = curl_init();
    $digest = base64_encode(pack("H*",(hash("sha256",("6653:SEK:testbutik")))));

    // set url
    curl_setopt($ch, CURLOPT_URL, "https://api-test.klarna.com/touchpoint/checkout/?merchant_id=6653&currency=SEK&locale=sv_se&total_price=500000");


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
            $sql = "SELECT * FROM `order` WHERE `invoice` IS NULL AND (`status` = 'Reserved' OR `status` = 'Pending') AND `datetime` >= '$fromdate' AND `datetime` <='$todate' AND storeid IN (SELECT storeid from user_stores WHERE userid=$userID)";
            break;
        case "complete":
            $sql = "SELECT * FROM `order` WHERE `invoice` IS NOT NULL AND `status` = 'Complete' AND `datetime` >= '$fromdate' AND `datetime` <='$todate' AND storeid IN (SELECT storeid from user_stores WHERE userid=$userID)";
            break;
        case "cancelled":
            $sql = "SELECT * FROM `order` WHERE `status` = 'Cancelled' AND `datetime` >= '$fromdate' AND `datetime` <='$todate' AND storeid IN (SELECT storeid from user_stores WHERE userid=$userID)";
            break;
        default:
            $sql = "SELECT * FROM `order` WHERE `datetime` >= '$fromdate' AND `datetime` <='$todate' AND storeid IN (SELECT storeid from user_stores WHERE userid=$userID)";
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
$app->run();