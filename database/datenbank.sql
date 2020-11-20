-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 20. Nov 2020 um 13:40
-- Server-Version: 10.4.14-MariaDB
-- PHP-Version: 7.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `reservierung`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rclient`
--

CREATE TABLE `rclient` (
  `clientID` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `reserveID` int(11) NOT NULL,
  `clientVorname` varchar(15) COLLATE utf8mb4_german2_ci NOT NULL,
  `clientName` varchar(15) COLLATE utf8mb4_german2_ci NOT NULL,
  `clientMail` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `clientAdresse` varchar(40) COLLATE utf8mb4_german2_ci NOT NULL,
  `clientTNR` varchar(15) COLLATE utf8mb4_german2_ci NOT NULL,
  `clientDate` datetime NOT NULL,
  `clientConfirm` varchar(40) COLLATE utf8mb4_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rclient`
--

INSERT INTO `rclient` (`clientID`, `reserveID`, `clientVorname`, `clientName`, `clientMail`, `clientAdresse`, `clientTNR`, `clientDate`, `clientConfirm`) VALUES
('5f8454f726b8b', 63, 'Test3', 'Lowicki', 'flowicki@web.de', 'Blücherstraße 17a', '1849846846', '2020-10-12 15:07:03', '5f8454f72a108'),
('5f84555d47096', 64, 'Test3', 'Lowicki', 'flowicki@web.de', 'Blücherstraße 17a', '1849846846', '0000-00-00 00:00:00', '5f84555d4aba7'),
('5f845736d2829', 65, 'Test3', 'Lowicki', 'flowicki@web.de', 'Blücherstraße 17a', '1849846846', '2020-10-12 15:16:38', '5f845736d5f48'),
('5f84575f297b1', 66, 'Test3', 'Lowicki', 'flowicki@web.de', 'Blücherstraße 17a', '1849846846', '2020-10-12 15:17:19', '5f84575f2d500'),
('5f853a7298d0d', 67, 'Test3', 'Lowicki', 'flowicki@web.de', 'Blücherstraße 17a', '1849846846', '2020-10-13 07:26:10', '5f853a729a1d2'),
('5f8543a91c8a2', 70, 'test4', 'Lowicki', 'mertero@web.de', 'Blücherstraße 17a', '1849846846', '2020-10-13 08:05:29', '5f8543a91fe10'),
('5f869ad0ef8e7', 73, 'Test3', 'Lowicki', 'flowicki@web.de', 'Blücherstraße 17a', '1849846846', '2020-10-14 08:29:37', '5f869ad0f2bc8'),
('5f86a4310f504', 76, 'David', 'Lowicki', 'dlowicki@ibs-ka.de', 'Sophienstraße 92', '07218303144', '2020-10-14 09:09:37', '5f86a4311305f'),
('5f86e1683a338', 78, 'Test5', 'TestName', 'TestMail', 'TestAdresse', 'TestNummer', '2020-10-14 13:30:48', '5f86e1683dafe'),
('5f86f17285e83', 79, 'David', 'Lowicki', 'lowicki@gmx.de', 'Sopienstraße 92', '07218303144', '2020-10-14 14:39:14', '5f86f17289903'),
('5f8d41d624334', 85, 'David', 'Lowicki', 'flowicki@web.de', 'Blücherstraße 17a', 'wasdwasd', '2020-10-19 09:35:50', '5f8d41d62a53e'),
('5f8d5f9815ea4', 86, 'David', 'Lowicki', 'mertero@web.de', 'Blücherstraße 17a', 'waswwasd', '2020-10-19 11:42:48', '5f8d5f981c4b5'),
('5f8d61ae8069c', 89, 'David', 'König', 'koenig@mail.com', 'koenigadresse 2', 'telefonnummer', '2020-10-19 11:51:42', '5f8d61ae87589'),
('5f8ee671d5ea4', 85, 'David2', 'David2', 'David2@web.de', 'David2Straße', 'David2', '2020-10-20 15:30:25', '5f8ee671d33a8'),
('5f917476db540', 93, 'David', 'Lowicki', 'lowicki@gmx.de', 'lowickistraße 2', '914832479324', '2020-10-22 14:00:54', '5f917476e2755'),
('5f91820f4a1c2', 99, 'David', 'Lowicki', 'lowicki@gmx.de', 'lowickistraße 2', '914832479324', '2020-10-22 14:58:55', '5f91820f4ed26'),
('5f91820f4f507', 99, 'Test22', 'Test22', 'Test22', 'Test22', 'Test22', '2020-10-22 14:58:55', '5f91820f4f51c'),
('5f97d70f0670d', 100, 'David', 'Lowicki', 'Test@web.de', 'TestAdresse', 'TestNummer', '2020-10-27 09:15:11', '5f97d70f0a9b2'),
('5fabc6e5bce0d', 101, 'test', 'test', 'test', 'test', 'test', '2020-11-11 12:11:33', '5fabc6e5c09f65fabc6e5c09f8'),
('5fabdd3fb396c', 102, 'test1', 'test1', 'test1', 'test1', 'test1', '2020-11-11 13:46:55', '5fabdd3fb75915fabdd3fb7594'),
('5fabdd3fb79f8', 102, 'test', 'test', 'test', 'test', 'test', '2020-11-11 13:46:55', '5fabdd3fb7a025fabdd3fb7a03'),
('f93b6ec4aaec', 100, 'Luca', 'Yavsan', '', '', '', '2020-10-28 14:51:59', '5f99777f321f8');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rnoshow`
--

CREATE TABLE `rnoshow` (
  `nsID` int(11) NOT NULL,
  `nsMail` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `nsAmount` int(11) NOT NULL,
  `nsDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rnoshow`
--

INSERT INTO `rnoshow` (`nsID`, `nsMail`, `nsAmount`, `nsDate`) VALUES
(4, 'flowicki@web.de', 7, '2020-10-19 11:00:26'),
(14, 'test@web.de', 1, '2020-11-17 09:06:49'),
(17, 'test@domain.de', 1, '2020-11-17 09:43:23'),
(21, 'wasd@domain.de', 3, '2020-11-17 12:31:46');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rreserve`
--

