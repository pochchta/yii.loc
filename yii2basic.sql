-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 23 2020 г., 03:59
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
('ChangingDepartment', 2, 'Изменение списка цехов', NULL, NULL, 1602133666, 1602133666),
('ChangingDevice', 2, 'Изменение данных приборов', NULL, NULL, 1601606502, 1601606502),
('ChangingScale', 2, 'Изменение списка пределов измерений', NULL, NULL, 1602133549, 1602133549),
('ChangingUsers', 2, 'Изменение данных пользователей', NULL, NULL, 1599102504, 1599114781),
('ChangingVerification', 2, 'Изменение данных поверок', NULL, NULL, 1601606646, 1601606646),
('manager', 1, 'Роль пользователя', NULL, NULL, 1599791590, 1602133710);

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
('manager', 'ChangingDepartment'),
('manager', 'ChangingDevice'),
('manager', 'ChangingScale'),
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
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `department`
--

INSERT INTO `department` (`id`, `name`, `phone`, `description`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted`) VALUES
(1, 'РТР', '02-02', 'Описание телеканала', 1602133907, 1602136653, 18, 17, 0),
(2, 'ОРТ', '01-01', 'Описание', 1602133907, 1602133907, 17, 17, 0);

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
  `id_department` int(11) unsigned DEFAULT NULL,
  `id_scale` int(11) unsigned DEFAULT NULL,
  `accuracy` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `updated_by` int(11) unsigned DEFAULT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `device`
--

INSERT INTO `device` (`id`, `name`, `number`, `type`, `description`, `id_department`, `id_scale`, `accuracy`, `position`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted`) VALUES
(1, 'ЦЦЦ32', '910', 'FTFM437081', 'Прибор описание39195542575592', 2, 2, '0.55', '59', 1601012825, 1603247186, 18, 17, 0),
(2, 'ЦЦЦ48', '124', 'type12392', 'klasdfkljboj kljkljklwer7147', 1, 2, '0.39', '25', 1601012825, 1603247186, 17, 17, 0),
(3, 'ЦЦЦ29', '265', '55', '92147', 2, 2, '0.70', '36', 1601012944, 1603247186, 17, 17, 0),
(4, 'ЦЦЦ80', '89', '54', '79482', 1, 2, '0.66', '19', 1602486911, 1603247187, 17, 17, 0),
(5, 'ЦЦЦ91', '208', '49', '30144', 2, 2, '0.63', '84', 1602486925, 1603247187, 17, 17, 0),
(6, 'ЦЦЦ41', '254', '83', '62875', 2, 1, '0.46', '93', 1602486946, 1603247187, 17, 17, 0),
(7, 'ЦЦЦ70', '785', '26', '6431', 1, 1, '0.85', '85', 1602486968, 1603247187, 17, 17, 0),
(8, 'ЦЦЦ67', '151', '30', '23348', 1, 1, '0.57', '46', 1602486977, 1603247187, 17, 17, 0),
(9, 'ЦЦЦ54', '419', '7', '53833', 2, 2, '0.97', '68', 1602486987, 1603247187, 17, 17, 0),
(10, 'ЦЦЦ87', '658', '64', '71277', 2, 1, '0.14', '79', 1602486996, 1603247187, 17, 17, 0),
(11, 'ЦЦЦ37', '182', '35', '87047', 2, 1, '0.5', '63', 1602487005, 1603247187, 17, 17, 0),
(12, 'ЦЦЦ17', '252', '11', '91321', 2, 2, '0.72', '51', 1602487014, 1603247187, 17, 17, 0),
(13, 'ЦЦЦ16', '774', '45', '50249', 1, 2, '0.96', '68', 1602487023, 1603247187, 17, 17, 0),
(14, 'ЦЦЦ32', '467', '1', '4963', 2, 2, '0.76', '42', 1602487032, 1603247187, 17, 17, 0),
(15, 'ЦЦЦ39', '531', '66', '94653', 1, 1, '0.71', '98', 1602487041, 1603247187, 17, 17, 0),
(16, 'ЦЦЦ58', '22', '7', '10437', 2, 1, '0.4', '55', 1602487049, 1603247187, 17, 17, 0),
(17, 'ЦЦЦ97', '282', '89', '87992', 2, 2, '0.42', '66', 1602487058, 1603247187, 17, 17, 0),
(18, 'ЦЦЦ13', '709', '28', '73792', 2, 1, '0.67', '22', 1602487068, 1603247187, 17, 17, 0),
(19, 'ЦЦЦ32', '143', '46', '82083', 2, 1, '0.5', '3', 1602487076, 1603247188, 17, 17, 0),
(20, 'ЦЦЦ48', '740', '26', '99903', 2, 1, '0.36', '7', 1602487085, 1603247188, 17, 17, 0),
(21, 'ЦЦЦ72', '90', '96', '17704', 1, 1, '0.52', '2', 1602487093, 1603247188, 17, 17, 0),
(22, 'ЦЦЦ11', '416', '6', '33568', 2, 1, '0.77', '13', 1603070237, 1603247188, 17, 17, 0),
(23, 'ЦЦЦ91', '27', '88', '80052', 2, 1, '0.28', '81', 1603070237, 1603247188, 17, 17, 0),
(24, 'ЦЦЦ52', '320', '73', '66690', 1, 2, '0.36', '78', 1603070237, 1603247188, 17, 17, 0),
(25, 'ЦЦЦ92', '219', '49', '97877', 2, 2, '0.19', '11', 1603070237, 1603247188, 17, 17, 0),
(26, 'ЦЦЦ8', '506', '64', '64408', 2, 2, '0.96', '18', 1603070237, 1603247188, 17, 17, 0),
(27, 'ЦЦЦ33', '775', '60', '64017', 2, 1, '0.66', '64', 1603070237, 1603247188, 17, 17, 0),
(28, 'ЦЦЦ16', '165', '60', '94993', 2, 2, '0.9', '92', 1603070237, 1603247188, 17, 17, 0),
(29, 'ЦЦЦ3', '177', '33', '30828', 1, 2, '0.86', '13', 1603070237, 1603247188, 17, 17, 0),
(30, 'ЦЦЦ5', '89', '71', '26258', 1, 1, '0.46', '92', 1603070237, 1603247188, 17, 17, 0),
(31, 'ЦЦЦ16', '18', '27', '76897', 1, 1, '0.68', '16', 1603070237, 1603247189, 17, 17, 0),
(32, 'ЦЦЦ95', '447', '97', '52290', 2, 2, '0.19', '14', 1603070238, 1603247189, 17, 17, 0),
(33, 'ЦЦЦ47', '714', '19', '31119', 2, 2, '0.39', '33', 1603070238, 1603247189, 17, 17, 0),
(34, 'ЦЦЦ86', '640', '77', '99969', 2, 2, '0.26', '7', 1603070238, 1603247189, 17, 17, 0),
(35, 'ЦЦЦ65', '875', '41', '99363', 2, 1, '0.97', '93', 1603070238, 1603247189, 17, 17, 0),
(36, 'ЦЦЦ22', '287', '2', '35008', 2, 2, '0.15', '4', 1603070238, 1603247189, 17, 17, 0),
(37, 'ЦЦЦ51', '350', '89', '63996', 1, 1, '0.94', '18', 1603070238, 1603247189, 17, 17, 0),
(38, 'ЦЦЦ42', '195', '39', '7003', 1, 2, '0.96', '80', 1603070238, 1603247189, 17, 17, 0),
(39, 'ЦЦЦ80', '235', '92', '35602', 1, 1, '0.98', '5', 1603070238, 1603247189, 17, 17, 0),
(40, 'ЦЦЦ37', '470', '40', '37315', 1, 2, '0.60', '60', 1603070238, 1603247189, 17, 17, 0),
(41, 'ЦЦЦ51', '63', '1', '33159', 1, 1, '0.73', '41', 1603070238, 1603247189, 17, 17, 0),
(42, 'ЦЦЦ63', '81', '57', '81051', 2, 1, '0.44', '53', 1603070238, 1603247189, 17, 17, 0),
(43, 'ЦЦЦ18', '955', '54', '83672', 2, 1, '0.41', '3', 1603070238, 1603247189, 17, 17, 0),
(44, 'ЦЦЦ45', '867', '18', '34053', 1, 1, '0.35', '86', 1603070238, 1603247189, 17, 17, 0),
(45, 'ЦЦЦ12', '621', '80', '49857', 1, 2, '0.95', '57', 1603070238, 1603247189, 17, 17, 0),
(46, 'ЦЦЦ10', '310', '4', '25119', 1, 1, '0.61', '34', 1603070238, 1603247190, 17, 17, 0),
(47, 'ЦЦЦ79', '804', '0', '59359', 2, 2, '0.20', '6', 1603070238, 1603247190, 17, 17, 0),
(48, 'ЦЦЦ4', '566', '97', '14304', 2, 2, '0.1', '21', 1603070238, 1603247190, 17, 17, 0),
(49, 'ЦЦЦ29', '181', '51', '47341', 1, 1, '0.58', '12', 1603070238, 1603247190, 17, 17, 0),
(50, 'ЦЦЦ46', '943', '39', '39677', 1, 1, '0.17', '57', 1603070238, 1603247190, 17, 17, 0),
(51, 'ЦЦЦ68', '112', '9', '36740', 2, 1, '0.21', '8', 1603070238, 1603247190, 17, 17, 0),
(52, 'ЦЦЦ6', '764', '37', '86509', 2, 1, '0.25', '99', 1603070238, 1603247190, 17, 17, 0),
(53, 'ЦЦЦ3', '10', '76', '2123', 1, 2, '0.7', '85', 1603070238, 1603247190, 17, 17, 0),
(54, 'ЦЦЦ79', '269', '68', '95182', 2, 1, '0.78', '60', 1603070238, 1603247190, 17, 17, 0),
(55, 'ЦЦЦ100', '151', '97', '50954', 2, 1, '0.11', '59', 1603070238, 1603247190, 17, 17, 0),
(56, 'ЦЦЦ51', '257', '59', '30986', 2, 1, '0.20', '15', 1603070238, 1603247190, 17, 17, 0),
(57, 'ЦЦЦ58', '336', '54', '14684', 1, 2, '0.12', '45', 1603070238, 1603247190, 17, 17, 0),
(58, 'ЦЦЦ28', '798', '39', '64622', 1, 2, '0.19', '76', 1603070238, 1603247191, 17, 17, 0),
(59, 'ЦЦЦ20', '522', '73', '83969', 1, 2, '0.18', '95', 1603070238, 1603247191, 17, 17, 0),
(60, 'ЦЦЦ40', '114', '5', '44391', 2, 2, '0.26', '62', 1603070238, 1603247191, 17, 17, 0),
(61, 'ЦЦЦ91', '408', '51', '71467', 1, 2, '0.90', '5', 1603070238, 1603247191, 17, 17, 0),
(62, 'ЦЦЦ25', '966', '70', '17658', 2, 1, '0.18', '62', 1603070238, 1603247191, 17, 17, 0),
(63, 'ЦЦЦ92', '305', '35', '49856', 1, 1, '0.60', '8', 1603070238, 1603247191, 17, 17, 0),
(64, 'ЦЦЦ14', '31', '49', '95366', 1, 2, '0.78', '84', 1603070238, 1603247191, 17, 17, 0),
(65, 'ЦЦЦ69', '64', '49', '62712', 2, 2, '0.92', '79', 1603070238, 1603247191, 17, 17, 0),
(66, 'ЦЦЦ70', '528', '3', '35061', 1, 2, '0.100', '13', 1603070238, 1603247191, 17, 17, 0),
(67, 'ЦЦЦ33', '111', '27', '6735', 2, 1, '0.75', '1', 1603070238, 1603247191, 17, 17, 0),
(68, 'ЦЦЦ4', '878', '99', '89965', 2, 1, '0.39', '31', 1603070238, 1603247191, 17, 17, 0),
(69, 'ЦЦЦ77', '406', '64', '29305', 2, 2, '0.69', '92', 1603070238, 1603247191, 17, 17, 0),
(70, 'ЦЦЦ87', '894', '16', '99289', 2, 1, '0.91', '94', 1603070238, 1603247191, 17, 17, 0),
(71, 'ЦЦЦ48', '293', '95', '35271', 1, 2, '0.27', '9', 1603070238, 1603247191, 17, 17, 0),
(72, 'ЦЦЦ18', '815', '68', '86058', 1, 2, '0.54', '60', 1603070238, 1603247192, 17, 17, 0),
(73, 'ЦЦЦ31', '528', '75', '69064', 2, 1, '0.88', '32', 1603070238, 1603247192, 17, 17, 0),
(74, 'ЦЦЦ50', '321', '35', '54924', 1, 2, '0.8', '87', 1603070238, 1603247192, 17, 17, 0),
(75, 'ЦЦЦ90', '612', '56', '53370', 1, 2, '0.90', '61', 1603070238, 1603247192, 17, 17, 0),
(76, 'ЦЦЦ35', '167', '100', '47784', 1, 1, '0.84', '50', 1603070238, 1603247192, 17, 17, 0),
(77, 'ЦЦЦ37', '123', '16', '18119', 1, 2, '0.91', '51', 1603070238, 1603247192, 17, 17, 0),
(78, 'ЦЦЦ16', '341', '49', '77293', 1, 2, '0.38', '19', 1603070238, 1603247192, 17, 17, 0),
(79, 'ЦЦЦ44', '370', '13', '3732', 1, 2, '0.15', '69', 1603070238, 1603247192, 17, 17, 0),
(80, 'ЦЦЦ83', '234', '93', '34828', 2, 1, '0.4', '1', 1603070238, 1603247192, 17, 17, 0),
(81, 'ЦЦЦ49', '995', '44', '32728', 1, 2, '0.43', '61', 1603070238, 1603247192, 17, 17, 0),
(82, 'ЦЦЦ27', '30', '31', '22623', 2, 2, '0.98', '90', 1603070238, 1603247192, 17, 17, 0),
(83, 'ЦЦЦ81', '43', '63', '67715', 2, 2, '0.81', '11', 1603070238, 1603247192, 17, 17, 0),
(84, 'ЦЦЦ12', '189', '33', '4970', 1, 1, '0.94', '100', 1603070238, 1603247193, 17, 17, 0),
(85, 'ЦЦЦ28', '634', '82', '11813', 1, 1, '0.68', '91', 1603070238, 1603247193, 17, 17, 0),
(86, 'ЦЦЦ16', '379', '49', '28849', 2, 1, '0.70', '71', 1603070238, 1603247193, 17, 17, 0),
(87, 'ЦЦЦ9', '573', '38', '50238', 1, 1, '0.84', '50', 1603070238, 1603247193, 17, 17, 0),
(88, 'ЦЦЦ41', '289', '23', '51417', 1, 2, '0.40', '45', 1603070238, 1603247193, 17, 17, 0),
(89, 'ЦЦЦ88', '445', '40', '493', 1, 2, '0.62', '45', 1603070238, 1603247193, 17, 17, 0),
(90, 'ЦЦЦ52', '138', '18', '79595', 1, 2, '0.48', '46', 1603070238, 1603247193, 17, 17, 0),
(91, 'ЦЦЦ77', '997', '89', '97664', 1, 2, '0.14', '43', 1603070238, 1603247193, 17, 17, 0),
(92, 'ЦЦЦ10', '923', '52', '85434', 1, 2, '0.95', '65', 1603070238, 1603247193, 17, 17, 0),
(93, 'ЦЦЦ46', '179', '19', '95346', 2, 2, '0.62', '75', 1603070238, 1603247194, 17, 17, 0),
(94, 'ЦЦЦ84', '82', '92', '59237', 2, 2, '0.59', '71', 1603070239, 1603247194, 17, 17, 0),
(95, 'ЦЦЦ35', '169', '97', '35938', 2, 1, '0.20', '40', 1603070239, 1603247194, 17, 17, 0),
(96, 'ЦЦЦ99', '597', '93', '75181', 2, 2, '0.59', '21', 1603070239, 1603247194, 17, 17, 0),
(97, 'ЦЦЦ24', '778', '80', '43187', 1, 1, '0.12', '19', 1603070239, 1603247194, 17, 17, 0),
(98, 'ЦЦЦ40', '239', '75', '60461', 2, 1, '0.65', '43', 1603070239, 1603247194, 17, 17, 0),
(99, 'ЦЦЦ100', '373', '33', '83057', 2, 2, '0.34', '52', 1603070239, 1603247194, 17, 17, 0),
(100, 'ЦЦЦ84', '579', '31', '22850', 1, 2, '0.16', '97', 1603070239, 1603247194, 17, 17, 0),
(101, 'ЦЦЦ44', '455', '13', '47682', 1, 2, '0.94', '58', 1603070239, 1603247194, 17, 17, 0),
(102, 'ЦЦЦ44', '768', '64', '26329', 1, 1, '0.98', '43', 1603070239, 1603247194, 17, 17, 0),
(103, 'ЦЦЦ67', '461', '3', '95759', 2, 2, '0.54', '65', 1603070239, 1603247194, 17, 17, 0),
(104, 'ЦЦЦ39', '380', '35', '87601', 1, 1, '0.47', '74', 1603070239, 1603247194, 17, 17, 0),
(105, 'ЦЦЦ100', '487', '63', '55040', 2, 2, '0.58', '75', 1603070239, 1603247194, 17, 17, 0),
(106, 'ЦЦЦ95', '773', '49', '38688', 1, 2, '0.24', '23', 1603070239, 1603247195, 17, 17, 0),
(107, 'ЦЦЦ2', '452', '21', '61621', 2, 1, '0.62', '13', 1603070239, 1603247195, 17, 17, 0),
(108, 'ЦЦЦ12', '187', '92', '32849', 1, 1, '0.34', '81', 1603070239, 1603247195, 17, 17, 0),
(109, 'ЦЦЦ40', '909', '33', '74355', 2, 2, '0.43', '33', 1603070239, 1603247195, 17, 17, 0),
(110, 'ЦЦЦ39', '961', '47', '48572', 1, 2, '0.10', '27', 1603070239, 1603247195, 17, 17, 0),
(111, 'ЦЦЦ99', '379', '84', '57192', 2, 2, '0.32', '37', 1603070239, 1603247195, 17, 17, 0),
(112, 'ЦЦЦ42', '137', '58', '44049', 2, 1, '0.22', '31', 1603070239, 1603247195, 17, 17, 0),
(113, 'ЦЦЦ33', '624', '45', '74022', 2, 2, '0.78', '92', 1603070239, 1603247195, 17, 17, 0),
(114, 'ЦЦЦ61', '433', '36', '58782', 1, 1, '0.66', '77', 1603070239, 1603247195, 17, 17, 0),
(115, 'ЦЦЦ59', '135', '100', '49728', 2, 1, '0.3', '49', 1603070239, 1603247195, 17, 17, 0),
(116, 'ЦЦЦ13', '566', '96', '73211', 2, 2, '0.63', '86', 1603070239, 1603247195, 17, 17, 0),
(117, 'ЦЦЦ6', '446', '59', '95702', 2, 2, '0.55', '20', 1603070239, 1603247195, 17, 17, 0),
(118, 'ЦЦЦ52', '476', '69', '72848', 2, 1, '0.16', '6', 1603070239, 1603247195, 17, 17, 0),
(119, 'ЦЦЦ39', '689', '45', '23208', 1, 2, '0.89', '33', 1603070239, 1603247195, 17, 17, 0),
(120, 'ЦЦЦ61', '804', '98', '43950', 1, 2, '0.21', '70', 1603070239, 1603247196, 17, 17, 0),
(121, 'ЦЦЦ51', '440', '51', '55508', 1, 2, '0.97', '62', 1603070239, 1603247196, 17, 17, 0),
(122, 'ЦЦЦ1', '450', '0', '52545', 1, 2, '0.91', '46', 1603070239, 1603247196, 17, 17, 0);

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
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `scale`
--

