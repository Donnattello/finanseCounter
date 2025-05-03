-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Квт 16 2025 р., 16:55
-- Версія сервера: 10.4.32-MariaDB
-- Версія PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `cashcrew`
--

-- --------------------------------------------------------

--
-- Структура таблиці `counts`
--

CREATE TABLE `counts` (
  `id_c` int(11) NOT NULL,
  `done_id` int(11) NOT NULL,
  `mean` float NOT NULL,
  `total` float NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `groups`
--

CREATE TABLE `groups` (
  `id_g` int(11) NOT NULL,
  `g_name` varchar(100) NOT NULL,
  `owner_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `group_users`
--

CREATE TABLE `group_users` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `group_user_count`
--

CREATE TABLE `group_user_count` (
  `id_owe_to` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `owe_to` int(11) NOT NULL,
  `amount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `transactions`
--

CREATE TABLE `transactions` (
  `id_tr` int(11) NOT NULL,
  `data` date NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `counted` bit(1) NOT NULL DEFAULT b'0',
  `amount` int(11) NOT NULL,
  `count_num_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `u_name` varchar(100) NOT NULL,
  `email` int(11) DEFAULT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `counts`
--
ALTER TABLE `counts`
  ADD PRIMARY KEY (`id_c`),
  ADD KEY `done_id` (`done_id`);

--
-- Індекси таблиці `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id_g`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Індекси таблиці `group_users`
--
ALTER TABLE `group_users`
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `group_user_count`
--
ALTER TABLE `group_user_count`
  ADD PRIMARY KEY (`id_owe_to`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id_tr`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `count_num_id` (`count_num_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `counts`
--
ALTER TABLE `counts`
  MODIFY `id_c` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `groups`
--
ALTER TABLE `groups`
  MODIFY `id_g` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `group_user_count`
--
ALTER TABLE `group_user_count`
  MODIFY `id_owe_to` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id_tr` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `counts`
--
ALTER TABLE `counts`
  ADD CONSTRAINT `counts_ibfk_1` FOREIGN KEY (`done_id`) REFERENCES `users` (`id_user`);

--
-- Обмеження зовнішнього ключа таблиці `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id_user`);

--
-- Обмеження зовнішнього ключа таблиці `group_users`
--
ALTER TABLE `group_users`
  ADD CONSTRAINT `group_users_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id_g`),
  ADD CONSTRAINT `group_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`);

--
-- Обмеження зовнішнього ключа таблиці `group_user_count`
--
ALTER TABLE `group_user_count`
  ADD CONSTRAINT `group_user_count_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id_g`),
  ADD CONSTRAINT `group_user_count_ibfk_2` FOREIGN KEY (`id_owe_to`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `group_user_count_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`);

--
-- Обмеження зовнішнього ключа таблиці `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`count_num_id`) REFERENCES `counts` (`id_c`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id_g`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
