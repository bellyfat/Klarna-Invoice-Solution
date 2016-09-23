<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-23
 * Time: 14:31
 */


if(isset($_POST["install"])) {
    $prefix = $_POST["prefix"];
    $server = $_POST["server"];
    $user = $_POST["user"];
    $pass = $_POST["pass"];
    $adminuser = $_POST["adminuser"];
    $adminpass = $_POST["adminpass"];
    $dblink = mysqli_connect($server, $user, $pass);
    if (!$dblink) {
        die("Incorrect Params - Could not connect");
    }
    file_put_contents("../db.php", "<?php \$dblink = mysqli_connect(\"" . $server . "\", \"" . $user . "\", \"" . $pass . "\", \"" . $prefix . "kpm\"); ?>");
   $res = mysqli_query($dblink,"CREATE DATABASE IF NOT EXISTS `".$prefix."kpm` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;");
    echo mysqli_error($dblink);
    $dblink = mysqli_connect($server, $user, $pass,$prefix."kpm");
    $sql = "CREATE TABLE IF NOT EXISTS `store` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `eid` int(11) NOT NULL,
          `shared` varchar(255) NOT NULL,
          `testmode` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;";
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql = "CREATE TABLE IF NOT EXISTS `user` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `email` varchar(255) NOT NULL,
          `passwordhash` varchar(255) NOT NULL,
          `level` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;";
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql="CREATE TABLE IF NOT EXISTS `user_stores` (
          `userid` int(11) NOT NULL,
          `storeid` int(11) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $createHash = password_hash($adminpass."".sha1($adminpass),PASSWORD_BCRYPT);
    $sql = "INSERT INTO `user` (`email`, `passwordhash`, `level`) VALUES
('$adminuser', '$createHash', 0);";
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
}
?>

<form method="post">
    <input type="text" placeholder="server" name="server">
    <input type="text" placeholder="prefix" name="prefix">
    <input type="text" placeholder="user" name="user">
    <input type="text" placeholder="pass" name="pass">
    <input type="text" placeholder="adminuser" name="adminuser">
    <input type="text" placeholder="adminpass" name="adminpass">
    <input type="submit" name="install" value="Install"/>
</form>