CREATE TABLE `rreserve` (
  `reserveID` int(11) NOT NULL,
  `tableID` int(11) NOT NULL,
  `clientID` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `reserveDate` date NOT NULL,
  `reserveStart` time NOT NULL,
  `reserveEnd` time NOT NULL,
  `reserveDuration` varchar(15) COLLATE utf8mb4_german2_ci NOT NULL,
  `reserveAmount` int(11) NOT NULL,
  `reserveCookie` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `reserveState` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rreserve`
--

INSERT INTO `rreserve` (`reserveID`, `tableID`, `clientID`, `reserveDate`, `reserveStart`, `reserveEnd`, `reserveDuration`, `reserveAmount`, `reserveCookie`, `reserveState`) VALUES
(29, 8, '5f3e2b1a6f4bc', '0000-00-00', '21:08:00', '00:00:00', '2:30', 10, '', 0),
(30, 31, '5f3e59e1b33f8', '0000-00-00', '17:09:00', '00:00:00', '2:30', 10, '', 0),
(32, 8, '5f4379bb46fe7', '0000-00-00', '17:26:00', '00:00:00', 'gz', 10, '', 0),
(33, 26, '5f449ebeb8dac', '0000-00-00', '12:30:00', '00:00:00', '2:30', 0, '', 0),
(34, 8, '5f44a085c0e43', '2020-10-20', '19:30:00', '22:00:00', '2:30', 5, '', 0),
(35, 31, '5f44bcea31a0d', '0000-00-00', '19:30:00', '00:00:00', '2:30', 10, '', 0),
(36, 31, '5f44e596d7c62', '0000-00-00', '17:18:00', '00:00:00', 'gz', 10, '', 0),
(37, 31, '5f44f3411b489', '0000-00-00', '13:17:00', '00:00:00', '2:30', 33, '', 0),
(46, 9, '5f461494b7dbd', '0000-00-00', '18:55:00', '00:00:00', 'gz', 5, '', 0),
(51, 9, '5f46184a6e791', '0000-00-00', '14:05:00', '00:00:00', '2:30', 15, '', 0),
(53, 98, '5f4746a85235e', '0000-00-00', '17:00:00', '00:00:00', '2:30', 10, '', 0),
(73, 34, '5f869ad0ef8e7', '2020-11-11', '20:00:00', '11:12:00', '2:30', 5, '5f869ad0ef8e9', 4),
(76, 34, '5f86a4310f504', '0000-00-00', '17:00:00', '00:00:00', '2:30', 5, '5f86a4310f507', 1),
(78, 8, '5f86e1683a338', '0000-00-00', '17:00:00', '03:07:00', 'gz', 1, '5f86e1683a33b', 0),
(79, 35, '5f86f17285e83', '0000-00-00', '17:00:00', '03:00:00', '2:30', 1, '5f86f17285e86', 0),
(85, 34, '5f8d41d624334', '2020-10-20', '18:30:00', '21:00:00', '2:30', 3, '5f8d41d624339', 1),
(92, 32, '5f8d6295e7c7c', '2020-10-20', '17:00:00', '19:30:00', '2:30', 1, '5f8d6295e7c7e', 0),
(93, 34, '5f917476db540', '2020-10-22', '17:00:00', '19:30:00', '2:30', 5, '5f917476db543', 0),
(99, 34, '5f91820f4a1c2', '2020-10-22', '22:00:00', '00:00:00', '2:30', 5, '5f91820f4a1c6', 0),
(100, 34, '5f97d70f0670d', '2020-10-27', '17:00:00', '19:30:00', '2:30', 5, '5f97d70f06712', 1),
(101, 9, '5fabc6e5bce0d', '2020-11-11', '17:00:00', '22:00:00', 'gz', 5, '5fabc6e5bce11', 0),
(102, 34, '5fabdd3fb396c', '2020-11-11', '17:00:00', '19:30:00', '2:30', 3, '5fabdd3fb3970', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rtable`
--

CREATE TABLE `rtable` (
  `tableID` int(11) NOT NULL,
  `tableType` varchar(15) COLLATE utf8mb4_german2_ci NOT NULL,
  `tableMax` int(11) NOT NULL,
  `tableMin` int(11) NOT NULL,
  `tableCode` varchar(40) COLLATE utf8mb4_german2_ci NOT NULL,
  `tableActive` varchar(15) COLLATE utf8mb4_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rtable`
--

INSERT INTO `rtable` (`tableID`, `tableType`, `tableMax`, `tableMin`, `tableCode`, `tableActive`) VALUES
(1, 'one', 2, 1, 'gN3M0CTnm0eOR6nnx5dspw', 'open'),
(2, 'one', 3, 1, '9wKVG6zjkkWsUqCcMPDAUg', 'open'),
(3, 'one', 3, 1, '7qMzupQws0yfiHsQaNyvEA', 'open'),
(8, 'one', 3, 1, 'Q9MA8CfFVUuTagANYJpBlg', 'open'),
(9, 'row', 5, 3, 'JMeu8qzzD0HYoqcAnJQOxA', 'open'),
(10, 'row', 6, 3, 'XVW3XZvJTkCIpABbILms6w', 'open'),
(11, 'row', 6, 3, 'tKPjLVITWk2iYiFm9S5BkQ', 'open'),
(12, 'row', 6, 3, 'k876dB47fUOC2HsB6XMzqQ', 'open'),
(21, 'pillar', 5, 3, 'dVR3TrZ8NkKJRvwc7anVVA', 'open'),
(22, 'pillar', 5, 3, 'zM8PFc083UCZ599yiM6XtA', 'open'),
(23, 'pillar', 5, 3, 'yNbCvCcUl0qD9AcxySp6mA', 'open'),
(24, 'pillar', 5, 3, 'qlTD0x5t9UySvl7OJC6CsA', 'open'),
(25, 'one', 3, 1, 'f5xy6jhTzkmhUEuQzVg1Tw', 'open'),
(26, 'one', 3, 1, 'XUdfQTfuEE2NUxBhEWovtg', 'open'),
(27, 'one', 3, 1, 'xbkQ44b2d0qNAbrnVeT5HA', 'open'),
(31, 'one', 4, 1, 'Kfw2nVFMlkWZhJ2MeyaUCw', 'open'),
(32, 'one', 4, 1, 'P68Sx9O9ZUu85tmYBn6w6Q', 'open'),
(33, 'one', 4, 1, 'b6EsC24FskeVaKF6Nwm6PA', 'open'),
(34, 'left', 8, 5, 'zpJ09e69sECQodzWlnGacQ', 'open'),
(35, 'one', 4, 1, '9Q8MbJT300a4u51lI1HCWg', 'open'),
(36, 'right-bottom', 7, 5, 'GOwXG6bYrUyefLddYt3uhg', 'open'),
(37, 'right', 7, 5, 'PnYzx4dNHEiRJeTE6zye8A', 'open'),
(41, 'pillar', 4, 1, '9MBPz9kHvUiurUfC1fIeqg', 'open'),
(42, 'pillar', 6, 1, 'Aifs0HuKHEyYP7YiUc6Msw', 'open'),
(43, 'pillar', 4, 1, 'Rju8PkpzS0SNgWubWXBPXg', 'open'),
(44, 'pillar', 6, 1, '3kpOoMtdxU2Vfk35ioHU0Q', 'open'),
(45, 'pillar', 4, 1, 'QcuoWSAEwk6XqEQEKQFchQ', 'open'),
(46, 'pillar', 6, 1, '9QwoL48I1UK9VbtEXFc1og', 'open'),
(47, 'pillar', 4, 1, 'cVb6gpSnx0WT5fn2gRbfIg', 'open'),
(48, 'pillar', 6, 1, 'Bzh9dxAGi0W0fLsn2Ac9wA', 'open'),
(49, 'pillar', 4, 1, 'cvWMzXPl4UilvyJcVsUqqA', 'open'),
(50, 'pillar', 6, 1, 'BbTipWuImEa53dluHCLvpw', 'open'),
(51, 'one', 4, 1, 'YfYdqyCrH02p5AqCTSq6MQ', 'open'),
(52, 'big', 5, 3, 'jecmM93s3EOjQmVe0aTXZg', 'open'),
(53, 'one', 4, 1, 'xBccO5XaMkWntuS34hOCTw', 'open'),
(54, 'big', 4, 3, 'lQXh61LY70aWeeS6G0DHfA', 'open'),
(55, 'big', 5, 3, 'Svu97xQrrkOMllNdQFeBcg', 'open'),
(56, 'big', 5, 3, 'lyQTRwARyUqNCcMIDerMlA', 'open'),
(57, 'right-layed', 7, 5, '8Zp8QKBzn0mvkHPsIAEaSA', 'open'),
(98, 'pillar', 10, 1, 'yJGSumzsqEGLainM1jCRJw', 'open'),
(99, 'right', 10, 5, 'rRNLXwxp9Ea8c4WOkl5p9w', 'open');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ruser`
--

CREATE TABLE `ruser` (
  `userID` int(11) NOT NULL,
  `userName` varchar(40) COLLATE utf8mb4_german2_ci NOT NULL,
  `userPW` varchar(40) COLLATE utf8mb4_german2_ci NOT NULL,
  `userIP` varchar(15) COLLATE utf8mb4_german2_ci NOT NULL,
  `userActive` tinyint(1) NOT NULL,
  `userPermission` varchar(10) COLLATE utf8mb4_german2_ci NOT NULL,
  `userCookie` varchar(40) COLLATE utf8mb4_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `ruser`
--

INSERT INTO `ruser` (`userID`, `userName`, `userPW`, `userIP`, `userActive`, `userPermission`, `userCookie`) VALUES
(1, 'b32cb8284e95d0b29dc79c649a10d20c', 'b32cb8284e95d0b29dc79c649a10d20c', '', 1, '1', '060e7e6c5b06280165871226dc406e03');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `rclient`
--
ALTER TABLE `rclient`
  ADD PRIMARY KEY (`clientID`);

--
-- Indizes für die Tabelle `rnoshow`
--
ALTER TABLE `rnoshow`
  ADD PRIMARY KEY (`nsID`),
  ADD UNIQUE KEY `nsMail` (`nsMail`) USING BTREE;

--
-- Indizes für die Tabelle `rreserve`
--
ALTER TABLE `rreserve`
  ADD PRIMARY KEY (`reserveID`);

--
-- Indizes für die Tabelle `rtable`
--
ALTER TABLE `rtable`
  ADD PRIMARY KEY (`tableID`);

--
-- Indizes für die Tabelle `ruser`
--
ALTER TABLE `ruser`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `rnoshow`
--
ALTER TABLE `rnoshow`
  MODIFY `nsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT für Tabelle `rreserve`
--
ALTER TABLE `rreserve`
  MODIFY `reserveID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT für Tabelle `ruser`
--
ALTER TABLE `ruser`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
