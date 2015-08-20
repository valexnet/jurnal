<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if ($user_p_config == 1)
	{
		if (isset($_GET['edit']))
			{
				$adress = "true";
				if (isset($_GET['do']))
					{
						$edit = "true";
						
						$_POST['sitename'] = str_replace($srch, $rpls, $_POST['sitename']);
						$_POST['url'] = str_replace($srch, $rpls, $_POST['url']);
						$_POST['mysql_bin'] = str_replace($srch, $rpls, $_POST['mysql_bin']);
						$_POST['backupdir'] = str_replace($srch, $rpls, $_POST['backupdir']);
						$_POST['backup_limit'] = str_replace($srch, $rpls, $_POST['backup_limit']);
						$_POST['anonymous_allow'] = str_replace($srch, $rpls, $_POST['anonymous_allow']);

						if ($_POST['sitename'] == "")
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_SITENAME}", $page);
							}
						if ($_POST['url'] == "")
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_URL}", $page);
							}
						if ($_POST['mysql_bin'] == "")
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_MYSQL_BIN}", $page);
							}
						if ($_POST['backupdir'] == "")
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_BACKUPDIR}", $page);
							}
						if ($_POST['backupdir2'] == "")
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_BACKUPDIR2}", $page);
							}
						if ($_POST['backup_plus'] == "")
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_BACKUP_PLUS}", $page);
							}
						if ($_POST['backup_limit'] < 0 OR $_POST['backup_limit'] > 9999)
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_BACKUP_LIMIT}", $page);
							}
						if ($_POST['anonymous_allow'] <> "" AND $_POST['anonymous_allow'] <> 1)
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_ANONYMOUS_ALLOW}", $page);
							}
						if ($_POST['timeout_auht'] < 300)
							{
								if ($_POST['timeout_auht'] <> 0)
									{
										$error = "true";
										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_TIMEOUT_AUHT}", $page);
									}
							}
						if ($_POST['page_limit'] <> "" AND $_POST['page_limit'] < 1)
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_PAGE_LIMIT}", $page);
							}
						if ($_POST['cron_backup_timeout'] < 0)
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_CRON_BACKUP_TIMEOUT}", $page);
							}							
						if ($_POST['cron_backup_on_email_timeout'] < 0)
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_CRON_BACKUP_ON_EMAIL_TIMEOUT}", $page);
							}
						if ($_POST['login_choose'] <> 0 AND $_POST['login_choose'] <> 1)
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_LOGIN_CHOOSE}", $page);
							}
						if ($_POST['year_start'] < 0 OR $_POST['year_start'] > 9999 OR $_POST['year_start'][3] == "")
							{
								$error = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_YEAR_START}", $page);
							}							
						if ($error <> "true")
							{
								if ($_POST['anonymous_allow'] == 1) {$anonymous_allow = 1;} else {$anonymous_allow = 0;}
								if ($_POST['login_choose'] == 1) {$login_choose = 1;} else {$login_choose = 0;}
								$query = "UPDATE `config` SET
								`name`='".$_POST['sitename']."',
								`url`='".$_POST['url']."',
								`mysql_bin`='".$_POST['mysql_bin']."',
								`backup_dir`='".$_POST['backupdir']."',
								`backup_dir2`='".$_POST['backupdir2']."',
								`backup_plus`='".$_POST['backup_plus']."',
								`backup_lim`='".$_POST['backup_limit']."',
								`anonymous`='".$anonymous_allow."', 
								`user_timeout`='".$_POST['timeout_auht']."', 
								`page_limit`='".$_POST['page_limit']."', 
								`login_choose`='".$login_choose."', 
								`year_start`='".$_POST['year_start']."' 
								WHERE `id`='1' LIMIT 1;";
								$temp345345 = $query;
								$sql = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								$query = "UPDATE `cron` SET `time`='".$_POST['cron_backup_timeout']."' WHERE `name`='backup' LIMIT 1;";
								$sql = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								$query = "UPDATE `cron` SET `time`='".$_POST['cron_backup_on_email_timeout']."' WHERE `name`='backup_on_email' LIMIT 1;";
								$sql = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								$loging_do = "{LANG_LOG_CONFIG_EDIT}";
								include ('inc/loging.php');
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_CONFIG_SAVED}", $page);
								$timeout = "config.php";
							}
					}
				if ($edit <> "true")
					{
						$page.= file_get_contents("templates/config_edit.html");
						if ($c_ano == 1) {$page = str_replace("{ANONYMOUS_ALLOW_C}", " checked", $page);} else {$page = str_replace("{ANONYMOUS_ALLOW_C}", "", $page);}
						if ($c_lch == 1) {$page = str_replace("{LOGIN_CHOOSE_Ñ}", " checked", $page);} else {$page = str_replace("{LOGIN_CHOOSE_Ñ}", "", $page);}
					}
			}
		if ($adress <> "true") $page.= file_get_contents("templates/config.html");

		$query = "SELECT * FROM `cron` WHERE `name`='backup' LIMIT 1 ;";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		while ($row=mysql_fetch_array($res))
			{
				$backup_timeout = $row['time'];
				$backup_last = $row['last'];
			}

		$query = "SELECT * FROM `cron` WHERE `name`='backup_on_email' LIMIT 1 ;";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		while ($row=mysql_fetch_array($res))
			{
				$backup_on_email_timeout = $row['time'];
				$backup_on_email_last = $row['last'];
			}

		$page = str_replace("{CRON_BACKUP_TIMEOUT}", $backup_timeout, $page);
		$page = str_replace("{CRON_BACKUP_LAST}", date('Y.m.d H:i:s', $backup_last), $page);
		$page = str_replace("{CRON_BACKUP_ON_EMAIL_TIMEOUT}", $backup_on_email_timeout, $page);
		$page = str_replace("{CRON_BACKUP_ON_EMAIL_LAST}", date('Y.m.d H:i:s', $backup_on_email_last), $page);

	}
	else
	{
		$loging_do = "{LANG_LOG_CONFIG_403}";
		include ('inc/loging.php');
		header('HTTP/1.1 403 Forbidden');
		$page.= file_get_contents("templates/information.html");
		$page = str_replace("{INFORMATION}", "{LANG_403}", $page);
		$timeout = "index.php";
	}
include ("inc/blender.php");
?>