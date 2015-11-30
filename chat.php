<?php
include ('inc/config.php');

if (!isset($_SESSION['user_id'])) $user_nick = $_SERVER['REMOTE_ADDR'];
if (isset($user_name)) $user_nick = $user_name;
if (isset($_SESSION['user_nick'])) $user_nick = $_SESSION['user_nick'];

if (!isset($_SESSION['last_message_id'])) $_SESSION['last_message_id'] = 0;
$view_ip = 0;
if (isset($user_p_config) AND $user_p_config == 1) $view_ip = 1;

Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache");
Header("Content-Type: text/javascript; charset=utf-8");

if (isset($_GET['act'])) {
    switch ($_GET['act']) {
        case "send" : // если она равняется send, вызываем функцию Send()
            Send($user_nick);
            break;
        case "load" : // если она равняется load, вызываем функцию Load()
            Load($user_nick);
            break;
        case "members" : // если она равняется load, вызываем функцию Load()
            Members($view_ip);
            break;
        default : // если ни тому и не другому  - выходим
            echo time();
    }
}

function Send($user_nick)
	{
		$text = $_GET['text'];
		$srch = array("\"", "<", ">", "`", "'");
		$rpls = array("&quot;", "&lt;", "&gt;", "&#096;", "&lsquo;");
		$text = str_replace($srch, $rpls, $text);
		if (strlen(utf8_decode($text)) > 1)
			{
				$do = 0;
				if (preg_match("/^nick (.{3,})/", $text, $a))
					{
						$do = 1;
						$_SESSION['user_nick'] = $a[1];
						mysql_query("INSERT INTO `messages` (`ip`, `time`, `name`, `text`) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$user_nick."', '<font color=\"green\">змінив нік на <b>".$a[1]."</b></font>')");
					}

				if (preg_match("/^me (.*)/", $text, $a))
					{
						$do = 1;
						mysql_query("INSERT INTO `messages` (`ip`, `time`, `name`, `text`) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$user_nick."', '<b>".$a[1]."</b>')");
					}

				if (preg_match("/^red (.*)/", $text, $a))
					{
						$do = 1;
						mysql_query("INSERT INTO `messages` (`ip`, `time`, `name`, `text`) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$user_nick."', '<font color=\"red\"><b>".$a[1]."</b></font>')");
					}

				if (preg_match("/^admin (.*)/", $text, $a))
					{
						$do = 1;
						mysql_query("INSERT INTO `messages` (`ip`, `time`, `name`, `text`) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".time()."', '', '<font color=\"red\"><b>".$a[1]."</b></font>')");
					}

				if (preg_match("/^img:(.*)/", $text, $a))
					{
						$do = 1;
						mysql_query("INSERT INTO `messages` (`ip`, `time`, `name`, `text`) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$user_nick."', '<img src=\"".$a[1]."\" />')");
					}

				if ($do == 0)
					{
						mysql_query("INSERT INTO `messages` (`ip`, `time`, `name`, `text`) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$user_nick."', '".$text."')");
					}
				echo "OK";
				$old_messages = time() - 18000;
				mysql_query("DELETE FROM `messages` WHERE `time` < '".$old_messages."' ; ");
			}
			else
			{
				echo ".";
			}
	}

function Load($user_nick)
	{
		mysql_query("INSERT INTO `messages` (`ip`, `time`, `name`, `text`) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$user_nick."', 'renew_user')");
		$query = mysql_query("SELECT * FROM `messages` WHERE `id` > ".$_SESSION['last_message_id']." ORDER BY `id` ; ");
		if (mysql_num_rows($query) > 0)
			{
				$text = '';
				while ($row = mysql_fetch_array($query))
					{
						for ($i = 1; $i <= 1000; $i++)
							{
								if (preg_match("/:(smile-[0-9]{3}):/", $row['text'], $a)) 
									{
										$row['text'] = str_replace(":".$a[1].":", "<img src=\"templates/images/smiles/".$a[1].".png\"</img>", $row['text']);
									}
									else
									{
										$i = 1000;
									}
							}
						if ($row['text'] != "new_user" AND $row['text'] != "renew_user") $text .= "<p>[".date('H:i:s' ,$row['time'])."] <b>".$row['name']."</b> &raquo; ".$row['text']."</p>";
						$last_row = $row['id'];
					}
				$_SESSION['last_message_id'] = $last_row;
				echo $text;
			}
			else
			{
				echo "OK";
			}
	}

function Members($view_ip)
	{
		$query = mysql_query("SELECT `ip`,`name` FROM `messages` WHERE `time` >= '".(time() - 15)."' ORDER BY `name` ; ");
		if (mysql_num_rows($query) > 0)
			{
				$text = "";
				$names = array();
				while ($row = mysql_fetch_array($query))
					{
						if (!in_array($row['name'], $names) AND $row['name'] != "")
							{
								$names[] = $row['name'];
								if ($view_ip == 1)
									{
										$text .= "<a onclick=\"document.getElementById('message').value += ' ".$row['name']."'; document.getElementById('message').focus();\" class=\"btn btn-default btn-block\" role=\"button\">".$row['name']." <span class=\"label label-success\">".$row['ip']."</span></a><br>";
									}
									else
									{
										$text .= "<a onclick=\"document.getElementById('message').value += ' ".$row['name']."'; document.getElementById('message').focus();\" class=\"btn btn-default btn-block\" role=\"button\">".$row['name']."</a><br>";
									}
							}
					}
				$text .= "<a onclick=\"DoLoadMembers()\" class=\"btn btn-default btn-block\" role=\"button\">Всього: ".count($names)." (".date('H:i:s').")</a>";
				echo $text;
			}
			else
			{
				echo "<a onclick=\"DoLoadMembers()\" class=\"btn btn-default btn-block\" role=\"button\">Чат пустий ".count($names)." (".date('H:i:s').")</a><br>";
			}
	}

?>