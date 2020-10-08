-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 08 2020 г., 04:49
-- Версия сервера: 5.6.37
-- Версия PHP: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `yii2basic`
--

-- --------------------------------------------------------

--
-- Структура таблицы `auth_assignment`
--

CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '17', 1598316453),
('manager', '17', 1601608350),
('manager', '18', 1599791639),
('manager', '22', 1600308542);

-- --------------------------------------------------------

--
-- Структура таблицы `auth_item`
--

CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('admin', 1, 'Роль администратора', NULL, NULL, 1594262191, 1600657155),
('ChangingAuthItem', 2, 'Изменение ролей и разрешений', NULL, NULL, 1599096920, 1599096920),
('ChangingDevice', 2, 'Изменение данных приборов', NULL, NULL, 1601606502, 1601606502),
('ChangingUsers', 2, 'Изменение данных пользователей', NULL, NULL, 1599102504, 1599114781),
('ChangingVerification', 2, 'Изменение данных поверок', NULL, NULL, 1601606646, 1601606646),
('manager', 1, 'Роль пользователя', NULL, NULL, 1599791590, 1601872387);

-- --------------------------------------------------------

--
-- Структура таблицы `auth_item_child`
--

CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('admin', 'ChangingAuthItem'),
('manager', 'ChangingDevice'),
('admin', 'ChangingUsers'),
('manager', 'ChangingVerification');

-- --------------------------------------------------------

--
-- Структура таблицы `auth_rule`
--

CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `department`
--

CREATE TABLE IF NOT EXISTS `department` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `description` text,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `department`
--

