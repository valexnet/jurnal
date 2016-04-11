<?php
session_start();

if (isset($_GET['install']) AND $_GET['install'] == "do" AND !file_exists("inc/db_connect.txt")) include ('inc/install.php');

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if (isset($_SESSION['user_id']))
    {
        $f_address = "true";
    }
    else
    {
        if ($c_ano == 0 && !isset($_GET['team']) && !isset($_GET['auth']))
            {
                DIE(file_get_contents("templates/close.html"));
            }
            else
            {
                $f_address = "true";
            }
    }
if ($f_address == "true")
    {
        /* Наша команда */
        if (isset($_GET['team']))
            {
                $address = "true";
                $page.= file_get_contents("templates/team.html");

                $query = "SELECT * FROM `users` WHERE `p_config`<>'0' OR `p_users`<>'0' OR `p_ip`<>'0' ORDER BY `time` DESC;";
                $res = mysql_query($query) or die(mysql_error());
                $queryes_num++;
                $numberall = mysql_num_rows($res);
                if ($numberall > 0)
                    {
                        $adminwork = 0;
                        while ($row=mysql_fetch_array($res))
                            {
                                if ($row['work']==1) $adminwork ++;
                            }
                        if ($adminwork > 0)
                            {
                                $res = mysql_query($query) or die(mysql_error());
                                $queryes_num++;
                                while ($row=mysql_fetch_array($res))
                                    {
                                        $adminlist_prv = '';
                                        if ($row['p_config']==1) $adminlist_prv .= "{LANG_USER_ADMIN_CONFIG} ";
                                        if ($row['p_users']==1) $adminlist_prv .= "{LANG_PPL_P_USERS} ";
                                        if ($row['p_ip']==1) $adminlist_prv .= "{LANG_USER_P_IP} ";
                                        if (time() >= $row['time'] + 2592000 ) {$user_color = " color=\"red\"";} else {$user_color = " color=\"green\"";}

                                        if ($row['work']==1) $adminlist_tmp .= "
                            <ul>
                                <p><b>".$row['name']."</b><br />(".$adminlist_prv.")</p>
                                <li>{LANG_TEL1}: <b>".$row['tel1']."</b></li>
                                <li>{LANG_TEL2}: <b>".$row['tel2']."</b></li>
                                <li>{LANG_TEL3}: <b>".$row['tel3']."</b></li>
                                <li>{LANG_USER_TIME_ACTIVITY}: <font".$user_color."><b>".date('Y.m.d H:i:s' ,$row['time'])."</b></font></li>
                            </ul>
                            <hr>";
                                    }
                                $page = str_replace("{ADMIN_LIST}", $adminlist_tmp, $page);
                            }
                            else
                            {
                                $page = str_replace("{ADMIN_LIST}", "{LANG_NO_ADMIN_IN_BD}", $page);
                            }
                    }
                    else
                    {
                        $page = str_replace("{ADMIN_LIST}", "{LANG_NO_ADMIN_IN_BD}", $page);
                    }
            }
        /* Чат */
        if (isset($_GET['chat']))
            {
                $address = "true";
                $_SESSION['last_message_id'] = 0;
                if (!isset($_SESSION['user_id'])) $user_name = $_SERVER['REMOTE_ADDR'];
                mysql_query("INSERT INTO `messages` (`time`, `name`, `text`) VALUES ('".time()."', '".$user_name."', 'new_user')");
                $page.= file_get_contents("templates/chat.html");
                $files = glob("templates/images/smiles/smile-*.png");
                $c = count($files);
                $html_smiles = "";
                $a = 0;
                foreach ($files as $file)
                    {
                        $a++;
                        $html_smiles .= "<img onclick=\"InsSm('".basename($file, ".png")."');\" src=\"templates/images/smiles/".basename($file)."\"</img> ";
                        if ($a == 10)
                            {
                                $a = 0;
                                $html_smiles .= "\n";
                            }
                    }
                $page = str_replace("{SMILES_LIST}", $html_smiles, $page);
            }

        /* Головна сторінка */
        if ($address <> "true")
            {
                if (isset($_SESSION['user_id']))
                    {
                        $page.= file_get_contents("templates/first_page.html");
                    }
                    else
                    {
                        if ($_SESSION['user_login_error_num'] >= 4)
                            {
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_LOGIN_ERR_4_TIMES}", $page);
                            }

                        $page.= file_get_contents("templates/user_login.html");
                        if ($c_lch == 1)
                            {
                                $query = "SELECT `login`,`name`,`ip` FROM `users` WHERE `del`='0' ORDER BY `name` ;";
                                $res = mysql_query($query) or die(mysql_error());
                                $queryes_num++;

                                while ($row=mysql_fetch_array($res))
                                    {
                                        if ($row['ip'] == $_SERVER['REMOTE_ADDR'])
                                            {
                                                $users_list.= "<OPTION selected value = \"".$row['login']."\">".$row['name']."</OPTION>";
                                            }
                                            else
                                            {
                                                $users_list.= "<OPTION value = \"".$row['login']."\">".$row['name']."</OPTION>";
                                            }
                                    }

                                $page = str_replace("{USERS_LIST}", "<SELECT name=\"login\" class=\"form-control\" />".$users_list."</SELECT>", $page);
                            }
                            else
                            {
                                $page = str_replace("{USERS_LIST}", "<input class=\"form-control\" type=\"text\" name=\"login\" />", $page);
                            }
                    }
            }
    }
include ("inc/blender.php");
?>
