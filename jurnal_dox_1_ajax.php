<?php
include ('inc/config.php');

Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache");
Header("Content-Type: text/javascript; charset=utf-8");

if (isset($_SESSION['user_id']))
	{
		if (isset($_POST['act']))
			{
				switch ($_POST['act'])
					{
						case "add" :
							Add($_SESSION['user_id'], $_SERVER['REMOTE_ADDR'], $privat9, $srch, $rpls, $user_p_mod);
							break;
						case "del" :
							Del($_SESSION['user_id'], $_SERVER['REMOTE_ADDR'], $privat8, $user_p_mod);
							break;
						case "load" :
							Load($_SESSION['user_year'], $privat8, $c_lmt);
							break;
						case "loadhelp" :
							LoadHelp($_SESSION['user_year'], $privat8, $privat9, $srch, $rpls);
							break;
						default :
							echo time();
					}
			}
	}
	else
	{
		echo "Помилка авторизації, поверніться до головної сторінки.";
	}

function Del($user_id, $user_ip, $view_permission, $user_p_mod)
	{
		if (preg_match("/^[1-9][0-9]*$/", $_POST['del_id']))
			{
				if (date('Y') == $_SESSION['user_year'])
					{
						$res = mysql_query("SELECT * FROM `db_".date('Y')."_dox_1` WHERE `id`='".$_POST['del_id']."' LIMIT 1 ;");
						if (mysql_num_rows($res) == 1)
							{
								$query = "SELECT MAX(`id`) FROM `db_".date('Y')."_dox_1`";
								$query = mysql_fetch_row(mysql_query($query));
								if ($query[0] == $_POST['del_id'])
									{
										$row=mysql_fetch_row($res);
										if ($row['add_user'] == $add_user OR $user_p_mod == 1)
											{
												mysql_query("DELETE FROM `db_".date('Y')."_dox_1` WHERE `id`='".$_POST['del_id']."' LIMIT 1 ;") or die(mysql_error());
												mysql_query("ALTER TABLE `db_".date('Y')."_out` AUTO_INCREMENT =".$_POST['del_id']." ;") or die(mysql_error());
												echo "<div id=\"result_html\"><hr>Результат: <font color=green>Дані вилучено</font> №<strong>".$_POST['del_id']."</strong>.<hr></div>";
												echo "<input id=\"result\" value=\"delSuccess\" class=\"hidden\" type=\"hidden\"/>";
											}
											else
											{
												echo "<div id=\"result_html\"><hr>Результат: <font color=red>Дані не вилучено</font> Ви не автор №<strong>".$_POST['del_id']."</strong> та не являєтесь модератором.<hr></div>";
												echo "<input id=\"result\" value=\"delError\" class=\"hidden\" type=\"hidden\"/>";
											}
									}
									else
									{
										echo "<div id=\"result_html\"><hr>Результат: <font color=red>Дані не вилучено</font> №<strong>".$_POST['del_id']."</strong> не останній, дозволено вилучати тільки останній номер.<hr></div>";
										echo "<input id=\"result\" value=\"delError\" class=\"hidden\" type=\"hidden\"/>";
									}
							}
							else
							{
								echo "<div id=\"result_html\"><hr>Результат: <font color=red>Дані не вилучено</font> №<strong>".$_POST['del_id']."</strong> Не знайдено в базі даних.<hr></div>";
								echo "<input id=\"result\" value=\"delError\" class=\"hidden\" type=\"hidden\"/>";
							}
					}
					else
					{
						echo "<div id=\"result_html\"><hr>Результат: <font color=red>Дані не вилучено</font> Вилучати дані дозволено тільки в поточному році.<hr></div>";
						echo "<input id=\"result\" value=\"delError\" class=\"hidden\" type=\"hidden\"/>";
					}
			}
			else
			{
				echo "<div id=\"result_html\"><hr>Результат: <font color=red>Дані не вилучено</font> не вірний №.<hr></div>";
				echo "<input id=\"result\" value=\"delError\" class=\"hidden\" type=\"hidden\"/>";
			}
	}
	