INSERT INTO `department` (`id`, `name`, `phone`, `description`, `deleted`) VALUES
(1, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `device`
--

CREATE TABLE IF NOT EXISTS `device` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `description` text,
  `last_date` int(11) DEFAULT NULL,
  `next_date` int(11) DEFAULT NULL,
  `period` tinyint(1) unsigned DEFAULT NULL,
  `id_department` int(10) unsigned NOT NULL,
  `id_scale` int(10) unsigned NOT NULL,
  `accuracy` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `updated_by` int(11) unsigned DEFAULT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `device`
--

INSERT INTO `device` (`id`, `name`, `number`, `type`, `description`, `last_date`, `next_date`, `period`, `id_department`, `id_scale`, `accuracy`, `position`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted`) VALUES
(1, 'ЦЦЦ-1', '11111', 'йцукылодло', 'ывапдллдоолдолдлдоыв', 1601337600, 1632873600, 1, 1, 1, NULL, NULL, 1601012825, 1601606118, 18, 17, 0),
(2, 'ЦЦЦ-1', '2222222', 'type123', 'klasdfkljboj kljkljklwer', 1601337600, 1664409600, 2, 1, 1, NULL, NULL, 1601012825, 1601525193, 17, 17, 0),
(3, 'ЦЦЦ-1', '3333333', '', '', 1577836800, 1609459200, 1, 1, 1, NULL, NULL, 1601012944, 1601610951, 17, 17, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1593670561),
('m140506_102106_rbac_init', 1594176874),
('m170907_052038_rbac_add_index_on_auth_assignment_user_id', 1594176874),
('m180523_151638_rbac_updates_indexes_without_prefix', 1594176874),
('m200409_110543_rbac_update_mssql_trigger', 1594176874);

-- --------------------------------------------------------

--
-- Структура таблицы `scale`
--

CREATE TABLE IF NOT EXISTS `scale` (
  `id` int(10) unsigned NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `description` text,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `scale`
--

INSERT INTO `scale` (`id`, `value`, `description`, `deleted`) VALUES
(1, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth_key` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `auth_key`) VALUES
(17, 'superUser', '$2y$13$KZq1nUjnIUYu12scH64bze1pJfCiD/YsgiI0P7sOKKXbmDCxE6mbm', 'NZuUMrEKYYd2nmc8CkxH1s8jX4arwWux'),
(18, 'user', '$2y$13$ak8tzMsxX/YOmOQOgrl8qu41XJtZeWFVCLi8kJaRT9B7N.DRpuyuO', 'HtrQO64jD4zr4rafrXIssBTqNEtZtOjH'),
(19, '123', '$2y$13$hVusrLF3xcXuqrtZeETvl.ozT4jyN/CiSvRwi6M1z3iJxV8SaI4XK', 'rS_NKeXl38gYzyUggB02v4AVf9TKxD9T'),
(22, 'newUser', '$2y$13$1zWPjekD2OTxdExwyXTxF.KylJaoA7JMYTmPjN.TdcuvZDdXyD/ei', NULL),
(23, '11', '$2y$13$HDvz7qYI/6oHDQI9lacyKu1UnA7sI7dSIkGH0iQAgaOIxCPDMedPy', 'CG-X5Nsi5r2sMljFao96cRgVAX_PxRcu'),
(24, '1-1', '$2y$13$aEorPYsXzuAGIsx.qlv5wu/quB5wvSvML26EQ02lBIyYULJSHPUWy', NULL),
(25, 'Иван Иванович', '$2y$13$XoWK5r5C2c425IwNJRsprufE0joz4WW7HHO8DN9men43MVDzYJxFu', 'K8Y_Ga-rQr_vwCkpWCT_nWVv-mOVozEu'),
(26, 'Николай Второй', '$2y$13$Ts6pcw2IJOqCBG4dwXV4XecsilKk1SaAbj28fpOYxuwd2U57yXcEq', NULL),
(27, '2', '$2y$13$9Dk35nCHjOJoYknDEf.4qegzXGhOYxlSZt/3USTyAR6wabe6k/QHG', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `verification`
--

CREATE TABLE IF NOT EXISTS `verification` (
  `id` int(11) unsigned NOT NULL,
  `device_id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `description` text,
  `last_date` int(11) DEFAULT NULL,
  `period` tinyint(1) unsigned DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `updated_by` int(11) unsigned DEFAULT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `verification`
--

INSERT INTO `verification` (`id`, `device_id`, `name`, `type`, `description`, `last_date`, `period`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted`) VALUES
(5, 3, 'new verif5', '123', '1', 1514851200, 1, 1601263460, 1601610791, 17, 17, 0),
(7, 1, 'verif', '', '', 1601337600, 1, 1601353813, 1601605663, 17, 17, 0),
(8, 1, 'ЦЦЦ-1', 'дфывдлао длф', 'дфд длд дфдл', 1538179200, 1, 1601353929, 1601525058, 17, 17, 0),
(10, 2, 'новый', '', '', 1514851200, 1, 1601354192, 1601440806, 17, 17, 0),
(11, 2, 'еще', '', '', 1601337600, 2, 1601356877, 1601440739, 17, 17, 0),
(12, 3, 'new verif', '', '', 631152000, 4, 1601357002, 1601357002, 17, 17, 0),
(13, 3, 'v2', '123', '2111212213', 1577836800, 1, 1601535770, 1601535770, 17, 17, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`),
  ADD KEY `idx-auth_assignment-user_id` (`user_id`);

--
-- Индексы таблицы `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);

--
-- Индексы таблицы `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Индексы таблицы `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Индексы таблицы `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_department` (`id_department`),
  ADD KEY `id_scale` (`id_scale`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Индексы таблицы `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `scale`
--
ALTER TABLE `scale`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `verification`
--
ALTER TABLE `verification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `device`
--
ALTER TABLE `device`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `scale`
--
ALTER TABLE `scale`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT для таблицы `verification`
--
ALTER TABLE `verification`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `device`
--
ALTER TABLE `device`
  ADD CONSTRAINT `device_ibfk_1` FOREIGN KEY (`id_department`) REFERENCES `department` (`id`),
  ADD CONSTRAINT `device_ibfk_2` FOREIGN KEY (`id_scale`) REFERENCES `scale` (`id`),
  ADD CONSTRAINT `device_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `device_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`);

--
-- Ограничения внешнего ключа таблицы `verification`
--
ALTER TABLE `verification`
  ADD CONSTRAINT `verification_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `device` (`id`),
  ADD CONSTRAINT `verification_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `verification_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
