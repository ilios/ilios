-- phpMyAdmin SQL Dump
-- version 3.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 18, 2011 at 03:38 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `schedulertest`
--

-- --------------------------------------------------------

--
-- Table structure for table `change_time`
--
DROP TABLE IF EXISTS `change_time`;
CREATE TABLE IF NOT EXISTS `change_time` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=347 ;

--
-- Dumping data for table `change_time`
--

INSERT INTO `change_time` (`event_id`, `event_name`, `start_date`, `end_date`, `details`) VALUES
(286, 'Midnight', '2010-03-01 00:00:00', '2010-03-03 00:00:00', ''),
(287, '1 hour', '2010-03-03 23:45:00', '2010-03-04 00:45:00', ''),
(288, '24 hours', '2010-03-05 23:45:00', '2010-03-06 23:45:00', ''),
(289, 'Winter-Summer', '2010-03-27 23:45:00', '2010-03-28 23:45:00', ''),
(290, 'Magic Midnight', '2010-03-27 00:00:00', '2010-03-29 00:00:00', ''),
(291, 'Lunch time', '2010-03-09 12:00:00', '2010-03-09 14:00:00', ''),
(297, 'Summer Midnight', '2010-03-30 00:00:00', '2010-04-01 00:00:00', ''),
(296, '1 hour Summer', '2010-03-31 23:45:00', '2010-04-01 00:45:00', ''),
(299, '24 hours Summer', '2010-04-02 23:45:00', '2010-04-03 23:45:00', ''),
(311, 'Midnight', '2009-10-01 00:00:00', '2009-10-03 00:00:00', ''),
(314, 'Midnight Winter', '2009-10-29 00:00:00', '2009-10-31 00:00:00', ''),
(315, '24 hours', '2009-10-05 23:45:00', '2009-10-06 23:45:00', ''),
(316, '24 hours Winter', '2009-10-27 23:45:00', '2009-10-28 23:45:00', ''),
(317, 'Magic Midnight', '2009-10-24 00:00:00', '2009-10-26 00:00:00', ''),
(318, 'Summer-Winter', '2009-10-24 23:45:00', '2009-10-25 23:45:00', ''),
(319, '1 hour', '2009-10-07 23:45:00', '2009-10-08 00:45:00', ''),
(320, '1 hour Winter', '2009-10-28 23:45:00', '2009-10-29 00:45:00', ''),
(321, 'Dinner', '2009-10-13 20:00:00', '2009-10-13 22:00:00', '');


-- --------------------------------------------------------