INSERT INTO `scale` (`id`, `value`, `description`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted`) VALUES
(1, '0-20 mA', 'text', 1602126556, 1602136777, 18, 17, 0),
(2, '4-20 mA', 'text', 1602126556, 1602126556, 17, 17, 0);

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
  `device_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `description` text,
  `last_date` int(11) DEFAULT NULL,
  `next_date` int(11) DEFAULT NULL,
  `period` tinyint(1) unsigned DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `updated_by` int(11) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `verification`
--

INSERT INTO `verification` (`id`, `device_id`, `name`, `type`, `description`, `last_date`, `next_date`, `period`, `created_at`, `updated_at`, `created_by`, `updated_by`, `status`, `deleted`) VALUES
(5, 3, 'new verif5', '123', '1', 1514851200, 1546387200, 1, 1601263460, 1603337201, 17, 17, 0, 0),
(7, 1, 'verif', '', '', 1601337600, 31538020, 1, 1601353813, 1603342842, 17, 17, 1, 0),
(8, 1, 'ЦЦЦ-1', 'дфывдлао длф', 'дфд длд дфдл', 1538179200, 31538018, 1, 1601353929, 1603342797, 17, 17, 0, 0),
(10, 2, 'новый', '', '', 1514851200, 31538018, 1, 1601354192, 1603342705, 17, 17, 0, 0),
(11, 2, 'еще', '', '', 1601337600, 63074020, 2, 1601356877, 1603342857, 17, 17, 1, 0),
(12, 3, 'new verif', '', '', 631152000, 757382400, 4, 1601357002, 1603338937, 17, 17, 0, 0),
(13, 3, 'v2', '123', '2111212213', 1577836800, 1609459200, 1, 1601535770, 1603338814, 17, 17, 1, 0);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

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
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=123;
--
-- AUTO_INCREMENT для таблицы `scale`
--
ALTER TABLE `scale`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
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
-- Ограничения внешнего ключа таблицы `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `department_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `department_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`);

--
-- Ограничения внешнего ключа таблицы `device`
--
ALTER TABLE `device`
  ADD CONSTRAINT `device_ibfk_1` FOREIGN KEY (`id_department`) REFERENCES `department` (`id`),
  ADD CONSTRAINT `device_ibfk_2` FOREIGN KEY (`id_scale`) REFERENCES `scale` (`id`),
  ADD CONSTRAINT `device_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `device_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`);

--
-- Ограничения внешнего ключа таблицы `scale`
--
ALTER TABLE `scale`
  ADD CONSTRAINT `scale_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `scale_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`);

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
