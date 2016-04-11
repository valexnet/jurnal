<?php
session_start();
include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if (isset($_SESSION['user_id']))
	{
		if ($privata == 1)
			{
				$page .= file_get_contents("templates/jurnal_it.html");
			}
			else
			{
				$page.= file_get_contents("templates/information.html");
				$page = str_replace("{INFORMATION}", "{LANG_IT_NO_VIEW_PERMISSIONS}", $page);
			}
	}
	else
	{
		$loging_do = "{LANG_LOG_JURNAL_IT_403}";
		include ('inc/loging.php');
		header('HTTP/1.1 403 Forbidden');
		$page.= file_get_contents("templates/information_danger.html");
		$page = str_replace("{INFORMATION}", "{LANG_403}", $page);
		$timeout = "index.php";
	}

include ("inc/blender.php");
?>
