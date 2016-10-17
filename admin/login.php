<?php

include "../db.php";

include '../KlarnaLogger.php';
include '../functions.php';
global $dblink;
global $rootFolder;
$logger = new KlarnaLogger($dblink);
if(isset($_POST["login"]))
{
    $enteredPass = $_POST["pass"];
    $email = $_POST["email"];
    $res = mysqli_query($dblink,"SELECT * FROM user where email = '$email'");
    if(mysqli_num_rows($res) == 1)
    {
        $user = mysqli_fetch_assoc($res);
        if(password_verify($_POST["pass"]."".sha1($_POST["pass"]),$user["passwordhash"]));
        {
            $_SESSION["loggedInUser"] = true;
            $_SESSION["user"] = $user["id"];
            $logger->logInformation("User successfully logged in");
            header("Location:/".$rootFolder);
        }
    }
    $logger->logError("User failed to login with email ".$email);
    echo "Could not login";
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
        <form method="post">
            <input type="text" placeholder="email" name="email">
            <input type="password" placeholder="password" name="pass">
            <input type="submit" name="login" value="log in"/>
        </form>
    </div>
</div>
