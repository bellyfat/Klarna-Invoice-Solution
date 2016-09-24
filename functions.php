<?php
/**
 * Created by PhpStorm.
 * User: matti
 * Date: 2016-09-24
 * Time: 16:16
 */
function verifyLoggedin()
{
    if(!isset($_SESSION["user"]))
    {
        header("Location:admin/login.php");
    }
}
function verifyAdminPrivileges()
{
    global $dblink;
    verifyLoggedin();
    $loggedin = $_SESSION["user"];
    $users = mysqli_query($dblink,"SELECT * FROM `user` WHERE `id` = $loggedin");
    $currentUser = mysqli_fetch_assoc($users);
    if($currentUser["level"] != 0)
    {
        die("You dont have access to view this page");
    }
}