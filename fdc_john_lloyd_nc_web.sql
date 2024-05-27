-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 27, 2024 at 11:46 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fdc_john_lloyd_nc_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `latest_message_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `room_id`, `latest_message_id`, `created`, `modified`) VALUES
(1, 1, 3, '2024-05-27 15:53:20', '2024-05-27 15:53:20'),
(2, 2, 6, '2024-05-27 15:57:20', '2024-05-27 15:57:20'),
(3, 3, 9, '2024-05-27 16:10:20', '2024-05-27 16:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) DEFAULT 0 COMMENT '1 = read and 0 = unread',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `room_id`, `sender_id`, `receiver_id`, `content`, `status`, `created`, `modified`) VALUES
(1, 1, 2, 1, 'Hello', 1, '2024-05-27 15:32:20', '2024-05-27 15:32:20'),
(2, 1, 1, 2, 'Hi', 1, '2024-05-27 15:52:20', '2024-05-27 15:52:20'),
(3, 1, 2, 1, 'Bro', 1, '2024-05-27 15:53:20', '2024-05-27 15:53:20'),
(4, 2, 2, 3, 'Hey', 1, '2024-05-27 15:55:20', '2024-05-27 15:55:20'),
(5, 2, 3, 2, 'What', 1, '2024-05-27 15:56:20', '2024-05-27 15:57:20'),
(6, 2, 3, 2, '???', 0, '2024-05-27 15:57:20', '2024-05-27 15:57:20'),
(7, 3, 3, 1, 'Good Morning', 1, '2024-05-27 15:59:20', '2024-05-27 15:59:20'),
(8, 3, 1, 3, 'Good Morning :) ', 1, '2024-05-27 16:00:20', '2024-05-27 16:00:20'),
(9, 3, 1, 3, '.....', 0, '2024-05-27 16:10:20', '2024-05-27 16:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bdate` date DEFAULT NULL,
  `gender` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hubby` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.webp',
  `last_login` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `password`, `bdate`, `gender`, `position`, `hubby`, `profile`, `last_login`, `created`, `modified`) VALUES
(1, 'Software', 'Developer', 'software@gmail.com', '$2a$10$qe6mNzBesmYQi.Ctz8O/QeP2h3WL5USrLL7r1SlvJsb/jZba4QxDe', '1999-06-04', 'Male', 'Software Developer', 'This is Hubby', 'default.webp', '2024-05-27 11:41:56', '2024-05-26 01:57:34', '2024-05-27 11:41:56'),
(2, 'system', 'Admin', 'sysadmin@gmail.com', '$2a$10$MM5JhbeXoUM047JAs3xfLeEFRtUc6KNHN/9wKV4DwyUyNNiRd2C8m', NULL, NULL, NULL, NULL, 'default.webp', '2024-05-27 11:35:35', '2024-05-26 09:26:53', '2024-05-27 11:35:35'),
(3, 'John Lloyd', 'Batican', 'jlbatican@gmail.com', '$2a$10$FGwF1AcDD/C986qgx7oBg.0hGWYauVEkNb0eh4jkmDKtomRbwUREO', '1999-06-04', 'Male', 'Software Developer', 'hubby', 'default.webp', '2024-05-27 11:31:24', '2024-05-26 10:57:41', '2024-05-27 11:31:24'),
(4, 'sample', 'account', 'sample@gmail.com', '$2a$10$5m2ymyp.pKQ/xFjg.WI.5OQy5XTiPlsOVd44tcjKr6EXgiN90WPjK', NULL, NULL, NULL, NULL, 'default.webp', NULL, '2024-05-27 11:42:24', '2024-05-27 11:42:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversations_rooms_FK` (`room_id`),
  ADD KEY `conversations_messages_FK` (`latest_message_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_users_FK` (`sender_id`),
  ADD KEY `messages_users_FK_1` (`receiver_id`),
  ADD KEY `messages_rooms_FK` (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_messages_FK` FOREIGN KEY (`latest_message_id`) REFERENCES `messages` (`id`),
  ADD CONSTRAINT `conversations_rooms_FK` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_rooms_FK` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `messages_users_FK` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_users_FK_1` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
