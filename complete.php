<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-23
 * Time: 15:59
 */
unlink(dirname(__FILE__)."/install/index.php");
unlink(dirname(__FILE__)."/install/data.sql");
rmdir(dirname(__FILE__)."/install");
