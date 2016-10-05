<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-30
 * Time: 13:47
 */
?>
<div class="menu">
    <div class="row">
        <span class="icon"><img src="https://cdn.klarna.com/1.0/shared/image/generic/logo/sv_se/basic/white.png?width=130"></span>
        <ul class="">
            <li><a href="../index.php" >Place order</a></li>
            <li <?php if(basename($_SERVER["SCRIPT_FILENAME"]) == "listusers.php") { echo "class='active'";} ?>><a href="listusers.php" >List Users</a></li>
            <li <?php if(basename($_SERVER["SCRIPT_FILENAME"]) == "addstore.php") { echo "class='active'";} ?>><a href="addstore.php">Add store</a></li>
            <li <?php if(basename($_SERVER["SCRIPT_FILENAME"]) == "adduser.php") { echo "class='active'";} ?>><a href="adduser.php">Add user</a></li>
        </ul>
    </div>
</div>

