<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if ($user_p_mod == 1)
    {
        if (isset($_GET['add']))
            {
                $ndi_do = "true";
                if (isset($_GET['save']))
                    {
                        if ($_POST['index'] <> "" AND $_POST['name'] <> "")
                            {
                                $index = $_POST['index'];
                                $name = $_POST['name'];
                                $query = "SELECT `index`,`name` FROM `structura` WHERE `index`='".$index."';";
                                $res = mysql_query($query) or die(mysql_error());
                                $queryes_num++;
                                $numberall = mysql_num_rows($res);
                                //if ($numberall == 0)
                                    //{
                                        $query = "INSERT INTO `structura` (`id`, `index`, `name`, `user`, `time`, `do`, `work`) VALUES (NULL , '".$index."', '".$name."', '".$_SESSION['user_id']."', '".time()."', '{LANG_NDI_STR_ADMIN_ADD}', '1');";
                                        $res = mysql_query($query) or die(mysql_error());
                                        $queryes_num++;
                                        $page.= file_get_contents("templates/information_success.html");
                                        $page = str_replace("{INFORMATION}", "{LANG_STR_ADD_OK}", $page);
                                        $timeout = "ndi_str.php";
                                    //}
                                    //else
                                    //{
                                        //$page.= file_get_contents("templates/information_danger.html");
                                        //$page = str_replace("{INFORMATION}", "{LANG_STR_ADD_EXIST}", $page);
                                    //}
                            }
                            else
                            {
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_STR_ADD_NAME_OR_INDEX_EMPTY}", $page);
                            }
                    }
                    else
                    {
                        $page.= file_get_contents("templates/ndi_str_add.html");
                    }
            }

        if (isset($_GET['edit']))
            {
                $ndi_do = "true";
				$page.= file_get_contents("templates/information_danger.html");
                $page = str_replace("{INFORMATION}", "{LANG_403}", $page);
				/*
                if (isset($_GET['save']))
                    {
                        if ($_POST['index'] <> "" AND $_POST['name'] <> "")
                            {
                                $index = $_POST['index'];
                                $name = $_POST['name'];
                                $id = $_GET['edit'];
                                $query = "SELECT * FROM `structura` WHERE `id`='".$id."' AND `work`='1' ;";
                                $res = mysql_query($query) or die(mysql_error());
                                $queryes_num++;
                                $numberall = mysql_num_rows($res);
                                if ($numberall == 1)
                                    {
                                        $query = "UPDATE `structura` SET `index`='".$index."', `name`='".$name."', `user`='".$_SESSION['user_id']."', `time`='".time()."', `do`='{LANG_NDI_STR_ADMIN_EDIT}' WHERE `id`='".$id."' LIMIT 1;";
                                        $res = mysql_query($query) or die(mysql_error());
                                        $queryes_num++;
                                        $page.= file_get_contents("templates/information_success.html");
                                        $page = str_replace("{INFORMATION}", "{LANG_STR_EDIT_OK}", $page);
                                        $timeout = "ndi_str.php";
                                    }
                                    else
                                    {
                                        $page.= file_get_contents("templates/information_danger.html");
                                        $page = str_replace("{INFORMATION}", "{LANG_STR_EDIT_NOT_EXIST}", $page);
                                    }
                            }
                            else
                            {
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_STR_ADD_NAME_OR_INDEX_EMPTY}", $page);
                            }
                    }
                    else
                    {
                        $edit = $_GET['edit'];
                        $query = "SELECT * FROM `structura` WHERE `id`='".$edit."' AND `work`='1' LIMIT 1;";
                        $res = mysql_query($query) or die(mysql_error());
                        $numberall = mysql_num_rows($res);
                        if ($numberall <> 0)
                            {
                                $page.= file_get_contents("templates/ndi_str_edit.html");
                                while ($row=mysql_fetch_array($res))
                                    {
                                        $page = str_replace("{ID}", $row['id'], $page);
                                        $page = str_replace("{INDEX}", $row['index'], $page);
                                        $page = str_replace("{NAME}", $row['name'], $page);
                                    }
                            }
                            else
                            {
                                $page.= file_get_contents("templates/information_danger.html");
                                $page = str_replace("{INFORMATION}", "{LANG_STR_DEL_NOT_EXIST}", $page);
                            }
                    }
				*/
            }

        if (isset($_GET['del']))
            {
                $del = $_GET['del'];
                $query = "SELECT * FROM `structura` WHERE `id`='".$del."';";
                $res = mysql_query($query) or die(mysql_error());
                $queryes_num++;
                $numberall = mysql_num_rows($res);
                if ($numberall == 1)
                    {
                        //$query = "DELETE FROM `structura` WHERE `id` = '".$del."' LIMIT 1;";
                        $query = "UPDATE `structura` SET `work`='0', `user`='".$_SESSION['user_id']."', `time`='".time()."', `do`='{LANG_NDI_STR_ADMIN_DELETE}' WHERE `id`='".$del."' LIMIT 1;";
                        $res = mysql_query($query) or die(mysql_error());
                        $queryes_num++;
                        $page.= file_get_contents("templates/information_success.html");
                        $page = str_replace("{INFORMATION}", "{LANG_STR_DEL_OK}", $page);
                        $loging_do = "{LANG_LOG_STR_DEL} ".$del;
                        include ('inc/loging.php');
                    }
                    else
                    {
                        $page.= file_get_contents("templates/information_danger.html");
                        $page = str_replace("{INFORMATION}", "{LANG_STR_DEL_NOT_EXIST}", $page);
                    }
            }

        if ($ndi_do <> "true")
            {
                $query = "SELECT * FROM `structura` WHERE `work`='1' ORDER BY `index`;";
                $res = mysql_query($query) or die(mysql_error());
                $queryes_num++;
                $numberall = mysql_num_rows($res);
                if ($numberall > 0)
                    {
                        $page.= file_get_contents("templates/ndi_str.html");
                        while ($row=mysql_fetch_array($res))
                            {
                                if ($old_admin_id <> $row['user'])
                                    {
                                        $query2 = "SELECT `name` FROM `users` WHERE `id`='".$row['user']."' LIMIT 1 ;";
                                        $res2 = mysql_query($query2) or die(mysql_error());
                                        $queryes_num++;
                                        if (mysql_num_rows($res2) > 0)
                                            {
                                                while ($row2=mysql_fetch_array($res2))
                                                    {
                                                        $user = $row2['name'];
                                                        $old_admin_id = $row['user'];
                                                        $old_admin_name = $user;
                                                    }
                                            }
                                            else
                                            {
                                                $user = "{LANG_USERS_ADMIN_DELETED}";
                                            }
                                    }
                                    else
                                    {
                                        $user = $old_admin_name;
                                    }

                                $color = $color + 1;
                                if ($color == 1) {$bgcolor ="";}
                                if ($color == 2) {$bgcolor ="bgcolor=\"#D3EDF6\""; $color = 0;}
                                $page_tmp .= "<tr valign=\"middle\" align=\"center\">
                                    <td ".$bgcolor." height=\"20\">".$row['index']."</td>
                                    <td ".$bgcolor." align=\"left\">".$row['name']."</td>
                                    <td ".$bgcolor." >".$user."</td>
                                    <td ".$bgcolor." >".date('d.m.Y H:i:s', $row['time'])."</td>
                                    <td ".$bgcolor." >".$row['do']."</td>
                                    <td ".$bgcolor." valign=\"bottom\">
                                        <!-- &nbsp;<a href=\"?edit=".$row['id']."\"><img src=\"templates/images/hammer_screwdriver.png\" border=\"0\" alt=\"{LANG_USERS_ADMIN_EDIT}\" title=\"{LANG_USERS_ADMIN_EDIT}\"></a> //-->
                                        &nbsp;<a href=\"?del=".$row['id']."\" onClick=\"if(confirm('{LANG_STR_ADMIN_DEL_CONFIRM} ".$row['index'].", ".$row['name']." ?')) {return true;} return false;\"><img src=\"templates/images/cross_octagon.png\" border=\"0\" alt=\"{LANG_USERS_ADMIN_DEL}\" title=\"{LANG_USERS_ADMIN_DEL}\"></a>
                                    </td>
                                </tr>
                                ";
                            }
                        $page = str_replace("{STR_VIEWS}", $page_tmp, $page);
                        $page = str_replace("{STR_STATS}", "{LANG_STR_STATS}" . $numberall, $page);
                    }
                    else
                    {
                        $page.= file_get_contents("templates/information_danger.html");
                        $page = str_replace("{INFORMATION}", "{LANG_STR_EMPTY}<br /><a href=\"?add\">{LANG_STR_ADMIN_ADD}</a>", $page);
                    }
            }
    }
    else
    {
        $loging_do = "{LANG_LOG_NDI_403}";
        include ('inc/loging.php');
        header('HTTP/1.1 403 Forbidden');
        $page.= file_get_contents("templates/information_danger.html");
        $page = str_replace("{INFORMATION}", "{LANG_403}", $page);
        $timeout = "index.php";
    }
include ("inc/blender.php");
?>
