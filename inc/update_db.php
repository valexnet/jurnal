<?phpif ($c_ver_db == 3)    {        $query = "UPDATE `config` SET `ver` = '4' WHERE `id` = '1' LIMIT 1 ;";        $res = mysql_query($query) or die(mysql_error());        $c_ver_db = 4;    }if ($c_ver_db == 4)    {        $query = "ALTER TABLE `db_".date('Y')."_in` CHANGE `make_data` `make_data` DATETIME NULL DEFAULT NULL ";        $res = mysql_query($query) or die(mysql_error());        $query = "UPDATE `config` SET `ver` = '5' WHERE `id` = '1' LIMIT 1 ;";        $res = mysql_query($query) or die(mysql_error());        $c_ver_db = 5;    }if ($c_ver_db == 5)    {        $query = "ALTER TABLE `db_".date('Y')."_out` CHANGE `str` `blank` INT( 11 ) NULL DEFAULT NULL ;";        mysql_query($query) or die(mysql_error());        $query = "SELECT * FROM `db_".date('Y')."_out_blank` ; ";        $res = mysql_query($query) or die(mysql_error());        while ($row=mysql_fetch_array($res))            {                $tmp_query = "UPDATE `db_".date('Y')."_out` SET `blank` = '".$row['id']."' WHERE `id` = '".$row['num']."' LIMIT 1 ;";                mysql_query($tmp_query) or die(mysql_error());            }        $query = "DROP TABLE `db_".date('Y')."_out_blank` ;";        mysql_query($query) or die(mysql_error());        $tmp_query = "UPDATE `db_".date('Y')."_out` SET `blank` = NULL WHERE `blank`='0' ;";        mysql_query($tmp_query) or die(mysql_error());        $query = "UPDATE `config` SET `ver` = '6' WHERE `id` = '1' LIMIT 1 ;";        $res = mysql_query($query) or die(mysql_error());        $c_ver_db = 6;    }if ($c_ver_db == 6)    {        $query = "UPDATE `config` SET `ver` = '7' WHERE `id` = '1' LIMIT 1 ;";        $res = mysql_query($query) or die(mysql_error());        $c_ver_db = 7;    }if ($c_ver_db == 7)    {        mysql_query("DROP TABLE IF EXISTS messages;");        mysql_query("CREATE TABLE `messages` (  `id` int(5) NOT NULL auto_increment, `ip` varchar(15) NOT NULL, `time` int(11) NOT NULL, `name` char(255) NOT NULL, `text` text, PRIMARY KEY  (`id`)) ;");        mysql_query("UPDATE `config` SET `ver` = '8' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 8;    }if ($c_ver_db == 8)    {        mysql_query("UPDATE `config` SET `reg_file` = 'zip|eml|rar|7z|txt|doc|xls|docx|xlcx|pdf|jpg', `ver` = '9' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 9;    }if ($c_ver_db == 9)    {        mysql_query("ALTER TABLE `db_".date('Y')."_in` ADD `inform_users` VARCHAR( 5000 ) NULL DEFAULT NULL ;");        mysql_query("ALTER TABLE `db_".date('Y')."_in_ep` ADD `inform_users` VARCHAR( 5000 ) NULL DEFAULT NULL ;");        mysql_query("UPDATE `config` SET `ver` = '10' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 10;    }if ($c_ver_db == 10)    {        mysql_query("ALTER TABLE `config` ADD `index_module` VARCHAR( 500 ) NOT NULL ;");        mysql_query("UPDATE `config` SET `index_module` = '[index]/[str]-[nom]' WHERE `id` =1 LIMIT 1 ;");        mysql_query("UPDATE `config` SET `ver` = '11' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 11;    }if ($c_ver_db == 11)    {        mysql_query("ALTER TABLE `users` CHANGE `privat` `privat` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL  ;");        mysql_query("UPDATE `config` SET `ver` = '12' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 12;    }if ($c_ver_db == 12)    {        mysql_query("UPDATE `config` SET `ver` = '13' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 13;    }if ($c_ver_db == 13)    {		mysql_query("CREATE TABLE `db_it_invent` (			`id` int(11) NOT NULL auto_increment,			`invent` int(11) NOT NULL,			`inv_plus` varchar(10) NOT NULL,			`name` varchar(250) NOT NULL,			`data_made` date NOT NULL,			`data_install` date NOT NULL,			`room_id` int(11) NOT NULL,			`user_id` int(11) NOT NULL,			`status_id` int(11) NOT NULL,			`suma` decimal(15,2) NOT NULL,			`amort` int(3) NOT NULL,			PRIMARY KEY  (`id`)		);") or die(mysql_error());        mysql_query("CREATE TABLE `db_it_kt` (			`id` int(11) NOT NULL auto_increment,			`invent_id` int(11) NOT NULL,			`name` varchar(250) NOT NULL,			`sn` varchar(100) NOT NULL,			`data_made` int(4) NOT NULL,			`data_install` int(4) NOT NULL,			`status_1_id` int(11) NOT NULL,			`status_2_id` int(11) NOT NULL,			`func` varchar(250) NOT NULL,			`note` text NOT NULL,			PRIMARY KEY  (`id`)		);") or die(mysql_error());        mysql_query("CREATE TABLE `db_it_rooms` (			  `id` int(11) NOT NULL auto_increment,			  `nom` int(11) NOT NULL,			  `name` varchar(150) NOT NULL,			  `name_full` varchar(250) NOT NULL,			  `str_id` int(11) NOT NULL,			  PRIMARY KEY  (`id`)		);") or die(mysql_error());        mysql_query("CREATE TABLE `db_it_specs` (			`id` int(11) NOT NULL auto_increment,			`kt_id` int(11) NOT NULL,			`name` varchar(150) NOT NULL,			`value` varchar(50) NOT NULL,			PRIMARY KEY  (`id`)		);") or die(mysql_error());        mysql_query("CREATE TABLE `db_it_status` (			`id` int(11) NOT NULL auto_increment,			`name` varchar(150) NOT NULL,			`name_full` varchar(250) NOT NULL,			`text_color` varchar(7) NOT NULL,			`bg_color` varchar(7) NOT NULL,			PRIMARY KEY  (`id`)		);") or die(mysql_error());        mysql_query("INSERT INTO `db_it_status` VALUES (1, 'Робочий', 'Техніка працює, вади відсутні', 'green', '#fff');") or die(mysql_error());        mysql_query("INSERT INTO `db_it_status` VALUES (2, 'Потребує ремонту', 'Техніка потребує ремонту, ремонт доцільний', 'green', '#ff0');") or die(mysql_error());        mysql_query("INSERT INTO `db_it_status` VALUES (3, 'До списання', 'Техніка морально застаріла, або ремонт не доцільний', '#fff', 'red');") or die(mysql_error());        mysql_query("INSERT INTO `db_it_status` VALUES (4, 'Архів', 'Техніка робоча', '#000', '#888');") or die(mysql_error());        mysql_query("INSERT INTO `db_it_status` VALUES (5, 'Оренда', 'Техніка орендована', '#666', '#fff');") or die(mysql_error());        mysql_query("INSERT INTO `db_it_status` VALUES (9, 'Резерв', 'Техніка в робочому стані, знаходиться в резерві', '#000', '#666');") or die(mysql_error());        mysql_query("INSERT INTO `db_it_status` VALUES (10, 'Інформація не повна', 'Техніка додана, але не відповідайє дійсності.', '#fff', '#000');") or die(mysql_error());		mysql_query("CREATE TABLE `db_it_soft` (			`id` int(11) NOT NULL auto_increment,			`invent_id` int(11) NOT NULL,			`name` varchar(150) NOT NULL,			`ver` varchar(15) NOT NULL,			`data` date NOT NULL,			`lic` varchar(150) NOT NULL,			PRIMARY KEY  (`id`)		);") or die(mysql_error());		mysql_query("UPDATE `config` SET `ver` = '14' WHERE `id` = '1' LIMIT 1 ;") or die(mysql_error());        $c_ver_db = 14;    }if ($c_ver_db == 14)    {		@mysql_query("ALTER TABLE `db_2015_out` DROP `to_mail`;");		@mysql_query("ALTER TABLE `db_2016_out` DROP `to_mail`;");	}unlink('inc/update_db.php') or die("Can't delete update_db.php");?>