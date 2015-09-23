-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Хост: 127.0.0.1
-- Время создания: Сен 23 2015 г., 10:25
-- Версия сервера: 5.0.45
-- Версия PHP: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- База данных: `jurnal`
-- 

-- --------------------------------------------------------

-- 
-- Структура таблицы `banned`
-- 

CREATE TABLE IF NOT EXISTS `banned` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `where` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Дамп данных таблицы `banned`
-- 


-- --------------------------------------------------------

-- 
-- Структура таблицы `config`
-- 

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `url` varchar(250) NOT NULL,
  `mysql_bin` varchar(250) NOT NULL,
  `backup_dir` varchar(250) NOT NULL,
  `backup_dir2` varchar(250) NOT NULL,
  `backup_plus` varchar(250) NOT NULL,
  `backup_lim` int(11) NOT NULL,
  `backup_time` int(11) NOT NULL,
  `anonymous` int(11) NOT NULL,
  `user_timeout` int(11) NOT NULL,
  `page_limit` int(11) NOT NULL,
  `login_choose` int(11) NOT NULL,
  `year_start` int(4) NOT NULL,
  `n_ray` varchar(4) NOT NULL,
  `reg_file` varchar(10240) NOT NULL,
  `file_size` int(9) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Дамп данных таблицы `config`
-- 

INSERT INTO `config` VALUES (1, 'УДКСУ у Тальнівському р-ні', 'http://10.98.97.32/jurnal/beta/', 'C:/AppServ/MySQL/bin/', 'C:/AppServ/BackUp/Jurnal/', 'C:/AppServ/www/jurnal/beta/', 'E:/BackUp/jurnal/beta/', 15, 1, 1, 3600, 20, 1, 2015, '2314', 'zip|rar|7z|txt|doc|xls|docx|xlcx|pdf|jpg', 10);

-- --------------------------------------------------------

-- 
-- Структура таблицы `cron`
-- 

