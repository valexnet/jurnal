#Автоматизована Система Журнал
==============================
###Графік розробки
------------------
  - Усунення помилок та зауважень.
  - Додавання нових можливостей.
  - Розробка документації.

###Останні зміни
----------------
####v.14.1 beta "Punctum" від 11.04.2016
  - Виправлено помилку із БД для вихідної кореспонденції для MySQL v.5.0+.

####v.14.0 beta "Punctum" від 11.04.2016
  - Додано підтримку PHP 5.2+.
  - Додано підтримку браузера OPERA 32+
  - Додано журнал ІТ, наповнення інвентарних номерів, КТ та характеристик, ПЗ - в розробці.
  - Виправлено експорт всієї вихідної кореспонденції у файл.
  - Вилучено ефекти при показі modal повідомлень.

####v.13.0 "Puella" від 10.03.2016
  - Додано інформування про прострочені задачі.
  - Мінімальні версії браузерів: Internet Explorer 11+, Mozilla Firefox 40+, Google Chrome 48+. Повязано з переходом на jQuery 2.
  - Додано "Журнал обліку висновків" та права на нього.
  - Вилучено AFFIX.

####v.12.0 "Tabernaculum" від 26.02.2016
  - У приватність додано права "Редагування розписаних листів виконавцем" - користувач може править лист якщо він росписаний на нього.
  - У вихідну кореспонденцію в інформацію про номер додано посилання на створення листів для локального почтового клієнта та OWA.
  - Виправлено помилку з IE коли знімалась галочка з ознайомленням користувача в переліку він не зникав.
  - Тепер при додаванні чи пошуку номера сторінка автоматично гортається до форми.
  - Виправлено помилку при редагуванні користувача коли логін співпадав з логіном вилученого користувача.

####v.11.1 "Subrubet" від 25.02.2016
  - У "Глобальні Налаштування", додано значення PHP "register_globals" та інформація по підключенню розширення IMAP.
  - Змінено реагування на помилки при додаванні нових записів, тепер форма відображається разом із помилкою.
  - Додано посилання на створення шаблону листа для локального почтового клієнта.
  - У формах додавання та редагування відбулись зміни: 1. Додано календарик для вибору дати і часу; 2. Підказки які показувались у деяких полях не зникали якщо їх не оберати - виправлено, тепер при клікові на пустому полі вони зникають.

####v.11.0 "Subrubet" від 12.01.2016
  - Вилучена можливість правки довідників. Для уникнення можливих звязаних помилок в мабутньому.
  - Додано шаблон індексу вихідного документа у "Глобальні Налаштування", доступні змінні: [index], [str], [nom], [usr-id], [year]. Початкове значення: [index]/[str]-[nom]

####v.10.3 "Sollertia" від 05.01.2016
  - Додано повідомлення, при не вірному заповнені поля "Строк виконання".
  - Поле "Номер документа" для вхідної кор. та ел. пошти тепер не обовязкове, при відправці пустого поля, воно буде замінено на "б/н".
  - Виправлено меню "Адміністрування" у Чаті.

####v.10.2 "Sollertia" від 28.12.2015
  - Додано розділ допомоги - Чат.
  - Збільшено мін. значення скріпта при імпорті з файл-бакапу. Вилучено повідомлення про пароль адміна.
  - Збільшено з 70 до 200 символів показу назви номенклатури для вихідної кореспонденції.
  - Вилучено блокування екрану у формах, були випадки коли вказувались не вірні дані і вони не відправлялись на сервер, а форма блокувалась.
  - Змінено перевірку e-mail адреси адміністратора при установці.
  - Виправлено помилки із перекладом.

####v.10.1 "Sollertia" від 28.12.2015
  - Додано розділ допомоги.

