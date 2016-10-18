<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-10-17
 * Time: 15:16
 */
include 'db.php';
$search=$_POST["search"];

$findorder = mysqli_query($dblink,"SELECT * FROM `order` where  reservation = '$search' OR invoice = '$search'");
if(mysqli_num_rows($findorder) == 1)
{
    $order = mysqli_fetch_assoc($findorder);
    $orderid = $order["id"];
    header("Location:orderview.php?id=".$orderid);
}
else{
    $findorder = mysqli_query($dblink,"SELECT pno,email FROM `order` where  pno = '$search' OR email = '$search'");
    if(mysqli_num_rows($findorder) > 0)
    {
        $cid = mysqli_fetch_assoc($findorder);
        header("Location:customerview.php?cid=".$cid["pno"]);
    }
}