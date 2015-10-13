<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if ($user_p_users == 1)
	{
		if (isset($_GET['user_add']))
			{
				$adress = "true";
				if (isset($_GET['save']))
					{
						$save = "true";
						if ($_POST['user_password1'] == "" OR $_POST['user_password2'] == "")
							{
								$error_save = "true";
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_EMPTY_PASS_NOT_ALLOW}", $page);
							}
						if ($_POST['user_password1'] <> $_POST['user_password2'])
							{
								$error_save = "true";
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_PASS_NOT_MIRROR}", $page);
							}
						if ($_POST['user_login'] == "")
							{
								$error_save = "true";
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_ADD_EMPTY_LOGIN}", $page);
							}
						if ($_POST['user_name'] == "")
							{
								$error_save = "true";
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_ADD_EMPTY_NAME}", $page);
							}
						if ($_POST['user_lang'] == "")
							{
								$_POST['user_lang'] = "ua";
							}
							else
							{
								if (!file_exists("inc/lang/".$_POST['user_lang'].".php"))
									{
										$error_save = "true";
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_ERROR_LANG_FILE_NOT_EXIST}", $page);
									}
							}
						if ($_POST['user_ip'] == "") $_POST['user_ip'] = 0;
						if ($_POST['user_mail1'] == "") $_POST['user_mail1'] = 0;
						if ($_POST['user_mail2'] == "") $_POST['user_mail2'] = 0;
						if ($_POST['user_tel1'] == "") $_POST['user_tel1'] = 0;
						if ($_POST['user_tel2'] == "") $_POST['user_tel2'] = 0;
						if ($_POST['user_tel3'] == "") $_POST['user_tel3'] = 0;

						if ($_POST['user_ip_c'] <> "" AND $_POST['user_ip_c'] <> "1")
							{
								$error_save = "true";
								include ('inc/banned_ip.php');
							}
						if ($_POST['user_a_ip'] <> "" AND $_POST['user_a_ip'] <> "1")
							{
								$error_save = "true";
								include ('inc/banned_ip.php');
							}
						if ($_POST['user_p_user'] <> "" AND $_POST['user_p_user'] <> "1")
							{
								$error_save = "true";
								include ('inc/banned_ip.php');
							}
						if ($_POST['user_p_config'] <> "" AND $_POST['user_p_config'] <> "1")
							{
								$error_save = "true";
								include ('inc/banned_ip.php');
							}
						if ($_POST['user_p_log'] <> "" AND $_POST['user_p_log'] <> "1")
							{
								$error_save = "true";
								include ('inc/banned_ip.php');
							}
						if ($_POST['user_p_users'] <> "" AND $_POST['user_p_users'] <> "1")
							{
								$error_save = "true";
								include ('inc/banned_ip.php');
							}
						if ($_POST['user_p_addr'] <> "" AND $_POST['user_p_addr'] <> "1")
							{
								$error_save = "true";
								include ('inc/banned_ip.php');
							}
						if ($_POST['user_p_ip'] <> "" AND $_POST['user_p_ip'] <> "1")
							{
								$error_save = "true";
								include ('inc/banned_ip.php');
							}
						if ($_POST['user_p_mod'] <> "" AND $_POST['user_p_mod'] <> "1")
							{
								$error_save = "true";
								include ('inc/banned_ip.php');
							}
						if ($_POST['user_work'] <> "" AND $_POST['user_work'] <> "1")
							{
								$error_save = "true";
								include ('inc/banned_ip.php');
							}
						if ($_POST['user_str'] == "" )
							{
								$error_save = "true";
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_ADD_EMPTY_STR}", $page);
							}
						
						if ($error_save <> "true")
							{
								$query = "SELECT `login` FROM `users` WHERE `login`='".$_POST['user_login']."' AND `del`='0' ; ";
								$res = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								$numberall = mysql_num_rows($res);
								if ($numberall <> 0)
									{
										$error_save = "true";
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_ADD_DOUBLE_LOGIN}", $page);
									}
									else
									{
										$user_password = md5($_POST['user_password1']);
										$user_login = $_POST['user_login'];
										$user_name = $_POST['user_name'];
										$user_ip = $_POST['user_ip'];
										$user_lang = $_POST['user_lang'];
										$user_template = $_POST['user_template'];
										$user_mail1 = $_POST['user_mail1'];
										$user_mail2 = $_POST['user_mail2'];
										$user_tel1 = $_POST['user_tel1'];
										$user_tel2 = $_POST['user_tel2'];
										$user_tel3 = $_POST['user_tel3'];
										$user_str = $_POST['user_str'];
										
										if ($user_str)
											{
												foreach ($user_str as $u_s)
													{
														$user_str_bd .= $u_s.",";
													}
											}
											
										$user_privat = $_POST['user_privat'];
										
										if ($user_privat)
											{
												foreach ($user_privat as $u_p)
													{
														$user_privat_bd .= $u_p.",";
													}
											}
										
										if ($_POST['user_ip_c'] == 1) {$user_ip_c = 1;} else {$user_ip_c = 0;}
										if ($_POST['user_a_ip'] == 1) {$user_a_ip = 1;} else {$user_a_ip = 0;}
										if ($_POST['user_p_user'] == 1) {$user_p_user = 1;} else {$user_p_user = 0;}
										if ($_POST['user_p_config'] == 1) {$user_p_config = 1;} else {$user_p_config = 0;}
										if ($_POST['user_p_log'] == 1) {$user_p_log = 1;} else {$user_p_log = 0;}
										if ($_POST['user_p_users'] == 1) {$user_p_users = 1;} else {$user_p_users = 0;}
										if ($_POST['user_p_addr'] == 1) {$user_p_addr = 1;} else {$user_p_addr = 0;}
										if ($_POST['user_p_ip'] == 1) {$user_p_ip = 1;} else {$user_p_ip = 0;}
										if ($_POST['user_p_mod'] == 1) {$user_p_mod = 1;} else {$user_p_mod = 0;}
										if ($_POST['user_work'] == 1) {$user_work = 1;} else {$user_work = 0;}
										$query = "INSERT INTO `users` (`id` ,`login` ,`pass` ,`out` ,`name` ,`time` ,`ip` ,`ip_c` ,`a_ip` ,`l_ip` ,`work` ,`lang` ,`reg` ,`p_user` ,`p_config` ,`p_log` ,`p_users` ,`p_addr` ,`p_ip` ,`p_mod` ,`mail1` ,`mail2` ,`tel1` ,`tel2` ,`tel3`, `structura`, `privat`, `del` ) VALUES (NULL , '".$user_login."', '".$user_password."', '0', '".$user_name."', '0', '".$user_ip."', '".$user_ip_c."', '".$user_a_ip."', '0', '".$user_work."', '".$user_lang."', '".time()."', '".$user_p_user."', '".$user_p_config."', '".$user_p_log."', '".$user_p_users."', '".$user_p_addr."', '".$user_p_ip."', '".$user_p_mod."', '".$user_mail1."', '".$user_mail2."', '".$user_tel1."', '".$user_tel2."', '".$user_tel3."', '".$user_str_bd."', '".$user_privat_bd."', '0' );";
										$res = mysql_query($query) or $error_save = "true";
										$queryes_num++;
										if ($error_save == "true")
											{
												$page.= file_get_contents("templates/information_danger.html");
												$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_ADD_BD_ERROR}", $page);
											}
											else
											{
												$loging_do = "{LANG_LOG_USERS_ADD} ".$user_login;
												include ('inc/loging.php');
												$page.= file_get_contents("templates/information_success.html");
												$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_ADD_OK}", $page);
											}
									}
							}

					}
					else
					{
						$page .= file_get_contents("templates/users_add.html");
						$query = "SELECT `id`,`index`,`name` FROM `structura` WHERE `work`='1' ORDER BY `index`;";
						$res = mysql_query($query) or die(mysql_error());
						$queryes_num++;
						$numberall = mysql_num_rows($res);
						if ($numberall > 0)
							{
								while ($row=mysql_fetch_array($res))
									{
										$list_str_size++;
										$temp_str .= "<OPTION value = \"".$row['id']."\">(".$row['index'].") ".$row['name']."</OPTION>";
									}
							}
							else
							{
								$page = str_replace("{LIST_STRUCTURA_SIZE}", 1, $page);
								$temp_str .= "<OPTION value = \"\">{LANG_NDI_STR_EMPTY}</OPTION>";
								$page = str_replace("{LIST_STRUCTURA}", $temp_str, $page);
							}
						if ($list_str_size < 3) $list_str_size = 3;
						$page = str_replace("{LIST_STRUCTURA_SIZE}", $list_str_size, $page);
						$page = str_replace("{LIST_STRUCTURA}", $temp_str, $page);
					}
			}
		if (isset($_GET['user_edit']))
			{
				$adress = "true";
				$user_edit = $_GET['user_edit'];
				if (isset($_GET['save']))
					{
						$save = "true";
						$query = "SELECT * FROM `users` WHERE `id`='".$user_edit."' AND `del`='0' LIMIT 1;";
						$res = mysql_query($query) or die(mysql_error());
						$queryes_num++;
						$numberall = mysql_num_rows($res);
						if ($numberall == 0)
							{
								$error_save = "true";
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_EDIT_NO_ID}", $page);
							}
							else
							{
								if ($_POST['user_password1'] <> "" OR $_POST['user_password2'] <> "")
									{
										$pass_change = "true";
										if ($_POST['user_password1'] <> $_POST['user_password2'])
											{
												$error_save = "true";
												$page.= file_get_contents("templates/information_danger.html");
												$page = str_replace("{INFORMATION}", "{LANG_PASS_NOT_MIRROR}", $page);
											}
									}
								if ($_POST['user_login'] == "")
									{
										$error_save = "true";
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_ADD_EMPTY_LOGIN}", $page);
									}
								if ($_POST['user_name'] == "")
									{
										$error_save = "true";
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_ADD_EMPTY_NAME}", $page);
									}
								if ($_POST['user_lang'] == "")
									{
										$_POST['user_lang'] = "ua";
									}
									else
									{
										if (!file_exists("inc/lang/".$_POST['user_lang'].".php"))
											{
												$error_save = "true";
												$page.= file_get_contents("templates/information_danger.html");
												$page = str_replace("{INFORMATION}", "{LANG_ERROR_LANG_FILE_NOT_EXIST}", $page);
											}
									}
								if ($_POST['user_ip'] == "") $_POST['user_ip'] = 0;
								if ($_POST['user_mail1'] == "") $_POST['user_mail1'] = 0;
								if ($_POST['user_mail2'] == "") $_POST['user_mail2'] = 0;
								if ($_POST['user_tel1'] == "") $_POST['user_tel1'] = 0;
								if ($_POST['user_tel2'] == "") $_POST['user_tel2'] = 0;
								if ($_POST['user_tel3'] == "") $_POST['user_tel3'] = 0;
								
								if ($_POST['user_ip_c'] <> "" AND $_POST['user_ip_c'] <> "1")
									{
										$error_save = "true";
										include ('inc/banned_ip.php');
									}
								if ($_POST['user_a_ip'] <> "" AND $_POST['user_a_ip'] <> "1")
									{
										$error_save = "true";
										include ('inc/banned_ip.php');
									}
								if ($_POST['user_p_user'] <> "" AND $_POST['user_p_user'] <> "1")
									{
										$error_save = "true";
										include ('inc/banned_ip.php');
									}
								if ($_POST['user_p_config'] <> "" AND $_POST['user_p_config'] <> "1")
									{
										$error_save = "true";
										include ('inc/banned_ip.php');
									}
								if ($_POST['user_p_log'] <> "" AND $_POST['user_p_log'] <> "1")
									{
										$error_save = "true";
										include ('inc/banned_ip.php');
									}
								if ($_POST['user_p_users'] <> "" AND $_POST['user_p_users'] <> "1")
									{
										$error_save = "true";
										include ('inc/banned_ip.php');
									}
								if ($_POST['user_p_addr'] <> "" AND $_POST['user_p_addr'] <> "1")
									{
										$error_save = "true";
										include ('inc/banned_ip.php');
									}
								if ($_POST['user_p_ip'] <> "" AND $_POST['user_p_ip'] <> "1")
									{
										$error_save = "true";
										include ('inc/banned_ip.php');
									}
								if ($_POST['user_p_mod'] <> "" AND $_POST['user_p_mod'] <> "1")
									{
										$error_save = "true";
										include ('inc/banned_ip.php');
									}
								if ($_POST['user_work'] <> "" AND $_POST['user_work'] <> "1")
									{
										$error_save = "true";
										include ('inc/banned_ip.php');
									}
								if ($_POST['user_str'] == "")
									{
										$error_save = "true";
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_ADD_EMPTY_STR}", $page);
									}
								$query = "SELECT `login` FROM `users` WHERE `login`='".$_POST['user_login']."' AND `id`<>'".$user_edit."';";
								$res = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								$numberall = mysql_num_rows($res);
								if ($numberall > 0)
									{
										$error_save = "true";
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_ADD_DOUBLE_LOGIN}", $page);
									}
								$query = "SELECT `name` FROM `users` WHERE `name`='".$_POST['user_name']."' AND `id`<>'".$user_edit."';";
								$res = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								$numberall = mysql_num_rows($res);
								if ($numberall > 0)
									{
										$error_save = "true";
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_EDIT_DOUBLE_NAME}", $page);
									}
								if ($error_save <> "true")
									{
										$user_login = $_POST['user_login'];
										$user_name = $_POST['user_name'];
										$user_ip = $_POST['user_ip'];
										$user_lang = $_POST['user_lang'];
										$user_template = $_POST['user_template'];
										$user_mail1 = $_POST['user_mail1'];
										$user_mail2 = $_POST['user_mail2'];
										$user_tel1 = $_POST['user_tel1'];
										$user_tel2 = $_POST['user_tel2'];
										$user_tel3 = $_POST['user_tel3'];
										$user_str = $_POST['user_str'];
										
										if ($user_str)
											{
												foreach ($user_str as $u_s)
													{
														$user_str_bd .= $u_s.",";
													}
											}
										
										$user_privat = $_POST['user_privat'];
										
										if ($user_privat)
											{
												foreach ($user_privat as $u_p)
													{
														$user_privat_bd .= $u_p.",";
													}
											}
											
										if ($_POST['user_ip_c'] == 1) {$user_ip_c = 1;} else {$user_ip_c = 0;}
										if ($_POST['user_a_ip'] == 1) {$user_a_ip = 1;} else {$user_a_ip = 0;}
										if ($_POST['user_p_user'] == 1) {$user_p_user = 1;} else {$user_p_user = 0;}
										if ($_POST['user_p_config'] == 1) {$user_p_config = 1;} else {$user_p_config = 0;}
										if ($_POST['user_p_log'] == 1) {$user_p_log = 1;} else {$user_p_log = 0;}
										if ($_POST['user_p_users'] == 1) {$user_p_users = 1;} else {$user_p_users = 0;}
										if ($_POST['user_p_addr'] == 1) {$user_p_addr = 1;} else {$user_p_addr = 0;}
										if ($_POST['user_p_ip'] == 1) {$user_p_ip = 1;} else {$user_p_ip = 0;}
										if ($_POST['user_p_mod'] == 1) {$user_p_mod = 1;} else {$user_p_mod = 0;}
										if ($_POST['user_work'] == 1) {$user_work = 1;} else {$user_work = 0;}
										if ($pass_change == "true")
											{
												$user_password = md5($_POST['user_password1']);
												$query = "UPDATE `users` SET
												`login`='".$user_login."',
												`pass`='".$user_password."',
												`name`='".$user_name."',
												`ip`='".$user_ip."',
												`ip_c`='".$user_ip_c."',
												`a_ip`='".$user_a_ip."', 
												`work`='".$user_work."',
												`lang`='".$user_lang."', 
												`p_user`='".$user_p_user."', 
												`p_config`='".$user_p_config."', 
												`p_log`='".$user_p_log."', 
												`p_users`='".$user_p_users."', 
												`p_addr`='".$user_p_addr."', 
												`p_ip`='".$user_p_ip."', 
												`p_mod`='".$user_p_mod."', 
												`mail1`='".$user_mail1."', 
												`mail2`='".$user_mail2."', 
												`tel1`='".$user_tel1."', 
												`tel2`='".$user_tel2."', 
												`tel3`='".$user_tel3."', 
												`structura`='".$user_str_bd."', 
												`privat`='".$user_privat_bd."' 
												WHERE `id`='".$user_edit."' LIMIT 1;";
											}
											else
											{
												$query = "UPDATE `users` SET
												`login`='".$user_login."',
												`name`='".$user_name."',
												`ip`='".$user_ip."',
												`ip_c`='".$user_ip_c."',
												`a_ip`='".$user_a_ip."', 
												`work`='".$user_work."',
												`lang`='".$user_lang."', 
												`p_user`='".$user_p_user."', 
												`p_config`='".$user_p_config."', 
												`p_log`='".$user_p_log."', 
												`p_users`='".$user_p_users."', 
												`p_addr`='".$user_p_addr."', 
												`p_ip`='".$user_p_ip."', 
												`p_mod`='".$user_p_mod."', 
												`mail1`='".$user_mail1."', 
												`mail2`='".$user_mail2."', 
												`tel1`='".$user_tel1."', 
												`tel2`='".$user_tel2."', 
												`tel3`='".$user_tel3."', 
												`structura`='".$user_str_bd."', 
												`privat`='".$user_privat_bd."' 
												WHERE `id`='".$user_edit."' LIMIT 1;";
											}
										$res = mysql_query($query) or $error_save = "true";
										$queryes_num++;
										if ($error_save == "true")
											{
												$page.= file_get_contents("templates/information_danger.html");
												$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_EDIT_BD_ERROR}", $page);
											}
											else
											{
												$loging_do = "{LANG_LOG_USERS_EDIT} ".$user_login;
												include ('inc/loging.php');
												$page.= file_get_contents("templates/information_success.html");
												$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_EDIT_OK}", $page);
											}
									}
							}
					}
					else
					{
						$query = "SELECT * FROM `users` WHERE `id`='".$user_edit."' AND `del`='0' LIMIT 1;";
						$res = mysql_query($query) or die(mysql_error());
						$queryes_num++;
						$numberall = mysql_num_rows($res);
						if ($numberall == 0)
							{
								$error_save = "true";
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_EDIT_NO_ID}", $page);
							}
							else
							{
								while ($row=mysql_fetch_array($res))
									{
										$template2.= file_get_contents("templates/users_edit.html");
										$query2 = "SELECT `id`,`index`,`name` FROM `structura` WHERE `work`='1' ORDER BY `index`;";
										$res2 = mysql_query($query2) or die(mysql_error());
										$queryes_num++;
										$numberall2 = mysql_num_rows($res2);
										if ($numberall2 > 0)
											{
												while ($row2=mysql_fetch_array($res2))
													{
														$list_str_size++;
														
														$usr_str_array = explode(",",$row['structura']);
														
														if (in_array($row2['id'], $usr_str_array))
															{
																$temp_str .= "<OPTION value = \"".$row2['id']."\" selected >(".$row2['index'].") ".$row2['name']."</OPTION>";
															}
															else
															{
																$temp_str .= "<OPTION value = \"".$row2['id']."\" >(".$row2['index'].") ".$row2['name']."</OPTION>";
															}
													}
											}
											else
											{
												$template2 = str_replace("{LIST_STRUCTURA_SIZE}", 1, $template2);
												$temp_str .= "<OPTION value = \"\">{LANG_NDI_STR_EMPTY}</OPTION>";
												$template2 = str_replace("{LIST_STRUCTURA}", $temp_str, $template2);
											}
										if ($list_str_size < 3) $list_str_size = 3;
										$template2 = str_replace("{LIST_STRUCTURA_SIZE}", $list_str_size, $template2);
										$template2 = str_replace("{LIST_STRUCTURA}", $temp_str, $template2);
										$template2 = str_replace("{USER_ID}", $row['id'], $template2);
										$template2 = str_replace("{LOGIN}", $row['login'], $template2);
										$template2 = str_replace("{NAME}", $row['name'], $template2);
										$template2 = str_replace("{IP}", $row['ip'], $template2);
										$template2 = str_replace("{MAIL1}", $row['mail1'], $template2);
										$template2 = str_replace("{MAIL2}", $row['mail2'], $template2);
										$template2 = str_replace("{TEL1}", $row['tel1'], $template2);
										$template2 = str_replace("{TEL2}", $row['tel2'], $template2);
										$template2 = str_replace("{TEL3}", $row['tel3'], $template2);
										$template2 = str_replace("{U_LANG}", $row['lang'], $template2);
										$template2 = str_replace("{U_STRUCTURA}", $row['structura'], $template2);
										if ($row['ip_c'] == 1) {$template2 = str_replace("{ADM_IP_C}", " checked", $template2);} else {$template2 = str_replace("{ADM_IP_C}", "", $template2);}
										if ($row['a_ip'] == 1) {$template2 = str_replace("{ADM_A_IP}", " checked", $template2);} else {$template2 = str_replace("{ADM_A_IP}", "", $template2);}
										if ($row['p_user'] == 1) {$template2 = str_replace("{ADM_P_USER}", " checked", $template2);} else {$template2 = str_replace("{ADM_P_USER}", "", $template2);}
										if ($row['p_config'] == 1) {$template2 = str_replace("{ADM_P_CONFIG}", " checked", $template2);} else {$template2 = str_replace("{ADM_P_CONFIG}", "", $template2);}
										if ($row['p_log'] == 1) {$template2 = str_replace("{ADM_P_LOG}", " checked", $template2);} else {$template2 = str_replace("{ADM_P_LOG}", "", $template2);}
										if ($row['p_users'] == 1) {$template2 = str_replace("{ADM_P_USERS}", " checked", $template2);} else {$template2 = str_replace("{ADM_P_USERS}", "", $template2);}
										if ($row['p_addr'] == 1) {$template2 = str_replace("{ADM_P_ADDR}", " checked", $template2);} else {$template2 = str_replace("{ADM_P_ADDR}", "", $template2);}
										if ($row['p_ip'] == 1) {$template2 = str_replace("{ADM_P_IP}", " checked", $template2);} else {$template2 = str_replace("{ADM_P_IP}", "", $template2);}
										if ($row['p_mod'] == 1) {$template2 = str_replace("{ADM_P_MOD}", " checked", $template2);} else {$template2 = str_replace("{ADM_P_MOD}", "", $template2);}
										if ($row['work'] == 1) {$template2 = str_replace("{WORK}", " checked", $template2);} else {$template2 = str_replace("{WORK}", "", $template2);}
										$usr_privat_array = explode(",",$row['privat']);
										if (in_array(1, $usr_privat_array)) {$template2 = str_replace("{PRIVAT_1}", "selected", $template2);} else {$template2 = str_replace("{PRIVAT_1}", "", $template2);}
										if (in_array(2, $usr_privat_array)) {$template2 = str_replace("{PRIVAT_2}", "selected", $template2);} else {$template2 = str_replace("{PRIVAT_2}", "", $template2);}
										if (in_array(3, $usr_privat_array)) {$template2 = str_replace("{PRIVAT_3}", "selected", $template2);} else {$template2 = str_replace("{PRIVAT_3}", "", $template2);}
										if (in_array(4, $usr_privat_array)) {$template2 = str_replace("{PRIVAT_4}", "selected", $template2);} else {$template2 = str_replace("{PRIVAT_4}", "", $template2);}
										if (in_array(5, $usr_privat_array)) {$template2 = str_replace("{PRIVAT_5}", "selected", $template2);} else {$template2 = str_replace("{PRIVAT_5}", "", $template2);}
										if (in_array(6, $usr_privat_array)) {$template2 = str_replace("{PRIVAT_6}", "selected", $template2);} else {$template2 = str_replace("{PRIVAT_6}", "", $template2);}
										$page.= $template2;
									}
							}
					}
			}
		if (isset($_GET['user_turn']))
			{
				if ($_GET['user_turn'] == "on") $turn=1;
				if ($_GET['user_turn'] == "off") $turn=0;
				$user_id = $_GET['user_id'];
				if ($turn<>1 AND $turn<>0 OR $user_id < 0)
					{
						DIE ("ERROR #1 USERS.PHP (USER_TURN)");
					}
				else
					{
						if ($turn == 1) $loging_do = "{LANG_LOG_USERS_EDIT_TURN_ON} ".$user_id;
						if ($turn == 0) $loging_do = "{LANG_LOG_USERS_EDIT_TURN_OFF} ".$user_id;
						include ('inc/loging.php');
						mysql_query("UPDATE `users` SET `work`='".$turn."' WHERE `id`='".$user_id."' LIMIT 1;") or die(mysql_error());
						$queryes_num++;
					}
			}
		if (isset($_GET['user_del']))
			{
				if ($_GET['user_del'] < 0 OR $_GET['user_del'] > 99999999)
					{
						DIE ("ERROR #2 USERS.PHP (user_del)");
					}
					else
					{
						$user_id = $_GET['user_del'];
						$loging_do = "{LANG_LOG_USERS_DEL} ".$user_id;
						include ('inc/loging.php');
						//mysql_query("DELETE FROM `users` WHERE `id` = '".$user_id."' LIMIT 1;") or die(mysql_error());
						$query = "UPDATE `users` SET `del`='1' WHERE `id`='".$user_id."' LIMIT 1;";
						@mysql_query($query);
						$queryes_num++;
						$page.= file_get_contents("templates/information_success.html");
						$page = str_replace("{INFORMATION}", "{LANG_USERS_ADMIN_DELETED}", $page);
					}
			}
		if ($adress <> "true")
			{
				$page.= file_get_contents("templates/users.html");
				$query = "SELECT * FROM `users` WHERE `del`='0' ORDER BY `name`;";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				$numberall = mysql_num_rows($res);
				
				$query2 = "SELECT `id`,`index`,`name` FROM `structura` WHERE `work`='1' ORDER BY `index`;";
				$res2 = mysql_query($query2) or die(mysql_error());
				$queryes_num++;
				$ndi_str_id = array();
				$ndi_str_index = array();
				$ndi_str_name = array();
				while ($row2=mysql_fetch_array($res2))
					{
						$ndi_str_id[] = $row2['id'];
						$ndi_str_index[] = $row2['index'];
						$ndi_str_name_tmp = implode(array_slice(explode('<br>',wordwrap($row2['name'],31,'<br>',false)),0,1));
						if($ndi_str_name_tmp!=$row2['name']) $ndi_str_name_tmp .= "...";
						$ndi_str_name[] = $ndi_str_name_tmp;
						$ndi_str_name_tmp = "";
					}

				while ($row=mysql_fetch_array($res))
					{
						if (time() >= $row['time'] + 2592000 ) {$user_color = " bgcolor=\"#FFAAAA\"";} else {$user_color = " bgcolor=\"#AAFFAA\"";}
						$color = $color + 1;
						if ($color == 1) {$bgcolor ="";}
						if ($color == 2) {$bgcolor ="bgcolor=\"#D3EDF6\""; $color = 0;}
						
						$temp_str = "";
						$usr_str_array = explode(",",$row['structura']);
						
						foreach ($usr_str_array as $u_s)
							{
								for ($i = 0; $i <= count($ndi_str_id); $i++)
									{
										if ($ndi_str_id[$i] == $u_s AND $u_s <> "") $temp_str .= "(".$ndi_str_index[$i].") ".$ndi_str_name[$i]."<br />";
									}
							}
							
						$template_users.="<tr valign=\"middle\" align=\"center\">
						<td ".$bgcolor." align=\"center\">".$row['id']."</td>
						<td ".$bgcolor." align=\"left\">".$row['name']."</td>
						<td ".$bgcolor." align=\"left\">".$row['login']."</td>
						<td ".$bgcolor." align=\"left\">".$temp_str."</td>
						<td align=\"left\"".$user_color.">".date('Y.m.d H:i:s' ,$row['time'])."</td>
						<td ".$bgcolor." align=\"left\">".$row['ip']."</td>
						<td ".$bgcolor." align=\"left\">";
						if ($row['p_user'] == 1) $template_users.="{LANG_USERS_ADMIN_P_USER}";
						if ($row['p_config'] == 1) $template_users.="{LANG_USERS_ADMIN_P_CONFIG}";
						if ($row['p_log'] == 1) $template_users.="{LANG_USERS_ADMIN_P_LOG}";
						if ($row['p_users'] == 1) $template_users.="{LANG_USERS_ADMIN_P_USERS}";
						if ($row['p_ip'] == 1) $template_users.="{LANG_USERS_ADMIN_P_IP}";
						if ($row['p_mod'] == 1) $template_users.="{LANG_USERS_ADMIN_P_MOD}";
						$template_users.="&nbsp;</td>
						<td ".$bgcolor." align=\"center\">
							<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
								<tr valign=\"top\" align=\"center\">";
									if ($row['work'] == "1") $template_users.="<td valign=\"middle\" width=\"20%\"><a href=\"users.php?user_turn=off&user_id=".$row['id']."\"><img src=\"templates/images/lightbulb.png\" border=\"0\" alt=\"{LANG_USERS_ADMIN_OFF}\" title=\"{LANG_USERS_ADMIN_OFF}\"></a></td>";
									if ($row['work'] == "0") $template_users.="<td valign=\"middle\" width=\"20%\"><a href=\"users.php?user_turn=on&user_id=".$row['id']."\"><img src=\"templates/images/lightbulb_off.png\" border=\"0\" alt=\"{LANG_USERS_ADMIN_ON}\" title=\"{LANG_USERS_ADMIN_ON}\"></a></td>";
									$template_users.="<td valign=\"middle\" width=\"20%\"><a href=\"users.php?user_edit=".$row['id']."\"><img src=\"templates/images/hammer_screwdriver.png\" border=\"0\" alt=\"{LANG_USERS_ADMIN_EDIT}\" title=\"{LANG_USERS_ADMIN_EDIT}\"></a></td>
									<td valign=\"middle\" width=\"20%\"><a href=\"users.php?user_del=".$row['id']."\" onClick=\"if(confirm('{LANG_USERS_ADMIN_DEL_CONFIRM} ".$row['name']." ?')) {return true;} return false;\"><img src=\"templates/images/cross_octagon.png\" border=\"0\" alt=\"{LANG_USERS_ADMIN_DEL}\" title=\"{LANG_USERS_ADMIN_DEL}\"></a></td>
								</tr>
							</table>
						</td>
					</tr>";
					}
				$page = str_replace("{USERS_VIEWS}", $template_users, $page);
				$page = str_replace("{USER_STATS}", "<b>".$numberall."</b> {LANG_USERS_ADMIN_TOTTAL}", $page);
			}
	}
	else
	{
		$loging_do = "{LANG_LOG_USERS_403}";
		include ('inc/loging.php');
		header('HTTP/1.1 403 Forbidden');
		$page.= file_get_contents("templates/information_danger.html");
		$page = str_replace("{INFORMATION}", "{LANG_403}", $page);
		$timeout = "index.php";
	}
include ("inc/blender.php");
?>