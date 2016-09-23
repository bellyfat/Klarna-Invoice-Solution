<?php

include("db.php");
global $dblink;
$allusers = mysqli_query($dblink,"SELECT * FROM user");
while($user = mysqli_fetch_assoc($allusers))
{
echo $user["email"];
    echo "<br>";
}