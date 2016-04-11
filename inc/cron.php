<?php

// CRON Створення копій БД, та вилучення старих.
$query = "SELECT * FROM `cron` WHERE `name`='backup' LIMIT 1 ;";
$res = mysql_query($query) or die(mysql_error());
$queryes_num++;
while ($row=mysql_fetch_array($res))
    {
        if (time() >= $row['time'] + $row['last'] && $row['time'] <> 0)
            {
$return = "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET NAMES 'utf8' COLLATE 'utf8_general_ci';
SET CHARACTER SET 'utf8';
";
                // Берем перелік всіх таблиць
                $tables = array();
                $result = mysql_query('SHOW TABLES');
                $queryes_num++;
                while($row = mysql_fetch_row($result))
                    {
                        $tables[] = $row[0];
                    }
                // Для кожної таблиці робим свій цикл
                foreach($tables as $table)
                    {
                        $result = mysql_query('SELECT * FROM '.$table);
                        $queryes_num++;
                        $num_fields = mysql_num_fields($result);

                        $return.= 'DROP TABLE IF EXISTS '.$table.';';
                        $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
                        $queryes_num++;
                        $return.= "\n\n".$row2[1].";\n\n";

                        for ($i = 0; $i < $num_fields; $i++)
                            {
                                while($row = mysql_fetch_row($result))
                                    {
                                        if ($table != "messages" AND $table != "log")
                                            {
                                                $return.= 'INSERT INTO '.$table.' VALUES(';
                                                for($j=0; $j<$num_fields; $j++)
                                                    {
                                                        $row[$j] = addslashes($row[$j]);
                                                        $row[$j] = ereg_replace("\n","\\n",$row[$j]);
                                                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                                                        if ($j<($num_fields-1)) { $return.= ','; }
                                                    }
                                                $return.= ");\n";
                                            }
                                    }
                            }
                        $return.="\n\n\n";
                    }
                // Зберігаєм результат обробки всіх таблиць
                $date_file = date('Y-m-d_H-i-s');
                $file = "db_jurnal_v".$c_ver."_".$date_file.".sql";
                $zip_file = "db_jurnal_v".$c_ver."_".$date_file.".zip";
                $backup_file = "inc/backups/".$file;
                $handle = fopen($backup_file,'w+');
                fwrite($handle,$return);
                fclose($handle);
                $loging_do = "{LANG_ARHIV_MADE_OK}";
                include ('inc/loging.php');
                // Якщо підключено ZIP - архівуєм
                if(extension_loaded('zip'))
                    {
                        $zip = new ZipArchive();
                        if($zip->open("inc/backups/".$zip_file, ZIPARCHIVE::CREATE))
                            {
                                $zip->addFile($backup_file);
                                $zip->close();
                                unlink($backup_file);
                                // Змінюєм найви файлу на ZIP для дод. директорії
                                $backup_file = "inc/backups/".$zip_file;
                                $file = $zip_file;
                            }
                    }
                // Копіюємо архів в додаткову директорію
                if ($c_dirp != "") copy($backup_file, $c_dirp . $file);
                // Ставим мітку про створення архіву
                mysql_query("UPDATE `cron` SET `last`='".time()."' WHERE `name`='backup' LIMIT 1 ;");
                $queryes_num++;
                // Вилучаємо старі архіви доки не дойдемо до обмеженої к-ті
                if ($c_bul != 0)
                    {
                        $files = glob("inc/backups/db_jurnal_*.*");
                        $c = count($files)+1;
                        if ($c > $c_bul)
                            {
                                foreach ($files as $file)
                                    {
                                        $c = $c - 1;
                                        if ($c > $c_bul)
                                            {
                                                if (file_exists($file)) unlink($file);
                                            }
                                    }
                            }
                    }
                // Відправляємо архів поштою, якщо це необхідно
                $query2 = "SELECT * FROM `cron` WHERE `name`='backup_on_email' LIMIT 1 ;";
                $res2 = mysql_query($query2) or die(mysql_error());
                $queryes_num++;
                while ($row2=mysql_fetch_array($res2))
                    {
                        if (time() >= $row2['last'] + $row2['time'] && $row2['time'] <> 0)
                            {
                                $mail->AddAddress($db_connect[4], $db_connect[10]);
                                $mail->Subject = "Архів АС Журнал";
                                $mail->MsgHTML(file_get_contents('templates/mail_arhiv.html'));
                                $mail->AddAttachment($backup_file);
                                if ($mail->Send())
                                    {
                                        @mysql_query("UPDATE `cron` SET `last`='".time()."' WHERE `name`='backup_on_email' LIMIT 1 ;");
                                        $queryes_num++;
                                        $loging_do = "{LANG_MAIL_ARHIV_SEND}";
                                        include ('inc/loging.php');
                                    }
                                    else
                                    {
                                        $loging_do = "{LANG_MAIL_ARHIV_NOT_SEND}";
                                        include ('inc/loging.php');
                                    }
                            }
                    }
                @mysql_query("UPDATE `cron` SET `last`='".time()."' WHERE `name`='backup' LIMIT 1 ;");
                $queryes_num++;
            }
    }
?>
