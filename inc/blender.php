<?php
mysql_close();
$page.= file_get_contents("templates/footer.html");
if (isset($_SESSION['user_id']))
	{
		$session_counter = file_get_contents("templates/timer.html");
		$page = str_replace("{TIMEOUT_SESSION_COUNTER}", $session_counter, $page);
		if ($c_tmt > 3600) { $page = str_replace("{LANG_TIMEOUT_MORE_THEN_HOUR}", "declOfNum(hours, [\"година\", \"години\", \"годин\"]),", $page); } else { $page = str_replace("{LANG_TIMEOUT_MORE_THEN_HOUR}", "", $page); }
		if ($c_tmt == 0) { $page = str_replace("{LANG_TIMER_TO_CLOSE_SESSION}", "", $page); } else { $page = str_replace("{LANG_TIMER_TO_CLOSE_SESSION}", "{LANG_SESSION_TO} <a id=\"counter\"></a><br />", $page); }
		$page = str_replace("{LINK_BUTTON_IN_OR_OUT_TYPE}", "warning", $page);
		$page = str_replace("{LINK_BUTTON_IN_OR_OUT}", "$('#UserExit').modal('show');", $page);
		$page = str_replace("{LANG_BUTTON_IN_OR_OUT}", "{LANG_USER_OUT}", $page);
	}
	else
	{
		$page = str_replace("{TIMEOUT_SESSION_COUNTER}", "", $page);
		$page = str_replace("{LINK_BUTTON_IN_OR_OUT_TYPE}", "success", $page);
		$page = str_replace("{LINK_BUTTON_IN_OR_OUT}", "window.location = 'user.php';", $page);
		$page = str_replace("{LANG_BUTTON_IN_OR_OUT}", "{LANG_LOG_USER_ENTER}", $page);
	}
	
$page = str_replace("{MENU}", $menu, $page);

if (isset($timeout))
	{
		$page = str_replace("{META_REFRESH}", "<meta http-equiv=\"refresh\" content=\"3;url=".$timeout."\" />", $page);
		$page = str_replace("{REDIRECT_ANNONCE}", file_get_contents("templates/information_warning.html"), $page);
		$page = str_replace("{INFORMATION}", "{REDIRECT_ANNONCE}", $page);
		$page = str_replace("{REDIRECT_GIF}", "<td style=\"vertical-align: middle;\"><img src=\"templates/images/globe64.gif\"></td>", $page); 
	}
	else
	{
		$page = str_replace("{META_REFRESH}", "", $page);
		$page = str_replace("{REDIRECT_ANNONCE}", "", $page);
		$page = str_replace("{REDIRECT_GIF}", "", $page); 
	}
$page = str_replace("{SITENAME}", $c_nam, $page);
$page = str_replace("{MYSQL_BIN}", $c_bin, $page);
$page = str_replace("{BACKUPDIR}", $c_dir, $page);
$page = str_replace("{BACKUPDIR2}", $c_dir2, $page);
$page = str_replace("{BACKLIMIT}", $c_bul, $page);
$page = str_replace("{BACK_TIME}", $c_but, $page);
$page = str_replace("{TIMEOUT_AUHT}", $c_tmt, $page);
$page = str_replace("{PAGE_LIMIT}", $c_lmt, $page);
$page = str_replace("{PAGE_LIMIT_SERVER}", $c_lmt_s, $page);
$page = str_replace("{VER_NUM}", $c_ver, $page);
$page = str_replace("{YEAR_START}", $c_y_s, $page);
$page = str_replace("{N_RAY}", $c_n_ray, $page);
$page = str_replace("{REG_FILE}", $c_reg_file, $page);
$page = str_replace("{FILE_SIZE}", $c_file_size, $page);

if ($user_name) $page = str_replace("{USER_NAME}", $user_name, $page);
if (!$user_name) $page = str_replace("{USER_NAME}", "{LANG_GUEST}", $page);
$page = str_replace("{USER_LOGIN}", $user_login, $page);
$page = str_replace("{USER_REGISTRATION}", date("d.m.Y H:i", $user_reg), $page);
$page = str_replace("{USER_MAIL1}", $user_mail1, $page);
$page = str_replace("{USER_MAIL2}", $user_mail2, $page);
$page = str_replace("{USER_TEL1}", $user_tel1, $page);
$page = str_replace("{USER_TEL2}", $user_tel2, $page);
$page = str_replace("{USER_TEL3}", $user_tel3, $page);
$page = str_replace("{USER_LANG}", $user_lang, $page);
$page = str_replace("{USER_IP}", $user_ip, $page);
$page = str_replace("{USER_YEAR}", $user_year, $page);
$page = str_replace("{USERS_ONLINE}", $online_numberall, $page);
$page = str_replace("{BACKUP_PLUS}", $c_dirp, $page);
$page = str_replace("{D-A-T-A}", date('Y-m-d'), $page);
$page = str_replace("{TIME_MD5}", md5(time()), $page);

