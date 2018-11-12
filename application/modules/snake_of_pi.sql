-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 12, 2018 at 06:40 AM
-- Server version: 5.6.38-log
-- PHP Version: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `snake_of_pi`
--

-- --------------------------------------------------------

--
-- Table structure for table `food`
--

CREATE TABLE `food` (
  `id` int(11) NOT NULL,
  `x` int(11) NOT NULL DEFAULT '0',
  `y` int(11) NOT NULL DEFAULT '0',
  `value` int(11) NOT NULL DEFAULT '1',
  `type` int(11) NOT NULL DEFAULT '0',
  `map_id` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `map`
--

CREATE TABLE `map` (
  `id` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `last_updated` int(32) DEFAULT NULL,
  `snake_size` int(12) NOT NULL DEFAULT '64'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `map`
--

INSERT INTO `map` (`id`, `width`, `height`, `last_updated`, `snake_size`) VALUES
(1, 14, 7, 1541961328, 64);

-- --------------------------------------------------------

--
-- Table structure for table `snake`
--

CREATE TABLE `snake` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `direction` varchar(16) NOT NULL DEFAULT 'left',
  `map_id` int(11) NOT NULL DEFAULT '1',
  `deleted_at` int(32) DEFAULT '0',
  `eating` int(16) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `snake`
--

INSERT INTO `snake` (`id`, `user_id`, `direction`, `map_id`, `deleted_at`, `eating`) VALUES
(11, 2, 'right', 1, 0, 0),
(12, 2, 'right', 1, 0, 0),
(13, 2, 'right', 1, 0, 0),
(14, 2, 'right', 1, 0, 0),
(15, 2, 'right', 1, 0, 0),
(16, 2, 'right', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `snake_body`
--

CREATE TABLE `snake_body` (
  `id` int(11) NOT NULL,
  `snake_id` int(11) NOT NULL,
  `x` int(11) NOT NULL DEFAULT '0',
  `y` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `snake_body`
--

INSERT INTO `snake_body` (`id`, `snake_id`, `x`, `y`) VALUES
(15, 1, 14, 0),
(31, 2, 13, 0),
(164, 11, 10, 0),
(165, 12, 6, 0),
(166, 13, 5, 0),
(167, 14, 4, 0),
(168, 15, 3, 0),
(169, 16, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `system`
--

CREATE TABLE `system` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `token` varchar(64) DEFAULT NULL,
  `login` varchar(32) NOT NULL,
  `password` int(32) NOT NULL,
  `score` int(12) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `token`, `login`, `password`, `score`) VALUES
(2, 'Вася', '12', 'vasya', 123, 1),
(3, 'Петя', NULL, 'petya', 111, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_access_token`
--

CREATE TABLE `user_access_token` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiredAt` int(32) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_access_token`
--

INSERT INTO `user_access_token` (`id`, `user_id`, `token`, `expiredAt`, `createdAt`) VALUES
(6, 2, 'RHwRQ69u7vyU3nQltGBvITVCEYppBoa5p8LnrEiuwUn8Zn5yH31s0mg3Jjq5elBK', 1541751857, '2018-11-02 08:24:17'),
(7, 2, 'lrmLp3PmhbzfjgHEjiOhFIYnbwODeaphDCl1LlJ6qk9eZ1HCjpYZHW4y1Bz3BRAG', 1541751922, '2018-11-02 08:25:22'),
(8, 2, 'qHrqTxxM0Qv9BAXf2eYjaK92iBAEiMRSwJ5UMqRvrlfRlsSHPcOfKAEcq3vNJAYL', 1541751950, '2018-11-02 08:25:50'),
(9, 2, 'ZE7i3cy7gJnssba2Z313HixfVnPPwgrwPrwxHQDBrZRVWOPewMBK1SNaOzvJHOia', 1541752055, '2018-11-02 08:27:35'),
(10, 2, 'jFHatv0JWl654E4sFowz62xCdyIlmq9XmBqbzxEeQfqPvaObpgq1T1nyNodwuR47', 1541752089, '2018-11-02 08:28:09'),
(11, 2, 'ZtwwhFIAFp1vwbwvGYNitdX7MTAETtN9HV9jwJMmdQf17JFv4mCxqdck0g0BbX8A', 1541752337, '2018-11-02 08:32:17'),
(12, 2, 'VdlTHBNsyQI3C9VEsgNHbn9cnelA82dR4w6tfSQohRO5gEPxMZedV6T3iG7zYYNH', 1542483159, '2018-11-10 19:32:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `food`
--
ALTER TABLE `food`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `map`
--
ALTER TABLE `map`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `snake`
--
ALTER TABLE `snake`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `snake_body`
--
ALTER TABLE `snake_body`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_access_token`
--
ALTER TABLE `user_access_token`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `food`
--
ALTER TABLE `food`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `map`
--
ALTER TABLE `map`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `snake`
--
ALTER TABLE `snake`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `snake_body`
--
ALTER TABLE `snake_body`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `system`
--
ALTER TABLE `system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_access_token`
--
ALTER TABLE `user_access_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
