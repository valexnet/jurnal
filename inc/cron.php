<?php

// CRON Створення копій БД, та вилучення старих.
$query = "SELECT * FROM `cron` WHERE `name`='backup' LIMIT 1 ;";
$res = mysql_query($query) or die(mysql_error());
$queryes_num++;
while ($row=mysql_fetch_array($res))
	{
		if (time() >= $row['time'] + $row['last'] && $row['time'] <> 0)
			{
				$backup_name = $c_dir."".time()."_".$dbName;
				$backup = shell_exec("".$c_bin."mysqldump.exe -u\"".$username."\" -p\"".$password."\" --result-file=\"".$backup_name."_auto.sql\" \"".$dbName."\"");
				$backup = shell_exec("rar a -r \"".$backup_name."_auto.rar\" -x\"uploads\" \"".$c_dir2."*.*\"");
				$backup = shell_exec("rar a -df -r \"".$backup_name."_auto.rar\" \"".$backup_name."_auto.sql\"");
				@copy($backup_name."_auto.rar", $c_dirp . time()."_".$dbName.".rar");
				$query2 = "SELECT * FROM `cron` WHERE `name`='backup_on_email' LIMIT 1 ;";
				$res2 = mysql_query($query2) or die(mysql_error());
				$queryes_num++;
				$loging_do = "{LANG_ARHIV_MADE_OK}";
				include ('inc/loging.php');
				while ($row2=mysql_fetch_array($res2))
					{
						if (time() >= $row2['last'] + $row2['time'] && $row2['time'] <> 0) 
							{
								$mail->AddAddress($db_connect[4], $db_connect[10]);
								$mail->Subject = "Архів АС Журнал";
								$mail->MsgHTML(file_get_contents('templates/mail_arhiv.html'));
								$mail->AddAttachment($backup_name . '_auto.rar');
								$mail->Send();
								@mysql_query("UPDATE `cron` SET `last`='".time()."' WHERE `name`='backup_on_email' LIMIT 1 ;");
								$queryes_num++;
								$loging_do = "{LANG_MAIL_ARHIV_SEND}";
								include ('inc/loging.php');
							}
					}
				if ($c_bul > 0)
					{
						if (is_dir($c_dir))
							{
								if ($dir = opendir($c_dir))
									{
										while (false !== ($file = readdir($dir)))
											{
												if ($file != "." && $file != "..")
													{
														if (preg_match("/\b.rar\b/i", $file))
															{
																$all_files = $all_files + 1;
																if ($a_file <> "" AND $b_file <> "" AND $c_file == "") $c_file = $file;
																if ($a_file <> "" AND $b_file == "") $b_file = $file;
																if ($a_file == "") $a_file = $file;
																if ($all_files == $c_bul + 1)
																	{
																		@unlink($c_dir."/".$a_file);
																	}
																if ($all_files == $c_bul + 2)
																	{
																		@unlink($c_dir."/".$a_file);
																		@unlink($c_dir."/".$b_file);
																	}
																if ($all_files >= $c_bul + 3)
																	{
																		@unlink($c_dir."/".$a_file);
																		@unlink($c_dir."/".$b_file);
																		@unlink($c_dir."/".$c_file);
																	}
															}
													}
											}
									}
							}
					}
				@mysql_query("UPDATE `cron` SET `last`='".time()."' WHERE `name`='backup' LIMIT 1 ;");
				$queryes_num++;
			}
	}

//if (md5_file('inc/config.php') <> "9c431b4b0fa4670ab454e26a3fcdc473") DIE (base64_decode("QU5USVZJUlVTIFBST1RFQ1Qg")." ".md5_file('inc/config.php'));

?>