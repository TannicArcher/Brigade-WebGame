-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 29 2023 г., 20:36
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `brigada`
--

-- --------------------------------------------------------

--
-- Структура таблицы `background`
--

CREATE TABLE `background` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `howGet` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `background`
--

INSERT INTO `background` (`id`, `name`, `howGet`) VALUES
(1, 'Улица', 'Стандартное расположение'),
(2, 'Старый спортзал', 'Шанс получить за победу на залётным типом - Володя «Гантеля»'),
(3, 'Подъезд', 'Шанс получить за победу на залётным типом - Лёня «Пироман»');

-- --------------------------------------------------------

--
-- Структура таблицы `backgrounds`
--

CREATE TABLE `backgrounds` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `background` varchar(255) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `backgrounds`
--

INSERT INTO `backgrounds` (`id`, `id_user`, `background`, `used`) VALUES
(1, 360, '1', 1),
(2, 1, '1', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `banList`
--

CREATE TABLE `banList` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `id_admin` bigint UNSIGNED NOT NULL,
  `startBan` int UNSIGNED NOT NULL,
  `endBan` int UNSIGNED DEFAULT NULL,
  `forever` tinyint NOT NULL DEFAULT '0',
  `apply` tinyint NOT NULL DEFAULT '0',
  `reason` text NOT NULL,
  `typeBan` tinyint(1) NOT NULL DEFAULT '0',
  `id_unban` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `boss`
--

CREATE TABLE `boss` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `about` text,
  `background` varchar(255) DEFAULT NULL,
  `health` bigint UNSIGNED NOT NULL DEFAULT '1000',
  `giveBolts` bigint UNSIGNED NOT NULL DEFAULT '0',
  `giveRepute` bigint UNSIGNED NOT NULL DEFAULT '0',
  `giveExp` bigint UNSIGNED NOT NULL DEFAULT '0',
  `needRepute` bigint UNSIGNED NOT NULL DEFAULT '1000',
  `needKey` int DEFAULT NULL,
  `giveKey` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `boss`
--

INSERT INTO `boss` (`id`, `name`, `about`, `background`, `health`, `giveBolts`, `giveRepute`, `giveExp`, `needRepute`, `needKey`, `giveKey`) VALUES
(1, 'Володя «Гантеля» ', 'Сельский лох, что сходил пару раз в зал и уже считает себя местным авторитетом. Покажешь, кто тут всем заправляет?', '2', 2000, 500, 50, 50, 500, NULL, 11),
(2, 'Лёня «Пироман» ', 'Говорят, что детям спички не игрушки. Так вот - не только детям. Этот псих может сжечь всё вокруг.', '3', 5000, 1500, 150, 150, 1000, 11, 12);

-- --------------------------------------------------------

--
-- Структура таблицы `boss_chat`
--

CREATE TABLE `boss_chat` (
  `id` bigint UNSIGNED NOT NULL,
  `id_fight` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `boss_drop`
--

CREATE TABLE `boss_drop` (
  `id` bigint UNSIGNED NOT NULL,
  `id_boss` bigint UNSIGNED NOT NULL,
  `typeDrop` set('item','object','back') NOT NULL DEFAULT 'item',
  `id_drop` bigint UNSIGNED NOT NULL,
  `chance` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `boss_drop`
--

INSERT INTO `boss_drop` (`id`, `id_boss`, `typeDrop`, `id_drop`, `chance`) VALUES
(1, 1, 'item', 4, 50),
(2, 1, 'item', 5, 20),
(3, 1, 'item', 6, 60);

-- --------------------------------------------------------

--
-- Структура таблицы `boss_fight`
--

CREATE TABLE `boss_fight` (
  `id` bigint UNSIGNED NOT NULL,
  `id_boss` bigint UNSIGNED NOT NULL,
  `health` bigint NOT NULL DEFAULT '1000',
  `id_lider` bigint UNSIGNED NOT NULL,
  `fightType` set('solo','all','invite','opg') NOT NULL DEFAULT 'solo',
  `id_opg` bigint UNSIGNED DEFAULT NULL COMMENT 'если тип опг, то ID опг',
  `created_at` int UNSIGNED DEFAULT NULL,
  `started_at` int UNSIGNED DEFAULT NULL,
  `ending_at` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `boss_invite`
--

CREATE TABLE `boss_invite` (
  `id` bigint UNSIGNED NOT NULL,
  `id_fight` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `id_to` bigint UNSIGNED NOT NULL,
  `invite` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `boss_logs`
--

CREATE TABLE `boss_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `id_fight` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `damage` bigint UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `boss_members`
--

CREATE TABLE `boss_members` (
  `id` bigint UNSIGNED NOT NULL,
  `id_fight` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `created_at` int NOT NULL,
  `kick` tinyint(1) NOT NULL DEFAULT '0',
  `takeDrop` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `boss_users`
--

CREATE TABLE `boss_users` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `boss_1_timeout` int UNSIGNED DEFAULT NULL,
  `boss_1_success` bigint UNSIGNED NOT NULL DEFAULT '0',
  `boss_2_timeout` int DEFAULT NULL,
  `boss_2_success` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `boss_users`
--

INSERT INTO `boss_users` (`id`, `id_user`, `boss_1_timeout`, `boss_1_success`, `boss_2_timeout`, `boss_2_success`) VALUES
(1, 360, NULL, 0, NULL, 0),
(2, 1, NULL, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `business`
--

CREATE TABLE `business` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `id_dot` bigint UNSIGNED DEFAULT NULL,
  `time` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `businessList`
--

CREATE TABLE `businessList` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `about` text,
  `crime` tinyint(1) NOT NULL DEFAULT '0',
  `profitTime` int UNSIGNED NOT NULL,
  `profitAmount` int UNSIGNED NOT NULL DEFAULT '1',
  `profitRepute` int UNSIGNED NOT NULL,
  `priceType` set('bolts','rubles') NOT NULL DEFAULT 'bolts',
  `priceAmount` int UNSIGNED NOT NULL,
  `level` int UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `businessRaid`
--

CREATE TABLE `businessRaid` (
  `id` bigint UNSIGNED NOT NULL,
  `id_dot` bigint UNSIGNED NOT NULL,
  `id_raider` bigint UNSIGNED NOT NULL,
  `time` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `chat`
--

CREATE TABLE `chat` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `message` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `timeAdd` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `craft`
--

CREATE TABLE `craft` (
  `id` int NOT NULL,
  `obj_type` set('weapon','object') NOT NULL DEFAULT 'weapon',
  `obj_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `craft_item`
--

CREATE TABLE `craft_item` (
  `id` int NOT NULL,
  `id_craft` int NOT NULL,
  `id_object` int NOT NULL,
  `amount` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `craft_turn`
--

CREATE TABLE `craft_turn` (
  `id` bigint NOT NULL,
  `id_craft` int NOT NULL,
  `id_user` int NOT NULL,
  `dateAdd` int NOT NULL,
  `dateEnd` int NOT NULL,
  `execution` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `districts`
--

CREATE TABLE `districts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `districts`
--

INSERT INTO `districts` (`id`, `name`) VALUES
(1, 'Бутово'),
(2, 'Люберцы'),
(3, 'Выхино'),
(4, 'Чертаново'),
(5, 'Арбат'),
(6, 'Хитровка'),
(7, 'Таганка'),
(8, 'Южный порт'),
(9, 'Дубровка'),
(10, 'Кузьминки');

-- --------------------------------------------------------

--
-- Структура таблицы `districts_users`
--

CREATE TABLE `districts_users` (
  `id` bigint UNSIGNED NOT NULL,
  `id_district` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `success` bigint UNSIGNED NOT NULL DEFAULT '0',
  `repute` bigint UNSIGNED NOT NULL DEFAULT '0',
  `repute_today` bigint UNSIGNED NOT NULL DEFAULT '0',
  `1` int UNSIGNED NOT NULL DEFAULT '0',
  `2` int UNSIGNED NOT NULL DEFAULT '0',
  `3` int UNSIGNED NOT NULL DEFAULT '0',
  `4` int UNSIGNED NOT NULL DEFAULT '0',
  `5` int UNSIGNED NOT NULL DEFAULT '0',
  `6` int UNSIGNED NOT NULL DEFAULT '0',
  `7` int UNSIGNED NOT NULL DEFAULT '0',
  `biz_1` bigint DEFAULT NULL,
  `biz_1_time` int DEFAULT NULL,
  `biz_2` bigint DEFAULT NULL,
  `biz_2_time` int DEFAULT NULL,
  `biz_3` bigint DEFAULT NULL,
  `biz_3_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `districts_users`
--

INSERT INTO `districts_users` (`id`, `id_district`, `id_user`, `success`, `repute`, `repute_today`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `biz_1`, `biz_1_time`, `biz_2`, `biz_2_time`, `biz_3`, `biz_3_time`) VALUES
(1, 1, 1, 0, 5, 5, 5, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `dots`
--

CREATE TABLE `dots` (
  `id` bigint NOT NULL,
  `name` varchar(255) NOT NULL,
  `about` text,
  `needRepute` bigint NOT NULL DEFAULT '100',
  `typePrice` set('bolts','rubles') NOT NULL DEFAULT 'bolts',
  `amountPrice` bigint NOT NULL DEFAULT '1',
  `typeCrime` set('crime','legal') NOT NULL DEFAULT 'crime',
  `giveBolts` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `dots`
--

INSERT INTO `dots` (`id`, `name`, `about`, `needRepute`, `typePrice`, `amountPrice`, `typeCrime`, `giveBolts`) VALUES
(1, 'Попрошайка', 'Клянчит деньги у прохожих', 100, 'bolts', 1337, 'crime', 2),
(2, 'Карманник', 'Крадет наличку у зевак, пока они любуются \"пейзажами\" района', 400, 'rubles', 10, 'crime', 3),
(3, 'Клоун', 'Мерзкий страшный клоун, но деньги все равно способен заработать', 800, 'bolts', 3500, 'legal', 5);

-- --------------------------------------------------------

--
-- Структура таблицы `emails`
--

CREATE TABLE `emails` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `emailToken` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `everyDay`
--

CREATE TABLE `everyDay` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `quest_1` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Набрать 50 репутации',
  `quest_2` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Потратить 100 энергии',
  `quest_3` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Собрать прибыль 5 раз',
  `quest_4` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Выполнить задания 20 раз',
  `quest_5` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Посетить тренировку 3 раза',
  `take` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `everyDay`
--

INSERT INTO `everyDay` (`id`, `id_user`, `quest_1`, `quest_2`, `quest_3`, `quest_4`, `quest_5`, `take`) VALUES
(1, 360, 0, 0, 0, 0, 0, 0),
(2, 1, 5, 5, 0, 5, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `forum_category`
--

CREATE TABLE `forum_category` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `parent` int DEFAULT NULL,
  `access` tinyint(1) NOT NULL DEFAULT '0',
  `onlyAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `onlySupport` tinyint(1) NOT NULL DEFAULT '0',
  `createAdmin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `forum_category`
--

INSERT INTO `forum_category` (`id`, `name`, `about`, `parent`, `access`, `onlyAdmin`, `onlySupport`, `createAdmin`) VALUES
(1, 'Логи разработки', 'Процесс создания игры.', NULL, 0, 0, 0, 1),
(2, 'Предложения по игре', 'Есть идея для улучшения игры?', NULL, 0, 0, 0, 0),
(3, 'Правила игры', 'Обязательно к прочтению', NULL, 0, 0, 0, 1),
(4, 'ОПГ', 'Для поиска братков в свое ОПГ.', NULL, 0, 0, 0, 0),
(5, 'Общение', 'Общение на разные темы.', NULL, 0, 0, 0, 0),
(6, 'Техническая поддержка', 'Нужна помощь администрации? Задай свой вопрос здесь.', NULL, 0, 0, 1, 0),
(7, 'Вопросы оплаты', 'Все, что связано с покупкой за реальные средства', 6, 0, 1, 0, 0),
(8, 'Критические баги', 'Баги, что могут дать преимущество другим игрокам. Нашедшему такие полагается вознаграждение.', 6, 0, 1, 0, 0),
(9, 'Жалобы на пользователей', 'Кто-то нарушил правила? Сообщи об этом!', 6, 0, 0, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `forum_messages`
--

CREATE TABLE `forum_messages` (
  `id` bigint NOT NULL,
  `id_topic` int NOT NULL,
  `id_user` int NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dateAdd` int NOT NULL,
  `dateUpd` int DEFAULT NULL,
  `whoUpd` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `forum_topics`
--

CREATE TABLE `forum_topics` (
  `id` int NOT NULL,
  `id_category` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` int NOT NULL,
  `dateAdd` int NOT NULL,
  `lastUpdate` int DEFAULT NULL,
  `dateUpd` int DEFAULT NULL,
  `whoUpd` int DEFAULT NULL,
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `attach` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE `groups` (
  `id` bigint NOT NULL,
  `id_lider` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `tag` varchar(3) DEFAULT NULL,
  `about` text,
  `exp` bigint NOT NULL DEFAULT '0',
  `level` int NOT NULL DEFAULT '1',
  `max_users` int NOT NULL DEFAULT '5',
  `dateCreate` int DEFAULT NULL,
  `radio` tinyint(1) NOT NULL DEFAULT '0',
  `bolts` bigint NOT NULL DEFAULT '0',
  `rubles` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `groups_chat`
--

CREATE TABLE `groups_chat` (
  `id` bigint UNSIGNED NOT NULL,
  `id_group` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `groups_logs`
--

CREATE TABLE `groups_logs` (
  `id` bigint NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_group` int DEFAULT NULL,
  `text` text,
  `time` int DEFAULT NULL,
  `types` set('bank','members','settings','improve','invite','radio') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `groups_users`
--

CREATE TABLE `groups_users` (
  `id` bigint NOT NULL,
  `id_group` int DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  `rank` enum('0','1','2','3','4') NOT NULL DEFAULT '0',
  `exp_today` bigint NOT NULL DEFAULT '0',
  `exp_all` bigint NOT NULL DEFAULT '0',
  `accept` enum('0','1') NOT NULL DEFAULT '0',
  `dateAdd` int DEFAULT NULL,
  `donate_bolts` bigint NOT NULL DEFAULT '0',
  `donate_rubles` bigint NOT NULL DEFAULT '0',
  `invite` int DEFAULT NULL,
  `invite_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `merchant`
--

CREATE TABLE `merchant` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `id_billing` bigint UNSIGNED NOT NULL,
  `time_init` int UNSIGNED DEFAULT NULL,
  `time_pay` int UNSIGNED DEFAULT NULL,
  `amount` decimal(11,2) UNSIGNED DEFAULT NULL,
  `give` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `npc`
--

CREATE TABLE `npc` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `about` text,
  `method` set('quest','dealer','equipment','objects','change','food','bank','craft','gaming','digger') DEFAULT NULL,
  `ratio` float NOT NULL DEFAULT '1',
  `sold_ratio` float NOT NULL DEFAULT '0.7',
  `location` int NOT NULL DEFAULT '1',
  `x` int DEFAULT NULL,
  `y` int DEFAULT NULL,
  `onlySell` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `objects`
--

CREATE TABLE `objects` (
  `id` bigint NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `about` text,
  `getObject` text,
  `types` set('none','hp','energy','medal') NOT NULL DEFAULT 'none',
  `what` int DEFAULT NULL,
  `whatType` enum('0','1') DEFAULT '0',
  `price` set('bolts','rubles') DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `random` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `objects`
--

INSERT INTO `objects` (`id`, `name`, `about`, `getObject`, `types`, `what`, `whatType`, `price`, `amount`, `random`) VALUES
(1, 'Бич-пакет', 'Главное не есть сухим', NULL, 'energy', 5, '0', NULL, 1, 20),
(2, 'Бутер', 'Что может быть лучше старого доброго бутерброда с колбаской и хлебом? Верно! НИЧЕГО!', NULL, 'energy', 10, '0', NULL, 1, 15),
(3, 'Салатик', 'Огурец, да помидор', NULL, 'energy', 15, '0', NULL, NULL, 10),
(4, 'Борщ', 'Ну тут и говорить нечего. Самый обычный борщ, знакомый всем с детства. ', NULL, 'energy', 20, '0', NULL, NULL, 9),
(5, 'Мешок кофе', 'Кружку кофе? Да тут хватит на весь район!', NULL, 'energy', 25, '0', NULL, 1, 7),
(6, 'Чили', 'Целая банка жгучего перца. Ух, горячо!', NULL, 'energy', 35, '0', NULL, 1, 5),
(7, 'Пакетик энергочая', 'Ходят слухи, что дает бодрости более, чем чефир.', NULL, 'energy', 50, '0', NULL, NULL, 3),
(8, 'Сгущёнка', 'Старая добрая сгущёночка', NULL, 'energy', 7, '0', NULL, NULL, 33),
(9, 'Шоколадка', '1 долька, но даёт немного бодрости.', NULL, 'energy', 3, '0', NULL, NULL, 33),
(10, 'Батон', 'Батон, да без колбасы.', NULL, 'energy', 4, '0', NULL, NULL, 33),
(11, 'Ярлык за Володю «Гантелю» ', 'Получаете за победу над залётным Володей «Гантелей».', NULL, 'medal', NULL, '0', NULL, NULL, 0),
(12, 'Ярлык за Лёню «Пиромана» ', 'Получаете за победу над залётным Лёней «Пироманом».', NULL, 'medal', NULL, '0', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `objects_users`
--

CREATE TABLE `objects_users` (
  `id` bigint NOT NULL,
  `id_object` int DEFAULT NULL,
  `id_user` int NOT NULL,
  `count` int NOT NULL DEFAULT '1',
  `dateAdd` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `objects_users`
--

INSERT INTO `objects_users` (`id`, `id_object`, `id_user`, `count`, `dateAdd`) VALUES
(1, 1, 1, 1, 1698600804);

-- --------------------------------------------------------

--
-- Структура таблицы `oldLogin`
--

CREATE TABLE `oldLogin` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `oldLogin` varchar(255) NOT NULL,
  `newLogin` varchar(255) NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `phone_notify`
--

CREATE TABLE `phone_notify` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `text` text,
  `created_at` int NOT NULL,
  `read_at` tinyint(1) NOT NULL DEFAULT '0',
  `linkAccept` text,
  `linkDecline` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `phone_sms`
--

CREATE TABLE `phone_sms` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `id_to` bigint UNSIGNED NOT NULL,
  `created` int UNSIGNED NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` tinyint NOT NULL DEFAULT '0',
  `answer` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `phone_sms_attach`
--

CREATE TABLE `phone_sms_attach` (
  `id` bigint UNSIGNED NOT NULL,
  `id_sms` bigint UNSIGNED NOT NULL,
  `attachType` set('rubles','bolts','repute','equip','object','userEquip') NOT NULL,
  `attachAmount` bigint UNSIGNED DEFAULT '1',
  `attachID` bigint UNSIGNED DEFAULT NULL,
  `take` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `refferals_in`
--

CREATE TABLE `refferals_in` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `id_ref` bigint UNSIGNED NOT NULL,
  `dateAdd` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `refferals_site`
--

CREATE TABLE `refferals_site` (
  `id` bigint UNSIGNED NOT NULL,
  `ref` text NOT NULL,
  `id_user` bigint NOT NULL,
  `created_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `refferals_site`
--

INSERT INTO `refferals_site` (`id`, `ref`, `id_user`, `created_at`) VALUES
(1, 'http://race.mobi/chat/', 1, 1698600792);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `login` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `save` tinyint NOT NULL DEFAULT '0',
  `access` enum('0','1','2','3','4') NOT NULL DEFAULT '0',
  `access_name` text,
  `level` int NOT NULL DEFAULT '1',
  `exp` bigint UNSIGNED NOT NULL DEFAULT '0',
  `repute` bigint UNSIGNED NOT NULL DEFAULT '0',
  `bolts` bigint UNSIGNED NOT NULL DEFAULT '100',
  `rubles` bigint UNSIGNED NOT NULL DEFAULT '50',
  `energy` bigint UNSIGNED NOT NULL DEFAULT '50',
  `max_energy` bigint UNSIGNED NOT NULL DEFAULT '50',
  `updateEnergy` int DEFAULT NULL,
  `hp` bigint UNSIGNED NOT NULL DEFAULT '100',
  `max_hp` bigint UNSIGNED NOT NULL DEFAULT '100',
  `updateHP` int DEFAULT NULL,
  `addDate` int DEFAULT NULL,
  `updDate` int DEFAULT NULL,
  `status` text,
  `power` bigint UNSIGNED NOT NULL DEFAULT '3',
  `shield` bigint UNSIGNED NOT NULL DEFAULT '3',
  `criticalDamage` int NOT NULL DEFAULT '120',
  `criticalChance` int NOT NULL DEFAULT '5',
  `training` int DEFAULT NULL,
  `whatTraining` set('power','shield','criticalDamage','max_energy','max_hp') DEFAULT NULL,
  `free_energy` int DEFAULT NULL,
  `everyDay` tinyint(1) NOT NULL DEFAULT '0',
  `changeBolts` int NOT NULL DEFAULT '50000',
  `autoload` tinyint(1) NOT NULL DEFAULT '1',
  `biceps` bigint UNSIGNED NOT NULL DEFAULT '0',
  `beta` tinyint(1) NOT NULL DEFAULT '0',
  `gift` tinyint(1) NOT NULL DEFAULT '0',
  `knife` bigint UNSIGNED NOT NULL DEFAULT '0',
  `pistol` bigint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `weapons`
--

CREATE TABLE `weapons` (
  `id` bigint NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `about` text,
  `slot` set('head','top','body','boot','accessory') DEFAULT NULL,
  `quality` set('trash','normal','rare','heroic','souvenir') DEFAULT NULL,
  `price` set('bolts','rubles') DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `lvl` int NOT NULL DEFAULT '1',
  `how` set('random','boss','market','ivent') NOT NULL DEFAULT 'ivent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `weapons`
--

INSERT INTO `weapons` (`id`, `name`, `about`, `slot`, `quality`, `price`, `amount`, `lvl`, `how`) VALUES
(1, 'Олимпийка «Beta»', 'Чёткая олимпийка для чёткого пацана, что тут с самого начала.', 'top', 'souvenir', NULL, NULL, 1, 'ivent'),
(2, 'Спортивки «Beta»', 'Чёткие спортивки для чёткого пацана, что тут с самого начала.', 'boot', 'souvenir', NULL, NULL, 1, 'ivent'),
(3, 'Восьмиклинка', 'Восьмиклинка не простая, а воровская.', 'head', 'souvenir', NULL, NULL, 1, 'ivent'),
(4, 'Сельская восьмиклинка', 'Кто вообще еще так носит?', 'head', 'rare', NULL, NULL, 1, 'boss'),
(5, 'Соломинка в зубы', 'Да-да, это знакомо с самого детства', 'accessory', 'heroic', NULL, NULL, 1, 'boss'),
(6, 'Грязная майка', 'Похож на типичного соседа сверху, что ругается с женой', 'body', 'rare', NULL, NULL, 1, 'boss'),
(7, 'Майка', 'Самая обычная майка, ничего такого', 'body', 'normal', NULL, NULL, 1, 'market'),
(8, 'Белая футболка', 'Белый цвет - это элегантность.', 'body', 'normal', NULL, NULL, 1, 'market'),
(9, 'Дедовский плащ', 'Семейная реликвия. Не надевать на голое тело и не ходить так в парк.', 'top', 'heroic', 'rubles', 20, 1, 'boss'),
(10, 'Черная футболка', 'Как будто с шахтера сняли', 'body', 'normal', 'bolts', NULL, 1, 'market'),
(11, 'Синяя футболка', 'Обычная синяя футболка.', 'body', 'normal', NULL, NULL, 1, 'market'),
(12, 'Красная футболка', 'Красная футболка. Без надписей, без картинок, без возможности роста.', 'body', 'normal', NULL, NULL, 1, 'market'),
(13, 'Жёлтая футболка', 'Как желток или цыпленок. Нет! Просто, как желток', 'body', 'normal', NULL, NULL, 1, 'market'),
(14, 'Серая футболка', '50 оттенков серой футболки.', 'body', 'normal', NULL, NULL, 1, 'market'),
(15, 'Футболка с флагом СССР', 'Патриотизм, все дела. Ну ты понимаешь', 'body', 'normal', NULL, NULL, 1, 'market'),
(16, 'Футболка с гербом СССР', 'Патриотичная футболка, чтобы все знали откуда ты такой нарисовался', 'body', 'normal', NULL, NULL, 1, 'market'),
(17, 'Футболка с титаником', 'В такой футболке можно и поплавать.', 'body', 'rare', 'rubles', 20, 1, 'market'),
(18, 'Олимпийский мишка', 'Олимпиада закончилась, но футболка осталась до лучших времен', 'body', 'normal', 'bolts', NULL, 1, 'market'),
(19, 'Форма сборной СССР', 'Форма игрока за сборную СССР', 'body', 'rare', 'rubles', NULL, 1, 'market'),
(20, 'Футбольная форма СССР', 'Форма игрока за футбольную сборную СССР', 'body', 'rare', 'rubles', NULL, 1, 'market'),
(21, 'Форма игрока СССР', 'Форма игрока за сборную СССР', 'body', 'rare', 'rubles', NULL, 1, 'market'),
(22, 'Худак', 'Нелегально привезена из США.', 'top', 'heroic', 'rubles', NULL, 1, 'market');

-- --------------------------------------------------------

--
-- Структура таблицы `weapons_name`
--

CREATE TABLE `weapons_name` (
  `id` int NOT NULL,
  `id_weapon` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `accept` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `weapons_stats`
--

CREATE TABLE `weapons_stats` (
  `id` bigint NOT NULL,
  `id_weapon` int DEFAULT NULL,
  `atrb` set('power','shield','criticalDamage','criticalChance','max_energy','max_hp') DEFAULT NULL,
  `bonus` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `weapons_stats`
--

INSERT INTO `weapons_stats` (`id`, `id_weapon`, `atrb`, `bonus`) VALUES
(1, 1, 'max_energy', 10),
(2, 1, 'shield', 10),
(3, 1, 'power', 5),
(4, 2, 'criticalDamage', 20),
(5, 2, 'criticalChance', 10),
(6, 2, 'max_hp', 25),
(7, 3, 'shield', 5),
(8, 3, 'power', 5),
(9, 3, 'criticalChance', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `weapons_users`
--

CREATE TABLE `weapons_users` (
  `id` bigint NOT NULL,
  `id_user` int NOT NULL,
  `id_weapon` int NOT NULL,
  `wear` int NOT NULL DEFAULT '100',
  `dateAdd` int DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `background`
--
ALTER TABLE `background`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `backgrounds`
--
ALTER TABLE `backgrounds`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `banList`
--
ALTER TABLE `banList`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `boss`
--
ALTER TABLE `boss`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `boss_chat`
--
ALTER TABLE `boss_chat`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `boss_drop`
--
ALTER TABLE `boss_drop`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `boss_fight`
--
ALTER TABLE `boss_fight`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_boss` (`id_boss`),
  ADD KEY `id_lider` (`id_lider`);

--
-- Индексы таблицы `boss_invite`
--
ALTER TABLE `boss_invite`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `boss_logs`
--
ALTER TABLE `boss_logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `boss_members`
--
ALTER TABLE `boss_members`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `boss_users`
--
ALTER TABLE `boss_users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `businessList`
--
ALTER TABLE `businessList`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `businessRaid`
--
ALTER TABLE `businessRaid`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `craft`
--
ALTER TABLE `craft`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `craft_item`
--
ALTER TABLE `craft_item`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `craft_turn`
--
ALTER TABLE `craft_turn`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `districts_users`
--
ALTER TABLE `districts_users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dots`
--
ALTER TABLE `dots`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `everyDay`
--
ALTER TABLE `everyDay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Индексы таблицы `forum_category`
--
ALTER TABLE `forum_category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `forum_messages`
--
ALTER TABLE `forum_messages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `groups_chat`
--
ALTER TABLE `groups_chat`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `groups_logs`
--
ALTER TABLE `groups_logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `groups_users`
--
ALTER TABLE `groups_users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `merchant`
--
ALTER TABLE `merchant`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `npc`
--
ALTER TABLE `npc`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `objects`
--
ALTER TABLE `objects`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `objects_users`
--
ALTER TABLE `objects_users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `oldLogin`
--
ALTER TABLE `oldLogin`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `phone_notify`
--
ALTER TABLE `phone_notify`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `phone_sms`
--
ALTER TABLE `phone_sms`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `phone_sms_attach`
--
ALTER TABLE `phone_sms_attach`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `refferals_in`
--
ALTER TABLE `refferals_in`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `refferals_site`
--
ALTER TABLE `refferals_site`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Индексы таблицы `weapons`
--
ALTER TABLE `weapons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Индексы таблицы `weapons_name`
--
ALTER TABLE `weapons_name`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `weapons_stats`
--
ALTER TABLE `weapons_stats`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `weapons_users`
--
ALTER TABLE `weapons_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `background`
--
ALTER TABLE `background`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `backgrounds`
--
ALTER TABLE `backgrounds`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `banList`
--
ALTER TABLE `banList`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `boss`
--
ALTER TABLE `boss`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `boss_chat`
--
ALTER TABLE `boss_chat`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `boss_drop`
--
ALTER TABLE `boss_drop`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `boss_fight`
--
ALTER TABLE `boss_fight`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `boss_invite`
--
ALTER TABLE `boss_invite`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `boss_logs`
--
ALTER TABLE `boss_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `boss_members`
--
ALTER TABLE `boss_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `boss_users`
--
ALTER TABLE `boss_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `business`
--
ALTER TABLE `business`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `businessList`
--
ALTER TABLE `businessList`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `businessRaid`
--
ALTER TABLE `businessRaid`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `craft`
--
ALTER TABLE `craft`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `craft_item`
--
ALTER TABLE `craft_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `craft_turn`
--
ALTER TABLE `craft_turn`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `districts`
--
ALTER TABLE `districts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `districts_users`
--
ALTER TABLE `districts_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dots`
--
ALTER TABLE `dots`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `emails`
--
ALTER TABLE `emails`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `everyDay`
--
ALTER TABLE `everyDay`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `forum_category`
--
ALTER TABLE `forum_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `forum_messages`
--
ALTER TABLE `forum_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `groups_chat`
--
ALTER TABLE `groups_chat`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `groups_logs`
--
ALTER TABLE `groups_logs`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `groups_users`
--
ALTER TABLE `groups_users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `merchant`
--
ALTER TABLE `merchant`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `npc`
--
ALTER TABLE `npc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `objects`
--
ALTER TABLE `objects`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `objects_users`
--
ALTER TABLE `objects_users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `oldLogin`
--
ALTER TABLE `oldLogin`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `phone_notify`
--
ALTER TABLE `phone_notify`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `phone_sms`
--
ALTER TABLE `phone_sms`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `phone_sms_attach`
--
ALTER TABLE `phone_sms_attach`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `refferals_in`
--
ALTER TABLE `refferals_in`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `refferals_site`
--
ALTER TABLE `refferals_site`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `weapons`
--
ALTER TABLE `weapons`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT для таблицы `weapons_name`
--
ALTER TABLE `weapons_name`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `weapons_stats`
--
ALTER TABLE `weapons_stats`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `weapons_users`
--
ALTER TABLE `weapons_users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `boss_fight`
--
ALTER TABLE `boss_fight`
  ADD CONSTRAINT `boss_fight_ibfk_1` FOREIGN KEY (`id_boss`) REFERENCES `boss` (`id`),
  ADD CONSTRAINT `boss_fight_ibfk_2` FOREIGN KEY (`id_lider`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
