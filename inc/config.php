<?php
session_start();
$start_php_time = microtime(TRUE);

//error_reporting(0);
// Налаштування БД
if (!file_exists("inc/db_connect.txt"))
	{
		DIE(file_get_contents("templates/install.html"));
	}
	else
	{
		$db_connect = explode("\r\n",file_get_contents("inc/db_connect.txt"));
	}
$hostname = $db_connect[0];
$username = $db_connect[1];
$password = base64_decode($db_connect[2]);
$dbName = $db_connect[3];
$admin_mail = $db_connect[4];
$c_lng = "ua";
$c_ver = 14;
$c_ver_alt = "3 beta";
$c_ver_lang = "Punctum";

// Налаштування пошти
require_once('inc/class.phpmailer.php');
include("inc/class.smtp.php");
$mail = new PHPMailer(true);
$mail->IsSMTP();
if ($db_connect[5] == "smtp_auth_true") $mail->SMTPAuth = true;
if ($db_connect[6] == "ssl_true") $mail->SMTPSecure = "ssl";
$mail->Host = $db_connect[7];
$mail->Port = $db_connect[8];
$mail->Username = $db_connect[11];
$mail->Password = base64_decode($db_connect[9]);
$mail->SetFrom($db_connect[4], $db_connect[10]);

// Підключення до БД
@mysql_connect($hostname,$username,$password) OR $DB_ERROR = "true";
@mysql_select_db($dbName) OR $DB_ERROR = "true";
@mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'") OR $DB_ERROR = "true";
@mysql_query("SET CHARACTER SET 'utf8'") OR $DB_ERROR = "true";

// Перевірка роботи сервісу
if ($DB_ERROR == "true")
	{
		if (!file_exists('inc/error_bd.txt'))
			{
				$file_error_content.="Час: ".date('Y.m.d H:i:s')."\n";
				foreach ($_SERVER as $k => $v)
					{
						$file_error_content.= $k.": ".$v."\n";
					}
				$f = @fopen("inc/error_bd.txt", "w");
				@fwrite($f, $file_error_content);
				@fclose($f);
				$mail->AddAddress($db_connect[4], $db_connect[10]);
				$mail->Subject = "АС Журнал: Помилка з БД";
				$mail->MsgHTML(file_get_contents('templates/mail_db_error.html'));
				$mail->AddAttachment('inc/error_bd.txt');
				$mail->Send();
			}
		DIE(file_get_contents("templates/db_error.html"));
	}
	else
	{
		if (file_exists('inc/error_bd.txt'))
			{
				@unlink('inc/error_bd.txt');
				$mail->AddAddress($db_connect[4], $db_connect[10]);
				$mail->Subject = "АС Журнал: Відновлено роботу сервісу";
				$mail->MsgHTML(file_get_contents('templates/mail_db_ok.html'));
				$loging_do = "{LANG_LOG_JURNAL_WORK_OK}";
				include ('inc/loging.php');
				if(!$mail->Send())
					{
						$loging_do = "{LANG_MAIL_SEND_ERROR}";
						include ('inc/loging.php');
					}
			}
	}

// Перевірка заблокованих IP
$query = "SELECT `ip` FROM `banned` WHERE `ip`='".$_SERVER['REMOTE_ADDR']."' LIMIT 1;";
$res = mysql_query($query) or die(mysql_error());
$queryes_num++;
if (mysql_num_rows($res) == 1)
	{
		if (preg_match("/chat\.php$/", $_SERVER['SCRIPT_FILENAME']))
			{
				DIE("you_baneed");
			}
			else
			{
				DIE(file_get_contents("templates/banned.html"));
			}
	}

// Завантаження конфігурації
$query = "SELECT * FROM `config` WHERE `id`='1' LIMIT 1;";
$res = mysql_query($query) or die(mysql_error());
$queryes_num++;
while ($row=mysql_fetch_array($res))
	{
		$c_nam = $row['name'];
		$c_dirp = $row['backup_plus'];
		$c_bul = $row['backup_lim'];
		$c_but = $row['backup_time'];
		$c_ano = $row['anonymous'];
		$c_tmt = $row['user_timeout'];
		$c_lmt_s = $row['page_limit'];
		$c_max_page_limit = $row['max_page_limit'];
		$c_lch = $row['login_choose'];
		$c_y_s = $row['year_start'];
		$c_n_ray = $row['n_ray'];
		$c_reg_file = $row['reg_file'];
		$c_reg_file_array = explode("|",$c_reg_file);
		$c_file_size = $row['file_size'];
		$max_file_size = (($row['file_size'] * 1024) * 1024);
		$c_ver_db = $row['ver'];
		$c_index_module = $row['index_module'];
	}