####v.10.0 "Sollertia" від 22.12.2015
  - Перейменовано форми "№ док." та "Дата док." Додавання та Редагування "Вхідної кореспондеції" та "Вхідної ел. пошти".
  - Додано автоматичну вставку "дату документа" та "час одержання" у форми додавання "Вхідної кореспондеції" та "Вхідної ел. пошти".
  - Змінено інформування в управліннях користувачами, раніше були мілкі помилки.
  - Додано повідомлення "жовтим" коли місце на сервері меньше 6 ГБ, та "червоним" коли місця меньше 2 ГБ.
  - Додано можливість інформування користувачів про нові записи у "Вхідній кореспонденції" та "Вхідній ел. пошти".

####v.9.5 "Lacplesis" від 20.12.2015
  - Додано повідомлення, про нольові таймаути бакапу і відправки бакапу на e-mail, при ручному запуску із "Глобальних Налаштувань".

####v.9.4 "Lacplesis" від 20.12.2015
  - Додано повідомлення, у разі не підключеного розширення IMAP в PHP при використанні функції "Додати із Поштового Серверу".

####v.9.3 "Lacplesis" від 08.12.2015
  - Виправлено мілкі помилки.
  - Пересортовано колонки у Вихідній кореспонденції.

####v.9.2 "Lacplesis" від 03.12.2015
  - Додано команди для модератора в чаті "admin clean": очистка повідомлень в базі даних та у користувачів, "admin ban IP": блокування вказаної IP адреси.
  - Працює перетвореня ссилок (http,https,ftp) в чаті.
  - Підключено звук вхідних повідомлень в чаті.
  - Виправлено помилку із відправкою листа для всіх адмінів по управлінню доступом при автоматичному блокуванні IP

####v.9.1 "Lacplesis" від 02.12.2015
  - Додано блокування сторінки з таймером при користуванні АС Журнал.
  - Виправлена помилка авторизації при вимкненому доступі до АС Журнал.

####v.9.0 "Lacplesis" від 11.11.2015
  - Додано формат файлу eml до дозволених розширень.
  - Виправлено помилку коли при вилучені даних з БД залишались файли, які привязані до вилучених даних.
  - Додана функція імпорту листа до файлу eml та його архівація zip-оп при імпорті даних з IMAP сервера.
  - Додана можливість скачати лист в форматі eml при перегляді останніх листів на IMAP сервері.
  - Виправлено помилку із шляхами зберігання файлів для різних типів ОС.
  - Виправлено помилку із зберіганням форми глобальних налаштувань по бакапам.

####v.8.0 від 09.11.2015
  - Додано Чат
  - Зменшено таймаут імпорту даних із поштового сервера до 5 секунд.
  - Відтепер дані логу і чату не попадають в резервну копію, що зменшує розмір бакапу.

####v.7.2 від 05.11.2015
  - Для вхідної ЕП додано функцію імпорту даних із поштового сервера по IMAP:143.

####v.7.1 від 04.11.2015
  - phpinfo перенесено для показу тільки користувачам із правами на "Глобальні налаштування".
  - Авторизацію перенесено на головну сторінку, кнопки профілю та входу вилучені для гостя.
  - При переході на Журнал реєстрації бланків, відмінено показ результатів пошуку.
  - Змінена навігація в розіділі "Перегляд журналу роботи".
  - Додано швидкий пошук в розіділі "Перегляд журналу роботи".
  - Додано альтернативну версію, почнеться з 7,1 - яка не чіпає БД, а вказує тільки на зміни у файлах.

####03.11.2015
  - Виправлено помилку із новою установкою АС Журнал.
  - Виправлено помилку із знуленням результатів пошуку по журналу зареєстрованих бланків.
  - Виправлено помилку із закритим доступом вхідної ЕП для користувачів у яких в профілі доступ відкрито.
  - Виправлено помилку із блокуванням IP адрес.

####02.11.2015
  - Відмінено показ помідомлення про завершення сесії для користувачів які автоматично авторизовані по IP адресі.
  - Виправлено помилку при взятті номера по шаблону вхідної кореспонденції.
  - Виправлено помилку при швидкому пошуку по даті одержання та даті документа вхідної кореспонденції.
  - Додано вхідну ел. кореспонденцію.

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
