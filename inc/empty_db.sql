SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET NAMES 'utf8' COLLATE 'utf8_general_ci';
SET CHARACTER SET 'utf8';

DROP TABLE IF EXISTS `banned`;
CREATE TABLE `banned` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `where` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ;

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
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
  `ver` int(9) NOT NULL,
  `max_page_limit` int(6) NOT NULL,
  `index_module` varchar(500) NOT NULL,
  PRIMARY KEY  (`id`)
) ;

INSERT INTO `config` VALUES (1, 'Назва УДКСУ', '', 15, 0, 0, 3600, 20, 1, 2015, '2314', 'zip|eml|rar|7z|txt|doc|xls|docx|xlcx|pdf|jpg', 10, 14, 250, "[index]/[str]-[nom]");


DROP TABLE IF EXISTS `cron`;
CREATE TABLE `cron` (
  `name` varchar(250) NOT NULL,
  `time` int(11) NOT NULL,
  `last` int(11) NOT NULL,
  PRIMARY KEY  (`name`)
) ;

INSERT INTO `cron` VALUES ('backup', 0, 0);
INSERT INTO `cron` VALUES ('backup_on_email', 0, 0);

DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(15) NOT NULL,
  `time` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `do` varchar(2500) NOT NULL,
  PRIMARY KEY  (`id`)
) ;

DROP TABLE IF EXISTS `nomenclatura`;
CREATE TABLE `nomenclatura` (
  `id` int(11) NOT NULL auto_increment,
  `structura` int(10) NOT NULL,
  `index` varchar(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `user` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `do` varchar(250) NOT NULL,
  `work` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ;

DROP TABLE IF EXISTS `structura`;
CREATE TABLE `structura` (
  `id` int(11) NOT NULL auto_increment,
  `index` varchar(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `user` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `do` varchar(250) NOT NULL,
  `work` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ;

DROP TABLE IF EXISTS messages;
CREATE TABLE `messages` (
  `id` int(5) NOT NULL auto_increment,
  `ip` varchar(15) NOT NULL,
  `time` int(11) NOT NULL,
  `name` char(255) NOT NULL,
  `text` text,
  PRIMARY KEY  (`id`)
) ;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
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
  `privat` varchar(250) NOT NULL,
  `del` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=2 ;

INSERT INTO `users` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 0, 'Адміністратор', 1442993102, '10.98.97.31', 1, 1, '0', 1, 'ua', 1294922873, 1, 1, 1, 1, 0, 1, 1, '0', '0', '34-27-003', '(047-31) 3-00-93', '(097) 491-73-34', '3,4,5,6,7,8,9,15,11,12,13,', '1,2,3,4,5,6', 0);