function LoadHelp($view_year, $view_permission, $add_permission, $srch, $rpls)
	{
		if ($view_permission == 1 AND $add_permission == 1)
			{
				$help_name = str_replace($srch, $rpls, $_POST['help_name']);
				$from = str_replace($srch, $rpls, $_POST['from']);
                $input = str_replace($srch, $rpls, $_POST['input']);
                $input = str_replace("*", "%", $_POST['input']);
				if ($help_name != "" AND $from != "" AND $input != "")
					{
						$query = "SELECT `".$from."` FROM `db_".$view_year."_dox_1` WHERE `".$from."` LIKE '".$input."%' ORDER BY `id` DESC LIMIT 100 ;";
						$res = mysql_query($query) or die(mysql_error());
						if (mysql_num_rows($res) > 0)
							{
								$count = 0;
								$find = array();
								$result = "";
								while ($row=mysql_fetch_array($res))
									{
										if (!in_array($row[$from], $find))
											{
												$count++;
												$find[] = $row[$from];
												echo "<a href=\"#\" onclick=\"insertData('#".$from."', '".$row[$from]."');\">".$row[$from]."</a><br />";
												if ($count >= 10) exit;
											}
									}
							}
					}
			}
	}

function Load($view_year, $view_permission, $c_lmt)
	{
		if ($view_permission == 1)
			{
				$sort = 'id';
				$asc_desc = 'DESC';
				$limit = "LIMIT ".$c_lmt;
				if ($_POST['limit'] == '0') $limit = '';
				if ($_POST['asc_desc'] == 'asc') $asc_desc = 'ASC';
				if ($_POST['sort'] == 'vys_nom') $sort = 'vys_nom';
				if ($_POST['sort'] == 'vys_data') $sort = 'vys_data';
				if ($_POST['sort'] == 'plat_cod') $sort = 'plat_cod';
				if ($_POST['sort'] == 'plat_name') $sort = 'plat_name';
				if ($_POST['sort'] == 'kbk') $sort = 'kbk';
				if ($_POST['sort'] == 'suma') $sort = 'suma';
				if ($_POST['sort'] == 'from_rah') $sort = 'from_rah';
				if ($_POST['sort'] == 'to_rah') $sort = 'to_rah';
				if ($_POST['sort'] == 'new_plat') $sort = 'new_plat';
				
				$res = mysql_query("SELECT * FROM `db_".$view_year."_dox_1` ORDER BY `".$sort."` ".$asc_desc." ".$limit." ;");
				if (mysql_num_rows($res) > 0)
					{
						$a = 0;
						$suma_all = 0;
						$table = '';
						while ($row=mysql_fetch_array($res))
                            {
								$a++;
								$del = 0;
								if ($a == 1) $del = 1;
								$suma_all = $suma_all + $row["suma"];
								$table .= '
	<tr id="tr_'.$row["id"].'" onclick="showInfo('.$row["id"].')">
		
			<input id="del_'.$row["id"].'" value="'.$del.'" class="hidden" type="hidden" />
			<input id="add_user_'.$row["id"].'" value="'.$row["add_user"].'" class="hidden" type="hidden" />
			<input id="add_time_'.$row["id"].'" value="'.$row["add_time"].'" class="hidden" type="hidden" />
			<input id="add_ip_'.$row["id"].'" value="'.$row["add_ip"].'" class="hidden" type="hidden" />
			<input id="edit_user_'.$row["id"].'" value="'.$row["edit_user"].'" class="hidden" type="hidden" />
			<input id="edit_time_'.$row["id"].'" value="'.$row["edit_time"].'" class="hidden" type="hidden" />
			<input id="edit_ip_'.$row["id"].'" value="'.$row["edit_ip"].'" class="hidden" type="hidden" />
		
		<td>'.$row["id"].'</td>
		<td class="small_text" id="vys_nom_'.$row["id"].'">'.$row["vys_nom"].'</td>
		<td class="small_text" id="vys_data_'.$row["id"].'">'.data_trans("mysql", "ua", $row["vys_data"]).'</td>
		<td id="plat_cod_'.$row["id"].'">'.$row["plat_cod"].'</td>
		<td class="small_text" id="plat_name_'.$row["id"].'">'.$row["plat_name"].'</td>
		<td class="small_text" id="kbk_'.$row["id"].'">'.$row["kbk"].'</td>
		<td id="suma_'.$row["id"].'">'.number_format($row["suma"], 2, ',', '').'</td>
		<td id="from_rah_'.$row["id"].'">'.$row["from_rah"].'</td>
		<td id="to_rah_'.$row["id"].'">'.$row["to_rah"].'</td>
		<td class="small_text" id="new_plat_'.$row["id"].'">'.$row["new_plat"].'</td>
	</tr>';
							}
						echo $table;
						echo '<tr><td colspan="6" align="left">Показано <strong>'.$a.'</strong> зиписи(ів)</td><td colspan="4">Разом <strong>'.number_format($suma_all, 2, ',', ' ').'</strong></td>';
						//echo '<tr><td colspan="10" align="left">Debug: sort:'.$sort.'. asc_desc:'.$asc_desc.'.</td>';
					}
					else
					{
						echo '<tr><td colspan="10" align="center" style="border-left: 1px solid #111; border-right: 1px solid #111; border-bottom: 1px solid #111;">Записи відсутні</td></tr>';
					}
			}
			else
			{
				echo '<tr><td colspan="10" align="center" style="border-left: 1px solid #111; border-right: 1px solid #111; border-bottom: 1px solid #111;">Відсутні права на перегляд</td></tr>';
			}
	}

