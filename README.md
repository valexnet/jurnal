#Журнал вихідної, вхідної та вхідної ел. пошти
==============================================

###Журнал в розробці
--------------------

На даний час працює вихідна кореспонденція, журнал бланків, вхідна кореспонденція.

###Наступні кроки
-----------------
  - Вхідна Ел. Пошта.

###Останні зміни
----------------
####30.10.2015
  - Додана можливість редагувати стутус зареєстрованого бланку по вихідній кореспонденції. Якщо це можливо.

####21.10.2015
  - Виправлено навігацію і пошук по вихідній кореспонденції.
  - Виправлено помилки з Журналом бланків.

####20.10.2015
  - Додана можливість створення вхідної кореспонденції із статусом "Виконано".
  - В швидкому пошуку тепер можна використовувати знак * - як будь-яка к-ть символів.
  - Якщо закрити доступ до Журналу, в шапці буде відповідне повідомлення.
  - Додано пошук та експорт до файлу у вхідній кореспонденції.
  - Змінено вивід навігації по сторінкам вхідної кореспонденції.
  - Додано можливість ставивти відмітку про Виконання Виконавцю та Модератору для вхідної кореспонденції.
  - Дадано можливість взяття вихідного номеру із вхідної кореспонденції, із автоматичним заповненням полів.
  - Оптимізовано код, вивільнена таблиця БД з номерами бланків.

####16.10.2015
  - Виправлено помилку при додаванні вихідного номеру, якщо було вказано помилкове значення вартості відправки - після повернення вартість відправки не відображалась.
  - Додано виринаюче вікно при існуванні нових вхідних номерів для користувача.
  - Додано поле "статус док." при перегляді вхідних номерів.
  - Додано змінну в глобальні налаштування "Макс. к-ть записів на сторінку" для зменшення можливого навантаження на сервер користувачем.
  - Виправлено помилку коли користувач при зміні к-ті записів на сторінку із вхідної кореспонденції попадав у вихідну.

####13.10.2015
  - Виправлено помилку при додаванні користувача коли логін співпадав з логіном вилученого користувача.
  - Додано підказки при вводі в деякі форми.
  - Додана можливість брати номери за шаблонами минулих номерів.
  - Додана можливість редагування вхідних номерів.
  - Кнопки "Зауваження та пропозиції" та "GitHub" змінені на показ інформації у виринаючих вікнах.
  - Прибрані копірайти.

####12.10.2015
  - Змінено структуру БД по вхідним номерам, працює запис до БД вхідних номерів.
  - Змінено формат дати з MySQL до Українського формату: ДД.ММ.РРРР
  - Вивід вхідних номерів та нівігація по сторінкам.
  - Вилучення останнього вхідного номеру автором або модератором.
  - Додано повідомлення у "Глобальні Налаштування" про стан підключення модуля ZIP.
  - Додано можливість управління файлами у вхідній кореспонденції для автора та модератора, також перегляд для виконавця.

####09.10.2015
  - Додано форму додавання нового вхідного номеру та перевірка даних.

####08.10.2015
  - Додано структуру БД для Вихідної кореспонденції.

####07.10.2015
  - Додано можливість експорту в Розширеному пошуці.

####06.10.2015
  - Додано Розширений пошук.
  - Виправлено мовні помилки.

####02.10.2015
  - Додано права модератора для вихідної кореспонденції.
  - Додано відмітка з інформацією про редагування файлу.
  - Додано перевірку на існування мовного файлу при додаванні/редагуванні користувача.
  - Піключені вспливаючі підказки до деяких кнопок.
  - Якщо використовується браузер IE менше 9-тої версії, буде показуватись повідомлення про застарілість браузеру.
  - Додано Affix, закріплене меню навігації при промотці сторінки.

####01.10.2015
  - Додано посилання на головну сторінку в шапці.
  - Змінено форму додавання вихідного номеру.
  - Додано вартість відправки якщо спосіб = поштова відправка.
  - Підключено validator.min.js для перевірки форми.
  - Виправлено перевірки при записі нових вихідних номерів.

####30.09.2015
  - Оптимізовано шапку сайту та меню.
  - При натисканні на вихідний номер - бачим розширену інформацію.
  - Вилучено малюнки, які не використовуються.
  - Преписаний метод Багатомовності.
  - Виправлено помилку з часом взяття вихідного номеру.
  - Реалізовано 3 типи установки журналу. 1 Нова установка. 2 Створення нового файлу налаштувань. 3 Відновлення резервної копії БД.

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
