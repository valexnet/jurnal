<?php

if (isset($_SESSION['user_id']))
    {
        $user = $_SESSION['user_id'];
    }
    else
    {
        $user = 0;
    }

$query = "INSERT INTO `log` ( `id`, `ip`, `time`, `user`, `do` ) VALUES ( NULL , '".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$user."', '".$loging_do."' ) ;";
mysql_query($query) or die(mysql_error());
$queryes_num++;