--
-- Table structure for table `events`
--
DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1261152023 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `start_date`, `end_date`, `details`) VALUES
(2, 'French Open', '2009-05-24 00:00:00', '2009-06-08 00:00:00', 'Details for French Open'),
(3, 'Aegon Championship', '2009-06-10 00:00:00', '2009-06-13 00:00:00', 'Details for Aegon Championship'),
(4, 'Wimbledon', '2009-06-21 00:00:00', '2009-07-05 00:00:00', 'Details for Wimbledon'),
(5, 'Indianapolis Tennis Championships', '2009-07-18 00:00:00', '2009-07-27 00:00:00', 'Details for Indianapolis Tennis Championships'),
(8, 'Countrywide Classic Tennis', '2009-07-27 00:00:00', '2009-08-02 00:00:00', 'Details for Countrywide Classic Tennis'),
(7, 'ATP Master Tennis', '2009-05-11 00:00:00', '2009-05-18 00:00:00', 'Details for ATP Master Tennis'),
(9, 'Legg Mason Tennis Classic', '2009-08-01 00:00:00', '2009-08-11 00:00:00', 'Details for Legg Mason Tennis Classic'),
(12, 'US Open Tennis Championship', '2009-08-29 00:00:00', '2009-09-14 00:00:00', 'Details for US Open Tennis Championship'),
(13, 'Barclays ATP World Tour Finals', '2009-11-22 00:00:00', '2009-11-28 00:00:00', 'Details for Barclays ATP World Tour Finals'),
(14, 'Western & Southern Financial Group Masters Tennis', '2009-08-17 00:00:00', '2009-08-24 00:00:00', 'Details for Western & Southern Financial Group Masters Tennis'),
(15, ' Parc Izvor ', '2009-05-16 15:00:00', '2009-05-16 18:00:00', 'Details for  Parc Izvor '),
(16, ' Arena Zagreb ', '2009-05-21 14:00:00', '2009-05-21 17:00:00', 'Details for  Arena Zagreb '),
(17, ' Gwardia Stadium ', '2009-05-23 11:00:00', '2009-05-23 14:00:00', 'Details for  Gwardia Stadium '),
(18, ' Skonto Stadium - Riga ', '2009-05-25 19:00:00', '2009-05-25 22:00:00', 'Details for  Skonto Stadium - Riga '),
(19, ' Zalgirio Stadionas ', '2009-05-27 15:00:00', '2009-05-27 18:00:00', 'Details for  Zalgirio Stadionas '),
(20, ' O2 Dome ', '2009-05-30 17:00:00', '2009-05-30 20:00:00', 'Details for  O2 Dome '),
(21, ' Evenemententerrein Megaland ', '2009-05-31 16:00:00', '2009-05-31 19:00:00', 'Details for  Evenemententerrein Megaland '),
(22, ' HSH Nordbank Arena (formerly AOL Arena) ', '2009-06-02 10:00:00', '2009-06-02 13:00:00', 'Details for  HSH Nordbank Arena (formerly AOL Arena) '),
(23, ' LTU Arena ', '2009-06-04 11:00:00', '2009-06-04 14:00:00', 'Details for  LTU Arena '),
(24, ' LTU Arena ', '2009-06-05 12:00:00', '2009-06-05 15:00:00', 'Details for  LTU Arena '),
(25, ' Zentralstadion - Leipzig ', '2009-06-07 20:00:00', '2009-06-07 23:00:00', 'Details for  Zentralstadion - Leipzig '),
(26, ' Zentralstadion - Leipzig ', '2009-06-08 17:00:00', '2009-06-08 20:00:00', 'Details for  Zentralstadion - Leipzig '),
(27, ' Olympiastadion - Berlin ', '2009-06-10 14:00:00', '2009-06-10 17:00:00', 'Details for  Olympiastadion - Berlin '),
(28, ' Commerz Bank Arena ', '2009-06-12 14:00:00', '2009-06-12 17:00:00', 'Details for  Commerz Bank Arena '),
(29, ' Olympic Stadium - Munich ', '2009-06-13 11:00:00', '2009-06-13 14:00:00', 'Details for  Olympic Stadium - Munich '),
(30, ' Stadio Olimpico ', '2009-06-16 19:00:00', '2009-06-16 22:00:00', 'Details for  Stadio Olimpico '),
(31, ' Comunale Giuseppe Meazza - San Siro ', '2009-06-18 20:00:00', '2009-06-18 23:00:00', 'Details for  Comunale Giuseppe Meazza - San Siro '),
(32, ' Inter Stadion Slovakia ', '2009-06-22 19:00:00', '2009-06-22 22:00:00', 'Details for  Inter Stadion Slovakia '),
(33, ' Puskas Ferenc Stadium ', '2009-06-23 14:00:00', '2009-06-23 17:00:00', 'Details for  Puskas Ferenc Stadium '),
(34, ' Slavia Stadion ', '2009-06-25 10:00:00', '2009-06-25 13:00:00', 'Details for  Slavia Stadion '),
(35, ' Stade de France - Paris ', '2009-06-27 19:00:00', '2009-06-27 22:00:00', 'Details for  Stade de France - Paris '),
(36, ' Parken Stadium ', '2009-06-30 18:00:00', '2009-06-30 21:00:00', 'Details for  Parken Stadium '),
(37, ' Koengen ', '2009-07-02 18:00:00', '2009-07-02 21:00:00', 'Details for  Koengen '),
(38, ' Folkets Park ', '2009-07-03 11:00:00', '2009-07-03 14:00:00', 'Details for  Folkets Park '),
(39, ' Estadio Jose Zorila ', '2009-07-08 18:00:00', '2009-07-08 21:00:00', 'Details for  Estadio Jose Zorila '),
(40, ' Bessa Stadium ', '2009-07-11 10:00:00', '2009-07-11 13:00:00', 'Details for  Bessa Stadium '),
(41, ' Estadio Olimpico - Seville ', '2009-07-12 14:00:00', '2009-07-12 17:00:00', 'Details for  Estadio Olimpico - Seville '),
(42, ' Molson Amphitheatre ', '2009-07-24 16:00:00', '2009-07-24 19:00:00', 'Details for  Molson Amphitheatre '),
(43, ' Bell Centre ', '2009-07-25 18:00:00', '2009-07-25 21:00:00', 'Details for  Bell Centre '),
(44, ' Nissan Pavilion ', '2009-07-28 17:00:00', '2009-07-28 20:00:00', 'Details for  Nissan Pavilion '),
(45, ' Comcast Center - MA (formerly Tweeter Center) ', '2009-07-31 12:00:00', '2009-07-31 15:00:00', 'Details for  Comcast Center - MA (formerly Tweeter Center) '),
(46, ' Borgata Hotel Casino & Spa ', '2009-08-01 15:00:00', '2009-08-01 18:00:00', 'Details for  Borgata Hotel Casino & Spa '),
(47, ' Madison Square Garden ', '2009-08-03 14:00:00', '2009-08-03 17:00:00', 'Details for  Madison Square Garden '),
(48, ' Madison Square Garden ', '2009-08-04 15:00:00', '2009-08-04 18:00:00', 'Details for  Madison Square Garden '),
(49, ' Key Arena ', '2009-08-10 16:00:00', '2009-08-10 19:00:00', 'Details for  Key Arena '),
(50, ' Shoreline Amphitheatre ', '2009-08-12 11:00:00', '2009-08-12 14:00:00', 'Details for  Shoreline Amphitheatre '),
(51, ' Cricket Wireless Amphitheatre ', '2009-08-14 19:00:00', '2009-08-14 22:00:00', 'Details for  Cricket Wireless Amphitheatre '),
(52, ' Hollywood Bowl ', '2009-08-16 17:00:00', '2009-08-16 20:00:00', 'Details for  Hollywood Bowl '),
(53, ' Hollywood Bowl ', '2009-08-17 13:00:00', '2009-08-17 16:00:00', 'Details for  Hollywood Bowl '),
(54, ' Honda Center ', '2009-08-19 17:00:00', '2009-08-19 20:00:00', 'Details for  Honda Center '),
(55, ' Santa Barbara Bowl ', '2009-08-20 16:00:00', '2009-08-20 19:00:00', 'Details for  Santa Barbara Bowl '),
(56, ' Palms Casino-the Pearl ', '2009-08-22 10:00:00', '2009-08-22 13:00:00', 'Details for  Palms Casino-the Pearl '),
(57, ' US Airways Center ', '2009-08-23 18:00:00', '2009-08-23 21:00:00', 'Details for  US Airways Center '),
(58, ' E Center ', '2009-08-25 15:00:00', '2009-08-25 18:00:00', 'Details for  E Center '),
(59, ' Red Rocks Amphitheatre ', '2009-08-27 18:00:00', '2009-08-27 21:00:00', 'Details for  Red Rocks Amphitheatre '),
(60, ' Superpages.com Center ', '2009-08-29 17:00:00', '2009-08-29 20:00:00', 'Details for  Superpages.com Center '),
(61, ' Cynthia Woods Mitchell Pavilion ', '2009-08-30 18:00:00', '2009-08-30 21:00:00', 'Details for  Cynthia Woods Mitchell Pavilion '),
(62, ' Lakewood Amphitheatre ', '2009-09-01 15:00:00', '2009-09-01 18:00:00', 'Details for  Lakewood Amphitheatre '),
(63, ' Ford Amphitheatre at the Florida State Fairgrounds ', '2009-09-04 10:00:00', '2009-09-04 13:00:00', 'Details for  Ford Amphitheatre at the Florida State Fairgrounds '),
(64, ' BankAtlantic Center ', '2009-09-05 13:00:00', '2009-09-05 16:00:00', 'Details for  BankAtlantic Center '),
(65, ' Konig Pilsener Arena ', '2009-10-31 17:00:00', '2009-10-31 20:00:00', 'Details for  Konig Pilsener Arena '),
(66, ' Awd Dome ', '2009-11-01 13:00:00', '2009-11-01 16:00:00', 'Details for  Awd Dome '),
(67, ' TUI Arena (formerly Preussag Arena) ', '2009-11-03 14:00:00', '2009-11-03 17:00:00', 'Details for  TUI Arena (formerly Preussag Arena) '),
(68, ' SAP Arena ', '2009-11-07 13:00:00', '2009-11-07 16:00:00', 'Details for  SAP Arena '),
(69, ' Schleyerhalle ', '2009-11-08 12:00:00', '2009-11-08 15:00:00', 'Details for  Schleyerhalle '),
(70, ' Stade De Geneve ', '2009-11-10 17:00:00', '2009-11-10 20:00:00', 'Details for  Stade De Geneve '),
(71, ' Recinto Ferial - Valencia ', '2009-11-12 15:00:00', '2009-11-12 18:00:00', 'Details for  Recinto Ferial - Valencia '),
(72, ' Palau Sant Jordi ', '2009-11-20 12:00:00', '2009-11-20 15:00:00', 'Details for  Palau Sant Jordi '),
(73, ' Halle Tony Garnier ', '2009-11-23 20:00:00', '2009-11-23 23:00:00', 'Details for  Halle Tony Garnier '),
(74, ' Arena Nurnberg ', '2009-12-01 13:00:00', '2009-12-01 16:00:00', 'Details for  Arena Nurnberg '),
(75, ' Stadthalle ', '2009-12-03 14:00:00', '2009-12-03 17:00:00', 'Details for  Stadthalle '),
(76, ' Stadthalle Graz ', '2009-12-04 13:00:00', '2009-12-04 16:00:00', 'Details for  Stadthalle Graz '),
(77, ' Hallenstadion ', '2009-12-06 16:00:00', '2009-12-06 19:00:00', 'Details for  Hallenstadion '),
(78, ' Hallenstadion ', '2009-12-07 10:00:00', '2009-12-07 13:00:00', 'Details for  Hallenstadion '),
(79, ' The O2 - Dublin ', '2009-12-10 17:00:00', '2009-12-10 20:00:00', 'Details for  The O2 - Dublin '),
(80, ' Scottish Exhibition & Conference Center ', '2009-12-12 14:00:00', '2009-12-12 17:00:00', 'Details for  Scottish Exhibition & Conference Center '),
(81, ' LG Arena ', '2009-12-13 15:00:00', '2009-12-13 18:00:00', 'Details for  LG Arena '),
(82, ' O2 Dome ', '2009-12-15 13:00:00', '2009-12-15 16:00:00', 'Details for  O2 Dome '),
(83, ' O2 Dome ', '2009-12-16 15:00:00', '2009-12-16 18:00:00', 'Details for  O2 Dome '),
(84, ' MEN Arena Manchester ', '2009-12-18 16:00:00', '2009-12-18 19:00:00', 'Details for  MEN Arena Manchester '),
(1261150491, 'International Horse Show', '2009-12-19 07:00:00', '2009-12-21 07:00:00', 'Olympia'),
(1261150492, 'Ladbrokes.com World Darts Championships (Evening session)', '2009-12-19 18:00:00', '2009-12-19 20:00:00', 'Alexandra Palace'),
(1261150493, 'Peter Pan', '2009-12-20 08:00:00', '2009-12-20 10:00:00', 'O2 Arena'),
(1261150494, 'Pet Shop Boys', '2009-12-21 08:00:00', '2009-12-21 10:00:00', 'O2 Arena'),
(1261150495, 'Wicked', '2009-12-22 06:00:00', '2009-12-22 08:00:00', 'Apollo Victoria Theatre'),
(1261150496, 'Ladbrokes.com World Darts Championships (Afternoon session)', '2009-12-23 15:00:00', '2009-12-25 15:00:00', 'Alexandra Palace'),
(1261150497, 'Calendar Girls', '2009-12-23 15:00:00', '2009-12-23 17:00:00', 'Noel Coward Theatre'),
(1261150498, 'Sister Act', '2009-12-24 14:00:00', '2009-12-24 16:00:00', 'Palladium'),
(1261150499, 'Dirty Dancing', '2009-12-26 18:00:00', '2009-12-26 20:00:00', 'Aldwych Theatre'),
(1261150500, 'Harlequins -  Wasps     Competition: Guinness Premiership', '2009-12-27 09:00:00', '2009-12-27 11:00:00', 'Twickenham Stadium'),
(1261150501, 'Peter Pan', '2009-12-28 07:00:00', '2009-12-30 07:00:00', 'O2 Arena'),
(1261150502, 'The Nutcracker', '2009-12-29 08:00:00', '2009-12-29 10:00:00', 'Coliseum'),
(1261150503, 'The Nutcracker', '2009-12-29 13:00:00', '2009-12-29 15:00:00', 'Coliseum'),
(1261150504, 'Peter Pan', '2009-12-30 15:00:00', '2009-12-30 17:00:00', 'O2 Arena'),
(1261150505, 'Legally Blonde The Musical', '2009-12-31 17:00:00', '2009-12-31 19:00:00', 'Savoy Theatre'),
(1261150506, 'Sister Act', '2010-01-01 18:00:00', '2010-01-03 18:00:00', 'Palladium'),
(1261150507, 'Cat On a Hot Tin Roof', '2010-01-02 07:00:00', '2010-01-02 09:00:00', 'Novello Theatre'),
(1261150508, 'Grease', '2010-01-02 07:00:00', '2010-01-02 09:00:00', 'Piccadilly Theatre'),
(1261150509, 'Ladbrokes.com World Darts Championships', '2010-01-03 17:00:00', '2010-01-03 19:00:00', 'Alexandra Palace'),
(1261150510, 'Calendar Girls', '2010-01-05 14:00:00', '2010-01-05 16:00:00', 'Noel Coward Theatre'),
(1261150511, 'Dirty Dancing', '2010-01-06 08:00:00', '2010-01-08 08:00:00', 'Aldwych Theatre'),
(1261150512, 'Cirque du Soleil Varekai', '2010-01-07 15:00:00', '2010-01-07 17:00:00', 'Royal Albert Hall'),
(1261150513, 'Grease', '2010-01-08 15:00:00', '2010-01-08 17:00:00', 'Piccadilly Theatre'),
(1261150514, 'The Lion King', '2010-01-09 09:00:00', '2010-01-09 11:00:00', 'Lyceum Theatre'),
(1261150515, 'Cirque du Soleil Varekai', '2010-01-09 07:00:00', '2010-01-09 09:00:00', 'Royal Albert Hall'),
(1261150516, 'Cirque du Soleil Varekai', '2010-01-10 10:00:00', '2010-01-12 10:00:00', 'Royal Albert Hall'),
(1261150517, 'Masters Snooker 2010      Afternoon session', '2010-01-12 09:00:00', '2010-01-12 11:00:00', 'Wembley Arena'),
(1261150518, 'The Lion King', '2010-01-13 10:00:00', '2010-01-13 12:00:00', 'Lyceum Theatre'),
(1261150519, 'Cirque du Soleil Varekai', '2010-01-13 13:00:00', '2010-01-13 15:00:00', 'Royal Albert Hall'),
(1261150520, 'Cat On a Hot Tin Roof', '2010-01-14 11:00:00', '2010-01-14 13:00:00', 'Novello Theatre'),
(1261150521, 'Cirque du Soleil Varekai', '2010-01-15 07:00:00', '2010-01-17 07:00:00', 'Royal Albert Hall'),
(1261150522, 'Ben Hur Live', '2010-01-16 07:00:00', '2010-01-16 09:00:00', 'O2 Arena    Not Available X'),
(1261150523, 'Billy Connolly', '2010-01-16 16:00:00', '2010-01-16 18:00:00', 'Hammersmith Apollo'),
(1261150524, 'Wicked', '2010-01-18 14:00:00', '2010-01-18 16:00:00', 'Apollo Victoria Theatre'),
(1261150525, 'Wicked', '2010-01-20 06:00:00', '2010-01-20 08:00:00', 'Apollo Victoria Theatre'),
(1261150526, 'Giselle', '2010-01-20 07:00:00', '2010-01-22 07:00:00', 'Coliseum'),
(1261150527, 'Giselle', '2010-01-21 12:00:00', '2010-01-21 14:00:00', 'Coliseum'),
(1261150528, 'Giselle', '2010-01-22 13:00:00', '2010-01-22 15:00:00', 'Coliseum'),
(1261150529, 'Billy Connolly', '2010-01-23 15:00:00', '2010-01-23 17:00:00', 'Hammersmith Apollo'),
(1261150530, 'Jersey Boys', '2010-01-24 11:00:00', '2010-01-24 13:00:00', 'Prince Edward Theatre'),
(1261150531, 'Dirty Dancing', '2010-01-26 18:00:00', '2010-01-28 18:00:00', 'Aldwych Theatre'),
(1261150532, 'Billy Elliot', '2010-01-27 16:00:00', '2010-01-27 18:00:00', 'Victoria Palace Theatre'),
(1261150533, 'Reel Big Fish', '2010-01-28 18:00:00', '2010-01-28 20:00:00', 'Koko'),
(1261150534, 'Jersey Boys', '2010-01-29 14:00:00', '2010-01-29 16:00:00', 'Prince Edward Theatre'),
(1261150535, 'West Ham  - Blackburn Rovers     Competition: Premier League', '2010-01-30 15:00:00', '2010-01-30 17:00:00', 'Craven Cottage'),
(1261150536, 'The Lion King', '2010-01-30 17:00:00', '2010-02-01 17:00:00', 'Lyceum Theatre'),
(1261150537, 'Legally Blonde The Musical', '2010-02-01 09:00:00', '2010-02-01 11:00:00', 'Savoy Theatre'),
(1261150538, 'Daniel Barenboim      + Berlin Staatskapelle', '2010-02-02 17:00:00', '2010-02-02 19:00:00', 'Royal Festival Hall'),
(1261150539, 'Cat On a Hot Tin Roof', '2010-02-03 09:00:00', '2010-02-03 11:00:00', 'Novello Theatre'),
(1261150540, 'Wicked', '2010-02-04 13:00:00', '2010-02-04 15:00:00', 'Apollo Victoria Theatre'),
(1261150541, 'Wicked', '2010-02-05 15:00:00', '2010-02-07 15:00:00', 'Apollo Victoria Theatre'),
(1261150542, 'Jersey Boys', '2010-02-06 15:00:00', '2010-02-06 17:00:00', 'Prince Edward Theatre'),
(1261150543, 'Cirque du Soleil Varekai', '2010-02-06 06:00:00', '2010-02-06 08:00:00', 'Royal Albert Hall'),
(1261150544, 'Wicked', '2010-02-08 08:00:00', '2010-02-08 10:00:00', 'Apollo Victoria Theatre'),
(1261150545, 'Wicked', '2010-02-10 14:00:00', '2010-02-10 16:00:00', 'Apollo Victoria Theatre'),
(1261150546, 'Cirque du Soleil Varekai', '2010-02-10 09:00:00', '2010-02-12 09:00:00', 'Royal Albert Hall'),
(1261150547, 'Cirque du Soleil Varekai', '2010-02-11 12:00:00', '2010-02-11 14:00:00', 'Royal Albert Hall'),
(1261150548, 'Cirque du Soleil Varekai', '2010-02-12 14:00:00', '2010-02-12 16:00:00', 'Royal Albert Hall'),
(1261150549, 'Billy Elliot', '2010-02-13 06:00:00', '2010-02-13 08:00:00', 'Victoria Palace Theatre'),
(1261150550, 'Ne-Yo', '2010-02-14 07:00:00', '2010-02-14 09:00:00', 'Wembley Arena'),
(1261150551, 'Dirty Dancing', '2010-02-16 08:00:00', '2010-02-18 08:00:00', 'Aldwych Theatre'),
(1261150552, 'Billy Elliot', '2010-02-17 08:00:00', '2010-02-17 10:00:00', 'Victoria Palace Theatre'),
(1261150553, 'Dirty Dancing', '2010-02-18 12:00:00', '2010-02-18 14:00:00', 'Aldwych Theatre'),
(1261150554, 'Dirty Dancing', '2010-02-19 09:00:00', '2010-02-19 11:00:00', 'Aldwych Theatre'),
(1261150555, 'Fulham - Birmingham City     Competition: Premier League', '2010-02-20 11:00:00', '2010-02-20 13:00:00', 'Craven Cottage'),
(1261150556, 'Legally Blonde The Musical', '2010-02-20 16:00:00', '2010-02-22 16:00:00', 'Savoy Theatre'),
(1261150557, 'Wicked', '2010-02-22 16:00:00', '2010-02-22 18:00:00', 'Apollo Victoria Theatre'),
(1261150558, 'Sister Act', '2010-02-24 11:00:00', '2010-02-24 13:00:00', 'Palladium'),
(1261150559, 'Legally Blonde The Musical', '2010-02-25 16:00:00', '2010-02-25 18:00:00', 'Savoy Theatre'),
(1261150560, 'Grease', '2010-02-26 15:00:00', '2010-02-26 17:00:00', 'Piccadilly Theatre'),
(1261150561, 'The Lion King', '2010-02-27 13:00:00', '2010-03-01 13:00:00', 'Lyceum Theatre'),
(1261150562, 'Cinderella On Ice', '2010-02-27 14:00:00', '2010-02-27 16:00:00', 'Royal Albert Hall'),
(1261150563, 'Legally Blonde The Musical', '2010-02-28 15:00:00', '2010-02-28 17:00:00', 'Savoy Theatre'),
(1261150564, 'Fulham - Stoke City     Competition: Premier League', '2010-03-06 16:00:00', '2010-03-06 18:00:00', 'Craven Cottage'),
(1261150565, 'The 69 Eyes', '2010-03-09 14:00:00', '2010-03-09 16:00:00', 'Carling Academy Islington'),
(1261150566, 'Sara Baras', '2010-03-13 13:00:00', '2010-03-15 13:00:00', 'Royal Albert Hall'),
(1261150567, 'Trivium', '2010-03-18 16:00:00', '2010-03-18 18:00:00', 'Koko'),
(1261150568, 'Love Never Dies', '2010-03-22 11:00:00', '2010-03-22 13:00:00', 'Adelphi Theatre'),
(1261150569, 'West Ham  - Stoke City     Competition: Premier League', '2010-03-27 14:00:00', '2010-03-27 16:00:00', 'Boleyn Ground'),
(1261150570, 'Swan Lake      Ballet Nacional de Cuba', '2010-03-31 08:00:00', '2010-03-31 10:00:00', 'Coliseum'),
(1261150571, 'Peter Andre', '2010-04-03 09:00:00', '2010-04-05 09:00:00', 'Hammersmith Apollo'),
(1261150572, 'Paolo Nutini', '2010-04-08 10:00:00', '2010-04-08 12:00:00', 'Royal Albert Hall'),
(1261150573, 'Love Never Dies', '2010-04-12 10:00:00', '2010-04-12 12:00:00', 'Adelphi Theatre'),
(1261150574, 'Dancing On Ice', '2010-04-17 08:00:00', '2010-04-17 10:00:00', 'O2 Arena'),
(1261150575, 'Love Never Dies', '2010-04-20 16:00:00', '2010-04-20 18:00:00', 'Adelphi Theatre'),
(1261150576, 'Love Never Dies', '2010-04-24 06:00:00', '2010-04-26 06:00:00', 'Adelphi Theatre'),
(1261150577, 'Deadmau5', '2010-04-30 11:00:00', '2010-04-30 13:00:00', 'Brixton Academy'),
(1261150578, 'Love Never Dies', '2010-05-06 14:00:00', '2010-05-06 16:00:00', 'Adelphi Theatre'),
(1261150579, 'Lee Mack', '2010-05-10 15:00:00', '2010-05-10 17:00:00', 'Hammersmith Apollo'),
(1261150580, 'Gotan Project', '2010-05-14 09:00:00', '2010-05-14 11:00:00', 'Brixton Academy'),
(1261150581, 'Love Never Dies', '2010-05-19 15:00:00', '2010-05-21 15:00:00', 'Adelphi Theatre'),
(1261150582, 'Love Never Dies', '2010-05-24 07:00:00', '2010-05-24 09:00:00', 'Adelphi Theatre'),
(1261150583, 'Guiness Premiership Final 2010     Competition: Guinness Premiership Final', '2010-05-29 14:00:00', '2010-05-29 16:00:00', 'Twickenham Stadium'),
(1261150584, 'Mark Knopfler', '2010-06-04 11:00:00', '2010-06-04 13:00:00', 'Royal Albert Hall'),
(1261150585, 'Swan Lake', '2010-06-11 15:00:00', '2010-06-11 17:00:00', 'Royal Albert Hall'),
(1261150586, 'Leona Lewis', '2010-06-18 15:00:00', '2010-06-20 15:00:00', 'O2 Arena'),
(1261150587, 'Wimbledon: 3rd Round (Centre Court)', '2010-06-26 17:00:00', '2010-06-26 19:00:00', 'All England Lawn Tennis Club'),
(1261150588, 'Placido Domingo', '2010-07-05 09:00:00', '2010-07-05 11:00:00', 'Royal Opera House'),
(1261150589, 'Pakistan v Australia 1st Test (Day 5)', '2010-07-17 11:00:00', '2010-07-17 13:00:00', 'Lords Cricket Ground'),
(1261150590, 'npower: England v Pakistan 3rd Test (Day 5)', '2010-08-22 07:00:00', '2010-08-22 09:00:00', 'Oval Cricket Ground'),
(1261150591, 'Level 42', '2010-10-23 06:00:00', '2010-10-25 06:00:00', 'Indigo2'),
(1261150592, 'Jason Manford', '2010-11-24 09:00:00', '2010-11-24 11:00:00', 'Hammersmith Apollo');

