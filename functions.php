<?php
/**
 * Created by PhpStorm.
 * User: matti
 * Date: 2016-09-24
 * Time: 16:16
 */
function verifyLoggedin()
{
    global $rootFolder;
    if(!isset($_SESSION["user"]))
    {
        header("Location:/".$rootFolder."/admin/login.php");
    }
}
function verifyAdminPrivileges()
{
    global $dblink;
    verifyLoggedin();
    $loggedin = getUserID();
    $users = mysqli_query($dblink,"SELECT * FROM `user` WHERE `id` = $loggedin");
    $currentUser = mysqli_fetch_assoc($users);
    if($currentUser["level"] != 0)
    {
        die("You dont have access to view this page");
    }
}
function getUserID()
{
    if(isset($_SESSION["user"]))
    {
        return $_SESSION["user"];
    }
    return 0;
}