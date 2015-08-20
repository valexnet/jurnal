<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if ($user_p_ip == 1)
	{
		if (isset($_GET['user_add']))
			{
				if (isset($_GET['save']))
					{
						$save = "true";
						if ($_POST['ban_ip'] == "")
							{
								$error_save = "true";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_BAN_ADMIN_ADD_EMPTY_IP}", $page);
							}
						if ($error_save <> "true")
							{
								$_POST['ban_ip'] = str_replace(",", ".", $_POST['ban_ip']);
								$query = "INSERT INTO `banned` (`id`, `time`, `ip`, `where`) VALUES (NULL , '".time()."', '".$_POST['ban_ip']."', 'ADMIN: ".$_SESSION['user_id']."');";
								$res = mysql_query($query) or $error_save = "true";
								$queryes_num++;
								if ($error_save == "true")
									{
										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", "{LANG_BAN_ADMIN_ADD_BD_ERROR}", $page);
									}
									else
									{
										$loging_do = "{LANG_LOG_BAN_ADD} ".$_POST['ban_ip'];
										include ('inc/loging.php');
										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", "{LANG_BAN_ADMIN_ADD_OK}", $page);
										$timeout = "ban.php";
									}
							}
					}
					else
					{
						$adress = "true";
						$page.= file_get_contents("templates/ban_add.html");
						if ($_GET['user_add'] <> "")
							{
								$page = str_replace("{BAN_IP}", $_GET['user_add'], $page);
							}
							else
							{
								$page = str_replace("{BAN_IP}", "", $page);
							}
					}
			}
		if (isset($_GET['user_del']))
			{
				if ($_GET['user_del'] == "")
					{
						DIE ("ERROR #2 BAN.PHP (user_del)");
					}
					else
					{
						//$adress = "true";
						$user_id = $_GET['user_del'];
						$loging_do = "{LANG_LOG_BAN_DEL} ".$user_id;
						include ('inc/loging.php');
						mysql_query("DELETE FROM `banned` WHERE `ip`='".$user_id."';");
						$queryes_num++;
						$page.= file_get_contents("templates/information.html");
						$page = str_replace("{INFORMATION}", "{LANG_BAN_ADMIN_DELETED}", $page);
					}
			}
		if ($adress <> "true")
			{
				$page.= file_get_contents("templates/ban.html");
				$query = "SELECT * FROM `banned` ORDER BY `id` DESC;";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				$numberall = mysql_num_rows($res);
				while ($row=mysql_fetch_array($res))
					{
						$template_clients.="<tr valign=\"middle\" align=\"center\">
							<td align=\"center\">".$row['id']."</td>
							<td align=\"center\">".date('d.m.Y H:s:i', $row['time'])."</td>
							<td align=\"center\">".$row['ip']."</td>
							<td align=\"center\">".$row['where']."</td>
							<td align=\"center\"><a href=\"ban.php?user_del=".$row['ip']."\" onClick=\"if(confirm('{LANG_BAN_ADMIN_DEL_CONFIRM} ".$row['ip']." ?')) {return true;} return false;\"><img src=\"templates/images/cross_octagon.png\" border=\"0\" alt=\"{LANG_BAN_ADMIN_DEL}\" title=\"{LANG_BAN_ADMIN_DEL}\"></a></td>
						</tr>";
					}
				$page = str_replace("{BAN_VIEWS}", $template_clients, $page);
				$page = str_replace("{BAN_STATS}", "<b>".$numberall."</b> {LANG_BAN_ADMIN_TOTTAL}", $page);
			}
	}
	else
	{
		$loging_do = "{LANG_LOG_BAN_403}";
		include ('inc/loging.php');
		header('HTTP/1.1 403 Forbidden');
		$page.= file_get_contents("templates/information.html");
		$page = str_replace("{INFORMATION}", "{LANG_403}", $page);
		$timeout = "index.php";
	}
include ("inc/blender.php");
?>