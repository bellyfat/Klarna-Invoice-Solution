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
if(isset($_POST["savestore"]))
{
    global $dblink;
    $name = $_POST["name"];
    $eid = $_POST["eid"];
    $shared = $_POST["shared"];
    $country = $_POST["country"];
    $testmode = 0;
    echo $_POST["testmode"];
    if(isset($_POST["testmode"]))
    {
        $testmode = $_POST["testmode"];
    }
    $name = $name." ".$country." ".($testmode == 0 ? "LIVE" : "TEST");
    mysqli_query($dblink,"INSERT INTO `store` (`name`,`eid`,`shared`,`testmode`,`country`) VALUES ('$name','$eid','$shared',testmode,'$country')");
   echo  mysqli_error($dblink);
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
<?php include 'menu.php';?>
<div class="container">
    <div class="row">
<form method="POST">
    <label>Name<input type="text" name="name" placeholder="Store Name"></label><br>
    <label>Store ID<input type="text" name="eid" placeholder="store ID"></label><br>
    <label>Shared Secret<input type="text" name="shared" placeholder="shared secret"></label><br>
    <label>Is Test Mode<input type="checkbox" value="1" name="testmode" placeholder="shared">Yes</label><br>
    <label>Country<select name="country">
            <option value="SE"><?php echo gettext("Sweden")?></option>
            <option value="DK"><?php echo gettext("Denmark")?></option>
            <option value="NO"><?php echo gettext("Norway")?></option>
            <option value="DE"><?php echo gettext("Germany")?></option>
            <option value="NL"><?php echo gettext("Holland")?></option>
    </label><br>
    <label><input type="submit" class="float-right button" name="savestore" value="<?php echo gettext("Create new store")?>"/>
</form>
        </div>
    </div>
</body>
</html>