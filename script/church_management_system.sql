-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 31, 2025 at 11:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `church_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'present',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `donation_type` varchar(100) DEFAULT NULL,
  `donation_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `income_source` enum('member','anonymous','service_total') NOT NULL DEFAULT 'member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `member_id`, `amount`, `donation_type`, `donation_date`, `notes`, `created_at`, `income_source`) VALUES
(3, NULL, 400.00, 'Tithe', '2025-11-08', 'Tithe', '2025-12-01 19:06:48', 'anonymous'),
(4, NULL, 690.00, 'Service Offering', '2025-12-01', 'Service', '2025-12-05 03:58:04', 'service_total'),
(7, 136, 120.00, 'Service Offering', '2025-12-01', 'Amy Plegde', '2025-12-05 03:59:55', 'member'),
(9, NULL, 1200.00, 'General', '2025-12-07', 'service_total', '2025-12-13 12:27:39', 'service_total'),
(16, NULL, 5000.00, 'General', '2025-03-14', 'Teasdt dfisd', '2025-12-25 16:48:18', 'service_total'),
(18, NULL, 1200.00, 'Other', '2025-02-05', 'dhjsjd', '2025-12-25 18:08:35', 'anonymous'),
(21, 130, 3200.00, 'Other', '2025-07-06', 'sfhsd', '2025-12-27 23:57:25', 'member'),
(22, NULL, 2000.00, 'Missions', '2025-06-08', 'fjdhs', '2025-12-28 00:04:32', 'anonymous'),
(23, NULL, 1500.00, 'General', '2025-04-28', 'etywrq', '2025-12-28 00:12:09', 'member'),
(24, NULL, 690.00, 'Service Tithe', '2025-08-31', 'paid in cash', '2025-12-28 00:28:31', 'member'),
(25, NULL, 3050.00, 'General', '2025-09-28', 'asdfghjkl', '2025-12-28 01:13:59', 'anonymous'),
(26, 37, 1250.00, 'General', '2025-10-05', 'sdfghjk', '2025-12-28 01:15:24', 'member');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `organizer_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `location`, `capacity`, `organizer_id`, `status`, `created_at`, `updated_at`) VALUES
(13, 'New Year Prayers & Fasting', 'Begin the new year with us in prayer and fasting. Let\'s seek God\'s guidance and blessings for 2026.', '2026-01-01 00:00:00', 'Church auditorium', 1500, 1, 'scheduled', '2025-11-29 23:21:22', '2025-12-25 01:55:46'),
(14, 'Community Food Drive', 'Participate in our community food drive to help provide nutritious meals to families in need.', '2026-01-12 07:30:00', 'Church Community', 500, 1, 'scheduled', '2025-11-29 23:22:44', '2025-12-25 01:55:37'),
(15, '31st Watch Night', 'Join us for our all night service as we crossover into the next year in victory', '2025-12-31 21:30:00', 'Church Premise', 1200, 1, 'ongoing', '2025-11-29 23:28:36', '2025-12-29 02:18:10');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `expense_date` date NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `expense_date`, `category_id`, `amount`, `description`, `receipt_path`, `created_at`) VALUES
(12, '2025-08-06', 2, 500.00, 'Bought fuel for generator', 'assets/uploads/receipts/1766698865_VID-20251003-WA0005_1_.jpg', '2025-12-12 18:25:37'),
(13, '2025-07-15', 3, 300.00, 'Paid instrumentalist', '', '2025-12-12 18:25:53'),
(15, '2025-06-18', 3, 300.00, 'Paid instrumentalist for Gig', '', '2025-12-14 23:41:59'),
(17, '2025-04-09', 5, 4500.00, 'Love Feast', '', '2025-12-28 00:57:22'),
(19, '2025-12-29', 1, 540.00, 'Banner for watch night service', NULL, '2025-12-29 01:40:30');

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Banner', '31st alnight banner', '2025-12-11 17:32:12'),
(2, 'Fuel', 'bought fuel', '2025-12-11 17:32:56'),
(3, 'Instrumentalist', 'Paid instrumentalist', '2025-12-12 18:25:53'),
(4, 'Maintenance', 'new chairs', '2025-12-12 19:09:27'),
(5, 'Get Together', NULL, '2025-12-24 23:02:07');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_ministries`
--

