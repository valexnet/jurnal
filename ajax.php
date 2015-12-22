<?
session_start();
include ('inc/config.php');

if (isset($_SESSION['user_id']))
    {
        if (isset($_GET['help']) AND !empty($_GET['help'])
            AND isset($_GET['for']) AND !empty($_GET['for'])
            AND isset($_GET['year']) AND !empty($_GET['year'])
            AND isset($_GET['where']) AND !empty($_GET['where'])
            AND isset($_GET['input']) AND !empty($_GET['input']))
            {
                $for = "";
                $where = "";
                $input = str_replace($srch, $rpls, $_GET['input']);
                $input = str_replace("*", "%", $_GET['input']);
                $help = str_replace($srch, $rpls, $_GET['help']);
                if (preg_match("/^[0-9]{4}$/", $_GET['year']))
                    {
                        if ($_GET['for'] == "org_name") $for = $_GET['for'];
                        if ($_GET['for'] == "org_subj") $for = $_GET['for'];
                        if ($_GET['for'] == "make_visa") $for = $_GET['for'];
                        if ($_GET['for'] == "to") $for = $_GET['for'];
                        if ($_GET['for'] == "to_num") $for = $_GET['for'];
                        if ($_GET['for'] == "subj") $for = $_GET['for'];
                        if ($_GET['where'] == "jurnal_in") $where = "db_".$_GET['year']."_in";
                        if ($_GET['where'] == "jurnal_in_ep") $where = "db_".$_GET['year']."_in_ep";
                        if ($_GET['where'] == "jurnal_out") $where = "db_".$_GET['year']."_out";

                        if ($for != "" AND $where != "")
                            {
                                $query = "SELECT `".$for."` FROM `".$where."` WHERE `".$for."` LIKE '".$input."%' ORDER BY `id` DESC LIMIT 100 ;";
                                $res = mysql_query($query) or die(mysql_error());
                                if (mysql_num_rows($res) > 0)
                                    {
                                        $count = 0;
                                        $find = array();
                                        $result = "";
                                        echo "<ul class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuDivider\">";
                                        while ($row=mysql_fetch_array($res))
                                            {
                                                if (!in_array($row[$for], $find))
                                                    {
                                                        $count++;
                                                        $find[] = $row[$for];
                                                        echo "<li><a onclick=\"document.getElementById('".$for."').value = '".$row[$for]."'; document.getElementById('".$help."').className = 'col-sm-5'; \">".$row[$for]."</a></li>";
                                                        if ($count >= 10) exit;
                                                    }
                                            }
                                        echo "</ul>";
                                    }
                                    else
                                    {

                                    }
                            }
                    }
            }

        if (isset($_GET['do']) AND !empty($_GET['do']))
            {
                if ($_GET['do'] == "check_new_rows")
                    {
                        $html = "";
                        $query = "SELECT `id`, `add_time`, `org_name`, `org_subj` FROM `db_".date('Y')."_in` WHERE `do_user`='".$_SESSION['user_id']."' AND `do_view` IS NULL AND `do_made` IS NULL ; ";
                        $res = mysql_query($query) or die(mysql_error());
                        if (mysql_num_rows($res) > 0)
                            {
                                $html .= "Для Вас є ".mysql_num_rows($res)." новий(х) запис(ів) у вхідній кореспонденції.<hr>";
                                while ($row=mysql_fetch_array($res))
                                    {
                                        $html .= "<strong>".$row['add_time']."</strong>, №<strong><a href=\"jurnal_in.php?do=search&id=".$row['id']."\">".$row['id']."</a></strong>, <strong>".$row['org_name']."</strong> - <strong>".$row['org_subj']."</strong><br>";
                                    }
                                $html .= "<hr>";
                            }

                        $query = "SELECT `id`, `add_time`, `org_name`, `org_subj` FROM `db_".date('Y')."_in_ep` WHERE `do_user`='".$_SESSION['user_id']."' AND `do_view` IS NULL AND `do_made` IS NULL ; ";
                        $res = mysql_query($query) or die(mysql_error());
                        if (mysql_num_rows($res) > 0)
                            {
                                $html .= "Для Вас є ".mysql_num_rows($res)." новий(х) запис(ів) у вхідній ел. кореспонденції.<hr>";
                                while ($row=mysql_fetch_array($res))
                                    {
                                        $html .= "<strong>".$row['add_time']."</strong>, №<strong><a href=\"jurnal_in_ep.php?do=search&id=".$row['id']."\">".$row['id']."</a></strong>, <strong>".$row['org_name']."</strong> - <strong>".$row['org_subj']."</strong><br>";
                                    }
                                $html .= "<hr>";
                            }
                            
                        $query = "SELECT `id`, `add_time`, `org_name`, `org_subj` FROM `db_".date('Y')."_in` WHERE `inform_users` LIKE '%,".$_SESSION['user_id'].",%' ; ";
                        $res = mysql_query($query) or die(mysql_error());
                        if (mysql_num_rows($res) > 0)
                            {
                                $html .= "Для Вас є ".mysql_num_rows($res)." новий(х) запис(ів) для ознайомлення у вхідній кореспонденції.<hr>";
                                while ($row=mysql_fetch_array($res))
                                    {
                                        $html .= "<strong>".$row['add_time']."</strong>, №<strong><a href=\"jurnal_in.php?do=search&id=".$row['id']."\">".$row['id']."</a></strong>, <strong>".$row['org_name']."</strong> - <strong>".$row['org_subj']."</strong><br>";
                                    }
                                $html .= "<hr>";
                            }

                        $query = "SELECT `id`, `add_time`, `org_name`, `org_subj` FROM `db_".date('Y')."_in_ep` WHERE `inform_users` LIKE '%,".$_SESSION['user_id'].",%' ; ";
                        $res = mysql_query($query) or die(mysql_error());
                        if (mysql_num_rows($res) > 0)
                            {
                                $html .= "Для Вас є ".mysql_num_rows($res)." новий(х) запис(ів) для ознайомлення у вхідній електронній пошті.<hr>";
                                while ($row=mysql_fetch_array($res))
                                    {
                                        $html .= "<strong>".$row['add_time']."</strong>, №<strong><a href=\"jurnal_in_ep.php?do=search&id=".$row['id']."\">".$row['id']."</a></strong>, <strong>".$row['org_name']."</strong> - <strong>".$row['org_subj']."</strong><br>";
                                    }
                                $html .= "<hr>";
                            }
                            
                        if ($html != "") echo "<div class=\"modal-header\">
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"{LANG_JURN_OUT_NUM_CLOSE}\"><span aria-hidden=\"true\">&times;</span></button>
                                <h4 class=\"modal-title text-center\" id=\"myModalLabel\">Нове повідомлення</h4>
                            </div>
                            <div class=\"modal-body text-center\">
                                ".$html."
                            </div>
                            <div class=\"modal-footer\">
                                <a class=\"btn btn-warning\" onClick=\"set_view_time();\">Принято до уваги</a>
                            </div>";
                    }
                if ($_GET['do'] == "set_view")
                    {
                        $query = "UPDATE `db_".date('Y')."_in` SET `do_view`='".date('Y-m-d H:i:s')."', `do_view_ip`='".$_SERVER['REMOTE_ADDR']."' WHERE `do_user`='".$_SESSION['user_id']."' AND `do_view` IS NULL ; ";
                        $res = mysql_query($query) or die(mysql_error());
                        $query = "UPDATE `db_".date('Y')."_in_ep` SET `do_view`='".date('Y-m-d H:i:s')."', `do_view_ip`='".$_SERVER['REMOTE_ADDR']."' WHERE `do_user`='".$_SESSION['user_id']."' AND `do_view` IS NULL ; ";
                        $res = mysql_query($query) or die(mysql_error());
                        $query = "SELECT `id`,`inform_users` FROM `db_".date('Y')."_in` WHERE `inform_users` LIKE '%,".$_SESSION['user_id'].",%' ; ";
                        $res = mysql_query($query) or die(mysql_error());
                        if (mysql_num_rows($res) > 0)
                            {
                                while ($row=mysql_fetch_array($res))
                                    {
                                        $row['inform_users'] = str_replace(",".$_SESSION['user_id'].",", ",".$_SESSION['user_id']."-".date('d.m.Y H:i:s').",", $row['inform_users']);
                                        $query = "UPDATE `db_".date('Y')."_in` SET `inform_users`='".$row['inform_users']."' WHERE `id`='".$row['id']."' LIMIT 1 ; ";
                                        $res = mysql_query($query) or die(mysql_error());
                                    }
                            }
                        $query = "SELECT `id`,`inform_users` FROM `db_".date('Y')."_in_ep` WHERE `inform_users` LIKE '%,".$_SESSION['user_id'].",%' ; ";
                        $res = mysql_query($query) or die(mysql_error());
                        if (mysql_num_rows($res) > 0)
                            {
                                while ($row=mysql_fetch_array($res))
                                    {
                                        $row['inform_users'] = str_replace(",".$_SESSION['user_id'].",", ",".$_SESSION['user_id']."-".date('d.m.Y H:i:s').",", $row['inform_users']);
                                        $query = "UPDATE `db_".date('Y')."_in_ep` SET `inform_users`='".$row['inform_users']."' WHERE `id`='".$row['id']."' LIMIT 1 ; ";
                                        $res = mysql_query($query) or die(mysql_error());
                                    }
                            }
                        echo "OK";
                    }
            }
    }

mysql_close();
exit;