function Add($add_user, $add_ip, $add_permission, $srch, $rpls, $user_p_mod)
	{
		$error = "";
		if (!preg_match("/^[1-9][0-9]*$/", $add_user)) $error .= "Помилка авторизації.<br />";
		if ($add_ip == '') $error .= "Помилка визначення IP адреси.<br />";
		if ($add_permission != 1) $error .= "Нема прав на додавання доходів.<br />";
		if ($error == "")
			{
				$vys_nom = str_replace($srch, $rpls, $_POST['vys_nom']);
				$vys_data = str_replace($srch, $rpls, $_POST['vys_data']);
				$plat_cod = str_replace($srch, $rpls, $_POST['plat_cod']);
				$plat_name = str_replace($srch, $rpls, $_POST['plat_name']);
				$kbk = str_replace($srch, $rpls, $_POST['kbk']);
				$suma = str_replace($srch, $rpls, $_POST['suma']);
				$from_rah = str_replace($srch, $rpls, $_POST['from_rah']);
				$to_rah = str_replace($srch, $rpls, $_POST['to_rah']);
				$new_plat = str_replace($srch, $rpls, $_POST['new_plat']);
				if ($vys_nom == "") $error .= "Не вказано номер висновку<br />";
				if ($vys_data == '') $vys_data = 0;
				if (!check_data(data_trans("ua", "mysql", $vys_data))) $error .= "Не вірна дата висновку<br />";
				if ($plat_cod == "") $error .= "Не вказано код платника<br />";
				if ($plat_name == "") $error .= "Не вказано платника<br />";
				if ($kbk == "") $error .= "Не вказано код бюджетної класифікації доходів<br />";
				if (!preg_match("/^[1-9][0-9]*$/", $from_rah)) $error .= "Не вказано з якого рахунку<br />";
				if (!preg_match("/^[1-9][0-9]*$/", $to_rah)) $error .= "Не вказано на який рахунок<br />";
				if ($new_plat == "") $error .= "Не вказано платіжне доручення<br />";
				$suma = check_suma($suma);
				if ($suma == 0) $error .= 'Сума вказана не вірно<br />';
				
				// echo "DEBUG ".date('H:i:s').".<hr>vys_nom=".$vys_nom.".<br>vys_data=".$vys_data.".<br>plat_cod=".$plat_cod.".<br>plat_name=".$plat_name.".<br>kbk=".$kbk.".<br>suma=".$suma.".<br>from_rah=".$from_rah.".<br>to_rah=".$to_rah.".<br>new_plat=".$new_plat.".<hr><font color=red>".$error."</font>";
				
				if ($error == "")
					{
						if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id']))
							{
								$edit_id = $_POST['edit_id'];
								if (date('Y') == $_SESSION['user_year'])
									{
										$res = mysql_query("SELECT * FROM `db_".date('Y')."_dox_1` WHERE `id`='".$edit_id."' LIMIT 1 ;");
										if (mysql_num_rows($res) == 1)
											{
												while ($row=mysql_fetch_array($res))
													{
														if ($row['add_user'] == $add_user OR $user_p_mod == 1)
															{
																mysql_query("UPDATE `db_".date('Y')."_dox_1` SET `vys_nom`='".$vys_nom."', `vys_data`='".data_trans("ua", "mysql", $vys_data)."', `plat_cod`='".$plat_cod."', `plat_name`='".$plat_name."', `kbk`='".$kbk."', `suma`='".$suma."', `from_rah`='".$from_rah."', `to_rah`='".$to_rah."', `new_plat`='".$new_plat."', `edit_user`='".$add_user."', `edit_time`='".date('Y-m-d H:i:s')."', `edit_ip`='".$add_ip."' WHERE `id`='".$edit_id."' LIMIT 1 ;") or die(mysql_error());
																echo "<hr>Результат: <font color=green>Дані оновлено</font> № <strong>".$edit_id."</strong>. Висновок №<strong>".$vys_nom."</strong> від <strong>".$vys_data."</strong> на суму <strong>".$suma."</strong><hr>";
																echo "<input id=\"result\" value=\"editSuccess\" class=\"hidden\" type=\"hidden\"/>";
																echo "<input id=\"editId\" value=\"".$edit_id."\" class=\"hidden\" type=\"hidden\"/>";
															}
															else
															{
																echo "<hr>Результат: <font color=red>Дані не оновлено</font> Ви не автор № <strong>".$edit_id."</strong> та права модератора відсутні.<hr>";
																echo "<input id=\"result\" value=\"editError\" class=\"hidden\" type=\"hidden\"/>";
																echo "<input id=\"reason\" value=\"noModer\" class=\"hidden\" type=\"hidden\"/>";
															}
													}
											}
											else
											{
												echo "<hr>Результат: <font color=red>Дані не оновлено</font> № <strong>".$edit_id."</strong> Не існує.<hr>";
												echo "<input id=\"result\" value=\"editError\" class=\"hidden\" type=\"hidden\"/>";
												echo "<input id=\"reason\" value=\"noExists\" class=\"hidden\" type=\"hidden\"/>";
											}
									}
									else
									{
										echo "<hr>Результат: <font color=red>Дані не оновлено</font> <strong>Редагувати номери минулого року заборонено</strong>.<hr>";
										echo "<input id=\"result\" value=\"editError\" class=\"hidden\" type=\"hidden\"/>";
										echo "<input id=\"reason\" value=\"lastYear\" class=\"hidden\" type=\"hidden\"/>";
									}
							}
							else
							{
								mysql_query("INSERT INTO `db_".date('Y')."_dox_1` (`id`, `add_user`, `add_time`, `add_ip`, `vys_nom`, `vys_data`, `plat_cod`, `plat_name`, `kbk`, `suma`, `from_rah`, `to_rah`, `new_plat`) 
								VALUES (NULL, '".$add_user."', '".date('Y-m-d H:i:s')."', '".$add_ip."', '".$vys_nom."', '".data_trans("ua", "mysql", $vys_data)."', '".$plat_cod."', '".$plat_name."', '".$kbk."', '".$suma."', '".$from_rah."', '".$to_rah."', '".$new_plat."')") or die(mysql_error());
								echo "<hr>Результат: <font color=green>Дані записано</font> під № <strong>".mysql_insert_id()."</strong>. Висновок №<strong>".$vys_nom."</strong> від <strong>".$vys_data."</strong> на суму <strong>".$suma."</strong><hr>";
								echo "<input id=\"result\" value=\"addSuccess\" class=\"hidden\" type=\"hidden\"/>";
								echo "<input id=\"new_id\" value=\"".mysql_insert_id()."\" class=\"hidden\" type=\"hidden\"/>";
							}
					}
					else
					{
						echo "<hr>Результат:<br /><font color=red>".$error."</font><hr>";
						echo "<input id=\"result\" value=\"addError\" class=\"hidden\" type=\"hidden\"/>";
						if (preg_match("/^[1-9][0-9]*$/", $_POST['edit_id'])) echo "<input id=\"editId\" value=\"".$_POST['edit_id']."\" class=\"hidden\" type=\"hidden\"/>";
					}
			}
			else
			{
				echo $error;
				echo "<input id=\"result\" value=\"addError\" class=\"hidden\" type=\"hidden\"/>";
			}
	}
?>