$page = str_replace("{LIST_LEFT}", "", $page);
$page = str_replace("{LIST_RIGHT}", "", $page);
$page = str_replace("{LIST_END}", "", $page);
$page = str_replace("{JURNAL_OUT_PRE_LINK}", "?".$pre_link, $page);


//Перелік доступних років
for ($i = $c_y_s; ; $i++)
	{
		$sel_year = "";
		if ($i == $user_year) $sel_year = "selected";
		$page_years .= "<OPTION value =\"".$i."\" ".$sel_year." >".$i."</OPTION>";
		if ($i == date('Y')) break;
	}
$page = str_replace("{SELECT_YEARS}", $page_years, $page);
/////////////

if ($user_ip_c == 1) {$page = str_replace("{USER_IP_C}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{USER_IP_C}", "{LANG_DISALLOW}", $page);}
if ($user_a_ip == 1) {$page = str_replace("{USER_A_IP}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{USER_A_IP}", "{LANG_DISALLOW}", $page);}
if ($user_p_user == 1) {$page = str_replace("{USER_P_USER}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{USER_P_USER}", "{LANG_DISALLOW}", $page);}
if ($user_p_config == 1) {$page = str_replace("{USER_P_CONFIG}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{USER_P_CONFIG}", "{LANG_DISALLOW}", $page);}
if ($user_p_log == 1) {$page = str_replace("{USER_P_LOG}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{USER_P_LOG}", "{LANG_DISALLOW}", $page);}
if ($user_p_users == 1) {$page = str_replace("{USER_P_USERS}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{USER_P_USERS}", "{LANG_DISALLOW}", $page);}
if ($user_p_addr == 1) {$page = str_replace("{USER_P_ADDR}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{USER_P_ADDR}", "{LANG_DISALLOW}", $page);}
if ($user_p_ip == 1) {$page = str_replace("{USER_P_IP}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{USER_P_IP}", "{LANG_DISALLOW}", $page);}
if ($user_p_mod == 1) {$page = str_replace("{USER_P_MOD}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{USER_P_MOD}", "{LANG_DISALLOW}", $page);}
if ($c_ano == 1) {$page = str_replace("{ANONYMOUS_ALLOW}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{ANONYMOUS_ALLOW}", "{LANG_DISALLOW}", $page);}
if ($c_lch == 1) {$page = str_replace("{LOGIN_CHOOSE_ALLOW}", "{LANG_ALLOW}", $page);} else {$page = str_replace("{LOGIN_CHOOSE_ALLOW}", "{LANG_DISALLOW}", $page);}

// Шукаємо старі версії ІЕ
$browser_alarm = "";
preg_match("/(MSIE)(?:\/| )([0-9.]+)/", $_SERVER['HTTP_USER_AGENT'], $browser_info);
list(,$browser_name,$browser_version) = $browser_info;
if ($browser_name == 'MSIE' AND $browser_version < 9)
	{
		$browser_alarm = file_get_contents("templates/information_warning.html");
		$browser_alarm = str_replace("{INFORMATION}", "{LANG_IE_IS_OLD}", $browser_alarm);
	}
$page = str_replace("{ALARM_IE_IS_OLD}", $browser_alarm, $page);
// Підключаємо мову
if (!file_exists("inc/lang/".$c_lng.".php")) die("Language file [inc/lang/".$c_lng.".php] not exist");
include_once ("lang/".$c_lng.".php");
foreach ($lang as $key => $value)
	{
		if (isset($_GET['export']) AND $_GET['export'] == "do")
			{
				$export = str_replace("{".$key."}", $value, $export);
			}
			else
			{
				$page = str_replace("{".$key."}", $value, $page);
			}
	}
$page = str_replace("{MAX_FILE_SIZE_MB}", (($max_file_size / 1024) / 1024 )." MB", $page);
if ($queryes_num < 1) $queryes_num = 0;
$page = str_replace("{PAGE_GENERATION_TIME}", "Page gen. ".sprintf("%.4f",(microtime(TRUE) - $start_php_time))."s. ", $page);
$page = str_replace("{PAGE_QUERYES_NUM}", " include ".$queryes_num." MySQL query'es", $page);

if (isset($_GET['export']) AND $_GET['export'] == "do")
	{
		$export = str_replace($srch_csv, $rpls_csv, $export);
		header("Content-Type: ".$export_type.";");
		header("Content-Disposition: attachment; filename=".$export_name."");
		echo(iconv('UTF-8', 'windows-1251', $export));
	}
	else
	{
		echo $page;
	}
?>
