<?php
session_start();
include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if (isset($_SESSION['user_id']))
    {
        $page .= file_get_contents("templates/jurnal_in_ep_header.html");
        $pre_link = "";
        $query_where = "";

        $query = "SHOW TABLES LIKE \"DB_".date('Y')."_IN_EP\";";
        $res = mysql_query($query) or die(mysql_error());
        $queryes_num++;
        if (mysql_num_rows($res) == 0)
            {
                if (@opendir("uploads/".date('Y')."/IN_EP"))
                    {
                        @closedir("uploads/".date('Y')."/IN_EP");
                    }
                    else
                    {
                        mkdir("uploads/".date('Y')."/IN_EP", null, true) or die("Can't create new folder - uploads/".date('Y')."/IN_EP");
                    }
                $query = file_get_contents("inc/db_in_ep.txt");
                $query = str_replace("{YEAR}", date('Y'), $query);
                mysql_query($query) or die(mysql_error());
            }

        if (isset($_GET['do']) && $_GET['do'] == 'add')
            {
                $adres = 'true';
                if ($privat5 == 1)
                    {
                        if (isset($_POST['get_data']) and !empty($_POST['get_data']))
                            {
                                // Убераєм з даних лишне
                                $_POST['org_name'] = str_replace($srch, $rpls, $_POST['org_name']);
                                $_POST['org_index'] = str_replace($srch, $rpls, $_POST['org_index']);
                                $_POST['org_subj'] = str_replace($srch, $rpls, $_POST['org_subj']);
                                $_POST['make_visa'] = str_replace($srch, $rpls, $_POST['make_visa']);
                                // Перевірка даних
                                $error = "";
                                if (!preg_match("/^[0-9]*$/", $_POST['imap_file_download'])) $error .= '{LANG_FORM_ERRROR_IMAP_DOWLOAD_ID}<br />';
                                if ($_POST['form_id'] == $_SESSION['form_id']) $error .= '{LANG_JURNAL_OUT_FORM_ERROR_FORM_ID}<br />';
                                if (!check_data(data_trans("ua", "mysql", $_POST['get_data']))) $error .= "{LANG_FORM_NO_GET_DATA}<br>";
                                if (!check_data(data_trans("ua", "mysql", $_POST['org_data']))) $error .= "{LANG_FORM_NO_ORG_DATA}<br>";
                                if ($_POST['make_data'] != "")
                                    {
                                        if (!check_data(data_trans("ua", "mysql", $_POST['make_data'])))
                                            {
                                                $error .= "{LANG_FORM_NO_MAKE_DATA}<br>";
                                            }
                                            else
                                            {
                                                $mysql_make_data = "'".data_trans("ua", "mysql", $_POST['make_data'])."'";
                                            }
                                    }
                                    else
                                    {
                                        $mysql_make_data = "NULL";
                                    }
                                if ($_POST['do_made'] == "1")
                                    {
                                        $mysql_do_made = "'".date('Y-m-d H:i:s')."'";
                                    }
                                    else
                                    {
                                        $mysql_do_made = "NULL";
                                    }
                                if (!preg_match("/^[1-9][0-9]*$/", $_POST['do_user'])) $error .= "{LANG_FORM_NO_DO_USER}<br>";
                                if ($_POST['org_name'] == "") $error .= "{LANG_FORM_NO_ORG_NAME}<br>";
                                if ($_POST['org_index'] == "") $error .= "{LANG_FORM_NO_ORG_INDEX}<br>";
                                if ($_POST['org_subj'] == "") $error .= "{LANG_FORM_NO_ORG_SUBJ}<br>";
                                if ($_POST['make_visa'] == "") $error .= "{LANG_FORM_NO_MAKE_VISA}<br>";

                                $inform_sql = "NULL";
                                if (isset($_POST['inform_users']) AND !empty($_POST['inform_users']))
                                    {
                                        $inform_sql = "";
                                        foreach ($_POST['inform_users'] as $inform_user_id)
                                            {
                                                if (preg_match("/^[1-9][0-9]*$/", $inform_user_id)) $inform_sql .= $inform_user_id.",";
                                            }
                                        $inform_sql = "',".$inform_sql."'";
                                        if ($inform_sql == "','") $inform_sql = "NULL";
                                    }

                                if ($error == "")
                                    {
                                        $query = "INSERT INTO `db_".date('Y')."_in_ep` (
                                        `id`,
                                        `add_user`,
                                        `add_time`,
                                        `add_ip`,
                                        `do_user`,
                                        `get_data`,
                                        `org_name`,
                                        `org_index`,
                                        `org_data`,
                                        `org_subj`,
                                        `make_visa`,
                                        `make_data`,
                                        `do_made`,
                                        `inform_users`
                                        ) VALUES (
                                        NULL ,
                                        '".$_SESSION['user_id']."',
                                        '".date('Y-m-d H:i:s')."',
                                        '".$_SERVER['REMOTE_ADDR']."',
                                        '".$_POST['do_user']."',
                                        '".data_trans("ua", "mysql", $_POST['get_data'])."',
                                        '".$_POST['org_name']."',
                                        '".$_POST['org_index']."',
                                        '".data_trans("ua", "mysql", $_POST['org_data'])."',
                                        '".$_POST['org_subj']."',
                                        '".$_POST['make_visa']."',
                                        ".$mysql_make_data.",
                                        ".$mysql_do_made.",
                                        ".$inform_sql."
                                        ) ;";
                                        mysql_query($query) or die(mysql_error());
                                        $queryes_num++;
                                        $_SESSION['error_in_ep_add'] = '';
                                        $_SESSION['error_in_ep_add_get_data'] = '';
                                        $_SESSION['error_in_ep_add_org_name'] = '';
                                        $_SESSION['error_in_ep_add_org_index'] = '';
                                        $_SESSION['error_in_ep_add_org_data'] = '';
                                        $_SESSION['error_in_ep_add_org_subj'] = '';
                                        $_SESSION['error_in_ep_add_make_visa'] = '';
                                        $_SESSION['error_in_ep_add_make_data'] = '';
                                        $_SESSION['error_in_ep_add_do_user'] = '';
                                        $_SESSION['form_id'] = $_POST['form_id'];

                                        $query = "SELECT `id` FROM `db_".date('Y')."_in_ep` ORDER BY `id` DESC LIMIT 1 ;";
                                        $res = mysql_query($query) or die(mysql_error());
                                        $queryes_num++;
                                        while ($row=mysql_fetch_array($res))
                                            {
                                                $last_id = $row['id'];
                                                $page.= file_get_contents("templates/information_success.html");
                                                $page = str_replace("{INFORMATION}", "{LANG_YOUR_IN_EP_N}: <kbd>".$row['id']."</kbd>", $page);
                                            }

                                        if ($_POST['imap_file_download'] != "0")
                                            {
                                                $connect = @imap_open('{'.$db_connect[7].':143/imap/novalidate-cert}INBOX', $db_connect[11], base64_decode($db_connect[9]), OP_READONLY);
                                                if ($connect)
                                                    {
                                                        $inbox = @imap_search($connect, 'UNDELETED');
                                                        if (in_array($_POST['imap_file_download'], $inbox))
                                                            {
                                                                $file = "uploads/".date('Y')."/IN_EP/".$c_n_ray."_".$last_id."_imap-".$_POST['imap_file_download'];
                                                                $file_eml = $file.".eml";
                                                                if (!file_exists($file_eml))
                                                                    {
                                                                        $headers = @imap_fetchheader($connect, $_POST['imap_file_download'], FT_PREFETCHTEXT);
                                                                        $body = @imap_body($connect, $_POST['imap_file_download']);
                                                                        @imap_close($connect);
                                                                        if (@file_put_contents($file_eml, $headers."\n".$body))
                                                                            {
                                                                                $new_file = $file_eml;
                                                                                if(extension_loaded('zip'))
                                                                                    {
                                                                                        $file_zip = $file.".zip";
                                                                                        if (!file_exists($file_zip))
                                                                                            {
                                                                                                $zip = new ZipArchive();
                                                                                                if($zip->open($file_zip, ZIPARCHIVE::CREATE))
                                                                                                    {
                                                                                                        $zip->addFile($file_eml);
                                                                                                        $zip->close();
                                                                                                        unlink($file_eml);
                                                                                                        $new_file = $file_zip;
                                                                                                    }
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                $page.= file_get_contents("templates/information_danger.html");
                                                                                                $page = str_replace("{INFORMATION}", "{LANG_IMAP_FILE_EXISTS}:".$file_zip."<br>{LANG_IMAP_FILE_NOT_ZIPED}<br>", $page);
                                                                                            }
                                                                                    }
                                                                                $page.= file_get_contents("templates/information_success.html");
                                                                                $page = str_replace("{INFORMATION}", "{LANG_IMAP_EML_IMPORTED}: <kbd>".$new_file."</kbd>", $page);
                                                                            }
                                                                            else
                                                                            {
                                                                                $page.= file_get_contents("templates/information_danger.html");
                                                                                $page = str_replace("{INFORMATION}", "{LANG_IMAP_FILE_NOT_CREATE}:".$file_eml."<br>{LANG_IMAP_EML_NOT_IMPORTED}<br>", $page);
                                                                            }
                                                                    }
                                                                    else
                                                                    {
                                                                        $page.= file_get_contents("templates/information_danger.html");
                                                                        $page = str_replace("{INFORMATION}", "{LANG_IMAP_FILE_EXISTS}:".$file_eml."<br>{LANG_IMAP_EML_NOT_IMPORTED}<br>", $page);
                                                                    }
                                                            }
                                                            else
                                                            {
                                                                $page.= file_get_contents("templates/information_danger.html");
                                                                $page = str_replace("{INFORMATION}", "{LANG_IMAP_EMAIL_NOT_UNDELETE}<br>{LANG_IMAP_EML_NOT_IMPORTED}<br>", $page);
                                                            }
                                                    }
                                                    else
                                                    {
                                                        $page.= file_get_contents("templates/information_danger.html");
                                                        $page = str_replace("{INFORMATION}", "{LANG_IMAP_NOT_CONNECTED}<br>{LANG_IMAP_EML_NOT_IMPORTED}<br>".imap_last_error(), $page);
                                                    }
                                            }
                                    }
                                    else
                                    {
                                        $page.= file_get_contents("templates/information_danger.html");
                                        $page = str_replace("{INFORMATION}", $error, $page);

                                        $_SESSION['error_in_ep_add'] = 1;
                                        $_SESSION['error_in_ep_add_get_data'] = $_POST['get_data'];
                                        $_SESSION['error_in_ep_add_org_name'] = $_POST['org_name'];
                                        $_SESSION['error_in_ep_add_org_index'] = $_POST['org_index'];
                                        $_SESSION['error_in_ep_add_org_data'] = $_POST['org_data'];
                                        $_SESSION['error_in_ep_add_org_subj'] = $_POST['org_subj'];
                                        $_SESSION['error_in_ep_add_make_visa'] = $_POST['make_visa'];
                                        $_SESSION['error_in_ep_add_make_data'] = $_POST['make_data'];
                                        $_SESSION['error_in_ep_add_do_user'] = $_POST['do_user'];
                                    }
                            }
                            else
                            {
                                if (isset($_GET['use']) AND $_GET['use'] == "imap")
                                    {
                                        $date_start = time();
                                        if(extension_loaded('imap'))
                                            {
                                                $connect = @imap_open('{'.$db_connect[7].':143/imap/novalidate-cert}INBOX', $db_connect[11], base64_decode($db_connect[9]), OP_READONLY);
                                                if ($connect)
                                                    {
                                                        $page.= file_get_contents("templates/information_success.html");
                                                        $page = str_replace("{INFORMATION}", "{LANG_IMAP_LAST_MAILS}<br>", $page);
                                                        $inbox = @imap_search($connect, 'UNDELETED');
                                                        if (isset($_GET['download']) AND preg_match("/^[1-9][0-9]*$/", $_GET['download']))
                                                            {
                                                                if (in_array($_GET['download'], $inbox))
                                                                {
                                                                    $headers = imap_fetchheader($connect, $_GET['download'], FT_PREFETCHTEXT);
                                                                    $body = imap_body($connect, $_GET['download']);
                                                                    @imap_close($connect);
                                                                    if (ob_get_level()) ob_end_clean();
                                                                    header('Content-Description: File Transfer');
                                                                    header('Content-Type: application/octet-stream');
                                                                    header('Content-Disposition: attachment; filename='.$_GET['download'].'.eml');
                                                                    header('Content-Transfer-Encoding: binary');
                                                                    header('Expires: 0');
                                                                    header('Cache-Control: must-revalidate');
                                                                    header('Pragma: public');
                                                                    echo $headers."\n".$body;
                                                                    exit;
                                                                    die();
                                                                }
                                                            }
                                                        @rsort($inbox);
                                                        $page .= file_get_contents("templates/jurnal_in_ep_imap.html");
                                                        $jurnal_in_ep_imap = "";
                                                        for ($i = 0; $i <= count($inbox); $i++)
                                                            {
                                                                if (isset($_GET['next']) and preg_match("/^[1-9][0-9]*$/", $_GET['next']) and $inbox[$i] >= $_GET['next']) continue;
                                                                if ((time() - $date_start) >= 5 AND $i >=3)
                                                                    {
                                                                        $page.= file_get_contents("templates/information_warning.html");
                                                                        $page = str_replace("{INFORMATION}", "{LANG_IMAP_BREAK_N} ".$last_imap_id."<br><a onclick=\"skm_LockScreen()\" href=\"?do=add&use=imap&next=".$last_imap_id."\">{LANG_IMAP_SHOW_NEXT}</a>", $page);
                                                                        break;
                                                                    }
                                                                if (isset($inbox[$i]))
                                                                    {
                                                                        $header = @imap_headerinfo($connect, $inbox[$i]);
                                                                        $html_to = "";
                                                                        for ($it = 0; $it<=4; $it++)
                                                                            {
                                                                                if (!isset($header->to[$it]->mailbox)) break;
                                                                                $tmp_to_name = imap_utf8($header->to[$it]->personal);
                                                                                if (preg_match("/^=\?utf-8\?B\?(.*)\?=$/i", $tmp_to_name, $utf8)) $tmp_to_name = base64_decode($utf8[1]);
                                                                                $tmp_to_email = imap_utf8($header->to[$it]->mailbox)."@".imap_utf8($header->to[$it]->host);
                                                                                if ($html_to != "" AND isset($header->to[$it]->personal)) $html_to .= "<br>";
                                                                                $html_to .= "<a href=\"https://".$db_connect[7]."/owa/?ae=Item&a=New&t=IPM.Note&to=".$tmp_to_email."\" target=\"_blank\">";
                                                                                if (isset($header->to[$it]->personal))
                                                                                    {
                                                                                        $html_to .= $tmp_to_name."</a>";
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        $html_to .= $tmp_to_email."</a>";
                                                                                    }
                                                                            }
                                                                        $html_from = "";
                                                                        $firs_email = "";
                                                                        for ($it = 0; $it<=4; $it++)
                                                                            {
                                                                                if (!isset($header->from[$it]->mailbox)) break;
                                                                                $tmp_from_name = imap_utf8($header->from[$it]->personal);
                                                                                if (preg_match("/^=\?utf-8\?B\?(.*)\?=$/i", $tmp_from_name, $utf8)) $tmp_from_name = base64_decode($utf8[1]);
                                                                                $tmp_from_email = imap_utf8($header->from[$it]->mailbox)."@".imap_utf8($header->from[$it]->host);
                                                                                if ($html_from != "" AND isset($header->from[$it]->personal)) $html_from .= "<br>";
                                                                                $html_from .= "<a href=\"https://".$db_connect[7]."/owa/?ae=Item&a=New&t=IPM.Note&to=".$tmp_from_email."\" target=\"_blank\">";
                                                                                if (isset($header->from[$it]->personal))
                                                                                    {
                                                                                        $html_from .= $tmp_from_name."</a>";
                                                                                        if ($firs_email == "") $firs_email = $tmp_from_name;
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        $html_from .= $tmp_from_email."</a>";
                                                                                    }
                                                                                if ($firs_email == "") $firs_email = $tmp_from_email;
                                                                            }
                                                                        if (isset($header->subject))
                                                                            {
                                                                                $html_subj = imap_utf8($header->subject);
                                                                                if (preg_match("/^=\?utf-8\?B\?(.*)\?=$/i", $html_subj, $utf8)) $html_subj = base64_decode($utf8[1]);
                                                                            }
                                                                            else
                                                                            {
                                                                                $html_subj = "{LANG_IMAP_SUBJECT_EMPTY}";
                                                                            }
                                                                        $html_mail_date = strtotime(imap_utf8($header->MailDate));
                                                                        $jurnal_in_ep_imap .= "
                                                                        <tr valign=\"top\" align=\"center\">
                                                                            <td valign=\"top\" align=\"center\"><a onclick=\"insert('".date('d.m.Y', $html_mail_date)."','".$firs_email."','".$html_subj."','".$inbox[$i]."');\"><strong>".$inbox[$i]."</strong></a></td>
                                                                            <td valign=\"top\" align=\"center\">".date('d.m.Y H:i', $html_mail_date)."</td>
                                                                            <td valign=\"top\" align=\"left\">".$html_from."</td>
                                                                            <td valign=\"top\" align=\"left\">".$html_subj."</td>
                                                                            <td valign=\"top\" align=\"left\">".$html_to."</td>
                                                                            <td valign=\"top\" align=\"right\"><a href=\"?do=add&use=imap&download=".$inbox[$i]."\"><span class=\"glyphicon glyphicon-floppy-disk\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"".$header->Size." байт\"></span></a></td>
                                                                        </tr>";
                                                                    }
                                                                $last_imap_id = $inbox[$i];
                                                            }
                                                        @imap_close($connect);
                                                        $page = str_replace("{JURNAL_IN_EP_IMAP}", $jurnal_in_ep_imap, $page);
                                                    }
                                                    else
                                                    {
                                                        $page.= file_get_contents("templates/information_danger.html");
                                                        $page = str_replace("{INFORMATION}", "{LANG_IMAP_NOT_CONNECTED}<br>".imap_last_error(), $page);
                                                    }
                                            }
                                            else
                                            {
                                                $page.= file_get_contents("templates/information_danger.html");
                                                $page = str_replace("{INFORMATION}", "{LANG_IMAP_EXT_NOT_LOADED}", $page);
                                            }
                                    }

                                $page.= file_get_contents("templates/jurnal_in_ep_add.html");

                                if (isset($_GET['template']) AND preg_match("/^[1-9][0-9]*$/", $_GET['template']))
                                    {
                                        $query = "SELECT * FROM `db_".$_SESSION['user_year']."_in_ep` WHERE `id`='".$_GET['template']."' LIMIT 1 ;";
                                        $res = mysql_query($query) or die(mysql_error());
                                        $queryes_num++;
                                        if (mysql_num_rows($res) > 0)
                                            {
                                                while ($row=mysql_fetch_array($res))
                                                    {
                                                        $_SESSION['error_in_ep_add'] = 1;
                                                        $_SESSION['error_in_ep_add_do_user'] = $row['do_user'];
                                                        $_SESSION['error_in_ep_add_get_data'] = data_trans("mysql", "ua", $row['get_data']);
                                                        $_SESSION['error_in_ep_add_org_name'] = $row['org_name'];
                                                        $_SESSION['error_in_ep_add_org_index'] = $row['org_index'];
                                                        $_SESSION['error_in_ep_add_org_data'] = data_trans("mysql", "ua", $row['org_data']);
                                                        $_SESSION['error_in_ep_add_org_subj'] = $row['org_subj'];
                                                        $_SESSION['error_in_ep_add_make_visa'] = $row['make_visa'];
                                                        $_SESSION['error_in_ep_add_make_data'] = data_trans("mysql", "ua", $row['make_data']);
                                                    }
                                            }
                                    }


                                $select_user = 0;
                                if (isset($_SESSION['error_in_ep_add']) AND $_SESSION['error_in_ep_add'] == 1)
                                    {
                                        $select_user = $_SESSION['error_in_ep_add_do_user'];
                                        $page = str_replace("{FORM_GET_DATA}", $_SESSION['error_in_ep_add_get_data'], $page);
                                        $page = str_replace("{FORM_ORG_NAME}", $_SESSION['error_in_ep_add_org_name'], $page);
                                        $page = str_replace("{FORM_ORG_INDEX}", $_SESSION['error_in_ep_add_org_index'], $page);
                                        $page = str_replace("{FORM_ORG_DATA}", $_SESSION['error_in_ep_add_org_data'], $page);
                                        $page = str_replace("{FORM_ORG_SUBJ}", $_SESSION['error_in_ep_add_org_subj'], $page);
                                        $page = str_replace("{FORM_MAKE_VISA}", $_SESSION['error_in_ep_add_make_visa'], $page);
                                        $page = str_replace("{FORM_MAKE_DATA}", $_SESSION['error_in_ep_add_make_data'], $page);
                                    }
                                $page = str_replace("{FORM_GET_DATA}", date('d.m.Y H:i:s'), $page);
                                $page = str_replace("{FORM_ORG_NAME}", "ГУДКСУ", $page);
                                $page = str_replace("{FORM_ORG_INDEX}", "", $page);
                                $page = str_replace("{FORM_ORG_DATA}", date('d.m.Y'), $page);
                                $page = str_replace("{FORM_ORG_SUBJ}", "", $page);
                                $page = str_replace("{FORM_MAKE_VISA}", "", $page);
                                $page = str_replace("{FORM_MAKE_DATA}", "", $page);
                                $html_select_users = get_users_selection_options($select_user, 0, "name", "ASC", 0);
                                $queryes_num++;
                                $page = str_replace("{FORM_DO_USER}", $html_select_users, $page);
                                $html_inform_users_list = get_inform_users_list(0);
                                $queryes_num++;
                                $page = str_replace("{FORM_INFORM_USERS_LIST}", $html_inform_users_list, $page);
                            }
                    }
                    else
                    {
                        $page.= file_get_contents("templates/information_danger.html");
                        $page = str_replace("{INFORMATION}", "{LANG_PRIVAT5_NO}", $page);
                    }
            }

        if (isset($_GET['edit']) && preg_match("/^[1-9][0-9]*$/", $_GET['edit']))
            {
                $adres = 'true';
                $query = "SELECT * FROM `db_".$_SESSION['user_year']."_in_ep` WHERE `id`='".$_GET['edit']."' LIMIT 1 ;";
                $res = mysql_query($query) or die(mysql_error());
                $queryes_num++;
                if (mysql_num_rows($res) > 0)
                    {
                        while ($row=mysql_fetch_array($res))
                            {
                                if ($row['add_user'] == $_SESSION['user_id'] OR $user_p_mod == 1)
                                    {
                                        if (isset($_POST['get_data']) and !empty($_POST['get_data']))
                                            {
                                                $_POST['org_name'] = str_replace($srch, $rpls, $_POST['org_name']);
                                                $_POST['org_index'] = str_replace($srch, $rpls, $_POST['org_index']);
                                                $_POST['org_subj'] = str_replace($srch, $rpls, $_POST['org_subj']);
                                                $_POST['make_visa'] = str_replace($srch, $rpls, $_POST['make_visa']);

                                                $error = "";
                                                if ($_POST['form_id'] == $_SESSION['form_id']) $error .= '{LANG_JURNAL_OUT_FORM_ERROR_FORM_ID}<br />';
                                                if ($_SESSION['user_year'] != date('Y')) $error .= '{LANG_JURNAL_IN_EDIT_LAST_YEAR}<br />';
                                                if (!check_data(data_trans("ua", "mysql", $_POST['get_data']))) $error .= "{LANG_FORM_NO_GET_DATA}<br>";
                                                if (!check_data(data_trans("ua", "mysql", $_POST['org_data']))) $error .= "{LANG_FORM_NO_ORG_DATA}<br>";
                                                if ($_POST['make_data'] != "")
                                                    {
                                                        if (!check_data(data_trans("ua", "mysql", $_POST['make_data'])))
                                                            {
                                                                $error .= "{LANG_FORM_NO_MAKE_DATA}<br>";
                                                            }
                                                            else
                                                            {
                                                                $mysql_make_data = "'".data_trans("ua", "mysql", $_POST['make_data'])."'";
                                                            }
                                                    }
                                                    else
                                                    {
                                                        $mysql_make_data = "NULL";
                                                    }
                                                if (!preg_match("/^[1-9][0-9]*$/", $_POST['do_user'])) $error .= "{LANG_FORM_NO_DO_USER}<br>";
                                                if ($_POST['org_name'] == "") $error .= "{LANG_FORM_NO_ORG_NAME}<br>";
                                                if ($_POST['org_index'] == "") $error .= "{LANG_FORM_NO_ORG_INDEX}<br>";
                                                if ($_POST['org_subj'] == "") $error .= "{LANG_FORM_NO_ORG_SUBJ}<br>";
                                                if ($_POST['make_visa'] == "") $error .= "{LANG_FORM_NO_MAKE_VISA}<br>";

                                                $inform_sql = "NULL";
                                                if (isset($_POST['inform_users']) AND !empty($_POST['inform_users']))
                                                    {
                                                        $inform_sql = "";
                                                        foreach ($_POST['inform_users'] as $inform_user_id)
                                                            {
                                                                if (preg_match("/^[1-9][0-9]*$/", $inform_user_id)) $inform_sql .= $inform_user_id.",";
                                                            }
                                                        $inform_sql = "',".$inform_sql."'";
                                                        if ($inform_sql == "','") $inform_sql = "NULL";
                                                    }
                                                    
                                                if ($error == "")
                                                    {
                                                        $query = "UPDATE `db_".date('Y')."_in_ep` SET
                                                        `moder`='".$_SESSION['user_id']."',
                                                        `edit`='".date('Y-m-d H:i:s')."',
                                                        `do_user`='".$_POST['do_user']."',
                                                        `get_data`='".data_trans("ua", "mysql", $_POST['get_data'])."',
                                                        `org_name`='".$_POST['org_name']."',
                                                        `org_index`='".$_POST['org_index']."',
                                                        `org_data`='".data_trans("ua", "mysql", $_POST['org_data'])."',
                                                        `org_subj`='".$_POST['org_subj']."',
                                                        `make_visa`='".$_POST['make_visa']."',
                                                        `make_data`=".$mysql_make_data.",
                                                        `inform_users`=".$inform_sql."
                                                        WHERE `id`='".$row['id']."' LIMIT 1 ; ";
                                                        mysql_query($query) or die(mysql_error());
                                                        $queryes_num++;
                                                        $_SESSION['form_id'] = $_POST['form_id'];
                                                        $page.= file_get_contents("templates/information_success.html");
                                                        $page = str_replace("{INFORMATION}", "{LANG_OUT_EDIT_OK}", $page);
                                                    }
                                                    else
                                                    {
                                                        $page .= file_get_contents("templates/information_danger.html");
                                                        $page = str_replace("{INFORMATION}", $error, $page);

                                                        $page.= file_get_contents("templates/information_danger.html");
                                                        $page = str_replace("{INFORMATION}", $error, $page);
                                                        $page.= file_get_contents("templates/information.html");
                                                        $page = str_replace("{INFORMATION}", "<a onclick=\"skm_LockScreen()\" href=\"jurnal_in_ep.php?edit=".$_GET['edit']."\">{LANG_RETURN_AND_GO}</a>", $page);
                                                        $loging_do = "{LANG_LOG_JURNAL_IN_EP_EDIT_ERROR}:<br />".$error;
                                                        include ('inc/loging.php');
                                                    }
                                            }
                                            else
                                            {
                                                $page .= file_get_contents("templates/jurnal_in_ep_edit.html");
                                                $page = str_replace("{FORM_ID}", $row['id'], $page);
                                                $page = str_replace("{FORM_DATA}", date('d.m.Y H:i:s'), $page);
                                                $page = str_replace("{FORM_GET_DATA}", data_trans("mysql", "ua", $row['get_data']), $page);
                                                $page = str_replace("{FORM_ORG_NAME}", $row['org_name'], $page);
                                                $page = str_replace("{FORM_ORG_INDEX}", $row['org_index'], $page);
                                                $page = str_replace("{FORM_ORG_DATA}", data_trans("mysql", "ua", $row['org_data']), $page);
                                                $page = str_replace("{FORM_ORG_SUBJ}", $row['org_subj'], $page);
                                                $page = str_replace("{FORM_MAKE_VISA}", $row['make_visa'], $page);
                                                if ($row['make_data'] != "") $page = str_replace("{FORM_MAKE_DATA}", data_trans("mysql", "ua", $row['make_data']), $page);
                                                $page = str_replace("{FORM_MAKE_DATA}", "", $page);
                                                $page = str_replace("{FORM_DO_USER}", get_users_selection_options($row['do_user'], 0, "name", "ASC", 0), $page);
                                                $queryes_num++;
                                                $html_inform_users_list = get_inform_users_list($row['inform_users']);
                                                $queryes_num++;
                                                $page = str_replace("{FORM_INFORM_USERS_LIST}", $html_inform_users_list, $page);
                                            }
                                    }
                                    else
                                    {
                                        $page.= file_get_contents("templates/information_danger.html");
                                        $page = str_replace("{INFORMATION}", "{LANG_JURNAL_IN_EP_EDIT_NOT_AUTHOR}", $page);
                                    }
                            }
                    }
                    else
                    {
                        $page.= file_get_contents("templates/information_danger.html");
                        $page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_ID_NOT_FOUND}", $page);
                    }

            }

        if (isset($_GET['do']) && $_GET['do'] == 'src')
            {
                $adres = 'true';
                $page.= file_get_contents("templates/jurnal_in_ep_search.html");
                $page = str_replace("{FORM_DATA_START}", "01.01.".$_SESSION['user_year'], $page);
                $page = str_replace("{FORM_DATA_END}", "31.12.".$_SESSION['user_year'], $page);
                $html_select_users = get_users_selection_options(0, 0, "name", "ASC", 0);
                $queryes_num++;
                $page = str_replace("{USERS}", $html_select_users, $page);
            }

        if (isset($_GET['attach']) && preg_match('/^[1-9][0-9]*$/', $_GET['attach']))
            {
                $adres = 'true';
                $query = "SELECT * FROM `db_".$_SESSION['user_year']."_in_ep` WHERE `id`='".$_GET['attach']."' LIMIT 1 ; ";
                $res = mysql_query($query) or die(mysql_error());
                $queryes_num++;
                if (mysql_num_rows($res) == 1)
                    {
                        $manage_files = 0;
                        $view_files = 0;
                        $row = mysql_fetch_assoc($res);
                        $page.= file_get_contents("templates/information.html");
                        $page = str_replace("{INFORMATION}", "{LANG_FILE_ABOUT_NUM} <strong>".$row['id']."</strong>", $page);
                        if ($row['add_user'] == $_SESSION['user_id'] OR $user_p_mod == 1)
                            {
                                $manage_files = 1;
                                $view_files = 1;
                            }
                        if ($privat2 == 1 OR $row['do_user'] == $_SESSION['user_id'])
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
                                                                                $file_new_name = $c_n_ray."_".$row['id']."_".$FILE['name'][$i];
                                                                                if (preg_match("/^".$c_n_ray."_".$row['id']."_.*/i", $FILE['name'][$i])) $file_new_name = $FILE['name'][$i];
                                                                                $file_name = "uploads/".$_SESSION['user_year']."/IN_EP/".$file_new_name;
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
                                $page = str_replace("{FILE_PRE_NAME}", "<b>".$c_n_ray."_".$row['id']."_</b>", $page);
                            }
                        if ($view_files == 1)
                            {
                                if ($dir = opendir("uploads/".$_SESSION['user_year']."/IN_EP"))
                                    {
                                        while (false !== ($file = readdir($dir)))
                                            {
                                                if ($file != "." && $file != "..")
                                                    {
                                                        $file_utf8 = iconv('windows-1251', 'UTF-8', $file);
                                                        if (preg_match("/^".$c_n_ray."_".$row['id']."_.*\.(?=".$c_reg_file.")/i", $file))
                                                            {
                                                                $tmp_do = 0;
                                                                if (isset($_GET['delete']) AND $_GET['delete'] == $file_utf8 AND $manage_files == 1 AND $tmp_add_new_file == 0)
                                                                    {
                                                                        $tmp_do = 1;
                                                                        if (@unlink("uploads/".$_SESSION['user_year']."/IN_EP/".$file))
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
                                                                        header('Content-Length: ' . filesize("uploads/".$_SESSION['user_year']."/IN_EP/".$file));
                                                                        if ($fd = fopen("uploads/".$_SESSION['user_year']."/IN_EP/".$file, 'rb'))
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
                                                                        $page = str_replace("{INFORMATION}", "{TMP_MANAGE_FILES}<a href=\"jurnal_in_ep.php?attach=".$_GET['attach']."&download=".$file_utf8."\">".$file_utf8."</a> [ ".date ('d.m.Y H:i:s', @filemtime ("uploads/".$_SESSION['user_year']."/IN_EP/".$file))." ]", $page);
                                                                        if ($manage_files == 1)
                                                                            {
                                                                                $page = str_replace("{TMP_MANAGE_FILES}", "<a href=\"jurnal_in_ep.php?attach=".$_GET['attach']."&delete=".$file_utf8."\" onClick=\"if(confirm('{LANG_REMOVE_FILE_CONFIRM}')) {return true;} return false;\"><img src=\"templates/images/cross_octagon.png\"></a> ", $page);
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

        if (isset($_GET['delete_last']) && $_GET['delete_last'] <> '')
            {
                $adres = 'true';
                $query = "SELECT * FROM `db_".date('Y')."_in_ep` ORDER BY `id` DESC LIMIT 1 ; ";
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
                                        $query = "DELETE FROM `db_".date('Y')."_in_ep` WHERE `id`='".$row['id']."' LIMIT 1 ; ";
                                        mysql_query($query) or die(mysql_error());
                                        $queryes_num++;
                                        @mysql_query("ALTER TABLE `db_".date('Y')."_in_ep` AUTO_INCREMENT =".$row['id']." ;") or die(mysql_error());
                                        $queryes_num++;

                                        $loging_do = "{LANG_LOG_JURNAL_IN_EP_DELETE_LAST} ".$row['id'];
                                        include ('inc/loging.php');
                                        $page.= file_get_contents("templates/information_success.html");
                                        $page = str_replace("{INFORMATION}", "{LANG_JURNAL_IN_EP_DELETE_LAST} ".$row['id'], $page);
                                        @array_map('unlink', @glob("uploads/".date('Y')."/IN_EP/".$c_n_ray."_".$row['id']."_*"));
                                        $timeout = "jurnal_in_ep.php";
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

        if (isset($_GET['do']) AND $_GET['do'] == "made" AND isset($_GET['id']) AND preg_match("/^[1-9][0-9]*$/", $_GET['id']))
            {
                $query = "SELECT * FROM `db_".$_SESSION['user_year']."_in_ep` WHERE `id`='".$_GET['id']."' LIMIT 1 ; ";
                $res = mysql_query($query) or die(mysql_error());
                $queryes_num++;
                if (mysql_num_rows($res) == 1)
                    {
                        while ($row=mysql_fetch_array($res))
                            {
                                $error = "";
                                if (!empty($row['do_made'])) $error = "{LANG_NEW_OUT_EP_WITH_DO_MADED}<br>";
                                if ($row['do_user'] != $_SESSION['user_id'] AND $user_p_mod != 1) $error = "{LANG_NEW_OUT_EP_WITH_NOT_DO_USER}<br>";
                                if ($error == "")
                                    {
                                        $query = "UPDATE `db_".$_SESSION['user_year']."_in_ep` SET `do_made`='".date('Y-m-d H:i:s')."', `do_made_ip`='".$_SERVER['REMOTE_ADDR']."' WHERE `id`='".$row['id']."' LIMIT 1 ; ";
                                        mysql_query($query) or die(mysql_error());
                                        $queryes_num++;
                                        $page.= file_get_contents("templates/information.html");
                                        $page = str_replace("{INFORMATION}", "{LANG_NEW_OUT_EP_WITH_UPDATED}: <strong>".$row['id']."</strong><br><kbd>{LANG_JURNAL_IN_STATUS_3} ".date('d.m.Y H:i:s')."</kbd>", $page);
                                    }
                                    else
                                    {
                                        $adres = 'true';
                                        $page.= file_get_contents("templates/information_danger.html");
                                        $page = str_replace("{INFORMATION}", $error, $page);
                                    }
                            }
                    }
                    else
                    {
                        $adres = 'true';
                        $page.= file_get_contents("templates/information_danger.html");
                        $page = str_replace("{INFORMATION}", "{LANG_NUM_NOT_FOUND}", $page);
                    }
            }

        if (isset($_GET['do']) AND $_GET['do'] == "search")
            {
                $error = "";

                if (!isset($_GET['id'])) $_GET['id'] = "";
                if (!isset($_GET['data_start'])) $_GET['data_start'] = "01.01.".$_SESSION['user_year'];
                if (!isset($_GET['data_end'])) $_GET['data_end'] = "31.12.".$_SESSION['user_year'];
                if (!isset($_GET['add_user'])) $_GET['add_user'] = "";
                if (!isset($_GET['status'])) $_GET['status'] = "";
                if (!isset($_GET['get_data'])) $_GET['get_data'] = "";
                if (!isset($_GET['org_name'])) $_GET['org_name'] = "";
                if (!isset($_GET['org_data'])) $_GET['org_data'] = "";
                if (!isset($_GET['org_index'])) $_GET['org_index'] = "";
                if (!isset($_GET['org_subj'])) $_GET['org_subj'] = "";
                if (!isset($_GET['make_visa'])) $_GET['make_visa'] = "";
                if (!isset($_GET['do_user'])) $_GET['do_user'] = "";
                if (!isset($_GET['make_data'])) $_GET['make_data'] = "";

                if ($_GET['data_start'] != "") $_GET['data_start'] = data_trans("ua", "mysql", $_GET['data_start']);
                if ($_GET['data_end'] != "") $_GET['data_end'] = data_trans("ua", "mysql", $_GET['data_end']);
                if ($_GET['get_data'] != "") $_GET['get_data'] = data_trans("ua", "mysql", $_GET['get_data']);
                if ($_GET['org_data'] != "") $_GET['org_data'] = data_trans("ua", "mysql", $_GET['org_data']);
                if ($_GET['make_data'] != "") $_GET['make_data'] = data_trans("ua", "mysql", $_GET['make_data']);

                if ($_GET['id'] != "" AND !preg_match("/^[1-9][0-9]*$/" ,$_GET['id'])) $error .= "{LANG_FORM_NO_ID}<br>";
                if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['data_start'])) $error .= "{LANG_FORM_NO_DATA_START}<br>";
                if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['data_end'])) $error .= "{LANG_FORM_NO_DATA_END}<br>";
                if ($_GET['add_user'] != "" AND !preg_match("/^[1-9][0-9]*$/" ,$_GET['add_user'])) $error .= "{LANG_SEARCH_NO_ADD_USER}<br>";
                if ($_GET['status'] != "" AND !preg_match("/^[0-3]$/" ,$_GET['status'])) $error .= "{LANG_SEARCH_NO_STATUS}<br>";
                if ($_GET['get_data'] != "" AND !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['get_data'])) $error .= "{LANG_FORM_NO_GET_DATA}<br>";
                if ($_GET['org_name'] != "") $_GET['org_name'] = str_replace($srch, $rpls, $_GET['org_name']);
                if ($_GET['org_data'] != "" AND !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['org_data'])) $error .= "{LANG_FORM_NO_ORG_DATA}<br>";
                if ($_GET['org_index'] != "") $_GET['org_index'] = str_replace($srch, $rpls, $_GET['org_index']);
                if ($_GET['org_subj'] != "") $_GET['org_subj'] = str_replace($srch, $rpls, $_GET['org_subj']);
                if ($_GET['make_visa'] != "") $_GET['make_visa'] = str_replace($srch, $rpls, $_GET['make_visa']);
                if ($_GET['do_user'] != "" AND !preg_match("/^[1-9][0-9]*$/" ,$_GET['do_user'])) $error .= "{LANG_SEARCH_NO_USER}<br>";
                if ($_GET['make_data'] != "" AND !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['make_data'])) $error .= "{LANG_FORM_NO_MAKE_DATA}<br>";

                if ($error == "")
                    {
                        $pre_link = "do=search";
                        $search_where = "`id` IS NOT NULL";
                        $where_lang = "{LANG_EXTRA_SEARCH}:<br>";

                        if ($_GET['id'] != "")
                            {
                                $pre_link .= "&id=".$_GET['id'];
                                $search_where .= " AND `id` = '".$_GET['id']."'";
                                $where_lang .= "<small>{LANG_START_SEARCH_ID}</small> ".$_GET['id']."<br>";
                                $queryes_num++;
                            }
                        if ($_GET['data_start'] != $_SESSION['user_year']."-01-01" OR $_GET['data_end'] != $_SESSION['user_year']."-12-31")
                            {
                                $pre_link .= "&data_start=".$_GET['data_start']."&data_end=".$_GET['data_end'];
                                $search_where .= " AND `get_data` >= '".$_GET['data_start']." 00:00:00' AND `get_data` <= '".$_GET['data_end']." 23:59:59'";
                                $where_lang .= "<small>{LANG_DATA_START}:</small> ".data_trans("mysql", "ua", $_GET['data_start'])." <small>{LANG_DATA_END}:</small> ".data_trans("mysql", "ua", $_GET['data_end'])."<br>";
                            }
                        if ($_GET['add_user'] != "")
                            {
                                $pre_link .= "&add_user=".$_GET['add_user'];
                                $search_where .= " AND `add_user` = '".$_GET['add_user']."'";
                                $where_lang .= "<small>{LANG_ADD_USER}</small> ".get_users_names($_GET['add_user'])."<br>";
                                $queryes_num++;
                            }
                        if ($_GET['status'] != "" AND $_GET['status'] != "0")
                            {
                                $pre_link .= "&status=".$_GET['status'];
                                $where_lang .= "<small>{LANG_JURNAL_STEP_DO}</small> {LANG_JURNAL_IN_STATUS_".$_GET['status']."}<br>";
                                if ($_GET['status'] == "1") $search_where .= " AND `do_view` IS NULL";
                                if ($_GET['status'] == "2") $search_where .= " AND `do_view` IS NOT NULL AND `do_made` IS NULL";
                                if ($_GET['status'] == "3") $search_where .= " AND `do_made` IS NOT NULL";
                            }
                        if ($_GET['get_data'] != "")
                            {
                                $pre_link .= "&get_data=".$_GET['get_data'];
                                $search_where .= " AND `get_data` = '".$_GET['get_data']."'";
                                $where_lang .= "<small>{LANG_GET_DATA_S}</small> ".data_trans("mysql", "ua", $_GET['get_data'])."<br>";
                            }
                        if ($_GET['org_name'] != "")
                            {
                                $pre_link .= "&org_name=".$_GET['org_name'];
                                $search_where .= " AND `org_name` LIKE '".str_replace("*", "%", $_GET['org_name'])."'";
                                $where_lang .= "<small>{LANG_ORG_NAME_S}</small> ".$_GET['org_name']."<br>";
                            }
                        if ($_GET['org_data'] != "")
                            {
                                $pre_link .= "&org_data=".$_GET['org_data'];
                                $search_where .= " AND `org_data` = '".$_GET['org_data']."'";
                                $where_lang .= "<small>{LANG_ORG_DATA_S}</small> ".data_trans("mysql", "ua", $_GET['org_data'])."<br>";
                            }
                        if ($_GET['org_index'] != "")
                            {
                                $pre_link .= "&org_index=".$_GET['org_index'];
                                $search_where .= " AND `org_index` LIKE '".str_replace("*", "%", $_GET['org_index'])."'";
                                $where_lang .= "<small>{LANG_ORG_INDEX}</small> ".$_GET['org_index']."<br>";
                            }
                        if ($_GET['org_subj'] != "")
                            {
                                $pre_link .= "&org_subj=".$_GET['org_subj'];
                                $search_where .= " AND `org_subj` LIKE '".str_replace("*", "%", $_GET['org_subj'])."'";
                                $where_lang .= "<small>{LANG_ORG_SUBJ}</small> ".$_GET['org_subj']."<br>";
                            }
                        if ($_GET['make_visa'] != "")
                            {
                                $pre_link .= "&make_visa=".$_GET['make_visa'];
                                $search_where .= " AND `make_visa` LIKE '".str_replace("*", "%", $_GET['make_visa'])."'";
                                $where_lang .= "<small>{LANG_MAKE_VISA}</small> ".$_GET['make_visa']."<br>";
                            }
                        if ($_GET['do_user'] != "")
                            {
                                $pre_link .= "&do_user=".$_GET['do_user'];
                                $search_where .= " AND `do_user` = '".$_GET['do_user']."'";
                                $where_lang .= "<small>{LANG_DO_USER}</small> ".get_users_names($_GET['do_user'])."<br>";
                                $queryes_num++;
                            }
                        if ($_GET['make_data'] != "")
                            {
                                $pre_link .= "&make_data=".$_GET['make_data'];
                                $search_where .= " AND `make_data` = '".$_GET['make_data']."'";
                                $where_lang .= "<small>{LANG_ORG_DATA_S}</small> ".data_trans("mysql", "ua", $_GET['make_data'])."<br>";
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
                $page = str_replace("{JURNAL_IN_EP_TOP_STAT}", file_get_contents("templates/jurnal_in_ep_top_stat.html"), $page);
                $page = str_replace("{JURNAL_IN_EP_AFFIX}", "data-spy=\"affix\" data-offset-top=\"170\"", $page);

                if (isset($_GET['page_num']) AND preg_match('/^[1-9][0-9]*$/', $_GET['page_num']))
                    {
                        $active = $_GET['page_num'];
                    }
                    else
                    {
                        $active = 1;
                    }

                $limit_pre = (($active - 1) * $_SESSION['user_page_limit']);
                $sql_limit = "LIMIT ".$limit_pre.", ".$_SESSION['user_page_limit'];

                if ($privat2 == 1)
                    {
                        $query_where = "";
                        if (isset($search_where) AND $search_where != "") $query_where = " WHERE ".$search_where;
                    }
                    else
                    {
                        $query_where = "WHERE `add_user`='".$_SESSION['user_id']."' OR `do_user`='".$_SESSION['user_id']."'";
                        if (isset($search_where) AND $search_where != "") $query_where = $query_where." AND ".$search_where;
                    }

                // Якщо є пошук, показуємо повідомлення і ссилку на знулення.
                if (isset($where_lang) AND !empty($where_lang))
                    {
                        $page.= file_get_contents("templates/information.html");
                        $page = str_replace("{INFORMATION}", $where_lang." <a onclick=\"skm_LockScreen()\" class=\"btn btn-default btn-sm\" href=\"jurnal_in_ep.php\">{LANG_CLEAN_SERCH_RESULTS}</a>", $page);
                    }

                $query_order_by = "ORDER BY `id` DESC ";

                if (isset($_GET['export']) AND $_GET['export'] == "do")
                    {
                        $query_order_by = "ORDER BY `id` ";
                        $sql_limit = "";
                        $export_type = "text/csv";
                        $export_name = "jurnal_in_ep.csv";
                        $export = "\"".$c_nam."\";\n\n";
                        $export .= "\"{LANG_HEADERINFO}\";\n";
                        $export .= "\"{LANG_SEARCH_RESULTS} :\";\n";
                        $export .=  "\"{LANG_ORG_INDEX_S}\";\"{LANG_JURNAL_STEP_DO}\";\"{LANG_ADD_USER}\";\"{LANG_ADD_TIME}\";\"{LANG_GET_DATA_S}\";\"{LANG_ORG_NAME_S}\";\"{LANG_ORG_INDEX_S}\";\"{LANG_ORG_DATA_S}\";\"{LANG_ORG_SUBJ_S}\";\"{LANG_DO_USER}\";\"{LANG_MAKE_VISA_S}\";\"{LANG_MAKE_DATA}\";\"{LANG_JURNAL_IN_STATUS_2}\";\"{LANG_JURNAL_IN_STATUS_3}\";\n";
                    }
                $html_navy = get_navy("DB_".$_SESSION['user_year']."_IN_EP", $query_where, $query_order_by, $active, $_SESSION['user_page_limit'], "jurnal_in_ep.php?".$pre_link."&page_num=");
                $page = str_replace("{NAVY}", $html_navy, $page);
                $page .= file_get_contents("templates/jurnal_in_ep.html");
                $query = "SELECT * FROM `db_".$_SESSION['user_year']."_in_ep` ".$query_where." ".$query_order_by." ".$sql_limit." ;";
                $res = mysql_query($query) or die(mysql_error());
                $queryes_num++;
                $page = str_replace("{NAVY_INSERT_SHOW}", mysql_num_rows($res), $page);
                $jurnal_in = "";
                $modals = "";
                $is_first = "";
                if (mysql_num_rows($res) > 0)
                    {
                        $users = get_users_names(0);
                        $queryes_num++;
                        while ($row=mysql_fetch_array($res))
                            {
                                $tr_color_info = "onclick=\"cTR('TRn".$row['id']."')\"";
                                if ($row['make_data'] != "")
                                    {
                                        $tmp = explode(" ", $row['make_data']);
                                        if ($tmp[1] == "00:00:00") $row['make_data'] = $tmp[0];
                                        if ($row['do_made'] == "")
                                            {
                                                $tmp = explode("-", $tmp[0]);
                                                $tmp = $tmp[0].$tmp[1].$tmp[2];
                                                if ($tmp == date('Ymd')) $tr_color_info = "class=\"warning\"";
                                                if ($tmp < date('Ymd')) $tr_color_info = "class=\"danger\"";
                                            }
                                    }
                                $tmp = explode(" ", $row['get_data']);
                                if ($tmp[1] == "00:00:00") $row['get_data'] = $tmp[0];
                                $tmp = explode(" ", $row['org_data']);
                                if ($tmp[1] == "00:00:00") $row['org_data'] = $tmp[0];

                                $num_is_edited = "";
                                if (!empty($row['edit'])) $num_is_edited = "<tr><td class=\"bg-warning\" colspan=\"2\"><p class=\"text-danger\"><strong>{LANG_NUM_IS_EDITED}</strong><br>{LANG_MODERATOR} <strong>".$users[$row['moder']]."</strong><br>{LANG_LOG_TIME} <strong>".data_trans("mysql", "ua", $row['edit'])."</strong></p></td></tr>";

                                $admin_links_do = "";

                                if ($row['do_user'] == $_SESSION['user_id'] OR $user_p_mod == 1)
                                    {
                                        if (empty($row['do_made'])) $admin_links_do .= "<a onclick=\"skm_LockScreen()\" href=\"?do=made&id=".$row['id']."\" class=\"btn btn-warning btn-lg\" role=\"button\"><span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_IN_SET_AS_MADED}\"></span></a>";
                                        $admin_links_do .= "<a onclick=\"skm_LockScreen()\" href=\"jurnal_out.php?add=do&from=in_ep&id=".$row['id']."\" class=\"btn btn-success btn-lg\" role=\"button\"><span class=\"glyphicon glyphicon-send\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_NEW_OUT_WITH_IN_ID}\"></span></a>";
                                    }
                                if ($privat5 == 1) $admin_links_do .= "<a onclick=\"skm_LockScreen()\" href=\"?do=add&template=".$row['id']."\" class=\"btn btn-info btn-lg\" role=\"button\"><span class=\"glyphicon glyphicon-random\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_NEW_WITH_TEMPLATE}\"></span></a>";
                                $show_files = 0;
                                if ($row['add_user'] == $_SESSION['user_id'] OR $row['do_user'] == $_SESSION['user_id'] OR $user_p_mod == 1) $show_files = 1;
                                if ($show_files == 1) $admin_links_do .= "<a onclick=\"skm_LockScreen()\" href=\"?attach=".$row['id']."\" class=\"btn btn-success btn-lg\" role=\"button\"><span class=\"glyphicon glyphicon-floppy-save\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_USERS_ADMIN_EDIT_FILES}\"></span></a>";
                                $user_edit_num = 0;
                                if ($row['add_user'] == $_SESSION['user_id'] AND $_SESSION['user_year'] == date('Y')) $user_edit_num = 1;
                                if ($user_edit_num == 1 OR $user_p_mod == 1) $admin_links_do .= "<a onclick=\"skm_LockScreen()\" href=\"?edit=".$row['id']."\" class=\"btn btn-warning btn-lg\" role=\"button\"><span class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_USERS_ADMIN_EDIT}\"></span></a>";

                                $user_del_num = 0;
                                if ($row['user'] == $_SESSION['user_id'] AND $active == 1 AND $is_first == "" AND $_SESSION['user_year'] == date('Y')) $user_del_num = 1;
                                if ($user_p_mod == 1 AND $active == 1 AND $is_first == "" AND $_SESSION['user_year'] == date('Y')) $user_del_num = 1;
                                if ($user_del_num == 1) $admin_links_do .= "<a href=\"?delete_last=".$row['id']."\" class=\"btn btn-danger btn-lg\" role=\"button\" onClick=\"if(confirm('{LANG_REMOVE_NUM_CONFIRM}')) {return true;} return false;\"><span class=\"glyphicon glyphicon-remove-circle\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_USERS_ADMIN_DEL}\"></span></a>";

                                $glyphicon = "glyphicon glyphicon-eye-close";
                                $glyphicon_tooltip = "{LANG_JURNAL_IN_STATUS_1}";
                                $glyphicon_export = "{LANG_JURNAL_IN_STATUS_1}";
                                $glyphicon_search_url = "?do=search&status=1";
                                if (!empty($row['do_view'])) { $glyphicon = "glyphicon glyphicon-eye-open"; $glyphicon_export = "{LANG_JURNAL_IN_STATUS_2}"; $glyphicon_tooltip = "{LANG_JURNAL_IN_STATUS_2} ".data_trans("mysql", "ua", $row['do_view']); $glyphicon_search_url = "?do=search&status=2"; }
                                if (!empty($row['do_made'])) { $glyphicon = "glyphicon glyphicon-ok"; $glyphicon_export = "{LANG_JURNAL_IN_STATUS_3}"; $glyphicon_tooltip = "{LANG_JURNAL_IN_STATUS_3} ".data_trans("mysql", "ua", $row['do_made']); $glyphicon_search_url = "?do=search&status=3";}

                                $html_inform_users = "";
                                if (!empty($row['inform_users']))
                                    {
                                        $html_inform_users .= "<tr>
                                                    <td colspan=\"2\"><br><strong>{LANG_INFORM_USERS}</strong></td>
                                                </tr>";
                                        $inform_users_array = explode(",",$row['inform_users']);
                                        for ($i = 1; $i <= count($inform_users_array); $i++)
                                            {
                                                if (empty($inform_users_array[$i])) continue;
                                                $inform_user_array = explode("-",$inform_users_array[$i]);
                                                if ($inform_user_array[1] == "") $inform_user_array[1] = "<span class=\"glyphicon glyphicon-eye-close\"></span>";
                                                $html_inform_users .= "<tr>
                                                    <td align=\"right\">".$users[$inform_user_array[0]]."</td>
                                                    <td align=\"left\">".$inform_user_array[1]."</td>
                                                </tr>";
                                            }
                                    }

                                $jurnal_in_ep .= "
                                <tr valign=\"top\" align=\"center\" id=\"TRn".$row['id']."\" ".$tr_color_info.">
                                    <td valign=\"top\" align=\"center\"><abbr title=\"{LANG_NUM_INFO_PLUS}\"><strong><a data-toggle=\"modal\" href=\"#JOn".$row['id']."\" aria-expanded=\"false\" aria-controls=\"JOn".$row['id']."\">".$row['id']."</strong></a></abbr></td>
                                    <td valign=\"top\" align=\"center\"><a onclick=\"skm_LockScreen()\" href=\"".$glyphicon_search_url."\"><span class=\"".$glyphicon."\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"".$glyphicon_tooltip."\"></span></a></td>
                                    <td valign=\"top\" align=\"center\"><a onclick=\"skm_LockScreen()\" href=\"?do=search&get_data=".data_trans("mysql", "ua", $row['get_data'])."\">".data_trans("mysql", "ua", $row['get_data'])."</a></td>
                                    <td valign=\"top\" align=\"left\"><a onclick=\"skm_LockScreen()\" href=\"?do=search&org_name=".$row['org_name']."\">".$row['org_name']."</a></td>
                                    <td valign=\"top\" align=\"left\">".$row['org_index']."</td>
                                    <td valign=\"top\" align=\"center\"><a onclick=\"skm_LockScreen()\" href=\"?do=search&org_data=".data_trans("mysql", "ua", $row['org_data'])."\">".data_trans("mysql", "ua", $row['org_data'])."</a></td>
                                    <td valign=\"top\" align=\"left\">".$row['org_subj']."</td>
                                    <td valign=\"top\" align=\"left\"><a onclick=\"skm_LockScreen()\" href=\"?do=search&do_user=".$row['do_user']."\">".$users[$row['do_user']]."</a></td>
                                    <td valign=\"top\" align=\"left\">".$row['make_visa']."</td>
                                    <td valign=\"top\" align=\"center\">".data_trans("mysql", "ua", $row['make_data'])."</td>
                                </tr>";

                                $modals .= "
                                <div class=\"modal fade\" id=\"JOn".$row['id']."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"JOn".$row['id']."Label\">
                                  <div class=\"modal-dialog\" role=\"document\">
                                    <div class=\"modal-content\">
                                      <div class=\"modal-header\">
                                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"{LANG_JURN_OUT_NUM_CLOSE}\"><span aria-hidden=\"true\">&times;</span></button>
                                        <h4 class=\"modal-title text-center\" id=\"myModalLabel\">{LANG_JURNAL_IN_INFO_N} ".$row['id']."</h4>
                                      </div>
                                      <div class=\"modal-body text-center\">
                                        <table class=\"table table-hover\">
                                            ".$num_is_edited."
                                            <tr>
                                                <td align=\"right\"><strong>".data_trans("mysql", "ua", $row['add_time'])."</strong></td>
                                                <td align=\"left\">{LANG_ADD_USER} <abbr data-toggle=\"tooltip\" data-placement=\"bottom\" data-original-title=\"".$row['add_ip']."\"><strong>".$users[$row['add_user']]."</strong></abbr></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\">{LANG_GET_DATA_S}</td>
                                                <td align=\"left\"><strong>".data_trans("mysql", "ua", $row['get_data'])."</strong></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\">{LANG_ORG_NAME_S}: <strong>".$row['org_name']."</strong></td>
                                                <td align=\"left\"><kbd>№".$row['org_index'].", від ".data_trans("mysql", "ua", $row['org_data'])."</kbd></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\">{LANG_ORG_SUBJ}</td>
                                                <td align=\"left\"><strong>".$row['org_subj']."</strong></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\">{LANG_MAKE_VISA}: <strong>".$row['make_visa']."</strong></td>
                                                <td align=\"left\">{LANG_DO_USER}: <strong>".$users[$row['do_user']]."</strong></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\">{LANG_MAKE_DATA}</td>
                                                <td align=\"left\"><strong>".data_trans("mysql", "ua", $row['make_data'])."</strong></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\">{LANG_JURNAL_STEP_DO}</td>
                                                <td align=\"left\"><strong><span class=\"".$glyphicon."\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"".$glyphicon_tooltip."\"></span> ".$glyphicon_tooltip."</td>
                                            </tr>".$html_inform_users;

                                            if (!empty($row['out_year']))
                                                {
                                                    $row_nom = explode(":", $row['out_index']);
                                                    $modals .= "<tr>
                                                        <td align=\"right\">{LANG_OUT_DOC_INDEX}: </td>
                                                        <td align=\"left\"><strong>".$row_nom[0]."</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td align=\"right\">{LANG_NOMENCLATURA}: </td>
                                                        <td align=\"left\"><strong>".$row_nom[1]."</strong></td>
                                                    </tr>";
                                                }
                                        $modals .= "</table>
                                      </div>
                                      <div class=\"modal-footer\"><a href=\"#\" role=\"button\" class=\"btn btn-default btn-lg\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-original-title=\"{LANG_JURN_OUT_NUM_CLOSE}\"></span></a>".$admin_links_do."</div>
                                    </div>
                                  </div>
                                </div>";
                                $is_first = 1;
                                if (isset($_GET['export']) AND $_GET['export'] == "do")$export .=  "\"".$row['id']."\";\"".$glyphicon_export."\";\"".$users[$row['add_user']]."\";\"".data_trans("mysql", "ua", $row['add_time'])."\";\"".data_trans("mysql", "ua", $row['get_data'])."\";\"".$row['org_name']."\";\"".$row['org_index']."\";\"".data_trans("mysql", "ua", $row['org_data'])."\";\"".$row['org_subj']."\";\"".$users[$row['do_user']]."\";\"".$row['make_visa']."\";\"".data_trans("mysql", "ua", $row['make_data'])."\";\"".data_trans("mysql", "ua", $row['do_view'])."\";\"".data_trans("mysql", "ua", $row['do_made'])."\";\n";
                            }
                    }
                    else
                    {
                        $page.= file_get_contents("templates/information.html");
                        $page = str_replace("{INFORMATION}", "{LANG_JURNAL_OUT_EMPTY}", $page);
                    }
                $page = str_replace("{JURNAL_IN_EP}", $jurnal_in_ep, $page);
                $page .= $modals;
            }
        $page = str_replace("{JURNAL_IN_EP_TOP_STAT}", "&nbsp;", $page);
        $page = str_replace("{JURNAL_IN_EP_AFFIX}", "", $page);
    }
    else
    {
        $loging_do = "{LANG_LOG_JURNAL_IN_EP_403}";
        include ('inc/loging.php');
        header('HTTP/1.1 403 Forbidden');
        $page.= file_get_contents("templates/information_danger.html");
        $page = str_replace("{INFORMATION}", "{LANG_403}", $page);
        $timeout = "index.php";
    }

include ("inc/blender.php");
