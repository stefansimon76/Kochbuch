-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 15. Jul 2021 um 12:45
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
-- Tabellenstruktur für Tabelle `tab_benutzer`
--

CREATE TABLE `tab_benutzer` (
                                `pk` bigint(11) UNSIGNED NOT NULL,
                                `loginname` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `password_hash` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `realname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `vkey` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                `verified` int(11) NOT NULL DEFAULT 0,
                                `created` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                `last_login` datetime NOT NULL DEFAULT current_timestamp(),
                                `token` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                `new_password` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tab_benutzer`
--
ALTER TABLE `tab_benutzer`
    ADD PRIMARY KEY (`pk`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tab_benutzer`
--
ALTER TABLE `tab_benutzer`
    MODIFY `pk` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