// Встановлення поновлень
if (file_exists("inc/update_db.php")) include ('inc/update_db.php');

// Звіряємо версію файлів та версію БД
if ($c_ver_db != $c_ver) DIE(file_get_contents("templates/need_update.html"));

// Перевірка авторизації
if (isset($_SESSION['user_id']))
	{
		$query = "SELECT * FROM `users` WHERE `id`='".$_SESSION['user_id']."' AND `work`='1' AND `del`='0' LIMIT 1;";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		$numberall = mysql_num_rows($res);
		if ($numberall <> 0)
			{
				$row = mysql_fetch_assoc($res);
				if ($_SESSION['user_login'] <> $row['login']) $session_error = "true";
				if ($_SESSION['user_pass'] <> $row['pass']) $session_error = "true";
				if ($session_error <> "true" && $c_tmt <> 0)
					{
						$user_last_clik = $c_tmt + $row['time'];
						if (time() >= $user_last_clik) $session_error = "true";
					}
				if ($row['ip_c'] == 0)
					{
						if ($row['ip'] <> $_SERVER['REMOTE_ADDR']) $session_error = "true";
					}

				if ($c_ano == 0 && $row['p_config'] == 0) $session_error = "true";

				if ($session_error <> "true")
					{
						$user_name = $row['name'];
						$user_login = $row['login'];
						$user_reg = $row['reg'];
						$user_mail1 = $row['mail1'];
						$user_mail2 = $row['mail2'];
						$user_tel1 = $row['tel1'];
						$user_tel2 = $row['tel2'];
						$user_tel3 = $row['tel3'];
						$user_lang = $row['lang'];
						$user_ip = $row['ip'];
						$user_ip_c = $row['ip_c'];
						$user_a_ip = $row['a_ip'];
						$user_l_ip = $row['l_ip'];
						$user_p_user = $row['p_user'];
						$user_p_config = $row['p_config'];
						$user_p_log = $row['p_log'];
						$user_p_users = $row['p_users'];
						$user_p_addr = $row['p_addr'];
						$user_p_ip = $row['p_ip'];
						$user_p_mod = $row['p_mod'];
						$usr_str_array = explode(",",$row['structura']);
						$usr_privat_array = explode(",",$row['privat']);
						$privat1 = 0;
						$privat2 = 0;
						$privat3 = 0;
						$privat4 = 0;
						$privat5 = 0;
						$privat6 = 0;
						$privat7 = 0;
						$privat8 = 0;
						$privat9 = 0;
						$privata = 0;
						$privatb = 0;
						if (in_array(1, $usr_privat_array)) $privat1 = 1; //Бачити всю вхідну
						if (in_array(2, $usr_privat_array)) $privat2 = 1; //Бачити всю вхідну ЕП
						if (in_array(3, $usr_privat_array)) $privat3 = 1; //Бачити всю вихідну
						if (in_array(4, $usr_privat_array)) $privat4 = 1; //Додавати вхідну
						if (in_array(5, $usr_privat_array)) $privat5 = 1; //Додавати вхідну ЕП
						if (in_array(6, $usr_privat_array)) $privat6 = 1; //Додавати вихідну
						if (in_array(7, $usr_privat_array)) $privat7 = 1; //Правити як основний виконавець
						if (in_array(8, $usr_privat_array)) $privat8 = 1; //Бачити доходи
						if (in_array(9, $usr_privat_array)) $privat9 = 1; //Додавати доходи
						if (in_array('a', $usr_privat_array)) $privata = 1; //Переглядати IT
						if (in_array('b', $usr_privat_array)) $privatb = 1; //Модерувати IT
						// Установка ліміту записів на сторінку
						if (!isset($_SESSION['user_page_limit'])) $_SESSION['user_page_limit'] = $c_lmt_s;
						if (isset($_POST['show_num_list']) AND preg_match("/^([1-9][0-9]*)$/", $_POST['show_num_list'])) $_SESSION['user_page_limit'] = $_POST['show_num_list'];
						if ($_SESSION['user_page_limit'] > $c_max_page_limit AND $c_max_page_limit > 0) $_SESSION['user_page_limit'] = $c_max_page_limit;
						$c_lmt = $_SESSION['user_page_limit'];
						// Установка обраного року
						if (!isset($_SESSION['user_year'])) $_SESSION['user_year'] = date('Y');
						if (isset($_POST['show_year']) AND preg_match("/^([1-9][0-9]{3})$/", $_POST['show_year'])) $_SESSION['user_year'] = $_POST['show_year'];
						if ($_SESSION['user_year'] < $c_y_s OR $_SESSION['user_year'] > date('Y')) $_SESSION['user_year'] = date('Y');
						$user_year = $_SESSION['user_year'];
					}
			}
			else
			{
				$session_error = "true";
			}
		if ($session_error == "true")
			{
				$loging_do = "{LANG_LOG_USER_CHECK_TIMEOUT}";
				include ('inc/loging.php');
				unset($_SESSION['user_id']);
				unset($_SESSION['user_login']);
				unset($_SESSION['user_pass']);
				unset($_SESSION['user_year']);
				session_unset();
				session_destroy();
			}
			else
			{
				$c_lng = $user_lang;
				$query = "UPDATE `users` SET `time`='".time()."' WHERE `id`='".$_SESSION['user_id']."' LIMIT 1;";
				$sql = mysql_query($query) or die(mysql_error());
				$queryes_num++;
			}
	}

