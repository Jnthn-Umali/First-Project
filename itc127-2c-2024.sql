-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2024 at 04:40 PM
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
-- Database: `itc127-2c-2024`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblaccounts`
--

CREATE TABLE `tblaccounts` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `usertype` varchar(20) NOT NULL,
  `userstatus` varchar(20) NOT NULL,
  `createdby` varchar(50) NOT NULL,
  `datecreated` varchar(20) NOT NULL,
  `email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblaccounts`
--

INSERT INTO `tblaccounts` (`username`, `password`, `usertype`, `userstatus`, `createdby`, `datecreated`, `email`) VALUES
('22-00123', '123456', 'STUDENT', 'ACTIVE', 'admin', '04/20/2024', NULL),
('22-00789', '1234567', 'STUDENT', 'ACTIVE', 'admin', '04/20/2024', NULL),
('22-00982', '123456', 'STUDENT', 'ACTIVE', 'admin', '04/20/2024', NULL),
('22-01214', '123456', 'STUDENT', 'ACTIVE', 'admin', '04/20/2024', NULL),
('admin', '123456', 'ADMINISTRATOR', 'ACTIVE', 'admin', '2/17/24', NULL),
('registrar', '123456', 'REGISTRAR', 'ACTIVE', 'admin', '2/03/24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblgrades`
--

CREATE TABLE `tblgrades` (
  `studentnumber` varchar(20) NOT NULL,
  `code` varchar(15) NOT NULL,
  `grade` varchar(15) NOT NULL,
  `encodedby` varchar(20) NOT NULL,
  `dateencoded` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblgrades`
--

INSERT INTO `tblgrades` (`studentnumber`, `code`, `grade`, `encodedby`, `dateencoded`) VALUES
('22-00982', 'ITC111', '1.75', 'admin', '2024-05-04'),
('22-00982', 'ITC112', '2.00', 'admin', '2024-05-04'),
('22-00982', 'ITC113', '1.25', 'admin', '2024-05-04');

-- --------------------------------------------------------

--
-- Table structure for table `tbllogs`
--

CREATE TABLE `tbllogs` (
  `datelog` varchar(15) NOT NULL,
  `timelog` varchar(15) NOT NULL,
  `action` varchar(20) NOT NULL,
  `module` varchar(30) NOT NULL,
  `ID` varchar(30) NOT NULL,
  `performedby` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbllogs`
--

INSERT INTO `tbllogs` (`datelog`, `timelog`, `action`, `module`, `ID`, `performedby`) VALUES
('04/27/2024', '02:24:47', 'Update', 'Subject Management', 'ITC124', 'admin'),
('04/27/2024', '02:25:00', 'Update', 'Subject Management', 'ITC124', 'admin'),
('04/27/2024', '03:03:36', 'Update', 'Subject Management', 'ITC123', 'admin'),
('04/27/2024', '03:04:05', 'Update', 'Subject Management', 'ITC127', 'admin'),
('05/04/2024', '03:27:25', 'Delete', 'Subject management', 'CS211', 'admin'),
('05/04/2024', '03:27:29', 'Delete', 'Subject management', 'GCAS09', 'admin'),
('05/04/2024', '03:27:33', 'Delete', 'Subject management', 'ITC123', 'admin'),
('05/04/2024', '03:27:38', 'Delete', 'Subject management', 'ITC124', 'admin'),
('05/04/2024', '03:27:44', 'Delete', 'Subject management', 'ITC127', 'admin'),
('05/04/2024', '03:28:39', 'Create', 'Subjects management', 'ITC111', 'admin'),
('05/04/2024', '03:29:32', 'Create', 'Subjects management', 'ITC112', 'admin'),
('05/04/2024', '03:29:55', 'Create', 'Subjects management', 'ITC113', 'admin'),
('05/04/2024', '03:30:27', 'Create', 'Subjects management', 'ITC227', 'admin'),
('05/04/2024', '04:12:03', 'Add', 'Grades Management', '22-00982', 'admin'),
('05/04/2024', '04:12:30', 'Add', 'Grades Management', '22-00982', 'admin'),
('05/04/2024', '04:13:00', 'Add', 'Grades Management', '22-00982', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tblstudents`
--

CREATE TABLE `tblstudents` (
  `studentnumber` varchar(20) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) NOT NULL,
  `course` varchar(50) NOT NULL,
  `yearlevel` varchar(5) NOT NULL,
  `createdby` varchar(50) NOT NULL,
  `datecreated` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblstudents`
--

INSERT INTO `tblstudents` (`studentnumber`, `lastname`, `firstname`, `middlename`, `course`, `yearlevel`, `createdby`, `datecreated`, `password`) VALUES
('22-00123', 'Benedict', 'John', 'Gallar', 'Bachelor of Science in Accountancy', '3rd', 'admin', '04/20/2024', '123456'),
('22-00789', 'Kono', 'Christian', 'Valerio', 'Bachelor of Science in Pharmacy', '1st', 'admin', '04/20/2024', '1234567'),
('22-00982', 'Villena', 'Rafael', 'Villagen', 'Bachelor of Science in Computer Science', '2nd', 'admin', '04/20/2024', '123456'),
('22-01214', 'Umali', 'Jonathan', 'Intal', 'Bachelor of Science in Computer Science', '2nd', 'admin', '04/20/2024', '123456');

-- --------------------------------------------------------

--
-- Table structure for table `tblsubjects`
--

CREATE TABLE `tblsubjects` (
  `code` varchar(15) NOT NULL,
  `description` varchar(50) NOT NULL,
  `unit` varchar(2) NOT NULL,
  `course` varchar(50) NOT NULL,
  `prerequisite1` varchar(20) NOT NULL,
  `prerequisite2` varchar(20) NOT NULL,
  `prerequisite3` varchar(20) NOT NULL,
  `createdby` varchar(50) NOT NULL,
  `datecreated` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblsubjects`
--

INSERT INTO `tblsubjects` (`code`, `description`, `unit`, `course`, `prerequisite1`, `prerequisite2`, `prerequisite3`, `createdby`, `datecreated`) VALUES
('ITC111', 'intro to programming', '3', 'Bachelor of Science in Computer Science', '', '', '', 'admin', '04/05/2024'),
('ITC112', 'intro to design', '3', 'Bachelor of Science in Computer Science', 'ITC111', '', '', 'admin', '04/05/2024'),
('ITC113', 'Fundamentals of Database', '3', 'Bachelor of Science in Computer Science', 'ITC111', 'ITC112', '', 'admin', '04/05/2024'),
('ITC227', 'Advance Database', '3', 'Bachelor of Science in Computer Science', 'ITC111', 'ITC112', 'ITC113', 'admin', '04/05/2024');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblaccounts`
--
ALTER TABLE `tblaccounts`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`studentnumber`);

--
-- Indexes for table `tblsubjects`
--
ALTER TABLE `tblsubjects`
  ADD PRIMARY KEY (`code`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
