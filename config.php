<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

$query = "SELECT * FROM `cron` WHERE `name`='backup' LIMIT 1 ;";
$res = mysql_query($query) or die(mysql_error());
$queryes_num++;
while ($row=mysql_fetch_array($res))
    {
        $backup_timeout = $row['time'];
        $backup_last = $row['last'];
    }

$query = "SELECT * FROM `cron` WHERE `name`='backup_on_email' LIMIT 1 ;";
$res = mysql_query($query) or die(mysql_error());
$queryes_num++;
while ($row=mysql_fetch_array($res))
    {
        $backup_on_email_timeout = $row['time'];
        $backup_on_email_last = $row['last'];
    }
            
if ($user_p_config == 1)
    {
        if (isset($_GET['backup']) AND $_GET['backup'] == "do")
            {
                if ($backup_timeout != 0)
                    {
                        mysql_query("UPDATE `cron` SET `last`='0' WHERE `name`='backup' LIMIT 1 ;");
                        if (isset($_GET['send']) AND $_GET['send'] == "email")
                            {
                                if ($backup_on_email_timeout != 0)
									{
									    mysql_query("UPDATE `cron` SET `last`='0' WHERE `name`='backup_on_email' LIMIT 1 ;");
                                        header('Location: config.php?backup=ok&mail=send');
										exit;
									}
									else
									{
                                        $page.= file_get_contents("templates/information.html");
                                        $page = str_replace("{INFORMATION}", "{LANG_CONFIG_BACKUP_ON_EMAIL_TIMEOUT_IS_ZERO}", $page);
									}
                            }
                            else
                            {
                                header('Location: config.php?backup=ok');
								exit;
                            }
                    }
                    else
                    {
                        $page.= file_get_contents("templates/information.html");
                        $page = str_replace("{INFORMATION}", "{LANG_CONFIG_BACKUP_TIMEOUT_IS_ZERO}", $page);
                    }
            }
        if (isset($_GET['backup']) AND $_GET['backup'] == "ok")
            {
                $page.= file_get_contents("templates/information.html");
                $page = str_replace("{INFORMATION}", "{LANG_ARHIV_MADE_OK}", $page);
            }
        if (isset($_GET['mail']) AND $_GET['mail'] == "send")
            {
                $page.= file_get_contents("templates/information.html");
                $page = str_replace("{INFORMATION}", "{LANG_ARHIV_SEND_BY_EMAIL}", $page);
            }

        if (isset($_GET['edit']))
            {
                $adress = "true";
                if (isset($_GET['do']))
                    {
                        $edit = "true";

                        $_POST['sitename'] = str_replace($srch, $rpls, $_POST['sitename']);
                        $_POST['backup_limit'] = str_replace($srch, $rpls, $_POST['backup_limit']);
                        $_POST['anonymous_allow'] = str_replace($srch, $rpls, $_POST['anonymous_allow']);
                        $_POST['n_ray'] = str_replace($srch, $rpls, $_POST['n_ray']);
                        $_POST['reg_file'] = str_replace($srch, $rpls, $_POST['reg_file']);
                        $_POST['file_size'] = str_replace($srch, $rpls, $_POST['file_size']);

                        if ($_POST['backup_plus'] != "")
                            {
                                $_POST['backup_plus'] = str_replace("\\\\", "/", $_POST['backup_plus']);
                                if (!preg_match("/^[a-z]{1}:.*\/$/i", $_POST['backup_plus']) OR !is_dir($_POST['backup_plus']))
                                    {
                                        $error = "true";
                                        $page.= file_get_contents("templates/information_danger.html");
                                        $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_BACKUP_PLUS}", $page);
                                    }
                            }

                        if ($_POST['sitename'] == "")
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_SITENAME}", $page);
                            }
                        if ($_POST['backup_limit'] < 0 OR $_POST['backup_limit'] > 9999)
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_BACKUP_LIMIT}", $page);
                            }
                        if ($_POST['anonymous_allow'] <> "" AND $_POST['anonymous_allow'] <> 1)
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_ANONYMOUS_ALLOW}", $page);
                            }
                        if ($_POST['timeout_auht'] < 300)
                            {
                                if ($_POST['timeout_auht'] <> 0)
                                    {
                                        $error = "true";
                                        $page.= file_get_contents("templates/information_danger.html");
                                        $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_TIMEOUT_AUHT}", $page);
                                    }
                            }
                        if ($_POST['page_limit'] <> "" AND $_POST['page_limit'] < 1)
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_PAGE_LIMIT}", $page);
                            }
                        if (!preg_match("/^(0|[1-9][0-9]*)$/" ,$_POST['max_page_limit']))
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_MAX_PAGE_LIMIT}", $page);
                            }
                        if ($_POST['cron_backup_timeout'] < 0)
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_CRON_BACKUP_TIMEOUT}", $page);
                            }
                        if ($_POST['cron_backup_on_email_timeout'] < 0)
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_CRON_BACKUP_ON_EMAIL_TIMEOUT}", $page);
                            }
                        if ($_POST['login_choose'] <> 0 AND $_POST['login_choose'] <> 1)
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_LOGIN_CHOOSE}", $page);
                            }
                        if ($_POST['year_start'] < 0 OR $_POST['year_start'] > 9999 OR $_POST['year_start'][3] == "")
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_YEAR_START}", $page);
                            }
                        if ($_POST['file_size'] < 0 OR $_POST['file_size'] > 9999)
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_FILE_SIZE}", $page);
                            }
                        if ($_POST['n_ray'] == "" OR $_POST['n_ray'][4] != "")
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_N_RAY}", $page);
                            }
                        if ($_POST['reg_file'] == "")
                            {
                                $error = "true";
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_ERROR_REG_FILE}", $page);
                            }
                        if ($error <> "true")
                            {
                                if ($_POST['anonymous_allow'] == 1) {$anonymous_allow = 1;} else {$anonymous_allow = 0;}
                                if ($_POST['login_choose'] == 1) {$login_choose = 1;} else {$login_choose = 0;}
                                $query = "UPDATE `config` SET
                                `name`='".$_POST['sitename']."',
                                `backup_plus`='".$_POST['backup_plus']."',
                                `backup_lim`='".$_POST['backup_limit']."',
                                `anonymous`='".$anonymous_allow."',
                                `user_timeout`='".$_POST['timeout_auht']."',
                                `page_limit`='".$_POST['page_limit']."',
                                `max_page_limit`='".$_POST['max_page_limit']."',
                                `login_choose`='".$login_choose."',
                                `year_start`='".$_POST['year_start']."',
                                `n_ray`='".$_POST['n_ray']."',
                                `reg_file`='".$_POST['reg_file']."',
                                `file_size`='".$_POST['file_size']."'
                                WHERE `id`='1' LIMIT 1;";
                                $sql = mysql_query($query) or die(mysql_error());
                                $queryes_num++;
                                $query = "UPDATE `cron` SET `time`='".$_POST['cron_backup_timeout']."' WHERE `name`='backup' LIMIT 1;";
                                $sql = mysql_query($query) or die(mysql_error());
                                $queryes_num++;
                                $query = "UPDATE `cron` SET `time`='".$_POST['cron_backup_on_email_timeout']."' WHERE `name`='backup_on_email' LIMIT 1;";
                                $sql = mysql_query($query) or die(mysql_error());
                                $queryes_num++;
                                $loging_do = "{LANG_LOG_CONFIG_EDIT}";
                                include ('inc/loging.php');
                                $page.= file_get_contents("templates/information_success.html");
                                $page = str_replace("{INFORMATION}", "{LANG_CONFIG_SAVED}", $page);
                                $timeout = "config.php";
                            }
                    }
                if ($edit != "true")
                    {
                        $page .= file_get_contents("templates/config_edit.html");
                        if ($c_ano == 1) $page = str_replace("{ANONYMOUS_ALLOW_C}", "checked", $page);
                        $page = str_replace("{ANONYMOUS_ALLOW_C}", "", $page);
                        if ($c_lch == 1) $page = str_replace("{LOGIN_CHOOSE_C}", "checked", $page);
                        $page = str_replace("{LOGIN_CHOOSE_C}", "", $page);
                    }
            }

        if (isset($_GET['phpinfo']))
            {
                $adress = "true";
                if ($_GET['phpinfo'] == "show") die(phpinfo());
                $page .= "<center><iframe src=\"?phpinfo=show\" frameborder=\"0\" width=\"820\" height=\"510\" scrolling=\"auto\"></iframe></center>";
            }

        if ($adress <> "true")
            {
                $page.= file_get_contents("templates/config.html");

                if(extension_loaded('zip'))
                    {
                        $page = str_replace("{ZIP_EXTENSION}", "<font color=\"green\">{LANG_ENABLE}</font>", $page);
                    }
                    else
                    {
                        $page = str_replace("{ZIP_EXTENSION}", "<font color=\"red\">{LANG_DISABLE}</font>", $page);
                    }
            }



        $page = str_replace("{CRON_BACKUP_TIMEOUT}", $backup_timeout, $page);
        $page = str_replace("{CRON_BACKUP_LAST}", date('Y.m.d H:i:s', $backup_last), $page);
        $page = str_replace("{CRON_BACKUP_ON_EMAIL_TIMEOUT}", $backup_on_email_timeout, $page);
        $page = str_replace("{CRON_BACKUP_ON_EMAIL_LAST}", date('Y.m.d H:i:s', $backup_on_email_last), $page);

    }
    else
    {
        $loging_do = "{LANG_LOG_CONFIG_403}";
        include ('inc/loging.php');
        header('HTTP/1.1 403 Forbidden');
        $page.= file_get_contents("templates/information_danger.html");
        $page = str_replace("{INFORMATION}", "{LANG_403}", $page);
        $timeout = "index.php";
    }
include ("inc/blender.php");
