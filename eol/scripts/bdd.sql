

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `eolatlas`
--

-- --------------------------------------------------------

--
-- Structure de la table `infosWeibull`
--

CREATE TABLE `infosWeibull` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idStation` int(11) NOT NULL,
  `vitesse` float NOT NULL,
  `weibull` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `station`
--

CREATE TABLE `station` (
  `idStation` int(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `facteurForme` float NOT NULL,
  `facteurEchelle` float NOT NULL,
  `altitude` float NOT NULL,
  PRIMARY KEY (`idStation`),
  UNIQUE KEY `idStation` (`idStation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vent`
--

CREATE TABLE `vent` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `idStation` int(255) NOT NULL,
  `date` date NOT NULL,
  `vitesse` float NOT NULL,
  `direction` float NOT NULL COMMENT 'en degré',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
