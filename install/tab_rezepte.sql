-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 01. Jul 2021 um 14:59
-- Server-Version: 10.3.28-MariaDB
-- PHP-Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `kochbuch`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tab_rezepte`
--

CREATE TABLE `tab_rezepte` (
  `pk` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `createdz` datetime NOT NULL DEFAULT current_timestamp(),
  `fs_benutzer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tab_rezepte`
--
ALTER TABLE `tab_rezepte`
  ADD PRIMARY KEY (`pk`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tab_rezepte`
--
ALTER TABLE `tab_rezepte`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
