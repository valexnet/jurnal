/* ========================================================================
 * Custom script
 * ======================================================================== */

function skm_LockScreen()
	{
		var timer = 30;
		var lock = document.getElementById('skm_LockPane');
		lock.className = 'LockOn';
		lock.innerHTML = '<h2><img src="templates/images/logo.png"><br /><font color="green">Ваш запит відправлено</font></h2>';
		var timerId = setInterval(function()
			{
				timer = timer - 1;
				if (timer >= 1)
					{
						lock.innerHTML = '<h2><img src="templates/images/logo.png"><br /><font color="green">Ваш запит відправлено<br />чекайте: ' + timer + ' сек...</font></h2>';
					}
					else
					{
						lock.innerHTML = '<h2><img src="templates/images/logo.png"><br /><font color="red">Сервер не відповідає</font><br /><a href="#" onclick="skm_OpenScreen();">Повернутись</a></h2>';
						clearTimeout(timerId);
					}
				//timer = parseFloat(timer.toFixed(1));
			}, 1000);
	}

function skm_OpenScreen()
	{
		var lock = document.getElementById('skm_LockPane');
		lock.className = 'LockOff';
	}

function show_admin_menu()
	{
		var menu = document.getElementById('dropdown_admin_menu');
		if (menu.className == "dropdown")
			{
				menu.className = 'dropdown open';
			}
			else
			{
				menu.className = 'dropdown';
			}
	}
