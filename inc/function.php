<?phpfunction get_users_selection_options($selected, $disabled, $order_by, $asc_or_desc, $deleted)	{		/*			Отримання випадаючого списку користувачів				До функції:					$disabled = array(); або $disabled = (1, 3, 8); - відмічаєм блокованих користувачів, це масів.					$selected = 0; - не ставити вибраного користувача, $selected = 5; - вибрати користувача ID = 5.					$order_by = "name"; - сорутвати по імені, id - по ІД.					$asc_or_desc = "DESC"; - сортувати за спаданням Я-А, ASC - зростання А-Я.					$deleted:						0 - показати всіх не вилучених						1 - показати всіх вилучених						-1 - показати всіх		Визов ф-ції:						$disabled = 0; (вимкнути блокування)						$disabled = array(1, 3); (блокування ID-1 та ID-3)					$users = get_users_selection_options(5, 0, "name", "ASC", 0);					вибрати із списку користувача з ID=5, нікого не блокувати, сортувати по імені із зростанням А-Я., показати всіх не вилучених.		*/		$where_del = " WHERE `del`='0' ";		if ($deleted == -1) $where_del = "";		if ($deleted == 1) $where_del = " WHERE `del`='1' ";		$asc_desc = "ASC";		if ($asc_or_desc == "DESC") $asc_desc = "DESC";		$query = "SELECT * FROM `users` ".$where_del." ORDER BY `".$order_by."` ".$asc_desc." ; ";		$res = mysql_query($query) or die(mysql_error());		$html = "";		while ($row=mysql_fetch_array($res))			{				$is_selected = "";				if ($selected == $row['id']) $is_selected = "selected";				$is_disabled = "";				if ($disabled != 0 AND in_array($row['id'], $disabled)) $is_disabled = "disabled";				$html .= "<option value=\"".$row['id']."\" ".$is_selected." ".$is_disabled.">".$row['name']."</option>\n";				$row['id'] = $row['name'];			}		return $html;	}function check_data($data)	{		// Перевірка дати по календарю if (check_data(2015-12-31)) die("Все супер");		// Можна з часом if (check_data(2015-12-31 23:59:59)) die("Все супер");		$result = true;		$tmp = explode(" ", $data);		if (!empty($tmp[1]) AND !preg_match("/^(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|[0-5][0-9]):(0[0-9]|[0-5][0-9])$/", $tmp[1])) $result = false;		$data = explode("-", $tmp[0]);		if (!preg_match("/^([1-9][0-9]{3}|[2-9]{1}[0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/" ,$tmp[0])) $result = false;		$data = explode("-", $tmp[0]);		if (!checkdate($data[1], $data[2], $data[0])) $result = false;		return $result;	}function get_navy($table, $where, $order, $active, $page_limit, $url)	{		// Тянем навігацію на основі пошуку втому числі без нього		$query = "SELECT `id` FROM `".$table."` ".$where." ".$order." ; ";		$res = mysql_query($query) or die(mysql_error());		$row_num = (mysql_num_rows($res) / $page_limit);		$rows = round($row_num, 0);		if ($rows < $row_num) $rows++;		$navy = "";		$tmp_start = $active - 3;		$tmp_end = $active + 3;		$tmp_a = 0;		for ($i = 1; $i <= $rows; $i++)			{				$style_btn = "default";				if ($i == $active) $style_btn = "primary";    			$tmp_show = 0;    			if ($i < 2) $tmp_show = 1; // $i < 3    			if ($i > ($rows - 1)) $tmp_show = 1; // $rows - 4    			if ($i > $tmp_start AND $i < $tmp_end) $tmp_show = 1;    			if ($tmp_show == 0)    				{    					if (!isset($tmp_dot_start) AND $i < $active)    						{    							$tmp_dot_start = 1;    							$navy .= " ... ";    						}    					if (!isset($tmp_dot_end) AND $i > $active)    						{    							$tmp_dot_end = 1;    							$navy .= " ... ";    						}    				}    			if ($tmp_show == 1)					{						$tmp_a++;						$navy .= "<a class=\"btn btn-".$style_btn." btn-xs\" href=\"".$url . $i ."\" role=\"button\">".$i."</a> ";					}			}		$tmp_b = "<kbd>Показано {NAVY_INSERT_SHOW} із ".mysql_num_rows($res)."</kbd>";		if ($tmp_a == 1)			{				return $tmp_b;			}			else			{				return $navy." ".$tmp_b;			}	}function get_users_names($user)	{		// Шукаєм імя користувача по ИД, якщо 0 - то отримуєм масів всіх імен		if ($user == 0)			{				$users = array();				$query_users = "SELECT `id`,`name` FROM `users` ORDER BY `name` ; ";				$res_users = mysql_query($query_users) or die(mysql_error());				while ($row_users=mysql_fetch_array($res_users))					{						$users[$row_users['id']] = $row_users['name'];					}				return $users;			}		if ($user > 0)			{				if (preg_match("/^[1-9][0-9]*$/", $user))					{						$query_user = "SELECT `id`,`name` FROM `users` WHERE `id`='".$user."' LIMIT 1 ; ";						$res_user = mysql_query($query_user) or die(mysql_error());						if (mysql_num_rows($res_user) == 1)							{								while ($row_user=mysql_fetch_array($res_user))								{									$name = $row_user['name'];								}							}							else							{								$name = "{LANG_USER_NOT_FOUND}";							}						return $name;					}			}	}function data_trans($a, $b, $data)	{		// Трансформуєм формат дати з $a в $b.		$data = str_replace(",", ".", $data);		$tmp_space = explode(" ", $data);		if (!empty($tmp_space[1])) $tmp_space[1] = " ".$tmp_space[1];		if ($a == "mysql")			{				$tmp = explode("-", $tmp_space[0]);				$year = $tmp[0];				$mon = $tmp[1];				$day = $tmp[2];			}		if ($a == "ua")			{				$tmp = explode(".", $tmp_space[0]);				$year = $tmp[2];				$mon = $tmp[1];				$day = $tmp[0];			}		if ($b == "mysql") $data = $year."-".$mon."-".$day . $tmp_space[1];		if ($b == "ua") $data = $day.".".$mon.".".$year . $tmp_space[1];		if ($data == "..") $data = "";		return $data;	}?>