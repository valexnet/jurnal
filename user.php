<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if (isset($_SESSION['user_id']))
	{
		if(isset($_GET['logout']))
			{
				$adress = "true";
				$loging_do = "{LANG_LOG_USER_LOGOUT}";
				include ('inc/loging.php');
				unset($_SESSION['user_id']);
				unset($_SESSION['user_login']);
				unset($_SESSION['user_pass']);
				unset($_SESSION['user_year']);
				session_unset();
				session_destroy();
				$page.= file_get_contents("templates/information_success.html");
				$page = str_replace("{INFORMATION}", "{LANG_LOGOUT}", $page);
				$timeout = "index.php";
			}
		if(isset($_GET['change_password']))
			{
				$adress = "true";
				if (isset($_POST['user_password']) && isset($_POST['user_password1']) && isset($_POST['user_password2']) && $user_p_user == 1)
					{
						if ($_POST['user_password1'] <> "" OR $_POST['user_password2'] <> "")
							{
								$user_password = md5($_POST['user_password']);
								$user_password1 = md5($_POST['user_password1']);
								$user_password2 = md5($_POST['user_password2']);
								if ($user_password1 == $user_password2 AND $_POST['user_password1'] <> "")
									{
										$query = "SELECT `id` FROM `users` WHERE `id`='".$_SESSION['user_id']."' AND `pass`='".$user_password."' LIMIT 1;";
										$sql = mysql_query($query) or die(mysql_error());
										$queryes_num++;
										if (mysql_num_rows($sql) == 1)
											{
												$loging_do = "{LANG_LOG_USER_CHANGE_PASSWORD}";
												include ('inc/loging.php');
												$query = "UPDATE `users` SET `pass`='".$user_password1."' WHERE `id`='".$_SESSION['user_id']."' LIMIT 1;";
												$sql = mysql_query($query) or die(mysql_error());
												$queryes_num++;
												unset($_SESSION['user_id']);
												unset($_SESSION['user_login']);
												unset($_SESSION['user_pass']);
												session_unset();
												session_destroy();
												$page.= file_get_contents("templates/information_success.html");
												$page = str_replace("{INFORMATION}", "{LANG_PASS_CHENGED}", $page);
												$timeout = "user.php";
											}
											else
											{
												$loging_do = "{LANG_LOG_USERS_CHANGEPASS_ERROR_OLD_PASS}";
												include ('inc/loging.php');
												$page.= file_get_contents("templates/information_danger.html");
												$page = str_replace("{INFORMATION}", "{LANG_OLD_PASS_ERR}", $page);
											}
									}
									else
									{
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_PASS_NOT_MIRROR}", $page);
									}
							}
							else
							{
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_EMPTY_PASS_NOT_ALLOW}", $page);
							}
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						if ($user_p_user <> 1) $page = str_replace("{INFORMATION}", "{LANG_USER_P_USER_0}", $page);
						$page = str_replace("{INFORMATION}", "{LANG_EMPTY_PASS}", $page);
					}
			}
		if ($adress <> "true")
			{
				$page.= file_get_contents("templates/user.html");
				if ($user_p_user == 1) $page.= file_get_contents("templates/user_pass.html");
			}
	}
	else
	{
		if (isset($_POST['login']) && isset($_POST['pass']))
			{
				$login = $_POST['login'];
				$pass = md5($_POST['pass']);
				$query = "SELECT * FROM `users` WHERE `login`='".$login."' AND `pass`='".$pass."' AND `del`='0' LIMIT 1;";
				$sql = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				if (mysql_num_rows($sql) == 1)
					{
						$row = mysql_fetch_assoc($sql);
						if ($row['work'] == 1)
							{
								if ($_SERVER['REMOTE_ADDR'] == $row['ip'])
									{
										$enter = "true";
									}
									else
									{
										if ($row['ip_c'] == 1)
											{
												$enter = "true";
											}
											else
											{
												$loging_do = "{LANG_LOG_USER_ENTER_OTHER_IP} ".$user_login;
												include ('inc/loging.php');
												$page.= file_get_contents("templates/information_danger.html");
												$page = str_replace("{INFORMATION}", "{LANG_LOGIN_ERR_IP}", $page);
											}
									}
									
								if ($c_ano == 0 && $row['p_config'] == 0)
									{
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_LOGIN_ERR_SITE_OFFLINE}", $page);
										$enter = "false";
									}
									
								if  ($enter == "true")
									{
										if (isset($_SESSION['user_login_error_num'])) unset($_SESSION['user_login_error_num']);
										$_SESSION['user_id'] = $row['id'];
										$_SESSION['user_login'] = $row['login'];
										$_SESSION['user_pass'] = $row['pass'];
										$_SESSION['user_year'] = date('Y');
										$loging_do = "{LANG_LOG_USER_ENTER}";
										include ('inc/loging.php');
										$page.= file_get_contents("templates/information_success.html");
										$page = str_replace("{INFORMATION}", "{LANG_LOGIN_OK}", $page);
										$timeout = "index.php";
										$query = "UPDATE `users` SET `time`='".time()."' WHERE `id`='".$row['id']."' LIMIT 1;";
										$sql = mysql_query($query) or die(mysql_error());
										$queryes_num++;
									}
							}
							else
							{
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_LOGIN_ERR_WORK}", $page);
							}
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_LOGIN_ERR}", $page);
						if (isset($_SESSION['user_login_error_num'])) {} else $_SESSION['user_login_error_num'] = 0;
						$_SESSION['user_login_error_num'] = $_SESSION['user_login_error_num'] + 1;
						if ($_SESSION['user_login_error_num'] >= 5)
							{
								unset($_SESSION['user_login_error_num']);
								session_unset();
								session_destroy();
								include ('inc/banned_ip.php');
							}
					}
			}
			else
			{
				if ($_SESSION['user_login_error_num'] >= 4)
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_LOGIN_ERR_4_TIMES}", $page);
					}
					
				if ($c_lch == 1)
					{
						$query = "SELECT `login`,`name`,`ip` FROM `users` WHERE `del`='0' ORDER BY `name` ;";
						$res = mysql_query($query) or die(mysql_error());
						$queryes_num++;
				
						while ($row=mysql_fetch_array($res))
							{
								if ($row['ip'] == $_SERVER['REMOTE_ADDR'])
									{
										$users_list.= "<OPTION selected value = \"".$row['login']."\">".$row['name']."</OPTION>";
									}
									else
									{
										$users_list.= "<OPTION value = \"".$row['login']."\">".$row['name']."</OPTION>";
									}
							}
						$page.= file_get_contents("templates/user_login.html");
						$page = str_replace("{USERS_LIST}", "<SELECT name=\"login\" class=\"form-control\" />".$users_list."</SELECT>", $page);
					}
					else
					{
						$page.= file_get_contents("templates/user_login.html");
						$page = str_replace("{USERS_LIST}", "<input type=\"text\" name=\"login\" />", $page);
					}
			}
	}
include ("inc/blender.php");
?>