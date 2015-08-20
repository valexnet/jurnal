<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

// Журнал вхідної Ел. пошти

include ("inc/blender.php");
?>