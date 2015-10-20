<?php
session_start();
include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if (isset($_SESSION['user_id']))
	{
		$page .= file_get_contents("templates/jurnal_out_name.html");
		// Заміняєм назву якщо користувач відкрив зареєстровані бланки
		if ($_GET['blank'] == "do") $page = str_replace("{LANG_JURNAL_OUT}", "{LANG_JURNAL_BLANK_NAME}", $page);

		$query = "SHOW TABLES LIKE \"DB_".date('Y')."_OUT\";";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		if (mysql_num_rows($res) == 0)
			{
				if (@opendir("uploads/".date('Y')."/OUT"))
					{
						@closedir("uploads/".date('Y')."/OUT");
					}
					else
					{
						mkdir("uploads/".date('Y')."/OUT", null, true) or die("Can't create new folder - uploads/".date('Y')."/OUT");
					}
				$query = file_get_contents("inc/db_out.txt");
				$query = str_replace("{YEAR}", date('Y'), $query);
				mysql_query($query) or die(mysql_error());
			}

		if (isset($_GET['add']) && $_GET['add'] == 'do')
			{
				$adres = 'true';
				if ($privat6 == 1)
					{
						$error = '';
						$from_update = '';
						if (isset($_GET['from']) AND $_GET['from'] == "in" AND isset($_GET['id']) AND preg_match("/^[1-9][0-9]*$/", $_GET['id']))
							{
								$from_query = "SELECT * FROM `db_".$_SESSION['user_year']."_in` WHERE `id`='".$_GET['id']."' LIMIT 1 ;";
								$from_res = mysql_query($from_query) or die(mysql_error());
								$queryes_num++;
								if (mysql_num_rows($from_res) == 1)
									{
										while ($from_row=mysql_fetch_array($from_res))
											{
												if ($from_row['do_user'] == $_SESSION['user_id'] OR $user_p_mod == 1)
													{
														if (empty($from_row['do_made']))
															{
																$from_update = 1;
																$from_db = "in";
																$from_id = $_GET['id'];
																$org_data = explode(" ", $from_row['org_data']);
																$_SESSION['error'] = 'true';
																$_SESSION['form_to'] = $from_row['org_name'];
																$_SESSION['form_to_num'] = $from_row['org_index']." від ".data_trans("mysql", "ua", $org_data[0]);
																$_SESSION['form_subj'] = $from_row['org_subj'];
																$_SESSION['form_nom'] = '';
																$_SESSION['form_money'] = '';
																$_SESSION['form_how'] = '1';
																$_SESSION['form_blank_n'] = '';
															}
															else
															{
																$page.= file_get_contents("templates/information_warning.html");
																$page = str_replace("{INFORMATION}", "{LANG_NEW_OUT_WITH_DO_MADED}", $page);
																//$error = 1;
															}
													}
													else
													{
														$page.= file_get_contents("templates/information_warning.html");
														$page = str_replace("{INFORMATION}", "{LANG_NEW_OUT_WITH_NOT_DO_USER}", $page);
														$error = 1;
													}
											}
									}
									else
									{
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_NEW_OUT_WITH_NOT_EXISTS}", $page);
										$error = 1;
									}
							}
						
						if ($error == '')
							{
								if (isset($_POST['to']) && isset($_POST['subj']) && isset($_POST['nom']))
									{
										$error = '';
										$FORM_TO = str_replace($srch, $rpls, $_POST['to']);
										$FORM_TO_NUM = str_replace($srch, $rpls, $_POST['to_num']);
										$FORM_TO_SUBJ = str_replace($srch, $rpls, $_POST['subj']);

										if ($_POST['form_id'] == $_SESSION['form_id']) $error .= '{LANG_JURNAL_OUT_FORM_ERROR_FORM_ID}<br />';
										if ($_POST['nom'] == '' OR !preg_match('/^[0-9]+$/', $_POST['nom'])) $error .= '{LANG_JURNAL_OUT_FORM_ERROR_NOM}<br />';
										if ($FORM_TO == '') $error .= '{LANG_FORM_NO_TO}<br />';
										if ($FORM_TO_SUBJ == '') $error .= '{LANG_FORM_NO_TEMA}<br />';

										// Рубаєм бабоси ;)
										if (!isset($_POST['money']) OR $_POST['money'] == '') $_POST['money'] = 0;
										$_POST['money'] = str_replace(",", ".", $_POST['money']);
										$money_tmp = explode(".", $_POST['money']);
										if (isset($money_tmp[1]))
											{
												$FORM_MONEY = $money_tmp[0].".".$money_tmp[1];
											}
											else
											{
												$FORM_MONEY = $money_tmp[0];
											}
										if (!preg_match('/^[0-9\.]+$/', $FORM_MONEY)) $error .= '{LANG_JURNAL_OUT_FORM_ERROR_MONEY}<br />';


										if ($_POST['how'] <> 3 AND $_POST['how'] <> 2) $_POST['how'] = 1;
										if ($_POST['blank_n'] == 1)
											{
												$query = "SELECT `blank` FROM `db_".$_SESSION['user_year']."_out` WHERE `blank` IS NOT NULL ORDER BY `id` DESC LIMIT 1 ;";
												$res = mysql_query($query) or die(mysql_error());
												$queryes_num++;
												$blank_n = 0;
												if (mysql_num_rows($res) == 1)
													{
														while ($row=mysql_fetch_array($res))
															{
																$blank_n = $row['blank'];
															}
													}
												$blank_n = $blank_n + 1;
												$blank_sql = "'".$blank_n."'";
											}
											else
											{
												$blank_sql = "NULL";
											}

										if ($error == '')
											{
												$query = "INSERT INTO `db_".date('Y')."_out` (
												`id`,
												`time`,
												`ip`,
												`blank`,
												`nom`,
												`data`,
												`to`,
												`subj`,
												`to_num`,
												`user`,
												`money`,
												`how`,
												`edit`,
												`fav`
												) VALUES (
												NULL ,
												'".time()."',
												'".$_SERVER['REMOTE_ADDR']."',
												".$blank_sql.",
												'".$_POST['nom']."',
												'".date('Y-m-d H:i:s')."',
												'".$FORM_TO."',
												'".$FORM_TO_SUBJ."',
												'".$FORM_TO_NUM."',
												'".$_SESSION['user_id']."',
												'".$FORM_MONEY."',
												'".$_POST['how']."',
												'0',
												'0'
												) ;";
												mysql_query($query) or die(mysql_error());
												$queryes_num++;
												$_SESSION['error'] = 'false';
												$_SESSION['form_to'] = '';
												$_SESSION['form_to_num'] = '';
												$_SESSION['form_subj'] = '';
												$_SESSION['form_nom'] = '';
												$_SESSION['form_money'] = '';
												$_SESSION['form_how'] = '';
												$_SESSION['form_blank_n'] = '';
												$_SESSION['form_id'] = $_POST['form_id'];

												$query = "SELECT `id`,`nom`,`blank` FROM `db_".date('Y')."_out` ORDER BY `id` DESC LIMIT 1 ;";
												$res = mysql_query($query) or die(mysql_error());
												$queryes_num++;
												while ($row=mysql_fetch_array($res))
													{
														$nomer = $row['id'];
														$nom = $row['nom'];
														$blank = $row['blank'];
														$query_nom = "SELECT `structura`,`index`,`name` FROM `nomenclatura` WHERE `id`='".$nom."' AND `work`='1' LIMIT 1 ;";
														$res_nom = mysql_query($query_nom) or die(mysql_error());
														$queryes_num++;
														while ($row_nom=mysql_fetch_array($res_nom))
															{
																$nom_name = $row_nom['name'];
																$nom_index = $row_nom['index'];
																$nom_srt_id = $row_nom['structura'];
																$query_str = "SELECT `index` FROM `structura` WHERE `id`='".$nom_srt_id."' AND `work`='1' LIMIT 1 ;";
																$res_str = mysql_query($query_str) or die(mysql_error());
																$queryes_num++;
																while ($row_srt=mysql_fetch_array($res_str))
																	{
																		$nom_str = $row_srt['index'];
																	}
															}
														$print_nomer = "<b>".$nomer."</b> / ".$nom_str."-".$nom_index."";
														$print_regular = "<b>".$c_n_ray."_".$nom_str."-".$nom_index."_".$nomer."</b>";
													}

												$page.= file_get_contents("templates/information_success.html");
												$page = str_replace("{INFORMATION}", "{RETURN_N}: <kbd>".$print_nomer."</kbd>", $page);

												if (!empty($blank))
													{
														$page.= file_get_contents("templates/information.html");
														$page = str_replace("{INFORMATION}", "{RETURN_BLANK_N}: <kbd>".$blank."</kbd>", $page);
													}
													
												$page.= file_get_contents("templates/information.html");
												$page = str_replace("{INFORMATION}", "{RETURN_REGULAR_N}: <kbd>".$print_regular."</kbd>", $page);
												
												if ($from_update == 1)
													{
														$query = "UPDATE `db_".$_SESSION['user_year']."_".$from_db."` SET `do_made`='".date('Y-m-d H:i:s')."', `do_made_ip`='".$_SERVER['REMOTE_ADDR']."', `out_year`='".date('Y')."', `out_index`='".$nom_str."-".$nom_index.":".$nom_name."' WHERE `id`='".$from_id."' LIMIT 1 ;";
														mysql_query($query) or die(mysql_error());
														$queryes_num++;
														$page.= file_get_contents("templates/information.html");
														$page = str_replace("{INFORMATION}", "{LANG_NEW_OUT_WITH_UPDATED}: <strong>".$from_id."</strong><br><kbd>{LANG_JURNAL_IN_STATUS_3} ".date('d.m.Y H:i:s')."</kbd>", $page);
													}
											}
											else
											{
												$_SESSION['error'] = 'true';
												$_SESSION['form_to'] = $FORM_TO;
												$_SESSION['form_to_num'] = $FORM_TO_NUM;
												$_SESSION['form_subj'] = $FORM_TO_SUBJ;
												$_SESSION['form_nom'] = $_POST['nom'];
												$_SESSION['form_money'] = $FORM_MONEY;
												$_SESSION['form_how'] = $_POST['how'];
												$_SESSION['form_blank_n'] = $_POST['blank_n'];
												$page.= file_get_contents("templates/information_danger.html");
												$page = str_replace("{INFORMATION}", $error, $page);
												$page.= file_get_contents("templates/information.html");
												$page = str_replace("{INFORMATION}", "<a href=\"jurnal_out.php?add=do\">{LANG_RETURN_AND_GO}</a>", $page);
												$loging_do = "{LANG_LOG_JURNAL_OUT_ADD_ERROR}:<br />".$error;
												include ('inc/loging.php');
											}
									}
									else
									{
										$page.= file_get_contents("templates/jurnal_out_add.html");
										
										if (isset($_GET['template']) AND preg_match("/^[1-9][0-9]*$/", $_GET['template']))
											{
												$query = "SELECT * FROM `db_".$_SESSION['user_year']."_out` WHERE `id`='".$_GET['template']."' LIMIT 1 ;";
												$res = mysql_query($query) or die(mysql_error());
												$queryes_num++;
												if (mysql_num_rows($res) > 0)
													{
														while ($row=mysql_fetch_array($res))
															{
																$_SESSION['error'] = 'true';
																$_SESSION['form_blank_n'] = $row['blank'];
																$_SESSION['form_to'] = $row['to'];
																$_SESSION['form_to_num'] = $row['to_num'];
																$_SESSION['form_subj'] = $row['subj'];
																$_SESSION['form_nom'] = $row['nom'];
																$_SESSION['form_money'] = $row['money'];
																$_SESSION['form_how'] = $row['how'];
															}
													}
											}
											
										$query = "SELECT `id`,`structura`,`index`,`name` FROM `nomenclatura` WHERE `work`='1' ORDER BY `structura`,`index` ; ";
										$res = mysql_query($query) or die(mysql_error());
										$queryes_num++;
										if (mysql_num_rows($res) > 0)
											{
												while ($row=mysql_fetch_array($res))
													{
														if (in_array($row['structura'], $usr_str_array))
															{
																if ($old_nom_id != $row['structura'])
																	{
																		$query3 = "SELECT `index` FROM `structura` WHERE `id`='".$row['structura']."' AND `work`='1' LIMIT 1 ;";
																		$res3 = mysql_query($query3) or die(mysql_error());
																		$queryes_num++;
																		if (mysql_num_rows($res3) > 0)
																			{
																				while ($row3=mysql_fetch_array($res3))
																					{
																						$structura = $row3['index'];
																						$old_nom_id = $row['structura'];
																						$old_nom_index = $structura;
																					}
																			}
																			else
																			{
																				$structura = "{LANG_SRT_DELETED}";
																			}
																	}
																	else
																	{
																		$structura = $old_nom_index;
																	}

																$ndi_nom_name_tmp = implode(array_slice(explode('<br>',wordwrap($row['name'],70,'<br>',false)),0,1));
																if($ndi_nom_name_tmp!=$row['name']) $ndi_nom_name_tmp .= "...";

																if ($_SESSION['error'] == 'true' AND $_SESSION['form_nom'] == $row['id'])
																	{
																		if ($structura <> "{LANG_SRT_DELETED}") $nom_tmp .= "<OPTION value = \"".$row['id']."\" selected >(".$structura."-".$row['index'].") ".$ndi_nom_name_tmp."</OPTION>";
																	}
																	else
																	{
																		if ($structura <> "{LANG_SRT_DELETED}") $nom_tmp .= "<OPTION value = \"".$row['id']."\" >(".$structura."-".$row['index'].") ".$ndi_nom_name_tmp."</OPTION>";
																	}
															}
													}
													if ($nom_tmp == "") $nom_tmp = "<OPTION value = \"\">{LANG_NDI_NOM_EMPTY}</OPTION>";
											}
											else
											{
												$nom_tmp .= "<OPTION value = \"\">{LANG_NDI_NOM_EMPTY}</OPTION>";
											}

										if ($nom_tmp == '') $nom_tmp .= "<OPTION value = \"\">{LANG_USER_NO_NOM}</OPTION>";

										$page = str_replace("{NOMENCLATURA}", $nom_tmp, $page);

										if ($_SESSION['error'] == 'true')
											{
												$page = str_replace("{FORM_TO}", $_SESSION['form_to'], $page);
												$page = str_replace("{FORM_TO_N}", $_SESSION['form_to_num'], $page);
												$page = str_replace("{FORM_SUBJ}", $_SESSION['form_subj'], $page);
												$page = str_replace("{FORM_MONEY}", $_SESSION['form_money'], $page);
												if ($_SESSION['form_how'] == 1) {$page = str_replace("{FORM_HOW_1}", "checked", $page);} else {$page = str_replace("{FORM_HOW_1}", "", $page);}
												if ($_SESSION['form_how'] == 2) {$page = str_replace("{FORM_HOW_2}", "checked", $page);} else {$page = str_replace("{FORM_HOW_2}", "", $page);}
												if ($_SESSION['form_how'] == 3) {$page = str_replace("{FORM_HOW_3}", "checked", $page);} else {$page = str_replace("{FORM_HOW_3}", "", $page);}
												if ($_SESSION['form_blank_n'] == 1) {$page = str_replace("{FORM_BLANK_N}", "checked", $page);} else {$page = str_replace("{FORM_BLANK_N}", "", $page);}
											}
											else
											{
												$page = str_replace("{FORM_TO}", "ГУДКСУ", $page);
												$page = str_replace("{FORM_TO_N}", "", $page);
												$page = str_replace("{FORM_SUBJ}", "", $page);
												$page = str_replace("{FORM_MONEY}", "0", $page);
												$page = str_replace("{FORM_HOW_1}", "checked", $page);
												$page = str_replace("{FORM_HOW_2}", "", $page);
												$page = str_replace("{FORM_HOW_3}", "", $page);
												$page = str_replace("{FORM_BLANK_N}", "", $page);
											}
									}
							}
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_PRIVAT6_NO}", $page);
					}
			}

		if (isset($_GET['edit']) && $_GET['edit'] > 0 AND $_GET['edit'] < 99999999999)
			{
				$adres = 'true';
				if ($privat6 == 1)
					{
						if (isset($_POST['to']) && isset($_POST['subj']) && isset($_POST['nom']))
							{
								$FORM_TO = str_replace($srch, $rpls, $_POST['to']);
								$FORM_TO_NUM = str_replace($srch, $rpls, $_POST['to_num']);
								$FORM_TO_SUBJ = str_replace($srch, $rpls, $_POST['subj']);

								if ($_POST['form_id'] == $_SESSION['form_id']) $error .= '{LANG_JURNAL_OUT_FORM_ERROR_FORM_ID}<br />';
								if ($_POST['nom'] == '' OR !preg_match('/^[0-9]+$/', $_POST['nom'])) $error .= '{LANG_JURNAL_OUT_FORM_ERROR_NOM}<br />';

								// Рубаєм бабоси ;)
								if (!isset($_POST['money'])) $_POST['money'] = 0;
								$_POST['money'] = str_replace(",", ".", $_POST['money']);
								$money_tmp = explode(".", $_POST['money']);
								if (isset($money_tmp[1]))
									{
										$FORM_MONEY = $money_tmp[0].".".$money_tmp[1];
									}
									else
									{
										$FORM_MONEY = $money_tmp[0];
									}
								if (!preg_match('/^[0-9\.]+$/', $FORM_MONEY)) $error .= '{LANG_JURNAL_OUT_FORM_ERROR_MONEY}<br />';

								if ($_POST['how'] <> 3 AND $_POST['how'] <> 2) $_POST['how'] = 1;
				
								$query = "SELECT * FROM `db_".date('Y')."_out` WHERE `id`='".$_GET['edit']."' LIMIT 1 ; ";
								$res = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								if (mysql_num_rows($res) > 0)
									{
										while ($row=@mysql_fetch_array($res))
											{
												if ($_GET['edit'] <> $row['id'] AND $user_p_mod <> 1) $error .= "{LANG_USER_OUT_NUM_NOT_EXIST}<br />";
												if ($_SESSION['user_id'] <> $row['user'] AND $user_p_mod <> 1) $error .= "{LANG_JURNAL_OUT_EDIT_LAST_NOT_AUTHOR}<br />";
												if (data_trans("ua", "mysql", $_POST['data']) <> $row['data'] AND $user_p_mod <> 1) $error = '{LANG_JURNAL_OUT_FORM_EDIT_DATE_NOT_PREG}<br />';
											}
									}
									else
									{
										$error .= "{LANG_USER_OUT_NUM_NOT_EXIST}<br />";
									}
									
								if ($error == '')
									{
										$query = "UPDATE `db_".date('Y')."_out` SET
										`time`='".time()."',
										`ip`='".$_SERVER['REMOTE_ADDR']."',
										`nom`='".$_POST['nom']."',
										`data`='".data_trans("ua", "mysql", $_POST['data'])."',
										`to`='".$FORM_TO."',
										`subj`='".$FORM_TO_SUBJ."',
										`to_num`='".$FORM_TO_NUM."',
										`money`='".$FORM_MONEY."',
										`how`='".$_POST['how']."',
										`edit`='1',
										`fav`='".$_SESSION['user_id']."'
										WHERE `id`='".$_GET['edit']."' ;";
										mysql_query($query) or die(mysql_error());
										$queryes_num++;
										$page.= file_get_contents("templates/information_success.html");
										$page = str_replace("{INFORMATION}", "{LANG_OUT_EDIT_OK}", $page);
									}
									else
									{
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", $error, $page);
										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", "<a href=\"jurnal_out.php?edit=".$_GET['edit']."\">{LANG_RETURN_AND_GO}</a>", $page);
										$loging_do = "{LANG_LOG_JURNAL_OUT_EDIT_ERROR}:<br />".$error;
										include ('inc/loging.php');
									}
							}
							else
							{
								$query = "SELECT * FROM `db_".date('Y')."_out` WHERE `id`='".$_GET['edit']."' LIMIT 1 ; ";
								$res = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								if (mysql_num_rows($res) > 0)
									{
										while ($row=mysql_fetch_array($res))
											{
												if ($row['user'] != $_SESSION['user_id'] AND $user_p_mod <> 1)
													{
														$page.= file_get_contents("templates/information_danger.html");
														$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_EDIT_LAST_NOT_AUTHOR}", $page);
													}
												$page.= file_get_contents("templates/jurnal_out_edit.html");
												$page = str_replace("{JURNAL_OUT_NUM_TO_EDIT}", $row['id'], $page);
												$page = str_replace("{FORM_DATA}", data_trans("mysql", "ua", $row['data']), $page);
												$page = str_replace("{FORM_TO}", $row['to'], $page);
												$page = str_replace("{FORM_TO_N}", $row['to_num'], $page);
												$page = str_replace("{FORM_SUBJ}", $row['subj'], $page);
												$page = str_replace("{FORM_TO}", $row['to'], $page);
												$page = str_replace("{FORM_MONEY}", $row['money'], $page);
												if ($row['money'] != 0) $page = str_replace("style=\"display: none;\" id=\"input-money\"", "id=\"input-money\"", $page);
												if ($row['how'] == 1) {$page = str_replace("{FORM_HOW_1}", "checked", $page);} else {$page = str_replace("{FORM_HOW_1}", "", $page);}
												if ($row['how'] == 2) {$page = str_replace("{FORM_HOW_2}", "checked", $page);} else {$page = str_replace("{FORM_HOW_2}", "", $page);}
												if ($row['how'] == 3) {$page = str_replace("{FORM_HOW_3}", "checked", $page);} else {$page = str_replace("{FORM_HOW_3}", "", $page);}
												$blank_ch = "";
												if (!empty($row['blank'])) $blank_ch = "checked";
												$page = str_replace("{FORM_BLANK_N}", $blank_ch, $page);

												$query5 = "SELECT `id`,`structura`,`index`,`name` FROM `nomenclatura` WHERE `work`='1' ORDER BY `structura`,`index` ; ";
												$res5 = mysql_query($query5) or die(mysql_error());
												$queryes_num++;
												if (mysql_num_rows($res5) > 0)
													{
														while ($row5=mysql_fetch_array($res5))
															{
																if (in_array($row5['structura'], $usr_str_array) OR $user_p_mod == 1)
																	{
																		if ($old_nom_id != $row5['structura'])
																			{
																				$query3 = "SELECT `index` FROM `structura` WHERE `id`='".$row5['structura']."' AND work='1' LIMIT 1 ;";
																				$res3 = mysql_query($query3) or die(mysql_error());
																				$queryes_num++;
																				if (mysql_num_rows($res3) > 0)
																					{
																						while ($row3=mysql_fetch_array($res3))
																							{
																								$structura = $row3['index'];
																								$old_nom_id = $row5['structura'];
																								$old_nom_index = $structura;
																							}
																					}
																					else
																					{
																						$structura = "{LANG_SRT_DELETED}";
																					}
																			}
																			else
																			{
																				$structura = $old_nom_index;
																			}

																		$ndi_nom_name_tmp = implode(array_slice(explode('<br>',wordwrap($row5['name'],70,'<br>',false)),0,1));
																		if($ndi_nom_name_tmp!=$row5['name']) $ndi_nom_name_tmp .= "...";

																		if ($row['nom'] == $row5['id'])
																			{
																				if ($structura <> "{LANG_SRT_DELETED}") $nom_tmp .= "<OPTION value = \"".$row5['id']."\" selected >(".$structura."-".$row5['index'].") ".$ndi_nom_name_tmp."</OPTION>";
																			}
																			else
																			{
																				if ($structura <> "{LANG_SRT_DELETED}") $nom_tmp .= "<OPTION value = \"".$row5['id']."\" >(".$structura."-".$row5['index'].") ".$ndi_nom_name_tmp."</OPTION>";
																			}
																	}
															}
														if ($nom_tmp == "") $nom_tmp = "<OPTION value = \"\">{LANG_NDI_NOM_EMPTY}</OPTION>";
													}
													else
													{
														$nom_tmp .= "<OPTION value = \"\">{LANG_NDI_NOM_EMPTY}</OPTION>";
													}

												if ($nom_tmp == '') $nom_tmp .= "<OPTION value = \"\">{LANG_USER_NO_NOM}</OPTION>";

												$page = str_replace("{NOMENCLATURA}", $nom_tmp, $page);
											}
									}
									else
									{
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", "{LANG_USER_OUT_NUM_NOT_EXIST}", $page);
									}
							}
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_PRIVAT6_NO}", $page);
					}
			}
			
		if (isset($_GET['src']) && $_GET['src'] == 'do')
			{
				$adres = 'true';
				$page.= file_get_contents("templates/jurnal_out_search.html");
				
				$page = str_replace("{FORM_DATA_START}", $_SESSION['user_year']."-01-01", $page);
				$page = str_replace("{FORM_DATA_END}", $_SESSION['user_year']."-12-31", $page);
				
				$query_users = "SELECT `id`,`name` FROM `users` ORDER BY `name` ; ";
				$res_users = mysql_query($query_users) or die(mysql_error());
				$queryes_num++;
				while ($row_users=mysql_fetch_array($res_users))
					{
						$users .= "<OPTION value = \"".$row_users['id']."\">".$row_users['name']."</OPTION>";
					}
				$page = str_replace("{USERS}", $users, $page);
				
				$query = "SELECT `id`,`structura`,`index`,`name` FROM `nomenclatura` WHERE `work`='1' ORDER BY `structura`,`index` ; ";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				if (mysql_num_rows($res) > 0)
					{
						while ($row=mysql_fetch_array($res))
							{
								if ($old_nom_id != $row['structura'])
									{
										$query3 = "SELECT `index` FROM `structura` WHERE `id`='".$row['structura']."' AND `work`='1' LIMIT 1 ;";
										$res3 = mysql_query($query3) or die(mysql_error());
										$queryes_num++;
										if (mysql_num_rows($res3) > 0)
											{
												while ($row3=mysql_fetch_array($res3))
													{
														$structura = $row3['index'];
														$old_nom_id = $row['structura'];
														$old_nom_index = $structura;
													}
											}
											else
											{
												$structura = "{LANG_SRT_DELETED}";
											}
									}
									else
									{
										$structura = $old_nom_index;
									}
								$ndi_nom_name_tmp = implode(array_slice(explode('<br>',wordwrap($row['name'],70,'<br>',false)),0,1));
								if($ndi_nom_name_tmp!=$row['name']) $ndi_nom_name_tmp .= "...";
								$nom_tmp .= "<OPTION value = \"".$row['id']."\" >(".$structura."-".$row['index'].") ".$ndi_nom_name_tmp."</OPTION>";
							}
						if ($nom_tmp == "") $nom_tmp = "<OPTION value = \"\">{LANG_NDI_NOM_EMPTY}</OPTION>";
					}
					else
					{
						$nom_tmp .= "<OPTION value = \"\">{LANG_NDI_NOM_EMPTY}</OPTION>";
					}
				$page = str_replace("{NOMENCLATURA}", $nom_tmp, $page);

			}

		if (isset($_GET['delete_last']) && $_GET['delete_last'] <> '')
			{
				$adres = 'true';
				$query = "SELECT * FROM `db_".date('Y')."_out` ORDER BY `id` DESC LIMIT 1 ; ";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				if (mysql_num_rows($res) > 0)
					{
						while ($row=mysql_fetch_array($res))
							{
								if ($_GET['delete_last'] <> $row['id']) $ERROR .= "{LANG_JURNAL_OUT_DELETE_LAST_NOT_FIRST}<br />";
								if ($row['user'] <> $_SESSION['user_id'] AND $user_p_mod <> 1) $ERROR .= "{LANG_JURNAL_OUT_DELETE_LAST_NOT_AUTHOR}<br />";
								if ($_SESSION['user_year'] <> date('Y')) $ERROR .= "{LANG_JURNAL_OUT_DELETE_LAST_YEAR}<br />";
								
								if ($ERROR == "")
									{
										$query = "DELETE FROM `db_".date('Y')."_out` WHERE `id`='".$row['id']."' LIMIT 1 ; ";
										mysql_query($query) or die(mysql_error());
										$queryes_num++;
										@mysql_query("ALTER TABLE `db_".date('Y')."_out` AUTO_INCREMENT =".$row['id']." ;") or die(mysql_error());
										$queryes_num++;
										$loging_do = "{LANG_LOG_JURNAL_OUT_DELETE_LAST} ".$row['id'];
										include ('inc/loging.php');
										$page.= file_get_contents("templates/information_success.html");
										$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_DELETE_LAST}", $page);
										$timeout = "jurnal_out.php";
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
			
		if (isset($_GET['attach']) && preg_match('/^[1-9][0-9]*$/', $_GET['attach']))
			{
				$adres = 'true';
				$query = "SELECT * FROM `db_".$_SESSION['user_year']."_out` WHERE `id`='".$_GET['attach']."' LIMIT 1 ; ";
				$res = mysql_query($query) or $error = true;
				$queryes_num++;
				if ($error <> true)
					{
						if (mysql_num_rows($res) == 1)
							{
								$manage_files = 0;
								$view_files = 0;
								$row = mysql_fetch_assoc($res);
								
								$query_numenclatura = "SELECT * FROM `nomenclatura` WHERE `id`='".$row['nom']."' LIMIT 1 ; ";
								$res_numenclatura = mysql_query($query_numenclatura) or $error = true;
								$queryes_num++;
								$row_numenclatura = mysql_fetch_assoc($res_numenclatura);
								
								$query_structura = "SELECT * FROM `structura` WHERE `id`='".$row_numenclatura['structura']."' LIMIT 1 ; ";
								$res_structura = mysql_query($query_structura) or $error = true;
								$queryes_num++;
								$row_structura = mysql_fetch_assoc($res_structura);

								
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_FILE_ABOUT_NUM} <b>".$row['id']." / ".$row_structura['index']."-".$row_numenclatura['index']."</b>", $page);
								
								if ($row['user'] == $_SESSION['user_id'])
									{
										$manage_files = 1;
										$view_files = 1;
									}
								if ($privat3 == 1)
									{
										$view_files = 1;
									}
								if ($user_p_mod == 1)
									{
										$manage_files = 1;
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
																						$file_new_name = $c_n_ray."_".$row_structura['index']."-".$row_numenclatura['index']."_".$row['id']."_".$FILE['name'][$i];
																						if (preg_match("/^".$c_n_ray."_".$row_structura['index']."-".$row_numenclatura['index']."_".$row['id']."_.*/i", $FILE['name'][$i])) $file_new_name = $FILE['name'][$i];
																						$file_name = "uploads\\".$_SESSION['user_year']."\\OUT\\".$file_new_name;
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
										$page = str_replace("{FILE_PRE_NAME}", "<b>".$c_n_ray."_".$row_structura['index']."-".$row_numenclatura['index']."_".$row['id']."_</b>", $page);
										
									}
									
								if ($view_files == 1)
									{
										if ($dir = opendir("uploads\\".$_SESSION['user_year']."\\OUT"))
											{
												while (false !== ($file = readdir($dir)))
													{
														if ($file != "." && $file != "..")
															{
																$file_utf8 = iconv('windows-1251', 'UTF-8', $file);
																if (preg_match("/^".$c_n_ray."_[0-9]{1,5}\-[0-9]{1,5}_".$_GET['attach']."_.*\.(?=".$c_reg_file.")/i", $file))
																	{
																		$tmp_do = 0;
																		if (isset($_GET['delete']) AND $_GET['delete'] == $file_utf8 AND $manage_files == 1 AND $tmp_add_new_file == 0)
																			{
																				$tmp_do = 1;
																				if (@unlink("uploads\\".$_SESSION['user_year']."\\OUT\\".$file))
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
																				header('Content-Length: ' . filesize("uploads\\".$_SESSION['user_year']."\\OUT\\".$file));
																				if ($fd = fopen("uploads\\".$_SESSION['user_year']."\\OUT\\".$file, 'rb'))
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
																				$page = str_replace("{INFORMATION}", "{TMP_MANAGE_FILES}<a href=\"jurnal_out.php?attach=".$_GET['attach']."&download=".$file_utf8."\">".$file_utf8."</a> [ ".date ('d.m.Y H:i:s', @filemtime ("uploads\\".$_SESSION['user_year']."\\OUT\\".$file))." ]", $page);
																				if ($manage_files == 1)
																					{
																						$page = str_replace("{TMP_MANAGE_FILES}", "<a href=\"jurnal_out.php?attach=".$_GET['attach']."&delete=".$file_utf8."\" onClick=\"if(confirm('{LANG_REMOVE_FILE_CONFIRM}')) {return true;} return false;\"><img src=\"templates/images/cross_octagon.png\"></a> ", $page);
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
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_ADD_ADMIN_ADD_BD_ERROR}", $page);
					}
			}
		
		$search_pre = "";
		$query_where = "";
		if (isset($_GET['find']) AND !empty($_GET['find']) AND isset($_GET['do']) AND !empty($_GET['do']))
			{
				if ($_GET['blank'] == "do") $search_pre .= "blank=do&";
				if ($_GET['find'] == "id" AND preg_match("/^[1-9][0-9]*$/" ,$_GET['do']))
					{
						$query_where = "`id` = '".$_GET['do']."'";
						$where_lang = "{LANG_JURNAL_OUT_FIND_ID}";
						$search_pre .= "find=id&do=".$_GET['do']."&";
					}
				if ($_GET['find'] == "user" AND preg_match("/^[1-9][0-9]*$/" ,$_GET['do']))
					{
						$query_where = "`user` = '".$_GET['do']."'";
						$where_lang = "{LANG_SEARCH_BY_USER}";
						$search_pre .= "find=user&do=".$_GET['do']."&";
					}
				if ($_GET['find'] == "nom" AND preg_match("/^[1-9][0-9]*$/" ,$_GET['do']))
					{
						$query_where = "`nom` = '".$_GET['do']."'";
						$where_lang = "{LANG_SEARCH_BY_NOM}";
						$search_pre .= "find=nom&do=".$_GET['do']."&";
					}
				if ($_GET['find'] == "data" AND preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/" ,$_GET['do']))
					{
						$query_where = "`data` LIKE '".$_GET['do']." %'";
						$where_lang = "{LANG_SEARCH_BY_DATA}";
						$search_pre .= "find=data&do=".$_GET['do']."&";
					}
				if ($_GET['find'] == "how" AND preg_match("/^[1-3]{1}$/" ,$_GET['do']))
					{
						$query_where = "`how` = '".$_GET['do']."'";
						$where_lang = "{LANG_SEARCH_BY_HOW}";
						$search_pre .= "find=how&do=".$_GET['do']."&";
					}
			}
		
		if (isset($_GET['search']) AND $_GET['search'] == "do")
			{
				$error = "";
				if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/" ,$_GET['data_start'])) $error .= "{LANG_FORM_NO_DATA_START}<br>";
				if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/" ,$_GET['data_end'])) $error .= "{LANG_FORM_NO_DATA_END}<br>";
				if (!preg_match("/^[0-9]{1,}$/" ,$_GET['user'])) $error .= "{LANG_SEARCH_NO_USER}<br>";
				if (!preg_match("/^[0-9]{1,}$/" ,$_GET['nom'])) $error .= "{LANG_SEARCH_NO_NOM}<br>";
				if (!preg_match("/^[0-3]$/" ,$_GET['how'])) $error .= "{LANG_SEARCH_NO_HOW}<br>";
				
				$_GET['to'] = str_replace($srch, $rpls, $_GET['to']);
				$_GET['to_num'] = str_replace($srch, $rpls, $_GET['to_num']);
				$_GET['subj'] = str_replace($srch, $rpls, $_GET['subj']);
				if ($_GET['blank'] != "do") $_GET['blank'] = "0";
				if ($_GET['nom'] == "") $_GET['nom'] = "0";
				
				if ($error == "")
					{
						$where_lang = "{LANG_EXTRA_SEARCH}:<br><small>{LANG_DATA_START}:</small> ".$_GET['data_start']." <small>{LANG_DATA_END}:</small> ".$_GET['data_end']."<br>";
						if ($_GET['user'] != "0") $where_lang .= "<small>{LANG_LOG_USER}:</small> {GET_NAME_USER_".$_GET['user']."}<br>";
						if ($_GET['nom'] != "0") $where_lang .= "<small>{LANG_NOMENCLATURA}:</small> {GET_NAME_NOM_".$_GET['nom']."}<br>";
						if ($_GET['how'] != "0") $where_lang .= "<small>{LANG_HOW}:</small> {LANG_HOW_".$_GET['how']."}<br>";
						if ($_GET['blank'] == "do") $where_lang .= "{LANG_IS_REGISTER_BLANK}<br>";
						if ($_GET['to'] != "") $where_lang .= "<small>{LANG_OUT_ADD_TO}:</small> ".$_GET['to']."<br>";
						if ($_GET['to_num'] != "") $where_lang .= "<small>{LANG_OUT_ADD_TO_N}:</small> ".$_GET['to_num']."<br>";
						if ($_GET['subj'] != "") $where_lang .= "<small>{LANG_OUT_TEMA}:</small> ".$_GET['subj']."<br>";
						
						$search_pre .= "search=do&data_start=".$_GET['data_start']."&data_end=".$_GET['data_end']."&user=".$_GET['user']."&to=".$_GET['to']."&to_num=".$_GET['to_num']."&subj=".$_GET['subj']."&nom=".$_GET['nom']."&how=".$_GET['how']."&blank=".$_GET['blank']."&";
						
						$query_where .= "`data` >= '".$_GET['data_start']." 00:00:00' AND `data` <= '".$_GET['data_end']." 23:59:59'";
						
						if ($_GET['blank'] == "do")
							{
								$query_where .= " AND `blank` IS NOT NULL";
							}
						if ($_GET['user'] != "0")
							{
								$query_where .= " AND `user`='".$_GET['user']."'";
							}
						if ($_GET['nom'] != "0")
							{
								$query_where .= " AND `nom`='".$_GET['nom']."'";
							}
						if ($_GET['how'] != "0")
							{
								$query_where .= " AND `how`='".$_GET['how']."'";
							}
						if ($_GET['to'] != "")
							{
								$_GET['to'] = str_replace("*", "%", $_GET['to']);
								$query_where .= " AND `to` LIKE '".$_GET['to']."'";
							}
						if ($_GET['to_num'] != "")
							{
								$_GET['to_num'] = str_replace("*", "%", $_GET['to_num']);
								$query_where .= " AND `to_num` LIKE '".$_GET['to_num']."'";
							}
						if ($_GET['subj'] != "")
							{
								$_GET['subj'] = str_replace("*", "%", $_GET['subj']);
								$query_where .= " AND `subj` LIKE '".$_GET['subj']."'";
							}
					}
					else
					{
						$adres = "true";
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_SEARCH_ERROR}", $page);
						$page.= file_get_contents("templates/information.html");
						$page = str_replace("{INFORMATION}", $error, $page);
					}
			}
			
		if ($adres <> 'true')
			{
				$page = str_replace("{JURNAL_OUT_TOP_STAT}", file_get_contents("templates/jurnal_out_top_stat.html"), $page);
				$page = str_replace("{JURNAL_OUT_AFFIX}", "data-spy=\"affix\" data-offset-top=\"170\"", $page);
				if (isset($_GET['list']) AND $_GET['list'] > 0)
					{
						if ($_GET['list'] > 0 AND $_GET['list'] < 99999999999)
							{
								$list = $_GET['list'];
							}
							else
							{
								$list = 0;
							}
					}
					else
					{
						$list = 0;
					}
					
				if (isset($_GET['export']) AND $_GET['export'] == "do")
					{
						$list = 0;
						$c_lmt = 999999;
						$export_type = "text/csv";
						$export_name = "jurnal_out.csv";
					}

				//Дивимся чи юзер пішов на бланки + звіряєм права.
				$pre_link = $search_pre;
				
				$where = "";
				if ($privat3 == 1)
					{
						if ($query_where != "") $where = " WHERE ".$query_where;
						$query = "SELECT * FROM `db_".$_SESSION['user_year']."_out` ".$where." ORDER BY `id` DESC LIMIT ".$list." , ".$c_lmt." ; ";
					}
					else
					{
						if ($query_where != "") $where = " AND ".$query_where;
						$query = "SELECT * FROM `db_".$_SESSION['user_year']."_out` WHERE `user`='".$_SESSION['user_id']."' ".$where." ORDER BY `id` DESC LIMIT ".$list." , ".$c_lmt." ; ";
					}

				// Шукаємо цифру для кнопки left
				if ($list > 0)
					{
						if ($list - $c_lmt > 0)
							{
								$page = str_replace("{LIST_LEFT}", "?list=".($list - $c_lmt)."&".$pre_link, $page);
							}
							else
							{
								$page = str_replace("{LIST_LEFT}", "?".$pre_link, $page);
							}
					}
					else
					{
						$page = str_replace("{LIST_LEFT}", "?".$pre_link, $page);
					}
				////

				// Шукаємо кількість номерів по ліміту $c_lmt для останньої сторінки
				$where = "";
				if ($privat3 == 1)
					{
						if ($query_where != "") $where = " WHERE ".$query_where;
						$a_qr = "SELECT COUNT(1) FROM `db_".$_SESSION['user_year']."_out` ".$where." ; ";
					}
					else
					{
						if ($query_where != "") $where = " AND ".$query_where;
						$a_qr = "SELECT COUNT(1) FROM `db_".$_SESSION['user_year']."_out` WHERE `user`=".$_SESSION['user_id']." ".$where." ; ";
					}
					
				$a = @mysql_query($a_qr);
				$queryes_num++;
				$b = @mysql_fetch_array($a);
				if ($b[0] - $c_lmt <= 0)
					{
						$page = str_replace("{LIST_END}", "?".$pre_link, $page);
					}
					else
					{
						$page = str_replace("{LIST_END}", "?list=".($b[0] - $c_lmt)."&".$pre_link, $page);
					}
				////

				// Шукаємо цифру для кнопки right
				if ($list > 0)
					{
						if ($b[0] - $c_lmt > $list)
							{
								$page = str_replace("{LIST_RIGHT}", "?list=".($list + $c_lmt)."&".$pre_link, $page);
							}
							else
							{
								$page = str_replace("{LIST_RIGHT}", "?list=".$list."&".$pre_link, $page);
							}

					}
					else
					{
						if ($b[0] < $c_lmt) $page = str_replace("{LIST_RIGHT}", "?list=0"."&".$pre_link, $page);
						$page = str_replace("{LIST_RIGHT}", "?list=".$c_lmt."&".$pre_link, $page);
					}
				////

				$page = str_replace("{LIST_END}", "", $page);
				$error = "false";
				if (isset($_GET['export']) AND $_GET['export'] == "do") $query = str_replace(" DESC ", " ", $query);
				$res = mysql_query($query) or $error = "true";
				$queryes_num++;
				if ($error != "true")
					{
						// Імена юзерів в масів
						$users = array();
						$query_users = "SELECT `id`,`name` FROM `users` ORDER BY `id` ; ";
						$res_users = mysql_query($query_users) or die(mysql_error());
						$queryes_num++;
						while ($row_users=mysql_fetch_array($res_users))
							{
								$users[$row_users['id']] = $row_users['name'];
								if ($where_lang != "") $where_lang = str_replace("{GET_NAME_USER_".$row_users['id']."}", $row_users['name'], $where_lang);
							}
						////////////////////////

						// Назва номенклатури в масів
						$structura = array();
						$query_structura = "SELECT `id`,`index` FROM `structura` ORDER BY `id` ; ";
						$res_structura = mysql_query($query_structura) or die(mysql_error());
						$queryes_num++;
						while ($row_structura=mysql_fetch_array($res_structura))
							{
								$structura[$row_structura['id']] = $row_structura['index'];
							}
						$nomenclatura = array();
						$query_nomenclatura = "SELECT `id`,`structura`,`index`,`name` FROM `nomenclatura` ORDER BY `id` ; ";
						$res_nomenclatura = mysql_query($query_nomenclatura) or die(mysql_error());
						$queryes_num++;
						while ($row_nomenclatura=mysql_fetch_array($res_nomenclatura))
							{
								$nomenclatura[$row_nomenclatura['id']] = $structura[$row_nomenclatura['structura']]."-".$row_nomenclatura['index'];
								if ($where_lang != "") $where_lang = str_replace("{GET_NAME_NOM_".$row_nomenclatura['id']."}", $structura[$row_nomenclatura['structura']]."-".$row_nomenclatura['index']." ".$row_nomenclatura['name'], $where_lang);
							}
						////////////////////////

						// Якщо є пошук, показуємо повідомлення і ссилку на знулення.
						if (isset($where_lang) AND !empty($where_lang))
							{
								$disable_serch = "jurnal_out.php";
								if ($_GET['blank'] == "do") $disable_serch .= "?blank=do";
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", $where_lang." <a class=\"btn btn-default btn-sm\" href=\"".$disable_serch."\">{LANG_CLEAN_SERCH_RESULTS}</a>", $page);
							}
							
						$page.= file_get_contents("templates/jurnal_out.html");
						$modals = "";
						
						if (isset($_GET['export']) AND $_GET['export'] == "do")
							{
								$export = "\"".$c_nam."\";\n\n";
								$export .= "\"{LANG_HEADERINFO}\";\n";
								$export .= "\"{LANG_SEARCH_RESULTS} :\";\n";
								$export .=  "\"{LANG_INDEX_DOC}\";\"{LANG_JURNAL_OUT_BLANK_NUM} №\";\"{LANG_OUT_ADD_TO_N}\";\"{LANG_LOG_TIME}\";\"{LANG_OUT_TEMA}\";\"{LANG_OUT_ADD_FROM}\";\"{LANG_OUT_ADD_TO}\";\"{LANG_HOW}\";\"{LANG_SEND_MONEY}\";\n";
							}
						
						while ($row=mysql_fetch_array($res))
							{
								$admin_links_do = "";
								if ($privat6 == 1) $admin_links_do .= "<a href=\"?add=do&template=".$row['id']."\" class=\"btn btn-info btn-lg\" role=\"button\"><span class=\"glyphicon glyphicon-random\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_NEW_WITH_TEMPLATE}\"></span></a>";
								$show_files = 0;
								//Робота з файлами для власника вихідного номеру та модератора
								if ($row['user'] == $_SESSION['user_id'] OR $user_p_mod == 1) $show_files = 1;
								//Перелік існуючих файлів для всіх користувачів
								if (file_exists("uploads\\".$_SESSION['user_year']."\\OUT\\".$c_n_ray."_".$nomenclatura[$row['nom']]."_".$row['id']."_*")) $show_files = 1;
								// Показуєм ссилку на управління файлами
								if ($show_files == 1) $admin_links_do .= "<a href=\"?attach=".$row['id']."\" class=\"btn btn-success btn-lg\" role=\"button\"><span class=\"glyphicon glyphicon-floppy-save\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_USERS_ADMIN_EDIT_FILES}\"></span></a>";
								
								$user_edit_num = 0;
								if ($row['user'] == $_SESSION['user_id'] AND $_SESSION['user_year'] == date('Y')) $user_edit_num = 1;
								if ($user_edit_num == 1 OR $user_p_mod == 1) $admin_links_do .= "<a href=\"?edit=".$row['id']."\" class=\"btn btn-warning btn-lg\" role=\"button\"><span class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_USERS_ADMIN_EDIT}\"></span></a>";
								
								$user_del_num = 0;
								if ($row['user'] == $_SESSION['user_id'] AND $list == 0 AND $is_first == "" AND $_SESSION['user_year'] == date('Y')) $user_del_num = 1;
								if ($user_p_mod == 1 AND $list == 0 AND $is_first == "" AND $_SESSION['user_year'] == date('Y')) $user_del_num = 1;
								if ($user_del_num == 1) $admin_links_do .= "<a href=\"?delete_last=".$row['id']."\" class=\"btn btn-danger btn-lg\" role=\"button\" onClick=\"if(confirm('{LANG_REMOVE_NUM_CONFIRM}')) {return true;} return false;\"><span class=\"glyphicon glyphicon-remove-circle\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_USERS_ADMIN_DEL}\"></span></a>";

								if ($admin_links_do == "") $admin_links_do = "&nbsp;";

								$row_data = explode(" ", $row['data']);

								$blank_num = "-";
								if (!empty($row['blank'])) $blank_num = $row['blank'];
								
								$how_img = "<img title=\"{LANG_HOW_1}\" alt=\"{LANG_HOW_1}\" src=\"templates/images/book_addresses.png\">";
								if ($row['how'] == 2) $how_img = "<img title=\"{LANG_HOW_2}\" alt=\"{LANG_HOW_2}\" src=\"templates/images/user_business_boss.png\">";
								if ($row['how'] == 3) $how_img = "<img title=\"{LANG_HOW_3}\" alt=\"{LANG_HOW_3}\" src=\"templates/images/email_open.png\">";
								
								$need_serch_blank = "";
								if ($_GET['blank'] == "do") $need_serch_blank = "&blank=do&";
								
								if ($row['to_num'] == "") $row['to_num'] = "-";
								
								$num_is_edited = "";
								if ($row['edit'] == 1) $num_is_edited = "<tr><td class=\"bg-warning\" colspan=\"2\"><p class=\"text-danger\"><strong>{LANG_NUM_IS_EDITED}</strong><br>{LANG_MODERATOR} <strong>".$users[$row['fav']]."</strong><br>{LANG_LOG_TIME} <strong>".date('Y-m-d H:i:s', $row['time'])."</strong></p></td></tr>";
								
								$jurnal_out .= "
								<tr valign=\"top\" align=\"center\">
									<td valign=\"top\" align=\"center\" ><abbr title=\"{LANG_NUM_INFO_PLUS}\"><a data-toggle=\"modal\" href=\"#JOn".$row['id']."\" aria-expanded=\"false\" aria-controls=\"JOn".$row['id']."\">".$row['id']."</a></abbr> / <a href=\"jurnal_out.php?".$need_serch_blank."find=nom&do=".$row['nom']."\">".$nomenclatura[$row['nom']]."</a></td>
									<td valign=\"top\" align=\"center\" >".$blank_num."</td>
									<td valign=\"top\" align=\"center\" ><a href=\"jurnal_out.php?".$need_serch_blank."find=how&do=".$row['how']."\">".$how_img."</a></td>
									<td valign=\"top\" align=\"center\" ><a href=\"jurnal_out.php?".$need_serch_blank."find=data&do=".$row_data[0]."\">".data_trans("mysql", "ua", $row_data[0])."</a></td>
									<td valign=\"top\" align=\"left\" ><a href=\"jurnal_out.php?".$need_serch_blank."find=user&do=".$row['user']."\">".$users[$row['user']]."</a></td>
									<td valign=\"top\" align=\"left\" >".$row['to']."</td>
									<td valign=\"top\" align=\"left\" >".$row['subj']."</td>
								</tr>";
								
								$modals .= "
								<div class=\"modal fade\" id=\"JOn".$row['id']."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"JOn".$row['id']."Label\">
								  <div class=\"modal-dialog\" role=\"document\">
									<div class=\"modal-content\">
									  <div class=\"modal-header\">
										<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"{LANG_JURN_OUT_NUM_CLOSE}\"><span aria-hidden=\"true\">&times;</span></button>
										<h4 class=\"modal-title text-center\" id=\"myModalLabel\">{LANG_JURN_OUT_NUM_INFO} ".$row['id']." / ".$nomenclatura[$row['nom']]."</h4>
									  </div>
									  <div class=\"modal-body text-center\">
										<table class=\"table table-hover\">
											".$num_is_edited."
											<tr>
												<td align=\"right\">{LANG_OUT_ADD_FROM}</td>
												<td align=\"left\"><strong>".$users[$row['user']]."</strong></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_LOG_TIME}</td>
												<td align=\"left\"><strong>".data_trans("mysql", "ua", $row_data[0])."</strong> ".$row_data[1]."</td>
											</tr>
											<tr>
												<td align=\"right\">{RETURN_BLANK_N}</td>
												<td align=\"left\"><strong>".$blank_num."</strong></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_JURNAL_TO}</td>
												<td align=\"left\"><strong>".$row['to']."</strong></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_OUT_TEMA}</td>
												<td align=\"left\"><strong>".$row['subj']."</strong></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_OUT_ADD_TO_N}</td>
												<td align=\"left\"><strong>".$row['to_num']."</strong></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_HOW}</td>
												<td align=\"left\"><strong>{LANG_HOW_".$row['how']."}</strong></td>
											</tr>
											<tr>
												<td align=\"right\">{LANG_SEND_MONEY}</td>
												<td align=\"left\"><strong>".$row['money']."</strong></td>
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
								$is_first = "true";
								if (isset($_GET['export']) AND $_GET['export'] == "do") $export .=  "\"".$row['id']." \\ ".$nomenclatura[$row['nom']]."\";\"".$blank_num."\";\"".$row['to_num']."\";\"".$row['data']."\";\"".$row['subj']."\";\"".$users[$row['user']]."\";\"".$row['to']."\";\"{LANG_HOW_".$row['how']."}\";\"".$row['money']."\";\n";
							}
						$page .= $modals;
						$page = str_replace("{JURNAL_OUT}", $jurnal_out, $page);
						$page = str_replace("{JURNAL_OUT_STAT}", "{JURNAL_OUT_NUM_ON_PAGE}: ".$list." / ".mysql_num_rows($res)." / ".($b[0] - mysql_num_rows($res) - $list), $page);
						
						if (mysql_num_rows($res) == 0)
							{
								$page.= file_get_contents("templates/information_danger.html");
								$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_EMPTY}", $page);
							}
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_YEAR_NOT_EXIST}", $page);
					}
			}
		$page = str_replace("{JURNAL_OUT_TOP_STAT}", "", $page);
		$page = str_replace("{JURNAL_OUT_AFFIX}", "", $page);
	}
	else
	{
		$loging_do = "{LANG_LOG_JURNAL_OUT_403}";
		include ('inc/loging.php');
		header('HTTP/1.1 403 Forbidden');
		$page.= file_get_contents("templates/information_danger.html");
		$page = str_replace("{INFORMATION}", "{LANG_403}", $page);
		$timeout = "index.php";
	}

include ("inc/blender.php");
?>