CREATE TABLE IF NOT EXISTS `cron` (
  `name` varchar(250) NOT NULL,
  `time` int(11) NOT NULL,
  `last` int(11) NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Дамп данных таблицы `cron`
-- 

INSERT INTO `cron` VALUES ('backup', 3600, 0);
INSERT INTO `cron` VALUES ('backup_on_email', 172800, 0);

-- --------------------------------------------------------

-- 
-- Структура таблицы `log`
-- 

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(15) NOT NULL,
  `time` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `do` varchar(2500) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Дамп данных таблицы `log`
-- 

-- --------------------------------------------------------


-- 
-- Структура таблицы `nomenclatura`
-- 

CREATE TABLE IF NOT EXISTS `nomenclatura` (
  `id` int(11) NOT NULL auto_increment,
  `structura` int(10) NOT NULL,
  `index` varchar(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `user` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `do` varchar(250) NOT NULL,
  `work` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

-- 
-- Дамп данных таблицы `nomenclatura`
-- 

INSERT INTO `nomenclatura` VALUES (7, 3, '10', 'Листування з ГУДКУ з питань роботи управління', 1, 1343906824, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (8, 3, '11', 'Листування з міс. та рай. радами, держ. уст. про фінансування', 1, 1343906894, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (9, 3, '12', 'Листування з територ. орг. ДПІ, КРУ, НБУ та фін. орг.', 1, 1343906968, '{LANG_NDI_STR_ADMIN_EDIT}', 1);
INSERT INTO `nomenclatura` VALUES (10, 4, '10', 'Аналітичні інф. УДК щодо виконання ЗУ “Про боротьбу з корупцією”', 1, 1343906990, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (11, 4, '13', 'Інформаціїї по вакантних посадах', 1, 1343907055, '{LANG_NDI_STR_ADMIN_EDIT}', 1);
INSERT INTO `nomenclatura` VALUES (12, 4, '19', 'Списки резерву кадрів УДКСУ у Тальнівському р-ні', 1, 1343907081, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (13, 4, '30', 'Листування з ГУДКУ з кадрових питань та поточних справ', 1, 1343907094, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (14, 6, '40', 'Інформації', 1, 1343907106, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (15, 6, '44', 'Листування з ГУДКУ', 1, 1343907117, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (16, 6, '45', 'Листування з уст. та орг. р-ну', 1, 1343907127, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (17, 7, '09', 'Річний звіт державного бюджету', 1, 1343907145, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (18, 7, '10', 'Квартальні звіти УДКСУ про вик. ДБ', 1, 1343907158, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (19, 7, '11', 'Міс. фін. звіти УДКСУ про вик. ДБ', 1, 1343907169, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (20, 7, '12', 'Річний звіт обл. бюждету', 1, 1343907601, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (21, 7, '13', 'Квартальні звіти УДКСУ про вик. ОБ', 1, 1343907614, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (22, 7, '15', 'Річний фін. звіт про виконання МБ', 1, 1343907625, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (23, 7, '16', 'Квартальні звіти УДКСУ про вик. МБ', 1, 1343907637, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (24, 7, '17', 'Місячні фін. звіти УДКСУ про вик. МБ', 1, 1343907649, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (25, 7, '25', 'Оперативна інформація', 1, 1343907661, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (26, 7, '31', 'Звіти розпорядникі що припин. діяльність', 1, 1421395339, '{LANG_NDI_STR_ADMIN_EDIT}', 1);
INSERT INTO `nomenclatura` VALUES (27, 7, '32', 'Звіти в розрізі бюджетів', 1, 1421394985, '{LANG_NDI_STR_ADMIN_EDIT}', 1);
INSERT INTO `nomenclatura` VALUES (28, 7, '36', 'Листування з ГУДКУ з питань роботи відділу', 1, 1343907699, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (29, 7, '37', 'Листування з фінансовими органами КРВ з питань вик. МБ', 1, 1343907723, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (30, 7, '38', 'Листування з уст. та орг. з питать фін. звітності', 1, 1343907734, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (31, 8, '03', 'Річний звіт', 1, 1343907748, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (32, 8, '04', 'Квартальні звіти', 1, 1343907761, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (33, 8, '09', 'Місячні звіти по ЄСВ', 1, 1343907774, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (34, 8, '11', 'Довідки про доходи', 1, 1343907790, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (35, 8, '15', 'Листки непрацездатності', 1, 1343907802, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (36, 8, '22', 'Листування з ГУДКУ', 1, 1343907813, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (37, 8, '23', 'Листування орг. по фінроботі', 1, 1424179028, '{LANG_NDI_STR_ADMIN_EDIT}', 1);
INSERT INTO `nomenclatura` VALUES (38, 9, '09', 'Листування з ГУДКУ з питань роботи ППЗ', 1, 1343907837, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (41, 15, '06', 'Листування з ГУДКУ з питань захисту інформації', 1, 1350308123, '{LANG_NDI_STR_ADMIN_EDIT}', 1);
INSERT INTO `nomenclatura` VALUES (42, 7, '30', 'Протоколи про порушення бюдж. зак.', 1, 1421394945, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `nomenclatura` VALUES (43, 7, '33', 'Протоколи по інвент. рахунків', 1, 1421395017, '{LANG_NDI_STR_ADMIN_ADD}', 1);

-- --------------------------------------------------------

-- 
-- Структура таблицы `structura`
-- 

CREATE TABLE IF NOT EXISTS `structura` (
  `id` int(11) NOT NULL auto_increment,
  `index` varchar(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `user` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `do` varchar(250) NOT NULL,
  `work` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- 
-- Дамп данных таблицы `structura`
-- 

INSERT INTO `structura` VALUES (3, '01', 'Керівництво', 1, 1415894389, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `structura` VALUES (4, '02', 'Кадрова робота', 1, 1415894427, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `structura` VALUES (5, '03', 'Юристконсульт', 1, 1343804904, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `structura` VALUES (6, '04', 'Відділ бюд. надходжень, видатків...', 1, 1343804918, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `structura` VALUES (7, '05', 'Відділ фін. звітності та бух. обліку', 1, 1343804932, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `structura` VALUES (8, '06', 'Планово фінансова робота', 1, 1343804947, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `structura` VALUES (9, '07', 'Спеціаліст ІТ та КТ', 1, 1343804960, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `structura` VALUES (15, '08', 'АЗІ', 1, 1343909501, '{LANG_NDI_STR_ADMIN_EDIT}', 1);
INSERT INTO `structura` VALUES (11, '09', 'Архів', 1, 1343804984, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `structura` VALUES (12, '10', 'Комісія', 1, 1343805005, '{LANG_NDI_STR_ADMIN_ADD}', 1);
INSERT INTO `structura` VALUES (13, '11', 'Профспілковий комітет', 1, 1343805017, '{LANG_NDI_STR_ADMIN_ADD}', 1);

-- --------------------------------------------------------

-- 
-- Структура таблицы `users`
-- 

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(250) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `out` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `time` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `ip_c` int(11) NOT NULL,
  `a_ip` int(11) NOT NULL,
  `l_ip` varchar(15) NOT NULL,
  `work` int(11) NOT NULL,
  `lang` varchar(250) NOT NULL,
  `reg` int(11) NOT NULL,
  `p_user` int(11) NOT NULL,
  `p_config` int(11) NOT NULL,
  `p_log` int(11) NOT NULL,
  `p_users` int(11) NOT NULL,
  `p_addr` int(11) NOT NULL,
  `p_ip` int(11) NOT NULL,
  `p_mod` int(11) NOT NULL,
  `mail1` varchar(250) NOT NULL,
  `mail2` varchar(250) NOT NULL,
  `tel1` varchar(19) NOT NULL,
  `tel2` varchar(19) NOT NULL,
  `tel3` varchar(19) NOT NULL,
  `structura` varchar(250) NOT NULL,
  `privat` varchar(11) NOT NULL,
  `del` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Дамп данных таблицы `users`
-- 

INSERT INTO `users` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 0, 'Адміністратор', 1442993102, '10.98.97.31', 1, 1, '0', 1, 'ua', 1294922873, 1, 1, 1, 1, 0, 1, 1, '0', '0', '34-27-003', '(047-31) 3-00-93', '(097) 491-73-34', '3,4,5,6,7,8,9,15,11,12,13,', '1,2,3,4,5,6', 0);
