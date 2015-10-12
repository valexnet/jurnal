<?php
session_start();

if (isset($_POST['show_year']) AND $_POST['show_year'] > 0 AND $_POST['show_year'] < 9999 AND $_POST['show_year'][3] <> "") $_SESSION['user_year'] = $_POST['show_year'];
if (isset($_POST['show_num_list']) AND $_POST['show_num_list'] > 0 AND $_POST['show_num_list'] < 9999) $_SESSION['user_page_limit'] = $_POST['show_num_list'];

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if (isset($_SESSION['user_id']))
	{
		$page .= file_get_contents("templates/jurnal_in_header.html");

		$query = "SHOW TABLES LIKE \"DB_".date('Y')."_IN\";";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		if (mysql_num_rows($res) == 0)
			{
				if (@opendir("uploads/".date('Y')."/IN"))
					{
						@closedir("uploads/".date('Y')."/IN");
					}
					else
					{
						mkdir("uploads/".date('Y')."/IN", null, true) or die("Can't create new folder - uploads/".date('Y')."/IN");
					}
				$query = file_get_contents("inc/db_in.txt");
				$query = str_replace("{YEAR}", date('Y'), $query);
				mysql_query($query) or die(mysql_error());
			}

		if (isset($_GET['do']) && $_GET['do'] == 'add')
			{
				$adres = 'true';
				if ($privat4 == 1)
					{
						if (isset($_POST['get_data']) and !empty($_POST['get_data']))
							{
								// Убераєм з даних лишне
								$_POST['org_name'] = str_replace($srch, $rpls, $_POST['org_name']);
								$_POST['org_index'] = str_replace($srch, $rpls, $_POST['org_index']);
								$_POST['org_subj'] = str_replace($srch, $rpls, $_POST['org_subj']);
								$_POST['make_visa'] = str_replace($srch, $rpls, $_POST['make_visa']);
								// Перевірка даних
								$error = "";
								if ($_POST['form_id'] == $_SESSION['form_id']) $error .= '{LANG_JURNAL_OUT_FORM_ERROR_FORM_ID}<br />';
								if (!check_data(data_trans("ua", "mysql", $_POST['get_data']))) $error .= "{LANG_FORM_NO_GET_DATA}<br>";
								if (!check_data(data_trans("ua", "mysql", $_POST['org_data']))) $error .= "{LANG_FORM_NO_ORG_DATA}<br>";
								if ($_POST['make_data'] != "")
									{
										if (!check_data(data_trans("ua", "mysql", $_POST['make_data'])))
											{
												$error .= "{LANG_FORM_NO_MAKE_DATA}<br>";
											}
											else
											{
												$mysql_make_data = "'".data_trans("ua", "mysql", $_POST['make_data'])."'";
											}
									}
									else
									{
										$mysql_make_data = "NULL";
									}
								if (!preg_match("/^([1-9]|[1-9][0-9]{1,})$/" ,$_POST['do_user'])) $error .= "{LANG_FORM_NO_DO_USER}<br>";
								if ($_POST['org_name'] == "") $error .= "{LANG_FORM_NO_ORG_NAME}<br>";
								if ($_POST['org_index'] == "") $error .= "{LANG_FORM_NO_ORG_INDEX}<br>";
								if ($_POST['org_subj'] == "") $error .= "{LANG_FORM_NO_ORG_SUBJ}<br>";
								if ($_POST['make_visa'] == "") $error .= "{LANG_FORM_NO_MAKE_VISA}<br>";

								if ($error == "")
									{
										$query = "INSERT INTO `db_".date('Y')."_in` (
										`id`,
										`add_user`,
										`add_time`,
										`add_ip`,
										`do_user`,
										`get_data`,
										`org_name`,
										`org_index`,
										`org_data`,
										`org_subj`,
										`make_visa`,
										`make_data`
										) VALUES (
										NULL ,
										'".$_SESSION['user_id']."',
										'".date('Y-m-d H:i:s')."',
										'".$_SERVER['REMOTE_ADDR']."',
										'".$_POST['do_user']."',
										'".data_trans("ua", "mysql", $_POST['get_data'])."',
										'".$_POST['org_name']."',
										'".$_POST['org_index']."',
										'".data_trans("ua", "mysql", $_POST['org_data'])."',
										'".$_POST['org_subj']."',
										'".$_POST['make_visa']."',
										".$mysql_make_data."
										) ;";
										mysql_query($query) or die(mysql_error());
										$queryes_num++;
										$_SESSION['error_in_add'] = '';
										$_SESSION['error_in_add_get_data'] = '';
										$_SESSION['error_in_add_org_name'] = '';
										$_SESSION['error_in_add_org_index'] = '';
										$_SESSION['error_in_add_org_data'] = '';
										$_SESSION['error_in_add_org_subj'] = '';
										$_SESSION['error_in_add_make_visa'] = '';
										$_SESSION['error_in_add_make_data'] = '';
										$_SESSION['error_in_add_do_user'] = '';
										$_SESSION['form_id'] = $_POST['form_id'];

										$query = "SELECT `id` FROM `db_".date('Y')."_in` ORDER BY `id` DESC LIMIT 1 ;";
										$res = mysql_query($query) or die(mysql_error());
										$queryes_num++;
										while ($row=mysql_fetch_array($res))
											{
												$page.= file_get_contents("templates/information_success.html");
												$page = str_replace("{INFORMATION}", "{LANG_YOUR_IN_N}: <kbd>".$row['id']."</kbd>", $page);
											}
									}
									else
									{
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", $error, $page);

										$_SESSION['error_in_add'] = 1;
										$_SESSION['error_in_add_get_data'] = $_POST['get_data'];
										$_SESSION['error_in_add_org_name'] = $_POST['org_name'];
										$_SESSION['error_in_add_org_index'] = $_POST['org_index'];
										$_SESSION['error_in_add_org_data'] = $_POST['org_data'];
										$_SESSION['error_in_add_org_subj'] = $_POST['org_subj'];
										$_SESSION['error_in_add_make_visa'] = $_POST['make_visa'];
										$_SESSION['error_in_add_make_data'] = $_POST['make_data'];
										$_SESSION['error_in_add_do_user'] = $_POST['do_user'];
									}
							}
							else
							{
								$page.= file_get_contents("templates/jurnal_in_add.html");
								$select_user = 0;
								if (isset($_SESSION['error_in_add']) AND $_SESSION['error_in_add'] == 1)
									{
										$select_user = $_SESSION['error_in_add_do_user'];
										$page = str_replace("{FORM_GET_DATA}", $_SESSION['error_in_add_get_data'], $page);
										$page = str_replace("{FORM_ORG_NAME}", $_SESSION['error_in_add_org_name'], $page);
										$page = str_replace("{FORM_ORG_INDEX}", $_SESSION['error_in_add_org_index'], $page);
										$page = str_replace("{FORM_ORG_DATA}", $_SESSION['error_in_add_org_data'], $page);
										$page = str_replace("{FORM_ORG_SUBJ}", $_SESSION['error_in_add_org_subj'], $page);
										$page = str_replace("{FORM_MAKE_VISA}", $_SESSION['error_in_add_make_visa'], $page);
										$page = str_replace("{FORM_MAKE_DATA}", $_SESSION['error_in_add_make_data'], $page);
									}
								$page = str_replace("{FORM_GET_DATA}", "", $page);
								$page = str_replace("{FORM_ORG_NAME}", "", $page);
								$page = str_replace("{FORM_ORG_INDEX}", "", $page);
								$page = str_replace("{FORM_ORG_DATA}", "", $page);
								$page = str_replace("{FORM_ORG_SUBJ}", "", $page);
								$page = str_replace("{FORM_MAKE_VISA}", "", $page);
								$page = str_replace("{FORM_MAKE_DATA}", "", $page);
								$html_select_users = get_users_selection_options($select_user, 0, "name", "ASC", 0);
								$queryes_num++;
								$page = str_replace("{FORM_DO_USER}", $html_select_users, $page);
							}
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_PRIVAT4_NO}", $page);
					}
			}

		if (isset($_GET['do']) && $_GET['do'] == 'src')
			{
				$adres = 'true';
				$page.= file_get_contents("templates/information.html");
				$page = str_replace("{INFORMATION}", "У РОЗРОБЦІ", $page);
			}
			
		if (isset($_GET['attach']) && preg_match('/^[1-9][0-9]*$/', $_GET['attach']))
			{
				$adres = 'true';
				$query = "SELECT * FROM `db_".$_SESSION['user_year']."_in` WHERE `id`='".$_GET['attach']."' LIMIT 1 ; ";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				if (mysql_num_rows($res) == 1)
					{
						$manage_files = 0;
						$view_files = 0;
						$row = mysql_fetch_assoc($res);
						$page.= file_get_contents("templates/information.html");
						$page = str_replace("{INFORMATION}", "{LANG_FILE_ABOUT_NUM} <strong>".$row['id']."</strong>", $page);
						if ($row['add_user'] == $_SESSION['user_id'] OR $user_p_mod == 1)
							{
								$manage_files = 1;
								$view_files = 1;
							}
						if ($privat1 == 1 OR $row['do_user'] == $_SESSION['user_id'])
							{
								$view_files = 1;
							}
						$tmp_add_new_file = 0;
						if ($manage_files == 1)
							{
								if (isset($_FILES) AND !empty($_FILES))
									{
										foreach ($_FILES as $FILE)
											{
												for ($i = 0; ; $i++)
													{
														if (empty($FILE['name'][$i])) break;
														$tmp_add_new_file = 1;
														$ext = end(explode(".", strtolower($FILE['name'][$i])));
														if (in_array($ext, $c_reg_file_array))
															{
																if ($max_file_size > $FILE['size'][$i])
																	{
																		if (is_uploaded_file($FILE['tmp_name'][$i]))
																			{
																				$file_new_name = $c_n_ray."_".$row['id']."_".$FILE['name'][$i];
																				if (preg_match("/^".$c_n_ray."_".$row['id']."_.*/i", $FILE['name'][$i])) $file_new_name = $FILE['name'][$i];
																				$file_name = "uploads\\".$_SESSION['user_year']."\\IN\\".$file_new_name;
																				$file_name = iconv('UTF-8', 'windows-1251', $file_name);
																				if (!file_exists($file_name))
																					{
																						if (@move_uploaded_file($FILE['tmp_name'][$i], $file_name))
																							{
																								$page.= file_get_contents("templates/information_success.html");
																								$page = str_replace("{INFORMATION}", "<font color=\"green\">{LANG_FILE_SAVE_OK} ".$file_new_name."</font>", $page);
																							}
																							else
																							{
																								$page.= file_get_contents("templates/information_danger.html");
																								$page = str_replace("{INFORMATION}", "<font color=\"green\">{LANG_FILE_SAVE_ERROR} ".$file_new_name."</font>", $page);
																							}
																					}
																					else
																					{
																						$page.= file_get_contents("templates/information_danger.html");
																						$page = str_replace("{INFORMATION}", $file_new_name." <font color=\"red\">{LANG_FILE_ALREADY_EXIST}</font>", $page);
																					}
																			}
																	}
																	else
																	{
																		$page.= file_get_contents("templates/information_danger.html");
																		$page = str_replace("{INFORMATION}", "<font color=\"red\">{LANG_FILE_SIZE_NOT_ALLOWED}</font> <b>".(($FILE['size'][$i] / 1024) / 1024 )." MB</b>", $page);
																	}
															}
															else
															{
																$page.= file_get_contents("templates/information_danger.html");
																$page = str_replace("{INFORMATION}", "<b>".$ext."</b> <font color=\"red\">{LANG_FILE_EXT_NOT_ALLOWED}</font>", $page);
															}
													}
											}
									}
								$page.= file_get_contents("templates/jurnal_out_add_file.html");
								$page = str_replace("{FILE_PRE_NAME}", "<b>".$c_n_ray."_".$row['id']."_</b>", $page);
							}
						if ($view_files == 1)
							{
								if ($dir = opendir("uploads\\".$_SESSION['user_year']."\\IN"))
									{
										while (false !== ($file = readdir($dir)))
											{
												if ($file != "." && $file != "..")
													{
														$file_utf8 = iconv('windows-1251', 'UTF-8', $file);
														if (preg_match("/^".$c_n_ray."_".$row['id']."_.*\.(?=".$c_reg_file.")/i", $file))
															{
																$tmp_do = 0;
																if (isset($_GET['delete']) AND $_GET['delete'] == $file_utf8 AND $manage_files == 1 AND $tmp_add_new_file == 0)
																	{
																		$tmp_do = 1;
																		if (@unlink("uploads\\".$_SESSION['user_year']."\\IN\\".$file))
																			{
																				$page.= file_get_contents("templates/information_success.html");
																				$page = str_replace("{INFORMATION}", $file_utf8." {LANG_REMOVE_FILE_OK}", $page);
																			}
																			else
																			{
																				$page.= file_get_contents("templates/information_danger.html");
																				$page = str_replace("{INFORMATION}", $file_utf8." {LANG_REMOVE_FILE_ERROR}", $page);
																			}
																	}

																if (isset($_GET['download']) AND $_GET['download'] == $file_utf8 AND $tmp_add_new_file == 0)
																	{
																		$tmp_do = 1;
																		if (ob_get_level()) ob_end_clean();
																		header('Content-Description: File Transfer');
																		header('Content-Type: application/octet-stream');
																		header('Content-Disposition: attachment; filename=' . $file_utf8);
																		header('Content-Transfer-Encoding: binary');
																		header('Expires: 0');
																		header('Cache-Control: must-revalidate');
																		header('Pragma: public');
																		header('Content-Length: ' . filesize("uploads\\".$_SESSION['user_year']."\\IN\\".$file));
																		if ($fd = fopen("uploads\\".$_SESSION['user_year']."\\IN\\".$file, 'rb'))
																			{
																				while (!feof($fd))
																					{
																						print fread($fd, 1024);
																					}
																				fclose($fd);
																			}
																		exit;
																		die();
																	}
																	
																if ($tmp_do == 0)
																	{
																		$page.= file_get_contents("templates/information.html");
																		$page = str_replace("{INFORMATION}", "{TMP_MANAGE_FILES}<a href=\"jurnal_in.php?attach=".$_GET['attach']."&download=".$file_utf8."\">".$file_utf8."</a> [ ".date ('d.m.Y H:i:s', @filemtime ("uploads\\".$_SESSION['user_year']."\\IN\\".$file))." ]", $page);
																		if ($manage_files == 1)
																			{
																				$page = str_replace("{TMP_MANAGE_FILES}", "<a href=\"jurnal_in.php?attach=".$_GET['attach']."&delete=".$file_utf8."\" onClick=\"if(confirm('{LANG_REMOVE_FILE_CONFIRM}')) {return true;} return false;\"><img src=\"templates/images/cross_octagon.png\"></a> ", $page);
																			}
																			else
																			{
																				$page = str_replace("{TMP_MANAGE_FILES}", "", $page);
																			}
																	}
															}
													}
											}
										closedir($dir);
									}
							}
							else
							{
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_FILES_NO}", $page);
							}	
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_ID_NOT_FOUND}", $page);
					}

			}

		if (isset($_GET['delete_last']) && $_GET['delete_last'] <> '')
			{
				$adres = 'true';
				$query = "SELECT * FROM `db_".date('Y')."_in` ORDER BY `id` DESC LIMIT 1 ; ";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				if (mysql_num_rows($res) > 0)
					{
						while ($row=@mysql_fetch_array($res))
							{
								if ($_GET['delete_last'] <> $row['id']) $ERROR .= "{LANG_JURNAL_OUT_DELETE_LAST_NOT_FIRST}<br />";
								if ($row['user'] <> $_SESSION['user_id'] AND $user_p_mod <> 1) $ERROR .= "{LANG_JURNAL_OUT_DELETE_LAST_NOT_AUTHOR}<br />";
								if ($_SESSION['user_year'] <> date('Y')) $ERROR .= "{LANG_JURNAL_OUT_DELETE_LAST_YEAR}<br />";

								if ($ERROR == "")
									{
										$query = "DELETE FROM `db_".date('Y')."_in` WHERE `id`='".$row['id']."' LIMIT 1 ; ";
										$res = mysql_query($query) or die(mysql_error());
										$queryes_num++;
										@mysql_query("ALTER TABLE `db_".date('Y')."_in` AUTO_INCREMENT =".$row['id']." ;") or die(mysql_error());
										$queryes_num++;

										$loging_do = "{LANG_LOG_JURNAL_IN_DELETE_LAST} ".$row['id'];
										include ('inc/loging.php');
										$page.= file_get_contents("templates/information_success.html");
										$page = str_replace("{INFORMATION}", "{LANG_JURNAL_IN_DELETE_LAST} ".$row['id'], $page);
										$timeout = "jurnal_in.php";
									}
									else
									{
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", $ERROR, $page);
									}
							}
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_EMPTY}", $page);
					}
			}

		if ($adres <> 'true')
			{
				$page = str_replace("{JURNAL_IN_TOP_STAT}", file_get_contents("templates/jurnal_in_top_stat.html"), $page);
				$page = str_replace("{JURNAL_IN_AFFIX}", "data-spy=\"affix\" data-offset-top=\"170\"", $page);

				if (isset($_GET['page_num']) AND preg_match('/^[1-9][0-9]*$/', $_GET['page_num']))
					{
						$active = $_GET['page_num'];
					}
					else
					{
						$active = 1;
					}

				$limit_pre = (($active - 1) * $_SESSION['user_page_limit']);
				$sql_limit = "LIMIT ".$limit_pre.", ".$_SESSION['user_page_limit'];

				if ($privat1 == 1)
					{
						$query_where = "";
					}
					else
					{
						$query_where = "WHERE `add_user`='".$_SESSION['user_id']."' OR `do_user`='".$_SESSION['user_id']."'";
					}

				$query_order_by = "ORDER BY `id` DESC ";
				$html_navy = get_navy("DB_".$_SESSION['user_year']."_IN", $query_where, $query_order_by, $active, $_SESSION['user_page_limit'], "jurnal_in.php?page_num=");
				$page = str_replace("{NAVY}", $html_navy, $page);
				$page .= file_get_contents("templates/jurnal_in.html");
				$query = "SELECT * FROM `db_".$_SESSION['user_year']."_in` ".$query_where." ".$query_order_by." ".$sql_limit." ;";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				$jurnal_in = "";
				$modals = "";
				$is_first = "";
				if (mysql_num_rows($res) > 0)
					{
						$users = get_users_names(0);
						$queryes_num++;
						while ($row=mysql_fetch_array($res))
							{
								$tmp = explode(" ", $row['get_data']);
								if ($tmp[1] == "00:00:00") $row['get_data'] = $tmp[0];
								$tmp = explode(" ", $row['org_data']);
								if ($tmp[1] == "00:00:00") $row['org_data'] = $tmp[0];
								$tmp = explode(" ", $row['make_data']);
								if (empty($row['make_data']))
									{
										$row['make_data'] = "-";
									}
									else
									{
										if ($tmp[1] == "00:00:00") $row['make_data'] = $tmp[0];
									}

								$num_is_edited = "";
								if (!empty($row['edit'])) $num_is_edited = "<tr><td class=\"bg-warning\" colspan=\"2\"><p class=\"text-danger\"><strong>{LANG_NUM_IS_EDITED}</strong><br>{LANG_MODERATOR} <strong>".$users[$row['moder']]."</strong><br>{LANG_LOG_TIME} <strong>".data_trans("mysql", "ua", $row['edit'])."</strong></p></td></tr>";

								$admin_links_do = "";
								$user_del_num = 0;
								if ($row['user'] == $_SESSION['user_id'] AND $active == 1 AND $is_first == "" AND $_SESSION['user_year'] == date('Y')) $user_del_num = 1;
								if ($user_p_mod == 1 AND $active == 1 AND $is_first == "" AND $_SESSION['user_year'] == date('Y')) $user_del_num = 1;
								if ($user_del_num == 1) $admin_links_do .= "<a href=\"?delete_last=".$row['id']."\" class=\"btn btn-danger btn-lg\" role=\"button\" onClick=\"if(confirm('{LANG_REMOVE_NUM_CONFIRM}')) {return true;} return false;\"><span class=\"glyphicon glyphicon-remove-circle\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_USERS_ADMIN_DEL}\"></span></a>";
								$show_files = 0;
								if ($row['add_user'] == $_SESSION['user_id'] OR $row['do_user'] == $_SESSION['user_id'] OR $user_p_mod == 1) $show_files = 1;
								if ($show_files == 1) $admin_links_do .= "<a href=\"?attach=".$row['id']."\" class=\"btn btn-success btn-lg\" role=\"button\"><span class=\"glyphicon glyphicon-floppy-save\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_USERS_ADMIN_EDIT_FILES}\"></span></a>";

								$jurnal_in .= "
								<tr valign=\"top\" align=\"center\" id=\"TRn".$row['id']."\" onclick=\"cTR('TRn".$row['id']."')\">
									<td valign=\"top\" align=\"center\" ><abbr title=\"{LANG_NUM_INFO_PLUS}\"><strong><a data-toggle=\"modal\" href=\"#JOn".$row['id']."\" aria-expanded=\"false\" aria-controls=\"JOn".$row['id']."\">".$row['id']."</strong></a></abbr></td>
									<td valign=\"top\" align=\"center\" >".data_trans("mysql", "ua", $row['get_data'])."</td>
									<td valign=\"top\" align=\"left\" >".$row['org_name']."</td>
									<td valign=\"top\" align=\"left\" >".$row['org_index']."</td>
									<td valign=\"top\" align=\"center\" >".data_trans("mysql", "ua", $row['org_data'])."</td>
									<td valign=\"top\" align=\"left\" >".$row['org_subj']."</td>
									<td valign=\"top\" align=\"left\" >".$users[$row['do_user']]."</td>
									<td valign=\"top\" align=\"left\" >".$row['make_visa']."</td>
									<td valign=\"top\" align=\"center\" >".data_trans("mysql", "ua", $row['make_data'])."</td>
								</tr>";

								$modals .= "
								<div class=\"modal fade\" id=\"JOn".$row['id']."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"JOn".$row['id']."Label\">
								  <div class=\"modal-dialog\" role=\"document\">
									<div class=\"modal-content\">
									  <div class=\"modal-header\">
										<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"{LANG_JURN_OUT_NUM_CLOSE}\"><span aria-hidden=\"true\">&times;</span></button>
										<h4 class=\"modal-title text-center\" id=\"myModalLabel\">{LANG_JURNAL_IN_INFO_N} ".$row['id']."</h4>
									  </div>
									  <div class=\"modal-body text-center\">
										<table class=\"table table-hover\">
											".$num_is_edited."
											<tr>
												<td align=\"right\"><strong>".data_trans("mysql", "ua", $row['add_time'])."</strong></td>
												<td align=\"left\">{LANG_ADD_USER} <abbr data-toggle=\"tooltip\" data-placement=\"bottom\" data-original-title=\"".$row['add_ip']."\"><strong>".$users[$row['add_user']]."</strong></abbr></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_GET_DATA_S}</td>
												<td align=\"left\"><strong>".data_trans("mysql", "ua", $row['get_data'])."</strong></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_ORG_NAME_S}: <strong>".$row['org_name']."</strong></td>
												<td align=\"left\"><kbd>№".$row['org_index'].", від ".data_trans("mysql", "ua", $row['org_data'])."</kbd></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_ORG_SUBJ}</td>
												<td align=\"left\"><strong>".$row['org_subj']."</strong></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_MAKE_VISA}: <strong>".$row['make_visa']."</strong></td>
												<td align=\"left\">{LANG_DO_USER}: <strong>".$users[$row['do_user']]."</strong></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_MAKE_DATA}</td>
												<td align=\"left\"><strong>".data_trans("mysql", "ua", $row['make_data'])."</strong></td>
											</tr>
										</table>
									  </div>
									  <div class=\"modal-footer\">
										<a href=\"#\" role=\"button\" class=\"btn btn-default btn-lg\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_JURN_OUT_NUM_CLOSE}\"></span></a>
										".$admin_links_do."
									  </div>
									</div>
								  </div>
								</div>";
							}
						$is_first = 1;
					}
					else
					{
						$page.= file_get_contents("templates/information.html");
						$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_EMPTY}", $page);
					}
				$page = str_replace("{JURNAL_IN}", $jurnal_in, $page);
				$page .= $modals;
			}
		$page = str_replace("{JURNAL_IN_TOP_STAT}", "&nbsp;", $page);
		$page = str_replace("{JURNAL_IN_AFFIX}", "", $page);
		$page = str_replace("{JURNAL_IN_STAT}", "", $page);
	}
	else
	{
		$loging_do = "{LANG_LOG_JURNAL_IN_403}";
		include ('inc/loging.php');
		header('HTTP/1.1 403 Forbidden');
		$page.= file_get_contents("templates/information_danger.html");
		$page = str_replace("{INFORMATION}", "{LANG_403}", $page);
		$timeout = "index.php";
	}

include ("inc/blender.php");
?>