// Автоматична авторизація по IP
if (!isset($_SESSION['user_id']) && $c_ano <> 0)
	{
		$query = "SELECT * FROM `users` WHERE `ip`='".$_SERVER['REMOTE_ADDR']."' AND `work`='1' AND `a_ip`='1' AND `del`='0' LIMIT 1;";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		if (mysql_num_rows($res) > 0)
			{
				$row = mysql_fetch_assoc($res);
				if (isset($_SESSION['user_login_error_num'])) unset($_SESSION['user_login_error_num']);
				$_SESSION['user_id'] = $row['id'];
				$_SESSION['user_login'] = $row['login'];
				$_SESSION['user_pass'] = $row['pass'];
				$user_name = $row['name'];
				$user_login = $row['login'];
				$user_reg = $row['reg'];
				$user_mail1 = $row['mail1'];
				$user_mail2 = $row['mail2'];
				$user_tel1 = $row['tel1'];
				$user_tel2 = $row['tel2'];
				$user_tel3 = $row['tel3'];
				$user_lang = $row['lang'];
				$user_ip = $row['ip'];
				$user_ip_c = $row['ip_c'];
				$user_a_ip = $row['a_ip'];
				$user_l_ip = $row['l_ip'];
				$user_p_user = $row['p_user'];
				$user_p_config = $row['p_config'];
				$user_p_log = $row['p_log'];
				$user_p_users = $row['p_users'];
				$user_p_addr = $row['p_addr'];
				$user_p_ip = $row['p_ip'];
				$user_p_mod = $row['p_mod'];
				$usr_str_array = explode(",",$row['structura']);
				$usr_privat_array = explode(",",$row['privat']);
				$privat1 = 0;
				$privat2 = 0;
				$privat3 = 0;
				$privat4 = 0;
				$privat5 = 0;
				$privat6 = 0;
				$privat7 = 0;
				$privat8 = 0;
				$privat9 = 0;
				$privata = 0;
				$privatb = 0;
				if (in_array(1, $usr_privat_array)) $privat1 = 1;
				if (in_array(2, $usr_privat_array)) $privat2 = 1;
				if (in_array(3, $usr_privat_array)) $privat3 = 1;
				if (in_array(4, $usr_privat_array)) $privat4 = 1;
				if (in_array(5, $usr_privat_array)) $privat5 = 1;
				if (in_array(6, $usr_privat_array)) $privat6 = 1;
				if (in_array(7, $usr_privat_array)) $privat7 = 1;
				if (in_array(8, $usr_privat_array)) $privat8 = 1;
				if (in_array(9, $usr_privat_array)) $privat9 = 1;
				if (in_array('a', $usr_privat_array)) $privata = 1;
				if (in_array('b', $usr_privat_array)) $privatb = 1;
				if ($_SESSION['user_year'] > 0 AND $_SESSION['user_year'] < 9999)
					{
						$user_year = $_SESSION['user_year'];
					}
					else
					{
						$_SESSION['user_year'] = date('Y');
						$user_year = $_SESSION['user_year'];
					}
				$loging_do = "{LANG_LOG_USER_A_ENTER}";
				include ('inc/loging.php');
				$c_lng = $user_lang;
				$query = "UPDATE `users` SET `time`='".time()."' WHERE `id`='".$_SESSION['user_id']."' LIMIT 1;";
				$sql = mysql_query($query) or die(mysql_error());
				$queryes_num++;
			}
	}

