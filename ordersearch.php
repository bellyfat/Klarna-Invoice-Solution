<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-28
 * Time: 10:27
 */
include('db.php');
include('functions.php');
global $dblink;
verifyLoggedin();
$userID= $_SESSION["user"];
?>
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
<?php include('menu.php'); ?>
<div class="container">
    <div class="row">
        <div class="small-6 columns">
            <label>Order - type
            <select id="order-type">
                <option value="processing"><?php echo gettext("Select an option") ?></option>
                <option value="processing"><?php echo gettext("Processing orders") ?></option>
                <option value="complete"><?php echo gettext("Completed orders") ?></option>
                <option value="cancelled"><?php echo gettext("Cancelled / returned orders") ?></option>
            </select></label>
        </div>

    <div class="small-3 columns">
        <label>From:
        <input id="fromdate" type="date" value="<?php echo date("Y-m-d", strtotime(date("Y-m-d"). ' - 30 days'));?>"></label>
        </div>
    <div class="small-3 columns">
        <label>To:
        <input id="todate" type="date" value="<?php echo date("Y-m-d");?>"></label>
        </div>
    </div>
    <div class="orderlist">
    </div>
</div>
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
<script src="scripts/search.js"></script>
<!-- endbuild -->
</body>
</html>
