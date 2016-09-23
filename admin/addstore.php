<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-21
 * Time: 09:42
 */
include("db.php");
verifyAdminPrivileges();
if(isset($_POST["savestore"]))
{
    global $dblink;
    $name = $_POST["name"];
    $eid = $_POST["eid"];
    $shared = $_POST["shared"];
    $norisk = 0;
    $testmode = 0;
    if(isset($_POST["norisk"]))
    {
        $norisk = $_POST["norisk"];
    }
    if(isset($_POST["testmode"]))
    {
        $testmode = $_POST["testmode"];
    }
    var_dump($_POST);
    mysqli_query($dblink,"INSERT INTO `store` (`name`,`eid`,`shared`,`norisk`,`testmode`) VALUES ('$name','$eid','$shared',$norisk,$testmode)");
   echo  mysqli_error($dblink);
    //header("Location:listusers.php");
}
?>
<form method="POST">
    <label>Name<input type="text" name="name" placeholder="Store Name"></label><br>
    <label>Store ID<input type="text" name="eid" placeholder="store ID"></label><br>
    <label>Shared Secret<input type="text" name="shared" placeholder="shared secret"></label><br>
    <label>Is No Risk<input type="checkbox" name="norisk" placeholder="shared">Yes</label><br>
    <label>Is Test Mode<input type="checkbox" name="testmode" placeholder="shared">Yes</label><br>
    <label><input type="submit" name="savestore" value="Skapa ny butik"/>
</form>