// Виконання запланованих задач
include ('inc/cron.php');

// Підключення функцій
include ('inc/function.php');

// Меню
$pre_menu = "";
$menu = "";
$aft_menu = "";
if (isset($_SESSION['user_id'])) $pre_menu .= "<a onclick=\"skm_LockScreen()\" class=\"btn btn-primary btn-sm\" href=\"user.php\">{USER_NAME}</a>";
if (isset($_SESSION['user_id'])) $aft_menu .= "<a class=\"btn btn-warning btn-sm\" href=\"#\" onclick=\"$('#UserExit').modal('show');\" role=\"button\">{LANG_USER_OUT}</a>";
if (!isset($_SESSION['user_id'])) $aft_menu .= "<a onclick=\"skm_LockScreen()\" class=\"btn btn-warning btn-sm\" href=\"index.php?chat\" role=\"button\">{LANG_CHAT}</a>";
if ($user_p_log == 1) $menu.="<li><a onclick=\"skm_LockScreen()\" href=\"loging.php\"><span class=\"glyphicon glyphicon-eye-open\" aria-hidden=\"true\" style=\"padding-right: 10px;\"></span> {LANG_LOG_VIEW}</a></li>";
if ($user_p_mod == 1) $menu.="<li><a onclick=\"skm_LockScreen()\" href=\"ndi.php\"><span class=\"glyphicon glyphicon-info-sign\" aria-hidden=\"true\" style=\"padding-right: 10px;\"></span> {LANG_NDI}</a></li>";
if ($user_p_users == 1) $menu.="<li><a onclick=\"skm_LockScreen()\" href=\"users.php\"><span class=\"glyphicon glyphicon-user\" aria-hidden=\"true\" style=\"padding-right: 10px;\"></span> {LANG_USER_P_USERS}</a></li>";
if ($user_p_ip == 1) $menu.="<li><a onclick=\"skm_LockScreen()\" href=\"ban.php\"><span class=\"glyphicon glyphicon-remove-circle\" aria-hidden=\"true\" style=\"padding-right: 10px;\"></span> {LANG_BAN_VIEW}</a></li>";
if ($user_p_config == 1) $menu.="<li><a onclick=\"skm_LockScreen()\" href=\"config.php\"><span class=\"glyphicon glyphicon-cog\" aria-hidden=\"true\" style=\"padding-right: 10px;\"></span> {LANG_USER_ADMIN_CONFIG}</a></li>";
if ($menu != "")
	{
		$menu = "<button onclick=\"show_admin_menu();\" class=\"btn btn-success btn-sm\" type=\"button\" id=\"dropdownMenu1\" aria-haspopup=\"true\" aria-expanded=\"true\">
				{LANG_STR_ADMIN}
				<span class=\"caret\"></span>
			</button>
			<ul class=\"dropdown-menu\" aria-labelledby=\"dropdownMenu1\">
			".$menu."
			</ul>";
	}

// Заміна спецсимволів
$srch = array("\"", "<", ">", "`", "'");
$rpls = array("&quot;", "&lt;", "&gt;", "&quot;", "&quot;");
$srch_e = array("&quot;", "&lt;", "&gt;", ";");
$rpls_e = array("'", "<", ">", ",");
?>
