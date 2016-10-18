<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-10-17
 * Time: 15:59
 */
include 'db.php';
include 'functions.php';
$cID = $_GET["cid"];
$allorders = mysqli_query($dblink,"SELECT * FROM `order` WHERE pno = '$cID'");
$alladresses = mysqli_query($dblink,"SELECT * FROM `address` WHERE id in (SELECT `billing` from `order` WHERE `pno` =  '$cID')");
$customervalue = mysqli_query($dblink,"SELECT SUM(incvat) as customervalue FROM orderitem where orderitem.orderid IN (SELECT id FROM `order` WHERE pno = '$cID') "); // TODO: remove refunded orders
$value = mysqli_fetch_assoc($customervalue);
$value =  $value["customervalue"];
?>

<!doctype html>
<html class="" lang="">
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klarna Invoice</title>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700'
          rel='stylesheet'
          type='text/css'>
    <link rel="icon"
          href="favicon.ico">
    <link rel="stylesheet" href="styles/foundation.min.css">
    <link rel="stylesheet" href="styles/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js"></script>
</head>
<body>
<!--[if lt IE 10]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<?php include('menu.php'); ?>
<div class="container">
    <div class="row">
        <div class="small-4 columns">
            <h2>Orders</h2>
            <?php
            while($ord = mysqli_fetch_assoc($allorders))
            {
                echo '<a href="orderview.php?id='.$ord["id"].'">'.$ord["id"].' - '.$ord["status"].' - '.$ord["datetime"].'</a>';
            }
            ?>
        </div>
        <div class="small-4 columns">
            <h2>Adresses</h2>
            <?php
            while($ord = mysqli_fetch_assoc($alladresses))
            {
                echo $ord["firstname"].' '.$ord["lastname"].' '.$ord["street"].' '.$ord["postal"].' '.$ord["city"];
            }
            ?>
        </div>
        <div class="small-4 columns">
            <h2>Customer Value</h2>
            <?php echo $value; ?>
            </div>
    </div>
</div>
</body></html>
