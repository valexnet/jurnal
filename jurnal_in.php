<?php
session_start();

if (isset($_POST['show_year']) AND $_POST['show_year'] > 0 AND $_POST['show_year'] < 9999 AND $_POST['show_year'][3] <> "") $_SESSION['user_year'] = $_POST['show_year'];
if (isset($_POST['show_num_list']) AND $_POST['show_num_list'] > 0 AND $_POST['show_num_list'] < 9999) $_SESSION['user_page_limit'] = $_POST['show_num_list'];

include ('inc/config.php');
$page.= file_get_contents("templates/header.html");

if (isset($_SESSION['user_id']))
	{
		$page .= file_get_contents("templates/jurnal_in_header.html");

		$query = "SHOW TABLES LIKE \"DB_".date('Y')."_IN\";";
		$res = mysql_query($query) or die(mysql_error());
		$queryes_num++;
		if (mysql_num_rows($res) == 0)
			{
				if (@opendir("uploads/".date('Y')."/IN"))
					{
						@closedir("uploads/".date('Y')."/IN");
					}
					else
					{
						mkdir("uploads/".date('Y')."/IN", null, true) or die("Can't create new folder - uploads/".date('Y')."/IN");
					}
				$query = file_get_contents("inc/db_in.txt");
				$query = str_replace("{YEAR}", date('Y'), $query);
				mysql_query($query) or die(mysql_error());
			}

		if (isset($_GET['do']) && $_GET['do'] == 'add')
			{
				$adres = 'true';
				if ($privat4 == 1)
					{
						if (isset($_POST['get_data']) and !empty($_POST['get_data']))
							{
								// Убераєм з даних лишне
								$_POST['get_data'] = htmlspecialchars($_POST['get_data'], ENT_QUOTES);
								$_POST['org_name'] = htmlspecialchars($_POST['org_name'], ENT_QUOTES);
								$_POST['org_index'] = htmlspecialchars($_POST['org_index'], ENT_QUOTES);
								$_POST['org_data'] = htmlspecialchars($_POST['org_data'], ENT_QUOTES);
								$_POST['org_subj'] = htmlspecialchars($_POST['org_subj'], ENT_QUOTES);
								$_POST['make_visa'] = htmlspecialchars($_POST['make_visa'], ENT_QUOTES);
								$_POST['make_data'] = htmlspecialchars($_POST['make_data'], ENT_QUOTES);
								$_POST['do_user'] = htmlspecialchars($_POST['do_user'], ENT_QUOTES);
								// Перевірка даних
								$error = "";
								if (!check_data($_POST['get_data'])) $error .= "{LANG_FORM_NO_GET_DATA}<br>";
								if (!check_data($_POST['org_data'])) $error .= "{LANG_FORM_NO_ORG_DATA}<br>";
								if ($_POST['make_data'] != "" AND !check_data($_POST['make_data'])) $error .= "{LANG_FORM_NO_MAKE_DATA}<br>";
								if (!preg_match("/^([1-9]|[1-9][0-9]{1,})$/" ,$_POST['do_user'])) $error .= "{LANG_FORM_NO_DO_USER}<br>";
								if ($_POST['org_name'] == "") $error .= "{LANG_FORM_NO_ORG_NAME}<br>";
								if ($_POST['org_index'] == "") $error .= "{LANG_FORM_NO_ORG_INDEX}<br>";
								if ($_POST['org_subj'] == "") $error .= "{LANG_FORM_NO_ORG_SUBJ}<br>";
								if ($_POST['make_visa'] == "") $error .= "{LANG_FORM_NO_MAKE_VISA}<br>";
								
								if ($error == "") $error .= "НА цьому все, запис до БД у розробці...";
								
								if ($error == "")
									{
										// save to db
										
										$_SESSION['error_in_add'] = '';
										$_SESSION['error_in_add_get_data'] = '';
										$_SESSION['error_in_add_org_name'] = '';
										$_SESSION['error_in_add_org_index'] = '';
										$_SESSION['error_in_add_org_data'] = '';
										$_SESSION['error_in_add_org_subj'] = '';
										$_SESSION['error_in_add_make_visa'] = '';
										$_SESSION['error_in_add_make_data'] = '';
										$_SESSION['error_in_add_do_user'] = '';
									}
									else
									{
										$page.= file_get_contents("templates/information_danger.html");
										$page = str_replace("{INFORMATION}", $error, $page);
										
										$_SESSION['error_in_add'] = 1;
										$_SESSION['error_in_add_get_data'] = $_POST['get_data'];
										$_SESSION['error_in_add_org_name'] = $_POST['org_name'];
										$_SESSION['error_in_add_org_index'] = $_POST['org_index'];
										$_SESSION['error_in_add_org_data'] = $_POST['org_data'];
										$_SESSION['error_in_add_org_subj'] = $_POST['org_subj'];
										$_SESSION['error_in_add_make_visa'] = $_POST['make_visa'];
										$_SESSION['error_in_add_make_data'] = $_POST['make_data'];
										$_SESSION['error_in_add_do_user'] = $_POST['do_user'];
									}
							}
							else
							{
								$page.= file_get_contents("templates/jurnal_in_add.html");
								$select_user = 0;
								if (isset($_SESSION['error_in_add']) AND $_SESSION['error_in_add'] == 1)
									{
										$select_user = $_SESSION['error_in_add_do_user'];
										$page = str_replace("{FORM_GET_DATA}", $_SESSION['error_in_add_get_data'], $page);
										$page = str_replace("{FORM_ORG_NAME}", $_SESSION['error_in_add_org_name'], $page);
										$page = str_replace("{FORM_ORG_INDEX}", $_SESSION['error_in_add_org_index'], $page);
										$page = str_replace("{FORM_ORG_DATA}", $_SESSION['error_in_add_org_data'], $page);
										$page = str_replace("{FORM_ORG_SUBJ}", $_SESSION['error_in_add_org_subj'], $page);
										$page = str_replace("{FORM_MAKE_VISA}", $_SESSION['error_in_add_make_visa'], $page);
										$page = str_replace("{FORM_MAKE_DATA}", $_SESSION['error_in_add_make_data'], $page);
									}
								$page = str_replace("{FORM_GET_DATA}", "", $page);
								$page = str_replace("{FORM_ORG_NAME}", "", $page);
								$page = str_replace("{FORM_ORG_INDEX}", "", $page);
								$page = str_replace("{FORM_ORG_DATA}", "", $page);
								$page = str_replace("{FORM_ORG_SUBJ}", "", $page);
								$page = str_replace("{FORM_MAKE_VISA}", "", $page);
								$page = str_replace("{FORM_MAKE_DATA}", "", $page);
								$html_select_users = get_users_selection_options($select_user, 0, "name", "ASC", 0);
								$queryes_num++;
								$page = str_replace("{FORM_DO_USER}", $html_select_users, $page);
							}
					}
					else
					{
						$page.= file_get_contents("templates/information_danger.html");
						$page = str_replace("{INFORMATION}", "{LANG_PRIVAT4_NO}", $page);
					}
			}

		if (isset($_GET['do']) && $_GET['do'] == 'src')
			{
				$adres = 'true';
				$page.= file_get_contents("templates/information.html");
				$page = str_replace("{INFORMATION}", "У РОЗРОБЦІ", $page);
			}

		if ($adres <> 'true')
			{
				$page = str_replace("{JURNAL_IN_TOP_STAT}", file_get_contents("templates/jurnal_in_top_stat.html"), $page);
				$page = str_replace("{JURNAL_IN_AFFIX}", "data-spy=\"affix\" data-offset-top=\"170\"", $page);

				$page.= file_get_contents("templates/information.html");
				$page = str_replace("{INFORMATION}", "У РОЗРОБЦІ", $page);
			}
		$page = str_replace("{JURNAL_IN_TOP_STAT}", "", $page);
		$page = str_replace("{JURNAL_IN_AFFIX}", "", $page);

		// Убрать пізніше
		$page = str_replace("{JURNAL_IN_STAT}", "", $page);
		$page = str_replace("{NAVY}", "", $page);
	}
	else
	{
		$loging_do = "{LANG_LOG_JURNAL_IN_403}";
		include ('inc/loging.php');
		header('HTTP/1.1 403 Forbidden');
		$page.= file_get_contents("templates/information_danger.html");
		$page = str_replace("{INFORMATION}", "{LANG_403}", $page);
		$timeout = "index.php";
	}

include ("inc/blender.php");
?>