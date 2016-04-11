<?php
include ('inc/config.php');

Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache");
Header("Content-Type: text/javascript; charset=utf-8");

$page = '';
if (isset($_SESSION['user_id']))
	{
		if (isset($_POST['menu']))
			{
				if ($_POST['menu'] == 'kt')
					{
						if (isset($_POST['act']))
							{
								if ($_POST['act'] == 'edit')
									{
										$invent_id = str_replace($srch, $rpls, $_POST['invent_id']);
										$invent_nom = str_replace($srch, $rpls, $_POST['invent_nom']);
										$name = str_replace($srch, $rpls, $_POST['name']);
										$sn = str_replace($srch, $rpls, $_POST['sn']);
										$data_made = str_replace($srch, $rpls, $_POST['data_made']);
										$data_install = str_replace($srch, $rpls, $_POST['data_install']);
										$status_1_id = str_replace($srch, $rpls, $_POST['status_1_id']);
										$status_2_id = str_replace($srch, $rpls, $_POST['status_2_id']);
										$func = str_replace($srch, $rpls, $_POST['func']);
										$note = str_replace($srch, $rpls, $_POST['note']);
										$spec_name = str_replace($srch, $rpls, $_POST['spec_name']);
										$spec_value = str_replace($srch, $rpls, $_POST['spec_value']);

										if ($privatb != 1) $error .= 'Відсутні права на редагування IT.<br />';
										if (!preg_match("/^[1-9][0-9]*$/", $invent_id)) $error .= 'Не обрана інвентарна картка.<br />';
										if (!preg_match("/^[1-9][0-9]*$/", $invent_nom)) $error .= 'Не вірний інвентарний номер.<br />';
										if ($name == '') $error .= 'Не вказано назву КТ.<br />';
										//if ($sn == '') $error .= 'Не вказано серійний номер КТ.<br />';
										if (!preg_match("/^[1-9][0-9]{3}$/", $data_made)) $error .= 'Не вірний рік виготовлення.<br />';
										if (!preg_match("/^[1-9][0-9]{3}$/", $data_install)) $error .= 'Не вірний рік введення в експлуатацію.<br />';
										if (!preg_match("/^[1-9][0-9]*$/", $status_1_id)) $error .= 'Не вірний робочий стан.<br />';
										if (!preg_match("/^[1-9][0-9]*$/", $status_2_id)) $error .= 'Не вірний стан експлуатації.<br />';

										$spec_name_count = 0;
										if ($spec_name != '')
											{
												$spec_name_array = array();
												foreach ($spec_name as $s_name)
													{
														if (strlen($s_name) > 0)
															{
																$spec_name_count++;
																$spec_name_array[] = $s_name;
															}
													}
											}

										$spec_value_count = 0;
										if ($spec_value != '')
											{
												$spec_value_array = array();
												foreach ($spec_value as $s_value)
													{
														if (strlen($s_value) > 0)
															{
																$spec_value_count++;
																$spec_value_array[] = $s_value;
															}
													}
											}

										if ($spec_name_count == 0 AND $spec_value_count == 0)
											{
												$spec_count = 0;
											}
											else
											{
												if ($spec_name_count != $spec_value_count)
													{
														$error .= 'Не всі обрані характеристики введені.<br />';
													}
													else
													{
														$spec_count = count($spec_name_array);
													}
											}

										if ($sn != '')
											{
												if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id']))
													{
														$res = mysql_query("SELECT `invent_id`, `name` FROM `db_it_kt` WHERE `sn`='".$sn."' AND `id`<>'".$_POST['edit_id']."' LIMIT 1;");
													}
													else
													{
														$res = mysql_query("SELECT `invent_id`, `name` FROM `db_it_kt` WHERE `sn`='".$sn."' LIMIT 1;");
													}

												if (mysql_num_rows($res) == 1)
													{
														$row_double = mysql_fetch_assoc($res);
														$find_double = mysql_query("SELECT `invent`, `name` FROM `db_it_invent` WHERE `id`='".$row_double['invent_id']."' LIMIT 1;");
														$data_double = mysql_fetch_assoc($find_double);
														$error .= '
														<hr>
														<font color="red">Серійний номер вже є в іншій КТ</font>
														<p>Інв. № <b>'.$data_double['invent'].'</b>. Назва: '.$data_double['name'].'</b></p>
														<p>Назва КТ: <b>'.$row_double['name'].'</b>.</p>
														<hr>';
													}
											}

										if ($error == '')
											{
												if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id']))
													{
														mysql_query("UPDATE `db_it_kt` SET `invent_id`='".$invent_id."', `name`='".$name."', `sn`='".$sn."', `data_made`='".$data_made."', `data_install`='".$data_install."', `status_1_id`='".$status_1_id."', `status_2_id`='".$status_2_id."', `func`='".$func."', `note`='".$note."' WHERE `id`='".$_POST['edit_id']."' LIMIT 1;") or die(mysql_error());
														$info_html = '
															<font color="green"><strong>Дані КТ оновлено, Інв. № '.$invent_nom.'</strong></font>
															<hr>
															<center>
																<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
															</center>';
														$spec_errors = 0;
														$spec_kt_id = $_POST['edit_id'];
													}
													else
													{
														mysql_query("INSERT INTO `db_it_kt` (`id`, `invent_id`, `name`, `sn`, `data_made`, `data_install`, `status_1_id`, `status_2_id`, `func`, `note`)
														VALUES (NULL, '".$invent_id."', '".$name."', '".$sn."', '".$data_made."', '".$data_install."', '".$status_1_id."', '".$status_2_id."', '".$func."', '".$note."')") or die(mysql_error());
														$info_html = '
															<font color="green"><strong>КТ успішно Додано до Інв. № '.$invent_nom.'</strong></font>
															<hr>
															<center>
																<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
															</center>';
														$spec_errors = 0;
														$spec_kt_id = mysql_insert_id();
													}

												mysql_query("DELETE FROM `db_it_specs` WHERE `kt_id`='".$spec_kt_id."' ;");
												if ($spec_count > 0)
													{
														for ($i = 0; $i < $spec_count; $i++)
															{
																mysql_query("INSERT INTO `db_it_specs` (`id`, `kt_id`, `name`, `value`) VALUES (NULL, '".$spec_kt_id."', '".$spec_name_array[$i]."', '".$spec_value_array[$i]."') ") or die(mysql_error());
															}
													}
												echo $info_html;
											}
											else
											{
												echo '
												<font color="red">'.$error.'</font>
												<hr>
												<center>
													<button onclick="$(\'#informer\').modal(\'hide\');" type="button" class="btn btn-danger" data-dismiss="modal">Повернутись</button>
												</center>';
											}
									}

								if ($_POST['act'] == 'delete' AND $privatb == 1)
									{
										if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id']))
											{
												$res = mysql_query("SELECT * FROM `db_it_kt` WHERE `id`='".$_POST['edit_id']."' LIMIT 1;");
												if (mysql_num_rows($res) > 0)
													{
														mysql_query("DELETE FROM `db_it_kt` WHERE `id`='".$_POST['edit_id']."' LIMIT 1;") or die(mysql_error());
														mysql_query("DELETE FROM `db_it_specs` WHERE `kt_id`='".$_POST['edit_id']."' ;") or die(mysql_error());
														echo '
															<font color="green"><strong>Дані вилучено успішно</strong></font>
															<hr>
															<center>
																<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
															</center>';
													}
													else
													{
														echo '
														<font color="red">Запис не існує, або був вилучений раніше.</font>
														<hr>';
													}
											}
									}

							}
					}

				if ($_POST['menu'] == 'rooms')
					{
						if (isset($_POST['act']))
							{
								if ($_POST['act'] == 'get_form_plus' AND $privata == 1)
									{
										$page .= file_get_contents("templates/jurnal_it_rooms_plus.html");
									}
								if ($_POST['act'] == 'delete' AND $privatb == 1)
									{
										if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id']))
											{
												$res = mysql_query("SELECT * FROM `db_it_rooms` WHERE `id`='".$_POST['edit_id']."' LIMIT 1;");
												if (mysql_num_rows($res) > 0)
													{
														mysql_query("DELETE FROM `db_it_rooms` WHERE `id`='".$_POST['edit_id']."' LIMIT 1;") or die(mysql_error());
														echo '
															<font color="green"><strong>Дані вилучено успішно</strong></font>
															<hr>
															<center>
																<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
															</center>';
													}
													else
													{
														echo '
														<font color="red">Запис не існує, або був вилучений раніше.</font>
														<hr>';
													}
											}
									}
								if ($_POST['act'] == 'edit')
									{
										$error = '';
										if ($privatb != 1) $error .= 'Відсутні права на редагування IT.<br />';
										$nom = str_replace($srch, $rpls, $_POST['nom']);
										$name = str_replace($srch, $rpls, $_POST['name']);
										$name_full = str_replace($srch, $rpls, $_POST['name_full']);
										if ($nom == '') $error .= 'Не вказано номер кімнати.<br />';
										if ($name == '') $error .= 'Не вказано назву кімнати.<br />';
										if ($name_full == '') $error .= 'Не вказано повну назву кімнати.<br />';
										if ($error == '')
											{
												if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id']))
													{
														mysql_query("UPDATE `db_it_rooms` SET `nom`='".$nom."', `name`='".$name."', `name_full`='".$name_full."' WHERE `id`='".$_POST['edit_id']."' LIMIT 1;") or die(mysql_error());
														echo '
															<font color="green"><strong>Дані оновлено</strong></font>
															<hr>
															<center>
																<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
															</center>';
													}
													else
													{
														mysql_query("INSERT INTO `db_it_rooms` (`id`, `nom`, `name`, `name_full`)
														VALUES (NULL, '".$nom."', '".$name."', '".$name_full."')") or die(mysql_error());
														echo '
															<font color="green"><strong>Дані записано</strong></font>
															<hr>
															<center>
																<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
															</center>';
													}
											}
											else
											{
												echo '
												<font color="red">'.$error.'</font>
												<hr>
												<center>
													<button onclick="$(\'#informer\').modal(\'hide\'); $(\'#room_modal\').modal(\'show\');" type="button" class="btn btn-danger" data-dismiss="modal">Повернутись</button>
												</center>';
											}
									}
								if ($_POST['act'] == 'load' AND $privata == 1)
									{
										$res = mysql_query("SELECT * FROM `db_it_rooms` ORDER BY `nom` ASC ;");
										if (mysql_num_rows($res) > 0)
											{
												$page .= file_get_contents("templates/jurnal_it_rooms.html");
												$tbody_content = '';
												$a = 0;
												while ($row=mysql_fetch_array($res))
													{
														$a++;
														$tbody_content .= '<tr ondblclick="plus('.$row['id'].')">
															<td id="nom_'.$row['id'].'">'.$row['nom'].'</td>
															<td id="name_'.$row['id'].'">'.$row['name'].'</td>
															<td id="name_full_'.$row['id'].'">'.$row['name_full'].'</td>
														</tr>';
													}
												$tbody_content .= '<tr><td colspan="4">Всього '.$a.' записи</td></tr>';
												$page = str_replace('{TBODY_CONTENT}', $tbody_content, $page);
											}
											else
											{
												echo '
												<font color="red">Відсутні записи</font>
												<hr>';
											}
									}
							}
					}

				if ($_POST['menu'] == 'status')
					{
						if (isset($_POST['act']))
							{
								if ($_POST['act'] == 'load' AND $privata == 1)
									{
										$res = mysql_query("SELECT * FROM `db_it_status` ORDER BY `name` ASC ;");
										if (mysql_num_rows($res) > 0)
											{
												$page .= file_get_contents("templates/jurnal_it_status.html");
												$tbody_content = '';
												$a = 0;
												while ($row=mysql_fetch_array($res))
													{
														$a++;
														$tbody_content .= '<tr ondblclick="plus('.$row['id'].')">
															<td id="name_'.$row['id'].'">'.$row['name'].'</td>
															<td id="name_full_'.$row['id'].'">'.$row['name_full'].'</td>
															<td id="text_color_'.$row['id'].'">'.$row['text_color'].'</td>
															<td id="bg_color_'.$row['id'].'">'.$row['bg_color'].'</td>
															<td style="color: '.$row['text_color'].'; background-color: '.$row['bg_color'].';">Тест: <b>Раз</b>, <i>Два</i>.</td>
														</tr>';
													}
												$tbody_content .= '<tr><td colspan="5">Всього '.$a.' записи</td></tr>';
												$page = str_replace('{TBODY_CONTENT}', $tbody_content, $page);
											}
											else
											{
												echo '
												<font color="red">Відсутні записи</font>
												<hr>';
											}
									}
								if ($_POST['act'] == 'get_form_plus' AND $privata == 1)
									{
										$page .= file_get_contents("templates/jurnal_it_status_plus.html");
									}
								if ($_POST['act'] == 'edit')
									{
										$error = '';
										if ($privatb != 1) $error .= 'Відсутні права на редагування IT.<br />';
										$name = str_replace($srch, $rpls, $_POST['name']);
										$name_full = str_replace($srch, $rpls, $_POST['name_full']);
										$text_color = str_replace($srch, $rpls, $_POST['text_color']);
										$bg_color = str_replace($srch, $rpls, $_POST['bg_color']);
										if ($name == '') $error .= 'Не вказано назву кімнати.<br />';
										if ($name_full == '') $error .= 'Не вказано повну назву кімнати.<br />';
										if ($text_color == '') $error .= 'Не вказано колір тексту.<br />';
										if ($bg_color == '') $error .= 'Не вказано колір фону.<br />';
										if ($error == '')
											{
												if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id']))
													{
														mysql_query("UPDATE `db_it_status` SET `name`='".$name."', `name_full`='".$name_full."', `text_color`='".$text_color."', `bg_color`='".$bg_color."' WHERE `id`='".$_POST['edit_id']."' LIMIT 1;") or die(mysql_error());
														echo '
															<font color="green"><strong>Дані оновлено</strong></font>
															<hr>
															<center>
																<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
															</center>';
													}
													else
													{
														mysql_query("INSERT INTO `db_it_status` (`id`, `name`, `name_full`, `text_color`, `bg_color`)
														VALUES (NULL, '".$name."', '".$name_full."', '".$text_color."', '".$bg_color."')") or die(mysql_error());
														echo '
															<font color="green"><strong>Дані записано</strong></font>
															<hr>
															<center>
																<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
															</center>';
													}
											}
											else
											{
												echo '
												<font color="red">'.$error.'</font>
												<hr>
												<center>
													<button onclick="$(\'#informer\').modal(\'hide\'); $(\'#status_modal\').modal(\'show\');" type="button" class="btn btn-danger" data-dismiss="modal">Повернутись</button>
												</center>';
											}
									}
								if ($_POST['act'] == 'delete' AND $privatb == 1)
									{
										if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id']))
											{
												$res = mysql_query("SELECT * FROM `db_it_status` WHERE `id`='".$_POST['edit_id']."' LIMIT 1;");
												if (mysql_num_rows($res) > 0)
													{
														mysql_query("DELETE FROM `db_it_status` WHERE `id`='".$_POST['edit_id']."' LIMIT 1;") or die(mysql_error());
														echo '
															<font color="green"><strong>Дані вилучено успішно</strong></font>
															<hr>
															<center>
																<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
															</center>';
													}
													else
													{
														echo '
														<font color="red">Запис не існує, або був вилучений раніше.</font>
														<hr>';
													}
											}
									}
							}
					}

				if ($_POST['menu'] == 'invent')
					{
						if (isset($_POST['act']))
							{
								if ($_POST['act'] == 'load_row_info' AND $privata == 1)
									{
										if (preg_match("/^[1-9][0-9]*$/", $_POST['selected_row']))
											{
												$res = mysql_query("SELECT * FROM `db_it_kt` WHERE `invent_id`='".$_POST['selected_row']."' ORDER BY `name` ASC ;");
												if (mysql_num_rows($res) > 0)
													{
														while ($row=mysql_fetch_array($res))
															{
																echo '<tr class="kt_info success">';
																echo '<td colspan="4" align="right"><b>'.$row['name'].'</b> SN: '.$row['sn'].'</td>';
																$specs_res = mysql_query("SELECT * FROM `db_it_specs` WHERE `kt_id`='".$row['id']."' ORDER BY `id` ASC ;");
																if (mysql_num_rows($specs_res) > 0)
																	{
																		echo '<td colspan="5">';
																		while ($specs_row=mysql_fetch_array($specs_res))
																			{
																				echo $specs_row['name'].' : <b>'.$specs_row['value'].'</b><br />';
																			}
																		echo '</td>';
																	}
																	else
																	{
																		echo '<td colspan="5">Характеристики відсутні</td>';
																	}
																echo '</tr>';
															}
													}
													else
													{
														echo '<tr class="kt_info success"><td colspan="9">КТ відсутня</td></tr>';
													}
											}
									}
								
								if ($_POST['act'] == 'load' AND $privata == 1)
									{
										if ($_POST['selected_kt_row'] == 0 AND $_POST['selected_pz_row'] == 0)
											{
												$res = mysql_query("SELECT * FROM `db_it_invent` ORDER BY `invent` ASC ;");
												if (mysql_num_rows($res) > 0)
													{
														$page .= file_get_contents("templates/jurnal_it_invent.html");
														$users = get_users_names(0);
														$rooms = get_rooms_names(0);
														$status = get_status_names(0);
														$tbody_content = '';
														$a = 0;
														$suma_all = 0;
														while ($row=mysql_fetch_array($res))
															{
																$a++;
																$suma_all = $suma_all + $row['suma'];
																$tbody_content .= '<tr class="default" id="row_'.$row['id'].'" onclick="select_one_row('.$row['id'].')" ondblclick="plus('.$row['id'].')">
																		<input id="hiden_status_id_'.$row['id'].'" value="'.$row['status_id'].'" type="hidden" class="hidden" />
																		<input id="hiden_user_id_'.$row['id'].'" value="'.$row['user_id'].'" type="hidden" class="hidden" />
																		<input id="hiden_room_id_'.$row['id'].'" value="'.$row['room_id'].'" type="hidden" class="hidden" />
																	<td id="invent_'.$row['id'].'">'.$row['invent'].'</td>
																	<td id="inv_plus_'.$row['id'].'">'.$row['inv_plus'].'</td>
																	<td id="name_'.$row['id'].'">'.$row['name'].'</td>
																	<td id="data_made_'.$row['id'].'" class="small_text">'.data_trans("mysql", "ua", $row['data_made']).'</td>
																	<td id="data_install_'.$row['id'].'" class="small_text">'.data_trans("mysql", "ua", $row['data_install']).'</td>
																	<td id="room_id_'.$row['id'].'">№'.$rooms[$row['room_id']]['nom'].' '.$rooms[$row['room_id']]['name'].'</td>
																	<td id="user_id_'.$row['id'].'">'.$users[$row['user_id']].'</td>
																	<td id="status_id_'.$row['id'].'" style="color: '.$status[$row['status_id']]['text_color'].'; background-color: '.$status[$row['status_id']]['bg_color'].';">'.$status[$row['status_id']]['name'].'</td>
																	<td id="suma_'.$row['id'].'">'.number_format($row['suma'], 2, ',', '').'</td>
																	<td id="amort_'.$row['id'].'">'.$row['amort'].'</td>
																</tr>';
															}
														$tbody_content .= '<tr><td colspan="5">Всього '.$a.' запис(ів)</td><td colspan="4" align="right">Всього <b>'.number_format($suma_all, 2, ',', ' ').'</b></td></tr>';
														$page = str_replace('{TBODY_CONTENT}', $tbody_content, $page);
													}
													else
													{
														echo '
														<font color="red">Відсутні записи</font>
														<hr>';
													}
											}
											else
											{
												if ($_POST['selected_kt_row'] != 0)
													{
														if (preg_match("/^[1-9][0-9]*$/", $_POST['selected_kt_row']))
															{
																$res = mysql_query("SELECT * FROM `db_it_kt` WHERE `invent_id`='".$_POST['selected_kt_row']."' ORDER BY `id` ASC ;");
																if (mysql_num_rows($res) > 0)
																	{
																		$kt_invent_id = $_POST['selected_kt_row'];
																		$page .= file_get_contents("templates/jurnal_it_kt.html");
																		$status = get_status_names(0);
																		$tbody_content = '';
																		$a = 0;
																		while ($row=mysql_fetch_array($res))
																			{
																				$spec_res = mysql_query("SELECT * FROM `db_it_specs` WHERE `kt_id`='".$row['id']."' ORDER BY `id` ASC ;");
																				if (mysql_num_rows($spec_res) > 0)
																					{
																						$spec_html = '<table class="table table-bordered">';
																						$spec_count = 0;
																						while ($spec_row=mysql_fetch_array($spec_res))
																							{
																								$spec_count++;
																								$spec_html .= '<tr>
																									<td class="spec_name_'.$row['id'].'">'.$spec_row['name'].'</td>
																									<td id="map_spec_value_'.$row['id'].'_'.$spec_count.'" class="spec_value_'.$row['id'].'">'.$spec_row['value'].'</td>
																								</tr>';
																							}
																						$spec_html .= '</table>';
																					}
																					else
																					{
																						$spec_html = '<div class="col-md-6">Характеристики відсутні</div>';
																					}

																				$a++;
																				$row['note'] = str_replace(array("\r\n", "\r", "\n"), "<br>", $row['note']);
																				
																				$tbody_content .= '<tr class="default" id="row_'.$row['id'].'" onclick="select_db_kt_row('.$row['id'].')" ondblclick="plus('.$row['id'].')">
																						<input id="hiden_status_1_id_'.$row['id'].'" value="'.$row['status_1_id'].'" type="hidden" class="hidden" />
																						<input id="hiden_status_2_id_'.$row['id'].'" value="'.$row['status_2_id'].'" type="hidden" class="hidden" />
																					<td id="name_'.$row['id'].'">'.$row['name'].'</td>
																					<td id="sn_'.$row['id'].'">'.$row['sn'].'</td>
																					<td id="data_made_'.$row['id'].'">'.$row['data_made'].'</td>
																					<td id="data_install_'.$row['id'].'">'.$row['data_install'].'</td>
																					<td id="status_1_id_'.$row['id'].'" style="color: '.$status[$row['status_1_id']]['text_color'].'; background-color: '.$status[$row['status_1_id']]['bg_color'].';">'.$status[$row['status_1_id']]['name'].'</td>
																					<td id="status_2_id_'.$row['id'].'" style="color: '.$status[$row['status_2_id']]['text_color'].'; background-color: '.$status[$row['status_2_id']]['bg_color'].';">'.$status[$row['status_2_id']]['name'].'</td>
																					<td id="func_'.$row['id'].'">'.$row['func'].'</td>
																					<td id="note_'.$row['id'].'">'.$row['note'].'</td>
																				</tr>
																				<tr class="default warning hidden" id="row_hidden_'.$row['id'].'" onclick="select_db_kt_row('.$row['id'].')" ondblclick="plus('.$row['id'].')">
																					<td colspan="8">
																						<div class="col-md-6">
																							'.$spec_html.'
																						</div>
																					</td>
																				</tr>
																				';
																			}
																		$tbody_content .= '<tr><td colspan="8">Всього '.$a.' запис(ів)</td></tr>';
																		$page = str_replace('{TBODY_CONTENT}', $tbody_content, $page);
																	}
																	else
																	{
																		echo '
																		<font color="red">Відсутня компютерна техніка</font>
																		<hr>';
																	}
															}
													}

												if ($_POST['selected_pz_row'] != 0)
													{
														if (preg_match("/^[1-9][0-9]*$/", $_POST['selected_pz_row']))
															{
																$res = mysql_query("SELECT * FROM `db_it_soft` WHERE `invent_id`='".$_POST['selected_pz_row']."' ORDER BY `name` ASC ;");
																if (mysql_num_rows($res) > 0)
																	{
																		$page .= file_get_contents("templates/jurnal_it_soft.html");
																		$tbody_content = '';
																		$a = 0;
																		while ($row=mysql_fetch_array($res))
																			{
																				$a++;
																				$tbody_content .= '<tr class="default" id="row_'.$row['id'].'" onclick="select_db_pz_row('.$row['id'].')" ondblclick="plus('.$row['id'].')">
																					<td id="name_'.$row['id'].'">'.$row['name'].'</td>
																					<td id="ver_'.$row['id'].'">'.$row['sn'].'</td>
																					<td id="data_'.$row['id'].'">'.$row['data'].'</td>
																					<td id="lic_'.$row['id'].'">'.$row['lic'].'</td>
																				</tr>
																				';
																			}
																		$tbody_content .= '<tr><td colspan="4">Всього '.$a.' ПЗ</td></tr>';
																		$page = str_replace('{TBODY_CONTENT}', $tbody_content, $page);
																	}
																	else
																	{
																		echo '
																		<font color="red">Відсутне програмне забезпечення</font>
																		<hr>';
																	}
															}
													}
											}
									}

								if ($_POST['act'] == 'loadhelp' AND $privata == 1)
									{
										$help_name = str_replace($srch, $rpls, $_POST['help_name']);
										$from = str_replace($srch, $rpls, $_POST['from']);
										$input = str_replace($srch, $rpls, $_POST['input']);
										$input = str_replace("*", "%", $_POST['input']);
										if ($help_name != "" AND $from != "" AND $input != "")
											{
												if ($_POST['selected_kt_row'] == 0 AND $_POST['selected_pz_row'] == 0)
													{
														$query = "SELECT `".$from."` FROM `db_it_invent` WHERE `".$from."` LIKE '".$input."%' ORDER BY `id` DESC LIMIT 100 ;";
													}
													else
													{
														if ($_POST['selected_kt_row'] != 0 AND preg_match("/^[1-9][0-9]*$/", $_POST['selected_kt_row']))
															{
																if (preg_match("/^spec_(name|value)_([1-9][0-9]*)$/", $from, $spec_array))
																	{
																		$query = "SELECT `".$spec_array[1]."` FROM `db_it_specs` WHERE `".$spec_array[1]."` LIKE '".$input."%' ORDER BY `id` DESC LIMIT 100 ;";
																		$from = 'spec_'.$spec_array[1].'_'.$spec_array[2];
																	}
																	else
																	{
																		$query = "SELECT `".$from."` FROM `db_it_kt` WHERE `".$from."` LIKE '".$input."%' ORDER BY `id` DESC LIMIT 100 ;";
																	}
															}
														if ($_POST['selected_pz_row'] != 0 AND preg_match("/^[1-9][0-9]*$/", $_POST['selected_pz_row']))
															{

																$query = "SELECT `".$from."` FROM `db_it_pz` WHERE `".$from."` LIKE '".$input."%' ORDER BY `id` DESC LIMIT 100 ;";
															}
													}
												$res = mysql_query($query) or die(mysql_error());
												if (mysql_num_rows($res) > 0)
													{
														$count = 0;
														$find = array();
														$result = "";
														while ($row=mysql_fetch_array($res))
															{
																if (preg_match("/^spec_(name|value)_([1-9][0-9]*)$/", $from, $spec_array))
																	{
																		$from_spec_name = 'spec_'.$spec_array[1].'_'.$spec_array[2];
																		$from_spec_value = $spec_array[1];
																		if (!in_array($row[$from_spec_value], $find))
																			{
																				$count++;
																				$find[] = $row[$from_spec_value];
																				echo "<a onclick=\"insertData('#".$from_spec_name."', '".$row[$from_spec_value]."');\">".$row[$from_spec_value]."</a><br />";
																				if ($count >= 10) exit;
																			}
																	}
																	else
																	{
																		if (!in_array($row[$from], $find))
																			{
																				$count++;
																				$find[] = $row[$from];
																				echo "<a onclick=\"insertData('#".$from."', '".$row[$from]."');\">".$row[$from]."</a><br />";
																				if ($count >= 10) exit;
																			}
																	}
															}
													}
													else
													{
														//echo $query;
													}
											}
									}

								if ($_POST['act'] == 'get_form_plus' AND $privata == 1)
									{
										if ($_POST['selected_kt_row'] == 0 AND $_POST['selected_pz_row'] == 0)
											{
												$page .= file_get_contents("templates/jurnal_it_invent_plus.html");
												$page = str_replace("{OPTIONS_USERS_ID}", get_users_selection_options(0, 0, "name", "ASC", 0), $page);
												$page = str_replace("{OPTIONS_ROOMS_ID}", get_rooms_selection_options(0, 0, "nom", "ASC"), $page);
												$page = str_replace("{OPTIONS_STATUS_ID}", get_status_selection_options(0, 0, "name", "ASC"), $page);
											}
											else
											{
												if ($_POST['selected_kt_row'] != 0)
													{
														if (preg_match("/^[1-9][0-9]*$/", $_POST['selected_kt_row']))
															{
																$page .= file_get_contents("templates/jurnal_it_invent_kt_plus.html");
																$status_2 = get_status_selection_options(0, 0, "name", "ASC");
																$page = str_replace("{OPTIONS_STATUS_1_ID}", $status_2, $page);
																$page = str_replace("{OPTIONS_STATUS_2_ID}", $status_2, $page);
															}
													}

												if ($_POST['selected_pz_row'] != 0)
													{
														if (preg_match("/^[1-9][0-9]*$/", $_POST['selected_pz_row']))
															{
																$page .= file_get_contents("templates/jurnal_it_invent_pz_plus.html");
																$status_2 = get_status_selection_options(0, 0, "name", "ASC");
																$page = str_replace("{OPTIONS_STATUS_1_ID}", $status_2, $page);
																$page = str_replace("{OPTIONS_STATUS_2_ID}", $status_2, $page);
															}
													}
											}
									}

								if ($_POST['act'] == 'edit')
									{
										$error = '';
										if ($privatb != 1) $error .= 'Відсутні права на редагування IT.<br />';
										$name = str_replace($srch, $rpls, $_POST['name']);
										$invent = str_replace($srch, $rpls, $_POST['invent']);
										$inv_plus = str_replace($srch, $rpls, $_POST['inv_plus']);
										$data_made = str_replace($srch, $rpls, $_POST['data_made']);
										$data_install = str_replace($srch, $rpls, $_POST['data_install']);
										$status_id = str_replace($srch, $rpls, $_POST['status_id']);
										$room_id = str_replace($srch, $rpls, $_POST['room_id']);
										$user_id = str_replace($srch, $rpls, $_POST['user_id']);
										$suma = str_replace($srch, $rpls, $_POST['suma']);
										$amort = str_replace($srch, $rpls, $_POST['amort']);
										if ($name == '') $error .= 'Не вказано назву інвентарного номеру.<br />';
										if (!preg_match("/^[1-9][0-9]*$/", $invent)) $error .= 'Не вірний інвентарний номер.<br />';
										if (!check_data(data_trans("ua", "mysql", $data_made))) $error .= "Не вірна дата виготовлення.<br />";
										if (!check_data(data_trans("ua", "mysql", $data_install))) $error .= "Не вірна дата встановлення.<br />";
										if (!preg_match("/^[1-9][0-9]*$/", $status_id)) $error .= 'Помилковий статус.<br />';
										if (!preg_match("/^[1-9][0-9]*$/", $room_id)) $error .= 'Помилковий кабінет.<br />';
										if (!preg_match("/^[1-9][0-9]*$/", $user_id)) $error .= 'Помилковий користувач.<br />';
										$suma = check_suma($suma);
										if ($suma == 0) $error .= 'Сума вказана не вірно<br />';
										if (!preg_match("/^[0-9]*$/", $amort) OR $amort > 100) $error .= 'Амортизація вказана не вірно.<br />';

										if ($error == '')
											{
												if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id']))
													{
														$res = mysql_query("SELECT * FROM `db_it_invent` WHERE `invent`='".$invent."' AND `inv_plus`='".$inv_plus."' AND `id`<>'".$_POST['edit_id']."' LIMIT 1;");
														if (mysql_num_rows($res) == 0)
															{
																mysql_query("UPDATE `db_it_invent` SET `name`='".$name."', `invent`='".$invent."', `inv_plus`='".$inv_plus."', `data_made`='".data_trans("ua", "mysql", $data_made)."', `data_install`='".data_trans("ua", "mysql", $data_install)."', `status_id`='".$status_id."', `room_id`='".$room_id."', `user_id`='".$user_id."', `suma`='".$suma."', `amort`='".$amort."' WHERE `id`='".$_POST['edit_id']."' LIMIT 1;") or die(mysql_error());
																echo '
																	<font color="green"><strong>Дані оновлено</strong></font>
																	<hr>
																	<center>
																		<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
																	</center>';
															}
															else
															{
																$error = 1;
															}
													}
													else
													{
														$res = mysql_query("SELECT * FROM `db_it_invent` WHERE `invent`='".$invent."' AND `inv_plus`='".$inv_plus."' LIMIT 1;");
														if (mysql_num_rows($res) == 0)
															{
																mysql_query("INSERT INTO `db_it_invent` (`id`, `invent`, `inv_plus`, `name`, `data_made`, `data_install`, `status_id`, `room_id`, `user_id`, `suma`, `amort`)
																VALUES (NULL, '".$invent."', '".$inv_plus."', '".$name."', '".data_trans("ua", "mysql", $data_made)."', '".data_trans("ua", "mysql", $data_install)."', '".$status_id."', '".$room_id."', '".$user_id."', '".$suma."', '".$amort."')") or die(mysql_error());
																echo '
																	<font color="green"><strong>Дані записано</strong></font>
																	<hr>
																	<center>
																		<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
																	</center>';
															}
															else
															{
																$error = 1;
															}
													}
												if ($error == 1)
													{
														echo '
														<font color="red">Інвентарний номер вже існує</font>
														<hr>
														<center>
															<button onclick="$(\'#informer\').modal(\'hide\'); $(\'#invent_modal\').modal(\'show\');" type="button" class="btn btn-danger" data-dismiss="modal">Повернутись</button>
														</center>';
													}
											}
											else
											{
												echo '
												<font color="red">'.$error.'</font>
												<hr>
												<center>
													<button onclick="$(\'#informer\').modal(\'hide\'); $(\'#invent_modal\').modal(\'show\');" type="button" class="btn btn-danger" data-dismiss="modal">Повернутись</button>
												</center>';
											}
									}

								if ($_POST['act'] == 'delete' AND $privatb == 1)
									{
										if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id']))
											{
												$res = mysql_query("SELECT * FROM `db_it_invent` WHERE `id`='".$_POST['edit_id']."' LIMIT 1;");
												if (mysql_num_rows($res) > 0)
													{
														$fint_kt_res = mysql_query("SELECT `id` FROM `db_it_kt` WHERE `invent_id`='".$_POST['edit_id']."' ;");
														if (mysql_num_rows($fint_kt_res) > 0)
															{
																while ($fint_kt_row=mysql_fetch_array($fint_kt_res))
																	{
																		mysql_query("DELETE FROM `db_it_specs` WHERE `kt_id`='".$fint_kt_row['id']."' ;") or die(mysql_error());
																	}
																mysql_query("DELETE FROM `db_it_kt` WHERE `invent_id`='".$_POST['edit_id']."' ;") or die(mysql_error());
															}
														mysql_query("DELETE FROM `db_it_invent` WHERE `id`='".$_POST['edit_id']."' LIMIT 1;") or die(mysql_error());
														echo '
															<font color="green"><strong>Дані вилучено успішно</strong></font>
															<hr>
															<center>
																<button onclick="$(\'#informer\').modal(\'hide\'); load();" type="button" class="btn btn-success" data-dismiss="modal">Закрити</button>
															</center>';
													}
													else
													{
														echo '
														<font color="red">Запис не існує, або був вилучений раніше.</font>
														<hr>';
													}
											}
									}
							}
					}
			}
	}
	else
	{
		$page .= "Помилка авторизації, поверніться до головної сторінки.";
	}

if ($page != '')
	{
		include_once ("inc/lang/".$c_lng.".php");
		foreach ($lang as $key => $value)
			{
				$page = str_replace("{".$key."}", $value, $page);
			}
		echo $page;
	}
?>
