<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-21
 * Time: 09:42
 */
include("../db.php");
include("../functions.php");
verifyAdminPrivileges();
if(isset($_POST["saveuser"]))
{
    global $dblink;
    $email = $_POST["email"];
    $pass = $_POST["wantedpassword"];
    $createHash = password_hash($pass."".sha1($pass),PASSWORD_BCRYPT);
    mysqli_query($dblink,"INSERT INTO user (email,passwordhash,level) VALUES ('$email','$createHash',1)");
    //header("Location:listusers.php");
}
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
        <form method="POST">
            <input type="text" name="email" placeholder="email">
            <input type="text" name="wantedpassword" placeholder="password">
            <input type="submit" name="saveuser" value="Skapa ny admin"/>
        </form>
    </div>
</div>
