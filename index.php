<?php
include('db.php');
include('functions.php');
global $dblink;
verifyLoggedin();
$userID= $_SESSION["user"];
error_reporting(E_ALL);
ini_set('display_errors',1);
$stores = mysqli_query($dblink,"SELECT * FROM store WHERE id IN (SELECT storeid from user_stores WHERE userid=$userID)");
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
    <form method="post" id="buyForm" action="api/buy">
        <div class="row">
            <div class="large-6 columns small-6">
                <label class="">
                    Please write pno / orgnummer

                    <input  class="" id="pno"  type="text"></label>
            </div>
            <div class="large-6 columns small-6">
                <a class="button secondary large" id="getadress" href="#" class="">
                    Vidare
                </a>
            </div>
        </div>
        <div class="row">
            <div class="large-12 columns">
                <label class="">
                    Select customer adress


                    <select id="adresses" class="">
                    </select></label>
            </div>
        </div>
        <div class="row">
            <div class="large-12 columns">

                <div class="store-select small-12">
                    <label>Select Store<select id="purchasestore" name="purchasestore">
                            <?php
                            while($st = mysqli_fetch_assoc($stores))
                            {
                                echo '<option value="'.$st["id"].'">'.$st["name"].'</option>';
                            }
                            ?>
                            ?>
                        </select></label>
                </div></div>
        </div>
        <div class="row">
            <div class="large-3 columns small-12">
                <label class="cui__input__label">
                    Email

                    <input  name="customer[email]" class="cui__input__input" id="email"  type="text"></label>
            </div>
            <div class="large-3 columns small-12">
                <label class="cui__input__label">
                    Phone

                    <input  name="customer[cellno]" class="cui__input__input" id="phone"  type="text"></label>
            </div>

            <div class="large-3 columns small-12">
                <label class="cui__input__label">
                    firstname

                    <input class="cui__input__input" id="custname" type="text" value="test" name="customer[fname]"></label>
            </div>
            <div class="large-3 columns small-12">
                <label class="cui__input__label">
                    lastname

                    <input type="text" class="cui__input__input" id="custsurname" value="testlast" name="customer[lname]"/></label>

            </div>
            <div class="large-5 columns small-12">
                <label class="cui__input__label">
                    street

                    <input type="text" class="cui__input__input" id="custstreet" value="teststreet" name="customer[street]"/></label>
            </div>
            <div class="large-4 columns small-12">
                <label class="cui__input__label">
                    city

                    <input type="text" class="cui__input__input" id="custcity" value="testcity" name="customer[city]"/></label>
            </div>
            <div class="large-3 columns small-12">
                <label class="cui__input__label">
                    postal

                    <input type="text" class="cui__input__input" id="custpostal" value="testpostal" name="customer[zip]"/></label>
            </div>
        </div>
        <div id="orderLines" class="row">
            <h1 class="cui__title--primary">
                Order Lines
            </h1>
            <div id="orderLine">
                <div class="large-2 columns small-4">
                    <label class="cui__input__label">
                        SKU

                        <input  name="orderlines[artno][]" class="cui__input__input"  type="text"></label>
                </div>
                <div class="large-4 columns small-8">
                    <label class="cui__input__label">
                        Name

                        <input  name="orderlines[title][]" class="cui__input__input"  type="text"></label>
                </div>
                <div class="large-2 columns small-4">
                    <label class="cui__input__label">
                        Quantity

                        <input  name="orderlines[qty][]" class="cui__input__input"  type="number"></label>
                </div>
                <div class="large-2 columns small-4">
                    <label class="cui__input__label">
                        Price

                        <input  name="orderlines[price][]" class="cui__input__input"  type="number"></label>
                </div>
                <div class="large-2 columns small-4">
                    <label class="cui__input__label">
                        VAT %

                        <input  name="orderlines[vat][]" class="cui__input__input"  type="number" max="100"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <a href="#" id="addNewProd" class="small">
                Add more products
            </a>
            <hr>
            <div class="small-12">
                <label class="cui__dropdown--native__label">
                    Invoice Method
                    <select name="pclass" id="paymentmethods" class="cui__dropdown--native__select">

                    </select></label>
            </div>

            <div class="small-12 float-right">
                <button class="button large  float-right" id="buy" class="cui__button--primary left is-disabled">
                    Invoice
                </button>
            </div>
        </div>
        <hr>
    </form>
</div>



<div class="dialog">
    <div class="dialog-inner">
        <span class="close button alert">X</span>
        <span class="label success">Order Successfull</span>
        <h4 class="cui__text-label">
            Klarna ID
        </h4>

        <p class="cui__paragraph--primary condensed">
            <span id="invoiceLabel"></span>
        </p>

        <h4 class="cui__text-label">
            Invoice amount
        </h4>

        <h2 class="cui__title--secondary">
            <span id="amountlabel"></span>
        </h2>

    </div>
</div>
<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<script>
    (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
        function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
        e=o.createElement(i);r=o.getElementsByTagName(i)[0];
        e.src='https://www.google-analytics.com/analytics.js';
        r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
    ga('create','UA-XXXXX-X');ga('send','pageview');
</script>

<!-- build:js scripts/vendor.js -->
<!-- bower:js -->
<!-- endbower -->
<!-- endbuild -->
<script   src="https://code.jquery.com/jquery-1.12.4.min.js"   integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="   crossorigin="anonymous"></script>
<!-- build:js scripts/main.js -->
<script src="scripts/main.js"></script>
<script src="scripts/order.js"></script>
<!-- endbuild -->
</body>
</html>