-- --------------------------------------------------------

--
-- Table structure for table `events_map`
--
DROP TABLE IF EXISTS `events_map`;
CREATE TABLE IF NOT EXISTS `events_map` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `details` text NOT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `lat` float(10,6) DEFAULT NULL,
  `lng` float(10,6) DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=287 ;

--
-- Dumping data for table `events_map`
--

INSERT INTO `events_map` (`event_id`, `event_name`, `start_date`, `end_date`, `details`, `event_location`, `lat`, `lng`) VALUES
(278, 'Sudan', '2011-07-22 12:10:00', '2011-07-22 12:15:00', '', 'Janub Kurdufan, Sudan', 11.199019, 29.417933),
(285, 'Ships', '2010-09-01 02:40:00', '2010-09-01 15:05:00', '', 'Australia', -29.532804, 145.491470),
(286, 'Argentina', '2011-09-15 00:00:00', '2011-09-15 00:05:00', '', 'Argentina', -38.416096, -63.616673),
(90, 'Berlin', '2011-09-16 00:00:00', '2011-09-16 00:05:00', '', 'Berlin', 52.523403, 13.411400),
(268, 'India', '2012-07-22 11:35:00', '2012-07-22 11:40:00', '', 'Brazil', -14.235004, -51.925282);

-- --------------------------------------------------------

