<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-21
 * Time: 09:42
 */
include("db.php");
verifyAdminPrivileges();
if(isset($_POST["saveuser"]))
{
    global $dblink;
    $email = $_POST["email"];
    $pass = $_POST["wantedpassword"];
    $createHash = password_hash($pass."".$passHash,PASSWORD_BCRYPT);
    mysqli_query($dblink,"INSERT INTO user (email,passwordhash,level) VALUES ('$email','$createHash',1)");
    //header("Location:listusers.php");
}
?>
<form method="POST">
<input type="text" name="email" placeholder="email">
<input type="text" name="wantedpassword" placeholder="password">
<input type="submit" name="saveuser" value="Skapa ny admin"/>
</form>