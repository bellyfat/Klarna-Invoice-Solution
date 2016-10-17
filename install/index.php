<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-23
 * Time: 14:31
 */
include '../KlarnaLogger.php';
include '../functions.php';
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
    $logger = new KlarnaLogger($dblink);
    $logger->logInformation("successfully installed DB at ".$server);
    $fileres = file_put_contents("../db.php", "<?php session_start(); \$rootFolder = '".$install_folder."'; \$dblink = mysqli_connect(\"" . $server . "\", \"" . $user . "\", \"" . $pass . "\", \"" . $prefix . "_kpm\"); ?>");
    if($fileres === false)
    {
        die("something went wrong with file-creation");
        $logger->logError("could not save db file");
    }
   $res = mysqli_query($dblink,"CREATE DATABASE IF NOT EXISTS `".$prefix."_kpm` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;");
    echo mysqli_error($dblink);
    mysqli_close($dblink);
    $dblink = mysqli_connect($server, $user, $pass,$prefix."_kpm");
    $sql = file_get_contents('data.sql');
  //  mysqli_multi_query($dblink, $sql);
    if ( mysqli_multi_query($dblink, $sql)) {
        do {
            if ($result = mysqli_store_result($dblink)) {
                mysqli_free_result($result);
            }

        } while (mysqli_more_results($dblink) && mysqli_next_result($dblink));
    }

    $createHash = password_hash($adminpass."".sha1($adminpass),PASSWORD_BCRYPT);
    $sql = "INSERT INTO `user` (`email`, `passwordhash`, `level`) VALUES
('$adminuser', '$createHash', 0);";
    mysqli_query($dblink,$sql);
    echo mysqli_error($dblink);
   // header("Location:/".$install_folder."/complete.php");
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