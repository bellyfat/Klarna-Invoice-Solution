<?php

include "../db.php";

global $dblink;
global $rootFolder;
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
            header("Location:/".$rootFolder);
        }
    }
    echo "Could not login";
}
?>
<form method="post">
<input type="text" placeholder="email" name="email">
<input type="password" placeholder="password" name="pass">
<input type="submit" name="login" value="log in"/>
</form>