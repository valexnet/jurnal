<?php
mysql_close();
$page.= file_get_contents("templates/footer.html");
if (isset($_SESSION['user_id']))
    {
        $session_counter = file_get_contents("templates/timer.html");
        $page = str_replace("{TIMEOUT_SESSION_COUNTER}", $session_counter, $page);
        if ($c_tmt > 3600) { $page = str_replace("{LANG_TIMEOUT_MORE_THEN_HOUR}", "declOfNum(hours, [\"година\", \"години\", \"годин\"]),", $page); } else { $page = str_replace("{LANG_TIMEOUT_MORE_THEN_HOUR}", "", $page); }
        if ($c_tmt == 0 OR $user_a_ip == "1" AND $user_ip == $_SERVER['REMOTE_ADDR'])
            {
                $page = str_replace("{LANG_TIMER_TO_CLOSE_SESSION}", "", $page);
                $page = str_replace("{TIMEOUT_SHOW_OF_NOT}", "false", $page);
            }
            else
            {
                $page = str_replace("{LANG_TIMER_TO_CLOSE_SESSION}", "{LANG_SESSION_TO} <a id=\"counter\"></a><br />", $page);
                $page = str_replace("{TIMEOUT_SHOW_OF_NOT}", "true", $page);
            }
    }
    else
    {
        $page = str_replace("{TIMEOUT_SESSION_COUNTER}", "", $page);
    }

$page = str_replace("{PRE_MENU}", $pre_menu, $page);
$page = str_replace("{MENU}", $menu, $page);
$page = str_replace("{AFT_MENU}", $aft_menu, $page);

if (isset($timeout))
    {
        $page = str_replace("{META_REFRESH}", "<meta http-equiv=\"refresh\" content=\"3;url=".$timeout."\" />", $page);
        $page = str_replace("{REDIRECT_ANNONCE}", file_get_contents("templates/information_warning.html"), $page);
        $page = str_replace("{INFORMATION}", "{REDIRECT_ANNONCE}", $page);
        $page = str_replace("{REDIRECT_GIF}", "<td style=\"vertical-align: middle;\"><img src=\"templates/images/globe64.gif\"></td>", $page);
        $page = str_replace("{SHOW_CHECK_NEW_ROWS}", "", $page);
		$page = str_replace("{VIEW_OVERDUE}", "false", $page);
    }
    else
    {
        $page = str_replace("{META_REFRESH}", "", $page);
        $page = str_replace("{REDIRECT_ANNONCE}", "", $page);
        $page = str_replace("{REDIRECT_GIF}", "", $page);
        $page = str_replace("{SHOW_CHECK_NEW_ROWS}", "check_new_rows(); check_overdue_rows();", $page);
		if (isset($_SESSION['overdue_timestamp']))
			{
				if (time() > $_SESSION['overdue_timestamp'] + 60)
					{
						$page = str_replace("{VIEW_OVERDUE}", "true", $page);
					}
					else
					{
						$page = str_replace("{VIEW_OVERDUE}", "false", $page);
					}
			}
			else
			{
				$page = str_replace("{VIEW_OVERDUE}", "true", $page);
			}
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
$page = str_replace("{MAX_PAGE_LIMIT}", $c_max_page_limit, $page);
$page = str_replace("{VER_NUM}", $c_ver.".".$c_ver_alt." ".$c_ver_lang, $page);
$page = str_replace("{YEAR_START}", $c_y_s, $page);
$page = str_replace("{N_RAY}", $c_n_ray, $page);
$page = str_replace("{REG_FILE}", $c_reg_file, $page);
$page = str_replace("{FILE_SIZE}", $c_file_size, $page);
$page = str_replace("{INDEX_MODULE}", $c_index_module, $page);

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
$page = str_replace("{D-A-T-A}", date('d.m.Y'), $page);
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

// Шукаємо нормальний браузер
if (preg_match("/rv:([1-9][0-9]*)/i", $_SERVER['HTTP_USER_AGENT'], $ie_ver)) $browser_version = "Internet Explorer v.".$ie_ver[1];
if (preg_match("/Firefox\/([1-9][0-9]*)/i", $_SERVER['HTTP_USER_AGENT'], $firefox_ver)) $browser_version = "Mozilla Firefox v.".$firefox_ver[1];
if (preg_match("/Chrome\/([1-9][0-9]*)/i", $_SERVER['HTTP_USER_AGENT'], $chrome_ver)) $browser_version = "Google Chrome v.".$chrome_ver[1];
if (preg_match("/OPR\/([1-9][0-9]*)/i", $_SERVER['HTTP_USER_AGENT'], $opera_ver)) $browser_version = "Opera v.".$opera_ver[1];

if ($ie_ver[1] >= 11 OR $firefox_ver[1] >= 40 OR $chrome_ver[1] >= 45 OR $opera_ver >= 32)
	{
		$browser_alarm .= "<!-- BROWSER: ".$browser_version." //-->";
	}
	else
	{
        $browser_alarm = file_get_contents("templates/information_warning.html");
        $browser_alarm = str_replace("{INFORMATION}", $_SERVER['HTTP_USER_AGENT'], $browser_alarm);
	}
$page = str_replace("{BROWSER_INFORMATION}", $browser_alarm, $page);

// Показуємо повідомлення про закритий журнал
if ($c_ano == 0)
    {
        $page = str_replace("{SHOW_C_ANO}", "{LANG_ANONYMOUS_ALLOW}: <strong>{LANG_DISALLOW}</strong>", $page);
    }
    else
    {
        $page = str_replace("{SHOW_C_ANO}", "", $page);
    }

// Показуємо що мало місця на діску
$free_giga_space = disk_free_space('/') / (1024*1024*1024);
if ($free_giga_space < 6)
    {
        if ($free_giga_space < 2)
            {
                $page = str_replace("{ALARM_FREE_SPACE}", file_get_contents("templates/information_danger.html"), $page);
            }
            else
            {
                $page = str_replace("{ALARM_FREE_SPACE}", file_get_contents("templates/information_warning.html"), $page);
            }
        $page = str_replace("{INFORMATION}", "{LANG_ALARM_FREE_SPACE}: ".round($free_giga_space, 1)." GB", $page);
    }
    else
    {
        $page = str_replace("{ALARM_FREE_SPACE}", "", $page);
    }
////

// Генеруємо {MD5_FORM}
$page = str_replace("{MD5_FORM}", md5(date('His')), $page);

// Підключаємо мову
$page = str_replace("{USER_LANG}", $c_lng, $page);
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
        header("Content-Type: ".$export_type.";");
        header("Content-Disposition: attachment; filename=".$export_name."");
		$export = htmlspecialchars_decode($export, ENT_NOQUOTES);
		$export = str_replace("&quot;", "'", $export);
		echo(@iconv('UTF-8', 'windows-1251//IGNORE', $export));
    }
    else
    {
        echo $page;
    }
?>
