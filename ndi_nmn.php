<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if ($user_p_mod == 1)
	{
		if (isset($_GET['add']))
			{
				$ndi_do = "true";
				if (isset($_GET['save']))
					{
						if ($_POST['index'] <> "" AND $_POST['name'] <> "" AND $_POST['ndi_str'] <> "")
							{
								$index = $_POST['index'];
								$name = $_POST['name'];
								$structura = $_POST['ndi_str'];
								$query = "SELECT `id` FROM `nomenclatura` WHERE `index`='".$index."' AND `structura`='".$structura."' AND `work`='1' ;";
								$res = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								$numberall = mysql_num_rows($res);
								if ($numberall == 0)
									{
										$query = "INSERT INTO `nomenclatura` (`id`, `structura`, `index`, `name`, `user`, `time`, `do`, `work`) VALUES (NULL , '".$structura."', '".$index."', '".$name."', '".$_SESSION['user_id']."', '".time()."', '{LANG_NDI_STR_ADMIN_ADD}', '1');";
										$res = mysql_query($query) or die(mysql_error());
										$queryes_num++;
										$page.= file_get_contents("templates/information_success.html");
										$page = str_replace("{INFORMATION}", "{LANG_NMN_ADD_OK}", $page);
										$ndi_do = "false";
									}
									else
									{
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_NMN_ADD_EXIST}", $page);
									}
							}
							else
							{
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_STR_ADD_NAME_OR_INDEX_EMPTY}", $page);
							}
					}
					else
					{
						$page.= file_get_contents("templates/ndi_nmn_add.html");
						$query = "SELECT `id`,`index`,`name` FROM `structura` WHERE `work`='1' ORDER BY `index`;";
						$res = mysql_query($query) or die(mysql_error());
						$queryes_num++;
						$numberall = mysql_num_rows($res);
						if ($numberall > 0)
							{
								while ($row=mysql_fetch_array($res))
									{
										$temp_str .= "<OPTION value = \"".$row['id']."\">".$row['index']." - ".$row['name']."</OPTION>";
									}
							}
							else
							{
								$temp_str .= "<OPTION value = \"\">{LANG_NDI_STR_EMPTY}</OPTION>";
							}
						$page = str_replace("{LIST_STRUCTURA}", $temp_str, $page);
					}
			}

		if (isset($_GET['edit']))
			{
				$ndi_do = "true";
				if (isset($_GET['save']))
					{
						if ($_POST['index'] <> "" AND $_POST['name'] <> "" AND $_POST['ndi_str'] <> "")
							{
								$index = $_POST['index'];
								$name = $_POST['name'];
								$structura = $_POST['ndi_str'];
								$id = $_GET['edit'];
								$query = "SELECT * FROM `nomenclatura` WHERE `id`='".$id."' AND `work`='1' ;";
								$res = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								$numberall = mysql_num_rows($res);
								if ($numberall == 1)
									{
										$query2 = "SELECT * FROM `nomenclatura` WHERE `id`<>'".$id."' AND `index`='".$index."' AND `structura`='".$structura."' AND `work`='1' ;";
										$res2 = mysql_query($query2) or die(mysql_error());
										$queryes_num++;
										$numberall2 = mysql_num_rows($res2);
										if ($numberall2 == 0)
											{
												$query = "UPDATE `nomenclatura` SET `structura`='".$structura."', `index`='".$index."', `name`='".$name."', `user`='".$_SESSION['user_id']."', `time`='".time()."', `do`='{LANG_NDI_STR_ADMIN_EDIT}' WHERE `id`='".$id."' LIMIT 1;";
												$res = mysql_query($query) or die(mysql_error());
												$queryes_num++;
												$page.= file_get_contents("templates/information_success.html");
												$page = str_replace("{INFORMATION}", "{LANG_NMN_EDIT_OK}", $page);
												$ndi_do = "false";
											}
											else
											{
												$page.= file_get_contents("templates/information_danger.html");
												$page = str_replace("{INFORMATION}", "{LANG_NMN_ADD_EXIST}", $page);
											}
									}
									else
									{
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_NMN_EDIT_NOT_EXIST}", $page);
									}
							}
							else
							{
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_STR_ADD_NAME_OR_INDEX_EMPTY}", $page);
							}
					}
					else
					{
						$edit = $_GET['edit'];
						$query = "SELECT * FROM `nomenclatura` WHERE `id`='".$edit."' AND `work`='1' LIMIT 1;";
						$res = mysql_query($query) or die(mysql_error());
						$numberall = mysql_num_rows($res);
						if ($numberall <> 0)
							{
								$page.= file_get_contents("templates/ndi_nmn_edit.html");
								while ($row=mysql_fetch_array($res))
									{
										$page = str_replace("{ID}", $row['id'], $page);
										$page = str_replace("{INDEX}", $row['index'], $page);
										$page = str_replace("{NAME}", $row['name'], $page);
										
										$query2 = "SELECT `id`,`index`,`name` FROM `structura` WHERE `work`='1' ORDER BY `index`;";
										$res2 = mysql_query($query2) or die(mysql_error());
										$queryes_num++;
										$numberall2 = mysql_num_rows($res2);
										if ($numberall2 > 0)
											{
												while ($row2=mysql_fetch_array($res2))
													{
														if ($row['structura'] == $row2['id'])
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
												$temp_str .= "<OPTION value = \"\">{LANG_NMN_EMPTY}</OPTION>";
											}
										$page = str_replace("{LIST_STRUCTURA}", $temp_str, $page);
									}
							}
							else
							{
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_STR_DEL_NOT_EXIST}", $page);
							}
					}
			}

		if (isset($_GET['del']))
			{
				$del = $_GET['del'];
				$query = "SELECT `id` FROM `nomenclatura` WHERE `id`='".$del."';";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				$numberall = mysql_num_rows($res);
				if ($numberall == 1)
					{
						//$query = "DELETE FROM `nomenclatura` WHERE `id` = '".$del."' LIMIT 1;";
						$query = "UPDATE `nomenclatura` SET `work`='0', `user`='".$_SESSION['user_id']."', `time`='".time()."', `do`='{LANG_NDI_NMN_ADMIN_DELETE}' WHERE `id`='".$del."' LIMIT 1;";
						$res = mysql_query($query) or die(mysql_error());
						$queryes_num++;
						$page.= file_get_contents("templates/information_success.html");
						$page = str_replace("{INFORMATION}", "{LANG_NMN_DEL_OK}", $page);
						$loging_do = "{LANG_LOG_NMN_DEL} ".$del;
						include ('inc/loging.php');
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_NMN_DEL_NOT_EXIST}", $page);
					}
			}

		if ($ndi_do <> "true")
			{
				$query = "SELECT * FROM `nomenclatura` WHERE `work`='1' ORDER BY `structura`,`index` ; ";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				$numberall = mysql_num_rows($res);
				if ($numberall > 0)
					{
						$page.= file_get_contents("templates/ndi_nmn.html");
						while ($row=mysql_fetch_array($res))
							{
								if ($old_admin_id <> $row['user'])
									{
										$query2 = "SELECT `name` FROM `users` WHERE `id`='".$row['user']."' LIMIT 1 ;";
										$res2 = mysql_query($query2) or die(mysql_error());
										$queryes_num++;
										if (mysql_num_rows($res2) > 0)
											{
												while ($row2=mysql_fetch_array($res2))
													{
														$user = $row2['name'];
														$old_admin_id = $row['user'];
														$old_admin_name = $user;
													}
											}
											else
											{
												$user = "{LANG_USERS_ADMIN_DELETED}";
											}
									}
									else
									{
										$user = $old_admin_name;
									}

								if ($old_structura_id <> $row['structura'])
									{
										$query3 = "SELECT `index` FROM `structura` WHERE `id`='".$row['structura']."' AND `work`='1' LIMIT 1 ;";
										$res3 = mysql_query($query3) or die(mysql_error());
										$queryes_num++;
										if (mysql_num_rows($res3) > 0)
											{
												while ($row3=mysql_fetch_array($res3))
													{
														$structura = $row3['index'];
														$old_structura_id = $row['structura'];
														$old_structura_index = $structura;
													}
											}
											else
											{
												$structura = "{LANG_SRT_DELETED}";
											}
									}
									else
									{
										$structura = $old_structura_index;
									}

								$color = $color + 1;
								if ($color == 1) {$bgcolor ="";}
								if ($color == 2) {$bgcolor =" bgcolor=\"#D3EDF6\""; $color = 0;}
								$page_tmp .= "<tr valign=\"middle\" align=\"center\">
									<td".$bgcolor." height=\"20\">".$structura."-".$row['index']."</td>
									<td".$bgcolor." align=\"left\">".$row['name']."</td>
									<td".$bgcolor.">".$user."</td>
									<td".$bgcolor.">".date('d.m.Y H:i:s', $row['time'])."</td>
									<td".$bgcolor.">".$row['do']."</td>
									<td".$bgcolor." valign=\"bottom\">
										&nbsp;<a href=\"?edit=".$row['id']."\"><img src=\"templates/images/hammer_screwdriver.png\" border=\"0\" alt=\"{LANG_USERS_ADMIN_EDIT}\" title=\"{LANG_USERS_ADMIN_EDIT}\"></a>
										&nbsp;<a href=\"?del=".$row['id']."\" onClick=\"if(confirm('{LANG_STR_ADMIN_DEL_CONFIRM} ".$row['index'].", ".$row['name']." ?')) {return true;} return false;\"><img src=\"templates/images/cross_octagon.png\" border=\"0\" alt=\"{LANG_USERS_ADMIN_DEL}\" title=\"{LANG_USERS_ADMIN_DEL}\"></a>
									</td>
								</tr>
								";
							}
						$page = str_replace("{NMN_VIEWS}", $page_tmp, $page);
						$page = str_replace("{NMN_STATS}", "{LANG_NMN_STATS}" . $numberall, $page);
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_NMN_EMPTY}<br /><a href=\"?add\">{LANG_NMN_ADMIN_ADD}</a>", $page);
					}
			}
	}
	else
	{
		$loging_do = "{LANG_LOG_NDI_403}";
		include ('inc/loging.php');
		header('HTTP/1.1 403 Forbidden');
		$page.= file_get_contents("templates/information_danger.html");
		$page = str_replace("{INFORMATION}", "{LANG_403}", $page);
		$timeout = "index.php";
	}
include ("inc/blender.php");
?>