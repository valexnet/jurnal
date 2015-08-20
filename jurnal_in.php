<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

// Журнал вхідної кореспонденції

include ("inc/blender.php");
?>