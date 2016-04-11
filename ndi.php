<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if ($user_p_mod == 1)
    {
        $page.= file_get_contents("templates/ndi.html");
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
