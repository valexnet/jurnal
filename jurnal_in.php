<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

// Журнал вхідної кореспонденції
$page.= file_get_contents("templates/information.html");
$page = str_replace("{INFORMATION}", "{LANG_IN_DEVELOPMENT}", $page);

include ("inc/blender.php");
?>