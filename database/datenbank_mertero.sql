-- phpMyAdmin SQL Dump
-- version 4.6.6deb4+deb9u2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 25. Mai 2021 um 15:18
-- Server-Version: 10.1.47-MariaDB-0+deb9u1
-- PHP-Version: 7.2.34-8+0~20201103.52+debian9~1.gbpafa084

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Tabellenstruktur für Tabelle `rblacklist`
--

CREATE TABLE `rblacklist` (
  `bID` int(11) NOT NULL,
  `bMail` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `bTNR` varchar(25) COLLATE utf8mb4_german2_ci NOT NULL,
  `bAnzahl` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rblacklist`
--

INSERT INTO `rblacklist` (`bID`, `bMail`, `bTNR`, `bAnzahl`) VALUES
(1, 'karl.ralf@web.de', 'Telefon', 2),
(2, 'admin@mertero.de', '049159', 1),
(4, 'mail@domain.de', '0', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rclient`
--

CREATE TABLE `rclient` (
  `clientID` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `reserveID` int(11) NOT NULL,
  `clientVorname` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `clientName` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `clientMail` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `clientTNR` varchar(25) COLLATE utf8mb4_german2_ci NOT NULL,
  `clientDate` datetime NOT NULL,
  `clientConfirm` varchar(40) COLLATE utf8mb4_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rclient`
--

INSERT INTO `rclient` (`clientID`, `reserveID`, `clientVorname`, `clientName`, `clientMail`, `clientTNR`, `clientDate`, `clientConfirm`) VALUES
('60a3abe198ee3', 116, 'Vorname', 'Nachname', 'E-Mail', 'Telefon', '2021-05-18 00:00:00', '60a3abe19a047'),
('60a3b0e034ceb', 117, 'Vorname', 'Nachname', 'E-Mail', 'Telefon', '2021-05-18 00:00:00', '60a3b0e0366cb'),
('60a3b0e647d01', 118, 'test', 'test1', 'karl.ralf@web.de', '8932z4872364', '2021-05-18 00:00:00', '60a3b0e64904b'),
('60a3b13d45582', 119, 'Vorname', 'Nachname', 'E-Mail', 'Telefon', '2021-05-18 00:00:00', '60a3b13d467e1'),
('60a3b14071e44', 120, 'Vorname', 'Nachname', 'E-Mail', 'Telefon', '2021-05-18 00:00:00', '60a3b14073306'),
('60a3b1ddc10b9', 121, 'Vorname', 'Nachname', 'E-Mail', 'Telefon', '2021-05-18 00:00:00', '60a3b1ddc23d6'),
('60a3b2037e203', 122, 'Vorname', 'Nachname', 'E-Mail', 'Telefon', '2021-05-18 00:00:00', '60a3b2037f551'),
('60a3b22b6f708', 113, 'adwad', 'Lowicki', 'flowicki@web.de', '', '2021-05-18 00:00:00', '60a3b2edb202f'),
('60a4b9b26cd28', 123, 'adwadaw', 'rewrewrwr', 'awdwawafea', '2423424324', '2021-05-19 09:09:38', '60a4b9b26f6f360a4b9b26f6f5'),
('60a4bb8f408a8', 124, 'David', 'Lowicki', 'test@web.de', '98264983', '2021-05-19 09:17:35', '60a4bb8f4b48060a4bb8f4b483'),
('60a51c2fc3552', 125, 'wadwa', 'wadawda', 'wdaadwada', 'wadadwadad', '2021-05-19 16:09:51', '60a51c2fc64d460a51c2fc64d7'),
('60a51d5852b9a', 126, 'wadawd', 'wadawd', 'wadawdad', 'awdawdadw', '2021-05-19 16:14:48', '60a51d58562f060a51d58562f4'),
('60a51e2debe8a', 127, 'wadad', 'awdadw', 'adwadad', '', '2021-05-19 16:18:21', '60a51e2def2aa60a51e2def2af'),
('60a61f13913c6', 129, 'wada', 'dwadawd', 'adadawd', 'adwadwada', '2021-05-20 10:34:27', '60a61f1399ea760a61f1399ea9'),
('60a61f414ed4d', 130, 'wada', 'dwadawd', 'adadawd', 'adwadwada', '2021-05-20 10:35:13', '60a61f415a0cd60a61f415a0d0'),
('60a61f7b89f01', 131, 'wwwww', 'wwwwww', 'wwwwww', 'wwwwwwww', '2021-05-20 10:36:11', '60a61f7b932b660a61f7b932b9'),
('60a61f9556dc1', 132, 'aaaa', 'aaaa', 'aaaaaa', 'aaaaaaa', '2021-05-20 10:36:37', '60a61f955fca260a61f955fca4'),
('60a62053e2abe', 133, 'rrrr', 'rrrr', 'rrrrr', 'rrrrrr', '2021-05-20 10:39:47', '60a62053f1c1c60a62053f1c1f'),
('60a6205409a01', 133, 'tttt', 'tttttt', 'tttttt', 'tttttttt', '2021-05-20 10:39:47', '60a6205409a0d60a6205409a0e'),
('60a621a690cea', 134, 'David', 'Lowicki', 'lowicki@gmx.de', '9837249234', '2021-05-20 10:45:26', '60a621a6991bd60a621a6991bf'),
('60a623967bf6d', 135, 'w', 'w', 'w', 'w', '2021-05-20 10:53:42', '60a623968377060a6239683772'),
('60a62bade92d3', 136, 'david', 'Lowicki', 'flowicki@web.de', 'adwadwadwad', '2021-05-20 11:28:13', '60a62bae0066f60a62bae00672'),
('60a62be6d40ec', 137, 'dawdwa', 'David Lowicki', 'dlowicki@ibs-ka.de', 'awdawdawd', '2021-05-20 11:29:10', '60a62be6df53360a62be6df536'),
('60a62c6f2eb26', 138, 'awda', 'wad', 'dlowicki@ibs-ka.de', '072195071338', '2021-05-20 11:31:27', '60a62c6f3dd0660a62c6f3dd08'),
('60a62c89a042e', 139, 'awdawad', 'David Lowicki', 'dlowicki@ibs-ka.de', 'wadawdawdad', '2021-05-20 11:31:53', '60a62c89aee2a60a62c89aee2c'),
('60a62ebf727f4', 140, 'wadwad', 'David', 'dlowicki@ibs-ka.de', 'wadawdawd', '2021-05-20 11:41:19', '60a62ebf813dd60a62ebf813e0'),
('60a62ede6b86e', 141, 'awdawd', 'David Lowicki', 'dlowicki@ibs-ka.de', 'awdadwawdadwa', '2021-05-20 11:41:50', '60a62ede7542f60a62ede75432'),
('60a6307df2a14', 142, 'David Lowicki Zwei', 'David Lowicki', 'dlowicki@ibs-ka.de', '072195071338', '2021-05-20 11:48:46', '60a6307e078bf60a6307e078c2'),
('60a63096475c5', 143, 'wasd', 'David Lowicki', 'dlowicki@ibs-ka.de', 'wadawdawdad', '2021-05-20 11:49:10', '60a630964f04f60a630964f052'),
('60a630e254428', 144, 'dawd', 'David Lowicki', 'dlowicki@ibs-ka.de', 'awdawdawda', '2021-05-20 11:50:26', '60a630e26167960a630e26167c'),
('60a64a48a766b', 145, 'wasd', 'David Lowicki', 'dlowicki@ibs-ka.de', 'wadwwad', '2021-05-20 13:38:48', '60a64a48b106860a64a48b106b'),
('60a6523662b4d', 146, 'wadwa', 'David Lowicki', 'dlowicki@ibs-ka.de', '072195071338', '2021-05-20 14:12:38', '60a652366a37e60a652366a381'),
('60a6568882976', 147, 'wdawa', 'wdadwawd', 'wadwadadwad', 'wasd', '2021-05-20 14:31:04', '60a656888b4c060a656888b4c3'),
('60a656e129e54', 148, 'wad2', 'wasd2', 'wasd2', 'wasd2', '2021-05-20 14:32:33', '60a656e13512760a656e13512a'),
('60a6866c86b54', 149, 'lllll', 'David Lowicki', 'lowicki.david@gmail.com', '049159066401', '2021-05-20 17:55:24', '60a6866c8a7bc60a6866c8a7bd'),
('60a69c8c915c9', 151, 'Karl', 'Ralf', 'karl.ralf@web.de', '8932z4872364', '2021-05-20 00:00:00', '60a69c8c91a4b'),
('60a6b5b5cbf28', 151, 'Lieber', 'Mark', 'mark.lieber@web.de', '432343242324', '2021-05-20 00:00:00', '60a6b5c3e2b4b'),
('60a93caaf3332', 152, 'wadawd', 'awdawdwa', 'wadawda', 'awdawdawd', '2021-05-22 19:17:30', '60a93caaf3d7060a93caaf3d71'),
('60a93cdc5246f', 153, 'grgdrg', 'drgdr', 'drgdrgd', 'rdgrdgrdgr', '2021-05-22 19:18:20', '60a93cdc52c2060a93cdc52c21'),
('60a93d1a19502', 154, 'David', 'Lowicki', 'lowicki@gmx.de', '3242342342', '2021-05-22 19:19:22', '60a93d1a1a62f60a93d1a1a630'),
('60aba5666c146', 162, 'awdawd', 'awdawdad', 'awdadwwadwa', 'dwadwadawdawd', '2021-05-24 15:08:54', '60aba5666d18260aba5666d183'),
('60abaa740fb2c', 163, 'wadw', 'awdawdaw', 'admin@mertero.de', '049159066401', '2021-05-24 15:30:28', '60abaa7410d9a60abaa7410d9b'),
('60abb4b0a202c', 164, 'Klaus', 'Karl', 'admin@mertero.de', '2e342432', '2021-05-24 16:14:08', '60abb4b0a40b760abb4b0a40b8'),
('60abb5c593324', 165, 'Uli', 'Hönes', 'admin@mertero.de', '049159066401', '2021-05-24 16:18:45', '60abb5c59449160abb5c594492'),
('60abb796d0cab', 166, 'Mark', 'Mustermann', 'lowicki.david@gmail.com', '6432784284', '2021-05-24 16:26:30', '60abb796d494260abb796d4944'),
('60ac10e71cfe6', 167, 'Luca', 'Yavsan', 'yuumi16@yahoo.com', '342234234', '2021-05-24 22:47:35', '60ac10e71e5e160ac10e71e5e2'),
('60ac187965a5c', 168, 'Vorname', 'Nachname', 'E-Mail', 'Telefon', '2021-05-24 00:00:00', '60ac187966c03'),
('60ac323f9f0a5', 169, 'Vorname', 'Nachname', 'E-Mail', 'Telefon', '2021-05-25 00:00:00', '60ac323f9fab3'),
('60ac330f8aed7', 170, 'Vorname', 'Nachname', 'E-Mail', 'Telefon', '2021-05-25 00:00:00', '60ac330f8ba4c');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rDays`
--

CREATE TABLE `rDays` (
  `daysID` int(11) NOT NULL,
  `daysDay` varchar(15) COLLATE utf8mb4_german2_ci NOT NULL,
  `daysWeekday` int(11) NOT NULL,
  `daysTime` varchar(25) COLLATE utf8mb4_german2_ci NOT NULL,
  `daysActive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rDays`
--

INSERT INTO `rDays` (`daysID`, `daysDay`, `daysWeekday`, `daysTime`, `daysActive`) VALUES
(1, 'Montag', 1, '15:00:00-22:00:00', 1),
(2, 'Dienstag', 2, '15:00:00-22:00:00', 1),
(3, 'Mittwoch', 3, '15:00:00-22:00:00', 1),
(4, 'Donnerstag', 4, '15:00:00-22:00:00', 1),
(5, 'Freitag', 5, '15:00:00-22:00:00', 1),
(6, 'Samstag', 6, '15:00:00-22:00:00', 1),
(7, 'Sonntag', 7, '15:00:00-22:00:00', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rnoshow`
--

CREATE TABLE `rnoshow` (
  `nsID` int(11) NOT NULL,
  `nsMail` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `nsTNR` varchar(25) COLLATE utf8mb4_german2_ci NOT NULL,
  `nsAmount` int(11) NOT NULL,
  `nsDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rnoshow`
--

INSERT INTO `rnoshow` (`nsID`, `nsMail`, `nsTNR`, `nsAmount`, `nsDate`) VALUES
(4, 'flowicki@web.de', '0', 2, '2020-10-19'),
(14, 'test@web.de', '0', 1, '2020-11-17'),
(17, 'test@domain.de', '464894869', 1, '2020-11-17'),
(21, 'wasd@domain.de', '0', 3, '2020-11-17'),
(29, 'test@etst.de', '0', 1, '2020-11-25'),
(31, 'hnn@web.de', 'null', 2, '2020-12-01');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rreserve`
--

CREATE TABLE `rreserve` (
  `reserveID` int(11) NOT NULL,
  `tableID` int(11) NOT NULL,
  `clientID` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `reserveDate` date NOT NULL,
  `reserveTime` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `reserveBlock` varchar(11) COLLATE utf8mb4_german2_ci NOT NULL,
  `reserveAmount` int(11) NOT NULL,
  `reserveCookie` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `reserveState` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rreserve`
--

INSERT INTO `rreserve` (`reserveID`, `tableID`, `clientID`, `reserveDate`, `reserveTime`, `reserveBlock`, `reserveAmount`, `reserveCookie`, `reserveState`) VALUES
(164, 54, '60abb4b0a202c', '2021-05-25', '17:00:00 - 19:30:00', '1', 2, '94549eec5051e239480ae07efccbc90d', 0),
(165, 44, '60abb5c593324', '2021-05-25', '17:00:00 - 19:30:00', '1', 4, '2f0f78a40d024db5605603f53a6f67b2', 5),
(166, 52, '60abb796d0cab', '2021-05-25', '17:00:00 - 19:30:00', '1', 2, '30f8c85971b224bdaea244a422230c0a', 0),
(167, 46, '60ac10e71cfe6', '2021-05-27', '17:00:00 - 19:30:00', '1', 3, '613613f40fe0e28d16e7b20c519e31b6', 0),
(168, 45, '60ac187965a5c', '2021-05-24', '17:00:00 - 19:30:00', '1', 5, '60ac187965bd5', 0),
(169, 45, '60ac323f9f0a5', '2021-05-25', '17:00:00 - 19:30:00', '1', 0, '60ac323f9f1c2', 3),
(170, 41, '60ac330f8aed7', '2021-05-24', '17:00:00 - 19:30:00', '1', 5, '60ac330f8b007', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rSettings`
--

CREATE TABLE `rSettings` (
  `sID` int(11) NOT NULL,
  `sEinstellung` varchar(35) COLLATE utf8mb4_german2_ci NOT NULL,
  `sWert` varchar(150) COLLATE utf8mb4_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rSettings`
--

INSERT INTO `rSettings` (`sID`, `sEinstellung`, `sWert`) VALUES
(1, 'tischplan', 'https://www.mertero.de/html/Reservierung/tischplan.jpg');

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
  `tablePlace` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `tableWidth` int(11) NOT NULL,
  `tableHeight` int(11) NOT NULL,
  `tableX` int(11) NOT NULL,
  `tableY` int(11) NOT NULL,
  `tableActive` varchar(15) COLLATE utf8mb4_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rtable`
--

INSERT INTO `rtable` (`tableID`, `tableType`, `tableMax`, `tableMin`, `tableCode`, `tablePlace`, `tableWidth`, `tableHeight`, `tableX`, `tableY`, `tableActive`) VALUES
(11, '', 10, 4, 'tKPjLVITWk2iYiFm9S5BkQ', 'alm', 150, 32, 1638, 374, 'open'),
(12, '', 10, 4, 'k876dB47fUOC2HsB6XMzqQ', 'alm', 150, 32, 1638, 284, 'open'),
(13, '', 10, 4, 'gN3M0CTnm0eOR6nnx5dspw', 'alm', 150, 32, 1637, 106, 'open'),
(14, '', 10, 4, '9wKVG6zjkkWsUqCcMPDAUg', 'alm', 150, 32, 1637, 16, 'open'),
(15, '', 10, 4, '7qMzupQws0yfiHsQaNyvEA', 'alm', 150, 32, 1430, 16, 'open'),
(16, '', 10, 4, 'Q9MA8CfFVUuTagANYJpBlg', 'alm', 150, 32, 1430, 106, 'open'),
(17, '', 10, 4, 'JMeu8qzzD0HYoqcAnJQOxA', 'alm', 150, 32, 1430, 257, 'open'),
(18, '', 10, 4, 'XVW3XZvJTkCIpABbILms6w', 'alm', 35, 138, 1444, 410, 'open'),
(21, '', 2, 1, 'dVR3TrZ8NkKJRvwc7anVVA', 'Terrasse', 35, 33, 1254, 625, 'open'),
(22, '', 2, 1, 'zM8PFc083UCZ599yiM6XtA', 'Terrasse', 35, 33, 1133, 625, 'open'),
(23, '', 2, 1, 'yNbCvCcUl0qD9AcxySp6mA', 'Terrasse', 35, 33, 1010, 625, 'open'),
(24, '', 7, 4, 'qlTD0x5t9UySvl7OJC6CsA', 'Terrasse', 34, 98, 854, 682, 'open'),
(25, '', 7, 4, 'f5xy6jhTzkmhUEuQzVg1Tw', 'Terrasse', 34, 98, 974, 682, 'open'),
(26, '', 7, 4, 'XUdfQTfuEE2NUxBhEWovtg', 'Terrasse', 36, 98, 1092, 682, 'open'),
(27, '', 4, 4, 'xbkQ44b2d0qNAbrnVeT5HA', 'Terrasse', 37, 98, 1211, 682, 'open'),
(31, 'lounge', 8, 5, 'Kfw2nVFMlkWZhJ2MeyaUCw', 'Feuerstelle', 100, 180, 1310, 380, 'open'),
(32, 'lounge', 5, 3, 'P68Sx9O9ZUu85tmYBn6w6Q', 'Feuerstelle', 100, 90, 1310, 220, 'open'),
(33, 'lounge', 8, 5, 'b6EsC24FskeVaKF6Nwm6PA', 'Feuerstelle', 200, 90, 1212, 48, 'open'),
(34, '', 10, 4, 'zpJ09e69sECQodzWlnGacQ', 'Feuerstelle', 160, 35, 982, 188, 'open'),
(35, '', 14, 6, '9Q8MbJT300a4u51lI1HCWg', 'Feuerstelle', 197, 36, 982, 372, 'open'),
(36, '', 14, 6, 'GOwXG6bYrUyefLddYt3uhg', 'Feuerstelle', 197, 36, 982, 490, 'open'),
(41, 'lounge', 8, 5, '9MBPz9kHvUiurUfC1fIeqg', 'Loungebereich', 197, 91, 615, 5, 'open'),
(42, 'lounge', 5, 3, 'Aifs0HuKHEyYP7YiUc6Msw', 'Loungebereich', 100, 90, 294, 5, 'open'),
(43, 'lounge', 5, 3, 'Rju8PkpzS0SNgWubWXBPXg', 'Loungebereich', 100, 90, 100, 5, 'open'),
(44, 'lounge', 6, 4, '3kpOoMtdxU2Vfk35ioHU0Q', 'Loungebereich', 97, 90, 103, 178, 'open'),
(45, '', 4, 2, 'QcuoWSAEwk6XqEQEKQFchQ', 'Loungebereich', 80, 36, 305, 204, 'open'),
(46, '', 4, 2, '9QwoL48I1UK9VbtEXFc1og', 'Loungebereich', 100, 90, 488, 175, 'open'),
(47, '', 4, 2, 'cVb6gpSnx0WT5fn2gRbfIg', 'Loungebereich', 80, 36, 693, 203, 'open'),
(51, '', 5, 2, 'YfYdqyCrH02p5AqCTSq6MQ', 'Markise', 40, 73, 728, 338, 'open'),
(52, '', 5, 2, 'jecmM93s3EOjQmVe0aTXZg', 'Markise', 40, 73, 555, 338, 'open'),
(53, '', 5, 2, 'xBccO5XaMkWntuS34hOCTw', 'Markise', 40, 73, 385, 338, 'open'),
(54, '', 5, 2, 'lQXh61LY70aWeeS6G0DHfA', 'Markise', 40, 73, 213, 338, 'open'),
(55, '', 2, 1, 'Svu97xQrrkOMllNdQFeBcg', 'Markise', 38, 38, 215, 474, 'open'),
(56, '', 2, 1, 'lyQTRwARyUqNCcMIDerMlA', 'Markise', 40, 38, 385, 474, 'open'),
(57, '', 5, 2, '8Zp8QKBzn0mvkHPsIAEaSA', 'Markise', 40, 72, 555, 485, 'open'),
(58, '', 5, 2, '8Zp8QKBzh0mvkHPsIAEaSA', 'Markise', 40, 75, 727, 483, 'open'),
(61, 'hochtisch', 4, 2, '8Zp8QK2zn0mvkHPsIAEaSA', '', 40, 38, 855, 490, 'open'),
(62, 'hochtisch', 4, 2, '8Zp8QK2zn0mvkHPsIAEaS1', '', 40, 38, 855, 387, 'open'),
(63, 'hochtisch', 4, 2, '8Zp8QK2zn0mvkHPsIAEaSE', 'Loungebereich', 40, 35, 854, 204, 'open'),
(71, '', 3, 2, '1Zp8QK2zn0mvkHPsIAEaSE', 'Restaurant', 37, 55, 836, 785, 'open'),
(72, '', 2, 1, 'RZp8QK2zn0mvkHPsIAEaSE', 'Restaurant', 37, 55, 938, 785, 'open'),
(73, '', 2, 1, 'RZp8QK2zn0mvkHPsIAEaTR', 'Restaurant', 37, 55, 1040, 785, 'open'),
(74, '', 3, 2, 'RZp8QK2zn0mvkHPsIAEaa2', 'Restaurant', 38, 55, 1140, 785, 'open'),
(75, '', 8, 4, 'RZp8QK2zn0mveWPsIAEaa2', 'Restaurant', 177, 34, 925, 870, 'open'),
(76, '', 4, 2, 'RZp8QK2zn0mveWPsIAEaaq', 'Restaurant', 60, 32, 1120, 951, 'open'),
(77, '', 4, 2, 'RZp8QK2zn0mveWPsIAEaaG', 'Restaurant', 60, 32, 1120, 1031, 'open'),
(78, '', 2, 1, 'RZp8QK2zn0mveWPsIAEavA', 'Restaurant', 34, 32, 1035, 1031, 'open'),
(79, '', 2, 1, 'RZp8QK2bH0mveWPsIAEavA', 'Restaurant', 34, 32, 1035, 951, 'open'),
(81, '', 3, 2, 'RZp8fK2bH0mveWPsIAEav3', 'Restaurant', 34, 55, 483, 785, 'open'),
(82, '', 3, 2, 'RZp8fK2bH0mveWPsIAEavt', 'Restaurant', 34, 55, 585, 785, 'open'),
(83, '', 3, 2, 'RZp8fK2bH0mveWPsIAEav6', 'Restaurant', 37, 55, 685, 785, 'open'),
(84, '', 2, 1, 'RZp8fK2bH0mveWPsIAEavm', 'Restaurant', 36, 32, 637, 870, 'open'),
(85, '', 2, 1, 'AZp8fK2bH0mveWPsIAEav2', 'Restaurant', 36, 32, 530, 870, 'open'),
(98, 'lounge', 8, 4, 'yJGSumzsqEGLainM1jCRJw', 'Eingang', 100, 180, 1775, 658, 'open'),
(99, 'lounge', 10, 6, 'rRNLXwxp9Ea8c4WOkl5p9w', 'Eingang', 100, 180, 1775, 895, 'open');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rtime`
--

CREATE TABLE `rtime` (
  `timeID` int(11) NOT NULL,
  `timeStart` time NOT NULL,
  `timeEnd` time NOT NULL,
  `timeActive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

--
-- Daten für Tabelle `rtime`
--

INSERT INTO `rtime` (`timeID`, `timeStart`, `timeEnd`, `timeActive`) VALUES
(1, '17:00:00', '19:30:00', 1),
(2, '19:30:00', '22:00:00', 1);

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
(1, 'b32cb8284e95d0b29dc79c649a10d20c', 'b32cb8284e95d0b29dc79c649a10d20c', '', 1, '1', '78033d32b9a68be9208eb479a034dfcc');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `rblacklist`
--
ALTER TABLE `rblacklist`
  ADD PRIMARY KEY (`bID`),
  ADD UNIQUE KEY `bMail` (`bMail`),
  ADD UNIQUE KEY `bTNR` (`bTNR`);

--
-- Indizes für die Tabelle `rclient`
--
ALTER TABLE `rclient`
  ADD PRIMARY KEY (`clientID`);

--
-- Indizes für die Tabelle `rDays`
--
ALTER TABLE `rDays`
  ADD PRIMARY KEY (`daysID`);

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
-- Indizes für die Tabelle `rSettings`
--
ALTER TABLE `rSettings`
  ADD PRIMARY KEY (`sID`);

--
-- Indizes für die Tabelle `rtable`
--
ALTER TABLE `rtable`
  ADD PRIMARY KEY (`tableID`);

--
-- Indizes für die Tabelle `rtime`
--
ALTER TABLE `rtime`
  ADD PRIMARY KEY (`timeID`);

--
-- Indizes für die Tabelle `ruser`
--
ALTER TABLE `ruser`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `rblacklist`
--
ALTER TABLE `rblacklist`
  MODIFY `bID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT für Tabelle `rDays`
--
ALTER TABLE `rDays`
  MODIFY `daysID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT für Tabelle `rnoshow`
--
ALTER TABLE `rnoshow`
  MODIFY `nsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT für Tabelle `rreserve`
--
ALTER TABLE `rreserve`
  MODIFY `reserveID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;
--
-- AUTO_INCREMENT für Tabelle `rSettings`
--
ALTER TABLE `rSettings`
  MODIFY `sID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `rtime`
--
ALTER TABLE `rtime`
  MODIFY `timeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT für Tabelle `ruser`
--
ALTER TABLE `ruser`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
