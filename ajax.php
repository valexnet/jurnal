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
														//if ($count != 1) echo ", ";
														echo "<li><a onclick=\"document.getElementById('".$for."').value = '".$row[$for]."'; document.getElementById('".$help."').className = 'col-sm-5'; \">".$row[$for]."</a></li>";
														if ($count >= 10) exit;
													}
											}
										echo "</ul>";
									}
							}
					}
			}
	}

mysql_close();

?>