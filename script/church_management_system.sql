-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 09:01 AM
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
(1, 1, NULL, '2025-11-27', 'present', '', '2025-11-27 15:32:39'),
(2, 7, NULL, '2025-12-01', 'present', '', '2025-12-01 11:49:14');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `member_id`, `amount`, `donation_type`, `donation_date`, `notes`, `created_at`) VALUES
(3, 1, 31.00, 'Tithe', '2025-11-08', 'Tithe', '2025-12-01 19:06:48'),
(4, NULL, 23.00, 'Service Offering', '2025-12-01', 'service_total', '2025-12-05 03:58:04'),
(7, 7, 39.00, 'Service Offering', '2025-12-01', 'Amy Plegde', '2025-12-05 03:59:55'),
(9, NULL, 12000.00, 'General', '2025-12-07', 'service_total', '2025-12-13 12:27:39');

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
(10, 'W\'asida Nie', 'Thanksgiving program', '2025-11-30 16:00:00', 'Church auditorium', 100, 1, 'scheduled', '2025-11-29 20:04:49', '2025-11-29 23:09:44'),
(11, 'Carols Night', 'Nine lessons and carols', '2025-12-21 18:00:00', 'Church auditorium', 1200, 1, 'scheduled', '2025-11-29 20:51:49', '2025-11-29 20:51:49'),
(12, 'Christmas Celebration Service', 'Join us for a special Christmas worship service celebrating the birth of Christ with music, prayers, and fellowship.', '2025-12-25 21:00:00', 'Church Premise', 1200, 1, 'scheduled', '2025-11-29 22:42:47', '2025-11-29 23:19:59'),
(13, 'New Year Prayers & Fasting', 'Begin the new year with us in prayer and fasting. Let\'s seek God\'s guidance and blessings for 2026.', '2026-01-01 00:00:00', 'Church auditorium', 1500, 1, 'scheduled', '2025-11-29 23:21:22', '2025-11-29 23:21:22'),
(14, 'Community Food Drive', 'Participate in our community food drive to help provide nutritious meals to families in need.', '2026-01-12 07:30:00', 'Church Community', 500, 1, 'scheduled', '2025-11-29 23:22:44', '2025-11-29 23:22:44'),
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
(12, '2025-12-12', 2, 500.00, 'Bought fuel for generator', '', '2025-12-12 18:25:37'),
(13, '2025-12-12', 3, 34.00, 'Paid instrumentalist', '', '2025-12-12 18:25:53'),
(14, '2025-10-01', 4, 1000.00, 'Maintenance for the church', '', '2025-12-12 19:09:27');

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
(4, 'Maintenance', 'new chairs', '2025-12-12 19:09:27');

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
  `gender` varchar(20) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `date_of_birth`, `gender`, `join_date`, `status`, `address`, `city`, `state`, `zip_code`, `emergency_contact`, `emergency_phone`, `photo`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Joe', 'Doe', 'joe@doe.com', '233456789', '2025-11-20', 'Male', '2025-11-27', 'active', 'Weija', 'Accra', 'Greater Accra', '233', NULL, NULL, NULL, '2025-11-27 15:32:00', '2025-11-29 05:18:07'),
(7, NULL, 'Amy', 'Chutti', 'amy@chutti.com', '023456321', '2025-11-28', 'Female', '2025-11-29', 'active', 'West Hills', 'Accra', 'Greater Accra', '500', NULL, NULL, NULL, '2025-11-29 20:49:14', '2025-11-29 22:03:47'),
(8, NULL, 'Ebenezer', 'Quayson', 'eben@gmail.com', '0538697161', '2025-12-09', 'Male', '2025-12-09', 'active', 'West Hills', 'Accra', 'Greater Accra', '334', NULL, NULL, NULL, '2025-12-09 20:30:51', '2025-12-09 20:30:51'),
(9, NULL, 'Florence', 'Ampoma', 'florenceampoma@gmail.com', '0543678954', '2005-08-23', 'Female', '2025-12-13', 'active', 'Odorkor', 'Accra', 'Greater Accra', '500', NULL, NULL, NULL, '2025-12-13 08:49:26', '2025-12-13 09:07:44'),
(10, NULL, 'Asantewaa', 'Agyeiwaa', 'asantewa243@gmail.com', '0553423124', '2005-06-16', 'Female', '2025-12-13', 'active', 'Odorkor', 'Accra', 'Greater Accra', '500', NULL, NULL, NULL, '2025-12-13 08:50:39', '2025-12-13 09:07:29'),
(11, NULL, 'Kwesi', 'Frimpong', 'kwesi@gmail.com', '0534568978', '2005-04-13', 'Male', '2025-12-13', 'active', 'Tesano', 'Abeka', 'Greater Accra', '423', NULL, NULL, NULL, '2025-12-13 08:52:10', '2025-12-13 08:52:10');

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
(3, 'Community Outreach', 'We serve our community through charity work and social justice initiatives.', NULL, 'james@cole.com', 'saturday', '07:00:00', 'Church Community', 'active', '2025-11-30 10:09:25', '2025-12-05 02:56:54');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ministries`
--
ALTER TABLE `ministries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ministry_members`
--
ALTER TABLE `ministry_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
