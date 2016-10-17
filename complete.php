<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-23
 * Time: 15:59
 */
unlink(dirname(__FILE__)."/install/index.php");
unlink(dirname(__FILE__)."/install/data.sql");
rmdir(dirname(__FILE__)."/install");

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
    <?php include('menu.php'); ?>
    <div class="container">
        <div class="small-12">
            <h1>Installation Complete!</h1>
            <p>Proceed to <a href="admin/login.php">login</a> in order to setup your first store</p>
        </div>

    </div>
</body>
</html>

