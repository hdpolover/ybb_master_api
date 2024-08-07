-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2024 at 07:33 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ybb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super','program') NOT NULL COMMENT '''super'',''program''',
  `program_id` int(11) NOT NULL,
  `profile_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`, `program_id`, `profile_url`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'IVAL TINDIK', 'ival@gmail.com', '202cb962ac59075b964b07152d234b70', 'super', 1, '123', 1, 0, '2024-02-09 20:01:28', '2024-02-12 20:20:14'),
(2, 'IVAL TATO', 'iva@gmail.com', '202cb962ac59075b964b07152d234b70', 'program', 1, '123', 1, 0, '2024-02-09 20:01:28', '2024-02-12 20:20:03');

-- --------------------------------------------------------

--
-- Table structure for table `ambassadors`
--

CREATE TABLE `ambassadors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ref_code` varchar(50) DEFAULT NULL,
  `program_id` int(11) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `is_active` char(1) NOT NULL DEFAULT '1',
  `is_deleted` char(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` varchar(255) NOT NULL COMMENT 'uid',
  `full_name` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `nationality` varchar(100) NOT NULL,
  `gender` enum('male','female') NOT NULL COMMENT '''male'',''female''',
  `country_code` varchar(10) NOT NULL,
  `picture_url` varchar(255) DEFAULT NULL,
  `phone_number` varchar(25) NOT NULL,
  `program_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `program_category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `guideline` varchar(255) DEFAULT NULL,
  `twibbon` varchar(255) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `registration_video_url` varchar(255) DEFAULT NULL,
  `sponsor_canva_url` varchar(255) DEFAULT NULL,
  `theme` varchar(255) DEFAULT NULL,
  `sub_themes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `program_category_id`, `name`, `logo_url`, `description`, `guideline`, `twibbon`, `start_date`, `end_date`, `registration_video_url`, `sponsor_canva_url`, `theme`, `sub_themes`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 1, 'IYS 2024', '123', '123', 'asfa', 'egeg', '2024-02-21 20:29:22', '2024-02-29 20:29:22', 'effe', 'fef', NULL, NULL, 1, 0, '2024-02-12 20:29:22', '2024-02-12 20:29:22');

-- --------------------------------------------------------

--
-- Table structure for table `program_announcements`
--

CREATE TABLE `program_announcements` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `visible_to` int(11) NOT NULL COMMENT '1: public. 2: participant, 3: program participant',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_categories`
--

CREATE TABLE `program_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `program_type_id` int(11) DEFAULT NULL,
  `web_url` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `tiktok` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `telegram` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_categories`
--

INSERT INTO `program_categories` (`id`, `name`, `description`, `program_type_id`, `web_url`, `contact`, `location`, `email`, `instagram`, `tiktok`, `youtube`, `telegram`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'Istanbul Youth Summit', 'test', NULL, 'https://istanbulyouthsummit.com', '232424', 'Istanbul, Turkiye', 'a@gmail.com', 'abc', 'anc', 'ac', 'ac', 1, 0, '2024-02-12 20:25:06', '2024-02-12 20:25:06');

-- --------------------------------------------------------

--
-- Table structure for table `program_faqs`
--

CREATE TABLE `program_faqs` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `faq_category` enum('event_details','registration','payments') NOT NULL COMMENT '''event_details'',''registration'',''payments''',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_payments`
--

CREATE TABLE `program_payments` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `order_number` int(11) NOT NULL,
  `idr_amount` double(10,2) NOT NULL,
  `usd_amount` double(10,2) NOT NULL,
  `category` enum('registration','progam_fee') NOT NULL COMMENT '("registration", "progam_fee")',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_photos`
--

CREATE TABLE `program_photos` (
  `id` int(11) NOT NULL,
  `program_category_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_schedules`
--

CREATE TABLE `program_schedules` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `order_number` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_sponsors`
--

CREATE TABLE `program_sponsors` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_testimonies`
--

CREATE TABLE `program_testimonies` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `person_name` varchar(255) DEFAULT NULL,
  `testimony` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_type`
--

CREATE TABLE `program_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` char(1) NOT NULL DEFAULT '1',
  `is_deleted` char(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `program_category_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_setting_about`
--

CREATE TABLE `web_setting_about` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `page_name` varchar(100) DEFAULT NULL,
  `menu_path` varchar(100) DEFAULT NULL,
  `about_ybb` varchar(500) DEFAULT NULL,
  `about_program` varchar(500) DEFAULT NULL,
  `why_program` varchar(500) DEFAULT NULL,
  `what_program` varchar(500) DEFAULT NULL,
  `ybb_video_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_setting_home`
--

CREATE TABLE `web_setting_home` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `page_name` varchar(100) DEFAULT NULL,
  `menu_path` varchar(100) DEFAULT NULL,
  `banner1_img_url` varchar(255) DEFAULT NULL,
  `banner1_title` varchar(100) DEFAULT NULL,
  `banner1_description` varchar(255) DEFAULT NULL,
  `banner1_date` varchar(100) DEFAULT NULL,
  `banner2_img_url` varchar(255) DEFAULT NULL,
  `banner2_title` varchar(100) DEFAULT NULL,
  `banner2_description` varchar(255) DEFAULT NULL,
  `banner2_date` varchar(100) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `agenda` text DEFAULT NULL,
  `introduction` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admins_program_id` (`program_id`);

--
-- Indexes for table `ambassadors`
--
ALTER TABLE `ambassadors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ambassador_program_id` (`program_id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_participans_program_id` (`program_id`),
  ADD KEY `fk_participans_user_id` (`user_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_methods_program_id` (`program_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_program_category_id` (`program_category_id`);

--
-- Indexes for table `program_announcements`
--
ALTER TABLE `program_announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_program_announcements_program_id` (`program_id`);

--
-- Indexes for table `program_categories`
--
ALTER TABLE `program_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_program_categories_program_type` (`program_type_id`);

--
-- Indexes for table `program_faqs`
--
ALTER TABLE `program_faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_program_faqs_program_id` (`program_id`);

--
-- Indexes for table `program_payments`
--
ALTER TABLE `program_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_program_payments_program_id` (`program_id`);

--
-- Indexes for table `program_photos`
--
ALTER TABLE `program_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_program_photos_program_category_id` (`program_category_id`);

--
-- Indexes for table `program_schedules`
--
ALTER TABLE `program_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_program_schedules_program_id` (`program_id`);

--
-- Indexes for table `program_sponsors`
--
ALTER TABLE `program_sponsors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_program_sponsors_program_id` (`program_id`);

--
-- Indexes for table `program_testimonies`
--
ALTER TABLE `program_testimonies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_program_testimonies_program_id` (`program_id`);

--
-- Indexes for table `program_type`
--
ALTER TABLE `program_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_program_category_id` (`program_category_id`);

--
-- Indexes for table `web_setting_about`
--
ALTER TABLE `web_setting_about`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_web_setting_about_program_id` (`program_id`);

--
-- Indexes for table `web_setting_home`
--
ALTER TABLE `web_setting_home`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_web_setting_home_program_id` (`program_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ambassadors`
--
ALTER TABLE `ambassadors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `program_announcements`
--
ALTER TABLE `program_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_categories`
--
ALTER TABLE `program_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `program_faqs`
--
ALTER TABLE `program_faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_payments`
--
ALTER TABLE `program_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_photos`
--
ALTER TABLE `program_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_schedules`
--
ALTER TABLE `program_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_sponsors`
--
ALTER TABLE `program_sponsors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_testimonies`
--
ALTER TABLE `program_testimonies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_type`
--
ALTER TABLE `program_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_setting_about`
--
ALTER TABLE `web_setting_about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_setting_home`
--
ALTER TABLE `web_setting_home`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `fk_admins_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `ambassadors`
--
ALTER TABLE `ambassadors`
  ADD CONSTRAINT `fk_ambassador_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `fk_participans_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`),
  ADD CONSTRAINT `fk_participans_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `fk_payment_methods_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `fk_programs_category_id` FOREIGN KEY (`program_category_id`) REFERENCES `program_categories` (`id`);

--
-- Constraints for table `program_announcements`
--
ALTER TABLE `program_announcements`
  ADD CONSTRAINT `fk_program_announcements_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `program_categories`
--
ALTER TABLE `program_categories`
  ADD CONSTRAINT `fk_program_categories_program_type` FOREIGN KEY (`program_type_id`) REFERENCES `program_type` (`id`);

--
-- Constraints for table `program_faqs`
--
ALTER TABLE `program_faqs`
  ADD CONSTRAINT `fk_program_faqs_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `program_payments`
--
ALTER TABLE `program_payments`
  ADD CONSTRAINT `fk_program_payments_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `program_photos`
--
ALTER TABLE `program_photos`
  ADD CONSTRAINT `fk_program_photos_program_category_id` FOREIGN KEY (`program_category_id`) REFERENCES `program_categories` (`id`);

--
-- Constraints for table `program_schedules`
--
ALTER TABLE `program_schedules`
  ADD CONSTRAINT `fk_program_schedules_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `program_sponsors`
--
ALTER TABLE `program_sponsors`
  ADD CONSTRAINT `fk_program_sponsors_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `program_testimonies`
--
ALTER TABLE `program_testimonies`
  ADD CONSTRAINT `fk_program_testimonies_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_program_category_id` FOREIGN KEY (`program_category_id`) REFERENCES `program_categories` (`id`);

--
-- Constraints for table `web_setting_about`
--
ALTER TABLE `web_setting_about`
  ADD CONSTRAINT `fk_web_setting_about_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `web_setting_home`
--
ALTER TABLE `web_setting_home`
  ADD CONSTRAINT `fk_web_setting_home_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
