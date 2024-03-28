-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2024 at 02:58 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `valorantguesser`
--

-- --------------------------------------------------------

--
-- Table structure for table `ability`
--

CREATE TABLE `ability` (
  `id` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `img` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ability`
--

INSERT INTO `ability` (`id`, `name`, `img`) VALUES
(1, 'Recon Bolt', '/ValorantGuesser/img/ability/Recon_Bolt.png'),
(2, 'Aftershock', '/ValorantGuesser/img/ability/Aftershock.png'),
(3, 'Thrash', '/ValorantGuesser/img/ability/Thrash.png');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `password`) VALUES
('admin', 'password');

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `id` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `img` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agent`
--

INSERT INTO `agent` (`id`, `name`, `img`) VALUES
(1, 'Breach', '/ValorantGuesser/img/agent/Breach.png'),
(2, 'Gekko', '/ValorantGuesser/img/agent/Gekko.png'),
(3, 'Sova', '/ValorantGuesser/img/agent/Sova.png');

-- --------------------------------------------------------

--
-- Table structure for table `dailychallenge`
--

CREATE TABLE `dailychallenge` (
  `id` int(11) NOT NULL,
  `ability_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `graffiti_id` int(11) NOT NULL,
  `playercard_id` int(11) NOT NULL,
  `quote_id` int(11) NOT NULL,
  `weapon_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dailychallenge`
--

INSERT INTO `dailychallenge` (`id`, `ability_id`, `agent_id`, `graffiti_id`, `playercard_id`, `quote_id`, `weapon_id`) VALUES
(1, 2, 1, 3, 2, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `graffiti`
--

CREATE TABLE `graffiti` (
  `id` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `img` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `graffiti`
--

INSERT INTO `graffiti` (`id`, `name`, `img`) VALUES
(1, 'Nice To Zap You', '/ValorantGuesser/img/graffiti/Nice_To_Zap_You_Spray.png'),
(2, 'Shrug', '/ValorantGuesser/img/graffiti/Shrug_Spray.png'),
(3, 'Stay Hydrated', '/ValorantGuesser/img/graffiti/Stay_Hydrated.png');

-- --------------------------------------------------------

--
-- Table structure for table `playercard`
--

CREATE TABLE `playercard` (
  `id` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `img` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playercard`
--

INSERT INTO `playercard` (`id`, `name`, `img`) VALUES
(1, 'Cosmic Origin', '/ValorantGuesser/img/playercard/Cosmic_Origin.png'),
(2, 'Nachtelang', '/ValorantGuesser/img/playercard/Nachtelang.png'),
(3, 'One Dark Night', '/ValorantGuesser/img/playercard/One_Dark_Night.png');

-- --------------------------------------------------------

--
-- Table structure for table `quote`
--

CREATE TABLE `quote` (
  `id` int(10) NOT NULL,
  `quote` varchar(80) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quote`
--

INSERT INTO `quote` (`id`, `quote`, `name`) VALUES
(1, '\"I paid a heavy price to commune with Nightmare. Don\'t waste it.\"', 'Fade'),
(2, '\"This neighborhood used to be so much better without this Kingdom shit.\"', 'Yoru'),
(3, '\"Those flowers are massive! Who grew these? Them and me, we gotta link up.\"', 'Skye');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(10) NOT NULL,
  `tries` int(10) NOT NULL,
  `current_table` int(10) NOT NULL,
  `winner` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `tries`, `current_table`, `winner`) VALUES
(1, 5, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `weapon`
--

CREATE TABLE `weapon` (
  `id` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `img` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weapon`
--

INSERT INTO `weapon` (`id`, `name`, `img`) VALUES
(1, 'Blastx knife', '/ValorantGuesser/img/weapon/blastx_knife.png'),
(2, 'Oni phantom', '/ValorantGuesser/img/weapon/oni_phantom.png'),
(3, 'Snowfall classic', '/ValorantGuesser/img/weapon/snowfall_classic.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ability`
--
ALTER TABLE `ability`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dailychallenge`
--
ALTER TABLE `dailychallenge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abilityFK` (`ability_id`),
  ADD KEY `agentFK` (`agent_id`),
  ADD KEY `graffitiFK` (`graffiti_id`),
  ADD KEY `playercardFK` (`playercard_id`),
  ADD KEY `quoteFK` (`quote_id`),
  ADD KEY `weaponFK` (`weapon_id`);

--
-- Indexes for table `graffiti`
--
ALTER TABLE `graffiti`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `playercard`
--
ALTER TABLE `playercard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quote`
--
ALTER TABLE `quote`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weapon`
--
ALTER TABLE `weapon`
  ADD PRIMARY KEY (`id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dailychallenge`
--
ALTER TABLE `dailychallenge`
  ADD CONSTRAINT `abilityFK` FOREIGN KEY (`ability_id`) REFERENCES `ability` (`id`),
  ADD CONSTRAINT `agentFK` FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`),
  ADD CONSTRAINT `graffitiFK` FOREIGN KEY (`graffiti_id`) REFERENCES `graffiti` (`id`),
  ADD CONSTRAINT `playercardFK` FOREIGN KEY (`playercard_id`) REFERENCES `playercard` (`id`),
  ADD CONSTRAINT `quoteFK` FOREIGN KEY (`quote_id`) REFERENCES `quote` (`id`),
  ADD CONSTRAINT `weaponFK` FOREIGN KEY (`weapon_id`) REFERENCES `weapon` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
