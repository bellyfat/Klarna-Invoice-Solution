<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-23
 * Time: 14:31
 */
bindtextdomain("klarna", "./localization");
Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
$languages = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
$lang= $languages[0];

$lang = str_replace("-","_",$lang);
setlocale(LC_ALL,$lang);

if(isset($_POST["install"])) {
    $prefix = $_POST["prefix"];
    $server = $_POST["server"];
    $user = $_POST["user"];
    $pass = $_POST["pass"];
    $adminuser = $_POST["adminuser"];
    $adminpass = $_POST["adminpass"];
    $install_folder =$_POST["installfolder"];
    $dblink = mysqli_connect($server, $user, $pass);
    echo mysqli_error($dblink);
    if (!$dblink) {
        die("Incorrect Params - Could not connect");
    }
    $fileres = file_put_contents("../db.php", "<?php session_start(); \$rootFolder = '".$install_folder."'; \$dblink = mysqli_connect(\"" . $server . "\", \"" . $user . "\", \"" . $pass . "\", \"" . $prefix . "_kpm\"); ?>");
    if($fileres === false)
    {
        die("something went wrong with file-creation");
    }
   $res = mysqli_query($dblink,"CREATE DATABASE IF NOT EXISTS `".$prefix."_kpm` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;");
    echo mysqli_error($dblink);
    $dblink = mysqli_connect($server, $user, $pass,$prefix."_kpm");
    $sql = 'CREATE TABLE `store` (
          `id` int(11) NOT NULL,
          `name` varchar(255) NOT NULL,
          `eid` int(11) NOT NULL,
          `shared` varchar(255) NOT NULL,
          `testmode` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql = 'CREATE TABLE `user` (
          `id` int(11) NOT NULL,
          `email` varchar(255) NOT NULL,
          `passwordhash` varchar(255) NOT NULL,
          `level` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql='CREATE TABLE `user_stores` (
          `userid` int(11) NOT NULL,
          `storeid` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql = 'CREATE TABLE `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `postal` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql = 'CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reservation` varchar(255) NOT NULL,
  `invoice` varchar(255) DEFAULT NULL,
  `status` enum(\'Pending\',\'Shipped\',\'Cancelled\',\'Reserved\') NOT NULL DEFAULT \'Reserved\',
  `billing` int(11) NOT NULL,
  `shipping` int(11) NOT NULL,
  `storeid` int(11) NOT NULL,
  `testmode` int(11) NOT NULL DEFAULT \'1\',
  PRIMARY KEY (`id`),
  KEY `storeid` (`storeid`),
  KEY `billing` (`billing`),
  KEY `shipping` (`shipping`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);

    $sql = 'ALTER TABLE `store`
  ADD PRIMARY KEY (`id`);';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql='
   ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql='
  ALTER TABLE `user_stores`
  ADD KEY `userid` (`userid`),
  ADD KEY `storeid` (`storeid`);';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql='
ALTER TABLE `store`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql='
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql='
  ALTER TABLE `user_stores`
  ADD CONSTRAINT `store_exists` FOREIGN KEY (`storeid`) REFERENCES `store` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_exists` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $sql = 'ALTER TABLE `order`
  ADD CONSTRAINT `billing` FOREIGN KEY (`billing`) REFERENCES `address` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shipping` FOREIGN KEY (`shipping`) REFERENCES `address` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `store_order` FOREIGN KEY (`storeid`) REFERENCES `store` (`id`);';
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    $createHash = password_hash($adminpass."".sha1($adminpass),PASSWORD_BCRYPT);
    $sql = "INSERT INTO `user` (`email`, `passwordhash`, `level`) VALUES
('$adminuser', '$createHash', 0);";
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
    header("Location:".(dirname(__FILE__))."/complete.php");
}
?>
<link rel="stylesheet" href="../styles/foundation.min.css">
<link rel="stylesheet" href="../styles/main.css">
<div class="row">
    <form method="post">
        <div class="large-3 small-12 columns">
            <input type="text" placeholder="<?php echo gettext("Server")?>" name="server">
        </div>
        <div class="large-3 small-12 columns">
            <input type="text" placeholder="<?php echo gettext("DB Prefix")?>" name="prefix">
        </div>
        <div class="large-3 small-12 columns">
            <input type="text" placeholder="<?php echo gettext("User")?>" name="user">
        </div>
        <div class="large-3 small-12 columns">
            <input type="text" placeholder="<?php echo gettext("Password")?>" name="pass">
        </div>
        <div class="large-3 small-12 columns">
            <input type="text" placeholder="<?php echo gettext("Installed folder ( leave empty if root )")?>" name="installfolder">
        </div>
        <div class="large-3 small-12 columns">
            <input type="text" placeholder="<?php echo gettext("Admin username")?>" name="adminuser">
        </div>
        <div class="large-3 small-12 columns">
            <input type="text" placeholder="<?php echo gettext("Admin password")?>" name="adminpass">
        </div>
        <input type="submit" class="button" name="install" value="<?php echo gettext("Install")?>"/>
    </form>
</div>