--
-- Table structure for table `events_ms`
--
DROP TABLE IF EXISTS `events_ms`;
CREATE TABLE IF NOT EXISTS `events_ms` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=109 ;

--
-- Dumping data for table `events_ms`
--

INSERT INTO `events_ms` (`event_id`, `event_name`, `start_date`, `end_date`, `details`) VALUES
(108, 'User: Nataly, Fruits: Madarin, Pineapple', '2009-11-05 10:15:00', '2009-11-05 13:35:00', 'Tokyo'),
(107, 'Users: George, Diana; Fruits: Orange, Kiwi, Plum', '2009-11-03 14:05:00', '2009-11-03 16:15:00', 'Belgium');

-- --------------------------------------------------------

--
-- Table structure for table `events_rec`
--
DROP TABLE IF EXISTS `events_rec`;
CREATE TABLE IF NOT EXISTS `events_rec` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `text` varchar(255) NOT NULL,
  `rec_type` varchar(64) NOT NULL,
  `event_pid` int(11) NOT NULL,
  `event_length` int(11) NOT NULL,
  PRIMARY KEY (`event_id`),
  KEY `start_date` (`start_date`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=81 ;

--
-- Dumping data for table `events_rec`
--

INSERT INTO `events_rec` (`event_id`, `start_date`, `end_date`, `text`, `rec_type`, `event_pid`, `event_length`) VALUES
(1, '2009-11-01 00:00:00', '9999-02-01 00:00:00', 'Second Friday', 'month_1_5_2_#no', 0, 172800),
(2, '2009-11-02 10:00:00', '9999-02-01 00:00:00', 'Test build', 'week_1___1,3,5#no', 0, 3600),
(63, '2009-11-04 10:00:00', '2009-11-04 11:00:00', 'Test build', 'none', 2, 1257321600),
(4, '2009-10-21 00:00:00', '2009-11-30 00:00:00', 'New event name for seria', 'day_8___#5', 0, 172800),
(62, '2009-11-11 10:00:00', '2009-11-11 11:00:00', 'Test build 11.11.09', '', 2, 1257926400),
(15, '2009-11-02 00:00:00', '2009-11-19 23:59:00', '2 Wed', 'week_1___0#2', 0, 300),
(19, '2009-07-01 00:00:00', '9999-02-01 00:00:00', '2nd monday', 'month_1_2_1_#no', 0, 300),
(20, '2009-01-01 00:00:00', '9999-02-01 00:00:00', 'Yearly', 'year_1_1_2_#no', 0, 300),
(21, '2010-01-31 00:00:00', '9999-02-01 00:00:00', 'New event', 'month_1___#no', 0, 86400),
(64, '2009-11-27 10:00:00', '2009-11-27 11:00:00', 'Test build', 'none', 2, 1259308800),
(66, '2009-11-10 03:15:00', '9999-02-01 00:00:00', 'Reccuring_03', 'day_2___#no', 0, 16200),
(67, '2009-11-10 03:15:00', '2009-12-04 00:00:00', 'Reccuring_05', 'day_2___#', 0, 16200),
(68, '2009-11-10 03:15:00', '2009-11-15 03:15:00', 'Reccuring_06', 'day_5___#1', 0, 16200),
(78, '2009-11-15 00:00:00', '2009-11-15 00:05:00', 'Play', 'none', 15, 1258232400),
(79, '2009-11-10 03:15:00', '2009-12-04 00:00:00', 'Reccuring_28', 'day_2___#', 0, 16200),
(80, '2009-11-09 00:00:00', '2009-11-16 00:00:00', 'Recurring_29', 'week_1___1,5#2', 0, 300);

-- --------------------------------------------------------

--
-- Table structure for table `events_shared`
--
DROP TABLE IF EXISTS `events_shared`;
CREATE TABLE IF NOT EXISTS `events_shared` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `text` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `event_type` int(11) NOT NULL DEFAULT '0',
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `events_shared`
--

INSERT INTO `events_shared` (`event_id`, `start_date`, `end_date`, `text`, `event_type`, `userId`) VALUES
(4, '2009-06-17 09:05:00', '2009-06-17 16:55:00', 'New event', 1, 1),
(2, '2009-06-03 00:00:00', '2009-06-06 00:00:00', 'New event', 0, 1),
(3, '2009-06-09 00:00:00', '2009-06-12 00:00:00', 'New event', 0, 1),
(5, '2009-06-03 00:00:00', '2009-06-05 00:00:00', 'USer 2 event', 1, 2),
(6, '2009-06-02 00:00:00', '2009-06-06 00:00:00', 'user 2', 1, 2),
(7, '2009-06-03 00:00:00', '2009-06-06 00:00:00', 'New event', 1, 2),
(8, '2009-06-10 00:00:00', '2009-06-12 00:00:00', '234', 0, 2),
(9, '2009-06-18 21:15:00', '2009-06-18 22:55:00', 'Some event', 1, 2),
(10, '2009-06-05 00:00:00', '2009-06-07 00:00:00', 'asd adf', 1, 1),
(11, '2009-06-09 00:00:00', '2009-06-10 16:55:00', 'Some event', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `events_tt`
--
DROP TABLE IF EXISTS `events_tt`;
CREATE TABLE IF NOT EXISTS `events_tt` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `details` text NOT NULL,
  `section_id` int(11) NOT NULL,
  `section2_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=96 ;

--
-- Dumping data for table `events_tt`
--

INSERT INTO `events_tt` (`event_id`, `event_name`, `start_date`, `end_date`, `details`, `section_id`, `section2_id`) VALUES
(93, 'David-David', '2009-06-30 08:00:00', '2009-06-30 09:00:00', '', 50, 5),
(92, 'Web Testing - Linda Brown', '2009-06-30 11:00:00', '2009-06-30 11:30:00', '', 10, 6),
(91, 'George -> George, 5 minutes', '2009-06-30 13:00:00', '2009-06-30 13:05:00', '', 70, 7),
(90, 'Kate-Dian 30 jun - 1 september', '2009-06-30 11:30:00', '2009-09-01 11:30:00', '', 80, 9),
(89, 'david- david 9-9.30', '2009-06-30 09:00:00', '2009-06-30 09:30:00', '', 50, 5),
(88, 'Managers -> Kate Moss, 09 09.30', '2009-06-30 09:00:00', '2009-06-30 09:30:00', '', 30, 8),
(87, '2009.06.08-09 David Miller -> Eliz Taylor', '2009-06-30 08:00:00', '2009-06-30 09:00:00', '', 50, 2),
(86, '30-01 Linda Brown - Dian Fossey', '2009-06-30 00:00:00', '2009-07-01 00:00:00', '', 60, 9),
(85, 'New event', '2009-06-30 00:00:00', '2009-07-01 00:00:00', '', 20, 2),
(94, 'New event', '2009-06-30 10:00:00', '2009-06-30 10:30:00', '', 60, 0),
(95, 'New event', '2009-06-30 10:30:00', '2009-06-30 16:00:00', '', 30, 0);

-- --------------------------------------------------------

--
-- Table structure for table `event_fruit`
--
DROP TABLE IF EXISTS `event_fruit`;
CREATE TABLE IF NOT EXISTS `event_fruit` (
  `event_fruit_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `fruit_id` int(11) NOT NULL,
  PRIMARY KEY (`event_fruit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

--
-- Dumping data for table `event_fruit`
--

INSERT INTO `event_fruit` (`event_fruit_id`, `event_id`, `fruit_id`) VALUES
(27, 107, 5),
(26, 107, 4),
(25, 107, 1),
(28, 108, 0);

-- --------------------------------------------------------

--
-- Table structure for table `event_user`
--
DROP TABLE IF EXISTS `event_user`;
CREATE TABLE IF NOT EXISTS `event_user` (
  `event_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`event_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=92 ;

--
-- Dumping data for table `event_user`
--

INSERT INTO `event_user` (`event_user_id`, `event_id`, `user_id`) VALUES
(91, 108, 2),
(90, 107, 3),
(89, 107, 1);

-- --------------------------------------------------------

--
-- Table structure for table `fruit`
--
DROP TABLE IF EXISTS `fruit`;
CREATE TABLE IF NOT EXISTS `fruit` (
  `fruit_id` int(11) NOT NULL AUTO_INCREMENT,
  `fruit_name` varchar(64) NOT NULL,
  PRIMARY KEY (`fruit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `fruit`
--

INSERT INTO `fruit` (`fruit_id`, `fruit_name`) VALUES
(1, 'Orange'),
(2, 'Banana'),
(3, 'Peach'),
(4, 'Kiwi'),
(5, 'Plum'),
(6, 'Grapefruit'),
(7, 'Lime'),
(8, 'Lemon'),
(9, 'Mandarin'),
(10, 'Pineapple');

-- --------------------------------------------------------

--
-- Table structure for table `tevents`
--
DROP TABLE IF EXISTS `tevents`;
CREATE TABLE IF NOT EXISTS `tevents` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `tevents`
--

INSERT INTO `tevents` (`event_id`, `event_name`, `start_date`, `end_date`, `type`) VALUES
(1, 'dblclick me!', '2010-03-02 00:00:00', '2010-03-04 00:00:00', 1),
(2, 'and me!', '2010-03-09 00:00:00', '2010-03-11 00:00:00', 2),
(3, 'and me too!', '2010-03-16 00:00:00', '2010-03-18 00:00:00', 3),
(4, 'Type 2 event', '2010-03-02 08:00:00', '2010-03-02 14:10:00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `types`
--
DROP TABLE IF EXISTS `types`;
CREATE TABLE IF NOT EXISTS `types` (
  `typeid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`typeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`typeid`, `name`) VALUES
(1, 'Simple'),
(2, 'Complex'),
(3, 'Unknown');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`) VALUES
(1, 'George'),
(2, 'Nataly'),
(3, 'Diana'),
(5, 'Adam');

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_nm` varchar(200) DEFAULT NULL,
  `item_cd` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=196 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`item_id`, `item_nm`, `item_cd`) VALUES
(1, 'Afghanistan ', '795552'),
(2, 'Albania ', '520663'),
(3, 'Algeria ', '359180'),
(4, 'Andorra ', '730120'),
(5, 'Angola ', '423689'),
(6, 'Antigua and Barbuda ', '442915'),
(7, 'Argentina ', '148510'),
(8, 'Armenia ', '506369'),
(9, 'Australia ', '286442'),
(10, 'Austria ', '876597'),
(11, 'Azerbaijan ', '375004'),
(12, 'Bahamas ', '402458'),
(13, 'Bahrain ', '248781'),
(14, 'Bangladesh ', '930504'),
(15, 'Barbados ', '687580'),
(16, 'Belarus ', '175332'),
(17, 'Belgium ', '607774'),
(18, 'Belize ', '145530'),
(19, 'Benin ', '303057'),
(20, 'Bhutan ', '878232'),
(21, 'Bolivia ', '364191'),
(22, 'Bosnia and Herzegovina ', '223067'),
(23, 'Botswana ', '598754'),
(24, 'Brazil ', '539705'),
(25, 'Brunei ', '679298'),
(26, 'Bulgaria ', '704010'),
(27, 'Burkina Faso ', '791490'),
(28, 'Burundi ', '212993'),
(29, 'Cambodia ', '553867'),
(30, 'Cameroon ', '697153'),
(31, 'Canada ', '530184'),
(32, 'Cape Verde ', '519239'),
(33, 'Central African Republic ', '284965'),
(34, 'Chad ', '607589'),
(35, 'Chile ', '760053'),
(36, 'China ', '783288'),
(37, 'Colombia ', '601761'),
(38, 'Comoros ', '408313'),
(39, 'Congo (Brazzaville) ', '265818'),
(40, 'Congo, Democratic Republic of the ', '762084'),
(41, 'Costa Rica ', '937123'),
(42, 'Croatia ', '856896'),
(43, 'Cuba ', '644670'),
(44, 'Cyprus ', '291822'),
(45, 'Czech Republic ', '868896'),
(46, 'Denmark ', '453807'),
(47, 'Djibouti ', '187991'),
(48, 'Dominica ', '691325'),
(49, 'Dominican Republic ', '178444'),
(50, 'East Timor (Timor Timur) ', '865968'),
(51, 'Ecuador ', '445288'),
(52, 'Egypt ', '272201'),
(53, 'El Salvador ', '134453'),
(54, 'Equatorial Guinea ', '878100'),
(55, 'Eritrea ', '309308'),
(56, 'Estonia ', '195718'),
(57, 'Ethiopia ', '277818'),
(58, 'Fiji ', '571221'),
(59, 'Finland ', '833292'),
(60, 'France ', '326846'),
(61, 'Gabon ', '581533'),
(62, 'Gambia, The ', '458449'),
(63, 'Georgia ', '909985'),
(64, 'Germany ', '962653'),
(65, 'Ghana ', '586491'),
(66, 'Greece ', '620802'),
(67, 'Grenada ', '728432'),
(68, 'Guatemala ', '239709'),
(69, 'Guinea ', '265686'),
(70, 'Guinea-Bissau ', '707201'),
(71, 'Guyana ', '909774'),
(72, 'Haiti ', '183481'),
(73, 'Honduras ', '888412'),
(74, 'Hungary ', '798664'),
(75, 'Iceland ', '778857'),
(76, 'India ', '658938'),
(77, 'Indonesia ', '699263'),
(78, 'Iran ', '544109'),
(79, 'Iraq ', '956824'),
(80, 'Ireland ', '234751'),
(81, 'Israel ', '967532'),
(82, 'Italy ', '888755'),
(83, 'Jamaica ', '768519'),
(84, 'Japan ', '504285'),
(85, 'Jordan ', '802013'),
(86, 'Kazakhstan ', '617321'),
(87, 'Kenya ', '835085'),
(88, 'Kiribati ', '900412'),
(89, 'Korea, North ', '607800'),
(90, 'Korea, South ', '675026'),
(91, 'Kuwait ', '481367'),
(92, 'Kyrgyzstan ', '423900'),
(93, 'Laos ', '357887'),
(94, 'Latvia ', '710788'),
(95, 'Lebanon ', '328508'),
(96, 'Lesotho ', '236017'),
(97, 'Liberia ', '457367'),
(98, 'Libya ', '805626'),
(99, 'Liechtenstein ', '565550'),
(100, 'Lithuania ', '201731'),
(101, 'Luxembourg ', '879234'),
(102, 'Macedonia', '608460'),
(103, 'Madagascar ', '949440'),
(104, 'Malawi ', '618112'),
(105, 'Malaysia ', '300183'),
(106, 'Maldives ', '200307'),
(107, 'Mali ', '805020'),
(108, 'Malta ', '485930'),
(109, 'Marshall Islands ', '421605'),
(110, 'Mauritania ', '958486'),
(111, 'Mauritius ', '753434'),
(112, 'Mexico ', '966450'),
(113, 'Micronesia, Federated States of ', '784396'),
(114, 'Moldova ', '371417'),
(115, 'Monaco ', '983725'),
(116, 'Mongolia ', '196246'),
(117, 'Montenegro ', '497349'),
(118, 'Morocco ', '680617'),
(119, 'Mozambique ', '388639'),
(120, 'Myanmar (Burma) ', '200782'),
(121, 'Namibia ', '829758'),
(122, 'Nauru ', '238707'),
(123, 'Nepal ', '885616'),
(124, 'Netherlands ', '845028'),
(125, 'New Zealand ', '890416'),
(126, 'Nicaragua ', '423003'),
(127, 'Niger ', '503204'),
(128, 'Nigeria ', '697654'),
(129, 'Norway ', '220219'),
(130, 'Oman ', '450326'),
(131, 'Pakistan ', '294644'),
(132, 'Palau ', '487828'),
(133, 'Panama ', '520558'),
(134, 'Papua New Guinea ', '833793'),
(135, 'Paraguay ', '881080'),
(136, 'Peru ', '512619'),
(137, 'Philippines ', '468128'),
(138, 'Poland ', '790224'),
(139, 'Portugal ', '723157'),
(140, 'Qatar ', '636996'),
(141, 'Romania ', '900122'),
(142, 'Russia ', '832738'),
(143, 'Rwanda ', '442018'),
(144, 'Saint Kitts and Nevis ', '293827'),
(145, 'Saint Lucia ', '493235'),
(146, 'Saint Vincent and The Grenadines ', '178154'),
(147, 'Samoa ', '226707'),
(148, 'San Marino ', '212281'),
(149, 'Sao Tome and Principe ', '948859'),
(150, 'Saudi Arabia ', '203788'),
(151, 'Senegal ', '698366'),
(152, 'Serbia ', '689426'),
(153, 'Seychelles ', '943690'),
(154, 'Sierra Leone ', '126462'),
(155, 'Singapore ', '317642'),
(156, 'Slovakia ', '726032'),
(157, 'Slovenia ', '450721'),
(158, 'Solomon Islands ', '459293'),
(159, 'Somalia ', '569876'),
(160, 'South Africa ', '619167'),
(161, 'Spain ', '739245'),
(162, 'Sri Lanka ', '419100'),
(163, 'Sudan ', '779912'),
(164, 'Suriname ', '233801'),
(165, 'Swaziland ', '918055'),
(166, 'Sweden ', '519002'),
(167, 'Switzerland ', '704696'),
(168, 'Syria ', '731201'),
(169, 'Taiwan ', '528048'),
(170, 'Tajikistan ', '840017'),
(171, 'Tanzania ', '533270'),
(172, 'Thailand ', '247937'),
(173, 'Togo ', '406414'),
(174, 'Tonga ', '166866'),
(175, 'Trinidad and Tobago ', '886777'),
(176, 'Tunisia ', '809477'),
(177, 'Turkey ', '958248'),
(178, 'Turkmenistan ', '308965'),
(179, 'Tuvalu ', '225863'),
(180, 'Uganda ', '552390'),
(181, 'Ukraine ', '428146'),
(182, 'United Arab Emirates ', '915234'),
(183, 'United Kingdom ', '900069'),
(184, 'United States ', '637945'),
(185, 'Uruguay ', '949598'),
(186, 'Uzbekistan ', '338292'),
(187, 'Vanuatu ', '505841'),
(188, 'Vatican City ', '931110'),
(189, 'Venezuela ', '767200'),
(190, 'Vietnam ', '819657'),
(191, 'Western Sahara ', '528522'),
(192, 'Yemen ', '864755'),
(193, 'Zambia ', '286047'),
(194, 'Zimbabwe ', '711948');
