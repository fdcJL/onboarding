-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 27, 2024 at 03:00 AM
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
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `created`, `modified`) VALUES
(1, '2024-05-26 17:29:28', '2024-05-26 17:29:28'),
(2, '2024-05-26 20:49:06', '2024-05-26 20:49:06'),
(3, '2024-05-26 20:52:15', '2024-05-26 20:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `convo_id` int(11) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `convo_id`, `sender_id`, `receiver_id`, `content`, `created`, `modified`) VALUES
(1, 1, 1, 2, 'Hi', '2024-05-26 17:29:28', '2024-05-26 17:29:28'),
(2, 1, 2, 1, 'Hello', '2024-05-26 17:32:28', '2024-05-26 17:32:28'),
(3, 1, 1, 2, 'How are you today?', '2024-05-26 17:33:28', '2024-05-26 17:33:28'),
(4, 1, 2, 1, 'Im good', '2024-05-26 17:34:28', '2024-05-26 17:34:28'),
(5, 2, 3, 1, 'Hey', '2024-05-26 20:50:06', '2024-05-26 20:50:06'),
(6, 2, 1, 3, 'yow', '2024-05-26 20:51:06', '2024-05-26 20:51:06'),
(7, 3, 2, 3, '....', '2024-05-26 20:53:06', '2024-05-26 20:53:06'),
(8, 3, 3, 2, '?????', '2024-05-26 20:54:06', '2024-05-26 20:54:06');

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
(1, 'Software', 'Developer', 'software@gmail.com', '$2a$10$qe6mNzBesmYQi.Ctz8O/QeP2h3WL5USrLL7r1SlvJsb/jZba4QxDe', '1999-06-04', 'Male', 'Software Developer', 'This is Hubby', 'profile_66529763bccce.jpg', '2024-05-26 03:40:37', '2024-05-26 01:57:34', '2024-05-26 03:40:37'),
(2, 'system', 'Admin', 'sysadmin@gmail.com', '$2a$10$MM5JhbeXoUM047JAs3xfLeEFRtUc6KNHN/9wKV4DwyUyNNiRd2C8m', NULL, NULL, NULL, NULL, 'default.webp', '2024-05-26 10:57:12', '2024-05-26 09:26:53', '2024-05-26 10:57:12'),
(3, 'John Lloyd', 'Batican', 'jlbatican@gmail.com', '$2a$10$FGwF1AcDD/C986qgx7oBg.0hGWYauVEkNb0eh4jkmDKtomRbwUREO', '1999-06-04', 'Male', 'Software Developer', 'hubby', 'default.webp', '2024-05-26 14:16:23', '2024-05-26 10:57:41', '2024-05-26 14:16:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_convo_id` (`convo_id`),
  ADD KEY `fk_sender_id` (`sender_id`),
  ADD KEY `fk_receiver_id` (`receiver_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_convo_id` FOREIGN KEY (`convo_id`) REFERENCES `conversations` (`id`),
  ADD CONSTRAINT `fk_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_sender_id` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
