<?PHP

// Перевірка переданих даних для первинної настройки
if (isset($_POST['mysql']) AND $_POST['mysql'] <> "") $post_mysql = 1;
if (isset($_POST['login']) AND $_POST['login'] <> "") $post_login = 1;
if (isset($_POST['password']) AND $_POST['password'] <> "") $post_password = 1;
if (isset($_POST['dbname']) AND $_POST['dbname'] <> "") $post_dbname = 1;
if (isset($_POST['email']) AND $_POST['email'] <> "") $post_email = 1;
if (isset($_POST['smtp_auth']) AND $_POST['smtp_auth'] <> "") $post_smtp_auth = 1;
if (isset($_POST['smtp_ssl']) AND $_POST['smtp_ssl'] <> "") $post_smtp_ssl = 1;
if (isset($_POST['smtp_server']) AND $_POST['smtp_server'] <> "") $post_smtp_server = 1;
if (isset($_POST['smtp_port']) AND $_POST['smtp_port'] <> "") $post_smtp_port = 1;
if (isset($_POST['smtp_pass']) AND $_POST['smtp_pass'] <> "") $post_smtp_pass = 1;
if (isset($_POST['email_name']) AND $_POST['email_name'] <> "") $post_email_name = 1;
if (isset($_POST['smtp_login']) AND $_POST['smtp_login'] <> "") $post_smtp_login = 1;
if (preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/", $_POST['email']))
	{
		$post_email = 1;
	}
	else
	{
		$post_email = 0;
	}
if ($post_mysql <> 1) $error .= "Не вказаний сервер MySQL<hr>";
if ($post_login <> 1) $error .= "Не вказаний логін MySQL<hr>";
if ($post_password <> 1) $error .= "Не вказаний пароль MySQL<hr>";
if ($post_dbname <> 1) $error .= "Не вказана БД MySQL<hr>";
if ($post_email <> 1 ) $error .= "E-mail адміністратора вказано не вірно<hr>";
if ($post_smtp_auth <> 1 ) $error .= "Не вказано SMTP авторизацію<hr>";
if ($post_smtp_ssl <> 1 ) $error .= "Не вказано SMTP SSL<hr>";
if ($post_smtp_server <> 1 ) $error .= "Не вказано SMTP SERVER<hr>";
if ($post_smtp_port <> 1 ) $error .= "Не вказано SMTP порт<hr>";
if ($post_smtp_pass  <> 1 ) $error .= "Не вказано SMTP пароль<hr>";
if ($post_email_name <> 1 ) $error .= "Не вказано від чийого імені будуть йти листи<hr>";
if ($post_smtp_login <> 1 ) $error .= "Не вказано SMTP логін<hr>";

// Шаблон сторінки
$page = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";

if ($error == "")
	{
		// Підключення до БД
		@mysql_connect($_POST['mysql'],$_POST['login'],$_POST['password']) OR $DB_ERROR = "true";
		if ($DB_ERROR <> "true")
			{
				if (@mysql_select_db($_POST['dbname']))
					{
						$tmp_db_exist = 1;
					}
					else
					{
						$tmp_db_exist = 0;
					}
				if ($tmp_db_exist == 0)
					{
						if (@mysql_query("CREATE DATABASE `".$_POST['dbname']."` ;")) $tmp_db_exist = 1;
					}
				if ($tmp_db_exist == 1)
					{
						@mysql_select_db($_POST['dbname']);
						$query = file_get_contents("inc/empty_db.sql");
						@mysql_query($query) or die(mysql_error());
						require_once('inc/class.phpmailer.php');
						include("inc/class.smtp.php");
						$mail = new PHPMailer(true);
						$mail->IsSMTP();
						if ($_POST['smtp_auth'] == "smtp_auth_true") $mail->SMTPAuth = true;
						if ($_POST['smtp_ssl'] == "ssl_true") $mail->SMTPSecure = "ssl";
						$mail->Host = $_POST['smtp_server'];
						$mail->Port = $_POST['smtp_port'];
						$mail->Username = $_POST['smtp_login'];
						$mail->Password = $_POST['smtp_pass'];
						$mail->SetFrom($_POST['email'], $_POST['email_name']);
						$mail->AddAddress($_POST['email'], $_POST['email_name']);
						$mail->Subject = "АС Журнал успішно встановлений";
						$mail->MsgHTML("<html><body>Якщо ви читаєте це повідомлення,<br>то АС Журнал був успішно встановлений.<br><br>Успішної вам праці ;)</body></html>");
						if ($mail->Send())
							{
								// Запис настройок
								$file = $_POST['mysql']."\r\n".$_POST['login']."\r\n".base64_encode($_POST['password'])."\r\n".$_POST['dbname']."\r\n".$_POST['email']."\r\n".$_POST['smtp_auth']."\r\n".$_POST['smtp_ssl']."\r\n".$_POST['smtp_server']."\r\n".$_POST['smtp_port']."\r\n".base64_encode($_POST['smtp_pass'])."\r\n".$_POST['email_name']."\r\n".$_POST['smtp_login']."\r\n";
								$f = fopen('inc/db_connect.txt', 'w');
								fwrite($f, $file);
								fclose($f);
								echo $page."</head><body>Журнал встановлено, перейдіть до <hr><a href=\"config.php?edit\">Глобальні налаштування</a>. Пароль Адміністратора: admin</body></html>";		
							}
							else
							{
								echo $page."</head><body>Не можливо підключитись до SMTP серверу, виправте дані.<hr><a href=\"index.php\">Повернутись</a></body></html>";		
							}
					}
					else
					{
						echo $page."</head><body>Бази Даних не існує і недостатньо прав на її створення, виправте дані.<hr><a href=\"index.php\">Повернутись</a></body></html>";		
					}
			}
			else
			{
				echo $page."</head><body>Не можливо підключитись до Сервера Бази Данних, виправте дані.<hr><a href=\"index.php\">Повернутись</a></body></html>";		
			}
	}
	else
	{
		echo $page."</head><body>".$error."<hr><a href=\"index.php\">Повернутись</a></body></html>";		
	}
DIE();
?>