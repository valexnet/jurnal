<?php
session_start();

if (isset($_POST['show_year']) AND $_POST['show_year'] > 0 AND $_POST['show_year'] < 9999 AND $_POST['show_year'][3] <> "") $_SESSION['user_year'] = $_POST['show_year'];
if (isset($_POST['show_num_list']) AND $_POST['show_num_list'] > 0 AND $_POST['show_num_list'] < 9999) $_SESSION['user_page_limit'] = $_POST['show_num_list'];

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
				$query = file_get_contents("inc/db_out.txt");
				$query = str_replace("{YEAR}", date('Y'), $query);
				mysql_query($query) or die(mysql_error());
				// Робим папку для файлів
				@mkdir("uploads\\".date('Y'));
			}

		$query = "SHOW TABLES LIKE \"DB_".date('Y')."_OUT_BLANK\";";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		if (mysql_num_rows($res) == 0)
			{
				$query = file_get_contents("inc/db_out_blank.txt");
				$query = str_replace("{YEAR}", date('Y'), $query);
				mysql_query($query) or die(mysql_error());
			}

		if (isset($_GET['add']) && $_GET['add'] == 'do')
			{
				$adres = 'true';
				if ($privat6 == 1)
					{
						if (isset($_POST['to']) && isset($_POST['subj']) && isset($_POST['nom']))
							{
								if ($_POST['data'] <> date('Y-m-d')) $error = '{LANG_JURNAL_OUT_FORM_ERROR_DATE}<br />';

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
								if ($_POST['blank_n'] == 1) {$blank_n = 1;} else {$blank_n = 0;}

								if ($error == '')
									{
										$query = "INSERT INTO `db_".date('Y')."_out` (
										`id`,
										`time`,
										`ip`,
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
										'".$_POST['nom']."',
										'".date('Y-m-d')."',
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

										$query = "SELECT `id`,`nom` FROM `db_".date('Y')."_out` ORDER BY `id` DESC LIMIT 1 ;";
										$res = mysql_query($query) or die(mysql_error());
										$queryes_num++;
										while ($row=mysql_fetch_array($res))
											{
												$nomer = $row['id'];
												$nom = $row['nom'];
												if ($blank_n == 1)
													{
														$query = "INSERT INTO `db_".date('Y')."_out_blank` ( `id`, `num` ) VALUES ( NULL , '".$row['id']."' ) ;";
														mysql_query($query) or die(mysql_error());
														$queryes_num++;
														$query_blank = "SELECT `id` FROM `db_".date('Y')."_out_blank` WHERE `num`='".$row['id']."' LIMIT 1 ;";
														$res_blank = mysql_query($query_blank) or die(mysql_error());
														$queryes_num++;
														while ($row_blank=mysql_fetch_array($res_blank))
															{
																$blank = $row_blank['id'];
															}
													}
													else
													{
														$blank = '';
													}
												$query_nom = "SELECT `structura`,`index` FROM `nomenclatura` WHERE `id`='".$nom."' AND `work`='1' LIMIT 1 ;";
												$res_nom = mysql_query($query_nom) or die(mysql_error());
												$queryes_num++;
												while ($row_nom=mysql_fetch_array($res_nom))
													{
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

										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", "{RETURN_N}: <h1>".$print_nomer."</h1>", $page);

										if ($blank_n == 1)
											{
												$page.= file_get_contents("templates/information.html");
												$page = str_replace("{INFORMATION}", "{RETURN_BLANK_N}: <h1><b>".$blank."</b></h1>", $page);
											}
											
										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", "{RETURN_REGULAR_N}: <h1>".$print_regular."</h1>", $page);
/*
										if (isset($_POST['file[]'])
											{
												// робота з файлами
											}
*/
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
										$_SESSION['form_blank_n'] = $blank_n;
										$page.= file_get_contents("templates/information.html");
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
					else
					{
						$page.= file_get_contents("templates/information.html");
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
								if ($_POST['data'] <> date('Y-m-d')) $error = '{LANG_JURNAL_OUT_FORM_ERROR_DATE}<br />';

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
				
								$query = "SELECT * FROM `db_".date('Y')."_out` ORDER BY `id` DESC LIMIT 1 ; ";
								$res = mysql_query($query) or die(mysql_error());
								$queryes_num++;
								if (mysql_num_rows($res) > 0)
									{
										while ($row=@mysql_fetch_array($res))
											{
												if ($_GET['edit'] <> $row['id']) $error .= "{LANG_JURNAL_OUT_EDIT_LAST_NOT_FIRST}<br />";
												if ($_SESSION['user_id'] <> $row['user']) $error .= "{LANG_JURNAL_OUT_EDIT_LAST_NOT_AUTHOR}<br />";
												//die($_SESSION['user_id']."-".$row['user']);
											}
									}
									
								if ($error == '')
									{
										$query = "UPDATE `db_".date('Y')."_out` SET
										`time`='".time()."',
										`ip`='".$_SERVER['REMOTE_ADDR']."',
										`nom`='".$_POST['nom']."',
										`data`='".date('Y-m-d')."',
										`to`='".$FORM_TO."',
										`subj`='".$FORM_TO_SUBJ."',
										`to_num`='".$FORM_TO_NUM."',
										`user`='".$_SESSION['user_id']."',
										`money`='".$FORM_MONEY."',
										`how`='".$_POST['how']."',
										`edit`='1'
										WHERE `id`='".$_GET['edit']."' ;";
										mysql_query($query) or die(mysql_error());
										$queryes_num++;
										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", "{LANG_OUT_EDIT_OK}", $page);
									}
									else
									{
										$page.= file_get_contents("templates/information.html");
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
												$page.= file_get_contents("templates/jurnal_out_edit.html");
												$page = str_replace("{JURNAL_OUT_NUM_TO_EDIT}", $row['id'], $page);
												$page = str_replace("{FORM_TO}", $row['to'], $page);
												$page = str_replace("{FORM_TO_N}", $row['to_num'], $page);
												$page = str_replace("{FORM_SUBJ}", $row['subj'], $page);
												$page = str_replace("{FORM_TO}", $row['to'], $page);
												if ($row['how'] == 1) {$page = str_replace("{FORM_HOW_1}", "checked", $page);} else {$page = str_replace("{FORM_HOW_1}", "", $page);}
												if ($row['how'] == 2) {$page = str_replace("{FORM_HOW_2}", "checked", $page);} else {$page = str_replace("{FORM_HOW_2}", "", $page);}
												if ($row['how'] == 3) {$page = str_replace("{FORM_HOW_3}", "checked", $page);} else {$page = str_replace("{FORM_HOW_3}", "", $page);}
												$query_blank = "SELECT * FROM `db_".date('Y')."_out_blank` WHERE `num`='".$row['id']."' LIMIT 1 ; ";
												$res_blank = mysql_query($query_blank) or die(mysql_error());
												$queryes_num++;
												if (mysql_num_rows($res_blank) > 0)
													{
														while ($row_blank=mysql_fetch_array($res_blank))
															{
																$page = str_replace("{FORM_BLANK_NUM}", " № ".$row_blank['id'], $page);
																$page = str_replace("{LANG_JURNAL_BLANK}", "{LANG_JURNAL_BLANK_REGISTERED}", $page);
																$blank_ch = "checked";
															}
													}
													else
													{
														$page = str_replace("{FORM_BLANK_NUM}", "", $page);
														$blank_ch = "";
													}
												$page = str_replace("{FORM_BLANK_N}", $blank_ch, $page);

												$query5 = "SELECT `id`,`structura`,`index`,`name` FROM `nomenclatura` WHERE `work`='1' ORDER BY `structura`,`index` ; ";
												$res5 = mysql_query($query5) or die(mysql_error());
												$queryes_num++;
												if (mysql_num_rows($res5) > 0)
													{
														while ($row5=mysql_fetch_array($res5))
															{
																if (in_array($row5['structura'], $usr_str_array))
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
										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", "{LANG_USER_OUT_NUM_NOT_EXIST}", $page);
									}
							}
					}
					else
					{
						$page.= file_get_contents("templates/information.html");
						$page = str_replace("{INFORMATION}", "{LANG_PRIVAT6_NO}", $page);
					}
			}

			
			
		if (isset($_GET['src']) && $_GET['src'] == 'do')
			{
				$adres = 'true';
				$page .= 'Пошук';
			}

		if (isset($_GET['exp']) && $_GET['exp'] == 'do')
			{
				$adres = 'true';
				$page .= 'Експорт';
			}

		if (isset($_GET['delete_last']) && $_GET['delete_last'] <> '')
			{
				$adres = 'true';
				$query = "SELECT * FROM `db_".date('Y')."_out` ORDER BY `id` DESC LIMIT 1 ; ";
				$res = mysql_query($query) or die(mysql_error());
				$queryes_num++;
				if (mysql_num_rows($res) > 0)
					{
						while ($row=@mysql_fetch_array($res))
							{
								if ($_GET['delete_last'] <> $row['id']) $ERROR .= "{LANG_JURNAL_OUT_DELETE_LAST_NOT_FIRST}<br />";
								if ($row['user'] <> $_SESSION['user_id']) $ERROR .= "{LANG_JURNAL_OUT_DELETE_LAST_NOT_AUTHOR}<br />";
								if ($_SESSION['user_year'] <> date('Y')) $ERROR .= "{LANG_JURNAL_OUT_DELETE_LAST_YEAR}<br />";
								
								if ($ERROR == "")
									{
										$query = "DELETE FROM `db_".date('Y')."_out` WHERE `id`='".$row['id']."' LIMIT 1 ; ";
										$res = mysql_query($query) or die(mysql_error());
										$queryes_num++;
										@mysql_query("ALTER TABLE `db_".date('Y')."_out` AUTO_INCREMENT =".$row['id']." ;") or die(mysql_error());
										$queryes_num++;

										$query_blank = "SELECT * FROM `db_".date('Y')."_out_blank` ORDER BY `id` DESC LIMIT 1 ; ";
										$res_blank = mysql_query($query_blank) or die(mysql_error());
										$queryes_num++;
										if (mysql_num_rows($res_blank) > 0)
											{
												while ($row_blank=mysql_fetch_array($res_blank))
													{
														if ($row_blank['num'] == $row['id'])
															{
																@mysql_query("DELETE FROM `db_".date('Y')."_out_blank` WHERE `id`='".$row_blank['id']."' LIMIT 1 ; ");
																$queryes_num++;
																@mysql_query("ALTER TABLE `db_".date('Y')."_out_blank` AUTO_INCREMENT =".$row_blank['id']." ; ");
																$queryes_num++;
															}
													}
											}

										$loging_do = "{LANG_LOG_JURNAL_OUT_DELETE_LAST} ".$row['id'];
										include ('inc/loging.php');
										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_DELETE_LAST}", $page);
									}
									else
									{
										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", $ERROR, $page);
									}
							}
					}
					else
					{
						$page.= file_get_contents("templates/information.html");
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
																						$file_name = "uploads\\".$_SESSION['user_year']."\\".$file_new_name;
																						$file_name = iconv('UTF-8', 'windows-1251', $file_name);
																						if (!file_exists($file_name))
																							{
																								if (@move_uploaded_file($FILE['tmp_name'][$i], $file_name))
																									{
																										$page.= file_get_contents("templates/information.html");
																										$page = str_replace("{INFORMATION}", "<font color=\"green\">{LANG_FILE_SAVE_OK} ".$file_new_name."</font>", $page);
																									}
																									else
																									{
																										$page.= file_get_contents("templates/information.html");
																										$page = str_replace("{INFORMATION}", "<font color=\"green\">{LANG_FILE_SAVE_ERROR} ".$file_new_name."</font>", $page);
																									}
																							}
																							else
																							{
																								$page.= file_get_contents("templates/information.html");
																								$page = str_replace("{INFORMATION}", $file_new_name." <font color=\"red\">{LANG_FILE_ALREADY_EXIST}</font>", $page);
																							}
																					}
																			}
																			else
																			{
																				$page.= file_get_contents("templates/information.html");
																				$page = str_replace("{INFORMATION}", "<font color=\"red\">{LANG_FILE_SIZE_NOT_ALLOWED}</font> <b>".(($FILE['size'][$i] / 1024) / 1024 )." MB</b>", $page);
																			}
																	}
																	else
																	{
																		$page.= file_get_contents("templates/information.html");
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
										if ($dir = opendir("uploads\\".$_SESSION['user_year']))
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
																				if (@unlink("uploads\\".$_SESSION['user_year']."\\".$file))
																					{
																						$page.= file_get_contents("templates/information.html");
																						$page = str_replace("{INFORMATION}", $file_utf8." {LANG_REMOVE_FILE_OK}", $page);
																					}
																					else
																					{
																						$page.= file_get_contents("templates/information.html");
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
																				header('Content-Length: ' . filesize("uploads\\".$_SESSION['user_year']."\\".$file));
																				if ($fd = fopen("uploads\\".$_SESSION['user_year']."\\".$file, 'rb'))
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
																				$page = str_replace("{INFORMATION}", "{TMP_MANAGE_FILES}<a href=\"jurnal_out.php?attach=".$_GET['attach']."&download=".$file_utf8."\">".$file_utf8."</a> [ ".date ('d.m.Y H:i:s', @filemtime ("uploads\\".$_SESSION['user_year']."\\".$file))." ]", $page);
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
										$page.= file_get_contents("templates/information.html");
										$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_FILES_NO}", $page);
									}
							}
							else
							{
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_ID_NOT_FOUND}", $page);
							}
					}
					else
					{
						$page.= file_get_contents("templates/information.html");
						$page = str_replace("{INFORMATION}", "{LANG_ADD_ADMIN_ADD_BD_ERROR}", $page);
					}
			}

		if ($adres <> 'true')
			{
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

				//Дивимся чи юзер пішов на бланки + звіряєм права.
				$pre_privat = "";
				$pre_link = "";
				
				if ($privat3 == 1)
					{
						$query = "SELECT * FROM `db_".$_SESSION['user_year']."_out` ORDER BY `id` DESC LIMIT ".$list." , ".$c_lmt." ; ";
						if ($_GET['blank'] == "do")
							{
								$query = "SELECT n.* FROM `db_".$_SESSION['user_year']."_out` n, `db_".$_SESSION['user_year']."_out_blank` b WHERE b.num = n.id ORDER BY `id` DESC LIMIT ".$list." , ".$c_lmt." ; ";
								$pre_link .= "blank=do";
							}
					}
					else
					{
						$query = "SELECT * FROM `db_".$_SESSION['user_year']."_out` WHERE `user`='".$_SESSION['user_id']."' ORDER BY `id` DESC LIMIT ".$list." , ".$c_lmt." ; ";
						if ($_GET['blank'] == "do")
							{
								$query = "SELECT n.* FROM `db_".$_SESSION['user_year']."_out` n, `db_".$_SESSION['user_year']."_out_blank` b WHERE b.num = n.id AND n.user = '".$_SESSION['user_id']."' ORDER BY `id` DESC LIMIT ".$list." , ".$c_lmt." ; ";
								$pre_link .= "blank=do";
							}
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
				if ($privat3 == 1)
					{
						$a_qr = "SELECT COUNT(1) FROM `db_".$_SESSION['user_year']."_out` ; ";
						if ($_GET['blank'] == "do") $a_qr = "SELECT COUNT(1) FROM `db_".$_SESSION['user_year']."_out_blank` ; ";
					}
					else
					{
						$a_qr = "SELECT COUNT(1) FROM `db_".$_SESSION['user_year']."_out` WHERE `user`=".$_SESSION['user_id']." ; ";
						if ($_GET['blank'] == "do") $a_qr = "SELECT COUNT(1) FROM `db_".$_SESSION['user_year']."_out_blank` WHERE `user`=".$_SESSION['user_id']." ; ";
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

				$res = mysql_query($query) or $error = true;
				$queryes_num++;
				if ($error <> true)
					{
						if (mysql_num_rows($res) > 0)
							{
								$page.= file_get_contents("templates/jurnal_out.html");

								// Імена юзерів в масів
								$users = array();
								$query_users = "SELECT `id`,`name` FROM `users` ORDER BY `id` ; ";
								$res_users = mysql_query($query_users) or die(mysql_error());
								$queryes_num++;
								while ($row_users=mysql_fetch_array($res_users))
									{
										$users[$row_users['id']] = $row_users['name'];
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
								$query_nomenclatura = "SELECT `id`,`structura`,`index` FROM `nomenclatura` ORDER BY `id` ; ";
								$res_nomenclatura = mysql_query($query_nomenclatura) or die(mysql_error());
								$queryes_num++;
								while ($row_nomenclatura=mysql_fetch_array($res_nomenclatura))
									{
										$nomenclatura[$row_nomenclatura['id']] = $structura[$row_nomenclatura['structura']]."-".$row_nomenclatura['index'];
									}
								////////////////////////

								while ($row=mysql_fetch_array($res))
									{
										$color++;
										if ($color == 1) {$bgcolor ="";} else {$bgcolor ="bgcolor=\"#D3EDF6\""; $color = 0;}

										$admin_links_do = "";
										$show_files = 0;
										//Вилучення останнього
										if ($row['user'] == $_SESSION['user_id'] AND $list == 0 AND $is_first == "" AND $_SESSION['user_year'] == date('Y')) $admin_links_do .= "<a title=\"{LANG_USERS_ADMIN_EDIT}\" alt=\"{LANG_USERS_ADMIN_EDIT}\" href=\"?edit=".$row['id']."\"><img src=\"templates/images/page_white_edit.png\"></a>";
										if ($row['user'] == $_SESSION['user_id'] AND $list == 0 AND $is_first == "" AND $_SESSION['user_year'] == date('Y')) $admin_links_do .= "<a title=\"{LANG_USERS_ADMIN_DEL}\" alt=\"{LANG_USERS_ADMIN_DEL}\" href=\"?delete_last=".$row['id']."\"  onClick=\"if(confirm('{LANG_REMOVE_NUM_CONFIRM}')) {return true;} return false;\"><img src=\"templates/images/cross_octagon.png\"></a>";
										//Робота з файлами для власника вихідного номеру
										if ($row['user'] == $_SESSION['user_id']) $show_files = 1;
										//Перелік існуючих файлів для всіх користувачів
										if (file_exists("uploads\\".$_SESSION['user_year']."\\".$c_n_ray."_".$nomenclatura[$row['nom']]."_".$row['id']."_*")) $show_files = 1;
										// Показуєм ссилку на управління файлами
										if ($show_files == 1) $admin_links_do .= "<a title=\"{LANG_USERS_ADMIN_EDIT_FILES}\" alt=\"{LANG_USERS_ADMIN_EDIT_FILES}\" href=\"?attach=".$row['id']."\"><img src=\"templates/images/attach_2.png\"></a>";
										
										if ($admin_links_do == "") $admin_links_do = "&nbsp;";

										$row_data = explode(" ", $row['data']);

										$blank_num = "-";

										$query_blank = "SELECT `id` FROM `db_".$_SESSION['user_year']."_out_blank` WHERE `num`='".$row['id']."' LIMIT 1 ; ";
										$res_blank = mysql_query($query_blank) or die(mysql_error());
										$queryes_num++;
										while ($row_blank=mysql_fetch_array($res_blank))
											{
												$blank_num = $row_blank['id'];
											}
										
										$how_img = "<img title=\"{LANG_HOW_EP}\" alt=\"{LANG_HOW_EP}\" src=\"templates/images/book_addresses.png\">";
										if ($row['how'] == 2) $how_img = "<img title=\"{LANG_HOW_NAR}\" alt=\"{LANG_HOW_NAR}\" src=\"templates/images/user_business_boss.png\">";
										if ($row['how'] == 3) $how_img = "<img title=\"{LANG_HOW_SEND}\" alt=\"{LANG_HOW_SEND}\" src=\"templates/images/email_open.png\">";
										
										$jurnal_out .= "
										<tr valign=\"top\" align=\"center\">
											<td ".$bgcolor." valign=\"top\" align=\"center\" >".$row['id']." / ".$nomenclatura[$row['nom']]."</td>
											<td ".$bgcolor." valign=\"top\" align=\"center\" >".$blank_num."</td>
											<td ".$bgcolor." valign=\"top\" align=\"center\" >".$how_img."</td>
											<td ".$bgcolor." valign=\"top\" align=\"center\" >".$row_data[0]."</td>
											<td ".$bgcolor." valign=\"top\" align=\"left\" >".$users[$row['user']]."</td>
											<td ".$bgcolor." valign=\"top\" align=\"left\" >".$row['to']."</td>
											<td ".$bgcolor." valign=\"top\" align=\"left\" >".$row['subj']."</td>
											<td ".$bgcolor." valign=\"top\" align=\"center\" >".$admin_links_do."</td>
										</tr>";
										$is_first = "true";
									}
								$page = str_replace("{JURNAL_OUT}", $jurnal_out, $page);
								$page = str_replace("{JURNAL_OUT_STAT}", "{JURNAL_OUT_NUM_ON_PAGE}: ".$list." / ".mysql_num_rows($res)." / ".($b[0] - mysql_num_rows($res) - $list), $page);
							}
							else
							{
								$page.= file_get_contents("templates/information.html");
								$page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_EMPTY}", $page);
							}
					}
					else
					{
						$page.= file_get_contents("templates/information.html");
						$page = str_replace("{INFORMATION}", "{LANG_YEAR_NOT_EXIST}", $page);
					}
			}
	}
	else
	{
		$loging_do = "{LANG_LOG_JURNAL_OUT_403}";
		include ('inc/loging.php');
		header('HTTP/1.1 403 Forbidden');
		$page.= file_get_contents("templates/information.html");
		$page = str_replace("{INFORMATION}", "{LANG_403}", $page);
		$timeout = "index.php";
	}

include ("inc/blender.php");
?>