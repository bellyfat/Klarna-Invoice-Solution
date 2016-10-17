SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `postal` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `user` int(11) DEFAULT NULL,
  `orderid` int(11) DEFAULT NULL,
  `browser` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reservation` varchar(255) NOT NULL,
  `invoice` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Shipped','Cancelled','Reserved') NOT NULL DEFAULT 'Reserved',
  `billing` int(11) NOT NULL,
  `shipping` int(11) NOT NULL,
  `storeid` int(11) NOT NULL,
  `testmode` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `storeid` (`storeid`),
  KEY `billing` (`billing`),
  KEY `shipping` (`shipping`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `orderitem`;
CREATE TABLE IF NOT EXISTS `orderitem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `artno` varchar(255) NOT NULL,
  `exvat` decimal(11,2) NOT NULL,
  `incvat` decimal(11,2) NOT NULL,
  `vat` decimal(4,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orderid` (`orderid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `store`;
CREATE TABLE IF NOT EXISTS `store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `eid` int(11) NOT NULL,
  `shared` varchar(255) NOT NULL,
  `testmode` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `passwordhash` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `user_stores`;
CREATE TABLE IF NOT EXISTS `user_stores` (
  `userid` int(11) NOT NULL,
  `storeid` int(11) NOT NULL,
  KEY `userid` (`userid`),
  KEY `storeid` (`storeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `order`
  ADD CONSTRAINT `billing` FOREIGN KEY (`billing`) REFERENCES `address` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shipping` FOREIGN KEY (`shipping`) REFERENCES `address` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `store_order` FOREIGN KEY (`storeid`) REFERENCES `store` (`id`);

ALTER TABLE `orderitem`
  ADD CONSTRAINT `orderitem_order` FOREIGN KEY (`orderid`) REFERENCES `order` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_stores`
  ADD CONSTRAINT `store_exists` FOREIGN KEY (`storeid`) REFERENCES `store` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_exists` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
