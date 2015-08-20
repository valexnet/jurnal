<?php
/*
PHP Скріпт для ведення вхідно та вихідної кореспонденції, недописався для ДКУ.
*/
session_start();

if (isset($_GET['install']) AND $_GET['install'] == "do" AND !file_exists("inc/db_connect.txt")) include ('inc/install.php');

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if (isset($_SESSION['user_id']))
	{
		$f_address = "true";
	}
	else
	{
		if ($c_ano == 0 && !isset($_GET['team']))
			{
				DIE(file_get_contents("templates/close.html"));
			}
			else
			{
				$f_address = "true";
			}
	}
if ($f_address == "true")
	{
		/* Наша команда */
		if (isset($_GET['team']))
			{
				$address = "true";
				$page.= file_get_contents("templates/team.html");

				$query = "SELECT * FROM `users` WHERE `p_config`<>'0' OR `p_users`<>'0' OR `p_ip`<>'0' ORDER BY `time` DESC;";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				$numberall = mysql_num_rows($res);
				if ($numberall > 0)
					{
						$adminwork = 0;
						while ($row=mysql_fetch_array($res))
							{
								if ($row['work']==1) $adminwork ++;
							}
						if ($adminwork > 0)
							{
								$res = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								while ($row=mysql_fetch_array($res))
									{
										$adminlist_prv = '';
										if ($row['p_config']==1) $adminlist_prv .= "{LANG_USER_ADMIN_CONFIG} ";
										if ($row['p_users']==1) $adminlist_prv .= "{LANG_PPL_P_USERS} ";
										if ($row['p_ip']==1) $adminlist_prv .= "{LANG_USER_P_IP} ";
										if (time() >= $row['time'] + 2592000 ) {$user_color = " color=\"red\"";} else {$user_color = " color=\"green\"";}
										
										if ($row['work']==1) $adminlist_tmp .= "
							<ul>
								<p><b>".$row['name']."</b><br />(".$adminlist_prv.")</p>
								<li>{LANG_TEL1}: <b>".$row['tel1']."</b></li>
								<li>{LANG_TEL2}: <b>".$row['tel2']."</b></li>
								<li>{LANG_TEL3}: <b>".$row['tel3']."</b></li>
								<li>{LANG_USER_TIME_ACTIVITY}: <font".$user_color."><b>".date('Y.m.d H:i:s' ,$row['time'])."</b></font></li>
							</ul>
							<hr>";
									}
								$page = str_replace("{ADMIN_LIST}", $adminlist_tmp, $page);							
							}
							else
							{
								$page = str_replace("{ADMIN_LIST}", "{LANG_NO_ADMIN_IN_BD}", $page);
							}
					}
					else
					{
						$page = str_replace("{ADMIN_LIST}", "{LANG_NO_ADMIN_IN_BD}", $page);
					}
			}

		/* Головна сторінка */
		if ($address <> "true")
			{
				$page.= file_get_contents("templates/first_page.html");
			}
	}
include ("inc/blender.php");
?>
