-- phpMyAdmin SQL Dump
-- version 4.6.6deb4+deb9u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 16. Okt 2020 um 16:13
-- Server-Version: 10.1.45-MariaDB-0+deb9u1
-- PHP-Version: 7.0.33-0+deb9u10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `web528_kochbuch`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tab_benutzer`
--

CREATE TABLE `tab_benutzer` (
                                `pk` int(11) NOT NULL,
                                `loginname` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `password_hash` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `realname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `vkey` text COLLATE utf8mb4_unicode_ci,
                                `verified` int(11) NOT NULL DEFAULT '0',
                                `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                `token` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                `new_password` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indizes für die Tabelle `tab_benutzer`
--
ALTER TABLE `tab_benutzer`
    ADD PRIMARY KEY (`pk`);

--
-- AUTO_INCREMENT für Tabelle `tab_benutzer`
--
ALTER TABLE `tab_benutzer`
    MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
