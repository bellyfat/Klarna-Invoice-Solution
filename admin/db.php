<?php
session_start();
$passHash = "ooapnidf2312aosokn";
$dblink = mysqli_connect("localhost", "root", "", "kpm");
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
?>