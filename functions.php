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
function getInvoiceUrl($order)
{
    if($order["testmode"] == 1)
    {
        return "https://online.testdrive.klarna.com/invoice_pdf.yaws/invoice_".$order["invoice"];
    }
    else{
        return "https://online.klarna.com/invoice_pdf.yaws/invoice_".$order["invoice"];
    }
}