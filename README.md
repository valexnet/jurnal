#Журнал вихідної, вхідної та вхідної ел. пошти
==============================================

###Журнал в розробці
--------------------

На даний час працює вихідна кореспонденція та журнал бланків.

###Наступні кроки
-----------------

  - Система пошуку та швидкий пошук.
  - Відновлення з копії БД.
  - Вхідна кореспонденція.

###Останні зміни
----------------
####29.09.2015
  - Допрацьовані всі шаблони.
  - Підключено bootstrap.min.js та jquery-1.11.3.min.js для функціонування без доступу до мережі Інтернет.

####28.09.2015
  - Змінено шаблони на bootstrap.
  - Виправлено незначні помилки.

####25.09.2015
  - Додано швидкий пошук по номенклатурі, способу відправки, даті та користувачу.
  - Виправлено помилку коли користувач взяв вихідний номер та не міг переглядати журнал вихідної.

####24.09.2015
  - Якщо підключено розширення ZIP, резервна копія архівується. 
  - Виводиться повідомлення якщо версія скріпта не відповідає версії БД.
  - Оптимізовано на вивільнено кілька налаштувань.
  - Тепер mysqldump не використовується при резервному копіюванні.
  - Резервну копію можна створити та/або відправити поштою з Глобальних Налаштувань.

####23.09.2015
  - Можливість інсталяції журналу при першому доступі та створення БД автоматично.
  
####21.09.2015
  - Додано менеджер управління файлами.
