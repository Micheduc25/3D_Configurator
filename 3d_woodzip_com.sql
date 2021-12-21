-- phpMyAdmin SQL Dump
-- version 4.6.6deb4+deb9u2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 18, 2021 at 01:55 PM
-- Server version: 10.1.48-MariaDB-0+deb9u1
-- PHP Version: 7.2.34-18+0~20210223.60+debian9~1.gbpb21322

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `3d_woodzip_com`
--

-- --------------------------------------------------------

--
-- Table structure for table `devis`
--

CREATE TABLE `devis` (
  `id` int(11) NOT NULL,
  `prenom` varchar(255) COLLATE utf8_croatian_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8_croatian_ci NOT NULL,
  `addresse_elec` varchar(255) COLLATE utf8_croatian_ci NOT NULL,
  `addresse` varchar(255) COLLATE utf8_croatian_ci NOT NULL,
  `telephone` varchar(100) COLLATE utf8_croatian_ci NOT NULL,
  `code_postal` varchar(150) COLLATE utf8_croatian_ci NOT NULL,
  `pays` varchar(100) COLLATE utf8_croatian_ci NOT NULL,
  `question` text COLLATE utf8_croatian_ci,
  `bardages` varchar(100) COLLATE utf8_croatian_ci NOT NULL,
  `enduits` varchar(100) COLLATE utf8_croatian_ci NOT NULL,
  `garage` int(11) NOT NULL,
  `terrasse` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_croatian_ci;

--
-- Dumping data for table `devis`
--

INSERT INTO `devis` (`id`, `prenom`, `nom`, `addresse_elec`, `addresse`, `telephone`, `code_postal`, `pays`, `question`, `bardages`, `enduits`, `garage`, `terrasse`) VALUES
(33, 'Ndjock', 'Junior', '333 Freemont Street', 'yaounde, soa , maison1', '+237681757514', '94105', 'france', 'Je vais bien et vous???', 'Bois Naturel', 'Blanc', 1, 1),
(34, 'lionel', 'chouraqui', 'lionel.chouraqui@gmail.com', 'yure teui dssd', '0656565645', '95600', 'france', 'PARIS\r\n\r\nJE TROUVE LE SITE TRES BEAU merci', 'Bois Naturel', 'Gris foncé', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `maisons`
--

CREATE TABLE `maisons` (
  `id` varchar(100) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `base_price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `maisons`
--

INSERT INTO `maisons` (`id`, `nom`, `base_price`) VALUES
('348778fc87b54be6b83ccd311b63fa3d', 'maison1', 0),
('b4b81d1b1d1c46058b0d96980f9718b8', 'maison2', 0);

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `maison_id` varchar(100) NOT NULL,
  `prix` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `nom`, `maison_id`, `prix`) VALUES
(1, 'woodzip1_Terrasse', '348778fc87b54be6b83ccd311b63fa3d', 0),
(2, 'woodzip1_Garage', '348778fc87b54be6b83ccd311b63fa3d', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `devis`
--
ALTER TABLE `devis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maisons`
--
ALTER TABLE `maisons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `devis`
--
ALTER TABLE `devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
