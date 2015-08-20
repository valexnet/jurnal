<?php
session_start();

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if ($user_p_log == 1)
	{
		$page.= file_get_contents("templates/log_admin.html");
		$query = "SELECT `id` FROM `log`;";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		$numberall_log = mysql_num_rows($res);
		$limit = 30;
		$end_page = $numberall_log - $limit;
		$start_page = 0;
		if(isset($_GET['page_num']))
			{
				$page_num = $_GET['page_num'];
				if ($page_num < 0) $page_num = 0;
				$nexpage = $page_num + $limit;
				$prev_page = $page_num - $limit;
				if ($prev_page < 0) $prev_page = 0;
			}
			else
			{
				$page_num = 0;
				$nexpage = $page_num + $limit;
				$prev_page = 0;
			}
		$query = "SELECT * FROM `log` ORDER BY `id` DESC LIMIT ".$page_num." , ".$limit.";";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		$numberall = mysql_num_rows($res);
		while ($row=mysql_fetch_array($res))
			{
				if ($row['user']==0)
					{
						$user = "{ANONYMOUS}";
					}
					else
					{
						$query2 = "SELECT `name` FROM `users` WHERE `id`='".$row['user']."' LIMIT 1;";
						$res2 = mysql_query($query2) or die(mysql_error());
						$queryes_num++;
						$numberall2 = mysql_num_rows($res2);
						if ($numberall2 == 0)
							{
								$user = $row['user']." {USER_DELETED}";
							}
							else
							{
								while ($row2=mysql_fetch_array($res2))
									{
										$user = $row2['name'];
									}
							}
					}
				$template_log.="<tr valign=\"middle\" align=\"center\">
							<td align=\"center\">".date('d.m.Y H:i:s', $row['time'])."</td>
							<td align=\"center\">".$user."</td>
							<td align=\"center\">".$row['ip']."</td>
							<td align=\"left\">".$row['do']."</td>
						</tr>";
			}
		$page_navy.="<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			<tr valign=\"top\" align=\"center\">
				<td valign=\"middle\" width=\"30\"><a href=\"loging.php?page_num=".$start_page."\"><img src=\"templates/images/home.JPG\" border=\"0\" alt=\"{LANG_LOG_NAVY_HOME}\" title=\"{LANG_LOG_NAVY_HOME}\"></a></td>";
				if ($page_num > 0)  $page_navy.="<td valign=\"middle\" width=\"30\"><a href=\"loging.php?page_num=".$prev_page."\"><img src=\"templates/images/left.JPG\" border=\"0\" alt=\"{LANG_LOG_NAVY_LEFT}\" title=\"{LANG_LOG_NAVY_LEFT}\"></a></td>";
				if ($page_num < $end_page)  $page_navy.="<td valign=\"middle\" width=\"30\"><a href=\"loging.php?page_num=".$nexpage."\"><img src=\"templates/images/right.JPG\" border=\"0\" alt=\"{LANG_LOG_NAVY_RIGHT}\" title=\"{LANG_LOG_NAVY_RIGHT}\"></a></td>";
				$page_navy.="<td valign=\"middle\" width=\"30\"><a href=\"loging.php?page_num=".$end_page."\"><img src=\"templates/images/end.JPG\" border=\"0\" alt=\"{LANG_LOG_NAVY_END}\" title=\"{LANG_LOG_NAVY_END}\"></a></td>
			</tr>
		</table>";
		$page = str_replace("{LOG_VIEWS}", $template_log, $page);
		$page = str_replace("{LOG_NAVY}", $page_navy, $page);
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
?>