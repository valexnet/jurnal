<?if ($c_ver_db == 3)    {        $query = "UPDATE `config` SET `ver` = '4' WHERE `id` = '1' LIMIT 1 ;";        $res = mysql_query($query) or die(mysql_error());        $c_ver_db = 4;    }if ($c_ver_db == 4)    {        $query = "ALTER TABLE `db_".date('Y')."_in` CHANGE `make_data` `make_data` DATETIME NULL DEFAULT NULL ";        $res = mysql_query($query) or die(mysql_error());        $query = "UPDATE `config` SET `ver` = '5' WHERE `id` = '1' LIMIT 1 ;";        $res = mysql_query($query) or die(mysql_error());        $c_ver_db = 5;    }    if ($c_ver_db == 5)    {        $query = "ALTER TABLE `db_".date('Y')."_out` CHANGE `str` `blank` INT( 11 ) NULL DEFAULT NULL ;";        mysql_query($query) or die(mysql_error());        $query = "SELECT * FROM `db_".date('Y')."_out_blank` ; ";        $res = mysql_query($query) or die(mysql_error());        while ($row=mysql_fetch_array($res))            {                $tmp_query = "UPDATE `db_".date('Y')."_out` SET `blank` = '".$row['id']."' WHERE `id` = '".$row['num']."' LIMIT 1 ;";                mysql_query($tmp_query) or die(mysql_error());            }        $query = "DROP TABLE `db_".date('Y')."_out_blank` ;";        mysql_query($query) or die(mysql_error());                $tmp_query = "UPDATE `db_".date('Y')."_out` SET `blank` = NULL WHERE `blank`='0' ;";        mysql_query($tmp_query) or die(mysql_error());                    $query = "UPDATE `config` SET `ver` = '6' WHERE `id` = '1' LIMIT 1 ;";        $res = mysql_query($query) or die(mysql_error());        $c_ver_db = 6;    }    if ($c_ver_db == 6)    {        $query = "UPDATE `config` SET `ver` = '7' WHERE `id` = '1' LIMIT 1 ;";        $res = mysql_query($query) or die(mysql_error());        $c_ver_db = 7;    }if ($c_ver_db == 7)    {        mysql_query("DROP TABLE IF EXISTS messages;");        mysql_query("CREATE TABLE `messages` (  `id` int(5) NOT NULL auto_increment, `ip` varchar(15) NOT NULL, `time` int(11) NOT NULL, `name` char(255) NOT NULL, `text` text, PRIMARY KEY  (`id`)) ;");        mysql_query("UPDATE `config` SET `ver` = '8' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 8;    }if ($c_ver_db == 8)    {        mysql_query("UPDATE `config` SET `reg_file` = 'zip|eml|rar|7z|txt|doc|xls|docx|xlcx|pdf|jpg', `ver` = '9' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 9;    }    if ($c_ver_db == 9)    {        mysql_query("ALTER TABLE `db_".date('Y')."_in` ADD `inform_users` VARCHAR( 5000 ) NULL DEFAULT NULL ;");        mysql_query("ALTER TABLE `db_".date('Y')."_in_ep` ADD `inform_users` VARCHAR( 5000 ) NULL DEFAULT NULL ;");        mysql_query("UPDATE `config` SET `ver` = '10' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 10;    }	if ($c_ver_db == 10)    {        mysql_query("ALTER TABLE `config` ADD `index_module` VARCHAR( 500 ) NOT NULL ;");        mysql_query("UPDATE `config` SET `index_module` = '[index]/[str]-[nom]' WHERE `id` =1 LIMIT 1 ;");        mysql_query("UPDATE `config` SET `ver` = '11' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 11;    }if ($c_ver_db == 11)    {        mysql_query("ALTER TABLE `users` CHANGE `privat` `privat` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL  ;");        mysql_query("UPDATE `config` SET `ver` = '12' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 12;    }unlink('inc/update_db.php') or die("Can't delete update_db.php");?>