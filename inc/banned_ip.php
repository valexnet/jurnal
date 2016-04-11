<?php

$loging_do = "{LANG_LOG_USER_BANNED}";
include ('inc/loging.php');

@mysql_query("INSERT INTO `banned` (`id`, `time`, `ip`, `where`) VALUES (NULL, '".time()."', '".$_SERVER['REMOTE_ADDR']."' , '".$_SERVER['REQUEST_URI']."');") or die(mysql_error());
$queryes_num++;
$query4 = "SELECT * FROM `users` WHERE `p_ip`='1';";
$res4 = mysql_query($query4) or die(mysql_error());
$queryes_num++;
$numberall4 = mysql_num_rows($res4);
if ($numberall4 <> 0)
    {
        while ($row4=mysql_fetch_array($res4))
            {
                if (filter_var($row4['mail1'], FILTER_VALIDATE_EMAIL))
                    {
                        $message = "Доброго дня ".$row4['name'].",\n\nВи являєтесь адміністратором по контролю доступу до АС Журнал.\n\nЩойно було блоковано IP адресу - ".$_SERVER['REMOTE_ADDR'].".\n".$_SERVER['REQUEST_URI']."\n\nАдреса: http://".$_SERVER['SERVER_ADDR'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI']." .\n\nЛист створено автоматично,\nХлівнюк В.О.,\n3427003";
                        $mail->AddAddress($row4['mail1'], $row4['name']);
                        $mail->Subject = "IP-адресу заблоковано";
                        $mail->MsgHTML($message);
                        $mail->Send();
                    }
            }
    }
@mysql_close();

DIE(file_get_contents("templates/banned.html"));
?>
