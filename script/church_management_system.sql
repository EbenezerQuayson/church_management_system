-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2025 at 12:07 AM
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

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `member_id`, `event_id`, `attendance_date`, `status`, `notes`, `created_at`) VALUES
(4, 25, NULL, '2025-12-24', 'absent', '', '2025-12-24 15:00:57');

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
(7, NULL, 120.00, 'Service Offering', '2025-12-01', 'Amy Plegde', '2025-12-05 03:59:55', 'anonymous'),
(9, NULL, 1200.00, 'General', '2025-12-07', 'service_total', '2025-12-13 12:27:39', 'service_total'),
(10, NULL, 210.00, 'General', '2025-12-15', 'Harcesyuf', '2025-12-15 00:08:04', 'service_total'),
(16, NULL, 5000.00, 'General', '2025-03-14', 'Teasdt dfisd', '2025-12-25 16:48:18', 'service_total'),
(18, NULL, 1200.00, 'Other', '2025-02-05', 'dhjsjd', '2025-12-25 18:08:35', 'anonymous');

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
(11, 'Carols Night', 'Nine lessons and carols', '2025-12-21 18:00:00', 'Church auditorium', 1200, 1, 'completed', '2025-11-29 20:51:49', '2025-12-24 23:05:12'),
(12, 'Christmas Celebration Service', 'Join us for a special Christmas worship service celebrating the birth of Christ with music, prayers, and fellowship.', '2025-12-25 21:00:00', 'Church Premise', 1200, 1, 'completed', '2025-11-29 22:42:47', '2025-12-25 01:56:02'),
(13, 'New Year Prayers & Fasting', 'Begin the new year with us in prayer and fasting. Let\'s seek God\'s guidance and blessings for 2026.', '2026-01-01 00:00:00', 'Church auditorium', 1500, 1, 'scheduled', '2025-11-29 23:21:22', '2025-12-25 01:55:46'),
(14, 'Community Food Drive', 'Participate in our community food drive to help provide nutritious meals to families in need.', '2026-01-12 07:30:00', 'Church Community', 500, 1, 'scheduled', '2025-11-29 23:22:44', '2025-12-25 01:55:37'),
(15, '31st Watch Night', 'Join us for our all night service as we crossover into the next year in victory', '2025-12-31 21:30:00', 'Church Premise', 1200, 1, 'scheduled', '2025-11-29 23:28:36', '2025-11-29 23:28:36');

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
(14, '2025-10-01', 4, 1000.00, 'Maintenance for the church', '', '2025-12-12 19:09:27'),
(15, '2025-06-18', 3, 300.00, 'Paid instrumentalist for gig', NULL, '2025-12-14 23:41:59'),
(16, '2025-12-19', 5, 700.00, 'Organised a get-together for the youth', NULL, '2025-12-24 23:02:07');

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
(22, NULL, 'Amy', 'Chutti', 'amy@chutti.com', '023456321', '2025-11-28', 'Female', '2025-12-24', 'active', '', '', '', '', '', '', '', '', NULL, '2025-12-24 13:58:06', '2025-12-24 22:51:38'),
(23, NULL, 'Asantewaa', 'Agyeiwaa', 'asantewa243@gmail.com', '0553423124', '2005-06-16', 'Female', '2025-12-23', 'active', '', '', '', NULL, NULL, '', '', '', NULL, '2025-12-24 13:58:06', '2025-12-24 13:58:06'),
(24, NULL, 'Ebenezer', 'Quayson', 'eben@gmail.com', '0538697161', '2025-12-09', 'Male', '2025-12-24', 'active', '', '', '', '', '', '', '', '', NULL, '2025-12-24 13:58:06', '2025-12-24 22:44:37'),
(25, NULL, 'Florence', 'Ampoma', 'florenceampoma@gmail.com', '0543678954', '2005-08-23', 'Female', '2025-12-23', 'active', 'Accra', 'Broadcasting', '', NULL, NULL, 'Greater Accra', '', '', NULL, '2025-12-24 13:58:06', '2025-12-24 13:58:06'),
(26, NULL, 'Joe', 'Doe', 'joe@doe.com', '233456789', '2025-11-20', 'Male', '2025-12-23', 'active', '', '', '', NULL, NULL, '', '', '', NULL, '2025-12-24 13:58:06', '2025-12-24 13:58:06');

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
(1, 'Youth Ministry', 'Youth meeting', NULL, 'joe@gmail.com', 'sunday', '19:00:00', 'Church Premises', 'active', '2025-11-27 15:29:56', '2025-11-27 15:29:56'),
(2, 'Music Ministry', 'Our blessed musicians lead worship and create meaningful spiritual experiences.', NULL, 'joseph@gmail.com', 'saturday', '18:00:00', 'Church Premise', 'active', '2025-11-30 10:07:36', '2025-12-05 03:14:31'),
(3, 'Community Outreach', 'We serve our community through charity work and social justice initiatives.', NULL, 'james@cole.com', 'saturday', '07:00:00', 'Church Community', 'active', '2025-11-30 10:09:25', '2025-12-05 02:56:54'),
(6, 'Ushering Department', 'sdhjfkdfjskskdjkdsdsd', NULL, 'joe@doe.com', 'sunday', '21:12:00', 'Church Premise', 'active', '2025-12-23 18:09:45', '2025-12-23 18:09:45'),
(8, 'V2D', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-12-24 19:42:56', '2025-12-24 19:42:56');

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
(27, 3, 23, 'Member', '2025-12-23', '2025-12-24 13:58:06'),
(29, 1, 25, 'Member', '2025-12-23', '2025-12-24 13:58:06'),
(30, 6, 25, 'Member', '2025-12-23', '2025-12-24 13:58:06'),
(31, 3, 26, 'Member', '2025-12-23', '2025-12-24 13:58:06'),
(43, 2, 24, 'Member', '2025-12-24', '2025-12-24 22:44:37'),
(44, 3, 24, 'Member', '2025-12-24', '2025-12-24 22:44:37'),
(45, 1, 22, 'Member', '2025-12-24', '2025-12-24 22:51:38'),
(46, 6, 22, 'Member', '2025-12-24', '2025-12-24 22:51:38');

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
(43, 1, 'Expense Updated', 'An expense of ¢500.00 was updated.', 'general', 'expenses.php', 1, '2025-12-25 21:41:06', '2025-12-25 22:59:56', NULL);

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
(1, 'church_name', 'The Methodist Ghana', 'Church name', '2025-11-26 23:13:02', '2025-12-08 23:42:14'),
(2, 'church_address', 'Accra', 'Church address', '2025-11-26 23:13:02', '2025-12-08 23:42:14'),
(3, 'church_phone', '04355421', 'Church phone', '2025-11-26 23:13:02', '2025-12-08 23:42:14'),
(4, 'church_email', 'james@cole.com', 'Church email', '2025-11-26 23:13:02', '2025-12-08 23:42:14'),
(5, 'primary_color', '#003DA5', 'Primary brand color (Methodist Blue)', '2025-11-26 23:13:02', '2025-12-08 23:42:14'),
(6, 'secondary_color', '#CC0000', 'Secondary brand color (Red)', '2025-11-26 23:13:02', '2025-12-08 23:42:14'),
(7, 'accent_color', '#F4C43F', 'Accent color (Gold/Yellow)', '2025-11-26 23:13:02', '2025-12-08 23:42:14');

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
(1, 'admin@church.com', '$2y$10$ez4BF3K9qdHHCpJWumZGIe8A4RqJwRZIlVCOCHbn.VVtOMkZMScVW', 'Admin', 'User', 1, NULL, '/assets/uploads/profiles/69283b2d585e9_Screenshot 2025-10-10 011214.png', 1, '2025-11-27 11:51:09', '2025-11-27 11:51:09');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `ministries`
--
ALTER TABLE `ministries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ministry_members`
--
ALTER TABLE `ministry_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
