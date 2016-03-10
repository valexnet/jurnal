<?php
session_start();
include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if (isset($_SESSION['user_id']))
	{
		$page .= file_get_contents("templates/jurnal_dox_1_header.html");
		$pre_link = "";
		$query_where = "";
		$query = "SHOW TABLES LIKE \"DB_".$_SESSION['user_year']."_DOX_1\";";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		if (mysql_num_rows($res) == 0)
			{
				$query = file_get_contents("inc/db_dox_1.txt");
				$query = str_replace("{YEAR}", $_SESSION['user_year'], $query);
				mysql_query($query) or die(mysql_error());
			}
			
		if ($privat8 == 1) //Бачити доходи
			{
				$page .= file_get_contents("templates/jurnal_dox_1_view.html");
			}
			else
			{
				$page.= file_get_contents("templates/information.html");
				$page = str_replace("{INFORMATION}", "{LANG_DOX_NO_VIEW_PERMISSIONS}", $page);
			}
	}
	else
	{
		$loging_do = "{LANG_LOG_JURNAL_DOX_1_403}";
		include ('inc/loging.php');
		header('HTTP/1.1 403 Forbidden');
		$page.= file_get_contents("templates/information_danger.html");
		$page = str_replace("{INFORMATION}", "{LANG_403}", $page);
		$timeout = "index.php";
	}

include ("inc/blender.php");
