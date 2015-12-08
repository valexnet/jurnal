<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if ($user_p_log == 1)
    {
        $error = "";
        $where = "";
        $query_where = "";
        $search_pre = "";
        $users = get_users_names(0);
        $queryes_num++;

        if (isset($_GET['search']) AND $_GET['search'] == "do")
            {
                $error = "";
                if (isset($_GET['ip']) AND !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $_GET['ip'])) $error .= "{LANG_SEARCH_NO_IP}<br>";
                if (isset($_GET['user']) AND !preg_match("/^[0-9]{1,}$/", $_GET['user'])) $error .= "{LANG_SEARCH_NO_USER}<br>";
                if ($error == "")
                    {
                        $query_where = "`id` IS NOT NULL";
                        if (isset($_GET['ip']))
                            {
                                $where_lang = "{LANG_SEARCH_BY_IP} ".$_GET['ip'];
                                $query_where .= " AND `ip`='".$_GET['ip']."'";
                                $search_pre .= "&ip=".$_GET['ip'];
                            }
                        if (isset($_GET['user']))
                            {
                                $where_lang = "{LANG_SEARCH_BY_USER} ".$users[$_GET['user']];
                                $query_where .= " AND `user`='".$_GET['user']."'";
                                $search_pre .= "&user=".$_GET['user'];
                            }
                        if ($search_pre != "") $search_pre = "search=do".$search_pre."&";
                    }
                    else
                    {
                        $error = "true";
                        $page.= file_get_contents("templates/information_danger.html");
                        $page = str_replace("{INFORMATION}", "{LANG_SEARCH_ERROR}", $page);
                        $page.= file_get_contents("templates/information.html");
                        $page = str_replace("{INFORMATION}", $error, $page);
                    }
            }

        if ($error == "")
            {
                if (isset($where_lang) AND !empty($where_lang))
                    {
                        $page.= file_get_contents("templates/information.html");
                        $page = str_replace("{INFORMATION}", $where_lang." <a class=\"btn btn-default btn-sm\" href=\"loging.php\">{LANG_CLEAN_SERCH_RESULTS}</a>", $page);
                    }
                $page.= file_get_contents("templates/log_admin.html");
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
                $query_order_by = "ORDER BY `id` DESC ";
                if ($query_where != "") $where = " WHERE ".$query_where;
                $html_navy = get_navy("log", $where, $query_order_by, $active, $_SESSION['user_page_limit'], "loging.php?".$search_pre."page_num=");
                $queryes_num++;
                $page = str_replace("{NAVY}", $html_navy, $page);
                $query = "SELECT * FROM `log` ".$where." ".$query_order_by." ".$sql_limit." ; ";
                $res = mysql_query($query) or die(mysql_error());
                $queryes_num++;
                while ($row=mysql_fetch_array($res))
                    {
                        $template_log.="<tr valign=\"middle\" align=\"center\">
                                    <td align=\"center\">".date('d.m.Y H:i:s', $row['time'])."</td>
                                    <td align=\"center\"><a href=\"?search=do&user=".$row['user']."\">".$users[$row['user']]."</a></td>
                                    <td align=\"center\"><a href=\"?search=do&ip=".$row['ip']."\">".$row['ip']."</a></td>
                                    <td align=\"left\">".$row['do']."</td>
                                </tr>";
                    }
                $page = str_replace("{LOG_VIEWS}", $template_log, $page);
                $page = str_replace("{NAVY_INSERT_SHOW}", mysql_num_rows($res), $page);
            }
    }
    else
    {
        $loging_do = "{LANG_LOG_LOGING_403}";
        include ('inc/loging.php');
        header('HTTP/1.1 403 Forbidden');
        $page.= file_get_contents("templates/information.html");
        $page = str_replace("{INFORMATION}", "{LANG_403}", $page);
        $timeout = "index.php";
    }
include ("inc/blender.php");
