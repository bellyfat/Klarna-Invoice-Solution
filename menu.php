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
            <li <?php if(basename($_SERVER["SCRIPT_FILENAME"]) == "index.php") { echo "class='active'";} ?>><a href="index.php" >Place order</a></li>
            <li <?php if(basename($_SERVER["SCRIPT_FILENAME"]) == "ordersearch.php") { echo "class='active'";} ?>><a href="ordersearch.php">Search Order</a></li>
            <li <?php if(basename($_SERVER["SCRIPT_FILENAME"]) == "admin.php") { echo "class='active'";} ?>><a href="admin/index.php">Admin</a></li>
        </ul>
    </div>
</div>

