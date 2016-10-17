<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-21
 * Time: 13:25
 */
include '../db.php';
include '../functions.php';
verifyAdminPrivileges();
$user = $_GET["user"];
if(isset($_POST["store"]))
{
    $stores = $_POST["store"];
    mysqli_query($dblink,"DELETE FROM user_stores WHERE userid = $user");
    foreach($stores as $sto)
    {
        mysqli_query($dblink,"INSERT INTO `user_stores` (userid,storeid) VALUES ($user,$sto)");
    }
}
$accessget = mysqli_query($dblink,"SELECT *, (select user_stores.userid from user_stores where user_stores.storeid = store.id) as access FROM `store`");
?>
<!doctype html>
<html class="" lang="">
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klarna Invoice</title>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700'
          rel='stylesheet'
          type='text/css'>
    <link rel="stylesheet" href="../styles/foundation.min.css">
    <link rel="stylesheet" href="../styles/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js"></script>
</head>
<body>
<!--[if lt IE 10]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<?php include('menu.php'); ?>
<div class="container">
<div class="row">
    <form action="" method="post">
        <?php
        while($store = mysqli_fetch_assoc($accessget))
        {?>
            <div class="small-8 columns">
                <?php echo $store["name"]; ?>
            </div>
             <div class="small-4 columns">
                 <label> Enable access  <input type="checkbox" name="store[]" <?php echo $store["access"] != null ? "checked":""?> value="<?php echo $store["id"]; ?>"/> Yes </label>
             </div>
      <?php  }
        ?>
</div>
    <div class="row">
    <div class="small-12 columns">
        <button class="float-right button">save</button>
    </div>
        </form>
        </div>
    </div>
</body>
</html>
