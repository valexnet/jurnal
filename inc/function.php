<?phpfunction get_mailto_subject($c_n_ray, $nom_str, $nom_index, $nomer, $FORM_TO_SUBJ)	{		// Формуємо Тему для листа.		return $c_n_ray."_".$nom_str."-".$nom_index."_".$nomer." (".$FORM_TO_SUBJ.")";	}function get_mailto_body($c_nam, $print_nomer, $user_name, $data, $c_ver, $c_ver_alt)	{		// Формуємо Тему для листа.		$data = str_replace(" ", "%20", $data);		return "%0A".$c_nam."%0A%0AВихідний%20номер:%20".$print_nomer.".%0AЗареєстрував:%20".$user_name."%0AДата,%20час:%20".$data.".%0A%0A----------%0AПiдготовлено%20АС%20Журнал%0AВерсiя: ".$c_ver.".".$c_ver_alt;	}function get_index_module($year,$where,$index,$str,$nom,$usr,$module)    {		// Формуємо індекс документа згідно шаблону із глобальних налаштувань.		$module = str_replace("[year]", $year, $module);		$module = str_replace("[index]", $index, $module);		$module = str_replace("[str]", $str, $module);		$module = str_replace("[nom]", $nom, $module);		$module = str_replace("[usr-id]", $usr, $module);		return $module;	}function get_inform_users_list($list)    {        // Отримуємо список користувачів для інформування по новому вхідному листу.        $query = "SELECT `id`,`name` FROM `users` WHERE `del`='0' ORDER BY `name`; ";        $res = mysql_query($query) or die(mysql_error());        $html = "<div class=\"row\">";        if     ($list == "0")            {                $a = 0;                while ($row=mysql_fetch_array($res))                    {                        $a++;                        $html .= "<div class=\"col-md-4\">                                    <label class=\"btn btn-default btn-block\" id=\"l_".$row['id']."\">                                        <input name=\"inform_users[]\" value=\"".$row['id']."\" id=\"cb_".$row['id']."\" type=\"checkbox\" onchange=\"ccu('".$row['id']."')\">                                        <strong id=\"name_".$row['id']."\">".$row['name']."</strong>                                    </label>                                </div>\n";                        if ($a >= 3)                            {                                $html .= "</div><hr></hr><div class=\"row\">";                                $a = 0;                            }                    }                $html .= "</div>";            }            else            {                $a = 0;                $users = explode(",", $list);                $exist_users = array();                $insert_script = "";                foreach ($users as $user)                    {                        $user_id = explode("-", $user);                        if (preg_match("/^[1-9][0-9]*$/", $user_id[0])) $exist_users[] = $user_id[0];                    }                while ($row=mysql_fetch_array($res))                    {                        $a++;                        $class_btn = "btn btn-default btn-block";                        $cheaked_btn = "";                        if (in_array($row['id'], $exist_users))                            {                                $cheaked_btn = "checked";                                $class_btn = "btn btn-info btn-block";                                $insert_script .= "document.getElementById('show_list_selected_inform_users').innerHTML += '<div id=\"sh_' + ".$row['id']." + '\">' + document.getElementById('name_' + ".$row['id'].").outerHTML + '</div>';";                            }                        $html .= "<div class=\"col-md-4\">                                    <label class=\"".$class_btn."\" id=\"l_".$row['id']."\">                                        <input name=\"inform_users[]\" value=\"".$row['id']."\" id=\"cb_".$row['id']."\" type=\"checkbox\" ".$cheaked_btn." onchange=\"ccu('".$row['id']."')\">                                        <strong id=\"name_".$row['id']."\">".$row['name']."</strong>                                    </label>                                </div>\n";                        if ($a >= 3)                            {                                $html .= "</div><hr></hr><div class=\"row\">";                                $a = 0;                            }                    }                $html .= "</div>                <script>                    <!--                " . $insert_script . "                    //-->                </script>";            }        return $html;    }function getParts($object, & $parts)    {        // Отримуємо всі куски листа, якщо він не один.        if ($object->type == 1)            {                foreach ($object->parts as $part)                    {                        getParts($part, $parts);                    }            }            else            {                $p['type'] = $object->type;                $p['encode'] = $object->encoding;                $p['subtype'] = $object->subtype;                $p['bytes'] = $object->bytes;                if ($object->ifparameters == 1)                    {                        foreach ($object->parameters as $param)                            {                                $p['params'][] = array('attr' => $param->attribute,    'val' => $param->value);                            }                    }                if ($object->ifdparameters == 1)                    {                        foreach ($object->dparameters as $param)                            {                                $p['dparams'][] = array('attr' => $param->attribute, 'val' => $param->value);                            }                    }                $p['disp'] = null;                if ($object->ifdisposition == 1)                    {                        $p['disp'] = $object->disposition;                    }                $parts[] = $p;            }    }function get_blank_change($id, $do_blank)    {        /*            У випадках коли користувач помилково відмітив або забув відмітити реєстрацію бланків,            можна виправити ситуацію власноруч, не звертаючись до Адміністратора БД.                Шукаємо $id і дивимось що треба зробить по $do_blank - 0 знять, 1 поставить.                Виводимо 0 якщо зміни не можливі, та $html - номер бланку, якщо потрібно поставить. -1 якщо можна вилучить.        */        $html = 0;        if (date('Y') == $_SESSION['user_year'] AND preg_match("/^[1-9][0-9]*$/", $id) AND preg_match("/^[0-1]$/", $do_blank))            {                // Перевіряємо наявність номеру в БД                $query = "SELECT `id` FROM `db_".date('Y')."_out` WHERE `id`='".$id."' LIMIT 1 ; ";                $res = mysql_query($query) or die(mysql_error());                if (mysql_num_rows($res) == 1)                    {                        // Шукаємо останній номер бланку                        $query = "SELECT `id`,`blank` FROM `db_".date('Y')."_out` WHERE `blank` IS NOT NULL ORDER BY `id` DESC LIMIT 1 ; ";                        $res = mysql_query($query) or die(mysql_error());                        while ($row=mysql_fetch_array($res))                            {                                $blank_id = $row['id'];                                $blank_num = $row['blank'];                            }                        // Якщо відсутні бланки, та потрібно поставить номер                        if (!isset($blank_id) AND $do_blank == 1) $html = 1;                        // Якщо номер вихідного більший від номера вихідного з бланком і потрібно поставить номер бланку.                        if ($id > $blank_id AND $do_blank == 1) $html = ($blank_num + 1);                        // Якщо номер бланку останній, можем його зняти.                        if ($id == $blank_id AND $do_blank == 0) $html = -1;                    }            }        return $html;    }function get_users_selection_options($selected, $disabled, $order_by, $asc_or_desc, $deleted)    {        /*            Отримання випадаючого списку користувачів                До функції:                    $disabled = array(); або $disabled = (1, 3, 8); - відмічаєм блокованих користувачів, це масів.                    $selected = 0; - не ставити вибраного користувача, $selected = 5; - вибрати користувача ID = 5.                    $order_by = "name"; - сорутвати по імені, id - по ІД.                    $asc_or_desc = "DESC"; - сортувати за спаданням Я-А, ASC - зростання А-Я.                    $deleted:                        0 - показати всіх не вилучених                        1 - показати всіх вилучених                        -1 - показати всіх        Визов ф-ції:                        $disabled = 0; (вимкнути блокування)                        $disabled = array(1, 3); (блокування ID-1 та ID-3)                    $users = get_users_selection_options(5, 0, "name", "ASC", 0);                    вибрати із списку користувача з ID=5, нікого не блокувати, сортувати по імені із зростанням А-Я., показати всіх не вилучених.        */        $where_del = " WHERE `del`='0' ";        if ($deleted == -1) $where_del = "";        if ($deleted == 1) $where_del = " WHERE `del`='1' ";        $asc_desc = "ASC";        if ($asc_or_desc == "DESC") $asc_desc = "DESC";        $query = "SELECT * FROM `users` ".$where_del." ORDER BY `".$order_by."` ".$asc_desc." ; ";        $res = mysql_query($query) or die(mysql_error());        $html = "";        while ($row=mysql_fetch_array($res))            {                $is_selected = "";                if ($selected == $row['id']) $is_selected = "selected";                $is_disabled = "";                if ($disabled != 0 AND in_array($row['id'], $disabled)) $is_disabled = "disabled";                $html .= "<option value=\"".$row['id']."\" ".$is_selected." ".$is_disabled.">".$row['name']."</option>\n";                $row['id'] = $row['name'];            }        return $html;    }function check_data($data)    {        // Перевірка дати по календарю if (check_data(2015-12-31)) die("Все супер");        // Можна з часом if (check_data(2015-12-31 23:59:59)) die("Все супер");        $result = true;        $tmp = explode(" ", $data);        if (!empty($tmp[1]) AND !preg_match("/^(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|[0-5][0-9]):(0[0-9]|[0-5][0-9])$/", $tmp[1])) $result = false;        $data = explode("-", $tmp[0]);        if (!preg_match("/^([1-9][0-9]{3}|[2-9]{1}[0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/" ,$tmp[0])) $result = false;        $data = explode("-", $tmp[0]);        if (!checkdate($data[1], $data[2], $data[0])) $result = false;        return $result;    }function get_navy($table, $where, $order, $active, $page_limit, $url)    {        // Тянем навігацію на основі пошуку втому числі без нього        $query = "SELECT `id` FROM `".$table."` ".$where." ".$order." ; ";        $res = mysql_query($query) or die(mysql_error());        $row_num = (mysql_num_rows($res) / $page_limit);        $rows = round($row_num, 0);        if ($rows < $row_num) $rows++;        $navy = "";        $tmp_start = $active - 3;        $tmp_end = $active + 3;        $tmp_a = 0;        for ($i = 1; $i <= $rows; $i++)            {                $style_btn = "default";                if ($i == $active) $style_btn = "primary";                $tmp_show = 0;                if ($i < 2) $tmp_show = 1; // $i < 3                if ($i > ($rows - 1)) $tmp_show = 1; // $rows - 4                if ($i > $tmp_start AND $i < $tmp_end) $tmp_show = 1;                if ($tmp_show == 0)                    {                        if (!isset($tmp_dot_start) AND $i < $active)                            {                                $tmp_dot_start = 1;                                $navy .= " ... ";                            }                        if (!isset($tmp_dot_end) AND $i > $active)                            {                                $tmp_dot_end = 1;                                $navy .= " ... ";                            }                    }                if ($tmp_show == 1)                    {                        $tmp_a++;                        $navy .= "<a onclick=\"skm_LockScreen()\" class=\"btn btn-".$style_btn." btn-xs\" href=\"".$url . $i ."\" role=\"button\">".$i."</a> ";                    }            }        $tmp_b = "<kbd>Показано {NAVY_INSERT_SHOW} із ".mysql_num_rows($res)."</kbd>";        if ($tmp_a == 1)            {                return $tmp_b;            }            else            {                return $navy." ".$tmp_b;            }    }function get_users_names($user)    {        // Шукаєм імя користувача по ИД, якщо 0 - то отримуєм масів всіх імен        if ($user == 0)            {                $users = array();                $users[0] = "{ANONYMOUS}";                $query_users = "SELECT `id`,`name` FROM `users` ORDER BY `name` ; ";                $res_users = mysql_query($query_users) or die(mysql_error());                while ($row_users=mysql_fetch_array($res_users))                    {                        $users[$row_users['id']] = $row_users['name'];                    }                return $users;            }        if ($user > 0)            {                if (preg_match("/^[1-9][0-9]*$/", $user))                    {                        $query_user = "SELECT `id`,`name` FROM `users` WHERE `id`='".$user."' LIMIT 1 ; ";                        $res_user = mysql_query($query_user) or die(mysql_error());                        if (mysql_num_rows($res_user) == 1)                            {                                while ($row_user=mysql_fetch_array($res_user))                                {                                    $name = $row_user['name'];                                }                            }                            else                            {                                $name = "{LANG_USER_NOT_FOUND}";                            }                        return $name;                    }            }    }function data_trans($a, $b, $data)    {        // Трансформуєм формат дати з $a в $b.        $data = str_replace(",", ".", $data);        $tmp_space = explode(" ", $data);        if (!empty($tmp_space[1])) $tmp_space[1] = " ".$tmp_space[1];        if ($a == "mysql")            {                $tmp = explode("-", $tmp_space[0]);                $year = $tmp[0];                $mon = $tmp[1];                $day = $tmp[2];            }        if ($a == "ua")            {                $tmp = explode(".", $tmp_space[0]);                $year = $tmp[2];                $mon = $tmp[1];                $day = $tmp[0];            }        if ($b == "mysql") $data = $year."-".$mon."-".$day . $tmp_space[1];        if ($b == "ua") $data = $day.".".$mon.".".$year . $tmp_space[1];        if ($data == "..") $data = "";        return $data;    }