CREATE TABLE `homepage_ministries` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homepage_programs`
--

CREATE TABLE `homepage_programs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `day_time` varchar(100) DEFAULT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `status` enum('active','inactive','transferred','deceased') NOT NULL DEFAULT 'active',
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `area` varchar(150) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `gps` varchar(20) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `member_img` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `date_of_birth`, `gender`, `join_date`, `status`, `address`, `city`, `area`, `landmark`, `gps`, `region`, `emergency_contact_name`, `emergency_phone`, `member_img`, `created_at`, `updated_at`) VALUES
(11, NULL, 'Kwesi', 'Frimpong', 'kwesi@gmail.com', '0534568978', '2005-04-13', 'Male', '2025-12-23', 'active', 'Tesano', 'Abeka', 'Sackey', 'Drug Store', '', 'Greater Accra', NULL, NULL, 'member_694a8ecd9eec72.04547396.png', '2025-12-13 08:52:10', '2025-12-23 12:45:01'),
(14, NULL, 'Godwin', 'Quansah', 'godwin@gmail.com', '43214675', '2006-02-22', 'Male', '2025-12-23', 'active', 'Kasoa', 'Broadcasting', 'Sackey', 'Drug Store', '', 'Greater Accra', 'Godwin Quayson', '43214675', 'member_694a7f23bb1b45.16674306.png', '2025-12-23 11:38:11', '2025-12-23 12:44:21'),
(36, NULL, 'Asantewaa', 'Agyeiwaa', 'asantewa243@gmail.com', '0553423124', '2005-06-16', 'Female', '2025-12-30', 'active', 'West Hills', 'Accra', 'Broadcasting', 'School', '', 'Greater Accra', 'Amy Chutti', '23456321', 'member_695330813d7078.88943060.png', '2025-12-28 01:30:42', '2025-12-30 02:39:46'),
(37, NULL, 'Ebenezer', 'Quayson', 'eben@gmail.com', '0538697161', '2025-12-09', 'Male', '2025-12-30', 'active', 'Tesano', 'Abeka', 'Free Pipe', 'GCTU', 'GP-6655-4323', 'Greater Accra', '', '', NULL, '2025-12-28 01:30:42', '2025-12-30 02:19:19'),
(38, NULL, 'Florence', 'Ampoma', 'florenceampoma@gmail.com', '0543678954', '2005-08-23', 'Female', '2025-12-23', 'active', 'Broadcasting', '', '', NULL, NULL, 'Greater Accra', '', '', NULL, '2025-12-28 01:30:42', '2025-12-28 01:30:42'),
(72, NULL, 'Abigail', 'Owusu', 'abigail.owusu@yahoo.com', '0204567812', '2001-09-03', 'Female', '2023-03-10', 'active', 'Near Asokwa Police Station', 'Kumasi', 'Asokwa', NULL, NULL, 'Ashanti', 'Samuel Owusu', '0209987345', NULL, '2025-12-29 00:08:30', '2025-12-29 00:08:30'),
(73, NULL, 'Joseph', 'Agyeman', 'joseph.agyeman@gmail.com', '0278893451', '1995-06-25', 'Male', '2021-11-05', 'active', 'Behind Suame Magazine', 'Kumasi', 'Suame', NULL, NULL, 'Ashanti', 'Linda Agyeman', '0246678123', NULL, '2025-12-29 00:08:30', '2025-12-29 00:08:30'),
(74, NULL, 'Priscilla', 'Boateng', 'priscilla.boateng@gmail.com', '0557812394', '2000-12-19', 'Female', '2022-07-21', 'active', 'Community 11 Junction', 'Tema', 'Community 11', NULL, NULL, 'Greater Accra', 'Esther Boateng', '0556678211', NULL, '2025-12-29 00:08:30', '2025-12-29 00:08:30'),
(75, NULL, 'Michael', 'Asare', 'michael.asare@gmail.com', '0249982314', '1992-02-14', 'Male', '2020-05-30', 'active', 'Effiduase Main Road', 'Koforidua', 'Effiduase', NULL, NULL, 'Eastern', 'Peter Asare', '0243345678', NULL, '2025-12-29 00:08:30', '2025-12-29 00:08:30'),
(128, NULL, 'Emmanuel', 'Tetteh', 'emmanuel.tetteh@gmail.com', '547812234', '1997-10-01', 'Male', '2025-12-31', 'active', 'Near Asokwa Police Station', 'Accra', 'Nungua', '', '', 'Greater Accra', 'Comfort Tetteh', '247811223', NULL, '2025-12-29 00:36:44', '2025-12-31 03:28:11'),
(129, NULL, 'Deborah', 'Sackey', 'deborah.sackey@gmail.com', '241123987', '2002-01-27', 'Female', '2024-01-07', 'active', 'Behind Suame Magazine', 'Cape Coast', 'Abura', NULL, NULL, 'Central', 'Isaac Sackey', '245566778', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(130, NULL, 'Stephen', 'Adu', 'stephen.adu@gmail.com', '276654321', '1994-03-09', 'Male', '2019-10-12', 'active', 'Community 11 Junction', 'Sunyani', 'Newtown', NULL, NULL, 'Bono', 'Martha Adu', '209988776', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(131, NULL, 'Grace', 'Nyarko', 'grace.nyarko@gmail.com', '503456789', '1996-05-17', 'Female', '2020-06-22', 'active', 'Effiduase Main Road', 'Obuasi', 'Sanso', NULL, NULL, 'Ashanti', 'Daniel Nyarko', '501122334', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(132, NULL, 'Joshua', 'Osei', 'joshua.osei@gmail.com', '245567123', '2003-07-11', 'Male', '2024-02-15', 'active', 'Madina Zongo Junction', 'Akosombo', 'Zongo', NULL, NULL, 'Eastern', 'Mercy Osei', '246677889', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(133, NULL, 'Felicia', 'Darko', 'felicia.darko@gmail.com', '559012345', '1998-11-06', 'Female', '2021-12-09', 'active', 'Near Asokwa Police Station', 'Accra', 'Dansoman', NULL, NULL, 'Greater Accra', 'Paul Darko', '553344556', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(134, NULL, 'Bernard', 'Appiah', 'bernard.appiah@gmail.com', '207765432', '1991-01-20', 'Male', '2018-08-03', 'active', 'Behind Suame Magazine', 'Kumasi', 'Atonsu', NULL, NULL, 'Ashanti', 'Veronica Appiah', '204455667', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(135, NULL, 'Naomi', 'Kusi', 'naomi.kusi@gmail.com', '243344556', '2004-04-02', 'Female', '2023-09-18', 'active', 'Community 11 Junction', 'Nkawkaw', 'Asona', NULL, NULL, 'Eastern', 'Richard Kusi', '248899001', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(136, NULL, 'Patrick', 'Boamah', 'patrick.boamah@gmail.com', '542233445', '1990-09-29', 'Male', '2017-04-11', 'active', 'Effiduase Main Road', 'Takoradi', 'Anaji', NULL, NULL, 'Western', 'Mary Boamah', '246677990', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(137, NULL, 'Ruth', 'Addai', 'ruth.addai@gmail.com', '509988776', '1997-06-14', 'Female', '2025-12-30', 'active', 'Madina Zongo Junction', 'Ejisu', 'Fumesua', '', '', 'Ashanti', 'Joseph Addai', '501122443', 'member_6953360e142548.12943430.png', '2025-12-29 00:36:44', '2025-12-30 02:16:46'),
(138, NULL, 'Kelvin', 'Arthur', 'kelvin.arthur@gmail.com', '279988112', '1995-12-05', 'Male', '2020-10-01', 'active', 'Near Asokwa Police Station', 'Accra', 'Achimota', NULL, NULL, 'Greater Accra', 'Sandra Arthur', '276677889', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(139, NULL, 'Hannah', 'Boadu', 'hannah.boadu@gmail.com', '244433221', '2001-02-23', 'Female', '2023-06-04', 'active', 'Behind Suame Magazine', 'Techiman', 'Kenten', NULL, NULL, 'Bono East', 'James Boadu', '247788990', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(140, NULL, 'Samuel', 'Fosu', 'samuel.fosu@gmail.com', '506677889', '1993-07-18', 'Male', '2019-02-14', 'active', 'Community 11 Junction', 'Mankessim', 'Estate', NULL, NULL, 'Central', 'Agnes Fosu', '502211334', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(141, NULL, 'Esther', 'Yeboah', 'esther.yeboah@gmail.com', '551122334', '1999-10-30', 'Female', '2022-11-20', 'active', 'Effiduase Main Road', 'Kumasi', 'Ayeduase', NULL, NULL, 'Ashanti', 'Daniel Yeboah', '554455667', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(142, NULL, 'Isaac', 'Koranteng', 'isaac.koranteng@gmail.com', '248811223', '1989-08-16', 'Male', '2016-05-19', 'active', 'Madina Zongo Junction', 'Accra', 'Spintex', NULL, NULL, 'Greater Accra', 'Sarah Koranteng', '245566443', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(143, NULL, 'Lydia', 'Prempeh', 'lydia.prempeh@gmail.com', '204455667', '2000-01-09', 'Female', '2023-04-27', 'active', 'Near Asokwa Police Station', 'Kumasi', 'Tafo', NULL, NULL, 'Ashanti', 'Michael Prempeh', '209988221', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(144, NULL, 'Francis', 'Antwi', 'francis.antwi@gmail.com', '503344556', '1996-09-04', 'Male', '2021-08-15', 'active', 'Behind Suame Magazine', 'Kade', 'Market', NULL, NULL, 'Eastern', 'Lucy Antwi', '507788990', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(145, NULL, 'Joyce', 'Aidoo', 'joyce.aidoo@gmail.com', '556677889', '1994-03-28', 'Female', '2019-12-01', 'active', 'Community 11 Junction', 'Elmina', 'New Town', NULL, NULL, 'Central', 'Patrick Aidoo', '551122445', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(146, NULL, 'Dennis', 'Quartey', 'dennis.quartey@gmail.com', '245566778', '1992-11-15', 'Male', '2018-07-23', 'active', 'Effiduase Main Road', 'Accra', 'Osu', NULL, NULL, 'Greater Accra', 'Gloria Quartey', '249988776', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(147, NULL, 'Patricia', 'Nkrumah', 'patricia.nkrumah@gmail.com', '203344556', '2003-05-19', 'Female', '2024-03-02', 'active', 'Madina Zongo Junction', 'Konongo', 'Zongo', NULL, NULL, 'Ashanti', 'Thomas Nkrumah', '207788991', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(148, NULL, 'Robert', 'Baffour', 'robert.baffour@gmail.com', '272233445', '1991-06-07', 'Male', '2017-09-09', 'active', 'Near Asokwa Police Station', 'Kumasi', 'Santasi', NULL, NULL, 'Ashanti', 'Elizabeth Baffour', '275566778', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(149, NULL, 'Vivian', 'Asiedu', 'vivian.asiedu@gmail.com', '501122334', '1998-12-22', 'Female', '2022-05-18', 'active', 'Behind Suame Magazine', 'Suhum', 'Estate', NULL, NULL, 'Eastern', 'Daniel Asiedu', '509988775', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(150, NULL, 'Philip', 'Kwarteng', 'philip.kwarteng@gmail.com', '247788990', '1987-04-11', 'Male', '2015-02-06', 'active', 'Community 11 Junction', 'Bekwai', 'Central', NULL, NULL, 'Ashanti', 'Martha Kwarteng', '243344557', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(151, NULL, 'Sharon', 'Mensima', 'sharon.mensima@gmail.com', '553344667', '2002-08-26', 'Female', '2023-10-12', 'active', 'Effiduase Main Road', 'Accra', 'Kasoa', NULL, NULL, 'Greater Accra', 'Peter Mensima', '557788992', NULL, '2025-12-29 00:36:44', '2025-12-29 00:36:44'),
(152, NULL, 'Daniel', 'Mensah', 'daniel.mensah@gmail.com', '0245123890', '1998-04-12', 'Male', '2022-01-16', 'active', 'Madina Zongo Junction', 'Accra', 'Madina', NULL, NULL, 'Greater Accra', 'Grace Mensah', '0247894561', NULL, '2025-12-31 03:31:55', '2025-12-31 03:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `ministries`
--

CREATE TABLE `ministries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `leader_id` int(11) DEFAULT NULL,
  `leader_email` varchar(255) DEFAULT NULL,
  `meeting_day` varchar(20) DEFAULT NULL,
  `meeting_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ministries`
--

INSERT INTO `ministries` (`id`, `name`, `description`, `leader_id`, `leader_email`, `meeting_day`, `meeting_time`, `location`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Church Commitee', 'Default ministry for all church members', NULL, 'joe@gmail.com', 'sunday', '07:00:00', 'Church Premises', 'active', '2025-11-27 15:29:56', '2025-12-28 01:24:56'),
(2, 'Music Ministry', 'Our blessed musicians lead worship and create meaningful spiritual experiences.', NULL, 'joseph@gmail.com', 'saturday', '18:00:00', 'Church Premise', 'active', '2025-11-30 10:07:36', '2025-12-05 03:14:31'),
(3, 'Community Outreach', 'We serve our community through charity work and social justice initiatives.', NULL, 'james@cole.com', 'saturday', '07:00:00', 'Church Community', 'active', '2025-11-30 10:09:25', '2025-12-05 02:56:54'),
(6, 'Ushering Department', 'sdhjfkdfjskskdjkdsdsd', NULL, 'joe@doe.com', 'sunday', '21:12:00', 'Church Premise', 'active', '2025-12-23 18:09:45', '2025-12-23 18:09:45'),
(8, 'V2D', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-12-24 19:42:56', '2025-12-24 19:42:56'),
(11, 'Youth', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-12-28 23:46:01', '2025-12-28 23:46:01'),
(12, 'Children', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-12-28 23:46:01', '2025-12-28 23:46:01'),
(13, 'Choir', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-12-28 23:46:01', '2025-12-28 23:46:01'),
(14, 'Ushering', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-12-28 23:46:01', '2025-12-28 23:46:01'),
(15, 'Protocol', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-12-28 23:46:01', '2025-12-28 23:46:01'),
(16, 'Security', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-12-28 23:46:01', '2025-12-28 23:46:01'),
(17, 'Elders', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-12-28 23:46:01', '2025-12-28 23:46:01'),
(18, 'Media', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-12-31 03:31:55', '2025-12-31 03:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `ministry_members`
--

CREATE TABLE `ministry_members` (
  `id` int(11) NOT NULL,
  `ministry_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `joined_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ministry_members`
--

INSERT INTO `ministry_members` (`id`, `ministry_id`, `member_id`, `role`, `joined_date`, `created_at`) VALUES
(13, 1, 14, 'Member', '2025-12-23', '2025-12-23 12:44:21'),
(14, 3, 14, 'Member', '2025-12-23', '2025-12-23 12:44:21'),
(17, 2, 11, 'Member', '2025-12-23', '2025-12-23 12:45:28'),
(18, 3, 11, 'Member', '2025-12-23', '2025-12-23 12:45:28'),
(58, 1, 38, 'Member', '2025-12-23', '2025-12-28 01:30:42'),
(59, 6, 38, 'Member', '2025-12-23', '2025-12-28 01:30:42'),
(60, 1, 14, 'Member', '2025-12-23', '2025-12-28 01:30:42'),
(61, 3, 14, 'Member', '2025-12-23', '2025-12-28 01:30:42'),
(63, 2, 11, 'Member', '2025-12-23', '2025-12-28 01:30:42'),
(64, 3, 11, 'Member', '2025-12-23', '2025-12-28 01:30:42'),
(71, 1, 38, 'Member', '2025-12-23', '2025-12-28 01:36:00'),
(72, 6, 38, 'Member', '2025-12-23', '2025-12-28 01:36:00'),
(73, 1, 14, 'Member', '2025-12-23', '2025-12-28 01:36:00'),
(74, 3, 14, 'Member', '2025-12-23', '2025-12-28 01:36:00'),
(76, 2, 11, 'Member', '2025-12-23', '2025-12-28 01:36:00'),
(77, 3, 11, 'Member', '2025-12-23', '2025-12-28 01:36:00'),
(141, 12, 72, 'Member', '2023-03-10', '2025-12-29 00:08:30'),
(142, 13, 73, 'Member', '2021-11-05', '2025-12-29 00:08:30'),
(143, 14, 74, 'Member', '2022-07-21', '2025-12-29 00:08:30'),
(144, 15, 75, 'Member', '2020-05-30', '2025-12-29 00:08:30'),
(147, 12, 72, 'Member', '2023-03-10', '2025-12-29 00:24:41'),
(148, 13, 73, 'Member', '2021-11-05', '2025-12-29 00:24:41'),
(149, 14, 74, 'Member', '2022-07-21', '2025-12-29 00:24:41'),
(150, 15, 75, 'Member', '2020-05-30', '2025-12-29 00:24:41'),
(206, 12, 129, 'Member', '2024-01-07', '2025-12-29 00:36:44'),
(207, 16, 130, 'Member', '2019-10-12', '2025-12-29 00:36:44'),
(208, 13, 131, 'Member', '2020-06-22', '2025-12-29 00:36:44'),
(209, 11, 132, 'Member', '2024-02-15', '2025-12-29 00:36:44'),
(210, 14, 133, 'Member', '2021-12-09', '2025-12-29 00:36:44'),
(211, 15, 134, 'Member', '2018-08-03', '2025-12-29 00:36:44'),
(213, 16, 136, 'Member', '2017-04-11', '2025-12-29 00:36:44'),
(216, 13, 139, 'Member', '2023-06-04', '2025-12-29 00:36:44'),
(217, 15, 140, 'Member', '2019-02-14', '2025-12-29 00:36:44'),
(218, 14, 141, 'Member', '2022-11-20', '2025-12-29 00:36:44'),
(219, 17, 142, 'Member', '2016-05-19', '2025-12-29 00:36:44'),
(221, 11, 144, 'Member', '2021-08-15', '2025-12-29 00:36:44'),
(222, 13, 145, 'Member', '2019-12-01', '2025-12-29 00:36:44'),
(223, 16, 146, 'Member', '2018-07-23', '2025-12-29 00:36:44'),
(224, 12, 147, 'Member', '2024-03-02', '2025-12-29 00:36:44'),
(225, 15, 148, 'Member', '2017-09-09', '2025-12-29 00:36:44'),
(226, 14, 149, 'Member', '2022-05-18', '2025-12-29 00:36:44'),
(227, 17, 150, 'Member', '2015-02-06', '2025-12-29 00:36:44'),
(228, 11, 151, 'Member', '2023-10-12', '2025-12-29 00:36:44'),
(233, 12, 137, 'Member', '2025-12-30', '2025-12-30 02:16:46'),
(235, 2, 37, 'Member', '2025-12-30', '2025-12-30 02:19:19'),
(236, 3, 37, 'Member', '2025-12-30', '2025-12-30 02:19:19'),
(244, 3, 36, 'Member', '2025-12-30', '2025-12-30 04:28:36'),
(245, 12, 72, 'Member', '2023-03-10', '2025-12-30 12:26:00'),
(246, 13, 73, 'Member', '2021-11-05', '2025-12-30 12:26:00'),
(247, 14, 74, 'Member', '2022-07-21', '2025-12-30 12:26:00'),
(248, 15, 75, 'Member', '2020-05-30', '2025-12-30 12:26:00'),
(252, 11, 128, 'Member', '2025-12-31', '2025-12-31 03:28:11'),
(253, 18, 152, 'Member', '2022-01-16', '2025-12-31 03:31:55'),
(254, 11, 152, 'Member', '2022-01-16', '2025-12-31 03:31:55'),
(255, 12, 72, 'Member', '2023-03-10', '2025-12-31 03:31:55'),
(256, 13, 73, 'Member', '2021-11-05', '2025-12-31 03:31:55'),
(257, 14, 74, 'Member', '2022-07-21', '2025-12-31 03:31:55'),
(258, 15, 75, 'Member', '2020-05-30', '2025-12-31 03:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `category` enum('general','finance','members','attendance','events','system') DEFAULT 'general',
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `category`, `link`, `is_read`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 1, 'Members Imported', '1 members were successfully imported.', 'general', 'members.php', 1, '2025-12-24 21:24:32', '2025-12-24 21:57:35', NULL),
(3, 1, 'Members Imported', '2 members were successfully imported.', 'general', 'members.php', 1, '2025-12-24 22:04:44', '2025-12-24 22:05:11', NULL),
(4, 1, 'Members Imported', '2 members were successfully imported.', 'general', 'members.php', 1, '2025-12-24 22:11:13', '2025-12-24 22:28:26', NULL),
(5, 1, 'Member Deleted', '  was deleted.', 'general', 'members.php', 1, '2025-12-24 22:38:42', '2025-12-24 22:39:25', NULL),
(6, 1, 'Member Deleted', '  was deleted.', 'general', 'members.php', 1, '2025-12-24 22:39:54', '2025-12-24 22:41:11', NULL),
(7, 1, 'Member Updated', 'Adwoa Serwaa was updated.', 'general', 'members.php', 1, '2025-12-24 22:41:33', '2025-12-24 22:42:00', NULL),
(8, 1, 'Member Deleted', '  was deleted.', 'general', 'members.php', 1, '2025-12-24 22:42:56', '2025-12-24 22:43:59', NULL),
(9, 1, 'Member Updated', 'Ebenezer Quayson was updated.', 'general', 'members.php', 1, '2025-12-24 22:44:37', '2025-12-24 22:44:47', NULL),
(10, 1, 'Members Exported', 'Members data was exported.', 'general', 'members.php', 1, '2025-12-24 22:48:16', '2025-12-24 22:48:37', NULL),
(11, 1, 'Member Updated', 'Amy Chutti was updated.', 'general', 'members.php', 1, '2025-12-24 22:51:38', '2025-12-24 22:52:01', NULL),
(12, 1, 'New Income Recorded', 'An income of ¢500.00 was recorded.', 'general', 'donations.php', 1, '2025-12-24 22:56:14', '2025-12-24 22:57:52', NULL),
(13, 1, 'Income Updated', 'An income of ¢234.00 was updated.', 'general', 'donations.php', 1, '2025-12-24 22:56:46', '2025-12-24 22:57:52', NULL),
(14, 1, 'New Income Recorded', 'An income of ¢20.00 was recorded.', 'general', 'donations.php', 1, '2025-12-24 22:57:24', '2025-12-24 22:57:52', NULL),
(15, 1, 'Expense Updated', 'An expense of ¢500.00 was updated.', 'general', 'expenses.php', 1, '2025-12-24 23:00:31', '2025-12-24 23:02:45', NULL),
(16, 1, 'Expense Updated', 'An expense of ¢300.00 was updated.', 'general', 'expenses.php', 1, '2025-12-24 23:01:06', '2025-12-24 23:02:45', NULL),
(17, 1, 'Expense Added', 'A new expense of ¢700.00 was added.', 'general', 'expenses.php', 1, '2025-12-24 23:02:07', '2025-12-24 23:02:45', NULL),
(18, 1, 'Event Updated', 'The event \"Carols Night\" was updated.', 'general', 'events.php', 1, '2025-12-24 23:05:12', '2025-12-24 23:17:49', NULL),
(19, 1, 'Event Deleted', 'An event record was deleted.', 'general', 'events.php', 1, '2025-12-24 23:05:32', '2025-12-24 23:17:49', NULL),
(20, 1, 'Income Updated', 'An income of ¢500.00 was updated.', 'general', 'donations.php', 1, '2025-12-24 23:18:13', '2025-12-24 23:29:29', NULL),
(21, 1, 'Event Updated', 'The event \"Community Food Drive\" was updated.', 'general', 'events.php', 1, '2025-12-25 01:55:37', '2025-12-25 01:56:09', NULL),
(22, 1, 'Event Updated', 'The event \"New Year Prayers & Fasting\" was updated.', 'general', 'events.php', 1, '2025-12-25 01:55:46', '2025-12-25 01:56:09', NULL),
(23, 1, 'Event Updated', 'The event \"Christmas Celebration Service\" was updated.', 'general', 'events.php', 1, '2025-12-25 01:56:02', '2025-12-25 01:56:09', NULL),
(24, 1, 'New Income Recorded', 'An income of ¢500.00 was recorded.', 'general', 'donations.php', 1, '2025-12-25 16:34:55', '2025-12-25 17:09:44', NULL),
(25, 1, 'Income Deleted', 'An income record was deleted.', 'general', 'donations.php', 1, '2025-12-25 16:38:39', '2025-12-25 17:09:44', NULL),
(26, 1, 'Income Deleted', 'An income record was deleted.', 'general', 'donations.php', 1, '2025-12-25 16:40:20', '2025-12-25 17:09:44', NULL),
(27, 1, 'New Income Recorded', 'An income of ¢41.00 was recorded.', 'general', 'donations.php', 1, '2025-12-25 16:43:59', '2025-12-25 17:09:44', NULL),
(28, 1, 'New Income Recorded', 'An income of ¢5,000.00 was recorded.', 'general', 'donations.php', 1, '2025-12-25 16:48:18', '2025-12-25 17:09:44', NULL),
(29, 1, 'Income Deleted', 'An income record was deleted.', 'general', 'donations.php', 1, '2025-12-25 17:09:18', '2025-12-25 17:09:44', NULL),
(30, 1, 'Income Deleted', 'An income record was deleted.', 'general', 'donations.php', 1, '2025-12-25 17:09:32', '2025-12-25 17:09:44', NULL),
(31, 1, 'Income Updated', 'An income of ¢210.00 was updated.', 'general', 'donations.php', 1, '2025-12-25 17:11:07', '2025-12-25 17:13:21', NULL),
(32, 1, 'Income Updated', 'An income of ¢500.00 was updated.', 'general', 'donations.php', 1, '2025-12-25 17:11:25', '2025-12-25 17:13:21', NULL),
(33, 1, 'New Income Recorded', 'An income of ¢200.00 was recorded.', 'general', 'donations.php', 1, '2025-12-25 17:43:28', '2025-12-25 18:27:30', NULL),
(34, 1, 'Income Updated', 'An income of ¢200.00 was updated.', 'general', 'donations.php', 1, '2025-12-25 17:46:12', '2025-12-25 18:27:30', NULL),
(35, 1, 'Income Updated', 'An income of ¢225.00 was updated.', 'general', 'donations.php', 1, '2025-12-25 18:05:26', '2025-12-25 18:27:30', NULL),
(36, 1, 'New Income Recorded', 'An income of ¢1,200.00 was recorded.', 'general', 'donations.php', 1, '2025-12-25 18:08:35', '2025-12-25 18:27:30', NULL),
(37, 1, 'Income Updated', 'An income of ¢120.00 was updated.', 'general', 'donations.php', 1, '2025-12-25 18:12:49', '2025-12-25 18:27:30', NULL),
(38, 1, 'Income Updated', 'An income of ¢690.00 was updated.', 'general', 'donations.php', 1, '2025-12-25 18:13:24', '2025-12-25 18:27:30', NULL),
(39, 1, 'Income Deleted', 'An income record was deleted.', 'general', 'donations.php', 1, '2025-12-25 20:51:02', '2025-12-25 21:01:30', NULL),
(40, 1, 'Income Deleted', 'An income record was deleted.', 'general', 'donations.php', 1, '2025-12-25 20:55:48', '2025-12-25 21:01:30', NULL),
(41, 1, 'Income Deleted', 'An income record was deleted.', 'general', 'donations.php', 1, '2025-12-25 20:56:14', '2025-12-25 21:01:30', NULL),
(42, 1, 'Income Deleted', 'An income record was deleted.', 'general', 'donations.php', 1, '2025-12-25 21:01:18', '2025-12-25 21:01:30', NULL),
(43, 1, 'Expense Updated', 'An expense of ¢500.00 was updated.', 'general', 'expenses.php', 1, '2025-12-25 21:41:06', '2025-12-25 22:59:56', NULL),
(44, 1, 'Income Updated', 'An income of ¢210.00 was updated.', 'general', 'donations.php', 1, '2025-12-27 20:23:11', '2025-12-27 20:30:10', NULL),
(45, 1, 'Member Updated', 'Amy Chutti was updated.', 'general', 'members.php', 1, '2025-12-27 20:30:58', '2025-12-27 20:32:35', NULL),
(46, 1, 'New Income Recorded', 'An income of ¢3,200.00 was recorded.', 'general', 'donations.php', 1, '2025-12-27 23:57:25', '2025-12-27 23:59:00', NULL),
(47, 1, 'New Income Recorded', 'An income of ¢3,200.00 was recorded.', 'general', 'donations.php', 1, '2025-12-27 23:57:25', '2025-12-27 23:59:00', NULL),
(48, 2, 'New Income Recorded', 'An income of ¢2,000.00 was recorded.', 'general', 'donations.php', 1, '2025-12-28 00:04:32', '2025-12-28 00:04:39', NULL),
(49, 2, 'New Income Recorded', 'An income of ¢2,000.00 was recorded.', 'general', 'donations.php', 1, '2025-12-28 00:04:32', '2025-12-28 00:04:39', NULL),
(50, 1, 'New Income Recorded', 'An income of ¢1,500.00 was recorded.', 'general', 'donations.php', 1, '2025-12-28 00:12:09', '2025-12-28 00:12:23', NULL),
(51, 2, 'New Income Recorded', 'An income of ¢1,500.00 was recorded.', 'general', 'donations.php', 1, '2025-12-28 00:12:09', '2025-12-28 00:12:24', NULL),
(52, 1, 'New Income Recorded', 'An income of ¢690.00 was recorded.', 'general', 'donations.php', 1, '2025-12-28 00:28:31', '2025-12-28 00:28:45', NULL),
(53, 2, 'New Income Recorded', 'An income of ¢690.00 was recorded.', 'general', 'donations.php', 1, '2025-12-28 00:28:31', '2025-12-28 00:29:04', NULL),
(54, 1, 'Income Updated', 'An income of ¢210.00 was updated.', 'general', 'donations.php', 1, '2025-12-28 00:39:45', '2025-12-28 00:39:52', NULL),
(55, 2, 'Income Updated', 'An income of ¢210.00 was updated.', 'general', 'donations.php', 1, '2025-12-28 00:39:45', '2025-12-28 00:39:56', NULL),
(56, 1, 'Expense Added', 'A new expense of ¢5,000.00 was added.', 'general', 'expenses.php', 1, '2025-12-28 00:57:22', '2025-12-28 00:57:39', NULL),
(57, 2, 'Expense Added', 'A new expense of ¢5,000.00 was added.', 'general', 'expenses.php', 1, '2025-12-28 00:57:22', '2025-12-28 00:57:26', NULL),
(58, 1, 'Expense Updated', 'An expense of ¢750.00 was updated.', 'general', NULL, 1, '2025-12-28 01:05:26', '2025-12-28 01:05:40', NULL),
(59, 2, 'Expense Updated', 'An expense of ¢750.00 was updated.', 'general', NULL, 1, '2025-12-28 01:05:26', '2025-12-28 01:05:34', NULL),
(60, 1, 'Income Updated', 'An income of ¢210.00 was updated.', 'general', NULL, 1, '2025-12-28 01:06:31', '2025-12-28 01:06:34', NULL),
(61, 2, 'Income Updated', 'An income of ¢210.00 was updated.', 'general', NULL, 1, '2025-12-28 01:06:31', '2025-12-28 01:06:56', NULL),
(62, 1, 'Expense Updated', 'An expense of ¢750.00 was updated.', 'general', NULL, 1, '2025-12-28 01:09:21', '2025-12-28 01:09:34', NULL),
(63, 2, 'Expense Updated', 'An expense of ¢750.00 was updated.', 'general', NULL, 1, '2025-12-28 01:09:21', '2025-12-28 01:09:28', NULL),
(64, 1, 'Expense Updated', 'An expense of ¢4,500.00 was updated.', 'general', NULL, 1, '2025-12-28 01:12:00', '2025-12-29 00:47:14', NULL),
(65, 2, 'Expense Updated', 'An expense of ¢4,500.00 was updated.', 'general', NULL, 1, '2025-12-28 01:12:00', '2025-12-28 01:15:39', NULL),
(66, 1, 'Expense Added', 'A new expense of ¢1,200.00 was added.', 'general', NULL, 1, '2025-12-28 01:12:36', '2025-12-29 00:47:14', NULL),
(67, 2, 'Expense Added', 'A new expense of ¢1,200.00 was added.', 'general', NULL, 1, '2025-12-28 01:12:36', '2025-12-28 01:15:39', NULL),
(68, 1, 'Expense Updated', 'An expense of ¢1,200.00 was updated.', 'general', NULL, 1, '2025-12-28 01:13:09', '2025-12-29 00:47:14', NULL),
(69, 2, 'Expense Updated', 'An expense of ¢1,200.00 was updated.', 'general', NULL, 1, '2025-12-28 01:13:09', '2025-12-28 01:15:39', NULL),
(70, 1, 'New Income Recorded', 'An income of ¢3,050.00 was recorded.', 'general', NULL, 1, '2025-12-28 01:13:59', '2025-12-29 00:47:14', NULL),
(71, 2, 'New Income Recorded', 'An income of ¢3,050.00 was recorded.', 'general', NULL, 1, '2025-12-28 01:13:59', '2025-12-28 01:15:39', NULL),
(72, 1, 'New Income Recorded', 'An income of ¢1,250.00 was recorded.', 'general', NULL, 1, '2025-12-28 01:15:24', '2025-12-29 00:47:14', NULL),
(73, 2, 'New Income Recorded', 'An income of ¢1,250.00 was recorded.', 'general', NULL, 1, '2025-12-28 01:15:24', '2025-12-28 01:15:39', NULL),
(74, 1, 'Member Updated', 'Amy Chutti was updated.', 'general', NULL, 1, '2025-12-28 01:29:23', '2025-12-29 00:47:14', NULL),
(75, 2, 'Member Updated', 'Amy Chutti was updated.', 'general', NULL, 1, '2025-12-28 01:29:23', '2025-12-28 01:29:30', NULL),
(76, 1, 'Members Exported', 'Members data was exported.', 'general', NULL, 1, '2025-12-28 01:29:42', '2025-12-29 00:47:14', NULL),
(77, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-28 01:30:03', '2025-12-29 00:47:14', NULL),
(78, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-28 01:30:03', '2025-12-28 01:30:03', NULL),
(79, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-28 01:30:06', '2025-12-29 00:47:14', NULL),
(80, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-28 01:30:06', '2025-12-28 01:30:06', NULL),
(81, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-28 01:30:10', '2025-12-29 00:47:14', NULL),
(82, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-28 01:30:10', '2025-12-28 01:30:10', NULL),
(83, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-28 01:30:13', '2025-12-29 00:47:14', NULL),
(84, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-28 01:30:13', '2025-12-28 01:30:13', NULL),
(85, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-28 01:30:16', '2025-12-29 00:47:14', NULL),
(86, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-28 01:30:16', '2025-12-28 01:30:16', NULL),
(87, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-28 01:35:37', '2025-12-29 00:47:14', NULL),
(88, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-28 01:35:37', '2025-12-28 01:35:37', NULL),
(89, 1, 'Members Imported', '7 members were successfully imported.', 'general', NULL, 1, '2025-12-28 01:36:00', '2025-12-29 00:47:14', NULL),
(90, 2, 'Members Imported', '7 members were successfully imported.', 'general', NULL, 0, '2025-12-28 01:36:00', '2025-12-28 01:36:00', NULL),
(91, 1, 'Event Deleted', 'An event record was deleted.', 'general', NULL, 1, '2025-12-28 01:47:43', '2025-12-29 00:47:14', NULL),
(92, 2, 'Event Deleted', 'An event record was deleted.', 'general', NULL, 0, '2025-12-28 01:47:43', '2025-12-28 01:47:43', NULL),
(93, 1, 'Event Deleted', 'An event record was deleted.', 'general', NULL, 1, '2025-12-28 01:47:50', '2025-12-29 00:47:14', NULL),
(94, 2, 'Event Deleted', 'An event record was deleted.', 'general', NULL, 0, '2025-12-28 01:47:50', '2025-12-28 01:47:50', NULL),
(95, 1, 'Members Imported', '30 members were successfully imported.', 'general', NULL, 1, '2025-12-28 23:34:20', '2025-12-29 00:47:14', NULL),
(96, 2, 'Members Imported', '30 members were successfully imported.', 'general', NULL, 0, '2025-12-28 23:34:20', '2025-12-28 23:34:20', NULL),
(97, 1, 'Members Imported', '30 members were successfully imported.', 'general', NULL, 1, '2025-12-28 23:46:01', '2025-12-29 00:47:14', NULL),
(98, 2, 'Members Imported', '30 members were successfully imported.', 'general', NULL, 0, '2025-12-28 23:46:01', '2025-12-28 23:46:01', NULL),
(99, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-28 23:57:04', '2025-12-29 00:47:14', NULL),
(100, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-28 23:57:04', '2025-12-28 23:57:04', NULL),
(101, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-28 23:59:11', '2025-12-29 00:47:14', NULL),
(102, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-28 23:59:11', '2025-12-28 23:59:11', NULL),
(103, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-28 23:59:26', '2025-12-29 00:47:14', NULL),
(104, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-28 23:59:26', '2025-12-28 23:59:26', NULL),
(105, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-28 23:59:37', '2025-12-29 00:47:14', NULL),
(106, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-28 23:59:37', '2025-12-28 23:59:37', NULL),
(107, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-28 23:59:42', '2025-12-29 00:47:14', NULL),
(108, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-28 23:59:42', '2025-12-28 23:59:42', NULL),
(109, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-28 23:59:58', '2025-12-29 00:47:14', NULL),
(110, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-28 23:59:58', '2025-12-28 23:59:58', NULL),
(111, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:00:02', '2025-12-29 00:47:14', NULL),
(112, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:00:02', '2025-12-29 00:00:02', NULL),
(113, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:00:06', '2025-12-29 00:47:14', NULL),
(114, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:00:06', '2025-12-29 00:00:06', NULL),
(115, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:00:11', '2025-12-29 00:47:14', NULL),
(116, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:00:11', '2025-12-29 00:00:11', NULL),
(117, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:00:15', '2025-12-29 00:47:14', NULL),
(118, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:00:15', '2025-12-29 00:00:15', NULL),
(119, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:00:19', '2025-12-29 00:47:14', NULL),
(120, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:00:19', '2025-12-29 00:00:19', NULL),
(121, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:00:24', '2025-12-29 00:47:14', NULL),
(122, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:00:24', '2025-12-29 00:00:24', NULL),
(123, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:00:28', '2025-12-29 00:47:14', NULL),
(124, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:00:28', '2025-12-29 00:00:28', NULL),
(125, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:00:32', '2025-12-29 00:47:14', NULL),
(126, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:00:32', '2025-12-29 00:00:32', NULL),
(127, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:00:47', '2025-12-29 00:47:14', NULL),
(128, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:00:47', '2025-12-29 00:00:47', NULL),
(129, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:00', '2025-12-29 00:47:14', NULL),
(130, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:00', '2025-12-29 00:01:00', NULL),
(131, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:04', '2025-12-29 00:47:14', NULL),
(132, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:04', '2025-12-29 00:01:04', NULL),
(133, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:10', '2025-12-29 00:47:14', NULL),
(134, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:10', '2025-12-29 00:01:10', NULL),
(135, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:13', '2025-12-29 00:47:14', NULL),
(136, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:13', '2025-12-29 00:01:13', NULL),
(137, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:19', '2025-12-29 00:47:14', NULL),
(138, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:19', '2025-12-29 00:01:19', NULL),
(139, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:23', '2025-12-29 00:47:14', NULL),
(140, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:23', '2025-12-29 00:01:23', NULL),
(141, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:28', '2025-12-29 00:47:14', NULL),
(142, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:28', '2025-12-29 00:01:28', NULL),
(143, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:34', '2025-12-29 00:47:14', NULL),
(144, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:34', '2025-12-29 00:01:34', NULL),
(145, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:37', '2025-12-29 00:47:14', NULL),
(146, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:37', '2025-12-29 00:01:37', NULL),
(147, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:41', '2025-12-29 00:47:14', NULL),
(148, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:41', '2025-12-29 00:01:41', NULL),
(149, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:46', '2025-12-29 00:47:14', NULL),
(150, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:46', '2025-12-29 00:01:46', NULL),
(151, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:50', '2025-12-29 00:47:14', NULL),
(152, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:50', '2025-12-29 00:01:50', NULL),
(153, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:53', '2025-12-29 00:47:14', NULL),
(154, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:53', '2025-12-29 00:01:53', NULL),
(155, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:01:57', '2025-12-29 00:47:14', NULL),
(156, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:01:57', '2025-12-29 00:01:57', NULL),
(157, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:02:00', '2025-12-29 00:47:14', NULL),
(158, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:02:00', '2025-12-29 00:02:00', NULL),
(159, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:02:04', '2025-12-29 00:47:14', NULL),
(160, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:02:04', '2025-12-29 00:02:04', NULL),
(161, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:02:08', '2025-12-29 00:47:14', NULL),
(162, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:02:08', '2025-12-29 00:02:08', NULL),
(163, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:02:13', '2025-12-29 00:47:14', NULL),
(164, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:02:13', '2025-12-29 00:02:13', NULL),
(165, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:02:27', '2025-12-29 00:47:14', NULL),
(166, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:02:27', '2025-12-29 00:02:27', NULL),
(167, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:06:57', '2025-12-29 00:47:14', NULL),
(168, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:06:57', '2025-12-29 00:06:57', NULL),
(169, 1, 'Members Imported', '5 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:08:30', '2025-12-29 00:47:14', NULL),
(170, 2, 'Members Imported', '5 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:08:30', '2025-12-29 00:08:30', NULL),
(171, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:13:10', '2025-12-29 00:47:14', NULL),
(172, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:13:10', '2025-12-29 00:13:10', NULL),
(173, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:14:00', '2025-12-29 00:47:14', NULL),
(174, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:14:00', '2025-12-29 00:14:00', NULL),
(175, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:19:22', '2025-12-29 00:47:14', NULL),
(176, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:19:22', '2025-12-29 00:19:22', NULL),
(177, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:23:59', '2025-12-29 00:47:14', NULL),
(178, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:23:59', '2025-12-29 00:23:59', NULL),
(179, 1, 'Members Imported', '30 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:24:41', '2025-12-29 00:47:14', NULL),
(180, 2, 'Members Imported', '30 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:24:41', '2025-12-29 00:24:41', NULL),
(181, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:27:23', '2025-12-29 00:47:14', NULL),
(182, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:27:23', '2025-12-29 00:27:23', NULL),
(183, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:28:35', '2025-12-29 00:47:14', NULL),
(184, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:28:35', '2025-12-29 00:28:35', NULL),
(185, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:28:48', '2025-12-29 00:47:14', NULL),
(186, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:28:48', '2025-12-29 00:28:48', NULL),
(187, 1, 'Members Imported', '1 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:29:02', '2025-12-29 00:47:14', NULL),
(188, 2, 'Members Imported', '1 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:29:02', '2025-12-29 00:29:02', NULL),
(189, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:30:13', '2025-12-29 00:47:14', NULL),
(190, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:30:13', '2025-12-29 00:30:13', NULL),
(191, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:30:18', '2025-12-29 00:47:14', NULL),
(192, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:30:18', '2025-12-29 00:30:18', NULL),
(193, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:30:23', '2025-12-29 00:47:14', NULL),
(194, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:30:23', '2025-12-29 00:30:23', NULL),
(195, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:30:27', '2025-12-29 00:47:14', NULL),
(196, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:30:27', '2025-12-29 00:30:27', NULL),
(197, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:30:31', '2025-12-29 00:47:14', NULL),
(198, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:30:31', '2025-12-29 00:30:31', NULL),
(199, 1, 'Members Imported', '25 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:32:13', '2025-12-29 00:47:14', NULL),
(200, 2, 'Members Imported', '25 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:32:13', '2025-12-29 00:32:13', NULL),
(201, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 00:36:23', '2025-12-29 00:47:14', NULL),
(202, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 00:36:23', '2025-12-29 00:36:23', NULL),
(203, 1, 'Members Imported', '26 members were successfully imported.', 'general', NULL, 1, '2025-12-29 00:36:44', '2025-12-29 00:47:14', NULL),
(204, 2, 'Members Imported', '26 members were successfully imported.', 'general', NULL, 0, '2025-12-29 00:36:44', '2025-12-29 00:36:44', NULL),
(205, 1, 'Organization Deleted', 'An organization was deleted.', 'general', NULL, 1, '2025-12-29 00:37:25', '2025-12-29 00:47:14', NULL),
(206, 2, 'Organization Deleted', 'An organization was deleted.', 'general', NULL, 0, '2025-12-29 00:37:25', '2025-12-29 00:37:25', NULL),
(207, 1, 'Expense Deleted', 'An expense record was deleted.', 'general', NULL, 1, '2025-12-29 01:30:47', '2025-12-29 01:34:44', NULL),
(208, 2, 'Expense Deleted', 'An expense record was deleted.', 'general', NULL, 0, '2025-12-29 01:30:47', '2025-12-29 01:30:47', NULL),
(209, 1, 'Expense Updated', 'An expense of ¢300.00 was updated.', 'general', NULL, 1, '2025-12-29 01:39:30', '2025-12-31 01:09:30', NULL),
(210, 2, 'Expense Updated', 'An expense of ¢300.00 was updated.', 'general', NULL, 0, '2025-12-29 01:39:30', '2025-12-29 01:39:30', NULL),
(211, 1, 'Expense Added', 'A new expense of ¢540.00 was added.', 'general', NULL, 1, '2025-12-29 01:40:30', '2025-12-31 01:09:30', NULL),
(212, 2, 'Expense Added', 'A new expense of ¢540.00 was added.', 'general', NULL, 0, '2025-12-29 01:40:30', '2025-12-29 01:40:30', NULL),
(213, 1, 'Income Updated', 'An income of ¢120.00 was updated.', 'general', NULL, 1, '2025-12-29 01:56:58', '2025-12-31 01:09:30', NULL),
(214, 2, 'Income Updated', 'An income of ¢120.00 was updated.', 'general', NULL, 0, '2025-12-29 01:56:58', '2025-12-29 01:56:58', NULL),
(215, 1, 'Income Updated', 'An income of ¢1,250.00 was updated.', 'general', NULL, 1, '2025-12-29 01:57:19', '2025-12-31 01:09:30', NULL),
(216, 2, 'Income Updated', 'An income of ¢1,250.00 was updated.', 'general', NULL, 0, '2025-12-29 01:57:19', '2025-12-29 01:57:19', NULL),
(217, 1, 'Income Updated', 'An income of ¢3,200.00 was updated.', 'general', NULL, 1, '2025-12-29 01:57:41', '2025-12-31 01:09:30', NULL),
(218, 2, 'Income Updated', 'An income of ¢3,200.00 was updated.', 'general', NULL, 0, '2025-12-29 01:57:41', '2025-12-29 01:57:41', NULL),
(219, 1, 'Event Updated', 'The event \"31st Watch Night\" was updated.', 'general', NULL, 1, '2025-12-29 02:18:10', '2025-12-31 01:09:30', NULL),
(220, 2, 'Event Updated', 'The event \"31st Watch Night\" was updated.', 'general', NULL, 0, '2025-12-29 02:18:10', '2025-12-29 02:18:10', NULL),
(221, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 21:50:29', '2025-12-31 01:09:30', NULL),
(222, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 21:50:29', '2025-12-29 21:50:29', NULL),
(223, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-29 21:50:49', '2025-12-31 01:09:30', NULL),
(224, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-29 21:50:49', '2025-12-29 21:50:49', NULL),
(225, 1, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 1, '2025-12-30 01:52:13', '2025-12-31 01:09:30', NULL),
(226, 2, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 0, '2025-12-30 01:52:13', '2025-12-30 01:52:13', NULL),
(227, 1, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 1, '2025-12-30 01:53:05', '2025-12-31 01:09:30', NULL),
(228, 2, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 0, '2025-12-30 01:53:05', '2025-12-30 01:53:05', NULL),
(229, 1, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 1, '2025-12-30 01:53:32', '2025-12-31 01:09:30', NULL),
(230, 2, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 0, '2025-12-30 01:53:32', '2025-12-30 01:53:32', NULL),
(231, 1, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 1, '2025-12-30 02:11:47', '2025-12-31 01:09:30', NULL),
(232, 2, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 0, '2025-12-30 02:11:47', '2025-12-30 02:11:47', NULL),
(233, 1, 'Member Updated', 'Ruth Addai was updated.', 'general', NULL, 1, '2025-12-30 02:16:46', '2025-12-31 01:09:30', NULL),
(234, 2, 'Member Updated', 'Ruth Addai was updated.', 'general', NULL, 0, '2025-12-30 02:16:46', '2025-12-30 02:16:46', NULL),
(235, 1, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 1, '2025-12-30 02:18:02', '2025-12-31 01:09:30', NULL),
(236, 2, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 0, '2025-12-30 02:18:02', '2025-12-30 02:18:02', NULL),
(237, 1, 'Member Updated', 'Ebenezer Quayson was updated.', 'general', NULL, 1, '2025-12-30 02:19:19', '2025-12-31 01:09:30', NULL),
(238, 2, 'Member Updated', 'Ebenezer Quayson was updated.', 'general', NULL, 0, '2025-12-30 02:19:19', '2025-12-30 02:19:19', NULL),
(239, 1, 'Member Updated', 'Daniel Mensah was updated.', 'general', NULL, 1, '2025-12-30 02:25:55', '2025-12-31 01:09:30', NULL),
(240, 2, 'Member Updated', 'Daniel Mensah was updated.', 'general', NULL, 0, '2025-12-30 02:25:55', '2025-12-30 02:25:55', NULL),
(241, 1, 'Member Updated', 'Daniel Mensah was updated.', 'general', NULL, 1, '2025-12-30 02:26:14', '2025-12-31 01:09:30', NULL),
(242, 2, 'Member Updated', 'Daniel Mensah was updated.', 'general', NULL, 0, '2025-12-30 02:26:14', '2025-12-30 02:26:14', NULL),
(243, 1, 'Member Updated', 'Daniel Mensah was updated.', 'general', NULL, 1, '2025-12-30 02:39:24', '2025-12-31 01:09:30', NULL),
(244, 2, 'Member Updated', 'Daniel Mensah was updated.', 'general', NULL, 0, '2025-12-30 02:39:24', '2025-12-30 02:39:24', NULL),
(245, 1, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 1, '2025-12-30 02:39:46', '2025-12-31 01:09:30', NULL),
(246, 2, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 0, '2025-12-30 02:39:46', '2025-12-30 02:39:46', NULL),
(247, 1, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 1, '2025-12-30 04:28:36', '2025-12-31 01:09:30', NULL),
(248, 2, 'Member Updated', 'Asantewaa Agyeiwaa was updated.', 'general', NULL, 0, '2025-12-30 04:28:36', '2025-12-30 04:28:36', NULL),
(249, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 1, '2025-12-30 12:24:59', '2025-12-31 01:09:30', NULL),
(250, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-30 12:24:59', '2025-12-30 12:24:59', NULL),
(251, 1, 'Members Imported', '4 members were successfully imported.', 'general', NULL, 1, '2025-12-30 12:26:00', '2025-12-31 01:09:30', NULL),
(252, 2, 'Members Imported', '4 members were successfully imported.', 'general', NULL, 0, '2025-12-30 12:26:00', '2025-12-30 12:26:00', NULL),
(253, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-30 20:23:18', '2025-12-31 01:09:30', NULL),
(254, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-30 20:23:18', '2025-12-30 20:23:18', NULL),
(255, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-30 20:31:48', '2025-12-31 01:09:30', NULL),
(256, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-30 20:31:48', '2025-12-30 20:31:48', NULL),
(257, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-30 20:33:51', '2025-12-31 01:09:30', NULL),
(258, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-30 20:33:51', '2025-12-30 20:33:51', NULL),
(259, 1, 'Income Deleted', 'An income record was deleted.', 'general', NULL, 1, '2025-12-30 20:50:07', '2025-12-31 01:09:30', NULL),
(260, 2, 'Income Deleted', 'An income record was deleted.', 'general', NULL, 0, '2025-12-30 20:50:07', '2025-12-30 20:50:07', NULL),
(261, 1, 'Income Deleted', 'An income record was deleted.', 'general', NULL, 1, '2025-12-30 20:50:19', '2025-12-31 01:09:30', NULL),
(262, 2, 'Income Deleted', 'An income record was deleted.', 'general', NULL, 0, '2025-12-30 20:50:19', '2025-12-30 20:50:19', NULL),
(263, 1, 'Income Deleted', 'An income record was deleted.', 'general', NULL, 1, '2025-12-30 20:50:35', '2025-12-31 01:09:30', NULL),
(264, 2, 'Income Deleted', 'An income record was deleted.', 'general', NULL, 0, '2025-12-30 20:50:35', '2025-12-30 20:50:35', NULL),
(265, 1, 'Expense Deleted', 'An expense record was deleted.', 'general', NULL, 1, '2025-12-30 20:50:52', '2025-12-31 01:09:30', NULL),
(266, 2, 'Expense Deleted', 'An expense record was deleted.', 'general', NULL, 0, '2025-12-30 20:50:52', '2025-12-30 20:50:52', NULL),
(267, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-30 21:01:16', '2025-12-31 01:09:30', NULL),
(268, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-30 21:01:16', '2025-12-30 21:01:16', NULL),
(269, 1, 'Expense Deleted', 'An expense record was deleted.', 'general', NULL, 1, '2025-12-30 21:01:39', '2025-12-31 01:09:30', NULL),
(270, 2, 'Expense Deleted', 'An expense record was deleted.', 'general', NULL, 0, '2025-12-30 21:01:39', '2025-12-30 21:01:39', NULL),
(271, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-30 21:05:42', '2025-12-31 01:09:30', NULL),
(272, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-30 21:05:42', '2025-12-30 21:05:42', NULL),
(273, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-30 21:17:08', '2025-12-31 01:09:30', NULL),
(274, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-30 21:17:08', '2025-12-30 21:17:08', NULL),
(275, 1, 'Organization Deleted', 'An organization was deleted.', 'general', NULL, 1, '2025-12-30 21:34:21', '2025-12-31 01:09:30', NULL),
(276, 2, 'Organization Deleted', 'An organization was deleted.', 'general', NULL, 0, '2025-12-30 21:34:22', '2025-12-30 21:34:22', NULL),
(277, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-30 21:34:39', '2025-12-31 01:09:30', NULL),
(278, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-30 21:34:39', '2025-12-30 21:34:39', NULL),
(279, 1, 'Member Updated', 'Dan Mensah was updated.', 'general', NULL, 1, '2025-12-30 22:04:29', '2025-12-31 01:09:30', NULL),
(280, 2, 'Member Updated', 'Dan Mensah was updated.', 'general', NULL, 0, '2025-12-30 22:04:29', '2025-12-30 22:04:29', NULL),
(281, 1, 'Member Updated', 'Linda Amankwah was updated.', 'general', NULL, 1, '2025-12-30 22:04:49', '2025-12-31 01:09:30', NULL),
(282, 2, 'Member Updated', 'Linda Amankwah was updated.', 'general', NULL, 0, '2025-12-30 22:04:49', '2025-12-30 22:04:49', NULL),
(283, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-30 22:26:32', '2025-12-31 01:09:30', NULL),
(284, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-30 22:26:32', '2025-12-30 22:26:32', NULL),
(285, 1, 'Member Deleted', 'A member was deleted.', 'general', NULL, 1, '2025-12-30 23:03:22', '2025-12-31 01:09:30', NULL),
(286, 2, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-30 23:03:22', '2025-12-30 23:03:22', NULL),
(287, 3, 'Member Deleted', 'A member was deleted.', 'general', NULL, 0, '2025-12-30 23:03:22', '2025-12-30 23:03:22', NULL),
(288, 1, 'Member Updated', 'Emmanuella Tetteh was updated.', 'general', NULL, 0, '2025-12-31 03:28:01', '2025-12-31 03:28:01', NULL),
(289, 2, 'Member Updated', 'Emmanuella Tetteh was updated.', 'general', NULL, 0, '2025-12-31 03:28:01', '2025-12-31 03:28:01', NULL),
(290, 3, 'Member Updated', 'Emmanuella Tetteh was updated.', 'general', NULL, 0, '2025-12-31 03:28:01', '2025-12-31 03:28:01', NULL),
(291, 1, 'Member Updated', 'Emmanuel Tetteh was updated.', 'general', NULL, 0, '2025-12-31 03:28:11', '2025-12-31 03:28:11', NULL),
(292, 2, 'Member Updated', 'Emmanuel Tetteh was updated.', 'general', NULL, 0, '2025-12-31 03:28:11', '2025-12-31 03:28:11', NULL),
(293, 3, 'Member Updated', 'Emmanuel Tetteh was updated.', 'general', NULL, 0, '2025-12-31 03:28:11', '2025-12-31 03:28:11', NULL),
(294, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-31 03:31:30', '2025-12-31 03:31:30', NULL),
(295, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-31 03:31:30', '2025-12-31 03:31:30', NULL),
(296, 3, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-31 03:31:30', '2025-12-31 03:31:30', NULL),
(297, 1, 'Members Imported', '5 members were successfully imported.', 'general', NULL, 0, '2025-12-31 03:31:55', '2025-12-31 03:31:55', NULL),
(298, 2, 'Members Imported', '5 members were successfully imported.', 'general', NULL, 0, '2025-12-31 03:31:55', '2025-12-31 03:31:55', NULL),
(299, 3, 'Members Imported', '5 members were successfully imported.', 'general', NULL, 0, '2025-12-31 03:31:55', '2025-12-31 03:31:55', NULL),
(300, 1, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:34:09', '2025-12-31 03:34:09', NULL),
(301, 2, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:34:09', '2025-12-31 03:34:09', NULL),
(302, 3, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:34:09', '2025-12-31 03:34:09', NULL),
(303, 1, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:35:43', '2025-12-31 03:35:43', NULL),
(304, 2, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:35:43', '2025-12-31 03:35:43', NULL),
(305, 3, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:35:43', '2025-12-31 03:35:43', NULL),
(306, 1, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-31 03:39:11', '2025-12-31 03:39:11', NULL),
(307, 2, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-31 03:39:11', '2025-12-31 03:39:11', NULL),
(308, 3, 'Members Imported', '0 members were successfully imported.', 'general', NULL, 0, '2025-12-31 03:39:11', '2025-12-31 03:39:11', NULL),
(309, 1, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:39:32', '2025-12-31 03:39:32', NULL),
(310, 2, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:39:32', '2025-12-31 03:39:32', NULL),
(311, 3, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:39:32', '2025-12-31 03:39:32', NULL),
(312, 1, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:49:20', '2025-12-31 03:49:20', NULL),
(313, 2, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:49:20', '2025-12-31 03:49:20', NULL),
(314, 3, 'Members Exported', 'Members data was exported.', 'general', NULL, 0, '2025-12-31 03:49:20', '2025-12-31 03:49:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Admin', 'Administrator with full access', '2025-11-26 23:13:02'),
(2, 'Pastor', 'Church pastor', '2025-11-26 23:13:02'),
(3, 'Leader', 'Ministry leader', '2025-11-26 23:13:02'),
(4, 'Member', 'Church member', '2025-11-26 23:13:02'),
(5, 'Staff', 'Church staff member', '2025-11-26 23:13:02');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'church_name', 'The Methodist Ghana', 'Church name', '2025-11-26 23:13:02', '2025-12-31 00:36:27'),
(2, 'church_address', 'West Hills', 'Church address', '2025-11-26 23:13:02', '2025-12-31 00:36:27'),
(3, 'church_phone', '23456321', 'Church phone', '2025-11-26 23:13:02', '2025-12-31 00:36:27'),
(4, 'church_email', 'amy@chutti.com', 'Church email', '2025-11-26 23:13:02', '2025-12-31 00:36:27'),
(5, 'primary_color', '#002feb', 'Primary brand color (Methodist Blue)', '2025-11-26 23:13:02', '2025-12-31 01:14:09'),
(6, 'secondary_color', '#cc0000', 'Secondary brand color (Red)', '2025-11-26 23:13:02', '2025-12-31 00:28:34'),
(7, 'accent_color', '#f4c43f', 'Accent color (Gold/Yellow)', '2025-11-26 23:13:02', '2025-12-31 00:28:34'),
(98, 'homepage_hero_title', 'Rising Saint', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(99, 'homepage_hero_subtitle', 'Ghana Diocese', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(100, 'homepage_hero_tagline', 'dsdjfjsd', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(101, 'homepage_hero_cta1_text', '', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(102, 'homepage_hero_cta1_link', '', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(103, 'homepage_hero_cta2_text', '', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(104, 'homepage_hero_cta2_link', '', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(105, 'homepage_about_text', 'Not Much Here', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(106, 'homepage_social_facebook', '', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(107, 'homepage_social_instagram', '', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(108, 'homepage_social_tiktok', '', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(109, 'homepage_social_youtube', '', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(110, 'homepage_social_x', '', NULL, '2025-12-31 02:26:14', '2025-12-31 02:26:14'),
(111, 'homepage_hero_image', 'uploads/homepage/Screenshot 2025-09-27 010521.png', NULL, '2025-12-31 02:26:14', '2025-12-31 02:48:08'),
(112, 'homepage_about_image', 'uploads/homepage/Screenshot 2025-09-28 214017.png', NULL, '2025-12-31 02:26:14', '2025-12-31 02:48:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `role_id`, `phone`, `profile_photo`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin@church.com', '$2y$10$dXpqXvGhNmEX8AZtqpqxyeegxkKTWjIPfSIE/lOQbZoD10M8fuA1a', 'Admin', 'User', 1, NULL, 'uploads/profile_photos/695474c7e02f3_Screenshot 2025-10-10 011325.png', 1, '2025-11-27 11:51:09', '2025-12-31 01:09:14'),
(2, 'man@pkay.com', '$2y$10$ZbeCe7PYyA64Kt2ok5/ykuJIPskz7bdT5EPCnk.L/lX1IWcZgoOnS', 'Man', 'Pkay', 1, NULL, '/assets/uploads/profiles/69503d08e20b2_Screenshot 2025-10-01 003314.png', 1, '2025-12-27 20:09:45', '2025-12-27 20:09:45'),
(3, 'newuser@gmail.com', '$2y$10$HNTLaR77vRfcjTBGVqu2QuWgqTzqKRLAPX/H/lAFLLAV0Sw/QvhJ6', 'New', 'User', 1, NULL, NULL, 1, '2025-12-30 22:38:53', '2025-12-30 22:38:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_expense_category` (`category_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_ministries`
--
ALTER TABLE `homepage_ministries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_programs`
--
ALTER TABLE `homepage_programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ministries`
--
ALTER TABLE `ministries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leader_id` (`leader_id`);

--
-- Indexes for table `ministry_members`
--
ALTER TABLE `ministry_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ministry_id` (`ministry_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user` (`user_id`),
  ADD KEY `idx_notifications_read` (`is_read`),
  ADD KEY `idx_notifications_category` (`category`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `homepage_ministries`
--
ALTER TABLE `homepage_ministries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `homepage_programs`
--
ALTER TABLE `homepage_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `ministries`
--
ALTER TABLE `ministries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `ministry_members`
--
ALTER TABLE `ministry_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=315;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_expense_category` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ministries`
--
ALTER TABLE `ministries`
  ADD CONSTRAINT `ministries_ibfk_1` FOREIGN KEY (`leader_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ministry_members`
--
ALTER TABLE `ministry_members`
  ADD CONSTRAINT `ministry_members_ibfk_1` FOREIGN KEY (`ministry_id`) REFERENCES `ministries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ministry_members_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
