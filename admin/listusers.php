<?php

include("../db.php");
global $dblink;
$allusers = mysqli_query($dblink,"SELECT * FROM user");
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
    <link rel="stylesheet" href="../styles/foundation.min.css">
    <link rel="stylesheet" href="../styles/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js"></script>
</head>
<body>
<?php include 'menu.php'; ?>
<div class="container">
    <div class="row">
        <?php
        while($user = mysqli_fetch_assoc($allusers))
        {
            echo $user["email"];
            echo "<br>";
        }
        ?>
    </div>
</div>
</body>
</html>
